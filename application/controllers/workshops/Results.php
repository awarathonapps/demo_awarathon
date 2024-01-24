<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Results extends CI_Controller {
    function __construct() {
		parent::__construct();

		header("Access-Control-Allow-Origin: *");
		ini_set('allow_url_fopen', 1);
        ini_set('max_file_uploads', '1000');
        ini_set('memory_limit', '200M');
        ini_set('post_max_size', '200M');
        ini_set('max_execution_time', -1);
        date_default_timezone_set('Asia/Kolkata');

		$this->load->model('smtp_model');
		$this->load->model('common_model');
		$this->load->model('workshops/results_model');
    }
    public function index($error='') {
		$company_id               = ($this->uri->segment(4)) ? $this->uri->segment(4): 0;
		$workshop_id              = ($this->uri->segment(5)) ? $this->uri->segment(5): 0;
		$workshop_session         = ($this->uri->segment(6)) ? $this->uri->segment(6): '';
		$data['company_id']       = $company_id;
		$data['workshop_id']      = $workshop_id;
		$data['workshop_session'] = $workshop_session;
		$data['workshopset'] = $this->common_model->get_value('workshop', 'workshop_name', 'id=' . base64_decode($workshop_id));
		$this->load->view('workshops/results', $data);
	}
	public function view_results(){
		$company_id               = ($this->uri->segment(4)) ? base64_decode($this->uri->segment(4)): 0;
		$workshop_id              = ($this->uri->segment(5)) ? base64_decode($this->uri->segment(5)): 0;
		$workshop_session         = ($this->uri->segment(6)) ? base64_decode($this->uri->segment(6)): '';
		
		$base_url   = base_url();
		$asset_url  = $this->config->item('assets_url');
		$sub_domain = $this->config->item('sub_domain');
        if($sub_domain!=""){
            $Rowset = $this->common_model->get_value('company','id',"portal_name='".$sub_domain."'");
            if(count($Rowset)>0){
                $_company_id = $Rowset->id;
            }else{
				$_company_id = '';   
            }
        }else{
            $_company_id = '';    
		}
		$questions_played_result =  $this->results_model->LoadQuestionsResult($company_id,$workshop_id,$workshop_session);
		$html='';
		if ($company_id==$_company_id){
			$html = '<table id="table_result">
						<tr>
							<th width="60%">Participant Name</th>
							<th width="10%">Total</th>
							<th width="10%">Correct</th>
							<th width="10%">Wrong</th>
							<th width="10%">Preference</th>
							<th width="10%">Accuracy</th>
						</tr>
					';
			if (count($questions_played_result)>0){
				foreach ($questions_played_result as $qkey => $qvalue) {
					$user_id    = $qvalue->user_id;
					$fullname   = $qvalue->fullname;
					$total      = $qvalue->total;
					$correct    = $qvalue->correct;
					$wrong      = $qvalue->wrong;
					$accuracy   = $qvalue->accuracy."%";
					$preference = 0;

					$feedback_played_result =  $this->results_model->LoadFeedbackResult($company_id,$workshop_id,$workshop_session,$user_id);
					if (count($feedback_played_result)>0){
						foreach ($feedback_played_result as $fkey => $fvalue) {
							$preference = $fvalue->total;
							$total = $total + $preference;
						}
					}

					$html .= '<tr>
								<td>'.$fullname.'</td>
								<td>'.$total.'</td>
								<td>'.$correct.'</td>
								<td>'.$wrong.'</td>
								<td>'.$preference .'</td>
								<td>'.$accuracy .'</td>
							</tr>
					';
				}
                        }else{
                                $feedback_played_result =  $this->results_model->LoadFeedbackResult($company_id,$workshop_id,$workshop_session);
                                if(count($feedback_played_result)>0){
                                    foreach ($feedback_played_result as $qkey => $qvalue) {
					$user_id    = $qvalue->user_id;
					$fullname   = $qvalue->fullname;
					$total      = $qvalue->total;

					$html .= '<tr>
                                                    <td>'.$fullname.'</td>
                                                    <td>'.$total.'</td>
                                                    <td>NA</td>
                                                    <td>NA</td>
                                                    <td>'.$total .'</td>
                                                    <td>NA</td>
                                            </tr>';
				}
                                }
                        }
			$html .= '</table>';
		}else{
			$html = '<div style="text-align:center;">Company mismatch.</div>';
		}
	$Rdata['toluser'] = count($questions_played_result)>0 ? count($questions_played_result) : count($feedback_played_result);
        $Rdata['html'] = $html;
        echo json_encode($Rdata);
		//echo $html;
	}
}
