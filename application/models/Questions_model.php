<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Questions_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT a.id,a.question_title,a.topic_id,a.subtopic_id,a.company_id,a.status,"
                . "CASE a.correct_answer
                    WHEN 'a' THEN a.option_a
                    WHEN 'b' THEN a.option_b
                    WHEN 'c' THEN a.option_c
                    WHEN 'd' THEN a.option_d
                    ELSE ''
                    END as correct_value,l.name as language_name,
                    c.company_name,qt.description as topic,qs.description as sub_topic FROM questions as a left join company c on c.id=a.company_id
                    left join question_topic qt on qt.id=a.topic_id left join question_subtopic 
                    qs on qs.id=a.subtopic_id LEFT JOIN language_mst as l ON l.id =a.language_id";
        $query .= " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(a.id) as total FROM questions as a left join company c on c.id=a.company_id
                    left join question_topic qt on qt.id=a.topic_id left join question_subtopic 
                    qs on qs.id=a.subtopic_id LEFT JOIN language_mst as l ON l.id =a.language_id ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }

    public function DuplicateQus($question, $Company_id = '', $Topic_id = '', $subTopic_id = '') {
        $query = "SELECT id,question_title FROM questions where CONVERT(CAST(question_title as BINARY) USING utf8) LIKE " . $this->db->escape($question);
        if ($Company_id != "") {
            $query .=" AND company_id=" . $Company_id;
        }
        if ($Topic_id != "") {
            $query .=" AND topic_id=" . $Topic_id;
        }
        if ($subTopic_id != "") {
            $query .=" AND subtopic_id=" . $subTopic_id;
        }
        $result = $this->db->query($query);
        return $result->result();
    }

    public function AddnewQusWorkshop($Company_id, $Questions_id, $topic_id, $subTopic_id = 0, $language_id = 1) {
        $TodayDt = date('Y-m-d H:i');
        if ($subTopic_id == NULL || $subTopic_id == "") {
            $subTopic_id = 0;
        }
        $lcSqlstr = " select a.company_id,a.workshop_id,a.questionset_id,a.trainer_id FROM("
                . " select c.company_id,b.workshop_id,a.questionset_id,a.trainer_id FROM questionset_trainer as a "
                . " INNER JOIN question_set as q ON q.id=a.questionset_id "
                . " INNER JOIN workshop_questionset_pre as b ON a.questionset_id=b.questionset_id "
                . " INNER JOIN workshop as c ON b.workshop_id=c.id  "
                . " WHERE c.company_id=" . $Company_id . " AND CONCAT(c.pre_end_date,' ',STR_TO_DATE(c.pre_end_time, '%l:%i %p')) >= '" . $TodayDt . "' "
                . " AND a.topic_id= " . $topic_id . " AND a.subtopic_id=" . $subTopic_id . " AND q.language_id=" . $language_id;
        $lcSqlstr .= " UNION ALL select c.company_id,b.workshop_id,a.questionset_id,a.trainer_id FROM questionset_trainer as a "
                . " INNER JOIN question_set as q ON q.id=a.questionset_id "
                . " INNER JOIN workshop_questionset_post as b ON a.questionset_id=b.questionset_id "
                . " INNER JOIN workshop as c ON b.workshop_id=c.id  "
                . " WHERE c.company_id=" . $Company_id .
                " AND (c.pre_end_date='0000-00-00' OR CONCAT(c.pre_end_date,' ',STR_TO_DATE(c.pre_end_time, '%l:%i %p')) >= '" . $TodayDt . "') "
                . " AND CONCAT(c.post_end_date,' ',STR_TO_DATE(c.post_end_time, '%l:%i %p')) >= '" . $TodayDt . "' "
                . " AND a.topic_id= " . $topic_id . " AND a.subtopic_id=" . $subTopic_id . " AND q.language_id=" . $language_id
                . ") as a group by a.workshop_id,a.questionset_id";
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {
                $Workshop_Id = $value->workshop_id;
                $Company_id = $value->company_id;
                $QuestionSet = $value->questionset_id;
                $CheckObjExists = $this->db->query("select id FROM workshop_questions where question_id=$Questions_id AND "
                        . "workshop_id=" . $Workshop_Id . " AND topic_id=" . $topic_id . " AND subtopic_id=" . $subTopic_id . " AND questionset_id=$QuestionSet");
                $CheckExists = $CheckObjExists->row();
                if (count((array)$CheckExists) > 0) {
                    continue;
                }
                $object_Set = $this->db->query("select ifnull(max(sorting),0)+1 as max_sort FROM workshop_questions where  "
                        . "workshop_id=" . $Workshop_Id . " AND questionset_id=$QuestionSet");
                $sorting_Set = $object_Set->row();
                if (count((array)$sorting_Set) > 0) {
                    $question_sort = $sorting_Set->max_sort;
                } else {
                    $question_sort = 1;
                }
                $query = " insert into workshop_questions(company_id,workshop_id,questionset_id,question_id,topic_id,subtopic_id,"
                        . "question_title,option_a,option_b,option_c,option_d,correct_answer,tip,hint_image,youtube_link,trainer_id,language_id,sorting) "
                        . "SELECT $Company_id as company_id,$Workshop_Id as workshop_id,$QuestionSet as questionSet,a.id,a.topic_id,"
                        . "a.subtopic_id,a.question_title,"
                        . "a.option_a,a.option_b,a.option_c,a.option_d,a.correct_answer,a.tip,a.hint_image,a.youtube_link,"
                        . $value->trainer_id . " as trainer_id,a.language_id, " . $question_sort . " as sorting "
                        . "FROM questions as a where a.id=" . $Questions_id;
                $this->db->query($query);
                //echo $this->db->last_query();
            }
        }
    }

    public function UpdateQusWorkshop($Company_id, $Questions_id, $Topic_id, $subTopic_id, $language_id = '') {
        $TodayDt = date('Y-m-d H:i');
        if ($subTopic_id == NULL || $subTopic_id == "") {
            $subTopic_id = 0;
        }
        $lcSqlstr = "select wq.id,wq.company_id,wq.workshop_id,wq.topic_id,wq.subtopic_id,wq.language_id  
            from workshop_questions as wq LEFT JOIN workshop as w ON w.id=wq.workshop_id  where wq.question_id= $Questions_id
                AND w.end_date > '" . $TodayDt . "'";
        //$lcSqlstr ="select wq.id,wq.company_id,wq.workshop_id,wq.topic_id,wq.subtopic_id 
//            from workshop_questions as wq where wq.question_id= $Questions_id "
//            . " AND wq.question_id NOT IN(select question_id FROM atom_results where company_id=" . $Company_id . " AND "
//            . " workshop_id=wq.workshop_id AND questionset_id=wq.questionset_id)";
//        echo $lcSqlstr;
//        exit;
//        $lcSqlstr = " select a.company_id,a.workshop_id,a.questionset_id,a.trainer_id,a.topic_id,a.subtopic_id FROM("
//                . " select c.company_id,b.workshop_id,a.questionset_id,a.trainer_id FROM questionset_trainer as a "
//                . "INNER JOIN workshop_questionset_pre as b"
//                . " ON a.questionset_id=b.questionset_id "
//                . " INNER JOIN workshop as c ON b.workshop_id=c.id  "
//                . " WHERE c.company_id=" . $Company_id . " AND CONCAT(c.pre_end_date,' ',STR_TO_DATE(c.pre_end_time, '%l:%i %p')) > '" . $TodayDt . "' "
//                . " AND a.question_id= ".$Questions_id;
//        $lcSqlstr .= " UNION ALL select c.company_id,b.workshop_id,a.questionset_id,a.trainer_id FROM questionset_trainer as a "
//                . " INNER JOIN workshop_questionset_post as b"
//                . " ON a.questionset_id=b.questionset_id "
//                . " INNER JOIN workshop as c ON b.workshop_id=c.id  "
//                . " WHERE c.company_id=" . $Company_id . " AND CONCAT(c.post_end_date,' ',STR_TO_DATE(c.post_end_time, '%l:%i %p')) > '" . $TodayDt . "' "
//                . " AND a.question_id= ".$Questions_id
//                . ") as a group by a.workshop_id,a.questionset_id";
//        echo $lcSqlstr;
//        exit;
        //Check Already Played Start
//                $Query ="select distinct question_id FROM atom_results where company_id=" . $Company_id . " AND "
//                . " question_id=$Questions_id AND workshop_id=$Workshop_Id AND questionset_id=$questionset_id ";
//                $PlayedSet = $this->db->query($Query);
//                if($PlayedSet->num_rows() >0){
//                    continue;
//                }
        //Check Already Played End
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {
                $Workshop_Id = $value->workshop_id;
                $Company_id = $value->company_id;
                $old_language_id = $value->language_id;
                if ($language_id != "" && $language_id != $old_language_id) {
                    $Query = "select id FROM atom_results where company_id=" . $Company_id . " AND "
                            . " workshop_id=$Workshop_Id AND question_id=$Questions_id";
                    $PlayedSet = $this->db->query($Query);
                    if ($PlayedSet->num_rows() > 0) {
                        continue;
                    }
                    $this->db->query("DELETE FROM workshop_questions where id=" . $value->id);
                    $this->AddnewQusWorkshop($Company_id, $Questions_id, $Topic_id, $subTopic_id, $language_id);
                } elseif ($Topic_id != $value->topic_id || $subTopic_id != $value->subtopic_id) {
                    $Query = "select id FROM atom_results where company_id=" . $Company_id . " AND "
                            . " workshop_id=$Workshop_Id AND question_id=$Questions_id";
                    $PlayedSet = $this->db->query($Query);
                    if ($PlayedSet->num_rows() > 0) {
                        continue;
                    }
                    $this->db->query("DELETE FROM workshop_questions where id=" . $value->id);
                    $this->AddnewQusWorkshop($Company_id, $Questions_id, $Topic_id, $subTopic_id, $language_id);
                } else {
                    $query = " update workshop_questions as a LEFT JOIN questions as b ON "
                            . "b.id=a.question_id AND a.workshop_id=$Workshop_Id AND "
                            . "a.topic_id=b.topic_id AND a.subtopic_id =b.subtopic_id "
                            . " SET a.question_title= b.question_title,a.option_a=b.option_a,a.option_b=b.option_b, "
                            . "a.option_c=b.option_c,a.option_d=b.option_d,a.correct_answer=b.correct_answer,a.tip=b.tip,"
                            . "a.hint_image=b.hint_image,a.youtube_link=b.youtube_link "
                            . " where a.workshop_id= " . $Workshop_Id . " AND a.question_id=" . $Questions_id;
                    $this->db->query($query);
                }
            }
        }
        $this->AddnewQusWorkshop($Company_id, $Questions_id, $Topic_id, $subTopic_id, $language_id);
    }

    public function DeleteWorkshopQus($Questions_id) {
        $TodayDt = date('Y-m-d H:i');
        $lcSqlstr = "DELETE a FROM workshop_questions as a LEFT JOIN workshop as b"
                . " ON a.workshop_id=b.id AND a.question_id= " . $Questions_id
                . " WHERE a.question_id=" . $Questions_id . " AND a.question_id "
                . " NOT IN(select DISTINCT question_id FROM atom_results where workshop_id=b.id)";
//        echo $lcSqlstr;exit;
        $this->db->query($lcSqlstr);
    }

    public function CrosstableValidation($Questions_id) {
        $Query = "SELECT id FROM atom_results WHERE question_id=" . $Questions_id;
        $query = $this->db->query($Query);
        return (count((array)$query->result()) > 0 ? 0 : 1);
    }

    public function ExportQuestions($dtWhere) {
        $query = "SELECT a.id,a.question_title,a.topic_id,a.subtopic_id,a.company_id,a.status,
                    a.option_a,a.option_b,a.option_c,a.option_d,a.tip,a.correct_answer, c.company_name,qt.description AS topic,qs.description AS sub_topic FROM questions as a 
                    left join company c on c.id=a.company_id
                    left join question_topic qt on qt.id=a.topic_id left join question_subtopic 
                    qs on qs.id=a.subtopic_id ";

        $query .= " $dtWhere ";
//        echo $query;exit;
        $result = $this->db->query($query);
        return $result->result();
    }

}
