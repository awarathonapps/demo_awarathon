<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->mw_session = $this->session->userdata('awarathon_session');
        $this->load->model('common_model');
    }
    public function index($error = '') {
        
        $data['error'] = $error;
        if(isset($this->mw_session)){
            redirect('dashboard');
        }else{
            // $this->load->view('login', $data);    
			$this->load->view('auth/signin',$data);
        }
    }

    public function service() {
        $remember = $this->input->post('remember');        
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $this->load->model('login_model');
        $sub_domain =$this->config->item('sub_domain');
        if($sub_domain!=""){
            $Rowset = $this->common_model->get_value('company','id',"portal_name='".$sub_domain."'");
            if(count((array)$Rowset)>0){
                $Company_id = $Rowset->id;
            }else{
                $error = "Invalid Sub domain..!";
                $this->index($error); 
            }
        }else{
            $Company_id = '';    
        }        
        $result = $this->login_model->validate($Company_id);
        if (!$result) {
            $error = 'Invalid username/password. Try again.';
            $this->index($error);
        } else {
            $acces_management = $this->session->userdata('awarathon_session');
             if($remember) 
                {
                    $encode_username = base64_encode($username);
                    $encode_password = base64_encode($password);
                    $encodedlogin = $encode_username.'///'.$encode_password;
                    setcookie ("MW_token",$encodedlogin,time() + (86400*360), "/","",0);
//                  setcookie ("member_username",$username,time() + (86400*360), "/","",0);
//                  setcookie ("member_password",$password,time() + (86400*360), "/","",0);                
                } else {                        
                        if(isset($_COOKIE["MW_token"])) {                              
                            setcookie ("MW_token","",time()- 60, "/","", 0);
                        }
                }
            if($acces_management['login_type']==3){
               // redirect('trainee_dashboard');
				redirect('home');
            }
            if($acces_management['login_type']==2){
				if($acces_management['role']==2 || $acces_management['role']==1){
					redirect('assessment');
				}else{
                	// redirect('manager_dashboard');
					redirect('home');
				}
            }else{
                // redirect('assessment_dashboard');
				redirect('home');
            }
        }
    }
	function login_as($dc_user_id){
		$sub_domain =$this->config->item('sub_domain');
        if($sub_domain!=""){
            $Rowset = $this->common_model->get_value('company','id,company_name',"portal_name='".$sub_domain."'");
            if(count($Rowset)>0){
                $Company_id = $Rowset->id;
				$user_id = base64_decode($dc_user_id);
				$this->load->model('login_model');
				$returnflag = $this->login_model->temp_session($user_id,$Company_id,$Rowset->company_name);
				if($returnflag){
					redirect('dashboard');
				}else{
					redirect(base_url());
				}
            }else{
                $error = "Invalid Sub domain..!";
                $this->index($error); 
            }
        }else{
			redirect(base_url());
		}
	}
    function logout() {
        $data['module_id'] = '1.0';
        //$user_id   = $this->mw_session['user_id'];
        //$rpt_token = $this->mw_session['user_token'];
        //$this->common_model->delete_whereclause('temp_trainer_reports', 'rpt_user_id="'.$user_id.'" AND rpt_token="'.$rpt_token.'"');
        $this->session->unset_userdata('awarathon_session');        
        session_destroy();
		foreach (array_keys((array)$this->session->userdata) as $key) {   $this->session->unset_userdata($key); }
		$this->output->delete_cache();
        $this->load->driver('cache');
        if (session_status() === PHP_SESSION_ACTIVE){
        	$this->session->sess_destroy();
		}
        $this->cache->clean();
        redirect('index');
    }
	function forget_password(){
		$success = 1;
		$message = '';
		$this->load->model('login_model');
		$email = $this->input->post('email');
		if(empty($email)){
			$success = 0;
			$message = "Email address required..!";
		}else{
			$sub_domain = $this->config->item('sub_domain');
			if($sub_domain!=""){
				$Rowset = $this->common_model->get_value('company','id',"portal_name='".$sub_domain."'");
				if(count((array)$Rowset)>0){
					$Company_id = $Rowset->id;
				}else{
					$success = 0;
					$message = "Invalid Sub domain..!";
				}
			}else{
				$Company_id = '';    
			}        
			$result = $this->login_model->validate_email($Company_id,$email);
			if(empty($result)){
				$success = 0;
				$message = "Email address not registered..!";
			}else{
				//reset password
				$first_name = $result->first_name;
				$user_id = $result->userid;
				//generate new password for each user
				$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				$new_pwd = substr(str_shuffle($str_result),0, 8);
				$data = array(
					'password' => $this->common_model->encrypt_password($new_pwd),
					'modifieddate' => date('Y-m-d H:i:s'),
				);
				$this->common_model->update('company_users', 'userid', $user_id, $data);
				$emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='on_password_request'");
				if(!empty($emailTemplate)){
					$pattern[0] = '/\[MEMBER_FULL_NAME\]/';
					$pattern[1] = '/\[MEMBER_USERNAME\]/';
					$pattern[2] = '/\[MEMBER_PASSWORD\]/';
					$replacement[0] = $fullname = $result->first_name.' '.$result->last_name;
					$replacement[1] = $result->username;
					$replacement[2] = $new_pwd;
					
					$subject = $emailTemplate->subject;
					$message = $emailTemplate->message;
					$body = preg_replace($pattern, $replacement, $message);
					$ToName = $fullname;
					$recipient = $result->email;
					$ReturnArray = $this->common_model->sendPhpMailer($Company_id, $ToName, $recipient, $subject, $body);
				}
				$message = 'Please check your email!';
			}
		}
		$response = [
					'success' => $success,
					'message' => $message
				];
		echo json_encode($response);
	}
}
