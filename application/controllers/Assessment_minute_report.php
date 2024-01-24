<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Assessment_minute_report extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('assessment_minute_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('assessment_minute_report_model');
    }
    public function index() {
        $data['module_id'] = '27.3';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['Company_id'] = $this->mw_session['company_id'];   
        if ($data['Company_id'] == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1','company_name');
        } else {
            $data['CompanyData'] = array();
            $data['assess_date_array'] = $this->common_model->get_selected_values('company_billing_minute', 'id,concat(DATE_FORMAT(from_date,"%d-%m-%Y")," To ",DATE_FORMAT(to_date,"%d-%m-%Y")) as assess_date', 'company_id=' . $data['Company_id']);
        } 
        $this->load->view('assessment_minute_report/index', $data);
    } 
    public function DatatableRefresh() {
        $dtSearchColumns = array('a.assessment','total_users','video_order');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $assessmin_id = $this->input->get('assessmin_id',TRUE);
        $assessmin_data = $this->common_model->get_value('company_billing_minute', '*', 'id=' . $assessmin_id);
        $billed_minute = $assessmin_data->billed_minute;
        $from_date = date('Y-m-d', strtotime($assessmin_data->from_date));
        $to_date = date('Y-m-d', strtotime($assessmin_data->to_date));
        
        if($dtWhere ==''){
            $dtWhere .=" WHERE 1=1 ";
        }
		/*else{
            $dtWhere .=" AND ar.ftp_status=1 ";
        }*/
        if($from_date !='' && $to_date !=''){
            $dtWhere .=" AND DATE(am.start_dttm) BETWEEN '".$from_date."' AND '".$to_date."'";  
        }
        $this->session->set_userdata(array('exportWhere'  =>$dtWhere));
        $DTRenderArray = $this->assessment_minute_report_model->LoadAssessmentMinuteData($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('assessment','total_users','utilize_duration');
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
        $objPHPExcel->getActiveSheet()->setCellValue('A1', "Assessment Minutes")
                ->setCellValue('A3', "Assessment Name")
                ->setCellValue('B3', "Total Users Played")
                ->setCellValue('C3', "Total Utilized Minutes");
      
        $styleArray = array(
            'font' => array(
//                'bold' => true
        ));
        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
        ));
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	 
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A3:C3')->applyFromArray($styleArray_header);

        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("A3:C3")->applyFromArray($styleArray_body);
        $i = 3;
        $dtWhere = $this->session->userdata('exportWhere');
        $Data_list = $this->assessment_minute_report_model->exportToExcel($dtWhere);
        
        foreach ($Data_list as $value) {
            $i++;        
            $objPHPExcel->getActiveSheet()                
                    ->setCellValue("A$i", $value->assessment)
		    ->setCellValue("B$i", $value->total_users)
                    ->setCellValue("C$i", $value->utilize_duration);
           $objPHPExcel->getActiveSheet()->getStyle("A$i:C$i")->applyFromArray($styleArray_body);
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Assessment Minutes Report.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }
    public function get_minute_data(){
        $data['acces_management'] = $this->acces_management;

        $htmchart = '';
        $chart_data = array();
        $utilized_min = [];
        $total_utilize = 0;$total_left = 0;$total_duration = 0;
        $assessmin_id = $this->input->post('assessmin_id',TRUE);
        $assessmin_data = $this->common_model->get_value('company_billing_minute', '*', 'id=' . $assessmin_id);
        $billed_minute = $assessmin_data->allocated_minute;
        $from_date = date('Y-m-d', strtotime($assessmin_data->from_date));
        $to_date = date('Y-m-d', strtotime($assessmin_data->to_date)); 
        $dtWhere =" WHERE DATE(am.start_dttm) BETWEEN '".$from_date."' AND '".$to_date."'";  
        $mindata = $this->assessment_minute_report_model->getassessment_minutechart_data($from_date,$to_date);
        if(count((array)$mindata)>0){
                $total_utilize = $mindata->utilize_duration;
                $total_left = $billed_minute - $mindata->utilize_duration;
                $utilized =0;
                $left_minute=0;
                if($billed_minute>0){
                        $utilized = number_format($total_utilize*100/$billed_minute,2);
                        $left_minute = number_format($total_left*100/$billed_minute,2);
                }elseif($total_utilize>0){
                        $utilized =100;
                }
                $chart_data[] = array('name'=>'Utilized Minutes', 'y'=>$utilized,'color'=>'#c0504d','u'=>($utilized));	
                $chart_data[] = array('name'=>'Minutes Left', 'y'=>$left_minute,'color'=>'#4f81bd','u'=>($left_minute));

                $utilized_min[0]=$total_utilize;
                $utilized_min[1]=$total_left;
        }
        $Rdata['utilized_minute'] = json_encode($utilized_min, JSON_NUMERIC_CHECK);
        $Rdata['dataset'] = json_encode($chart_data, JSON_NUMERIC_CHECK);
   
        $htmchart .= $this->load->view('assessment_minute_report/load_piechart',$Rdata,true); 
        
        $data['billed_minute'] = $billed_minute;
        $data['minute_utilized'] = $total_utilize;
        $data['minute_left'] = $total_left;
        $data['minute_graph'] = $htmchart;
        echo json_encode($data);
    }
}
