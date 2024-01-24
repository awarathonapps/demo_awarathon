<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainer_wise_summary_report extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('trainer_wise_summary_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('workshop_report_model');
        }

    public function index() {
        $data['module_id'] = '24.5';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
            $trainer_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
            if ($RightsFlag) {
                $data['TrainerData'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $Company_id . '"');
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id, 'region_name');
                $data['WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 and company_id=' . $Company_id);
            } else {
                $data['TrainerData'] = $this->common_model->getUserRightsList($Company_id, $trainer_id);
                $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
            }
            if ($WRightsFlag != 1) {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
            }
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('trainer_wise_summary_report/index', $data);
    }

    public function ajax_companywise_data() {
        $RightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
            $data['TrainerData'] = $this->common_model->get_selected_values('company_users', 'userid,concat(first_name," ",last_name) as trainername', 'company_id=' . $company_id, 'first_name');
        } else {
            $company_id = $this->mw_session['company_id'];
            $trainer_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
            }
            if ($RightsFlag) {
                $data['TrainerData'] = $this->common_model->get_selected_values('company_users', 'userid,CONCAT(first_name, " " ,last_name) as fullname', 'status="1" AND login_type="1" AND company_id="' . $company_id . '"', 'first_name');
            } else {
                $data['TrainerData'] = $this->common_model->getUserRightsList($company_id, $trainer_id);
            }
        }
        echo json_encode($data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('CONCAT(cu.first_name," ", cu.last_name)');
        //'cm.company_name',
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
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
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
            echo json_encode($output);
            exit;
        }
        $trainer_id = ($this->input->get('trainer_id') ? $this->input->get('trainer_id') : '');
        if ($trainer_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.trainer_id  = " . $trainer_id;
            } else {
                $dtWhere .= " WHERE ar.trainer_id  = " . $trainer_id;
            }
        }
        $region_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '0');
        if ($region_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.region  = " . $region_id;
            } else {
                $dtWhere .= " WHERE w.region  = " . $region_id;
            }
        }
        $wsubrgion_id = ($this->input->get('subregion_id') ? $this->input->get('subregion_id') : '');
        if ($wsubrgion_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
            } else {
                $dtWhere .= " WHERE w.workshopsubregion_id = " . $wsubrgion_id;
            }
        }
        $workshop_type = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '0');
        if ($workshop_type != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshop_type  = " . $workshop_type;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $workshop_type;
            }
        }
        $workshop_subtype = ($this->input->get('workshopsubtype_id') ? $this->input->get('workshopsubtype_id') : '');
        if ($workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshop_subtype;
            }
        }
        $dthaving = '';
        $range_value = ($this->input->get('range_id') ? $this->input->get('range_id') : '');

        if ($range_value != '') {
            $range_id = explode("-", $range_value);
            if (count((array)$range_id) > 0) {
                $from_range = $range_id[0];
                $to_range = $range_id[1];
                if ($from_range != "" && $to_range != "") {
                    $dthaving = " HAVING (format(sum(ar.is_correct)*100/count(ar.id),2)) between " . $from_range . " and " . $to_range;
                }
            }
        }

        $this->session->set_userdata(array('exportWhere' => $dtWhere, 'exportHaving' => $dthaving, 'RightsFlag' => $RightsFlag, 'WRightsFlag' => $WRightsFlag, 'Company_id' => $cmp_id));

        $DTRenderArray = $this->workshop_report_model->TrainerSummaryLoadDataTable($dtWhere, $dthaving, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);


        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('trainername', 'TOTALworkshop', 'TOTALtrainee', 'TOTALtopic', 'TOTALsubtopic', 'played_que', 'correct', 'wrong', 'result');
        $site_url = base_url();
        $acces_management = $this->acces_management;

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
        $Company_name = "";
        $Company_id = $this->session->userdata('Company_id');
        if ($Company_id != "") {
            if ($this->mw_session['company_id'] == "") {
                $Rowset = $this->common_model->get_value('company', 'company_name', 'id=' . $Company_id);
                $Company_name = 'Company Name : ' . $Rowset->company_name;
            }
            $this->load->library('PHPExcel');
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "$Company_name")
                    ->setCellValue('A3', "Trainer Name")
                    ->setCellValue('B3', "No of Workshop")
                    ->setCellValue('C3', "No of Trainees")
                    ->setCellValue('D3', "No of Topics")
                    ->setCellValue('E3', "No of Sub-topics")
                    ->setCellValue('F3', "Questions Played")
                    ->setCellValue('G3', "Correct")
                    ->setCellValue('H3', "Wrong")
                    ->setCellValue('I3', "Result");


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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($styleArray_header);


            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $dtWhere = $this->session->userdata('exportWhere');
            $dthaving = $this->session->userdata('exportHaving');
            $RightsFlag = $this->session->userdata('RightsFlag');
            $WRightsFlag = $this->session->userdata('WRightsFlag');
            $i = 3;

            $Data_list = $this->workshop_report_model->TrainerSummaryExportToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag);


            foreach ($Data_list as $value) {
                $i++;
                $objPHPExcel->getActiveSheet()
//                    ->setCellValue("A$i", $value->company_name)
                        ->setCellValue("A$i", $value->trainername)
                        ->setCellValue("B$i", $value->TOTALworkshop)
                        ->setCellValue("C$i", $value->TOTALtrainee)
                        ->setCellValue("D$i", $value->TOTALtopic)
                        ->setCellValue("E$i", $value->TOTALsubtopic)
                        ->setCellValue("F$i", $value->played_que)
                        ->setCellValue("G$i", $value->correct)
                        ->setCellValue("H$i", $value->wrong)
                        ->setCellValue("I$i", $value->result);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:I$i")->applyFromArray($styleArray_body);
            }



            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Trainer_wise Summary Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        } else {
            redirect('trainer_wise_summary_report');
        }
    }

}
