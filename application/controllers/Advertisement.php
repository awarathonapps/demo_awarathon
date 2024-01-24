<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Advertisement extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('advertisement');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('advertisement_model');
    }

    public function index() {
        $data['module_id'] = '8.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompanyData'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('advertisement/index', $data);
    }

    public function create($errors = '') {
        $data['module_id'] = '8.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('advertisement');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['errors'] = $errors;
        $this->load->view('advertisement/create', $data);
    }

    public function edit($id, $errors = '') {

        $avdt_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('advertisement');
            return;
        }

        $data['errors'] = $errors;
        $data['module_id'] = '8.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['result'] = $this->advertisement_model->fetch_advertisement($avdt_id);
        $this->load->view('advertisement/edit', $data);
    }

    public function view($id) {
        $avdt_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('advertisement');
            return;
        }

        $data['module_id'] = '8.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->advertisement_model->fetch_advertisement($avdt_id);
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('advertisement/view', $data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('a.id', 'c.company_name', 'a.advt_name', 'a.start_date', 'a.end_date', 'a.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $status = $this->input->get('status');
        if ($status != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.status  = " . $status;
            } else {
                $dtWhere .= " WHERE a.status  = " . $status;
            }
        }
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
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
        $DTRenderArray = $this->advertisement_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'company_name', 'advt_name', 'start_date', 'end_date', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $advt_name = $dtRow['advt_name'];
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "status") {
                    if ($dtRow['status'] == 1) {
                        $status = '<span class="label label-sm label-info status-active" > Active </span>';
                    } else {
                        $status = '<span class="label label-sm label-danger status-inactive" > In Active </span>';
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "start_date") {
                    $row[] = ($dtRow['start_date'] == '01-01-1970' ? '' : $dtRow['start_date']);
                } else if ($dtDisplayColumns[$i] == "end_date") {
                    $row[] = ($dtRow['end_date'] == '01-01-1970' ? '' : $dtRow['end_date']);
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
                                        <a href="' . $site_url . 'advertisement/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'advertisement/edit/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_delete) {
                            $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\'' . base64_encode($dtRow['id']) . '\');" href="javascript:void(0)">
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
        $thumbnail_image = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            redirect('advertisement');
            return;
        }
        $this->load->library('form_validation');
        $data['username'] = $this->mw_session['username'];
        //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
        if ($this->mw_session['company_id'] == "") {
            $this->form_validation->set_rules('company_id', 'Company name', 'required');
            $Company_id = $this->input->post('company_id');
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        $upload_Path = './assets/uploads/advertisement/';
        $this->form_validation->set_rules('advt_name', 'Advertisement Title', 'required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            if (isset($_FILES['thumbnail_image']['name']) && $_FILES['thumbnail_image']['size'] > 0) {
                $this->load->library('upload');
                $config = array();
                $thumbnail_image = time();

                $config['upload_path'] = $upload_Path;
                $config['overwrite'] = FALSE;
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_width'] = 750;
                $config['max_height'] = 400;
                $config['min_width'] = 750;
                $config['min_height'] = 400;
                $config['file_name'] = $thumbnail_image;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('thumbnail_image')) {
                    $error = $this->upload->display_errors();
                    $this->create($error);
                    return;
                }

                $ImgArrays = explode('.', $_FILES['thumbnail_image']['name']);
                $thumbnail_image .="." . $ImgArrays[1];
            }

            $start_date = date('Y-m-d', strtotime($this->input->post('start_date')));
            $end_date = date('Y-m-d', strtotime($this->input->post('end_date')));
            $now = date('Y-m-d H:i:s');
            $data = array(
                'advt_name' => ucfirst(strtolower($this->input->post('advt_name'))),
                'company_id' => $Company_id,
                'remarks' => ucfirst(strtolower($this->input->post('remarks'))),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'url' => $this->input->post('url'),
                'thumbnail_image' => $thumbnail_image,
                'sorting' => $this->input->post('sorting'),
                'status' => $this->input->post('status'),
                'addeddate' => $now,
                'addedby' => $this->mw_session['user_id']
            );
            $this->common_model->insert('advertisement', $data);
            $this->session->set_flashdata('flash_message', "Advertisement Created Successfully.");
            redirect('advertisement');
        }
    }

    public function update($Decodeid) {
        $id = base64_decode($Decodeid);
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            redirect('advertisement');
            return;
        }
        $this->load->library('form_validation');
        if ($this->mw_session['company_id'] == "") {
            $this->form_validation->set_rules('company_id', 'Company name', 'required');
            $Company_id = $this->input->post('company_id');
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        $upload_Path = './assets/uploads/advertisement/';
        $this->form_validation->set_rules('advt_name', 'Advertisement Title', 'required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
        $Oldata = $this->common_model->get_value('advertisement', 'thumbnail_image', 'id=' . $id);
        if ($this->form_validation->run() == FALSE) {
            $this->edit($Decodeid);
        } else {
            $thumbnail_image = $Oldata->thumbnail_image;
            if (isset($_FILES['thumbnail_image']['name']) && $_FILES['thumbnail_image']['size'] > 0) {
                // $this->load->library('upload');
                $config = array();
                $thumbnail_image = time();
                $config['upload_path'] = $upload_Path;
                $config['overwrite'] = FALSE;
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_width'] = 750;
                $config['max_height'] = 400;
                $config['min_width'] = 750;
                $config['min_height'] = 400;
                $config['file_name'] = $thumbnail_image;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('thumbnail_image')) {
                    $error = $this->upload->display_errors();
                    $this->edit($Decodeid, $error);
                    return;
                }
                $ImgArrays = explode('.', $_FILES['thumbnail_image']['name']);
                $thumbnail_image .="." . $ImgArrays[1];
            } else {
                if ($thumbnail_image != "" && $this->input->post('RemoveImage') == 1) {
                    $Path = $upload_Path . $thumbnail_image;
                    if (file_exists($Path)) {
                        unlink($Path);
                    }
                    $thumbnail_image = '';
                }
            }
            $start_date = date('Y-m-d', strtotime($this->input->post('start_date')));
            $end_date = date('Y-m-d', strtotime($this->input->post('end_date')));
            $now = date('Y-m-d H:i:s');
            $data = array(
                'company_id' => $Company_id,
                'advt_name' => ucfirst(strtolower($this->input->post('advt_name'))),
                'remarks' => ucfirst(strtolower($this->input->post('remarks'))),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'url' => $this->input->post('url'),
                'thumbnail_image' => $thumbnail_image,
                'sorting' => $this->input->post('sorting'),
                'status' => $this->input->post('status'),
                'addeddate' => $now,
                'addedby' => $this->mw_session['user_id']
            );
//            if($thumbnail_image!='')
//            {
//                 $advrtid='id='.$id;
//                 $photo=$this->common_model->get_value('advertisement','thumbnail_image',$advrtid);
//                 $path='./assets/uploads/advertisement/'.$photo->thumbnail_image;
//                 unlink($path);
//                 $data['thumbnail_image'] = $thumbnail_image;
//            }                   

            $this->common_model->update('advertisement', 'id', $id, $data);
            $this->session->set_flashdata('flash_message', "Advertisement updated successfully");
            redirect('advertisement');
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
            $deleted_id = base64_decode($this->input->Post('deleteid'));
            $Oldata = $this->common_model->get_value('advertisement', 'thumbnail_image', 'id=' . $deleted_id);
            if ($Oldata->thumbnail_image != "") {
                $upload_Path = './assets/uploads/advertisement/';
                $Path = $upload_Path . $Oldata->thumbnail_image;
                if (file_exists($Path)) {
                    unlink($Path);
                }
            }
            $this->advertisement_model->remove($deleted_id);
            $message = "Advertisement deleted successfully.";
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
                $this->common_model->update('advertisement', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                // $StatusFlag = $this->workshop_model->CheckUserAssignRole($id);
                $StatusFlag = true;
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('advertisement', 'id', $id, $data);
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
                // $DeleteFlag = $this->workshop_model->CheckUserAssignRole($id);
                $DeleteFlag = true;
                if ($DeleteFlag) {
                    $Oldata = $this->common_model->get_value('advertisement', 'thumbnail_image', 'id=' . $id);
                    if ($Oldata->thumbnail_image != "") {
                        $upload_Path = './assets/uploads/advertisement/';
                        $Path = $upload_Path . $Oldata->thumbnail_image;
                        if (file_exists($Path)) {
                            unlink($Path);
                        }
                    }
                    $this->common_model->delete('advertisement', 'id', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Advertisement cannot be deleted.!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Advertisement(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function Check_advertisement() {
        $advertisement = $this->input->post('advt_name', true);
        $advt_id = $this->input->post('advt_id', true);
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id', TRUE);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        echo $this->advertisement_model->check_advertisement($advertisement, $advt_id, $cmp_id);
    }

}
