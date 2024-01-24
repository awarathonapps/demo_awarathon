<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class State_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        if ($dtWhere!==''){
            $query = "SELECT s.*,c.description as country_name FROM state as s INNER JOIN country as c ON s.country_id = c.id $dtWhere AND s.deleted='0' $dtOrder $dtLimit";
        }else{
            $query = "SELECT s.*,c.description as country_name FROM state as s INNER JOIN country as c ON s.country_id = c.id WHERE s.deleted='0' $dtOrder $dtLimit";
        }
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        if ($dtWhere!==''){
            $query = "SELECT COUNT(s.id) as total FROM state as s INNER JOIN country as c ON s.country_id = c.id $dtWhere AND s.deleted='0'";
        }else{
            $query = "SELECT COUNT(s.id) as total FROM state as s INNER JOIN country as c ON s.country_id = c.id WHERE s.deleted='0'";
        }
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function find_by_id($id) {
        $query = "SELECT s.*,c.description as country_name FROM state as s INNER JOIN country as c ON s.country_id = c.id WHERE s.deleted='0' and s.id=$id";
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
            $query = $this->db->query("select count(*) as found from state where deleted='0' and description='".$data['description']."'");
        }else{
            $query = $this->db->query("select count(*) as found from state where deleted='0' and description='".$data['description']."' and id!='".$id."'");
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
    public function fetch_country_data($data) {
        $return_arr = array();
        $row_array = array();
        $whereClause = "1";
        $limit="";
        if((isset($data['search']) AND isset($data['search']['term'])) || (isset($data['id']) && is_numeric($data['id'])))
        {
            if(isset($data['search']))
            {
                $getVar = strip_tags(trim($data['search']['term'])); 
                $whereClause =  " description LIKE '%" . $getVar ."%' ";
                $limit = 'LIMIT '.intval($data['page_limit']);
            }
            elseif(isset($data['id']))
            {
                $getVar = strip_tags(trim($data['id'])); 
                $whereClause =  " id = $getVar ";
            }
            
            
            $query = "SELECT id,description FROM country WHERE status='1' and $whereClause ORDER BY description $limit";
            $result = $this->db->query($query);
            $output = $result->result_array();
            $resultdata = array();
            foreach ($output as $key => $value) {
                $row_array['id'] = $value['id'];
                $row_array['text'] = utf8_encode(ucfirst(strtolower($value['description'])));
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
    public function CrosstableValidation($roleID) {
        $sQuery = "SELECT userid FROM users WHERE state= $roleID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) >0 ? false:true);
    }
    public function remove($id){
        $this->db->where('id', $id);
        $this->db->delete('state');
        return true;
    }
}
