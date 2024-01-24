<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Traineetopic_wise_report extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('traineetopic_wise_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('traineetopic_wise_report_model');
        }
 public function index() {
        $data['module_id'] = '24.7';
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
                 if ($WRightsFlag) {
                    $data['TraineeData'] = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname," (",email," )") '
                        . 'as traineename', 'status=1  AND company_id=' . $Company_id, 'firstname'); 
                    $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id);
                    $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id);
                    $data['WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 and company_id=' . $Company_id);
                 } else {
                    $this->common_model->SyncWorkshopRights($trainer_id,0);
                    $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
                    $data['TraineeData'] = $this->common_model->getUserTraineeList($Company_id);
                    $data['WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);
                    $data['WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
                }
            $data['DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'company_id=' . $Company_id);    
//                 $data['WSubTypeData'] = $this->common_model->get_selected_values('workshopsubtype_mst', 'id,description', 'status=1 and company_id=' . $Company_id);
//                 $data['WSubRegionData'] = $this->common_model->get_selected_values('workshopsubregion_mst', 'id,description', 'company_id=' . $Company_id);
//                 $data['TopicData']    = $this->common_model->get_selected_values('question_topic','id,description','company_id='.$Company_id);
        }
        $data['Company_id'] = $Company_id;                       
        $this->load->view('traineetopic_wise_report/index', $data);
    }
       
    public function DatatableRefresh() {
        $report_type = ($this->input->get('report_type') ? $this->input->get('report_type') : '1');
        if($report_type==1){
            $dtSearchColumns = array('ar.user_id','du.emp_id','concat(du.firstname," ",du.lastname)','wr.region_name','wt.workshop_type','w.workshop_name','qt.description','du.lastname','wsr.description','wst.description');
        }else if($report_type==2){
            $dtSearchColumns = array('ar.user_id','du.emp_id','concat(du.firstname," ",du.lastname)','wr.region_name','wt.workshop_type','w.workshop_name','qt.title','du.lastname','wsr.description','wst.description');
        }else{
            $dtSearchColumns = array('ar.user_id','du.emp_id','concat(du.firstname," ",du.lastname)','wr.region_name','wt.workshop_type','w.workshop_name','w.workshop_name','du.lastname','wsr.description','wst.description');
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
                 $dtWhere .= " AND ar.company_id  = ".$cmp_id; 
            }else{
                $dtWhere .= " WHERE ar.company_id  = ".$cmp_id; 
            }                                  
    }else{
        $output = array(
                    "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($output);
            exit;
    }
        
        $user_id = ($this->input->get('user_id') ? $this->input->get('user_id') : '');
        if ($user_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.user_id  = " . $user_id;
            } else {
                $dtWhere .= " WHERE ar.user_id  = " . $user_id;
            }
        }
        $wkshop_id = ($this->input->get('workshop_id') ? $this->input->get('workshop_id') : '');
        if ($wkshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_id  = " . $wkshop_id;
            } else {
                $dtWhere .= " WHERE ar.workshop_id  = " . $wkshop_id;
            }
        }
        $topic_id = ($this->input->get('topic_id') ? $this->input->get('topic_id') : '');
        if ($topic_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.topic_id  = " . $topic_id;
            } else {
                $dtWhere .= " WHERE ar.topic_id  = " . $topic_id;
            }
        }
        $dthaving= '';
        $range_value = ($this->input->get('range_id') ? $this->input->get('range_id') : '');
        
            if($range_value !=''){
                $range_id = explode("-",$range_value);
                    if(count((array)$range_id) > 0){
                        $from_range = $range_id[0];        
                        $to_range = $range_id[1];
                        if ($from_range != "" && $to_range != "") {
                        $dthaving = " HAVING (format(sum(ar.is_correct)*100/count(ar.id),2)) between " .$from_range." and " . $to_range;  
                        }
                    }
            }
          $session_name = ($this->input->get('workshop_session') ? $this->input->get('workshop_session') : '');
        if ($session_name != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_session  = '" . $session_name."'";
            } else {
                $dtWhere .= " WHERE ar.workshop_session  = '" . $session_name."'";
            }
        }
        $store_id = ($this->input->get('store_id') ? $this->input->get('store_id') : '');
        if ($store_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.store_id  = " . $store_id;
            } else {
                $dtWhere .= " WHERE du.store_id = " . $store_id;
            }
        }
        $tregion_id = ($this->input->get('tregion_id') ? $this->input->get('tregion_id') : '0');
        if ($tregion_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.region_id  = " . $tregion_id;
            } else {
                $dtWhere .= " WHERE du.region_id = " . $tregion_id;
            }
        }
        $wrgion_id = ($this->input->get('wregion_id') ? $this->input->get('wregion_id') : '0');
        if ($wrgion_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.region  = " . $wrgion_id;
            } else {
                $dtWhere .= " WHERE w.region = " . $wrgion_id;
            }
        }
        $wsubrgion_id = ($this->input->get('wsubregion_id') ? $this->input->get('wsubregion_id') : '');
        if ($wsubrgion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $wsubrgion_id;
            }
        }
        $workshop_type = ($this->input->get('workshop_type') ? $this->input->get('workshop_type') : '0');
        if ($workshop_type != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshop_type  = " . $workshop_type;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $workshop_type;
            }
        }
        $workshop_subtype = ($this->input->get('workshop_subtype') ? $this->input->get('workshop_subtype') : '');
        if ($workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshop_subtype;
            }
        }
        $designation_id= ($this->input->get('designation_id') ? $this->input->get('designation_id') :'0');
        if($designation_id !="0")
        {
        if($dtWhere<>''){
                $dtWhere .= " AND du.designation_id  = ".$designation_id; 
           }else{
                $dtWhere .= " WHERE du.designation_id = ".$designation_id; 
           } 
        }
        $this->session->set_userdata(array('exportWhere'  =>$dtWhere,'exportHaving'  =>$dthaving, 'RightsFlag' => $RightsFlag, 'WRightsFlag' => $WRightsFlag,'report_type'=>$report_type, 'Company_id' => $cmp_id));
            
        $DTRenderArray = $this->traineetopic_wise_report_model->LoadDataTable($dtWhere, $dthaving , $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag,$report_type);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id','emp_id','trainee_region','traineename','designation','workshop_region','workshop_subregion','workshop_type','workshop_subtype','workshop_name','title','played_que','correct','wrong','result');
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
        $dtWhere = $this->session->userdata('exportWhere');
        $Company_name = "";
        $Company_id = $this->session->userdata('Company_id');
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $report_type = $this->session->userdata('report_type');
        $dthaving = $this->session->userdata('exportHaving');
        $RightsFlag  = $this->session->userdata('RightsFlag');
        $WRightsFlag = $this->session->userdata('WRightsFlag');
        if($report_type==1){
            $title ="Topic";
            $file_name ="Trainee-Topic Wise Report.xls";
        }else if($report_type==2){
            $title ="Questions Set";
            $file_name ="Trainee-Questionsset Wise Report.xls";
        }else{
            $title =" No of Questions Set";
            $file_name ="Trainee-workshop Wise Report.xls";
        }
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', "$Company_name")
                ->setCellValue('A3', "Trainee ID")
                ->setCellValue('B3', "Employee ID")
                ->setCellValue('C3', "Trainee Region")
