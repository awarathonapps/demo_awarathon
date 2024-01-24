<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Workshop_play_attendence extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('awarathon_session') == FALSE) {
            redirect('index');
        } else {
            $this->mw_session = $this->session->userdata('awarathon_session');
            $acces_management = CheckRights($this->mw_session['user_id'], 'workshop_play_attendence');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('workshop_play_attendence_model');
            $this->load->model('common_model');
        }
    }
    public function index() {
        $data['module_id'] = '9.22';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
            if (!$this->mw_session['superaccess']) {
                $trainer_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
            if ($WRightsFlag) {                                
                $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id);
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);                
                $data['WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);                
            }       
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('workshop_play_attendence/index', $data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('wru.user_id', 'CONCAT(du.firstname," ",du.lastname)','','wru.registered_date_time');
        $DTRenderArray = DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];        
        $dtOrder = $DTRenderArray['dtOrder'];
        if ($dtOrder == "") {
            $dtOrder = "ORDER BY wru.registered_date_time";
        }
        $dtLimit = $DTRenderArray['dtLimit'];
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = (isset($_GET['company_id']) ? $_GET['company_id'] : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE du.company_id  = " . $cmp_id;
            }
        } else {
            $output = array(
                "sEcho" => isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($output);
            exit;
        }
        $from_schdule = '';
        $to_schdule = '';
        $session_id = (isset($_GET['session_id']) ? $_GET['session_id'] : '');
        if($session_id !=''){                
            $dtWhere .= " AND wru.workshop_session  = '" . $session_id."'";                
        }
        $from_date = (isset($_GET['from_date']) ? $_GET['from_date'] : '');
        $from_time = (isset($_GET['from_time']) ? $_GET['from_time'] : '');
        if($from_time !=''){
            $fdate = date("Y-m-d", strtotime($from_date));
            $ftime = date("H:i:s", strtotime($from_time));
            $from_schdule = $fdate.' '.$ftime;
        }else{
            $fdate = date("Y-m-d", strtotime($from_date));
            $from_schdule = $fdate.' 00:00:00';
        }
        $to_date = (isset($_GET['to_date']) ? $_GET['to_date'] : '');
        $to_time = (isset($_GET['to_time']) ? $_GET['to_time'] : '');        
        if($to_time !=''){
            if($to_date !=''){
                $tdate = date("Y-m-d", strtotime($to_date));
            }else{
                $tdate = date("Y-m-d"); 
            }           
            $ttime = date("H:i:s", strtotime($to_time));
            $to_schdule = $tdate.' '.$ttime;
        }else{
            if($to_date !=''){
                $tdate = date("Y-m-d", strtotime($to_date));
            }else{
                $tdate = date("Y-m-d H:i:s");
            }                        
            $to_schdule = $tdate;
        }
        if($to_schdule == ''){
            $to_schdule = $date("Y-m-d H:i:s");
        }
        if($from_schdule !=''){                
            $dtWhere .= " AND (wru.registered_date_time between '" . $from_schdule."' and '".$to_schdule."') ";                
        }                
        $workshop_id = (isset($_GET['workshop_id']) ? $_GET['workshop_id'] : '');                
        if ($workshop_id != "") {
            $dtWhere .= " AND wru.workshop_id  = " . $workshop_id;
        }
        
        if (!$WRightsFlag) {
            $login_id = $this->mw_session['user_id'];
            $dtWhere .= " AND wru.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }        
        $DTRenderArray = $this->workshop_play_attendence_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
		$isFeedbackQ = array();
		if($cmp_id !='' && $workshop_id!=''){
			$isFeedbackQ   = $this->workshop_play_attendence_model->get_feedbackquestion($cmp_id,$workshop_id);
		}
        $output = array(
            "sEcho" => isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id','emp_id','trainee','registered_date_time','session_preclose_dttm','timespan');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "session_preclose_dttm") {
                    if ($dtRow['session_preclose_dttm'] == '00-00-0000 12:00:00' || $dtRow['session_preclose_dttm'] == '') {
                        $isclose = '-';
                    }else{
						if(count((array)$isFeedbackQ) > 0){
							if($dtRow['all_questions_fired'] == 1 && $dtRow['all_feedbacks_fired'] == 1){
								$isclose = $dtRow['session_preclose_dttm'];
							}else{
								$isclose = '-';
							}
						}else{
							if($dtRow['all_questions_fired'] == 1){
								$isclose = $dtRow['session_preclose_dttm'];
							}else{
								$isclose = '-';
							}
						}						                       
                    } 
                    $row[] = $isclose;
                }else if ($dtDisplayColumns[$i] == "timespan") {
					if($dtRow['end_date'] != ''){
						if(count((array)$isFeedbackQ) > 0){
							if($dtRow['all_questions_fired'] == 1 && $dtRow['all_feedbacks_fired'] == 1){
								$dateDiff=date_diff(date_create($dtRow['regdate']),date_create($dtRow['end_date']));
								$timespan = $dateDiff->format('%H:%I:%S');	
							}else{
								$timespan = '-';
							}
						}else{
							if($dtRow['all_questions_fired'] == 1){
								$dateDiff=date_diff(date_create($dtRow['regdate']),date_create($dtRow['end_date']));
								$timespan = $dateDiff->format('%H:%I:%S');	
							}else{
								$timespan = '-';
							}
						}																		
					}else{
						$timespan = '-';
					}
					$row[] = $timespan;
				}
				else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    function get_workshop_datetime(){
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $session_id = $this->input->post('session_id', TRUE);
		
        if($workshop_id !=''){
            $timedata = $this->workshop_play_attendence_model->get_workshopdatetime($company_id,$workshop_id);
                if($session_id == 'PRE'){					
					if($timedata->pre_start_date == '01-01-1970'){
						$data['from_date'] = "";
					}else{
						$data['from_date'] = $timedata->pre_start_date;
					}                    
                    $data['from_time'] = $timedata->pre_start_time;
                }else{
					if($timedata->post_start_date == '01-01-1970'){
						$data['from_date'] = "";
					}else{
						$data['from_date'] = $timedata->post_start_date;
					}                    
                    $data['from_time'] = $timedata->post_start_time;
                }
        }else{
            $data['from_date'] = "";
            $data['from_time'] = "";
        }
		
        echo json_encode($data);
    }
}
