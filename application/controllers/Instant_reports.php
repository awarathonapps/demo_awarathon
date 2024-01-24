<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
defined('BASEPATH') OR exit('No direct script access allowed');
class instant_reports extends MY_Controller {
    function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('ai_process_reports');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->common_db = $this->common_model->connect_db2();
        $this->acces_management = $acces_management;
        $this->load->model('instant_reports_model');
    }
    public function index() {
        $data['module_id'] = '14.04';
        $data['acces_management'] = $this->acces_management;
        $data['assessment']=$this->instant_reports_model->get_assessments();
       
        $data['company_id'] = $this->mw_session['company_id'];
        $this->load->view('instant_reports/index_tabs',$data);
    }
    //AI Process functions -------------------------------------------------------------------------------------------------------------------
    function fetch_process_participants(){
        $html = '';
        $company_id = $this->mw_session['company_id'];
        $asssessment_id = $this->input->post('assessment_id', true);
        $_participants_result =$this->instant_reports_model->get_process_participants($company_id,$asssessment_id);
        $data['_participants_result'] = $_participants_result;
        $data['assesment_id'] = $asssessment_id;
        // print_r($data);exit;
        $html = $this->load->view('instant_reports/ai_process_participants',$data,true);
        
        $data['html'] = $html;
        $data['success'] = "true";
        $data['message'] = "";
        echo json_encode($data);
    }
   
     public function exportAi_Report_search() {//In use for Export
       
            $Company_id = $this->session->userdata();
           
            $c_id=$Company_id['awarathon_session']['company_id'];
          
            $user_id=$this->input->post('user_id',true);
            $assessment_id=$this->input->post('assesment_id',true);
          
            $file_name="Spotlight_dump_".$assessment_id."_".$user_id.".xls";
            $objPHPExcel  =new Spreadsheet();
    
            $objPHPExcel->setActiveSheetIndex(0);
           
            $objPHPExcel->getActiveSheet()->setCellValue('A2', "Question Fired ")
                    ->setCellValue('B2', "Embed Value")
                    ->setCellValue('C2', "Answer Transcript (Text)")
                    ->setCellValue('D2', "Cosine Score");
                    
          
            $styleArray = array(
                'font' => array(
    //                'bold' => true
            ));
    
            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
            ));
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
           
         
            $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->applyFromArray($styleArray_header);
    
    
            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
    //                    'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray_body);
            $i = 2;
           
             $Data_list=$this->instant_reports_model->get_questions_user_details($c_id,$assessment_id,$user_id);
            //   print_r($Data_list);
            //   exit;
            foreach ($Data_list as $value) {
                $i++;      
                $score="'".$value->cosine_score."'";  
                $objPHPExcel->getActiveSheet()                
                        ->setCellValue("A$i", "$value->question")
                        ->setCellValue("B$i", "$value->embeddings")
                        ->setCellValue("C$i", "$value->audio_totext")
                        ->setCellValue("D$i", "$score");
           
            $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->getFill();
         
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Disposition: attachment;filename="'.$file_name.'"');
            header('Cache-Control: max-age=0');
            $objWriter =\PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        }
}