<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

if (!defined('BASEPATH')) exit('No direct script access allowed');
class Assessment_trainee_report extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('assessment_trainee_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('assessment_trainee_report_model');
        }
 public function index() {
        $data['module_id'] = '27.1';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        $WRightsFlag=1;
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1','company_name');
        } else {
            $data['CompanyData'] = array();
            $trainer_id =$this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
            $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$trainer_id);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }
               $data['AssessmentData'] = $this->common_model->get_selected_values('assessment_mst', 'id,assessment', 'company_id=' . $Company_id);    
               $data['report_type'] = $this->common_model->get_selected_values('assessment_report_type', 'id,description,default_selected', 'status=1');    
               //$data['RegionData'] = $this->assessment_trainee_report_model->getUserRegionList($Company_id);
               //$data['TraineeData'] = $this->assessment_trainee_report_model->getUserTraineeList($Company_id);
               //$data['ParameterData'] = $this->assessment_trainee_report_model->getParametersList($Company_id);    
               //$data['DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'company_id=' . $Company_id);    
//               $data['ParameterData'] = $this->common_model->get_selected_values('parameter_mst', 'id,description', 'company_id=' . $Company_id);    
        }
        $data['Company_id'] = $Company_id;                       
        $this->load->view('assessment_trainee_report/index', $data);
    }
    public function ajax_companywise_data() {
            if ($this->mw_session['company_id'] == "") {
               $company_id = $this->input->post('company_id', TRUE); 
            } else {
                $company_id = $this->mw_session['company_id'];
            }
            $parameter_html='<option value="0">All Parameter</option>';
            $region_html='<option value="0">All Region</option>';
            $trainee_html='<option value="0">All Trainee</option>'; 
            $designation_html='<option value="0">All Designation</option>';

            $ParameterDataset = $this->assessment_trainee_report_model->getParametersList($company_id,'','');            
            $RegionDataset = $this->assessment_trainee_report_model->getUserRegionList($company_id,'');            
            $TraineeDataset = $this->assessment_trainee_report_model->getUserTraineeList($company_id,'');            
            $DesignationDataset = $this->assessment_trainee_report_model->getUserDesignationList($company_id,'');            
            
            if(count((array)$ParameterDataset)>0){
                foreach ($ParameterDataset as $value) {
                    $parameter_html .='<option value="'.$value->id.'">'.$value->parameter_label_name.'</option>';
                }
            }
            if(count((array)$RegionDataset)>0){
                foreach ($RegionDataset as $value) {
                    $region_html .='<option value="'.$value->id.'">'.$value->region_name.'</option>';
                }
            }
            if(count((array)$TraineeDataset)>0){
                foreach ($TraineeDataset as $value) {
                    $trainee_html .='<option value="'.$value->user_id.'">'.$value->traineename.'</option>';
                }
            }
            if(count((array)$DesignationDataset)>0){
                foreach ($DesignationDataset as $value) {
                    $designation_html .='<option value="'.$value->id.'">'.$value->description.'</option>';
                }
            }            
        $data['RegionData']     = $region_html;
        $data['TraineeData']    = $trainee_html;
        $data['DesignationData']= $designation_html;
        $data['ParameterData']  = $parameter_html;
        
        echo json_encode($data);
    }
    public function ajax_assessmentwise_data() {
            if ($this->mw_session['company_id'] == "") {
               $company_id = $this->input->post('company_id', TRUE); 
            } else {
                $company_id = $this->mw_session['company_id'];
            }
            $assessment_id = ($this->input->post('assessment_id', TRUE) ? $this->input->post('assessment_id', TRUE) : 0); 
            $parameter_html='<option value="0">All Parameter</option>';
            $region_html='<option value="0">All Region</option>';
            $trainee_html='<option value="0">All Trainee</option>'; 
            $designation_html='<option value="0">All Designation</option>';

            $ParameterDataset = $this->assessment_trainee_report_model->getParametersList($company_id,$assessment_id,'');            
            $RegionDataset = $this->assessment_trainee_report_model->getUserRegionList($company_id,$assessment_id);            
            $TraineeDataset = $this->assessment_trainee_report_model->getUserTraineeList($company_id,$assessment_id);            
            $DesignationDataset = $this->assessment_trainee_report_model->getUserDesignationList($company_id,$assessment_id);            
            
            if(count((array)$ParameterDataset)>0){
                foreach ($ParameterDataset as $value) {
                    $parameter_html .='<option value="'.$value->id.'">'.$value->parameter_label_name.'</option>';
                }
            }
            if(count((array)$RegionDataset)>0){
                foreach ($RegionDataset as $value) {
                    $region_html .='<option value="'.$value->id.'">'.$value->region_name.'</option>';
                }
            }
            if(count((array)$TraineeDataset)>0){
                foreach ($TraineeDataset as $value) {
                    $trainee_html .='<option value="'.$value->user_id.'">'.$value->traineename.'</option>';
                }
            }
            if(count((array)$DesignationDataset)>0){
                foreach ($DesignationDataset as $value) {
                    $designation_html .='<option value="'.$value->id.'">'.$value->description.'</option>';
                }
            }            
        $data['RegionData']     = $region_html;
        $data['TraineeData']    = $trainee_html;
        $data['DesignationData']= $designation_html;
        $data['ParameterData']  = $parameter_html;
        
        echo json_encode($data);
    }
    public function ajax_desigparawise_data() {
            if ($this->mw_session['company_id'] == "") {
               $company_id = $this->input->post('company_id', TRUE); 
            } else {
                $company_id = $this->mw_session['company_id'];
            }
            $assessment_id = ($this->input->post('assessment_id', TRUE) ? $this->input->post('assessment_id', TRUE) : 0);
            $designation_id = $this->input->post('designation_id', TRUE); 
            $parameter_id = $this->input->post('parameter_id', TRUE); 
            $trainee_html='<option value="0">All Trainee</option>'; 
            
            $TraineeDataset = $this->assessment_trainee_report_model->getParaTraineeList($company_id,$assessment_id,$designation_id,$parameter_id);            
            if(count((array)$TraineeDataset)>0){
                foreach ($TraineeDataset as $value) {
                    $trainee_html .='<option value="'.$value->user_id.'">'.$value->traineename.'</option>';
                }
            }
        $data['TraineeData']    = $trainee_html;
        echo json_encode($data);
    }  
    public function DatatableRefresh() {
        $report_type = ($this->input->get('report_type') ? $this->input->get('report_type') : '1');
        $report_type_catg = $this->input->get('report_type_catg');
        
        if($report_type==1){
            $dtSearchColumns = array('ar.user_id','du.emp_id','rg.region_name','concat(du.firstname," ",du.lastname)','dt.description','am.assessment','pm.description');
        }else if($report_type==2){
            $dtSearchColumns = array('ar.user_id','du.emp_id','rg.region_name','concat(du.firstname," ",du.lastname)','dt.description','am.assessment','aq.question');
        }else{
            $dtSearchColumns = array('ar.user_id','du.emp_id','rg.region_name','concat(du.firstname," ",du.lastname)','dt.description','am.assessment');
        }

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $RightsFlag=1;
        $WRightsFlag=1; 
        $Login_id  =$this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id= ($this->input->get('company_id') ? $this->input->get('company_id') :'');
        } else {
            $cmp_id=$this->mw_session['company_id'];
            if(!$this->mw_session['superaccess']){
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$Login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }   
        }
        if($cmp_id !=""){            
			if($dtWhere<>''){
				 $dtWhere .= " AND am.company_id  = ".$cmp_id; 
			}else{
				$dtWhere .= " WHERE am.company_id  = ".$cmp_id; 
			}                                  
		}
        $assessment_id = ($this->input->get('assessment_id') ? $this->input->get('assessment_id') : '0');
        if ($assessment_id != "0") {
            $dtWhere .= " AND ar.assessment_id  = " . $assessment_id;
        }
        $user_id = ($this->input->get('user_id') ? $this->input->get('user_id') : '0');
        if ($user_id != "0") {
            $dtWhere .= " AND ar.user_id  = " . $user_id;
        }
 
        $dthaving= '';
        $range_value = ($this->input->get('range_id') ? $this->input->get('range_id') : '');
        
            if($range_value !=''){
                $range_id = explode("-",$range_value);
                    if(count((array)$range_id) > 0){
                        $from_range = $range_id[0];        
                        $to_range = $range_id[1];
                        if ($from_range != "" && $to_range != "") {
                        $dthaving = " HAVING hv_range between " .$from_range." and " . $to_range;  
                        }
                    }
            }
        $tregion_id = ($this->input->get('tregion_id') ? $this->input->get('tregion_id') : '0');
        if ($tregion_id != "0") {
            $dtWhere .= " AND du.region_id  = " . $tregion_id;
        }
        $designation_id= ($this->input->get('designation_id') ? $this->input->get('designation_id') :'0');
        if($designation_id !="0")
        {
			$dtWhere .= " AND du.designation_id  = ".$designation_id;  
        }
        $parameter_id= ($this->input->get('parameter_id') ? $this->input->get('parameter_id') :'0');
        if($parameter_id !="0")
        {
			$dtWhere .= " AND art.parameter_id  = ".$parameter_id; 
        }
        $this->session->set_userdata(array('exportWhere'  =>$dtWhere,'exportHaving'  =>$dthaving, 'RightsFlag' => $RightsFlag, 'WRightsFlag' => $WRightsFlag,'report_type'=>$report_type, 'Company_id' => $cmp_id));
         //print_r($dtWhere);
        $DTRenderArray = $this->assessment_trainee_report_model->LoadDataTable($assessment_id,$dtWhere, $dthaving , $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag,$report_type,$parameter_id, $report_type_catg);
        //print_r($DTRenderArray);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id','emp_id','trainee_region','traineename','designation','assessment','title','total_rating','rating','result');
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
    public function report_wise_assessment()
    {
        $assessment_html= '';
        $report_type_catg =$this->input->post('report_type_catg', true);
        $assessment_list= $this->assessment_trainee_report_model->get_all_assessment($report_type_catg);
        if(count((array)$assessment_list)>0){
            foreach ($assessment_list as $value) {
              $assessment_html .='<option value="'.$value->assessment_id.'">'.$value->assessment.'</option>';
            }
        }
        $data['assessment_list_data']  = $assessment_html;
    
    echo json_encode($data);
    }
    public function exportReport() {//In use for Export
        $dtWhere = $this->session->userdata('exportWhere');
        $Company_name = "";
        $Company_id = $this->session->userdata('Company_id');
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
        //$report_type = $this->session->userdata('report_type');
        $dthaving = $this->session->userdata('exportHaving');
        $RightsFlag  = $this->session->userdata('RightsFlag');
        $WRightsFlag = $this->session->userdata('WRightsFlag');
		$assessment_id = $this->input->post('assessment_id');
                $parameter_id = $this->input->post('parameter_id');
		$report_type = $this->input->post('report_type');
        $report_type_catg = $this->input->post('report_type_catg');
		
        if($report_type==1){
            $title ="Parameters";
            $file_name ="Trainee-Parameters Wise Report.xls";
        }else if($report_type==2){
            $title ="Questions";
            $file_name ="Trainee-Question Wise Report.xls";
        }else{
            $title =" ";
            $file_name ="Trainee-Assessment Wise Report.xls";
        }
//        $this->load->library('PHPExcel');
  //      $objPHPExcel = new PHPExcel();
        $objPHPExcel  =new Spreadsheet();

        $objPHPExcel->setActiveSheetIndex(0);
        if($report_type==3){
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setVisible(false);
        }
        $objPHPExcel->getActiveSheet()->setCellValue('A1', "$Company_name")
                ->setCellValue('A3', "Trainee ID")
                ->setCellValue('B3', "Employee ID")
                ->setCellValue('C3', "Trainee Region")
                ->setCellValue('D3', "Trainee Name")
                ->setCellValue('E3', "Designation")
                ->setCellValue('F3', "Assessment Name")
                ->setCellValue('G3', "$title")
                ->setCellValue('H3', "Total Rating")
                ->setCellValue('I3', "Rating Received")
                ->setCellValue('J3', "Result");
      
        $styleArray = array(
            'font' => array(
//                'bold' => true
        ));

        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
        ));
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(18);
	 
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($styleArray_header);


        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray_body);
        $i = 3;
        
        $Data_list = $this->assessment_trainee_report_model->exportToExcel($assessment_id,$dtWhere, $dthaving, $RightsFlag, $WRightsFlag,$report_type,$parameter_id, $report_type_catg);
            
        foreach ($Data_list as $value) {
            $i++;        
            $objPHPExcel->getActiveSheet()                
                    ->setCellValue("A$i", $value->user_id)
		           ->setCellValue("B$i", $value->emp_id)
                    ->setCellValue("C$i", $value->trainee_region)
                    ->setCellValue("D$i", $value->traineename)
                    ->setCellValue("E$i", $value->designation)
                    ->setCellValue("F$i", $value->assessment)
                    ->setCellValue("G$i", $value->title)
                    ->setCellValue("H$i", $value->total_rating)
                    ->setCellValue("I$i", $value->rating)
                    ->setCellValue("J$i", $value->result);
        //   $objPHPExcel->getActiveSheet()->getStyle("A$i:J$i")->applyFromArray($styleArray_body);
        $objPHPExcel->getActiveSheet()->getStyle("A$i:J$i")->getFill();
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        //header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        header('Cache-Control: max-age=0');
    //    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter =\PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        // ob_end_clean();
        $objWriter->save('php://output');
        }else{
            redirect('assessment_trainee_report');
        }
        
        // Sending headers to force the user to download the file
    }

}