<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Workshoptype extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('workshoptype');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('workshopmst_model');
    }

    public function index() {
        $data['module_id'] = '1.05';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmpdata'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['cmpdata'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('workshop_type/index', $data);
    }

    public function submit() {
        $alert_type = 'success';
        $url = '';
        $mode = '';
        $message = "";
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $message = "You have no rights to Add,Contact Administrator for rights.";
            $alert_type = 'error';
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
            $this->form_validation->set_rules('workshop_type', 'WorkShop Type', 'trim|required|max_length[250]');
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
                        'workshop_type' => $this->input->post('workshop_type'),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                        'deleted' => 0
                    );
                    $this->common_model->insert('workshoptype_mst', $data);
                    $message = "WorkShop Type created successfully.";
                    $url = base_url() . 'workshoptype';
                } else {
                    $mode = 'edit';
                    $now = date('Y-m-d H:i:s');
                    $edit_id = base64_decode($this->input->post('edit_id'));
                    $data = array(
                        'company_id' => $Company_id,
                        'workshop_type' => $this->input->post('workshop_type'),
                        'status' => $this->input->post('status'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                        'deleted' => 0
                    );
                    $this->common_model->update('workshoptype_mst', 'id', $edit_id, $data);
                    $message = "WorkShop Type updated successfully.";
                    $url = base_url() . 'workshoptype';
                }
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type, 'url' => $url, 'mode' => $mode));
    }

    public function edit() {
        $alert_type = 'success';
        $message = '';
        if (count((array)$this->input->post()) > 0) {
            $edit_id = base64_decode($this->input->post('edit_id'));
            $data['acces_management'] = $this->acces_management;

            if (!$data['acces_management']->allow_edit) {
                
            } else {
                $data['result'] = $this->workshopmst_model->find_by_id($edit_id);
                echo json_encode(array('message' => $message, 'alert_type' => $alert_type, 'result' => $data['result']));
            }
        } else {
            $alert_type = 'error';
            $message = "Failed to retrive data from server";
            echo json_encode(array('message' => $message, 'alert_type' => $alert_type, 'result' => ''));
        }
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('m.id', 'm.id', 'c.company_name', 'm.workshop_type', 'm.status', 'm.id');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('filter_cmp') ? $this->input->get('filter_cmp') : '');
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

        $DTRenderArray = $this->workshopmst_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
//        echo '<pre>';
//        print_r($DTRenderArray);exit;
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'company_name', 'workshop_type', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);

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
        echo json_encode($output);
    }

    public function Check_WorkshopType() {
        $workshop_type = $this->input->post('workshop_type', true);
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id', TRUE);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        $wtype_id = $this->input->post('workshoType_id', true);
        if ($cmp_id != '') {
            echo $this->workshopmst_model->check_workshopType($workshop_type, $cmp_id, $wtype_id);
        }
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
            $StatusFlag = $this->workshopmst_model->CrosstableValidation(base64_decode($deleted_id));
            if ($StatusFlag) {
                $this->workshopmst_model->remove(base64_decode($deleted_id));
                $message = "Workshop Type deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Workshop Type cannot be deleted. Reference of Workshop Type found in other module!<br/>";
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
                $this->common_model->update('workshoptype_mst', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->workshopmst_model->CrosstableValidation($id);
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('workshoptype_mst', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. Reference of Workshop Type found in other module!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->workshopmst_model->CrosstableValidation($id);
                if ($DeleteFlag) {
                    $this->common_model->delete('workshoptype_mst', 'id', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Workshop Type cannot be deleted. Reference in other module!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Workshop Type(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function ajax_company() {
        return $this->common_model->fetch_company_data($this->input->get());
    }

}
