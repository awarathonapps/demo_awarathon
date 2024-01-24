<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Device_changed_report extends MY_Controller {

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
        $data['module_id'] = '24.8';
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
                $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $Company_id, 'region_name');
                $data['WTypeData'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'status="1" AND company_id="' . $Company_id . '"', 'workshop_type');
                $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'company_id,id,workshop_name', 'company_id=' . $Company_id);
            } else {
                $this->common_model->SyncWorkshopRights($trainer_id, 0);
                $data['WTypeData'] = $this->common_model->getWTypeRightsList($Company_id);
                $data['WorkshopData'] = $this->common_model->getWkshopRightsList($Company_id);
                $data['RegionData'] = $this->common_model->getUserRegionList($Company_id);
            }
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('device_changed_report/index', $data);
    }

    public function ajax_companywise_data() {
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('company_id', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        if ($company_id != '') {
            $data['RegionResult'] = $this->common_model->get_selected_values('region', 'id,region_name', 'company_id=' . $company_id, 'region_name');
            $data['WtypeResult'] = $this->common_model->get_selected_values('workshoptype_mst', 'id,workshop_type', 'company_id=' . $company_id, 'workshop_type');
            $data['WorkshopData'] = $this->common_model->get_selected_values('workshop', 'id,workshop_name', 'company_id=' . $company_id, 'workshop_name');
            echo json_encode($data);
        }
    }

    public function ajax_wtypewise_workshop() {
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $trainer_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $user_id = "";
        $region_id = "";
        $workshop_type = $this->input->post('workshop_type', TRUE);
        if ($WRightsFlag) {
            $data['WorkshopData'] = $this->common_model->getUserWorkshopList($company_id, $user_id, $workshop_type);
        } else {
            $data['WorkshopData'] = $this->common_model->getWkshopRegRightsList($company_id, $trainer_id, $region_id, $workshop_type);
        }

        echo json_encode($data);
    }

    public function ajax_resionwise_data() {
        $WRightsFlag = 1;
        if ($this->mw_session['company_id'] == "") {
            $company_id = $this->input->post('data', TRUE);
        } else {
            $company_id = $this->mw_session['company_id'];
            $trainer_id = $this->mw_session['user_id'];
            if (!$this->mw_session['superaccess']) {
                $Rowset = $this->common_model->get_value('company_users', 'company_id,userrights_type,workshoprights_type', 'userid=' . $trainer_id);
                $WRightsFlag = ($Rowset->workshoprights_type == 1 ? 1 : 0);
            }
        }
        $workshop_type = "";
        $region_id = $this->input->post('region_id', TRUE);
        if ($WRightsFlag) {
            $data['WorkshopData'] = $this->Workshop_report_model->getWorkshopList($company_id, $region_id);
        } else {
            $data['WorkshopData'] = $this->common_model->getWkshopRegRightsList($company_id, $trainer_id, $region_id, $workshop_type);
        }
        echo json_encode($data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('u.user_id', 'u.emp_id', 'u.firstname', 'u.lastname', '', '', '', 'u.education_background', 'u.department', 'rg.region_name', 'dr.description', 'u.area');

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
                $dtWhere .= " AND u.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE u.company_id  = " . $cmp_id;
            }
        }
        $workshoptype_id = ($this->input->get('workshoptype_id') ? $this->input->get('workshoptype_id') : '');
        if ($workshoptype_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.workshop_type  = " . $workshoptype_id;
            } else {
                $dtWhere .= " WHERE w.workshop_type  = " . $workshoptype_id;
            }
        }

        $region_id = ($this->input->get('region_id') ? $this->input->get('region_id') : '');
        if ($region_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND w.region  = " . $region_id;
            } else {
                $dtWhere .= " WHERE w.region = " . $region_id;
            }
        }
        $workshop_id = $this->input->get('workshop_id', true);
        if ($workshop_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND wdi.workshop_id  = " . $workshop_id;
            } else {
                $dtWhere .= " WHERE wdi.workshop_id  = " . $workshop_id;
            }
            $DTRenderArray = $this->Workshop_report_model->DeviceChangedAlertDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag);
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
                "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
                "aaData" => array()
            );
            $dtDisplayColumns = array('user_id', 'emp_id', 'firstname', 'lastname', 'email', 'mobile', 'employment_year', 'education_background',
                'department', 'region_name', 'designation', 'area', 'status', 'platform', 'model', 'imei', 'serial', 'info_dttm');
            foreach ($DTRenderArray['ResultSet'] as $dtRow) {
                $row = array();
                $TotalHeader = count((array)$dtDisplayColumns);
                for ($i = 0; $i < $TotalHeader; $i++) {
                    if ($dtDisplayColumns[$i] == "info_dttm") {
                        $row[] = ($dtRow['info_dttm'] != '00-00-0000 12:00 AM' ? $dtRow['info_dttm'] :'');
                    } else if ($dtDisplayColumns[$i] == "status") {
                        $row[] = ($dtRow['status'] == 1 ? 'Active' : 'In-Active');
                    } else if ($dtDisplayColumns[$i] != ' ') {
                        $row[] = $dtRow[$dtDisplayColumns[$i]];
                    }
                }
                $output['aaData'][] = $row;
            }
        } else {
            $output = array(
                "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );
        }
        $this->session->set_userdata(array('exportWhere' => $dtWhere, 'RightsFlag' => $RightsFlag, 'WRightsFlag' => $WRightsFlag));
        echo json_encode($output);
    }

    public function exportReport() {//In use for Export       
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0);
		$workshop_id = $this->input->post('workshop_id',true);
        $WorkshopData = $this->common_model->get_value('workshop', 'id,workshop_name', 'id=' . $workshop_id);

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Workshop : '.$WorkshopData->workshop_name)
                ->setCellValue('A2', 'Trainee Id')
                ->setCellValue('B2', 'Employee Code')
                ->setCellValue('C2', 'First Name')
                ->setCellValue('D2', 'Last Name')
                ->setCellValue('E2', 'Email')
                ->setCellValue('F2', 'Mobile No.')
                ->setCellValue('G2', 'Employment Year')
                ->setCellValue('H2', 'Education Background')
                ->setCellValue('I2', 'Department/Division')
                ->setCellValue('J2', 'Region/Branch')
                ->setCellValue('K2', 'Designation')
                ->setCellValue('L2', 'Area')
                ->setCellValue('M2', 'Status')
                ->setCellValue('N2', 'Platform')
                ->setCellValue('O2', 'Model')
                ->setCellValue('P2', 'IMEI')
                ->setCellValue('Q2', 'Serial No')
                ->setCellValue('R2', 'Date & Time');

        $styleArray = array(
            'font' => array(
                'bold' => true
        ));

        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
        ));
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getStyle('A2:R2')->applyFromArray($styleArray_header);

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("A2:R2")->applyFromArray($styleArray_body);
        $dtWhere = $this->session->userdata('exportWhere');
        $RightsFlag = $this->session->userdata('RightsFlag');
        $WRightsFlag = $this->session->userdata('WRightsFlag');
        $i = 2;
        $TraineeSet = $this->Workshop_report_model->DeviceChangedExportToExcel($dtWhere, $RightsFlag, $WRightsFlag);
        $j = 0;
        foreach ($TraineeSet as $Trainee) {
            $i++;
            $j++;
            $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $Trainee->user_id)
                    ->setCellValue("B$i", $Trainee->emp_id)
                    ->setCellValue("C$i", $Trainee->firstname)
                    ->setCellValue("D$i", $Trainee->lastname)
                    ->setCellValue("E$i", $Trainee->email)
                    ->setCellValue("F$i", $Trainee->mobile)
                    ->setCellValue("G$i", $Trainee->employment_year)
                    ->setCellValue("H$i", $Trainee->education_background)
                    ->setCellValue("I$i", $Trainee->department)
                    ->setCellValue("J$i", $Trainee->region_name)
                    ->setCellValue("K$i", $Trainee->designation)
                    ->setCellValue("L$i", $Trainee->area)
                    ->setCellValue("M$i", ($Trainee->status ? 'Active' : 'In-Active'))
                    ->setCellValue("N$i", $Trainee->platform)
                    ->setCellValue("O$i", $Trainee->model)
                    ->setCellValue("P$i", $Trainee->imei)
                    ->setCellValue("Q$i", $Trainee->serial)
                    ->setCellValue("R$i", ($Trainee->info_dttm != '00-00-0000 12:00 AM' ? $Trainee->info_dttm : ''));
            $objPHPExcel->getActiveSheet()->getStyle("A$i:R$i")->applyFromArray($styleArray_body);
        }

        //Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Device Changed Alert Report.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        // ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }

}
