<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Company_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function fetch_access_data() {
        $query = "SELECT * FROM access_modules WHERE status='1' ORDER BY module_sort,sortorder";
        $result = $this->db->query($query);
        return $result->result();
    }    
    public function fetch_data($id) {
        $query = "SELECT co.*,c.description AS country_name,s.description AS state_name,ct.description AS city_name,it.description as industry_name 
        FROM company AS co 
        LEFT JOIN country AS c ON c.id = co.country_id
        LEFT JOIN state AS s ON s.id = co.state_id
        LEFT JOIN city AS ct ON ct.id = co.city_id
        LEFT JOIN industry_type AS it ON it.id= co.industry_type_id
        WHERE
        co.id='" . $id . "'
        AND co.deleted = 0";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function fetch_industry_data($data) {
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
            
            
            $query = "SELECT id,description FROM industry_type WHERE status='1' and $whereClause ORDER BY description $limit";
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
        $query = "SELECT a.id,a.company_code,a.company_name,a.portal_name,a.status,it.description as industry_type FROM company as a"
                . " LEFT JOIN industry_type as it ON it.id=a.industry_type_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(a.id) as total FROM company as a"
                . " LEFT JOIN industry_type as it ON it.id=a.industry_type_id ";
        $query_count .="$dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }    
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('company');
        return true;
    }
    public function CheckUserAssignRole($roleID) {
        $sQuery = "SELECT userid FROM users WHERE role= $roleID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function coname_validate($data){
        $status = "false";
        if ($data['id']!==''){
            $id = base64_decode(urldecode($data['id']));
            $query = $this->db->query("select count(*) as found from company where status='1' and deleted='0' and id!='".$id."' and company_name='".$data['company_name']."'");
        }else{
            $query = $this->db->query("select count(*) as found from company where status='1' and deleted='0' and company_name='".$data['company_name']."'");
        }
        
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
    public function portal_validate($data){
        $status = "false";
        if ($data['id']!==''){
            $id = base64_decode(urldecode($data['id']));
            $query = $this->db->query("select count(*) as found from company where status='1' and deleted='0' and id!='".$id."' and portal_name='".$data['portal_name']."'");
        }else{
            $query = $this->db->query("select count(*) as found from company where status='1' and deleted='0' and portal_name='".$data['portal_name']."'");
        }
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
    public function check_company($company, $cmp_id='') {
        
        $querystr="Select company_name from company where company_name='" . $company . "'";
//        if($cmp_id!=''){
//            $querystr.=" and company_id=".$cmp_id;
//        }
        if($cmp_id!=''){
            $querystr.=" and id!=".$cmp_id;
        }
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
    public function check_portal($portal, $cmp_id='') {
        
        $querystr="Select portal_name from company where portal_name='" . $portal . "'";
//        if($cmp_id!=''){
//            $querystr.=" and company_id=".$cmp_id;
//        }
        if($cmp_id!=''){
            $querystr.=" and id!=".$cmp_id;
        }       
        $query = $this->db->query($querystr);        
        return (count($query->row()) > 0 ? true : false);
    }
    public function check_user($login_name, $id='') {
    
        $query = "Select username from users where username='" . $login_name ."' and deleted=0 ";
        if($id<>''){
            $query .= " AND  userid!= " . $id ;
        }
        $result = $this->db->query($query);
        return $output = $result->row();
    }
    public function CrosstableValidation($id) {
        $sQuery = "SELECT id FROM workshop_company WHERE company_id= $id";
        $query = $this->db->query($sQuery);
        $co_in_found = (count((array)$query->row()) >0 ? false:true);
        
        $sQuery = "SELECT arid FROM company_roles WHERE company_id= $id";
        $query = $this->db->query($sQuery);
        $co_inrole_found = (count((array)$query->row()) >0 ? false:true);

        $sQuery = "SELECT userid FROM company_users WHERE company_id= $id";
        $query = $this->db->query($sQuery);
        $co_inusers_found = (count((array)$query->row()) >0 ? false:true);
        
        if ($co_inrole_found ==false OR $co_inusers_found==false OR $co_in_found==false){
            return false;
        }else{
            return true;
        }
        //return (count((array)$query->row()) >0 ? false:true);
    }
    public function LoadUsersDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT u.user_id,u.company_id,CONCAT(u.firstname,' ',u.lastname) as name,u.emp_id,u.area,"
                . "u.email,u.mobile,u.otp,u.otp_last_attempt,u.status,u.istester "
                . "FROM device_users as u  ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(u.user_id) as total FROM device_users as u  ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function DuplicateEmployeeCode($emp_id,$Company_id='',$User_id=0) {
        $query = "SELECT user_id,emp_id FROM device_users where emp_id LIKE '".$emp_id."'";
        if($Company_id!=""){
            $query .=" AND company_id=".$Company_id;
        }
        if($User_id!=0){
            $query .=" AND user_id !=".$User_id;
        }
       // print_r($query);exit;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function ExportDeviceUsers($Company_id="",$idList="") {
        $query = "SELECT u.user_id,u.firstname,u.lastname,u.emp_id,u.area, "
                . "u.employment_year,u.education_background,u.department, "
                . "u.region_id,u.email,u.mobile,u.status,u.istester,rg.region_name "
                . "FROM device_users as u LEFT JOIN region as rg ON rg.id=u.region_id";
        if($Company_id !=""){
            $query .= " WHERE u.company_id= ".$Company_id;
            if($idList !=""){
                $query .= " AND u.user_id IN(".$idList.")";
            }
        }else{
            if($idList !=""){
                $query .= " Where u.user_id IN(".$idList.")";
            }
        }
        
        $result = $this->db->query($query);
        return $result->result();
    }
}
