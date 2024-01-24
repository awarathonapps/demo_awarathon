<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Profile extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('awarathon_session') == FALSE) {
            redirect('login');
        } else {
            $this->mw_session = $this->session->userdata('awarathon_session');
//            print_r($this->mw_session);
//            exit;
            $this->load->model('common_model');
            $this->load->model('users_model');
        }
    }

    public function index($errors = "") {
        $id = '';
        $data['module_id'] = '12.00';
        $data['errors'] = $errors;
        if ($this->mw_session['login_type'] == 3) {
            $data['RowSet'] = $this->common_model->get_value('device_users', 'avatar,email as username,firstname as first_name,'
                    . 'lastname as last_name,mobile', 'user_id=' . $this->mw_session['user_id']);
        } elseif ($this->mw_session['login_type'] == 2) {
            $data['RowSet'] = $this->common_model->get_value('company_users', '*', 'userid=' . $this->mw_session['user_id']);
        } else {
            $data['RowSet'] = $this->common_model->get_value('users', '*', 'userid=' . $this->mw_session['user_id']);
        }
        //$data['SelectCountry'] = $this->users_model->SelectedCountry($this->mw_session['user_id']);
        //$data['SelectState'] = $this->users_model->SelectedState($this->mw_session['user_id']);
        //$data['SelectCity'] = $this->users_model->SelectedCity($this->mw_session['user_id']);
        $data['username'] = $this->mw_session['username'];
        $data['login_type'] = $this->mw_session['login_type'];
//        echo "<pre>";
//        print_r( $data['RowSet']);exit;
        $this->load->view('profile/index', $data);
    }

    public function upload_image() {
        if ($this->mw_session['login_type'] == 3) {
            $Oldata = $this->common_model->get_value('device_users', 'avatar', 'user_id=' . $this->mw_session['user_id']);
        } elseif ($this->mw_session['login_type'] == 2) {
            $Oldata = $this->common_model->get_value('company_users', 'avatar', 'userid=' . $this->mw_session['user_id']);
        } else {
            $Oldata = $this->common_model->get_value('users', 'avatar', 'userid=' . $this->mw_session['user_id']);
        }
        $profile_image = $Oldata->avatar;
        if ($this->mw_session['company_id'] == "") {
            $asset_url = base_url();
        } else {
            $asset_url = $this->config->item('assets_url');
        }
        if ($this->mw_session['login_type'] == 3) {
            $upload_path = './assets/uploads/profile/';
        } else {
            $upload_path = './assets/uploads/avatar/';
        }
        if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['size'] > 0) {

            if ($profile_image != "") {
                $Path = $upload_path . $profile_image;
                if (file_exists($Path)) {
                    unlink($Path);
                }
            }
            $config = array();
            $profile_image = time();
            $config['upload_path'] = $upload_path;
            $config['overwrite'] = FALSE;
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
//            $config['max_width'] = 750;
//            $config['max_height'] = 400;
//            $config['min_width'] = 750;
//            $config['min_height'] = 400;
            $config['file_name'] = $profile_image;
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('profile_image')) {
                echo $error = $this->upload->display_errors();
                //$this->index($error);
                //return ;
                exit;
            }
            $ImgArrays = explode('.', $_FILES['profile_image']['name']);
            $profile_image .="." . $ImgArrays[1];
        } else {

            if ($profile_image != "" && $this->input->post('RemoveWrkImage')) {
                $Path = $upload_path . $profile_image;
                if (file_exists($Path)) {
                    unlink($Path);
                    $profile_image = '';
                }
            }
        }
        //$this->db->where('username', $this->mw_session['username']);
        //$query = $this->db->get('users');
        $now = date('Y-m-d H:i:s');
        $image_data = array(
            'avatar' => $profile_image,
            'modifieddate' => $now,
            'modifiedby' => $this->mw_session['user_id'],
        );
        $_SESSION['awarathon_session']['avatar'] = $asset_url . 'assets/uploads/avatar/' . $profile_image;
        if ($this->mw_session['login_type'] == 3) {
            $this->common_model->update('device_users', 'user_id', $this->mw_session['user_id'], $image_data);
        } elseif ($this->mw_session['login_type'] == 2) {
            $this->common_model->update('users', 'userid', $this->mw_session['user_id'], $image_data);
        } else {
            $this->common_model->update('users', 'userid', $this->mw_session['user_id'], $image_data);
        }
        $this->session->set_flashdata('flash_message', "User Profile updated Successfully.");
        redirect('profile');
    }

    public function submit() {
//        echo "<pre>";
//        print_r($_POST);exit;        
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required');

        if ($this->mw_session['login_type'] == 3) {
            $this->form_validation->set_rules('login_id', 'Login ID', 'trim|required|valid_email');
        } else {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('login_id', 'Login ID', 'trim|required');
        }

        //$this->form_validation->set_rules('address1', 'Address 1', 'required');        
        // $this->form_validation->set_rules('country_id', 'Country', 'required');
        // $this->form_validation->set_rules('state_id', 'State', 'required');
        //$this->form_validation->set_rules('city_id', 'City', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $Login_id = $this->input->post('login_id');
            $Compnay_id = $this->mw_session['company_id'];
            if ($this->mw_session['login_type'] == 3) {
                $DuplicateFlag = $this->common_model->DuplicateEmail($Login_id, $Compnay_id, $this->mw_session['user_id']);
            } elseif ($this->mw_session['login_type'] == 2) {
                $this->load->model('company_users_model');
                $DuplicateFlag = $this->company_users_model->check_Login_id($Login_id, $this->mw_session['user_id'], $Compnay_id);
            } else {
                $this->load->model('users_model');
                $DuplicateFlag = $this->users_model->check_user($Login_id, $this->mw_session['user_id']);
            }
            if ($DuplicateFlag) {
                $Message = "Login ID already exists!!!";
                $this->session->set_flashdata('flash_message', $Message);
            } else {
                $now = date('Y-m-d H:i:s');
                $first_name = ucfirst(strtolower($this->input->post('first_name')));
                $LastName = ucfirst(strtolower($this->input->post('last_name')));
                if ($this->mw_session['login_type'] == 3) {
                    $data = array(
                        'email' => $Login_id,
                        'firstname' => $first_name,
                        'lastname' => $LastName,
                        'mobile' => $this->input->post('mobile'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                    );
                    $this->common_model->update('device_users', 'user_id', $this->mw_session['user_id'], $data);
                } elseif ($this->mw_session['login_type'] == 2) {
                    $data = array(
                        'username' => $Login_id,
                        'first_name' => $first_name,
                        'last_name' => $LastName,
                        'mobile' => $this->input->post('mobile'),
                        'email' => $this->input->post('email'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                    );
                    $this->common_model->update('company_users', 'userid', $this->mw_session['user_id'], $data);
                } else {
                    $data = array(
                        'username' => $Login_id,
                        'first_name' => $first_name,
                        'last_name' => $LastName,
                        'mobile' => $this->input->post('mobile'),
                        'email' => $this->input->post('email'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                    );
                    $this->common_model->update('users', 'userid', $this->mw_session['user_id'], $data);
                }
                $_SESSION['awarathon_session']['first_name'] = $first_name;
                $_SESSION['awarathon_session']['last_name'] = $LastName;
                //echo $this->mw_session['user_id'];
                $this->session->set_flashdata('flash_message', "Profile updated successfully.");
            }
            redirect('profile');
        }
    }

    public function ChangePassword() {
        $SuccessFlag = 1;
        $Message = '';
        $this->load->library('form_validation');
        //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
        $this->form_validation->set_rules('oldpassword', 'Old Password', 'callback_validateOLDPWD');
        $this->form_validation->set_rules('newpassword', 'New Password', 'trim|required|max_length[15]');
        $this->form_validation->set_rules('confirmpassword', 'Confirm Password', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            $confirmpwd = $this->input->post('confirmpassword');
            $data = array(
                'password' => $this->common_model->encrypt_password($confirmpwd),
                'modifieddate' => date('Y-m-d H:i:s'),
                'modifiedby' => $this->mw_session['user_id'],
            );
            if ($this->mw_session['login_type'] == 3) {
                $this->common_model->update('device_users', 'user_id', $this->mw_session['user_id'], $data);
            } elseif ($this->mw_session['login_type'] == 2) {
                $this->common_model->update('company_users', 'userid', $this->mw_session['user_id'], $data);
            } else {
                $this->common_model->update('users', 'userid', $this->mw_session['user_id'], $data);
            }
            $Message = "Password Change successfully.";
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function validateOLDPWD($str) {
        $this->load->library('form_validation');
        $oldpwd = $this->security->xss_clean($str);
        if ($this->mw_session['login_type'] == 3) {
            $db_password = $this->common_model->get_value('device_users', 'password', "user_id=" . $this->mw_session['user_id']);
        } elseif ($this->mw_session['login_type'] == 2) {
            $db_password = $this->common_model->get_value('company_users', 'password', "userid=" . $this->mw_session['user_id']);
        } else {
            $db_password = $this->common_model->get_value('users', 'password', "userid=" . $this->mw_session['user_id']);
        }
        if ($db_password == '') {
            $this->form_validation->set_message('validateOLDPWD', 'your current password does not match');
            return false;
        }
        if ($this->common_model->decrypt_password($oldpwd, $db_password->password) != 1) {
            $this->form_validation->set_message('validateOLDPWD', 'your current password does not match');
            return false;
        } else {
            return true;
        }
    }

    public function check_loginid() {
        $login_id = $this->input->post('login_id', true);
        $user_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $this->load->model('users_model');
            echo $this->users_model->check_user($login_id, $user_id);
        } else {
            $company_id = $this->mw_session['company_id'];
            $this->load->model('company_users_model');
            echo $this->company_users_model->check_Login_id($login_id, $user_id, $company_id);
        }
    }

}
