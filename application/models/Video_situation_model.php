<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Video_situation_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = " SELECT q.id,q.question,q.status,q.weightage,q.read_timer,q.response_timer,q.addeddate,"
                . " at.description as assessment_type,ae.embeddings FROM assessment_question as q "
                . " left join assessment_type at on at.id=q.assessment_type "
                . " left join ai_embeddings ae on ae.question_id=q.id ";
        $query .= " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(q.id) as total FROM assessment_question as q "
                . " left join assessment_type at on at.id=q.assessment_type";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->row();
        $data['dtTotalRecords'] = $data_array->total;
        return $data;
    }

    public function check_question_exist($assessment_type, $question, $question_id) {
        $querystr = "select question from assessment_question where question like " . $this->db->escape($question);
        if ($assessment_type != '') {
            $querystr.=" and assessment_type=" . $assessment_type;
        }
        if ($question_id != '') {
            $querystr.=" and id!=" . $question_id;
        }
        $query = $this->db->query($querystr);
        return (count((array)$query->row()) > 0 ? true : false);
    }
    public function non_embading() {
        $querystr = "SELECT * FROM `assessment_question` WHERE id NOT IN(SELECT question_id from ai_embeddings) LIMIT 5";
       
        $query = $this->db->query($querystr);
        return $query->result();
    }

}
