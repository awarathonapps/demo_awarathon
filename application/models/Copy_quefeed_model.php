<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Copy_quefeed_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    } 
    public function DCPQuery($query){
        $result = $this->db->query($query);        
        return $result->result();
    }

    public function CopyWorkshopQuestion($Company_id,$Workshop_Id,$QuestionSet){
        $result = $this->db->query("select id FROM workshop_questions where questionset_id=$QuestionSet AND workshop_id=".$Workshop_Id);
        $data_array = $result->row();
        if(count((array)$data_array)==0){
            $query = " insert into workshop_questions(company_id,workshop_id,questionset_id,question_id,topic_id,subtopic_id,
                question_title,option_a,option_b,option_c,option_d,correct_answer,tip,hint_image,youtube_link,trainer_id) 
                SELECT ".$Company_id." as company_id,$Workshop_Id as workshop_id,$QuestionSet as questionSet,a.id,a.topic_id,
                a.subtopic_id,a.question_title,a.option_a,a.option_b,a.option_c,a.option_d,a.correct_answer,a.tip,
                a.hint_image,a.youtube_link,b.trainer_id 
                FROM questions as a LEFT JOIN questionset_trainer as b ON a.topic_id=b.topic_id 
                AND a.subtopic_id=a.subtopic_id where a.status=1 and b.questionset_id=".$QuestionSet." 
                AND a.id NOT IN(select question_id FROM question_inactive where questionset_id=$QuestionSet) 
                GROUP BY a.id,a.company_id,a.topic_id,a.subtopic_id,b.trainer_id";
            $this->db->query($query);
        }
        return true;
    }
    public function CopyFeedbackQuestion($Company_id,$Workshop_Id,$feedbackset_id){
        $result = $this->db->query("select id FROM workshop_feedback_questions where feedbackset_id=$feedbackset_id AND workshop_id=".$Workshop_Id);
        $data_array = $result->row();
        if(count((array)$data_array)==0){
            $query = " insert into workshop_feedback_questions(company_id,workshop_id,feedbackset_id,question_id,
                type_id,subtype_id,question_title,option_a,weight_a,option_b,weight_b,option_c,weight_c,option_d,weight_d,
                option_e,weight_e,option_f,weight_f,multiple_allow ) 
                SELECT ".$Company_id." as company_id,$Workshop_Id as workshop_id,$feedbackset_id as feedbackset_id,a.id,
                a.feedback_type_id,a.feedback_subtype_id,a.question_title,a.option_a,a.weight_a,a.option_b,a.weight_b,
                a.option_c,a.weight_c,a.option_d,a.weight_d,a.option_e,a.weight_e,a.option_f,a.weight_f,a.multiple_allow 
                FROM feedback_questions as a LEFT JOIN feedbackset_type as b ON a.feedback_type_id=b.feedback_type_id 
                AND a.feedback_subtype_id=a.feedback_subtype_id where a.status=1 and b.feedbackset_id=".$feedbackset_id."
                AND a.id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id=$feedbackset_id)
                GROUP BY a.id,a.company_id,a.feedback_type_id,a.feedback_subtype_id";
            $this->db->query($query);
        }
        return true;
    }
}
