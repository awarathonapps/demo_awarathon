<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Workshop extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('workshop');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('workshop_model');
    }

    public function ajax_populate_question_set() {
        return $this->common_model->fetch_question_set_data($this->input->get());
    }

    public function ajax_populate_feedback() {
        return $this->common_model->fetch_feedback_data();
    }

    public function index() {
        $data['module_id'] = '4.05';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        $wcreate_lock = false;
        if ($Company_id == "") {
            $data['cmpdata'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['cmpdata'] = array();
            $data['WorkshopType'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 AND company_id=' . $Company_id);
            $data['Region'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 AND company_id=' . $Company_id);
            $wcreate_lock = $this->check_workshop_restrict($Company_id);
        }
        $data['Company_id'] = $Company_id;
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $data['wcreate_lock'] = $wcreate_lock;
        $this->load->view('workshop/index', $data);
    }

    public function create($errors = "") {
        $data['module_id'] = '4.05';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('workshop');
            return;
        }

        $data['errors'] = $errors;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmpdata'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
            $data['WorkshopType'] = array();
            $data['Region'] = array();
            $data['feedback_form'] = array();
            $data['RewardResult'] = array();
            $data['feedbackset_Qresult'] = array();
            $data['question_Qresult'] = array();
            $data['df_trainer_list'] = array();
        } else {
            if ($this->check_workshop_restrict($Company_id)) {
                redirect('workshop');
            }

            $data['cmpdata'] = array();
            $data['WorkshopType'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 AND company_id=' . $Company_id);
            $data['WorkshopSubType'] = $this->common_model->get_selected_values('workshopsubtype_mst', 'id,description as sub_type', 'company_id=' . $Company_id);
            $data['Region'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 AND company_id=' . $Company_id);
            $data['SubRegion'] = $this->common_model->get_selected_values('workshopsubregion_mst', 'id,description as sub_region', 'company_id=' . $Company_id);
            $data['feedback_form'] = $this->common_model->get_selected_values('feedback_form_header', 'id,form_name', 'status=1 AND company_id=' . $Company_id);
            $data['RewardResult'] = $this->common_model->get_selected_values('reward', 'id,reward_name', 'status=1 AND company_id=' . $Company_id);
            $data['feedbackset_Qresult'] = $this->common_model->get_selected_values('feedback', 'title,id', 'status=1 AND company_id=' . $Company_id);
            $data['question_Qresult'] = $this->common_model->get_selected_values('question_set', 'title,id', 'status=1 AND company_id=' . $Company_id);
            $data['df_trainer_list'] = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as trainer', 'status=1 and company_id=' . $Company_id);
        }
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $data['Company_id'] = $Company_id;
        $data['Session_code'] = $this->common_model->ajax_genratepassword();
        $this->load->view('workshop/create', $data);
    }

    public function edit($id, $step = 1, $errors = "") {
        $Workshop_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('workshop');
            return;
        }
        $data['errors'] = $errors;
        $data['module_id'] = '4.05';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['Row'] = $this->common_model->fetch_object_by_id('workshop', 'id', $Workshop_id);
        $TodayData = strtotime(date('Y-m-d H:i'));
        $PreSessionStartDisabled = false;
        $PreSessionEndDisabled = false;
        $Edit_lock = false;
        $PlayedData = $this->common_model->get_value('atom_results', 'id', 'workshop_id =' . $Workshop_id . " and workshop_session='PRE'");
        $FeedbackPlayedData = $this->common_model->get_value('atom_feedback', 'id', 'workshop_id =' . $Workshop_id . " and workshop_session='PRE'");
        if (count((array)$PlayedData) == 0 && count((array)$FeedbackPlayedData) == 0) {
            $PreSessionStartDisabled = false;
            $PreSessionEndDisabled = false;
        } else {
            $PreSessionStartDisabled = true;
            if ($data['Row']->pre_end_date != "1970-01-01") {
                $PreData = strtotime($data['Row']->pre_end_date . ' ' . $data['Row']->pre_end_time);
                if ($data['Row']->post_start_date != "1970-01-01") {
                    $PostData = strtotime($data['Row']->post_start_date . ' ' . $data['Row']->post_start_time);
                    if ($PreData >= $PostData || $PostData <= $TodayData) {
                        $PreSessionEndDisabled = true;
                    }
                }
                if ($PreData <= $TodayData) {
                    $PreSessionEndDisabled = true;
                }
            }
        }

        if ((count((array)$PlayedData) > 0 || count((array)$FeedbackPlayedData) > 0) && $data['Row']->pre_time_status) {
            $PreSessionStartDisabled = true;
            $PreSessionEndDisabled = true;
        }
        $PostSessionStartDisabled = false;
        $PlayedData = $this->common_model->get_value('atom_results', 'id', 'workshop_id =' . $Workshop_id . " and workshop_session='POST'");
        $FeedbackPlayedData = $this->common_model->get_value('atom_feedback', 'id', 'workshop_id =' . $Workshop_id . " and workshop_session='POST'");
        $PostSessionEndDisabled = false;
        if ($data['Row']->post_end_date != "1970-01-01") {
            $PreData = strtotime($data['Row']->post_end_date . ' ' . $data['Row']->post_end_time);
            if ($PreData <= $TodayData) {
                $PostSessionEndDisabled = true;
            }
        }
        if (count((array)$PlayedData) == 0 && count((array)$FeedbackPlayedData) == 0) {
            $PostSessionStartDisabled = false;
            $PostSessionEndDisabled = false;
        } else {
            $PostSessionStartDisabled = true;
            if ($data['Row']->post_time_status) {
                $PostSessionStartDisabled = true;
                $PostSessionEndDisabled = true;
            }
        }
        if ($PreSessionEndDisabled && $PostSessionEndDisabled) {
            $Edit_lock = true;
        }
        $data['Edit_lock'] = $Edit_lock;
        $data['PreSessionStartDisabled'] = $PreSessionStartDisabled;
        $data['PreSessionEndDisabled'] = $PreSessionEndDisabled;
        $data['PostSessionStartDisabled'] = $PostSessionStartDisabled;
        $data['PostSessionEndDisabled'] = $PostSessionEndDisabled;

        $company_id = $this->mw_session['company_id'];
        $data['Company_id'] = $company_id;
        if ($company_id == "") {
            $data['SelectCompany'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
            $company_id = $data['Row']->company_id;
        } else {
            $data['SelectCompany'] = array();
            if ($company_id != $data['Row']->company_id) {
                redirect('workshop');
                return;
            }
        }

        $data['WorkshopType'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'company_id=' . $company_id);
        $data['Region'] = $this->common_model->get_selected_values('region', 'company_id,id,region_name', 'company_id=' . $company_id);

        $data['WorkshopSubType'] = $this->common_model->get_selected_values('workshopsubtype_mst', 'id,description as sub_type', 'workshoptype_id=' . $data['Row']->workshop_type);
        $data['SubRegion'] = $this->common_model->get_selected_values('workshopsubregion_mst', 'id,description as sub_region', 'region_id=' . $data['Row']->region);
        $data['BannerImageSet'] = $this->common_model->fetch_object_by_field('workshop_banner', 'workshop_id', $Workshop_id);
        //$data['SelectCompany'] = $this->workshop_model->SelectedCompany($Workshop_id);

        $data['FeedbackForm'] = $this->workshop_model->SelectedFeedbackForm($Workshop_id, $company_id);
        $data['pre_SelectQuestionSet'] = $this->workshop_model->pre_SelectedQuestionSet($Workshop_id, 1, $company_id);

        $data['pre_QuestionSetStatus'] = $this->workshop_model->pre_QuestionSetStatus($Workshop_id, 1);
        $data['post_QuestionSetStatus'] = $this->workshop_model->post_QuestionSetStatus($Workshop_id, 1);

        $data['post_SelectQuestionSet'] = $this->workshop_model->post_SelectedQuestionSet($Workshop_id, 1, $company_id);

        $data['pre_FeedbackSetStatus'] = $this->workshop_model->pre_QuestionSetStatus($Workshop_id, 2);
        $data['post_FeedbackSetStatus'] = $this->workshop_model->post_QuestionSetStatus($Workshop_id, 2);

        $data['pre_SelectFeedbackSet'] = $this->workshop_model->pre_SelectedQuestionSet($Workshop_id, 2, $company_id);
        $data['post_SelectFeedbackSet'] = $this->workshop_model->post_SelectedQuestionSet($Workshop_id, 2, $company_id);

        //$data['SelectFeedBack'] = $this->workshop_model->SelectedFeedBack($Workshop_id, $data['Row']->company_id);
        $data['SelectReward'] = $this->workshop_model->SelectedReward($Workshop_id, $company_id);
        $data['step'] = $step;
        $data['Session_code'] = $this->common_model->ajax_genratepassword();
        $data['df_trainer_list'] = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as trainer', 'status=1 and company_id=' . $company_id);
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $this->load->view('workshop/edit', $data);
    }

    public function copy($id, $step = 1, $errors = "") {
        $Workshop_id = base64_decode($id);
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('workshop');
            return;
        }
        $data['errors'] = $errors;
        $data['module_id'] = '4.05';
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        //$data['WorkshopType']     = $this->common_model->get_selected_values('workshoptype_mst','id,workshop_type','status=1');
        //$data['Region']           = $this->common_model->get_selected_values('region','id,region_name','status=1');
        //$data['StartTime'] = $this->common_model->get_value('workshop', 'start_time', 'id =' . $Workshop_id . ' and status=1');
        //$data['EndTime'] = $this->common_model->get_value('workshop', 'end_time', 'id =' . $Workshop_id . ' and status=1');
        $data['Row'] = $this->common_model->fetch_object_by_id('workshop', 'id', $Workshop_id);

        $company_id = $this->mw_session['company_id'];
        $data['Company_id'] = $company_id;
        if ($company_id == "") {
            $data['SelectCompany'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
            $company_id = $data['Row']->company_id;
        } else {
            $data['SelectCompany'] = array();
            if ($this->check_workshop_restrict($company_id)) {
                redirect('workshop');
            }
        }


        $data['WorkshopType'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'company_id=' . $company_id);
        $data['Region'] = $this->common_model->get_selected_values('region', 'company_id,id,region_name', 'company_id=' . $company_id);
        $data['WorkshopSubType'] = $this->common_model->get_selected_values('workshopsubtype_mst', 'id,description as sub_type', 'workshoptype_id=' . $data['Row']->workshop_type);
        $data['SubRegion'] = $this->common_model->get_selected_values('workshopsubregion_mst', 'id,description as sub_region', 'region_id=' . $data['Row']->region);
        $data['BannerImageSet'] = $this->common_model->fetch_object_by_field('workshop_banner', 'workshop_id', $Workshop_id);
        //$data['SelectCompany'] = $this->workshop_model->SelectedCompany($Workshop_id);

        $data['FeedbackForm'] = $this->workshop_model->SelectedFeedbackForm($Workshop_id, $company_id);
        $data['pre_SelectQuestionSet'] = $this->workshop_model->pre_SelectedQuestionSet($Workshop_id, 1, $company_id);

        $data['pre_QuestionSetStatus'] = $this->workshop_model->New_pre_QuestionSetStatus($Workshop_id, 1);
        $data['post_QuestionSetStatus'] = $this->workshop_model->New_post_QuestionSetStatus($Workshop_id, 1);

        $data['post_SelectQuestionSet'] = $this->workshop_model->post_SelectedQuestionSet($Workshop_id, 1, $company_id);

        $data['pre_FeedbackSetStatus'] = $this->workshop_model->New_pre_QuestionSetStatus($Workshop_id, 2);
        $data['post_FeedbackSetStatus'] = $this->workshop_model->New_post_QuestionSetStatus($Workshop_id, 2);

        $data['pre_SelectFeedbackSet'] = $this->workshop_model->pre_SelectedQuestionSet($Workshop_id, 2, $company_id);
        $data['post_SelectFeedbackSet'] = $this->workshop_model->post_SelectedQuestionSet($Workshop_id, 2, $company_id);


        //$data['SelectFeedBack'] = $this->workshop_model->SelectedFeedBack($Workshop_id, $data['Row']->company_id);
        $data['SelectReward'] = $this->workshop_model->SelectedReward($Workshop_id, $company_id);
        $data['Session_code'] = $this->common_model->ajax_genratepassword();
        $data['step'] = $step;
        $data['df_trainer_list'] = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as trainer', 'status=1 and company_id=' . $company_id);
        $this->load->view('workshop/copy', $data);
    }

    public function view($id, $step = 1, $errors = "") {
        $Workshop_id = base64_decode($id);
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('workshop');
            return;
        }
        $data['language_mst'] = $this->common_model->get_selected_values('language_mst', 'id,name', 'status=1');
        $data['errors'] = $errors;
        $data['module_id'] = '4.05';
        $data['Row'] = $this->common_model->fetch_object_by_id('workshop', 'id', $Workshop_id);
        $company_id = $data['Row']->company_id;
        $data['WorkshopType'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'company_id=' . $company_id);
        $data['Region'] = $this->common_model->get_selected_values('region', 'company_id,id,region_name', 'company_id=' . $company_id);
        $data['WorkshopSubType'] = $this->common_model->get_selected_values('workshopsubtype_mst', 'id,description as sub_type', 'company_id=' . $company_id);
        $data['SubRegion'] = $this->common_model->get_selected_values('workshopsubregion_mst', 'id,description as sub_region', 'company_id=' . $company_id);
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['SelectCompany'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['SelectCompany'] = array();
        }
        $data['Company_id'] = $Company_id;

        $data['BannerImageSet'] = $this->common_model->fetch_object_by_field('workshop_banner', 'workshop_id', $Workshop_id);
        //$data['SelectCompany'] = $this->workshop_model->SelectedCompany($Workshop_id);

        $data['FeedbackForm'] = $this->workshop_model->SelectedFeedbackForm($Workshop_id, $company_id);
        $data['pre_SelectQuestionSet'] = $this->workshop_model->pre_SelectedQuestionSet($Workshop_id, 1, $company_id);

        $data['pre_QuestionSetStatus'] = $this->workshop_model->pre_QuestionSetStatus($Workshop_id, 1);
        $data['post_QuestionSetStatus'] = $this->workshop_model->post_QuestionSetStatus($Workshop_id, 1);

        $data['post_SelectQuestionSet'] = $this->workshop_model->post_SelectedQuestionSet($Workshop_id, 1, $company_id);

        $data['pre_FeedbackSetStatus'] = $this->workshop_model->pre_QuestionSetStatus($Workshop_id, 2);
        $data['post_FeedbackSetStatus'] = $this->workshop_model->post_QuestionSetStatus($Workshop_id, 2);

        $data['pre_SelectFeedbackSet'] = $this->workshop_model->pre_SelectedQuestionSet($Workshop_id, 2, $company_id);
        $data['post_SelectFeedbackSet'] = $this->workshop_model->post_SelectedQuestionSet($Workshop_id, 2, $company_id);

        $data['SelectReward'] = $this->workshop_model->SelectedReward($Workshop_id, $company_id);
        $data['step'] = $step;

        $this->load->view('workshop/view', $data);
    }

    public function UploadBanner($Encoded_id) {
        $Workshop_id = base64_decode($Encoded_id);
        $this->load->library('upload');
        $ImageFormat = explode('.', $_FILES['file']['name']);
        $NewImageName = time();
        $upload_path = './assets/uploads/workshop/banners';
        $asset_url = $this->config->item('assets_url');
        $this->upload->initialize($this->set_upload_options($NewImageName, $upload_path));
        $errorFlag = true;
        $image = '';
        if (!$this->upload->do_upload('file')) {
            $response['result'] = array('error' => $this->upload->display_errors());
            $errorFlag = false;
        }
        if ($errorFlag) {
            $MaxRowSet = $this->common_model->get_value('workshop_banner', 'max(sorting) as sorting', 'workshop_id=' . $Workshop_id);
            $image = $NewImageName . '.' . $ImageFormat[1];
            $MaxNo = $MaxRowSet->sorting + 1;
            $data = array(
                'workshop_id' => $Workshop_id,
                'sorting' => $MaxNo,
                'thumbnail_image' => $image);
            $insertId = $this->common_model->insert('workshop_banner', $data);
            $response['result'] = 'OK';
            $image = $asset_url . 'assets/uploads/workshop/banners/' . $image;
            $response['NewId'] = $insertId;
            $response['NewSortNo'] = $MaxNo;
        }
        $response['image'] = $image;
        echo json_encode($response);
    }

    public function RemoveBanner() {
        $ImageId = $this->input->Post('ImageId');
        $Image = $this->common_model->get_value('workshop_banner', 'thumbnail_image', 'id=' . $ImageId);
        $upload_path = './assets/uploads/workshop/banners/';
        $Path = $upload_path . $Image->thumbnail_image;
        if (file_exists($Path)) {
            unlink($Path);
        }
        $this->common_model->delete('workshop_banner', 'id', $ImageId);
        echo true;
    }

    private function set_upload_options($ImageName, $upload_path) {
        //upload an image options
        $config = array();
        $config['upload_path'] = $upload_path;
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
        $dtSearchColumns = array('a.id', 'a.id', 'b.company_name', 'r.region_name', 'wt.workshop_type', 'a.workshop_name', 'a.otp', 'a.start_date', 'a.end_date', 'a.workshop_image', 'a.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $wcreate_lock = false;
        $now = date('Y-m-d H:i:s');
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->get('company_id') ? $this->input->get('company_id') : '';
        } else {
            $company_id = $this->mw_session['company_id'];
            if ($this->check_workshop_restrict($company_id)) {
                $wcreate_lock = true;
            }
        }
        if ($company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.company_id  = " . $company_id;
            } else {
                $dtWhere .= " WHERE a.company_id  = " . $company_id;
            }
        }
        $status = $this->input->get('status') ? $this->input->get('status') : '';
        if ($status == "1") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.end_date >= '" . $now . "'";
            } else {
                $dtWhere .= " WHERE a.end_date  >= '" . $now . "'";
            }
        } elseif ($status == "2") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.end_date < '" . $now . "'";
            } else {
                $dtWhere .= " WHERE a.end_date  < '" . $now . "'";
            }
        } elseif ($status == "3") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.start_date > '" . $now . "' AND a.status = 1";
            } else {
                $dtWhere .= " WHERE a.start_date > '" . $now . "' AND a.status = 1";
            }
        } elseif ($status == "4") {

            if ($dtWhere <> '') {
                $dtWhere .= " AND a.status = 0";
            } else {
                $dtWhere .= " WHERE  a.status = 0";
            }
        }
        $region_id = $this->input->get('region_id') ? $this->input->get('region_id') : '';
        if ($region_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.region  = " . $region_id;
            } else {
                $dtWhere .= " WHERE a.region  = " . $region_id;
            }
        }
        $wtype_id = $this->input->get('wtype_id') ? $this->input->get('wtype_id') : '';
        if ($wtype_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.workshop_type  = " . $wtype_id;
            } else {
                $dtWhere .= " WHERE a.workshop_type  = " . $wtype_id;
            }
        }
        $Start_date = $this->input->get('start_date') ? $this->input->get('start_date') : '';
        $End_date = $this->input->get('end_date') ? $this->input->get('end_date') : '';
        if ($Start_date != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.start_date between '" . date('Y-m-d', strtotime($Start_date)) .
                        "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
            } else {
                $dtWhere .= " WHERE a.start_date between '" . date('Y-m-d', strtotime($Start_date)) .
                        "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
            }
        }
        $DTRenderArray = $this->workshop_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'id', 'company_name', 'region_name', 'workshop_type', 'workshop_name', 'otp', 'start_date', 'end_date', 'workshop_image', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        if ($this->mw_session['company_id'] == "") {
            $asset_url = base_url();
        } else {
            $asset_url = $this->config->item('assets_url');
        }
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {

            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $Curr_Time = strtotime($now);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "status") {
                    if (strtotime($dtRow['start_dt_time']) >= $Curr_Time) {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-info status-active" > Active </span>';
                        } else {
                            $status = '<span class="label label-sm label-danger status-active" > In-Active </span>';
                        }
                    } else if (strtotime($dtRow['end_dt_time']) >= $Curr_Time) {
                        $status = '<span class="label label-sm  label-success " style="background-color: #5cb85c;" > Live </span>';
                    } else {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-danger " > Expired </span>';
                        } else {
                            $status = '<span class="label label-sm label-danger status-active" > In-Active </span>';
                        }
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "workshop_image") {
                    $row[] = '<img src="' . $asset_url . 'assets/uploads/' . ($dtRow['workshop_image'] != '' ? 'workshop/' . $dtRow['workshop_image'] : 'no_image.png') . '" class="thumbnail" width="80px" height="40px">';
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
                                        <a href="' . $site_url . 'workshop/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'workshop/edit/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_add) {

                            $action .= '<li>
                                        <a href="' . ($wcreate_lock ? 'javascript:void(ShowAlret(\'Workshop creation limit is over.Contact Admnistrator more details..\',\'info\'))' : $site_url . 'workshop/copy/' . base64_encode($dtRow['id']) ) . '">
                                        <i class="fa fa-copy"></i>&nbsp;Copy
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

    public function submit($Copy_id = '') {
        if ($Copy_id != "") {
            $Copy_id = base64_decode($Copy_id);
        }
        $acces_management = $this->acces_management;
        $Success = 1;
        $Msg = "";
        if (!$acces_management->allow_add) {
            $Msg = "You have no rights to Add Workshop,Contact Administrator for rights";
            $Success = 0;
        } else {
            $this->load->library('form_validation');
            $pre_question_type = $this->input->post('pre_question_type');
            //$this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
                if ($this->check_workshop_restrict($Company_id)) {
                    $Msg = "Workshop creation limit is over.Contact Admnistrator more details..";
                    $Success = 0;
                }
            }
            $upload_path = './assets/uploads/workshop';
        }
        if ($Success) {
            $this->form_validation->set_rules('workshop_name', 'Workshop name', 'required');
            $this->form_validation->set_rules('powered_by', 'Powered By', 'required');
            $this->form_validation->set_rules('language_id', 'language', 'trim|required');
            $this->form_validation->set_rules('creation_date', 'Creation Date', 'trim|required');
            $this->form_validation->set_rules('heading', 'Workshop Heading', 'trim|required');
            $this->form_validation->set_rules('message', 'Message', 'trim|required');
            $PreQuestionArray = $this->input->post('pre_question_set');
            $PostQuestionArray = $this->input->post('post_question_set');
            $PreFeedbackArray = $this->input->post('pre_feedback_id');
            $PostFeedbackArray = $this->input->post('post_feedback_id');
            $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('point_multiplier', 'Point Multiplier', 'trim|required|numeric');
            $Pretime_status = $this->input->post('Pretime_status');
            $Prestart_date = $this->input->post('start_date');
            $Prestart_time = $this->input->post('start_time');
            $Preend_date = $this->input->post('end_date');
            $Preend_time = $this->input->post('end_time');
            $Posttime_status = $this->input->post('Posttime_status');
            $Post_start_date = $this->input->post('post_start_date');
            $Post_start_time = $this->input->post('post_start_time');
            $Post_end_date = $this->input->post('post_end_date');
            $Post_end_time = $this->input->post('post_end_time');
            $fset_pre_trigger = '';
            $fset_post_trigger = '';
            $token_key = $this->input->post('token_key');
            if ($pre_question_type == 1) {
                if (count((array)$PreQuestionArray) == 0 && count((array)$PostQuestionArray) == 0) {
                    $this->form_validation->set_rules('pre_question_set[]', 'Pre/Post Questionset', 'trim|required');
                }
                if (count((array)$PreQuestionArray) > 0) {
                    $this->form_validation->set_rules('start_date', 'Pre Start Date', 'trim|required');
                    $this->form_validation->set_rules('end_date', 'Pre End Date', 'trim|required');
                    $this->form_validation->set_rules('start_time', 'Pre Start Time', 'trim|required');
                    $this->form_validation->set_rules('end_time', 'Pre End Time', 'trim|required');
                    foreach ($PreQuestionArray as $key => $value) {
                        $ReturnFlag = $this->workshop_model->CheckQuestionAvaiable($value);
                        if (!$ReturnFlag) {
                            $QusetionSet = $this->common_model->get_value('question_set', 'title', 'id=' . $value);
                            $Msg .="QuestionSet '" . $QusetionSet->title . "' has no any questions are mapped...<br/>";
                            $Success = 0;
                        }
                    }
                    if (count((array)$PreFeedbackArray) > 0) {
                        $this->form_validation->set_rules('prefeedback_trigger', 'PRE Feedback Trigger after Nos. of Questions', 'trim|required');
                        $fset_pre_trigger = $this->input->post('prefeedback_trigger');
                    }
                } else {
                    $Prestart_date = "";
                    $Prestart_time = "";
                    $Preend_date = "";
                    $Preend_time = "";
                    $Pretime_status = 0;
                }
                if (count((array)$PostQuestionArray) > 0) {
                    $this->form_validation->set_rules('post_start_date', 'Post Start Date', 'trim|required');
                    $this->form_validation->set_rules('post_end_date', 'PostEnd Date', 'trim|required');
                    $this->form_validation->set_rules('post_start_time', 'Post Start Time', 'trim|required');
                    $this->form_validation->set_rules('post_end_time', 'Post End Time', 'trim|required');
                    foreach ($PostQuestionArray as $key => $value) {
                        $ReturnFlag = $this->workshop_model->CheckQuestionAvaiable($value);
                        if (!$ReturnFlag) {
                            $QusetionSet = $this->common_model->get_value('question_set', 'title', 'id=' . $value);
                            $Msg .="QuestionSet '" . $QusetionSet->title . "' has no any questions are mapped...<br/>";
                            $Success = 0;
                        }
                    }
                    if (count((array)$PostFeedbackArray) > 0) {
                        $this->form_validation->set_rules('postfeedback_trigger', 'Post Feedback Trigger after Nos. of Questions', 'trim|required');
                        $fset_post_trigger = $this->input->post('postfeedback_trigger');
                    }
                } else {
                    $Post_start_date = "";
                    $Post_start_time = "";
                    $Post_end_date = "";
                    $Post_end_time = "";
                    $Posttime_status = 0;
                }
            } else {
                if (count((array)$PreFeedbackArray) == 0 && count((array)$PostFeedbackArray) == 0) {
                    $this->form_validation->set_rules('pre_feedback_id[]', 'Pre/Post Feedbackset', 'trim|required');
                }
                if (count((array)$PreFeedbackArray) > 0) {
                    $this->form_validation->set_rules('start_date', 'Pre Start Date', 'trim|required');
                    $this->form_validation->set_rules('end_date', 'Pre End Date', 'trim|required');
                    $this->form_validation->set_rules('start_time', 'Pre Start Time', 'trim|required');
                    $this->form_validation->set_rules('end_time', 'Pre End Time', 'trim|required');
                } else {
                    $Prestart_date = "";
                    $Prestart_time = "";
                    $Preend_date = "";
                    $Preend_time = "";
                    $Pretime_status = 0;
                }
                if (count((array)$PostFeedbackArray) > 0) {
                    $this->form_validation->set_rules('post_start_date', 'Post Start Date', 'trim|required');
                    $this->form_validation->set_rules('post_end_date', 'Post End Date', 'trim|required');
                    $this->form_validation->set_rules('post_start_time', 'Post Start Time', 'trim|required');
                    $this->form_validation->set_rules('post_end_time', 'Post End Time', 'trim|required');
                } else {
                    $Post_start_date = "";
                    $Post_start_time = "";
                    $Post_end_date = "";
                    $Post_end_time = "";
                    $Posttime_status = 0;
                }
            }
            if (count((array)$PreFeedbackArray) > 0) {
                foreach ($PreFeedbackArray as $key => $value) {
                    $ReturnFlag = $this->workshop_model->CheckFeedbackQuestionAvaiable($value);
                    if (!$ReturnFlag) {
                        $QusetionSet = $this->common_model->get_value('feedback', 'title', 'id=' . $value);
                        $Msg .="FeedbackSet '" . $QusetionSet->title . "' has no any questions are mapped...<br/>";
                        $Success = 0;
                    }
                }
            }
            if (count((array)$PostFeedbackArray) > 0) {
                foreach ($PostFeedbackArray as $key => $value) {
                    $ReturnFlag = $this->workshop_model->CheckFeedbackQuestionAvaiable($value);
                    if (!$ReturnFlag) {
                        $QusetionSet = $this->common_model->get_value('feedback', 'title', 'id=' . $value);
                        $Msg .="FeedbackSet '" . $QusetionSet->title . "' has no any questions are mapped...<br/>";
                        $Success = 0;
                    }
                }
            }
            if ($this->form_validation->run() == FALSE) {
                $Msg = validation_errors();
                $Success = 0;
            } else {
                $WorkShopName = ucwords($this->input->post('workshop_name'));
                $DublicateWorkshop = $this->workshop_model->check_workshop($Company_id, $WorkShopName);
                if ($DublicateWorkshop) {
                    $Msg = "Workshop name already exists.!!";
                    $Success = 0;
                }
                if (strpos($WorkShopName, "'") !== false || strpos($WorkShopName, '"') !== false) {
                    $Msg = "Single and Double quotes are not allowed!!!";
                    $Success = 0;
                }
                $TodayDateTime = strtotime(date('d-m-Y H:i:s'));
                $tPreStartDate = strtotime($Prestart_date . $Prestart_time);
                $tPreEndDate = strtotime($Preend_date . $Preend_time);
                $tPostStartDate = strtotime($Post_start_date . $Post_start_time);
                $tPostEndDate = strtotime($Post_end_date . $Post_end_time);
                if ($Prestart_date != "" && $tPreStartDate < $TodayDateTime) {
                    $Msg = "Pre Start datetime cannot be current time or earlier.!!<br/>";
                    $Success = 0;
                } else {
                    if ($Preend_date != "" && $tPreEndDate < $TodayDateTime) {
                        $Msg = " Pre End datetime cannot be current time or earlier.!! <br/>";
                        $Success = 0;
                    } elseif ($Prestart_date != "" && $tPreStartDate >= $tPreEndDate) {
                        $Msg = " Pre Start Datetime cannot same or more than Pre End Datetime .!!<br/>";
                        $Success = 0;
                    }
                }
                if ($Post_start_date != "" && $tPostStartDate < $TodayDateTime) {
                    $Msg = " Post Start datetime cannot be current time or earlier.!!<br/>";
                    $Success = 0;
                } else {
                    if ($Post_end_date != "" && $tPostEndDate < $TodayDateTime) {
                        $Msg = " Post End datetime cannot be current time or earlier.!!<br/>";
                        $Success = 0;
                    } elseif ($Post_end_date != "" && $tPostStartDate >= $tPostEndDate) {
                        $Msg = " Post Start Datetime cannot be same or more than Post End Datetime .!!<br/>";
                        $Success = 0;
                    }
                }
                if ($Prestart_date != "" && $Post_start_date != "" && $tPostStartDate <= $tPreStartDate) {
                    $Msg = " Post Start Datetime cannot be same or less than Pre Start Datetime .!!<br/>";
                    $Success = 0;
                }
                if ($Prestart_date != "" && $Post_start_date != "" && $tPostStartDate <= $tPreEndDate) {
                    $Msg = " Post Start Datetime cannot be same or less than Pre End Datetime .!!<br/>";
                    $Success = 0;
                }
                if ($Success) {
                    $Workshop_image = '';
                    if (isset($_FILES['workshop_image']['name']) && $_FILES['workshop_image']['size'] > 0) {
                        $config = array();
                        $Workshop_image = time();
                        $config['upload_path'] = $upload_path;
                        $config['overwrite'] = FALSE;
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';
                        //$config['max_width'] = 750;
                        //$config['max_height'] = 400;
                        //$config['min_width'] = 750;
                        //$config['min_height'] = 400;
                        $config['file_name'] = $Workshop_image;
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('workshop_image')) {
                            $Msg = $this->upload->display_errors();
                            $Success = 0;
                        } else {
                            $ImgArrays = explode('.', $_FILES['workshop_image']['name']);
                            $Workshop_image .="." . $ImgArrays[1];
                        }
                    } else {
                        if ($Copy_id != "") {
                            $Oldata = $this->common_model->get_value('workshop', 'workshop_image', 'id=' . $Copy_id);
                            $OldWorkshop_image = $Oldata->workshop_image;
                            if ($OldWorkshop_image != "") {
                                $Path = $upload_path . '/' . $Oldata->workshop_image;
                                $ImgArrays = explode('.', $OldWorkshop_image);
                                $Workshop_image = time() . '.' . $ImgArrays[1];
                                $Copy = $upload_path . "/" . $Workshop_image;
                                if (file_exists($Path)) {
                                    if (!copy($Path, $Copy)) {
                                        $Msg = "failed to copy $Path...\n";
                                        $Success = 0;
                                    }
                                }
                            }
                        }
                    }
                    if ($Success) {
                        $now = date('Y-m-d H:i:s');
                        $creation_date = date('Y-m-d', strtotime($this->input->post('creation_date')));

                        if ($Prestart_date != '') {
                            $WorkshopStartDate = date('Y-m-d H:i:s', strtotime($Prestart_date . $Prestart_time));
                        } else {
                            $WorkshopStartDate = date('Y-m-d H:i:s', strtotime($Post_start_date . $Post_start_time));
                        }
                        if ($Post_end_date != '') {
                            $WorkshopEndDate = date('Y-m-d H:i:s', strtotime($Post_end_date . $Post_end_time));
                        } else {
                            $WorkshopEndDate = date('Y-m-d H:i:s', strtotime($Preend_date . $Preend_time));
                        }
                        $play_feedback = $this->input->post('play_all_feedback');
                        $hide_website = $this->input->post('hide_on_website');
                        $Payback_option = $this->input->post('payback_option');
                        $end_time_display = $this->input->post('end_time_display', true);
                        $data = array(
                            'workshop_name' => $WorkShopName,
                            'company_id' => $Company_id,
                            'df_trainer_id' => $this->input->post('df_trainer_id', true),
                            'feedbackform_id' => $this->input->post('feedback_form'),
                            'powered_by' => $this->input->post('powered_by'),
                            'questions_order' => $this->input->post('questions_order'),
                            'feedback_qus_order' => $this->input->post('feedback_qus_order'),
                            'language_id' => $this->input->post('language_id'),
                            'workshop_image' => $Workshop_image,
                            'creation_date' => $creation_date,
                            'timer' => 0, //$this->input->post('time'),
                            'questionset_type' => $pre_question_type,
                            'pre_time_status' => (isset($Pretime_status) ? $Pretime_status : 0),
                            'start_date' => $WorkshopStartDate,
                            'end_date' => $WorkshopEndDate,
                            'pre_start_date' => date('Y-m-d', strtotime($Prestart_date)),
                            'pre_start_time' => $Prestart_time,
                            'pre_end_date' => date('Y-m-d', strtotime($Preend_date)),
                            'pre_end_time' => $Preend_time,
                            'post_time_status' => (isset($Posttime_status) ? $Posttime_status : 0),
                            'post_start_date' => date('Y-m-d', strtotime($Post_start_date)),
                            'post_start_time' => $Post_start_time,
                            'post_end_date' => date('Y-m-d', strtotime($Post_end_date)),
                            'post_end_time' => $Post_end_time,
                            'fset_pre_trigger' => $fset_pre_trigger,
                            'fset_post_trigger' => $fset_post_trigger,
                            'workshop_type' => $this->input->post('wktype'),
                            'region' => $this->input->post('region'),
                            'workshopsubtype_id' => $this->input->post('workshop_subtype'),
                            'workshopsubregion_id' => $this->input->post('subregion'),
                            'point_multiplier' => $this->input->post('point_multiplier'),
                            'otp' => $this->input->post('otp'),
                            //'target'          => $this->input->post('target'),
                            'payback_option' => $Payback_option,
                            'hide_on_website' => (isset($hide_website) ? 1 : 0),
                            'play_only_once' => 1,
                            'play_all_feedback' => (isset($play_feedback) ? 1 : 0),
                            'end_time_display' => (isset($end_time_display) ? 1 : 0),
                            'heading' => $this->input->post('heading'),
                            'message' => $this->input->post('message'),
                            //'short_description' => $this->input->post('short_description'),
                            //'long_description' => $this->input->post('long_description'),
                            'remarks' => $this->input->post('remarks'),
                            'status' => $this->input->post('status'),
                            'addeddate' => $now,
                            'addedby' => $this->mw_session['user_id']
                        );
                        $Workshop_Id = $this->common_model->insert('workshop', $data);
                        if ($Workshop_Id != '') {
                            if ($Copy_id != "") {
                                $this->workshop_model->CopyParticipantUsers($Workshop_Id, $Copy_id);
                                $BannerImageSet = $this->common_model->fetch_object_by_field('workshop_banner', 'workshop_id', $Copy_id);
                                if (count((array)$BannerImageSet) > 0) {
                                    foreach ($BannerImageSet as $key => $value) {
                                        $Path = $upload_path . "/banners/" . $value->thumbnail_image;
                                        $ImgArrays = explode('.', $OldWorkshop_image);
                                        if (count((array)$ImgArrays) > 0) {
                                            $Banner_image = time() . '.' . $ImgArrays[1];
                                        } else {
                                            $Banner_image = time();
                                        }
                                        $Copy = $upload_path . "/banners/" . $Banner_image;
                                        if (file_exists($Path)) {
                                            if (!copy($Path, $Copy)) {
                                                $Msg = "failed to copy $Path...\n";
                                                $Success = 0;
                                            }
                                        } else {
                                            $data = array(
                                                'workshop_id' => $Workshop_Id,
                                                'sorting' => $value->sorting,
                                                'thumbnail_image' => $Banner_image);
                                            $this->common_model->insert('workshop_banner', $data);
                                        }
                                    }
                                }
                            }
                            if (count((array)$PreQuestionArray) > 0) {
                                foreach ($PreQuestionArray as $key => $value) {
                                    $Status = $this->input->post('preqstatus_switch');
                                    $SetStatus = (isset($Status) && in_array($value, $Status) ? 1 : 0);

                                    $SetShow_Ans = $this->input->post('prehide_answer');
                                    $ShowAns_Flag = (isset($SetShow_Ans) && in_array($value, $SetShow_Ans) ? 0 : 1);
                                    $questions_order = 1;
                                    $issession_live = 0;
									$questions_limit='';
                                    $tPre_set = array();
                                    if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$value]['pre_qus_limit'])) {
                                        $questions_limit = $_SESSION['Workshop_Qus_set_' . $token_key][$value]['pre_qus_limit'];
                                    }
                                    if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$value])) {
                                        $tPre_set = $_SESSION['Workshop_Qus_set_' . $token_key][$value];
                                        if (isset($tPre_set['pre_qus_orderby'])) {
                                            $questions_order = $tPre_set['pre_qus_orderby'];
                                            if (isset($tPre_set['trainer_List']) && count($tPre_set['trainer_List']) > 0) {
                                                $issession_live = 1;
                                            }
                                        }

                                    } else if ($questions_limit=='' && $Copy_id != "") {
                                        $Oldata = $this->common_model->get_value('workshop_questionset_pre', 'questions_order,questions_limit', 'workshop_id=' . $Copy_id . " AND questionset_id=" . $value);
                                        if (count((array)$Oldata) > 0) {
                                            $questions_order = $Oldata->questions_order;
											$questions_limit = $Oldata->questions_limit;
                                        }
                                    }
                                    $data = array(
                                        'workshop_id' => $Workshop_Id,
                                        'questionset_id' => $value,
                                        'status' => $SetStatus,
                                        'active' => $SetStatus,
                                        'hide_answer' => $ShowAns_Flag,
                                        'active_date' => $now,
                                        'questions_order' => $questions_order,
										'questions_limit'=>$questions_limit
                                    );
                                    $workshop_questionset_id = $this->common_model->insert('workshop_questionset_pre', $data);
                                    $this->add_trainer_questionsSet($Workshop_Id, $value, $tPre_set, $workshop_questionset_id, $issession_live, 1, $Copy_id);
                                    if ($value != "") {
                                        $this->workshop_model->CopyWorkshopQuestion($Company_id, $Workshop_Id, $value);
                                    }
                                }
                            }
                            if (count($PostQuestionArray) > 0) {
                                foreach ($PostQuestionArray as $key => $value) {
                                    $Status = $this->input->post('postqstatus_switch');
                                    $SetStatus = (isset($Status) && in_array($value, $Status) ? 1 : 0);
                                    $SetShow_Ans = $this->input->post('posthide_answer');
                                    $ShowAns_Flag = (isset($SetShow_Ans) && in_array($value, $SetShow_Ans) ? 0 : 1);
                                    $questions_order = 1;
                                    $issession_live = 0;
									$questions_limit='';
                                    $tPost_set = array();
                                    if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$value]['post_qus_limit'])) {
                                        $questions_limit = $_SESSION['Workshop_Qus_set_' . $token_key][$value]['post_qus_limit'];
                                    }
                                    if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$value])) {
                                        $tPost_set = $_SESSION['Workshop_Qus_set_' . $token_key][$value];
                                        if (isset($tPost_set['post_qus_orderby'])) {
                                            $questions_order = $tPost_set['post_qus_orderby'];
                                            if (count((array)$tPost_set['trainer_List']) > 0) {
                                                $issession_live = 1;
                                            }
                                        }
                                    } else if ($questions_limit=='' && $Copy_id != "") {
                                        $Oldata = $this->common_model->get_value('workshop_questionset_post', 'questions_order,questions_limit', 'workshop_id=' . $Copy_id . " AND questionset_id=" . $value);
                                        if (count((array)$Oldata) > 0) {
                                            $questions_order = $Oldata->questions_order;
											$questions_limit= $Oldata->questions_limit;
                                        }
                                    }
                                    $data = array(
                                        'workshop_id' => $Workshop_Id,
                                        'questionset_id' => $value,
                                        'status' => $SetStatus,
                                        'active' => $SetStatus,
                                        'hide_answer' => $ShowAns_Flag,
                                        'active_date' => $now,
                                        'questions_order' => $questions_order,
										'questions_limit'=>$questions_limit
                                    );
                                    $workshop_questionset_id = $this->common_model->insert('workshop_questionset_post', $data);
                                    $this->add_trainer_questionsSet($Workshop_Id, $value, $tPost_set, $workshop_questionset_id, $issession_live, 2, $Copy_id);
                                    if ($value != "") {
                                        $this->workshop_model->CopyWorkshopQuestion($Company_id, $Workshop_Id, $value);
                                    }
                                }
                            }
                            if (count((array)$PreFeedbackArray) > 0) {
                                foreach ($PreFeedbackArray as $key => $value) {
                                    $Status = $this->input->post('prefeedstatus_switch');
                                    $SetStatus = (isset($Status) && in_array($value, $Status) ? 1 : 0);
                                    $questions_order = 1;
                                    $questions_limit='';
									if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$value]['pre_qus_limit'])) {
                                        $questions_limit = $_SESSION['Workshop_feedback_set_' . $token_key][$value]['pre_qus_limit'];
                                    }
                                    if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$value]['pre_qus_orderby'])) {
                                        $questions_order = $_SESSION['Workshop_feedback_set_' . $token_key][$value]['pre_qus_orderby'];
                                    } else if ($questions_limit=='' && $Copy_id != "") {
                                        $Oldata = $this->common_model->get_value('workshop_feedbackset_pre', 'questions_order,questions_limit', 'workshop_id=' . $Copy_id . " AND feedbackset_id=" . $value);
                                        if (count((array)$Oldata) > 0) {
                                            $questions_order = $Oldata->questions_order;
                                            $questions_limit = $Oldata->questions_limit;

                                        }
                                    }
                                    $data = array(
                                        'workshop_id' => $Workshop_Id,
                                        'feedbackset_id' => $value,
                                        'status' => $SetStatus,
                                        'active' => $SetStatus,
                                        'active_date' => $now,
                                        'questions_order' => $questions_order,
										'questions_limit' => $questions_limit,
                                    );
                                    $this->common_model->insert('workshop_feedbackset_pre', $data);
                                    if ($value != "") {
                                        $this->workshop_model->CopyFeedbackQuestion($Company_id, $Workshop_Id, $value);
                                    }
                                }
                            }
                            if (count((array)$PostFeedbackArray) > 0) {
                                foreach ($PostFeedbackArray as $key => $value) {
                                    $Status = $this->input->post('postfeedstatus_switch');
                                    $SetStatus = (isset($Status) && in_array($value, $Status) ? 1 : 0);
                                    $questions_order = 1;
                                    $questions_limit='';
									if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$value]['post_qus_limit'])) {
                                        $questions_limit = $_SESSION['Workshop_feedback_set_' . $token_key][$value]['post_qus_limit'];
                                    }
                                    if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$value]['post_qus_orderby'])) {
                                        $questions_order = $_SESSION['Workshop_feedback_set_' . $token_key][$value]['post_qus_orderby'];
                                    } else if ($questions_limit=='' && $Copy_id != "") {
                                        $Oldata = $this->common_model->get_value('workshop_feedbackset_post', 'questions_order,questions_limit', 'workshop_id=' . $Copy_id . " AND feedbackset_id=" . $value);
                                        if (count((array)$Oldata) > 0) {
                                            $questions_order = $Oldata->questions_order;
											$questions_limit = $Oldata->questions_limit;
                                        }
                                    }
                                    $data = array(
                                        'workshop_id' => $Workshop_Id,
                                        'feedbackset_id' => $value,
                                        'status' => $SetStatus,
                                        'active' => $SetStatus,
                                        'active_date' => $now,
                                        'questions_order' => $questions_order,
										'questions_limit' => $questions_limit
                                    );
                                    $this->common_model->insert('workshop_feedbackset_post', $data);
                                    if ($value != "") {
                                        $this->workshop_model->CopyFeedbackQuestion($Company_id, $Workshop_Id, $value);
                                    }
                                }
                            }
                            if ($Payback_option == 1) {
                                $RewardArray = $this->input->post('reward_id');
                                if (count((array)$RewardArray) > 0) {
                                    foreach ($RewardArray as $key => $value) {
                                        $data = array(
                                            'workshop_id' => $Workshop_Id,
                                            'reward_id' => $value
                                        );
                                        $this->common_model->insert('workshop_reward', $data);
//                                        if ($value != "") {
//                                            $this->workshop_model->CopyFeedbackQuestion($Company_id, $Workshop_Id, $value);
//                                        }
                                    }
                                }
                            }
                            $Msg = "Workshop created successfully..!";
                            $Rdata['Workshop_id'] = base64_encode($Workshop_Id);

                            $this->session->unset_userdata('Workshop_feedback_set_' . $token_key);
                            $this->session->unset_userdata('Workshop_Qus_set_' . $token_key);
                        } else {
                            $Msg = "Error while creating Workshop,Contact Administrator for technical support";
                            $Success = 0;
                        }
                    }
                }
            }
        }

        $Rdata['success'] = $Success;
        $Rdata['Msg'] = $Msg;
        echo json_encode($Rdata);
    }

    public function Banner_update() {
        $Message = '';
        $SuccessFlag = 1;
        $url = $this->input->post('url');
        if ($url != '') {
            foreach ($url as $key => $value) {
                $data = array(
                    'url' => $value,
                    'sorting' => $this->input->post('sort')[$key],
                    'status' => 1
                );
                $this->common_model->update('workshop_banner', 'id', $key, $data);
            }
        }
        $Message = "Workshop Banner data change successfully";
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function checkQuestion_setis_used($Workshop_id, $QuestionArray, $session) {
        $lcwhere = "workshop_id=" . $Workshop_id . " AND workshop_session='$session'";
        if (count((array)$QuestionArray) > 0) {
            $lcwhere .=" AND questionset_id NOT IN(" . implode(',', $QuestionArray) . ")";
        }
        return $this->common_model->get_value('atom_results', 'id', $lcwhere);
    }

    public function checkfeedback_setis_used($Workshop_id, $QuestionArray, $session) {
        $lcwhere = "workshop_id=" . $Workshop_id . " AND workshop_session='$session'";
        if (count((array)$QuestionArray) > 0) {
            $lcwhere .=" AND feedbackset_id NOT IN(" . implode(',', $QuestionArray) . ")";
        }
        return $this->common_model->get_value('atom_feedback', 'feedbackset_id', $lcwhere);
    }

    public function CheckQuestionAvaiable($QuestionArray) {
        $Msg = '';
        foreach ($QuestionArray as $key => $value) {
            $ReturnFlag = $this->workshop_model->CheckQuestionAvaiable($value);
            if (!$ReturnFlag) {
                $QusetionSet = $this->common_model->get_value('question_set', 'title', 'id=' . $value);
                $Msg = "QuestionSet '" . $QusetionSet->title . "' has no any questions are mapped...<br/>";
                break;
            }
        }
        return $Msg;
    }

    public function Checkfeedback_QuestionAvaiable($FeedbackArray) {
        $Msg = '';
        foreach ($FeedbackArray as $key => $value) {
            $ReturnFlag = $this->workshop_model->CheckFeedbackQuestionAvaiable($value);
            if (!$ReturnFlag) {
                $QusetionSet = $this->common_model->get_value('feedback', 'title', 'id=' . $value);
                $Msg .="FeedbackSet '" . $QusetionSet->title . "' has no any questions are mapped...<br/>";
                break;
            }
        }
        return $Msg;
    }

    public function tab1_update($id) {
        $Success = 1;
        $Msg = "";
        $Workshop_id = base64_decode($id);
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Msg = "You have no rights to Edit Workshop,Contact Administrator for rights";
            $Success = 0;
        } else {
            $TodayData = strtotime(date('Y-m-d H:i'));
            $preLockSesstion = false;
            $postLockSesstion = false;
            $expired_lock = $this->input->post('Edit_lock');
            $upload_path = './assets/uploads/workshop';
            $PlayedData = $this->common_model->get_value('atom_results', 'id', 'workshop_id =' . $Workshop_id . " AND workshop_session='PRE'");
            if (count((array)$PlayedData) > 0) {
                $preLockSesstion = true;
            } else {
                $FeedbackPlayedData = $this->common_model->get_value('atom_feedback', 'id', 'workshop_id =' . $Workshop_id . " AND workshop_session='PRE'");
                if (count((array)$FeedbackPlayedData) > 0) {
                    $preLockSesstion = true;
                }
            }
            $PlayedData = $this->common_model->get_value('atom_results', 'id', 'workshop_id =' . $Workshop_id . " AND workshop_session='POST'");
            if (count((array)$PlayedData) > 0) {
                $postLockSesstion = true;
            } else {
                $FeedbackPlayedData = $this->common_model->get_value('atom_feedback', 'id', 'workshop_id =' . $Workshop_id . " AND workshop_session='POST'");
                if (count((array)$FeedbackPlayedData) > 0) {
                    $postLockSesstion = true;
                }
            }
            $this->load->library('form_validation');
            $this->form_validation->set_rules('workshop_name', 'Workshop name', 'required');
            $this->form_validation->set_rules('powered_by', 'Powered By', 'required');
			$this->form_validation->set_rules('language_id', 'language ', 'required');
            if (!$preLockSesstion && !$postLockSesstion) {
                $this->form_validation->set_rules('creation_date', 'Creation Date', 'trim|required');
                $this->form_validation->set_rules('region', 'Workshop Region', 'trim|required');
                $this->form_validation->set_rules('wktype', 'Workshop Type', 'trim|required');
                $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
            }
            if (!$expired_lock) {
                $this->form_validation->set_rules('message', 'Message', 'trim|required');
                $this->form_validation->set_rules('heading', 'Heading', 'trim|required');
            }
            if ($this->form_validation->run() == FALSE) {
                $Msg = validation_errors();
                $Success = 0;
            } else {
                $WorkShopName = $this->input->post('workshop_name');
                $Oldata = $this->common_model->fetch_object_by_id('workshop', 'id', $Workshop_id);
                $DublicateWorkshop = $this->workshop_model->check_workshop($Oldata->company_id, $WorkShopName, $Workshop_id);
                if ($DublicateWorkshop) {
                    $Msg = "Workshop name already exists.!!";
                    $Success = 0;
                }
                if (strpos($WorkShopName, "'") !== false || strpos($WorkShopName, '"') !== false) {
                    $Msg = "Single and Double quotes are not allowed!!!";
                    $Success = 0;
                }
                if (!$preLockSesstion && !$postLockSesstion) {
                    $creation_date = date('Y-m-d', strtotime($this->input->post('creation_date')));
                    if (strtotime($creation_date) > strtotime($Oldata->start_date)) {
                        $Msg = "Workshop Creation Date cannot be more than workshop Start Date..!!!";
                        $Success = 0;
                    }
                }
            }
            if ($Success && !$expired_lock) {
                $Workshop_image = $Oldata->workshop_image;
                if (isset($_FILES['workshop_image']['name']) && $_FILES['workshop_image']['size'] > 0) {
                    if ($Workshop_image != "") {
                        $Path = $upload_path . "/" . $Workshop_image;
                        if (file_exists($Path)) {
                            unlink($Path);
                        }
                    }
                    $config = array();
                    $Workshop_image = time();
                    $config['upload_path'] = $upload_path;
                    $config['overwrite'] = FALSE;
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
//                    $config['max_width'] = 750;
//                    $config['max_height'] = 400;
//                    $config['min_width'] = 750;
//                    $config['min_height'] = 400;
                    $config['file_name'] = $Workshop_image;
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('workshop_image')) {
                        $Msg = $this->upload->display_errors();
                        $Success = 0;
                    } else {
                        $ImgArrays = explode('.', $_FILES['workshop_image']['name']);
                        $Workshop_image .="." . $ImgArrays[1];
                    }
                } else {
                    if ($Workshop_image != "" && $this->input->post('RemoveWrkImage')) {
                        $Path = $upload_path . "/" . $Workshop_image;
                        if (file_exists($Path)) {
                            unlink($Path);
                            $Workshop_image = '';
                        }
                    }
                }
            }
            if ($Success) {
                $now = date('Y-m-d H:i:s');
                $data = array(
                    'workshop_name' => $WorkShopName,
                    //'short_description' => $this->input->post('short_description'),
                    //'long_description' => $this->input->post('long_description'),
                    'remarks' => $this->input->post('remarks'),
					'language_id' => $this->input->post('language_id'),
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']
                );
                if (!$preLockSesstion && !$postLockSesstion) {
                    $data['creation_date'] = $creation_date;
                    $data['workshop_type'] = $this->input->post('wktype');
                    $data['region'] = $this->input->post('region');
                    $data['workshopsubtype_id'] = $this->input->post('workshop_subtype');
                    $data['workshopsubregion_id'] = $this->input->post('subregion');
                    $data['status'] = $this->input->post('status');
                }
                if (!$expired_lock) {
                    $data['workshop_image'] = $Workshop_image;
                    $data['message'] = $this->input->post('message');
                    $data['otp'] = strtoupper($this->input->post('otp'));
                    $data['heading'] = $this->input->post('heading');
                    $data['powered_by'] = $this->input->post('powered_by');
                    $data['workshop_image'] = $Workshop_image;
                }
                $this->common_model->update('workshop', 'id', $Workshop_id, $data);
                $Msg = "Workshop updated successfully..!";
            }
        }
        $Rdata['success'] = $Success;
        $Rdata['Msg'] = $Msg;
        echo json_encode($Rdata);
    }

    public function tab2_update($id) {
        $Success = 1;
        $Msg = "";
        $Workshop_id = base64_decode($id);
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Msg = "You have no rights to Edit Workshop,Contact Administrator for rights";
            $Success = 0;
        } else {
            $pre_question_type = $this->input->post('pre_question_type');
            $fset_pre_trigger = '';
            $fset_post_trigger = '';
            //Lock Code
            $Pretime_status = $this->input->post('Pretime_status');
            $Prestart_date = $this->input->post('start_date');
            $Prestart_time = $this->input->post('start_time');
            $Preend_date = $this->input->post('end_date');
            $Preend_time = $this->input->post('end_time');
            $Posttime_status = $this->input->post('Posttime_status');
            $Post_start_date = $this->input->post('post_start_date');
            $Post_start_time = $this->input->post('post_start_time');
            $Post_end_date = $this->input->post('post_end_date');
            $Post_end_time = $this->input->post('post_end_time');
            $Oldata = $this->common_model->fetch_object_by_id('workshop', 'id', $Workshop_id);
            $TodayData = strtotime(date('Y-m-d H:i'));
            $PreSessionStartDisabled = false;
            $PreSessionEndDisabled = false;
            $PostSessionStartDisabled = false;
            $PostSessionEndDisabled = false;
            $PreLockSesstion = false;
            $PostLockSesstion = false;
            $this->load->library('form_validation');
            $this->form_validation->set_rules('token_key', 'Token', 'trim|required');
            if (!isset($Preend_date) || $Preend_date == "") {
                if ($Oldata->pre_end_date != "1970-01-01") {
                    $PreData = strtotime($Oldata->pre_end_date . ' ' . $Oldata->pre_end_time);
                    $Preend_date = $Oldata->pre_end_date;
                    $Preend_time = $Oldata->pre_end_time;
                    if ($Oldata->post_start_date != "1970-01-01") {
                        $PostData = strtotime($Oldata->post_start_date . ' ' . $Oldata->post_start_time);
                        if ($PreData >= $PostData) {
                            $PreSessionEndDisabled = true;
                        }
                        if ($PreData <= $TodayData) {
                            $PreSessionEndDisabled = true;
                        }
                    } else {
                        if ($PreData <= $TodayData) {
                            $PreSessionEndDisabled = true;
                        }
                    }
                }
            }
            $PlayedData = $this->common_model->get_value('atom_results', 'id', 'workshop_id =' . $Workshop_id . " and workshop_session='PRE'");
            $FeedbackPlayedData = $this->common_model->get_value('atom_feedback', 'id', 'workshop_id =' . $Workshop_id . " and workshop_session='PRE'");
            if (count((array)$PlayedData) > 0 || count((array)$FeedbackPlayedData) > 0) {
                $PreSessionStartDisabled = true;
                $Prestart_date = $Oldata->pre_start_date;
                $Prestart_time = $Oldata->pre_start_time;
                if ($Oldata->pre_time_status) {
                    $PreSessionEndDisabled = true;
                    $PreLockSesstion = true;
                }
            }
            if ($PreSessionStartDisabled && $PreSessionEndDisabled) {
                $PreLockSesstion = true;
            }
            if (!isset($Post_end_date) || $Post_end_date == "") {
                if ($Oldata->post_end_date != "1970-01-01") {
                    $Post_Dt = strtotime($Oldata->post_end_date . ' ' . $Oldata->post_end_time);
                    $Post_end_date = $Oldata->post_end_date;
                    $Post_end_time = $Oldata->post_end_time;
                    if ($Post_Dt <= $TodayData) {
                        $PostSessionEndDisabled = true;
                    }
                }
            }
            $PlayedData = $this->common_model->get_value('atom_results', 'id', 'workshop_id =' . $Workshop_id . " and workshop_session='POST'");
            $FeedbackPlayedData = $this->common_model->get_value('atom_feedback', 'id', 'workshop_id =' . $Workshop_id . " and workshop_session='POST'");
            if (count((array)$PlayedData) > 0 || count((array)$FeedbackPlayedData) > 0) {
                $PostSessionStartDisabled = true;
                $Post_start_date = $Oldata->post_start_date;
                $Post_start_time = $Oldata->post_start_time;
                if ($Oldata->post_time_status) {
                    $PostLockSesstion = true;
                    $PostSessionEndDisabled = true;
                }
            }
            if ($PostSessionStartDisabled && $PostSessionEndDisabled) {
                $PostLockSesstion = true;
            }
            if ($PreSessionStartDisabled || $PostSessionStartDisabled) {
                $pre_question_type = $Oldata->questionset_type;
            } else {
                $this->form_validation->set_rules('point_multiplier', 'Point Multiplier', 'trim|required|numeric');
            }
            $PreQuestionArray = $this->input->post('pre_question_set');
            $PreFeedbackArray = $this->input->post('pre_feedback_id');
            $PostQuestionArray = $this->input->post('post_question_set');
            $PostFeedbackArray = $this->input->post('post_feedback_id');

            if (!$PreLockSesstion && !$PostLockSesstion) {

                if ($pre_question_type == 1) {
                    if (count((array)$PreQuestionArray) == 0 && count((array)$PostQuestionArray) == 0) {
                        $this->form_validation->set_rules('pre_question_set[]', 'Pre/Post Questionset', 'trim|required');
                    }
                } else {
                    if (count((array)$PreFeedbackArray) == 0 && count((array)$PostFeedbackArray) == 0) {
                        $this->form_validation->set_rules('pre_feedback_id[]', 'Pre/Post Feedbackset', 'trim|required');
                    }
                }
            }
            if (!$PreLockSesstion) {
                $RemovedRowSet = $this->checkQuestion_setis_used($Workshop_id, $PreQuestionArray, 'PRE');
                if (count((array)$RemovedRowSet) > 0) {
                    $Msg .="Workshop PRE session is live,you can not remove the QuestionSet...<br/>";
                }
                // Feedback Check
                $RemovedRowSet2 = $this->checkfeedback_setis_used($Workshop_id, $PreFeedbackArray, 'PRE');
                if (count((array)$RemovedRowSet2) > 0) {
                    $Msg .="Workshop PRE session is live,you can not remove the FeedbackSet...<br/>";
                }
                if ($pre_question_type == 1) {
                    if (count((array)$PreQuestionArray) > 0) {
                        if (!$PreSessionStartDisabled) {
                            $this->form_validation->set_rules('start_date', 'Pre Start Date', 'trim|required');
                            $this->form_validation->set_rules('start_time', 'Pre Start Time', 'trim|required');
                        }
                        if (!$PreSessionEndDisabled) {
                            $this->form_validation->set_rules('end_date', 'Pre End Date', 'trim|required');
                            $this->form_validation->set_rules('end_time', 'Pre End Time', 'trim|required');
                        }
                        $Msg.=$this->CheckQuestionAvaiable($PreQuestionArray);
                    } else {
                        $Prestart_date = "";
                        $Prestart_time = "";
                        $Preend_date = "";
                        $Preend_time = "";
                        $Pretime_status = 0;
                    }
                    if (count((array)$PreFeedbackArray) > 0) {
                        $this->form_validation->set_rules('prefeedback_trigger', 'Post Feedback Trigger after Nos. of Questions', 'trim|required');
                        $fset_pre_trigger = $this->input->post('prefeedback_trigger');
                    }
                } else {
                    if (count((array)$PreFeedbackArray) > 0) {
                        if (!$PreSessionStartDisabled) {
                            $this->form_validation->set_rules('start_date', 'Pre Start Date', 'trim|required');
                            $this->form_validation->set_rules('start_time', 'Pre Start Time', 'trim|required');
                        }
                        if (!$PreSessionEndDisabled) {
                            $this->form_validation->set_rules('end_date', 'Pre End Date', 'trim|required');
                            $this->form_validation->set_rules('end_time', 'Pre End Time', 'trim|required');
                        }
                    } else {
                        $Prestart_date = "";
                        $Prestart_time = "";
                        $Preend_date = "";
                        $Preend_time = "";
                        $Pretime_status = 0;
                    }
                }
                if (count((array)$PreFeedbackArray) > 0) {
                    $Msg.=$this->Checkfeedback_QuestionAvaiable($PreFeedbackArray);
                }
            }
            if (!$PostLockSesstion) {
                $RemovedRowSet = $this->checkQuestion_setis_used($Workshop_id, $PostQuestionArray, 'POST');
                if (count((array)$RemovedRowSet) > 0) {
                    $Msg .="Workshop POST session is live,you can not remove the QuestionSet...<br/>";
                }
                // Feedback Check
                $RemovedRowSet2 = $this->checkfeedback_setis_used($Workshop_id, $PostFeedbackArray, 'POST');
                if (count((array)$RemovedRowSet2) > 0) {
                    $Msg .="Workshop POST session is live,you can not remove the FeedbackSet...<br/>";
                }
                if ($pre_question_type == 1) {
                    if (count((array)$PostQuestionArray) > 0) {
                        if (!$PostSessionStartDisabled) {
                            $this->form_validation->set_rules('post_start_date', 'Post Start Date', 'trim|required');
                            $this->form_validation->set_rules('post_start_time', 'Post Start Time', 'trim|required');
                        }
                        if (!$PostSessionEndDisabled) {
                            $this->form_validation->set_rules('post_end_date', 'PostEnd Date', 'trim|required');
                            $this->form_validation->set_rules('post_end_time', 'Post End Time', 'trim|required');
                        }
                        $Msg.=$this->CheckQuestionAvaiable($PostQuestionArray);
                    } else {
                        $Post_start_date = "";
                        $Post_start_time = "";
                        $Post_end_date = "";
                        $Post_end_time = "";
                        $Posttime_status = 0;
                    }
                    if (count((array)$PostFeedbackArray) > 0) {
                        $this->form_validation->set_rules('postfeedback_trigger', 'Post Feedback Trigger after Nos. of Questions', 'trim|required');
                        $fset_post_trigger = $this->input->post('postfeedback_trigger');
                    }
                } else {
                    if (count((array)$PostFeedbackArray) > 0) {
                        if (!$PostSessionStartDisabled) {
                            $this->form_validation->set_rules('post_start_date', 'Post Start Date', 'trim|required');
                            $this->form_validation->set_rules('post_start_time', 'Post Start Time', 'trim|required');
                        }
                        if (!$PostSessionEndDisabled) {
                            $this->form_validation->set_rules('post_end_date', 'PostEnd Date', 'trim|required');
                            $this->form_validation->set_rules('post_end_time', 'Post End Time', 'trim|required');
                        }
                    } else {
                        $Post_start_date = "";
                        $Post_start_time = "";
                        $Post_end_date = "";
                        $Post_end_time = "";
                        $Posttime_status = 0;
                    }
                }
                if (count((array)$PostFeedbackArray) > 0) {
                    $Msg.=$this->Checkfeedback_QuestionAvaiable($PostFeedbackArray);
                }
            }
            if ($this->form_validation->run() == FALSE) {
                $Msg = validation_errors();
                $Success = 0;
            } else {
                if ($Msg != "") {
                    $Success = 0;
                }
                $TodayDate = strtotime(date('d-m-Y H:i:s'));
                if ($Prestart_date != "") {
                    $temppreStartDate = strtotime($Prestart_date . $Prestart_time);
                    $temppreEndDate = strtotime($Preend_date . $Preend_time);
                    if (strtotime($Oldata->pre_start_date . $Oldata->pre_start_time) != $temppreStartDate) {
                        if ($temppreStartDate < $TodayDate) {
                            $Msg = " Pre Start datetime cannot be current time or earlier. .!!<br/>";
                            $Success = 0;
                        }
                    }
                    if ($Success && strtotime($Oldata->pre_end_date . $Oldata->pre_end_time) != $temppreEndDate) {
                        if ($temppreEndDate < $TodayDate) {
                            $Msg = " Pre End datetime cannot be current time or earlier. .!!<br/>";
                            $Success = 0;
                        }
                    }
                    if ($Success && $temppreStartDate >= $temppreEndDate) {
                        $Msg = " Pre Start Datetime cannot be same or more than Pre End Datetime .!!<br/>";
                        $Success = 0;
                    }
                }
                if ($Post_start_date != "") {
                    //echo $Post_start_time;
                    $temppostStartDate = strtotime($Post_start_date . $Post_start_time);
                    $temppostEndDate = strtotime($Post_end_date . $Post_end_time);
                    if (strtotime($Oldata->post_start_date . $Oldata->post_start_time) != $temppostStartDate) {
                        if ($temppostStartDate < $TodayDate) {
                            $Msg .= " Post Start Datetime cannot be less than current datetime .!!<br/>";
                            $Success = 0;
                        }
                    }
                    if (strtotime($Oldata->post_end_date . $Oldata->post_end_time) != $temppostEndDate) {
                        if ($temppostEndDate < $TodayDate) {
                            $Msg .= " Post End Datetime cannot be less than current datetime .!!<br/>";
                            $Success = 0;
                        }
                    }
                    if ($Success && $temppostStartDate >= $temppostEndDate) {
                        $Msg = " Post Start Datetime cannot be same or more than Post End Datetime .!!<br/>";
                        $Success = 0;
                    }
                    if ($Success && $Prestart_date != "" && $temppostStartDate <= $temppreStartDate) {
                        $Msg = " Post Start Datetime cannot be same or less than Pre Start Datetime .!!<br/>";
                        $Success = 0;
                    }
                    if ($Success && $Prestart_date != "" && $temppostStartDate <= $temppreEndDate) {
                        $Msg = " Post Start Datetime cannot be same or less than Pre End Datetime .!!<br/>";
                        $Success = 0;
                    }
                }
            }
            if ($Success) {
                $now = date('Y-m-d H:i:s');
                $token_key = $this->input->post('token_key');
                if ($Prestart_date != '') {
                    $WorkshopStartDate = date('Y-m-d H:i:s', strtotime($Prestart_date . $Prestart_time));
                } else {
                    $WorkshopStartDate = date('Y-m-d H:i:s', strtotime($Post_start_date . $Post_start_time));
                }
                if ($Post_end_date != '') {
                    $WorkshopEndDate = date('Y-m-d H:i:s', strtotime($Post_end_date . $Post_end_time));
                } else {
                    $WorkshopEndDate = date('Y-m-d H:i:s', strtotime($Preend_date . $Preend_time));
                }
                $play_feedback = $this->input->post('play_all_feedback');
                $hide_website = $this->input->post('hide_on_website');
                $Payback_option = $this->input->post('payback_option');
                $end_time_display = $this->input->post('end_time_display', true);
                $data = array(
                    'df_trainer_id' => $this->input->post('df_trainer_id', true),
                    'questions_order' => $this->input->post('questions_order', true),
                    'feedback_qus_order' => $this->input->post('feedback_qus_order'),
                    'hide_on_website' => (isset($hide_website) ? 1 : 0),
                    'play_only_once' => 1,
                    'play_all_feedback' => (isset($play_feedback) ? 1 : 0),
                    'end_time_display' => (isset($end_time_display) ? 1 : 0),
                    'payback_option' => $Payback_option,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']
                );
                if (!$PreSessionStartDisabled && !$PostSessionStartDisabled) {
                    $data['questionset_type'] = $pre_question_type;
                    $data['feedbackform_id'] = $this->input->post('feedback_form');
                    $data['point_multiplier'] = $this->input->post('point_multiplier');
                } else {
                    if (!$Oldata->pre_time_status) {
                        $data['pre_time_status'] = (isset($Pretime_status) ? $Pretime_status : 0);
                        if ($data['pre_time_status'] == 1) {
                            $data['preinactive_time'] = $now;
                        } else {
                            $data['preinactive_time'] = '';
                        }
                    }
                    if (!$Oldata->post_time_status) {
                        $data['post_time_status'] = (isset($Posttime_status) ? $Posttime_status : 0);
                        if ($data['post_time_status'] == 1) {
                            $data['postinactive_time'] = $now;
                        } else {
                            $data['postinactive_time'] = '';
                        }
                    }
//                        if ($Post_end_date != '' && isset($data['post_time_status']) && $data['post_time_status'] == 1 ) {
//                            $WorkshopEndDate = $now;
//                        } else {
//                            if(isset($data['post_time_status']) && $data['post_time_status'] == 1){
//                                $WorkshopEndDate = $now;
//                            }
//                        }
                }
                $data['start_date'] = $WorkshopStartDate;
                $data['end_date'] = $WorkshopEndDate;
                if (!$PreSessionStartDisabled) {
                    $data['pre_start_date'] = date('Y-m-d', strtotime($Prestart_date));
                    $data['pre_start_time'] = $Prestart_time;
                    $data['pre_time_status'] = (isset($Pretime_status) ? $Pretime_status : 0);

                    if ($data['pre_time_status'] == 1) {
                        $data['preinactive_time'] = $now;
                    } else {
                        $data['preinactive_time'] = '';
                    }
                }
                if (!$PreSessionEndDisabled) {
                    $data['pre_end_date'] = date('Y-m-d', strtotime($Preend_date));
                    $data['pre_end_time'] = $Preend_time;
                    $data['fset_pre_trigger'] = $fset_pre_trigger;
                }
                if (!$PostSessionStartDisabled) {
                    $data['post_start_date'] = date('Y-m-d', strtotime($Post_start_date));
                    $data['post_start_time'] = $Post_start_time;
                    $data['post_time_status'] = (isset($Posttime_status) ? $Posttime_status : 0);
                    if ($data['post_time_status'] == 1) {
                        $data['postinactive_time'] = $now;
                    } else {
                        $data['postinactive_time'] = '';
                    }
                }
                if (!$PostSessionEndDisabled) {
                    $data['post_end_date'] = date('Y-m-d', strtotime($Post_end_date));
                    $data['post_end_time'] = $Post_end_time;
                    $data['fset_post_trigger'] = $fset_post_trigger;
                }
                $this->common_model->update('workshop', 'id', $Workshop_id, $data);
                $Company_id = $Oldata->company_id;
                if (!$PreLockSesstion) {
                    if (count((array)$PreQuestionArray) > 0) {
                        $IdList = "";
                        foreach ($PreQuestionArray as $key => $value) {

                            $Oldata = $this->common_model->get_value('workshop_questionset_pre', 'id,questionset_id,active,status,questions_order,questions_limit', 'workshop_id=' . $Workshop_id . " AND questionset_id=" . $value);
                            $Status = $this->input->post('preqstatus_switch');
                            $SetStatus = (isset($Status) && in_array($value, $Status) ? 1 : 0);

                            $SetShow_Ans = $this->input->post('prehide_answer');
                            $ShowAns_Flag = (isset($SetShow_Ans) && in_array($value, $SetShow_Ans) ? 0 : 1);
                            $questions_order = 1;
                            $issession_live = 0;
							$questions_limit='';
                            $tPre_set = array();

                            if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$value])) {
                                $tPre_set = $_SESSION['Workshop_Qus_set_' . $token_key][$value];
                                if (isset($tPre_set['pre_qus_orderby'])) {
                                    $questions_order = $tPre_set['pre_qus_orderby'];
                                } elseif (count((array)$Oldata) > 0) {
                                    $questions_order = $Oldata->questions_order;
                                }
                                if (isset($tPre_set['pre_qus_limit'])) {
                                    $questions_limit = $tPre_set['pre_qus_limit'];
                                } elseif (count((array)$Oldata) > 0) {
                                    $questions_limit = $Oldata->questions_limit;
                                }
                                if (isset($tPre_set['trainer_List']) && count($tPre_set['trainer_List']) > 0) {
                                    $issession_live = 1;
                                }
                            }
                            if (count((array)$Oldata) == 0) {
                                if ($value != "") {
                                    $data = array(
                                        'workshop_id' => $Workshop_id,
                                        'questionset_id' => $value,
                                        'status' => $SetStatus,
                                        'active' => $SetStatus,
                                        'hide_answer' => $ShowAns_Flag,
                                        'active_date' => $now,
                                        'questions_order' => $questions_order,
                                        'questions_limit' => $questions_limit,
                                    );
                                    $Inserted_id = $this->common_model->insert('workshop_questionset_pre', $data);

                                    $this->add_trainer_questionsSet($Workshop_id, $value, $tPre_set, $Inserted_id, $issession_live, 1);
                                    $this->workshop_model->CopyWorkshopQuestion($Company_id, $Workshop_id, $value);
                                }
                            } else {
                                $Inserted_id = $Oldata->id;
                                $value = $Oldata->questionset_id;
                                $data = array(
                                    'status' => $SetStatus,
                                    'hide_answer' => $ShowAns_Flag,
                                    'questions_order' => $questions_order,
                                    'questions_limit' => $questions_limit
                                );
                                if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$value])) {
                                    $tPre_set = $_SESSION['Workshop_Qus_set_' . $token_key][$value];
                                    if (isset($tPre_set['trainer_List']) && count($tPre_set['trainer_List']) > 0) {
                                        $this->update_trainer_questionsSet($tPre_set, $value, $Workshop_id);
                                    }
                                }
                                if ($Oldata->active && !$SetStatus) {
                                    $RtFlag = $this->workshop_model->CheckQuestionSetPlayed($Workshop_id, $Oldata->questionset_id, "PRE");
                                    if (!$RtFlag) {
                                        $data['active'] = 0;
                                        $data['active_date'] = $now;
                                    }
                                } else if (!$Oldata->active && $SetStatus) {
                                    $data['active'] = 1;
                                    $data['active_date'] = $now;
                                }
                                $this->common_model->update('workshop_questionset_pre', 'id', $Inserted_id, $data);
                            }
                            $IdList .=$Inserted_id . ",";
                        }
                        $this->common_model->delete_whereclause("workshop_questionset_pre", "id NOT IN (" . rtrim($IdList, ',') . ") and workshop_id=" . $Workshop_id);
                    } else {
                        $this->common_model->delete('workshop_questionset_pre', 'workshop_id', $Workshop_id);
                    }
                    if (count((array)$PreFeedbackArray) > 0) {
                        $IdList = "";
                        foreach ($PreFeedbackArray as $key => $value) {
                            $Oldata = $this->common_model->get_value('workshop_feedbackset_pre', 'id,feedbackset_id,active,questions_order,questions_limit', 'workshop_id=' . $Workshop_id . " AND feedbackset_id=" . $value);
                            $Status = $this->input->post('prefeedstatus_switch');
                            $SetStatus = (isset($Status) && in_array($value, $Status) ? 1 : 0);
                            $questions_order = 1;
							$questions_limit='';
                            if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$value]['pre_qus_orderby'])) {
                                $questions_order = $_SESSION['Workshop_feedback_set_' . $token_key][$value]['pre_qus_orderby'];
                            } else if (count((array)$Oldata) > 0) {
                                $questions_order = $Oldata->questions_order;
                            }
                            if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$value]['pre_qus_limit'])) {
                                $questions_limit = $_SESSION['Workshop_feedback_set_' . $token_key][$value]['pre_qus_limit'];
                            }elseif (count((array)$Oldata) > 0) {
								$questions_limit = $Oldata->questions_limit;
							}
                            if (count((array)$Oldata) == 0) {
                                $data = array(
                                    'workshop_id' => $Workshop_id,
                                    'feedbackset_id' => $value,
                                    'status' => $SetStatus,
                                    'active' => $SetStatus,
                                    'active_date' => $now,
                                    'questions_order' => $questions_order,
									'questions_limit' => $questions_limit
                                );
                                $Inserted_id = $this->common_model->insert('workshop_feedbackset_pre', $data);
                                $this->workshop_model->CopyFeedbackQuestion($Company_id, $Workshop_id, $value);
                            } else {
                                $Inserted_id = $Oldata->id;
                                $value = $Oldata->feedbackset_id;
                                $data = array(
                                    'status' => $SetStatus,
                                    'questions_order' => $questions_order,
									'questions_limit' => $questions_limit
                                );
                                if ($Oldata->active && !$SetStatus) {
                                    $RtFlag = $this->workshop_model->CheckFeedbackSetPlayed($Workshop_id, $Oldata->feedbackset_id, "PRE");
                                    if (!$RtFlag) {
                                        $data['active'] = 0;
                                        $data['active_date'] = $now;
                                        //$data['questions_order']= $questions_order;
                                    }
                                } else if (!$Oldata->active && $SetStatus) {
                                    $data['active'] = 1;
                                    $data['active_date'] = $now;
                                }
                                $this->common_model->update('workshop_feedbackset_pre', 'id', $Inserted_id, $data);
                            }
                            $IdList .=$Inserted_id . ",";
                        }
                        $this->common_model->delete_whereclause("workshop_feedbackset_pre", "id NOT IN (" . rtrim($IdList, ',') . ")and workshop_id=" . $Workshop_id);
                    } else {
                        $this->common_model->delete('workshop_feedbackset_pre', 'workshop_id', $Workshop_id);
                    }
                }
                if (!$PostLockSesstion) {

                    if (count((array)$PostQuestionArray) > 0) {
                        $IdList = "";
                        foreach ($PostQuestionArray as $key => $value) {
                            $Oldata = $this->common_model->get_value('workshop_questionset_post', 'id,questionset_id,active,questions_order,questions_limit', 'workshop_id=' . $Workshop_id . " AND questionset_id=" . $value);
                            $Status = $this->input->post('postqstatus_switch');
                            $SetStatus = (isset($Status) && in_array($value, $Status) ? 1 : 0);

                            $SetShow_Ans = $this->input->post('posthide_answer');
                            $ShowAns_Flag = (isset($SetShow_Ans) && in_array($value, $SetShow_Ans) ? 0 : 1);
                            $questions_order = 1;
							$questions_limit='';
                            $issession_live = 0;
                            $tPost_set = array();
                            if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$value])) {
                                $tPost_set = $_SESSION['Workshop_Qus_set_' . $token_key][$value];
                                if (isset($tPost_set['post_qus_orderby'])) {
                                    $questions_order = $tPost_set['post_qus_orderby'];
                                } elseif (count((array)$Oldata) > 0) {
                                    $questions_order = $Oldata->questions_order;
                                }
                                if (isset($tPost_set['post_qus_limit'])) {
                                    $questions_limit = $tPost_set['post_qus_limit'];
                                } elseif (count((array)$Oldata) > 0) {
                                    $questions_limit = $Oldata->questions_limit;
                                }
                                if (isset($tPost_set['trainer_List']) && count($tPost_set['trainer_List']) > 0) {
                                    $issession_live = 1;
                                }
                            }
                            if (count((array)$Oldata) == 0) {
                                $data = array(
                                    'workshop_id' => $Workshop_id,
                                    'questionset_id' => $value,
                                    'status' => $SetStatus,
                                    'active' => $SetStatus,
                                    'hide_answer' => $ShowAns_Flag,
                                    'active_date' => $now,
                                    'questions_order' => $questions_order,
									'questions_limit'=>$questions_limit
                                );
                                $Inserted_id = $this->common_model->insert('workshop_questionset_post', $data);
                                $this->add_trainer_questionsSet($Workshop_id, $value, $tPost_set, $Inserted_id, $issession_live, 2);
                                $this->workshop_model->CopyWorkshopQuestion($Company_id, $Workshop_id, $value);
                            } else {
                                $Inserted_id = $Oldata->id;
                                $value = $Oldata->questionset_id;
                                $data = array(
                                    'status' => $SetStatus,
                                    'hide_answer' => $ShowAns_Flag,
                                    'questions_order' => $questions_order,
									'questions_limit'=>$questions_limit
                                );
                                $RtFlag = $this->workshop_model->CheckQuestionSetPlayed($Workshop_id, $Oldata->questionset_id, "POST");
                                if (!$RtFlag) {
                                    $this->update_trainer_questionsSet($tPost_set, $value, $Workshop_id);
                                }
                                if ($Oldata->active && !$SetStatus) {
                                    if (!$RtFlag) {

                                        $data['active'] = 0;
                                        $data['active_date'] = $now;
                                    }
                                } else if (!$Oldata->active && $SetStatus) {
                                    $data['active'] = 1;
                                    $data['active_date'] = $now;
                                }
                                $this->common_model->update('workshop_questionset_post', 'id', $Inserted_id, $data);
                            }
                            $IdList .=$Inserted_id . ",";
                        }
                        $this->common_model->delete_whereclause("workshop_questionset_post", "id NOT IN (" . rtrim($IdList, ',') . ") and workshop_id=" . $Workshop_id);
                    } else {
                        $this->common_model->delete('workshop_questionset_post', 'workshop_id', $Workshop_id);
                    }
                    if (count((array)$PostFeedbackArray) > 0) {
                        $IdList = "";
                        foreach ($PostFeedbackArray as $key => $value) {
                            $Oldata = $this->common_model->get_value('workshop_feedbackset_post', 'id,feedbackset_id,active,questions_order,questions_limit', 'workshop_id=' . $Workshop_id . " AND feedbackset_id=" . $value);
                            $Status = $this->input->post('postfeedstatus_switch');
                            $SetStatus = (isset($Status) && in_array($value, $Status) ? 1 : 0);
                            $questions_order = 1;
                            if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$value]['post_qus_orderby'])) {
                                $questions_order = $_SESSION['Workshop_feedback_set_' . $token_key][$value]['post_qus_orderby'];
                            } elseif (count((array)$Oldata) > 0) {
                                $questions_order = $Oldata->questions_order;
                            }
                            if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$value]['post_qus_limit'])) {
                                $questions_limit = $_SESSION['Workshop_feedback_set_' . $token_key][$value]['post_qus_limit'];
                            }elseif (count((array)$Oldata) > 0) {
								$questions_limit = $Oldata->questions_limit;
							}
                            if (count((array)$Oldata) == 0) {
                                $data = array(
                                    'workshop_id' => $Workshop_id,
                                    'feedbackset_id' => $value,
                                    'status' => $SetStatus,
                                    'active' => $SetStatus,
                                    'active_date' => $now,
                                    'questions_order' => $questions_order,
									'questions_limit'=>$questions_limit
                                );
                                $Inserted_id = $this->common_model->insert('workshop_feedbackset_post', $data);
                                $this->workshop_model->CopyFeedbackQuestion($Company_id, $Workshop_id, $value);
                            } else {
                                $Inserted_id = $Oldata->id;
                                $value = $Oldata->feedbackset_id;
                                $data = array(
                                    'status' => $SetStatus,
                                    'questions_order' => $questions_order,
									'questions_limit'=>$questions_limit
                                );
                                if ($Oldata->active && !$SetStatus) {
                                    $RtFlag = $this->workshop_model->CheckFeedbackSetPlayed($Workshop_id, $Oldata->feedbackset_id, "POST");
                                    if (!$RtFlag) {
                                        $data['active'] = 0;
                                        $data['active_date'] = $now;
                                    }
                                } else if (!$Oldata->active && $SetStatus) {
                                    $data['active'] = 1;
                                    $data['active_date'] = $now;
                                }
                                $this->common_model->update('workshop_feedbackset_post', 'id', $Inserted_id, $data);
                            }
                            $IdList .=$Inserted_id . ",";
                        }
                        $this->common_model->delete_whereclause("workshop_feedbackset_post", "id NOT IN (" . rtrim($IdList, ',') . ") and workshop_id=" . $Workshop_id);
                    } else {
                        $this->common_model->delete('workshop_feedbackset_post', 'workshop_id', $Workshop_id);
                    }
                }
                if (!$PreLockSesstion || !$PostLockSesstion) {
                    $this->workshop_model->RemoveWorkshopQuestions($Workshop_id);
                    $this->workshop_model->RemoveWorkshopFeedback_Qus($Workshop_id);
                    $this->workshop_model->RemoveWorkshopTrainer($Workshop_id);
                }
                if (!$PreSessionStartDisabled && !$PostSessionStartDisabled) {
                    if ($Payback_option == 1) {
                        $RewardArray = $this->input->post('reward_id');
                        if (count((array)$RewardArray) > 0) {
                            $IdList = "";
                            foreach ($RewardArray as $key => $value) {
                                $Oldata = $this->common_model->get_value('workshop_reward', 'id', 'workshop_id=' . $Workshop_id . " AND reward_id=" . $value);
                                if (count((array)$Oldata) == 0) {
                                    $data = array(
                                        'workshop_id' => $Workshop_id,
                                        'reward_id' => $value
                                    );
                                    $Inserted_id = $this->common_model->insert('workshop_reward', $data);
                                } else {
                                    $Inserted_id = $Oldata->id;
                                }
                                $IdList .=$Inserted_id . ",";
                            }
                        }
                        $this->common_model->delete_whereclause("workshop_reward", "id NOT IN (" . rtrim($IdList, ',') . ") and workshop_id=" . $Workshop_id);
                    } else {
                        $this->common_model->delete('workshop_reward', 'workshop_id', $Workshop_id);
                    }
                }
                $Msg = "Workshop updated successfully..!";
                $this->session->unset_userdata('Workshop_Qus_set_' . $token_key);
                $this->session->unset_userdata('Workshop_feedback_set_' . $token_key);
                $this->common_model->delete('temp_questions_order', 'workshop_id', $Workshop_id);
            }
        }
        $Rdata['success'] = $Success;
        $Rdata['Msg'] = $Msg;
        echo json_encode($Rdata);
    }

    public function remove($id) {
        $Workshop_id = base64_decode($id);
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $ReturnFlag = $this->workshop_model->CheckWorkshopExpired($Workshop_id);
            if ($ReturnFlag) {
                $alert_type = 'error';
                $message = "Workshop cannot be delete.";
            } else {
                $upload_path = './assets/uploads/workshop/';
                $BannerImageSet = $this->common_model->fetch_object_by_field('workshop_banner', 'workshop_id', $Workshop_id);
                $Oldata = $this->common_model->get_value('workshop', 'workshop_image', 'id=' . $Workshop_id);
                if ($Oldata->workshop_image != "") {
                    $Path = $upload_path . $Oldata->workshop_image;
                    if (file_exists($Path)) {
                        unlink($Path);
                    }
                }
                if (count((array)$BannerImageSet) > 0) {
                    foreach ($BannerImageSet as $key => $value) {
                        $Path = $upload_path . "banners/" . $value->thumbnail_image;
                        if (file_exists($Path)) {
                            unlink($Path);
                        }
                    }
                }
                $this->common_model->delete('workshop', 'id', $Workshop_id);
                $this->common_model->delete('workshop_questions', 'workshop_id', $Workshop_id);
                $this->common_model->delete('workshop_feedback_questions', 'workshop_id', $Workshop_id);
                $this->common_model->delete('workshop_feedbackset_pre', 'workshop_id', $Workshop_id);
                $this->common_model->delete('workshop_feedbackset_post', 'workshop_id', $Workshop_id);
                $this->common_model->delete('workshop_banner', 'workshop_id', $Workshop_id);
                $this->common_model->delete('workshop_questionset_pre', 'workshop_id', $Workshop_id);
                $this->common_model->delete('workshop_questionset_post', 'workshop_id', $Workshop_id);
                $this->common_model->delete('workshop_reward', 'workshop_id', $Workshop_id);
                $this->common_model->delete('workshop_questionset_trainer', 'workshop_id', $Workshop_id);
                $message = "Workshop deleted successfully.";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function record_actions($Action) {
        $action_id = $this->input->Post('id');
        if (count($action_id) == 0) {
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
                $this->common_model->update('workshop', 'id', $id, $data);
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
                    $this->common_model->update('workshop', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. User(s) assigned to role!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            $upload_path = './assets/uploads/workshop/';
            foreach ($action_id as $Workshop_id) {
                // $DeleteFlag = $this->workshop_model->CheckUserAssignRole($id);
                $DeleteFlag = true;
                if ($DeleteFlag) {
                    $BannerImageSet = $this->common_model->fetch_object_by_field('workshop_banner', 'workshop_id', $Workshop_id);
                    $Oldata = $this->common_model->get_value('workshop', 'workshop_image', 'id=' . $Workshop_id);
                    if ($Oldata->workshop_image != "") {
                        $Path = $upload_path . $Oldata->workshop_image;
                        if (file_exists($Path)) {
                            unlink($Path);
                        }
                    }
                    if (count((array)$BannerImageSet) > 0) {
                        foreach ($BannerImageSet as $key => $value) {
                            $Path = $upload_path . "banners/" . $value->thumbnail_image;
                            if (file_exists($Path)) {
                                unlink($Path);
                            }
                        }
                    }
                    $this->common_model->delete('workshop', 'id', $Workshop_id);
                    //$this->common_model->delete('workshop_questionset', 'workshop_id', $Workshop_id);
                    $this->common_model->delete('workshop_feedbackset_pre', 'workshop_id', $Workshop_id);
                    $this->common_model->delete('workshop_feedbackset_post', 'workshop_id', $Workshop_id);
                    $this->common_model->delete('workshop_banner', 'workshop_id', $Workshop_id);
                    $this->common_model->delete('workshop_questionset_pre', 'workshop_id', $Workshop_id);
                    $this->common_model->delete('workshop_questionset_post', 'workshop_id', $Workshop_id);
                    $this->common_model->delete('workshop_reward', 'workshop_id', $Workshop_id);
                    $this->common_model->delete('workshop_questionset_trainer', 'workshop_id', $Workshop_id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Workshop cannot be deleted. User(s) assigned to role!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Workshop(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function validate() {
        $status = $this->workshop_model->validate($this->input->post());
        echo $status;
    }

    public function Check_workshop() {
        $workshop_name = $this->input->post('workshop_name', true);
        $workshop_id = $this->input->post('workshop_id', true);
        if ($workshop_id != "") {
            $workshop_id = base64_decode($workshop_id);
        }
        if ($this->mw_session['company_id'] == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        echo $this->workshop_model->check_workshop($Company_id, $workshop_name, $workshop_id);
    }

    public function validate_edit() {
        $status = $this->workshop_model->validate_edit($this->input->post());
        echo $status;
    }

    public function ajax_company_questionset($Flag = 1) {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        if ($Flag) {
            $data['feedback_result'] = $this->common_model->get_selected_values('feedback', 'id,title', 'company_id=' . $company_id);
            $data['feedback_form'] = $this->common_model->get_selected_values('feedback_form_header', 'company_id,id,form_name', 'company_id=' . $company_id);
            $data['RewardResult'] = $this->common_model->get_selected_values('reward', 'id,reward_name', 'company_id=' . $company_id);
            $data['feedbackset_Qresult'] = $this->workshop_model->get_feedbackset($company_id);
            $data['question_Qresult'] = $this->workshop_model->get_questionset($company_id);
        }
        $data['WorkshopType'] = $this->common_model->get_selected_values('workshoptype_mst', 'company_id,id,workshop_type', 'company_id=' . $company_id);
        $data['Region'] = $this->common_model->get_selected_values('region', 'company_id,id,region_name', 'company_id=' . $company_id);
        $data['df_trainer_list'] = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as trainer', 'status=1 and company_id=' . $company_id);
        echo json_encode($data);
    }

    public function ajax_regionwise_subregion() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $region = $this->input->post('region', TRUE);
        if ($region != '') {
            $data['SubRegion'] = $this->common_model->get_selected_values('workshopsubregion_mst', 'company_id,id,description as sub_region', 'company_id=' . $company_id . ' and region_id=' . $region);
        } else {
            $data['SubRegion'] = array();
        }
        echo json_encode($data);
    }

    public function ajax_wrktypewise_subtype() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $wktype = $this->input->post('wktype', TRUE);
        if ($wktype != '') {
            $data['WSubType'] = $this->common_model->get_selected_values('workshopsubtype_mst', 'company_id,id,description as sub_type', 'company_id=' . $company_id . ' and workshoptype_id=' . $wktype);
        } else {
            $data['WSubType'] = array();
        }
        echo json_encode($data);
    }

    public function ajax_question_type() {

        $company_id = $this->input->post('cmp_id', TRUE);
        $pre_Qtype = $this->input->post('pre_Qtype', TRUE);
        //$post_Qtype = $this->input->post('post_Qtype', TRUE);
        if ($pre_Qtype != '' && $company_id != '') {
            if ($pre_Qtype == 1) {
                $data['Pre_Qresult'] = $this->common_model->get_selected_values('question_set', 'title,id', 'company_id=' . $company_id);
            } else {
                $data['Pre_Qresult'] = $this->common_model->get_selected_values('feedback', 'title,id', 'company_id=' . $company_id);
            }
            echo json_encode($data);
        }
//        if($post_Qtype!=''){
//            if($post_Qtype==1){
//            $data['Post_Qresult'] = $this->common_model->get_selected_values('question_set','title,id','company_id='.$company_id);
//            }else{
//            $data['Post_Qresult'] = $this->common_model->get_selected_values('feedback','title,id','company_id='.$company_id);
//            }
//        }
    }

    public function temp_avatar_upload() {
        $crop = new CropAvatar(
                $this->input->post('avatar_src'), $this->input->post('avatar_data'), $_FILES['avatar_file']
        );
        $response = array(
            'state' => 200,
            'message' => $crop->getMsg(),
            'result' => $crop->getResult(),
            'preview' => $crop->getTemporery()
        );

        echo json_encode($response);
    }

    public function topicSubtopic($Encode_wkshid) {
        $QuestionSet_id = $this->input->post('QuestionSet_id');
        $disabled = $this->input->post('lockflag');
        $token_key = $this->input->post('token_key');
        $workshop_id = base64_decode($Encode_wkshid);
        $session = $this->input->post('session');
        $type = $this->input->post('type');
        $AddEdit = $this->input->post('AddEdit');
        $data['type'] = 1;
        $data['Session'] = $session;
        $data['QuestionSet_id'] = $QuestionSet_id;
        $data['QuestionSet_data'] = $this->common_model->get_value('question_set', 'company_id,title', 'id=' . $QuestionSet_id);
        $data['TopicSubtopic'] = $this->workshop_model->topic_subtopic_list($QuestionSet_id, $workshop_id, $type, $session);
        $lcWhere = " questionset_id=" . $QuestionSet_id;
        $lcWhere .= " AND Workshop_id=" . $workshop_id;
        $data['Qus_Orders'] = 1;
		$data['Qus_limit'] = '';
        $Selectd_array['trainer_List'] = array();
        if ($session == 1) {

            if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['pre_qus_orderby'])) {
                $data['Qus_Orders'] = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['pre_qus_orderby'];
				$data['Qus_limit'] = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['pre_qus_limit'];
            } else {
                $OrderData = $this->common_model->get_value('workshop_questionset_pre', 'id,questions_order,questions_limit', $lcWhere);
                $data['Qus_Orders'] = $OrderData->questions_order;
				$data['Qus_limit'] = $OrderData->questions_limit;
            }
        } else {
            if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'])) {
                $data['Qus_Orders'] = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'];
                $data['Qus_limit'] = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['post_qus_limit'];
            } else {
                $OrderData = $this->common_model->get_value('workshop_questionset_post', 'id,questions_order,questions_limit', $lcWhere);
                $data['Qus_Orders'] = $OrderData->questions_order;
				$data['Qus_limit'] = $OrderData->questions_limit;
            }
        }
        if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id])) {
            $Selectd_array = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id];
            if (!isset($Selectd_array['trainer_List'])) {
                $Selectd_array['trainer_List'] = array();
            }
        }
        $PlayedFlag = $this->workshop_model->CheckQuestionSetPlayed($workshop_id, $QuestionSet_id);
        if ($PlayedFlag || $AddEdit == 'V') {
            $disabled = true;
        }
        $workshop_status = $this->common_model->get_value('workshop', 'end_date', 'id=' . $workshop_id);
        $lockflag = false;
        if (strtotime($workshop_status->end_date) < strtotime(date('Y-m-d'))) {
            $disabled = true;
            $AddEdit = 'V';
        }
        if ($disabled) {
            $data['df_trainer_id'] = '';
        } else {
            $data['df_trainer_id'] = ''; //$this->input->post('df_trainer_id',true);
        }
        $data['token_key'] = $token_key;
        $data['Qus_Session_Array'] = $Selectd_array['trainer_List'];
        $data['disabled_selected'] = $disabled;
        $data['lockflag'] = $lockflag;
        $data['AddEdit'] = $AddEdit;
        $data['workshop_id'] = $workshop_id;
        $data['Trainer'] = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as trainer', 'status=1 and company_id=' . $data['QuestionSet_data']->company_id);
        $data['istrainer_changable'] = 0;
        $this->load->view('workshop/TopicSubtopic', $data);
    }

    public function edit_feedback_set($Encode_wkshid = "") {
        $token_key = $this->input->post('token_key');
        $workshop_id = base64_decode($Encode_wkshid);
        $session = $this->input->post('session');
        $type = 2;
        $QuestionSet_id = $this->input->post('QuestionSet_id');
        $lcWhere = " feedbackset_id=" . $QuestionSet_id;
        $lcWhere .= " AND Workshop_id=" . $workshop_id;
        $Selectd_array['trainer_List'] = array();
        $data['Qus_Orders'] = 1;
		$data['Qus_limit'] = '';
        if ($session == 1) {
            if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['pre_qus_orderby'])) {
                $data['Qus_Orders'] = $_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['pre_qus_orderby'];
				$data['Qus_limit'] = $_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['pre_qus_limit'];
            } else {
                $OrderData = $this->common_model->get_value('workshop_feedbackset_pre', 'id,questions_order,questions_limit', $lcWhere);
                $data['Qus_Orders'] = $OrderData->questions_order;
				$data['Qus_limit'] = $OrderData->questions_limit;
            }
        } else {
            if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'])) {
                $data['Qus_Orders'] = $_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'];
				$data['Qus_limit'] = $_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'];
            } else {
                $OrderData = $this->common_model->get_value('workshop_feedbackset_post', 'id,questions_order,questions_limit', $lcWhere);
                $data['Qus_Orders'] = $OrderData->questions_order;
				$data['Qus_limit'] = $OrderData->questions_limit;
            }
        }
        if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id])) {
            $Selectd_array = $_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id];
            if (!isset($Selectd_array['trainer_List'])) {
                $Selectd_array['trainer_List'] = array();
            }
        }
        $data['token_key'] = $token_key;
        $data['Session'] = $session;
        $data['QuestionSet_id'] = $QuestionSet_id;
        $data['type'] = $type;
        $data['QuestionSet_data'] = $this->common_model->get_value('feedback', 'company_id,title', 'id=' . $QuestionSet_id);
        $data['TopicSubtopic'] = $this->workshop_model->topic_subtopic_list($QuestionSet_id, $workshop_id, $type, $session);

        $data['AddEdit'] = 'E';
        $data['workshop_id'] = '';
        $data['disabled_selected'] = false;
        $this->load->view('workshop/TopicSubtopic', $data);
    }

    public function getfeedback_set($Encode_wkshid = "") {
        $QuestionSet_id = $this->input->post('QuestionSet_id');
        //$disabled = $this->input->post('lockflag');
        $token_key = $this->input->post('token_key');
        $session = $this->input->post('session');
        $data['type'] = 2;
        $data['Session'] = $session;
        $data['QuestionSet_id'] = $QuestionSet_id;
        $AddEdit = $this->input->post('AddEdit');
        $Selectd_array['trainer_List'] = array();
        $data['Qus_Orders'] = 1;
		$data['Qus_limit'] = '';
        if ($session == 1) {
            if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['pre_qus_orderby'])) {
                $data['Qus_Orders'] = $_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['pre_qus_orderby'];
				$data['Qus_limit'] = $_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['pre_qus_limit'];
            } else if ($Encode_wkshid != "") {
                $lcWhere = " feedbackset_id=" . $QuestionSet_id;
                $lcWhere .= " AND Workshop_id=" . base64_decode($Encode_wkshid);
                $OrderData = $this->common_model->get_value('workshop_feedbackset_pre', 'id,questions_order,questions_limit', $lcWhere);
                if (count((array)$OrderData) > 0) {
                    $data['Qus_Orders'] = $OrderData->questions_order;
					$data['Qus_limit'] = $OrderData->questions_limit;
                }
            }
            $_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['pre_qus_orderby'] = $data['Qus_Orders'];
			$_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['pre_qus_limit'] = $data['Qus_limit'];
        } else {
            if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'])) {
                $data['Qus_Orders'] = $_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'];
				$data['Qus_limit'] = $_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['post_qus_limit'];
            } else if ($Encode_wkshid != "") {
                $lcWhere = " feedbackset_id=" . $QuestionSet_id;
                $lcWhere .= " AND Workshop_id=" . base64_decode($Encode_wkshid);
                $OrderData = $this->common_model->get_value('workshop_feedbackset_post', 'id,questions_order,questions_limit', $lcWhere);
                if (count((array)$OrderData) > 0) {
                    $data['Qus_Orders'] = $OrderData->questions_order;
					$data['Qus_limit'] = $OrderData->questions_limit;
                }
            }
            $_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'] = $data['Qus_Orders'];
			$_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id]['post_qus_limit'] = $data['Qus_limit'];
        }
        if (isset($_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id])) {
            $Selectd_array = $_SESSION['Workshop_feedback_set_' . $token_key][$QuestionSet_id];
            if (!isset($Selectd_array['trainer_List'])) {
                $Selectd_array['trainer_List'] = array();
            }
        }
        $data['QuestionSet_data'] = $this->common_model->get_value('feedback', 'company_id,title', 'id=' . $QuestionSet_id);
        $data['TopicSubtopic'] = $this->workshop_model->topic_subtopic_creat($QuestionSet_id, $data['type']);
