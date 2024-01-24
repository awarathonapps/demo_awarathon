<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Question_wise_report extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('question_wise_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('workshop_report_model');
        }

    public function index() {
        $data['module_id'] = '24.6';
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
            if ($WRightsFlag) {
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id, 'region_name');
                $data['WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 and company_id=' . $Company_id);
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
            }
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('question_wise_report/index', $data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('a.question_id', 'wrk.question_title', 'qs.title', 'a.workshop_name', 'wt.workshop_type', 'r.region_name', 'wrk.option_a', 'wrk.option_b', 'wrk.option_c', 'wrk.option_d');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere2 = $DTRenderArray['dtWhere'];

        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        $dtHaving = '';
        $RightsFlag = 1;
        $WRightsFlag = 1;
        $login_id = $this->mw_session['user_id'];
        $dtWhere = '';
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $login_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND wq.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE wq.company_id  = " . $cmp_id;
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
//      $from_range= ($this->input->get('from_range') ? $this->input->get('from_range') :'');
//      $to_range= ($this->input->get('from_range') ? $this->input->get('from_range') :'');
        $range = ($this->input->get('result_range') ? $this->input->get('result_range') : '');

        if ($range != '') {
            $result_range = explode("-", $range);
            if (count((array)$result_range) > 0) {
                $from_range = $result_range[0];
                $to_range = $result_range[1];
                if ($from_range != "" && $to_range != "") {
                    $dtHaving .= " having result between " . $from_range . " AND " . $to_range;
                }
            }
        }
        if (!$WRightsFlag) {
            $dtWhere .= " AND wq.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $dtWhere .= " AND (wq.trainer_id = $login_id OR wq.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }

        $this->session->set_userdata(array('exportWhere' => $dtWhere, 'exportHaving' => $dtHaving, 'Company_id' => $cmp_id));

        $DTRenderArray = $this->workshop_report_model->QuestionWiseLoadDataTable($dtWhere, $dtWhere2, $dtOrder, $dtLimit, $dtHaving);
//        
//        echo "<pre>";
//        print_r($DTRenderArray['ResultSet']);exit;
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('question_id', 'question_title', 'questionset', 'workshop_name', 'workshop_type','workshop_subtype','workshop_subregion','region_name', 'correct_answer', 'no_of_trainee_played', 'result');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == 'result') {
                    $row[] = $dtRow['result'] . '%';
                } else if ($dtDisplayColumns[$i] != ' ') {
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
                    ->setCellValue('A3', "QUESTION ID")
                    ->setCellValue('B3', "QUESTION")
                    ->setCellValue('C3', "QUESTION SET")
                    ->setCellValue('D3', "WORKSHOP NAME")
                    ->setCellValue('E3', "WORKSHOP TYPE")
                    ->setCellValue('F3', "WORKSHOP SUB-TYPE")
                    ->setCellValue('G3', "WORKSHOP REGION")
                    ->setCellValue('H3', "WORKSHOP SUB-REGION")
                    ->setCellValue('I3', "CORRECT ANSWERS")
                    ->setCellValue('J3', "NO OF TRAINEE PLAYED")
                    ->setCellValue('K3', "RESULT");
            $styleArray = array(
                'font' => array(
//                    'bold' => true
            ));

            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
            ));
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);


            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($styleArray_header);


            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $exportWhere = $this->session->userdata('exportWhere');
            $exportHaving = $this->session->userdata('exportHaving');
            $i = 3;
            $j = 0;
            $Data_list = $this->workshop_report_model->QuestionWiseExportToExcel($exportWhere, $exportHaving);

            foreach ($Data_list as $value) {
                $i++;
                $j++;
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$i", $value->question_id)
                        ->setCellValue("B$i", $value->question_title)
                        ->setCellValue("C$i", $value->questionset)
                        ->setCellValue("D$i", $value->workshop_name)
                        ->setCellValue("E$i", $value->workshop_type)
                        ->setCellValue("F$i", $value->workshop_subtype)
                        ->setCellValue("G$i", $value->region_name)
                        ->setCellValue("H$i", $value->workshop_subregion)
                        ->setCellValue("I$i", $value->correct_answer)
                        ->setCellValue("J$i", $value->no_of_trainee_played)
                        ->setCellValue("K$i", $value->result . '%');
                $objPHPExcel->getActiveSheet()->getStyle("A$i:K$i")->applyFromArray($styleArray_body);
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="QuestionWise_Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        } else {
            redirect('question_wise_report');
        }
    }

}
