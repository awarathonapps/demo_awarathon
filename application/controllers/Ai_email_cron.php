<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ai_email_cron extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('common_model');
        $this->load->model('ai_email_cron_model');
    }
	
    public function index(){
        echo "Welcome to the Awarathon - AIDEMO";
        exit;
    }
	
	public function schedule_data(){
		$company_id = $this->input->post('company_id');
		$assessment_id = $this->input->post('assessment_id');
		$sendAll = $this->input->post('sendAll');
		$success = 1;
		$message = '';
		$trainee_ids = $this->input->post('trainee_id');	//selected trainees if given
		if(empty($assessment_id)){
			$success = 0;
			$message = 'Error while scheduling email reports!';
		} else{
			$assessment_arr = implode(', ',[$assessment_id]);
			$ass_users_result = $this->ai_email_cron_model->get_schedule_users_data($company_id,$assessment_arr,$trainee_ids,$sendAll);	
			// print_r($ass_users_result);	die('here');			
			//Add assessment wise users to the email schedule table
			if(!empty($ass_users_result)){
				foreach($ass_users_result as $user){
					$i_data = [
						'assessment_id' => $user->assessment_id,
						'company_id' 	=> $user->company_id,
						'user_id'	 	=> $user->user_id,
						'email'  	 	=> !empty($user->email) ? $user->email : '',
						'attempt'		=> 0,
						'is_sent'	 	=> 0,
						'scheduled_at'  => date('Y-m-d H:i:s'),
					];
					$this->common_model->insert('trainee_report_schedule', $i_data);
				}
				
				//check if cron scheduled or not
				$cron_status = $this->ai_email_cron_model->get_cron_status();
				if(empty($cron_status)){
					$schedule_cron = [
						'schedule_status' => 0,
						'cron_status' 	  => 0
					];
					$this->common_model->insert('email_schedule', $schedule_cron); //schedule cron to send report link via email
				}
				$message = count($ass_users_result). ' Email reports sent successfully!';
			}else{
				$message = 'No email reports found to be scheduled!';
			}
		}
		$response = [
					'success' => $success,
					'message' => $message
				];
		echo json_encode($response);
	}
	
	public function trainee_email_cron(){
		$cron_status = $this->ai_email_cron_model->get_cron_status();
		if(!empty($cron_status)){
			$is_scheduled = $cron_status[0]->schedule_status;
			$is_running = $cron_status[0]->cron_status;
			if($is_scheduled == 0 && $is_running == 0){
				//execute the email schedule code
				$this->ai_email_cron_model->set_cron_status('1');
				$this->send_report();
			}
			//else wait till the cron execute
			// $this->ai_email_cron_model->set_cron_status('0'); // close the cron
		}
		//else wait till the user data to be scheduled
	}
	
	public function send_report(){
		error_reporting(0);
		
		//Fetch assessment users to send email
		$ass_users_result = $this->ai_email_cron_model->get_scheduled_trainee_data();
		if(!empty($ass_users_result)){
			
			//Get Email template
			$emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='on_assessment_report_send'");
			
			if(!empty($emailTemplate)){
				foreach($ass_users_result as $userdata){
					//send email
					$db_id = $userdata->id;
					$name = $userdata->name;					
					$email = $userdata->email;
					$Company_id = $userdata->company_id;
					$report_type= $userdata->report_type;					
					$pattern[0] = '/\[NAME\]/';
					$pattern[1] = '/\[ASSESSMENT_NAME\]/';
					$pattern[2] = '/\[START_DATE\]/';
					$pattern[3] = '/\[END_DATE\]/';
					$pattern[4] = '/\[REPORT_LINK\]/';
					
					$replacement[0] = $userdata->name;
					$replacement[1] = $userdata->assessment;
					$replacement[2] = $userdata->start_dttm;
					$replacement[3] = $userdata->end_dttm;
					
					$company_id_enc = ($Company_id);
					$assessment_id_enc = ($userdata->assessment_id);
					$user_id_enc = base64_encode($userdata->user_id);
					$report_link = '<table cellpadding="5">';
					switch($report_type){
						case 1:
							$report_link .= '<tr><td>AI Report</td>';
							$report_link .= '<td>'.base_url().'pdf/ai/'.$company_id_enc.'/'.$assessment_id_enc.'/'.$user_id_enc.'</td></tr>';
							break;
						case 2:
							$report_link .= '<tr><td>Assessor Report</td>';
							$report_link .= '<td>'.base_url().'pdf/manual/'.$company_id_enc.'/'.$assessment_id_enc.'/'.$user_id_enc.'</td></tr>';
							break;
						case 3:
							$report_link .= '<tr><td>AI Report</td><td>'.base_url().'pdf/ai/'.$company_id_enc.'/'.$assessment_id_enc.'/'.$user_id_enc.'</td></tr>';
							$report_link .= '<tr><td>Assessor Report</td><td>'.base_url().'pdf/manual/'.$company_id_enc.'/'.$assessment_id_enc.'/'.$user_id_enc.'</td></tr>';
							$report_link .= '<tr><td>Final Report</td><td>'.base_url().'pdf/combine/'.$company_id_enc.'/'.$assessment_id_enc.'/'.$user_id_enc.'</td></tr>';
					}
					$report_link .= '</table>';
					$replacement[4] = $report_link;

					$subject = $emailTemplate->subject;
					$message = $emailTemplate->message;
					$body = preg_replace($pattern, $replacement, $message);
					$ToName = $userdata->name;
					$recipient = $userdata->email;
					// $recipient = 'krishna.revawala@awarathon.com';
					$ReturnArray = $this->common_model->sendPhpMailer($Company_id, $ToName, $recipient, $subject, $body);
					$result = [
						'attempt' => ++$userdata->attempt,
						'is_sent' => $ReturnArray['sendflag'],
						'sent_at' => date('Y-m-d H:i:s')
					];
					$this->common_model->update('trainee_report_schedule','id',$db_id,$result);
					sleep(2);	
				}	
				// sleep(10);	//Sleep for 10 seconds after completing one cycle of sending email
				$this->ai_email_cron_model->set_cron_status('0');
			}
		}else{
			//close the cron job
			$this->common_model->delete_whereclause('email_schedule', 'schedule_status = 0');
		}
		
	}
}

