<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_notification extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->session->userdata('awarathon_session');
        $SupperAccess = false;
        if ($acces_management['superaccess']) {
            $SupperAccess = true;
        }else{
            redirect('dashboard');
        }
        $this->SupperAccess = $SupperAccess;
        $this->load->model('admin_notification_model');
    }

    public function index(){
        $data['module_id'] = '1.14';
        $data['username'] = $this->mw_session['username'];
        $data['supperAccess'] = $this->SupperAccess;
        $Company_id = $this->mw_session['company_id'];
        $data['Company_id'] = $Company_id;
        $data['notifications'] = $this->common_model->get_selected_values('admin_notification', 'id,message', 'status=1');
        $this->load->view('admin_notification/index', $data);
    }

    public function DatatableRefresh() {
        $SupperAccess = $this->SupperAccess;

        $dtSearchColumns = array('an.id', 'an.message', 'an.addeddate', 'an.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $DTRenderArray = $this->admin_notification_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('id', 'message', 'addeddate', 'status', 'Actions');
        $site_url = base_url();

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
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if ($SupperAccess) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($dtRow['status'] == 1) {
                            $action .= '<li>
                                        <a onclick="updateStatus(\'' . base64_encode($dtRow['id']) . '\', \'0\')">
                                        <i class="fa fa-times"></i>&nbsp;In-active
                                        </a>
                                    </li>';
                        } else {
                            $action .= '<li>
                                        <a onclick="updateStatus(\'' . base64_encode($dtRow['id']) . '\', \'1\')">
                                        <i class="fa fa-check"></i>&nbsp;Active
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
        foreach($output as $outkey=>$outval){
            if($outkey !== 'aaData'){
                $output[$outkey] = $this->security->xss_clean($outval);
            }
        }
        //VAPT CHANGE POINT 3 -- END
        echo json_encode($output);
    }

    public function submit() {
        $alert_type = 'success';
        $url = '';
        $mode = '';
        $SupperAccess = $this->SupperAccess;
        if (!$SupperAccess) {
            $url = base_url() . 'dashboard';
        } else {
            $this->load->library('form_validation');
            $data['username'] = $this->mw_session['username'];

            $this->form_validation->set_rules('notification_message', 'Notification Message', 'trim|required|max_length[100]');
            // $this->form_validation->set_rules('status', 'Status', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $alert_type = 'error';
                $message = validation_errors();
            } else {
                // if ($this->input->post('edit_id') == '') {
                    $mode = 'add';
                    $now = date('Y-m-d H:i:s');

                    $inactive_data = [
                        'status' => 0
                    ];
                    $this->common_model->update('admin_notification', 'status', '1', $inactive_data);
                    $data = array(
                        'message' => $this->security->xss_clean($this->input->post('notification_message')),
                        // 'status' => $this->security->xss_clean($this->input->post('status')),
                        'addeddate' => $now
                    );

                    $this->common_model->insert('admin_notification', $data);
                    $this->session->set_flashdata('flash_message', "Notification added successfully.");
                    $message = "Notification added successfully.";
                    $url = base_url() . 'admin_notification';
                // } else {
                //     $mode = 'edit';
                //     $now = date('Y-m-d H:i:s');
                //     $edit_id = base64_decode($this->input->post('edit_id'));
                //     $data = array(
                //         'company_id' => $Company_id,
                //         'region_name' => $this->security->xss_clean($this->input->post('region_name')),
                //         'status' => $this->security->xss_clean($this->input->post('status')),
                //         'modifieddate' => $now,
                //         'modifiedby' => $this->security->xss_clean($this->mw_session['user_id']),
                //         'deleted' => 0
                //     );

                //     $this->common_model->update('region', 'id', $edit_id, $data);
                //     $this->session->set_flashdata('flash_message', "Region updated successfully.");
                //     $message = "Region updated successfully.";
                //     $url = base_url() . 'region';
                // }
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type, 'url' => $url, 'mode' => $mode));
    }

    public function updateStatus(){
        $alert_type = 'success';
        $id = base64_decode($this->input->post('id'));
        $status = $this->security->xss_clean($this->input->post('status'));
        if($status == 1){   //if active any notification then make in-active other notifications
            $inactive_data = [
                'status' => 0
            ];
            $this->common_model->update('admin_notification', 'status', '1', $inactive_data);
        }
        $data = array(
            'status' => $status,
            'modifieddate' => date('Y-m-d H:i:s')
        );

        $this->common_model->update('admin_notification', 'id', $id, $data);
        $message = "Notification status updated successfully.";

        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
    }

}