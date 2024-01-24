<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Company extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('awarathon_session') == FALSE) {
            redirect('index');
        } else {
            $this->mw_session = $this->session->userdata('awarathon_session');
            $acces_management = CheckRights($this->mw_session['user_id'], 'company');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('company_model');
            $this->load->model('smtp_model');
            $this->load->model('common_model');
        }
    }

    public function ajax_populate_industry() {
        return $this->company_model->fetch_industry_data($_GET);
    }

    public function ajax_populate_country() {
        return $this->company_model->fetch_country_data($_GET);
    }

    public function ajax_populate_state() {
        return $this->company_model->fetch_state_data($_GET);
    }

    public function ajax_populate_city() {
        return $this->company_model->fetch_city_data($_GET);
    }

    public function index() {
        $data['module_id'] = '1.01';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['rows'] = $this->company_model->fetch_access_data();
        $data['IndustryType'] = $this->common_model->fetch_object_by_field('industry_type', 'status', 1);
        $this->load->view('company/index', $data);
    }

    public function create() {
        $data['module_id'] = '1.01';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('company');
            return;
        }
        $data['FeedbackForm'] = $this->common_model->get_FeedbackForm();
        $data['result'] = $this->smtp_model->find_by_id();
        $data['IndustryType'] = $this->common_model->fetch_object_by_field('industry_type', 'status', 1);
        $data['OtpCode'] =$this->common_model->generatePIN(4);
        
        $this->load->view('company/create', $data);
    }

    public function edit($Encode_id, $step = 1) {
        $id = base64_decode($Encode_id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('company');
            return;
        }
        $data['module_id'] = '1.01';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->company_model->fetch_data($id);
        $data['IndustryType'] = $this->common_model->fetch_object_by_field('industry_type', 'status', 1);
        //$data['Country'] = $this->common_model->fetch_object_by_field('country','status',1);
        //$data['State'] = $this->common_model->fetch_object_by_field('state','status',1);
        //$data['City'] = $this->common_model->fetch_object_by_field('city','status',1);
        $data['FeedbackForm'] = $this->common_model->get_FeedbackForm();
        $data['smtpDetails'] = $this->common_model->fetch_object_by_id('company_smtp', 'company_id', $id);
        $data['step'] = $step;
        $data['roleResult'] = $this->common_model->get_selected_values('company_roles', 'arid,	rolename', 'status=1 AND company_id='.$id);
        //echo "<pre>"; print_r($data['smtpDetails']);print_r($data['step']);exit;
        //if (count((array)$data['result'])>0){
        $this->load->view('company/edit', $data);
//        }else{
//            $this->session->set_flashdata('flash_message', "Current record is not exists in database.");
//            redirect('company/index');
//        }
    }

    public function view($id, $step = 1) {
        $user_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('company');
            return;
        }
        
        $data['module_id'] = '1.01';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->company_model->fetch_data($user_id);
        $data['IndustryType'] = $this->common_model->fetch_object_by_field('industry_type', 'status', 1);
        $data['Country'] = $this->common_model->fetch_object_by_field('country', 'status', 1);
        $data['State'] = $this->common_model->fetch_object_by_field('state', 'status', 1);
        $data['City'] = $this->common_model->fetch_object_by_field('city', 'status', 1);
        $data['FeedbackForm'] = $this->common_model->get_FeedbackForm();
        $data['smtpDetails'] = $this->common_model->fetch_object_by_id('company_smtp', 'company_id', $user_id);
        $data['roleResult'] = $this->common_model->get_selected_values('company_roles', 'arid,	rolename', 'status=1 AND company_id='.$user_id);
        $data['step'] = $step;
        $this->load->view('company/view', $data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('a.id', 'a.id','it.description', 'a.company_code', 'a.company_name', 'a.portal_name');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $filter_industry_type = (isset($_GET['filter_industry_type']) ? $_GET['filter_industry_type'] : '');
        if ($filter_industry_type != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.industry_type_id  = " . $filter_industry_type;
            } else {
                $dtWhere .= " WHERE a.industry_type_id  = " . $filter_industry_type;
            }
        }
        $status = (isset($_GET['filter_status']) ? $_GET['filter_status'] : '');
        if ($status != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.status  = " . $status;
            } else {
                $dtWhere .= " WHERE a.status  = " . $status;
            }
        }
        $DTRenderArray = $this->company_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id','industry_type', 'company_code', 'company_name', 'portal_name', 'status', 'Actions');
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
                }else if ($dtDisplayColumns[$i] == "portal_name") {
                    $row[] = '<a target="_blank" href="https://' . $dtRow['portal_name'] . '.atomapp.in">'.$dtRow['portal_name'].'</a>';
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
                                        <a href="' . $site_url . 'company/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'company/edit/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_delete) {
                            $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\'' . $dtRow['company_name'] . '\',\'' . base64_encode($dtRow['id']) . '\');" href="javascript:void(0)">
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
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to Add,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            
            $this->load->library('form_validation');
            $data['username'] = $this->mw_session['username'];
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            $this->form_validation->set_rules('company_name', 'Company name', 'trim|required|max_length[50]|is_unique[company.company_name]');
            $this->form_validation->set_rules('otp', 'One Time Code', 'trim|required|max_length[50]|is_unique[company.otp]');
            $this->form_validation->set_rules('portal_name', 'Portal name', 'trim|required|max_length[50]|is_unique[company.portal_name]');
            $this->form_validation->set_rules('industry_type_id', 'Industry', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $now = date('Y-m-d H:i:s');
                
                //Upload Company Logo
                $companyLogo = '';
                if ($SuccessFlag && isset($_FILES['company_logo']['name']) && $_FILES['company_logo']['size'] > 0) {
                    $config     = array();
                    $companyLogo = time();
                    $config['upload_path']   = './assets/uploads/company';
                    $config['overwrite']     = FALSE;
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_width']     = 266;
                    $config['max_height']    = 144;
                    $config['min_width']     = 266;
                    $config['min_height']    = 144;
                    $config['file_name']     = $companyLogo;
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('company_logo')) {
                        $Message = $this->upload->display_errors();
                        $SuccessFlag = 0;
                    } else {
                        $ImgArrays    = explode('.', $_FILES['company_logo']['name']);
                        $companyLogo .="." . $ImgArrays[1];
                    }
                }
                
                $empcode_res = $this->input->post('empcode_restrict');
                if ($empcode_res != '') {
                    $EmpCodeRest = 1;
                } else {
                    $EmpCodeRest = 0;
                }
                $eotp_required = $this->input->post('eotp_required');
                $data = array(
                    'company_code' => strtoupper($this->input->post('company_code')),
                    'company_name' => ucwords($this->input->post('company_name')),
                    'portal_name' => strtolower($this->input->post('portal_name')),
                    'industry_type_id' => $this->input->post('industry_type_id'),
                    'eotp_required'=> (isset($eotp_required)? 1 :0),
                    //'corporate_partner' => $this->input->post('corporate_partner'),
                    'address_i' => $this->input->post('address_i'),
                    'address_ii' => $this->input->post('address_ii'),
                    'country_id' => $this->input->post('country_id'),
                    'state_id' => $this->input->post('state_id'),
                    'city_id' => $this->input->post('city_id'),
                    'pincode' => $this->input->post('pincode'),
                    'contact_no' => $this->input->post('contact_no'),
                    'contact_person' => $this->input->post('contact_person'),
                    'email' => $this->input->post('email'),
                    'website' => $this->input->post('website'),
                    'remarks' => $this->input->post('remarks'),
                    //'empcode' => $this->input->post('empcode'),
                    'otp' => $this->input->post('otp'),
                    'empcode_restrict' => $EmpCodeRest,
                    'company_logo'     => $companyLogo,
                    //                'users_restrict' => $this->input->post('users_restrict'),
                    //                'app_users_count' => $this->input->post('app_users_count'),
                    //                'portal_restrict' => $this->input->post('portal_restrict'),
                    //                'portal_users_count' => $this->input->post('portal_users_count'),
                    //                'empcode_restrict' => $EmpCodeRest,
                    //                'otp_required' => $this->input->post('otp_required'),                
                    //                'personal_form_required' => $this->input->post('personal_form_required'),
                    //                'form_id' => $this->input->post('form_id'),
                    //                'restrict_workshop' => $this->input->post('restrict_workshop'),
                    //                'workshop_count' => $this->input->post('workshop_count'),
                    //                'restrict_feedback' => $this->input->post('restrict_feedback'),
                    //                'feedback_count' => $this->input->post('feedback_count'),
                    //                'restrict_workshop_question' => $this->input->post('restrict_workshop_question'),
                    //                'workshop_question_count' => $this->input->post('workshop_question_count'),
                    //                'restrict_feedback_question' => $this->input->post('restrict_feedback_question'),
                    //                'feedback_question_count' => $this->input->post('feedback_question_count'),
                    //                'restrict_workshop_users' => $this->input->post('restrict_workshop_users'),
                    //                'workshop_users_count' => $this->input->post('workshop_users_count'),
                    //                'restrict_feedback_users' => $this->input->post('restrict_feedback_users'),
                    //                'feedback_users_count' => $this->input->post('feedback_users_count'),
                    'status' => $this->input->post('status'),
                    'addeddate' => $now,
                    'addedby' => $this->mw_session['user_id'],
                    'deleted' => 0
                );
                $cmp_id = $this->common_model->insert('company', $data);
                if ($cmp_id != "") {
                    $Message = "Company created successfully.";
                    $Rdata['id'] = base64_encode($cmp_id);
                } else {
                    $Message = "Error while creating Company,Contact Mediaworks for technical support.!";
                    $SuccessFlag = 0;
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function reset_smtp($Encoded_id){
        $cmp_id = base64_decode($Encoded_id);
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $this->common_model->delete('company_smtp', 'company_id', $cmp_id);
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function Smtp_save($Encoded_id) {
        $cmp_id = base64_decode($Encoded_id);
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            
            $this->load->library('form_validation');
            $this->form_validation->set_rules('host_name', 'Host name', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('smtp_secure', 'SMTP Secure', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('port_no', 'Port No', 'trim|required');
            $this->form_validation->set_rules('authentication', 'Authentication', 'trim|required');
            $this->form_validation->set_rules('user_name', 'User Name', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('alias_name', 'Alias Name', 'trim|required');
            $this->form_validation->set_rules('status', 'Status', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $now = date('Y-m-d H:i:s');
                $rowdata = $this->common_model->get_selected_values('company_smtp', 'smtp_id', 'company_id=' . $cmp_id);
                if (count((array)$rowdata) > 0) {
                    $data = array(
                        'smtp_ipadress' => $this->input->post('host_name'),
                        'smtp_secure' => $this->input->post('smtp_secure'),
                        'smtp_portno' => $this->input->post('port_no'),
                        'smtp_authentication' => $this->input->post('authentication'),
                        'smtp_username' => $this->input->post('user_name'),
                        'smtp_alias' => $this->input->post('alias_name'),
                        'smtp_password' => $this->input->post('password'),
                        'status' => $this->input->post('status'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id'],
                    );
                    $this->common_model->update('company_smtp', 'smtp_id', $rowdata[0]->smtp_id, $data);
                    $Message = "SMTP setting updated successfully.";
                } else {
                    $data = array(
                        'company_id' => $cmp_id,
                        'smtp_ipadress' => $this->input->post('host_name'),
                        'smtp_secure' => $this->input->post('smtp_secure'),
                        'smtp_portno' => $this->input->post('port_no'),
                        'smtp_authentication' => $this->input->post('authentication'),
                        'smtp_username' => $this->input->post('user_name'),
                        'smtp_alias' => $this->input->post('alias_name'),
                        'smtp_password' => $this->input->post('password'),
                        'status' => $this->input->post('status'),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                    $this->common_model->insert('company_smtp', $data);
                    $Message = "SMTP setting updated successfully.";
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function Testmail($Encoded_id) {
        $SuccessFlag = 1;
        $Message = '';
        $id = base64_decode($Encoded_id);
        $emailData = $this->common_model->get_value('company_smtp', 'smtp_id', 'status=1 and company_id=' . $id);
        if (count((array)$emailData) > 0) {
            $testmail = $this->input->post('testmail');
            $body = "This is a test email generated by the Awarathon SMTP .";
            $subject = " Test Mail";
            $ReturnData = $this->common_model->sendPhpMailer($id, 'Test Mail', $testmail, $subject, $body);
            if (!$ReturnData['sendflag']) {
                $Message = "Email sending failed,Please check smtp setting..<br/>";
                $Message .=$ReturnData['Msg'];
                $SuccessFlag = 0;
            } else {
                $Message = "Test Email sent success.";
            }
        } else {
            $Message = "Please enter valid smtp setting.";
            $SuccessFlag = 0;
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function update($Encoded_id) {
        $SuccessFlag = 1;
        $Message = '';
        $id = base64_decode($Encoded_id);
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            
            $this->load->library('form_validation');
            //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            $this->form_validation->set_rules('company_name', 'Company name', 'trim|required|max_length[50]');
            //$this->form_validation->set_rules('otp', 'One Time Code', 'trim|required|max_length[50]');
            //$this->form_validation->set_rules('portal_name', 'Portal name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('industry_type_id', 'Industry', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                
                $now          = date('Y-m-d H:i:s');
                $CompanyLogo  = $this->common_model->get_value("company", "company_logo", "id=" . $id);
                $company_logo = $CompanyLogo->company_logo;

                if ($SuccessFlag && isset($_FILES['company_logo']['name']) && $_FILES['company_logo']['size'] > 0) {
                $config  = array();
                $NewLogo = time();
                $config['upload_path']   = './assets/uploads/company';
                $config['overwrite']     = FALSE;
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_width']     = 266;
                $config['max_height']    = 144;
                $config['min_width']     = 266;
                $config['min_height']    = 144;
                $config['file_name']     = $NewLogo;
                
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('company_logo')) {
                    $Message = $this->upload->display_errors();
                    $SuccessFlag = 0;
                } else {
                    if ($company_logo != "") {
                        $Path = "./assets/uploads/company/" . $company_logo;
                        if (file_exists($Path)) {
                            unlink($Path);
                        }
                    }
                    $ImgArrays = explode('.', $_FILES['company_logo']['name']);
                    $company_logo = $NewLogo . "." . $ImgArrays[1];
                }
            }
            else {
                if ($this->input->post('removeLogo') !='0' && $company_logo !="") {
                    $Path = "./assets/uploads/company/" . $company_logo;
                    if (file_exists($Path)) {
                        unlink($Path);
                        $company_logo = '';
                    }
                }
            }
            if ($SuccessFlag) {
                $eotp_required = $this->input->post('eotp_required');
                $data = array(
                    'company_code'      => strtoupper($this->input->post('company_code')),
                    'company_name'      => ucwords($this->input->post('company_name')),
                    'industry_type_id'  => $this->input->post('industry_type_id'),
                    'eotp_required'=> (isset($eotp_required)? 1 :0),
                    'address_i'         => $this->input->post('address_i'),
                    'address_ii'        => $this->input->post('address_ii'),
                    'country_id'        => $this->input->post('country_id'),
                    'state_id'          => $this->input->post('state_id'),
                    'city_id'           => $this->input->post('city_id'),
                    'pincode'           => $this->input->post('pincode'),
                    'contact_no'        => $this->input->post('contact_no'),
                    'contact_person'    => $this->input->post('contact_person'),
                    'email'             => strtolower($this->input->post('email')),
                    'website'           => strtolower($this->input->post('website')),
                    'remarks'           => $this->input->post('remarks'),
                    'empcode_restrict'  => $this->input->post('empcode_restrict'),
                    'company_logo'      => $company_logo,
                    'status'            => $this->input->post('status'),
                    'modifieddate'      => $now,
                    'modifiedby'        => $this->mw_session['user_id'],
                    'deleted'           => 0,
                    'deviceuser_role'=>$this->input->post('deviceuser_role')
                    //'portal_name' => $this->input->post('portal_name'),
                    //'corporate_partner' => $this->input->post('corporate_partner'),
                    //'otp' => $this->input->post('otp'),
                );
  
                $this->common_model->update('company', 'id', $id, $data);
                $Message = "Company updated successfully";
            }
        }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function SettingUpdate($Encoded_id) {
        
        $SuccessFlag = 1;
        $Message = '';
        $id = base64_decode($Encoded_id);
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            //
            //$this->load->library('form_validation');//        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
//        $this->form_validation->set_rules('company_name', 'Company name', 'trim|required|max_length[50]');        
//        if ($this->form_validation->run() == FALSE) {
//            
//            $this->edit(base64_encode($id));
//            return;
//        } else {
            $now = date('Y-m-d H:i:s');
            $data = array(
                'users_restrict' => $this->input->post('users_restrict'),
                'app_users_count' => $this->input->post('app_users_count'),
                'portal_restrict' => $this->input->post('portal_restrict'),
                'portal_users_count' => $this->input->post('portal_users_count'),
                //'empcode_restrict' => $this->input->post('empcode_restrict'),
                //'empcode' => $this->input->post('empcode'),
                //'otp_required' => $this->input->post('otp_required'),
                //'otp' => $this->input->post('otp'),
                'personal_form_required' => $this->input->post('personal_form_required'),
                'form_id' => $this->input->post('form_id'),
                'restrict_workshop' => $this->input->post('restrict_workshop'),
                'workshop_count' => $this->input->post('workshop_count'),
                'restrict_feedback' => $this->input->post('restrict_feedback'),
                'feedback_count' => $this->input->post('feedback_count'),
                'restrict_workshop_question' => $this->input->post('restrict_workshop_question'),
                'workshop_question_count' => $this->input->post('workshop_question_count'),
                'restrict_feedback_question' => $this->input->post('restrict_feedback_question'),
                'feedback_question_count' => $this->input->post('feedback_question_count'),
                'restrict_workshop_users' => $this->input->post('restrict_workshop_users'),
                'workshop_users_count' => $this->input->post('workshop_users_count'),
                'restrict_feedback_users' => $this->input->post('restrict_feedback_users'),
                'feedback_users_count' => $this->input->post('feedback_users_count'),
                'modifieddate' => $now,
                'modifiedby' => $this->mw_session['user_id'],
                'deleted' => 0
            );
            $this->common_model->update('company', 'id', $id, $data);
            $Message = "Settings updated successfully";
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function remove() {
        $alert_type = 'success';
        $message = '';
        
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $deleted_id = base64_decode($this->input->Post('deleteid'));
            
            $StatusFlag = $this->company_model->CrosstableValidation($deleted_id);
            if ($StatusFlag) {
                $CompanyLogo = $this->common_model->get_value('company', 'id,company_logo', 'id=' .$deleted_id);
                
                if (count((array)$CompanyLogo) > 0) {
                    if ($CompanyLogo->company_logo != "") {
                        $Path = "./assets/uploads/company/" . $CompanyLogo->company_logo;
                        if (file_exists($Path)) {
                            unlink($Path);
                        }
                    }
                }
                $this->company_model->remove($deleted_id);
                $this->common_model->delete('company_smtp', 'company_id', $deleted_id);
                
                $message = "Company deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Company cannot be deleted. Reference of compnay found in other module!<br/>";
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

        if ($Action == 1) {
            foreach ($action_id as $id) {
                $data = array(
                    'status' => 1,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']);
                $this->common_model->update('company', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $StatusFlag = $this->company_model->CrosstableValidation($id);
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('company', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. Reference of company found in other module!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $DeleteFlag = $this->company_model->CrosstableValidation($id);
                if ($DeleteFlag) {
                    $this->common_model->delete('company', 'id', $id);
                    $this->common_model->delete('company_smtp', 'company_id', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Company cannot be deleted. Reference of company found in other module!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Company(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function Check_company() {
        $company = $this->input->post('company', true);
        $cmp_id = $this->input->post('cmp_id', true);
        if ($cmp_id != "") {
            $cmp_id = base64_decode($cmp_id);
        }
        echo $this->company_model->check_company($company, $cmp_id);
    }

    public function Check_portal() {
        $portal = $this->input->post('portal', true);
        $cmp_id = $this->input->post('cmp_id', true);
        if ($cmp_id != "") {
            $cmp_id = base64_decode($cmp_id);
        }
        echo $this->company_model->check_portal($portal, $cmp_id);
    }

    public function coname_validate() {
        $status = $this->company_model->coname_validate($_POST);
        echo $status;
    }

    public function portal_validate() {
        $status = $this->company_model->portal_validate($_POST);
        echo $status;
    }

    public function NewUserAdd($Encode_id, $Module) {
        $data['Company_id'] = base64_decode($Encode_id);
        $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 and company_id='.$data['Company_id']);
        if ($Module == 1) {
            $this->load->view('company/import_users', $data);
        } else {
            $data['AddEdit'] = 'A';
            $this->load->view('company/userform', $data);
        }
    }

    public function UserEdit($Encode_id) {
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('company');
            return;
        }
        $user_id = base64_decode($Encode_id);
        $data['AddEdit'] = 'E';
        $data['result'] = $this->common_model->fetch_object_by_id('device_users', 'user_id', $user_id);
        $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 and company_id='.$data['result']->company_id);
        $this->load->view('company/userform', $data);
    }

    function alpha_dash_space($str) {
        if (!preg_match("/^([-a-z0-9_ ])+$/i", $str)) {
            $this->form_validation->set_message('alpha_dash_space', 'The %s field may only contain alpha characters & White spaces');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function UpdateUserData($Encode_id, $User_id) {
        $acces_management = $this->acces_management;
        $Success = 1;
        $Msg = "";
        if ($User_id != 0) {
            if (!$acces_management->allow_edit) {
                $Msg = "You have no rights to Edit Users,Contact Administrator for rights.!";
                $Success = 0;
            }
        } else {
            if (!$acces_management->allow_add) {
                $Msg = "You have no rights to Add Users,Contact Administrator for rights.!";
                $Success = 0;
            }
        }
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('first_name', 'First name', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('last_name', 'Last name', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('emp_id', 'Employee Code', 'trim|required|max_length[50]|callback_alpha_dash_space');
        //$this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|max_length[50]'); 
        if ($User_id == 0) {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[15]');
        }
        if ($this->form_validation->run() == FALSE) {
            $Msg = validation_errors();
            $Success = 0;
        } else {
            $Company_id = base64_decode($Encode_id);
            $Email = $this->input->post('email');
            $emp_code = $this->input->post('emp_id');
            $EmailDuplicateCheck = $this->common_model->DuplicateEmail($Email, $Company_id, $User_id);
            $Emp_idDuplicateCheck = $this->company_model->DuplicateEmployeeCode($emp_code, $Company_id, $User_id);
            if (count((array)$EmailDuplicateCheck) > 0) {
                $Msg .= "Email Id Already exists.!<br/>";
                $Success = 0;
            } else if (count((array)$Emp_idDuplicateCheck) > 0) {
                $Msg .= "Employee Id Already exists.!<br/>";
                $Success = 0;
            } else {
                $now = date('Y-m-d H:i:s');
                $istester = $this->input->post('istester');
                $data = array(
                    'company_id' => $Company_id,
                    'emp_id' => $this->input->post('emp_id'),
                    'firstname' => ucwords($this->input->post('first_name')),
                    'lastname' => ucwords($this->input->post('last_name')),
                    'email' => strtolower($Email),
                    'mobile' => $this->input->post('mobile'),
                    'employment_year' => $this->input->post('empyear'),
                    'education_background' => $this->input->post('edubg'),
                    'department' => $this->input->post('depart'),
                    'region_id' => $this->input->post('region_id'),
                    'area' => $this->input->post('area'),
                    'registration_date' => $now,
                    'otp_verified' => 1,
                    'block' => 0,
                    'fb_registration' => 1,
                    'istester' => (isset($istester) ? 1 : 0),
                    'status' => $this->input->post('status'),
                );
                if ($User_id != 0) {
                    $this->common_model->update('device_users', 'user_id', $User_id, $data);
                    $Msg = "Device user updated successfully";
                } else {
                    $pwd = $this->input->post('password');
                    $data['password'] = $this->common_model->encrypt_password($pwd);
                    $Inserted_id =$this->common_model->insert('device_users', $data);
                    if($Inserted_id !=""){
                        $Msg = "Device user added successfully";
                        $Udata=array('token'=>$Inserted_id.".".base64_encode(mcrypt_create_iv(32)));
                        $this->common_model->update('device_users', 'user_id', $Inserted_id, $Udata);
                    }else{
                        $Msg = "Error while creating Device users,Contact Mediaworks for technical support.!";
                        $Success = 0;
                    }
                    
                    
                }
            }
        }
        $Rdata['success'] = $Success;
        $Rdata['Msg'] = $Msg;
        echo json_encode($Rdata);
    }

    public function RemoveDeviceUser($deleted_id) {
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $OldData = $this->common_model->get_value('workshop_registered_users', 'id', "user_id=".$deleted_id);
            if(count((array)$OldData)>0){
                $message = "User cannot be deleted. Reference of Trainee found in other module!<br/>";
                $alert_type = 'error';
            }else{
                $this->common_model->delete('device_users', 'user_id', $deleted_id);
                $message = "User deleted successfully.";
            }
            
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function userssamplexls() {
        $this->load->library('PHPExcel_CI');
        $Excel = new PHPExcel_CI;
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('User_List');
        $Excel->createSheet();
        $Excel->getActiveSheet()
                ->setCellValue('A1', 'Do not modify or delete the Columns.');
        $Excel->getActiveSheet()->getStyle('A1:D1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FF0000'
            )
        ));
        //merge cell A1 until D1
        $Excel->getActiveSheet()->mergeCells('A1:D1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
        ));
        $Excel->getActiveSheet()
                ->setCellValue('A2', 'Employee Code*')
                ->setCellValue('B2', 'First Name*')
                ->setCellValue('C2', 'Last Name*')
                ->setCellValue('D2', 'Email*')
                ->setCellValue('E2', 'Password*')
                ->setCellValue('F2', 'Mobile No.')
                ->setCellValue('G2', 'Employment Year')
                ->setCellValue('H2', 'Education Background')
                ->setCellValue('I2', 'Department/Division')
                ->setCellValue('J2', 'Region/Branch')
                ->setCellValue('K2', 'Area');


        $Excel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:K2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('B')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('C')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('D')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('E')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('F')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('G')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('H')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('I')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('J')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('K')->setWidth("30");
        $Excel->getActiveSheet()->getStyle('A2:K2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'eb3a12'
            )
        ));
        //set aligment to center for that merged cell (A1 to D1)
        $filename = "Device_User_Import.xls";
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
       if (ob_get_length()) ob_end_clean();
        $objWriter->save('php://output');
    }

    public function file_check($str) {
        $allowed_mime_type_arr = array('application/excel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/octet-stream');
        $mime = $_FILES['filename']['type'];
        if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != "") {
            if (in_array($mime, $allowed_mime_type_arr)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only .xlsx or.xls file.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please select xls to import.');
            return false;
        }
    }

    public function UploadUsersXls($Encode_id) {
        $Message = '';
        $SuccessFlag = 1;
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
        } else {
            
            $this->load->library('form_validation');
            $this->form_validation->set_rules('filename', '', 'callback_file_check');
        }
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            $FileData = $_FILES['filename'];
            $Error = '';
            $this->load->library('PHPExcel_CI');
            $objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumm = $worksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
            if ($highestRow < 2) {
                $Message .= "Excel row/column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }if ($highestRow == 2) {
                $Message .= "Excel file cannot be empty.";
                $SuccessFlag = 0;
            }
            if ($highestColumnIndex < 6) {
                $Message .= "Excel column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                $Company_id = base64_decode($Encode_id);
                for ($row = 3; $row <= $highestRow; $row++) {
                    $Emp_code = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    if ($Emp_code == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Employee Code is Empty. </br> ";
                        continue;
                    }
                    $First_name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    if ($First_name == '') {
                        continue;
                    }
                    $Last_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    if ($Last_name == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Last Name is Empty. </br> ";
                        continue;
                    }
                    $Email = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    if ($Email == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Email is Empty. </br> ";
                        continue;
                    }
                    $Pwd = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    if ($Pwd == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Password is Empty. </br> ";
                        continue;
                    }
//                    $Mobile=$worksheet->getCellByColumnAndRow(3, $row)->getValue();                    
//                    if($Mobile=='') {
//                        $SuccessFlag=0;
//                        $Message .= "Row No. $row, Mobile No. is Empty. </br> ";
//                        continue;
//                    }

                    $EmailDuplicateCheck = $this->common_model->DuplicateEmail($Email, $Company_id);
                    if (count((array)$EmailDuplicateCheck) > 0) {
                        $Message .= "Row No. $row,Email Id Already exists.!<br/>";
                        $SuccessFlag = 0;
                        continue;
                    }
                    $Emp_idDuplicateCheck = $this->company_model->DuplicateEmployeeCode($Emp_code, $Company_id);
                    if (count((array)$Emp_idDuplicateCheck) > 0) {
                        $Message .= "Row No. $row,Employee Id Already exists.!<br/>";
                        $SuccessFlag = 0;
                        continue;
                    }
                    $region = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                    if($region!=''){
                    $regionId = $this->common_model->get_value('region', 'id', "region_name LIKE '".$region."' AND company_id=".$Company_id);                    
                    if (count((array)$regionId)== 0) {
                        $Message .= "Invalid Region,Please enter valid Region!<br/>";
                        $SuccessFlag = 0;
                        continue;
                    }
                   }
                }
            }
            if ($SuccessFlag) {
                $now = date('Y-m-d H:i:s');
                $Counter = 0;
                for ($row = 3; $row <= $highestRow; $row++) {
                    $First_name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $pwd = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $region = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                    if($region!=''){
                    $regionId = $this->common_model->get_value('region', 'id', "region_name LIKE '".$region."' AND company_id=".$Company_id);
                    $regionId=$regionId->id;
                    }else{
                        $regionId=0;
                    }
                    $Counter++;
                    $data = array(
                        'company_id' => $Company_id,
                        'emp_id' => $worksheet->getCellByColumnAndRow(0, $row)->getFormattedValue(),
                        'firstname' => $worksheet->getCellByColumnAndRow(1, $row)->getFormattedValue(),
                        'lastname' => $worksheet->getCellByColumnAndRow(2, $row)->getFormattedValue(),
                        'email' => $worksheet->getCellByColumnAndRow(3, $row)->getFormattedValue(),
                        'password' => $this->common_model->encrypt_password($pwd),
                        'mobile' => $worksheet->getCellByColumnAndRow(5, $row)->getFormattedValue(),
                        'employment_year' => $worksheet->getCellByColumnAndRow(6, $row)->getFormattedValue(),
                        'education_background' => $worksheet->getCellByColumnAndRow(7, $row)->getFormattedValue(),
                        'department' => $worksheet->getCellByColumnAndRow(8, $row)->getFormattedValue(),
                        'region_id' => $regionId,
                        'otp_verified' => 1,
                        'area' => $worksheet->getCellByColumnAndRow(10, $row)->getFormattedValue(),
                        'status' => 1,
                        'registration_date' => $now,
                    );
                    $Inserted_id =$this->common_model->insert('device_users', $data);
                    $Udata=array('token'=>$Inserted_id.".".base64_encode(mcrypt_create_iv(32)));
                    $this->common_model->update('device_users', 'user_id', $Inserted_id, $Udata);
                }
                $Message = $Counter . " Device Users Imported successfully.";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function UsersDatatableRefresh($Encode_id) {

        $Company_id = base64_decode($Encode_id);
        $dtSearchColumns = array('u.user_id', 'u.user_id', 'u.emp_id', 'u.firstname', 'u.email', 'u.mobile','u.otp','u.otp_last_attempt','u.area', 'u.istester', 'status', 'u.lastname');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($dtWhere <> '') {
            $dtWhere .= " AND u.company_id  = " . $Company_id;
        } else {
            $dtWhere .= " WHERE u.company_id  = " . $Company_id;
        }
        $testerfilter = (isset($_GET['testerfilter']) ? $_GET['testerfilter'] : 'false');
        if ($testerfilter == 'true') {
            $dtWhere .= " AND u.istester  = 1";
        }
        $DTRenderArray = $this->company_model->LoadUsersDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'user_id', 'emp_id', 'name', 'email', 'mobile','otp','otp_last_attempt','area', 'istester', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "otp_last_attempt") {
                    $row[] = ($dtRow['otp_last_attempt'] !='' ? date('d-m-Y H:i', strtotime($dtRow['otp_last_attempt'])) :'');
                }
                elseif ($dtDisplayColumns[$i] == "status") {
                    if ($dtRow['status'] == 1) {
                        $status = '<span class="label label-sm label-info status-active" > Active </span>';
                    } else {
                        $status = '<span class="label label-sm label-danger status-inactive" > In Active </span>';
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['user_id'] . '"/>
                                <span></span>
                            </label>';
                } elseif ($dtDisplayColumns[$i] == "istester") {
                    if ($dtRow['istester']) {
                        $Testerstatus = 'Yes';
                    } else {
                        $Testerstatus = '';
                    }
                    $row[] = $Testerstatus;
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if ($acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a  href="' . $site_url . 'company/UserEdit/' . base64_encode($dtRow['user_id']) . '" 
                                                    data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-pencil"></i>&nbsp;Edit </a>
                                    </li>';
                        }
                        if ($acces_management->allow_delete) {
                            $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\'' . $dtRow['user_id'] . '\');" href="javascript:void(0)">
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
    public function send_otp($Encode_id,$type){        
        $Company_id = base64_decode($Encode_id);
        $action_id = $this->input->Post('id');
        if($type == 1){
        $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='on_otp_request'");
        }else{
            $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='on_otc_request'");
        }
        $SuccessFlag = 1;
        $Message = '';
        $CompanyData = $this->common_model->get_value('company', 'otp', "id=".$Company_id);
        if(count((array)$emailTemplate)>0){
            foreach ($action_id as $id) {
                $UserData =$this->common_model->get_value('device_users', 'firstname,email', "status=1 AND user_id=".$id);
                if(count((array)$UserData)>0){
                    $pattern[0]          = '/\[CUSTOMER_NAME\]/';
                    $pattern[1]          = '/\[ONETIME_CODE\]/';
                    $pattern[2]          = '/\[SUBJECT\]/';
                    $replacement[0]      = $UserData->firstname;
                    if($type == 1){
                        $six_digit_otp = mt_rand(100000, 999999);                   
                    }else{
                        $six_digit_otp = $CompanyData->otp;
                    }
                    $replacement[1]      = $six_digit_otp;
                    $subject =$emailTemplate->subject;
                    $replacement[2] = $subject;
                    $message   = $emailTemplate->message;
                    $body =  preg_replace($pattern, $replacement, $message);
                    $ToName =$UserData->firstname;
                    $recipient =$UserData->email;
                    //$Company_id ='';
                    $ReturnArray = $this->common_model->sendPhpMailer($Company_id,$ToName,$recipient, $subject, $body);
                    if($ReturnArray['sendflag']){
                        if($type == 1){
                            $Message = "OTP send successfully.";
                            $data=array('otp'=>$six_digit_otp,
                                'modifieddate' => date('Y-m-d H:i:s'),
                                'otp_last_attempt'=> date('Y-m-d H:i:s'),
                                'modifiedby' => $this->mw_session['user_id']);
                            $this->common_model->update(' device_users', 'user_id', $id, $data);
                    }else{
                            $Message = "OTC send successfully.";
                        }
                    }else{
                        $Message .= "Error while sending email,Plese try again..";
                        $Message .= '<br/>'.$ReturnArray['sendflag'];
                        $SuccessFlag=0;
                    }
                }else{
                    $Message .=" some user data not found..";
                }
            }
        }else{
            $SuccessFlag = 0;
            $Message = 'Email Template not defined,Contact Adminstrator for technical support';
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function export_trainee($Encode_id){
        $Company_id = base64_decode($Encode_id);
        $this->load->library('PHPExcel_CI');
        $objPHPExcel = new PHPExcel_CI();
        $objPHPExcel->setActiveSheetIndex(0);
        
        $objPHPExcel->getActiveSheet()
                ->setCellValue('A2', 'Employee Code')
                ->setCellValue('B2', 'First Name')
                ->setCellValue('C2', 'Last Name')
                ->setCellValue('D2', 'Email')
                ->setCellValue('E2', 'Mobile No.')
                ->setCellValue('F2', 'Employment Year')
                ->setCellValue('G2', 'Education Background')
                ->setCellValue('H2', 'Department/Division')
                ->setCellValue('I2', 'Region/Branch')
                ->setCellValue('J2', 'Area')
                ->setCellValue('K2', 'Status');
        
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A2:K2')->applyFromArray($styleArray_header);
        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $i = 2;
        if ($this->input->post('id') != NULL) {
            $id_list = implode(',', $this->input->post('id'));
            $TraineeSet = $this->company_model->ExportDeviceUsers($Company_id,$id_list);
        } else {
            $TraineeSet = $this->company_model->ExportDeviceUsers($Company_id);
        }
        $j=0;
        foreach ($TraineeSet as $Trainee) {
            $i++;
            $j++;
            $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $Trainee->emp_id)
                    ->setCellValue("B$i", $Trainee->firstname)
                    ->setCellValue("C$i", $Trainee->lastname)
                    ->setCellValue("D$i", $Trainee->email)
                    ->setCellValue("E$i", $Trainee->mobile)
                    ->setCellValue("F$i", $Trainee->employment_year)
                    ->setCellValue("G$i", $Trainee->education_background)
                    ->setCellValue("H$i", $Trainee->department)
                    ->setCellValue("I$i", $Trainee->region_name)
                    ->setCellValue("J$i", $Trainee->area)
                    ->setCellValue("K$i", ($Trainee->status ? 'Active':'In-Active'));
            $objPHPExcel->getActiveSheet()->getStyle("A$i:K$i")->applyFromArray($styleArray_body);
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet

        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="TraineeExports.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        if (ob_get_length()) ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }
    
}

