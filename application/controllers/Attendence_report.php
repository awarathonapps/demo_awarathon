<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Attendence_report extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('attendence_report');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('workshop_report_model');
        }

    public function index() {
        $data['module_id'] = '24.2';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        $RightsFlag = 1;
        $WRightsFlag = 1;
        if ($Company_id == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
            if (!$this->mw_session['superaccess']) {
                $trainer_id = $this->mw_session['user_id'];
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $RightsFlag = ($Rowset->userrights_type == 1 ? 1 : 0);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
            if ($WRightsFlag) {
                $data['TraineeData'] = $this->common_model->get_selected_values('device_users', 'user_id,concat(firstname," ",lastname) '
                        . 'as traineename', 'status=1  AND company_id=' . $Company_id, 'firstname');
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id, 'region_name');
                $data['WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $Company_id . '"', 'workshop_type');
                $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id);
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
                $data['WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);
                $data['TraineeData'] = $this->common_model->getUserTraineeList($Company_id);
                $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
            }
        $data['DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'company_id=' . $Company_id);    
//                $data['QsetResult']   = $this->common_model->get_selected_values('question_set','id,title','company_id='.$Company_id);
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('attendence_report/index', $data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('du.user_id', 'CONCAT(du.firstname," ",du.lastname)', 'r.region_name', 'wm.workshop_type', 'w.workshop_name', 'du.email', 'du.mobile', 'registration_date');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere2 = $DTRenderArray['dtWhere'];
        $dtWhere = " WHERE 1=1 ";
        $dtOrder = $DTRenderArray['dtOrder'];
        if ($dtOrder == "") {
            $dtOrder = " ORDER BY traineename ";
        }
        $dtLimit = $DTRenderArray['dtLimit'];
        $WRightsFlag = 1;
        $Login_id = $this->mw_session['user_id'];
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $cmp_id = $this->mw_session['company_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $Login_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE w.company_id  = " . $cmp_id;
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
            $dtWhere .= " AND wru.user_id  = " . $user_id;
        }
        $workshop_id = ($this->input->get('workshop_id') ? $this->input->get('workshop_id') : '');
        if ($workshop_id != "") {
            $dtWhere .= " AND wru.workshop_id  = " . $workshop_id;
        }
        $workshoptype_id = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '0');
        if ($workshoptype_id != "0") {
            $dtWhere .= " AND w.workshop_type  = " . $workshoptype_id;
        }
        $region_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '0');
        if ($region_id != "0") {
            $dtWhere .= " AND w.region  = " . $region_id;
        }
        $tregion_id = ($this->input->get('tregion_id') ? $this->input->get('tregion_id') : '0');
        if ($tregion_id != "0") {
            if ($dtWhere2 != "") {
                $dtWhere2 .= " AND du.region_id=" . $tregion_id;
            } else {
                $dtWhere2 .= " WHERE du.region_id=" . $tregion_id;
            }
        }
        $workshop_subtype = ($this->input->get('workshop_subtype') ? $this->input->get('workshop_subtype') : '');
        if ($workshop_subtype != "") {
            if ($dtWhere2 <> '') {
                $dtWhere2 .= " AND  w.workshopsubtype_id  = " . $workshop_subtype;
            } else {
                $dtWhere2 .= " WHERE w.workshopsubtype_id = " . $workshop_subtype;
            }
        }
        $wsubrgion_id = ($this->input->get('wsubregion_id') ? $this->input->get('wsubregion_id') : '');
        if ($wsubrgion_id != "") {
            if ($dtWhere2 <> '') {
                $dtWhere2 .= " AND  w.workshopsubregion_id  = " . $wsubrgion_id;
            } else {
                $dtWhere2 .= " WHERE w.workshopsubregion_id = " . $wsubrgion_id;
            }
        }
        $designation_id= ($this->input->get('designation_id') ? $this->input->get('designation_id') :'0');
        if($designation_id !="0")
        {
        if($dtWhere2 <> ''){
                $dtWhere2 .= " AND du.designation_id  = ".$designation_id; 
           }else{
                $dtWhere2 .= " WHERE du.designation_id = ".$designation_id; 
           } 
        }
        if (!$WRightsFlag) {
            $login_id = $this->mw_session['user_id'];
            $dtWhere .= " AND wru.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $this->session->set_userdata(array('exportWhere' => $dtWhere, 'exportWhere2' => $dtWhere2, 'Company_id' => $cmp_id));


        $DTRenderArray = $this->workshop_report_model->AttendanceLoadDataTable($dtWhere, $dtOrder, $dtLimit, $dtWhere2);


        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
//        echo "<pre>";
//        print_r($DTRenderArray['ResultSet']);
//        exit;
        $dtDisplayColumns = array('user_id', 'traineename','designation','trainee_region', 'region_name', 'workshop_subregion', 'workshop_type', 'workshop_subtype', 'workshop_name', 'email', 'mobile', 'registration_date', 'trainee_status', 'pre_session', 'post_session', 'pre_feedback', 'post_feedback');
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "trainee_status") {
					if ($dtRow['pre_session'] == '1' && $dtRow['post_session'] == '3' &&(( $dtRow['pre_feedback'] == '3' ||  $dtRow['pre_feedback'] == '1') && ($dtRow['post_feedback'] == '3' || $dtRow['post_feedback'] == '1'))) {
                        $row[] = 'Attended';
                    } else if ($dtRow['pre_session'] == '3' && $dtRow['post_session'] == '1' &&(( $dtRow['pre_feedback'] == '3' ||  $dtRow['pre_feedback'] == '1') && ($dtRow['post_feedback'] == '3' || $dtRow['post_feedback'] == '1'))) {
                        $row[] = 'Attended';
                    } else if ($dtRow['pre_session'] == '1' && $dtRow['post_session'] == '3' &&(( $dtRow['pre_feedback'] == '3' ||  $dtRow['pre_feedback'] == '1') && ($dtRow['post_feedback'] == '3' || $dtRow['post_feedback'] == '1'))) {
                        $row[] = 'Attended';
                    } else if ($dtRow['pre_session'] == '1' && $dtRow['post_session'] == '1' && $dtRow['pre_feedback'] == '1' && $dtRow['post_feedback'] == '1') {
                        $row[] = 'Attended';
                    } else if ($dtRow['pre_session'] == '1' && $dtRow['post_session'] == '1' && $dtRow['pre_feedback'] == '3' && $dtRow['post_feedback'] == '3') {
                        $row[] = 'Attended';
                    } else if ($dtRow['pre_session'] == '1' && $dtRow['post_session'] == '1' && $dtRow['pre_feedback'] == '3' && $dtRow['post_feedback'] == '1') {
                        $row[] = 'Attended';
                    } else if ($dtRow['pre_session'] == '1' && $dtRow['post_session'] == '1' && $dtRow['pre_feedback'] == '1' && $dtRow['post_feedback'] == '3') {
                        $row[] = 'Attended';
                    } else if ($dtRow['pre_session'] == '2' || $dtRow['post_session'] == '2' || $dtRow['pre_feedback'] == '2' || $dtRow['post_feedback'] == '2') {
                        $row[] = 'Incomplete';
                    } else if (($dtRow['pre_session'] == '0' && $dtRow['post_session'] == '0' && $dtRow['pre_feedback'] == '3' && $dtRow['post_feedback'] == '3')) {
                        $row[] = 'Absent';
                    } else if (($dtRow['pre_session'] == '0' && $dtRow['post_session'] == '0' && $dtRow['pre_feedback'] == '0' && $dtRow['post_feedback'] == '0')) {
                        $row[] = 'Absent';
                    } else {
                        $row[] = 'Incomplete';
                    }
                } else if ($dtDisplayColumns[$i] == "pre_session" || $dtDisplayColumns[$i] == "post_session" || $dtDisplayColumns[$i] == "pre_feedback" || $dtDisplayColumns[$i] == "post_feedback") {
                    $row[] = $this->get_AttendanceStatus($dtRow[$dtDisplayColumns[$i]]);
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
                    ->setCellValue('A3', "Trainee ID")
                    ->setCellValue('B3', "Trainee Name")
                    ->setCellValue('C3', "Designation")
                    ->setCellValue('D3', "Trainee Region")
                    ->setCellValue('E3', "Workshop Region")
                    ->setCellValue('F3', "Workshop Sub-region")
                    ->setCellValue('G3', "Workshop Type")
                    ->setCellValue('H3', "Workshop Sub-type")
                    ->setCellValue('I3', "Workshop Name")
                    ->setCellValue('J3', "Email")
                    ->setCellValue('K3', "Mobile")
                    ->setCellValue('L3', "Registration Date")
                    ->setCellValue('M3', "Status")
                    ->setCellValue('N3', "Pre Session")
                    ->setCellValue('O3', "Post Session")
                    ->setCellValue('P3', "Feedback Pre")
                    ->setCellValue('Q3', "Feedback Post");
            $styleArray = array(
                'font' => array(
//                'bold' => true
            ));

            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
            ));
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(14);


            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:Q3')->applyFromArray($styleArray_header);

            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $exportWhere = $this->session->userdata('exportWhere');
            $exportWhere2 = $this->session->userdata('exportWhere2');
            //$store_id = $this->session->userdata('store_id');
            $i = 3;
            $j = 0;
            $Data_list = $this->workshop_report_model->AttendanceExportToExcel($exportWhere, $exportWhere2);


            foreach ($Data_list as $value) {
                $i++;
                $j++;
                $trainee_status = '';
				if ($value->pre_session == '1' && $value->post_session == '3' &&(($value->pre_feedback == '3' || $value->pre_feedback == '1') && ($value->post_feedback == '3' || $value->post_feedback == '1'))) {
                    $trainee_status = 'Attended';
                } else if ($value->pre_session == '3' && $value->post_session == '1' &&(($value->pre_feedback == '3' || $value->pre_feedback == '1') && ($value->post_feedback == '3' || $value->post_feedback == '1'))) {
                    $trainee_status = 'Attended';
                } else if ($value->pre_session == '1' && $value->post_session == '3' &&(($value->pre_feedback == '3' || $value->pre_feedback == '1') && ($value->post_feedback == '3' || $value->post_feedback == '1'))) {
                    $trainee_status = 'Attended';
                } else if ($value->pre_session == '1' && $value->post_session == '1' && $value->pre_feedback == '1' && $value->post_feedback == '1') {
                    $trainee_status = 'Attended';
                } else if ($value->pre_session == '1' && $value->post_session == '1' && $value->pre_feedback == '3' && $value->post_feedback == '3') {
                    $trainee_status = 'Attended';
                } else if ($value->pre_session == '1' && $value->post_session == '1' && $value->pre_feedback == '3' && $value->post_feedback == '1') {
                    $trainee_status = 'Attended';
                } else if ($value->pre_session == '1' && $value->post_session == '1' && $value->pre_feedback == '1' && $value->post_feedback == '3') {
                    $trainee_status = 'Attended';
                } else if ($value->pre_session == '2' || $value->post_session == '2' || $value->pre_feedback == '2' || $value->post_feedback == '2') {
                    $trainee_status = 'Incomplete';
                } else if (($value->pre_session == '0' && $value->post_session == '0' && $value->pre_feedback == '3' && $value->post_feedback == '3')) {
                    $trainee_status = 'Absent';
                } else if (($value->pre_session == '0' && $value->post_session == '0' && $value->pre_feedback == '0' && $value->post_feedback == '0')) {
                    $trainee_status = 'Absent';
                } else {
                    $trainee_status = 'Incomplete';
                }
                $objPHPExcel->getActiveSheet()
                        ->setCellValue("A$i", $value->user_id)
                        ->setCellValue("B$i", $value->traineename)
                        ->setCellValue("C$i", $value->designation)
                        ->setCellValue("D$i", $value->trainee_region)
                        ->setCellValue("E$i", $value->region_name)
                        ->setCellValue("F$i", $value->workshop_subregion)
                        ->setCellValue("G$i", $value->workshop_type)
                        ->setCellValue("H$i", $value->workshop_subtype)
                        ->setCellValue("I$i", $value->workshop_name)
                        ->setCellValue("J$i", $value->email)
                        ->setCellValue("K$i", $value->mobile)
                        ->setCellValue("L$i", $value->registration_date)
                        ->setCellValue("M$i", $trainee_status)
                        ->setCellValue("N$i", $this->get_AttendanceStatus($value->pre_session))
                        ->setCellValue("O$i", $this->get_AttendanceStatus($value->post_session))
                        ->setCellValue("P$i", $this->get_AttendanceStatus($value->pre_feedback))
                        ->setCellValue("Q$i", $this->get_AttendanceStatus($value->post_feedback));
                $objPHPExcel->getActiveSheet()->getStyle("A$i:Q$i")->applyFromArray($styleArray_body);
            }

            //Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Attendance Report.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // ob_end_clean();
            $objWriter->save('php://output');
            // Sending headers to force the user to download the file
        } else {
            redirect('attendence_report');
        }
    }

    function get_AttendanceStatus($Status) {
        $RtStatus = '';
        switch ($Status) {
            case "1":
                $RtStatus = 'Completed';
                break;
            case "2":
                $RtStatus = 'Playing';
                break;
            case "3":
                $RtStatus = 'Not Applicable';
                break;
            default:
                $RtStatus = 'Not Played';
                break;
        }
        return $RtStatus;
    }

}
