<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Roles_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function fetch_access_data() {
        $query = "SELECT * FROM access_modules WHERE status='1' ORDER BY module_sort,sortorder";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT a.arid,a.rolename,a.description,a.status,count(b.userid) as usercount FROM access_roles as a
                LEFT JOIN users as b  on b.role=a.arid ";
        $query .= "$dtWhere group By a.arid $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(a.arid) as total  FROM access_roles as a";
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
    public function check_role($role,$role_id='') {
         
        $querystr="Select rolename from access_roles where rolename='" . $role . "'";
        if($role_id!=''){
            $querystr.=" and arid!=".$role_id;
        }     
        $query = $this->db->query($querystr);  
        return (count((array)$query->row()) > 0 ? true : false);
    }
    public function submit_role() {
        $session_data = $this->session->userdata('awarathon_session');
        $now = date('Y-m-d H:i:s');
        $data = array(
            'rolename' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'status' => $this->input->post('status'),
            'addeddate' => $now,
            'addedby' => $session_data['user_id'],
            'deleted' => 0);
        $this->db->insert('access_roles', $data);
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
                $this->db->insert('access_role_modules', $data);
            }
        }
        return true;
    }
    public function update_role($id) {
        $session_data = $this->session->userdata('awarathon_session');
        $now = date('Y-m-d H:i:s');
        $data = array(
            'rolename' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'status' => $this->input->post('status') ,
            'addeddate' => $now,
            'addedby' => $session_data['user_id'],
            'deleted' => 0);
        $this->db->where('arid', $id);
        $this->db->update('access_roles', $data);

        $this->db->where('roleid', $id);
        $this->db->delete('access_role_modules');
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
                $this->db->insert('access_role_modules', $data);
            }
        }
        return true;
    }
    public function remove_role($id){
        $this->db->where('arid', $id);
        $this->db->delete('access_roles');

        $this->db->where('roleid', $id);
        $this->db->delete('access_role_modules');

        return true;
    }
    public function CheckUserAssignRole($roleID) {
        $sQuery = "SELECT userid FROM users WHERE role= $roleID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function validate($data){
        $status = "false";
        $query = $this->db->query("select count(*) as found from access_roles where status='1' and deleted='0' and rolename='".$data['name']."'");
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
        $query = $this->db->query("select count(*) as found from access_roles where status='1' and deleted='0' and rolename='".$data['name']."' and arid!='".base64_decode(urldecode($data['id']))."'");
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
}
