<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Workshop_wise_report extends MY_Controller {

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
        $data['module_id'] = '24.3';
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
                $data['WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $Company_id . '"', 'workshop_type');
                $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id, 'id desc');
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
                $data['WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);
                $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
            }
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('workshop_wise_report/index', $data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('w.id', 'r.region_name', 'wm.workshop_type', 'w.workshop_name', 'w.id','wsr.description','wst.description');

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
        $workshop_id = ($this->input->get('workshop_id') ? $this->input->get('workshop_id') : '');
        if ($workshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.workshop_id  = " . $workshop_id;
            } else {
                $dtWhere .= " WHERE ar.workshop_id  = " . $workshop_id;
            }
        }
        $workshoptype_id = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '0');
        if ($workshoptype_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND wm.id  = " . $workshoptype_id;
            } else {
                $dtWhere .= " WHERE wm.id  = " . $workshoptype_id;
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
        $region_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '0');
        if ($region_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND r.id  = " . $region_id;
            } else {
                $dtWhere .= " WHERE r.id  = " . $region_id;
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

        $DTRenderArray = $this->Workshop_report_model->WorkshopWiseLoadDataTable($dtWhere, $dthaving, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);

        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('region_name', 'workshop_subregion', 'workshop_type', 'workshop_subtype', 'workshop_name', 'questionset', 'played_que', 'correct', 'wrong', 'result');
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
                    ->setCellValue('A3', "Workshop Region")
                    ->setCellValue('B3', "Workshop Sub-region")
                    ->setCellValue('C3', "Workshop Type")
                    ->setCellValue('D3', "Workshop Sub-type")
                    ->setCellValue('E3', "Workshop name")
                    ->setCellValue('F3', "No of Question Set")
                    ->setCellValue('G3', "Questions Played")
                    ->setCellValue('H3', "Correct")
                    ->setCellValue('I3', "Wrong")
                    ->setCellValue('J3', "Result");
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

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($styleArray_header);


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

            $Data_list = $this->Workshop_report_model->WorkshopWiseExportToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag);


            foreach ($Data_list as $value) {
                $i++;
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$i", $value->region_name)
                        ->setCellValue("B$i", $value->workshop_subregion)
                        ->setCellValue("C$i", $value->workshop_type)
                        ->setCellValue("D$i", $value->workshop_subtype)
                        ->setCellValue("E$i", $value->workshop_name)
                        ->setCellValue("F$i", $value->questionset)
                        ->setCellValue("G$i", $value->played_que)
                        ->setCellValue("H$i", $value->correct)
                        ->setCellValue("I$i", $value->wrong)
                        ->setCellValue("J$i", $value->result . '%');

                $objPHPExcel->getActiveSheet()->getStyle("A$i:J$i")->applyFromArray($styleArray_body);
            }


            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Workshop-wise Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        } else {
            redirect('workshop_wise_report');
        }
    }

}
