<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Video_min_report extends MY_Controller {
    function __construct() {
        parent::__construct();
            $acces_management = $this->check_rights('video_min_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
        $this->load->model('video_min_report_model');
    }
    public function index() {
        $data['module_id'] = '14.01';
        $data['acces_management'] = $this->acces_management;
        $cmpdata_set = $this->common_model->get_selected_values('company', 'id,company_name,db_name', 'status=1','company_name');
        $data['company_id'] = $this->mw_session['company_id'];
        $data['cmpdata'] = $cmpdata_set;
        $this->load->view('video_min_report/index',$data);
    }
    public function ajax_getWeeks() {
        $year = $this->input->post('year', true);
        $month = $this->input->post('month', true);
        $data['WStartEnd'] = $this->common_model->getMonthWeek($year, $month);
        echo json_encode($data);
    }
    public function get_user_data(){
        $data['acces_management'] = $this->acces_management;
        $Month = $this->input->post('month', true);
        $Year = $this->input->post('year', true);
		if($Year ==""){
			$Year =date('Y');
		}
        $Week = $this->input->post('week', true);
		

		$company_id = $this->input->post('company_id',true);
        $rpt_period = $this->input->post('rpt_period',true);
		if($rpt_period==""){
			$rpt_period ='yearly';
		}
        if($company_id !=''){
            $_SESSION['company_id'][$company_id]= array('month'=>$Month,'year'=>$Year,'week'=>$Week,'rpt_period'=>$rpt_period);  
        }else{
            unset($_SESSION['company_id']);
        }     
        $chart_data = array();	
        $htmchart = '';
        $data_array = [];
        $data_label = [];
        $index_label = [];
        $total_user = [];
        $cmpdata_set= array();
        $current_month = date('m');
        $current_date = date('Y-m-d');
        $WeekStartDate = '';
        $WeekEndDate = '';
        if ($Week != '' && $Month != '' && $Year != '') {
            $WeekDate = explode('-', $Week);
            $WeekStartDay = $WeekDate[0];
            $WeekEndDay = $WeekDate[1];
            $WeekStartDate = date('Y-m-d', strtotime("$Year-$Month-$WeekStartDay"));
            $WeekEndDate = date('Y-m-d', strtotime("$Year-$Month-$WeekEndDay"));
        }
        $groupby = ' year(start_time) ';
        $columname = 'year(start_time)';
        $period_title = 'All Years';
        $report_title = 'All Years' ;
        if ($rpt_period == "weekly") {
            if ($WeekStartDate != '' && $WeekEndDate != '') {
                for ($i = $WeekStartDay; $i <= $WeekEndDay; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    if ($Year != '' && $Month != '') {
                        $TempDate = $Year . '-' . $Month . '-' . $i;
                    } else {
                        $TempDate = Date('Y-m-' . $i);
                    }
                    $data_label[] = date("l", strtotime($TempDate));
                    $data_array[] = $day;
                }
            }else{
                $WeekStartDate = date('Y-m-d', strtotime("-6 days"));
                $WeekEndDate = $current_date;
                $StartStrDt = date('d-m-Y', strtotime("-6 days"));
                $EndStrDt = date('d-m-Y');
                $StartWeek = date('d', strtotime("-6 days"));
                $EndWeek = date('d');
                for ($i = $StartWeek; $i <= $EndWeek; $i++) {
                    $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $TempDate = Date('Y-m-' . $i);
                    $data_label[] = date("l", strtotime($TempDate));
                    $data_array[] = $day;
                }
            }
            $period_title = 'Weekly';
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
                //$diff = abs(strtotime($WeekEndDate) - strtotime($WeekStartDate));
                //$nyears = floor($diff / (365 * 60 * 60 * 24));
                //$nmonths = floor(($diff - $nyears * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $noofdays = Date('d');//floor(($diff - $nyears * 365 * 60 * 60 * 24 - $nmonths * 30 * 60 * 60 * 24) / (60 * 60 * 24));
            }
            for ($i = 1; $i <= $noofdays; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = $Year . '-' . $Month . '-' . $day;
                $data_label[] = date("d-M", strtotime($TempDate));
                $data_array[] = $day;
            }
            $period_title = 'Monthly';
        } elseif ($rpt_period == "yearly") {
            $WeekStartDate = $Year . '-01-01';
            $WeekEndDate = $Year . '-12-31';
            for ($i = 1; $i <= 12; $i++) {
                $day = str_pad($i, 2, "0", STR_PAD_LEFT);
                $TempDate = Date('Y-' . $day . '-01');
                $data_label[] = date("M", strtotime($TempDate));
                $data_array[] = $i;
            }
            $period_title = 'Yearly';
        }
        $hcwhere=' where ftp_status=1';
        $lcwhere = "status=1 ";
        if($company_id !=""){
                $lcwhere .= " AND id= ".$company_id;
        }else{
			$lcwhere .= " AND isvideo_assessment=1";
		}
        $cmpdata_set = $this->common_model->get_selected_values('company', 'id,company_name,db_name',$lcwhere,'company_name');
		if($WeekStartDate !='' && $WeekEndDate !=''){
            $hcwhere .="  AND date(start_time) BETWEEN '$WeekStartDate' AND '$WeekEndDate' ";
            $report_title = 'Period From ' . date('d-m-Y', strtotime($WeekStartDate)) . ' To ' . date('d-m-Y', strtotime($WeekEndDate));
        }
        $chart_data['count_chart']=($Year =='' ? count((array)$cmpdata_set) : 0 );
   
        foreach($cmpdata_set as $value){
                $index_data =array();
                $minvideo_data =array();
                $company_id = $value->id;
                $chart_data['company_id'] = $value->id;
                $chart_data['company_name']=$value->company_name;
				$total_user=array();
				$index_label=array();
                $obj_set =$this->common_model->CheckTableExist('assessment_results',$value->db_name);
                if(count((array)$obj_set)==0){
                        continue;
                }else{
                    $minvideo_data =$this->video_min_report_model->getuser_played_data($value->db_name,$hcwhere,$rpt_period);
					
                }
                if($rpt_period !='all'){
                    foreach ($data_array as $key => $value) {
                        $index_data[]=json_encode((isset($minvideo_data['duration'][$value]) ? $minvideo_data['duration'][$value] : 0), JSON_NUMERIC_CHECK);
                        $index_label[]=$data_label[$key];
                        $total_user[]=(isset($minvideo_data['users'][$value]) ? $minvideo_data['users'][$value] : 0);
                    }
                }else{
                    if(count((array)$minvideo_data)>0){
                        foreach ($minvideo_data['duration'] as $key => $value) {
                           $index_data[] = json_encode($value, JSON_NUMERIC_CHECK);
                           $index_label[] = $key;
                           $total_user[] = $minvideo_data['users'][$key];
                        } 
                    }
                }
            //    echo '<pre>';
            //    print_r($total_user);
                $chart_data['period_title'] = $period_title;
                $chart_data['report_title']= json_encode($report_title);
                $chart_data['index_data']= json_encode($index_data, JSON_NUMERIC_CHECK);
                $chart_data['index_label']=json_encode($index_label);
                $chart_data['total_user']=json_encode($total_user, JSON_NUMERIC_CHECK);
         
                $htmchart .= $this->load->view('video_min_report/load_videomin',$chart_data,true);     
        }
     
        echo json_encode($htmchart);
    }
    public function get_piechart_data(){
        $data['acces_management'] = $this->acces_management;
        $asssessment_id = $this->input->post('assessment_id', true);
        $company_id = $this->input->post('company_id',true);
        if($company_id !=''){
            $_SESSION['assessment_id'][$company_id]= array('assessment_id'=>$asssessment_id);  
        }else{
            unset($_SESSION['assessment_id']);
        }    
        $chart_data = array();	
        $htmchart = '';
        $total_user = [];
        $cmpdata_set= array();
        $current_month = date('m');
        $current_date = date('Y-m-d');
   
        $hcwhere=' where ftp_status=1';
        $lcwhere = "status=1 ";
        if($company_id !=""){
                $lcwhere .= " AND id= ".$company_id;
        }else{
			$lcwhere .= " AND isvideo_assessment=1";
		}
        $cmpdata_set = $this->common_model->get_selected_values('company', 'id,company_name,db_name',$lcwhere,'company_name');
	if($asssessment_id !='' ){
            $hcwhere .=" and ar.assessment_id=".$asssessment_id;
        }
        foreach($cmpdata_set as $value){
                $minvideo_data =array();
                $chart_data = array();
                $total_user = [];
                $utilized_min = [];
                $utilized = 0;
                $left_minute=0;
                $assessment ='';
                $company_id = $value->id;
                $Rdata['company_id'] = $value->id;
                $Rdata['company_name']=$value->company_name;
                $obj_set =$this->common_model->CheckTableExist('assessment_results',$value->db_name);
                if(count((array)$obj_set)==0){
                        continue;
                }else{
                    $minvideo_data =$this->video_min_report_model->getassessment_played_data($value->db_name,$hcwhere);
                }
                if(count((array)$minvideo_data)>0){
                    foreach ($minvideo_data as $key => $value) {
                        $total_duration =$value->total_duration;
						$utilized =0;
						$left_minute=0;
                        if($total_duration>0){
							$utilized = number_format($value->playing_duration*100/$total_duration,2);
							$left_minute = number_format($value->unutilize_duration*100/$total_duration,2);
						}elseif($value->playing_duration>0){
							$utilized =100;
						}
                                
						$chart_data[] = array('name'=>'Utilized Min', 'y'=>$utilized,'color'=>'#0070c0','u'=>($value->playing_duration!=''? $value->playing_duration:0));	
						$chart_data[] = array('name'=>'Unutilized Min', 'y'=>$left_minute,'color'=>'#ff0000','u'=>($value->unutilize_duration));
                        
                        $assessment = $value->assessment;
                        $total_user[0]=$value->total_users;
                        $utilized_min[0]=$value->playing_duration;
                        $total_user[1]=$value->total_users;
                        $utilized_min[1]=$value->unutilize_duration;
                    } 
                }
                $Rdata['assessment'] = ($asssessment_id !='' ? $assessment : 'All Assessment');
                $Rdata['utilized_minute'] = json_encode($utilized_min, JSON_NUMERIC_CHECK);
                $Rdata['total_user'] = json_encode($total_user, JSON_NUMERIC_CHECK);
                $Rdata['dataset'] = json_encode($chart_data, JSON_NUMERIC_CHECK);
                $htmchart .= $this->load->view('video_min_report/load_piechart',$Rdata,true); 
        }
        echo json_encode($htmchart);
    }
    public function add_filtermodel($assesid) {        
        $data['company_id'] = $this->input->post('company_id', TRUE);
        
        if(!$assesid){
            echo $this->load->view('video_min_report/load_filtermodal', $data,true);
        }else{
            $data['assessment_set']=array();
            if($data['company_id']!=''){
                $cmp_set = $this->common_model->get_value('company', 'id,company_name,db_name', 'status=1 and id='.$data['company_id']);
                $data['assessment_set'] = $this->common_model->get_selected_values($cmp_set->db_name.'.assessment_mst', 'id,assessment', 'status=1','assessment');
            }
            echo $this->load->view('video_min_report/load_assessment_filtermodal', $data,true);
        } 
    }
}