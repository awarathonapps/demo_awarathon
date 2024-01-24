<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Common_controller extends MY_Controller {

//    public function __construct() {
//        parent::__construct();
//        if ($this->session->userdata('awarathon_session') == FALSE) {
//            redirect('index');
//        } else {
//            $this->mw_session = $this->session->userdata('awarathon_session');
//            $this->load->model('workshop_report_model');
//            $this->load->model('common_model');
//        }
//    }
    public function __construct() {
        parent::__construct();
            $this->load->model('workshop_report_model');
        }
    public function ajax_companywise_data($trainee_req=1) {
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Login_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($company_id != '') {
            if($trainee_req){
                $data['TraineeData']  = $this->get_trainee_selectbox($company_id,$WRightsFlag);            
            }
            $data['RegionData']   = $this->get_region_selectbox($company_id,$WRightsFlag);            
            $data['WTypeData']    = $this->get_workshop_type_selectbox($company_id,$WRightsFlag);
            $data['WorkshopData'] = $this->get_workshop_selectbox($company_id,$WRightsFlag);
            $data['TrainerData']  = $this->get_trainer_selectbox($company_id,$RightsFlag);
            $data['DesignationData']   = $this->get_trainee_designation_selectbox($company_id);
            $data['TraineeRegionData']   = $this->get_trainee_region_selectbox($company_id);
            $data['FeedbackWorkshopData'] = $this->get_feedback_workshop_selectbox($company_id,$WRightsFlag,0,0,'','');
        }else{
            $data =array();
        }
        echo json_encode($data);
    }
     public function ajax_tregionwise_data() {
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Login_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $region_id           = $this->input->post('tregion_id', TRUE);
        if($region_id=="" || $region_id==null){
            $region_id= 0;
        }
        $trainer_id          = $this->input->post('trainer_id', TRUE);
        if($trainer_id=="" || $trainer_id==null){
            $trainer_id= 0;
        }
        $workshop_type       = $this->input->post('workshop_type', TRUE);
        if($workshop_type=="" || $workshop_type==null){
            $workshop_type= 0;
        }
        $workshop_id           = $this->input->post('workshop_id', TRUE);
        if($workshop_id=="" || $workshop_id==null){
            $workshop_id= "";
        }
        $workshop_session     = $this->input->post('workshop_session', TRUE);
        if($workshop_session=="" || $workshop_session==null){
            $workshop_session= '';
        }        
        $data['TraineeData'] = $this->get_trainee_selectbox($company_id,$WRightsFlag,$trainer_id,$region_id,$workshop_type,$workshop_id,$workshop_session);
        $data['AllSelectionTrainee'] = $this->get_allselection_trainee_selectbox($company_id,$WRightsFlag,$trainer_id,$region_id,$workshop_type,$workshop_id,$workshop_session);
        echo json_encode($data);
    }
    public function ajax_topicwise_data(){
        $topic_id = $this->input->post('topic_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }                        
        $lchtml='<option value="">Please Select</option>';
        if($topic_id !=''){
            $SubTopicDataSet = $this->common_model->get_selected_values('question_subtopic', 'id,description', 'company_id=' . $company_id . ' and topic_id=' . $topic_id);
            if(count((array)$SubTopicDataSet)>0){
                foreach ($SubTopicDataSet as $value) {
                    $lchtml .='<option value="'.$value->id.'">'.$value->description.'</option>';
                }
            }
        }
        $data['SubTopicData'] = $lchtml;
        echo json_encode($data);        
    }
     
    public function ajax_workshoptypewise_data(){
        $RightsFlag = 1;
        $WRightsFlag = 1;        
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Login_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $trainer_id = ($this->input->post('user_id', TRUE) !='' ? $this->input->post('user_id', TRUE) : 0);        
        $workshoptype_id = ($this->input->post('workshoptype_id', TRUE) !='' ? $this->input->post('workshoptype_id', TRUE) : 0);        
        $region_id = ($this->input->post('region_id', TRUE) !='' ? $this->input->post('region_id', TRUE) : 0);
        $workshopsubtype_id = ($this->input->post('workshopsubtype_id', TRUE) !='' ? $this->input->post('workshopsubtype_id', TRUE) : 0);
        $subregion_id = ($this->input->post('subregion_id', TRUE) !='' ? $this->input->post('subregion_id', TRUE) : 0);
        $lchtml='<option value="">All Workshop</option>';
        
        $Dataset = $this->common_model->getTrainerWorkshop($company_id,$WRightsFlag,$trainer_id,$region_id,$workshoptype_id,$workshopsubtype_id,$subregion_id);                            
        if(count((array)$Dataset)>0){
            foreach ($Dataset as $value) {
                $lchtml .='<option value="'.$value->workshop_id.'">'.$value->workshop_name.'</option>';
            }
        }
        
        $data['WorkshopData'] = $lchtml;
        $data['FeedbackWorkshopData'] = $this->get_feedback_workshop_selectbox($company_id,$WRightsFlag,$region_id,$workshoptype_id,$workshopsubtype_id,$subregion_id);
        $data['WorkshopSubtypeData'] = $this->get_workshop_subtype_selectbox($company_id,$workshoptype_id);        
        $data['WorkshopSubregionData'] = $this->get_workshop_subregion_selectbox($company_id,$region_id);
        echo json_encode($data);
    }
     public function ajax_workshopwise_data() {
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Login_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $workshop_id = $this->input->post('workshop_id', TRUE);        
            $lchtml1='<option value="0">All Trainer</option>';                    
            $TrainerDataset = $this->workshop_report_model->workshopwise_trainerdata($company_id,$workshop_id);            
            $TraineeData    = $this->get_trainee_selectbox($company_id,$WRightsFlag,'0','0','0',$workshop_id,'');            
            $topic_lchtml   = $this->get_topic_selectbox($company_id,$workshop_id);
            if(count((array)$TrainerDataset)>0){
                foreach ($TrainerDataset as $value) {
                    $lchtml1 .='<option value="'.$value->trainer_id.'">'.$value->trainer_name.'</option>';
                }
            }
            if($TraineeData != ''){
                $data['TraineeData'] = $TraineeData;
            }else{
                $data['TraineeData']  = $this->get_trainee_selectbox($company_id,$WRightsFlag,'0','0','0','','');
            }            
        $data['TopicData']   = $topic_lchtml;
        $data['TrainerData'] = $lchtml1;
        
        echo json_encode($data);
    }
    public function get_trainee_selectbox($company_id,$WRightsFlag=1,$trainer_id="0",$tregion_id="0",$workshop_type="0",$workshop_id="",$workshop_session=""){
        $TraineeData = $this->workshop_report_model->get_traineeData($company_id,$WRightsFlag,$trainer_id,$tregion_id,$workshop_type,$workshop_id,$workshop_session);
        $lcTrainee_html='<option value="">All Trainee</option>';
        if(count((array)$TraineeData)>0){
            foreach ($TraineeData as $value) {
                $lcTrainee_html .='<option value="'.$value->user_id.'">'.$value->traineename.'</option>';
            }
        }
        return $lcTrainee_html;
    }
    public function get_allselection_trainee_selectbox($company_id,$WRightsFlag=1,$trainer_id="0",$tregion_id="0",$workshop_type="0",$workshop_id="",$workshop_session=""){        
        $TraineeData = $this->workshop_report_model->get_traineeData($company_id,$WRightsFlag,$trainer_id,$tregion_id,$workshop_type,$workshop_id,$workshop_session);
        $lcTrainee_html='<option value="0">All Trainee</option>';
        if(count((array)$TraineeData)>0){
            foreach ($TraineeData as $value) {
                $lcTrainee_html .='<option value="'.$value->user_id.'">'.$value->traineename.'</option>';
            }
        }
        return $lcTrainee_html;
    }
    public function get_region_selectbox($company_id,$WRightsFlag){
        $RegionData = $this->common_model->getUserRegionList($company_id,$WRightsFlag);
        $lchtml='<option value="0">All Region</option>';
            if(count((array)$RegionData)>0){
                foreach ($RegionData as $value) {
                    $lchtml .='<option value="'.$value->id.'">'.$value->region_name.'</option>';
                }
            }
        return $lchtml;
    }
    public function get_trainee_designation_selectbox($company_id){
        $DesignationData = $this->common_model->get_selected_values('designation_trainee','id,description','company_id='.$company_id);
        $lchtml='<option value="0">All Designation</option>';
            if(count((array)$DesignationData)>0){
                foreach ($DesignationData as $value) {
                    $lchtml .='<option value="'.$value->id.'">'.$value->description.'</option>';
                }
            }
        return $lchtml;
    }
    public function get_workshop_type_selectbox($company_id,$WRightsFlag){
        $Dataset = $this->common_model->getWTypeRightsList($company_id,$WRightsFlag);
        $lchtml='<option value="0">All Type</option>';
        if(count((array)$Dataset)>0){
            foreach ($Dataset as $value) {
                $lchtml .='<option value="'.$value->id.'">'.$value->workshop_type.'</option>';
            }
        }
        return $lchtml;
    }
     public function get_workshop_selectbox($company_id,$WRightsFlag){
        $Dataset = $this->common_model->getTrainerWorkshop($company_id,$WRightsFlag,0,0,0);
        $lchtml='<option value="">All Workshop</option>';
        if(count((array)$Dataset)>0){
            foreach ($Dataset as $value) {
                $lchtml .='<option value="'.$value->workshop_id.'">'.$value->workshop_name.'</option>';
            }
        }
        return $lchtml;
    }
    public function get_trainer_selectbox($company_id,$RightsFlag){
        $Dataset = $this->common_model->getUserRightsList($company_id,$RightsFlag,0);
        $lchtml='<option value="0">All Trainer</option>';
        if(count((array)$Dataset)>0){
            foreach ($Dataset as $value) {
                $lchtml .='<option value="'.$value->userid.'">'.$value->fullname.'</option>';
            }
        }
        return $lchtml;
    }
    public function get_topic_selectbox($company_id,$workshop_id=''){
        $lchtml='<option value="">Please Select</option>';
        if($workshop_id !=''){
            $Dataset        = $this->common_model->workshopwise_data($company_id, $workshop_id);
            if(count((array)$Dataset)>0){
                foreach ($Dataset as $value) {
                    $lchtml .='<option value="'.$value->id.'">'.$value->topic_name.'</option>';
                }
            }
        }
        return $lchtml;
    }
    public function get_workshop_subtype_selectbox($company_id,$workshoptype_id=''){
        $lchtml ='<option value="">All Sub-Type</option>';
        if($workshoptype_id !=''){
            $Dataset        = $this->common_model->get_selected_values('workshopsubtype_mst','id,description as sub_type','company_id='.$company_id.' and workshoptype_id='.$workshoptype_id);
            if(count((array)$Dataset)>0){
                foreach ($Dataset as $value) {
                    $lchtml .='<option value="'.$value->id.'">'.$value->sub_type.'</option>';
                }
            }
        }
        return $lchtml;
    }
    public function get_workshop_subregion_selectbox($company_id,$region_id=''){
        $lchtml='<option value="">All Sub-region</option>';
        if($region_id !=''){
            $Dataset = $this->common_model->get_selected_values('workshopsubregion_mst','id,description as sub_region',
                'company_id='.$company_id.' and region_id='.$region_id);
            if(count((array)$Dataset)>0){
                foreach ($Dataset as $value) {
                    $lchtml .='<option value="'.$value->id.'">'.$value->sub_region.'</option>';
                }
            }
        }
        return $lchtml;
    }
    public function get_trainee_region_selectbox($company_id){
        $RegionData = $this->workshop_report_model->get_TraineeRegionData($company_id);
        $lchtml='<option value="0">All Trainee Region</option>';
            if(count((array)$RegionData)>0){
                foreach ($RegionData as $value) {
                    $lchtml .='<option value="'.$value->region_id.'">'.$value->region_name.'</option>';
                }
            }
        return $lchtml;
    }
    public function get_feedback_workshop_selectbox($company_id,$WRightsFlag,$region_id,$workshoptype_id,$workshopsubtype_id,$subregion_id){
        $feedbacklchtml='<option value="">please select</option>';
        $this->load->model('weights_report_model');
        $FeedbackWorkshop = $this->weights_report_model->getFeedbackWorkshop($company_id,$WRightsFlag,$region_id,$workshoptype_id,$workshopsubtype_id,$subregion_id);                            
        if(count((array)$FeedbackWorkshop)>0){
            foreach ($FeedbackWorkshop as $value) {
                $feedbacklchtml .='<option value="'.$value->workshop_id.'">'.$value->workshop_name.'</option>';
            }
        }
        return $feedbacklchtml;
    }
}