//        if ($EncodeEdit_id != "") {
//            $data['AddEdit'] = 'E';
//        } else {
//            $data['AddEdit'] = 'A';
//        }
        $data['token_key'] = $token_key;
        $data['AddEdit'] = $AddEdit;
        $data['workshop_id'] = '';
        $data['disabled_selected'] = false;
        $this->load->view('workshop/TopicSubtopic', $data);
    }

    public function topicSubtCreat($Encode_wkshid = "") {
        $QuestionSet_id = $this->input->post('QuestionSet_id');
        $disabled = $this->input->post('lockflag');
        $session = $this->input->post('session');
        $type = $this->input->post('type');
        $data['type'] = $type;
        $data['Session'] = $session;
        $data['QuestionSet_id'] = $QuestionSet_id;
        $data['df_trainer_id'] = '';
        $token_key = $this->input->post('token_key');
        $data['Qus_Orders'] = 1;
		$data['Qus_limit'] = '';
        $Selectd_array['trainer_List'] = array();
        if ($session == 1) {
            if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['pre_qus_orderby'])) {
                $data['Qus_Orders'] = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['pre_qus_orderby'];
				$data['Qus_limit'] = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['pre_qus_limit'];
				
            } else if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'])) {
                $data['Qus_Orders'] = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'];
				//$data['Qus_limit'] = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['post_qus_limit'];
            } else if ($Encode_wkshid != "") {
                $lcWhere = " questionset_id=" . $QuestionSet_id;
                $lcWhere .= " AND Workshop_id=" . base64_decode($Encode_wkshid);
                $OrderData = $this->common_model->get_value('workshop_questionset_post', 'id,questions_order,questions_limit', $lcWhere);
                if (count((array)$OrderData) > 0) {
                    $data['Qus_Orders'] = $OrderData->questions_order;
					$data['Qus_limit'] = $OrderData->questions_limit;
                }
            }
            $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['pre_qus_orderby'] = $data['Qus_Orders'];
			$_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['pre_qus_limit'] = $data['Qus_limit'];
        } else {
            if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'])) {
                $data['Qus_Orders'] = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'];
				$data['Qus_limit'] = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['post_qus_limit'];
            } 
			elseif (isset($_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['pre_qus_orderby'])) {
                $data['Qus_Orders'] = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['pre_qus_orderby'];
				//$data['Qus_limit'] = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['pre_qus_limit'];
            } 
			else if ($Encode_wkshid != "") {
                $lcWhere = " questionset_id=" . $QuestionSet_id;
                $lcWhere .= " AND Workshop_id=" . base64_decode($Encode_wkshid);
                $OrderData = $this->common_model->get_value('workshop_questionset_pre', 'id,questions_order,questions_limit', $lcWhere);
                if (count((array)$OrderData) > 0) {
                    $data['Qus_Orders'] = $OrderData->questions_order;
					$data['Qus_limit'] = $OrderData->questions_limit;
                }
            }
            $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['post_qus_orderby'] = $data['Qus_Orders'];
			$_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id]['post_qus_limit'] = $data['Qus_limit'];
        }
        if (isset($_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id])) {
            $Selectd_array = $_SESSION['Workshop_Qus_set_' . $token_key][$QuestionSet_id];
            if (!isset($Selectd_array['trainer_List'])) {
                $Selectd_array['trainer_List'] = array();
            }
        }

        $data['disabled_selected'] = $disabled;
        $data['Qus_Session_Array'] = $Selectd_array['trainer_List'];
        $data['token_key'] = $token_key;
        $data['QuestionSet_data'] = $this->common_model->get_value('question_set', 'company_id,title', 'id=' . $QuestionSet_id);
        $data['TopicSubtopic'] = $this->workshop_model->topic_subtopic_creat($QuestionSet_id, $type);
		
        $data['Trainer'] = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as trainer', 'status=1 and company_id=' . $data['QuestionSet_data']->company_id);

        if ($Encode_wkshid != "") {
            $data['AddEdit'] = 'E';
        } else {
            $data['AddEdit'] = 'A';
        }
        $data['workshop_id'] = '';
        $data['istrainer_changable'] = 1;
        $this->load->view('workshop/TopicSubtopic', $data);
    }

    public function update_trainer_questionsSet($Session_Array, $questionset_id, $workshop_id) {

        if (isset($Session_Array['trainer_List']) && count($Session_Array['trainer_List']) > 0) {
            $getQuestions_trans = $Session_Array['trainer_List'];
            foreach ($getQuestions_trans as $value2) {
                $workshop_Ques_upd_id = $value2['workshop_Ques_upd_id'];
                $data = array(
                    'trainer_id' => $value2['trainer_id'],
                );
                $this->common_model->update('workshop_questionset_trainer', 'id', $workshop_Ques_upd_id, $data);
            }
            $this->workshop_model->UpdateQusWorkshop($questionset_id, $workshop_id);
        }
    }

    public function add_trainer_questionsSet($Workshop_Id, $questionset_id, $Session_Array, $workshop_questionset_id, $issession_live, $session, $copy_id = "") {
        $Already_setData = $this->common_model->get_value('workshop_questionset_trainer', 'id', 'workshop_id=' . $Workshop_Id . ' AND questionset_id=' . $questionset_id);
        $df_trainer_id = $this->input->post('df_trainer_id', true);
        if (count((array)$Already_setData) > 0) {

            return false;
        } else {
            if ($issession_live) {
                $getQuestions_trans = $Session_Array['trainer_List'];
            } else if ($copy_id != "") {
                $copy_setData = $this->common_model->get_value('workshop_questionset_trainer', 'id', 'workshop_id=' . $copy_id . ' AND questionset_id=' . $questionset_id);
                if (count((array)$copy_setData) > 0) {
                    $this->workshop_model->workshop_questionset_details($Workshop_Id, $questionset_id, $copy_id);
                    return true;
                } else {
                    $getQuestions_trans = $this->workshop_model->questionset_details($questionset_id);
                }
            } else {
                $getQuestions_trans = $this->workshop_model->questionset_details($questionset_id);
            }
            if (count((array)$getQuestions_trans) > 0) {
                foreach ($getQuestions_trans as $key => $value2) {
                    $questions_trans_id = $value2['qsettrainertable_id'];
                    $Questions_setData = $this->common_model->get_value('questionset_trainer', 'topic_id,subtopic_id', 'id=' . $questions_trans_id);
                    if ($df_trainer_id == "") {
                        $trainer_id = $value2['trainer_id'];
                    } else {
                        $trainer_id = $df_trainer_id;
                    }
                    $data = array(
                        'workshop_id' => $Workshop_Id,
                        'questionset_id' => $questionset_id,
                        'questions_trans_id' => $questions_trans_id,
                        //'wrk_questionset_id'=>$workshop_questionset_id,
                        'trainer_id' => $trainer_id,
                        'topic_id' => $Questions_setData->topic_id,
                        'subtopic_id' => $Questions_setData->subtopic_id,
                    );
                    $this->common_model->insert('workshop_questionset_trainer', $data);
                }
            }
        }
    }

    public function addParticipant($Encode_id) {
        $data['Workshop_id'] = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            //$company_id = $this->input->post('data', TRUE);
            $Workshop_data = $this->common_model->get_value('workshop', 'company_id', 'id=' . $data['Workshop_id']);
            $company_id = $Workshop_data->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['RegionList'] = $this->workshop_model->get_TraineeRegionList($company_id);
        $this->load->view('workshop/UsersFilterModal', $data);
    }

    public function RemoveParticipantUser($Encode_id) {
        $Workshop_id = base64_decode($Encode_id);
        $Remove_id = $this->input->post('Remove_id');
        $this->common_model->delete('workshop_users', 'id', $Remove_id);
        $Message = "Participant User removed successfully .";
        $Rdata['success'] = 1;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function check_workshop_restrict($Company_id) {
        $wcreate_lock = false;
        if (!$this->mw_session['superaccess']) {
            $company_set = $this->common_model->get_value('company', 'restrict_workshop,workshop_count', 'id=' . $Company_id);
            if ($company_set->restrict_workshop == 1) {
                $count_workshopset = $this->common_model->get_value('workshop', 'count(id) as counter', 'company_id=' . $Company_id);
                if ($count_workshopset->counter > $company_set->workshop_count) {
                    $wcreate_lock = true;
                }
            }
        }
        return $wcreate_lock;
    }

    public function file_check($str) {
        $allowed_mime_type_arr = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
        $mime = $_FILES['filename']['type'];
        if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != "") {
            if (in_array($mime, $allowed_mime_type_arr)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only .csv file.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please select csv to import.');
            return false;
        }
    }

    public function importTrainee($Encode_id) {
        $data['Workshop_id'] = base64_decode($Encode_id);
        $this->load->view('workshop/import_trainee', $data);
    }

    public function trainee_samplecsv() {
        $this->load->library('PHPExcel_CI');
        $Excel = new PHPExcel_CI;
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('Trainee_List');
        $Excel->createSheet();
        $Excel->getActiveSheet()
                ->setCellValue('A1', 'Do not modify or delete the Columns.');
        $Excel->getActiveSheet()->getStyle('A1:A1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FF0000'
            )
        ));
        $Excel->getActiveSheet()->mergeCells('A1:A1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
        ));
        $Excel->getActiveSheet()->setCellValue('A2', 'Employee Code*');
        $Excel->getActiveSheet()->getStyle('A1:A1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:A2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("50");

        $Excel->getActiveSheet()->getStyle('A2:A2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'eb3a12'
            )
        ));
        header('Content-type: text/csv');
        header('Content-Disposition: attachment;filename="trainee_sample.csv"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'CSV');
        $objWriter->save('php://output');
        exit;
    }

    public function UploadTraineeXls($Encode_id) {
        $Message = '';
        $SuccessFlag = 1;
        $company_id = $this->input->post('company_id', TRUE);
        $Workshop_id = base64_decode($Encode_id);
        $Error = '';
        $Error = '';
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
                $Message .= "CSV file cannot be empty.";
                $SuccessFlag = 0;
            }
            if ($highestColumnIndex < 1 || $highestColumnIndex > 1) {
                $Message .= "Excel column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($company_id != "" && !$this->mw_session['superaccess']) {
                $company_set = $this->common_model->get_value('company', 'restrict_workshop_users,workshop_users_count', 'id=' . $company_id);
                if ($company_set->restrict_workshop_users == 1) {
                    $count_workshopuserset = $this->common_model->get_value('workshop_users', 'count(id) as counter', 'workshop_id=' . $Workshop_id);
                    if ($count_workshopuserset->counter > $company_set->workshop_users_count) {
                        $Message = "Allow Workshop user limit is over,Contact Administrator for more details..";
                        $SuccessFlag = 0;
                    } else {
                        if (($highestRow - 2) > $company_set->workshop_users_count) {
                            $Message = "You cannot import more than " . $company_set->workshop_users_count . " Users,Contact Administrator for more details..";
                            $SuccessFlag = 0;
                        }
                    }
                }
            }
            if ($SuccessFlag) {
                for ($row = 3; $row <= $highestRow; $row++) {
                    $Emp_code = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    if ($Emp_code == '') {
                        continue;
                    }
                    $EmpId = $this->workshop_model->get_userid($company_id, $Emp_code);
                    if (count((array)$EmpId) == 0) {
                        $Message .= "Row No. $row, Employee Code is Not Exist. </br> ";
                        $SuccessFlag = 0;
                        continue;
                    }
                }
            }
            if ($SuccessFlag) {
                $Counter = 0;
                for ($row = 3; $row <= $highestRow; $row++) {
                    $Emp_code = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    if ($Emp_code == '') {
                        continue;
                    }
                    $EmpId = $this->workshop_model->get_userid($company_id, $Emp_code);
                    $UserId = $this->common_model->get_value('workshop_users', 'id', " user_id =" . $EmpId->user_id . " AND workshop_id=" . $Workshop_id);
                    if (count((array)$UserId) > 0) {
                        continue;
                    }
                    $Counter++;
                    $data = array(
                        'workshop_id' => $Workshop_id,
                        'user_id' => $EmpId->user_id
                    );
                    $Inserted_id = $this->common_model->insert('workshop_users', $data);
                }
                $Message = $Counter . " Trainee Map successfully.";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function SaveParticipantUsers($Encode_id) {
        $Workshop_id = base64_decode($Encode_id);
        $Message = '';
        $SuccessFlag = 1;
        $NewUsersArrray = $this->input->post('NewUsersArrray');
        if (count((array)$NewUsersArrray) > 0) {
            $Company_id = $this->mw_session['company_id'];
            if ($Company_id != "" && !$this->mw_session['superaccess']) {
                $company_set = $this->common_model->get_value('company', 'restrict_workshop_users,workshop_users_count', 'id=' . $Company_id);
                if ($company_set->restrict_workshop_users == 1) {
                    $count_workshopuserset = $this->common_model->get_value('workshop_users', 'count(id) as counter', 'workshop_id=' . $Workshop_id);
                    if ($count_workshopuserset->counter > $company_set->workshop_users_count) {
                        $Message = "Allow Workshop user limit is over,Contact Administrator for more details..";
                        $SuccessFlag = 0;
                    } else {
                        if (count((array)$NewUsersArrray) > $company_set->workshop_users_count) {
                            $Message = "You cannot select more than " . $company_set->workshop_users_count . " Users,Contact Administrator for more details..";
                            $SuccessFlag = 0;
                        }
                    }
                }
            }
            if ($SuccessFlag) {
                foreach ($NewUsersArrray as $user_id) {
                    $AlreadyExist = $this->common_model->get_value('workshop_users', 'user_id', 'workshop_id=' . $Workshop_id . ' AND user_id=' . $user_id);
                    if (count((array)$AlreadyExist) > 0) {
                        continue;
                    }
                    $data = array(
                        'user_id' => $user_id,
                        'workshop_id' => $Workshop_id);
                    $this->common_model->insert('workshop_users', $data);
                }
                $Message = "User added successfully.!";
            }
        } else {
            $Message = "Please select Users.!";
            $SuccessFlag = 0;
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function UsersFilterTable($Encode_id) {
        $Workshop_id = base64_decode($Encode_id);
        $dtSearchColumns = array('u.user_id', 'tr.region_name', 'u.firstname', 'u.email', 'u.mobile', 'u.area', 'u.lastname');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($this->input->get('sSearch') != '') {
            $search_Result = explode(' ', trim($this->input->get('sSearch')));
            if (count((array)$search_Result) > 1) {
                $dtWhere = " WHERE ((u.firstname like '%" . $search_Result[0] . "%' AND u.lastname like '%" . $search_Result[1] . "%') ) ";
            }
        }
        if ($this->mw_session['company_id'] == "") {
            $Company_id = $this->input->get('company_id') ? $this->input->get('company_id') : '';
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        if ($Company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.status=1 AND  u.company_id  = " . $Company_id;
            } else {
                $dtWhere .= " WHERE u.status=1 AND u.company_id  = " . $Company_id;
            }
        }
        $flt_region_id = $this->input->get('flt_region_id') ? $this->input->get('flt_region_id') : '';
        if ($flt_region_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.region_id  = " . $flt_region_id;
            } else {
                $dtWhere .= " WHERE u.region_id  = " . $flt_region_id;
            }
        }
        if ($dtWhere <> '') {
            $dtWhere .= " AND u.user_id Not IN(SELECT user_id FROM workshop_users where workshop_id=" . $Workshop_id . ")";
        } else {
            $dtWhere .= " WHERE u.user_id Not IN(SELECT user_id FROM workshop_users where workshop_id=" . $Workshop_id . ")";
        }
        $this->load->model('company_model');
        $DTRenderArray = $this->workshop_model->LoadUsersDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );

        $NewUsersArrray = $this->input->get('NewUsersArrray');
        $TestArray = explode(',', $NewUsersArrray);

        $dtDisplayColumns = array('user_id', 'region_name', 'name', 'email', 'mobile', 'area', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count($dtDisplayColumns);
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
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['user_id'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                            <input type="checkbox" class="checkboxes" name="id[]" id="chk' . $dtRow['user_id'] . '" ';
                    $action .= 'value="' . $dtRow['user_id'] . '" onclick="SelectedUsers(' . $dtRow['user_id'] . ')"';
                    if (count($TestArray) > 0 && in_array($dtRow['user_id'], $TestArray)) {
                        $action .= "checked";
                    }
                    $action .='/><span></span></label>';
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function ParticipantUsers($Encode_id) {

        $Workshop_id = base64_decode($Encode_id);
        $dtSearchColumns = array('u.user_id', 'u.user_id', 'u.firstname', 'u.email', 'u.mobile', 'u.area', 'tr.region_name', 'u.lastname');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($dtWhere <> '') {
            $dtWhere .= " AND w.workshop_id  = " . $Workshop_id;
        } else {
            $dtWhere .= " WHERE w.workshop_id  = " . $Workshop_id;
        }
        $fttrainer_id = $this->input->get('fttrainer_id') ? $this->input->get('fttrainer_id') : '';
        if ($fttrainer_id != "") {
            $dtWhere .= " AND u.trainer_id  = " . $fttrainer_id;
        }
        $DTRenderArray = $this->workshop_model->LoadParticipantUsers($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'user_id', 'name', 'email', 'mobile', 'region_name', 'area');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count($dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="Participant_all[]" value="' . $dtRow['id'] . '"/>
                                <span></span>
                        </label>';
                } elseif ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    $action = '<button type="button" id="remove" value="' . $dtRow['id'] . '" name="remove"  class="btn btn-danger btn-sm delete" '
                            . ' ><i class="fa fa-times"></i></button>';
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function remove_questionset($EncodeEdit_id) {
        $Message = '';
        $SuccessFlag = 1;
        $Workshop_id = '';
        $QusSet_id = $this->input->post('question_set');
        $session = $this->input->post('module_id');
        if ($EncodeEdit_id != "") {
            $Workshop_id = base64_decode($EncodeEdit_id);
            if ($session == 1) {
                $PlayedData = $this->common_model->get_value('atom_results', 'id', 'workshop_id =' . $Workshop_id . " and workshop_session='PRE' AND questionset_id=" . $QusSet_id);
            } elseif ($session == 2) {
                $PlayedData = $this->common_model->get_value('atom_results', 'id', 'workshop_id =' . $Workshop_id . " and workshop_session='POST' AND questionset_id=" . $QusSet_id);
            } elseif ($session == 3) {
                $PlayedData = $this->common_model->get_value('atom_feedback', 'id', 'workshop_id =' . $Workshop_id . " and workshop_session='PRE' AND feedbackset_id=" . $QusSet_id);
            } elseif ($session == 4) {
                $PlayedData = $this->common_model->get_value('atom_feedback', 'id', 'workshop_id =' . $Workshop_id . " and workshop_session='POST' AND feedbackset_id=" . $QusSet_id);
            }
            if (count((array)$PlayedData) > 0) {
                $Message .="Workshop is live,you can not remove this Questionset/Feedbackset.!";
                $SuccessFlag = 0;
            }
        }
        $data['success'] = $SuccessFlag;
        $data['Msg'] = $Message;
        echo json_encode($data);
    }

    public function ChangeQuestionset($EncodeEdit_id = "") {
        $Workshop_id = '';
        if ($EncodeEdit_id != "") {
            $Workshop_id = base64_decode($EncodeEdit_id);
        }
        $company_id = $this->mw_session['company_id'];
        if ($company_id == '') {
            $company_id = $this->input->post('company_id');
        }
        $QusSet_id = $this->input->post('question_set');
        $session = $this->input->post('session');
        $status_switch = $this->input->post('status_switch');
        $show_answer = $this->input->post('show_answer');
        $html = "";
        $Success = 1;
        $Msg = "";
        if ($QusSet_id != null) {
            $SetName = $this->workshop_model->getQuestionCountData(1, $QusSet_id);
            if ($SetName->totalqsn > 0) {
                $html .='<tr id="' . ($session == 1 ? 'QPre' : 'QPost') . $QusSet_id . '">
                    <td><a href="javascript:void(0)"  onclick="getTopic_subtopic(' . $QusSet_id . ',' . $session . ',1);"  ><i class="fa fa-plus-circle"></i></a></td>
                    <td>' . $SetName->text . '</td>
                    <td>' . ($SetName->timer > 0 ? $SetName->timer : '-') . '</td>
                    <td>' . $SetName->totalqsn.'/'.$SetName->totalqsn . '</td>';
                if ($session == 1) {
                    $html .=' <td>
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                    <input type="checkbox" class="prehide_answer"  name="prehide_answer[]" id="prehide_answer" value="' . $QusSet_id . '"';
                    if (!isset($show_answer) || !in_array($QusSet_id, $show_answer)) {
                        $html .= 'checked ';
                    }
                    $html .='/>
                                    <span></span>
                                </label>
                            </td>';
                    $html .= '<td style="float:right">'
                            . '<input type="checkbox" class="make-switch preqstatus_switch" name="preqstatus_switch[]" value="' . $QusSet_id . '"';
                    if (!isset($status_switch) || !in_array($QusSet_id, $status_switch)) {
                        $html .= 'checked ';
                    }
                    $html .=' data-size="small" data-off-color="danger" data-off-text="In-Active" data-on-color="success" data-on-text="Active"></td>';
                } else {
                    $html .=' <td>    
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                    <input type="checkbox" class="posthide_answer" name="posthide_answer[]" id="posthide_answer" value="' . $QusSet_id . '"';
                    if (!isset($show_answer) || !in_array($QusSet_id, $show_answer)) {
                        $html .= 'checked ';
                    }
                    $html .='/>
                                    <span></span>
                                </label>
                            </td>';
                    $html .= '<td style="float:right">'
                            . '<input type="checkbox" class="make-switch postqstatus_switch" name="postqstatus_switch[]" value="' . $QusSet_id . '"';
                    if (!isset($status_switch) || !in_array($QusSet_id, $status_switch)) {
                        $html .= 'checked ';
                    }
                    $html .=' data-size="small" data-off-color="danger" data-off-text="In-Active" data-on-color="success" data-on-text="Active"></td>';
                }
                $html .= '</tr>';
            } else {
                $Success = 0;
                $Msg = "Selected Questionset has no any Questions are Mapped..";
            }
        }
        $data['success'] = $Success;
        $data['HtmlData'] = $html;
        $data['Msg'] = $Msg;
        echo json_encode($data);
        exit;
    }

    public function createSession_queset($questionset_id, $Qsession, $token_key, $Workshop_id) {
        if (!isset($_SESSION['Workshop_Qus_set_' . $token_key][$Qsession][$questionset_id])) {
            if ($Workshop_id != "") {
                $getQuestions_trans = $this->workshop_model->topic_subtopic_list($questionset_id, $Workshop_id, 1, $Qsession);
            } else {
                $getQuestions_trans = $this->workshop_model->questionset_details($questionset_id);
            }
            $data_array = array();
            foreach ($getQuestions_trans as $value) {
                $qsettrainertable_id = $value['qsettrainertable_id'];
                $data_array[$qsettrainertable_id] = array(
                    'questionset_id' => $questionset_id,
                    'trainer_id' => $value['trainer_id'],
                    'qsettrainertable_id' => $qsettrainertable_id
                );
            }
            $Question_set_Array = array('qus_orderby' => 1, 'trainer_List' => $data_array);
        } else {
            $Question_set_Array = $_SESSION['Workshop_Qus_set_' . $token_key][$Qsession][$questionset_id];
        }
        return $Question_set_Array;
    }

    public function ChangeFeedbackset($EncodeEdit_id = '') {
        if ($EncodeEdit_id != "") {
            $Workshop_id = base64_decode($EncodeEdit_id);
        } else {
            $Workshop_id = '';
        }
        $Cmp_id = $this->mw_session['company_id'];
        if ($Cmp_id == '') {
            $company_id = $this->input->post('company_id');
        } else {
            $company_id = $Cmp_id;
        }
        $QusSet_id = $this->input->post('question_set');
        $session = $this->input->post('session');
        $status_switch = $this->input->post('status_switch');
        $html = "";
        $Success = 1;
        $Msg = "";
        if ($QusSet_id != '') {
            $SetName = $this->workshop_model->getQuestionCountData(2, $QusSet_id);
            if ($SetName->totalqsn > 0) {
                $html .='<tr id="' . ($session == 1 ? 'FPre' : 'FPost') . $QusSet_id . '">
                <td><a href="javascript:void(0)" onclick="get_feeback_subtopic(' . $QusSet_id . ',' . $session . ',1);"><i class="fa fa-plus-circle"></i></a></td>
                <td>' . $SetName->text . '</td>
                <td>' . ($SetName->timer > 0 ? $SetName->timer : '-') . '</td>
                <td>' . $SetName->totalqsn.'/'.$SetName->totalqsn . '</td>
                <td style="float:right">';
                if ($session == 1) {
                    $html .= '<input type="checkbox" class="make-switch prefeedstatus_switch" name="prefeedstatus_switch[]" value="' . $QusSet_id . '"';
                } else {
                    $html .= '<input type="checkbox" class="make-switch postfeedstatus_switch" name="postfeedstatus_switch[]" value="' . $QusSet_id . '"';
                }
                if (!isset($status_switch) || !in_array($QusSet_id, $status_switch)) {
                    $html .= 'checked ';
                }
                $html .=' data-size="small" data-off-color="danger" data-off-text="In-Active" data-on-color="success" data-on-text="Active"></td>
                </tr>';
            } else {
                $Success = 0;
                $Msg = "Selected Feedback has no any Questions are Mapped..";
            }
        }
        $data['success'] = $Success;
        $data['HtmlData'] = $html;
        $data['Msg'] = $Msg;
        echo json_encode($data);
        exit;
    }

    public function testing_users($Company_id) {

        $data['Company_id'] = base64_decode($Company_id);
        $this->load->view('workshop/userlist', $data);
    }

    public function LoadDeviceUsersTable($mode) {
        $dtSearchColumns = array('u.user_id', 'u.user_id', 'u.firstname', 'u.email', 'u.lastname');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        if ($this->input->get('sSearch') != '') {
            $search_Result = explode(' ', trim($this->input->get('sSearch')));
            if (count($search_Result) > 1) {
                $dtWhere = " WHERE ((u.firstname like '%" . $search_Result[0] . "%' AND u.lastname like '%" . $search_Result[1] . "%') ) ";
            }
        }
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $acces_management = $this->acces_management;
        $workshop_id = $this->input->get('workshop_id') ? $this->input->get('workshop_id') : '';
        if ($mode == 2 || $mode == 3) {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshop_id  = " . base64_decode($workshop_id);
            } else {
                $dtWhere .= " where w.workshop_id  = " . base64_decode($workshop_id);
            }
        } else {
            if ($this->mw_session['company_id'] == "") {
                $company_id = $this->input->get('modalcompany_id') ? $this->input->get('modalcompany_id') : '';
                if ($company_id != "") {
                    if ($dtWhere <> '') {
                        $dtWhere .= " AND u.company_id  = " . $company_id;
                    } else {
                        $dtWhere .= " where u.company_id  = " . $company_id;
                    }
                }
            } else {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND u.company_id  = " . $this->mw_session['company_id'];
                } else {
                    $dtWhere .= " where u.company_id  = " . $this->mw_session['company_id'];
                }
            }
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.user_id NOT IN(SELECT tester_id FROM workshop_tester_users where workshop_id= " . base64_decode($workshop_id) . " ) ";
            } else {
                $dtWhere .= " where u.user_id NOT IN(SELECT tester_id FROM workshop_tester_users where workshop_id= " . base64_decode($workshop_id) . " ) ";
            }
        }
        $DTRenderArray = $this->workshop_model->UserDataTable($dtWhere, $dtOrder, $dtLimit, $mode);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $PreDatedisabled = $this->input->get('PreDatedisabled');
        $PostDatedisabled = $this->input->get('PostDatedisabled');
        $NewUsersArrray = $this->input->get('NewtestUsersArrray');

        $TestArray = explode(',', $NewUsersArrray);
        if ($mode == 3) {
            $dtDisplayColumns = array('user_id', 'name', 'email');
        } else {
            $dtDisplayColumns = array('user_id', 'name', 'email', 'Actions');
        }
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count($dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if ($mode == 2) {
                        if ($acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete) {
                            if ($acces_management->allow_delete) {
                                $action = '<button type="button" value="' . $dtRow['user_id'] . '" name="remove" onclick="UserDeleteDialog(\'' . base64_encode($dtRow['user_id']) . '\');" class="btn btn-danger btn-sm delete"'
                                        . ($PreDatedisabled && $PostDatedisabled ? " disabled " : "") . ' ><i class="fa fa-times"></i></button>';
                            }
                        }
                    } else {
                        $action = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="selected_id[]" id="modalchk' . $dtRow['user_id'] . '" value="' . $dtRow['user_id'] . '" onclick="SelectedtestUsers(' . $dtRow['user_id'] . ')"';
                        if (count($TestArray) > 0 && in_array($dtRow['user_id'], $TestArray)) {
                            $action .= "checked";
                        }
                        $action .='/><span></span></label>';
                        $row[] = $action;
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

    public function users_submit() {
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to edit,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
//            if ($this->mw_session['company_id'] == "") {
//                $this->form_validation->set_rules('company_id', 'Company name', 'required');
//                $Company_id = $this->input->post('company_id');
//            } else {
//                $Company_id = $this->mw_session['company_id'];
//            }
            $workshop_id = base64_decode($this->input->post('workshop_id'));
            $NewUsersArrray = $this->input->post('NewtestUsersArrray');
            if (count((array)$NewUsersArrray) == 0) {
                $Message = "Please select Device users";
                $SuccessFlag = 0;
            }

            if ($SuccessFlag) {
                foreach ($NewUsersArrray as $key => $id) {
                    $exitarray = $this->common_model->get_value('workshop_tester_users', 'tester_id', " tester_id=$id AND workshop_id=" . $workshop_id);
                    if (count((array)$exitarray) > 0) {
                        continue;
                    }
                    $data = array(
                        'workshop_id' => $workshop_id,
                        'tester_id' => $id,
                        'addeddate' => date('Y-m-d H:i:s'),
                        'addedby' => $this->mw_session['user_id']);

                    $this->common_model->insert('workshop_tester_users', $data);
                }
                if ($SuccessFlag) {
                    $Message = "Testing user added successfully.";
                } else {
                    $Message = "Error while adding Testing User,Contact Mediaworks for technical support.!";
                    $SuccessFlag = 0;
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function remove_user() {
        $alert_type = 'success';
        $message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $workshop_id = base64_decode($this->input->Post('workshop_id'));
            $deleted_id = base64_decode($this->input->Post('deleteid'));
            $this->common_model->delete_whereclause('workshop_tester_users', 'tester_id=' . $deleted_id . ' and workshop_id=' . $workshop_id);
            $message = "Testing User removed successfully.";
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function Removeall_participants($Encode_id) {
        $workshop_id = base64_decode($Encode_id);
        $action_id = $this->input->Post('Participant_all');
        $alert_type = 'success';
        $message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $message = "You have no rights to Edit Workshop,Contact Administrator for rights";
            $alert_type = 'error';
        } else {
            if (count($action_id) == 0) {
                $message = "Please select record from the list";
                $alert_type = 'error';
            } else {
                foreach ($action_id as $id) {
                    $this->common_model->delete_whereclause('workshop_users', 'id=' . $id . ' and workshop_id=' . $workshop_id);
                }
                $message = "Workshop Participant User(s) removed successfully";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function confirm_feedback_set($token_key) {
        $SuccessFlag = 1;
        $Message = '';
        $Qsession = $this->input->post('Qsession');
        $qus_orderby = $this->input->post('qus_orderby');
        $question_total = $this->input->post('question_total');
		$questions_limit = $this->input->post('questions_limit');
        $Qset_id = $this->input->post('Qset_id');
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        }elseif($questions_limit >$question_total){ 
			$Message = "Questions Limit can`t be more than total questions";
            $SuccessFlag = 0;
		}else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('Qsession', 'Session', 'required');
            $this->form_validation->set_rules('Qset_id', 'ID', 'required');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            }
        }
        if ($SuccessFlag) {
            if ($Qsession == 1) {
                $_SESSION['Workshop_feedback_set_' . $token_key][$Qset_id]['pre_qus_orderby'] = $qus_orderby;
				$_SESSION['Workshop_feedback_set_' . $token_key][$Qset_id]['pre_qus_limit'] = $questions_limit;
            } else {
                $_SESSION['Workshop_feedback_set_' . $token_key][$Qset_id]['post_qus_orderby'] = $qus_orderby;
				$_SESSION['Workshop_feedback_set_' . $token_key][$Qset_id]['post_qus_limit'] = $questions_limit;
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function ConfirmQuestionsetTrainer($token_key) {
        $SuccessFlag = 1;
        $Message = '';
        //$TrainerArrray = array();
        $acces_management = $this->acces_management;
        $Qset_id = $this->input->post('Qset_id');
        $qus_lockflag = $this->input->post('qus_lockflag');
        $question_total = $this->input->post('question_total');
		$questions_limit = $this->input->post('questions_limit');
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        }elseif($questions_limit >$question_total){ 
			$Message = "Questions Limit can`t be more than total questions";
            $SuccessFlag = 0;
        } else {
            $this->load->library('form_validation');
            if (!$qus_lockflag) {
                $this->form_validation->set_rules('qset_trainer[]', 'Trainer', 'required');
            }
            $this->form_validation->set_rules('Qsession', 'Session', 'required');
            $this->form_validation->set_rules('Qset_id', 'ID', 'required');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            }
        }

        if ($SuccessFlag) {
            $TrainerArrray = $this->input->post('qset_trainer');
            $Qsession = $this->input->post('Qsession');

            $Qsettrainer = $this->input->post('Qsettrainertableid');
            $update_array = $this->input->post('workshop_Ques_tra_id');

            $qus_orderby = $this->input->post('qus_orderby');
            //$workshop_id= $this->input->post('ques_workshop_id');

            if ($qus_orderby == "") {
                $qus_orderby = 1;
            }
            $data_array = array();
//            if(!isset($_SESSION['Workshop_Qus_set_' . $token_key][$Qset_id])){
//                $qus_lockflag=false;
//            }
            if (!$qus_lockflag || $Qsession == 1) {
                if (count($TrainerArrray) > 0) {
                    foreach ($TrainerArrray as $key => $trinerid) {
                        $data_array[$Qsettrainer[$key]] = array(
                            'questionset_id' => $Qset_id,
                            'trainer_id' => $trinerid,
                            'qsettrainertable_id' => $Qsettrainer[$key],
                            'workshop_Ques_upd_id' => $update_array[$key]
                        );
                    }
                }
                $_SESSION['Workshop_Qus_set_' . $token_key][$Qset_id]['trainer_List'] = $data_array;
            }
            if ($Qsession == 1) {
                $_SESSION['Workshop_Qus_set_' . $token_key][$Qset_id]['pre_qus_orderby'] = $qus_orderby;
				$_SESSION['Workshop_Qus_set_' . $token_key][$Qset_id]['pre_qus_limit'] = $questions_limit;
            } else {
                $_SESSION['Workshop_Qus_set_' . $token_key][$Qset_id]['post_qus_orderby'] = $qus_orderby;
				$_SESSION['Workshop_Qus_set_' . $token_key][$Qset_id]['post_qus_limit'] = $questions_limit;
            }
        }


        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function Question_tableRefresh($EncodeEdit_id, $Qset_id, $session, $isnew, $disabled) {
        $wrkshp_id = base64_decode($EncodeEdit_id);
        $session_id = ($session == 1 ? 'PRE' : 'POST');
        $PlayedFlag = $this->workshop_model->CheckQuestionSetPlayed($wrkshp_id, $Qset_id, $session_id);
        if ($PlayedFlag) {
            $disabled = true;
        }
        $Already_mapped_set = $this->common_model->get_value('workshop_questions', 'id', "workshop_id=" . $wrkshp_id . ' AND questionset_id=' . $Qset_id);
        if (count((array)$Already_mapped_set) > 0) {
            $isnew = 0;
        }
        if ($isnew == 1) {
            $dtSearchColumns = array('sorting', 'q.id', 'CONCAT(cu.first_name," ",cu.last_name)', 'qt.description', 'qs.description', 'q.question_title', 'correct_answer');
            $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        } else {
            $dtSearchColumns1 = array('sorting', 'q.question_id', 'CONCAT(cu.first_name," ",cu.last_name)', 'qt.description', 'qs.description', 'q.question_title', 'correct_answer');
            $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns1);
        }

        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($isnew == 1) {
            if ($dtWhere <> "") {
                $dtWhere .= " AND qtr.questionset_id= " . $Qset_id . " AND q.id NOT IN(select question_id FROM question_inactive where questionset_id=$Qset_id)";
            } else {
                $dtWhere .= " WHERE qtr.questionset_id= " . $Qset_id . " AND q.id NOT IN(select question_id FROM question_inactive where questionset_id=$Qset_id)";
            }
        } else {
            if ($dtWhere <> "") {
                $dtWhere .= " AND wt.workshop_id=" . $wrkshp_id . " AND wt.questionset_id= " . $Qset_id . " AND q.question_id NOT IN(select question_id FROM question_inactive where questionset_id=$Qset_id)";
            } else {
                $dtWhere .= " WHERE wt.workshop_id=" . $wrkshp_id . " AND wt.questionset_id= " . $Qset_id . " AND q.question_id NOT IN(select question_id FROM question_inactive where questionset_id=$Qset_id)";
            }
        }
        $DtQuestionArray = $this->workshop_model->getQuestionLoadData($dtWhere, $dtOrder, $dtLimit, $wrkshp_id, $isnew);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DtQuestionArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DtQuestionArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('sorting', 'question_id', 'trainer_name', 'topic', 'subtopic', 'question_title', 'correct_answer');
        foreach ($DtQuestionArray['ResultSet'] as $dtRow) {
            $row = array();
            $Qst_id = $dtRow['question_id'];
            $TotalHeader = count($dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "question_id") {
                    $row[] = $Qst_id . '<input type="hidden"  name="question_id[]" value="' . $Qst_id . '"/>';
                    //($disabled ? "disabled" : "" )
                } elseif ($dtDisplayColumns[$i] == "sorting1") {
                    $row[] = '<input type="number" class="custom-sorting" min="1" name="sort[' . $Qst_id . ']" value="' . $dtRow[$dtDisplayColumns[$i]] . '" style="width: 75px;height: 31px;"/>';
                    //($disabled ? "disabled" : "" )
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function FeedbackQuestion_tableRefresh($EncodeEdit_id, $Qset_id, $session, $isnew, $disabled) {
        $wrkshp_id = base64_decode($EncodeEdit_id);
        $session_id = ($session == 1 ? 'PRE' : 'POST');
        $PlayedFlag = $this->workshop_model->CheckFeedbackSetPlayed($wrkshp_id, $Qset_id, $session_id);
        if ($PlayedFlag) {
            $disabled = true;
        }
        if ($isnew == 1) {
            $dtSearchColumns = array('wq.sorting', 'wq.id', 'qt.description', 'qs.description', 'wq.question_type', 'wq.question_title', '', '');
            $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        } else {
            $dtSearchColumns1 = array('wq.sorting', 'wq.question_id', 'qt.description', 'qs.description', 'wq.question_type', 'wq.question_title', '', '');
            $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns1);
        }
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($isnew == 1) {
            if ($dtWhere <> "") {
                $dtWhere .= " AND ft.feedbackset_id= $Qset_id AND wq.id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id=$Qset_id) ";
            } else {
                $dtWhere .= " WHERE ft.feedbackset_id= $Qset_id AND wq.id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id=$Qset_id) ";
            }
        } else {
            if ($dtWhere <> "") {
                $dtWhere .= " AND wq.workshop_id=" . $wrkshp_id . " AND wq.feedbackset_id = $Qset_id AND wq.question_id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id=$Qset_id) ";
            } else {
                $dtWhere .= " WHERE wq.workshop_id=" . $wrkshp_id . " AND wq.feedbackset_id = $Qset_id AND wq.question_id NOT IN(select question_id FROM feedback_questions_inactive where feedbackset_id=$Qset_id) ";
            }
        }

        $DtQuestionArray = $this->workshop_model->getFeedbackQuestionLoadData($dtWhere, $dtOrder, $dtLimit, $wrkshp_id, $isnew);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DtQuestionArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DtQuestionArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('sorting', 'question_id', 'ftype', 'fsubtype', 'question_type', 'question_title', 'hide2');
        foreach ($DtQuestionArray['ResultSet'] as $dtRow) {
            $row = array();
            $Qst_id = $dtRow['question_id'];
            $TotalHeader = count($dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "question_id") {
                    $row[] = $Qst_id . '<input type="hidden"  name="question_id[]" value="' . $Qst_id . '"/>';
                    //($disabled ? "disabled" : "" )
                } elseif ($dtDisplayColumns[$i] == "sorting1") {
                    $row[] = '<input class="sortclass custom-sorting" min="0" type="number" name="sort[' . $Qst_id . ']" value="' . $dtRow[$dtDisplayColumns[$i]] . '" style="width: 75px;height: 31px;"' . ($disabled ? "disabled" : "" ) . '/>';
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function update_sorting($EncodeEdit_id, $type) {
//        echo "<pre>";
//        print_r($_POST);
//        exit;
        $SuccessFlag = 1;
        $workshop_id = base64_decode($EncodeEdit_id);
        $sort = $this->input->post('question_id');
        $qus_orderby = $this->input->post('qus_orderby');
        $question_total = $this->input->post('question_total');
        $questions_limit = $this->input->post('questions_limit');
        $Qset_id = $this->input->post('Qset_id');
        $token_key = $this->input->post('token_key');
        $now = date('Y-m-d H:i:s');
        $message = '';
        $acces_management = $this->acces_management;
        $Qsession = $this->input->post('Qsession');
        if (!$acces_management->allow_edit) {
            $message = 'You have no rights to Edit,Contact Administrator for details.';
            $SuccessFlag = 0;
        } 
        elseif($questions_limit >$question_total){ 
            $Message = "Questions Limit can`t be more than total questions";
            $SuccessFlag = 0;
        }else {
            if ($SuccessFlag) {
                $tdata = array('questions_order' => $qus_orderby,'questions_limit'=>$questions_limit);
                $postupdate = 0;
                /*if ($type == 1) {
                    if ($Qsession == 1) {
                        $Already_mapped_set1 = $this->common_model->get_value('workshop_questionset_pre', 'id', "workshop_id=" . $workshop_id . ' AND questionset_id=' . $Qset_id);
                        if (count($Already_mapped_set1) > 0) {
                            $this->common_model->update('workshop_questionset_pre', 'id', $Already_mapped_set1->id, $tdata);
                            //$postupdate=1;
                        }
                        $_SESSION['Workshop_Qus_set_' . $token_key][$Qset_id]['pre_qus_orderby'] = $qus_orderby;
                        $_SESSION['Workshop_Qus_set_' . $token_key][$Qset_id]['pre_qus_limit'] = $questions_limit;
                    } else {
                        $_SESSION['Workshop_Qus_set_' . $token_key][$Qset_id]['post_qus_orderby'] = $qus_orderby;
                        $_SESSION['Workshop_Qus_set_' . $token_key][$Qset_id]['post_qus_limit'] = $questions_limit;
                        $postupdate = 1;
                    }
                    if ($postupdate) {
                        $Already_mapped_set2 = $this->common_model->get_value('workshop_questionset_post', 'id', "workshop_id=" . $workshop_id . ' AND questionset_id=' . $Qset_id);
                        if (count($Already_mapped_set2) > 0) {
                            $this->common_model->update('workshop_questionset_post', 'id', $Already_mapped_set2->id, $tdata);
                        }
                    }
                } else {
                    if ($Qsession == 1) {
                        $Already_mapped_set1 = $this->common_model->get_value('workshop_feedbackset_pre', 'id', "workshop_id=" . $workshop_id . ' AND feedbackset_id=' . $Qset_id);
                        if (count($Already_mapped_set1) > 0) {
                            $this->common_model->update('workshop_feedbackset_pre', 'id', $Already_mapped_set1->id, $tdata);
                            $postupdate = 1;
                        }
                        $_SESSION['Workshop_feedback_set_' . $token_key][$Qset_id]['pre_qus_orderby'] = $qus_orderby;
                        $_SESSION['Workshop_feedback_set_' . $token_key][$Qset_id]['pre_qus_limit'] = $questions_limit;
                    } else {
                        $_SESSION['Workshop_feedback_set_' . $token_key][$Qset_id]['post_qus_orderby'] = $qus_orderby;
                        $_SESSION['Workshop_feedback_set_' . $token_key][$Qset_id]['post_qus_limit'] = $questions_limit;
                        $postupdate = 1;
                    }
                    if ($postupdate) {
                        $Already_mapped_set2 = $this->common_model->get_value('workshop_feedbackset_post', 'id', "workshop_id=" . $workshop_id . ' AND feedbackset_id=' . $Qset_id);
                        if (count($Already_mapped_set2) > 0) {
                            $this->common_model->update('workshop_feedbackset_post', 'id', $Already_mapped_set2->id, $tdata);
                        }
                    }
                }*/
                if ($qus_orderby == 2 && count($sort) > 0) {
                    $MappingFlag = 0;
                    if ($type == 1) {
                        $Already_mapped_set = $this->common_model->get_value('workshop_questions', 'id', "workshop_id=" . $workshop_id . ' AND questionset_id=' . $Qset_id);
                        if (count((array)$Already_mapped_set) > 0) {
                            $MappingFlag = 1;
                        }
                    } else {
                        $Already_mapped_set = $this->common_model->get_value('workshop_feedback_questions', 'id', "workshop_id=" . $workshop_id . ' AND feedbackset_id=' . $Qset_id);
                        if (count((array)$Already_mapped_set) > 0) {
                            $MappingFlag = 1;
                        }
                    }
                    $seq = 0;
                    foreach ($sort as $key => $question_id) {
                        $seq++;
                        $data = array(
                            'sorting' => $seq
                        );
                        if ($type == 1) {
                            if ($MappingFlag) {
                                $this->workshop_model->update_sorting('workshop_questions', 'workshop_id=' . $workshop_id . ' AND question_id=' . $question_id . ' AND questionset_id=' . $Qset_id, $data);
                            } else {
                                $this->workshop_model->update_temp_sorting($workshop_id, $Qset_id, $question_id, $seq, $type);
                            }
                        } else {
                            if ($MappingFlag) {
                                $this->workshop_model->update_sorting('workshop_feedback_questions', 'workshop_id=' . $workshop_id . ' AND question_id=' . $question_id . ' AND feedbackset_id=' . $Qset_id, $data);
                            } else {
                                $this->workshop_model->update_temp_sorting($workshop_id, $Qset_id, $question_id, $seq, $type);
                            }
                        }
                    }
                }
                $message = "Question order updated successfully..";
            }
        }
        $Rdata['Msg'] = $message;
        $Rdata['success'] = $SuccessFlag;
        echo json_encode($Rdata);
    }

}
