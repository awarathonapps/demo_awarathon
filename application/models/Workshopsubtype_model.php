<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Workshopsubtype_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        if ($dtWhere!==''){
            $query =  " SELECT s.*,c.workshop_type as workshop_type,cm.company_name "
                    . " FROM workshopsubtype_mst as s left JOIN workshoptype_mst as c ON s.workshoptype_id = c.id "
                    . " left join company as cm on cm.id=s.company_id $dtWhere AND s.deleted='0' $dtOrder $dtLimit";
        }else{
            $query = " SELECT s.*,c.workshop_type as workshop_type,cm.company_name "
                    . " FROM workshopsubtype_mst as s left JOIN workshoptype_mst as c ON s.workshoptype_id = c.id "
                    . " left join company as cm on cm.id=s.company_id WHERE s.deleted='0' $dtOrder $dtLimit";
        }
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        if ($dtWhere!==''){
            $query = "SELECT COUNT(s.id) as total FROM workshopsubtype_mst as s "
                    . " INNER JOIN workshoptype_mst as c ON s.workshoptype_id = c.id left join company as cm on cm.id=s.company_id "
                    . " $dtWhere AND s.deleted='0'";
        }else{
            $query = " SELECT COUNT(s.id) as total FROM workshopsubtype_mst as s "
                    . " INNER JOIN workshoptype_mst as c ON s.workshoptype_id = c.id "
                    . " left join company as cm on cm.id=s.company_id "
                    . " WHERE s.deleted='0'";
        }
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function find_by_id($id) {
        $query = " SELECT s.*,c.workshop_type as workshop_type,cmp.company_name "
                . " FROM workshopsubtype_mst as s LEFT JOIN workshoptype_mst as c ON s.workshoptype_id = c.id "
                . " left join company as cmp on cmp.id=s.company_id WHERE s.deleted='0' and s.id='".$id."'";
        $result = $this->db->query($query);
        $output = $result->result_array();
        return $output;
    }    
    public function CrosstableValidation($ID) {
        $sQuery = "SELECT id FROM workshop WHERE workshopsubtype_id= $ID ";
                //. " UNION ALL SELECT id FROM questionset_trainer WHERE subtopic_id= $ID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('workshopsubtype_mst');
        return true;
    }    
    public function check_workshopsubtype($wsubtype, $cmp_id='', $workshop_type_id='', $wsubtype_id='') {
        
        $querystr="Select description from workshopsubtype_mst where description like " . $this->db->escape($wsubtype);
        if($cmp_id!=''){
            $querystr.=" and company_id=".$cmp_id;
        }
        if($workshop_type_id!=''){
            $querystr.=" and workshoptype_id=".$workshop_type_id;
        }
        if($wsubtype_id!=''){
            $querystr.=" and id!=".$wsubtype_id;
        }
       
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }
}
