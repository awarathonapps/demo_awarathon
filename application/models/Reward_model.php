<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Reward_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function fetch_access_data() {
        $query = "SELECT * FROM company_modules WHERE status='1' ORDER BY module_sort,sortorder";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function reward_data() {
        $query = "SELECT id,reward_name FROM reward where status=1";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function fetch_reward($id) {
        $query = "select *,DATE_FORMAT(start_date,'%d-%m-%Y')as start_date,DATE_FORMAT(end_date,'%d-%m-%Y')as end_date from reward where id='" . $id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT r.id,r.sponsor_name,r.reward_name,r.offer_code,r.quantity,r.status,c.company_name FROM reward r "
                . " left join company c on r.company_id=c.id ";
        $query .= " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(r.id) as total FROM reward r left join company c on r.company_id=c.id ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
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
    public function CheckUserAssignRole($roleID) {
        $sQuery = "SELECT userid FROM workshop WHERE role= $roleID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function check_code($code,$reward_id='',$cmp_id="") {
        
        $querystr="Select id from reward where offer_code='" . $code . "'";
        if($cmp_id!=''){
            $querystr.=" and company_id=".$cmp_id;
        }
        if($reward_id!=''){
            $querystr.=" and id!=".$reward_id;
        }
        $query = $this->db->query($querystr);
        //echo count((array)$query->row());exit;
        return (count((array)$query->row()) > 0 ? true : false);
    }
    public function validate($data){
        $status = "false";
        $query = $this->db->query("select count(*) as found from workshop where status='1' and deleted='0' and username='".$data['loginid']."'");
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
        $query = $this->db->query("select count(*) as found from workshop where status='1' and deleted='0' and username='".$data['loginid']."' and userid!='".base64_decode(urldecode($data['user_id']))."'");
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
    
        $query = "Select username from workshop  where username='" . $login_name ."' and deleted=0 ";
        if($id<>''){
            $query .= " AND  userid!= " . $id ;
        }
        $result = $this->db->query($query);
        return $output = $result->row();
    }
}
