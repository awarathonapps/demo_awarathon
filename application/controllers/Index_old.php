<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Index extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('ratelimit');
    }
    public function index($error='') {
        $mw_session = $this->session->userdata('awarathon_session');
        if(isset($mw_session)){
            // redirect('dashboard');
			redirect($mw_session['home']);
        }else{
            //KRISHNA --- VAPT - ENABLED MAX 3 ATTEMPT FOR LOGIN WITHIN 5 MINUTES
            // if($this->session->userdata('login_attempt_session')){
            //     $data['error']='HTTP 429';

            //     $isBlock = limitRequests($this->input->ip_address(),3,300);
            //     if($isBlock !== 'HTTP 429'){
            //         $this->session->unset_userdata('login_attempt_session');
            //         $data['error']='';
            //     }
            // }else{
            //     $data['error']='';
            // }

            $data['error']='';
            $member_username = '';
            $member_password = '';
            if(isset($_COOKIE["MW_token"])) {
                $login_secure = explode("///",$_COOKIE["MW_token"]);
                if(count((array)$login_secure) > 0){
                    $member_username  = base64_decode($login_secure[0]);
                    $member_password  = base64_decode($login_secure[1]);
                }     
            }
            $data['member_username']=$member_username;
            $data['member_password']=$member_password;
            // $this->load->view('login',$data);
			$this->load->view('auth/signin',$data);
        }
        
    }

}
