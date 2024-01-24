<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Questionset_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function fetch_access_data() {
        $query = "SELECT * FROM company_modules WHERE status='1' ORDER BY module_sort,sortorder";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function fetch_feedback($id) {
        $query = "select a.*,b.company_name from question_set a left join company b on a.company_id=b.id "
                . " where a.id='" . $id . "'";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function fetch_questionset_trainer($id) {
        $query = "select * from questionset_trainer  where questionset_id=" . $id . " group by topic_id,trainer_id";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT a.id,a.title,a.powered_by,a.status,c.company_name,a.timer,l.name as language_name"
            . " FROM question_set as a LEFT JOIN language_mst as l  ON l.id=a.language_id "
            . " LEFT join company c on c.id=a.company_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query1 = "SELECT COUNT(a.id) as total FROM question_set as a LEFT JOIN language_mst as l  ON l.id=a.language_id "
            . "left join company c on c.id=a.company_id ";
        $query1 .= " $dtWhere";
        $result1 = $this->db->query($query1);
        $data_array = $result1->row();
        $data['dtTotalRecords'] = $data_array->total;
        return $data;
    }
    public function Export_questions($dtWhere,$Questionsset_id){
        $query = "SELECT q.question_title,q.id,
            qt.description as topic,qi.id as inactive,ifnull(qst.description,'No sub-topic') as subtopic, 
                q.option_a,q.option_b,q.option_c,q.option_d,q.tip,q.correct_answer,
                CONCAT(cu.first_name,' ',cu.last_name) as trainer_name
                FROM questions q left join question_topic qt on qt.id=q.topic_id 
                left join question_subtopic qst on qst.id=q.subtopic_id 
                left join question_inactive qi on qi.question_id=q.id and qi.questionset_id=$Questionsset_id
                left join questionset_trainer qtr on qtr.topic_id =q.topic_id and qtr.subtopic_id=q.subtopic_id
                left join company_users as cu ON cu.userid=qtr.trainer_id";
        
        $query .= " $dtWhere order by q.id ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getQuestionLoadData($dtWhere, $dtOrder, $dtLimit, $Questionsset_id) {
        $query = "SELECT q.question_title,q.id,
            qt.description as topic,qi.id as inactive,ifnull(qst.description,'No sub-topic') as subtopic, 
                CASE q.correct_answer 
                WHEN 'a' THEN q.option_a
                WHEN 'b' THEN q.option_b
                WHEN 'c' THEN q.option_c
                WHEN 'd' THEN q.option_d
                ELSE 'Wrong'
                END as correct_answer,CONCAT(cu.first_name,' ',cu.last_name) as trainer_name
                FROM questions q left join question_topic qt on qt.id=q.topic_id 
                left join question_subtopic qst on qst.id=q.subtopic_id 
                left join question_inactive qi on qi.question_id=q.id and qi.questionset_id=$Questionsset_id
                left join questionset_trainer qtr on qtr.topic_id =q.topic_id and qtr.subtopic_id=q.subtopic_id
                left join company_users as cu ON cu.userid=qtr.trainer_id";
        $query .= " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query1 = "SELECT COUNT(q.id) as total FROM questions q left join question_topic qt on qt.id=q.topic_id 
                left join question_subtopic qst on qst.id=q.subtopic_id 
                left join question_inactive qi on qi.question_id=q.id and qi.questionset_id=$Questionsset_id
                left join questionset_trainer qtr on qtr.topic_id =q.topic_id and qtr.subtopic_id=q.subtopic_id
                left join company_users as cu ON cu.userid=qtr.trainer_id ";
        $query1 .= " $dtWhere";
        $result1 = $this->db->query($query1);
        $data_array = $result1->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }

    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('question_set');
        return true;
    }

    public function find_by_id($id,$Company_id="") {
        $query = "SELECT a.*,b.company_name FROM question_set as a LEFT JOIN company as b ON a.company_id = b.id "
            . " WHERE a.id=$id";
        if($Company_id !=""){
            $query .= " AND a.company_id=".$Company_id;
        }
        $result = $this->db->query($query);
        $output = $result->result_array();
        return $output;
    }

    public function CrosstableValidation($ID) {
        $sQuery = "SELECT id FROM workshop_questionset_pre WHERE questionset_id= $ID"
                . " union all SELECT id FROM workshop_questionset_post WHERE questionset_id= $ID ";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) > 0 ? false : true);
    }

    public function check_Questionset($feedback, $cmp_id = '', $feedback_id = '') {

        $querystr = "Select title from question_set where title='" . $feedback . "'";
        if ($cmp_id != '') {
            $querystr.=" and company_id=" . $cmp_id;
        }
        if ($feedback_id != '') {
            $querystr.=" and id!=" . $feedback_id;
        }

        $query = $this->db->query($querystr);
        return (count((array)$query->row()) > 0 ? true : false);
    }

    public function get_company() {
        $this->db->select('id ,company_name')
                ->from('company')
                ->where('status', '1');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function get_trainer() {
        $this->db->select('userid ,username')
                ->from('company_users')
                ->where('status', '1');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function getEditSubtopic($Q_id, $topic_id,$trainer_id="") {
        $query = "SELECT a.id,a.description,subtopic_id  FROM question_subtopic as a "
                . "LEFT JOIN questionset_trainer as b ON a.id = b.subtopic_id AND"
                . " b.questionset_id=$Q_id AND b.trainer_id=$trainer_id WHERE a.topic_id=" . $topic_id ." ";
        $result = $this->db->query($query);
        $output = $result->result();
        if (count((array)$output) == 0) {
            $Query = "SELECT id,description, id subtopic_id FROM question_subtopic where status=1 AND topic_id=0 and company_id=0";
            $Obj = $this->db->query($Query);
            $output = $Obj->result();
        }
        return $output;
    }

    public function getQuestionTopic($Q_id) {
        $query = "SELECT a.id,a.topic_id,a.trainer_id,qt.description FROM questionset_trainer as a"
                . " LEFT JOIN question_topic as qt"
                . " ON qt.id=a.topic_id "
                . "where a.questionset_id= " . $Q_id . " group by a.topic_id ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getQuestionTrainer($Q_id) {
        $query = "SELECT a.id,a.trainer_id,CONCAT(cu.first_name,cu.last_name) as trainer_name FROM questionset_trainer as a"
                . " LEFT JOIN company_users as cu"
                . " ON cu.userid=a.trainer_id "
                . "where a.questionset_id= " . $Q_id . " group by a.trainer_id ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getQuestionSubTopic($Q_id, $Topic_id, $subTopic = "",$trainer_id="") {
        $query = "SELECT id,subtopic_id,trainer_id FROM questionset_trainer where questionset_id= " . $Q_id . " "
                . "AND topic_id=" . $Topic_id;
        if ($subTopic != "") {
            $query .= " AND subtopic_id=" . $subTopic;
        }
        if ($trainer_id != "") {
            $query .= " AND trainer_id=" . $trainer_id;
        }
        $result = $this->db->query($query);
        return $result->row();
    }

    public function removeTopic($Q_id, $Topic_id, $subTopic = "", $inFlag = false) {
        $query = " Delete FROM questionset_trainer where questionset_id= " . $Q_id . " "
                . " AND topic_id=" . $Topic_id;
        if (!$inFlag && $subTopic != "") {
            $query .= " AND subtopic_id=" . $subTopic;
        }
        if ($inFlag && $subTopic != "") {
            $query .= " AND id NOT IN(" . $subTopic . ")";
        }
        return $this->db->query($query);
    }

    public function fetch_company_topic($cmp_id) {
        $query = "select a.topic_id,b.description from question_subtopic a left join question_topic b on a.topic_id=b.id where b.company_id=" . $cmp_id . " group by a.topic_id";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function CopyInactiveQuestions($Questionset_id, $Copy_id) {
        $query = " insert into question_inactive(questionset_id,question_id) SELECT $Questionset_id as questionset_id,question_id "
                . "FROM question_inactive where questionset_id=$Copy_id";
        $this->db->query($query);
        return true;
    }
    public function CheckQuestionset_ismap($Questionset_id) {
        $TodayDt = date('Y-m-d H:i');
        $query = " select a.id FROM workshop_questionset_trainer as a "
                . " where a.questionset_id=$Questionset_id  ";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function Questionsetis_Played($Questionset_id) {
        $query = " select id FROM atom_results where questionset_id=$Questionset_id limit 0,1";
        $result = $this->db->query($query);
        return (count((array)$result->row())>0 ? 1: 0);
    }
    public function CheckWorkshopQnsSet($Questionset_id) {
        $TodayDt = date('Y-m-d H:i');
        $query = " select b.workshop_name FROM atom_results as a LEFT JOIN workshop as b ON b.id=a.workshop_id "
                . " where a.questionset_id=$Questionset_id  group by a.workshop_id ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function copyQusWorkshop($QuestionSet, $subtopicdata) {
        $TodayDt = date('Y-m-d H:i');
        if($subtopicdata['subtopic_id']==NULL || $subtopicdata['subtopic_id']==""){
            $subtopicdata['subtopic_id'] =0;
        }
        $lcSqlstr = "select a.company_id,a.id as workshop_id FROM( select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_questionset_pre as b"
                . " ON b.workshop_id=a.id and b.questionset_id=".$QuestionSet ." LEFT JOIN questionset_trainer as c ON c.questionset_id=b.questionset_id "
                . " WHERE b.questionset_id=" . $QuestionSet . " AND a.id NOT IN(select distinct workshop_id FROM atom_results where company_id=a.company_id and questionset_id=b.questionset_id )"
                . " group by a.id";
        $lcSqlstr .= " UNION ALL select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_questionset_post as b"
                . " ON b.workshop_id=a.id and b.questionset_id=".$QuestionSet ." LEFT JOIN questionset_trainer as c ON c.questionset_id=b.questionset_id "
                . " WHERE b.questionset_id=" . $QuestionSet . " AND a.id NOT IN(select distinct workshop_id FROM atom_results where company_id=a.company_id and questionset_id=b.questionset_id )"
                . " group by a.id) as a group by a.company_id,a.id";
        //echo $lcSqlstr;
        //exit;
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {
                $Workshop_Id = $value->workshop_id;
                $Company_id = $value->company_id;
                $CheckObjExists = $this->db->query("select id FROM workshop_questions where questionset_id=$QuestionSet AND "
                        . "workshop_id=" . $Workshop_Id . " AND topic_id=" . $subtopicdata['topic_id']. " AND subtopic_id=".$subtopicdata['subtopic_id']);
                $CheckExists = $CheckObjExists->row();
                if (count((array)$CheckExists) > 0) {
                    continue;
                }
                $Trainer_id = $subtopicdata['trainer_id'];
                $query = " insert into workshop_questions(company_id,workshop_id,questionset_id,question_id,topic_id,subtopic_id,"
                        . "question_title,option_a,option_b,option_c,option_d,correct_answer,tip,hint_image,youtube_link,trainer_id) "
                        . "SELECT $Company_id as company_id,$Workshop_Id as workshop_id,$QuestionSet as questionSet,a.id,a.topic_id,"
                        . "a.subtopic_id,a.question_title,"
                        . "a.option_a,a.option_b,a.option_c,a.option_d,a.correct_answer,a.tip,a.hint_image,a.youtube_link,"
                        . " $Trainer_id as trainer_id "
                        . "FROM questions as a where a.status=1 "
                        . " AND a.id NOT IN(select question_id FROM question_inactive where questionset_id=$QuestionSet)"
                        . " AND a.topic_id=" . $subtopicdata['topic_id'] . " AND a.subtopic_id=" . $subtopicdata['subtopic_id'];
                $this->db->query($query);
            }
        }
    }

    public function UpdateQusWorkshop($QuestionSet,$subtopicdata, $UpdatedId) {
        $TodayDt = date('Y-m-d H:i');
        $lcSqlstr = "select a.company_id,a.id as workshop_id FROM( select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_questionset_pre as b"
                . " ON b.workshop_id=a.id and b.questionset_id=".$QuestionSet ." LEFT JOIN questionset_trainer as c ON c.questionset_id=b.questionset_id "
                . " WHERE b.questionset_id=" . $QuestionSet . " AND a.id NOT IN(select distinct workshop_id FROM atom_results where company_id=a.company_id and questionset_id=b.questionset_id )"
                . " group by a.id";
        $lcSqlstr .= " UNION ALL select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_questionset_post as b"
                . " ON b.workshop_id=a.id and b.questionset_id=".$QuestionSet ." LEFT JOIN questionset_trainer as c ON c.questionset_id=b.questionset_id "
                . " WHERE b.questionset_id=" . $QuestionSet . " AND a.id NOT IN(select distinct workshop_id FROM atom_results where company_id=a.company_id and questionset_id=b.questionset_id )"
                . " group by a.id) as a group by a.company_id,a.id";
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {
                $Workshop_Id = $value->workshop_id;
                $Company_id = $value->company_id;
                $CheckObjExists = $this->db->query("select id FROM workshop_questions where questionset_id=$QuestionSet AND "
                        . "workshop_id=" . $Workshop_Id . " AND topic_id=" . $subtopicdata['topic_id']. " AND subtopic_id=".$subtopicdata['subtopic_id']);
                $CheckExists = $CheckObjExists->row();
                if (count((array)$CheckExists) > 0) {
                    $query = " update workshop_questions as a LEFT JOIN questionset_trainer as b ON "
                            . "b.questionset_id=a.questionset_id AND a.workshop_id=$Workshop_Id AND "
                            . "a.topic_id=b.topic_id AND a.subtopic_id =b.subtopic_id "
                            . " SET a.trainer_id= b.trainer_id "
                            . " where a.workshop_id= $Workshop_Id AND a.questionset_id= $QuestionSet AND"
                            . " b.id=" . $UpdatedId;
                }else{
                    $Trainer_id = $subtopicdata['trainer_id'];
                    $query = " insert into workshop_questions(company_id,workshop_id,questionset_id,question_id,topic_id,subtopic_id,"
                        . "question_title,option_a,option_b,option_c,option_d,correct_answer,tip,hint_image,youtube_link,trainer_id) "
                        . "SELECT $Company_id as company_id,$Workshop_Id as workshop_id,$QuestionSet as questionSet,a.id,a.topic_id,"
                        . "a.subtopic_id,a.question_title,"
                        . "a.option_a,a.option_b,a.option_c,a.option_d,a.correct_answer,a.tip,a.hint_image,a.youtube_link,"
                        . " $Trainer_id as trainer_id "
                        . "FROM questions as a where a.status=1 "
                        . " AND a.id NOT IN(select question_id FROM question_inactive where questionset_id=$QuestionSet)"
                        . " AND a.topic_id=" . $subtopicdata['topic_id'] . " AND a.subtopic_id=" . $subtopicdata['subtopic_id'];
                }
                $this->db->query($query);
                //echo $this->db->last_query();
            }
        }
    }

    public function DeleteQusWorkshop($QuestionSet, $idStr, $topic_id = '', $TopicFlag = false) {
        $TodayDt = date('Y-m-d H:i');
        $lcSqlstr = "select a.company_id,a.id as workshop_id FROM( select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_questionset_pre as b"
                . " ON b.workshop_id=a.id and b.questionset_id=".$QuestionSet ." LEFT JOIN questionset_trainer as c ON c.questionset_id=b.questionset_id "
                . " WHERE b.questionset_id=" . $QuestionSet . " AND a.id NOT IN(select distinct workshop_id FROM atom_results where company_id=a.company_id and questionset_id=b.questionset_id )"
                . " group by a.id";
        $lcSqlstr .= " UNION ALL select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_questionset_post as b"
                . " ON b.workshop_id=a.id and b.questionset_id=".$QuestionSet ." LEFT JOIN questionset_trainer as c ON c.questionset_id=b.questionset_id "
                . " WHERE b.questionset_id=" . $QuestionSet . " AND a.id NOT IN(select distinct workshop_id FROM atom_results where company_id=a.company_id and questionset_id=b.questionset_id )"
                . " group by a.id) as a group by a.company_id,a.id";
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {
                $Workshop_Id = $value->workshop_id;
                $query = " DELETE a FROM workshop_questions as a LEFT JOIN questionset_trainer as b ON "
                        . "b.questionset_id=a.questionset_id AND a.workshop_id=$Workshop_Id AND "
                        . "a.topic_id=b.topic_id AND a.subtopic_id =b.subtopic_id "
                        . " where a.workshop_id= $Workshop_Id AND a.questionset_id=" . $QuestionSet;
                if ($TopicFlag) {
                    $query .= " AND a.topic_id NOT IN(" . $idStr . ")";
                } else {
                    $query .= " AND a.topic_id= $topic_id AND  b.id NOT IN(" . $idStr . ")";
                }
                $this->db->query($query);
                //echo $this->db->last_query();
            }
        }
    }

    public function DeleteInactiveQusWorkshop($QuestionSet, $idStr = "") {
        $TodayDt = date('Y-m-d H:i');
        $lcSqlstr = "select a.company_id,a.id as workshop_id FROM( "
                . "select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_questionset_pre as b"
                . " ON b.workshop_id=a.id and b.questionset_id=".$QuestionSet ." LEFT JOIN questionset_trainer as c ON c.questionset_id=b.questionset_id "
                . " WHERE b.questionset_id=" . $QuestionSet . " AND a.end_date > '".$TodayDt."' AND a.id NOT IN(select distinct workshop_id FROM atom_results where company_id=a.company_id and questionset_id=b.questionset_id )"
                . " group by a.id";
        $lcSqlstr .= " UNION ALL select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_questionset_post as b"
                . " ON b.workshop_id=a.id and b.questionset_id=".$QuestionSet ." LEFT JOIN questionset_trainer as c ON c.questionset_id=b.questionset_id "
                . " WHERE b.questionset_id=" . $QuestionSet . " AND a.end_date > '".$TodayDt."' AND a.id NOT IN(select distinct workshop_id FROM atom_results where company_id=a.company_id and questionset_id=b.questionset_id )"
                . " group by a.id) as a group by a.company_id,a.id";
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {
                $Workshop_Id = $value->workshop_id;
                $query = " DELETE a FROM workshop_questions as a LEFT JOIN questionset_trainer as b ON "
                        . "b.questionset_id=a.questionset_id AND a.workshop_id=$Workshop_Id AND "
                        . "a.topic_id=b.topic_id AND a.subtopic_id =b.subtopic_id "
                        . " where a.workshop_id= $Workshop_Id AND a.questionset_id=" . $QuestionSet;
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
        $lcSqlstr = "select a.company_id,a.id as workshop_id FROM( select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_questionset_pre as b"
                . " ON b.workshop_id=a.id and b.questionset_id=".$QuestionSet ." LEFT JOIN questionset_trainer as c ON c.questionset_id=b.questionset_id "
                . " WHERE b.questionset_id=" . $QuestionSet . " AND a.end_date > '".$TodayDt."' AND a.id NOT IN(select distinct workshop_id FROM atom_results where company_id=a.company_id and questionset_id=b.questionset_id )"
                . " group by a.id";
        $lcSqlstr .= " UNION ALL select a.company_id,a.id FROM workshop as a LEFT JOIN workshop_questionset_post as b"
                . " ON b.workshop_id=a.id and b.questionset_id=".$QuestionSet ." LEFT JOIN questionset_trainer as c ON c.questionset_id=b.questionset_id "
                . " WHERE b.questionset_id=" . $QuestionSet . " AND a.end_date > '".$TodayDt."' AND a.id NOT IN(select distinct workshop_id FROM atom_results where company_id=a.company_id and questionset_id=b.questionset_id )"
                . " group by a.id) as a group by a.company_id,a.id";
        $result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if (count((array)$ResultSet) > 0) {
            foreach ($ResultSet as $value) {

                $Workshop_Id = $value->workshop_id;
                $Company_id = $value->company_id;

                $CheckObjExists = $this->db->query("select id FROM workshop_questions where questionset_id=$QuestionSet AND "
                        . "workshop_id=" . $Workshop_Id . " AND question_id=" . $idStr);
                $CheckExists = $CheckObjExists->row();
                if (count((array)$CheckExists) > 0) {
                    continue;
                }
                $object_Set = $this->db->query("select ifnull(max(sorting),0)+1 as max_sort FROM workshop_questions where  "
                . "workshop_id=" . $Workshop_Id . " AND questionset_id=$QuestionSet");
                $sorting_Set = $object_Set->row();
                if(count((array)$sorting_Set)>0){
                    $question_sort =$sorting_Set->max_sort;
                }else{
                    $question_sort =1;
                }
                $lcSqlstr = " SELECT a.trainer_id FROM questionset_trainer as a where a.questionset_id=$QuestionSet and"
                        . " a.topic_id IN(select topic_id from questions where id =$idStr) group by a.topic_id";
                $ObjSet = $this->db->query($lcSqlstr);
                $RowSet = $ObjSet->row();
                $query = " insert into workshop_questions(company_id,workshop_id,questionset_id,question_id,topic_id,subtopic_id,"
                    . "question_title,option_a,option_b,option_c,option_d,correct_answer,tip,hint_image,youtube_link,trainer_id,sorting) "
                    . "SELECT $Company_id as company_id,$Workshop_Id as workshop_id,$QuestionSet as questionSet,a.id,a.topic_id,"
                    . "a.subtopic_id,a.question_title,"
                    . "a.option_a,a.option_b,a.option_c,a.option_d,a.correct_answer,a.tip,a.hint_image,a.youtube_link,"
                    . $RowSet->trainer_id . " as trainer_id, ".$question_sort ." as sorting "
                    . "FROM questions as a where a.id=" . $idStr;
                $this->db->query($query);
                //echo $this->db->last_query();
            }
        }
    }

}
