<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainee_comparison_report extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('trainee_comparison_report');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('trainee_reports_model');
    }

    public function index() {
        $data['module_id'] = '26.2';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Trainee_id = '';
        $data['Company_id'] = $this->mw_session['company_id'];
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($data['Company_id'] == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
            if ($this->mw_session['login_type'] == 3) {
                $Trainee_id = $this->mw_session['user_id'];
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['company_id'],1);
                $data['WorkshopResultSet'] = $this->common_model->getUserWorkshopList($data['Company_id'], $Trainee_id);
                $data['RegionData'] = $this->common_model->getUserRegionList($data['Company_id'],1);
            }else{
                $Login_id  =$this->mw_session['user_id'];
                if(!$this->mw_session['superaccess']){
                    $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                    $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
                }
                if (!$WRightsFlag) {
                    $this->common_model->SyncWorkshopRights($Login_id,0);
                }
                $data['WorkshopResultSet'] = $this->common_model->getTrainerWorkshop($data['Company_id'],$WRightsFlag);
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($data['Company_id'],$WRightsFlag);
                $data['RegionData'] = $this->common_model->getUserRegionList($data['Company_id'],$WRightsFlag);
            }
        }
        $data['TraineeRegionData'] = $this->trainee_reports_model->get_TraineeRegionData($data['Company_id']);
        $data['Trainee_id'] = $Trainee_id;
        $data['login_type'] = $this->mw_session['login_type'];
        $this->load->view('trainee_comparison_report/index', $data);
    }

    public function ajax_traineeWiseData() {
        $TraineeTable = '';
        $error = '';
        $workshop_id = $this->input->post('workshop_id', TRUE);
        $trainee_id = $this->input->post('trainee_id', TRUE);
        $RowId = $this->input->post('RowId', TRUE);
        if ($workshop_id != '') {
            if ($this->mw_session['login_type'] == 3) {
                $trainee_id = $this->mw_session['user_id'];
            }
            $islive_workshop = $this->trainee_reports_model->isWorkshopLive($workshop_id);
            if ($islive_workshop) {
                $TraineeCEData = $this->trainee_reports_model->getLivePrePostData($workshop_id, $trainee_id);
            } else {
                $TraineeCEData = $this->trainee_reports_model->getPrePostData($workshop_id, $trainee_id);
            }
            if ($trainee_id != "") {
                $RankData = $this->trainee_reports_model->get_Traineewise_Rank($workshop_id, $trainee_id,$islive_workshop);
                if(count((array)$RankData)>0){
                    $Rank = $RankData[0]->rank;
                }else{
                    $Rank = "-";
                }
            }
            $WorkshopData = $this->common_model->get_value('workshop', 'workshop_name', 'id=' . $workshop_id);
            $TraineeTable = '<div id="childdiv_' . $RowId . '" class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Workshop : '.$WorkshopData->workshop_name.'</span>
                                        </div>
                                        <div class="tools">
                                            <a href="javascript:void(0)" class="remove" onclick="remove_workshop('.$RowId.');"> </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">'
                                    . '<table class="table table-hover table-light scroll" id="Traineetable" width="400px">
                                        <thead >
                                        <tr class="uppercase" >                                            
                                            <th>Trainee Name</th>                        
                                            <th>Pre</th>
                                            <th>Post</th>
                                            <th>C.E</th>
                                            <th width="12%">RESPONSE TIME</th>
                                            <th>Rank</th>
                                        </tr></thead><tbody>';
            if (count((array)$TraineeCEData) > 0) {
                foreach ($TraineeCEData as $value) {
                    $ceTable = $value->ce . '%';
                    $pre_average = $value->pre_average;
                    if ($pre_average == 'Not Played') {
                        $ceTable = "Not Played";
                    }
                    $post_average = $value->post_average;
                    if ($post_average == 'Not Played') {
                        $ceTable = "Not Played";
                    }
                    if ($trainee_id == "") {
                        $Rank = $value->rank;
                    }
                    $TraineeTable .='<tr class="datatr">
                                <td>' . $value->trainee_name . '</td>
                                <td>' . $pre_average . '</td>
                                <td>' . $post_average . '</td>
                                <td>' . $ceTable . '</td>    
                                <td>' . $value->response_time . '</td>      
                                <td>' . $Rank . '</td>
                                </tr>';
                }
            } else {
                $TraineeTable .='<tr class="datatr"><td colspan="4">No Data found...</td></tr>';
            }

            $TraineeTable .='</tbody></table></div></div></div>';
        } else {
            $error = "Please Select Company,Workshop";
        }
        $data['TraineeTable'] = $TraineeTable;
        $data['Error'] = $error;

        echo json_encode($data);
    }

    public function ComparisonWorkshopTable($Counter) {
        $WTable = '';
        $error = '';
        $ExportRights = $this->acces_management;
        $workshop_id = $this->input->post('workshop_id', TRUE);
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $Trainee_id = $this->input->post('trainee_id', TRUE);        
        if ($workshop_id != '' && $company_id != '') {
            $this->trainee_reports_model->SynchTraineeData($company_id);
            $islive_workshop = $this->trainee_reports_model->isWorkshopLive($workshop_id);
            if ($islive_workshop) {
                $WorkshopPrePostData = $this->trainee_reports_model->getLivePrePostWorkshopwise($workshop_id);
            } else {
                $WorkshopPrePostData = $this->trainee_reports_model->getPrePostWorkshopwise($workshop_id);
            }
            
            if (count((array)$WorkshopPrePostData) > 0 && $WorkshopPrePostData->workshop_name !="") {
                $CE = $WorkshopPrePostData->post_average - $WorkshopPrePostData->pre_average . '%';
                $Pre_avg = $WorkshopPrePostData->pre_average;
                $Post_avg = $WorkshopPrePostData->post_average;
                if ($Pre_avg == 0) {
                    $Pre_avg = "Not Played";
                    $CE = "Not Played";
                } else {
                    $Pre_avg .="%";
                }
                if ($Post_avg == 0) {
                    $Post_avg = "Not Played";
                    $CE = "Not Played";
                } else {
                    $Post_avg .="%";
                }
                $WTable .='<tr id="datatr_' . $Counter . '" class="datatr trClickeble">
                <td style="width: 33%;" onclick="traineeTableData(' . $workshop_id . ',' . $Counter . ','.$Trainee_id.')">' . $WorkshopPrePostData->workshop_name . '</td>
                <td style="width: 22%;">' . $Pre_avg . '</td>
                <td style="width: 20%;">' . $Post_avg . '</td>
                <td style="width: 20%;">' . $CE . '</td> 
                <td style="width: 30%;">';
                if($ExportRights->allow_export) {
                               $WTable .= ' <a  href="'.  base_url().'trainee_comparison_report/export_workshop/'.$company_id.'/'.$workshop_id.'/'.$Trainee_id.'" class="btn btn-xs green">
                                   <i class="fa fa-file-excel-o"></i> Export
                               </a>';
                           }
//                 $WTable .= '<td style="width: 30%;"><button id="button-filter"  class="btn btn-sm btn-small btn-danger" type="button" onclick="RemoveChart(' . $Counter . ');">X</button></td>
//                </tr>';
                  $WTable .= '<a  href="javascript:void(0)" onclick="RemoveChart(' . $Counter . ');" class="btn btn-xs red">
                                    <i class="fa fa-remove"></i> Remove
                                </a>
                  <a style="width:200px;float:right;text-decoration:none;display: block;">&nbsp;</a>
                            </td>
                        </tr>';
            }
        } else {
            $error = "Please Select Company,Workshop";
        }
        $data['ChartTable'] = $WTable;
        $data['Error'] = $error;

        echo json_encode($data);
    }
    public function export_workshop($company_id,$workshop_id,$trainee_id=""){
        if($company_id=="" || $workshop_id=="" ){
             redirect('trainee_comparison_report');
        }
        $ExportRights = $this->acces_management;
        if(!$ExportRights->allow_export) {
             redirect('trainee_comparison_report');
        }
       
        if ($workshop_id != '') {
            if ($this->mw_session['login_type'] == 3) {
                $trainee_id = $this->mw_session['user_id'];
            }
            $islive_workshop = $this->trainee_reports_model->isWorkshopLive($workshop_id);
            if ($islive_workshop) {
                $TraineeCEData = $this->trainee_reports_model->getLivePrePostData($workshop_id, $trainee_id);
            } else {
                $TraineeCEData = $this->trainee_reports_model->getPrePostData($workshop_id, $trainee_id);
            }
            if ($trainee_id != "") {
                $RankData = $this->trainee_reports_model->get_Traineewise_Rank($workshop_id, $trainee_id,$islive_workshop);
                if(count((array)$RankData)>0){
                    $Rank = $RankData[0]->rank;
                }else{
                    $Rank = "-";
                }
            }
            $Workshop_rowset = $this->common_model->get_value('workshop', "workshop_name ", 'id=' . $workshop_id);
            $workshop_name = $Workshop_rowset->workshop_name;
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('A2', "Workshop Name :".$workshop_name)
                    ->setCellValue('A3', "Trainee ID")
                    ->setCellValue('B3', "Trainee Name")
                    ->setCellValue('C3', "Trainee Region")
                    ->setCellValue('D3', "PRE")
                    ->setCellValue('E3', "POST")
                    ->setCellValue('F3', "C.E")
                    ->setCellValue('G3', "RESPONSE TIME")
                    ->setCellValue('H3', "RANK");
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

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($styleArray_header);
            
            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $i = 3;
            foreach ($TraineeCEData as $compr) {
                $i++;
                        $comp_pre_average_accuracy = ($compr->pre_average == "NP" ? "Not Played" : $compr->pre_average);
                        $comp_post_average_accuracy = ($compr->post_average == "NP" ? "Not Played" : $compr->post_average);
                        $comp_ce = ($compr->pre_average == 'Not Played' || $compr->post_average == 'Not Played' ? "Not Played" : $compr->ce . "%");
                        $comp_rank = $compr->rank;
                        
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$i", $compr->trainee_id)
                        ->setCellValue("B$i", $compr->trainee_name)
                        ->setCellValue("C$i", $compr->trainee_region)
                        ->setCellValue("D$i", $comp_pre_average_accuracy)
                        ->setCellValue("E$i", $comp_post_average_accuracy)
                        ->setCellValue("F$i", $comp_ce)
                        ->setCellValue("G$i", $compr->response_time)
                        ->setCellValue("H$i", $comp_rank);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:H$i")->applyFromArray($styleArray_body);
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Trainee Comparison Reports.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
        }
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
