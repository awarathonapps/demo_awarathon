<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Users_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function fetch_access_data() {
        $query = "SELECT * FROM access_modules WHERE status='1' ORDER BY module_sort,sortorder";
        $result = $this->db->query($query);
        return $result->result();
    }
    // public function fetch_roles_data() {
    //     $query = "SELECT * FROM access_roles WHERE status='1' ORDER BY rolename";
    //     $result = $this->db->query($query);
    //     return $result->result();
    // }
    public function fetch_user($id) {
        $query = "select u.*,c.description as country_name,s.description as state_name,ct.description as city_name,ar.rolename from users as u LEFT JOIN country AS c ON c.id = u.country LEFT JOIN state AS s ON s.id = u.state LEFT JOIN city AS ct ON ct.id = u.city LEFT JOIN access_roles AS ar ON ar.arid = u.role where u.userid='" . $id . "' and u.deleted=0";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function fetch_roles_data($data) {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit="";
        if((isset($data['search']) AND isset($data['search']['term'])) || (isset($data['id']) && is_numeric($data['id'])))
        {
            if(isset($data['search']))
            {
                $getVar = strip_tags(trim($data['search']['term'])); 
                $whereClause =  " rolename LIKE '%" . $getVar ."%' ";
                $limit = 'LIMIT '.intval($data['page_limit']);
            }
            elseif(isset($data['id']))
            {
                $getVar = strip_tags(trim($data['id'])); 
                $whereClause =  " arid = $getVar ";
            }
            
            
            $query = "SELECT arid,rolename FROM access_roles WHERE status='1' and $whereClause ORDER BY rolename $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['arid'];
                $row_array['text'] = utf8_encode(ucfirst(strtolower($value['rolename'])));
                array_push($return_arr,$row_array);
            }
        }else{
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr,$row_array);
        }    
            
        $ret = array();
        if(isset($data['id'])){
            $ret = $row_array;
        }else{
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }
    public function fetch_country_data($data) {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit="";
        if((isset($data['search']) AND isset($data['search']['term'])) || (isset($data['id']) && is_numeric($data['id'])))
        {
            if(isset($data['search']))
            {
                $getVar = strip_tags(trim($data['search']['term'])); 
                $whereClause =  " description LIKE '%" . $getVar ."%' ";
                $limit = 'LIMIT '.intval($data['page_limit']);
            }
            elseif(isset($data['id']))
            {
                $getVar = strip_tags(trim($data['id'])); 
                $whereClause =  " id = $getVar ";
            }
            
            
            $query = "SELECT id,description FROM country WHERE status='1' and $whereClause ORDER BY description $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] = utf8_encode(ucfirst(strtolower($value['description'])));
                array_push($return_arr,$row_array);
            }
        }else{
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr,$row_array);
        }    
            
        $ret = array();
        if(isset($data['id'])){
            $ret = $row_array;
        }else{
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }
    public function fetch_state_data($data) {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit="";
        if((isset($data['search']) AND isset($data['search']['term'])) || (isset($data['id']) && is_numeric($data['id'])))
        {
            if(isset($data['search']))
            {
                $getVar = strip_tags(trim($data['search']['term'])); 
                $getCountryId = strip_tags(trim($data['country_id']));
                $whereClause =  "country_id='".$getCountryId."' AND  description LIKE '%" . $getVar ."%' ";
                $limit = 'LIMIT '.intval($data['page_limit']);
            }
            elseif(isset($data['id']))
            {
                $getVar = strip_tags(trim($data['id'])); 
                $whereClause =  " id = $getVar ";
            }
            
            
            $query = "SELECT id,description FROM state WHERE status='1' and $whereClause ORDER BY description $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] =  utf8_encode(ucfirst(strtolower($value['description'])));
                array_push($return_arr,$row_array);
            }
        }else{
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr,$row_array);
        }    
            
        $ret = array();
        if(isset($data['id'])){
            $ret = $row_array;
        }else{
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }
    public function fetch_city_data($data) {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit="";
        if((isset($data['search']) AND isset($data['search']['term'])) || (isset($data['id']) && is_numeric($data['id'])))
        {
            if(isset($data['search']))
            {
                $getVar = strip_tags(trim($data['search']['term'])); 
                $getstateId = strip_tags(trim($data['state_id']));
                $whereClause =  "state_id='".$getstateId."' AND  description LIKE '%" . $getVar ."%' ";
                $limit = 'LIMIT '.intval($data['page_limit']);
            }
            elseif(isset($data['id']))
            {
                $getVar = strip_tags(trim($data['id']));
                $whereClause =  " id = $getVar ";
            }
            
            $query = "SELECT id,description FROM city WHERE status='1' and $whereClause ORDER BY description $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] =  utf8_encode(ucfirst(strtolower($value['description'])));
                array_push($return_arr,$row_array);
            }
        }else{
            $row_array['id'] = 0;
            $row_array['text'] = utf8_encode('Start Typing....');
            array_push($return_arr,$row_array);
        }    
            
        $ret = array();
        if(isset($data['id'])){
            $ret = $row_array;
        }else{
            $ret['results'] = $return_arr;
        }
        echo json_encode($ret);
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT u.*,ar.rolename,ar.status,CONCAT(u.first_name,' ',u.last_name) as name FROM users as u left join access_roles as ar on u.role=ar.arid ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(u.userid) as total FROM users as u left join access_roles as ar on u.role=ar.arid";
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
    public function remove($id){
        $this->db->where('userid', $id);
        $this->db->delete('users');
        return true;
    }
    public function CheckUserAssignRole($roleID) {
        $sQuery = "SELECT userid FROM users WHERE role= $roleID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function validate($data){
        $status = "false";
        $query = $this->db->query("select count(*) as found from users where status='1' and deleted='0' and username='".$data['loginid']."'");
        foreach ($query->result() as $row)
        {
            if ($row->found>0){
                $status = "false";
            }else{
                $status = "true";
            }
        }
        return $status;
    }
    public function validate_edit($data){
        $status = "false";
        $query = $this->db->query("select count(*) as found from users where status='1' and deleted='0' and username='".$data['loginid']."' and userid!='".base64_decode(urldecode($data['user_id']))."'");
        foreach ($query->result() as $row)
        {
            if ($row->found>0){
                $status = "false";
            }else{
                $status = "true";
            }
        }
        return $status;
    }
     public function check_email($emailid,$user='') {
        $querystr="Select email from users where email='" . $emailid . "'";
//        if($cmp_id!=''){
//            $querystr.=" and company_id=".$cmp_id;
//        }
        if($user!=''){
            $querystr.=" and userid!=".$user;
        }        
        $query = $this->db->query($querystr);
        return (count((array)$query->row()) > 0 ? true : false);
    }
    public function check_user($login_name, $id='') {    
        $query = "Select username from users  where username='" . $login_name ."' and deleted=0 ";
        if($id<>''){
            $query .= " AND  userid!= " . $id ;
        }
        $result = $this->db->query($query);
        $ReturnFlag =false;
        if(count((array)$result->row())>0){
            $ReturnFlag =true;
        }else{
            $querystr="Select email from device_users where email like '" . $login_name . "'";
            $query = $this->db->query($querystr);
            if(count((array)$query->row())>0){
                $ReturnFlag =true;
            }
        }
    }
    public function check_firstlast($fname,$lname,$user='') {        
        $querystr="Select first_name,last_name from users where first_name='" . $fname . "' and last_name='".$lname."'";        
        if($user!=''){
            $querystr.=" and userid!=".$user;
        }        
        $query = $this->db->query($querystr);
        return (count((array)$query->row()) > 0 ? true : false);
    }
    public function SelectedCountry($user_id){
        $query = "SELECT c.id,c.description as country,ifnull(u.country,0) as u_id FROM country as c LEFT JOIN"
                . " users as u ON u.country=c.id AND c.status=1 AND u.userid=".$user_id;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function SelectedState($user_id){
        $query = "SELECT s.id,s.description as state_name,ifnull(u.state,0) as u_id FROM state as s LEFT JOIN"
                . " users as u ON u.state=s.id AND s.status=1 AND u.userid=".$user_id;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function SelectedCity($user_id){
        $query = "SELECT c.id,c.description as city_name,ifnull(u.city,0) as u_id FROM city as c LEFT JOIN"
                . " users as u ON u.city=c.id AND c.status=1 AND u.userid=".$user_id;
        $result = $this->db->query($query);
        return $result->result();
    }
}
