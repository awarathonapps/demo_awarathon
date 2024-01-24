<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Feedback_set extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('feedback_set');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('feedback_set_model');
    }

    public function ajax_feedback_company() {
        return $this->common_model->fetch_company_data($this->input->get());
    }

    public function index() {
        $data['module_id'] = '7.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('feedback_set/index', $data);
    }

    public function create() {
        $data['module_id'] = '7.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('feedback_set');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('feedback_set/create', $data);
    }

    public function edit($id, $step = 1) {
        $feedback_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('feedback_set');
            return;
        }
        $data['company'] = $this->feedback_set_model->find_by_id($feedback_id);
        $data['module_id'] = '7.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->feedback_set_model->fetch_feedback($feedback_id);
        $data['SelectFeedbackType'] = $this->feedback_set_model->SelectedFeedbackType($feedback_id);
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmp_result'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['cmp_result'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['FType'] = $this->feedback_set_model->getFeedbackType($feedback_id);
        $company_id = $data['result']->company_id;
        $data['FTypeResultSet'] = $this->common_model->get_selected_values('feedback_type', 'id,description', 'company_id=' . $company_id);

        // $data['SubTypeResultSet'] = $this->common_model->fetch_object_by_field('feedback_subtype', 'company_id', $company_id);

        $type_subtype_id = $this->feedback_set_model->fetch_feedbackset_type($feedback_id);
        $TypeSubtypeArray = array();
        $subTypeList = '';
        foreach ($type_subtype_id as $key => $tr_id) {
            $feedback_type_id = $tr_id->feedback_type_id;
            $NextType = (isset($type_subtype_id[$key + 1]) ? $type_subtype_id[$key + 1]->feedback_type_id : '' );
            $subTypeList .= $tr_id->feedback_subtype_id . ',';
            if ($NextType != $feedback_type_id) {
                $SubType_set = $this->feedback_set_model->getEditSubtype($feedback_id, $feedback_type_id);
                $TypeSubtypeArray[] = array('id' => $tr_id->id,
                    'feedback_id' => $tr_id->feedbackset_id,
                    'feedback_type_id' => $feedback_type_id,
                    'feedback_subtype_id' => $SubType_set);
            }
        }
        $data['TypeSubtypeArray'] = $TypeSubtypeArray;
        $data['TypeSubtypeArrayCount'] = count((array)$type_subtype_id);
        $data['step'] = $step;
        $UsedWorkshopList = $this->feedback_set_model->CheckWorkshopQnsSet($feedback_id);

        $LockFlag = false;
        if (count((array)$UsedWorkshopList) > 0) {
            $LockFlag = true;
        }
        //$data['basic_lock'] = $this->feedback_set_model->Questionsetis_Played($feedback_id);
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $data['LockFlag'] = $LockFlag;
        $this->load->view('feedback_set/edit', $data);
    }

    public function copy($id) {
        $feedback_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('feedback_set');
            return;
        }
        $data['company'] = $this->feedback_set_model->find_by_id($feedback_id);
        $data['module_id'] = '7.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->feedback_set_model->fetch_feedback($feedback_id);
        $data['SelectFeedbackType'] = $this->feedback_set_model->SelectedFeedbackType($feedback_id);
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmp_result'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['cmp_result'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['FType'] = $this->feedback_set_model->getFeedbackType($feedback_id);
        $company_id = $data['result']->company_id;
        $data['FTypeResultSet'] = $this->common_model->get_selected_values('feedback_type', 'id,description', 'company_id=' . $company_id);

        // $data['SubTypeResultSet'] = $this->common_model->fetch_object_by_field('feedback_subtype', 'company_id', $company_id);

        $type_subtype_id = $this->feedback_set_model->fetch_feedbackset_type($feedback_id);
        $TypeSubtypeArray = array();
        $subTypeList = '';
        foreach ($type_subtype_id as $key => $tr_id) {
            $feedback_type_id = $tr_id->feedback_type_id;
            $NextType = (isset($type_subtype_id[$key + 1]) ? $type_subtype_id[$key + 1]->feedback_type_id : '' );
            $subTypeList .= $tr_id->feedback_subtype_id . ',';
            if ($NextType != $feedback_type_id) {
                $SubType_set = $this->feedback_set_model->getEditSubtype($feedback_id, $feedback_type_id);
                $TypeSubtypeArray[] = array('id' => $tr_id->id,
                    'feedback_id' => $tr_id->feedbackset_id,
                    'feedback_type_id' => $feedback_type_id,
                    'feedback_subtype_id' => $SubType_set);
            }
        }
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $data['TypeSubtypeArray'] = $TypeSubtypeArray;
        $data['TypeSubtypeArrayCount'] = count((array)$type_subtype_id);
        $this->load->view('feedback_set/copy', $data);
    }

    public function view($id, $step = 1) {
        $feedback_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('feedback_set');
            return;
        }
        $data['company'] = $this->feedback_set_model->find_by_id($feedback_id);
        $data['module_id'] = '7.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->feedback_set_model->fetch_feedback($feedback_id);
        $data['SelectFeedbackType'] = $this->feedback_set_model->SelectedFeedbackType($feedback_id);
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmp_result'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['cmp_result'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['FType'] = $this->feedback_set_model->getFeedbackType($feedback_id);
        $company_id = $data['result']->company_id;
        $data['FTypeResultSet'] = $this->common_model->get_selected_values('feedback_type', 'id,description', 'company_id=' . $company_id);

        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $type_subtype_id = $this->feedback_set_model->fetch_feedbackset_type($feedback_id);
        $TypeSubtypeArray = array();
        $subTypeList = '';
        foreach ($type_subtype_id as $key => $tr_id) {
            $feedback_type_id = $tr_id->feedback_type_id;
            $NextType = (isset($type_subtype_id[$key + 1]) ? $type_subtype_id[$key + 1]->feedback_type_id : '' );
            $subTypeList .= $tr_id->feedback_subtype_id . ',';
            if ($NextType != $feedback_type_id) {
                $SubType_set = $this->feedback_set_model->getEditSubtype($feedback_id, $feedback_type_id);
                $TypeSubtypeArray[] = array('id' => $tr_id->id,
                    'feedback_id' => $tr_id->feedbackset_id,
                    'feedback_type_id' => $feedback_type_id,
                    'feedback_subtype_id' => $SubType_set);
            }
        }
        $data['TypeSubtypeArray'] = $TypeSubtypeArray;
        $data['TypeSubtypeArrayCount'] = count((array)$type_subtype_id);
        $data['step'] = $step;
        $this->load->view('feedback_set/view', $data);
    }

    public function DatatableRefresh() {

        $dtSearchColumns = array('a.id', 'a.id', 'c.company_name','l.name', 'a.title', 'a.powered_by', 'a.timer', 'a.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('filter_company_id') ? $this->input->get('filter_company_id') : '');
        } else {
            $cmp_id =$this->mw_session['company_id'];
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE a.company_id  = " . $cmp_id;
            }
        }
        $status = $this->input->get('filter_status');
        if ($status != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.status  = " . $status;
            } else {
                $dtWhere .= " WHERE a.status  = " . $status;
            }
        }
        $language_id = $this->input->get('language_id');
        if ($language_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.language_id  = " . $language_id;
            } else {
                $dtWhere .= " WHERE a.language_id  = " . $language_id;
            }
        }
        $DTRenderArray = $this->feedback_set_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'company_name','language_name', 'title', 'powered_by', 'timer', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $title = $dtRow['title'];
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "status") {
                    if ($dtRow['status'] == 1) {
                        $status = '<span class="label label-sm label-info status-active" > Active </span>';
                    } else {
                        $status = '<span class="label label-sm label-danger status-inactive" > In Active </span>';
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['id'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if ($acces_management->allow_add OR $acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_view) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'feedback_set/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'feedback_set/edit/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_add) {
                            $action .= '<li>
                                                <a href="' . $site_url . 'feedback_set/copy/' . base64_encode($dtRow['id']) . '">
                                                <i class="fa fa-copy"></i>&nbsp;Copy
                                                </a>
                                            </li>';
                        }
                        if ($acces_management->allow_delete) {
                            $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\'' . $title . '\',\'' . base64_encode($dtRow['id']) . '\');" href="javascript:void(0)">
                                        <i class="fa fa-trash-o"></i>&nbsp;Delete
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

    public function Question_tableRefresh($Encode_id) {
        $id = base64_decode($Encode_id);
        $dtSearchColumns = array('q.id', 'q.id', 'qt.description', 'qst.description','q.question_type', 'q.question_title');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($dtWhere <> '') {
            $dtWhere .= " AND  fset.feedbackset_id=$id";
        } else {
            $dtWhere .= " WHERE fset.feedbackset_id=$id";
        }
        $search_Tid = ($this->input->get('search_type') ? $this->input->get('search_type') : '');

        if ($search_Tid != "") {
            $dtWhere .= " AND q.feedback_type_id  = " . $search_Tid;
        }
        $search_Stid = ($this->input->get('search_subtype') ? $this->input->get('search_subtype') : '');
        if ($search_Stid != "") {
            $dtWhere .= " AND q.feedback_subtype_id  = " . $search_Stid;
        }
        $search_status = ($this->input->get('search_status') ? $this->input->get('search_status') : '');
        if ($search_status != "") {
            if ($search_status == 2) {
                    $dtWhere .= " AND q.id IN(select question_id from feedback_questions_inactive where feedbackset_id=$id)";
            } else {
                    $dtWhere .= " AND q.id NOT IN(select question_id from feedback_questions_inactive where feedbackset_id=$id )";
            }
        }
        $question_type = $this->input->get('question_type');
        if ($question_type != "") {
            $dtWhere .= " AND q.question_type=".$question_type;
        }
	$language_id = $this->input->get('language_id');
        if ($language_id != "") {
            $dtWhere .= " AND q.language_id  = " . $language_id;
        }	
        $AddEdit = ($this->input->get('AddEdit') ? $this->input->get('AddEdit') : 'E');
        $DtQuestionArray = $this->feedback_set_model->getQuestionLoadData($dtWhere, $dtOrder, $dtLimit, $id);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DtQuestionArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DtQuestionArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'type', 'subtype','question_type', 'question_title', 'status');
        foreach ($DtQuestionArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "status") {
                    $row[] = '<input type="checkbox" class="make-switch" name="status_switch[]" value="' . $dtRow['id'] . '" data-size="small" '
                            . 'data-off-color="success" data-off-text="Active" data-on-color="danger" data-on-text="In-Active"'
                            . ($AddEdit == "V" ? "disabled " : "enabled ") . '   ' . ($dtRow['inactive'] != "" ? "checked" : "") . '>';
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline ">
                                <input type="checkbox" class="checkboxes leftchk" name="id[]" value="' . $dtRow['id'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function export_question($Encode_id) {
        $id = base64_decode($Encode_id);
        $dtWhere = " WHERE fset.feedbackset_id=$id AND q.status=1";
        if ($this->mw_session['company_id'] != "") {
            $dtWhere .= " AND q.company_id  = " . $this->mw_session['company_id'];
        }
        $topic_id = $this->input->post('search_type');
        if ($topic_id != "") {
            $dtWhere .= " AND q.feedback_type_id  = " . $topic_id;
        }
        $subtopic_id = $this->input->post('search_subtype');
        if ($subtopic_id != "") {
            $dtWhere .= " AND q.feedback_subtype_id  = " . $subtopic_id;
        }
        $search_status = ($this->input->post('search_status') ? $this->input->post('search_status') : '');
        if ($search_status != "") {
            if ($search_status == 2) {
                $dtWhere .= " AND q.id IN(select question_id from feedback_questions_inactive where feedbackset_id=$id)";
            } else {
                $dtWhere .= " AND q.id NOT IN(select question_id from feedback_questions_inactive where feedbackset_id=$id )";
            }
        }
		$question_type = $this->input->post('question_type');
        if ($question_type != "") {
            $dtWhere .= " AND q.question_type=".$question_type;
        }
//        $question_id = $this->input->post('id', TRUE);
//        if ($question_id != "") {
//            $id_list = implode(',', $question_id);
//            if ($dtWhere <> '') {
//                $dtWhere .= " AND a.id IN(" . $id_list . ")";
//            } else {
//                $dtWhere .= " Where a.id IN(" . $id_list . ")";
//            }
//        }
        $DTQuestSet = $this->feedback_set_model->Export_questions($dtWhere, $id);
        $questionset_row = $this->common_model->get_value('feedback', 'title', 'id=' . $id);
        $this->load->library('PHPExcel_CI');
        $objPHPExcel = new PHPExcel_CI();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'FEEDBACK SET : ' . $questionset_row->title)
                ->setCellValue('A2', 'Question ID')
                ->setCellValue('B2', 'Type')
                ->setCellValue('C2', 'Subtype')
				->setCellValue('D2', 'Question type')
                ->setCellValue('E2', 'Question')
                ->setCellValue('F2', 'Option A')
                ->setCellValue('G2', 'Weightage')
                ->setCellValue('H2', 'Option B')
                ->setCellValue('I2', 'Weightage')
                ->setCellValue('J2', 'Option C')
                ->setCellValue('K2', 'Weightage')
                ->setCellValue('L2', 'Option D')
                ->setCellValue('M2', 'Weightage')
                ->setCellValue('N2', 'Option E')
                ->setCellValue('O2', 'Weightage')
                ->setCellValue('P2', 'Option F')
                ->setCellValue('Q2', 'Weightage');


        $styleArray = array(
            'font' => array(
                'bold' => true
        ));

        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
        ));
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A2:Q2')->applyFromArray($styleArray_header);

        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $i = 2;
        foreach ($DTQuestSet as $Question) {
            $i++;
            $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $Question->id)
                    ->setCellValue("B$i", $Question->feedback_type)
                    ->setCellValue("C$i", $Question->feedback_subtype)
					->setCellValue("D$i", $Question->question_type)
                    ->setCellValue("E$i", $Question->question_title)
                    ->setCellValue("F$i", $Question->option_a)
                    ->setCellValue("G$i", $Question->weight_a)
                    ->setCellValue("H$i", $Question->option_b)
                    ->setCellValue("I$i", ($Question->weight_b > 0 ? $Question->weight_b : ''))
                    ->setCellValue("J$i", $Question->option_c)
                    ->setCellValue("K$i", ($Question->weight_c > 0 ? $Question->weight_c : ''))
                    ->setCellValue("L$i", $Question->option_d)
                    ->setCellValue("M$i", ($Question->weight_d > 0 ? $Question->weight_d : ''))
                    ->setCellValue("N$i", $Question->option_e)
                    ->setCellValue("O$i", ($Question->weight_e > 0 ? $Question->weight_e : ''))
                    ->setCellValue("P$i", $Question->option_f)
                    ->setCellValue("Q$i", ($Question->weight_f > 0 ? $Question->weight_f : ''));
            $objPHPExcel->getActiveSheet()->getStyle("A$i:Q$i")->applyFromArray($styleArray_body);
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Export_feedbackset.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }

    public function submit($Copy_id = "") {
        if ($Copy_id != "") {
            $Copy_id = base64_decode($Copy_id);
        }
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to Add,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $New_ftype_idArray = $this->input->post('New_ftype_id');
            $this->load->library('form_validation');
            //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            $this->form_validation->set_rules('New_ftype_id[]', 'New Feedback Type', 'required');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('feedback_name', 'Feedback name', 'required');
            $this->form_validation->set_rules('powered_by', 'Powered By', 'required');
            $this->form_validation->set_rules('language_id', 'language', 'trim|required');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');

            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                if ($New_ftype_idArray != '') {
                    $ArrayCountArray = array_count_values($New_ftype_idArray);
                    foreach ($ArrayCountArray as $key => $value) {
                        if ($value > 1) {
                            $SuccessFlag = 0;
                            $TypeData = $this->common_model->get_value("feedback_type", "description", "id=" . $key);
                            $Message .= "Same  '" . $TypeData->description . "' Feedback Type are selected";
                        }
                    }
                } else {
                    $Message = "Please add Feedback Type/Sub-Type.!";
                    $SuccessFlag = 0;
                }
                if ($SuccessFlag) {
                    if (count((array)$New_ftype_idArray) > 0) {
                        $subtype_idArray = $this->input->post('New_subtype_id');
                        foreach ($New_ftype_idArray as $key => $Type) {
                            $TotalSubTopic = $this->input->post('TotalSubTopic')[$key];
                            $SubTopicArray = $this->input->post('New_subtype_id' . $TotalSubTopic);
                            if (count((array)$SubTopicArray) == 0) {
                                $SubTopicData = $this->common_model->get_value("feedback_subtype", "count(id) as counter", "feedbacktype_id=" . $Type);
                                if ($SubTopicData->counter > 0) {
                                    $TopicData = $this->common_model->get_value("feedback_type", "description", "id=" . $Type);
                                    $Message .= "Please select sub-Type of '" . $TopicData->description . "' Type.<br/>";
                                    $SuccessFlag = 0;
                                }
                            }
                            $QuestionSet = $this->common_model->get_value("feedback_questions", "count(id) as counter", "feedback_type_id=" . $Type);
                            if ($QuestionSet->counter == 0) {
                                $TopicData = $this->common_model->get_value("feedback_type", "description", "id=" . $Type);
                                $Message .= "Selectd Type '" . $TopicData->description . "' has no any question are Mapped.<br/>";
                                $SuccessFlag = 0;
                            }
                        }
                    }
                }
                if ($SuccessFlag) {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'company_id' => $Company_id,
                        'title' => $this->input->post('feedback_name'),
                        'language_id' => $this->input->post('language_id'),
                        'powered_by' => $this->input->post('powered_by'),
                        'timer' => $this->input->post('timer'),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                    $insert_id = $this->common_model->insert('feedback', $data);
                    if ($insert_id != "") {
                        foreach ($New_ftype_idArray as $key => $type) {
                            //$Topic_id = $TopicData->topic_id;
                            $TotalSubTopic = $this->input->post('TotalSubTopic')[$key];
                            $SubTopicArray = $this->input->post('New_subtype_id' . $TotalSubTopic);
                            foreach ($SubTopicArray as $subtopic_id) {
                                $subtopicdata = array(
                                    'feedbackset_id' => $insert_id,
                                    'feedback_type_id' => $type,
                                    'feedback_subtype_id' => $subtopic_id
                                );
                                $this->common_model->insert('feedbackset_type', $subtopicdata);
                            }
                        }
                        if ($Copy_id != "") {
                            $this->feedback_set_model->CopyInactiveQuestions($insert_id, $Copy_id);
                        }
                        $Message = "Feedbackset created Successfully.";
                        $Rdata['id'] = base64_encode($insert_id);
                    } else {
                        $Message = "Error while creating Feedbackset,Contact administrator for technical support.!";
                        $SuccessFlag = 0;
                    }
//                $this->session->set_flashdata('flash_message', "Feedback Set Added Successfully.");
//                redirect('feedback_set');
//                $SuccessFlag = 1;
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function update($Encode_id) {
        $SuccessFlag = 1;
        $ErrorFlag = '';
        $Message = '';
        $id = base64_decode($Encode_id);
        $acces_management = $this->acces_management;
        $UsedWorkshopList = $this->feedback_set_model->CheckWorkshopQnsSet($id);
        $LockFlag = false;
        if (count((array)$UsedWorkshopList) > 0) {
            $LockFlag = true;
        }
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {

            $New_ftype_idArray = $this->input->post('New_ftype_id');
            $this->load->library('form_validation');
            $data['username'] = $this->mw_session['username'];
            //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            $this->form_validation->set_rules('feedback_name', 'Feedback name', 'required');
            $this->form_validation->set_rules('powered_by', 'Powered By', 'required');
            if (!$LockFlag) {
                $this->form_validation->set_rules('New_ftype_id[]', 'Feedback Type', 'required');
                $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
                $this->form_validation->set_rules('language_id', 'language', 'required');
                $ArrayCountArray = array_count_values($New_ftype_idArray);
            } else {
                $ArrayCountArray = array();
            }
            //$this->form_validation->set_rules('no_of_question', 'No of Questions', 'trim|required|max_length[50]');

            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                if (!$LockFlag) {
                    foreach ($ArrayCountArray as $key => $value) {
                        if ($value > 1) {
                            $SuccessFlag = 0;
                            $TypeData = $this->common_model->get_value("feedback_type", "description", "id=" . $key);
                            $Message .= "Same  '" . $TypeData->description . "' Feedback Type are selected";
                        }
                    }
                    if (count((array)$New_ftype_idArray) > 0) {
                        $subtype_idArray = $this->input->post('New_subtype_id');
                        foreach ($New_ftype_idArray as $key => $Type) {
                            $TotalSubTopic = $this->input->post('TotalSubTopic')[$key];
                            $SubTopicArray = $this->input->post('New_subtype_id' . $TotalSubTopic);
                            if (count((array)$SubTopicArray) == 0) {
                                $SubTopicData = $this->common_model->get_value("feedback_subtype", "count((array)id) as counter", "feedbacktype_id=" . $Type);
                                if ($SubTopicData->counter > 0) {
                                    $TopicData = $this->common_model->get_value("feedback_type", "description", "id=" . $Type);
                                    $Message .= "Please select sub-Type of '" . $TopicData->description . "' Type.<br/>";
                                    $SuccessFlag = 0;
                                }
                            }
                            $QuestionSet = $this->common_model->get_value("feedback_questions", "count(id) as counter", "feedback_type_id=" . $Type);
                            if ($QuestionSet->counter == 0) {
                                $TopicData = $this->common_model->get_value("feedback_type", "description", "id=" . $Type);
                                $Message .= "Selectd Type '" . $TopicData->description . "' has no any question are Mapped.<br/>";
                                $SuccessFlag = 0;
                            }
                        }
                    }
                }
                if ($SuccessFlag) {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        //'company_id' => $this->input->post('company_id'),
                        'title' => $this->input->post('feedback_name'),
                        //'short_description' => $this->input->post('short_description'),
                        'powered_by' => $this->input->post('powered_by'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                    );
                    if (!$LockFlag) {
                        $data['timer'] = $this->input->post('timer');
                        $data['status'] = $this->input->post('status');
                        $data['language_id'] = $this->input->post('language_id');
                    }
                    $this->common_model->update('feedback', 'id', $id, $data);
                    $Message = "Feedbackset updated Successfully.";
                    if (!$LockFlag) {
                        $this->common_model->delete('feedbackset_type', 'feedbackset_id', $id);
                        $this->common_model->delete('workshop_feedback_questions', 'feedbackset_id', $id);
                        foreach ($New_ftype_idArray as $key => $type) {
                            //$Topic_id = $TopicData->topic_id;
                            $TotalSubTopic = $this->input->post('TotalSubTopic')[$key];
                            $SubTopicArray = $this->input->post('New_subtype_id' . $TotalSubTopic);
                            foreach ($SubTopicArray as $subtopic_id) {
                                $subtopicdata = array(
                                    'feedbackset_id' => $id,
                                    'feedback_type_id' => $type,
                                    'feedback_subtype_id' => $subtopic_id
                                );
                                $this->common_model->insert('feedbackset_type', $subtopicdata);
                                //$this->feedback_set_model->copyQusWorkshop($id,$subtopicdata);
                            }
                        }
                        $Message = "Feedbackset updated Successfully.";
                    }
//            $this->session->set_flashdata('flash_message', "Feedback Set updated successfully");
//            redirect('feedback_set');
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function remove() {
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $deleted_id = $this->input->Post('deleteid');
            $DeleteFlag = $this->feedback_set_model->CrosstableValidation(base64_decode($deleted_id));
            if ($DeleteFlag) {
                $this->feedback_set_model->remove(base64_decode($deleted_id));
                $this->common_model->delete('feedbackset_type', 'feedbackset_id', base64_decode($deleted_id));
                $this->common_model->delete('workshop_feedback_questions', 'feedbackset_id', base64_decode($deleted_id));
                $message = "Feedback Set deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Feedback Set cannot be deleted. Reference of Feedback Set found in other module!<br/>";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function record_actions($Action) {
        $action_id = $this->input->Post('id');
        if (count((array)$action_id) == 0) {
            echo json_encode(array('message' => "Please select record from the list", 'alert_type' => 'error'));
            exit;
        }
        $now = date('Y-m-d H:i:s');
        $alert_type = 'success';
        $message = '';
        $title = '';
        if ($Action == 1) {
            foreach ($action_id as $id) {
                $data = array(
                    'status' => 1,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']);
                $this->common_model->update('feedback', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->feedback_set_model->CrosstableValidation($id);
                //$StatusFlag = true;
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('feedback', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. Feedback Set(s) assigned to Workshop!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->feedback_set_model->CrosstableValidation($id);
                //$DeleteFlag=true;
                if ($DeleteFlag) {
                    $this->common_model->delete('feedback', 'id', $id);
                    $this->common_model->delete('feedbackset_type', 'feedbackset_id', $id);
                    $this->common_model->delete('workshop_feedback_questions', 'feedbackset_id', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Feedback Set cannot be deleted. Feedback Set(s) assigned to Workshop!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Feedback Set(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function Check_feedback() {
        $feedback = $this->input->post('feedback', true);
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id', TRUE);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        $feedback_id = $this->input->post('feedback_id', true);
        if ($cmp_id != '') {
            echo $this->feedback_set_model->check_feedback($feedback, $cmp_id, base64_decode($feedback_id));
        }
    }

    public function ajax_company_feedbackType() {

        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['result'] = $this->common_model->get_selected_values('feedback_type', 'id,description', 'company_id=' . $company_id);
        echo json_encode($data);
    }

    public function ajax_type_subtype() {
        $feedback_type_id = $this->input->post('data', TRUE);
        $data['result'] = $this->common_model->getFeedbackSubTopic($feedback_type_id);
        echo json_encode($data);
    }

    public function gettype($tr_no) {
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('cmp_id', true);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        $ftypedata = $this->common_model->get_selected_values('feedback_type', 'id,description', 'company_id=' . $cmp_id);

        $htdata = '<tr id="Row-' . $tr_no . '">';
        $htdata .= '<input type="hidden" value="' . $tr_no . '" name="TotalSubTopic[]">';
        $htdata .= '<td><select id="ftype_id' . $tr_no . '" name="New_ftype_id[]" class="form-control input-sm select2 ValueUnq" onchange="getTypewiseSubtype(' . $tr_no . ');" style="width:100%">';
        $htdata .= '<option value="">please select</option>';
        foreach ($ftypedata as $type) {
            $htdata .= '<option value="' . $type->id . '">' . $type->description . '</option>';
        }
        $htdata .= '</select></td>';
        $htdata .= '<td><select id="subtype' . $tr_no . '" name="New_subtype_id' . $tr_no . '[]" class="form-control input-sm select2" style="width:100%" multiple="" selected></select></td>';
        $htdata .= '<td><button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="RowDelete(' . $tr_no . ')";><i class="fa fa-times"></i></button> </td>';
        $htdata .='<script>$("#subtype' . $tr_no . '" ).rules( "add", {
    required: true
});</script></tr>';
        $data['htmlData'] = $htdata;
        echo json_encode($data);
    }

    public function StatusUpdate($Encode_id) {
        $SuccessFlag = 1;
        $Qset_id = base64_decode($Encode_id);
        $Qstatus = $this->input->post('Qstatus');
        $Qid = $this->input->Post('Q_id');
        $now = date('Y-m-d H:i:s');
        $message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $message = 'You have no rights to Edit,Contact Administrator for details.';
            $SuccessFlag = 0;
        } else {
            if ($Qstatus == 'true') {
                $data = array(
                    'feedbackset_id' => $Qset_id,
                    'question_id' => $Qid,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']);
                $this->common_model->insert('feedback_questions_inactive', $data);
                $this->feedback_set_model->DeleteInactiveQusWorkshop($Qset_id, $Qid);
                $message = "Feedback Question In-Active successfully.";
            } else {
                $this->common_model->delete_whereclause('feedback_questions_inactive', 'question_id=' . $Qid . ' and feedbackset_id=' . $Qset_id);
                $this->feedback_set_model->ActiveQusWorkshop($Qset_id, $Qid);
                $message = "Feedback Question Active successfully.";
            }
        }
        $Rdata['Msg'] = $message;
        $Rdata['success'] = $SuccessFlag;
        echo json_encode($Rdata);
    }

    public function QuestionTable_actions($Encode_id, $Action) {
        $action_id = $this->input->Post('id');
        $Qset_id = base64_decode($Encode_id);
        $now = date('Y-m-d H:i:s');
        $alert_type = 'success';
        $message = '';
        $title = '';
        if ($Action == 2) {
            foreach ($action_id as $Qid) {
                $data = array(
                    'feedbackset_id' => $Qset_id,
                    'question_id' => $Qid,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']);
                $this->common_model->insert('feedback_questions_inactive', $data);
            }
            $message = 'Status changed to In-Active successfully.';
        } else if ($Action == 1) {
            $SuccessFlag = false;
            foreach ($action_id as $Qid) {
                $StatusFlag = true;
                if ($StatusFlag) {
                    $this->common_model->delete_whereclause('feedback_questions_inactive', 'question_id=' . $Qid . ' and feedbackset_id=' . $Qset_id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. Feedback(s) assigned to Workshop!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to Active sucessfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

}
