<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Designation_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function TrainerLoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        if ($dtWhere!==''){
            $query = "SELECT s.*,cm.company_name FROM designation as s left join company as cm on cm.id=s.company_id $dtWhere AND s.deleted='0' $dtOrder $dtLimit";
        }else{
            $query = "SELECT s.*,cm.company_name FROM designation as s left join company as cm on cm.id=s.company_id WHERE s.deleted='0' $dtOrder $dtLimit";
        }
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        if ($dtWhere!==''){
            $query = "SELECT COUNT(s.id) as total FROM designation as s left join company as cm on cm.id=s.company_id $dtWhere AND s.deleted='0'";
        }else{
            $query = "SELECT COUNT(s.id) as total FROM designation as s left join company as cm on cm.id=s.company_id WHERE s.deleted='0'";
        }
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function trainer_find_by_id($id) {
        $query = "SELECT s.*,cmp.company_name FROM designation as s "
                . " left join company as cmp on cmp.id=s.company_id WHERE s.deleted='0' and s.id='".$id."'";
        $result = $this->db->query($query);
        $output = $result->result_array();
        return $output;
    }        
    public function TrainerCrosstableValidation($ID) {
        $sQuery = "SELECT userid FROM company_users WHERE designation_id= $ID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function trainer_remove($id){
        $this->db->where('id', $id);
        $this->db->delete('designation');
        return true;
    }    
    public function trainer_check_designation($designation, $cmp_id='',$designation_id='') {
        
        $querystr="Select description from designation where description='" . $designation . "'";
        if($cmp_id!=''){
            $querystr.=" and company_id=".$cmp_id;
        }        
        if($designation_id!=''){
            $querystr.=" and id!=".$designation_id;
        }       
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
    public function TraineeLoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        if ($dtWhere!==''){
            $query = "SELECT s.*,cm.company_name FROM designation_trainee as s left join company as cm on cm.id=s.company_id $dtWhere AND s.deleted='0' $dtOrder $dtLimit";
        }else{
            $query = "SELECT s.*,cm.company_name FROM designation_trainee as s left join company as cm on cm.id=s.company_id WHERE s.deleted='0' $dtOrder $dtLimit";
        }
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        if ($dtWhere!==''){
            $query = "SELECT COUNT(s.id) as total FROM designation_trainee as s left join company as cm on cm.id=s.company_id $dtWhere AND s.deleted='0'";
        }else{
            $query = "SELECT COUNT(s.id) as total FROM designation_trainee as s left join company as cm on cm.id=s.company_id WHERE s.deleted='0'";
        }
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function trainee_find_by_id($id) {
        $query = "SELECT s.*,cmp.company_name FROM designation_trainee as s "
                . " left join company as cmp on cmp.id=s.company_id WHERE s.deleted='0' and s.id='".$id."'";
        $result = $this->db->query($query);
        $output = $result->result_array();
        return $output;
    }        
    public function TraineeCrosstableValidation($ID) {
        $sQuery = "SELECT user_id FROM device_users WHERE designation_id= $ID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->result()) >0 ? false:true);
    }
    public function trainee_remove($id){
        $this->db->where('id', $id);
        $this->db->delete('designation_trainee');
        return true;
    }    
    public function trainee_check_designation($designation, $cmp_id='',$designation_id='') {
        
        $querystr="Select description from designation_trainee where description='" . $designation . "'";
        if($cmp_id!=''){
            $querystr.=" and company_id=".$cmp_id;
        }        
        if($designation_id!=''){
            $querystr.=" and id!=".$designation_id;
        }       
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
}
