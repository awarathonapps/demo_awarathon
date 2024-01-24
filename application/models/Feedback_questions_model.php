<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Feedback_questions_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT a.id,a.question_title,a.company_id,a.status,IF(a.question_type = 0,'Multiple choice','Text') as question_type,"
                . " c.company_name,a.feedback_type_id,a.feedback_subtype_id,l.name as language_name FROM feedback_questions as a "
                . " LEFT JOIN language_mst as l ON l.id =a.language_id left join company c on c.id=a.company_id ";
        //. "left join feedback_type f on f.id=a.feedback_type_id "
        //. "left join feedback_subtype fc on fc.id=a.feedback_subtype_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(a.id) as total FROM feedback_questions as a "
                . " LEFT JOIN language_mst as l ON l.id =a.language_id"
                . " left join company c on c.id=a.company_id ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }

    public function ExportQuestions($dtWhere) {
        $query = " SELECT a.id,a.question_title,a.company_id,f.description as feedback_type,fc.description as feedback_subtype,a.status,"
                . " c.company_name,a.feedback_type_id,a.feedback_subtype_id,a.option_a,a.weight_a,a.option_b,a.weight_b,a.option_c,"
                . " a.weight_c,a.option_d,a.weight_d,a.option_e,a.weight_e,a.option_f,a.weight_f,l.name as language_name FROM feedback_questions as a"
                . " left join company c on c.id=a.company_id"
                . " left join feedback_type f on f.id=a.feedback_type_id"
                . " left join feedback_subtype fc on fc.id=a.feedback_subtype_id"
                . " LEFT JOIN language_mst as l ON l.id =a.language_id ";
        $query .= "$dtWhere ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function DuplicateQus($question, $Company_id = '', $feedback_type = "", $feedback_subtype = "", $Edit_id = '') {
        $query = 'SELECT id,question_title FROM feedback_questions where CONVERT(CAST(question_title as BINARY) USING utf8) LIKE ' . $this->db->escape($question);
        if ($Company_id != "") {
            $query .=" AND company_id=" . $Company_id;
        }
        if ($feedback_type != "") {
            $query .=" AND feedback_type_id=" . $feedback_type;
        }
        if ($feedback_subtype != "") {
            $query .=" AND feedback_subtype_id=" . $feedback_subtype;
        }
        if ($Edit_id != "") {
            $query .=" AND id !=" . $Edit_id;
        }
        $result = $this->db->query($query);
        return $result->result();
    }

    public function AddnewQusWorkshop($Company_id, $Questions_id, $topic_id, $subType = 0, $language_id = 1) {
        if ($subType == NULL || $subType == "") {
            $subType = 0;
        }
        $TodayDt = date('Y-m-d H:i');

        $lcSqlstr = " select a.company_id,a.workshop_id,a.feedbackset_id FROM( "
                . " select c.company_id,b.workshop_id,a.feedbackset_id "
                . " FROM feedbackset_type as a INNER JOIN feedback as f ON f.id=a.feedbackset_id "
                . " INNER JOIN workshop_feedbackset_pre as b"
                . " ON a.feedbackset_id=b.feedbackset_id "
                . " INNER JOIN workshop as c ON b.workshop_id=c.id  "
                . " WHERE c.company_id=" . $Company_id . " AND CONCAT(c.pre_end_date,' ',STR_TO_DATE(c.pre_end_time, '%l:%i %p')) >= '" . $TodayDt . "' "
                . " AND a.feedback_type_id= " . $topic_id . " AND a.feedback_subtype_id=" . $subType . " AND f.language_id=" . $language_id;
        $lcSqlstr .= " UNION ALL select c.company_id,b.workshop_id,a.feedbackset_id FROM feedbackset_type as a "
                . " INNER JOIN feedback as f ON f.id=a.feedbackset_id "
                . " INNER JOIN workshop_feedbackset_post as b ON a.feedbackset_id=b.feedbackset_id "
                . " INNER JOIN workshop as c ON b.workshop_id=c.id  "
                . " WHERE c.company_id=" . $Company_id . " AND CONCAT(c.post_end_date,' ',STR_TO_DATE(c.post_end_time, '%l:%i %p')) >= '" . $TodayDt . "' "
                . " AND a.feedback_type_id= " . $topic_id . " AND a.feedback_subtype_id=" . $subType . " AND f.language_id=" . $language_id
                . ") as a group by a.workshop_id,a.feedbackset_id";
//        echo $lcSqlstr;
//        exit;
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {
                $Workshop_Id = $value->workshop_id;
                $Company_id = $value->company_id;
                $QuestionSet = $value->feedbackset_id;
                $CheckObjExists = $this->db->query("select id FROM workshop_feedback_questions where question_id=$Questions_id AND "
                        . "workshop_id=" . $Workshop_Id . " AND feedbackset_id=$QuestionSet");
                $CheckExists = $CheckObjExists->row();
                //print_r($CheckExists);
                if (count((array)$CheckExists) > 0) {
                    continue;
                }
                $object_Set = $this->db->query("select ifnull(max(sorting),0)+1 as max_sort FROM workshop_feedback_questions where  "
                        . "workshop_id=" . $Workshop_Id . " AND feedbackset_id=$QuestionSet");
                $sorting_Set = $object_Set->row();
                if (count((array)$sorting_Set) > 0) {
                    $question_sort = $sorting_Set->max_sort;
                } else {
                    $question_sort = 1;
                }
                $query = " insert into workshop_feedback_questions(company_id,workshop_id,feedbackset_id,question_id,"
                        . "type_id,subtype_id,question_title,"
                        . "option_a,weight_a,option_b,weight_b,option_c,weight_c,option_d,weight_d,"
                        . "option_e,weight_e,option_f,weight_f,multiple_allow,hint_image,tip,question_type,min_length,"
                        . "max_length,question_timer,text_weightage,language_id,sorting ) "
                        . " SELECT $Company_id as company_id,$Workshop_Id as workshop_id,$QuestionSet as questionSet,a.id,a.feedback_type_id,"
                        . " a.feedback_subtype_id,a.question_title,"
                        . " option_a,weight_a,option_b,weight_b,option_c,weight_c,option_d,weight_d,"
                        . " option_e,weight_e,option_f,weight_f,multiple_allow ,hint_image,tip,question_type,min_length,"
                        . "max_length,question_timer,text_weightage,language_id, " . $question_sort . " as sorting "
                        . " FROM feedback_questions as a where a.id=" . $Questions_id;
                $this->db->query($query);
                //echo $this->db->last_query();
            }
        }
    }

    public function UpdateQusWorkshop($Company_id, $Questions_id, $Topic_id, $subTopic_id = 0, $language_id = '') {
        if ($subTopic_id == NULL || $subTopic_id == "") {
            $subTopic_id = 0;
        }
        $TodayDt = date('Y-m-d H:i');
        $lcSqlstr = "SELECT a.id,a.company_id,a.workshop_id,a.type_id,a.subtype_id,a.language_id 
            FROM workshop_feedback_questions as a LEFT JOIN workshop as w ON w.id=a.workshop_id 
            WHERE a.question_id=$Questions_id AND a.company_id=" . $Company_id . " AND w.end_date > '" . $TodayDt . "'";

//        $lcSqlstr = "select a.id,a.company_id,a.workshop_id,a.type_id,a.subtype_id FROM workshop_feedback_questions as a 
//            WHERE a.question_id=$Questions_id AND a.company_id=" . $Company_id . " AND"
//                . " a.question_id NOT IN(select feedback_id FROM atom_feedback where company_id=" . $Company_id . " AND "
//                . " feedbackset_id=a.feedbackset_id AND workshop_id=a.workshop_id)";
//        echo $lcSqlstr;
//        exit;
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {
                $Workshop_Id = $value->workshop_id;
                $Company_id = $value->company_id;
                $old_language_id = $value->language_id;
                if ($language_id != "" && $language_id != $old_language_id) {
                    $Query = "select id FROM atom_feedback where company_id=" . $Company_id . " AND "
                            . " workshop_id=$Workshop_Id AND feedback_id=$Questions_id";
                    $PlayedSet = $this->db->query($Query);
                    if ($PlayedSet->num_rows() > 0) {
                        continue;
                    }
                    $this->db->query("DELETE FROM workshop_feedback_questions where id=" . $value->id);
                    $this->AddnewQusWorkshop($Company_id, $Questions_id, $Topic_id, $subTopic_id, $language_id);
                } elseif ($Topic_id != $value->type_id || $subTopic_id != $value->subtype_id) {
                    $Query = "select id FROM atom_feedback where company_id=" . $Company_id . " AND "
                            . " workshop_id=$Workshop_Id AND feedback_id=$Questions_id";
                    $PlayedSet = $this->db->query($Query);
                    if ($PlayedSet->num_rows() > 0) {
                        continue;
                    }
                    $this->db->query("DELETE FROM workshop_feedback_questions where id=" . $value->id);
                    $this->AddnewQusWorkshop($Company_id, $Questions_id, $Topic_id, $subTopic_id, $old_language_id);
                } else {
                    $query = " update workshop_feedback_questions as a LEFT JOIN feedback_questions as b ON "
                            . "b.id=a.question_id AND a.workshop_id=$Workshop_Id AND "
                            . "a.type_id=b.feedback_type_id AND a.subtype_id =b.feedback_subtype_id "
                            . " SET a.question_title= b.question_title,a.option_a=b.option_a,a.weight_a=b.weight_a,"
                            . "a.option_b=b.option_b,a.weight_b=b.weight_b,a.option_c=b.option_c,"
                            . "a.weight_c=b.weight_c,a.option_d=b.option_d,a.weight_d=b.weight_d,"
                            . "a.option_e=b.option_e,a.weight_e=b.weight_e,a.option_f=b.option_f,"
                            . "a.weight_f=b.weight_f,a.multiple_allow=b.multiple_allow,a.hint_image=b.hint_image,a.tip=b.tip,
                            a.question_type=b.question_type,a.min_length=b.min_length,a.max_length=b.max_length,
                            a.question_timer=b.question_timer,a.text_weightage=b.text_weightage "
                            . " where a.id=" . $value->id . " AND a.workshop_id= " . $Workshop_Id . " AND a.question_id=" . $Questions_id;
                    $this->db->query($query);
                }
            }
        }
        $this->AddnewQusWorkshop($Company_id, $Questions_id, $Topic_id, $subTopic_id, $language_id);
    }

    public function DeleteWorkshopQus($Questions_id) {
        $TodayDt = date('Y-m-d H:i');
        $lcSqlstr = "DELETE a FROM workshop_feedback_questions as a LEFT JOIN workshop as b"
                . " ON a.workshop_id=b.id AND a.question_id= " . $Questions_id
                . " WHERE a.question_id=" . $Questions_id . " AND a.question_id "
                . " NOT IN(select DISTINCT feedback_id FROM atom_feedback where workshop_id=b.id)";
//        echo $lcSqlstr;exit;
        $this->db->query($lcSqlstr);
    }

    public function CrosstableValidation($Questions_id) {
        $Query = "SELECT id FROM atom_feedback WHERE feedback_id=" . $Questions_id;
        $query = $this->db->query($Query);
        return (count((array)$query->result()) > 0 ? 0 : 1);
    }

}
