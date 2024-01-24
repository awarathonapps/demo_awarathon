<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Index extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

    }
    public function index($error = '')
    {
        $data['error'] = '';
        $data['member_username'] = '';
        $data['member_password'] = '';
        $data['is_sign'] = 0;
        $this->load->view('auth/signin', $data);
    }
    public function login($error = '')
    {
        $mw_session = $this->session->userdata('awarathon_session');
        if (isset($mw_session)) {
            // redirect('dashboard');
            redirect($mw_session['home']);
        } else {
            $data['error'] = '';
            $member_username = '';
            $member_password = '';
            if (isset($_COOKIE["MW_token"])) {
                $login_secure = explode("///", $_COOKIE["MW_token"]);
                if (count((array) $login_secure) > 0) {
                    $member_username = base64_decode($login_secure[0]);
                    $member_password = base64_decode($login_secure[1]);
                }
            }
            $data['member_username'] = $member_username;
            $data['member_password'] = $member_password;
            $data['is_sign'] = 1;
            // $data['path'] = $this->config->item('base_url');
            // $this->load->view('login',$data);
            $this->load->view('auth/signin', $data);
        }

    }
    public function reset($user_id = '')
    {
        $error = '';
        $success = 1;
        $data['user_id'] = base64_decode($user_id);
        $this->load->model('common_model');
        $Rowset = $this->common_model->get_value('company_users', 'reset_link_expire_at', "userid='" . base64_decode($user_id) . "'");
        if (count((array) $Rowset) > 0) {
            $expire_date = $Rowset->reset_link_expire_at;
            $today_date = date("Y-m-d H:i:s");
            if ($today_date > $expire_date) {
                $error = "Reset password link is expired, Please try again.";
                $success = 0;
            }
        } else {
            $error = "User not Exist.";
            $success = 0;
        }
        if ($success) {
            $data['is_sign'] = 2;
        } else {
            $data['is_sign'] = 1;
        }
        $data['error'] = $error;
        $data['member_username'] = '';
        $data['member_password'] = '';
        // $data['path'] = $this->config->item('base_url');
        $this->load->view('auth/signin', $data);
    }

}
