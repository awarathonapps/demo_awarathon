<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Information_form extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('information_form');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('information_form_model');
    }

    public function ajax_feedback_company() {
        return $this->common_model->fetch_company_data($this->input->get());
    }

    public function index() {
        $data['module_id'] = '1.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmpdata'] = $this->common_model->fetch_object_by_field('company', 'status', '1');
        } else {
            $data['cmpdata'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('information_form/index', $data);
    }

    public function create() {
        $data['module_id'] = '1.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanySet'] = $this->common_model->fetch_object_by_field('company', 'status', '1');
        } else {
            $data['CompanySet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('information_form/create', $data);
    }
    public function edit($id,$Errors = '',$step=1) {
        //$step = 2;
        $alert_type = 'success';
        $message = '';
        $F_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('information_form');
            return;
        }
        $data['customr_errors'] = $Errors;
        
        $data['module_id'] = '1.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanySet'] = $this->common_model->fetch_object_by_field('company', 'status', '1');
        } else {
            $data['CompanySet'] = array();
        }
        $data['step'] = $step;
        $data['Company_id'] = $Company_id;
        $data['SelectType'] = $this->common_model->fetch_object_by_field('field_type', 'feedback_visible', '1');
        $data['HeadResult'] = $this->common_model->fetch_object_by_id('feedback_form_header', 'id', $F_id);
        $data['Result'] = $this->information_form_model->getFormDetails($data['HeadResult']->id);
        $DataSet = $this->common_model->get_value('feedback_form_data', 'id', 'form_header_id =' . $F_id);
        $LockFlag=false;
        if(count((array)$DataSet)>0){
            $LockFlag =true;
        }
        $data['LockFlag'] =$LockFlag;
        $this->load->view('information_form/edit', $data);
    }
    public function copy($id) {
        $F_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('information_form');
            return;
        }
        
        $data['module_id'] = '1.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanySet'] = $this->common_model->fetch_object_by_field('company', 'status', '1');
        } else {
            $data['CompanySet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['SelectType'] = $this->common_model->fetch_object_by_field('field_type', 'feedback_visible', '1');
        $data['HeadResult'] = $this->common_model->fetch_object_by_id('feedback_form_header', 'id', $F_id);
        $data['Result'] = $this->information_form_model->getFormDetails($data['HeadResult']->id);
        $this->load->view('information_form/copy', $data);
    }
    public function view($id,$step=1) {
        //$step = 2;
        $alert_type = 'success';
        $message = '';
        $F_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('information_form');
            return;
        }
        $data['module_id'] = '1.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanySet'] = $this->common_model->fetch_object_by_field('company', 'status', '1');
        } else {
            $data['CompanySet'] = array();
        }
        $data['step'] = $step;
        $data['Company_id'] = $Company_id;
        $data['SelectType'] = $this->common_model->fetch_object_by_field('field_type', 'feedback_visible', '1');
        $data['HeadResult'] = $this->common_model->fetch_object_by_id('feedback_form_header', 'id', $F_id);
        $data['Result'] = $this->information_form_model->getFormDetails($data['HeadResult']->id);
        $this->load->view('information_form/view', $data);
    }

    public function DatatableRefresh() {

        $dtSearchColumns = array('a.id', 'a.id', 'b.company_name', 'a.form_name', 'a.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('filter_cmp') ? $this->input->get('filter_cmp') : '');
            if ($cmp_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND a.company_id  = " . $cmp_id;
                } else {
                    $dtWhere .= " WHERE a.company_id  = " . $cmp_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE a.company_id  = " . $this->mw_session['company_id'];
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
        $DTRenderArray = $this->information_form_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'company_name', 'form_name', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $title = $dtRow['form_name'];
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
                    if ($acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_view) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'information_form/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'information_form/edit/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_add) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'information_form/copy/' . base64_encode($dtRow['id']) . '">
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

    public function submit($Copy_id = "") {
        $isRequired = 0;
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
            
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('form_name', 'Form Name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('New_disp_name[]', 'Display Name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('New_fieldtype_id[]', 'Field Type', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('New_order[]', 'Order', 'trim|required|max_length[255]');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $DisplayArray = $this->input->post('New_disp_name');
                $ArrayCountArray = array_count_values($DisplayArray);
                foreach ($ArrayCountArray as $key => $value) {
                    if ($value > 1) {
                        $SuccessFlag = 0;
                        $Message .= "Duplicate Display Name..";
                    }
                }
                $OrderArray = $this->input->post('New_order');
                $ArrayCount = array_count_values($OrderArray);
                foreach ($ArrayCount as $key => $value) {
                    if ($value > 1) {
                        $SuccessFlag = 0;
                        $Message .= "Same Order Value..";
                    }
                }
                if ($SuccessFlag) {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'company_id' => $Company_id,
                        'form_name' => ucfirst(strtolower($this->input->post('form_name'))),
                        'short_description' => $this->input->post('short_description'),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                    $insert_id = $this->common_model->insert('feedback_form_header', $data);
                    if ($insert_id != "") {
                        foreach ($DisplayArray as $key => $Caption) {
                            $fieldType = $this->input->post('New_fieldtype_id')[$key];
                            $text_data = "";
                            if ($fieldType == "dropdown") {
                                $text_data = $this->input->post('New_data_area')[$key];
                            }
                            $isRequired = $this->input->post('New_required_id')[$key];
                            $order = $this->input->post('New_order')[$key];
                            $NewFieldData = array(
                                'header_id' => $insert_id,
                                'field_name' => strtolower(str_replace(' ', '_', $Caption)),
                                'field_display_name' => $Caption,
                                'field_type' => $fieldType,
                                'default_value' => $text_data,
                                'is_required' => $isRequired,
                                'field_order' => $order,
                                'status' => $this->input->post('New_field_status')[$key],
                                'addeddate' => $now,
                                'addedby' => $this->mw_session['user_id'],
                            );
                            $this->common_model->insert('feedback_form_details', $NewFieldData);
                        }
                        $Message = "Information Form created successfully.";
                    } else {
                        $Message = "Error while creating Information Form,Contact Mediaworks for technical support.!";
                        $SuccessFlag = 0;
                    }
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function update($Encode_id) {
        $id = base64_decode($Encode_id);
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                //$this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('form_name', 'Form Name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('New_disp_name[]', 'Display Name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('New_fieldtype_id[]', 'Field Type', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('New_order[]', 'Order', 'trim|required|max_length[255]');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $DisplayArray = $this->input->post('New_disp_name');
                $ArrayCountArray = array_count_values($DisplayArray);
                foreach ($ArrayCountArray as $key => $value) {
                    if ($value > 1) {
                        $SuccessFlag = 0;
                        $Message .= "Duplicate Display Name..";
                    }
                }
                $OrderArray = $this->input->post('New_order');
                $ArrayCount = array_count_values($OrderArray);
                foreach ($ArrayCount as $key => $value) {
                    if ($value > 1) {
                        $SuccessFlag = 0;
                        $Message .= "Same Order Value..";
                    }
                }
                if ($SuccessFlag) {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'form_name' => ucfirst(strtolower($this->input->post('form_name'))),
                        'short_description' => $this->input->post('short_description'),
                        'status' => $this->input->post('status'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                    );
                    $this->common_model->update('feedback_form_header', 'id', $id, $data);
                    //$this->common_model->delete('feedback_form_details', 'header_id', $id);
                    foreach ($DisplayArray as $key => $Caption) {
                            $fieldType = $this->input->post('New_fieldtype_id')[$key];
                            $text_data = "";
                            if ($fieldType == "dropdown") {
                                $text_data = $this->input->post('New_data_area')[$key];
                            }
                            $isRequired = $this->input->post('New_required_id')[$key];
                            $order = $this->input->post('New_order')[$key];
                            if(isset($this->input->get('detail_id')[$key])){
                                 $NewFieldData = array(
                                    'field_name' => strtolower(str_replace(' ', '_', $Caption)),
                                    'field_display_name' => $Caption,
                                    'field_type' => $fieldType,
                                    'default_value' => $text_data,
                                    'is_required' => $isRequired,
                                    'field_order' => $order,
                                    'status' => $this->input->post('New_field_status')[$key],
                                    'modifieddate' => $now,
                                    'modifiedby' => $this->mw_session['user_id'],
                                );
                                $this->common_model->update('feedback_form_details','id',$this->input->get('detail_id')[$key], $NewFieldData);
                            }else{
                                $NewFieldData = array(
                                    'header_id' => $id,
                                    'field_name' => strtolower(str_replace(' ', '_', $Caption)),
                                    'field_display_name' => $Caption,
                                    'field_type' => $fieldType,
                                    'default_value' => $text_data,
                                    'is_required' => $isRequired,
                                    'field_order' => $order,
                                    'status' => $this->input->post('New_field_status')[$key],
                                    'addeddate' => $now,
                                    'addedby' => $this->mw_session['user_id'],
                                    'modifieddate' => $now,
                                    'modifiedby' => $this->mw_session['user_id'],
                                );
                                $this->common_model->insert('feedback_form_details', $NewFieldData);
                            }
                            
                    }
                    $Message ="Information Form data updated successfully.";
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function remove() {
        $deleted_id = base64_decode($this->input->Post('deleteid'));
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {

            $DeleteFlag = $this->information_form_model->CrosstableValidation($deleted_id);
            if ($DeleteFlag) {
                $this->common_model->delete('feedback_form_header', 'id', $deleted_id);
                $this->common_model->delete('feedback_form_details', 'header_id', $deleted_id);
                $message = "Information Form deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Information Form cannot be deleted. Reference of Information form found in other module!<br/>";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function record_actions($Action) {
        $action_id = $this->input->Post('id');
        if(count((array)$action_id)==0){
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
                $this->common_model->update('feedback_form_header', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->information_form_model->CrosstableValidation($id);
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('feedback_form_header', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. Information Form assigned to Other Module.....!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->information_form_model->CrosstableValidation($id);
                if ($DeleteFlag) {
                    $this->common_model->delete('feedback_form_header', 'id', $id);
                    $this->common_model->delete('feedback_form_details', 'header_id', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Information Form cannot be deleted. Information Form assigned to Other Module.... !<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Information Form(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function Check_form() {
        $form_name = $this->input->post('form_name', true);
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id', true);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        $form_id = $this->input->post('form_id', true);
        if ($form_id != "") {
            $form_id = base64_decode($form_id);
        }
        echo $this->information_form_model->check_form($form_name, $cmp_id, $form_id);
    }

    public function Check_fieldDuplicate() {
        $form_name = $this->input->post('form_name', true);
        $field_name = $this->input->post('field_name', true);
        $form_id = $this->input->post('form_id', true);
        echo $this->information_form_model->check_fieldDuplication($form_name, $field_name, $form_id);
    }

    public function getfield($fld_no) {
        $field_data = $this->common_model->fetch_object_by_field('field_type', 'feedback_visible', 1);
        $htdata = '<tr id="Row-' . $fld_no . '">';
        //$htdata .= '<td><input type="text" name="New_field_name[]" id="field_name' . $fld_no . '" class="form-control input-sm" maxlength="255" style="width:100%"></td>';        
        $htdata .= '<td><input type="text" name="New_disp_name[]" id="disp_name' . $fld_no . '" class="form-control input-sm" maxlength="255" style="width:100%"></td>';
        $htdata .= '<td><select id="field_type' . $fld_no . '" name="New_fieldtype_id[]" class="form-control input-sm select2" style="width:100%" onchange="addDATA(' . $fld_no . ')">';
        $htdata .= '<option value="">please select</option>';
        foreach ($field_data as $ft) {
            $htdata .= '<option value="' . $ft->name . '">' . $ft->title . '</option>';
        }
        $htdata .= '</select></td>';
        $htdata .= '<td><textarea rows="3" class="form-control input-sm" id="data_area' . $fld_no . '" maxlength="255" name="New_data_area[]" readonly></textarea></td>';
        $htdata .= '<td><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="required_id[]" value="1" id="chk' . $fld_no . '" onclick="setCheckBoxValue(' . $fld_no . ');" />
                                <span></span>
                    </label>
                    <input type="hidden" name="New_required_id[]" id="required_id' . $fld_no . '" value="0">
                    </td>';
        $htdata .= '<td><input type="number" name="New_order[]" id="order' . $fld_no . '" value="'.$fld_no.'" class="form-control input-sm" max="255" min="1" style="width:100%"></td>';
        $htdata .= '<td><select id="field_status' . $fld_no . '" name="New_field_status[]" class="form-control input-sm select2" style="width:100%">';
        $htdata .= '<option value="1" selected>Active</option><option value="0">In-Active</option>';
        $htdata .= '</select></td>';
        $htdata .= '<td><button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="RowDelete(' . $fld_no . ')";><i class="fa fa-times"></i></button> </td></tr>';
        $data['htmlData'] = $htdata;
        echo json_encode($data);
    }
    public function InfoDatatableRefresh($Encode_id) {
        
        $fheader_id = base64_decode($Encode_id);
        $dtSearchColumns = array('w.workshop_name','du.firstname','du.lastname');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        
        if ($dtWhere <> '') {
            $dtWhere .= " AND fd.form_header_id = " . $fheader_id;
        } else {
            $dtWhere .= " WHERE fd.form_header_id  = " . $fheader_id;
        }
        
        $DTRenderArray = $this->information_form_model->LoadInfoDataTable($dtWhere, $dtOrder, $dtLimit);
        
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('workshop_name','username');                
        
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $FieldResult = $this->information_form_model->getInfoFormData($fheader_id,$dtRow['user_id'],$dtRow['workshop_id']);
            if(count((array)$FieldResult)>0){
                foreach ($FieldResult as $key => $value) {
                    if($value->field_type=='date'){
                        $row[] =date('d-m-Y',  strtotime($value->field_value));
                    }else{
                        $row[] =$value->field_value;
                    }
                    
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
}
