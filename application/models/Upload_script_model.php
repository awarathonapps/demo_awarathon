<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Upload_script_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit)
    {
        $query = " SELECT sm.id, sm.script_title,DATE_FORMAT(sm.addeddate,'%d-%m-%Y %H:%i') as addeddate, if(sm.question_limit!=0, sm.question_limit, count(asq.id))as number_of_qna FROM script_mst as sm left join assessment_script_qna as asq on sm.id = asq.script_id group by sm.id ";
        $query .= " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(id) as total FROM script_mst ";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }

    public function get_value($Table, $Column, $Clause) {
        $LcSqlStr = "SELECT " . $Column . " FROM " . $Table . " WHERE " . $Clause . " ";
        $query = $this->db->query($LcSqlStr);
        $row = $query->result();
        return $row;
    }

    public function get_value_result($Table, $Column, $Clause) {
        $LcSqlStr = "SELECT " . $Column . " FROM " . $Table . " WHERE " . $Clause . " ";
        $query = $this->db->query($LcSqlStr);
        $result = $query->result();
        return $result;
    }
}