<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Store_wise_report extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('workshop_wise_report');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('Workshop_report_model');
    }
    
 public function index() {
        $data['module_id'] = '24.11';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1','company_name');
        } else {
            $data['CompanyData'] = array();
            $trainer_id =$this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
            $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$trainer_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }
            if ($WRightsFlag) {
                    $data['RegionData']    = $this->common_model->get_selected_values('region','id,region_name','company_id='.$Company_id,'region_name');   
                    $data['WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $Company_id . '"', 'workshop_type');
                    $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id,'id desc');
                } else {
                    $this->common_model->SyncWorkshopRights($trainer_id,0);
                    $data['WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
                    $data['WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);
                    $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
                }
             $data['StoreData'] = $this->common_model->get_selected_values('store_mst', 'id,store_name', 'status=1 and company_id=' . $Company_id);
        }
        $data['Company_id'] = $Company_id;                       
        $this->load->view('store_wise_report/index', $data);
    }
 public function ajax_companywise_data() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        if($company_id != ''){
            $data['RegionResult'] = $this->common_model->get_selected_values('region','id,region_name','company_id='.$company_id,'region_name');
            $data['WtypeResult']  = $this->common_model->get_selected_values('workshoptype_mst','id,workshop_type','company_id='.$company_id,'workshop_type');
            $data['WorkshopData'] = $this->common_model->get_selected_values('workshop','id,workshop_name','company_id='.$company_id,'workshop_name');
            echo json_encode($data);
        }
    }
    public function ajax_wtypewise_workshop() {
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $trainer_id =$this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$trainer_id);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0); 
            }
        }
        $user_id="";
        $region_id="";
        $workshop_type = $this->input->post('workshop_type', TRUE);
        if ($WRightsFlag) {
            $data['WorkshopData'] = $this->common_model->getUserWorkshopList($company_id,$user_id,$workshop_type);
        }else{
            $data['WorkshopData'] = $this->common_model->getWkshopRegRightsList($company_id,$trainer_id,$region_id,$workshop_type);
        }
      
        echo json_encode($data);
    }
     public function ajax_resionwise_data() {
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $trainer_id =$this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$trainer_id);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0); 
            }
        }
        $workshop_type ="";
        $region_id = $this->input->post('region_id', TRUE);
         if ($WRightsFlag) {
            $data['WorkshopData'] = $this->Workshop_report_model->getWorkshopList($company_id,$region_id);
         }else{
            $data['WorkshopData'] = $this->common_model->getWkshopRegRightsList($company_id,$trainer_id,$region_id,$workshop_type);
         }
        echo json_encode($data);
    }
    public function DatatableRefresh() {
        $dtSearchColumns = array('r.region_name','wm.workshop_type','w.workshop_name','s.store_name');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $RightsFlag=1;
        $WRightsFlag=1;
        $Login_id  =$this->mw_session['user_id'];          
        if ($this->mw_session['company_id'] == "") {
            $cmp_id= ($this->input->get('company_id') ? $this->input->get('company_id') :'');
            
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
            if(!$this->mw_session['superaccess']){
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type,workshoprights_type','userid='.$Login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }
        }
        $workshop_id = ($this->input->get('workshop_id') ? $this->input->get('workshop_id') : '');
        if ($workshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_id  = " . $workshop_id;
            } else {
                $dtWhere .= " WHERE ar.workshop_id  = " . $workshop_id;
            }
        }
        $workshoptype_id = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '');
        if ($workshoptype_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND wm.id  = " . $workshoptype_id;
            } else {
                $dtWhere .= " WHERE wm.id  = " . $workshoptype_id;
            }
        }

        $region_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '');
        if ($region_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND r.id  = " . $region_id;
            } else {
                $dtWhere .= " WHERE r.id  = " . $region_id;
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
        $session_name = ($this->input->get('workshop_session') ? $this->input->get('workshop_session') : '');
        if ($session_name != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_session  = '" . $session_name."'";
            } else {
                $dtWhere .= " WHERE ar.workshop_session  = '" . $session_name."'";
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
        
        $this->session->set_userdata(array('exportWhere'  =>$dtWhere,'exportHaving'  =>$dthaving, 'RightsFlag' => $RightsFlag, 'WRightsFlag' => $WRightsFlag));
            
        $DTRenderArray = $this->Workshop_report_model->StorewiseDataTable($dtWhere, $dthaving , $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);
        
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('region_name','workshop_type','workshop_name','store_name','participant','played_que','correct','wrong','result');
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
        $objPHPExcel->getActiveSheet()->setCellValue('A1', "")
                ->setCellValue('A3', "Region")
                ->setCellValue('B3', "Workshop Type")
                ->setCellValue('C3', "Workshop name")
                ->setCellValue('D3', "Store Name")
                ->setCellValue('E3', "No. of Trainee participated")
                ->setCellValue('F3', "Questions Played")
                ->setCellValue('G3', "Correct")
                ->setCellValue('H3', "Wrong")
                ->setCellValue('I3', "Result");
        $styleArray = array(
            'font' => array(
                'bold' => true
        ));

        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
        ));
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(23);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
        
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($styleArray_header);


        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $dtWhere     = $this->session->userdata('exportWhere');
        $dthaving    = $this->session->userdata('exportHaving');
        $RightsFlag  = $this->session->userdata('RightsFlag');
        $WRightsFlag = $this->session->userdata('WRightsFlag');
        $i = 3;
        
        $Data_list = $this->Workshop_report_model->storeWiseExportToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag);
        
        
        foreach ($Data_list as $value) {
            $i++;        
            $objPHPExcel->getActiveSheet()                
                    ->setCellValue("A$i", $value->region_name)
                    ->setCellValue("B$i", $value->workshop_type)
                    ->setCellValue("C$i", $value->workshop_name)
                    ->setCellValue("D$i", $value->store_name)
                    ->setCellValue("E$i", $value->participant)
                    ->setCellValue("F$i", $value->played_que)
                    ->setCellValue("G$i", $value->correct)
                    ->setCellValue("H$i", $value->wrong)
                    ->setCellValue("I$i", $value->result);
                    
            $objPHPExcel->getActiveSheet()->getStyle("A$i:I$i")->applyFromArray($styleArray_body);
        }


        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Store-wise Report.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }
}