//                ->setCellValue('D3', "Store Name")
                ->setCellValue('D3', "Trainee Name")
                ->setCellValue('E3', "Designation")
                ->setCellValue('F3', "Workshop Region")
                ->setCellValue('G3', "Workshop Sub-region")
                ->setCellValue('H3', "Workshop type")
                ->setCellValue('I3', "Workshop Sub-type")
                ->setCellValue('J3', "Workshop Name")
                ->setCellValue('K3', $title)
                ->setCellValue('L3', "Questions Played")                
                ->setCellValue('M3', "Correct")
                ->setCellValue('N3', "Wrong")
                ->setCellValue('O3', "Result");
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(14);
        
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A3:O3')->applyFromArray($styleArray_header);


        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $i = 3;
        
        $Data_list = $this->traineetopic_wise_report_model->exportToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag,$report_type);
        
        foreach ($Data_list as $value) {
            $i++;        
            $objPHPExcel->getActiveSheet()                
                    ->setCellValue("A$i", $value->user_id)
					->setCellValue("B$i", $value->emp_id)
                    ->setCellValue("C$i", $value->trainee_region)
//                    ->setCellValue("D$i", $value->store_name)
                    ->setCellValue("D$i", $value->traineename)
                    ->setCellValue("E$i", $value->designation)
                    ->setCellValue("F$i", $value->workshop_region)
                    ->setCellValue("G$i", $value->workshop_subregion)
                    ->setCellValue("H$i", $value->workshop_type)
                    ->setCellValue("I$i", $value->workshop_subtype)
                    ->setCellValue("J$i", $value->workshop_name)
                    ->setCellValue("K$i", $value->title)
                    ->setCellValue("L$i", $value->played_que)
                    ->setCellValue("M$i", $value->correct)
                    ->setCellValue("N$i", $value->wrong)
                    ->setCellValue("O$i", $value->result);
           $objPHPExcel->getActiveSheet()->getStyle("A$i:O$i")->applyFromArray($styleArray_body);
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // ob_end_clean();
        $objWriter->save('php://output');
        }else{
            redirect('traineetopic_wise_report');
        }
        
        // Sending headers to force the user to download the file
    }

}
