<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Trainee_consolidated_report extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('awarathon_session') == FALSE) {
            redirect('index');
        } else {
            $this->mw_session = $this->session->userdata('awarathon_session');
            $acces_management = CheckRights($this->mw_session['user_id'], 'trainee_consolidated_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('workshop_report_model');
            $this->load->model('common_model');            
        }
    }     
    public function index() {
        $data['module_id'] = '23.3';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompanyData'] = array();
        }
        $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'id,workshop_name', 'status=1');
        $data['Company_id'] = $Company_id;                       
        $this->load->view('trainee_consolidated_report/index', $data);
    }
   public function ajax_companywise_data() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        
        $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $company_id);        
        $data['TopicData']    = $this->common_model->get_selected_values('question_topic','id,description','company_id='.$company_id);
        $data['TrainerData']  = $this->common_model->get_selected_values('company_users','userid,concat(first_name," ",last_name," (",email,")") as trainername','company_id='.$company_id);
        $data['TraineeData']  = $this->common_model->get_selected_values('device_users','user_id,concat(firstname," ",lastname," (",email,")") as traineename','company_id='.$company_id);
        echo json_encode($data);
    } 
    public function ajax_topicwise_data() {
        $topic_id = $this->input->post('topic_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
                
        $data['SubTopicData'] = $this->common_model->get_selected_values('question_subtopic','id,description','company_id='.$company_id.' and topic_id='.$topic_id);
        echo json_encode($data);
    } 
    public function ajax_trainerwise_data() {
        $trainer_id = $this->input->post('trainer_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }       
        if($company_id !=""){
            $data['TraineeData'] = $this->workshop_report_model->get_traineeData($company_id,$trainer_id);
        }else{
            $data['TraineeData'] =array();
        }
        
        echo json_encode($data);
    }
    public function DatatableRefresh() {
        $dtSearchColumns = array('ar.id','c.company_name','w.workshop_name','ar.workshop_session','qt.description');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
                        

        if ($this->mw_session['company_id'] == "") {
            $cmp_id= (isset($_GET['company_id']) ? $_GET['company_id'] :'');
            
            if($cmp_id !="")
             {            
               if($dtWhere<>''){
                    $dtWhere .= " AND ar.company_id  = ".$cmp_id; 
               }else{
                   $dtWhere .= " WHERE ar.company_id  = ".$cmp_id; 
               }                                  
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.company_id  = " . $this->mw_session['company_id'];
            }else{
                $dtWhere .= " WHERE ar.company_id  = " . $this->mw_session['company_id'];
            } 
        }
        
        $wrkshop_id= (isset($_GET['workshop_id']) ? $_GET['workshop_id'] :'');
        if($wrkshop_id !="")
            {
            if($dtWhere<>''){
                    $dtWhere .= " AND ar.workshop_id  = ".$wrkshop_id; 
               }else{
                   $dtWhere .= " WHERE ar.workshop_id = ".$wrkshop_id; 
               } 
            }
        $session_id= (isset($_GET['sessions']) ? $_GET['sessions'] :'');
        if($session_id !="")
            {
                if($session_id==0){
                    if($dtWhere<>''){
                            $dtWhere .= " AND ar.workshop_session  = 'PRE' "; 
                       }else{
                           $dtWhere .= " WHERE ar.workshop_session = 'PRE' "; 
                       } 
                }else{
                    if($dtWhere<>''){
                            $dtWhere .= " AND ar.workshop_session  = 'POST' "; 
                       }else{
                           $dtWhere .= " WHERE ar.workshop_session = 'POST' "; 
                       } 
                }
            }
        $topic_id= (isset($_GET['topic_id']) ? $_GET['topic_id'] :'');
        if($topic_id !="")
            {
            if($dtWhere<>''){
                    $dtWhere .= " AND ar.topic_id  = ".$topic_id; 
               }else{
                   $dtWhere .= " WHERE ar.topic_id = ".$topic_id; 
               } 
            }  
        $subtopic_id= (isset($_GET['subtopic_id']) ? $_GET['subtopic_id'] :'');
        if($subtopic_id !="")
            {
            if($dtWhere<>''){
                    $dtWhere .= " AND ar.subtopic_id  = ".$subtopic_id; 
               }else{
                   $dtWhere .= " WHERE ar.subtopic_id = ".$subtopic_id; 
               } 
            }      
            
        $user_id= (isset($_GET['user_id']) ? $_GET['user_id'] :'');
        if($user_id !="")
            {
            if($dtWhere<>''){
                    $dtWhere .= " AND ar.user_id  = ".$user_id; 
               }else{
                   $dtWhere .= " WHERE ar.user_id = ".$user_id; 
               } 
            }  
        $trainer_id= (isset($_GET['trainer_id']) ? $_GET['trainer_id'] :'');
        if($trainer_id !="")
            {
            if($dtWhere<>''){
                    $dtWhere .= " AND ar.trainer_id  = ".$trainer_id; 
               }else{
                   $dtWhere .= " WHERE ar.trainer_id = ".$trainer_id; 
               } 
            } 
            
        $result_search= (isset($_GET['result_search']) ? $_GET['result_search'] :'');
        if($result_search !="")
            {
                if($result_search==1){
                    if($dtWhere<>''){
                            $dtWhere .= " AND ar.is_correct  = 1"; 
                       }else{
                            $dtWhere .= " WHERE ar.is_correct = 1"; 
                       } 
                }
                if($result_search==2){
                    if($dtWhere<>''){
                            $dtWhere .= " AND ar.is_wrong  = 1"; 
                       }else{
                            $dtWhere .= " WHERE ar.is_wrong = 1"; 
                       } 
                }
                if($result_search==3){
                    if($dtWhere<>''){
                            $dtWhere .= " AND ar.is_timeout  = 1"; 
                       }else{
                            $dtWhere .= " WHERE ar.is_timeout = 1"; 
                       } 
                }
            }
            
        $this->session->set_userdata(array('exportWhere'  =>$dtWhere));
            
        $DTRenderArray = $this->workshop_report_model->TraineeConsolidatedLoadDataTable($dtWhere, $dtOrder, $dtLimit);
        
        
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('id','traineename','company_name','workshop_name', 'workshop_session','questionset', 'trainername','topicname', 'subtopicname','question_title','correct_answer','user_answer','start_dttm','end_dttm','seconds','timer','question_result');
        $site_url = base_url();
        $acces_management = $this->acces_management;

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
    public function exportReport() {//In use for Export
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', "Reports")
                ->setCellValue('A3', "Sr. No.")
                ->setCellValue('B3', "Trainee Name")
                ->setCellValue('C3', "Company Name")
                ->setCellValue('D3', "Workshop Name")
                ->setCellValue('E3', "Seesion")                
                ->setCellValue('F3', "QUESTION SET NAME")
                ->setCellValue('G3', "TRAINER NAME")
                ->setCellValue('H3', "TOPIC NAME")
                ->setCellValue('I3', "SUB TOPIC NAME")
                ->setCellValue('J3', "QUESTION TITLE")
                ->setCellValue('K3', "CORRECT ANSWER")
                ->setCellValue('L3', "USER ANSWERER")
                ->setCellValue('M3', "START DATE / TIME	")
                ->setCellValue('N3', "END DATE / TIME")
                ->setCellValue('O3', "SECONDS")
                ->setCellValue('P3', "CORRECT/WRONG/TIME OUT");                              
        $styleArray = array(
            'font' => array(
                'bold' => true
        ));

        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
        ));
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(14);
        
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A3:P3')->applyFromArray($styleArray_header);


        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $exportWhere = $this->session->userdata('exportWhere');
        $i = 3;
        $j = 0;
        $Data_list = $this->workshop_report_model->TraineeConsolidatedExportToExcel($exportWhere);
       
        
        foreach ($Data_list as $value) {
            $i++;
            $j++;            
            $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $j)                    
                    ->setCellValue("B$i", $value->traineename)
                    ->setCellValue("C$i", $value->company_name)
                    ->setCellValue("D$i", $value->workshop_name)
                    ->setCellValue("E$i", $value->workshop_session)
                    ->setCellValue("F$i", $value->questionset)
                    ->setCellValue("G$i", $value->trainername)
                    ->setCellValue("H$i", $value->topicname)
                    ->setCellValue("I$i", $value->subtopicname)
                    ->setCellValue("J$i", $value->question_title)
                    ->setCellValue("K$i", $value->correct_answer)
                    ->setCellValue("L$i", $value->user_answer)
                    ->setCellValue("M$i", $value->start_dttm)
                    ->setCellValue("N$i", $value->end_dttm)
                    ->setCellValue("O$i", $value->seconds)
                    ->setCellValue("P$i", $value->question_result);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:P$i")->applyFromArray($styleArray_body);
        }
        $objPHPExcel->getActiveSheet()->getStyle("A$i:P$i")->applyFromArray($styleArray_body);


        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="report_Exports.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }
}
