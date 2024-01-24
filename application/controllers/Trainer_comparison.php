<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Trainer_comparison extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('trainer_comparison');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('trainer_comparison_model');
        }

    public function index($trainer_id = '') {
        $data['module_id'] = '25.2';
        $data['username'] = $this->mw_session['username'];
        $data['trainee_name'] = $this->mw_session['first_name'] . " " . $this->mw_session['last_name'];
        $data['company_id'] = $this->mw_session['company_id'];
        $data['user_id'] = $this->mw_session['user_id'];
        $data['acces_management'] = $this->acces_management;
        $company_id='';
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($data['company_id'] == "") {
            $data['company_array'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
            if ($trainer_id != '') {
                $Rowset = $this->common_model->get_value('company_users', 'company_id', 'userid=' . $trainer_id);
                $company_id = $Rowset->company_id;
                $data['WtypeResult'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
                $data['WorkshopResultSet'] = $this->common_model->getTrainerWorkshop($company_id,1);
            }
        } else {
            $data['company_array'] = array();
            $Login_id = $this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
            $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }
            $company_id = $data['company_id'];
            if ($RightsFlag) {
                $data['TrainerResult'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"');
            } else {
                $this->common_model->SyncTrainerRights($Login_id);
                $data['TrainerResult'] = $this->common_model->getUserRightsList($company_id, $Login_id);
            }
            if ($WRightsFlag) {
                $data['WtypeResult'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
                $data['WorkshopResultSet'] = $this->common_model->getTrainerWorkshop($company_id,1);
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $company_id);
            } else {
                $this->common_model->SyncWorkshopRights($Login_id,0);
                $data['WtypeResult'] = $this->common_model->getWTypeRightsList($company_id);
                $data['WorkshopResultSet'] = $this->common_model->getTrainerWorkshop($company_id,0);
                $data['RegionData'] = $this->common_model->getUserRegionList($company_id);
            }
        }
        $data['trainer_id'] = $trainer_id;
        $data['Supcompany_id'] = $company_id;
        $this->load->view('trainer_comparison/index', $data);
    }

    public function ajax_company_trainer_type() {
        $RightsFlag=1;
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
            $Trainer_id = '';
        } else {
            $company_id = $this->mw_session['company_id'];
            $Trainer_id = $this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
            $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Trainer_id);
            $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
            $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
        }
        }
        $lcWhere = 'status=1 AND  company_id=' . $company_id;
        if ($RightsFlag) {
            $data['user_array'] = $this->common_model->get_selected_values('company_users', 'company_id,userid,CONCAT(first_name, " " ,last_name) as fullname', $lcWhere, "fullname");
        } else {
            $data['user_array'] = $this->common_model->getUserRightsList($company_id, $Trainer_id);
        }
        if ($WRightsFlag) {
            $data['wksh_type_array'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $company_id . '"', 'workshop_type');
        } else {
            $data['wksh_type_array'] = $this->common_model->getWTypeRightsList($company_id);
        }
        echo json_encode($data);
    }

    public function ajax_fetch_workshop() {
        $WRightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $Login_id =$this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
                $Rowset = $this->common_model->get_value('company_users', 'company_id,workshoprights_type', 'userid=' . $Login_id);
            //$RightsFlag=($Rowset->userrights_type==1 ? 1:0);
                $WRightsFlag=($Rowset->workshoprights_type==1 ? 1:0);
            }
        }
        $trainer_id = $this->input->post('user_id', TRUE);
        $workshop_type_id = $this->input->post('workshop_type_id', TRUE);
        $data['workshop_array'] = $this->common_model->getTrainerWorkshop($company_id,$WRightsFlag, $trainer_id,'0', $workshop_type_id);
        
        echo json_encode($data);
    }
    public function load_workshop_table($cnt) {
        //WORKSHOP ACCURACY TABLE
//         $cnt = $this->input->post('cnt', TRUE);
        $RightsFlag=1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
            
        } else {
            $company_id = $this->mw_session['company_id'];
            $Login_id  =$this->mw_session['user_id'];
            if(!$this->mw_session['superaccess']){
            $Rowset = $this->common_model->get_value('company_users','company_id,userrights_type','userid='.$Login_id);
            $RightsFlag=($Rowset->userrights_type==1 ? 1:0);
        }
        }
        $this->load->model('trainee_reports_model');
        //$this->trainee_reports_model->SynchTraineeData($company_id);
        $trainer_id = $this->input->post('user_id', TRUE);
        $workshop_type_id = ($this->input->post('workshop_type_id', TRUE) !='' ? $this->input->post('workshop_type_id', TRUE) : 0);
        $Workshop_id = $this->input->post('workshop_id', TRUE);
        $PlayedStatus = $this->trainer_comparison_model->isWorkshopPlayed($Workshop_id);
        $islive_workshop = $this->trainee_reports_model->isWorkshopLive($Workshop_id);
        $workshop_statistics = $this->trainer_comparison_model->workshop_statistics($company_id, $trainer_id, $Workshop_id, $workshop_type_id,
            $PlayedStatus['PreFlag'],$PlayedStatus['PostFlag'],$islive_workshop);
        $html = '';
        $comparison_html = '';
        $ExportRights = $this->acces_management;
        $trainer_name ="All Trainer";
        if (count((array)$workshop_statistics) > 0) {
            foreach ($workshop_statistics as $wksh) {
                $company_id = $wksh->company_id;
                //$workshop_id = $wksh->workshop_id;
                $workshop_name = $wksh->workshop_name;
                if($trainer_id !="0"){
                $trainer_name = $wksh->trainer_name;
                }                                
                if(!$PlayedStatus['PreFlag']  || $wksh->pre_accuracy==""){
                    $pre_average_accuracy = 'Not Played';
                    $ce = 'Not Played';
                }else{
                    $pre_average_accuracy = $wksh->pre_accuracy . '%';
                }
                $ce = $wksh->ce . '%';
                if(!$PlayedStatus['PostFlag'] || $wksh->post_accuracy==""){
                    $post_average_accuracy = 'Not Played';
                    $ce = 'Not Played';
                }else{
                    $post_average_accuracy = $wksh->post_accuracy . '%';
                }
                //TRAINEE COMPARISON TABLE
                $trainee_comparison = $this->trainer_comparison_model->trainee_comparison($company_id,$islive_workshop, $trainer_id, $Workshop_id, $workshop_type_id);
                if (count((array)$trainee_comparison) > 0) {
                    $comparison_html .='<div id="tdata'.$cnt.'" class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">' . $workshop_name . '</span>
                                            <h5>' . $trainer_name . '</h5>
                                        </div>
                                        <div class="tools">
                                            <a href="javascript:void(0)" class="remove" onclick="remove_workshop(' . $cnt . ');"> </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <table class="table table-hover table-light scroll" width="400px">
                                            <thead>
                                                <tr class="uppercase">
                                                    <th>TRAINEE NAME</th>
                                                    <th >PRE</th>
                                                    <th >POST</th>
                                                    <th >C.E</th>
                                                    <th width="12%">RESPONSE TIME</th>
                                                    <th >RANK</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                    foreach ($trainee_comparison as $compr) {
                        $comp_trainee_name = $compr->trainee_name;

                        $comp_pre_average_accuracy = ($compr->pre_average == "NP" ? "Not Played" : $compr->pre_average);
                        $comp_post_average_accuracy = ($compr->post_average == "NP" ? "Not Played" : $compr->post_average);

                        $comp_ce = ($compr->pre_average == 'NP' || $compr->post_average == 'NP' ? "Not Played" : $compr->ce . "%");
                        $comp_rank = $compr->rank;

                        $comparison_html .='<tr>
                                                <td>' . $comp_trainee_name . '</td>
                                                <td>' . $comp_pre_average_accuracy . '</td>
                                                <td>' . $comp_post_average_accuracy . '</td>
                                                <td>' . $comp_ce . '</td>
                                                <td width="12%">' . $compr->response_time . '</td>
                                                <td>' . $comp_rank . '</td>
                                            </tr>';
                    }
                    $comparison_html .= '</tbody></table></div></div></div>';
                }

                $html .= '<tr id="rdata'.$cnt.'">
                            <td style="width: 23.5%;">' . $workshop_name . '</td>
                            <td style="width: 15.5%;">' . $trainer_name . '</td>
                            <td style="width: 11.5%;">' . $pre_average_accuracy . '</td>
                            <td style="width: 11.5%;">' . $post_average_accuracy . '</td>
                            <td style="width: 11.5%;">' . $ce . '</td>
                            <td style="width: 28%;">';
                        if($ExportRights->allow_export) {
                                    $html .= ' <a  href="'.  base_url().'trainer_comparison/export_workshop/'.$company_id.'/'.$Workshop_id.'/'.$trainer_id.'" class="btn btn-xs green">
                                        <i class="fa fa-file-excel-o"></i> Export
                                    </a>';
                                }
                                $html .= '<a  href="javascript:void(0)" onclick="remove_workshop(' .$cnt. ');" class="btn btn-xs red">
                                    <i class="fa fa-remove"></i> Remove
                                </a>
                                <a style="width:200px;float:right;text-decoration:none;display: block;">&nbsp;</a>
                            </td>
                        </tr>';
            }
        }
        $data['wksh_list'] = $html;
        $data['comparison_panels'] = $comparison_html;
        echo json_encode($data);
    }
    public function export_workshop($company_id,$Workshop_id,$trainer_id=""){
        if($company_id=="" || $Workshop_id=="" ){
             redirect('trainer_comparison');
        }
        $ExportRights = $this->acces_management;
        if(!$ExportRights->allow_export) {
             redirect('trainer_comparison');
        }
        $workshop_type_id ='0';
        $PlayedStatus = $this->trainer_comparison_model->isWorkshopPlayed($Workshop_id);
        $islive_workshop = '';//$this->trainee_reports_model->isWorkshopLive($Workshop_id);
        $workshop_statistics = $this->trainer_comparison_model->workshop_statistics($company_id, $trainer_id, $Workshop_id, $workshop_type_id,
            $PlayedStatus['PreFlag'],$PlayedStatus['PostFlag'],$islive_workshop);
        if(count((array)$workshop_statistics)>0){
            $trainee_comparison = $this->trainer_comparison_model->trainee_comparison($company_id,$islive_workshop, $trainer_id, $Workshop_id, $workshop_type_id);
            $tariner_name='All';
            if($trainer_id !="0"){
                $Trainer_rowset = $this->common_model->get_value('company_users', "CONCAT(first_name,' ',last_name) as name ", 'userid=' . $trainer_id);
                $tariner_name = $Trainer_rowset->name;
            }
            $Workshop_rowset = $this->common_model->get_value('workshop', "workshop_name ", 'id=' . $Workshop_id);
            $workshop_name = $Workshop_rowset->workshop_name;
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "Trainer Name :".$tariner_name)
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
            foreach ($trainee_comparison as $compr) {
                $i++;
                        $comp_pre_average_accuracy = ($compr->pre_average == "NP" ? "Not Played" : $compr->pre_average);
                        $comp_post_average_accuracy = ($compr->post_average == "NP" ? "Not Played" : $compr->post_average);
                        $comp_ce = ($compr->pre_average == 'NP' || $compr->post_average == 'NP' ? "Not Played" : $compr->ce . "%");
                        $comp_rank = $compr->rank;
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$i", $compr->trainee_id)
                        ->setCellValue("B$i", $compr->trainee_name)
                        ->setCellValue("C$i", $compr->trainee_region)
                        ->setCellValue("D$i", $comp_pre_average_accuracy)
                        ->setCellValue("E$i",$comp_post_average_accuracy)
                        ->setCellValue("F$i",$comp_ce)
                        ->setCellValue("G$i", $compr->response_time)
                        ->setCellValue("H$i", $comp_rank);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:H$i")->applyFromArray($styleArray_body);
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Trainer Comparison Reports.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
        }
    }

}
