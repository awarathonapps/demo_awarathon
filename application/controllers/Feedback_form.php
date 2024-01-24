<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Feedback_form extends CI_Controller {

    public function __construct() {

        parent::__construct();
        if ($this->session->userdata('awarathon_session') == FALSE) {
            redirect('index');
        } else {
            $this->mw_session = $this->session->userdata('awarathon_session');
            $acces_management = CheckRights($this->mw_session['user_id'], 'feedback_form');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('feedback_form_model');
            $this->load->model('common_model');
            $this->load->library('form_validation');
            $this->load->library('upload');
        }
    }

    public function ajax_feedback_company() {
        return $this->common_model->fetch_company_data($_GET);
    }
    public function index() {
        $data['module_id'] = '1.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        //$data['rows'] = $this->feedback_form_model->fetch_access_data();
        $this->load->view('feedback_form/index', $data);
    }
    public function create() {
        $data['module_id'] = '1.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('feedback_form');
            return;
        }
        $data['company'] = $this->feedback_form_model->get_company();        
        $this->load->view('feedback_form/create', $data);
    }
    public function edit($id,$Errors='') {        
        $alert_type = 'success';
        $message = '';
        $F_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('feedback_form');
            return;
        } else {
            //$data['company'] = $this->questionset_model->find_by_id($F_id);
            //echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>$data['company']));
        }
        $data['customr_errors'] =$Errors;
        $this->load->helper('form');
        $data['module_id'] = '1.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['SelectCompany'] = $this->common_model->fetch_object_by_field('company', 'status', '1');
        $data['SelectType'] = $this->common_model->fetch_object_by_field('field_type', 'feedback_visible', '1');
        $data['HeadResult'] = $this->common_model->fetch_object_by_id('feedback_form_header', 'id', $F_id);
        $data['Result'] = $this->common_model->fetch_object_by_field('feedback_form_details', 'header_id', $data['HeadResult']->id);                
        $FieldArray = array();
        $this->load->view('feedback_form/edit', $data);
    }
    public function view($id) {
        $alert_type = 'success';
        $message = '';
        $F_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('feedback_form');
            return;
        } else {
            //$data['company'] = $this->questionset_model->find_by_id($F_id);
            //echo json_encode(array('message' => $message,'alert_type'=>$alert_type,'result'=>$data['company']));
        }
        //$data['customr_errors'] =$Errors;
        $this->load->helper('form');
        $data['module_id'] = '1.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['SelectCompany'] = $this->common_model->fetch_object_by_field('company', 'status', '1');
        $data['SelectType'] = $this->common_model->fetch_object_by_field('field_type', 'feedback_visible', '1');
        $data['HeadResult'] = $this->common_model->fetch_object_by_id('feedback_form_header', 'id', $F_id);
        $data['Result'] = $this->common_model->fetch_object_by_field('feedback_form_details', 'header_id', $data['HeadResult']->id);                
        $FieldArray = array();
        $this->load->view('feedback_form/view', $data);
    }

    public function DatatableRefresh() {

        $dtSearchColumns = array('a.id', 'b.company_name', 'a.form_name','a.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $DTRenderArray = $this->feedback_form_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'company_name', 'form_name','status', 'Actions');
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
                                        <a href="' . $site_url . 'feedback_form/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'feedback_form/edit/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
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

    public function submit() {
        $SuccessFlag = 0;
        $Message = '';    
        $isRequired=0;
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            redirect('feedback_form');
            return;
        }
        $New_field_nameArray = $this->input->post('New_field_name');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['username'] = $this->mw_session['username'];
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            if ($ErrorFlag) {
                
                $this->create();;
            }
        else {
        $now = date('Y-m-d H:i:s');
        $data = array(
            'company_id' => $this->input->post('company_id'),
            'form_name' => ucfirst(strtolower($this->input->post('form_name'))),
            'short_description' => $this->input->post('short_description'),                                   
            'status' => $this->input->post('status'),
            'addeddate' => $now,
            'addedby' => $this->mw_session['user_id'],
        );
        $insert_id = $this->common_model->insert('feedback_form_header', $data);
                    foreach ($New_field_nameArray as $key => $new_field) {
                        $data_area=$this->input->post('New_data_area')[$key];
                                if($data_area==null){
                                    $text_data="";
                                }else{
                                    $text_data=$data_area;
                                }
                        $isSelect=$this->input->post('New_required_id')[$key];
                                if($isSelect==1){
                                    $isRequired=1;
                                }else{
                                    $isRequired=0;
                                }
                            $NewFieldData = array(
                                'header_id' => $insert_id,
                                'field_name' => $this->input->post('New_field_name')[$key],
                                'field_display_name' => $this->input->post('New_disp_name')[$key],
                                'field_type' => $this->input->post('New_fieldtype_id')[$key],
                                'default_value' => $text_data,
                                'is_required' => $isRequired,
                                'status' => $this->input->post('New_field_status')[$key]
                            );
                            $this->common_model->insert('feedback_form_details', $NewFieldData);                                            
                }                                                        
                $Message = "Feedback Form Added Successfully.";
                $this->session->set_flashdata('flash_message', "Feedback Form Added Successfully.");
                redirect('feedback_form');
                $SuccessFlag = 1;
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);    
}
    public function update($Encode_id) { 
        $id = base64_decode($Encode_id);
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            redirect('feedback_form');
            return;
        }        
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['username'] = $this->mw_session['username'];
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');


//        if ($this->form_validation->run() == FALSE) {
//            $this->edit($Encode_id);
//        } else {
                $now = date('Y-m-d H:i:s');                
                $data = array(
                    'form_name' => ucfirst(strtolower($this->input->post('form_name'))),
                    'short_description' => $this->input->post('short_description'),                                                                  
                    'status' => $this->input->post('status'),
                    'addeddate' => $now,
                    'addedby' => $this->mw_session['user_id'],
                );
                $this->common_model->update('feedback_form_header', 'id', $id, $data);
                $data_Field = $this->common_model->fetch_object_by_field('feedback_form_details', 'header_id', $id); 
                
                foreach ($data_Field as $key => $value) {
                    $field_name_postdata = $this->input->post('field_name');                    
                    $disp_name_postdata = $this->input->post('disp_name');
                    $field_type_postdata = $this->input->post('field_type');
                    $data_area_postdata = $this->input->post('data_area');
                    $required_id_postdata = $this->input->post('required_id');
                    $field_old_status_postdata = $this->input->post('field_old_status');                    
                    $FieldRowset = $this->feedback_form_model->getFeedbackField($id, $value->id);
                    foreach ($field_name_postdata as $fkey=>$fvalue){

                            $data_area=$data_area_postdata[$fkey];
                                if($data_area==null){
                                    $text_data="";
                                }else{
                                    $text_data=$data_area;
                                }
                            $isSelect=$required_id_postdata[$fkey];
                                if($isSelect==1){
                                    $isRequired=1;
                                }else{
                                    $isRequired=0;
                                }
                        if (count((array)$FieldRowset) == 0) {
                                $fielddata = array(
                                    'header_id' => $id,
                                    'field_name' => $field_name_postdata[$fkey],
                                    'field_display_name' => $disp_name_postdata[$fkey],
                                    'field_type' => $field_type_postdata[$fkey],
                                    'default_value' => $text_data,
                                    'is_required'=> $isRequired,
                                    'status'=>$field_old_status_postdata[$fkey]
                                );
                                
                                $New_id = $this->common_model->insert('feedback_form_details', $fielddata);                                
                            } 
                        else if (!array_key_exists($value->id,$field_name_postdata)){
                            $this->feedback_form_model->removeField($value->id);
                            }
                            else {
                                $fielddata = array(
                                    'header_id' => $id,
                                    'field_name' => $field_name_postdata[$fkey],
                                    'field_display_name' => $disp_name_postdata[$fkey],
                                    'field_type' => $field_type_postdata[$fkey],
                                    'default_value' => $text_data,
                                    'is_required'=> $isRequired,
                                    'status'=>$field_old_status_postdata[$fkey]
                                );
                                $this->common_model->update('feedback_form_details', 'id', $fkey, $fielddata);                                
                            }                       
                    }
                }
                $NewField_Array = $this->input->post('New_field_name');
                $NewDisp_Array = $this->input->post('New_disp_name');
                $New_fieldtype_Array = $this->input->post('New_fieldtype_id');
                $New_dataArea_Array = $this->input->post('New_data_area');
                $New_field_status_Array = $this->input->post('New_field_status');
                $New_required_id_Array = $this->input->post('New_required_id');
               
                    foreach ($NewField_Array as $key => $fieldval) { 
                            $data_area=$data_area_postdata[$key];
                                if($data_area==null){
                                    $text_data="";
                                }else{
                                    $text_data=$data_area;
                                }
                            $isRequire=$New_required_id_Array[$key];
                                if($isRequire==1){
                                    $isRequire=1;
                                }else{
                                    $isRequired=0;
                                }
                        if ($fieldval != '') {
                            $new_fielddata = array(
                                'header_id' => $id,
                                'field_name' => $NewField_Array[$key],
                                'field_display_name' => $NewDisp_Array[$key],
                                'field_type' => $New_fieldtype_Array[$key],
                                'default_value' => $text_data,
                                'is_required'=> $isRequired,
                                'status'=>$New_field_status_Array[$key]
                            );
                            $this->common_model->insert('feedback_form_details', $new_fielddata);
                        }
                    }
                
                $this->session->set_flashdata('flash_message', "Feedback Form updated successfully");
                redirect('feedback_form');
         
        //}
    }

    public function remove() {
        $deleted_id=base64_decode($this->input->Post('deleteid'));
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {   
            $DeleteFlag=true;
            //$DeleteFlag = $this->questionset_model->CrosstableValidation($deleted_id);
            if ($DeleteFlag) {
                $this->common_model->delete('feedback_form_header','id',$deleted_id);
                $this->common_model->delete('feedback_form_details','header_id',$deleted_id);
                $message = "Feedback Form deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Feedback Form cannot be deleted. Reference of Feedback form found in other module!<br/>";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function record_actions($Action) {
        $action_id = $this->input->Post('id');
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
                //$StatusFlag = $this->questionset_model->CrosstableValidation($id);
                $StatusFlag = true;
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('feedback_form_header', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. Feedback(s) assigned to.....!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                // $DeleteFlag = $this->workshop_model->CheckUserAssignRole($id);
                //$DeleteFlag = $this->questionset_model->CrosstableValidation($id);
                $DeleteFlag = true;
                if ($DeleteFlag) {
                    $this->common_model->delete('feedback_form_header', 'id', $id);
                    $this->common_model->delete('feedback_form_details','header_id',$id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Feedbak Form cannot be deleted. Feedback Form assigned to.... !<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Feedback Form(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function Check_form() {
        $form_name = $this->input->post('form_name', true);
        $cmp_id = $this->input->post('company_id', true);
        $form_id = $this->input->post('form_id', true);
        echo $this->feedback_form_model->check_form($form_name, $cmp_id,$form_id);
    }
    public function Check_fieldDuplicate() {
        $form_name = $this->input->post('form_name', true);
        $field_name = $this->input->post('field_name', true);
        $form_id = $this->input->post('form_id', true);
        echo $this->feedback_form_model->check_fieldDuplication($form_name, $field_name,$form_id);
    }
    public function getfield($fld_no) {            
        $field_data = $this->common_model->fetch_object_by_field('field_type','feedback_visible',1);
        $htdata = '<tr id="Row-' . $fld_no . '">';
        $htdata .= '<td><input type="text" name="New_field_name[]" id="field_name' . $fld_no . '" class="form-control input-sm" maxlength="255" style="width:100%"></td>';        
        $htdata .= '<td><input type="text" name="New_disp_name[]" id="disp_name' . $fld_no . '" class="form-control input-sm" maxlength="255" style="width:100%"></td>';
        $htdata .= '<td><select id="field_type' . $fld_no . '" name="New_fieldtype_id[]" class="form-control input-sm select2" style="width:100%" onchange="addDATA('.$fld_no.')">';
        $htdata .= '<option value="">please select</option>';
        foreach ($field_data as $ft) {
            $htdata .= '<option value="' . $ft->name . '">' . $ft->title . '</option>';
        }
        $htdata .= '</select></td>';
        $htdata .= '<td><textarea rows="3" class="form-control input-sm" id="data_area'.$fld_no.'" maxlength="150" name="New_data_area[]" disabled></textarea></td>';
        $htdata .= '<td><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="New_required_id[]" value="1"/>
                                <span></span>
                    </label></td>';        
        $htdata .= '<td><select id="field_status' . $fld_no . '" name="New_field_status[]" class="form-control input-sm select2" style="width:100%">';
        $htdata .= '<option value="1" selected>Active</option><option value="0">In-Active</option>';         
        $htdata .= '</select></td>';
        $htdata .= '<td><button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="RowDelete(' . $fld_no . ')";><i class="fa fa-times"></i></button> </td></tr>';
        $data['htmlData'] = $htdata;
        echo json_encode($data);
    }

}
