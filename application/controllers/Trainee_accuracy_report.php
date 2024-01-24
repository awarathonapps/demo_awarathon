<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainee_accuracy_report extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('trainee_accuracy_report');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('trainee_reports_model');
    }

    public function index() {
        $data['module_id'] = '26.3';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Trainee_id = '';
        $data['company_id'] = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($data['company_id'] == "") {
            $data['company_array'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['company_array'] = array();
            if ($this->mw_session['login_type'] != 3) {
                $Login_id = $this->mw_session['user_id'];
                if (!$this->mw_session['superaccess']) {
                    $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                    $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
                }
                if (!$WRightsFlag) {
                    $this->common_model->SyncWorkshopRights($Login_id, 0);
                }
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['company_id'],$WRightsFlag);
                $data['Trainee'] = $this->common_model->getUserTraineeList($data['company_id'],$WRightsFlag);
                $data['RegionData'] = $this->common_model->getUserRegionList($data['company_id'],$WRightsFlag);
            } else {
                $Trainee_id = $this->mw_session['user_id'];
                $data['WtypeResult'] = $this->common_model->getTraineeWTypeList($data['company_id'],$Trainee_id);
                $data['WorkshopResultSet'] = $this->common_model->getUserWorkshopList($data['company_id'], $Trainee_id);
                $data['RegionData'] = $this->common_model->getUserRegionList($data['company_id'],1);
            }
        }
        $data['TraineeRegionData'] = $this->trainee_reports_model->get_TraineeRegionData($data['company_id']);
        $data['DefaultTrainee_id'] = $Trainee_id;
        $data['login_type'] = $this->mw_session['login_type'];
        $this->load->view('trainee_accuracy_report/index', $data);
    }
    
     public function ajax_traineewtypewise_data() {
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            if ($this->mw_session['login_type'] != 3) {
                $Login_id = $this->mw_session['user_id'];
                if (!$this->mw_session['superaccess']) {
                    $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                    $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                    $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
                }
            }
        }
        $trainee_id = $this->input->post('trainee_id', TRUE);
        $workshoptype_id = $this->input->post('workshoptype_id', TRUE);
        $region_id = $this->input->post('region_id', TRUE);
        if ($WRightsFlag) {
            $WorkshopData = $this->common_model->getUserWorkshopList($company_id, $trainee_id, $workshoptype_id,$region_id);
//            $WtypeResult = $this->common_model->getTraineeWTypeList($company_id, $trainee_id);
        } else {
            $WorkshopData = $this->common_model->getTrainerWorkshop($company_id, $WRightsFlag,0,$region_id);
//            $WtypeResult = $this->common_model->getWTypeRightsList($company_id,$WRightsFlag);
        }
         $lchtml='<option value="">Please Select</option>';
//         $lchtml1='<option value="">All Select</option>';
        
        if($WorkshopData > 0){
            foreach ($WorkshopData as $value) {
                $lchtml .='<option value="'.$value->workshop_id.'">'.$value->workshop_name.'</option>';
            }
        }
//         if($WtypeResult > 0){
//            foreach ($WtypeResult as $value) {
//                $lchtml1 .='<option value="'.$value->id.'">'.$value->workshop_type.'</option>';
//            }
//        }
        $data['WorkshopData'] = $lchtml;
