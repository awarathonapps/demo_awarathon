<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Traineequestionset_wise_report extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
            $acces_management = $this->check_rights('traineequestionset_wise_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('traineequestionset_wise_report_model');
        }
    public function index() {
        $data['module_id'] = '9.23';
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
        }
        $data['Company_id'] = $Company_id;                       
        $this->load->view('traineequestionset_wise_report/index', $data);
    }
    public function getqset_tablecolumn(){
        $Login_id  =$this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id= ($this->input->post('company_id') ? $this->input->post('company_id') :'');
        }else{
            $cmp_id=$this->mw_session['company_id'];
            if(!$this->mw_session['superaccess']){
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$Login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }   
        }
        $wkshop_id = $this->input->post('workshop_id');
        $trainee_id = "";        
        $qset = $this->traineequestionset_wise_report_model->QsetTable($cmp_id,$wkshop_id,$trainee_id);        
        $data['qset'] = $qset;                   
        echo $this->load->view('traineequestionset_wise_report/table_view', $data,true);        
    }
    public function DatatableRefresh() {                
        $dtSearchColumns = array('du.user_id','rg.region_name','CONCAT(du.firstname," ",du.lastname)','du.area','wr.region_name','wt.workshop_type','w.workshop_name','CONCAT(FORMAT(sum(pre),2),"%")');
		$dtSearchColumns1 = array('du.user_id','rg.region_name','CONCAT(du.firstname," ",du.lastname)','du.area','wr.region_name','wt.workshop_type','w.workshop_name','CONCAT(FORMAT(sum(pre),2),"%")');        
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
		$DTRenderArray1 = $this->common_libraries->DT_RenderColumns($dtSearchColumns1);
        $dtWhere = $DTRenderArray['dtWhere'];
		$dtWhere1 = $DTRenderArray1['dtWhere'];
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
        $this->session->set_userdata(array('exportWhere'  =>$dtWhere,'RightsFlag' => $RightsFlag, 'WRightsFlag' => $WRightsFlag,'Company_id' => $cmp_id,'workshop_id'=>$wkshop_id,'trainee_id'=>$user_id));
            
        $DTRenderArray = $this->traineequestionset_wise_report_model->LoadDataTable($dtWhere,$dtOrder, $dtLimit, $RightsFlag, $WRightsFlag,$dtWhere1);
        $spanResult = array();
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        ); 
        $no_of_qset = $this->traineequestionset_wise_report_model->QsetTable($cmp_id,$wkshop_id);
        $dtDisplayColumns = array('emp_id','trainee_region','traineename','area','workshop_region','workshop_type','workshop_name','pre','qset_post','post');        
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count($dtDisplayColumns);                
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == 'qset_post') {
                    if(count($no_of_qset['qset']) > 0){
                        foreach ($no_of_qset['qset'] as $val){
                            $row[] = $this->traineequestionset_wise_report_model->QsetWisePost($cmp_id,$wkshop_id,$dtRow['user_id'],$val['questionset_id']);   
                        }   
                    }
                }
                elseif ($dtDisplayColumns[$i] == 'post') {                       
                    if($dtRow['postplayed'] == 0){                        
                        $row[] = 'Not Played';
                    }else{                        
                        $row[] = $dtRow['post'];
                    }
                }elseif($dtDisplayColumns[$i] == 'pre'){
                    if($dtRow['preplayed'] == 0){
                        $row[] = 'Not Played';
                    }else{
                        $row[] = $dtRow['pre'];
                    }
                }                
                else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;            
        }
        $spanResult['output'] =$output;        
        echo json_encode($spanResult);
    }
    public function exportReport() {//In use for Export
        $dtWhere = $this->session->userdata('exportWhere');           
        $Company_id = $this->session->userdata('Company_id');
        $workshop_id = $this->session->userdata('workshop_id');
        $trainee_id = $this->session->userdata('trainee_id');
        if ($Company_id != "") {                                
        $RightsFlag  = $this->session->userdata('RightsFlag');
        $WRightsFlag = $this->session->userdata('WRightsFlag');        
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $j=1;
        $alpha_array = array();
        for($i='A';$i<='Z';$i++){
            $alpha_array[$j]=$i;
            $j++;
        }
        $qset = $this->traineequestionset_wise_report_model->QsetTable($Company_id,$workshop_id,"");
        $workshop_name = $this->common_model->get_value('workshop','workshop_name','id='.$workshop_id);
        $objPHPExcel->getActiveSheet()->mergeCells('E1:G1');
        for($i='A';$i<='H';$i++){
            $objPHPExcel->getActiveSheet()->mergeCells($i.'2:'.$i.'3');
        }
        $objPHPExcel->getActiveSheet()->setCellValue('E1','Workshop Name : '.$workshop_name->workshop_name)
                ->setCellValue('A2', "Trainee ID")
                ->setCellValue('B2', "Trainee Region")
                ->setCellValue('C2', "Trainee Name")
                ->setCellValue('D2', "Area(HQ)")
                ->setCellValue('E2', "Workshop Region")
                ->setCellValue('F2', "Workshop Type")
                ->setCellValue('G2', "Workshop Name")
                ->setCellValue('H2', "Pre")
                ->setCellValue('I2', "Post(Question SetWise)");
                $j=9; 
                $k=9;
                if(count($qset['qset']) > 0){
                    if(count($qset['qset']) == 1){
                        $objPHPExcel->getActiveSheet()->mergeCells('I2:I2');
			$objPHPExcel->getActiveSheet()->setCellValue($alpha_array[$j+1].'2', 'Average(Post)');
                    }else{
                        $j += count($qset['qset']);						
                        $objPHPExcel->getActiveSheet()->mergeCells('I2:'.$alpha_array[$j-1].'2');
			$objPHPExcel->getActiveSheet()->setCellValue($alpha_array[$j].'2', 'Average(Post)');
                    }					
                    foreach($qset['qset'] as $val){
                        $objPHPExcel->getActiveSheet()->setCellValue($alpha_array[$k].'3', $val['questionset']);  
                        $k++;
                    }
                }				

                $styleArray = array(
                    'font' => array()
                );
                $styleArray_header = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'font' => array(
                    'color' => array('rgb' => '990000')
                    )
                );
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $q=9;
	$r=9;
        if(count($qset['qset']) > 0){  
            if(count($qset['qset']) == 1){
                //$objPHPExcel->getActiveSheet()->mergeCells('I2:I2');
                $objPHPExcel->getActiveSheet()->getStyle('I3:J3')->applyFromArray($styleArray_header);
				$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleArray_header);
            }else{
                $q =$q+ count($qset['qset']);
                $objPHPExcel->getActiveSheet()->mergeCells('I2:'.$alpha_array[$q-1].'2');
                $objPHPExcel->getActiveSheet()->getStyle('I3:'.$alpha_array[$q].'3')->applyFromArray($styleArray_header);
				$objPHPExcel->getActiveSheet()->getStyle('A2:'.$alpha_array[$q].'2')->applyFromArray($styleArray_header);
            }            
            foreach($qset['qset'] as $val){
                $objPHPExcel->getActiveSheet()->getColumnDimension($alpha_array[$r])->setWidth(18);  
                $r++;
            }
        }        
        $objPHPExcel->getActiveSheet()->getColumnDimension($alpha_array[$q+1])->setWidth(14);
        
        $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
        


        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $i = 3;        
        $Data_list = $this->traineequestionset_wise_report_model->exportToExcel($dtWhere, $RightsFlag, $WRightsFlag);
		
        if(count($Data_list) > 0){
            foreach ($Data_list as $value) {
                    $i++;        
                    $objPHPExcel->getActiveSheet()                
                    ->setCellValue("A$i", $value->emp_id)
                    ->setCellValue("B$i", $value->trainee_region)
                    ->setCellValue("C$i", $value->traineename)
                    ->setCellValue("D$i", $value->area)
                    ->setCellValue("E$i", $value->workshop_region)
                    ->setCellValue("F$i", $value->workshop_type)
                    ->setCellValue("G$i", $value->workshop_name);
                    if($value->preplayed == 0){
                       $objPHPExcel->getActiveSheet()->setCellValue("H$i", 'Not Played');
                    }else{
                       $objPHPExcel->getActiveSheet()->setCellValue("H$i", $value->pre);
                    }                    
                    $no_of_qset = $this->traineequestionset_wise_report_model->QsetTable($Company_id,$workshop_id);
                    $z = 9;            
                    if($value->postplayed == 0){
                        $objPHPExcel->getActiveSheet()->setCellValue($alpha_array[$z].$i, 'Not Played');                
                    }
                    else if(count($no_of_qset['qset']) > 0){				
                        foreach ($no_of_qset['qset'] as $val){                                      
                            $qset_post = $this->traineequestionset_wise_report_model->QsetWisePost($Company_id,$workshop_id,$value->user_id,$val['questionset_id']);   
                            $objPHPExcel->getActiveSheet()->setCellValue($alpha_array[$z].$i, $qset_post);
                            $z++;
                        }   
                    }            
                    if($value->postplayed == 0){	
                        $objPHPExcel->getActiveSheet()->setCellValue($alpha_array[$z+1].$i, 'Not Played');
                    }else{
                        $objPHPExcel->getActiveSheet()->setCellValue($alpha_array[$z].$i, $value->post);
                    }

                    $objPHPExcel->getActiveSheet()->getStyle("A$i:".$alpha_array[$z].$i)->applyFromArray($styleArray_body);
            }
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="traineequestionset_wise_report.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // ob_end_clean();
        $objWriter->save('php://output');
        }else{
            redirect('traineequestionset_wise_report');
        }                
    }

}
