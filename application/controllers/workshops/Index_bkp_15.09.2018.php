<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Index extends CI_Controller {
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
		$this->load->model('workshops/index_model');
    }
    public function index($error='') {
		$data = [];
		$this->load->view('workshops/list', $data);
	}
	public function load_workshops(){
		$base_url   = base_url();
		$asset_url  = $this->config->item('assets_url');
		$sub_domain = $this->config->item('sub_domain');
        if($sub_domain!=""){
            $Rowset = $this->common_model->get_value('company','id',"portal_name='".$sub_domain."'");
            if(count($Rowset)>0){
                $company_id = $Rowset->id;
            }else{
				$company_id = '';   
            }
        }else{
            $company_id = '';    
		}
		$workshop_list = $this->index_model->LoadDataTable($company_id);
		$html = '';
		$colcount = 0;
		$box_class = 'pro-mar-right';
		if (count($workshop_list)>0){
			foreach ($workshop_list as $key => $value) {
				$workshop_id                = $value->id;
				$workshop_name              = $value->workshop_name;
				$powered_by                 = $value->powered_by;
				$short_description          = $value->short_description;
				$workshop_image             = $value->workshop_image;
				$end_date                   = $value->end_date;
				$workshop_session           = $value->workshop_session;
				if ($workshop_session=="PRE"){
					$workshop_start_dttm = $value->workshop_start_date." ".$value->pre_start_time;
					$workshop_end_dttm   = $value->workshop_end_date." ".$value->pre_end_time;
				}
				if ($workshop_session=="POST"){
					$workshop_start_dttm = $value->workshop_start_date." ".$value->post_start_time;
					$workshop_end_dttm   = $value->workshop_end_date." ".$value->post_end_time;
				}

				$total_questions_played = 0;
				$total_feedback_played  = 0;
				$total_played           = 0;
				$total_correct          = 0;
				$total_wrong            = 0;

				$total_questions_result          = $this->index_model->fetch_total_questions_played($company_id,$workshop_id,$workshop_session); 
				if (count($total_questions_result)>0){
					$total_questions_played = $total_questions_result->total;
				}
				
				$total_feedback_result = $this->index_model->fetch_total_feedback_played($company_id,$workshop_id,$workshop_session);
				if (count($total_feedback_result)>0){
					$total_feedback_played = $total_feedback_result->total;
				}
				$total_played = ($total_questions_played + $total_feedback_played);
				
				$total_correct_result = $this->index_model->fetch_total_correct($company_id,$workshop_id,$workshop_session);
				if (count($total_correct_result)>0){
					$total_correct = $total_correct_result->total;
				}
				$total_wrong_result = $this->index_model->fetch_total_wrong($company_id,$workshop_id,$workshop_session);
				if (count($total_wrong_result)>0){
					$total_wrong = $total_wrong_result->total;
				}

				if ($colcount==2){
					$box_class = '';
				}else{
					$box_class = 'pro-mar-right';
				}
	
				$html .= '<div class="project-one-fourth '.$box_class.'  pro-mar-top">     
								<a href="#" class="white-space-link">       	
								<img src="'.$asset_url.'assets/uploads/workshop/'.$workshop_image.'" alt="">
								<h4>'.$workshop_name.'</h4>
								<h5><span>START : </span>'.$workshop_start_dttm.'</h5>
								<h5><span>END : </span>'.$workshop_end_dttm.'</h5>
								<h5><span>SESSION : </span>'.$workshop_session.'</h5>
								<h5><span>BY : </span>'.$powered_by.'</h5>
								<h6>'.$short_description.'</h6>
								<div class="clearfix"></div>
								<div class="number">
									<div class="col-3 pad-bdr">
										<i class="fa fa-lg fa-question " style="float:left; font-size:17px; margin:3px 5px 0 0;"></i>
										<div class="value">'.$total_played.'</div>
										<div style="width:100%;">Total</div>
									</div>
									<div class="col-3 pad-bdr">
										<i class="fa fa-lg fa-check" style="float:left; font-size:17px; margin:3px 5px 0 0;"></i>
										<div class="value">'.$total_correct.'</div>
										<span>Correct</span>
									</div>
									<div class="col-3 pad-bdr">
										<i class="fa fa-lg fa-times" style="float:left; margin:5px 5px 0 0;"></i>
										<div class="value">'.$total_wrong.'</div>
										<span>Wrong</span>
									</div>
									<div class="col-3">
										<i class="fa fa-lg fa-comments-o " style="float:left; margin:5px 5px 0 0;"></i>
										<div class="value">'.$total_feedback_played.'</div>
										<span>Preference</span>
									</div>
								</div>
							</a>
							<a target="_blank" href="'.site_url("workshops/results/index/").base64_encode($company_id)."/".base64_encode($workshop_id)."/".base64_encode($workshop_session).'">   
								<div class="clickbt" >
									View Results
								</div> 
							</a>          
						</div>';
	
				$colcount++;
				if ($colcount==3){
					$colcount = 0;
					$html .= '<div class="clearfix"></div>';
				}
			}
		}else{
			$html = '<div style="text-align:center;">Live Workshop Not Available.</div>';
		}
		echo $html;
	}
}
