<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Workshop_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function fetch_access_data() {
        $query = "SELECT * FROM company_modules WHERE status='1' ORDER BY module_sort,sortorder";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function fetch_user($id) {
        $query = "select u.*,c.description as country_name,s.description as state_name,ct.description as city_name,ar.rolename,co.company_name from workshop as u LEFT JOIN country AS c ON c.id = u.country LEFT JOIN state AS s ON s.id = u.state LEFT JOIN city AS ct ON ct.id = u.city LEFT JOIN company_roles AS ar ON ar.arid = u.role LEFT JOIN company AS co ON co.id= u.company_id where u.userid='" . $id . "' and u.deleted=0";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function SelectedCompany($workshop_id) {
        $query = "SELECT c.id,c.company_name as text,ifnull(wc.company_id,0) as wc_id FROM company as c LEFT JOIN"
                . " workshop as wc ON wc.company_id=c.id AND  wc.id=" . $workshop_id;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function SelectedFeedbackForm($workshop_id, $company_id) {
        $query = "SELECT f.id,f.form_name as form_name,ifnull(wc.company_id,0) as wc_id FROM feedback_form_header as f LEFT JOIN "
                . " workshop as wc ON wc.feedbackform_id=f.id AND  wc.id=" . $workshop_id . " WHERE f.company_id=$company_id";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function SelectedWorkshopType($workshop_id) {
        $query = "SELECT wt.id,wt.workshop_type,ifnull(wc.company_id,0) as wc_id FROM workshoptype_mst as wt LEFT JOIN "
                . " workshop as wc ON wc.workshop_type=wt.id AND  wc.id=" . $workshop_id;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function SelectedRegion($workshop_id) {
        $query = "SELECT r.id,r.region_name,ifnull(wc.company_id,0) as wc_id FROM region as r LEFT JOIN "
                . " workshop as wc ON wc.region=r.id AND  wc.id=" . $workshop_id;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function pre_SelectedQuestionSet($workshop_id, $Q_type, $company_id) {
        $query = '';
        if ($Q_type != '') {
            if ($Q_type == 1) {
                $query = "SELECT c.id,c.title as text,ifnull(wc.questionset_id,0) as wc_id,wc.status,wc.questions_limit FROM question_set as c LEFT JOIN"
                        . " workshop_questionset_pre as wc ON wc.questionset_id=c.id AND wc.workshop_id=" . $workshop_id;
            } else {
                $query = "SELECT c.id,c.title as text,ifnull(wc.feedbackset_id,0) as wc_id,wc.status, wc.questions_limit FROM feedback as c LEFT JOIN"
                        . " workshop_feedbackset_pre as wc ON wc.feedbackset_id =c.id AND wc.workshop_id=" . $workshop_id;
            }
            $query .=" WHERE c.company_id=$company_id";
            $result = $this->db->query($query);
            return $result->result();
        }
    }

    public function getQuestionCountData($Q_type, $QsnSet) {
        if ($Q_type == 1) {
            $query = "SELECT c.id,c.title as text,timer,count(wq.id) as totalqsn FROM question_set as c "
                    . " LEFT JOIN questionset_trainer as qt ON qt.questionset_id=c.id"
                    . " LEFT JOIN questions as wq ON wq.status=1 AND wq.topic_id=qt.topic_id AND wq.subtopic_id=qt.subtopic_id AND c.language_id=wq.language_id "
                    . "  ";
            $query .=" WHERE c.id= " . $QsnSet . " AND wq.id NOT IN(select question_id FROM question_inactive where questionset_id=$QsnSet)";
        } else {
            $query = "SELECT c.id,c.title as text,timer,count(wq.id) as totalqsn FROM feedback as c "
                    . " LEFT JOIN feedbackset_type as ft ON ft.feedbackset_id =c.id "
                    . " LEFT JOIN feedback_questions as wq ON wq.status=1 AND  wq.feedback_type_id=ft.feedback_type_id "
                    . " AND wq.feedback_subtype_id=ft.feedback_subtype_id AND c.language_id=wq.language_id ";
            $query .=" WHERE c.id= " . $QsnSet . " AND wq.id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id=$QsnSet)";
        }

        $result = $this->db->query($query);
        return $result->row();
    }

    public function pre_QuestionSetStatus($workshop_id, $Q_type, $QsnSet = "") {
        $query = '';
        if ($Q_type != '') {
            if ($Q_type == 1) {
                $query = "SELECT 'pre' as session,wq.company_id,wc.workshop_id,c.id,c.title as text,timer,count(wq.id) as totalqsn,wc.status,wc.hide_answer,
				wc.questions_limit FROM workshop_questionset_pre as wc LEFT JOIN "
                        . " question_set as c ON c.id=wc.questionset_id LEFT JOIN workshop_questions as wq "
                        . " ON wq.questionset_id=wc.questionset_id AND wq.workshop_id=wc.workshop_id ";
                $query .=" WHERE wc.workshop_id=" . $workshop_id;
                if ($QsnSet != "") {
                    $query .=" AND wq.questionset_id= " . $QsnSet;
                }
                $query .=" group by wc.questionset_id order by wc.id";
            } else {
                $query = "SELECT 'pre' as session,wq.company_id,wc.workshop_id,c.id,c.title as text,timer,count(wq.id) as totalqsn,wc.status,
				wc.questions_limit FROM workshop_feedbackset_pre as wc LEFT JOIN "
                        . " feedback as c ON c.id=wc.feedbackset_id LEFT JOIN workshop_feedback_questions as wq "
                        . "ON wq.feedbackset_id=wc.feedbackset_id AND wq.workshop_id=wc.workshop_id AND wq.id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id=wc.feedbackset_id)";
                $query .=" WHERE  wc.workshop_id=" . $workshop_id;
                if ($QsnSet != "") {
                    $query .=" AND wq.feedbackset_id= " . $QsnSet;
                }
                $query .=" group by wc.feedbackset_id order by wc.id ";
            }
//                echo $query;
//                exit;
            $result = $this->db->query($query);
            return $result->result();
        }
    }

    public function post_QuestionSetStatus($workshop_id, $Q_type, $QsnSet = "") {
        $query = '';
        if ($Q_type != '') {
            if ($Q_type == 1) {
                $query = "SELECT 'post' as session,wq.company_id,wc.workshop_id,c.id,c.title as text,timer,count(wq.id) as totalqsn,wc.status,wc.hide_answer,
				wc.questions_limit FROM workshop_questionset_post as wc LEFT JOIN "
                        . " question_set as c ON wc.questionset_id=c.id LEFT JOIN workshop_questions as wq "
                        . "ON wq.questionset_id=wc.questionset_id AND wq.workshop_id=wc.workshop_id ";
                $query .=" WHERE  wc.workshop_id=" . $workshop_id;
                if ($QsnSet != "") {
                    $query .=" AND wq.questionset_id= " . $QsnSet;
                }
                $query .=" group by wc.questionset_id order by wc.id";
            } else {
                $query = "SELECT 'post' as session,wq.company_id,wc.workshop_id,c.id,c.title as text,timer,count(wq.id) as totalqsn,wc.status,wc.questions_limit  FROM workshop_feedbackset_post as wc LEFT JOIN "
                        . " feedback as c ON wc.feedbackset_id=c.id LEFT JOIN workshop_feedback_questions as wq "
                        . "ON wq.feedbackset_id=wc.feedbackset_id AND wq.workshop_id=wc.workshop_id";
                $query .=" WHERE  wc.workshop_id=" . $workshop_id;
                if ($QsnSet != "") {
                    $query .=" AND wq.feedbackset_id= " . $QsnSet;
                }
                $query .=" group by wc.feedbackset_id order by wc.id";
            }
//                echo $query;exit
            $result = $this->db->query($query);
            return $result->result();
        }
    }

    public function New_pre_QuestionSetStatus($workshop_id, $Q_type, $QsnSet = "") {
        $query = '';
        if ($Q_type != '') {
            if ($Q_type == 1) {
                $query = "SELECT 'pre' as session,wq.company_id,wc.workshop_id,c.id,c.title as text,timer,count(wq.id) as totalqsn,wc.status,wc.questions_limit , wc.hide_answer "
                        . "FROM workshop_questionset_pre as wc LEFT JOIN question_set as c ON wc.questionset_id=c.id "
                        . "LEFT JOIN questionset_trainer as qt ON qt.questionset_id=c.id "
                        . "LEFT JOIN questions as wq  ON wq.status=1 AND wq.language_id=c.language_id AND wq.topic_id=qt.topic_id AND wq.subtopic_id=qt.subtopic_id AND wq.id NOT IN(select question_id FROM question_inactive where questionset_id= wc.questionset_id)   ";
                $query .=" WHERE wc.workshop_id=" . $workshop_id;
                if ($QsnSet != "") {
                    $query .=" AND wc.questionset_id= " . $QsnSet;
                }
                $query .=" group by wc.questionset_id order by wc.id";
            } else {
                $query = "SELECT 'pre' as session,wq.company_id,wc.workshop_id,c.id,c.title as text,timer,count(wq.id) as totalqsn,wc.status,wc.questions_limit FROM workshop_feedbackset_pre as wc LEFT JOIN "
                        . " feedback as c ON wc.feedbackset_id=c.id LEFT JOIN feedbackset_type as ft ON ft.feedbackset_id =c.id "
                        . " LEFT JOIN feedback_questions as wq ON wq.status=1 AND wq.language_id=c.language_id AND wq.feedback_type_id=ft.feedback_type_id "
                        . " AND wq.feedback_subtype_id=ft.feedback_subtype_id AND wq.id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id= wc.feedbackset_id)";
                $query .=" WHERE  wc.workshop_id=" . $workshop_id;
                if ($QsnSet != "") {
                    $query .=" AND wq.feedbackset_id= " . $QsnSet;
                }
                $query .=" group by wc.feedbackset_id order by wc.id ";
            }
//                echo $query;
//                exit;
            $result = $this->db->query($query);
            return $result->result();
        }
    }

    public function New_post_QuestionSetStatus($workshop_id, $Q_type, $QsnSet = "") {
        $query = '';
        if ($Q_type != '') {
            if ($Q_type == 1) {
                $query = "SELECT 'post' as session,wq.company_id,wc.workshop_id,c.id,c.title as text,timer,count(wq.id) as totalqsn,wc.status,wc.hide_answer,wc.questions_limit "
                        . "FROM workshop_questionset_post as wc LEFT JOIN question_set as c ON wc.questionset_id=c.id "
                        . "LEFT JOIN questionset_trainer as qt ON qt.questionset_id=c.id "
                        . "LEFT JOIN questions as wq ON wq.status=1 AND wq.language_id=c.language_id AND wq.topic_id=qt.topic_id AND wq.subtopic_id=qt.subtopic_id AND wq.id NOT IN(select question_id FROM question_inactive where questionset_id= wc.questionset_id)   ";
                $query .=" WHERE wc.workshop_id=" . $workshop_id;
                if ($QsnSet != "") {
                    $query .=" AND wc.questionset_id= " . $QsnSet;
                }
                $query .=" group by wc.questionset_id order by wc.id";
            } else {
                $query = "SELECT 'post' as session,wq.company_id,wc.workshop_id,c.id,c.title as text,timer,count(wq.id) as totalqsn,wc.status,wc.questions_limit "
                        . "FROM workshop_feedbackset_post as wc LEFT JOIN "
                        . " feedback as c ON wc.feedbackset_id=c.id LEFT JOIN feedbackset_type as ft ON ft.feedbackset_id =c.id "
                        . " LEFT JOIN feedback_questions as wq ON wq.status=1 AND wq.language_id=c.language_id AND wq.feedback_type_id=ft.feedback_type_id "
                        . " AND wq.feedback_subtype_id=ft.feedback_subtype_id AND wq.id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id= wc.feedbackset_id) ";
                $query .=" WHERE  wc.workshop_id=" . $workshop_id;
                if ($QsnSet != "") {
                    $query .=" AND wq.feedbackset_id= " . $QsnSet;
                }
                $query .=" group by wc.feedbackset_id order by wc.id ";
            }
//                echo $query;
//                exit;
            $result = $this->db->query($query);
            return $result->result();
        }
    }

    public function post_SelectedQuestionSet($workshop_id, $Q_type, $company_id) {
        $query = '';
        if ($Q_type != '') {
            if ($Q_type == 1) {
                $query = "SELECT c.id,c.title as text,ifnull(wc.questionset_id,0) as wc_id,wc.status,wc.questions_limit FROM question_set as c LEFT JOIN"
                        . " workshop_questionset_post as wc ON wc.questionset_id=c.id AND wc.workshop_id=" . $workshop_id;
            } else {
                $query = "SELECT c.id,c.title as text,ifnull(wc.feedbackset_id,0) as wc_id,wc.status,wc.questions_limit FROM feedback as c LEFT JOIN"
                        . " workshop_feedbackset_post as wc ON wc.feedbackset_id =c.id AND wc.workshop_id=" . $workshop_id;
            }
            $query .=" WHERE c.company_id=$company_id";
            $result = $this->db->query($query);
            return $result->result();
        }
    }

    public function SelectedFeedBack($workshop_id, $cmp_id) {
        $query = "SELECT f.id,f.title as text,ifnull(wf.feedback_id,0) as wc_id FROM feedback as f LEFT JOIN"
                . " workshop_feedback as wf ON wf.feedback_id=f.id AND  wf.workshop_id=" . $workshop_id . " where f.company_id=" . $cmp_id;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function SelectedReward($workshop_id, $cmp_id) {
        $query = "SELECT r.id,r.reward_name as text,ifnull(wr.reward_id,0) as wr_id FROM reward as r LEFT JOIN"
                . " workshop_reward as wr ON wr.reward_id=r.id AND  wr.workshop_id=" . $workshop_id . " where r.company_id=" . $cmp_id;
        $result = $this->db->query($query);
        return $result->result();
    }

    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT a.id,a.workshop_name,a.otp,a.workshop_image,b.company_name,"
                . "DATE_FORMAT(a.start_date,'%d-%m-%Y') as start_date ,DATE_FORMAT(a.end_date,'%d-%m-%Y') as end_date,a.status,
                a.start_date as start_dt_time,a.end_date as end_dt_time,"
                . " wt.workshop_type,r.region_name FROM workshop as a "
                . " LEFT JOIN workshoptype_mst wt on wt.id=a.workshop_type"
                . " LEFT JOIN region r on r.id=a.region"
                . " LEFT JOIN company b on b.id=a.company_id";
        $query .= " $dtWhere  group by a.id $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(a.id) as total FROM workshop as a "
                . " LEFT JOIN workshoptype_mst wt on wt.id=a.workshop_type"
                . " LEFT JOIN region r on r.id=a.region"
                . " LEFT JOIN company b on b.id=a.company_id"
                . " $dtWhere ";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }

    public function find_by_value($id) {
        $query = "Select ar.arid,ar.rolename,ar.description,ar.status from company_roles as ar where ar.deleted=0 and ar.arid=$id";
        $result = $this->db->query($query);
        return $result->row();
    }

    public function find_by_id($id) {
        $query = "Select ar.arid,ar.rolename,ar.description,ar.status,arm.moduleid,arm.allow_access,arm.allow_add,arm.allow_view,arm.allow_edit,arm.allow_delete,arm.allow_print,arm.allow_import,arm.allow_export from company_roles as ar left join
        company_role_modules as arm on arm.roleid = ar.arid where ar.deleted=0 and ar.arid=$id";
        $result = $this->db->query($query);
        $output = $result->result_array();
        $resultdata = array();
        foreach ($output as $key => $value) {
            $resultdata[$value['moduleid']] = $value;
        }
        return $resultdata;
    }

    public function remove($id) {
        $this->db->where('userid', $id);
        $this->db->delete('workshop');
        return true;
    }

    public function CheckUserAssignRole($roleID) {
        $sQuery = "SELECT userid FROM workshop WHERE role= $roleID";
        $query = $this->db->query($sQuery);
        return (count((array)$query->row()) > 0 ? false : true);
    }

    public function validate($data) {
        $status = "false";
        $query = $this->db->query("select count(*) as found from workshop where status='1' and deleted='0' and username='" . $data['loginid'] . "'");
        foreach ($query->result() as $row) {
            if ($row->found > 0) {
                $status = "false";
            } else {
                $status = "true";
            }
        }
        return $status;
    }

    public function validate_edit($data) {
        $status = "false";
        $query = $this->db->query("select count(*) as found from workshop where status='1' and deleted='0' and username='" . $data['loginid'] . "' and userid!='" . base64_decode(urldecode($data['user_id'])) . "'");
        foreach ($query->result() as $row) {
            if ($row->found > 0) {
                $status = "false";
            } else {
                $status = "true";
            }
        }
        return $status;
    }

//    public function check_Workshop($WorkshopName, $id='') {
//    
//        $query = "Select username from workshop  where workshop_name like '" . $WorkshopName ."'";
//        if($id<>''){
//            $query .= " AND  id != " . $id ;
//        }
//        $result = $this->db->query($query);
//        return $output = $result->row();
//    }
    public function check_workshop($Company_id = "", $workshop_name, $workshop_id = '') {

        $querystr = "Select workshop_name from workshop where workshop_name like '" . str_replace("'", "\'", $workshop_name) . "'";
        if ($Company_id != '') {
            $querystr.=" and company_id=" . $Company_id;
        }
        if ($workshop_id != '') {
            $querystr.=" and id!=" . $workshop_id;
        }

        $query = $this->db->query($querystr);
        return (count((array)$query->row()) > 0 ? true : false);
    }

    public function LoadParticipantUsers($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT w.id,u.user_id,CONCAT(u.firstname,' ',u.lastname) as name,u.area,u.email,u.mobile,tr.region_name "
                . "FROM device_users as u LEFT JOIN workshop_users as w ON w.user_id=u.user_id
                    LEFT JOIN region as tr ON tr.id=u.region_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(u.user_id) as total FROM device_users as u LEFT JOIN workshop_users as w ON w.user_id=u.user_id 
                    LEFT JOIN region as tr ON tr.id=u.region_id ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->row();
        $data['dtTotalRecords'] = $data_array->total;
        return $data;
    }

    public function RemovedParticipantUser() {
        
    }

    public function CopyParticipantUsers($Workshop_Id, $Copy_id) {
        $query = " insert into workshop_users(workshop_id,user_id) SELECT $Workshop_Id as workshop_id,user_id "
                . "FROM workshop_users where workshop_id=$Copy_id";
        $this->db->query($query);
        return true;
    }

    public function CopyWorkshopQuestion($Company_id, $Workshop_Id, $QuestionSet) {
        $result = $this->db->query("select id FROM workshop_questions where questionset_id=$QuestionSet AND workshop_id=" . $Workshop_Id);
        $data_array = $result->row();
        if (count((array)$data_array) == 0) {
            $query = " insert into workshop_questions(company_id,workshop_id,questionset_id,question_id,topic_id,subtopic_id,"
                    . "question_title,option_a,option_b,option_c,option_d,correct_answer,tip,hint_image,youtube_link,trainer_id,language_id,sorting) "
                    . "select $Company_id as company_id,$Workshop_Id as workshop_id,$QuestionSet as questionSet,ft.id,"
                    . "ft.topic_id," . "ft.subtopic_id,ft.question_title,ft.option_a,ft.option_b,ft.option_c,ft.option_d,
                    ft.correct_answer,ft.tip,ft.hint_image,ft.youtube_link,
                    ft.trainer_id,ft.language_id,ifnull(ft.temp_sorting,@od:=@od+1) sorting FROM ("
                    . "SELECT a.id,a.topic_id,a.subtopic_id,a.question_title,a.option_a,a.option_b,
                    a.option_c,a.option_d,a.correct_answer,a.tip,a.hint_image,a.youtube_link,b.trainer_id,a.language_id,
                    tq.sorting temp_sorting "
                    . "FROM questions as a LEFT JOIN workshop_questionset_trainer as b ON a.topic_id=b.topic_id "
                    . "AND a.subtopic_id=b.subtopic_id AND b.workshop_id=$Workshop_Id AND questionset_id=$QuestionSet
                     INNER JOIN question_set as qt ON qt.language_id=a.language_id AND qt.id=$QuestionSet "
                    . "LEFT JOIN temp_questions_order tq ON tq.question_type=1 AND tq.question_id =a.id AND tq.questionset_id =$QuestionSet AND tq.workshop_id=$Workshop_Id
                        ,(SELECT @od:= 0) AS od where a.status=1 and b.questionset_id=" . $QuestionSet . ""
                    . " AND a.id NOT IN(select question_id FROM question_inactive where questionset_id=$QuestionSet)"
                    . " group by a.id,a.company_id,a.topic_id,a.subtopic_id,b.trainer_id order by a.id"
                    . ") as ft ,(SELECT @od:= 0) AS od";
            $this->db->query($query);
        }
        return true;
    }

    public function CopyFeedbackQuestion($Company_id, $Workshop_Id, $feedbackset_id) {
        $result = $this->db->query("select id FROM workshop_feedback_questions where feedbackset_id=$feedbackset_id AND workshop_id=" . $Workshop_Id);
        $data_array = $result->row();
        //print_r($result);

        if (count((array)$data_array) == 0) {
            $query = " insert into workshop_feedback_questions(company_id,workshop_id,feedbackset_id,question_id,"
                    . "type_id,subtype_id,question_title,"
                    . "option_a,weight_a,option_b,weight_b,option_c,weight_c,option_d,weight_d,"
                    . "option_e,weight_e,option_f,weight_f,multiple_allow,hint_image,tip,question_type,min_length,"
                    . "max_length,question_timer,text_weightage,language_id,sorting ) "
                    . "select $Company_id as company_id,$Workshop_Id as workshop_id,$feedbackset_id as feedbackset_id,ft.id,"
                    . "ft.feedback_type_id,ft.feedback_subtype_id,ft.question_title,"
                    . "ft.option_a,ft.weight_a,ft.option_b,ft.weight_b,ft.option_c,ft.weight_c,ft.option_d,ft.weight_d,"
                    . "ft.option_e,ft.weight_e,ft.option_f,ft.weight_f,ft.multiple_allow,ft.hint_image,ft.tip,ft.question_type,ft.min_length,"
                    . "ft.max_length,ft.question_timer,ft.text_weightage,ft.language_id,ifnull(ft.temp_sorting,@od:=@od+1) sorting FROM ("
                    . "SELECT a.id,"
                    . "a.feedback_type_id,a.feedback_subtype_id,a.question_title,"
                    . "a.option_a,a.weight_a,a.option_b,a.weight_b,a.option_c,a.weight_c,a.option_d,a.weight_d,"
                    . "a.option_e,a.weight_e,a.option_f,a.weight_f,a.multiple_allow,a.hint_image,a.tip,a.question_type,a.min_length,"
                    . "a.max_length,a.question_timer,a.text_weightage,a.language_id,tq.sorting as temp_sorting "
                    . "FROM feedback_questions as a LEFT JOIN feedbackset_type as b ON a.feedback_type_id=b.feedback_type_id "
                    . "AND a.feedback_subtype_id=b.feedback_subtype_id INNER JOIN feedback as qt ON qt.language_id=a.language_id AND qt.id=$feedbackset_id "
                    . " LEFT JOIN temp_questions_order tq ON tq.question_type=2 AND tq.question_id =a.id AND tq.questionset_id =$feedbackset_id AND tq.workshop_id=$Workshop_Id
                     where a.status=1 and b.feedbackset_id=" . $feedbackset_id . ""
                    . " AND a.id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id=$feedbackset_id)"
                    . " group by a.id,a.company_id,a.feedback_type_id,a.feedback_subtype_id order by a.id) as ft ,(SELECT @od:= 0) AS od";
            $this->db->query($query);
        }
        return true;
    }

    public function RemoveWorkshopQuestions($Workshop_id) {

        //$StrQusSet = implode(",", $ConfirmQuesSet);
        $query = "delete FROM workshop_questions where workshop_id=" . $Workshop_id . " AND questionset_id "
                . "NOT IN(select questionset_id FROM workshop_questionset_pre where workshop_id= $Workshop_id UNION ALL "
                . " select questionset_id FROM workshop_questionset_post where workshop_id= $Workshop_id  )";
        $this->db->query($query);
        return true;
    }

    public function RemoveWorkshopTrainer($Workshop_id) {
        $query = "delete FROM workshop_questionset_trainer where workshop_id=" . $Workshop_id . " AND questionset_id "
                . "NOT IN(select questionset_id FROM workshop_questionset_pre where workshop_id= $Workshop_id UNION ALL "
                . " select questionset_id FROM workshop_questionset_post where workshop_id= $Workshop_id  )";
        $this->db->query($query);
        return true;
    }

    public function RemoveWorkshopFeedback_Qus($Workshop_id) {
        //$StrQusSet = implode(",", $ConfirmQuesSet);
        $query = "delete FROM workshop_feedback_questions where workshop_id=" . $Workshop_id . " "
                . "AND feedbackset_id NOT IN(select feedbackset_id FROM workshop_feedbackset_pre where workshop_id= $Workshop_id UNION ALL "
                . " select feedbackset_id FROM workshop_feedbackset_post where workshop_id= $Workshop_id  )";
        $this->db->query($query);
        return true;
    }

    public function CheckQuestionAvaiable($QuestionSet) {
        $query = "select a.id FROM questions as a INNER JOIN questionset_trainer as b ON a.topic_id=b.topic_id AND"
                . " a.subtopic_id=a.subtopic_id INNER JOIN question_set as q ON q.id=b.questionset_id AND q.language_id=a.language_id "
                . " where a.status=1 AND b.questionset_id=" . $QuestionSet;
        $query .=" AND a.id NOT IN(select question_id FROM question_inactive where questionset_id=$QuestionSet)";
        $result = $this->db->query($query);
        $data_array = $result->row();
        return (count((array)$data_array) > 0 ? true : false );
    }

    public function CheckQuestionSetPlayed($Workshop_id, $QuestionSet, $Sessions = '') {
        $query = "select a.id FROM atom_results as a WHERE a.workshop_id=$Workshop_id "
                . "  AND a.questionset_id=" . $QuestionSet;
        if ($Sessions != "") {
            $query .= " AND a.workshop_session='$Sessions' ";
        }
        $result = $this->db->query($query);
        $data_array = $result->row();
        $ReturnFlag = false;
        if (count((array)$data_array) > 0) {
            $ReturnFlag = true;
        }
        return $ReturnFlag;
    }

    public function CheckFeedbackSetPlayed($Workshop_id, $QuestionSet, $Sessions) {
        $query = "select a.id FROM atom_feedback as a WHERE a.workshop_id=$Workshop_id "
                . " AND a.workshop_session='$Sessions' AND a.feedbackset_id=" . $QuestionSet;
        $result = $this->db->query($query);
        $data_array = $result->row();
        $ReturnFlag = false;
        if (count((array)$data_array) > 0) {
            $ReturnFlag = true;
        }
        return $ReturnFlag;
    }

    public function CheckFeedbackQuestionAvaiable($QuestionSet) {
        $query = "select a.id FROM feedback_questions as a INNER JOIN feedbackset_type as b ON a.feedback_type_id=b.feedback_type_id AND"
                . " a.feedback_subtype_id=a.feedback_subtype_id INNER JOIN feedback as q ON q.id=b.feedbackset_id AND q.language_id=a.language_id "
                . " where a.status=1 AND b.feedbackset_id=" . $QuestionSet;
        $query .=" AND a.id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id=$QuestionSet)";
        $result = $this->db->query($query);
        $data_array = $result->row();
        return (count((array)$data_array) > 0 ? true : false );
    }

    public function CheckWorkshopExpired($Workshop_id) {
        $lcSqlstr = "select id FROM workshop WHERE id=" . $Workshop_id . " AND "
                . "start_date <= '" . date('Y-m-d H:i') . "'";
        $result = $this->db->query($lcSqlstr);
        $data_array = $result->row();
        return (count((array)$data_array) > 0 ? true : false);
    }

    public function topic_subtopic_list($questionset_id, $workshop_id = "", $type, $session) {
        $where = '';
        $session_val = 1;
        if ($session == 'post') {
            $session_val = 2;
        }
        if ($type == 1) {
            $query = "SELECT wt.id,wt.questions_trans_id as qsettrainertable_id,wt.trainer_id ,qt.description as topic,qs.description as subtopic,COUNT(wq.id) AS totalqsn "
                    . " From workshop_questionset_trainer as wt "
                    . " LEFT JOIN workshop_questions AS wq ON wq.workshop_id=wt.workshop_id AND wq.questionset_id=wt.questionset_id AND wq.topic_id=wt.topic_id "
                    . " AND wq.subtopic_id=wt.subtopic_id "
                    . " LEFT JOIN question_topic AS qt ON qt.id = wq.topic_id "
                    . " LEFT JOIN question_subtopic AS qs ON qs.id=wq.subtopic_id "
                    . " WHERE  wt.questionset_id = " . $questionset_id;
            if ($workshop_id != "" || $workshop_id != "0") {
                $query .= " AND wt.workshop_id = " . $workshop_id;
            }
            $query .= " group by wt.id ";
        } else {
            if ($workshop_id != "" || $workshop_id != "0") {
                $where .= " AND wq.workshop_id = " . $workshop_id;
            }
            $query = "SELECT '' as trainer_name,qt.description as topic, qs.description as subtopic,COUNT(wq.id) AS totalqsn "
                    . " From workshop_feedback_questions AS wq LEFT JOIN feedback_type AS qt ON qt.id = wq.type_id "
                    . " LEFT JOIN feedback_subtype AS qs ON qs.id=wq.subtype_id "
                    . " WHERE wq.feedbackset_id = " . $questionset_id . " $where GROUP BY wq.type_id,wq.subtype_id ";
        }
	
        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function UpdateQusWorkshop($questionset_id, $workshop_id) {
        $query = " update workshop_questions as a LEFT JOIN workshop_questionset_trainer as b ON "
                . "b.questionset_id=a.questionset_id AND a.workshop_id=b.workshop_id AND "
                . "a.topic_id=b.topic_id AND a.subtopic_id =b.subtopic_id "
                . " SET a.trainer_id= b.trainer_id "
                . " where b.workshop_id= $workshop_id AND b.questionset_id= $questionset_id ";
        $this->db->query($query);
    }

    public function questionset_details($questionset_id) {
        $query = "SELECT id as qsettrainertable_id,trainer_id,topic_id,subtopic_id FROM  questionset_trainer where questionset_id=" . $questionset_id;
        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function workshop_questionset_details($Workshop_Id, $questionset_id, $copy_id) {
        $query = "INSERT INTO workshop_questionset_trainer(workshop_id,questionset_id,questions_trans_id,topic_id,subtopic_id,trainer_id) "
                . "SELECT $Workshop_Id as workshop_id,questionset_id,questions_trans_id,topic_id,subtopic_id,trainer_id FROM  "
                . " workshop_questionset_trainer where questionset_id=" . $questionset_id . " AND workshop_id=" . $copy_id;
        $this->db->query($query);
        return true;
    }

    public function topic_subtopic_creat($questionset_id, $type) {
        if ($type == 1) {
            $query = "SELECT qtr.id,qtr.id as qsettrainertable_id,cu.userid as trainer_id,CONCAT(cu.first_name,' ',cu.last_name) as trainer_name,"
                    . " qt.description as topic, qs.description as subtopic,COUNT(wq.id) AS totalqsn "
                    . " FROM questionset_trainer as qtr LEFT JOIN question_set as q ON q.id= qtr.questionset_id "
                    . " LEFT JOIN questions as wq ON wq.topic_id=qtr.topic_id AND wq.subtopic_id=qtr.subtopic_id AND wq.language_id=q.language_id"
                    . " LEFT JOIN question_topic AS qt ON qt.id = qtr.topic_id "
                    . " LEFT JOIN question_subtopic AS qs ON qs.id=qtr.subtopic_id LEFT JOIN company_users AS cu ON cu.userid=qtr.trainer_id "
                    . " WHERE qtr.questionset_id=$questionset_id AND wq.id NOT IN(select question_id FROM question_inactive where questionset_id=$questionset_id) GROUP BY qtr.topic_id,qtr.subtopic_id,qtr.trainer_id ";
        } else {
            $query = "SELECT '' as trainer_name,qt.description as topic, qs.description as subtopic,COUNT(wq.id) AS totalqsn "
                    . " FROM feedbackset_type AS ft LEFT JOIN feedback as f ON f.id=ft.feedbackset_id "
                    . " LEFT JOIN feedback_questions AS wq ON wq.feedback_type_id=ft.feedback_type_id AND wq.feedback_subtype_id=ft.feedback_subtype_id AND wq.language_id=f.language_id"
                    . " LEFT JOIN feedback_type AS qt ON qt.id = ft.feedback_type_id "
                    . " LEFT JOIN feedback_subtype AS qs ON qs.id=ft.feedback_subtype_id "
                    . " WHERE ft.feedbackset_id=$questionset_id AND wq.id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id=$questionset_id) GROUP BY ft.feedback_type_id,ft.feedback_subtype_id ";
        }
//           echo $query;exit;
        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function UserDataTable($dtWhere, $dtOrder, $dtLimit, $mode) {
        if ($mode == 2 || $mode == 3) {
            $query = "SELECT u.user_id,u.email,CONCAT(u.firstname,' ',u.lastname) AS name
                FROM workshop_tester_users AS w LEFT JOIN device_users AS u ON u.user_id = w.tester_id ";
        } else {
            $query = "SELECT u.user_id,u.email,CONCAT(u.firstname,' ',u.lastname) AS name
                  FROM device_users AS u ";
        }
        $query .= " $dtWhere $dtOrder $dtLimit";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        if ($mode == 2 || $mode == 3) {
            $query = "SELECT COUNT(u.user_id) as total FROM workshop_tester_users as w left join device_users as u ON u.user_id = w.tester_id ";
        } else {
            $query = "SELECT COUNT(u.user_id) as total FROM device_users as u left join company as c on u.company_id=c.id ";
        }
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }

    public function get_TraineeRegionList($company_id) {
        $query = "SELECT id,region_name FROM region where status=1 AND company_id = $company_id AND"
                . " id IN(select distinct region_id FROM device_users where company_id=$company_id) order by region_name ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function LoadUsersDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query = "SELECT u.user_id,u.company_id,CONCAT(u.firstname,' ',u.lastname) as name,u.emp_id,u.area,"
                . "u.email,u.mobile,u.otp,u.otp_last_attempt,u.status,u.istester,tr.region_name "
                . "FROM device_users as u "
                . " LEFT JOIN region as tr ON tr.id=u.region_id   ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(u.user_id) as total FROM device_users as u "
                . " LEFT JOIN region as tr ON tr.id=u.region_id   ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->row();
        $data['dtTotalRecords'] = $data_array->total;
        return $data;
    }

    public function get_userid($company_id, $Emp_code) {
        $query = " SELECT user_id from device_users where emp_id LIKE " . $this->db->escape($Emp_code);
        if ($company_id != "") {
            $query .=" AND company_id=" . $company_id;
        }
        $query = $this->db->query($query);
        return $query->row();
    }

    public function getQuestionLoadData($dtWhere, $dtOrder, $dtLimit, $workshop_id, $isnew = '') {
        if ($isnew == 1) {
			
            $query = "SELECT q.question_title,q.id as question_id,ifnull(tq.sorting,@a:=@a+1) sorting,"
                    . " CONCAT(cu.first_name,' ',cu.last_name) AS trainer_name,
                     qt.description AS topic,IFNULL(qs.description,'No sub-topic') AS subtopic,
                     CASE q.correct_answer WHEN 'a' THEN q.option_a WHEN 'b' THEN q.option_b 
                     WHEN 'c' THEN q.option_c WHEN 'd' THEN q.option_d 
                     ELSE 'Wrong' END AS correct_answer  "
                    . " FROM questionset_trainer as qtr LEFT JOIN question_set as qst ON qst.id= qtr.questionset_id "
                    . " LEFT JOIN questions as q ON q.topic_id=qtr.topic_id AND q.subtopic_id=qtr.subtopic_id  AND q.language_id=qst.language_id"
                    . " LEFT JOIN question_topic AS qt ON qt.id = qtr.topic_id"
                    . " LEFT JOIN temp_questions_order tq ON tq.question_type=1 AND tq.question_id =q.id AND tq.questionset_id =qtr.questionset_id AND tq.workshop_id=$workshop_id "
                    . " LEFT JOIN question_subtopic AS qs ON qs.id=qtr.subtopic_id LEFT JOIN company_users AS cu ON cu.userid=qtr.trainer_id,(SELECT @a:= 0) AS a ";
        } else {
            $query = "  SELECT q.question_title,q.question_id,q.sorting,
                    CONCAT(cu.first_name,' ',cu.last_name) AS trainer_name,
                    qt.description AS topic,IFNULL(qs.description,'No sub-topic') AS subtopic,
                    CASE q.correct_answer WHEN 'a' THEN q.option_a WHEN 'b' THEN q.option_b 
                    WHEN 'c' THEN q.option_c WHEN 'd' THEN q.option_d 
                    ELSE 'Wrong' END AS correct_answer
                    FROM workshop_questionset_trainer AS wt
					LEFT JOIN question_set as qst ON qst.id= wt.questionset_id
                    LEFT JOIN workshop_questions AS q ON q.workshop_id=wt.workshop_id AND q.questionset_id=wt.questionset_id AND q.topic_id=wt.topic_id AND q.subtopic_id=wt.subtopic_id AND q.language_id=qst.language_id
                    LEFT JOIN question_topic AS qt ON qt.id = q.topic_id
                    LEFT JOIN question_subtopic AS qs ON qs.id=q.subtopic_id
                    LEFT JOIN company_users AS cu ON cu.userid=wt.trainer_id ";
        }

        $query .= " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        if ($isnew == 1) {
            $query1 = "SELECT count(q.id) as total "
                    . " FROM questionset_trainer as qtr "
                    . " LEFT JOIN questions as q ON q.topic_id=qtr.topic_id AND q.subtopic_id=qtr.subtopic_id "
                    . " LEFT JOIN question_topic AS qt ON qt.id = qtr.topic_id "
                    . " LEFT JOIN question_subtopic AS qs ON qs.id=qtr.subtopic_id LEFT JOIN company_users AS cu ON cu.userid=qtr.trainer_id ";
        } else {
            $query1 = "  SELECT count(q.question_id) as total
                        FROM workshop_questionset_trainer AS wt
                        LEFT JOIN workshop_questions AS q ON q.workshop_id=wt.workshop_id AND q.questionset_id=wt.questionset_id AND q.topic_id=wt.topic_id AND q.subtopic_id=wt.subtopic_id
                        LEFT JOIN question_topic AS qt ON qt.id = q.topic_id
                        LEFT JOIN question_subtopic AS qs ON qs.id=q.subtopic_id
                        LEFT JOIN company_users AS cu ON cu.userid=wt.trainer_id ";
        }
        $query1 .= " $dtWhere";

        $result1 = $this->db->query($query1);
        $data_array = $result1->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }

    public function getFeedbackQuestionLoadData($dtWhere, $dtOrder, $dtLimit, $workshop_id, $isnew = '') {
        if ($isnew == 1) {
            $query = " SELECT wq.id as question_id,wq.question_title,ifnull(tq.sorting,@a:=@a+1) sorting,qt.description as ftype, "
                    . " IFNULL(qs.description,'No sub-type') AS fsubtype,'' as hide1,'' as hide2,"
                    . "IF(wq.question_type = 0,'Multiple choice','Text') as question_type "
                    . " FROM feedbackset_type AS ft "
                    . " LEFT JOIN feedback_questions AS wq ON wq.feedback_type_id=ft.feedback_type_id AND wq.feedback_subtype_id=ft.feedback_subtype_id"
                    . " LEFT JOIN temp_questions_order tq ON tq.question_type=2 AND tq.question_id =wq.id AND tq.questionset_id =ft.feedbackset_id AND tq.workshop_id=$workshop_id "
                    . " LEFT JOIN feedback_type AS qt ON qt.id = ft.feedback_type_id "
                    . " LEFT JOIN feedback_subtype AS qs ON qs.id=ft.feedback_subtype_id,(SELECT @a:= 0) AS a ";
        } else {
            $query = " SELECT wq.question_id,wq.question_title,wq.sorting,qt.description as ftype, "
                    . " IFNULL(qs.description,'No sub-type') AS fsubtype,"
                    . " '' as hide1,'' as hide2,IF(wq.question_type = 0,'Multiple choice','Text') as question_type "
                    . " From workshop_feedback_questions AS wq "
                    . " LEFT JOIN feedback_type AS qt ON qt.id = wq.type_id "
                    . " LEFT JOIN feedback_subtype AS qs ON qs.id=wq.subtype_id ";
        }
        $query .= " $dtWhere $dtOrder $dtLimit";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        if ($isnew == 1) {
            $query1 = " SELECT count(wq.id) as total "
                    . " FROM feedbackset_type AS ft "
                    . " LEFT JOIN feedback_questions AS wq ON wq.feedback_type_id=ft.feedback_type_id AND wq.feedback_subtype_id=ft.feedback_subtype_id"
                    . " LEFT JOIN feedback_type AS qt ON qt.id = ft.feedback_type_id "
                    . " LEFT JOIN feedback_subtype AS qs ON qs.id=ft.feedback_subtype_id ";
        } else {
            $query1 = " SELECT count(wq.question_id) as total "
                    . " From workshop_feedback_questions AS wq "
                    . " LEFT JOIN feedback_type AS qt ON qt.id = wq.type_id "
                    . " LEFT JOIN feedback_subtype AS qs ON qs.id=wq.subtype_id ";
        }
        $query1 .= " $dtWhere";

        $result1 = $this->db->query($query1);
        $data_array = $result1->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }

    public function update_temp_sorting($workshop_id, $Qset_id, $question_id, $sorting_order, $question_type) {
        $query = "select id FROM temp_questions_order where workshop_id=" . $workshop_id . ""
                . " AND questionset_id=" . $Qset_id . " AND questionset_id=" . $Qset_id . " AND question_id=" . $question_id;
        $objset = $this->db->query($query);
        $row_set = $objset->row();
        if (count((array)$row_set) > 0) {
            $data = array('sorting' => $sorting_order);
            $this->db->where('id', $row_set->id);
            $this->db->update('temp_questions_order', $data);
        } else {
            $data = array('workshop_id' => $workshop_id, 'questionset_id' => $Qset_id,
                'question_id' => $question_id, 'sorting' => $sorting_order, 'question_type' => $question_type);
            $this->db->insert('temp_questions_order', $data);
        }
        return true;
    }

    public function update_sorting($Table, $Clause, $data) {
        $LcSqlStr = "UPDATE " . $Table . " SET sorting='" . $data['sorting'] . "' WHERE " . $Clause . " ";
        $this->db->query($LcSqlStr);
        return true;
    }

    public function get_questionset($company_id) {
        $query = "SELECT distinct a.id,a.title FROM question_set as a INNER JOIN questionset_trainer as qt ON qt.questionset_id=a.id"
                . " where a.status=1 AND a.company_id = $company_id "
                . " AND qt.topic_id IN (select distinct topic_id FROM questions where company_id= $company_id AND language_id=a.language_id) "
                . " order by title ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function get_feedbackset($company_id) {
        $query = "SELECT distinct a.id,a.title FROM feedback as a INNER JOIN feedbackset_type as qt ON qt.feedbackset_id=a.id"
                . " where a.status=1 AND a.company_id = $company_id "
                . " AND qt.feedback_type_id IN (select distinct feedback_type_id FROM feedback_questions where company_id= $company_id AND language_id=a.language_id) "
                . " order by title ";
        $query = $this->db->query($query);
        return $query->result();
    }

}
