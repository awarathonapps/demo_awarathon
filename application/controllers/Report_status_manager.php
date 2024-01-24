<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
defined('BASEPATH') OR exit('No direct script access allowed');
class Report_status_manager extends MY_Controller {
    function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('report_status');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('report_status_model');
    }


    public function index() {
        $data['module_id'] = '14.04';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } 
        $data['Company_id'] = $Company_id;
        //$Company_id= 67;

        //$data['user_details']= $this->report_status_model->get_participants_distinct($Company_id, $assessment_id1);
        $data['assessment']=$this->report_status_model->get_all_assessment();
        $data['parameter_score_result'] = $this->report_status_model->get_all_parameters($Company_id);
        $this->load->model('report_status_model');
        $this->load->view('report_status_manager/index', $data);

    }
    function generate_report_manager(){
        $company_id = $this->mw_session['company_id'];
        $manager_id = $this->input->get('manager_id',true);
        $status_id = $this->input->get('status_id',true);
        $assessment_id1 = $this->input->get('assessment_id',true);
		$dtWhere='';
		if($assessment_id1 !=''){
			if($dtWhere !=''){
                $dtWhere .= " AND a.assessment_id  = ".$assessment_id1; 
           }else{
               $dtWhere .= " WHERE a.assessment_id = ".$assessment_id1; 
           } 
			//$manager_count=$this->common_model->get_value('assessment_allow_users','user_id','assessment_id=' .$assessment_id1);
			$manager_count=$this->common_model->get_value('assessment_mapping_user','user_id','assessment_id=' .$assessment_id1);
			$ismapped=0;
			if(count((array)$manager_count) >0){
					$ismapped=1;
			}
			if($status_id ==0){
				$dtWhere .= ' AND b.is_completed= 1 ';
			}elseif($status_id !=2){
				$dtWhere .= ' AND (b.is_completed= 0 OR b.is_completed IS NULL) ';
			}
			
			if($manager_id !=""){
				$dtWhere .= ' AND amu.trainer_id='.$manager_id;
			}
			$user_details= $this->report_status_model->status_check_manager($dtWhere, $ismapped);
		}
            // $user_details= $this->report_status_model->get_participants($company_id, $dtWhere);
            
            $user_list = [];
            $x=0;
            foreach ($user_details as $ud){               
                $assessment_name  = $ud->assessment;
                $user_list[$x]['user_id'] = $ud->emp_id;
                $user_list[$x]['user_name']= $ud->user_name;
                $b=$ud->registration_date;
                $a= date("d/m/Y", strtotime($b));
                
                $user_list[$x]['reg_date']= $a;
                $user_list[$x]['division']= $ud->department;
                $user_list[$x]['pc_hq']= "";
                $user_list[$x]['state']= "";
                $user_list[$x]['zone']= $ud->region_name;
                $user_list[$x]['ec']= "";
                $user_list[$x]['ec_name']= "";
                
                $user_id= $ud-> user_id;
                $assessment_id=$ud->assessment_id;
               
                
                //$assessment_detils= $this->report_model->get_assessment($user_id, $dtWhere);
                //foreach($assessment_detils as $ad)
               // {
                 //   $assessment_id= $ad->assessment_id;

                    $user_list[$x]['email']= $ud->email;  
                    $user_list[$x]['assessment_name']= $ud->assessment;
                    $user_list[$x]['status']=  $ud->status1;
                    //$ai_score= $this->report_model->get_ai_score($company_id, $assessment_id, $user_id);  
                    $overall_score = 0;
                    $ai_score= $this->report_status_model->get_ai_score($company_id, $assessment_id, $user_id);
                    
                    $user_list[$x]['ai_overall_score']= !empty($ai_score->overall_score) ? $ai_score->overall_score: '-';
                    $ai_score1= !empty($ai_score->overall_score) ? $ai_score->overall_score: 0;
                    $parameter_score_result = $this->report_status_model->get_parameters($company_id,$assessment_id);
                   
                    foreach ($parameter_score_result as $psr){
                        $parameter_id= $psr->parameter_id;
                        $parameter_name =$psr->parameter_name;
                      //  print_r($psr->parameter_name);
                       // exit;
                            $parameter_label_id            = $psr->parameter_label_id;
                            $parameter_your_score_result   = $this->report_status_model->get_parameters_your_score($company_id,$assessment_id,$user_id,$parameter_id,$parameter_label_id);
                      
                            //$user_list[$x][$parameter_name]= $parameter_your_score_result->score;
                            $user_list[$x][$parameter_name]= !empty($parameter_your_score_result->score) ? $parameter_your_score_result->score: '-';
                            $user_score[]= !empty($parameter_your_score_result->score) ? $parameter_your_score_result->score: '-';
                          
                                                       
                    }
                    $manual_overall_score=0;
                    $manual_score = $this->report_status_model->get_manual_score($assessment_id,$user_id);
                    if (isset($manual_score) AND count((array)$manual_score)>0){
                        $manual_overall_score = $manual_score->overall_score;
                    }
                    
                    
                    if($manual_overall_score=="0")
                    {   
                        $user_list[$x]['manual_overall_score']= '-';    
                    }
                    else
                    {
                        $user_list[$x]['manual_overall_score']= $manual_overall_score;
                    }
                    
                    //$user_list[$x]['manual_overall_score']= !empty($manual_score->score) ? $$manual_score->score: '-';                    
                   
                    if($manual_overall_score==0)
                    {
                        if($ai_score1==0)
                        {
                            $total='-';
                            $diff='-';
                        }
                        else
                        {
                        $total=$ai_score1;
                        $diff=round(($ai_score1-$manual_overall_score),2);
                        }
                    }
                    
                    elseif($ai_score1==0)
                    {
                        if($manual_overall_score==0)
                        {
                            
                            $total='-';
                            $diff='-';
                        }
                        else
                        {
                            $total=$manual_overall_score;
                            $diff=round(($ai_score1-$manual_overall_score),2);
                        }

                    }
                    else
                    {
                    //$sum_differnce=$this->report_model->sum_differnce($assessment_id,$user_id);
                    $total=round(($ai_score1+$manual_overall_score)/2,2);
                    $diff=round(($ai_score1-$manual_overall_score),2);
                    }
                    $user_list[$x]['aiandmanual']= $total;
                    $user_list[$x]['differnce']= $diff;
                    
                    
                    $rating = '';
                        if ((float)$ai_score1 >= 75){
                            $rating = 'Above 75%';
                        }else if ((float)$ai_score1 < 75 AND (float)$ai_score1 >= 60){
                            $rating = '60 to 74%';
                        }else if ((float)$ai_score1 < 60 AND (float)$ai_score1 >= 40){
                            $rating = '40 to 59%';
                        }else if ((float)$ai_score1 < 40 and (float)$ai_score1 > 0){
                            $rating = 'Less then 40';
                        }
                        else
                        {
                            $rating='-';
                        }
                        $user_list[$x]['ai_rating']= $rating;
                         

                    $manual_rating = '';
                        if ((float)$manual_overall_score >= 75){
                            $manual_rating = 'Above 75%';
                        }else if ((float)$manual_overall_score < 75 AND (float)$manual_overall_score >= 60){
                            $manual_rating = '60 to 74%';
                        }else if ((float)$manual_overall_score < 60 AND (float)$manual_overall_score >= 40){
                            $manual_rating = '40 to 59%';
                        }else if ((float)$manual_overall_score < 40 AND (float) $manual_overall_score >0){
                            $manual_rating = 'Less then 40';
                        }
                        else
                        {
                            $manual_rating='-';
                        }
                        $user_list[$x]['manual_rating']= $manual_rating;
          
                    
                    $reg_date=date_create($ud->registration_date);
                    $today = date_create(date('d-m-Y'));
                    $interval = date_diff($reg_date, $today)->format('%R%a days');
                    if($interval<182.5)
                    {
                        $join_interval="06 months";
                    }
                    elseif($interval>182.5 AND $interval < 730)
                    {
                        $join_interval="Within 2 years";   
                    }
                    elseif($interval>730 AND $interval <1825)
                    {
                        $join_interval="2 years to 5 years";
                    }
                    else
                    {
                        $join_interval="5 years and above";
                    }
                    $user_list[$x]['join_range']= $join_interval;
               
                    //print_r($join_interval);
                    //echo "<pre>";
                    //print_r($user_list);
                    //exit;
                    //die("here");
                    $x++;
               
            }   
       // }
            
        
        //} 
        $dtSearchColumns = array('user_id','user_name');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

            $DTRenderArray = $user_list;
            //echo "<pre>";
           // print_r($DTRenderArray);
            //exit;
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                //"iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
                "iTotalRecords" => count((array)$user_list),
                "iTotalDisplayRecords" => 15,
                "aaData" => array()
            );
            //$dtDisplayColumns = array('user_id', 'traineename','designation', 'workshop_name','workshop_type','workshop_subtype','region_name','sub_region','workshop_session', 'questionset', 'trainername', 'tregion_name', 'topicname', 'subtopicname', 'question_title', 'correct_answer', 'user_answer', 'start_dttm', 'end_dttm', 'seconds', 'timer', 'question_result');
            $dtDisplayColumns[0] = 'user_id';
            $dtDisplayColumns[1] = 'user_name';
            $dtDisplayColumns[2] ='reg_date';
            $dtDisplayColumns[3]= 'division';
            $dtDisplayColumns[4]='pc_hq';
            $dtDisplayColumns[5]='state';
            $dtDisplayColumns[6]='zone';
            $dtDisplayColumns[7]='ec';
            $dtDisplayColumns[8]='ec_name';
            $dtDisplayColumns[9]='email';
            $dtDisplayColumns[10]='assessment_name';
            $dtDisplayColumns[11]='status';
            $dtDisplayColumns[12]='ai_overall_score';
            $y=13;           
            $parameter_score_result = $this->report_status_model->get_parameters($company_id,$assessment_id1);
            foreach ($parameter_score_result as $psr){
     
                $dtDisplayColumns[$y] = $psr->parameter_name;
                $y++;
            }
            $dtDisplayColumns[$y++]='manual_overall_score';
            $dtDisplayColumns[$y++]='aiandmanual';
            $dtDisplayColumns[$y++]='differnce';
            $dtDisplayColumns[$y++]='ai_rating';
            $dtDisplayColumns[$y++]='manual_rating';
            $dtDisplayColumns[$y++]='join_range';
           
            //$dtDisplayColumns = array('user_id','user_name','reg_date','division','pc_hq','zone','state','ec','ec_name','email','assessment_name','status','ai_overall_score','Voice Modulation','Objection Handling','Pitch','Body Language','Pace Of Speech','Awarathon Information','Aargon Product ','Q2 Aargon ','Q3 Aargon ','Q4 Aargon ','manual_overall_score','aiandmanual','differnce','ai_rating','manual_rating','join_range');
            
           
            $site_url = base_url();
            $acces_management = $this->acces_management;
    
            foreach ($DTRenderArray as $dtRow) {
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($dtDisplayColumns[$i] != ' ' AND isset($dtDisplayColumns)) {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
             echo json_encode($output);
    }
    public function exportReport() {//In use for Export
        $Company_name = "";
        $Company_id = $this->mw_session['company_id'];
        //$Company_id=67;
        if ($Company_id != "") {
      
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $company_id = $this->mw_session['company_id'];
           // $company_id=67;
            $user_id = $this->input->post('manager_id',true);
            $status_id = $this->input->post('status_id',true);
            $dtWhere='';
        if($user_id !="")
        {
        if($dtWhere<>''){
                $dtWhere .= " AND c.user_id  = ".$user_id; 
           }else{
               $dtWhere .= " AND c.user_id = ".$user_id; 
           } 
        }
        $assessment_id1 = $this->input->post('assessment_id',true);
        
        if($assessment_id1 !="")
        {
        if($dtWhere<>''){
                $dtWhere .= " AND a.assessment_id  = ".$assessment_id1; 
           }else{
               $dtWhere .= " AND a.assessment_id = ".$assessment_id1; 
           } 
        }     
        
        if($status_id=="0")
        {
            // $user_details= $this->report_status_model->get_participants($company_id, $dtWhere);
            $user_details= $this->report_status_model->status_check($company_id, $status_id, $dtWhere);
            $user_list = [];
            $x=0;
            foreach ($user_details as $ud){               
                $assessment_name  = $ud->assessment;
                $user_list[$x]['E Code'] = $ud->emp_id;
                $user_list[$x]['Employee name']= $ud->user_name;
                $b=$ud->registration_date;
                $a= date("d/m/Y", strtotime($b));
                
                $user_list[$x]['DOJ']= $a;
                $user_list[$x]['division']= $ud->department;
                $user_list[$x]['pc hq']= "";
                $user_list[$x]['state']= "";
                $user_list[$x]['zone']= $ud->region_name;
                $user_list[$x]['EC']= "";
                $user_list[$x]['L+1 name']= "";
                
                $user_id= $ud-> user_id;
                $assessment_id=$ud->assessment_id;
                $assessment_name=$ud->assessment;

                //$assessment_detils= $this->report_model->get_assessment($user_id, $dtWhere);
                //foreach($assessment_detils as $ad)
               // {
                 //   $assessment_id= $ad->assessment_id;

                    $user_list[$x]['Email']= $ud->email;  
                    $user_list[$x]['assessment_name']= $ud->assessment;
                    $user_list[$x]['status']= 'completed';
                    //$ai_score= $this->report_model->get_ai_score($company_id, $assessment_id, $user_id);  
                    $overall_score = 0;
                    $ai_score= $this->report_status_model->get_ai_score($company_id, $assessment_id, $user_id);
                    /*if (isset($ai_score) AND count((array)$ai_score)>0){
                        $overall_score = $ai_score;
                    }*/
                    
                    //$user_list[$x]['ai_overall_score']= $ai_score;
                    $user_list[$x]['AI Score']= !empty($ai_score->overall_score) ? $ai_score->overall_score: '-';
                    $ai_score1= !empty($ai_score->overall_score) ? $ai_score->overall_score: 0;
                    $parameter_score_result = $this->report_status_model->get_parameters($company_id,$assessment_id);
                    
                    foreach ($parameter_score_result as $psr){
                        $parameter_id= $psr->parameter_id;
                        $parameter_name =$psr->parameter_name;
                      //  print_r($psr->parameter_name);
                       // exit;
                            $parameter_label_id            = $psr->parameter_label_id;
                            $parameter_your_score_result   = $this->report_status_model->get_parameters_your_score($company_id,$assessment_id,$user_id,$parameter_id,$parameter_label_id);
                      
                            //$user_list[$x][$parameter_name]= $parameter_your_score_result->score;
                            $user_list[$x][$parameter_name]= !empty($parameter_your_score_result->score) ? $parameter_your_score_result->score: '-';
                          $user_score[]= !empty($parameter_your_score_result->score) ? $parameter_your_score_result->score: '-';
                          //print_r($user_score);
                          //exit;
                                                       
                    }
                    $manual_overall_score=0;
                    $manual_score = $this->report_status_model->get_manual_score($assessment_id,$user_id);
                    if (isset($manual_score) AND count((array)$manual_score)>0){
                        $manual_overall_score = $manual_score->overall_score;
                    }
                    
                    
                    if($manual_overall_score=="0")
                    {   
                        $user_list[$x]['Assessor Rating']= '-';    
                    }
                    else
                    {
                        $user_list[$x]['Assessor Rating']= $manual_overall_score;
                    }
                    
                    //$user_list[$x]['manual_overall_score']= !empty($manual_score->score) ? $$manual_score->score: '-';                    
                    if($manual_overall_score==0)
                    {
                        if($ai_score1==0)
                        {
                            $total='-';
                            $diff='-';
                        }
                        else
                        {
                        $total=$ai_score1;
                        $diff=round(($ai_score1-$manual_overall_score),2);
                        }
                    }
                    elseif($ai_score1==0)
                    {
                        if($manual_overall_score==0)
                        {
                            $total='-';
                            $diff='-';
                        }
                        else
                        {
                            $total=$manual_overall_score;
                            $diff=round(($ai_score1-$manual_overall_score),2);
                        }

                    }
                    else
                    {
                    //$sum_differnce=$this->report_model->sum_differnce($assessment_id,$user_id);
                    $total=round(($ai_score1+$manual_overall_score)/2,2);
                    $diff=round(($ai_score1-$manual_overall_score),2);
                    }
                    $user_list[$x]['Overall Avg']= $total;
                    $user_list[$x]['DIFF (AI-Avg)']= $diff;
                    
                    
                    $rating = '';
                        if ((float)$ai_score1 >= 75){
                            $rating = 'Above 75%';
                        }else if ((float)$ai_score1 < 75 AND (float)$ai_score1 >= 60){
                            $rating = '60 to 74%';
                        }else if ((float)$ai_score1 < 60 AND (float)$ai_score1 >= 40){
                            $rating = '40 to 59%';
                        }else if ((float)$ai_score1 < 40 and (float)$ai_score1 > 0){
                            $rating = 'Less then 40';
                        }
                        else
                        {
                            $rating='-';
                        }
                        $user_list[$x]['Ai Rating']= $rating;
                         

                    $manual_rating = '';
                        if ((float)$manual_overall_score >= 75){
                            $manual_rating = 'Above 75%';
                        }else if ((float)$manual_overall_score < 75 AND (float)$manual_overall_score >= 60){
                            $manual_rating = '60 to 74%';
                        }else if ((float)$manual_overall_score < 60 AND (float)$manual_overall_score >= 40){
                            $manual_rating = '40 to 59%';
                        }else if ((float)$manual_overall_score < 40 AND (float) $manual_overall_score >0){
                            $manual_rating = 'Less then 40';
                        }
                        else
                        {
                            $manual_rating='-';
                        }
                        $user_list[$x]['Manual Rating']= $manual_rating;
                    //print_r($manual_score); 
                    
                    $reg_date=date_create($ud->registration_date);
                    $today = date_create(date('d-m-Y'));
                    $interval = date_diff($reg_date, $today)->format('%R%a days');
                    if($interval<182.5)
                    {
                        $join_interval="06 months";
                    }
                    elseif($interval>182.5 AND $interval < 730)
                    {
                        $join_interval="Within 2 years";   
                    }
                    elseif($interval>730 AND $interval <1825)
                    {
                        $join_interval="2 years to 5 years";
                    }
                    else
                    {
                        $join_interval="5 years and above";
                    }
                    $user_list[$x]['Joinning range']= $join_interval;
                    //--
                    
                    //print_r($join_interval);
                    //echo "<pre>";
                    //print_r($user_list);
                    //exit;
                    //die("here");
                    $x++;
               // }    
            }   
        }
        elseif($status_id=="1")
        {
            $user_details= $this->report_status_model->status_check($company_id, $status_id, $dtWhere);
            $user_list = [];
            $x=0;
            foreach ($user_details as $ud){               
                $assessment_name  = $ud->assessment;
                $user_list[$x]['Emp ID'] = $ud->emp_id;
                $user_list[$x]['Emp name']= $ud->user_name;
                $b=$ud->registration_date;
                $a= date("d/m/Y", strtotime($b));
                
                    $user_list[$x]['DOJ']= $a;
                    $user_list[$x]['division']= $ud->department;
                    $user_list[$x]['pc hq']= "";
                    $user_list[$x]['state']= "";
                    $user_list[$x]['zone']= $ud->region_name;
                    $user_list[$x]['EC']= "";
                    $user_list[$x]['L+1 name']= "";
                
                    $user_id= $ud-> user_id;
                    $assessment_id=$ud->assessment_id;
                    $assessment_name=$ud->assessment;
                    $user_list[$x]['Email']= $ud->email;  
                    $user_list[$x]['assessment_name']= $ud->assessment;
                    $user_list[$x]['status']= 'Incomplete';
                    //$ai_score= $this->report_model->get_ai_score($company_id, $assessment_id, $user_id);  
                    $overall_score = 0;
                    $ai_score= $this->report_status_model->get_ai_score($company_id, $assessment_id, $user_id);
                    /*if (isset($ai_score) AND count((array)$ai_score)>0){
                        $overall_score = $ai_score;
                    }*/
                    
                    //$user_list[$x]['ai_overall_score']= $ai_score;
                    $user_list[$x]['AI Score']= !empty($ai_score->overall_score) ? $ai_score->overall_score: '-';
                    $ai_score1= !empty($ai_score->overall_score) ? $ai_score->overall_score: 0;
                    $parameter_score_result = $this->report_status_model->get_parameters($company_id,$assessment_id);
                    
                    foreach ($parameter_score_result as $psr){
                        $parameter_id= $psr->parameter_id;
                        $parameter_name =$psr->parameter_name;
                      //  print_r($psr->parameter_name);
                       // exit;
                            $parameter_label_id            = $psr->parameter_label_id;
                            $parameter_your_score_result   = $this->report_status_model->get_parameters_your_score($company_id,$assessment_id,$user_id,$parameter_id,$parameter_label_id);
                      
                            //$user_list[$x][$parameter_name]= $parameter_your_score_result->score;
                            $user_list[$x][$parameter_name]= !empty($parameter_your_score_result->score) ? $parameter_your_score_result->score: '-';
                          $user_score[]= !empty($parameter_your_score_result->score) ? $parameter_your_score_result->score: '-';
                          //print_r($user_score);
                          //exit;
                                                       
                    }
                    $manual_overall_score=0;
                    $manual_score = $this->report_status_model->get_manual_score($assessment_id,$user_id);
                    if (isset($manual_score) AND count((array)$manual_score)>0){
                        $manual_overall_score = $manual_score->overall_score;
                    }
                    
                    
                    if($manual_overall_score=="0")
                    {   
                        $user_list[$x]['Assessor Rating']= '-';    
                    }
                    else
                    {
                        $user_list[$x]['Assessor Rating']= $manual_overall_score;
                    }
                    
                    //$user_list[$x]['manual_overall_score']= !empty($manual_score->score) ? $$manual_score->score: '-';                    
                    if($manual_overall_score==0)
                    {
                        if($ai_score1==0)
                        {
                            $total='-';
                            $diff='-';
                        }
                        else
                        {
                        $total=$ai_score1;
                        $diff=round(($ai_score1-$manual_overall_score),2);
                        }
                    }
                    elseif($ai_score1==0)
                    {
                        if($manual_overall_score==0)
                        {
                            $total='-';
                            $diff='-';
                        }
                        else
                        {
                            $total=$manual_overall_score;
                            $diff=round(($ai_score1-$manual_overall_score),2);
                        }

                    }
                    else
                    {
                    //$sum_differnce=$this->report_model->sum_differnce($assessment_id,$user_id);
                    $total=round(($ai_score1+$manual_overall_score)/2,2);
                    $diff=round(($ai_score1-$manual_overall_score),2);
                    }
                    $user_list[$x]['Overall Avg']= $total;
                    $user_list[$x]['DIFF (AI-Avg)']= $diff;
                    
                    
                    $rating = '';
                        if ((float)$ai_score1 >= 75){
                            $rating = 'Above 75%';
                        }else if ((float)$ai_score1 < 75 AND (float)$ai_score1 >= 60){
                            $rating = '60 to 74%';
                        }else if ((float)$ai_score1 < 60 AND (float)$ai_score1 >= 40){
                            $rating = '40 to 59%';
                        }else if ((float)$ai_score1 < 40 and (float)$ai_score1 > 0){
                            $rating = 'Less then 40';
                        }
                        else
                        {
                            $rating='-';
                        }
                        $user_list[$x]['AI Rating']= $rating;
                         

                    $manual_rating = '';
                        if ((float)$manual_overall_score >= 75){
                            $manual_rating = 'Above 75%';
                        }else if ((float)$manual_overall_score < 75 AND (float)$manual_overall_score >= 60){
                            $manual_rating = '60 to 74%';
                        }else if ((float)$manual_overall_score < 60 AND (float)$manual_overall_score >= 40){
                            $manual_rating = '40 to 59%';
                        }else if ((float)$manual_overall_score < 40 AND (float) $manual_overall_score >0){
                            $manual_rating = 'Less then 40';
                        }
                        else
                        {
                            $manual_rating='-';
                        }
                        $user_list[$x]['Manual Rating']= $manual_rating;
                    //print_r($manual_score); 
                    
                    $reg_date=date_create($ud->registration_date);
                    $today = date_create(date('d-m-Y'));
                    $interval = date_diff($reg_date, $today)->format('%R%a days');
                    if($interval<182.5)
                    {
                        $join_interval="06 months";
                    }
                    elseif($interval>182.5 AND $interval < 730)
                    {
                        $join_interval="Within 2 years";   
                    }
                    elseif($interval>730 AND $interval <1825)
                    {
                        $join_interval="2 years to 5 years";
                    }
                    else
                    {
                        $join_interval="5 years and above";
                    }
                    $user_list[$x]['Joinning Range']= $join_interval;
                    //--
                    
                    //print_r($join_interval);
                    //echo "<pre>";
                    //print_r($user_list);
                    //exit;
                    //die("here");
                    $x++;
               // }    
            }   

        }    
        else{
            // $user_details= $this->report_status_model->get_participants($company_id, $dtWhere);
            $user_details= $this->report_status_model->status_check($company_id, $status_id, $dtWhere);
            $user_list = [];
            $x=0;
            foreach ($user_details as $ud){               
                $assessment_name  = $ud->assessment;
                $user_list[$x]['user_id'] = $ud->emp_id;
                $user_list[$x]['user_name']= $ud->user_name;
                $b=$ud->registration_date;
                $a= date("d/m/Y", strtotime($b));
                
                $user_list[$x]['reg_date']= $a;
                $user_list[$x]['division']= $ud->department;
                $user_list[$x]['pc_hq']= "";
                $user_list[$x]['state']= "";
                $user_list[$x]['zone']= $ud->region_name;
                $user_list[$x]['ec']= "";
                $user_list[$x]['ec_name']= "";
                
                $user_id= $ud-> user_id;
                $assessment_id=$ud->assessment_id;
                     $user_list[$x]['email']= $ud->email;  
                    $user_list[$x]['assessment_name']= $ud->assessment;
                    $user_list[$x]['status']= $ud->status1;
                    //$ai_score= $this->report_model->get_ai_score($company_id, $assessment_id, $user_id);  
                    $overall_score = 0;
                    $ai_score= $this->report_status_model->get_ai_score($company_id, $assessment_id, $user_id);
                    
                    //$user_list[$x]['ai_overall_score']= $ai_score;
                    $user_list[$x]['ai_overall_score']= !empty($ai_score->overall_score) ? $ai_score->overall_score: '-';
                    $ai_score1= !empty($ai_score->overall_score) ? $ai_score->overall_score: 0;
                    $parameter_score_result = $this->report_status_model->get_parameters($company_id,$assessment_id);
                   
                    foreach ($parameter_score_result as $psr){
                        $parameter_id= $psr->parameter_id;
                        $parameter_name =$psr->parameter_name;
                      //  print_r($psr->parameter_name);
                       // exit;
                            $parameter_label_id            = $psr->parameter_label_id;
                            $parameter_your_score_result   = $this->report_status_model->get_parameters_your_score($company_id,$assessment_id,$user_id,$parameter_id,$parameter_label_id);
                      
                            //$user_list[$x][$parameter_name]= $parameter_your_score_result->score;
                            $user_list[$x][$parameter_name]= !empty($parameter_your_score_result->score) ? $parameter_your_score_result->score: '-';
                          $user_score[]= !empty($parameter_your_score_result->score) ? $parameter_your_score_result->score: '-';
                          //print_r($user_score);
                          //exit;
                                                       
                    }
                    $manual_overall_score=0;
                    $manual_score = $this->report_status_model->get_manual_score($assessment_id,$user_id);
                    if (isset($manual_score) AND count((array)$manual_score)>0){
                        $manual_overall_score = $manual_score->overall_score;
                    }
                    
                    
                    if($manual_overall_score=="0")
                    {   
                        $user_list[$x]['manual_overall_score']= '-';    
                    }
                    else
                    {
                        $user_list[$x]['manual_overall_score']= $manual_overall_score;
                    }
                    
                    //$user_list[$x]['manual_overall_score']= !empty($manual_score->score) ? $$manual_score->score: '-';                    
                   
                    if($manual_overall_score==0)
                    {
                        if($ai_score1==0)
                        {
                            $total='-';
                            $diff='-';
                        }
                        else
                        {
                        $total=$ai_score1;
                        $diff=round(($ai_score1-$manual_overall_score),2);
                        }
                    }
                    
                    elseif($ai_score1==0)
                    {
                        if($manual_overall_score==0)
                        {
                            
                            $total='-';
                            $diff='-';
                        }
                        else
                        {
                            $total=$manual_overall_score;
                            $diff=round(($ai_score1-$manual_overall_score),2);
                        }

                    }
                    else
                    {
                    //$sum_differnce=$this->report_model->sum_differnce($assessment_id,$user_id);
                    $total=round(($ai_score1+$manual_overall_score)/2,2);
                    $diff=round(($ai_score1-$manual_overall_score),2);
                    }
                    $user_list[$x]['aiandmanual']= $total;
                    $user_list[$x]['differnce']= $diff;
                    
                    
                    $rating = '';
                        if ((float)$ai_score1 >= 75){
                            $rating = 'Above 75%';
                        }else if ((float)$ai_score1 < 75 AND (float)$ai_score1 >= 60){
                            $rating = '60 to 74%';
                        }else if ((float)$ai_score1 < 60 AND (float)$ai_score1 >= 40){
                            $rating = '40 to 59%';
                        }else if ((float)$ai_score1 < 40 and (float)$ai_score1 > 0){
                            $rating = 'Less then 40';
                        }
                        else
                        {
                            $rating='-';
                        }
                        $user_list[$x]['ai_rating']= $rating;
                         

                    $manual_rating = '';
                        if ((float)$manual_overall_score >= 75){
                            $manual_rating = 'Above 75%';
                        }else if ((float)$manual_overall_score < 75 AND (float)$manual_overall_score >= 60){
                            $manual_rating = '60 to 74%';
                        }else if ((float)$manual_overall_score < 60 AND (float)$manual_overall_score >= 40){
                            $manual_rating = '40 to 59%';
                        }else if ((float)$manual_overall_score < 40 AND (float) $manual_overall_score >0){
                            $manual_rating = 'Less then 40';
                        }
                        else
                        {
                            $manual_rating='-';
                        }
                        $user_list[$x]['manual_rating']= $manual_rating;
          
                    
                    $reg_date=date_create($ud->registration_date);
                    $today = date_create(date('d-m-Y'));
                    $interval = date_diff($reg_date, $today)->format('%R%a days');
                    if($interval<182.5)
                    {
                        $join_interval="06 months";
                    }
                    elseif($interval>182.5 AND $interval < 730)
                    {
                        $join_interval="Within 2 years";   
                    }
                    elseif($interval>730 AND $interval <1825)
                    {
                        $join_interval="2 years to 5 years";
                    }
                    else
                    {
                        $join_interval="5 years and above";
                    }
                    $user_list[$x]['join_range']= $join_interval;
                    //--
                    
                    //print_r($join_interval);
                    //echo "<pre>";
                    //print_r($user_list);
                    //exit;
                    //die("here");
                    $x++;
               // }    
            }   
       } 
            $Data_list = $user_list;
            $this->load->library('PHPExcel');
            //$objPHPExcel = new PHPExcel();
            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->setActiveSheetIndex(0);
            //$Excel->getActiveSheet()->setCellValueByColumnAndRow(1,1, $report_title);
            $i=1;
            $j=1;
            $dtDisplayColumns = array_keys($user_list[0]);
            
            
            foreach($dtDisplayColumns as $column){
            
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j,1, $column);
                $j++;
            }
            $j=2;
            foreach ($Data_list as $value) {
                
                $i=1;
                foreach($dtDisplayColumns as $column){
                                
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $j, $value[$column]);
                    $i++;
                }
                
                $j++;
            }
            
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            if($assessment_id1!='')
            {
            
                //$assessment_name=$this->common_model->get_selected_values('assessment_mst', 'assessment', 'id=' . $assessment1);
                //print_r($assessment_name);
                //exit;
                header('Content-Disposition: attachment;filename='. "$assessment_name.xls");     
            }
            else
            {
                 header('Content-Disposition: attachment;filename="Report.xls"');
            }
            header('Cache-Control: max-age=0');
            $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
            
        }else{
            
            redirect('report_status');
            
        }
    }
    
    
    public function ajax_assessmentwise_data_manager() {
        $assessment_html= '';
        $assessment_id = ($this->input->post('assessment_id', TRUE) ? $this->input->post('assessment_id', TRUE) : 0); 
        $assessment_list= $this->report_status_model->get_distinct_manager($assessment_id);
        $assessment_html .= '<option value="">';
        if(count((array)$assessment_list)>0){
            foreach ($assessment_list as $value) {  
                
              $assessment_html .='<option value="'.$value->trainer_id.'">'.$value->fullname.'</option>';
            }
        }
        
    $data['assessment_list_data']  = $assessment_html;
    
    
    echo json_encode($data);
}        
}
