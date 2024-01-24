<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Advertisement_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function fetch_access_data() {
        $query = "SELECT * FROM company_modules WHERE status='1' ORDER BY module_sort,sortorder";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function fetch_advertisement($id) {
        $query = "select *,DATE_FORMAT(start_date,'%d-%m-%Y')as start_date,DATE_FORMAT(end_date,'%d-%m-%Y')as end_date from advertisement where id='" . $id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT a.id,a.advt_name,a.status,a.company_id,DATE_FORMAT(a.start_date,'%d-%m-%Y')as start_date,c.company_name,"
                . " DATE_FORMAT(a.end_date,'%d-%m-%Y')as end_date FROM advertisement a left join company c on c.id=a.company_id ";
        $query .= " $dtWhere $dtOrder $dtLimit"; 
        
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);

        $query_count = "SELECT COUNT(a.id) as total FROM advertisement a left join company c on c.id=a.company_id";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('advertisement');
        return true;
    }
     public function check_advertisement($advertisement,$advt_id='',$cmp_id="") {
        
        $querystr="Select advt_name from advertisement where advt_name='" . $advertisement . "'";
        if($cmp_id!=''){
            $querystr.=" and company_id=".$cmp_id;
        }
        if($advt_id!=''){
            $querystr.=" and id!=".$advt_id;
        }
       
        $query = $this->db->query($querystr);        
        return (count((array)$query->row()) > 0 ? true : false);
    }

}
