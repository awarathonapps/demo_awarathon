<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trainee_wise_summary_report extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('trainee_wise_summary_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('workshop_report_model');
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
                $data['TraineeData'] = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname," (",email," )") '
                        . 'as traineename', 'status=1  AND company_id=' . $Company_id, 'firstname');
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id);
                $data['WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status=1 and company_id=' . $Company_id);
            } else {
                $this->common_model->SyncWorkshopRights($Login_id, 0);
                $data['TraineeData'] = $this->common_model->getUserTraineeList($Company_id);
                $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
                $data['WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
            }
            $data['DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'company_id=' . $Company_id);
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('trainee_wise_summary_report/index', $data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('ar.user_id', 'concat(du.firstname," ",du.lastname)','dt.description','','','','','','result');
        
        $DTRenderArray = DT_RenderColumns($dtSearchColumns);
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
        
        $user_id = ($this->input->get('user_id') ? $this->input->get('user_id') : '');
        if ($user_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND ar.user_id  = " . $user_id;
            } else {
                $dtWhere .= " WHERE ar.user_id  = " . $user_id;
            }
        }
        $workshop_type = ($this->input->get('workshop_type') ? $this->input->get('workshop_type') : '0');
        if ($workshop_type != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshop_type  = " . $workshop_type;
            } else {
                $dtWhere .= " WHERE w.workshop_type = " . $workshop_type;
            }
        }
        $wrgion_id = ($this->input->get('wregion_id') ? $this->input->get('wregion_id') : '0');
        if ($wrgion_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.region  = " . $wrgion_id;
            } else {
                $dtWhere .= " WHERE w.region = " . $wrgion_id;
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
        $workshop_subtype = ($this->input->get('workshop_subtype') ? $this->input->get('workshop_subtype') : '');
        if ($workshop_subtype != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
            } else {
                $dtWhere .= " WHERE w.workshopsubtype_id = " . $workshop_subtype;
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
        $region_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '0');
        if ($region_id != "0") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND du.region_id  = " . $region_id;
            } else {
                $dtWhere .= " WHERE du.region_id  = " . $region_id;
            }
        }
        $designation_id= ($this->input->get('designation_id') ? $this->input->get('designation_id') :'0');
        if($designation_id !="0")
        {
        if($dtWhere<>''){
                $dtWhere .= " AND du.designation_id  = ".$designation_id; 
           }else{
                $dtWhere .= " WHERE du.designation_id = ".$designation_id; 
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
        
        $this->session->set_userdata(array('exportWhere' => $dtWhere, 'exportHaving' => $dthaving, 'RightsFlag' => $RightsFlag, 'WRightsFlag' => $WRightsFlag, 'Company_id' => $cmp_id,'exportOrder'=>$dtOrder));

        $DTRenderArray = $this->workshop_report_model->TraineeSummaryLoadDataTable($dtWhere, $dthaving, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);


        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('user_id', 'traineename','designation','region_name', 'TOTALworkshop', 'played_que', 'correct', 'wrong', 'result','avg_resp_time');
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
        $dtWhere = "";
        $dthaving = '';
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
                    ->setCellValue('A3', "Trainee ID")
                    ->setCellValue('B3', "Trainee Name")
                    ->setCellValue('C3', "Designation")
                    ->setCellValue('D3', "Trainee Region")
                    ->setCellValue('E3', "No of Workshop")
                    ->setCellValue('F3', "Questions Played")
                    ->setCellValue('G3', "Correct")
                    ->setCellValue('H3', "Wrong")
                    ->setCellValue('I3', "Result")
                    ->setCellValue('J3', "Avg Responce Time");


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
            $dtOrder = $this->session->userdata('exportOrder');
            
            $dtWhere = $this->session->userdata('exportWhere');
            $dthaving = $this->session->userdata('exportHaving');
            $RightsFlag = $this->session->userdata('RightsFlag');
            $WRightsFlag = $this->session->userdata('WRightsFlag');
            $i = 3;

            $Data_list = $this->workshop_report_model->TraineeSummaryExportToExcel($dtWhere, $dthaving, $RightsFlag, $WRightsFlag,$dtOrder);


            foreach ($Data_list as $value) {
                $i++;
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$i", $value->user_id)
                        ->setCellValue("B$i", $value->traineename)
                        ->setCellValue("C$i", $value->designation)
                        ->setCellValue("D$i", $value->region_name)
                        ->setCellValue("E$i", $value->TOTALworkshop)
                        ->setCellValue("F$i", $value->played_que)
                        ->setCellValue("G$i", $value->correct)
                        ->setCellValue("H$i", $value->wrong)
                        ->setCellValue("I$i", $value->result)
                        ->setCellValue("J$i", $value->avg_resp_time);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:J$i")->applyFromArray($styleArray_body);
            }
            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Trainee_wise Summary Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        } else {
            redirect('trainee_wise_summary_report');
        }
//          echo $cmp=$this->input->post('company_id');exit;
    }

}
