<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class View_Trinity extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('view_trinity');
        if(!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->common_db = $this->common_model->connect_db2();
        $this->acces_management = $acces_management;
        $this->load->model('view_trinity_model');
        $this->load->model('assessment_model');
    }

    public function index() {
        $data['module_id'] = '102';
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

        $this->load->view('view_trinity/index',$data);
    }

    public function view ($id, $view_type)
    {
        $data['module_id'] = '102';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('view_trinity');
            return;
        }
        $assessment_id = base64_decode($id);
        $data['assessment_id'] = $assessment_id;
        $data['Rowset'] = $this->common_model->get_value('assessment_mst', '*', 'id=' . $assessment_id);
        $data['view_type'] = $view_type;
        $this->load->view('view_trinity/view', $data);
    }

    public function DatatableRefresh() {

        $dtSearchColumns = array('am.id', 'am.id', 'am.assessment', 'am.start_dttm', 'am.end_dttm', 'am.assessor_dttm', 'am.assessment');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');
        $view_type = $this->input->get('view_type');
        if($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        if($cmp_id != "") {
            if($dtWhere <> '') {
                $dtWhere .= " AND am.company_id = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE am.company_id = " . $cmp_id;
            }
        }
        $assessment_type = $this->input->get('assessment_type');
        if($assessment_type != "") {
            $dtWhere .= " AND am.assessment_type = " . $assessment_type;
        }
        $question_type = $this->input->get('question_type');
        if($question_type != "") {
            $dtWhere .= " AND am.is_situation = " . $question_type;
        }
        $division_id = '';
        if($this->mw_session['role'] == 4) {
            $division_id = $this->mw_session['division_id'];
            if($division_id != '' && $division_id != 0) {
                $dtWhere .= " AND am.division_id = " . $division_id;
            }
        }
        $status = $this->input->get('filter_status');
        if($status == "1") {
            $dtWhere .= " AND am.assessor_dttm >= '" . $now . "'";
        } elseif ($status == "2") {
            $dtWhere .= " AND am.assessor_dttm < '" . $now . "'";
        } elseif ($status == "3") {
            $dtWhere .= " AND am.start_dttm > '" . $now . "'";
        } elseif ($status == "4") {
            $dtWhere .= " AND am.status = 0";
        }
        $superaccess = $this->mw_session['superaccess'];
        if($superaccess) {
            $trainer_id = '';
        } else if (isset($this->acces_management) and ((int) $this->acces_management->role == 1) and ((int) $this->acces_management->allow_access == 1)) {
            $trainer_id = '';
        } else {
            $trainer_id = $this->mw_session['user_id'];
            if ($view_type == 1) {
                $dtWhere .= " AND am.id IN (select assessment_id FROM assessment_supervisors where trainer_id=$trainer_id)";
            } else {
                $dtWhere .= " AND am.id IN (select assessment_id FROM assessment_managers where trainer_id=$trainer_id)";
            }
        }
        
        $DTRenderArray = $this->view_trinity_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        
        $dtDisplayColumns = array('id', 'assessment_type', 'assessment', 'start_dttm', 'end_dttm', 'assessor_dttm', 'status', 'status1', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            $Curr_Time = strtotime($now);
            for($i = 0; $i < $TotalHeader; $i++) {
                $assessement_status = array();
                if($view_type == 1) {
                    $assessement_status = $this->view_trinity_model->getAssessmentStatus($dtRow['id']);
                } else {
                    $assessement_status = $this->view_trinity_model->getAssessmentStatus($dtRow['id'], $trainer_id);
                }
                $candidate_status = '';
                if(count((array) $assessement_status) > 0) {
                    $candidate_status = ($assessement_status->is_candidate_complete ? 'Completed' : 'Incomplete');
                } else {
                    $candidate_status = 'Incomplete';
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
                        $row[] = '<a href="' . $site_url . 'view_trinity/candidate_details/' . base64_encode($dtRow['id']) . '" 
                                data-target="#LoadModalFilter" data-toggle="modal">' . $candidate_status . ' </a>';
                    } else {
                        if ($candidate_status == 'Completed') {
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
                                            <a href="' . $site_url . 'view_trinity/view/' . base64_encode($dtRow['id']) . '/' . $view_type . '">
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
        foreach ($output as $outkey => $outval) {
            if( $outkey !== 'aaData') {
                $output[$outkey] = $this->security->xss_clean($outval);
            }
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
            $trainer_id = '';
        } else if(!$superaccess) {
            $trainer_id = $this->mw_session['user_id'];
        }
        $AssesserMapped = $this->common_model->get_value('assessment_mapping_user', 'id', 'assessment_id=' . $assessment_id);
        if(count((array) $AssesserMapped) > 0) {
            if($trainer_id != '') {
                $dtWhere .= " AND amu.trainer_id  = " . $trainer_id;
            }
        }
        $dtWhere .= " AND u.user_id IS NOT NULL ";

        $trainer_data = $this->assessment_model->get_trainerdata($assessment_id, $trainer_id);
        $trainerdata_array = $this->assessment_model->get_trainerdata_new($assessment_id, $trainer_id);

        $DTRenderArray = $this->assessment_model->LoadAssessmentUsers($dtWhere, $dtOrder, $dtLimit);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );

        $ass_type = $this->common_model->get_value('assessment_mst', 'assessment_type', "id=$assessment_id");
        $output['assessment_type'] = $ass_type->assessment_type;

        $dtDisplayColumns = array('user_id', 'name', 'email', 'mobile', 'region_name', 'is_completed');

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $userid = $dtRow['user_id'];
            $TotalHeader = count((array) $dtDisplayColumns);
            if ($dtRow['is_completed']) {
                $view_type = 0;
            }else {
                $view_type = 1;
            }
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "is_completed") {
                    if ($dtRow["is_completed"]) {
                        $status = '<span class="label label-sm label-success status-active" > Completed </span>';
                    } else {
                        $status = '<span class="label label-sm label-warning status-active" > Incomplete </span>';
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        foreach ($output as $outkey => $outval) {
            if ($outkey !== 'aaData') {
                $output[$outkey] = $this->security->xss_clean($outval);
            }
        }

        echo json_encode($output);
    }

}