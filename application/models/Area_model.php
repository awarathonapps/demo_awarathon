<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Area_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query  = "SELECT m.id,m.area_name,m.status,m.company_id,c.company_name FROM area m left join company c"
                . " on c.id=m.company_id $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet']        = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query  = "SELECT COUNT(m.id) as total,c.company_name FROM area as m left join company c "
                . " on c.id=m.company_id $dtWhere ";
        $result = $this->db->query($query);
        $data_array             = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function find_by_id($id) {
        $query = "select a.id,a.area_name,a.status,a.company_id,b.company_name FROM area a left join company b "
                . " on a.company_id=b.id where a.deleted=0 and a.id=$id";
        $result = $this->db->query($query);
        $output = $result->result_array();
        return $output;
    }
    
    public function CrosstableValidation($ID) {
        $sQuery = "SELECT id FROM workshop WHERE area_id= $ID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('area');
        //echo $this->db->last_query();
        return true;
    }
    
    public function CheckAreaName($area_name,$cmp_id,$id) {        
        if ($id==''){
           $lcsqlstr = "Select area_name from area where area_name='".$area_name."' and company_id=".$cmp_id;
        }else{
           $lcsqlstr = "Select area_name from area where area_name='".$area_name."' "
                   . " and company_id=".$cmp_id." and id!=".$id;
        }        
        $query = $this->db->query($lcsqlstr);
        return $result = $query->row();
    }
}
