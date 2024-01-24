<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Division_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query  = "SELECT m.id,m.division_name,m.status,m.company_id,c.company_name FROM division_mst m left join company c"
                . " on c.id=m.company_id $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet']        = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);
        $query  = "SELECT COUNT(m.id) as total,c.company_name FROM division_mst as m left join company c "
                . " on c.id=m.company_id $dtWhere ";
        $result = $this->db->query($query);
        $data_array             = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function find_by_id($id) {
        $query = "select a.id,a.division_name,a.status,a.company_id,b.company_name FROM division_mst a left join company b "
                . " on a.company_id=b.id where a.deleted=0 and a.id=$id";
        $result = $this->db->query($query);
        $output = $result->result_array();
        return $output;
    }
    public function validate($data){
        $status = "false";
        if(count((array)$data) > 0 ){
            $id = base64_decode($data['id']);
        }
        if ($id==''){
            $query = $this->db->query("select count(*) as found from question_topic where deleted='0' and description='".$data['description']."'");
        }else{
            $query = $this->db->query("select count(*) as found from question_topic where deleted='0' and description='".$data['description']."' and id!='".$id."'");
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
    public function CrosstableValidation($ID) {
        $sQuery = "SELECT id FROM assessment_mst WHERE division_id= $ID";
        $query = $this->db->query($sQuery);
        $flag = (count((array)$query->row()) >0 ? false:true);
        if ($flag) {
            $sQuery = "SELECT userid FROM company_users WHERE division_id= $ID";
            $query = $this->db->query($sQuery);
            $flag = (count((array)$query->row()) >0 ? false:true);
        }
        return $flag;
    }
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('division_mst');
        //echo $this->db->last_query();
        return true;
    }
    
    public function CheckDivisionName($division_name,$cmp_id,$id) {        
        if ($id==''){
           $lcsqlstr = "Select division_name from division_mst where division_name='".$division_name."' and company_id=".$cmp_id;
        }else{
           $lcsqlstr = "Select division_name from division_mst where division_name='".$division_name."' "
                   . " and company_id=".$cmp_id." and id!=".$id;
        }
        
        $query = $this->db->query($lcsqlstr);
        return $result = $query->row();
    }
    
//    public function check_region_name($region_name) {
//        
//        $querystr = "Select region_name from region where region_name='" . $region_name . "'";
//        
//        $query    = $this->db->query($querystr);        
//        return (count((array)$query->row()) > 0 ? true : false);
//    }
}
