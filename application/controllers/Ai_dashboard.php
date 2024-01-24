<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ai_dashboard extends MY_Controller {
	function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('ai_dashboard');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('common_model');
        $this->load->model('ai_dashboard_model');
        $this->load->model('emailtemplate_model');
    }
	
	public function index() {
        $data['module_id'] = '14.01';
        $data['acces_management'] = $this->acces_management;
        $_assessment_result = $this->common_model->get_selected_values('assessment_mst', 'id,assessment', 'status=1','assessment');
        $data['company_id'] = $this->mw_session['company_id'];
        $data['assessment_result'] = $_assessment_result;
		$data['step'] = 1;
        $this->load->view('ai_dashboard/index',$data);
    }

    public function fetch_statistics(){
        $_company_id            = $this->input->post('company_id', true);
        $_statistics_start_date = $this->input->post('statistics_start_date', true);
        $_statistics_end_date   = $this->input->post('statistics_end_date', true);
        $box_i_statistics       = 0;
        $box_ii_statistics      = 0;
        $box_iii_statistics     = 0;
        $box_iv_statistics      = 0;
        $box_v_statistics       = 0;
        $box_vi_statistics      = 0;
        $box_i_result =$this->ai_dashboard_model->get_box_i_statistics($_company_id,$_statistics_start_date,$_statistics_end_date);
        if (isset($box_i_result) AND count((array)$box_i_result)>0){
            $box_i_statistics = $box_i_result->total;
        }
        $box_vi_result = $this->ai_dashboard_model->get_box_vi_statistics($_company_id,$_statistics_start_date,$_statistics_end_date);
        if (isset($box_vi_result) AND count((array)$box_vi_result)>0){
            // $box_vi_statistics = $box_vi_result->questions;
            $box_vi_statistics = $box_vi_result->total;
        }
        $box_ii_result =$this->ai_dashboard_model->get_box_ii_statistics($_company_id,$_statistics_start_date,$_statistics_end_date);
        if (isset($box_ii_result) AND count((array)$box_ii_result)>0){
            $box_ii_statistics = $box_ii_result->total;
        }
        $box_iii_result =$this->ai_dashboard_model->get_box_iii_statistics($_company_id,$_statistics_start_date,$_statistics_end_date);
        if (isset($box_iii_result) AND count((array)$box_iii_result)>0){
            $box_iii_statistics = $box_iii_result->completed."/".$box_iii_result->played;
        }
        $box_iv_result =$this->ai_dashboard_model->get_box_iv_statistics($_company_id,$_statistics_start_date,$_statistics_end_date);
        if (isset($box_iv_result) AND count((array)$box_iv_result)>0){
            $box_iv_statistics = $box_iv_result->total;
        }
        $box_v_result =$this->ai_dashboard_model->get_box_v_statistics($_company_id,$_statistics_start_date,$_statistics_end_date);
        if (isset($box_v_result) AND count((array)$box_v_result)>0){
            $box_v_statistics = $box_v_result->total;
        }
        $output = json_decode('{"success": "true", "box_i_statistics":"'.$box_i_statistics.'","box_vi_statistics":"'.$box_vi_statistics.'","box_ii_statistics":"'.$box_ii_statistics.'","box_iii_statistics":"'.$box_iii_statistics.'","box_iv_statistics":"'.$box_iv_statistics.'","box_v_statistics":"'.$box_v_statistics.'"}');
        echo json_encode($output);
    }

    public function fetch_assessment(){
        $assessment_selected = $this->input->get('assessment_selected', true);
        $dtSearchColumns = array('am.id','am.assessment','art.description','am.start_dttm','am.end_dttm','am.status');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id =$this->mw_session['company_id'];
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND am.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE am.company_id  = " . $cmp_id;
            }
        }
        $superaccess = $this->mw_session['superaccess'];		
        $DTRenderArray = $this->ai_dashboard_model->LoadAssessmentDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('id','assessment', 'report_type','start_dttm', 'end_dttm', 'status', 'mapped', 'played', 'uploaded','processed', 'checkbox1','checkbox2', 'checkbox3', 'checkbox4');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        $assessment_ids = array_column($DTRenderArray['ResultSet'], 'id');
        $users_count = $this->ai_dashboard_model->getUserCount($cmp_id,$assessment_ids);
        $assessment_users_count = [];
        foreach($users_count as $user_cnt){
            $assessment_users_count[$user_cnt->assessment_id] = [
                'mapped' => $user_cnt->mapped, 
                'played' => $user_cnt->played
            ];
        }
        $videos_processed = $this->ai_dashboard_model->getVideoCount($cmp_id,$assessment_ids);
        $assessment_video_processed = [];
        foreach($videos_processed as $video_cnt){
            $assessment_video_processed[$video_cnt->assessment_id] = $video_cnt->total_video_processsed;
        }
        $videos_uploaded = $this->ai_dashboard_model->getUploadedVideos($cmp_id,$assessment_ids);
        $assessment_video_uploaded = [];
        foreach($videos_uploaded as $video_cnt){
            $assessment_video_uploaded[$video_cnt->assessment_id] = $video_cnt->total_video_uploaded;
        }
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $Curr_Time = strtotime($now);
			$assessment_id = $dtRow['id'];
			// $users_count = $this->ai_dashboard_model->getAssessmentUserCount($cmp_id,$assessment_id);
			// $video_count = $this->ai_dashboard_model->getAssessmentVideoCount($cmp_id,$assessment_id);
            // $video_upload_count= $this->ai_dashboard_model->getVideoUploaded($cmp_id,$assessment_id);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "assessment") {
					$row[] = '<a href="' . $site_url . 'ai_dashboard/candidates_list/' . base64_encode($dtRow['id']) . '" 
                                data-target="#LoadModalFilter-view" data-toggle="modal">' . $dtRow['assessment'] . ' </a>';
				} else if ($dtDisplayColumns[$i] == "status") {
                    if (strtotime($dtRow['start_dttm']) >= $Curr_Time) {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-info status-active" > Active </span>';
                        } else {
                            $status = '<span class="label label-sm label-danger status-active" > In-Active </span>';
                        }
                    } else if (strtotime($dtRow['end_dttm']) >= $Curr_Time) {
                        $status = '<span class="label label-sm  label-success " style="background-color: #5cb85c;" > Live </span>';
                    } else {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-danger " > Expired </span>';
                        } else {
                            $status = '<span class="label label-sm label-warning status-active" > In-Active </span>';
                        }
                    }
                    $row[] = $status;
				} else if($dtDisplayColumns[$i] == "checkbox1") {
                    if($dtRow['report_type'] == 'AI' || $dtRow['report_type'] == 'AI and Manual'){
                        $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                    <input type="checkbox" class="checkboxes" id="rank_id_'.$dtRow['id'].'" name="md_id[]" onclick="confirm_ranking('.$dtRow['id'].')" '.($dtRow['show_ranking'] ? 'checked="checked"' :'').' '.($dtRow['show_ranking'] ? 'disabled="disabled"' :'').'/>
                                <span></span>
                            </label>';
                    }else{
                        $row[] = '';
                    }
                }else if($dtDisplayColumns[$i] == "checkbox2") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" id="md_id_'.$dtRow['id'].'" name="md_id[]" onChange="save_ai_cronreports(1, '.$dtRow['id'].');" '.($dtRow['show_dashboard'] ? 'checked="checked"' :'').'/>
                                <span></span>
                            </label>';
                }else if($dtDisplayColumns[$i] == "checkbox3") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" id="rp_id_'.$dtRow['id'].'" name="rp_id[]" onChange="save_ai_cronreports(2, '.$dtRow['id'].');" '.($dtRow['show_reports'] ? 'checked="checked"' :'').'/>
                                <span></span>
                            </label>';
                }else if($dtDisplayColumns[$i] == "checkbox4") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" id="pwa_id_'.$dtRow['id'].'" name="pwa_id[]" onChange="save_ai_cronreports(3, '.$dtRow['id'].');" '.($dtRow['show_pwa'] ? 'checked="checked"' :'').'/>
                                <span></span>
                            </label>';
                } else if($dtDisplayColumns[$i] == "mapped") {
                    $row[] = (!empty($assessment_users_count) && isset($assessment_users_count[$assessment_id])) ? $assessment_users_count[$assessment_id]['mapped'] : 0;
                } else if($dtDisplayColumns[$i] == "played") {
                    $row[] = (!empty($assessment_users_count) && isset($assessment_users_count[$assessment_id])) ? $assessment_users_count[$assessment_id]['played'] : 0;
                } else if($dtDisplayColumns[$i] == "uploaded") {
                    $row[] = (!empty($assessment_video_uploaded) && isset($assessment_video_uploaded[$assessment_id])) ? $assessment_video_uploaded[$assessment_id] : 0;
                }else if($dtDisplayColumns[$i] == "processed") {
                    $row[] = (!empty($assessment_video_processed) && isset($assessment_video_processed[$assessment_id])) ? $assessment_video_processed[$assessment_id] : 0;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function DatatableRefresh_ideal() {
        $dtSearchColumns = array('am.id','am.assessment','art.description','am.start_dttm','am.end_dttm','am.status');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id =$this->mw_session['company_id'];
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND am.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE am.company_id  = " . $cmp_id;
            }
        }
        $superaccess = $this->mw_session['superaccess'];
        $DTRenderArray = $this->ai_dashboard_model->LoadAssessmentDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        //$dtDisplayColumns = array('checkbox', 'id','assessment', 'report_type','start_dttm', 'end_dttm', 'status', 'mapped', 'played', 'uploaded','processed');
        $dtDisplayColumns = array( 'id','assessment','report_type','start_dttm','end_dttm','status','que_mapped','mapped','played','uploaded','processed');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        $assessment_ids = array_column($DTRenderArray['ResultSet'], 'id');
        $users_count = $this->ai_dashboard_model->getUserCount($cmp_id,$assessment_ids);
        $assessment_users_count = [];
        foreach($users_count as $user_cnt){
            $assessment_users_count[$user_cnt->assessment_id] = [
                'mapped' => $user_cnt->mapped, 
                'played' => $user_cnt->played
            ];
        }
        $videos_processed = $this->ai_dashboard_model->getVideoCount($cmp_id,$assessment_ids);
        $assessment_video_processed = [];
        foreach($videos_processed as $video_cnt){
            $assessment_video_processed[$video_cnt->assessment_id] = $video_cnt->total_video_processsed;
        }
        $videos_uploaded = $this->ai_dashboard_model->getUploadedVideos($cmp_id,$assessment_ids);
        $assessment_video_uploaded = [];
        foreach($videos_uploaded as $video_cnt){
            $assessment_video_uploaded[$video_cnt->assessment_id] = $video_cnt->total_video_uploaded;
        }
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $Curr_Time = strtotime($now);
			$assessment_id = $dtRow['id'];
			// $users_count = $this->ai_dashboard_model->getAssessmentUserCount($cmp_id,$assessment_id);
			// $video_count = $this->ai_dashboard_model->getAssessmentVideoCount($cmp_id,$assessment_id);
            // $video_upload_count= $this->ai_dashboard_model->getVideoUploaded($cmp_id,$assessment_id);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "assessment") {
					$row[] = '<a href="' . $site_url . 'ai_dashboard/ideal_list/' . base64_encode($dtRow['id']) . '" 
                                data-target="#LoadModalFilter_ideal" data-toggle="modal">' . $dtRow['assessment'] . ' </a>';
				} else if ($dtDisplayColumns[$i] == "status") {
                    if (strtotime($dtRow['start_dttm']) >= $Curr_Time) {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-info status-active" > Active </span>';
                        } else {
                            $status = '<span class="label label-sm label-danger status-active" > In-Active </span>';
                        }
                    } else if (strtotime($dtRow['end_dttm']) >= $Curr_Time) {
                        $status = '<span class="label label-sm  label-success " style="background-color: #5cb85c;" > Live </span>';
                    } else {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-danger " > Expired </span>';
                        } else {
                            $status = '<span class="label label-sm label-warning status-active" > In-Active </span>';
                        }
                    }
                    $row[] = $status;
				} else if($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['id'] . '"/>
                                <span></span>
                            </label>';
                } else if($dtDisplayColumns[$i] == "mapped") {
                    $row[] = (!empty($assessment_users_count) && isset($assessment_users_count[$assessment_id])) ? $assessment_users_count[$assessment_id]['mapped'] : 0;
                } else if($dtDisplayColumns[$i] == "played") {
                    $row[] = (!empty($assessment_users_count) && isset($assessment_users_count[$assessment_id])) ? $assessment_users_count[$assessment_id]['played'] : 0;
                } else if($dtDisplayColumns[$i] == "uploaded") {
                    $row[] = (!empty($assessment_video_uploaded) && isset($assessment_video_uploaded[$assessment_id])) ? $assessment_video_uploaded[$assessment_id] : 0;
                }else if($dtDisplayColumns[$i] == "processed") {
                    $row[] = (!empty($assessment_video_processed) && isset($assessment_video_processed[$assessment_id])) ? $assessment_video_processed[$assessment_id] : 0;
                } else if($dtDisplayColumns[$i] == "que_mapped") {
                    $row[] = $dtRow['que_mapped'];
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    } 

	public function DatatableRefresh_send() {
        $dtSearchColumns = array('am.id','am.id','am.assessment','art.description','am.start_dttm','am.end_dttm','am.status');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id =$this->mw_session['company_id'];
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND am.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE am.company_id  = " . $cmp_id;
            }
        }
        $superaccess = $this->mw_session['superaccess'];
        $DTRenderArray = $this->ai_dashboard_model->LoadAssessmentDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox','id','assessment','report_type','start_dttm','end_dttm','status','que_mapped','mapped','played','uploaded','processed','analysis','send');
        //$dtDisplayColumns = array( 'id','assessment', 'report_type','start_dttm', 'end_dttm', 'status', 'mapped', 'played', 'uploaded','processed');
        $site_url = base_url();
        $acces_management = $this->acces_management;
		$assessment_email_count = $this->ai_dashboard_model->getAssessmentEmailCount($cmp_id);
		$assessment_email_status = [];
		if(!empty($assessment_email_count)){
			foreach($assessment_email_count as $count){
				$assessment_email_status[$count->assessment_id] = [$count->sent, $count->scheduled];
			}
		}
        $assessment_ids = array_column($DTRenderArray['ResultSet'], 'id');
        $users_count = $this->ai_dashboard_model->getUserCount($cmp_id,$assessment_ids);
        $assessment_users_count = [];
        foreach($users_count as $user_cnt){
            $assessment_users_count[$user_cnt->assessment_id] = [
                'mapped' => $user_cnt->mapped, 
                'played' => $user_cnt->played
            ];
        }
        $videos_processed = $this->ai_dashboard_model->getVideoCount($cmp_id,$assessment_ids);
        $assessment_video_processed = [];
        foreach($videos_processed as $video_cnt){
            $assessment_video_processed[$video_cnt->assessment_id] = $video_cnt->total_video_processsed;
        }
        $videos_uploaded = $this->ai_dashboard_model->getUploadedVideos($cmp_id,$assessment_ids);
        $assessment_video_uploaded = [];
        foreach($videos_uploaded as $video_cnt){
            $assessment_video_uploaded[$video_cnt->assessment_id] = $video_cnt->total_video_uploaded;
        }
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $Curr_Time = strtotime($now);
			$assessment_id = $dtRow['id'];
			// $users_count = $this->ai_dashboard_model->getAssessmentUserCount($cmp_id,$assessment_id);
			// $video_count = $this->ai_dashboard_model->getAssessmentVideoCount($cmp_id,$assessment_id);
            // $video_upload_count= $this->ai_dashboard_model->getVideoUploaded($cmp_id,$assessment_id);
            for ($i = 0; $i < $TotalHeader; $i++) {
				if($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['id'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "assessment") {
					$row[] = '<a href="' . $site_url . 'ai_dashboard/candidates_list_send/' . base64_encode($dtRow['id']) . '" 
                                data-target="#LoadModalFilter-view" data-toggle="modal">' . $dtRow['assessment'] . ' </a>';
				} else if ($dtDisplayColumns[$i] == "status") {
                    if (strtotime($dtRow['start_dttm']) >= $Curr_Time) {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-info status-active" > Active </span>';
                        } else {
                            $status = '<span class="label label-sm label-danger status-active" > In-Active </span>';
                        }
                    } else if (strtotime($dtRow['end_dttm']) >= $Curr_Time) {
                        $status = '<span class="label label-sm  label-success " style="background-color: #5cb85c;" > Live </span>';
                    } else {
                        if ($dtRow['status']) {
                            $status = '<span class="label label-sm label-danger " > Expired </span>';
                        } else {
                            $status = '<span class="label label-sm label-warning status-active" > In-Active </span>';
                        }
                    }
                    $row[] = $status;
				} else if($dtDisplayColumns[$i] == "que_mapped") {
                    $row[] = $dtRow['que_mapped'];
                } else if($dtDisplayColumns[$i] == "mapped") {
                    $row[] = (!empty($assessment_users_count) && isset($assessment_users_count[$assessment_id])) ? $assessment_users_count[$assessment_id]['mapped'] : 0;
                } else if($dtDisplayColumns[$i] == "played") {
                    $row[] = (!empty($assessment_users_count) && isset($assessment_users_count[$assessment_id])) ? $assessment_users_count[$assessment_id]['played'] : 0;
                } else if($dtDisplayColumns[$i] == "uploaded") {
                    $row[] = (!empty($assessment_video_uploaded) && isset($assessment_video_uploaded[$assessment_id])) ? $assessment_video_uploaded[$assessment_id] : 0;
                }else if($dtDisplayColumns[$i] == "processed") {
                    $row[] = (!empty($assessment_video_processed) && isset($assessment_video_processed[$assessment_id])) ? $assessment_video_processed[$assessment_id] : 0;
                } else if($dtDisplayColumns[$i] == "analysis") {
                    // $row[] = isset($assessment_email_status[$dtRow['id']]) ? $assessment_email_status[$dtRow['id']][0].'/'.$assessment_email_status[$dtRow['id']][1] : '0/0';
                    $row[] = isset($assessment_email_status[$dtRow['id']]) ? $assessment_email_status[$dtRow['id']][1] : '0';
                } elseif ($dtDisplayColumns[$i] == "send") {
					$row[] = '<a onClick="scheduleEmail('.$cmp_id.','.$dtRow['id'].')"> Send </a>';
				} else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function save_ai_cronreports($assessment_id){
		$success = 1;
		$message = '';
		$target = $this->input->post('target', true);
		$value = $this->input->post('value', true);
		$column = ($target == 1 ? 'show_dashboard' : ($target == 2 ? 'show_reports' : ($target == 3 ? 'show_pwa' : 'show_ranking')));
		if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $company_id =$this->mw_session['company_id'];
        }
		try{
			$ai_cronreport_result = $this->common_model->get_value('ai_cronreports', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'"');
			if(!empty($ai_cronreport_result)){
				//update the value
				$id = $ai_cronreport_result->id;
				$data = [$column => $value];
				$this->common_model->update('ai_cronreports', 'id', $id, $data);
			}else{
				//insert the value
				$data = [
							'company_id' => $company_id,
							'assessment_id' => $assessment_id,
							$column => $value
						];
				$this->common_model->insert('ai_cronreports', $data);
			}
			$message = 'Assessment view settings updated successfully!';
		}catch(Exception $e) {
			$success = 0;
			$message = 'Error occured while updating assessment view settings!';
		}
		$response = [
				'success' => $success,
				'message' => $message
			];
		echo json_encode($response);
	}

    public function CandidateDatatableRefresh($assessment_id,$is_send_tab){
		// $dtSearchColumns = array('ar.user_id', 'ar.user_id', "CONCAT(du.firstname,' ',du.lastname)", 'du.email');
		$dtSearchColumns = array('user_id', 'user_id', 'user_name', 'email', 'mobile', 'user_id', 'user_id', 'user_id');
		$DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $data['assessment_id']=$assessment_id;
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');
		if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $company_id =$this->mw_session['company_id'];
        }
        if ($company_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.company_id  = " . $company_id;
            } else {
                $dtWhere .= " WHERE ar.company_id  = " . $company_id;
            }
        }
		$DTRenderArray = $this->ai_dashboard_model->get_distinct_participants($company_id,$assessment_id);
		$output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
		$report_type_result = $this->common_model->get_value('assessment_mst', 'report_type', 'id="'.$assessment_id.'"');
		$report_type = 0;
		if(isset($report_type_result) AND count((array)$report_type_result)>0){
			$report_type = (int)$report_type_result->report_type;
		}
		$dtDisplayColumns = array('checkbox', 'user_id', 'user_name', 'email', 'mobile');
		if ($report_type==1 OR $report_type==3 OR $report_type==0){
			array_push($dtDisplayColumns,'Report_ai');
		}
		if ($report_type==2 OR $report_type==3 OR $report_type==0){
			array_push($dtDisplayColumns,'Report_manual');
		}
		if ($report_type==3 OR $report_type==0){
			array_push($dtDisplayColumns,'Report_combine');
		}
		if ($is_send_tab){
			array_push($dtDisplayColumns,'Status');
		}
        $site_url = base_url();
		if($this->mw_session['company_id'] == ""){
			$Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
			$company_id = $Company->company_id;
		}else{
			$company_id = $this->mw_session['company_id'];
		}
		$total_questions_played        = 0;
		$total_task_completed          = 0;
		$total_manual_rating_completed = 0;
		$show_ai_pdf                   = false;
		$show_manual_pdf               = false;
		$is_schdule_running            = false;
		$show_reports_flag             = true;
		$_total_played_result     = $this->common_model->get_value('assessment_results', 'count(*) as total', "company_id = '".$company_id."' AND assessment_id = '".$assessment_id."' AND trans_id > 0 AND question_id > 0 AND vimeo_uri!='' AND ftp_status=1");
		if (isset($_total_played_result) AND count((array)$_total_played_result)>0){
			$total_questions_played = $_total_played_result->total;
		}
		$_tasksc_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND xls_imported="1" AND company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'"');
		if (isset($_tasksc_results) AND count((array)$_tasksc_results)>0){
			$total_task_completed = $_tasksc_results->total;
		}
		$_manualrate_results     = $this->common_model->get_value('assessment_results_trans', 'count(DISTINCT user_id,question_id) as total', 'assessment_id="'.$assessment_id.'"');
		if (isset($_manualrate_results) AND count((array)$_manualrate_results)>0){
			$total_manual_rating_completed = $_manualrate_results->total;
		}
		$_schdule_running_result     = $this->common_model->get_value('ai_cronjob', '*', 'assessment_id="'.$assessment_id.'"');
		if (isset($_schdule_running_result) AND count((array)$_schdule_running_result)>0){
			$is_schdule_running = true;
		}
		if (((int)$total_questions_played >= (int)$total_task_completed) AND ((int)$total_task_completed>0) AND ($is_schdule_running==false)) {
			$show_ai_pdf = true;
		}
		if ((int)$total_questions_played >= (int)$total_manual_rating_completed){
			$show_manual_pdf = true;
		}
		$user_rating = $this->common_model->get_selected_values('assessment_results_trans', 'DISTINCT user_id,question_id', 'assessment_id="'.$assessment_id.'"');
		$TotalHeader = count((array)$dtDisplayColumns);
		$Curr_Time = strtotime($now);
		$user_rating_array = json_decode(json_encode($user_rating), true);
		$userid_rating_array = array_column($user_rating_array, 'user_id');
        foreach ($DTRenderArray['ResultSet'] as $dtRow){
		    $user_id= $dtRow['user_id'];
            //Added
			$row = array();
            $_score_imported = false;    
            $_xls_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND xls_imported="1" AND company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'"');
            if (isset($_xls_results) AND count((array)$_xls_results)>0){
                if ((int)$_xls_results->total>0){
                $_score_imported = true;
                }
            }                                
            $pdf_icon = "";
            $mpdf_icon = "";
            $cpdf_icon = "";
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "Report_ai") {
                    if ($show_reports_flag==false){
                        $pdf_icon        = '<span style="height: 25px;width: 25px;background: #004369;padding: 9px;color: #ffffff;">PP</span>';          
                    }else if ($show_reports_flag==true AND $show_ai_pdf==true AND $_score_imported==true){
                        $pdf_icon        = '<a href="'.base_url().'/ai_reports/view_ai_reports/'.$company_id.'/'.$assessment_id.'/'. $user_id.'" target="_blank"><img src="'.base_url().'/assets/images/pdf2.png" style="height:21px;width:21px;" /></a>';
                    }else{
                        $pdf_icon    = '<span style="height: 25px;width: 25px;background: #db1f48;padding: 9px;color: #ffffff;">SP</span>';
                    }
					$row[] = $pdf_icon;
                }
                elseif ($dtDisplayColumns[$i] == "Report_manual") {
                    if ($show_reports_flag==false){
                        $mpdf_icon        = '<span style="height: 25px;width: 25px;background: #004369;padding: 9px;color: #ffffff;">PP</span>';
                    }else if ($show_reports_flag== true AND $show_manual_pdf){
						if(in_array($user_id, $userid_rating_array)){
							$mpdf_icon       = '<a href="'.base_url().'/ai_reports/view_manual_reports/'.$company_id.'/'.$assessment_id.'/'. $user_id.'" target="_blank"><img src="'.base_url().'/assets/images/pdf2.png" style="height:21px;width:21px;" /></a>';
						}else{
							$mpdf_icon        = '<span style="height: 25px;width: 25px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span>';        
						}
                    }else{
                        $mpdf_icon        = '<span style="height: 25px;width: 25px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span>';
                    }
                    $row[] = $mpdf_icon;
                }
                elseif ($dtDisplayColumns[$i] == "Report_combine") {
                    if ($show_reports_flag==false){
                        $cpdf_icon        = '<span style="height: 25px;width: 25px;background: #004369;padding: 9px;color: #ffffff;">PP</span>';
                    }else if ($show_reports_flag==true AND $show_ai_pdf==true AND $_score_imported==true){
                        if ($show_manual_pdf){
                            if(in_array($user_id, $userid_rating_array)){
								$cpdf_icon       = '<a href="'.base_url().'/ai_reports/view_combine_reports/'.$company_id.'/'.$assessment_id.'/'. $user_id.'" target="_blank"><img src="'.base_url().'/assets/images/pdf2.png" style="height:21px;width:21px;" /></a>';
							}else{
								$cpdf_icon        = '<span style="height: 25px;width: 25px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span>';        
							}
                        }else{
                            $cpdf_icon        = '<span style="height: 25px;width: 25px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span>';
                        }
                    }else{
                        $cpdf_icon        = '<span style="height: 25px;width: 25px;background: #db1f48;padding: 9px;color: #ffffff;">SP</span>';
                    }
                    $row[] = $cpdf_icon;
                }
                else if($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="candidate_id[]" value="' . $dtRow['user_id'] . '"/>
                                <span></span>
                            </label>';
                } 
				else if($dtDisplayColumns[$i] == "Status") {
					if($dtRow['is_sent']){
						$row[] = '<span style="height: 25px;width: 25px;background: #4caf50;padding: 9px;color: #ffffff;">Sent</span>';
					}else if($dtRow['is_sent']==='0' && $dtRow['attempt']>0){
						$row[] = '<span style="height: 25px;width: 25px;background: #db1f48;padding: 9px;color: #ffffff;">Failed</span>';
					}else if($dtRow['is_sent']==='0'){
						$row[] = '<span style="height: 25px;width: 25px;background: #004369;padding: 9px;color: #ffffff;">Scheduled</span>';
					}else{
						$row[] = '';
					}
                } 
                elseif ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
	}

    public function QuestionDatatableRefresh($assessment_id){
		$dtSearchColumns = array('question_id','aq.question', "CONCAT('https://player.vimeo.com/video/',ar.vimeo_uri)", 'abv.best_video_link');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $data['assessment_id']=$assessment_id;
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $now = date('Y-m-d H:i:s');
        $dtWhere='';
		if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id =$this->mw_session['company_id'];
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE ar.company_id  = " . $cmp_id;
            }
        }
        $question_arr = $this->ai_dashboard_model->get_question($assessment_id);
        $question_details=[];
        $x=0;
        foreach($question_arr as $qd)
        {
            $question_id = $qd->question_id;        
            $question_list=$this->ai_dashboard_model->LoadQuestionDataTableRefresh($assessment_id, $question_id);
            $ideal_video= $this->ai_dashboard_model->ideal_url_link($assessment_id, $question_id);
			if(!empty($question_list)){
				$question_details[$x]['question_id']= $question_list->question_id;
				$question_details[$x]['question']= !empty($question_list->question)?$question_list->question:'';
				$question_details[$x]['best_url']= !empty($question_list->best_url)?$question_list->best_url:'';
				$question_details[$x]['ideal_url']= !empty($ideal_video->ideal_url)?$ideal_video->ideal_url:'';
			}else{
				$question_details[$x]['question_id']= $question_id;
				$question_details[$x]['question']= !empty($qd->question)?$qd->question:'';
				$question_details[$x]['best_url']= '';
				$question_details[$x]['ideal_url']= '';
			}
			$x++;
        }
        $DTRenderArray=$question_details;
		$output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => count((array)$DTRenderArray),
			"iTotalDisplayRecords" => 10,
            "aaData" => array()
        );
        // $dtDisplayColumns = array('question', 'best_url','ideal_url');
        $dtDisplayColumns = array('question_id','question', 'best_url','ideal_url');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray as $dtRow) {    
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);            
            for ($i = 0; $i < $TotalHeader; $i++) {
               if ($dtDisplayColumns[$i] == "ideal_url") {            
                    $row[] = '<input type="text"  name="ideal_url['.$dtRow['question_id']. ']" id="question_id" class="form-control input-sm"
                    value="'. $dtRow[$dtDisplayColumns[$i]]. '"/>';
                    
				}else if ($dtDisplayColumns[$i] == "question_id") {
                    $row[] = $dtRow['question_id'];
				}else if ($dtDisplayColumns[$i] == "question") {
                    $row[] = '<input type="hidden" style="display:none"  name= "question_id" id="question_id" class="form-control input-sm"
                    value="'. $dtRow['question_id']. '"/>'.$dtRow['question'];
				}else if ($dtDisplayColumns[$i] != ' ' AND isset($dtDisplayColumns)) {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
	}

    public function fetch_participants(){
        $html               = '';
        $company_id         = $this->input->post('company_id', true);
        $assessment_selected  = $this->input->post('assessment_selected', true);
        $asssessment_id     = $this->input->post('assessment_id', true);
        $report_type_result = $this->common_model->get_value('assessment_mst', 'report_type', 'company_id="'.$company_id.'" AND id="'.$asssessment_id.'"');
        $report_type        = 0;
        if (isset($report_type_result) AND count((array)$report_type_result)>0){
            $report_type = (int)$report_type_result->report_type;
        }
        $_participants_result         = $this->ai_dashboard_model->get_distinct_participants($company_id,$asssessment_id);
        $data['report_type']          = $report_type;
        $data['_participants_result'] = $_participants_result;
        $total_questions_played        = 0;
        $total_task_completed          = 0;
        $total_manual_rating_completed = 0;
        $show_ai_pdf                   = false;
        $show_manual_pdf               = false;
        $is_schdule_running            = false;
        $show_reports_flag             = false;
        $_total_played_result     = $this->common_model->get_value('assessment_results', 'count(*) as total', "company_id = '".$company_id."' AND assessment_id = '".$asssessment_id."' AND trans_id > 0 AND question_id > 0 AND vimeo_uri!='' AND ftp_status=1");
        if (isset($_total_played_result) AND count((array)$_total_played_result)>0){
            $total_questions_played = $_total_played_result->total;
        }
        $_tasksc_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND xls_imported="1" AND company_id="'.$company_id.'" AND assessment_id="'.$asssessment_id.'"');
        if (isset($_tasksc_results) AND count((array)$_tasksc_results)>0){
            $total_task_completed = $_tasksc_results->total;
        }
        $_manualrate_results     = $this->common_model->get_value('assessment_results_trans', 'count(DISTINCT user_id,question_id) as total', 'assessment_id="'.$asssessment_id.'"');
        if (isset($_manualrate_results) AND count((array)$_manualrate_results)>0){
            $total_manual_rating_completed = $_manualrate_results->total;
        }
        $_schdule_running_result     = $this->common_model->get_value('ai_cronjob', '*', 'assessment_id="'.$asssessment_id.'"');
        if (isset($_schdule_running_result) AND count((array)$_schdule_running_result)>0){
            $is_schdule_running = true;
        }
        $show_report_result = $this->common_model->get_value('ai_cronreports', 'id', 'company_id="'.$company_id.'" AND assessment_id="'.$asssessment_id.'" AND show_reports="1"');
        if (isset($show_report_result) AND count((array)$show_report_result)>0){
            $show_reports_flag = true;
        }
        if (((int)$total_questions_played >= (int)$total_task_completed) AND ((int)$total_task_completed>0)) {
            $show_ai_pdf = true;
        }
        if ((int)$total_questions_played == (int)$total_manual_rating_completed){
            $show_manual_pdf = true;
        }
        $data['show_reports_flag'] = $show_reports_flag;
        $data['show_ai_pdf']       = $show_ai_pdf;
        $data['show_manual_pdf']   = $show_manual_pdf;
        $html                      = $this->load->view('ai_dashboard/load_participants',$data,true);
        $output['html']            = $html;
        $output['success']         = "true";
        $output['message']         = "";
        echo json_encode($output);
    }

    public function candidates_list($Encode_id) {
        $data['assessment_id'] = base64_decode($Encode_id);
		$data['is_send'] = 0;
        if ($this->mw_session['company_id'] == "") {
            $Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];  
        }
		$report_type_result = $this->common_model->get_value('assessment_mst', 'report_type', 'company_id="'.$company_id.'" AND id="'.$data['assessment_id'].'"');
		$report_type = 0;
		if(isset($report_type_result) AND count((array)$report_type_result)>0){
			$report_type = (int)$report_type_result->report_type;
		}
		$data['report_type']=$report_type;
        $this->load->view('ai_dashboard/CandidateListModal', $data);
    }
	
	public function candidates_list_send($Encode_id) {
        $data['assessment_id'] = base64_decode($Encode_id);
        $data['is_send'] = 1;
        if ($this->mw_session['company_id'] == "") {
            $Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }
		$report_type_result = $this->common_model->get_value('assessment_mst', 'report_type', 'company_id="'.$company_id.'" AND id="'.$data['assessment_id'].'"');
		$report_type = 0;
		if(isset($report_type_result) AND count((array)$report_type_result)>0){
			$report_type = (int)$report_type_result->report_type;
		}
		$data['report_type']=$report_type;
        $this->load->view('ai_dashboard/CandidateListModal', $data);
    }

    public function ideal_list($Encode_id) {
        $data['assessment_id'] = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $Company = $this->common_model->get_value('assessment_mst', 'company_id', 'id=' . $data['assessment_id']);
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $this->load->view('ai_dashboard/IdealListModal', $data);
    }

    public function getemailbody(){
        $this->load->helper('form');
		$emailbody_data = $this->emailtemplate_model->emailbody('on_assessment_report_send');
		$data['emailbodys'] = $emailbody_data;
		$emailtemplate_data = $this->emailtemplate_model->fetch_all();
		$data['emailtemplates'] = $emailtemplate_data;
    	$response['email_content'] = $this->load->view('ai_dashboard/email_template',$data,true);
		echo json_encode($response);
    }
	
	public function update_template(){	
        $SuccessFlag = 1;
        $Message = ''; 
        $Company_id = $this->mw_session['company_id'];
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');            
        $this->form_validation->set_rules('label', 'Lebel', 'required');
        $this->form_validation->set_rules('subject', 'Subject', 'required');
        $this->form_validation->set_rules('message', 'Message', 'required');        
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            if($SuccessFlag){
                $data = array(
                    'alert_title'   => $this->input->post('label'),
                    'subject'       => $this->input->post('subject'),
                    'message'       => $this->input->post('message'),
                    'fromname'      => $this->input->post('fromname'),
                    'fromemail'     => $this->input->post('fromemail'),
                    'company_id'    => $Company_id
                );
                $this->emailtemplate_model->update($data, 'on_assessment_report_send');
                $Message = "Successfully Update Email Template !.";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
	}

    public function submit(){
        $SuccessFlag = 1;
        $Message = 'Data saved successfully!';
        $acces_management = $this->acces_management;
        $assessment_id= $this->input->post('assessment_id');
        $ideal_url= $this->input->post('ideal_url');
        $ideal_url_check = $this->common_model->get_selected_values('ai_best_ideal_video', 'id,assessment_id,best_video_link', 'assessment_id=' . $assessment_id);
		if(count((array)$ideal_url_check)==0){
			foreach($ideal_url as $key=>$value){
				if(!empty($value)){
					$data = array(
						'assessment_id' => $this->input->post('assessment_id'),
						'question_id' => $key,
						'best_video_link' => $value,
					);
					$insert_id = $this->common_model->insert('ai_best_ideal_video', $data);
				}
			}
		}else{
			foreach($ideal_url as $key=>$value){
				$question_id=$key;
				$id_video=$this->common_model->get_selected_values('ai_best_ideal_video','best_video_link, id','assessment_id=' . $assessment_id . ' AND question_id=' . $question_id);   
				if(!empty($id_video)){
					$id = $id_video[0]->id;
					if(!empty($value)){
						$this->common_model->update('ai_best_ideal_video', 'id', $id, ['best_video_link' => $value]);
					}else{
						$this->common_model->delete('ai_best_ideal_video', 'id', $id);
					}
				}
			}
		}
		$response = [
			'success' => $SuccessFlag,
			'Msg' => $Message
		];
		echo json_encode($response);
    }
}