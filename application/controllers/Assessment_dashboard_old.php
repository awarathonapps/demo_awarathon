<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Assessment_dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('assessment_dashboard');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('assessment_dashboard_model');
    }

    public function index() {
        $data['module_id'] = '44.01';
        $data['acces_management'] = $this->acces_management;
        $data['company_id'] = $this->mw_session['company_id'];
        if ($data['company_id'] == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
        }

        $data['ThresholdData'] = $this->common_model->get_selected_values('company_threshold_range', 'id,range_from,range_to,range_color', 'company_id=' . $data['company_id']);
		$Threshold_array=array();
		if(count((array)$data['ThresholdData'])>0){
			foreach($data['ThresholdData'] as $value){
				$Threshold_array[$value->id]= array('range_from'=>$value->range_from,
				'range_to'=>$value->range_to,'range_color'=>$value->range_color,
				);
			}
		}
        $this->session->set_userdata('Assessment_threshold_session', $Threshold_array);
		
        $data['ResultData'] = $this->common_model->get_selected_values('company_threshold_result', 'result_from as range_from,result_to as range_to,result_color as range_color,assessment_status', 'company_id=' . $data['company_id']);
		$result_array=array();
		if(count((array)$data['ResultData'])>0){
			foreach($data['ResultData'] as $value){
				$result_array[$value->assessment_status]= array('range_from'=>$value->range_from,
				'range_to'=>$value->range_to,'range_color'=>$value->range_color,
				);
			}
		}
        $this->session->set_userdata('Assessment_result_session', $result_array);

        $data['region_data'] = $this->assessment_dashboard_model->get_trainee_region($data['company_id']);
		
        $data['start_date'] = date('01-01-Y');
        $data['end_date'] = date("t-m-Y");
        $start_date = date('Y-01-01');
        $end_date = date("Y-m-t");
        //$data['region_data'] = $this->assessment_dashboard_model->get_trainee_region($data['company_id'], '', '', $start_date, $end_date);
        $data['store_data'] = $this->assessment_dashboard_model->get_trainee_store($data['company_id']);
        
        $data['assessment_data'] = $this->assessment_dashboard_model->get_assessment($data['company_id'], '', $start_date, $end_date);
        $data['parameter_data'] = $this->assessment_dashboard_model->get_parameter();
        //$data['parameter'] = $this->common_model->get_selected_values('parameter_mst', 'id,description', 'status=1', 'description');
        $this->load->view('assessment_dashboard/index', $data);
    }

    public function getdashboardData() {
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $question_type = $this->input->post('question_type', true);
        $report_by = $this->input->post('report_by', true);
        $region_id = $this->input->post('region_id');
        $store_id = $this->input->post('store_id');

        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        $data=$this->getFilterDashboardData(1);
        $data['Total_Assessment'] = $this->assessment_dashboard_model->get_Total_Assessment($Company_id, $SDate, $EDate, $region_id, $store_id);
        $data['Candidate_Count'] = $this->assessment_dashboard_model->get_Candidate_Assessment($Company_id, $question_type, $report_by, $SDate, $EDate, $region_id, $store_id);
        $data['Avg_Accuracy'] = $this->assessment_dashboard_model->get_Average_Accuracy($Company_id,$report_by, $SDate, $EDate, $region_id, $store_id);
//        $hightlow_Accuracy = $this->assessment_dashboard_model->get_MaxMin_Accuracy($Company_id, $SDate, $EDate, $report_by);
//        $data['high_Accuracy'] = $hightlow_Accuracy->max_accuracy;
//        $data['low_Accuracy'] = $hightlow_Accuracy->min_accuracy;
        $TopFiveParameter = $this->assessment_dashboard_model->get_top_five_parameter($Company_id, $report_by, $SDate, $EDate, $region_id, $store_id);
        
        //--- High and Low Accuracy--//
        $data['high_Accuracy'] = 0;
        $data['low_Accuracy'] = 0;
        if(count((array)$TopFiveParameter) > 0){
            $data['high_Accuracy'] = $TopFiveParameter[0]->result;
            $existid = ($report_by == 1 ? $TopFiveParameter[0]->parameter_id : $TopFiveParameter[0]->assessment_id);
            $minSet = $this->assessment_dashboard_model->get_bottom_five_parameter($Company_id, $report_by, $existid, $SDate, $EDate, $region_id, $store_id);
            if(count((array)$minSet) > 0){
            $data['low_Accuracy'] = $minSet[0]->result;
            }
        }
        //----- End -----//
        $top_five_para_id = "0,";
        $para_top_five_html = '';
        if (count((array)$TopFiveParameter) > 0) {
            foreach ($TopFiveParameter as $para_top) {
                if ($report_by == 1) {
                    $top_five_para_id .= $para_top->parameter_id . ",";
                } else {
                    $top_five_para_id .= $para_top->assessment_id . ",";
                }
                $para_top_five_html .= '<tr class="tr-background">';
                if ($report_by == 1) {
                    $para_top_five_html .= '<td class="wksh-td">' . $para_top->parameter . '</td>';
                } else {
                    $para_top_five_html .= '<td class="wksh-td">' . $para_top->assessment . '</td>';
                }
                $para_top_five_html .= '<td class="wksh-td">
                                                <span class="bold theme-font">' . $para_top->result . '%</span>
                                            </td>
                                        </tr>';
            }
        } else {
            $para_top_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }
        if ($top_five_para_id != '') {
            $top_five_para_id = substr($top_five_para_id, 0, strlen($top_five_para_id) - 1);
        }
        $data['para_top_five_html'] = $para_top_five_html;

        $BottomFiveParameter = $this->assessment_dashboard_model->get_bottom_five_parameter($Company_id, $report_by, $top_five_para_id, $SDate, $EDate, $region_id, $store_id);

        $para_bottom_five_html = '';
        if (count((array)$BottomFiveParameter) > 0) {
            foreach ($BottomFiveParameter as $para_bottom) {
                $para_bottom_five_html .= '<tr class="tr-background">';
                if ($report_by == 1) {
                    $para_bottom_five_html .= '<td class="wksh-td">' . $para_bottom->parameter . '</td>';
                } else {
                    $para_bottom_five_html .= '<td class="wksh-td">' . $para_bottom->assessment . '</td>';
                }
                $para_bottom_five_html .= '<td class="wksh-td">
                                                <span class="bold theme-font">' . $para_bottom->result . '%</span>
                                            </td>
                                        </tr>';
            }
        } else {
            $para_bottom_five_html .= '<tr class="tr-background">
                                        <td colspan="2" class="wksh-td">No Records Found</td>
                                    </tr>';
        }
        $data['para_bottom_five_table'] = $para_bottom_five_html;

        $Rdataset = array();
        $pass = 0;
        $fail = 0;
        $session_set =$this->session->userdata('Assessment_result_session');
        if (count((array)$session_set) > 0) {
            $passrange_from = $session_set['Pass']['range_from'];
            $passrange_to =$session_set['Pass']['range_to'];
            $failrange_from = $session_set['Fail']['range_from'];
            $failrange_to =$session_set['Fail']['range_to'];
            if($report_by==1){
                $region_data = $this->assessment_dashboard_model->overall_result_parameter(1,$passrange_from,$passrange_to,$failrange_from,
                        $failrange_to,$SDate,$EDate,'','','',$store_id);
            }else{
                $region_data = $this->assessment_dashboard_model->overall_result_assessment(1,$passrange_from,$passrange_to,$failrange_from,$failrange_to,$SDate,$EDate,'','','',$store_id);
            }
            if (count((array)$region_data['region_data']) > 0) {
                $pass_color_code= $this->session->userdata('Assessment_result_session')['Pass']['range_color'];
                $fail_color_code= $this->session->userdata('Assessment_result_session')['Fail']['range_color'];
                $pending_color_code= $this->session->userdata('Assessment_result_session')['Pending']['range_color'];
                 foreach ($region_data['region_data'] as $rkey => $regd) {
                    $total_ass =$regd->total_users;
					if($total_ass>0){
						$pass = number_format($regd->pass*100/$total_ass,2);
						$Rdataset[] = array('name'=>'Pass', 'y'=>$pass,'color'=>$pass_color_code,'u'=>($regd->pass!=''? $regd->pass:0));
						$fail = number_format($regd->fail*100/$total_ass,2);
						$Rdataset[] = array('name'=>'Fail', 'y'=>$fail,'color'=>$fail_color_code,'u'=>($regd->fail!=''? $regd->fail:0));
						$pending = number_format(($total_ass-($regd->fail+$regd->pass))*100/$total_ass,2);
						$Rdataset[] = array('name'=>'Pending', 'y'=>$pending,'color'=>$pending_color_code,'u'=>($total_ass-($regd->fail+$regd->pass)));
                    }
                    $Rdata['dataset'] = json_encode($Rdataset, JSON_NUMERIC_CHECK);
                    $data['overall_graph'] = $this->load->view('assessment_dashboard/overall_graph', $Rdata, true);
                 }
            }
        } else {
            $data['overall_graph'] = "";
        }
//           $regionchtml = '<option value="">All region</option>';
//            $RegionData = $this->assessment_dashboard_model->get_trainee_region($Company_id, '', '', $SDate, $EDate);
//            if (count((array)$RegionData) > 0) {
//                foreach ($RegionData as $value) {
//                    $regionchtml .= '<option value="' . $value->region_id . '">' . $value->region_name . '</option>';
//                }
//            }
//            $data['Regionchtml'] = $regionchtml;
            $assessmentchtml = '<option value="">All Assessment</option>';
            $AssessmentData = $this->assessment_dashboard_model->get_assessment($Company_id, '', $SDate, $EDate);

            if (count((array)$AssessmentData) > 0) {
                foreach ($AssessmentData as $value) {
                    $assessmentchtml .= '<option value="' . $value->assessment_id . '">' . $value->assessment . '</option>';
                }
            }
            $data['Assessmentchtml'] = $assessmentchtml;
        echo json_encode($data);
    }
    public function getRegionChart($returnflag=0) {
        $SDate = $this->input->post('StartDate', true);
        $StartDate = date('Y-m-d', strtotime($SDate));
        $EDate = $this->input->post('EndDate', true);
        $EndDate = date('Y-m-d', strtotime($EDate));
        $report_by = $this->input->post('report_by', true);
        
        $step = ($returnflag==0 ? $this->input->post('step', true) : 0);

        $region_id = $this->input->post('region_id');
        $store_id = $this->input->post('store_id');
//        $region_id_array = $this->input->post('region_id');
        $assessment_id_array = $this->input->post('assessment_id');
//        $region_id='';
//        if($region_id_array !='' && count((array)$region_id_array)>0){
//            $region_id = implode(",", $region_id_array);
//        }
        $assessment_id='';
        if($assessment_id_array !='' && count((array)$assessment_id_array)>0){
            $assessment_id = implode(",", $assessment_id_array);
        }
           
        $html_graph = '';
        $session_set =$this->session->userdata('Assessment_result_session');
        if (count((array)$session_set) > 0) {
            $passrange_from = $session_set['Pass']['range_from'];
            $passrange_to =$session_set['Pass']['range_to'];
            $failrange_from = $session_set['Fail']['range_from'];
            $failrange_to =$session_set['Fail']['range_to'];
            if($report_by==1){
                $region_data = $this->assessment_dashboard_model->overall_result_parameter(0,$passrange_from,$passrange_to,$failrange_from,$failrange_to,$StartDate,$EndDate,$region_id,$assessment_id,$step,$store_id);
            }else{
                $region_data = $this->assessment_dashboard_model->overall_result_assessment(0,$passrange_from,$passrange_to,$failrange_from,$failrange_to,$StartDate,$EndDate,$region_id,$assessment_id,$step,$store_id);
            }
            if (count((array)$region_data['region_data']) > 0) {
                $pass_color_code= $this->session->userdata('Assessment_result_session')['Pass']['range_color'];
                $fail_color_code= $this->session->userdata('Assessment_result_session')['Fail']['range_color'];
                $pending_color_code= $this->session->userdata('Assessment_result_session')['Pending']['range_color'];
                 foreach ($region_data['region_data'] as $rkey => $regd) {
                     $Rdataset=array();
                    $total_ass =$regd->total_users;
                    $pass = number_format($regd->pass*100/$total_ass,2);
                    $Rdataset[] = array('name'=>'Pass', 'y'=>$pass,'color'=>$pass_color_code,'u'=>($regd->pass!=''? $regd->pass :0));
                    $fail = number_format($regd->fail*100/$total_ass,2);
                    $Rdataset[] = array('name'=>'Fail', 'y'=>$fail,'color'=>$fail_color_code,'u'=>($regd->fail!=''? $regd->fail :0));
                    $pending = number_format(($total_ass-($regd->fail+$regd->pass))*100/$total_ass,2);
                    $Rdataset[] = array('name'=>'Pending', 'y'=>$pending,'color'=>$pending_color_code,'u'=>($total_ass-($regd->fail+$regd->pass)));
                    
                    $Rgdata['rdataset'] = json_encode($Rdataset, JSON_NUMERIC_CHECK);
                    $Rgdata['region_name'] = json_encode($regd->region_name, JSON_NUMERIC_CHECK);
                    $Rgdata['rg_id'] = json_encode($regd->region_id, JSON_NUMERIC_CHECK);
                    $html_graph .= $this->load->view('assessment_dashboard/region_graph', $Rgdata, true);
                 }
            }
            
        }
        $data['region_total']=(isset($region_data['region_total']) ? $region_data['region_total'] :0);
        $data['region_graph'] = $html_graph;
        if($returnflag){
            return $data;
        }else{
            echo json_encode($data);
        }
    }
    public function getFilterDashboardData($returnflag=0) {
        $Company_id = $this->mw_session['company_id'];
        $SDate = $this->input->post('StartDate', true);
        $StartDate = date('Y-m-d', strtotime($SDate));
        $EDate = $this->input->post('EndDate', true);
        $EndDate = date('Y-m-d', strtotime($EDate));
        $report_by = $this->input->post('report_by', true);
        
        $region_id = $this->input->post('region_id');
        $store_id = $this->input->post('store_id');
//        $region_id_array = $this->input->post('region_id');
        $assessment_id_array = $this->input->post('assessment_id');
//        $region_id='';
//        if($region_id_array !='' && count((array)$region_id_array)>0){
//            $region_id = implode(",", $region_id_array);
//        }
        $assessment_id='';
        if($assessment_id_array !='' && count((array)$assessment_id_array)>0){
            $assessment_id = implode(",", $assessment_id_array);
        }
        $data=$this->getRegionChart(1);
        
        $para_assess = array();
        $regiondata_result = array();
        $region_list = array();
        $regionset = $this->assessment_dashboard_model->get_region_result($Company_id, $report_by,$region_id,$StartDate,$EndDate,$assessment_id,$store_id);
	
        $last_avg = array();
        $horizontal_avg = array();
        $total_result = 0;
		$Horizontal_avg =array();
		$Vertical_avg =array();
		$Final_avg=0;
        if (count((array)$regionset) > 0) {
			if($report_by==1){
				$Avgset = $this->assessment_dashboard_model->get_region_average($Company_id, 1,$region_id,$StartDate,$EndDate,$assessment_id,$store_id);
				$Avgset2 = $this->assessment_dashboard_model->get_region_average($Company_id, 3,$region_id,$StartDate,$EndDate,$assessment_id,$store_id);
			}else{
				$Avgset = $this->assessment_dashboard_model->get_region_average($Company_id, 2,$region_id,$StartDate,$EndDate,$assessment_id,$store_id);
				$Avgset2 = $this->assessment_dashboard_model->get_region_average($Company_id, 4,$region_id,$StartDate,$EndDate,$assessment_id,$store_id);
			}
			foreach ($Avgset2 as $key => $rl) {
				$Horizontal_avg[$rl->region_id] =$rl->result;
			}
			
			foreach ($Avgset as $key => $rl) {
				$Vertical_avg[$rl->id] =$rl->result;
				$Final_avg +=$rl->result;
			}
			$Final_avg = number_format($Final_avg/count((array)$Vertical_avg),2);
            foreach ($regionset as $key => $rl) {
                if (!in_array($rl->region_name, $region_list)) {
                    $region_list[$rl->region_id] = $rl->region_name;
                }
                if (!in_array($rl->name, $para_assess)) {
                    $para_assess[$rl->para_assess_id] = $rl->name;
                }
                if (!isset($horizontal_avg[$rl->region_id]['resultRow'])) {
                    $horizontal_avg[$rl->region_id]['value'] = $rl->result;
                } else {
                    $horizontal_avg[$rl->region_id]['value'] = $rl->result;
                }
                $regiondata_result[$rl->region_id][$rl->para_assess_id] = $rl;
            }
        }
   
        $Rtdata['Horizontal_avg'] = $Horizontal_avg;
		$Rtdata['Vertical_avg'] = $Vertical_avg;
		$Rtdata['Final_avg'] = $Final_avg;
        $Rtdata['region_list'] = $region_list;
        $Rtdata['para_assess'] = $para_assess;
        $Rtdata['regiondata'] = $regiondata_result;
        $Rtdata['report_by'] = $report_by;
        $Rtdata['store_id'] = $store_id;
        $data['regiontable_graph'] = $this->load->view('assessment_dashboard/regiontable_view', $Rtdata, true);
        if($returnflag){
            return $data;
        }else{
            echo json_encode($data);
        }
        
    }

    public function getDatewiseRegion() {
        $StartDate = date('Y-m-d H:i:s', strtotime($this->input->post('StartDate', true)));
        $EndDate = date('Y-m-d H:i:s', strtotime($this->input->post('EndDate', true)));
        $data['company_id'] = $this->mw_session['company_id'];
        $regionchtml = '<option value="">All region</option>';
        $RegionData = $this->assessment_dashboard_model->get_trainee_region($data['company_id'], '', '', $StartDate, $EndDate);

        if (count((array)$RegionData) > 0) {
            foreach ($RegionData as $value) {
                $regionchtml .= '<option value="' . $value->id . '">' . $value->region_name . '</option>';
            }
        }
        $data['Regionchtml'] = $regionchtml;
        echo json_encode($data);
    }

    public function getDatewiseAssessment() {
        $StartDate = date('Y-m-d H:i:s', strtotime($this->input->post('StartDate', true)));
        $EndDate = date('Y-m-d H:i:s', strtotime($this->input->post('EndDate', true)));
        $data['company_id'] = $this->mw_session['company_id'];
        $assessmentchtml = '<option value="">All Assessment</option>';
        $AssessmentData = $this->assessment_dashboard_model->get_assessment($data['company_id'], '', $StartDate, $EndDate);

        if (count((array)$AssessmentData) > 0) {
            foreach ($AssessmentData as $value) {
                $assessmentchtml .= '<option value="' . $value->assessment_id . '">' . $value->assessment . '</option>';
            }
        }
        $data['Assessmentchtml'] = $assessmentchtml;
        echo json_encode($data);
    }

    public function parameter_scoredata() {
        $data['company_id'] = $this->input->post('company_id', true);
        $data['region_id'] = $this->input->post('region_id', true);
        $data['store_id'] = $this->input->post('store_id',true);
        $data['parameter_id'] = $this->input->post('parameter_id', true);
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        $data['assessment_result'] = $this->assessment_dashboard_model->get_parameter_assessment_score($data['company_id'], $data['parameter_id'], $data['region_id'],$SDate,$EDate,$data['store_id']);
        $this->load->view('assessment_dashboard/parameter_scoremodal', $data);
    }

    public function region_scoredata() {
        $data['company_id'] = $this->mw_session['company_id'];
        $data['region_id'] = $this->input->post('region_id', true);
        $data['assessment_id'] = $this->input->post('assessment_id', true);
        $this->load->view('assessment_dashboard/regionwise_scoremodal', $data);
    }

    public function assessment_parameter_scoredata() {
        $data['company_id'] = $this->mw_session['company_id'];
        $data['assessment_id'] = $this->input->post('assessment_id', true);
        $data['assessment_name'] = $this->common_model->get_value('assessment_mst', 'assessment', 'id=' . $data['assessment_id']);
        $this->load->view('assessment_dashboard/assessmentparameter_scoremodal', $data);
    }

    public function regionwise_table() {
        $company_id = $this->mw_session['company_id'];
        $region_id = $this->input->post('region_id', true);
        $store_id = $this->input->post('store_id',true);
        $assessment_id = $this->input->post('assessment_id', true);

        $para_assess = array();
        $user_list = array();
        $regiondata_result = array();
        $result_data = $this->assessment_dashboard_model->get_parameter_user_result($company_id, $region_id, $assessment_id, $store_id);
        $Horizontal_avg =array();
        $Vertical_avg =array();
        $Final_avg=0;
        if (count((array)$result_data) > 0) {
                      
                $Avgset = $this->assessment_dashboard_model->get_user_average($company_id, 1,$region_id,$assessment_id,$store_id);
                $Avgset2 = $this->assessment_dashboard_model->get_user_average($company_id, 3,$region_id,$assessment_id,$store_id);

                $Horizontal_avg =array();
                $Vertical_avg =array();
                $Final_avg=0;
                foreach ($Avgset2 as $key => $rl) {
                        $Horizontal_avg[$rl->user_id] =$rl->result;
						$Final_avg +=$rl->result;
                }
                foreach ($Avgset as $key => $rl) {
                        $Vertical_avg[$rl->id] =$rl->result;
                        
                }
                
                $Final_avg = number_format($Final_avg/count((array)$Horizontal_avg),2);
                
            foreach ($result_data as $key => $rd) {
                if (!isset($user_list[$rd->user_id])) {
                    $user_list[$rd->user_id] = $rd->firstname;
                }
                if (!in_array($rd->parameter, $para_assess)) {
                    $para_assess[$rd->parameter_id] = $rd->parameter;
                }
                $regiondata_result[$rd->user_id][$rd->parameter_id] = $rd;
            }
        }
        $Rtdata['Horizontal_avg'] = $Horizontal_avg;
		$Rtdata['Vertical_avg'] = $Vertical_avg;
		$Rtdata['Final_avg'] = $Final_avg;
        $Rtdata['user_list'] = $user_list;
        $Rtdata['para_assess'] = $para_assess;
        $Rtdata['regiondata'] = $regiondata_result;
        $data['regiontable_graph'] = $this->load->view('assessment_dashboard/parameter_usertable_view', $Rtdata, true);

        echo json_encode($data);
    }

    public function assessment_parameterwise_table() {
        $company_id = $this->mw_session['company_id'];
        $assessment_id = $this->input->post('assessment_id', true);

        $para_assess = array();
        $region_list = array();
        $regiondata_result = array();
        $result_data = $this->assessment_dashboard_model->get_parameter_assessment_result($company_id, $assessment_id);

        $last_avg = array();
        $horizontal_avg = array();
        if (count((array)$result_data) > 0) {
            
                $Avgset = $this->assessment_dashboard_model->get_pararegion_average($company_id,1,$assessment_id);
                $Avgset2 = $this->assessment_dashboard_model->get_pararegion_average($company_id,3,$assessment_id);
                
                $Horizontal_avg =array();
                $Vertical_avg =array();
                $Final_avg=0;
                foreach ($Avgset2 as $key => $rl) {
                        $Horizontal_avg[$rl->region_id] =$rl->result;
                }
                foreach ($Avgset as $key => $rl) {
                        $Vertical_avg[$rl->id] =$rl->result;
                        $Final_avg +=$rl->result;
                }
                $Final_avg = number_format($Final_avg/count((array)$Vertical_avg),2);
                
            foreach ($result_data as $key => $rd) {
                if (!in_array($rd->region_name, $region_list)) {
                    $region_list[$rd->region_id] = $rd->region_name;
                }
                if (!in_array($rd->parameter, $para_assess)) {
                    $para_assess[$rd->parameter_id] = $rd->parameter;
                }
                $regiondata_result[$rd->region_id][$rd->parameter_id] = $rd;
            }
        }
        $Rtdata['Horizontal_avg'] = $Horizontal_avg;
		$Rtdata['Vertical_avg'] = $Vertical_avg;
		$Rtdata['Final_avg'] = $Final_avg;
        $Rtdata['horizontal_set'] = $horizontal_avg;
        $Rtdata['region_list'] = $region_list;
        $Rtdata['assessment_id'] = $assessment_id;
        $Rtdata['para_assess'] = $para_assess;
        $Rtdata['regiondata'] = $regiondata_result;
        $data['assessmenttable_graph'] = $this->load->view('assessment_dashboard/parameter_assessmenttable_view', $Rtdata, true);

        echo json_encode($data);
    }
    public function QuestionScoreDatatable() {

        $assessment_id = $this->input->get('assessment_id');
        $region_id = $this->input->get('region_id');
        $parameter_id = $this->input->get('parameter_id');
        $start_date = $this->input->get('StartDate');
        $end_date = $this->input->get('EndDate');
        $StartDate = date('Y-m-d', strtotime($start_date));
        $EndDate = date('Y-m-d', strtotime($end_date));        
        $dtSearchColumns = array('art.question_id', 'aq.question');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($this->mw_session['company_id'] == "") {
            $Company_id = $this->input->get('company_id');
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        if ($dtWhere <> '') {
            $dtWhere .= " AND ar.company_id = " . $Company_id;
        } else {
            $dtWhere .= " WHERE ar.company_id = " . $Company_id;
        }
        if ($assessment_id != "") {
            $dtWhere .= " AND ar.assessment_id  = " . $assessment_id;
        }
        if ($region_id != "") {
            $dtWhere .= " AND du.region_id  = " . $region_id;
        }
        if ($parameter_id != "") {
            $dtWhere .= " AND art.parameter_id  = " . $parameter_id;
        }
        if($StartDate !='' && $EndDate !=''){
            $dtWhere .=" AND DATE(am.start_dttm) BETWEEN '".$StartDate."' AND '".$EndDate."'";  
        }
        $DTRenderArray = $this->assessment_dashboard_model->get_questions_score($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('question', 'result');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function ajax_getWeeks() {
        $year = $this->input->post('year', true);
        $month = $this->input->post('month', true);
        $data['WStartEnd'] = $this->common_model->getMonthWeek($year, $month);
        echo json_encode($data);
    }

    public function assessment_usercount() {
        $data['total_user']=0;
        $data['complete_user'] = 0;
        $data['incomplete_user'] = 0;
        $assessment_id = $this->input->post('assessment_id', true);
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        $user_result = $this->assessment_dashboard_model->get_assessment_user($assessment_id,$SDate,$EDate);
        $complete_count = 0;
        $incomplete_count = 0;
        if(count((array)$user_result) > 0){
            $data['total_user'] = $user_result['user_count']->user_count;
            if(count((array)$user_result['ass_status']) > 0){
                foreach ($user_result['ass_status'] as $key => $value) {
                        if($value->assess_status == 'complete'){
                            $complete_count++;
                        }else{
                            $incomplete_count++;
                    }
                }
            }
        }
        $data['complete_user'] = $complete_count;
        $data['incomplete_user'] = $incomplete_count;
        echo json_encode($data);
    }
     public function update_range() {
        $SuccessFlag=1;
        $Message='';
 
        $Threshold_array=array();
        $result_array=array();
        $range_id=$this->input->post('range_id',true);
        $result_status=$this->input->post('result_status',true);
                if (count((array)$range_id) > 0 && count((array)$result_status) > 0) {
                     
                    foreach ($range_id as $key1 => $rg) {
                        $range1=$this->input->post('range_from')[$key1];
                        $range2=$this->input->post('range_to')[$key1];
                      if($range1 < 100 && $range2 <= 100){
                       if($range1 > $range2){
                           $Message = '2nd Threshold percentage field not be less than 1st';
                           $SuccessFlag=0;
                       }else{
                           $j=$key1-1;
                           for($i=$j;$i>=0; $i--){
                               if(($range1 >= $this->input->post('range_from')[$i] && $range1 <=$this->input->post('range_to')[$i]) || ($this->input->post('range_from')[$i] >= $range1 && $this->input->post('range_from')[$i] <= $range2)){
                                   $Message = 'You entered same percentage Threshold range';
                                   $SuccessFlag=0;
                               }
                           }
                           if($SuccessFlag){
                            $k=$key1+1;
                            if($k < count((array)$range_id)){
                                 $rangeto=$this->input->post('range_to')[$key1];
                                 $rangefrom=$this->input->post('range_from')[$k];
                                 if($rangeto+1 != $rangefrom){
                                         $Message = 'Difference between previous End range and current start range only 1%';
                                         $SuccessFlag=0;
                                 }
                                }
                            }
                       }
                     }else{
                         $Message = 'Percentage range not start with 100% and not greater than 100%';
                         $SuccessFlag=0;
                     }
                     if($SuccessFlag){
                     $k=count((array)$range_id)+1;
                        if($this->input->post('range_from')[0] !='0' && $this->input->post('range_to')[$k] !='100'){
                             $Message = 'Percentage range start with 0% and and end with 100%';
                             $SuccessFlag=0;
                        }
                     }
                    }
                    if($SuccessFlag){
                    foreach ($result_status as $key2 => $rg) {
                                if($key2 < 2){
                                 $result1 = $this->input->post('result_from')[$key2];
                                 $result2= $this->input->post('result_to')[$key2];
                                    if($result1 < 100 && $result2 <= 100){
                                        if($result1 > $result1){
                                            $Message = '2nd Pass/Fail %age value field not be less than 1st';
                                            $SuccessFlag=0;
                                        }else{
                                            $i=$key2-1;
                                            if($i >=0){
                                                if(($result1 >= $this->input->post('result_from')[$i] && $result1 <=$this->input->post('result_to')[$i]) || ($this->input->post('result_from')[$i] >= $result1 && $this->input->post('result_from')[$i] <= $result2)){
                                                    $Message = 'You entered same %age Pass/Fail value';
                                                    $SuccessFlag=0;
                                                }
                                            }
                                            if($SuccessFlag){
                                                if(($this->input->post('result_from')[0]-1)!= $this->input->post('result_to')[1]){
                                                          $Message = 'Difference between Pass/Fail previous End range and current start range only 1%';
                                                          $SuccessFlag=0;
                                                }
                                             }
                                        }
                                    }else{
                                        $Message = 'Pass/Fail %age range not start with 100% and not Greater than 100%';
                                        $SuccessFlag=0;
                                    }
                                    if($SuccessFlag){
                                        if($this->input->post('result_from')[1] !='0' && $this->input->post('result_to')[0] !='100'){
                                            $Message = 'Pass Percentage End with 100% and Fail Percentage Start with 0%';
                                            $SuccessFlag=0;
                                       }
                                    }
                                }
                         }
                    }
                }else{
                    $Message = 'Please Enter Threshold Range';
                    $SuccessFlag=0;
                }
    
        if($SuccessFlag){
            foreach ($range_id as $key1 => $range_val) { 
                    $Threshold_array[$range_val]= array('range_from'=>$this->input->post('range_from')[$key1],
                    'range_to'=>$this->input->post('range_to')[$key1],'range_color'=>$this->input->post('range_color')[$key1],
                    );
            }
            foreach($result_status as $key2 => $result_val){
                    $result_array[$result_val]= array('range_from'=>$this->input->post('result_from')[$key2],
                    'range_to'=>$this->input->post('result_to')[$key2],'range_color'=>$this->input->post('result_color')[$key2],
                    );
            }

            $this->session->set_userdata('Assessment_threshold_session', $Threshold_array);                      
            $this->session->set_userdata('Assessment_result_session', $result_array);
            $Message = 'Threshold Ranged Successfully';
            $SuccessFlag=1;
        }
  
        $data['Success']=$SuccessFlag;
        $data['Message']=$Message;
        echo json_encode($data);
    }
    public function region_level_scoredata(){
        $Company_id    = $this->mw_session['company_id'];                
        $region_id = $this->input->post('region_id', true);
        $SDate = $this->input->post('StartDate', true);
        $StartDate = date('Y-m-d', strtotime($SDate));
        $EDate = $this->input->post('EndDate', true);
        $EndDate = date('Y-m-d', strtotime($EDate));
        if($region_id == ''){
            $region_id = 0; 
        }
        $pass = array();
        $fail = array();
        $pending = array();
        $assessment = array();
        $session_set =$this->session->userdata('Assessment_result_session');
        if (count((array)$session_set) > 0) {
            $pass_range_from = $session_set['Pass']['range_from'];
            $pass_range_to   = $session_set['Pass']['range_to'];
            $fail_range_from = $session_set['Fail']['range_from'];
            $fail_range_to   = $session_set['Fail']['range_to'];
            $Result_data = $this->assessment_dashboard_model->region_level_result($region_id,$StartDate,$EndDate,$pass_range_from,$pass_range_to,$fail_range_from,$fail_range_to);
			$pass_color_code = $this->session->userdata('Assessment_result_session')['Pass']['range_color'];                    
			$fail_color_code= $this->session->userdata('Assessment_result_session')['Fail']['range_color'];
			$pending_color_code= $this->session->userdata('Assessment_result_session')['Pending']['range_color'];
            if(count((array)$Result_data) > 0){
                foreach ($Result_data as $val){
                    
                    
                    $pass[]   = number_format($val->pass*100/$val->total_users,2);                    
                    $fail[]   = number_format($val->fail*100/$val->total_users,2);
                    $pending_user = $val->total_users - ($val->pass + $val->fail);                    
                    $pending[]= number_format($pending_user*100/$val->total_users,2);
                    $assessment[] = $val->assessment;
                            
                }
            }
            $Rdata['pass']    = json_encode($pass, JSON_NUMERIC_CHECK);
            $Rdata['fail']    = json_encode($fail, JSON_NUMERIC_CHECK);
            $Rdata['pending'] = json_encode($pending, JSON_NUMERIC_CHECK);
            $Rdata['assessment'] = json_encode($assessment, JSON_NUMERIC_CHECK);
            $Rdata['pass_color_code'] = json_encode($pass_color_code, JSON_NUMERIC_CHECK);
            $Rdata['fail_color_code'] = json_encode($fail_color_code, JSON_NUMERIC_CHECK);
            $Rdata['pending_color_code'] = json_encode($pending_color_code, JSON_NUMERIC_CHECK);
            
            $data['region_level_graph'] = $this->load->view('assessment_dashboard/region_level_graph', $Rdata, true);
        }else{
            $data['region_level_graph'] = '';
        }
        echo $data['region_level_graph'];                                                
    }
    public function load_assessment_index($returnflag=0) {
       $data = array();
       $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
		$this->load->model('assessment_trainee_dashboard_model');
        $report_by = $this->input->post('report_by', true);
        
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        
        $rpt_period = $this->input->post('rpt_period', true);
        $current_month = date('m');
        $current_date = date('Y-m-d');
        $report_data = array();
        $index_dataset = [];
        $index_label = [];
        $report_title = '';
        $report_xaxis_title = '';
        $Month = $this->input->post('month', true);
        $Year = $this->input->post('year', true);
        $Week = $this->input->post('week', true);
        $WeekStartDate = '';
        $WeekEndDate = '';
        if ($Week != '' && $Month != '' && $Year != '') {
            $WeekDate = explode('-', $Week);
            $WeekStartDay = $WeekDate[0];
            $WeekEndDay = $WeekDate[1];
            $WeekStartDate = date('Y-m-d', strtotime("$Year-$Month-$WeekStartDay"));
            $WeekEndDate = date('Y-m-d', strtotime("$Year-$Month-$WeekEndDay"));
        }
        if ($rpt_period == "weekly") {
            if ($WeekStartDate != '' && $WeekEndDate != '') {
                $AccuracySet = $this->assessment_trainee_dashboard_model->assessment_index_weekly_monthly($report_by,$WeekStartDate, $WeekEndDate, $parameter_id);
                for ($i = $WeekStartDay; $i <= $WeekEndDay; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    if ($Year != '' && $Month != '') {
                        $TempDate = $Year . '-' . $Month . '-' . $i;
                    } else {
                        $TempDate = Date('Y-m-' . $i);
                    }
                    if (isset($AccuracySet['period'][$day])) {
                        $index_dataset[] = json_encode($AccuracySet['period'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = date("l", strtotime($TempDate));
                }
            } else {
                $WeekStartDate = date('Y-m-d', strtotime("-6 days"));
                $WeekEndDate = $current_date;
                $StartStrDt = date('d-m-Y', strtotime("-6 days"));
                $EndStrDt = date('d-m-Y');
                $StartWeek = date('d', strtotime("-6 days"));
                $EndWeek = date('d');
                $AccuracySet = $this->assessment_trainee_dashboard_model->assessment_index_weekly_monthly($report_by,$WeekStartDate, $WeekEndDate);
                for ($i = $StartWeek; $i <= $EndWeek; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $TempDate = Date('Y-m-' . $i);
                    if (isset($AccuracySet['period'][$day])) {
                        $index_dataset[] = json_encode($AccuracySet['period'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = date("l", strtotime($TempDate));
                }
            }
            $report_xaxis_title = 'Weekly';
        } elseif ($rpt_period == "monthly") {
            if ($Year != '' && $Month != '' && $Month != $current_month) {
                $StartDate = $Year . '-' . $Month . '-01';
                $WeekStartDate = $StartDate;
                $StartStrDt = '01-' . $Month . '-' . $Year;
                $noofdays = date('t', strtotime($StartDate));
                $EndDate = $Year . '-' . $Month . '-' . $noofdays;
                $WeekEndDate = $EndDate;
                $EndStrDt = $noofdays . '-' . $Month . '-' . $Year;
            } else {
                $WeekStartDate = Date('Y-m-1');
                $WeekEndDate = $current_date;
                $noofdays = Date('d');
            }

            $report_xaxis_title = 'Monthly';
            $AccuracySet = $this->assessment_trainee_dashboard_model->assessment_index_weekly_monthly($report_by,$WeekStartDate, $WeekEndDate);
            $WeekNo = 1;
            $Divider = 0;
            for ($i = 1; $i <= $noofdays; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = $Year . '-' . $Month . '-' . $day;
                if (isset($AccuracySet['period'][$day])) {
                    $index_dataset[] = json_encode($AccuracySet['period'][$day], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("d-M", strtotime($TempDate));
            }
        } elseif ($rpt_period == "yearly") {
            $WeekStartDate = $Year . '-01-01';
            $WeekEndDate = $Year . '-12-31';

            $report_xaxis_title = 'Yearly';
            $AccuracySet = $this->assessment_trainee_dashboard_model->assessment_index_yearly($report_by,$WeekStartDate, $WeekEndDate);
            for ($i = 1; $i <= 12; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                if (isset($AccuracySet['period'][$i])) {
                    $index_dataset[] = json_encode($AccuracySet['period'][$i], JSON_NUMERIC_CHECK);
                } else {
                    $index_dataset[] = 0;
                }
                $index_label[] = date("M", strtotime($TempDate));
            }
        }
        $report_title = 'Assessment Index - (Period From ' . date('d-m-Y', strtotime($WeekStartDate)) . ' To ' . date('d-m-Y', strtotime($WeekEndDate)).')';
		$parameter_id = $this->input->post('parameter_id', true);
		if($parameter_id !=''){
			$data=$this->load_parameter_index(1);
		}
		
        $data['report'] = $report_data;
        $Rdata['report_period'] = $report_xaxis_title;
        $Rdata['report_title'] = json_encode($report_title);
        $Rdata['index_dataset'] = json_encode($index_dataset, JSON_NUMERIC_CHECK);
        $Rdata['index_label'] = json_encode($index_label);
        $indexGraph = $this->load->view('assessment_trainee_dashboard/assessment_index_report', $Rdata, true);
        $data['index_graph'] = $indexGraph;
		
        if($returnflag){
            return $data;
        }else{
            echo json_encode($data);
        }
	}
	public function load_parameter_index($returnflag=0) {
       $data = array();
	   $this->load->model('assessment_trainee_dashboard_model');
       $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $report_by = $this->input->post('report_by', true);
        
        $start_date = $this->input->post('StartDate', true);
        $end_date = $this->input->post('EndDate', true);
        $SDate = date('Y-m-d', strtotime($start_date));
        $EDate = date('Y-m-d', strtotime($end_date));
        
        $rpt_period = $this->input->post('rpt_period', true);
        $current_month = date('m');
        $current_date = date('Y-m-d');
        $report_data = array();
        
        
        $index_paradataset = [];
        $index_paralabel = [];
        $report_paratitle = '';
        $report_xaxis_paratitle = '';
        
        $parameter_id = $this->input->post('parameter_id', true);
        $Month = $this->input->post('month', true);
        $Year = $this->input->post('year', true);
        $Week = $this->input->post('week', true);
        $WeekStartDate = '';
        $WeekEndDate = '';
        if ($Week != '' && $Month != '' && $Year != '') {
            $WeekDate = explode('-', $Week);
            $WeekStartDay = $WeekDate[0];
            $WeekEndDay = $WeekDate[1];
            $WeekStartDate = date('Y-m-d', strtotime("$Year-$Month-$WeekStartDay"));
            $WeekEndDate = date('Y-m-d', strtotime("$Year-$Month-$WeekEndDay"));
        }
        if ($rpt_period == "weekly") {
            if ($WeekStartDate != '' && $WeekEndDate != '') {
                $AccuracySet = $this->assessment_trainee_dashboard_model->parameter_index_charts($parameter_id, $report_by,$WeekStartDate, $WeekEndDate);
                for ($i = $WeekStartDay; $i <= $WeekEndDay; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    if ($Year != '' && $Month != '') {
                        $TempDate = $Year . '-' . $Month . '-' . $i;
                    } else {
                        $TempDate = Date('Y-m-' . $i);
                    }
                    if (isset($AccuracySet['period'][$day])) {
                        $index_dataset[] = json_encode($AccuracySet['period'][$day], JSON_NUMERIC_CHECK);
                    } else {
                        $index_dataset[] = 0;
                    }
                    $index_label[] = date("l", strtotime($TempDate));
					$index_paralabel[] = $assess->assessment;
                }
            } else {
                $WeekStartDate = date('Y-m-d', strtotime("-6 days"));
                $WeekEndDate = $current_date;
                $StartStrDt = date('d-m-Y', strtotime("-6 days"));
                $EndStrDt = date('d-m-Y');
                $StartWeek = date('d', strtotime("-6 days"));
                $EndWeek = date('d');
                $AccuracySet = $this->assessment_trainee_dashboard_model->parameter_index_charts($parameter_id, $report_by,$WeekStartDate, $WeekEndDate,  $user_id);
            }
            $report_xaxis_title = 'Weekly';
        } elseif ($rpt_period == "monthly") {
            if ($Year != '' && $Month != '' && $Month != $current_month) {
                $StartDate = $Year . '-' . $Month . '-01';
                $WeekStartDate = $StartDate;
                $StartStrDt = '01-' . $Month . '-' . $Year;
                $noofdays = date('t', strtotime($StartDate));
                $EndDate = $Year . '-' . $Month . '-' . $noofdays;
                $WeekEndDate = $EndDate;
                $EndStrDt = $noofdays . '-' . $Month . '-' . $Year;
            } else {
                $WeekStartDate = Date('Y-m-1');
                $WeekEndDate = $current_date;
                $noofdays = Date('d');
            }
				$report_xaxis_title = 'Monthly';
				$AccuracySet = $this->assessment_trainee_dashboard_model->parameter_index_charts($parameter_id, $report_by,$WeekStartDate, $WeekEndDate);
        } elseif ($rpt_period == "yearly") {
            $WeekStartDate = $Year . '-01-01';
            $WeekEndDate = $Year . '-12-31';

            $report_xaxis_title = 'Yearly';
            $AccuracySet = $this->assessment_trainee_dashboard_model->parameter_index_charts($parameter_id,$report_by,$WeekStartDate, $WeekEndDate);
            
        }
        
        if(count((array)$AccuracySet) > 0){
            foreach($AccuracySet as  $assess ){
                $index_paradataset[] = json_encode($assess['result'], JSON_NUMERIC_CHECK);
                $index_paralabel[] = $assess['assessment_name'];
            }
        }
        if($parameter_id !=''){
            $parameter_data = $this->common_model->get_value('parameter_mst', 'id,description', 'status=1 and id='.$parameter_id);
            $report_paratitle = ' <strong>'.$parameter_data->description.'</strong>';
        }else{
            $report_paratitle .= ' <strong>All Parameters</strong>)';
        }
        
        $PRdata['report_paraperiod'] = $report_xaxis_title;
		$PRdata['report_paratitle'] = $report_paratitle;
        $PRdata['index_paradataset'] = json_encode($index_paradataset, JSON_NUMERIC_CHECK);
        $PRdata['index_paralabel'] = json_encode($index_paralabel);
        $indexAssessGraph = $this->load->view('assessment_trainee_dashboard/parameter_index_report', $PRdata, true);
        $data['index_paragraph'] = $indexAssessGraph;
        
        if($returnflag){
            return $data;
        }else{
            echo json_encode($data);
        }
    }
}
