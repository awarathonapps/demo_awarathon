<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Goals extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('goals');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('goals_model');
    }
    public function index()
    {
        $data['module_id'] = '88';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('goals/index', $data);
    }
    public function create()
    {
        $data['module_id'] = '88';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('goals');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;

        $parameter_result = $this->common_model->get_selected_values('parameter_mst', 'id,description', 'status=1 AND company_id="' . $Company_id . '"');
        $data['parameter_result'] = $parameter_result;

        $this->load->view('goals/create', $data);
    }
    public function edit($id)
    {
        $subparameter_id = $this->security->xss_clean(base64_decode($id));
        $data['module_id'] = '88';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('goals');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            // $data['cmp_result'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');

            $this->db->select('id,company_name');
            $this->db->from('company');
            $this->db->where('status', '1');
            $data['cmp_result'] = $this->db->get()->result();
        } else {
            $data['cmp_result'] = array();
        }
        $data['Company_id'] = $Company_id;
        // $parameter_result = $this->common_model->get_selected_values('parameter_mst', 'id,description', 'status=1 AND company_id="' . $Company_id . '"');

        $this->db->select('id,description');
        $this->db->from('parameter_mst');
        $where = 'status ="1" AND company_id ="' . $Company_id . '" ';
        $this->db->where($where);
        $parameter_result = $this->db->get()->result();

        $data['parameter_result'] = $parameter_result;

        // $data['result'] = $this->common_model->get_value('goal_mst', '*', 'id=' . $subparameter_id);

        $this->db->select('*');
        $this->db->from('goal_mst');
        $this->db->where('id', $subparameter_id);
        $data['result'] = $this->db->get()->row();
        $this->load->view('goals/edit', $data);
    }
    public function view($id)
    {
        $subparameter_id = $this->security->xss_clean(base64_decode($id));
        $data['module_id'] = '88';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('goals');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            // $data['cmp_result'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');

            $this->db->select('id,company_name');
            $this->db->from('company');
            $this->db->where('status', '1');
            $data['cmp_result'] = $this->db->get()->result();
        } else {
            $data['cmp_result'] = array();
        }
        $data['Company_id'] = $Company_id;
        // $data['result'] = $this->common_model->get_value('goal_mst', '*', 'id=' . $subparameter_id);

        $this->db->select('*');
        $this->db->from('goal_mst');
        $this->db->where('id', $subparameter_id);
        $data['result'] = $this->db->get()->row();

        if (isset($data['result']) and count((array)$data['result']) > 0) {
            // $parameter_result = $this->common_model->get_value('parameter_mst', '*', 'id=' . $data['result']->parameter_id);
            $this->db->select('*');
            $this->db->from('parameter_mst');
            $this->db->where('id', $data['result']->parameter_id);
            $parameter_result = $this->db->get()->row();

            if (isset($parameter_result) and count((array)$parameter_result) > 0) {
                $data['parameter_name'] = $parameter_result->description;
            }
        }
        $this->load->view('goals/view', $data);
    }
    public function DatatableRefresh()
    {
        $dtSearchColumns = array('p.id', 'p.id', 'p.description', 'pm.description');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $status = $this->input->get('filter_status');
        if ($status != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND p.status  = " . $status;
            } else {
                $dtWhere .= " WHERE p.status  = " . $status;
            }
        }

        $DTRenderArray = $this->goals_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'description', 'parameter_name', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $title = $dtRow['description'];
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
                    if ($acces_management->allow_add or $acces_management->allow_view or $acces_management->allow_edit or $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_view) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'goals/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'goals/edit/' . base64_encode($dtRow['id']) . '">
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
    public function submit()
    {
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message .= "You have no rights to Add,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('parameter_id', 'Parameter', 'required');
            $this->form_validation->set_rules('description', 'Sub Parameter', 'required');
            $this->form_validation->set_rules('status', 'Status', 'required');
            if ($this->form_validation->run() == FALSE) {
                $Message .= validation_errors();
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                $now = date('Y-m-d H:i:s');
                $data = array(
                    'parameter_id'          => $this->input->post('parameter_id'),
                    'description'           => $this->input->post('description'),
                    'status'                => $this->input->post('status'),
                    'addeddate'             => $now,
                    'addedby'               => $this->mw_session['user_id'],
                );
                $insert_id = $this->common_model->insert('goal_mst', $data);
                $Message .= "Sub Parameter Save Successfully..";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function update($Encode_id)
    {
        $id = base64_decode($Encode_id);
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message .= "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('parameter_id', 'Parameter', 'required');
            $this->form_validation->set_rules('description', 'Sub Parameter', 'required');
            $this->form_validation->set_rules('status', 'Status', 'required');
            if ($this->form_validation->run() == FALSE) {
                $Message .= validation_errors();
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                $now = date('Y-m-d H:i:s');
                $data = array(
                    'parameter_id'          => $this->input->post('parameter_id'),
                    'description'           => $this->input->post('description'),
                    'status'                => $this->input->post('status'),
                    'modifieddate'          => $now,
                    'modifiedby'            => $this->mw_session['user_id'],
                );
                $insert_id = $this->common_model->update('goal_mst', 'id', $id, $data);
                $Message .= "Sub Parameter Update Successfully..";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
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
            $deleted_id = $this->input->post('deleteid');
            $DeleteFlag = 1;
            if ($DeleteFlag) {
                $this->common_model->delete('goal_mst', 'id', base64_decode($deleted_id));
                $message = "Sub Parameter deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Sub Parameter cannot be deleted.";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function record_actions($Action)
    {
        $action_id = $this->input->post('id');
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
                    'modifiedby' => $this->mw_session['user_id']
                );
                $this->common_model->update('goal_mst', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $StatusFlag = 1;
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']
                    );
                    $this->common_model->update('goal_mst', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. !<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $DeleteFlag = 1;
                if ($DeleteFlag) {
                    $this->common_model->delete('goal_mst', 'id', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Sub Parameter cannot be deleted.";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Sub Parameter deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }
    public function Check_subparameter()
    {
        $parameterlabel = $this->security->xss_clean($this->input->post('description', true));
        $subparameter_id    = $this->security->xss_clean($this->input->post('subparameter_id', true));
        if ($parameterlabel != '') {
            
            if (!preg_match("/^[a-z0-9-_]+$/", $parameterlabel)) {
                echo "Please enter a valid description";

            } else {

                // echo $this->goals_model->Check_subparameter_exist($parameterlabel,$subparameter_id);
                $this->db->select('description')->from('goal_mst');
                $this->db->where('description', $parameterlabel);

                if ($subparameter_id != '') {
                    $this->db->where('id!= ', $subparameter_id);
                }
                $check = $this->db->get()->row();
                echo (count((array)$check) > 0 ? true : false);
            }
        }
    }
}