<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Subjective extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('subjective');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('subjective_model');
        }

    public function index() {
        $data['module_id'] = '24.4';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        $Login_id = $this->mw_session['user_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
            if ($WRightsFlag) {
                 $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id);
                $data['TraineeData'] = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname," (",email," )") '
                        . 'as traineename', 'status=1  AND company_id=' . $Company_id, 'firstname');
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id);
                $data['WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 and company_id=' . $Company_id);
            } else {
                $this->common_model->SyncWorkshopRights($Login_id, 0);
                $data['WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);
                $data['TraineeData'] = $this->common_model->getUserTraineeList($Company_id);
                $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
            }
            $data['DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'company_id=' . $Company_id);
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('subjective/index', $data);
    }

    public function sample_xls() {
//        echo '<pre>';
//       print_r($this->input->post());exit;
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $dtWhere='';    
        $Login_id = $this->mw_session['user_id'];
        
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id',true);
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
       if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE ar.company_id  = " . $cmp_id;
            }
        }
        $user_id = $this->input->post('user_id',true);
        if ($user_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.user_id  = " . $user_id;
            } else {
                $dtWhere .= " WHERE ar.user_id  = " . $user_id;
            }
        }
        $workshop_type = $this->input->post('workshop_type',true);
        if ($workshop_type != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshop_type  = " . $workshop_type;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $workshop_type;
            }
        }
        $wrgion_id = $this->input->post('wregion_id');
        if ($wrgion_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.region  = " . $wrgion_id;
            } else {
                $dtWhere .= " WHERE w.region = " . $wrgion_id;
            }
        }
        $wsubrgion_id = $this->input->post('wsubregion_id',true);
        if ($wsubrgion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $wsubrgion_id;
            }
        }
        $workshop_subtype = $this->input->post('workshop_subtype',true);
        if ($workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshop_subtype;
            }
        }
        $wrkshop_id = $this->input->post('workshop_id');
        if ($wrkshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_id  = " . $wrkshop_id;
            } else {
                $dtWhere .= " WHERE ar.workshop_id = " . $wrkshop_id;
            }
        }
        $region_id = $this->input->post('region_id',true) ;
        if ($region_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.region_id  = " . $region_id;
            } else {
                $dtWhere .= " WHERE du.region_id  = " . $region_id;
            }
        }
//        $designation_id= $this->input->post('designation_id',true);
//        if($designation_id !="0")
//        {
//        if($dtWhere<>''){
//                $dtWhere .= " AND du.designation_id  = ".$designation_id; 
//           }else{
//                $dtWhere .= " WHERE du.designation_id = ".$designation_id; 
//           } 
//        }
        
         $data_array = $this->subjective_model->LoadDataTable($dtWhere, $RightsFlag, $WRightsFlag);
        
//          foreach ($data_array as $value) {
//              print_r($value);
//          }
//     exit;
        $this->load->library('PHPExcel_CI');
        $Excel = new PHPExcel_CI;
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('Subjective data');
        $Excel->createSheet();
        $Excel->getActiveSheet()
                ->setCellValue('A1', 'Do not modify or delete the Columns.');
        $Excel->getActiveSheet()->getStyle('A1:D1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FF0000'
            )
        ));
        //merge cell A1 until D1
        $Excel->getActiveSheet()->mergeCells('A1:D1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
        ));
        $Excel->getActiveSheet()
                ->setCellValue('A2', 'Sr. No.')
                ->setCellValue('B2', 'Employee Code')
                ->setCellValue('C2', 'Employee Name')
                ->setCellValue('D2', 'Designation')
                ->setCellValue('E2', 'Region')
                ->setCellValue('F2', 'Online Test ')
                ->setCellValue('G2', 'Out Off')
                ->setCellValue('H2', 'Recitement')
                ->setCellValue('I2', 'Out Off')
                ->setCellValue('J2', 'Demo')
                ->setCellValue('K2', 'Out Off')
                ->setCellValue('L2', 'Written Test')
                ->setCellValue('M2', 'Out Off')
                ->setCellValue('N2', 'Total')
                ->setCellValue('O2', 'Per');
        
         $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
