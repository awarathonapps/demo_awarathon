<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Assessment_candidate_rpt extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('assessment');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
	$this->common_db = $this->common_model->connect_db2();
        $this->acces_management = $acces_management;
        $this->load->model('assessment_model');
    }

    public function index() {
        $data['module_id'] = '27.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['assessment_type'] = $this->common_model->get_selected_values('assessment_type', 'id,description,default_selected', 'status=1');               
        $this->load->view('assessment_candidate_rpt/index', $data);
    }    
    public function get_question_title() {
        $question_id = $this->input->post('question_id');
        $Question_set = $this->common_model->get_value('assessment_question', 'question', 'id=' . $question_id);
        $data['lchtml'] = $Question_set->question;
        echo json_encode($data);
    }        
    public function view($id) {
        $data['module_id'] = '27.02';
        $data['username'] = $this->mw_session['username'];        
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('assessment');
            return;
        }
        $assessment_id = base64_decode($id);
        $data['assessment_id'] = $assessment_id;
        $data['Rowset'] = $this->common_model->get_value('assessment_mst', '*', 'id=' . $assessment_id);        
        $this->load->view('assessment_candidate_rpt/view', $data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('am.id', 'am.id', 'am.assessment','at.description','am.start_dttm','am.end_dttm', 'am.assessment');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');        
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id =$this->mw_session['company_id'];
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND am.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE am.company_id  = " . $cmp_id;
            }
        }
        $assessment_type = $this->input->get('assessment_type');
        if ($assessment_type != "") {
            $dtWhere .= " AND am.assessment_type  = " . $assessment_type;
        }
	$question_type = $this->input->get('question_type');
        if ($question_type != "") {
            $dtWhere .= " AND am.is_situation  = " . $question_type;
        }
		
        $status = $this->input->get('filter_status');
        if ($status == "1") {
            $dtWhere .= " AND am.end_dttm >= '" . $now . "'";
        } elseif ($status == "2") {
            $dtWhere .= " AND am.end_dttm < '" . $now . "'";
        } elseif ($status == "3") {
            $dtWhere .= " AND am.start_dttm > '" . $now . "' AND am.status = 1";
        } elseif ($status == "4") {
            $dtWhere .= " AND am.status = 0";
        }
        $superaccess = $this->mw_session['superaccess'];  
        if($superaccess){
            $trainer_id='';
        }else{
            $trainer_id  = $this->mw_session['user_id'];
        }                
        if($trainer_id !=''){
            $dtWhere .= " AND am.id IN (select assessment_id FROM assessment_managers where trainer_id=$trainer_id)";
        }        
                                           		
        $DTRenderArray = $this->assessment_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
 
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('id','question_type','assessment', 'start_dttm', 'end_dttm', 'status','status1','status2','Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $Curr_Time = strtotime($now);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if($trainer_id != ''){                   
                    $assessment_status = $this->assessment_model->getAssessmentStatus($dtRow['id'],$trainer_id);
                }else{
                    $assessment_status = $this->assessment_model->getAssessmentStatus($dtRow['id']);
                }
                $candidate_status = '';
                $assessor_status = '';
                if(count((array)$assessment_status)> 0){
                    $candidate_status = ($assessment_status->is_candidate_complete ? 'Completed' : 'Incomplete');
                    $assessor_status = ($assessment_status->assessor_status ? 'Completed' : 'Incomplete');
                }else{
                    $candidate_status = 'Incomplete';
                    $assessor_status  = 'Incomplete';
                }                
                if ($dtDisplayColumns[$i] == "status") {
                    if (strtotime($dtRow['start_dttm']) >= $Curr_Time) {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-info status-active" > Active </span>';
                        } else {
                            $status = '<span class="label label-sm label-danger status-active" > In-Active </span>';
                        }
                    } else if (strtotime($dtRow['end_dttm']) >= $Curr_Time) {
                        $status = '<span class="label label-sm  label-success " style="background-color: #5cb85c;" > Live </span>';
                    } else {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-danger " > Expired </span>';
                        } else {
                            $status = '<span class="label label-sm label-warning status-active" > In-Active </span>';
                        }
                    }
                    $row[] = $status;
                } else if($dtDisplayColumns[$i] == "status1"){                                        
                    $row[] = $candidate_status;                    
                    
                }else if($dtDisplayColumns[$i] == "status2"){                                                             
                    $row[] = $assessor_status;                    
                    
                }else if ($dtDisplayColumns[$i] == "Actions") {
                        $action = '';
                        if ($acces_management->allow_add OR $acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete) {
                            $action = '<div class="btn-group">
                                    <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                        Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">';
                            if ($acces_management->allow_view) {                                
                                $action .= '<li>
                                            <a href="' . $site_url . 'assessment_candidate_rpt/view/' . base64_encode($dtRow['id']).'">
                                    <i class="fa fa-star-half-empty"></i>&nbsp;View Assessment
                                            </a>
                                        </li>';
                            }                        
                            $action .= '</ul>
                                </div>';
                        } else {
                            $action = '<button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Locked&nbsp;&nbsp;<i class="fa fa-lock"></i>
                                </button>';
                        }

                        $row[] = $action;
                    } else if ($dtDisplayColumns[$i] != ' ') {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }    
    public function AssessmentUsers($Encode_id) {
        $site_url = base_url();
        $assessment_id = base64_decode($Encode_id);
        $acces_management = $this->acces_management;
        $dtSearchColumns = array('u.user_id', 'u.firstname', 'u.email', 'u.mobile', 'tr.region_name', 'w.retake');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($dtWhere <> '') {
            $dtWhere .= " AND w.assessment_id  = " . $assessment_id;
        } else {
            $dtWhere .= " WHERE w.assessment_id  = " . $assessment_id;
        }
        $fttrainer_id = $this->input->get('fttrainer_id') ? $this->input->get('fttrainer_id') : '';
        if ($fttrainer_id != "") {
            $dtWhere .= " AND u.trainer_id  = " . $fttrainer_id;
        }
        $superaccess = $this->mw_session['superaccess'];
        if(!$superaccess){
            $trainer_id= $this->mw_session['user_id'];
        }else{
            $trainer_id='';
        }
        $trainer_data  = $this->assessment_model->get_trainerdata($assessment_id,$trainer_id);
        
        $DTRenderArray = $this->assessment_model->LoadAssessmentUsers($dtWhere, $dtOrder, $dtLimit);
        
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id', 'name', 'email', 'mobile', 'region_name', 'is_completed','assesor_status','Actions');        
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
			if ($dtRow['is_completed']) {
                $view_type = 0;
            } else {
                $view_type = 1;
            }
            for ($i = 0; $i < $TotalHeader; $i++) {
				if ($dtDisplayColumns[$i] == "is_completed") {
                    if ($dtRow['is_completed']) {

                        $status = '<span class="label label-sm label-success status-active" > Completed </span>';
                    } else {

                        $status = '<span class="label label-sm label-warning status-active" > Incomplete </span>';
                    }
                    $row[] = $status;
                }
                elseif ($dtDisplayColumns[$i] == "retake") {
                    if ($dtRow['retake']) {
                        $status = '<span class="label label-sm label-warning status-active" > Retake </span>';
                    } else {
                        $status = '<span class="label label-sm label-success status-active" > Completed </span>';
                    }
                    $row[] = $status;
                }else if ($dtDisplayColumns[$i] == "assesor_status") {
                    if(count((array)$trainer_data) > 0){
                        $isTrainerComplete = 0;
                        foreach ($trainer_data as $value) {
                            $isTrainerComplete  += $this->assessment_model->isCompletedAssessor($assessment_id,$value->trainer_id,$dtRow['user_id']);
                        }
                        if($isTrainerComplete == count((array)$trainer_data)){
                            $status = '<span class="label label-sm label-success status-active" > Completed </span>';
                        }else{
                            $status = '<span class="label label-sm label-warning status-active" > Incomplete </span>';
                        }
                    }                    
                    $row[] = $status;
                } elseif ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if(count((array)$trainer_data) > 0){
                        if($trainer_id !=''){
                            $action .= '<a data-target="#LoadModalFilter" data-toggle="modal" href="' . $site_url . 'assessment_candidate_rpt/LoadViewModal/' . base64_encode($dtRow['assessment_id']) . '/' . base64_encode($dtRow['user_id']).'/'.$trainer_id.'">
                                            <i class="fa fa-star-half-full"></i>&nbsp;Rate
                                        </a> ';
                        }else{
                            $action = '<div class="btn-group row">
                                    <div class="col-md-6">
                                    <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                        Rate&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">';
                            foreach ($trainer_data as $value) {
                                    $action .= '<li> 
                                        <a data-target="#LoadModalFilter" data-toggle="modal" href="' . $site_url . 'assessment_candidate_rpt/LoadViewModal/' . base64_encode($dtRow['assessment_id']) . '/' . base64_encode($dtRow['user_id']) . '/'.$value->trainer_id.'">
                                            <i class="fa fa-user"></i>&nbsp;'.$value->name.
                                        ' </a>
                                    </li>';
                            }
                            $action .= '</ul>
                                </div>';
 
                            $action .= '</div>';                            
                        }    
                    }
                    $row[] = $action;
                        
                }else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function LoadViewModal($encoded_id, $en_user_id,$trainer_id) {        
        
        $assessment_id = base64_decode($encoded_id);
        $user_id = base64_decode($en_user_id);
        $AssessmentData = $this->common_model->get_value('assessment_mst', 'assessment_type,assessor_dttm', 'id=' . $assessment_id);
        $company_id =$this->mw_session['company_id'];
        
        $RatingData = $this->common_model->get_value('assessment_trainer_result', '*', 'trainer_id=' . $trainer_id . ' AND user_id=' . $user_id . ' AND assessment_id=' . $assessment_id);
        
        $UserData = $this->common_model->get_value('device_users', 'user_id,concat(firstname," ",lastname) as username,email,avatar', 'company_id=' . $company_id . ' AND user_id=' . $user_id);
        $trainer_name = $this->common_model->get_value('company_users', 'userid,concat(first_name," ",last_name) as trainer_name', 'company_id=' . $company_id . ' AND userid=' . $trainer_id);
        $remarks_data = '';
        $QuestionData = $this->assessment_model->LoadAssessmentQuestions($assessment_id);
        
        $ass_result_id = '';
        $video_screen = '';
        $embed = '';
        $remarks = '';
        $your_rating = 0;
        $team_rating = 0;
        $cnt = 0;
        if(count((array)$RatingData) > 0){
            $remarks = $RatingData->remarks;
        }

        $ScoreData = $this->assessment_model->get_your_rating($assessment_id,$user_id, $trainer_id);
        if (count((array)$ScoreData) > 0 && $ScoreData->total_rating != 0) {
            $your_rating = round($ScoreData->total_score / ($ScoreData->total_rating) * 100, 2);
            $cnt = 1;
        }
        $data['your_rating'] = $your_rating . '%';
        $total_rating= $this->assessment_model->get_team_rating($assessment_id, $user_id, $trainer_id);
        if (count((array)$total_rating) > 0){
             $team_rating = round(($total_rating->total_rating + $your_rating)/($total_rating->total_trainer+$cnt),2);
            $data['team_rating'] = $team_rating . '%';
        }else{
            $data['team_rating'] = $your_rating . '%';
        }
        $data['trainer_id'] = $trainer_id;
        $data['remarks'] = $remarks;
        $data['question_remarks'] = $remarks_data;
        $data['ass_result_id'] = $ass_result_id;
        $data['UserData'] = $UserData;
        $data['trainer_name'] = $trainer_name;
        $data['Questions'] = $QuestionData;
        $data['company_id'] = $company_id;
        $data['assessment_id'] = $assessment_id;
        $data['assessment_type'] = $AssessmentData->assessment_type;
       
        $data['mode'] = (strtotime($AssessmentData->assessor_dttm) <strtotime(date('Y-m-d H:i:s')) ? 1 : 2);
        $data['user_id'] = $user_id;       
        
        $superaccess = $this->mw_session['superaccess'];    
        
        $this->load->view('assessment_candidate_rpt/ViewAssessmentModal', $data);
    }

    public function getquestionwiseparameter($q_id, $srno) {
        
        $superaccess = $this->mw_session['superaccess'];    
        $trainer_id= $this->mw_session['user_id'];
        $ISEXIST  = $this->common_model->get_value('assessment_supervisors', 'id,trainer_id', 'trainer_id='.$trainer_id);            
        if(count((array)$ISEXIST) > 0){
            $is_supervisor = 1;
        }else{
            $is_supervisor = 0;
        }
        
        $assessment_id = $this->input->post('assessment_id', true);
        $user_id = $this->input->post('user_id', true);
        $trainer_id = $this->input->post('trainer_id', true);

        $htdata = '';
        $QParameter_table = '';
        $your_rating = 0;
        $para_rating = array();
        $remarks_data='';
        $ParameterData = $this->common_model->get_value('assessment_trans', 'parameter_id', 'question_id=' . $q_id . ' AND assessment_id=' . $assessment_id);
        $StarRatingData = $this->common_model->get_selected_values('assessment_results_trans', 'parameter_id,score', 'question_id=' . $q_id . '  AND user_id=' . $user_id. ' AND trainer_id=' . $trainer_id);
        if (count((array)$StarRatingData) > 0) {
            foreach ($StarRatingData as $val) {
                $para_rating[$val->parameter_id] = $val->score;
            }
        }                        
        $AssessmentData = $this->common_model->get_value('assessment_mst', 'assessor_dttm', 'id=' . $assessment_id);
        $Tdata['mode'] = (strtotime($AssessmentData->assessor_dttm) <strtotime(date('Y-m-d H:i:s')) ? 1 : 2);
        $cnt_rate = $this->common_model->get_value('assessment_complete_rating', 'id', 'assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id);
        $Tdata['cnt_rate']= count((array)$cnt_rate);
        $Question = $this->common_model->get_value('assessment_question', 'id,question', 'id=' . $q_id);
        
        
        if (count((array)$ParameterData) > 0) {
            $QParameterData = $this->assessment_model->get_question_parameter($ParameterData->parameter_id,$q_id,$user_id,$trainer_id,$assessment_id);            
            if (count((array)$QParameterData) > 0) {                
                $Tdata['is_supervisor'] = $is_supervisor;
                $Tdata['QParameterData'] = $QParameterData;
                $Tdata['para_rating'] = $para_rating;
                
                $Tdata['Question'] = $srno . ". " . $Question->question;
                //$api_data = $this->common_model->get_value('api_details', 'client_id,client_secret,access_token,url', 'name="vimeo" and status=1');
                $Tdata['video_data'] = $this->common_model->get_value('assessment_results', 'id,video_url', 'question_id=' . $q_id . ' AND user_id=' . $user_id . ' AND assessment_id=' . $assessment_id ." order by id desc");
                $trainer_question = $this->common_model->get_value('assessment_trainer_remarks', 'remarks',' assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id. ' AND question_id=' . $q_id);
                $remarks_data = (count((array)$trainer_question)>0 ? $trainer_question->remarks:'');
                $QParameter_table = $this->load->view('assessment_candidate_rpt/parameter_table', $Tdata, TRUE);
            }
        }        
        $data['cnt_rate'] = count((array)$cnt_rate);
        $data['question_comments'] =$remarks_data;
        $data['QParameter_table'] = $QParameter_table;

        echo json_encode($data);
    }
   
}
