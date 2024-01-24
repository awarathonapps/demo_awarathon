<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->model('common_model');
    }

    public function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 1)
            return $min;
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1;
        $bits = (int) $log + 1;
        $filter = (int) (1 << $bits) - 1;
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter;
        } while ($rnd > $range);
        return $min + $rnd;
    }

    public function getToken($length) {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet);
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max - 1)];
        }
        return $token;
    }

    public function validate($Company_id = "") {

        $username = $this->security->xss_clean($this->input->post('username'));
        $password = $this->security->xss_clean($this->input->post('password'));
        $token = $this->getToken(10);
        $ReturnFlag = false;
        $BackendUser = true;
        $asset_url = $this->config->item('assets_url');
        $Company_Name = "";
        $db_password = '';
        if ($Company_id != "") {
            $CompanyData = $this->common_model->get_value("company", "company_name,deviceuser_role", "id=" . $Company_id);
            $Company_Name = $CompanyData->company_name;
            $BackendUser = false;
        }
        //Superadmin Login Code Start
        $this->db->where('username', $username);
        $hash_query = $this->db->get('hash');
        $hash_result = $hash_query->row();
        if (count((array)$hash_result) > 0) {
            $db_password = $hash_result->password;
            if ($this->common_model->decrypt_password($password, $db_password) == 1) {
                $data = array(
                    'login_type' => 1,
                    'user_id' => '999999999',
                    'username' => $username,
                    'first_name' => 'Super',
                    'last_name' => 'Administrator',
                    'avatar' => $asset_url . "assets/uploads/avatar/no-avatar.jpg",
                    'role' => 1,
                    'user_type' => 1,
                    'company_id' => $Company_id,
                    'company_name' => $Company_Name,
                    'validated' => true,
                    'superaccess' => true,
                    'user_token' => $token,
					'dashboard' => 'assessment_dashboard',
                    'home' => 'home',
                );
                $this->session->set_userdata('awarathon_session', $data);
                $ReturnFlag = true;
            }
        }
        //Superadmin Login Code End
        if (!$ReturnFlag) {
            if ($Company_id == "") {
                $this->db->where('username', $username);
                $query = $this->db->get('users');
                $UserDataset = $query->row();
            } else {
                $this->db->where('username', $username);
                $this->db->where('status', 1);  //KRISHNA --- Restrict inactive CMS users to login to System
                $this->db->where('company_id', $Company_id);
                $query = $this->db->get('company_users');
                $UserDataset = $query->row();
                 // if (count((array)$UserDataset) == 0) {
                    // $this->db->where('email', $username);
                    // $this->db->where('company_id', $Company_id);
                    // $query = $this->db->get('device_users');
                    // $DeviceUserRowSet = $query->row();
                    // if (count((array)$DeviceUserRowSet) > 0) {
                        // $db_password = $DeviceUserRowSet->password;
                        // if ($this->common_model->decrypt_password($password, $db_password) == 1) {
                            // if ($DeviceUserRowSet->avatar != "") {
                                // $Avtar = $asset_url . 'assets/uploads/profile/' . $DeviceUserRowSet->avatar;
                            // } else {
                                // $Avtar = $asset_url . "assets/uploads/avatar/no-profile.jpg";
                            // }
                            // //$company_id = $row->company_id;
                            // $data = array(
                                // 'login_type' => 3, //trainee
                                // 'user_id' => $DeviceUserRowSet->user_id,
                                // 'username' => $DeviceUserRowSet->email,
                                // 'first_name' => ucwords($DeviceUserRowSet->firstname),
                                // 'last_name' => ucwords($DeviceUserRowSet->lastname),
                                // 'avatar' => $Avtar,
                                // 'role' => ($CompanyData->deviceuser_role != "0" ? $CompanyData->deviceuser_role : 2),
                                // 'user_type' => 0,
                                // 'company_id' => $Company_id,
                                // 'company_name' => $Company_Name,
                                // 'validated' => true,
                                // 'superaccess' => false,
                                // 'user_token' => $DeviceUserRowSet->user_id . $token
                            // );
                            // $this->session->set_userdata('awarathon_session', $data);
                            // $ReturnFlag = true;
                        // }
                    // }
                // }
            }
            if (!$ReturnFlag && isset($UserDataset) && count((array)$UserDataset) > 0) {
                $db_password = $UserDataset->password;
                if ($this->common_model->decrypt_password($password, $db_password) == 1) {
                    $row = $query->row();
                    if ($row->avatar != "") {
                        $Avtar = $asset_url . 'assets/uploads/avatar/' . $row->avatar;
                    } else {
                        $Avtar = $asset_url . "assets/uploads/avatar/no-profile.jpg";
                    }
                    $data = array(
                        'login_type' => ($BackendUser ? 0 : 2), //trainer
                        'user_id' => $row->userid,
                        'username' => $row->username,
                        'first_name' => $row->first_name,
                        'last_name' => $row->last_name,
                        'avatar' => $Avtar,
                        'role' => $row->role,
                        'user_type' => ($BackendUser ? 0 : $row->login_type),
                        'company_id' => $Company_id,
                        'company_name' => $Company_Name,
                        'validated' => true,
                        'superaccess' => false,
                        'user_token' => $row->userid . $token,
						'dashboard' => 'manager_dashboard',
                        'home' => 'home',
                    );
                    $this->session->set_userdata('awarathon_session', $data);
                    $ReturnFlag = true;
                }
            }
        }
        return $ReturnFlag;
    }
	public function temp_session($user_id,$Company_id,$company_name){
		$token = $this->getToken(10);
		$Rowset = $this->common_model->get_value('hash', 'id', "id='" .$user_id."'");
		$ReturnFlag = 0;
		if(count((array)$Rowset)>0){
			$data = array(
				'login_type' => 1,
				'user_id' => $Rowset->id,
				'username' => $Rowset->username,
				'first_name' => 'Super',
				'last_name' => 'Administrator',
				'avatar' => $asset_url . "assets/uploads/avatar/no-avatar.jpg",
				'role' => 1,
				'user_type' => 1,
				'company_id' => $Company_id,
				'company_name' => $company_name,
				'validated' => true,
				'superaccess' => true,
				'user_token' => $token
			);
			$ReturnFlag = 1;
			$this->session->set_userdata('awarathon_session', $data);
		}
		return $ReturnFlag;
	}
	public function validate_email($Company_id,$email){
		$this->db->where('email', $email);
		$this->db->where('company_id', $Company_id);
		$query = $this->db->get('company_users');
		return $query->row();
	}
}

?>