<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Common_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function CheckTableExist($tablename,$database_name){
        $sQuery = "SELECT Table_rows FROM information_schema.tables WHERE  table_name = '".$tablename."' and table_schema='".$database_name."'  LIMIT 1";
        $query = $this->db->query($sQuery);
        return $query->row();
    }
    public function check_user_rights($menu, $user_id, $Mode) {
        $apps_session = $this->mw_session;
        $login_type = $apps_session['login_type'];
        if ($login_type == 3) {
            $LcSqlStr = "SELECT a.roleid as role,a.allow_access,allow_add,allow_view,allow_edit,allow_delete,allow_print,allow_import,allow_export FROM company_role_modules a "
                    . "LEFT JOIN company_modules c ON a.moduleid=c.moduleid "
                    . " WHERE a.roleid= " . $apps_session['role'] . " AND  c.status=1 and c.modulename='" . $menu . "'";
        } else if ($apps_session['company_id'] == "") {
            $LcSqlStr = "SELECT b.role,a.allow_access,allow_add,allow_view,allow_edit,allow_delete,allow_print,allow_import,allow_export FROM access_role_modules a LEFT JOIN users b ON a.roleid =b.role "
                    . "LEFT JOIN access_modules c ON a.moduleid=c.moduleid  WHERE c.status=1 and c.modulename='" . $menu . "' AND b.userid=" . $user_id;
        } else {
            $LcSqlStr = "SELECT b.role,a.allow_access,allow_add,allow_view,allow_edit,allow_delete,allow_print,allow_import,allow_export FROM company_role_modules a LEFT JOIN company_users b ON a.roleid =b.role "
                    . "LEFT JOIN company_modules c ON a.moduleid=c.moduleid  WHERE c.status=1 and c.modulename='" . $menu . "' AND b.userid=" . $user_id;
        }
        $query = $this->db->query($LcSqlStr);
        $ResultSet = $query->row();
        if ($Mode <> '') {
            if (count((array)$ResultSet) > 0 && $ResultSet->$Mode == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return $ResultSet;
        }
    }

    function connect_db2() {
        return $this->load->database('common_db', TRUE);
    }

    public function insert($Table, $data) {
        $this->db->insert($Table, $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function insert_db2($Table, $data) {
        $this->common_db->insert($Table, $data);
        $insert_id = $this->common_db->insert_id();
        return $insert_id;
    }

    public function update($Table, $Column, $id, $data) {
        $this->db->where($Column, $id);
        $this->db->update($Table, $data);
        return true;
    }

    public function update_multiple($Table, $Clause, $data) {
        $LcSqlStr = "UPDATE " . $Table . " SET trainer_id=" . $data['trainer_id'] . ", topic_id=" . $data['topic_id'] . ", subtopic_id=" . $data['subtopic_id'] . " WHERE " . $Clause . " ";
        $query = $this->db->query($LcSqlStr);
        return true;
    }

    public function delete_whereclause($Table, $Clause) {
        $LcSqlStr = "DELETE FROM " . $Table . " WHERE " . $Clause . " ";
        $query = $this->db->query($LcSqlStr);
        return true;
    }

    public function delete($Table, $Column, $id) {
        $this->db->where($Column, $id)->delete($Table);
        return true;
    }

    public function selectall($Table) {
        $this->db->select('*')
                ->from($Table);
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function fetch_object_by_id($Table, $Column, $id) {
        $this->db->select('*')
                ->from($Table)
                ->where($Column, $id);
        $query = $this->db->get();
        $result = $query->row();
        return $result;
    }

    public function fetch_object_by_field($Table, $Column, $id) {
        $this->db->select('*')
                ->from($Table)
                ->where($Column, $id);
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function get_company() {
        $this->db->select('id ,company_name')
                ->from('reward')
                ->where('status', '1');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function get_sponsor() {
        $this->db->select('id ,sponsor_name')
                ->from('reward')
                ->where('status', '1');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function get_FeedbackForm() {
        $this->db->select('id ,form_name')
                ->from('feedback_form_header')
                ->where('status', '1');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function get_value($Table, $Column, $Clause) {
        $LcSqlStr = "SELECT " . $Column . " FROM " . $Table . " WHERE " . $Clause . " ";
        $query = $this->db->query($LcSqlStr);
        $row = $query->row();
        return $row;
    }

    public function get_selected_values($Table, $Column, $Clause, $OrderBy = "") {
        $LcSqlStr = "SELECT " . $Column . " FROM " . $Table . " WHERE " . $Clause . " ";
        if ($OrderBy != "") {
            $LcSqlStr .= " Order By " . $OrderBy;
        }
        $query = $this->db->query($LcSqlStr);
        $row = $query->result();
        return $row;
    }

    public function get_count($Table, $Column, $Clause) {
        $LcSqlStr = "SELECT " . $Column . " FROM " . $Table . " WHERE " . $Clause . " ";
        $query = $this->db->query($LcSqlStr);
        $row = $query->result();
        return count((array)$row);
    }

    public function ajax_genratepassword() {
        $length = 6;
        $randomString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, $length);
        return $randomString;
    }

    public function fetch_company_data($data) {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit = "";
        if ((isset($data['search']) AND isset($data['search']['term'])) || (isset($data['id']) && is_numeric($data['id']))) {
            if (isset($data['search'])) {
                $getVar = strip_tags(trim($data['search']['term']));
                $whereClause = " company_name LIKE '%" . $getVar . "%' ";
                $limit = 'LIMIT ' . intval($data['page_limit']);
            } elseif (isset($data['id'])) {
                $getVar = strip_tags(trim($data['id']));
                $whereClause = " id = $getVar ";
            }


            $query = "SELECT id,company_name FROM company WHERE status='1' and $whereClause ORDER BY company_name $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] = utf8_encode($value['company_name']);
                array_push($return_arr, $row_array);
            }
        } else {
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr, $row_array);
        }

        $ret = array();
        if (isset($data['id'])) {
            $ret = $row_array;
        } else {
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }

    public function fetch_question_set_data($data) {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit = "";
        if ((isset($data['search']) AND isset($data['search']['term'])) || (isset($data['id']) && is_numeric($data['id']))) {
            if (isset($data['search'])) {
                $getVar = strip_tags(trim($data['search']['term']));
                $whereClause = " title LIKE '%" . $getVar . "%' ";
                $limit = 'LIMIT ' . intval($data['page_limit']);
            } elseif (isset($data['id'])) {
                $getVar = strip_tags(trim($data['id']));
                $whereClause = " id = $getVar ";
            }


            $query = "SELECT id,title FROM question_set WHERE status='1' and $whereClause ORDER BY title $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] = utf8_encode($value['title']);
                array_push($return_arr, $row_array);
            }
        } else {
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr, $row_array);
        }

        $ret = array();
        if (isset($data['id'])) {
            $ret = $row_array;
        } else {
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }

    public function fetch_feedback_data() {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit = "";
        if ((isset($_GET['search']) AND isset($_GET['search']['term'])) || (isset($_GET['id']) && is_numeric($_GET['id']))) {
            if (isset($_GET['search'])) {
                $getVar = strip_tags(trim($_GET['search']['term']));
                $whereClause = " title LIKE '%" . $getVar . "%' ";
                $limit = 'LIMIT ' . intval($_GET['page_limit']);
            } elseif (isset($_GET['id'])) {
                $getVar = strip_tags(trim($_GET['id']));
                $whereClause = " id = $getVar ";
            }
            $query = "SELECT id,title FROM feedback WHERE status='1' and $whereClause ORDER BY title $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] = utf8_encode($value['title']);
                array_push($return_arr, $row_array);
            }
        } else {
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr, $row_array);
        }

        $ret = array();
        if (isset($_GET['id'])) {
            $ret = $row_array;
        } else {
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }

    public function fetch_reward_data() {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit = "";
        if ((isset($_GET['search']) AND isset($_GET['search']['term'])) || (isset($_GET['id']) && is_numeric($_GET['id']))) {
            if (isset($_GET['search'])) {
                $getVar = strip_tags(trim($_GET['search']['term']));
                $whereClause = " reward_name LIKE '%" . $getVar . "%' ";
                $limit = 'LIMIT ' . intval($_GET['page_limit']);
            } elseif (isset($_GET['id'])) {
                $getVar = strip_tags(trim($_GET['id']));
                $whereClause = " id = $getVar ";
            }
            $query = "SELECT id,reward_name FROM reward WHERE status='1' and $whereClause ORDER BY reward_name $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] = utf8_encode($value['reward_name']);
                array_push($return_arr, $row_array);
            }
        } else {
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr, $row_array);
        }

        $ret = array();
        if (isset($_GET['id'])) {
            $ret = $row_array;
        } else {
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }

    public function fetch_reward_company() {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit = "";
        if ((isset($_GET['search']) AND isset($_GET['search']['term'])) || (isset($_GET['id']) && is_numeric($_GET['id']))) {
            if (isset($_GET['search'])) {
                $getVar = strip_tags(trim($_GET['search']['term']));
                $whereClause = " company_name LIKE '%" . $getVar . "%' ";
                $limit = 'LIMIT ' . intval($_GET['page_limit']);
            } elseif (isset($_GET['id'])) {
                $getVar = strip_tags(trim($_GET['id']));
                $whereClause = " id = $getVar ";
            }
            $query = "SELECT id,company_name FROM reward WHERE status='1' and $whereClause ORDER BY company_name $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] = utf8_encode($value['company_name']);
                array_push($return_arr, $row_array);
            }
        } else {
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr, $row_array);
        }

        $ret = array();
        if (isset($_GET['id'])) {
            $ret = $row_array;
        } else {
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }

    public function fetch_roles_data($data) {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit = "";
        if ((isset($data['search']) AND isset($data['search']['term'])) || (isset($data['id']) && is_numeric($data['id']))) {
            if (isset($data['search'])) {
                $getVar = strip_tags(trim($data['search']['term']));
                $whereClause = " rolename LIKE '%" . $getVar . "%' ";
                $limit = 'LIMIT ' . intval($data['page_limit']);
            } elseif (isset($data['id'])) {
                $getVar = strip_tags(trim($data['id']));
                $whereClause = " arid = $getVar ";
            }


            $query = "SELECT arid,rolename FROM company_roles WHERE status='1' and $whereClause ORDER BY rolename $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['arid'];
                $row_array['text'] = utf8_encode(ucfirst(strtolower($value['rolename'])));
                array_push($return_arr, $row_array);
            }
        } else {
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr, $row_array);
        }

        $ret = array();
        if (isset($data['id'])) {
            $ret = $row_array;
        } else {
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }

    public function fetch_country_data($data) {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit = "";
        if ((isset($data['search']) AND isset($data['search']['term'])) || (isset($data['id']) && is_numeric($data['id']))) {
            if (isset($data['search'])) {
                $getVar = strip_tags(trim($data['search']['term']));
                $whereClause = " description LIKE '%" . $getVar . "%' ";
                $limit = 'LIMIT ' . intval($data['page_limit']);
            } elseif (isset($data['id'])) {
                $getVar = strip_tags(trim($data['id']));
                $whereClause = " id = $getVar ";
            }


            $query = "SELECT id,description FROM country WHERE status='1' and $whereClause ORDER BY description $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] = utf8_encode(ucfirst(strtolower($value['description'])));
                array_push($return_arr, $row_array);
            }
        } else {
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr, $row_array);
        }

        $ret = array();
        if (isset($data['id'])) {
            $ret = $row_array;
        } else {
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }

    public function fetch_state_data($data) {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit = "";
        if ((isset($data['search']) AND isset($data['search']['term'])) || (isset($data['id']) && is_numeric($data['id']))) {
            if (isset($data['search'])) {
                $getVar = strip_tags(trim($data['search']['term']));
                $getCountryId = strip_tags(trim($data['country_id']));
                $whereClause = "country_id='" . $getCountryId . "' AND  description LIKE '%" . $getVar . "%' ";
                $limit = 'LIMIT ' . intval($data['page_limit']);
            } elseif (isset($data['id'])) {
                $getVar = strip_tags(trim($data['id']));
                $whereClause = " id = $getVar ";
            }


            $query = "SELECT id,description FROM state WHERE status='1' and $whereClause ORDER BY description $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] = utf8_encode(ucfirst(strtolower($value['description'])));
                array_push($return_arr, $row_array);
            }
        } else {
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr, $row_array);
        }

        $ret = array();
        if (isset($data['id'])) {
            $ret = $row_array;
        } else {
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }

    public function fetch_city_data($data) {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit = "";
        if ((isset($data['search']) AND isset($data['search']['term'])) || (isset($data['id']) && is_numeric($data['id']))) {
            if (isset($data['search'])) {
                $getVar = strip_tags(trim($data['search']['term']));
                $getstateId = strip_tags(trim($data['state_id']));
                $whereClause = "state_id='" . $getstateId . "' AND  description LIKE '%" . $getVar . "%' ";
                $limit = 'LIMIT ' . intval($data['page_limit']);
            } elseif (isset($data['id'])) {
                $getVar = strip_tags(trim($data['id']));
                $whereClause = " id = $getVar ";
            }

            $query = "SELECT id,description FROM city WHERE status='1' and $whereClause ORDER BY description $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] = utf8_encode(ucfirst(strtolower($value['description'])));
                array_push($return_arr, $row_array);
            }
        } else {
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr, $row_array);
        }

        $ret = array();
        if (isset($data['id'])) {
            $ret = $row_array;
        } else {
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }

    function encrypt_password($password) {
        $salt = substr(md5(uniqid(rand(), true)), 0, 8);
        $hash = $salt . md5($salt . $password);
        return $hash;
    }

    function decrypt_password($password, $hash) {
        $salt = substr($hash, 0, 8);

        if ($hash == $salt . md5($salt . $password)) {
            return 1;
        } else {
            return 0;
        }
    }

    function getSubTopic($Topic_id) {
        $Query = "SELECT id,description FROM question_subtopic where status=1 AND topic_id= " . $Topic_id;
        $Obj = $this->db->query($Query);
        $ResultSet = $Obj->result();
        if (count((array)$ResultSet) == 0) {
            $Query = "SELECT id,description FROM question_subtopic where status=1 AND topic_id=0 and company_id=0";
            $Obj = $this->db->query($Query);
            $ResultSet = $Obj->result();
        }
        return $ResultSet;
    }

    function getFeedbackSubTopic($Topic_id) {
        $Query = "SELECT id,description FROM feedback_subtype where status=1 AND feedbacktype_id= " . $Topic_id;
        $Obj = $this->db->query($Query);
        $ResultSet = $Obj->result();
        if (count((array)$ResultSet) == 0) {
            $Query = "SELECT id,description FROM feedback_subtype where status=1 AND feedbacktype_id=0 and company_id=0";
            $Obj = $this->db->query($Query);
            $ResultSet = $Obj->result();
        }
        return $ResultSet;
    }

    public function DuplicateEmail($email, $Company_id = '', $User_id = 0) {
        $query = "SELECT user_id,email FROM device_users where email LIKE " . $this->db->escape($email);
        // $query = "SELECT user_id,email FROM device_users where email LIKE " . '".$email."';
       
        
//        if ($Company_id != "") {
//            $query .=" AND company_id=" . $Company_id;
//        }
        if ($User_id != 0) {
            $query .=" AND user_id !=" . $User_id;
        }
        $query .= " AND block = 0 ";
        $result = $this->common_db->query($query);
        return $result->result();
    }

    public function sendPhpMailer($Company_id, $toname, $toemail, $subject, $body, $attachment = '') {
        $emailData = array();
        if ($Company_id != "") {
            $emailData = $this->get_value('company_smtp', '*', 'status=1 and company_id=' . $Company_id);
        }
        if (count((array)$emailData) == 0) {
			if($this->common_db==null){
				$this->common_db = $this->common_model->connect_db2();
			}
            $query_obj = $this->common_db->query("select * from smtp where status=1");
            $emailData = $query_obj->row();
        }
		
        $Msg = "";
        if (count((array)$emailData) > 0) {
            $this->load->library('My_PHPMailer');
            $mail = new PHPMailer;
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $emailData->smtp_ipadress;
            $mail->SMTPAuth = $emailData->smtp_authentication;
            $mail->Username = $emailData->smtp_username;
            $mail->Password = $emailData->smtp_password;
            $mail->SMTPSecure = $emailData->smtp_secure;
            $mail->Port = $emailData->smtp_portno;
            $mail->XMailer = ' ';
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            //$mail->AddCustomHeader('Message-ID:'.$headers['Message-ID']);
            //$mail->AddCustomHeader('X-Mailer:'.$headers['X-Mailer']);

            $mail->addAddress($toemail, $toname);
            //$toemail
            $mail->isHTML(true);
            $mail->setFrom($emailData->smtp_username, $emailData->smtp_alias);
            if (is_array($toemail)) {
                foreach ($toemail as $email) {
                    $mail->addAddress($email, $toname);
                }
            } else {
                $mail->addAddress($toemail, $toname);
            }
            $mail->SMTPKeepAlive = true;
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $subject;
            $mail->Mailer = "smtp";
            $MailFlag = $mail->send();
            if (!$MailFlag) {
                $MailFlag=1;
                $Msg = 'Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            $MailFlag = 0;
            $Msg = "Your SMTP setting is in-Active or not set,contact administrator for technical support..";
        }

        $data['sendflag'] = $MailFlag;
        $data['Msg'] = $Msg;
        return $data;
    }

    //Our custom function.
    function generatePIN($digits = 4) {
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while ($i < $digits) {
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        $AlreadyExists = $this->get_value('company', 'id', "otp='" . $pin . "'");
        if (count((array)$AlreadyExists) > 0) {
            $this->generatePIN($digits);
        }
        return $pin;
    }

    public function getUserWorkshopList($Company_id = "", $user_id = "", $Workshop_Type = "", $region_id = "0") {
        $lcSqlStr = "select distinct a.workshop_id,b.workshop_name FROM workshop_registered_users a "
                . "LEFT JOIN workshop as b ON b.id=a.workshop_id where 1=1 ";
        if ($user_id != "") {
            $lcSqlStr .=" AND a.user_id=" . $user_id;
        }
        if ($Company_id != "") {
            $lcSqlStr .=" AND b.company_id=" . $Company_id;
        }
        if ($Workshop_Type != "" && $Workshop_Type != "0") {
            $lcSqlStr .=" AND b.workshop_type=" . $Workshop_Type;
        }
        if ($region_id != "" && $region_id != "0") {
            $lcSqlStr .=" AND b.region=" . $region_id;
        }
        $lcSqlStr .=" order by b.workshop_name ";
        //echo $lcSqlStr;
        $result = $this->db->query($lcSqlStr);
        return $result->result();
    }

    public function getWkshopRegRightsList($company_id, $trainer_id = "", $region_id = "", $workshoptype_id = "") {

        $query = "select id,id as workshop_id ,workshop_name FROM workshop Where status=1 AND company_id= $company_id ";

        if ($region_id != "") {
            $query .= " AND region =" . $region_id;
        }
        if ($workshoptype_id != "") {
            $query .= " AND workshop_type =" . $workshoptype_id;
        }
        if ($trainer_id != "") {
            $query .=" AND (id IN(select workshop_id FROM cmsusers_workshop_rights WHERE userid= $trainer_id )  "
                    . "  OR id IN(select distinct workshop_id FROM atom_results where trainer_id= $trainer_id )) order by workshop_name  ";
        }
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getTrainerWorkshop($company_id, $WRightsFlag = 1, $trainer_id = "0", $region_id = "0", $workshoptype_id = "0", $workshopsubtype_id = "0", $subregion_id = "0") {
        $lcSqlStr = "select distinct a.workshop_id ,b.workshop_name FROM workshop_questions as a "
                . "LEFT JOIN workshop as b ON b.company_id=a.company_id AND b.id=a.workshop_id where a.company_id=" . $company_id;
        if ($trainer_id != "0") {
            $lcSqlStr .=" AND a.trainer_id=" . $trainer_id;
        }
        if ($region_id != "0") {
            $lcSqlStr .= " AND b.region =" . $region_id;
        }
        if ($workshoptype_id != "0") {
            $lcSqlStr .= " AND b.workshop_type =" . $workshoptype_id;
        }
        if ($workshopsubtype_id != "0") {
            $lcSqlStr .= " AND b.workshopsubtype_id =" . $workshopsubtype_id;
        }
        if ($subregion_id != "0") {
            $lcSqlStr .= " AND b.workshopsubregion_id =" . $subregion_id;
        }
        if (!$WRightsFlag) {
            $login_id = $this->mw_session['user_id'];
            $lcSqlStr .=" AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights WHERE user_id= $login_id )";
        }
        $lcSqlStr .=" order by workshop_name ";
        //echo $lcSqlStr;exit;
        $result = $this->db->query($lcSqlStr);
        return $result->result();
    }

    public function TruncateTable($Table) {
        $this->db->query("TRUNCATE table $Table");
    }

    public function getMonthWeek($year = '', $month = '') {
        $result = '';
        if ($year != '' && $month != '') {
            $p = new DatePeriod(
                    DateTime::createFromFormat('!Y-n-d', "$year-$month-01"), new DateInterval('P1D'), DateTime::createFromFormat('!Y-n-d', "$year-$month-01")->add(new DateInterval('P1M'))
            );

            $datesByWeek = array();
            $WStartEndDate = array();
            $Week = 0;
            $WeekStr = '';
            $EndDate = '';
            $i = 0;
            foreach ($p as $d) {
                $i++;
                if ($d->format('W') != $WeekStr) {
                    if ($EndDate != "") {
                        $dateByWeek[$Week][] = $EndDate;
                    }
                    $Week++;
                    $WeekStr = $d->format('W');
                    $dateByWeek[$Week][] = $d->format('d');
                }
                $EndDate = $d->format('d');
            }
            $dateByWeek[$Week][] = $EndDate;
            foreach ($dateByWeek as $value) {
                $WStartEndDate[] = $value[0] . '-' . $value[1];
            }
            $result = $WStartEndDate;
        } else {
            $result = '';
        }
        return $result;
    }

    public function getUserRightsList($company_id, $CheckRights = 0, $Region_id = 0) {
        $query = "select userid,CONCAT(first_name, ' ' ,last_name) as fullname FROM company_users "
                . " Where status=1 AND company_id= $company_id "
                . "AND userid IN(SELECT distinct trainer_id FROM workshop_questions where company_id= $company_id ) ";
        if (!$CheckRights) {
            $trainer_id = $this->mw_session['user_id'];
            $query .=" AND (userid=$trainer_id OR userid IN(select trainer_id FROM temp_trights WHERE user_id= $trainer_id ) ) ";
        }
        if ($Region_id != '0') {
            $query .=" AND region_id=$Region_id ";
        }
        $query .=" order by fullname ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getWkshopFeedRightsList($company_id, $Wtype = '', $CheckRights = 0) {
        $trainer_id = $this->mw_session['user_id'];
        $query = "select distinct wf.workshop_id,w.workshop_name FROM workshop_feedback_questions as wf LEFT JOIN workshop as w "
                . " ON w.id=wf.workshop_id Where status=1 AND wf.company_id= $company_id ";
        if ($Wtype != '') {
            $query .= " AND w.workshop_type=" . $Wtype;
        }
        if (!$CheckRights) {
            $trainer_id = $this->mw_session['user_id'];
            $query .= " AND wf.workshop_id IN(select distinct workshop_id FROM temp_wrights WHERE user_id= $trainer_id )";
        }
        $query .=" order by workshop_name ";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function getWkshopRightsList($company_id) {
        $trainer_id = $this->mw_session['user_id'];
        $query = "select id,workshop_name FROM workshop "
                . " Where status=1 AND company_id= $company_id AND "
                . " id IN(select distinct workshop_id FROM temp_wrights WHERE user_id= $trainer_id ) order by workshop_name  ";
//       echo $query;exit;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getWTypeFeedRightsList($company_id, $CheckRights = 0) {
        $query = "select id,workshop_type FROM workshoptype_mst "
                . " Where status=1 AND  company_id= $company_id ";
        if (!$CheckRights) {
            $trainer_id = $this->mw_session['user_id'];
            $query .=" AND id IN(select distinct workshop_type FROM temp_wrights WHERE user_id= $trainer_id ) ";
        }
        $query .=" order by workshop_type ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getWTypeRightsList($company_id, $CheckRights = 0) {
        $query = "select id,workshop_type FROM workshoptype_mst "
                . " Where status=1 AND  company_id= $company_id ";
        if (!$CheckRights) {
            $trainer_id = $this->mw_session['user_id'];
            $query .=" AND id IN(select distinct workshop_type FROM temp_wrights WHERE user_id= $trainer_id ) ";
        }
        $query .=" order by workshop_type ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getUserRegionList($company_id, $CheckRights = 0) {
        $query = "select id,region_name FROM region "
                . " Where status=1 AND company_id= $company_id ";
        if (!$CheckRights) {
            $trainer_id = $this->mw_session['user_id'];
            $query .=" AND id IN(select distinct region_id FROM temp_wrights WHERE user_id= $trainer_id ) ";
        }
        $query .=" order by region_name ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getUserTraineeList($company_id, $CheckRights = 0, $Workshop_id = '') {
        $query = "select distinct ar.user_id,concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename FROM workshop_registered_users as ar "
                . " INNER JOIN device_users as du ON du.user_id=ar.user_id"
                . " LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ar.workshop_id AND wtu.tester_id=ar.user_id "
                . " Where du.company_id= $company_id AND wtu.tester_id IS NULL ";
        if (!$CheckRights) {
            $trainer_id = $this->mw_session['user_id'];
            $query .=" AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights WHERE user_id= $trainer_id ) ";
        }
        if ($Workshop_id != "") {
            $query .=" AND ar.workshop_id = $Workshop_id ";
        }
        $query .=" order by traineename ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getQusetData($company_id, $workshop_id) {
        $query = "SELECT qs.id,qs.title FROM workshop_questions wq LEFT JOIN question_set qs ON qs.id=wq.questionset_id AND qs.company_id=wq.company_id "
                . " WHERE wq.company_id = $company_id AND wq.workshop_id = $workshop_id GROUP BY qs.id";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function workshopwise_data($company_id, $workshop_id) {
        $LcSqlStr = "SELECT qt.id,qt.description as topic_name "
                . "FROM workshop_questions wq LEFT JOIN question_topic qt ON qt.id=wq.topic_id and qt.company_id=wq.company_id "
                . " where wq.company_id=" . $company_id . " and wq.workshop_id=" . $workshop_id . " group by qt.id ";
        $query = $this->db->query($LcSqlStr);
        $row = $query->result();
        return $row;
    }

    public function checkWorkshopType_rights($login_id, $Rights_id) {
        $result = $this->db->query("select workshop_type_id,all_flag FROM cmsusers_wtype_rights where userid= $login_id AND rights_id=$Rights_id");
        return $result->result();
    }

    public function checkWorkshop_rights($login_id) {
        $result = $this->db->query("select workshop_id FROM cmsusers_workshop_rights where userid= $login_id ");
        return (count((array)$result->row()) > 0 ? 1 : 0);
    }

    public function checkWRegion_rights($login_id) {
        $result = $this->db->query("select id,region_id,all_flag FROM cmsusers_wregion_rights where userid= $login_id");
        return $result->result();
    }

    public function checkspecifed_rights($login_id, $Rights_id) {
        $result = $this->db->query("select userid FROM cmsusers_rights where userid= $login_id AND rights_id=$Rights_id");
        return $result->row();
    }

    public function checkTRegion_rights($login_id) {
        $result = $this->db->query("select id,region_id FROM cmsusers_tregion_rights where userid= $login_id");
        return $result->result();
    }

    public function SyncTrainerRights($login_id) {

        $result = $this->db->query("select userid FROM company_users where region_id IN (select region_id 
                FROM cmsusers_tregion_rights where userid=$login_id) AND "
                . " userid NOT IN(select trainer_id FROM temp_trights where user_id= $login_id )");
        if (count((array)$result->row()) == 0) {
            return false;
        }
        $Rowset = $this->get_value('company_users', 'userrights_type', 'userid=' . $login_id);
        $TRightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
        if (!$TRightsFlag) {
            $this->delete_whereclause("temp_trights", "user_id=" . $login_id);
            $TRegion_rights = $this->checkTRegion_rights($login_id);
            $TotalRegion = count((array)$TRegion_rights);
            $lnsertStm = "INSERT INTO temp_trights (user_id,region_id,trainer_id) "
                    . "SELECT $login_id as user_id, w.region_id,w.userid FROM company_users as w WHERE w.userid != $login_id AND (";
            if ($TotalRegion > 0) {
                $i = 0;
                foreach ($TRegion_rights as $value) {
                    $i++;
                    $Rights_id = $value->id;
                    $lnsertStm .= " (w.region_id= " . $value->region_id . "";
                    $Specfied_rights = $this->checkspecifed_rights($login_id, $Rights_id);
                    if (count((array)$Specfied_rights) > 0) {
                        $lnsertStm .= " AND w.userid IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id AND rights_id=$Rights_id)";
                    }
                    if ($i != $TotalRegion) {
                        $lnsertStm .= ") OR ";
                    } else {
                        $lnsertStm .= ")";
                    }
                }
                $lnsertStm .= ")";
                $this->db->query($lnsertStm);
            }
        }

        return true;
    }

    public function SyncWorkshopRights($login_id, $DeleteFlag = 0) {
        if (!$DeleteFlag) {
            $result = $this->db->query("select distinct workshop_id FROM atom_results where trainer_id=$login_id"
                    . " AND workshop_id NOT IN(select workshop_id FROM temp_wrights where user_id= $login_id )");
            if (count((array)$result->row()) == 0) {
                $Checkset = $this->get_value('temp_wrights', 'user_id', 'user_id=' . $login_id);
                if (count((array)$Checkset) > 0) {
                    return false;
                }
            }
        }
        $Rowset = $this->get_value('company_users', 'userrights_type,workshoprights_type', 'userid=' . $login_id);
        $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
        if (!$WRightsFlag) {
            $this->delete_whereclause("temp_wrights", "user_id=" . $login_id);
            $WRegion_rights = $this->checkWRegion_rights($login_id);
            $TotalRegion = count((array)$WRegion_rights);
            $lnsertStm = "INSERT INTO temp_wrights (user_id,region_id,workshop_type,workshop_id) SELECT $login_id as user_id, w.region,w.workshop_type,w.id FROM workshop as w WHERE (";
            if ($TotalRegion > 0) {
                $i = 0;
                foreach ($WRegion_rights as $value) {
                    $i++;
                    $Rights_id = $value->id;
                    $lnsertStm .= " (w.region= " . $value->region_id . "";
                    if (!$value->all_flag) {
                        $WType_rights = $this->checkWorkshopType_rights($login_id, $Rights_id);
                        if (count((array)$WType_rights) > 0) {
                            foreach ($WType_rights as $value2) {
                                $workshop_type_id = $value2->workshop_type_id;
                                $lnsertStm .= " AND w.workshop_type= " . $workshop_type_id;
                                if (!$value2->all_flag) {
                                    $lnsertStm .= " AND w.id IN(select workshop_id FROM cmsusers_workshop_rights where userid= $login_id)";
                                }
                            }
                        }
                    }
                    if ($i != $TotalRegion) {
                        $lnsertStm .= ") OR ";
                    } else {
                        $lnsertStm .= ")";
                    }
                }
                $lnsertStm .= " OR w.id IN(select distinct workshop_id FROM atom_results where trainer_id= $login_id))";
            } else {
                $lnsertStm .= "  w.id IN(select distinct workshop_id FROM atom_results where trainer_id= $login_id))";
            }
            $this->db->query($lnsertStm);
        }

        return true;
    }
    public function get_users_value($Table, $Column, $Clause)
    {
        $LcSqlStr = "SELECT " . $Column . " FROM " . $Table . " WHERE " . $Clause . " ";
        $query = $this->db->query($LcSqlStr);
        $row = $query->result_array();
        return $row;
    }
    public function update_id($table,$name,$assessment_id,$u_id)
    {
        $LcSqlStr = "UPDATE ".$table." SET `send_mail`='1' WHERE ".$name."  in ('" . implode("', '", $u_id) . "') and assessment_id =".$assessment_id."";
        $query = $this->db->query($LcSqlStr);
        return true;
    }
    public function get_all_assessment()
    {
        $cur_dt = date('Y-m-d');
        $Lc = "SELECT am.id,am.assessment,am.end_dttm,am.start_dttm,DATEDIFF(am.end_dttm , '" . $cur_dt . "') as days_diff FROM assessment_mst as am WHERE am.end_dttm >='" . $cur_dt . "' ";
        $result = $this->db->query($Lc);
        return $result->result_array();
    }
    public function get_assessment_wise_users($table, $id="")
    {
        // $query = "SELECT * FROM ".$table." WHERE assessment_id = '$id'";
        $query = "SELECT * FROM $table WHERE user_id NOT IN (SELECT user_id FROM assessment_attempts WHERE assessment_id=$id AND is_completed=1) and assessment_id=$id";
        $result = $this->db->query($query);
        return $result->result_array();
    }
    
    public function get_assessment_wise_managers($table, $id="")
    {
        $query = "SELECT * FROM ".$table." WHERE assessment_id = '$id'";
        $result = $this->db->query($query);
        return $result->result_array();
    }

}

?>