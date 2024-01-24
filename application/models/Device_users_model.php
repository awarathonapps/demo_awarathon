<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Device_users_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function fetch_access_data() {
        $query = "SELECT * FROM access_modules WHERE status='1' ORDER BY module_sort,sortorder";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function fetch_user($id) {
        $query = "select u.*,c.company_name from device_users as u LEFT JOIN company AS c ON c.id = u.company_id  where u.user_id=" . $id;
        $result = $this->db->query($query);
        return $result->row();
    }

    public function user_device_info($user_id) {
        $query = " select di.id,di.app_name,di.package_name,di.version_number,di.model,di.platform,di.uuid,"
                . " di.imei,di.version,di.manufacturer,di.serial,di.version_code,di.info_dttm,di.isprimary_imei "
                . " from device_info as di where di.user_id=" . $user_id . " order by id desc";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT u.user_id,u.email,DATE_FORMAT(u.registration_date,'%d-%m-%Y') registration_date, cu.userid, cu.emp_id as l1_empid, IF(cu.first_name!='',CONCAT(cu.first_name,' ',cu.last_name),'') as l1_manager,cu2.emp_id as l2_empid,IF(cu2.first_name!='',CONCAT(cu2.first_name,' ',cu2.last_name),'') as l2_manager,
                rg.region_name, IF(u.designation='',dt.description,u.designation) as designation, u.emp_id,"
                . " u.status,CONCAT(u.firstname,' ',u.lastname) as name,u.otp,u.otp_last_attempt,"
                . " c.company_name FROM device_users as u 
                LEFT join company_users as cu on cu.userid = u.trainer_id 
                LEFT join company_users as cu2 on cu2.userid = u.trainer_id_i
                LEFT join company as c on u.company_id=c.id 
                LEFT JOIN region as rg on rg.id=u.region_id 
                LEFT JOIN designation_trainee as dt on dt.id=u.designation_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        // die($query);
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(u.user_id) as total FROM device_users as u 
        LEFT JOIN company as c on u.company_id=c.id LEFT join company_users as cu on cu.userid=u.trainer_id 
        LEFT JOIN company_users as cu2 on cu2.userid = u.trainer_id_i LEFT JOIN region as rg on rg.id=u.region_id 
        LEFT JOIN designation_trainee as dt on dt.id=u.designation_id ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }

    public function find_by_value($id) {
        $query = "Select ar.arid,ar.rolename,ar.description,ar.status from access_roles as ar where ar.deleted=0 and ar.arid=$id";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function find_by_id($id) {
        $query = "Select ar.arid,ar.rolename,ar.description,ar.status,arm.moduleid,arm.allow_access,arm.allow_add,arm.allow_view,arm.allow_edit,arm.allow_delete,arm.allow_print,arm.allow_import,arm.allow_export from access_roles as ar left join
           access_role_modules as arm on arm.roleid = ar.arid where ar.deleted=0 and ar.arid=$id";
        $result = $this->db->query($query);
        $output = $result->result_array();
        $resultdata = array();
        foreach ($output as $key => $value) {
            $resultdata[$value['moduleid']] = $value;
        }
        return $resultdata;
    }

    public function remove($id) {
        $this->db->where('user_id', $id);
        $this->db->delete('device_users');
        return true;
    }

    public function CheckUserAssignRole($roleID) {
        $sQuery = "SELECT userid FROM users WHERE role= $roleID";
        $query = $this->db->query($sQuery);
        return (count($query->row()) > 0 ? false : true);
    }

    public function validate($data) {
        $status = "false";
        $query = $this->db->query("select count(*) as found from users where status='1' and deleted='0' and username='" . $data['loginid'] . "'");
        foreach ($query->result() as $row) {
            if ($row->found > 0) {
                $status = "false";
            } else {
                $status = "true";
            }
        }
        return $status;
    }

    public function validate_edit($data) {
        $status = "false";
        $query = $this->db->query("select count(*) as found from users where status='1' and deleted='0' and username='" . $data['loginid'] . "' and userid!='" . base64_decode(urldecode($data['user_id'])) . "'");
        foreach ($query->result() as $row) {
            if ($row->found > 0) {
                $status = "false";
            } else {
                $status = "true";
            }
        }
        return $status;
    }

    public function check_email($emailid, $user = '', $cmp_id = "") {
        $querystr = "Select email from device_users where email='" . $emailid . "'";
        if ($cmp_id != '') {
            //$querystr.=" and company_id=".$cmp_id;
        }
        if ($user != '') {
            $querystr.=" and user_id !=" . $user;
        }
        $query = $this->db->query($querystr);
        return (count((array)$query->row()) > 0 ? true : false);
    }

    public function DuplicateEmail($email, $Company_id = '') {
        $query = "SELECT user_id,email FROM device_users where email LIKE '" . $email . "'";
        if ($Company_id != "") {
            $query .=" AND company_id=" . $Company_id;
        }
        // print_r($query);exit;
        $result = $this->db->query($query);
        return $result->result();
    }
    // Create by Bhautik Rana start
    public function DuplicateEmployeeCode_deviceuser($emp_id, $Company_id = '', $User_id = 0)
    {
        $query = "SELECT user_id,emp_id FROM device_users where emp_id LIKE '" . $emp_id . "'";
        if ($Company_id != "") {
            $query .= " AND company_id=" . $Company_id;
        }
        if ($User_id != 0) {
            $query .= " AND userid !=" . $User_id;
        }
        $result = $this->db->query($query);
        return $result->result();
    }
    public function DuplicateEmployeeCode($emp_id, $Company_id = '', $User_id = 0)
    {
        $query = "SELECT userid,emp_id FROM company_users where username LIKE '" . $emp_id . "'";
        if ($Company_id != "") {
            $query .= " AND company_id=" . $Company_id;
        }
        if ($User_id != 0) {
            $query .= " AND userid !=" . $User_id;
        }
        $result = $this->db->query($query);
        return $result->result();
    }
    // Create by Bhautik Rana end
    public function check_user($login_name, $id = '') {
        $query = "Select username from device_users where username='" . $login_name . "' and deleted=0 ";
        if ($id <> '') {
            $query .= " AND  userid!= " . $id;
        }
        $result = $this->db->query($query);
        return (count((array)$result->row()) > 0 ? true : false);
    }

    public function check_firstlast($fname, $lname, $user = '') {
        $querystr = "Select first_name,last_name from users where first_name='" . $fname . "' and last_name='" . $lname . "'";
        if ($user != '') {
            $querystr.=" and userid!=" . $user;
        }
        $query = $this->db->query($querystr);
        return (count((array)$query->row()) > 0 ? true : false);
    }

    public function SelectedCountry($user_id) {
        $query = "SELECT c.id,c.description as country,ifnull(u.country,0) as u_id FROM country as c LEFT JOIN"
                . " users as u ON u.country=c.id AND c.status=1 AND u.userid=" . $user_id;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function SelectedState($user_id) {
        $query = "SELECT s.id,s.description as state_name,ifnull(u.state,0) as u_id FROM state as s LEFT JOIN"
                . " users as u ON u.state=s.id AND s.status=1 AND u.userid=" . $user_id;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function SelectedCity($user_id) {
        $query = "SELECT c.id,c.description as city_name,ifnull(u.city,0) as u_id FROM city as c LEFT JOIN"
                . " users as u ON u.city=c.id AND c.status=1 AND u.userid=" . $user_id;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function ExportDeviceUsers($whereCond = "", $export_type = 1) {
        if ($export_type == 1) {
            $query = "SELECT u.user_id,u.firstname,u.lastname,u.emp_id,u.area,DATE_FORMAT(u.registration_date,'%d-%m-%Y') as registration_date, "
                    . "u.employment_year,u.education_background,u.department, "
                    . "u.region_id,u.email,u.mobile,u.status,u.istester,rg.region_name,dr.description as designation, "
                    . "cu.emp_id as l1_empid, IF(cu.first_name!='',CONCAT(cu.first_name,' ',cu.last_name),'') as l1_manager,cu2.emp_id as l2_empid,IF(cu2.first_name!='',CONCAT(cu2.first_name,' ',cu2.last_name),'') as l2_manager "
                    . "FROM device_users as u LEFT JOIN region as rg ON rg.id=u.region_id 
                    LEFT join company_users as cu on cu.userid = u.trainer_id LEFT join company_users as cu2 on cu2.userid = u.trainer_id_i
				 LEFT JOIN designation_trainee as dr ON dr.id=u.designation_id $whereCond order by firstname ";
        } else {
            $query = "SELECT u.user_id,u.firstname,u.lastname,u.emp_id,u.area,DATE_FORMAT(di.info_dttm,'%d-%m-%Y %h:%i %p') as info_dttm, DATE_FORMAT(u.registration_date,'%d-%m-%Y') as registration_date, "
                    . "u.employment_year,u.education_background,u.department,di.model,di.platform,di.imei,di.serial, "
                    . "u.region_id,u.email,u.mobile,u.status,u.istester,rg.region_name,dr.description as designation, "
                    . "cu.emp_id as l1_empid, IF(cu.first_name!='',CONCAT(cu.first_name,' ',cu.last_name),'') as l1_manager,cu2.emp_id as l2_empid,IF(cu2.first_name!='',CONCAT(cu2.first_name,' ',cu2.last_name),'') as l2_manager "
                    . "FROM device_users as u LEFT JOIN device_info as di ON di.user_id= u.user_id LEFT JOIN region as rg ON rg.id=u.region_id 
                    LEFT join company_users as cu on cu.userid = u.trainer_id LEFT join company_users as cu2 on cu2.userid = u.trainer_id_i
				LEFT JOIN designation_trainee as dr ON dr.id=u.designation_id $whereCond order by firstname,di.id desc  ";
        }
        $result = $this->db->query($query);
        return $result->result();
    }

    public function update_imei($id, $data, $user_id) {
        $this->db->where('id !=', $id);
        $this->db->where('user_id', $user_id);
        $this->db->update('device_info', $data);
        return true;
    }

    public function update_userdb2($id, $Company_id, $data2) {
        $this->common_db->where('user_id', $id);
        $this->common_db->where('block', 0);
        $this->common_db->where('company_id', $Company_id);
        $this->common_db->update('device_users', $data2);
        return true;
    }

    public function delete_user($Company_id,$id) {
        $this->db->where('user_id', $id)->delete('device_users');
		$this->common_db->where('company_id', $Company_id);
        $this->common_db->where('user_id', $id);
		$this->common_db->delete('device_users');
        return true;
    }

    public function count_max_active_user($start_date = '', $end_date ='', $company_id){
        $dwhere = '';
        if($start_date !='' && $end_date !=''){
            $dwhere = " AND date(system_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        }
        $query = "SELECT total_users, active_users, inactive_users FROM `device_users_statistics` 
                  WHERE company_id ='" . $company_id . "'".$dwhere. "
                  AND active_users =(SELECT MAX(active_users) FROM device_users_statistics 
                  WHERE 1=1".$dwhere.") order by id desc";
        $result = $this->db->query($query);
        if (count((array)$result->row()) == 0) {
            $query1 = "SELECT total_users, active_users, inactive_users FROM `device_users_statistics` 
                  WHERE company_id ='" . $company_id . "' order by id desc";
                  $result = $this->db->query($query1);
        }
        return $result->row();
    }
}
