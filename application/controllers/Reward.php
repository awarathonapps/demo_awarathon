<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reward extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('reward');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('reward_model');
    }

    public function ajax_reward_company() {
        return $this->common_model->fetch_reward_company($this->input->get());
    }

    public function ajax_populate_reward() {
        return $this->common_model->fetch_reward_data($this->input->get());
    }

    public function index() {
        $data['module_id'] = '6.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompanyData'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['spondata'] = $this->common_model->get_sponsor();
        $data['rows'] = $this->reward_model->fetch_access_data();
        $this->load->view('reward/index', $data);
    }

    public function create() {
        $data['module_id'] = '6.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('reward/create', $data);
    }

    public function edit($id, $step = 1) {
        $reward_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('reward');
            return;
        }

        $data['module_id'] = '6.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->reward_model->fetch_reward($reward_id);
        $data['Row'] = $this->common_model->fetch_object_by_id('reward', 'id', $reward_id);
        $Company_id = $this->mw_session['company_id'];
        $asset_url = $this->config->item('assets_url');
        if ($Company_id != "") {
            $data['CompnayResultSet'] = array();
        } else {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        }
        $data['Image_path'] = $asset_url . "assets/uploads/reward/banners/";
        $data['Company_id'] = $Company_id;
        $data['BannerImageSet'] = $this->common_model->fetch_object_by_field('reward_banner', 'reward_id', $reward_id);
        $data['step'] = $step;
        $this->load->view('reward/edit', $data);
    }

    public function view($id) {
        $reward_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('reward');
            return;
        }

        $data['module_id'] = '6.00';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->reward_model->fetch_reward($reward_id);
        $data['BannerImageSet'] = $this->common_model->fetch_object_by_field('reward_banner', 'reward_id', $reward_id);
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id != "") {
            $data['CompnayResultSet'] = array();
        } else {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        }
        $data['Image_path'] = base_url() . 'assets/uploads/reward/banners/';
        $data['Company_id'] = $Company_id;
        $this->load->view('reward/view', $data);
    }

    public function UploadBanner($reward_id) {
        $this->load->library('upload');
        $ImageFormat = explode('.', $_FILES['file']['name']);
        $NewImageName = time();
        $Success = 1;
        $Msg = "";
        $UploadPath = './assets/uploads/reward/banners/';
        if ($Success) {
            $this->upload->initialize($this->set_upload_options($NewImageName, $UploadPath));
            if (!$this->upload->do_upload('file')) {
                $response['result'] = array('error' => $this->upload->display_errors());
                $Success = 0;
            }
        }
        $image = '';
        if ($Success) {
            $MaxRowSet = $this->common_model->get_value('reward_banner', 'max(sorting) as sorting', 'reward_id=' . $reward_id);
            $MaxNo = $MaxRowSet->sorting + 1;
            $image = $NewImageName . '.' . $ImageFormat[1];
            $data = array(
                'reward_id' => $reward_id,
                'sorting' => $MaxNo,
                'thumbnail_image' => $image);
            $insertId = $this->common_model->insert('reward_banner', $data);
            $response['result'] = 'OK';
            $image = $UploadPath . $image;
            $response['NewId'] = $insertId;
            $response['NewSortNo'] = $MaxNo;
        }
        $response['image'] = $image;
        echo json_encode($response);
    }

    public function RemoveBanner() {
        $ImageId = $this->input->Post('ImageId');
        $Image = $this->common_model->get_value('reward_banner', 'thumbnail_image', 'id=' . $ImageId);
        $Success = 1;
        $Path = "./assets/uploads/reward/banners/" . $Image->thumbnail_image;
        if (file_exists($Path)) {
            unlink($Path);
        } else {
            $Success = 0;
        }
        if ($Success) {
            $this->common_model->delete('reward_banner', 'id', $ImageId);
        }
        echo $Success;
    }

    private function set_upload_options($ImageName, $UploadPath) {
        //upload an image options

        $config = array();
        $config['upload_path'] = $UploadPath;
        $config['overwrite'] = FALSE;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_width'] = 320;
        $config['max_height'] = 60;
        $config['min_width'] = 320;
        $config['min_height'] = 60;
        $config['file_name'] = $ImageName;
        return $config;
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('r.id', 'c.company_name', 'r.sponsor_name', 'r.reward_name', 'r.offer_code', 'r.quantity', 'r.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];


        $sponsor_id = ($this->input->get('sponsor_id') ? $this->input->get('sponsor_id') : '');
        if ($sponsor_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND r.id  = " . $sponsor_id;
            } else {
                $dtWhere .= " WHERE r.id  = " . $sponsor_id;
            }
        }
        $reward_id = ($this->input->get('reward_id') ? $this->input->get('reward_id') : '');
        if ($reward_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND r.id  = " . $reward_id;
            } else {
                $dtWhere .= " WHERE r.id  = " . $reward_id;
            }
        }
        $status = $this->input->get('status');
        if ($status != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND r.status  = " . $status;
            } else {
                $dtWhere .= " WHERE r.status  = " . $status;
            }
        }
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
            if ($cmp_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND r.company_id  = " . $cmp_id;
                } else {
                    $dtWhere .= " WHERE r.company_id  = " . $cmp_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND r.company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE r.company_id  = " . $this->mw_session['company_id'];
            }
        }
        $DTRenderArray = $this->reward_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'company_name', 'sponsor_name', 'reward_name', 'offer_code', 'quantity', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $Reward_Name = $dtRow['reward_name'];
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
                                        <a href="' . $site_url . 'reward/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'reward/edit/' . base64_encode($dtRow['id']) . '">
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
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            redirect('reward');
            return;
        }
        $this->load->library('upload');
        $this->load->library('form_validation');
        $data['username'] = $this->mw_session['username'];
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
        $this->form_validation->set_rules('sponsor_name', 'Company name', 'required');
        if ($this->mw_session['company_id'] == "") {
            $this->form_validation->set_rules('company_id', 'Company name', 'required');
            $Company_id = $this->input->post('company_id');
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        $this->form_validation->set_rules('reward_title', 'Title', 'required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('start_date', 'Start Date', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('end_date', 'End Date', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('offer_code', 'Offer Code', 'trim|required|max_length[10]|is_unique[reward.offer_code]');
        $this->form_validation->set_rules('qty', 'Quantity', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('stride_limit', 'Stride Limit', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {

            $start_date = date('Y-m-d', strtotime($this->input->post('start_date')));
            $end_date = date('Y-m-d', strtotime($this->input->post('end_date')));
            if ($this->input->post('send_to_email_reward') != null) {
                $email_reward = 1;
            } else {
                $email_reward = 0;
            }
            if ($this->input->post('send_to_email_rules') != null) {
                $email_rules = 1;
            } else {
                $email_rules = 0;
            }
            if ($this->input->post('send_to_email_term') != null) {
                $email_term = 1;
            } else {
                $email_term = 0;
            }
            if ($this->input->post('send_to_email_contact') != null) {
                $email_cantact = 1;
            } else {
                $email_cantact = 0;
            }
            $now = date('Y-m-d H:i:s');
            $data = array(
                'sponsor_name' => ucfirst(strtolower($this->input->post('sponsor_name'))),
                'reward_name' => ucfirst(strtolower($this->input->post('reward_title'))),
                'short_description' => $this->input->post('short_description'),
                'company_id' => $Company_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'offer_code' => $this->input->post('offer_code'),
                'quantity' => $this->input->post('qty'),
                'stride_limit' => $this->input->post('stride_limit'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'email' => $this->input->post('email'),
                'send_reward_details' => $email_reward,
                'reward_details' => $this->input->post('reward_details'),
                'send_contest_rules' => $email_rules,
                'contest_rules' => $this->input->post('rules_regulation'),
                'send_terms_conditions' => $email_term,
                'terms_conditions' => $this->input->post('term_condition'),
                'send_contact_details' => $email_cantact,
                'contact_details' => $this->input->post('contact_detail'),
                'addeddate' => $now,
                'addedby' => $this->mw_session['user_id']
            );
            $reward_Id = $this->common_model->insert('reward', $data);
            //$this->session->set_flashdata('flash_message', "Reward Details Added Successfully.");
            //redirect('reward');
            if ($reward_Id != '') {
                $this->session->set_flashdata('flash_message', "Reward Details Added successfully.");
                $Encode_id = base64_encode($reward_Id);
                redirect('reward/edit/' . $Encode_id . "/2");
            } else {
                $this->session->set_flashdata('flash_message', "Error while Adding Reward,Contact Mediaworks for technical support.!.");
                redirect('reward');
            }
        }
    }

    public function update($id) {
        $id = base64_decode($id);
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            redirect('reward');
            return;
        }
        $this->load->library('form_validation');
        //$data['username'] = $this->mw_session['username'];
        //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
        $this->form_validation->set_rules('sponsor_name', 'Sponsor name', 'required');
        $this->form_validation->set_rules('reward_title', 'Title', 'required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('start_date', 'Start Date', 'trim|required|max_length[50]');
        //$this->form_validation->set_rules('end_date', 'End Date', 'callback_validateENDDate');
        $this->form_validation->set_rules('offer_code', 'Offer Code', 'trim|required|max_length[10]');
        $this->form_validation->set_rules('qty', 'Quantity', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('stride_limit', 'Stride Limit', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
        if ($this->mw_session['company_id'] == "") {
            $this->form_validation->set_rules('company_id', 'Company name', 'required');
            $Company_id = $this->input->post('company_id');
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        if ($this->form_validation->run() == FALSE) {
            $this->edit(base64_encode($id));
            return;
        } else {
            $start_date = date('Y-m-d', strtotime($this->input->post('start_date')));
            $end_date = date('Y-m-d', strtotime($this->input->post('end_date')));
            if ($this->input->post('send_to_email_reward') != null) {
                $email_reward = 1;
            } else {
                $email_reward = 0;
            }
            if ($this->input->post('send_to_email_rules') != null) {
                $email_rules = 1;
            } else {
                $email_rules = 0;
            }
            if ($this->input->post('send_to_email_term') != null) {
                $email_term = 1;
            } else {
                $email_term = 0;
            }
            if ($this->input->post('send_to_email_contact') != null) {
                $email_cantact = 1;
            } else {
                $email_cantact = 0;
            }

            $now = date('Y-m-d H:i:s');
            $data = array(
                'sponsor_name' => ucfirst(strtolower($this->input->post('sponsor_name'))),
                'reward_name' => ucfirst(strtolower($this->input->post('reward_title'))),
                'short_description' => $this->input->post('short_description'),
                'company_id' => $Company_id,
                //'password' => $this->common_model->encrypt_password($this->input->post('password')),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'offer_code' => $this->input->post('offer_code'),
                'quantity' => $this->input->post('qty'),
                'stride_limit' => $this->input->post('stride_limit'),
                'remarks' => $this->input->post('remarks'),
                'status' => $this->input->post('status'),
                'email' => $this->input->post('email'),
                'send_reward_details' => $email_reward,
                'reward_details' => $this->input->post('reward_details'),
                'send_contest_rules' => $email_rules,
                'contest_rules' => $this->input->post('rules_regulation'),
                'send_terms_conditions' => $email_term,
                'terms_conditions' => $this->input->post('term_condition'),
                'send_contact_details' => $email_cantact,
                'contact_details' => $this->input->post('contact_detail'),
                'modifieddate' => $now,
                'modifiedby' => $this->mw_session['user_id']
            );
            $this->common_model->update('reward', 'id', $id, $data);
            $Url = $this->input->post('url');
            if (count((array)$Url) > 0) {
                foreach ($Url as $key => $value) {
                    $data = array(
                        'url' => $value,
                        'sorting' => $this->input->post('url')[$key],
                        'status' => 1,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']
                    );
                    $this->common_model->update('reward_banner', 'id', $key, $data);
                }
            }
            $this->session->set_flashdata('flash_message', "Reward updated successfully");
            redirect('reward/edit/' . base64_encode($id));
        }
    }

    public function validateENDDate($str) {
        if ($str == '') {
            return true;
        }
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $nowdate = date('Y-m-d H:i:s');
        if (strtotime($start_date) > strtotime($str)) {
            $this->form_validation->set_message('validateReceiptDate', 'Start Date Can Not be Greater Than End Date');
            return FALSE;
        } else {
            return true;
        }
    }

    public function remove() {
        $alert_type = 'success';
        $message = '';
        $title = '';
        $deleted_id = base64_decode($this->input->Post('deleteid'));
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $RowData = $this->common_model->get_value('workshop_reward', 'workshop_id', 'reward_id=' . $deleted_id);
            if (count((array)$RowData) == 0) {
                $BannerImageSet = $this->common_model->fetch_object_by_field('reward_banner', 'reward_id', $deleted_id);
//                if ($this->mw_session['company_id'] != "") {
//                    $Path = $_SERVER['DOCUMENT_ROOT'] . '/mwadmin/assets/reward/banners/';
//                } else {
//                    $Path = "./assets/uploads/reward/banners/";
//                }
                $Path = "./assets/uploads/reward/banners/";
            } else {
                $message = "Reward cannot be deleted. Reward(s) already in Use!";
                $alert_type = 'error';
            }
            if ($alert_type == 'success') {
                if (count((array)$BannerImageSet) > 0) {
                    foreach ($BannerImageSet as $key => $value) {
                        $Image_Path = $Path . $value->thumbnail_image;
                        if (file_exists($Image_Path)) {
                            unlink($Image_Path);
                        }
                    }
                }
                //$this->common_model->delete('reward', 'id', base64_decode($deleted_id));
                $this->common_model->delete('reward', 'id', $deleted_id);
                $this->common_model->delete('reward_banner', 'reward_id', $deleted_id);
                $message = "Reward deleted successfully.";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function Check_offercode() {
        $offer_code = $this->input->post('offer_code', true);
        $reward_id = $this->input->post('reward_id', true);
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id', true);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        echo $this->reward_model->check_code($offer_code, $reward_id, $cmp_id);
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
                $this->common_model->update('reward', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            foreach ($action_id as $id) {
                $data = array(
                    'status' => 0,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']);
                $this->common_model->update('reward', 'id', $id, $data);
            }
            $message .= 'Status changed to in-active sucessfully.';
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $RowData = $this->common_model->get_value('workshop_reward', 'workshop_id', 'reward_id=' . $id);
                if (count((array)$RowData) == 0) {
                    $this->common_model->delete('reward', 'id', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message .= "Reward cannot be deleted. Reward(s) already in Use!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Reward(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

}
