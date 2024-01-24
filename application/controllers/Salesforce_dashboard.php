<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Salesforce_dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('assessment_dashboard');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->common_db = $this->common_model->connect_db2();
        $this->acces_management = $acces_management;
        $this->load->model('salesforce_dashboard_model');
    }
    public function index() {
        $data['module_id'] = '44.03';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['Assessment_list'] = $this->common_model->get_selected_values('assessment_mst', 'id,assessment', 'status=1','assessment');
        $data['threshold_list'] = $this->common_model->get_selected_values('company_threshold_salesforce', 'id,category_id,category,input_range', 'assessment_id=0');

        $this->load->view('salesforce_dashboard/index', $data);
    }
    public function samplexls_sales_input() {
        $this->load->library('PHPExcel_CI');
        $Excel = new PHPExcel_CI;
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('User_List');
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
        $Excel->getActiveSheet()->mergeCells('A1:E1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
        ));
        $Excel->getActiveSheet()
				->setCellValue('A2', 'System ID*')
                ->setCellValue('B2', 'Candidate Name*')
                ->setCellValue('C2', 'Input*')
                ->setCellValue('D2', 'Target*')
                ->setCellValue('E2', 'Description');
		$Excel->getActiveSheet()
                ->setCellValue('A3', '21')
				->setCellValue('B3', 'Amit Kumar')
                ->setCellValue('C3', '12')
                ->setCellValue('D3', '10')
                ->setCellValue('E3', 'Sales performances was very good. Keep it up!');
        $Excel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:D2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("20");
        $Excel->getActiveSheet()->getColumnDimension('B')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('C')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('D')->setWidth("30");
		$Excel->getActiveSheet()->getColumnDimension('E')->setWidth("30");

        $Excel->getActiveSheet()->getStyle('A2:E2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'eb3a12'
            )
        ));
        //set aligment to center for that merged cell (A1 to D1)
        $filename = "Sales_input_Import.xls";
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        ob_end_clean();
        $objWriter->save('php://output');
    }
    public function uploadXls_salses_input() {
        $Message = '';
        $SuccessFlag = 1;
        $acces_management = $this->acces_management;
        $user_list = array();
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('assessment_id', 'Assessment name', 'required');
            $assessment_id = $this->input->post('assessment_id');
            if(isset($_FILES['filename'])){
                $this->form_validation->set_rules('filename', '', 'callback_file_check');
            }
            $this->form_validation->set_rules('threshold[]', 'Threshold', 'required');
        }
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            if(isset($_FILES['filename'])){
            $FileData = $_FILES['filename'];
            $this->load->library('PHPExcel_CI');
            $objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumm = $worksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
            if ($highestRow <= 2) {
                $Message .= "Excel row/column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($highestColumnIndex < 4) {
                $Message .= "Excel column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                $user_idarray = array();
                $assessment_set = $this->salesforce_dashboard_model->get_user($assessment_id);
                if(count((array)$assessment_set) > 0){
                    foreach ($assessment_set as $userid) {
                        $user_idarray[] = $userid->user_id;
                    }
                }else{
                    $SuccessFlag = 0;
                    $Message .= "No any candidate played this assessment. </br> ";
                }
                if($SuccessFlag && ($highestRow-2) != count((array)$assessment_set)){
                    $SuccessFlag = 0;
                    $Message .= "Invalid Candidate Details. </br> ";
                }
                for ($row = 3; $row <= $highestRow; $row++) {
                    $user_id = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
					$name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					if ($user_id == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row,Invalid System ID. </br> ";
                        continue;
                    }
                    elseif ($name == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row,Candidate Name is Empty. </br> ";
                        continue;
                    } else {
                        if(!in_array($user_id, $user_idarray)){
							$SuccessFlag = 0;
							$Message .= "Row No. $row,Invalid Candidate details.!! </br> ";
							continue;
						}
                    }
                    $Input_no = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    if ($Input_no == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Input no is Empty. </br> ";
                        continue;
                    } 
                    $Target = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    if ($Target == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Target is Empty. </br> ";
                        continue;
                    }
//                    $desciption = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
//                    if ($desciption == '') {
//                        $SuccessFlag = 0;
//                        $Message .= "Row No. $row, Desciption is Empty. </br> ";
//                        continue;
//                    }
                }
            }
            }
        }
        if ($SuccessFlag) {
            $now = date('Y-m-d H:i:s');
            $Counter = 0;
            if(isset($_FILES['filename'])){
            for ($row = 3; $row <= $highestRow; $row++) {
                $Counter++;
				$user_id = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                $name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                //$Name_array = explode(" ", $name);
                //$lchwhere = "firstname LIKE '" . $Name_array[0] . "' AND lastname LIKE '" . $Name_array[1].(isset($Name_array[2]) ? " ".$Name_array[2]."'" : "'");
                    $Input_no = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $Target = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $desciption = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $lcwhere = "assessment_id=".$assessment_id." AND user_id=" . $user_id;
                    $resultset = $this->common_model->get_value('assessment_import_sales_sheet', 'id', $lcwhere);
                    if (count((array)$resultset) == 0) {
                        $data = array(
                            'assessment_id' => $assessment_id,
                            'user_id' => $user_id,
                            'input' => $Input_no,
                            'target' => $Target,
                            'description' => $desciption
                        );
                        $this->common_model->insert('assessment_import_sales_sheet', $data);
                    } else {
                        $data = array(
                            'input' => $Input_no,
                            'target' => $Target,
                            'description' => $desciption
                        );
                        $this->common_model->update('assessment_import_sales_sheet', 'id', $resultset->id, $data);
                    }
                    $Message = $Counter . " Sales Input sheet Imported successfully.</br>";
            }
            }
            $threshold_id_array = $this->input->post('threshold_id');
            if(count((array)$threshold_id_array) > 0){
                foreach ($threshold_id_array as $key => $th_id) {
                    $thdata = array(
                        'assessment_id' => $this->input->post('assessment_id'),
                        'input_range' => $this->input->post('threshold')[$key]
                    );
                    if($th_id !=''){
                        $this->common_model->update('company_threshold_salesforce', 'id', $th_id, $thdata);
                    }else{
                        $thdata['category_id'] = $this->input->post('category_id')[$key];
                        $thdata['category'] = $this->input->post('category')[$key];
                        $this->common_model->insert('company_threshold_salesforce', $thdata);
                    }
                }
            }
            $Message .=  " Threshold changed successfully.";
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function file_check($str) {
        $allowed_mime_type_arr = array('application/excel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/octet-stream');
        $mime = $_FILES['filename']['type'];
        if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != "") { 
            if (in_array($mime, $allowed_mime_type_arr)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only .xlsx or.xls file.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please select xls to import.');
            return false;
        }
    }
    public function getdashboardData() {
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', TRUE);
        }
        $Rtdata['assessment_id'] = $this->input->post('assessment_id', true);
        
        $tbhtml = '';
        $sales_array =array();
        $threshold_list = $this->common_model->get_selected_values('company_threshold_salesforce', 'id,category_id,category,input_range', 'assessment_id='.$Rtdata['assessment_id']);
       
        if(count((array)$threshold_list) > 0) { 
           foreach($threshold_list as $thr){ 
           $tbhtml .='<tr class="tr-background">
                <td class="wksh-td" width="65%">'.$thr->category.'</td>
                <input type="hidden"  name="category_id[]" value="'.$thr->category_id.'">
                <input type="hidden"  name="category[]" value="'.$thr->category.'">
                <td class="wksh-td form-group" width="35%"><input type="number" class=" form-control input-sm bold theme-font" id="threshold'.$thr->category_id.'" name="threshold[]" value="'.$thr->input_range.'"></td>
                <input type="hidden"  name="threshold_id[]" value="'.$thr->id.'">
            </tr>';
            }
        }else{
          $threshold_new= $this->common_model->get_selected_values('company_threshold_salesforce', 'id,category_id,category,input_range', 'assessment_id=0');
          if(count((array)$threshold_new) > 0) { 
            foreach($threshold_new as $th){ 
              $tbhtml .='<tr class="tr-background">
                  <td class="wksh-td" width="65%">'.$th->category.'</td>
                  <input type="hidden"  name="category_id[]" value="'.$th->category_id.'">
                  <input type="hidden"  name="category[]" value="'.$th->category.'">
                  <td class="wksh-td form-group" width="35%"><input type="number" class=" form-control input-sm bold theme-font" id="threshold'.$th->category_id.'" name="threshold[]" value=""></td>
                  <input type="hidden"  name="threshold_id[]" value="">
              </tr>';
             }
          }
        }
        $data['salestable'] = '';
        $exist_sales = $this->common_model->get_value('assessment_import_sales_sheet', 'id', 'assessment_id='.$Rtdata['assessment_id']);
        if(count((array)$exist_sales)>0){
        $th_range[1]=0;$th_range[2]=0;$th_range[3]=0;
        $sales_range = $this->common_model->get_selected_values('company_threshold_salesforce', 'category_id,input_range', 'assessment_id=' . $Rtdata['assessment_id']);
        if(count((array)$sales_range)>0){
            foreach ($sales_range as $rng) {
                $th_range[$rng->category_id]=$rng->input_range;
            }
        }
        $sales_set = $this->salesforce_dashboard_model->SalesrDataTableRefresh($Rtdata['assessment_id'],$th_range[1],$th_range[2],$th_range[3]);
        if(count((array)$sales_set) > 0){
            foreach ($sales_set as $value) {
                if (!isset($sales_status[$value->knowledge.$value->skill.$value->bussiness])) {
                    $status_set = $this->salesforce_dashboard_model->get_sales_status($value->knowledge,$value->skill,$value->bussiness);
                   
                    $sales_status[$value->knowledge.$value->skill.$value->bussiness]= 1;
                    $sales_array[$value->knowledge.$value->skill.$value->bussiness]['knowledge']=$value->knowledge;
                    $sales_array[$value->knowledge.$value->skill.$value->bussiness]['skill']=$value->skill;
                    $sales_array[$value->knowledge.$value->skill.$value->bussiness]['bussiness']=$value->bussiness;
                    $sales_array[$value->knowledge.$value->skill.$value->bussiness]['status'] = $status_set->status;
                    $sales_array[$value->knowledge.$value->skill.$value->bussiness]['frequency'] = 1;
                    $sales_array[$value->knowledge.$value->skill.$value->bussiness]['percent'] = $value->bussiness_result;
					$sales_array[$value->knowledge.$value->skill.$value->bussiness]['percent_avg'] = array($value->bussiness_result);
                    $sales_array[$value->knowledge.$value->skill.$value->bussiness]['users'][] = $value->user_id;
                } else { 
                    $sales_status[$value->knowledge.$value->skill.$value->bussiness] = 1;
                    $sales_array[$value->knowledge.$value->skill.$value->bussiness]['frequency'] += 1;
                    $sales_array[$value->knowledge.$value->skill.$value->bussiness]['percent']  += $value->bussiness_result;
					$sales_array[$value->knowledge.$value->skill.$value->bussiness]['percent_avg'][] = $value->bussiness_result;
                    $sales_array[$value->knowledge.$value->skill.$value->bussiness]['users'][] = $value->user_id;
                }
                $sales_array[$value->knowledge.$value->skill.$value->bussiness]['High_cl'] = 'background-color:#a4e7bfa1';
                $sales_array[$value->knowledge.$value->skill.$value->bussiness]['Low_cl'] = 'background-color:#ff00005e;';
            }
        }
        $Rtdata['sales_data']= $sales_array;
        $data['salestable'] = $this->load->view('salesforce_dashboard/salestable_view', $Rtdata, true);
        }
        
        $data['tbhtml']= $tbhtml;
        $data['sales_cnt']= count((array)$exist_sales);
        echo json_encode($data);
    }
    public function LoadViewModal($encoded_id, $en_user_str, $status) {

        $assessment_id = base64_decode($encoded_id);
        $data['status'] = base64_decode($status);
        $user_str = base64_decode($en_user_str);
        $data['assessment_data']= $this->common_model->get_value('assessment_mst', 'assessment,assessment_type,assessor_dttm,ratingstyle', 'id=' . $assessment_id);
        $company_id = $this->mw_session['company_id'];
        $User_list = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname) as username,email', ' user_id IN (' . $user_str.')');
        $data['Questions'] = $this->salesforce_dashboard_model->LoadParameterQuestions($assessment_id);
        $data['Userlist'] = $User_list;       
        $data['company_id'] = $company_id;
        $data['assessment_id'] = $assessment_id;
        

        $this->load->view('salesforce_dashboard/ViewAssessmentModal', $data);
    }
    public function getvideoData() {
        $assessment_id = $this->input->post('assessment_id');
        $user_id = $this->input->post('user_id');
        $question_id = $this->input->post('question_id');
        $company_id = $this->mw_session['company_id'];
        $UserData = $this->common_model->get_value('device_users', 'user_id,concat(firstname," ",lastname) as username,email,avatar', 'company_id=' . $company_id . ' AND user_id=' . $user_id);
        $data['video_data'] = $this->common_model->get_value('assessment_results', 'id,video_url', 'question_id=' . $question_id . ' AND user_id=' . $user_id . ' AND assessment_id=' . $assessment_id . " order by id desc");
        $data['sales_data'] = $this->common_model->get_value('assessment_import_sales_sheet', 'input,description', 'user_id=' . $user_id . ' AND assessment_id=' . $assessment_id);
        $data['UserData'] = $UserData;
        echo $this->load->view('salesforce_dashboard/uservideo_view', $data,TRUE);
    }
}