//        $Excel->getActiveSheet()->getStyle("A2:O2")->applyFromArray($styleArray_body);
        $i = 2;
        $j =1;
          $data_array = $this->subjective_model->LoadDataTable($dtWhere, $RightsFlag, $WRightsFlag);
          
          foreach ($data_array as $value) {
                $i++;
                $Excel->getActiveSheet()
                        ->setCellValue("A$i", $j)
                        ->setCellValue("B$i", $value->user_id)
                        ->setCellValue("C$i", $value->traineename)
                        ->setCellValue("D$i", $value->designation)
                        ->setCellValue("E$i", $value->region_name)
                        ->setCellValue("F$i", $value->played_que)
                        ->setCellValue("G$i", $value->total_que)
                        ->setCellValue("H$i", '')
                        ->setCellValue("I$i", '')
                        ->setCellValue("J$i", '')
                        ->setCellValue("K$i", '')
                        ->setCellValue("L$i", '')
                        ->setCellValue("M$i", '')
                        ->setCellValue("N$i", '')
                        ->setCellValue("O$i", '');
                $j++;
                 $Excel->getActiveSheet()->getStyle("A$i:O$i")->applyFromArray($styleArray_body);
            }

//            $Excel->getActiveSheet()->getStyle("A2:O$i")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:O2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('B')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('C')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('D')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('E')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('F')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('G')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('H')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('I')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('J')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('K')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('L')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('M')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('N')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('O')->setWidth("20");
        $Excel->getActiveSheet()->getStyle('A2:O2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'eb3a12'
            )
        ));
        //set aligment to center for that merged cell (A1 to D1)
        $filename = "Subjective_Import.xls";
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
       if (ob_get_length()) ob_end_clean();
        $objWriter->save('php://output');
     
    }
    
    public function uploads_xls() {
        $thatml= '';
        $Message = '';
        $SuccessFlag = 1;

          $FileData = $_FILES['filename'];
            $Error = '';
            $this->load->library('PHPExcel_CI');
            $objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumm = $worksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
            if ($highestRow < 2) {
                $Message .= "Excel row/column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }if ($highestRow == 2) {
                $Message .= "Excel file cannot be empty.";
                $SuccessFlag = 0;
            }
            if ($highestColumnIndex < 15) {
                $Message .= "Excel column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
        if($SuccessFlag){  
          for($row = 3; $row <= $highestRow; $row++) {
           
              $recitment = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
              $outoff2 = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
              if($recitment !='' && $outoff2 !=''){
                if($recitment > $outoff2 ){
                    $Message .= "Row No. $row,Out off cannot be leass than Recitement</br>";
                    $SuccessFlag = 0;
                     continue;
                  }
              }else{
                   $Message .= "Row No. $row,Out off or Recitement is Empty</br>";
                    $SuccessFlag = 0;
                     continue;
              }
              $demo = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
              $outoff3 = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
              if($demo !='' && $outoff3 !=''){
                if($demo > $outoff3 ){
                   $Message .= "Row No. $row,Out off cannot be leass than Demo</br>";
                   $SuccessFlag = 0;
                    continue;
                }
              }else{
                   $Message .= "Row No. $row,Out off or Demo is Empty</br>";
                    $SuccessFlag = 0;
                     continue;
              }
              $wittentest = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
              $outoff4 = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
              if($wittentest !='' && $outoff4 !=''){
                if($wittentest > $outoff4 ){
                   $Message .= "Row No. $row,Out off cannot be leass than Written Test</br>";
                   $SuccessFlag = 0;
                    continue;
                }
              }else{
                  $Message .= "Row No. $row,Out off or Written Test is Empty</br>";
                    $SuccessFlag = 0;
                     continue;
              }
          }
    }
       if($SuccessFlag){
          $rowdata = array(); 
          
          for($row = 3; $row <= $highestRow; $row++) {
              $onlinetest = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
              $recitment = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
              $demo = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
              $wittentest = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
              
              $total = $onlinetest + $recitment + $demo + $wittentest;
              
              $outoff1 = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
              $outoff2 = $worksheet->getCellByColumnAndRow(8, $row)->getValue(); 
              $outoff3 = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
              $outoff4 = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
              
              $outoff = $outoff1 + $outoff2 + $outoff3 + $outoff4;
              
             $percentage = round(($total * 100/$outoff),2).'%';
                      
//               $rowdata[] = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
               $data = array(
                        'srno' => $worksheet->getCellByColumnAndRow(0, $row)->getValue(),
                        'empid' => $worksheet->getCellByColumnAndRow(1, $row)->getValue(),
                        'empname' => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
                        'designation' => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                        'region' => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
                        'onlinetest' => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                        'outoff1' => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
                        'recitment' => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
                        'outoff2' => $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
                        'demo' => $worksheet->getCellByColumnAndRow(9, $row)->getValue(),
                        'outoff3' => $worksheet->getCellByColumnAndRow(10, $row)->getValue(),
                        'wittentest' => $worksheet->getCellByColumnAndRow(11, $row)->getValue(),
                        'outoff4' => $worksheet->getCellByColumnAndRow(12, $row)->getValue(),
                        'total' => $total,
                        'per' => $percentage,
                    );
               $rowdata[]=$data;
          }
         foreach ($rowdata as $key => $value) {
           $thatml .= '<tr><td>'.$value["srno"].'</td>
           <td>'.$value["empid"].'</td>
           <td>'.$value["empname"].'</td>
           <td>'.$value["designation"].'</td>
           <td>'.$value["region"].'</td>
           <td>'.$value["onlinetest"].'</td> 
           <td>'.$value["outoff1"].'</td>
           <td>'.$value["recitment"].'</td>
           <td>'.$value["outoff2"].'</td>
           <td>'.$value["demo"].'</td>
           <td>'.$value["outoff3"].'</td>
           <td>'.$value["wittentest"].'</td> 
           <td>'.$value["outoff4"].'</td>
           <td>'.$value["total"].'</td>
           <td>'.$value["per"].'</td><tr>';
          
     }
   }
  
        $Rdata['tdata'] = $thatml;
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
     
    }

    public function exportReport() {//In use for Export
    
        $thatml= '';
        $Message = '';
        $SuccessFlag = 1;
//        print_r($_FILES['filename']);exit;
          $FileData = $_FILES['filename'];
            $Error = '';
            $this->load->library('PHPExcel_CI');
            $objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumm = $worksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
       
       if($SuccessFlag){
          $rowdata = array(); 
          
          for($row = 3; $row <= $highestRow; $row++) {
              $onlinetest = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
              $recitment = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
              $demo = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
              $wittentest = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
              
              $total = $onlinetest + $recitment + $demo + $wittentest;
              
              $outoff1 = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
              $outoff2 = $worksheet->getCellByColumnAndRow(8, $row)->getValue(); 
              $outoff3 = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
              $outoff4 = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
              
              $outoff = $outoff1 + $outoff2 + $outoff3 + $outoff4;
              
             $percentage = round(($total * 100/$outoff),2);
                      
//               $rowdata[] = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
               $data = array(
                        'srno' => $worksheet->getCellByColumnAndRow(0, $row)->getValue(),
                        'empid' => $worksheet->getCellByColumnAndRow(1, $row)->getValue(),
                        'empname' => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
                        'designation' => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                        'region' => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
                        'onlinetest' => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                        'outoff1' => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
                        'recitment' => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
                        'outoff2' => $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
                        'demo' => $worksheet->getCellByColumnAndRow(9, $row)->getValue(),
                        'outoff3' => $worksheet->getCellByColumnAndRow(10, $row)->getValue(),
                        'wittentest' => $worksheet->getCellByColumnAndRow(11, $row)->getValue(),
                        'outoff4' => $worksheet->getCellByColumnAndRow(12, $row)->getValue(),
                        'total' => $total,
                        'per' => $percentage,
                    );
               $rowdata[]=$data;
          }
  
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "")
                ->setCellValue('A2', 'Sr. No.')
                ->setCellValue('B2', 'Employee Code')
                ->setCellValue('C2', 'Employee Name')
                ->setCellValue('D2', 'Designation')
                ->setCellValue('E2', 'Region')
                ->setCellValue('F2', 'Online Test ')
                ->setCellValue('G2', 'Out Off')
                ->setCellValue('H2', 'Recitement')
                ->setCellValue('I2', 'Out Off')
                ->setCellValue('J2', 'Demo')
                ->setCellValue('K2', 'Out Off')
                ->setCellValue('L2', 'Written Test')
                ->setCellValue('M2', 'Out Off')
                ->setCellValue('N2', 'Total')
                ->setCellValue('O2', 'Per');

            $styleArray = array(
                'font' => array(
//                    'bold' => true
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A2:O2')->applyFromArray($styleArray_header);


            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );

            $i = 2;

            foreach ($rowdata as $value) {
                $i++;
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$i", $value["srno"])
                        ->setCellValue("B$i", $value["empid"])
                        ->setCellValue("C$i", $value["empname"])
                        ->setCellValue("D$i", $value["designation"])
                        ->setCellValue("E$i", $value["region"])
                        ->setCellValue("F$i", $value["onlinetest"])
                        ->setCellValue("G$i", $value["outoff1"])
                        ->setCellValue("H$i", $value["recitment"])
                        ->setCellValue("I$i", $value["outoff2"])
                        ->setCellValue("J$i", $value["demo"])
                        ->setCellValue("K$i", $value["outoff3"])
                        ->setCellValue("L$i", $value["wittentest"])
                        ->setCellValue("M$i", $value["outoff4"])
                        ->setCellValue("N$i", $value["total"])
                        ->setCellValue("O$i", $value["per"].'%');
             
                $objPHPExcel->getActiveSheet()->getStyle("A$i:O$i")->applyFromArray($styleArray_body);
            }
       }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Subjective Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
     
    }

}
