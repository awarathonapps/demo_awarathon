<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Division extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('division');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('division_model');
    }

    public function index()
    {
        $data['module_id'] = '1.17';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompanyData'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('division/index', $data);
    }

    public function submit()
    {
        $alert_type = 'success';
        $url = '';
        $mode = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $url = base_url() . 'division';
        } else {
            $this->load->library('form_validation');
            $data['username'] = $this->mw_session['username'];
            //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('division_name', 'Division Name', 'trim|required|max_length[250]|callback_duplicate_division');
            $this->form_validation->set_rules('status', 'Status', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $alert_type = 'error';
                $message = validation_errors();
            } else {
                if ($this->input->post('edit_id') == '') {
                    $mode = 'add';
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'company_id' => $Company_id,
                        'division_name' => $this->security->xss_clean($this->input->post('division_name')),
                        'status' => $this->security->xss_clean($this->input->post('status')),
                        'addeddate' => $now,
                        'addedby' => $this->security->xss_clean($this->mw_session['user_id']),
                        'deleted' => 0
                    );

                    $this->common_model->insert('division_mst', $data);
                    // $this->session->set_flashdata('flash_message', "Division created successfully.");
                    $message = "Division created successfully.";
                    $url = base_url() . 'division';
                } else {
                    $mode = 'edit';
                    $now = date('Y-m-d H:i:s');
                    $edit_id = base64_decode($this->input->post('edit_id'));
                    $data = array(
                        'company_id' => $Company_id,
                        'division_name' => $this->security->xss_clean($this->input->post('division_name')),
                        'status' => $this->security->xss_clean($this->input->post('status')),
                        'modifieddate' => $now,
                        'modifiedby' => $this->security->xss_clean($this->mw_session['user_id']),
                        'deleted' => 0
                    );

                    $this->common_model->update('division_mst', 'id', $edit_id, $data);
                    // $this->session->set_flashdata('flash_message', "Division updated successfully.");
                    $message = "Division updated successfully.";
                    $url = base_url() . 'division';
                }
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type, 'url' => $url, 'mode' => $mode));
    }

    public function edit()
    {
        $alert_type = 'success';
        $message = '';
        if (count((array) $this->input->post()) > 0) {
            $edit_id = base64_decode($this->input->post('edit_id'));
            $data['acces_management'] = $this->acces_management;

            if (!$data['acces_management']->allow_edit) {
            } else {
                // $data['result'] = $this->division_model->find_by_id($edit_id);
                $this->db->select('a.id,a.division_name,a.status,a.company_id,b.company_name')->from('division_mst a');
                $this->db->join('company b', 'a.company_id=b.id', 'left');
                $this->db->where('a.deleted', 0);
                $this->db->where('a.id', $edit_id);
                $data['result'] = $this->db->get()->result_array();
                echo json_encode(array('message' => $message, 'alert_type' => $alert_type, 'result' => $data['result']));
            }
        } else {
            $alert_type = 'error';
            $message = "Failed to retrive data from server";
            echo json_encode(array('message' => $message, 'alert_type' => $alert_type, 'result' => ''));
        }
    }
    public function duplicate_division($division){
       
        $division_name = $this->security->xss_clean($division);
        $id = base64_decode($this->input->post('edit_id'));

        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id', TRUE);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        if ($id == '') {
            $this->db->select('division_name')->from('division_mst');
            $this->db->where('division_name', $division_name);
            $this->db->where('company_id', $cmp_id);
            $CheckResult = $this->db->get()->row();
        } else {
            $this->db->select('division_name')->from('division_mst');
            $this->db->where('division_name', $division_name);
            $this->db->where('company_id', $cmp_id);
            $this->db->where('id!=', $id);
            $CheckResult = $this->db->get()->row();
        }
        if (count((array) $CheckResult) > 0){
                $this->form_validation->set_message('duplicate_division', 'Division Name already exists..!!');
                return FALSE;
        }else{
                return TRUE;
        }
    }

    public function Check_Division()
    {
        // echo '<pre>';
        // print_r($this->input->post());exit;
        $division_name = $this->security->xss_clean($this->input->post('division_name', TRUE));
        $msg = '';
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id', TRUE);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        if ($cmp_id != "") {
            $id = $this->security->xss_clean($this->input->post('edit_id', TRUE));
            if ($id == '') {
                $this->db->select('division_name')->from('division_mst');
                $this->db->where('division_name', $division_name);
                $this->db->where('company_id', $cmp_id);
                $CheckResult = $this->db->get()->row();
            } else {
                $this->db->select('division_name')->from('division_mst');
                $this->db->where('division_name', $division_name);
                $this->db->where('company_id', $cmp_id);
                $this->db->where('id!=', $id);
                $CheckResult = $this->db->get()->row();
            }
            if (count((array) $CheckResult) > 0) {
                $msg = "Division Name already exists....";
            }
        }
        echo $msg;
    }

    public function DatatableRefresh()
    {
        $dtSearchColumns = array('m.id', 'm.id', 'm.division_name', 'c.company_name', 'm.status', 'm.id');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('filter_company_id') ? $this->input->get('filter_company_id') : '');
            if ($cmp_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND m.company_id  = " . $cmp_id;
                } else {
                    $dtWhere .= " WHERE m.company_id  = " . $cmp_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND m.company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE m.company_id  = " . $this->mw_session['company_id'];
            }
        }
        $status = $this->input->get('filter_status');
        if ($status != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND m.status  = " . $status;
            } else {
                $dtWhere .= " WHERE m.status  = " . $status;
            }
        }
        $DTRenderArray = $this->division_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'division_name', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);

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
                    if ($acces_management->allow_view or $acces_management->allow_edit or $acces_management->allow_delete) {
                        // $action = '<div class="btn-group">';
                        // if ($acces_management->allow_edit){
                        //     $action .='<a type="button" class="btn btn-default btn-xs">Edit&nbsp;<i class="fa fa-pencil"></i></a>';
                        // }
                        // if ($acces_management->allow_delete){
                        //     $action .='<a type="button" class="btn btn-default btn-xs">Delete&nbsp;<i class="fa fa-trash-o"></i></a>'; 
                        // }
                        // $action .='</div>';
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a onclick="LoadEditModal(\'' . base64_encode($dtRow['id']) . '\')">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_delete) {
                            $action .= '<li>
                                        <a onclick="LoadDeleteDialog(\'' . base64_encode($dtRow['id']) . '\');" href="javascript:void(0)">
                                        <i class="fa fa-trash-o"></i>&nbsp;Delete
                                        </a>
                                    </li>';
                        }
                        $action .= '</ul>';
                    } else {
                        $action = '<button class="btn btn-default btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
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
        //VAPT CHANGE POINT 3 -- START
        foreach ($output as $outkey => $outval) {
            if ($outkey !== 'aaData') {
                $output[$outkey] = $this->security->xss_clean($outval);
            }
        }
        //VAPT CHANGE POINT 3 -- END
        echo json_encode($output);
    }

    public function remove()
    {
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $deleted_id = $this->security->xss_clean($this->input->Post('deleteid'));
            $del_id = base64_decode($deleted_id);
            $StatusFlag = $this->division_model->CrosstableValidation($del_id);
            if ($StatusFlag) {
                $this->division_model->remove(base64_decode($deleted_id));
                $message = "Division deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Division cannot be deleted. Reference of Division found in other module!.<br/>";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function record_actions($Action)
    {
        $action_id = $this->input->Post('id');
        if (count((array) $action_id) == 0) {
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
                    'modifiedby' => $this->mw_session['user_id']
                );
                $this->common_model->update('division_mst', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->division_model->CrosstableValidation($id);
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']
                    );
                    $this->common_model->update('division_mst', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. Reference of Division found in other module!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->division_model->CrosstableValidation($id);
                if ($DeleteFlag) {
                    $this->common_model->delete('division_mst', 'id', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Division cannot be deleted. Reference in other module!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Division(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }
}