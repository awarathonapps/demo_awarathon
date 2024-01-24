<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Feedback_set_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function fetch_access_data() {
        $query = "SELECT * FROM company_modules WHERE status='1' ORDER BY module_sort,sortorder";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function fetch_feedback($id) {
        $query = "select a.*,b.company_name from feedback a left join company b on a.company_id=b.id where a.id='" . $id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function SelectedFeedbackType($feedbackset_id) {
        $query = "SELECT ft.id,ft.description as text,ifnull(fst.feedback_type_id,0) as fst_id FROM feedback_type as ft LEFT JOIN"
                . " feedbackset_type as fst ON fst.feedback_type_id=ft.id AND  fst.feedbackset_id=" . $feedbackset_id;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT a.id,a.title,a.powered_by,a.timer,a.status,c.company_name,l.name as language_name FROM feedback as a"
                . " LEFT JOIN language_mst as l  ON l.id=a.language_id left join company c on c.id=a.company_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query_count = "SELECT COUNT(a.id) as total FROM feedback as a LEFT JOIN language_mst as l ON l.id=a.language_id "
                . " left join company c on c.id=a.company_id ";
        $query_count .= " $dtWhere";
        $result_count = $this->db->query($query_count);
        $data_array = $result_count->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }

    public function Export_questions($dtWhere, $feedbackset_id) {
        $query = "SELECT q.id,q.question_title,qt.description as feedback_type,qst.description as feedback_subtype,q.status,"
                . "q.feedback_type_id,q.feedback_subtype_id,q.option_a,q.weight_a,q.option_b,q.weight_b,q.option_c,"
                . " q.weight_c,q.option_d,q.weight_d,q.option_e,q.weight_e,q.option_f,q.weight_f,
				IF(q.question_type = 0,'Multiple choice','Text') as question_type				FROM feedback_questions q left join feedback_type qt on qt.id=q.feedback_type_id 
                    left join feedback_subtype qst on qst.id=q.feedback_subtype_id
                    left join feedbackset_type as fset ON fset.feedback_type_id=q.feedback_type_id AND fset.feedback_subtype_id=q.feedback_subtype_id
                    left join feedback_questions_inactive fqi on fqi.question_id=q.id and fqi.feedbackset_id=$feedbackset_id
                    ";

        $query .= " $dtWhere order by q.id ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getQuestionLoadData($dtWhere, $dtOrder, $dtLimit, $feedbackset_id) {
        $query = "SELECT q.question_title,q.id,qt.description as type,qst.description as subtype,fqi.id as inactive,IF(q.question_type = 0,'Multiple choice','Text') as question_type              
                    FROM feedback_questions q left join feedback_type qt on qt.id=q.feedback_type_id 
                    left join feedback_subtype qst on qst.id=q.feedback_subtype_id
                    left join feedbackset_type as fset ON fset.feedback_type_id=q.feedback_type_id AND fset.feedback_subtype_id=q.feedback_subtype_id
                    left join feedback_questions_inactive fqi on fqi.question_id=q.id and fqi.feedbackset_id=$feedbackset_id
                    ";
        $query .= " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query1 = "SELECT COUNT(q.id) as total FROM feedback_questions q left join feedback_type qt on qt.id=q.feedback_type_id 
                    left join feedback_subtype qst on qst.id=q.feedback_subtype_id
                    left join feedbackset_type as fset ON fset.feedback_type_id=q.feedback_type_id AND fset.feedback_subtype_id=q.feedback_subtype_id
                    left join feedback_questions_inactive fqi on fqi.question_id=q.id and fqi.feedbackset_id=$feedbackset_id";
        $query1 .= " $dtWhere";
        $result1 = $this->db->query($query1);
        $data_array = $result1->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }

    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('feedback');
        return true;
    }

    public function find_by_id($id) {
        $query = "SELECT a.*,b.company_name FROM feedback as a LEFT JOIN company as b ON a.company_id = b.id WHERE a.id=$id";
        $result = $this->db->query($query);
        $output = $result->result_array();
        return $output;
    }

    public function CrosstableValidation($ID) {
        $sQuery = "SELECT id FROM workshop_feedbackset_pre WHERE feedbackset_id= $ID"
                . " UNION ALL SELECT id FROM workshop_feedbackset_post WHERE feedbackset_id= $ID ";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) > 0 ? false : true);
    }

    public function check_feedback($feedback, $cmp_id = '', $feedbackset_id = '') {

        $querystr = "Select title from feedback where title='" . $feedback . "'";
        if ($cmp_id != '') {
            $querystr.=" and company_id=" . $cmp_id;
        }
        if ($feedbackset_id != '') {
            $querystr.=" and id!=" . $feedbackset_id;
        }

        $query = $this->db->query($querystr);
        return (count((array)$query->row()) > 0 ? true : false);
    }

    public function fetch_company_type($cmp_id) {
        $query = "select a.feedbacktype_id,b.description from feedback_subtype a left join feedback_type b on a.feedbacktype_id=b.id where b.company_id=" . $cmp_id . " group by a.feedbacktype_id";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function fetch_feedbackset_type($id) {
        $query = "select * from feedbackset_type  where feedbackset_id=" . $id . " group by feedback_type_id ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getFeedbackSubType($F_id, $Type_id, $subType = "") {
        $query = "SELECT id FROM feedbackset_type where feedbackset_id= " . $F_id . " "
                . "AND feedback_type_id=" . $Type_id;
        if ($subType != "") {
            $query .= " AND feedback_subtype_id=" . $subType;
        }
        $result = $this->db->query($query);
        return $result->row();
    }

    public function removeType($F_id, $Type_id, $subType = "", $inFlag = false) {
        $query = " Delete FROM feedbackset_type where feedbackset_id= " . $F_id . " "
                . " AND feedback_type_id=" . $Type_id;
        if (!$inFlag && $subType != "") {
            $query .= " AND feedback_subtype_id=" . $subType;
        }
        if ($inFlag && $subType != "") {
            $query .= " AND id NOT IN(" . $subType . ")";
        }
        return $this->db->query($query);
    }

    public function getEditSubtype($feedbackset_id, $feedback_type_id) {
        $query = "SELECT a.id,a.description,b.feedback_subtype_id FROM feedback_subtype as a "
                . "LEFT JOIN feedbackset_type as b ON a.id = b.feedback_subtype_id AND b.feedbackset_id=$feedbackset_id WHERE a.feedbacktype_id=" . $feedback_type_id;
        $result = $this->db->query($query);
        $output = $result->result();
        if (count((array)$output) == 0) {
            $Query = "SELECT id,description, id feedback_subtype_id FROM feedback_subtype where status=1 AND feedbacktype_id=0 and company_id=0";
            $Obj = $this->db->query($Query);
            $output = $Obj->result();
        }
        return $output;
    }

    public function getFeedbackType($F_id) {
        $query = "SELECT a.id,a.feedback_type_id,ft.description,ft.id FROM feedbackset_type as a"
                . " LEFT JOIN feedback_type as ft"
                . " ON ft.id=a.feedback_type_id "
                . "where a.feedbackset_id= " . $F_id . " group by a.feedback_type_id ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function CopyInactiveQuestions($feedbackset_id, $Copy_id) {
        $query = " insert into feedback_questions_inactive(feedbackset_id,question_id) SELECT $feedbackset_id as feedbackset_id,question_id "
                . "FROM feedback_questions_inactive where feedbackset_id=$Copy_id";
        $this->db->query($query);
        return true;
    }

    public function copyQusWorkshop($QuestionSet, $subtopicdata) {
        $lcSqlstr = "select a.company_id,a.id as workshop_id FROM( select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_feedbackset_pre as b"
                . " ON b.workshop_id=a.id and b.feedbackset_id=" . $QuestionSet . " LEFT JOIN feedbackset_type as c ON c.feedbackset_id=b.feedbackset_id "
                . "WHERE b.feedbackset_id=" . $QuestionSet . " AND a.id NOT IN(select distinct workshop_id FROM atom_feedback where company_id=a.company_id and feedbackset_id=b.feedbackset_id )"
                . " group by a.id";
        $lcSqlstr .= " UNION ALL select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_feedbackset_post as b"
                . " ON b.workshop_id=a.id and b.feedbackset_id=" . $QuestionSet . " LEFT JOIN feedbackset_type as c ON c.feedbackset_id=b.feedbackset_id "
                . "WHERE b.feedbackset_id=" . $QuestionSet . " AND a.id NOT IN(select distinct workshop_id FROM atom_feedback where company_id=a.company_id and feedbackset_id=b.feedbackset_id )"
                . " group by a.id) as a group by a.company_id,a.id";
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if ($subtopicdata['feedback_subtype_id'] == NULL || $subtopicdata['feedback_subtype_id'] == "") {
            $subtopicdata['feedback_subtype_id'] = 0;
        }
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {
                $Workshop_Id = $value->workshop_id;
                $Company_id = $value->company_id;
                $CheckObjExists = $this->db->query("select id FROM workshop_feedback_questions where feedbackset_id=$QuestionSet AND "
                        . "workshop_id=" . $Workshop_Id . " AND type_id=" . $subtopicdata['feedback_type_id'] . " AND subtype_id=" . $subtopicdata['feedback_subtype_id']);
                $CheckExists = $CheckObjExists->row();
                if (count((array)$CheckExists) > 0) {
                    continue;
                }
                $query = " insert into workshop_feedback_questions(company_id,workshop_id,feedbackset_id,question_id,"
                        . "type_id,subtype_id,question_title,"
                        . "option_a,weight_a,option_b,weight_b,option_c,weight_c,option_d,weight_d,"
                        . "option_e,weight_e,option_f,weight_f,multiple_allow,hint_image,tip) "
                        . "SELECT $Company_id as company_id,$Workshop_Id as workshop_id,$QuestionSet as questionSet,a.id,a.feedback_type_id,"
                        . "a.feedback_subtype_id,a.question_title,"
                        . "a.option_a,a.weight_a,a.option_b,a.weight_b,a.option_c,a.weight_c,a.option_d,a.weight_d,"
                        . "a.option_e,a.weight_e,a.option_f,a.weight_f,a.multiple_allow,a.hint_image,a.tip"
                        . " FROM feedback_questions as a where a.status=1 "
                        . " AND a.id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id=$QuestionSet)"
                        . " AND a.feedback_type_id=" . $subtopicdata['feedback_type_id'] . " AND a.feedback_subtype_id=" . $subtopicdata['feedback_subtype_id'];
                //echo $query;
                $this->db->query($query);
            }
        }
    }

    public function DeleteQusWorkshop($QuestionSet, $idStr, $feedback_type_id = '', $TopicFlag = false) {
        $TodayDt = date('Y-m-d H:i');
        $lcSqlstr = "select a.company_id,a.workshop_id,a.feedbackset_id FROM workshop_feedback_questions as a LEFT JOIN workshop as b"
                . " ON a.workshop_id=b.id "
                . " WHERE a.feedbackset_id=" . $QuestionSet . " AND "
                . "  a.feedbackset_id NOT IN(select distinct feedbackset_id FROM atom_feedback where feedbackset_id=$QuestionSet )"
                . " group by a.workshop_id";
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {
                $Workshop_Id = $value->workshop_id;
                $query = " DELETE a FROM workshop_feedback_questions as a LEFT JOIN feedbackset_type as b ON "
                        . "b.feedbackset_id=a.feedbackset_id AND a.workshop_id=$Workshop_Id AND "
                        . "a.type_id=b.feedback_type_id AND a.subtype_id =b.feedback_subtype_id "
                        . " where a.workshop_id= $Workshop_Id AND a.feedbackset_id=" . $QuestionSet;
                if ($TopicFlag) {
                    $query .= " AND a.type_id NOT IN(" . $idStr . ")";
                } else {
                    $query .= " AND a.type_id= $feedback_type_id AND  b.id NOT IN(" . $idStr . ")";
                }
                $this->db->query($query);
                //echo $this->db->last_query();
            }
        }
    }

    public function DeleteInactiveQusWorkshop($QuestionSet, $idStr = "") {
        $TodayDt = date('Y-m-d H:i');
        $lcSqlstr = "select a.company_id,a.workshop_id,a.feedbackset_id FROM workshop_feedback_questions as a LEFT JOIN workshop as b "
                . " ON a.workshop_id=b.id "
                . " WHERE a.feedbackset_id=" . $QuestionSet . " AND b.end_date > '" . $TodayDt . "' AND "
                . "  a.feedbackset_id NOT IN(select distinct feedbackset_id FROM atom_feedback where feedbackset_id=$QuestionSet )"
                . " group by a.workshop_id";
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {
                $Workshop_Id = $value->workshop_id;
                $query = " DELETE a FROM workshop_feedback_questions as a LEFT JOIN feedbackset_type as b ON "
                        . "b.feedbackset_id=a.feedbackset_id AND a.workshop_id=$Workshop_Id AND "
                        . "a.type_id=b.feedback_type_id AND a.subtype_id =b.feedback_subtype_id "
                        . " where a.workshop_id= $Workshop_Id AND a.feedbackset_id=" . $QuestionSet;
                if ($idStr != "") {
                    $query .= " AND a.question_id IN(" . $idStr . ")";
                }
                $this->db->query($query);
                //echo $this->db->last_query();
            }
        }
    }

    public function ActiveQusWorkshop($QuestionSet, $idStr = "") {
        $TodayDt = date('Y-m-d H:i');
        $lcSqlstr = "select a.company_id,a.id as workshop_id FROM( select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_feedbackset_pre as b"
                . " ON b.workshop_id=a.id and b.feedbackset_id=" . $QuestionSet . " LEFT JOIN feedbackset_type as c ON c.feedbackset_id=b.feedbackset_id"
                . " LEFT JOIN feedback_questions as d ON c.feedback_type_id=d.feedback_type_id AND c.feedback_subtype_id=d.feedback_subtype_id "
                . " WHERE b.feedbackset_id=" . $QuestionSet . " AND a.end_date > '" . $TodayDt . "' AND a.id NOT IN(select distinct workshop_id FROM atom_feedback where company_id=a.company_id AND feedbackset_id=$QuestionSet ) AND d.id=" . $idStr
                . " group by a.id";
        $lcSqlstr .= " UNION ALL select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_feedbackset_post as b"
                . " ON b.workshop_id=a.id and b.feedbackset_id=" . $QuestionSet . " LEFT JOIN feedbackset_type as c ON c.feedbackset_id=b.feedbackset_id "
                . " LEFT JOIN feedback_questions as d ON c.feedback_type_id=d.feedback_type_id AND c.feedback_subtype_id=d.feedback_subtype_id "
                . " WHERE b.feedbackset_id=" . $QuestionSet . " AND a.end_date > '" . $TodayDt . "' AND a.id NOT IN(select distinct workshop_id FROM atom_feedback where company_id=a.company_id AND  feedbackset_id=$QuestionSet ) AND d.id=" . $idStr . ") as a group by a.company_id,a.id";
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {

                $Workshop_Id = $value->workshop_id;
                $Company_id = $value->company_id;
                $CheckObjExists = $this->db->query("select id FROM workshop_feedback_questions where feedbackset_id=$QuestionSet AND "
                        . "workshop_id=" . $Workshop_Id . " AND question_id=" . $idStr);
                $CheckExists = $CheckObjExists->row();
                if (count((array)$CheckExists) > 0) {
                    continue;
                }
                //$lcSqlstr = " SELECT a.trainer_id FROM feedbackset_type as a where a.feedbackset_id=$QuestionSet and"
                //        . " a.feedback_type_id IN(select feedback_type_id from feedback_questions where id =$idStr) group by a.feedback_type_id";
                //$ObjSet = $this->db->query($lcSqlstr);
                // $RowSet = $ObjSet->row();
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
                        . "max_length,question_timer,text_weightage,sorting ) "
                        . "SELECT $Company_id as company_id,$Workshop_Id as workshop_id,$QuestionSet as questionSet,a.id,a.feedback_type_id,"
                        . "a.feedback_subtype_id,a.question_title,"
                        . "a.option_a,a.weight_a,a.option_b,a.weight_b,a.option_c,a.weight_c,a.option_d,a.weight_d,"
                        . "a.option_e,a.weight_e,a.option_f,a.weight_f,a.multiple_allow,hint_image,tip,question_type,min_length,"
                        . "max_length,question_timer,text_weightage, " . $question_sort . " as sorting"
                        . " FROM feedback_questions as a where a.id=" . $idStr;
                $this->db->query($query);
                //echo $this->db->last_query();
            }
        }
    }

    public function CheckWorkshopQnsSet($Questionset_id) {
        $query = " select distinct workshop_id FROM atom_feedback as a where a.feedbackset_id=$Questionset_id";
        $result = $this->db->query($query);
        return $result->result();
    }

}
