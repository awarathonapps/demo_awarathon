<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Language_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query  = "SELECT m.id,m.name,m.status "
                . " FROM language_mst m "
                . " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet']        = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_cnt  = "SELECT COUNT(m.id) as total FROM language_mst as m "
                . " $dtWhere ";
        $result = $this->db->query($query_cnt);
        $data_result            = $result->row();
        $data['dtTotalRecords'] = $data_result->total;
        return $data;
    }
    public function find_by_id($id) {
        $query = "select a.id,a.name,a.status"
                . " FROM language_mst a "
                . " where a.id=$id";
        $result = $this->db->query($query);
        $output = $result->result_array();
        return $output;
    }
    public function CrosstableValidation($ID) {
        $sQuery = "SELECT language_id FROM device_users WHERE language_id = $ID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('language_mst');
        return true;
    }
    public function check_language($language_id,$Eid='') { 
        $querystr = "Select id from language_mst where name ='" . $language_id . "'";
                
        if($Eid!=''){
            $querystr.=" and id!=".$Eid;
        } 
        $query    = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
}
