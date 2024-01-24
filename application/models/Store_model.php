<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Store_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query  = "SELECT m.id,m.store_name,m.status,m.company_id,c.company_name "
                . " FROM store_mst m left join company c on c.id=m.company_id"
                . " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet']        = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query  = "SELECT COUNT(m.id) as total,c.company_name FROM store_mst as m "
                . " left join company c on c.id=m.company_id $dtWhere ";
        $result = $this->db->query($query);
        $data_array             = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function find_by_id($id) {
        $query = "select a.id,a.store_name,a.status,a.company_id,b.company_name "
                . " FROM store_mst a left join company b on b.id=a.company_id "
                . " where a.id=$id";
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
        $sQuery = "SELECT store_id FROM device_users WHERE store_id = $ID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('store_mst');
        //echo $this->db->last_query();
        return true;
    }
    public function check_store($store_id,$cmp_id,$Eid='') { 
//        $id=base64_decode($wtype_id);
        $querystr = "Select id from store_mst where store_name ='" . $store_id . "'"
                . " and company_id=".$cmp_id;
        if($Eid!=''){
            $querystr.=" and id!=".$Eid;
        } 
        $query    = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
}
