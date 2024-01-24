<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Company_users_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function fetch_access_data() {
        $query = "SELECT * FROM company_modules WHERE status='1' ORDER BY module_sort,sortorder";
        $result = $this->db->query($query);
        return $result->result();
    }    
    public function fetch_user($id) {
        $query = "select u.*,c.description as country_name,s.description as state_name,ct.description as city_name,"
                . " ar.rolename,co.company_name from company_users as u LEFT JOIN country AS c ON c.id = u.country LEFT JOIN state AS s ON s.id = u.state LEFT JOIN city AS ct ON ct.id = u.city LEFT JOIN company_roles AS ar ON ar.arid = u.role LEFT JOIN company AS co ON co.id= u.company_id where u.userid='" . $id . "' and u.deleted=0";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function fetch_company_data($data) {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit="";
        if((isset($data['search']) AND isset($data['search']['term'])) || (isset($data['id']) && is_numeric($data['id'])))
        {
            if(isset($data['search']))
            {
                $getVar = strip_tags(trim($data['search']['term'])); 
                $whereClause =  " company_name LIKE '%" . $getVar ."%' ";
                $limit = 'LIMIT '.intval($data['page_limit']);
            }
            elseif(isset($data['id']))
            {
                $getVar = strip_tags(trim($data['id'])); 
                $whereClause =  " id = $getVar ";
            }
            
            
            $query = "SELECT id,company_name FROM company WHERE status='1' and $whereClause ORDER BY company_name $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] = utf8_encode(ucfirst(strtolower($value['company_name'])));
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
    public function fetch_roles_data($data) {
        ///print_r($cmp_id);exit;
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
            
            
            $query = "SELECT arid,rolename FROM company_roles WHERE status='1' and $whereClause ORDER BY rolename $limit";
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
    public function ExportUserData()
    {
         $query = "SELECT ar.rolename,u.status,u.userid,u.email,u.username as emp_code, u.username, u.addeddate,u.department,"
                . "CONCAT(u.first_name,' ',u.last_name) as name,co.company_name,d.description as designation,rg.region_name,u.emp_id"
                . " FROM company_users as u left join company_roles as ar on u.role=ar.arid "
                . "LEFT JOIN company AS co ON co.id= u.company_id "
                . " LEFT JOIN designation as d ON d.id=u.designation_id "
                . " LEFT JOIN region as rg ON rg.id=u.region_id ORDER BY u.userid DESC";
         $result = $this->db->query($query);
         return $result->result(); 
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT ar.rolename,u.status,u.userid,u.email,u.username as emp_code, u.username, u.addeddate,"
                . "CONCAT(u.first_name,' ',u.last_name) as name,co.company_name,d.description as designation,rg.region_name"
                . " FROM company_users as u left join company_roles as ar on u.role=ar.arid "
                . "LEFT JOIN company AS co ON co.id= u.company_id "
                . " LEFT JOIN designation as d ON d.id=u.designation_id "
                . " LEFT JOIN region as rg ON rg.id=u.region_id ";
        $query .= " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(u.userid) as total FROM company_users as u left join "
                . "company_roles as ar on u.role=ar.arid LEFT JOIN company AS co ON co.id= u.company_id"
                . " LEFT JOIN designation as d ON d.id=u.designation_id "
                . " LEFT JOIN region as rg ON rg.id=u.region_id ";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function find_by_value($id) {
        $query = "Select ar.arid,ar.rolename,ar.description,ar.status from company_roles as ar where ar.deleted=0 and ar.arid=$id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function find_by_id($id) {
        $query = "Select ar.arid,ar.rolename,ar.description,ar.status,arm.moduleid,arm.allow_access,arm.allow_add,arm.allow_view,arm.allow_edit,arm.allow_delete,arm.allow_print,arm.allow_import,arm.allow_export from company_roles as ar left join
        company_role_modules as arm on arm.roleid = ar.arid where ar.deleted=0 and ar.arid=$id";
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
        $this->db->delete('company_users');
        return true;
    }
    public function CheckUserAssignRole($roleID) {
        $sQuery = "SELECT userid FROM company_users WHERE role= $roleID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    
    public function check_email($emailid,$cmp_user='',$cmp_id="") {
        $querystr="Select email from company_users where email like '" . $emailid . "'";
        if($cmp_id!=''){
            $querystr.=" and company_id=".$cmp_id;
        }
        if($cmp_user!=''){
            $querystr.=" and userid!=".$cmp_user;
        }
        $query = $this->db->query($querystr);
        if(count((array)$query->row())>0){
            return true;
        }else{
            $querystr="Select user_id from device_users where email like '" . $emailid . "'";
            if($cmp_id!=''){
                $querystr.=" and company_id=".$cmp_id;
            }
            $query = $this->db->query($querystr);
            return (count((array)$query->row()) > 0 ? true : false);
        }
        
    }
    public function check_Login_id($login_id, $user_id='',$company_id="") {
        $querystr="Select username from company_users where username like '" . $login_id . "'";
        if($user_id!=''){
            $querystr.=" and userid!=".$user_id;
        }
        if($company_id!=''){
            $querystr.=" and company_id=".$company_id;
        }
        $query = $this->db->query($querystr);
        $ReturnFlag =false;
        if(count((array)$query->row())>0){
            $ReturnFlag =true;
        }else{
            $querystr="Select email from device_users where email like '" . $login_id . "' and status=1";
//            if($company_id!=''){
//                $querystr.=" and company_id=".$company_id;
//            }
            $query = $this->db->query($querystr);
            if(count((array)$query->row())>0){
                $ReturnFlag =true;
            }
        }
        return $ReturnFlag;
    }
    public function check_firstlast($fname,$lname,$cmp_id='',$cmp_user='') {        
        $querystr="Select first_name,last_name from company_users where first_name='" . $fname . "' and last_name='".$lname."'";
        if($cmp_id!=''){
            $querystr.=" and company_id=".$cmp_id;
        }
        if($cmp_user!=''){
            $querystr.=" and userid!=".$cmp_user;
        }        
        $query = $this->db->query($querystr);
        return (count((array)$query->row()) > 0 ? true : false);
    }
    public function validate($data){
        $status = "false";
        $query = $this->db->query("select count(*) as found from company_users where status='1' and deleted='0' and username='".$data['loginid']."'");
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
        $query = $this->db->query("select count(*) as found from company_users where status='1' and deleted='0' and username='".$data['loginid']."' and userid!='".base64_decode(urldecode($data['user_id']))."'");
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
    public function check_user($login_name, $id='') {
    
        $query = "Select username from company_users  where username='" . $login_name ."' and deleted=0 ";
        if($id<>''){
            $query .= " AND  userid!= " . $id ;
        }
        $result = $this->db->query($query);
        return $output = $result->row();
    }
    public function getUserrightsData($dtWhere, $dtOrder='', $dtLimit=''){
        
        $query = "SELECT u.userid,CONCAT(u.first_name,' ',u.last_name) as name,u.email,ar.rolename,"
                . "d.description as designation,r.region_name "
                . "FROM company_users as u left join company_roles as ar on u.role=ar.arid"
                . " LEFT JOIN designation as d ON d.id=u.designation_id "
                . " LEFT JOIN region r on r.id=u.region_id";
        $query .= " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(u.userid) as total FROM company_users as u "
                . "left join company_roles as ar on u.role=ar.arid "
                . " LEFT JOIN designation as d ON d.id=u.designation_id "
                . " LEFT JOIN region r on r.id=u.region_id";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function getUserWorkshopData($dtWhere, $dtOrder='', $dtLimit=''){
        
        $query = "SELECT u.id,u.workshop_name,DATE_FORMAT(u.start_date,'%d-%m-%Y') as start_date ,"
                . "DATE_FORMAT(u.end_date,'%d-%m-%Y') as end_date,wt.workshop_type,r.region_name "
                . "FROM workshop as u "
                . " LEFT JOIN workshoptype_mst wt on wt.id=u.workshop_type"
                . " LEFT JOIN region r on r.id=u.region";
        $query .= " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(u.id) as total FROM workshop as u "
                . " LEFT JOIN workshoptype_mst wt on wt.id=u.workshop_type"
                . " LEFT JOIN region r on r.id=u.region";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function getUserRegionWorkshopData($dtWhere, $dtOrder='', $dtLimit=''){
        
        $query = "SELECT u.id,u.region_name,count(w.id) as totalWorkshop FROM region as u LEFT JOIN workshop as w "
                . " ON w.region=u.id   ";
        $query .= " $dtWhere group by w.region $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(u.id) as total FROM workshop as u ";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function getSelectedTrainer($Company_id,$Edit_id) {
        $query = "SELECT a.userid,CONCAT(a.first_name,' ',a.last_name) as name,"
                . "ifnull(b.rightsuser_id,0) as rights FROM company_users as a LEFT JOIN cmsusers_rights as b "
                . " ON b.rightsuser_id=a.userid and b.userid=".$Edit_id.""
                . " WHERE a.status=1 AND a.company_id=".$Company_id;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getSelectedWorkshop($Company_id,$Edit_id) {
        $query = "SELECT a.id,a.workshop_name,"
                . "ifnull(b.workshop_id,0) as rights FROM workshop as a LEFT JOIN cmsusers_workshop_rights as b "
                . " ON b.workshop_id=a.id and b.userid=".$Edit_id.""
                . " WHERE a.status=1 AND a.company_id=".$Company_id;
        $result = $this->db->query($query);
        return $result->result();
    } 
    public function getWorkshopTypeRightset($Company_id,$Edit_id) {
        $query = "SELECT a.id,a.workshop_type	,"
                . " ifnull(b.workshop_type_id,0) as rights FROM workshoptype_mst as a LEFT JOIN cmsusers_wtype_rights as b"
                . " ON b.workshop_type_id=a.id and b.userid=".$Edit_id.""
                . " WHERE a.status=1 AND a.company_id=".$Company_id;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getWorkshopRegionRightset($Company_id,$Edit_id) {
        $query = "SELECT a.id,a.region_name,"
                . " ifnull(b.region_id,0) as rights FROM region as a LEFT JOIN cmsusers_wregion_rights as b"
                . " ON b.region_id=a.id and b.userid=".$Edit_id.""
                . " WHERE a.status=1 AND a.company_id=".$Company_id;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getTrainerRegionRightset($Company_id,$Edit_id) {
        $query = "SELECT a.id,a.region_name,"
                . " ifnull(b.region_id,0) as rights FROM region as a LEFT JOIN cmsusers_tregion_rights as b"
                . " ON b.region_id=a.id and b.userid=".$Edit_id.""
                . " WHERE a.status=1 AND a.company_id=".$Company_id;
        $result = $this->db->query($query);
        return $result->result();
    }
    public function DuplicateEmployeeCode($emp_id,$Company_id='',$User_id=0) {
        $query = "SELECT userid,emp_id FROM company_users where emp_id LIKE '".$emp_id."'";
        if($Company_id!=""){
            $query .=" AND company_id=".$Company_id;
        }
        if($User_id!=0){
            $query .=" AND userid !=".$User_id;
        }
        $result = $this->db->query($query);
        return $result->result();
    }
}
