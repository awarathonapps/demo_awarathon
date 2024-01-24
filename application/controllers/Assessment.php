<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Assessment extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('assessment');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->common_db = $this->common_model->connect_db2();
        $this->acces_management = $acces_management;
        $this->load->model('assessment_model');
    }

    public function index()
    {
        $data['module_id'] = '13.03';
        $data['username'] = $this->mw_session['username'];
        $data['role_id'] = $this->mw_session['role'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['assessment_type'] = $this->common_model->get_selected_values('assessment_type', 'id,description,default_selected', 'status=1');
        $superaccess = $this->mw_session['superaccess'];
        if ($superaccess) {
            $data['superaccess'] = 1;
        } else {
            $data['superaccess'] = 0;
        }
        $login_id = $this->mw_session['user_id'];
        $ISEXIST = $this->common_model->get_value('assessment_managers', 'id,trainer_id', 'trainer_id=' . $login_id);
        if (count((array) $ISEXIST) > 0) {
            $data['is_supervisor'] = 1;
        } else {
            $data['is_supervisor'] = 0;
        }
        $this->load->view('assessment/index', $data);
    }

    public function get_question_title()
    {
        $question_id = $this->input->post('question_id');
        $Question_set = $this->common_model->get_value('assessment_question', 'question', 'id=' . $question_id);
        $data['lchtml'] = $Question_set->question;
        echo json_encode($data);
    }

    public function view($id, $view_type)
    {
        $data['module_id'] = '13.03';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('assessment');
            return;
        }
        $assessment_id = base64_decode($id);
        $data['assessment_id'] = $assessment_id;
        $data['Rowset'] = $this->common_model->get_value('assessment_mst', '*', 'id=' . $assessment_id);
        $data['view_type'] = $view_type;
        $this->load->view('assessment/view', $data);
    }

    public function DatatableRefresh()
    {
        // $dtSearchColumns = array('am.id', 'am.id', 'am.assessment', 'at.description', 'am.start_dttm', 'am.end_dttm', 'am.assessor_dttm', 'am.assessment');
        $dtSearchColumns = array('am.id', 'am.id', 'am.assessment', 'am.start_dttm', 'am.end_dttm', 'am.assessor_dttm', 'am.assessment');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');
        $view_type = $this->input->get('view_type');
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
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
        $dtWhere .= " AND am.assessment_type  != 3";    //Hide Trinity assessments
        $question_type = $this->input->get('question_type');
        if ($question_type != "") {
            $dtWhere .= " AND am.is_situation  = " . $question_type;
        }

        $status = $this->input->get('filter_status');
        if ($status == "1") {
            $dtWhere .= " AND am.assessor_dttm >= '" . $now . "'";
        } elseif ($status == "2") {
            $dtWhere .= " AND am.assessor_dttm < '" . $now . "'";
        } elseif ($status == "3") {
            $dtWhere .= " AND am.start_dttm > '" . $now . "' AND am.status = 1";
        } elseif ($status == "4") {
            $dtWhere .= " AND am.status = 0";
        }
        $superaccess = $this->mw_session['superaccess'];
        if ($superaccess) {
            $trainer_id = '';
        } else if (isset($this->acces_management) and ((int) $this->acces_management->role == 1) and ((int) $this->acces_management->allow_access == 1)) {
            //Changes suggested by rahul on 06.10.2021
            //If role is administrator and full access rights given for menu video assessmenent then show all assessment.
            $trainer_id = '';
        } else {
            $trainer_id = $this->mw_session['user_id'];
            if ($view_type == 1) {
                $dtWhere .= " AND am.id IN (select assessment_id FROM assessment_supervisors where trainer_id=$trainer_id)";
            } else {
                $dtWhere .= " AND am.id IN (select assessment_id FROM assessment_managers where trainer_id=$trainer_id)";
            }
        }

        //        $ISEXIST  = $this->common_model->get_value('assessment_supervisors', 'id,trainer_id', 'trainer_id='.$trainer_id);            
//        if(count((array)$ISEXIST) > 0 || $superaccess){
//            $is_supervisor = 1;
//        }else{
//            $is_supervisor = 0;
//        }                          

        $DTRenderArray = $this->assessment_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('id', 'assessment_type', 'assessment', 'start_dttm', 'end_dttm', 'assessor_dttm', 'status', 'status1', 'status2', 'Actions');
        // $dtDisplayColumns = array('id', 'question_type', 'assessment', 'start_dttm', 'end_dttm', 'assessor_dttm', 'status', 'status1', 'status2', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            $Curr_Time = strtotime($now);
            for ($i = 0; $i < $TotalHeader; $i++) {
                $assessment_status = array();
                if ($view_type == 1) {
                    $assessment_status = $this->assessment_model->getAssessmentStatus($dtRow['id']);
                } else {
                    $assessment_status = $this->assessment_model->getAssessmentStatus($dtRow['id'], $trainer_id);
                }
                // if ($dtRow['id'] <16) {
                //     if ($view_type == 1) {
                //         $assessment_status = $this->assessment_model->getAssessmentStatus($dtRow['id']);
                //     } else {
                //         $assessment_status = $this->assessment_model->getAssessmentStatus($dtRow['id'], $trainer_id);
                //     }
                // }else{
                //     $assessment_status=array();
                // }
                $candidate_status = '';
                $assessor_status = '';
                if (count((array) $assessment_status) > 0) {
                    $candidate_status = ($assessment_status->is_candidate_complete ? 'Completed' : 'Incomplete');
                    $assessor_status = ($assessment_status->assessor_status ? 'Completed' : 'Incomplete');
                } else {
                    $candidate_status = 'Incomplete';
                    $assessor_status = 'Incomplete';
                }
                if ($dtDisplayColumns[$i] == "status") {
                    if (strtotime($dtRow['start_dttm']) >= $Curr_Time) {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-info status-active" > Active </span>';
                        } else {
                            $status = '<span class="label label-sm label-danger status-active" > In-Active </span>';
                        }
                    } else if (strtotime($dtRow['assessor_dttm']) >= $Curr_Time) {
                        $status = '<span class="label label-sm  label-success " style="background-color: #5cb85c;" > Live </span>';
                    } else {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-danger " > Expired </span>';
                        } else {
                            $status = '<span class="label label-sm label-warning status-active" > In-Active </span>';
                        }
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "status1") {
                    if ($view_type == 1) {
                        $row[] = '<a href="' . $site_url . 'assessment/candidate_details/' . base64_encode($dtRow['id']) . '" 
                                data-target="#LoadModalFilter" data-toggle="modal">' . $candidate_status . ' </a>';
                    } else {
                        if ($candidate_status == 'Completed') {
                            $row[] = '<span class="label label-sm label-success status-active" > Completed </span>';
                        } else {
                            $row[] = '<span class="label label-sm label-warning status-active" > Incomplete </span>';
                        }
                    }
                } else if ($dtDisplayColumns[$i] == "status2") {
                    if ($view_type == 1) {
                        $row[] = '<a href="' . $site_url . 'assessment/assessor_details/' . base64_encode($dtRow['id']) . '" 
                                data-target="#LoadModalFilter" data-toggle="modal">' . $assessor_status . ' </a>';
                    } else {
                        if ($assessor_status == 'Completed') {
                            $row[] = '<span class="label label-sm label-success status-active" > Completed </span>';
                        } else {
                            $row[] = '<span class="label label-sm label-warning status-active" > Incomplete </span>';
                        }
                    }
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if ($acces_management->allow_add or $acces_management->allow_view or $acces_management->allow_edit or $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_view) {
                            $action .= '<li>
                                            <a href="' . $site_url . 'assessment/view/' . base64_encode($dtRow['id']) . '/' . $view_type . '">
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

    public function CandidateDatatableRefresh($assessment_id)
    {
        $dtSearchColumns = array('A.user_id', 'A.emp_id', "CONCAT(u.firstname,' ',u.lastname)", '', "CONCAT(cm.first_name,' ',cm.last_name)");

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');

        //        if ($this->mw_session['company_id'] == "") {
//            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
//        } else {
//            $cmp_id =$this->mw_session['company_id'];
//        }
//        if ($cmp_id != "") {
//            if ($dtWhere <> '') {
//                $dtWhere .= " AND am.company_id  = " . $cmp_id;
//            } else {
//                $dtWhere .= " WHERE am.company_id  = " . $cmp_id;
//            }
//        }
//        $superaccess = $this->mw_session['superaccess'];
//        if(!$superaccess){
//            $trainer_id= $this->mw_session['user_id'];
//            $dtWhere .= " AND atr.assessment_id IN (select assessment_id FROM assessment_managers where trainer_id=$trainer_id)";
//        }

        $DTRenderArray = $this->assessment_model->LoadCandidateDataTableRefresh($dtWhere, $dtOrder, $dtLimit, $assessment_id);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id', 'emp_id', 'trainee_name', 'candidate_status', 'trainer_name', 'assessor_status', 'Action');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        $viewmode = 1;
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            $Curr_Time = strtotime($now);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Action") {
                    $action = '';
                    if ($acces_management->allow_view) {
                        $action .= '<a href="' . $site_url . 'assessment/LoadViewModal/' . base64_encode($dtRow['assessment_id']) . '/' . base64_encode($dtRow['user_id']) . '/' . $dtRow['trainer_id'] . '/1" data-toggle="modal" data-target="#stack2">
                                        <i class="fa fa-eye"></i>&nbspView
                                    </a>';
                    }
                    $row[] = $action;
                } elseif ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function LastplayedDatatableRefresh($assessment_id, $user_id)
    {
        // echo $assessment_id."---".$user_id;

        $Company_id = $this->session->userdata();
        $c_id = $Company_id['awarathon_session']['company_id'];
        $Data_list = $this->ai_reports_model->get_questions_user_details($c_id, $assessment_id, $user_id);
        // print_r($Data_list);
        // exit;
        $dtSearchColumns = array('A.user_id', "CONCAT(u.firstname,' ',u.lastname)", '', "CONCAT(cm.first_name,' ',cm.last_name)");

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');

        $DTRenderArray = $this->assessment_model->LoadCandidateDataTableRefresh($dtWhere, $dtOrder, $dtLimit, $assessment_id);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id', 'trainee_name', 'candidate_status', 'trainer_name', 'assessor_status', 'Action');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        $viewmode = 1;
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            $Curr_Time = strtotime($now);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Action") {
                    $action = '';
                    if ($acces_management->allow_view) {
                        $action .= '<a href="' . $site_url . 'assessment/LoadViewModal/' . base64_encode($dtRow['assessment_id']) . '/' . base64_encode($dtRow['user_id']) . '/' . $dtRow['trainer_id'] . '/1" data-toggle="modal" data-target="#stack2">
                                        <i class="fa fa-eye"></i>&nbspView
                                    </a>';
                    }
                    $row[] = $action;
                } elseif ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function AssessorDatatableRefresh($assessment_id)
    {
        $dtSearchColumns = array('A.trainer_id', "CONCAT(cm.first_name,' ',cm.last_name)");

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');
        $acces_management = $this->acces_management;
        //        if ($assessment_id != "") {
//            if ($dtWhere <> '') {
//                $dtWhere .= " AND A.assessment_id  = " . $assessment_id;
//            } else {
//                $dtWhere .= " WHERE A.assessment_id  = " . $assessment_id;
//            }
//        }        
//        if ($this->mw_session['company_id'] == "") {
//            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
//        } else {
//            $cmp_id =$this->mw_session['company_id'];
//        }
//        if ($cmp_id != "") {
//            if ($dtWhere <> '') {
//                $dtWhere .= " AND am.company_id  = " . $cmp_id;
//            } else {
//                $dtWhere .= " WHERE am.company_id  = " . $cmp_id;
//            }
//        }
//        $superaccess = $this->mw_session['superaccess'];
//        if(!$superaccess){
//            $trainer_id= $this->mw_session['user_id'];
//            $dtWhere .= " AND atr.assessment_id IN (select assessment_id FROM assessment_managers where trainer_id=$trainer_id)";
//        }
        $superaccess = $this->mw_session['superaccess'];
        if ($superaccess) {
            $trainer_id = '';
        } else {
            $trainer_id = $this->mw_session['user_id'];
        }

        $DTRenderArray = $this->assessment_model->LoadAssessorDataTableRefresh($dtWhere, $dtOrder, $dtLimit, $assessment_id, $trainer_id);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('trainer_id', 'trainer_name', 'assessor_status', 'completed_candidate', 'Action');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            $Curr_Time = strtotime($now);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Action") {
                    $action = '';
                    if ($acces_management->allow_view) {
                        $action .= '<a href="' . $site_url . 'assessment/assessor_status_view/' . $assessment_id . '/' . base64_encode($dtRow['trainer_id']) . '" data-toggle="modal" data-target="#stack2">
                                        <i class="fa fa-eye"></i>&nbspView
                                    </a>';
                    }
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] == "completed_candidate") {
                    $row[] = $dtRow['is_completed'] . '/' . $dtRow['total_user'];
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function AssessorSubDatatableRefresh($assessment_id, $trainer_id)
    {
        $dtSearchColumns = array('CONCAT(u.firstname," ",u.lastname)');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);

        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');
        $acces_management = $this->acces_management;

        //        if ($dtWhere <> '') {
//            $dtWhere .= " AND A.assessment_id  = " . $assessment_id;
//        } else {
//            $dtWhere .= " WHERE B.assessment_id  = " . $assessment_id;
//        }
//        if ($this->mw_session['company_id'] == "") {
//            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
//        } else {
//            $cmp_id =$this->mw_session['company_id'];
//        }
//        if ($cmp_id != "") {
//            if ($dtWhere <> '') {
//                $dtWhere .= " AND am.company_id  = " . $cmp_id;
//            } else {
//                $dtWhere .= " WHERE am.company_id  = " . $cmp_id;
//            }
//        }
//        $superaccess = $this->mw_session['superaccess'];
//        if(!$superaccess){
//            $trainer_id= $this->mw_session['user_id'];
//            $dtWhere .= " AND atr.assessment_id IN (select assessment_id FROM assessment_managers where trainer_id=$trainer_id)";
//        }

        $DTRenderArray = $this->assessment_model->LoadAssessorSubDataTableRefresh($dtWhere, $dtOrder, $dtLimit, $assessment_id, $trainer_id);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('trainee_name', 'candidate_status', 'assessor_status', 'Action');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            $Curr_Time = strtotime($now);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Action") {
                    $action = '';
                    if ($acces_management->allow_view) {
                        $action .= '<a href="' . $site_url . 'assessment/LoadViewModal/' . base64_encode($assessment_id) . '/' . base64_encode($dtRow['user_id']) . '/' . $dtRow['trainer_id'] . '/1" data-toggle="modal" data-target="#stack3">
                                        <i class="fa fa-eye"></i>&nbspView
                                    </a>';
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

    public function candidate_details($Encode_id)
    {
        $data['assessment_id'] = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['RegionList'] = $this->assessment_model->get_TrainerRegionList($company_id);
        $this->load->view('assessment/CandidateStatusModal', $data);
    }
    public function last_played_details($user_id, $assessment_id)
    {
        $data['assessment_id'] = $assessment_id;
        $data['user_id'] = $user_id;
        $data['company_id'] = $this->mw_session['company_id'];
        $data['Data_list'] = $this->assessment_model->get_questions_user_details($this->mw_session['company_id'], $assessment_id, $user_id);
        // echo "<pre>";
        // print_r($data['Data_list']);
        // exit;
        $this->load->view('assessment/LastPlayedModal', $data);
    }
    public function assessor_details($Encode_id)
    {
        $data['assessment_id'] = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }

        $data['RegionList'] = $this->assessment_model->get_TrainerRegionList($company_id);
        $this->load->view('assessment/AssessorStatusModal', $data);
    }

    public function assessor_status_view($assessment_id, $Encode_id)
    {
        $data['trainer_id'] = base64_decode($Encode_id);
        $data['assessment_id'] = $assessment_id;
        $this->load->view('assessment/AssessorStatusSubModal', $data);
    }

    public function AssessmentUsers($Encode_id, $mode = '', $view_type = '')
    {
        $site_url = base_url();
        $assessment_id = base64_decode($Encode_id);
        $acces_management = $this->acces_management;
        $dtSearchColumns = array('u.user_id', 'u.firstname', 'u.email', 'u.mobile', 'tr.region_name', 'w.is_completed');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $trainer_id = '';
        if ($dtWhere <> '') {
            $dtWhere .= " AND amu.assessment_id  = " . $assessment_id;
        } else {
            $dtWhere .= " WHERE amu.assessment_id  = " . $assessment_id;
        }
        $fttrainer_id = $this->input->get('fttrainer_id') ? $this->input->get('fttrainer_id') : '';
        if ($fttrainer_id != "") {
            $dtWhere .= " AND u.trainer_id  = " . $fttrainer_id;
        }
        $superaccess = $this->mw_session['superaccess'];
        if (isset($this->acces_management) and ((int) $this->acces_management->role == 1) and ((int) $this->acces_management->allow_access == 1)) {
            //Changes suggested by rahul on 06.10.2021
            //If role is administrator and full access rights given for menu video assessmenent then show all assessment & trainee.
            $trainer_id = '';
        } else if (!$superaccess) {
            $trainer_id = $this->mw_session['user_id'];
        }
        $AssesserMapped = $this->common_model->get_value('assessment_mapping_user', 'id', 'assessment_id=' . $assessment_id);
        if (count((array) $AssesserMapped) > 0) {
            if ($trainer_id != '') {
                $dtWhere .= " AND amu.trainer_id  = " . $trainer_id;
                //$dtWhere .= " AND am.trainer_id  = " . $trainer_id;
            }
        }
        $dtWhere .= " AND u.user_id IS NOT NULL ";

        $trainer_data = $this->assessment_model->get_trainerdata($assessment_id, $trainer_id);

        $DTRenderArray = $this->assessment_model->LoadAssessmentUsers($dtWhere, $dtOrder, $dtLimit);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        //Get assessment type
        $ass_type = $this->common_model->get_value('assessment_mst', 'assessment_type', "id=$assessment_id");
        $output['assessment_type'] = $ass_type->assessment_type;

        if (!empty($ass_type) && $ass_type->assessment_type == 2) {
            $dtDisplayColumns = array('user_id', 'name', 'email', 'mobile', 'region_name', 'is_completed', 'assesor_status', 'last_played', 'Actions');
        } else {
            $dtDisplayColumns = array('user_id', 'name', 'email', 'mobile', 'region_name', 'is_completed', 'assesor_status', 'Actions');
        }
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
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
                } elseif ($dtDisplayColumns[$i] == "assesor_status") {
                    if (count((array) $trainer_data) > 0) {
                        $isTrainerComplete = 0;
                        foreach ($trainer_data as $value) {
                            if (!empty($dtRow['user_id'])) {
                                $isTrainerComplete += $this->assessment_model->isCompletedAssessor($assessment_id, $value->trainer_id, $dtRow['user_id']);
                            }
                        }
                        if ($isTrainerComplete == count((array) $trainer_data)) {
                            $status = '<span class="label label-sm label-success status-active" > Completed </span>';
                        } else {
                            $status = '<span class="label label-sm label-warning status-active" > Incomplete </span>';
                        }
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "last_played") {

                    $row[] = '<a href="' . $site_url . 'assessment/last_played_details/' . $dtRow['user_id'] . '/' . $dtRow['assessment_id'] . '" 
                            data-target="#LoadModalFilter" class="label label-sm label-success status-active" data-toggle="modal">Last Played</a>';

                } elseif ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if (count((array) $trainer_data) > 0) {
                        if ($trainer_id != '') {
                            $action .= '<a data-target="#LoadModalFilter" data-toggle="modal" href="' . $site_url . 'assessment/LoadViewModal/' . base64_encode($dtRow['assessment_id']) . '/' . base64_encode($dtRow['user_id']) . '/' . $trainer_id . '/' . $view_type . '">
                                    <i class="fa fa-star-half-full"></i>&nbsp;Rate
                                        </a> ';
                        } else {
                            $action = '<div class="btn-group row">
                                    <div class="col-md-6">
                                    <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                        Rate&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">';
                            foreach ($trainer_data as $value) {
                                $action .= '<li> 
                                        <a data-target="#LoadModalFilter" data-toggle="modal" href="' . $site_url . 'assessment/LoadViewModal/' . base64_encode($dtRow['assessment_id']) . '/' . base64_encode($dtRow['user_id']) . '/' . $value->trainer_id . '/' . $view_type . '">
                                            <i class="fa fa-user"></i>&nbsp;' . $value->name .
                                    ' </a>
                                    </li>';
                            }
                            $action .= '</ul>
                            </div>';
                            //                            if($this->mw_session['superaccess']){
//                                $action .= '<div class="col-md-6"><a onclick="LoadDeleteDialog_ass_user(\'' . base64_encode($dtRow['user_id']) . '\');" href="javascript:void(0)"><button type="button" id="remove" value="' . $dtRow['id'] . '" name="remove"  class="btn btn-danger btn-sm delete" style="padding: 1px 8px;"'
//                                        . ' ><i class="fa fa-times"></i></button></a></div>';
//                            }   
                            $action .= '</div>';
                        }
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

    public function LoadViewModal($encoded_id, $en_user_id, $trainer_id, $view_type = 0)
    {

        $assessment_id = base64_decode($encoded_id);
        $user_id = base64_decode($en_user_id);
        $AssessmentData = $this->common_model->get_value('assessment_mst', 'assessment_type,assessor_dttm,ratingstyle,is_weights', 'id=' . $assessment_id);
        $company_id = $this->mw_session['company_id'];

        $RatingData = $this->common_model->get_value('assessment_trainer_result', '*', 'trainer_id=' . $trainer_id . ' AND user_id=' . $user_id . ' AND assessment_id=' . $assessment_id);

        $UserData = $this->common_model->get_value('device_users', 'user_id,concat(firstname," ",lastname) as username,email,avatar', 'company_id=' . $company_id . ' AND user_id=' . $user_id);
        $trainer_name = $this->common_model->get_value('company_users', 'userid,concat(first_name," ",last_name) as trainer_name', 'company_id=' . $company_id . ' AND userid=' . $trainer_id);
        $remarks_data = '';
        //$QuestionData = $this->assessment_model->LoadAssessmentQuestions($assessment_id);
        $assessment_type = $AssessmentData->assessment_type;
        $QuestionData = $this->assessment_model->LoadParameterQuestions($assessment_id, $user_id, $trainer_id, $assessment_type);
        $ass_result_id = '';
        $video_screen = '';
        $embed = '';
        $remarks = '';
        $your_rating = 0;
        $team_rating = 0;
        $cnt = 0;
        if (count((array) $RatingData) > 0) {
            $remarks = $RatingData->remarks;
        }
        $data['ratingstyle'] = $AssessmentData->ratingstyle;
        $data['is_weights'] = $AssessmentData->is_weights;
        $ScoreData = $this->assessment_model->get_your_rating($assessment_id, $user_id, $trainer_id, $data['is_weights']);

        $total_rating = $this->assessment_model->get_team_rating($assessment_id, $user_id, $trainer_id, $view_type);
        if (count((array) $ScoreData) > 0 && $ScoreData->total_rating != 0) {
            $your_rating = $ScoreData->total_rating;

            //            if($data['is_weights'] && $data['ratingstyle']==2){
//                    $your_rating = round(($ScoreData->w_percentage / $ScoreData->weights), 2);
//            }elseif($data['is_weights'] && $data['ratingstyle']==1){
//                    $your_rating = round(($ScoreData->w_score / $ScoreData->weights), 2);
//            }elseif($data['ratingstyle']==2){
//                    $your_rating = round($ScoreData->avg_percentage / ($ScoreData->total_param), 2);
//            }else{
//                    $your_rating = round($ScoreData->total_score / ($ScoreData->total_rating) * 100, 2);
//            }
            $cnt = 1;
        }
        if ($total_rating->total_trainer > 0) {
            $team_rating = number_format(($total_rating->total_rating + $your_rating) / ($total_rating->total_trainer + $cnt), 2);
        } else {
            $team_rating = $your_rating;
        }
        $data['your_rating'] = $your_rating . '%';
        $data['team_rating'] = $team_rating . '%';
        $data['trainer_id'] = $trainer_id;
        $data['remarks'] = $remarks;
        $data['question_remarks'] = $remarks_data;
        $data['ass_result_id'] = $ass_result_id;
        $data['UserData'] = $UserData;
        $data['trainer_name'] = $trainer_name;
        $data['Questions'] = $QuestionData;
        $data['company_id'] = $company_id;
        $data['assessment_id'] = $assessment_id;
        $data['assessment_type'] = $assessment_type;
        if (!$view_type) {

            $view_type = (strtotime($AssessmentData->assessor_dttm) < strtotime(date('Y-m-d H:i:s')) ? 1 : 0);
            if (!$view_type) {
                $ISEXIST = $this->common_model->get_value('assessment_managers', 'id', 'assessment_id=' . $assessment_id . ' AND trainer_id=' . $trainer_id);
                if (count((array) $ISEXIST) == 0) {
                    $view_type = 1;
                } else {
                    $cnt_rate = $this->common_model->get_value('assessment_complete_rating', 'id', 'assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id);
                    if (count((array) $cnt_rate) > 0) {
                        $view_type = 1;
                    }
                }
            }
        }
        $data['is_supervisor'] = $view_type;

        $data['user_id'] = $user_id;

        //$superaccess = $this->mw_session['superaccess'];  
        //$ISEXIST  = $this->common_model->get_value('assessment_managers', 'id,trainer_id', 'trainer_id='.$trainer_id); 
        //if($superaccess){
        //$data['is_supervisor'] = 1;
        //}else{
        //$data['is_supervisor'] = 0;
        //}		
        /* if(count((array)$ISEXIST) > 0 || $superaccess){
          $is_supervisor = 1;
          }else{
          $is_supervisor = 0;
          }
          $data['is_supervisor'] = $is_supervisor; */


        $this->load->view('assessment/ViewAssessmentModal', $data);
    }

    public function getquestionwiseparameter($q_id, $srno)
    {

        $superaccess = $this->mw_session['superaccess'];
        $trainer_id = $this->mw_session['user_id'];
        $is_supervisor = $this->input->post('is_supervisor', true);
        $assessment_id = $this->input->post('assessment_id', true);
        $user_id = $this->input->post('user_id', true);
        $trainer_id = $this->input->post('trainer_id', true);
        $htdata = '';
        $QParameter_table = '';
        $your_rating = 0;
        $para_rating = array();
        $remarks_data = '';
        $ParameterData = $this->common_model->get_value('assessment_trans', 'parameter_id', 'question_id=' . $q_id . ' AND assessment_id=' . $assessment_id);
        $calculaterating = 1;
        if ($is_supervisor) {
            $cnt_rate = $this->common_model->get_value('assessment_complete_rating', 'id', 'assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id);
            if (count((array) $cnt_rate) == 0) {
                $calculaterating = 0;
            }
        }
        $AssessmentData = $this->common_model->get_value('assessment_mst', 'assessor_dttm,ratingstyle', 'id=' . $assessment_id);
        if ($calculaterating) {
            if ($AssessmentData->ratingstyle != 2) {
                $StarRatingData = $this->common_model->get_selected_values('assessment_results_trans', 'parameter_id,score', 'question_id=' . $q_id . '  AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id . ' AND assessment_id=' . $assessment_id);
                if (count((array) $StarRatingData) > 0) {
                    foreach ($StarRatingData as $val) {
                        $para_rating[$val->parameter_id] = $val->score;
                    }
                }
            }
        }
        $Tdata['mode'] = (strtotime($AssessmentData->assessor_dttm) < strtotime(date('Y-m-d H:i:s')) ? 1 : 2);
        $Question = $this->common_model->get_value('assessment_question', 'id,question,assessor_guide', 'id=' . $q_id);


        if (count((array) $ParameterData) > 0) {
            if ($AssessmentData->ratingstyle != 2) {
                $QParameterData = $this->assessment_model->get_question_parameter($ParameterData->parameter_id);
            } else {
                $QParameterData = $this->assessment_model->get_question_parameter($ParameterData->parameter_id, $q_id, $user_id, $trainer_id, $assessment_id);
            }
            $Tdata['cnd_para'] = count((array) $QParameterData);
            if (count((array) $QParameterData) > 0) {
                $Tdata['question_id'] = $q_id;
                $Tdata['is_supervisor'] = $is_supervisor;
                $Tdata['QParameterData'] = $QParameterData;
                $Tdata['para_rating'] = $para_rating;
                $Tdata['Question'] = $srno . ". " . $Question->question;
                //$api_data = $this->common_model->get_value('api_details', 'client_id,client_secret,access_token,url', 'name="vimeo" and status=1');
                $ass_type = $this->common_model->get_value('assessment_mst', 'assessment_type', "id=$assessment_id");
                $Tdata['assessment_type'] = $ass_type->assessment_type;
                if (!empty($ass_type) && $ass_type->assessment_type == 2) {
                    $Tdata['video_data'] = $this->assessment_model->get_audio_data($assessment_id, $user_id, $q_id); //spotlight get audio details
                } else {
                    $Tdata['video_data'] = $this->common_model->get_value('assessment_results', 'id,video_url,vimeo_uri', 'question_id=' . $q_id . ' AND user_id=' . $user_id . ' AND assessment_id=' . $assessment_id . " order by id desc"); //roleplay get video details
                }
                $trainer_question = $this->common_model->get_value('assessment_trainer_remarks', 'remarks', ' assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id . ' AND question_id=' . $q_id);
                $remarks_data = (count((array) $trainer_question) > 0 ? $trainer_question->remarks : '');
                $Tdata['ratingstyle'] = $AssessmentData->ratingstyle;
                $QParameter_table = $this->load->view('assessment/parameter_table', $Tdata, TRUE);
            }
        }

        //        $ScoreData = $this->assessment_model->get_your_rating($q_id,$assessment_id,$user_id);         
//        if(count((array)$ScoreData) > 0 && $ScoreData->total_rating !=0){
//            $your_rating = round($ScoreData->total_score/($ScoreData->total_rating)*100, 2);
//        }
//        $data['your_rating'] = $your_rating.'%';
        $data['assessor_guide'] = $Question->assessor_guide;
        //$data['cnt_rate'] = count((array)$cnt_rate);
        $data['question_comments'] = $remarks_data;
        $data['QParameter_table'] = $QParameter_table;

        echo json_encode($data);
    }

    public function save_rating($rate_flag = '')
    {
        $Message = '';
        $SuccessFlag = 1;
        $user_rating = 0;
        $your_rating = 0;
        $team_rating = 0;
        $cnt = 0;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('assessment_id', 'Assessment name', 'required');
        $this->form_validation->set_rules('question_id', 'question', 'required');
        $this->form_validation->set_rules('trainer_id', 'trainer', 'required');
        $this->form_validation->set_rules('user_id', 'Device User', 'required');
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            $trainer_id = $this->input->post('trainer_id');
            $parameter_rating = $this->input->post('rating');
            $question_id = $this->input->post('question_id');
            $assessment_id = $this->input->post('assessment_id');
            $user_id = $this->input->post('user_id');
            $ass_result_id = $this->input->post('ass_result_id', true);
            if ($ass_result_id == "") {
                $ass_result_id = 0;
            }
            $que_remark = $this->input->post('que_remark', true);
            $remark_que = $this->input->post('remark_que', true);
            $Ratingdata = array(
                'result_id' => $ass_result_id,
                'assessment_id' => $assessment_id,
                'trainer_id' => $trainer_id,
                'user_id' => $user_id,
                'question_id' => $question_id
            );
            $para_array = array();
            $existpara = array();
            $AssessmentData = $this->common_model->get_value('assessment_mst', 'assessor_dttm,ratingstyle,is_weights', 'id=' . $assessment_id);
            $is_weights = $AssessmentData->is_weights;
            $ratingstyle = $AssessmentData->ratingstyle;
            // $Parameter = $this->common_model->get_value('assessment_trans', 'parameter_id', 'question_id=' . $question_id . ' AND assessment_id=' . $assessment_id);
            // if (count((array)$Parameter) > 0 && $Parameter->parameter_id != '') {
            //     $existpara = explode(',', $Parameter->parameter_id);
            // }
            $Parameter = $this->common_model->get_selected_values('assessment_trans_sparam', 'parameter_id, parameter_label_id, SUM(parameter_weight) as parameter_weight', 'question_id=' . $question_id . ' AND assessment_id=' . $assessment_id . ' GROUP BY parameter_id');
            $ParameterData = [];
            if (!empty($Parameter)) {
                foreach ($Parameter as $para) {
                    $ParameterData[$para->parameter_id] = $para;
                }
            }
            $existpara = array_keys($ParameterData);
            if ($parameter_rating != '' && count((array) $parameter_rating) > 0) {
                foreach ($parameter_rating as $para_key => $rating) {
                    $para_array[] = $para_key;
                    $Ratingdata['parameter_id'] = $para_key;
                    $Ratingdata['parameter_label_id'] = isset($ParameterData[$para_key]) ? $ParameterData[$para_key]->parameter_label_id : 0;
                    if ($ratingstyle == 2) {
                        $Ratingdata['percentage'] = $rating;
                        $rating = number_format(($Ratingdata['percentage'] * 5) / 100, 2);
                    } else {
                        $Ratingdata['percentage'] = number_format(($rating / 5) * 100, 2);
                    }
                    $Ratingdata['score'] = $rating;
                    $parameter_weight = isset($ParameterData[$para_key]) ? (float) $ParameterData[$para_key]->parameter_weight : 0;
                    $weighted_percentage = number_format($Ratingdata['percentage'] * ($parameter_weight / 100), 2);
                    
                    $Ratingdata['weighted_percentage'] = $weighted_percentage;

                    $ISEXIST = $this->common_model->get_value('assessment_results_trans', 'parameter_id', 'question_id=' . $question_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id . ' AND parameter_id=' . $para_key . ' AND assessment_id=' . $assessment_id);
                    if (count((array) $ISEXIST) > 0) {
                        $this->assessment_model->update_assessment_results_trans('assessment_results_trans', $question_id, $ass_result_id, $user_id, $trainer_id, $para_key, $Ratingdata);
                    } else {
                        $this->common_model->insert('assessment_results_trans', $Ratingdata);
                    }
                }
            }
            $parameter = array_diff($existpara, $para_array);
            if (count((array) $parameter) > 0) {
                foreach ($parameter as $val) {
                    $Ratingdata['parameter_id'] = $val;
                    $Ratingdata['score'] = 0;
                    $Ratingdata['percentage'] = 0;
                    $Ratingdata['weighted_percentage'] = 0;
                    $ISEXIST = $this->common_model->get_value('assessment_results_trans', 'parameter_id', 'question_id=' . $question_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id .
                        ' AND parameter_id=' . $val . ' AND assessment_id=' . $assessment_id);
                    if (count((array) $ISEXIST) > 0) {
                        $this->assessment_model->update_assessment_results_trans('assessment_results_trans', $question_id, $ass_result_id, $user_id, $trainer_id, $val, $Ratingdata);
                    } else {
                        $this->common_model->insert('assessment_results_trans', $Ratingdata);
                    }
                }
            }
            $UserScoreData = $this->assessment_model->get_your_rating($assessment_id, $user_id, $trainer_id, $is_weights);
            if (count((array) $UserScoreData) > 0 && $UserScoreData->total_rating != 0) {
                $user_rating = $UserScoreData->total_rating;
                /*if($ratingstyle==2){
                                $user_rating = round($UserScoreData->avg_percentage / ($UserScoreData->total_param), 2);
                            }else{
                                $user_rating = round($UserScoreData->total_score / ($UserScoreData->total_rating) * 100, 2);
                            }*/
            }
            //        $data = array('remarks' => $que_remark, 'user_rating' => $user_rating);
            //$this->assessment_model->update_assessment_results('assessment_results',$company_id,$assessment_id,$user_id,$data);
//        $this->common_model->update('assessment_results', 'id', $ass_result_id, $data);

            $trainer_data = array(
                'assessment_id' => $assessment_id,
                'user_id' => $user_id,
                'trainer_id' => $trainer_id,
                'remarks' => $que_remark,
                'user_rating' => $user_rating,
            );
            $trainer_rate = $this->common_model->get_value('assessment_trainer_result', 'id', 'assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id);
            if (count((array) $trainer_rate) > 0) {
                $this->common_model->update('assessment_trainer_result', 'id', $trainer_rate->id, $trainer_data);
            } else {
                $this->common_model->insert('assessment_trainer_result', $trainer_data);
            }
            $remark_data = array(
                'result_id' => $ass_result_id,
                'assessment_id' => $assessment_id,
                'user_id' => $user_id,
                'trainer_id' => $trainer_id,
                'remarks' => $remark_que,
                'question_id' => $question_id
            );
            $trainer_question = $this->common_model->get_value('assessment_trainer_remarks', 'id', ' assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id . ' AND question_id=' . $question_id);
            if (count((array) $trainer_question) > 0) {
                $this->common_model->update('assessment_trainer_remarks', 'id', $trainer_question->id, $remark_data);
            } else {
                $this->common_model->insert('assessment_trainer_remarks', $remark_data);
            }
            $cnt_rate = $this->common_model->get_value('assessment_complete_rating', 'id', 'assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id);
            $prameterData = $this->assessment_model->LoadTotalParameter($assessment_id, $user_id, $trainer_id);
            $total_para = count((array) explode(',', $prameterData->para_list));

            // 21-02-2023_start
            if (isset($parameter_rating) && count((array) $parameter_rating) > 0) {
                foreach ($parameter_rating as $key => $rating) {
                    
                    if ($rating == 0) {
                        $Message = "Please give rating to all question's Parameter!";
                        $SuccessFlag = 0;
                        break;
                    }
                    if (!$SuccessFlag) {
                        break;
                    }
                }
                if ($SuccessFlag) {
                    $Message = "Rating Update successfully...!";
                }
            }
            // 21-02-2023_end
            if ($rate_flag == 1 && count((array) $cnt_rate) == 0) {
                if (isset($assessment_id) && isset($user_id)) {
                    $this->db->select("question_id");
                    $this->db->from("assessment_trans");
                    $this->db->where("assessment_id", $assessment_id);
                    $question_ids = $this->db->get()->result();

                    $this->db->DISTINCT("question_id");
                    $this->db->select("question_id");
                    $this->db->from("assessment_results_trans");
                    $this->db->where("assessment_id", $assessment_id);
                    $this->db->where("user_id", $user_id);
                    $rated_questions = $this->db->get()->result();

                    $total_qna = array();

                    if (count((array) $question_ids) > 0) {
                        foreach ($question_ids as $tq) {
                            $total_qna[] = $tq->question_id;
                        }
                    }

                    $rated_question = array();
                    if (count((array) $rated_questions) > 0) {
                        foreach ($rated_questions as $rt_qna) {
                            $rated_question[] = $rt_qna->question_id;
                        }
                    }

                    for ($i = 0; $i < count((array) $total_qna); $i++) {
                        if (!in_array($total_qna[$i], $rated_question)) {
                            $Message = "Please give rating to all question's Parameter!";
                            $SuccessFlag = 0;
                            break;
                        }
                        if (!$SuccessFlag) {
                            break;
                        }
                    }
                }
                if ($SuccessFlag != 0) {
                    if ($total_para == $prameterData->tot_para) {
                        $qrate_data = array(
                            'assessment_id' => $assessment_id,
                            'user_id' => $user_id,
                            'trainer_id' => $trainer_id
                        );
                        $this->common_model->insert('assessment_complete_rating', $qrate_data);
                        $this->stored_parameterwise_data($assessment_id, $user_id);

                    
                        // Notification EMail after manual rating done - "20-02-2023"
                        $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='manual_reports_&_combined_reports_(rep)'");

                        if (count((array) $emailTemplate) > 0) {
                            $pattern[0] = '/\[SUBJECT\]/';
                            $pattern[1] = '/\[NAME\]/';
                            $pattern[2] = '/\[ASSESSMENT_NAME\]/';
                            $pattern[3] = '/\[REPORT_LINK\]/';

                            $subject = $emailTemplate->subject;
                            $replacement[0] = $subject;

                            $UserData = $this->common_model->get_value('device_users', 'company_id,concat(firstname," ",lastname) as trainee_name,email', '  user_id =' . $user_id);
                            $ToName = $UserData->trainee_name;
                            $email_to = $UserData->email;
                            $Company_id = $UserData->company_id;
                            $replacement[1] = $UserData->trainee_name;

                            $assessment_data = $this->common_model->get_value('assessment_mst', 'assessment', 'id = ' . $assessment_id);
                            $replacement[2] = $assessment_data->assessment;

                            $user_id_enc = base64_encode($user_id);
                            $report_link = '<table cellpadding="5">';
                            $report_link .= '<tr><td>Assessor Report</td><td>' . base_url() . 'pdf/manual/' . $Company_id . '/' . $assessment_id . '/' . $user_id_enc . '</td></tr>';
                            $report_link .= '<tr><td>Final Report</td><td>' . base_url() . 'pdf/combine/' . $Company_id . '/' . $assessment_id . '/' . $user_id_enc . '</td></tr>';
                            $report_link .= '</table>';
                            $replacement[3] = $report_link;

                            $message = $emailTemplate->message;
                            $body = preg_replace($pattern, $replacement, $message);
                            $ReturnArray = $this->common_model->sendPhpMailer($Company_id, $ToName, $email_to, $subject, $body);
                            $log_data = [
                                'company_id' => $Company_id,
                                'assessment_id' => $assessment_id,
                                'email_alert_id' => $emailTemplate->alert_id, //manual_reports_&_combined_reports_(rep)
                                'user_id' => $user_id,
                                'role_id' => 3,
                                'user_name' => $ToName,
                                'email' => $email_to,
                                'attempt' => 1,
                                'scheduled_at' => date('Y-m-d H:i:s'),
                                'is_sent' => $ReturnArray['sendflag'],
                                'sent_at' => date('Y-m-d H:i:s')
                            ];
                            $this->common_model->insert('assessment_notification_schedule', $log_data); //Add Reps notification log - Manual & Combined reports link send
                        }
                        // Notification EMail after manual rating

                        $Message = "Rating updated successfully.!";
                    }
                } else {
                    $Message = "Please give rating to all question's Parameter!";
                    $SuccessFlag = 0;
                }
            }
            // else {
            //     $Message = "Rating updated successfully.!";
            // }

            $ScoreData = $this->assessment_model->get_your_rating($assessment_id, $user_id, $trainer_id, $is_weights);

            if (count((array) $ScoreData) > 0 && $ScoreData->total_rating != 0) {
                /*if($ratingstyle==2){
                                $your_rating = round($ScoreData->avg_percentage / ($ScoreData->total_param), 2);
                            }else{
                                $your_rating = round($ScoreData->total_score / ($ScoreData->total_rating) * 100, 2);
                            }*/
                $your_rating = $ScoreData->total_rating;
                $cnt = 1;
            }
            $total_rating = $this->assessment_model->get_team_rating($assessment_id, $user_id, $trainer_id, 0);
            if (count((array) $total_rating) > 0 && $total_rating->total_rating > 0) {
                $team_rating = round(($total_rating->total_rating + $your_rating) / ($total_rating->total_trainer + $cnt), 2);
            } else {
                $team_rating = $your_rating;
            }
            $para_data = $this->common_model->get_value('assessment_results_trans', 'count(distinct parameter_id) as total_para', 'assessment_id=' . $assessment_id . ' AND question_id=' . $question_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id . ' AND score != 0');

            $para_cnt = $this->input->post('cnt_para', true);

            if ($para_cnt == $para_data->total_para) {
                $Rdata['cross_tick'] = '&#10004;';
            } else {
                $Rdata['cross_tick'] = '';
            }

            $Rdata['cnt_rate'] = count((array) $cnt_rate);
            $Rdata['your_rating'] = $your_rating . '%';
            $Rdata['team_rating'] = $team_rating . '%';
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function save_retake()
    {
        $Message = '';
        $SuccessFlag = 1;
        $assessment_id = $this->input->post('assessment_id');
        $user_id = $this->input->post('user_id');
        $company_id = $this->input->post('company_id');
        $data = array('retake' => 1);
        $this->assessment_model->update_assessment_results('assessment_results', $company_id, $assessment_id, $user_id, $data);
        $Message = "Save successfully.!";
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function save_question_remark()
    {
        $trainer_id = $this->input->post('trainer_id');
        $question_id = $this->input->post('question_id');
        $assessment_id = $this->input->post('assessment_id');
        $user_id = $this->input->post('user_id');
        //$ass_result_id = $this->input->post('ass_result_id');        

        $remark_que = $this->input->post('remark_que');
        $remark_data = array(
            'assessment_id' => $assessment_id,
            'user_id' => $user_id,
            'trainer_id' => $trainer_id,
            'remarks' => $remark_que,
            'question_id' => $question_id
        );
        $trainer_question = $this->common_model->get_value('assessment_trainer_remarks', 'id', ' assessment_id=' . $assessment_id . ' AND user_id=' . $user_id . ' AND trainer_id=' . $trainer_id . ' AND question_id=' . $question_id);
        if (count((array) $trainer_question) > 0) {
            $this->common_model->update('assessment_trainer_remarks', 'id', $trainer_question->id, $remark_data);
        } else {
            $this->common_model->insert('assessment_trainer_remarks', $remark_data);
        }
    }
    public function stored_parameterwise_data($assessment_id = '', $user_id = '')
    {
        $lcwhere = 'status=1';
        if ($assessment_id != '') {
            $lcwhere .= ' AND id =' . $assessment_id;
        }
        $assessment_set = $this->common_model->get_selected_values('assessment_mst', 'id,ratingstyle,is_weights', $lcwhere);
        if (count($assessment_set) > 0) {
            foreach ($assessment_set as $value) {
                $this->assessment_model->insert_parameterwise_data($value->id, $value->ratingstyle, $value->is_weights, $user_id);
                //$this->assessment_model->insert_assessmentwise_data($value->id,$value->ratingstyle,$value->is_weights);
            }
        }
    }
}