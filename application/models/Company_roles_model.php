<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Company_roles_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function fetch_access_data() {
        $query = "SELECT * FROM company_modules WHERE status='1' ORDER BY module_sort,sortorder";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT a.arid,a.rolename,a.description,a.status,c.company_name,count(b.userid) as usercount FROM company_roles as a
                LEFT JOIN company_users as b  on b.role=a.arid left join company c on c.id=a.company_id";
        $query .= " $dtWhere group By a.arid $dtOrder $dtLimit";
        //echo $query;exit;
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(a.arid) as total FROM company_roles as a 
                 left join company c on c.id=a.company_id";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function find_by_value($id) {
        $query = "Select ar.arid,ar.rolename,ar.description,ar.company_id,c.company_name,ar.status from company_roles as ar LEFT JOIN company as c ON c.id = ar.company_id where ar.deleted=0 and ar.arid=$id";
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
    public function submit_role() {
        $session_data = $this->session->userdata('awarathon_session');
        $now = date('Y-m-d H:i:s');
        $data = array(
            'company_id' => $this->input->post('company_id'),
            'rolename' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'status' => $this->input->post('status'),
            'addeddate' => $now,
            'addedby' => $session_data['user_id'],
            'deleted' => 0);
        $this->db->insert('company_roles', $data);
        $newid = $this->db->insert_id();
        $result = $this->fetch_access_data();
        foreach ($result as $checks) {
            $modulename = $checks->modulename;
            $moduleid = $checks->moduleid;
            $role_checks = $this->input->post($modulename . '_own');
            if (count((array)$role_checks) > 0) {
                $data = array(
                    'roleid' => $newid,
                    'moduleid' => $moduleid,
                    'allow_access' => 1,
                    'addeddate' => $now,
                    'addedby' => $session_data['user_id']
                );
                foreach ($role_checks as $rc) {
                    switch ($rc) {
                        case 2:
                            $data['allow_view'] = 1;
                            break;
                        case 3:
                            $data['allow_add'] = 1;
                            break;
                        case 4:
                            $data['allow_edit'] = 1;
                            break;
                        case 5:
                            $data['allow_delete'] = 1;
                            break;
                        case 6:
                            $data['allow_print'] = 1;
                            break;
                        case 7:
                            $data['allow_import'] = 1;
                            break;
                        case 8:
                            $data['allow_export'] = 1;
                            break;
                    }
                }
                $this->db->insert('company_role_modules', $data);
            }
        }
        return true;
    }
    public function update_role($id) {
        $session_data = $this->session->userdata('awarathon_session');
        $now = date('Y-m-d H:i:s');
        $data = array(
            'company_id' => $this->input->post('company_id'),
            'rolename' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'status' => $this->input->post('status') ,
            'addeddate' => $now,
            'addedby' => $session_data['user_id'],
            'deleted' => 0);
        $this->db->where('arid', $id);
        $this->db->update('company_roles', $data);

        $this->db->where('roleid', $id);
        $this->db->delete('company_role_modules');
        $result = $this->fetch_access_data();
        foreach ($result as $re) {
            $modulename = $re->modulename;
            $moduleid = $re->moduleid;
            $role_checks = $this->input->post($modulename . '_own');
            if (count((array)$role_checks) > 0) {
                $data = array(
                    'roleid' => $id,
                    'moduleid' => $moduleid,
                    'allow_access' => 1,
                    'addeddate' => $now,
                    'addedby' => $session_data['user_id']
                );
                foreach ($role_checks as $rc) {
                    switch ($rc) {
                        case 2:
                            $data['allow_view'] = 1;
                            break;
                        case 3:
                            $data['allow_add'] = 1;
                            break;
                        case 4:
                            $data['allow_edit'] = 1;
                            break;
                        case 5:
                            $data['allow_delete'] = 1;
                            break;
                        case 6:
                            $data['allow_print'] = 1;
                            break;
                        case 7:
                            $data['allow_import'] = 1;
                            break;
                        case 8:
                            $data['allow_export'] = 1;
                            break;
                    }
                }
                $this->db->insert('company_role_modules', $data);
            }
        }
        return true;
    }
    public function remove_role($id){
        $this->db->where('arid', $id);
        $this->db->delete('company_roles');

        $this->db->where('roleid', $id);
        $this->db->delete('company_role_modules');

        return true;
    }
    public function CheckUserAssignRole($roleID) {
        $sQuery = "SELECT userid FROM company_users WHERE role= $roleID";
        $query = $this->db->query($sQuery);
        $co_in_found = (count((array)$query->row()) >0 ? false:true);
        
        $sQuery = "SELECT userid FROM users WHERE role= $roleID";
        $query = $this->db->query($sQuery);
        $co_uin_found = (count((array)$query->row()) >0 ? false:true);
        
        if ($co_in_found ==false OR $co_uin_found==false ){
            return false;
        }else{
            return true;
        }
    }
    public function validate($data){
        $status = "false";
        $query = $this->db->query("select count(*) as found from company_roles where status='1' and deleted='0' and rolename='".$data['name']."'");
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
    
    public function check_role($role,$company_id='',$role_id='') {
         
        $querystr="Select rolename from company_roles where rolename='" . $role . "'";
        if($company_id!=''){
            $querystr.=" and company_id=".$company_id;
        }
        if($role_id!=''){
            $querystr.=" and arid!=".$role_id;
        }     
        $query = $this->db->query($querystr);  
        return (count((array)$query->row()) > 0 ? true : false);
    }
    
    public function validate_edit($data){
        $status = "false";
        $query = $this->db->query("select count(*) as found from company_roles where status='1' and deleted='0' and rolename='".$data['name']."' and arid!='".base64_decode(urldecode($data['id']))."'");
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
}