//        $data['WtypeResult'] = $lchtml1;
        $data['WorkshopSubtypeData'] = $this->get_workshop_subtype_selectbox($company_id,$workshoptype_id);        
        $data['WorkshopSubregionData'] = $this->get_workshop_subregion_selectbox($company_id,$region_id);
        echo json_encode($data);
    }
    public function get_workshop_subtype_selectbox($company_id,$workshoptype_id=''){
        $lchtml ='<option value="">Please Select</option>';
        if($workshoptype_id !=''){
            $Dataset        = $this->common_model->get_selected_values('workshopsubtype_mst','id,description as sub_type','company_id='.$company_id.' and workshoptype_id='.$workshoptype_id);
            if(count((array)$Dataset)>0){
                foreach ($Dataset as $value) {
                    $lchtml .='<option value="'.$value->id.'">'.$value->sub_type.'</option>';
                }
            }
        }
        return $lchtml;
    }
    public function get_workshop_subregion_selectbox($company_id,$region_id=''){
        $lchtml='<option value="">Please Select</option>';
        if($region_id !=''){
            $Dataset        = $this->common_model->get_selected_values('workshopsubregion_mst','id,description as sub_region','company_id='.$company_id.' and region_id='.$region_id);
            if(count((array)$Dataset)>0){
                foreach ($Dataset as $value) {
                    $lchtml .='<option value="'.$value->id.'">'.$value->sub_region.'</option>';
                }
            }
        }
        return $lchtml;
    }
    public function ajax_workshopwise_data() {
        $workshop_id = $this->input->post('workshop_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $data['UserData'] = $this->trainee_reports_model->get_WorkshopRegisterdusers($workshop_id, $company_id);

        echo json_encode($data);
    }

    public function ajax_chart($TotalChart) {
        $successFlag = 0;
        $Table = '';
        $error = '';
        $lcHtml = '';
        $Label = [];
        $dataset = [];
        $ExportRights = $this->acces_management;
        
        $workshoptype_id = $this->input->post('workshoptype_id', TRUE);
        $workshop_id = $this->input->post('workshop_id', TRUE);
        if ($this->mw_session['login_type'] != 3) {
            $user_id = $this->input->post('trainee_id', TRUE);
        } else {
            $user_id = $this->mw_session['user_id'];
        }
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $this->trainee_reports_model->SynchTraineeData($company_id);
        $workshop_session = $this->input->post('workshop_session', TRUE);
        if ($workshop_id != '') {
            $ChartData = $this->trainee_reports_model->get_PrepostAccuracy($workshop_id, $user_id, $workshop_session);
            if (count((array)$ChartData) > 0) {
                foreach ($ChartData as $value) {
                    $dataset[] = $value->accuracy;
                    $Label[] = $value->topic . ($value->subtopic != 'No sub-Topic' ? '-' . $value->subtopic : '');
                }
            }
            $username = $this->common_model->get_value('device_users', 'concat(firstname," ",lastname) as username,email', 'user_id=' . $user_id);
            $data['dataset'] = json_encode($dataset, JSON_NUMERIC_CHECK);
            $data['label'] = json_encode($Label);
            $data['totallabel'] = count((array)$Label);
            $data['user'] = json_encode($username->username);
            $data['user_id'] = json_encode($user_id);
            $data['email'] = json_encode($username->email);
            $data['TotalChart'] = $TotalChart;
            $WorkshopRow = $this->common_model->get_value('workshop', 'workshop_name', 'id=' . $workshop_id);
            $data['Workshop_name'] = $WorkshopRow->workshop_name . '(' . ($workshop_session == "" ? 'All' : $workshop_session) . ' Session )';
            $lcHtml = $this->load->view('trainee_accuracy_report/show_report', $data, true);

            $islive_workshop = $this->trainee_reports_model->isWorkshopLive($workshop_id);
            if ($islive_workshop) {
                $PrepostAccSet = $this->trainee_reports_model->getLivePrePostData($workshop_id, $user_id);
            } else {
                $PrepostAccSet = $this->trainee_reports_model->getPrePostData($workshop_id, $user_id);
            }
            $RankData = $this->trainee_reports_model->get_Traineewise_Rank($workshop_id, $user_id, $islive_workshop);
            if (count((array)$RankData) > 0) {
                $Rank = $RankData[0]->rank;
            } else {
                $Rank = "-";
            }
            if (count((array)$PrepostAccSet) > 0) {
                if ($workshop_session == 'PRE') {
                    $Overallaccuracy = $PrepostAccSet[0]->pre_average;
                } else {
                    $Overallaccuracy = $PrepostAccSet[0]->post_average;
                }
                $Table = '<tr id="datatr_' . $TotalChart . '">
                <td> Workshop :' . $data['Workshop_name'] . ',Trainee :' . $PrepostAccSet[0]->trainee_name . '</td>
                <td>' . $Overallaccuracy . '</td>                            
                <td>' . $Rank . '</td>
                <td style="width: 15%;">'; 
                 if($ExportRights->allow_export) {
                               $Table .= ' <a  href="'.  base_url().'trainee_accuracy_report/export_workshop/'.$company_id.'/'.$workshop_id.'/'.$user_id.'/'.$workshop_session.'" class="btn btn-xs green">
                                   <i class="fa fa-file-excel-o"></i> Export
                               </a>';
                           }
                '</td></tr>';
            } else {
                $Table = '<tr id="datatr_' . $TotalChart . '">
                <td> Workshop :' . $data['Workshop_name'] . '</td>
                <td>Not Played</td>                            
                <td>-</td>                            
                </tr>';
            }
        } else {
            $error = "Please Select Workshop..!";
        }
        $Rdata['HtmlData'] = $lcHtml;
        $Rdata['OverallTable'] = $Table;
        $Rdata['Error'] = $error;
        echo json_encode($Rdata);
    }
    public function export_workshop($company_id,$workshop_id,$trainee_id,$workshop_session=''){
        $ExportRights = $this->acces_management;
        if(!$ExportRights->allow_export) {
             redirect('trainee_accuracy_report');
        }
        $RightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
            if(!$this->mw_session['superaccess']){
                $Login_id  =$this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type','userid='.$Login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
            }
        }
        $trainer_id=0;
        $WorkshopLive =$this->trainee_reports_model->WorkshopLive($workshop_id,$workshop_session);
        $TraineeData = $this->trainee_reports_model->get_traineeAccuracy($RightsFlag,$trainee_id, $trainer_id, $workshop_id, $workshop_session,$WorkshopLive);
        
            $Workshop_rowset = $this->common_model->get_value('workshop', "workshop_name ", 'id=' . $workshop_id);
            $workshop_name = $Workshop_rowset->workshop_name;
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('A1', "Workshop Name :".$workshop_name)
                    ->setCellValue('A2', "Workshop Session :".$workshop_session)
//                    ->setCellValue('A3', "Trainer :")
                    ->setCellValue('A4', "TRAINEE ID")
                    ->setCellValue('B4', "TRAINEE NAME")
                    ->setCellValue('C4', "TRAINEE REGION")
                    ->setCellValue('D4', "TOTAL PLAYED")
                    ->setCellValue('E4', "CORRECT")
                    ->setCellValue('F4', "WRONG")
                    ->setCellValue('G4', "RESULT")
                    ->setCellValue('H4', "RANK")
                    ->setCellValue('I4', "STATUS");
            $styleArray = array(
                'font' => array(
//                'color' => array('rgb' => '990000'),
            ));
            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
            ));
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleArray_header);
            
            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
        $i = 4;
        if (count((array)$TraineeData) > 0) {
            foreach ($TraineeData as $wksh) {
                $i++;
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$i", $wksh->trainee_id)
                        ->setCellValue("B$i", $wksh->trainee_name)
                        ->setCellValue("C$i", $wksh->trainee_region)
                        ->setCellValue("D$i", $wksh->played_questions)
                        ->setCellValue("E$i",$wksh->correct)
                        ->setCellValue("F$i",($wksh->played_questions-$wksh->correct))
                        ->setCellValue("G$i", ($wksh->accuracy=="" ? "Not Played" : $wksh->accuracy."%"))
                        ->setCellValue("H$i", $wksh->rank)
                        ->setCellValue("I$i", ($wksh->played_questions >0 ? $wksh->status :'Not Attended'));
                $objPHPExcel->getActiveSheet()->getStyle("A$i:I$i")->applyFromArray($styleArray_body);
            }
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Trainee Accuracy Reports.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
    }
    public function getTraineeData(){
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }        
        $trainee_region_id = $this->input->post('trainee_region_id', TRUE);
        $lcTrainee_html='<option value="">Select Trainee</option>';
        if($trainee_region_id !='0'){
            $TraineeData = $this->common_model->get_selected_values('device_users','user_id,concat(firstname," ",lastname) as traineename','company_id='.$company_id.' and region_id='.$trainee_region_id);
        }        
        else{
            $TraineeData = $this->common_model->get_selected_values('device_users','user_id,concat(firstname," ",lastname) as traineename','company_id='.$company_id);
        }
        if(count((array)$TraineeData)>0){
            foreach ($TraineeData as $value) {
                $lcTrainee_html .='<option value="'.$value->user_id.'">'.$value->traineename.'</option>';
            }
        }
        $data['TraineeData'] = $lcTrainee_html;
        echo json_encode($data);
    }
}
