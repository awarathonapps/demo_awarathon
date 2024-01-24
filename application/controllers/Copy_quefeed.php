<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Copy_quefeed extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('copy_quefeed_model');
        $this->load->model('common_model');
    }
    public function index()
    {
        $workshop_questionset_pre = $this->copy_quefeed_model->DCPQuery("SELECT * FROM workshop_questionset_pre 
            WHERE workshop_id IN (SELECT id FROM workshop WHERE company_id='6')");
        if (count((array)$workshop_questionset_pre)>0){
            foreach($workshop_questionset_pre as $data){
                $Company_id = 6;
                $Workshop_Id =$data->workshop_id;
                $QuestionSet =$data->questionset_id;
                $this->copy_quefeed_model->CopyWorkshopQuestion($Company_id,$Workshop_Id,$QuestionSet);   
            }
        }

        $workshop_questionset_pre = $this->copy_quefeed_model->DCPQuery("SELECT * FROM workshop_questionset_post 
            WHERE workshop_id IN (SELECT id FROM workshop WHERE company_id='6')");
        if (count((array)$workshop_questionset_pre)>0){
            foreach($workshop_questionset_pre as $data){
                $Company_id = 6;
                $Workshop_Id =$data->workshop_id;
                $QuestionSet =$data->questionset_id;
                $this->copy_quefeed_model->CopyWorkshopQuestion($Company_id,$Workshop_Id,$QuestionSet);   
            }
        }

        $workshop_questionset_pre = $this->copy_quefeed_model->DCPQuery("SELECT * FROM workshop_feedbackset_pre 
            WHERE workshop_id IN (SELECT id FROM workshop WHERE company_id='6')");
        if (count((array)$workshop_questionset_pre)>0){
            foreach($workshop_questionset_pre as $data){
                $Company_id = 6;
                $Workshop_Id =$data->workshop_id;
                $feedbackset_id =$data->feedbackset_id;
                $this->copy_quefeed_model->CopyFeedbackQuestion($Company_id,$Workshop_Id,$feedbackset_id);   
            }
        }
        $workshop_questionset_pre = $this->copy_quefeed_model->DCPQuery("SELECT * FROM workshop_feedbackset_post 
            WHERE workshop_id IN (SELECT id FROM workshop WHERE company_id='6')");
        if (count((array)$workshop_questionset_pre)>0){
            foreach($workshop_questionset_pre as $data){
                $Company_id = 6;
                $Workshop_Id =$data->workshop_id;
                $feedbackset_id =$data->feedbackset_id;
                $this->copy_quefeed_model->CopyFeedbackQuestion($Company_id,$Workshop_Id,$feedbackset_id);   
            }
        }
        
    }
}
