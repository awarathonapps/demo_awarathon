<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Billing_module extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('billing_module');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->common_db = $this->common_model->connect_db2();
        $this->acces_management = $acces_management;
        $this->load->model('billing_module_model');
    }

    public function index()
    {
        $data['module_id'] = '22.3';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('billing_module/index', $data);
    }

    public function DatatableRefresh()
    {
        $dtSearchColumns = array('YEAR(a.system_dttm)', 'MONTHNAME(a.system_dttm)', 'a.active_users', 'a.inactive_users');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        if ($dtWhere == '') {
            $dtWhere .= " WHERE 1=1 ";
        }
        if ($company_id != "") {
            $dtWhere .= " AND a.company_id  = " . $company_id;
        }
        // filter data
        $Start_date = ($this->input->get('start_date') ? $this->input->get('start_date') : '');
        $End_date = ($this->input->get('end_date') ? $this->input->get('end_date') : '');
        // if ($Start_date != "") {
        //     $dtWhere .= " AND date(a.system_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        // }
        $DTRenderArray = $this->billing_module_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('year', 'month', 'active_users', 'inactive_users');
        // $dtDisplayColumns = array('checkbox', 'year', 'month', 'total_users', 'active_users', 'user_per_month', 'inactive_users');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]"/>
                                <span></span>
                            </label>';
                } elseif ($dtDisplayColumns[$i] == "user_per_month") {
                    $dtWhere1 = $dtWhere . " AND MONTH(a.system_dttm) = " . $dtRow['month_id'] . " AND YEAR(system_dttm) = " . $dtRow['year_id'];
                    $monthData = $this->billing_module_model->monthwiseUsers($dtWhere1);
                    $row[] = (count((array) $monthData) > 0 ? $monthData->active_users : 0);
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }


    public function export_data()
    {
        $export_type = $this->input->post('export_type', true);
        if ($export_type == 1) {
            $Company_id = $this->mw_session['company_id'];
            $dtWhere = " WHERE 1=1 ";
            $Heading = "";
            if ($Company_id == "") {
                $Company_id = $this->input->post('company_id', true);
                if ($Company_id != "") {
                    $Company_set = $this->common_model->get_value("company", "company_name", "id=" . $Company_id);
                    $Heading = "Compnay Name : " . $Company_set->company_name;
                } else {
                    $Heading = "Compnay Name : ALL";
                }
            }
            if ($Company_id != "") {
                $dtWhere .= " AND a.company_id  = " . $Company_id;
            }

            $Start_date = ($this->input->post('start_date') ? $this->input->post('start_date') : '');
            $End_date = ($this->input->post('end_date') ? $this->input->post('end_date') : '');

            // if ($Start_date != "") {
            //     $dtWhere .= " AND date(a.system_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
            // }
            // $id_list = $this->input->post('id', true);
            // if (count((array)$id_list) > 0) {
            //     $dtWhere .= " AND u.user_id IN(" . implode(',', $id_list) . ")";
            // }
            $export_type = $this->input->post('export_type', true);
            $this->load->library('PHPExcel_CI');
            $objPHPExcel = new PHPExcel_CI();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', $Heading)
                ->setCellValue('A2', 'Year')
                ->setCellValue('B2', 'Month')
                // ->setCellValue('C2', 'User Ids Generated Cumulative')
                ->setCellValue('C2', 'User Ids Generated Cumulative')
                // ->setCellValue('E2', 'Live User IDs per Month')
                ->setCellValue('D2', 'Suspended User IDs Cumulative');
            $styleArray = array(
                'font' => array(
                    'bold' => true
                )
            );

            $styleArray_header = array(
                'font' => array(
                    'color' => array('rgb' => '990000'),
                    'border' => 1
                )
            );
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            // $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            // $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);

            $objPHPExcel->getActiveSheet()->getStyle('A2:D2')->applyFromArray($styleArray_header);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

            $styleArray_body = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle("A2:D2")->applyFromArray($styleArray_body);
            $i = 2;
            $TraineeSet = $this->billing_module_model->ExportDeviceUsers($dtWhere, $export_type);
            $j = 0;
            foreach ($TraineeSet as $Trainee) {
                $dtWhere1 = $dtWhere . " AND MONTH(a.system_dttm) = " . $Trainee->month_id . " AND YEAR(system_dttm) = " . $Trainee->year_id;
                $monthData = $this->billing_module_model->monthwiseUsers($dtWhere1);
                $permonth = (count((array) $monthData) > 0 ? $monthData->active_users : 0);
                $i++;
                $j++;
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("A$i", $Trainee->year)
                    ->setCellValue("B$i", $Trainee->month)
                    // ->setCellValue("C$i", $Trainee->total_users)
                    ->setCellValue("C$i", $Trainee->active_users)
                    // ->setCellValue("E$i", $permonth)
                    ->setCellValue("D$i", $Trainee->inactive_users);
                $objPHPExcel->getActiveSheet()->getStyle("A$i:D$i")->applyFromArray($styleArray_body);
            }

            $objPHPExcel->setActiveSheetIndex(0);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="UserStatistic.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            ob_end_clean();
            $objWriter->save('php://output');
        } else {
            $this->users_export_data();
        }
    }
    public function users_export_data()
    {
        $Company_id = $this->mw_session['company_id'];
        $dtWhere = " WHERE 1=1 ";
        $Heading = "";
        if ($Company_id == "") {
            $Company_id = $this->input->post('company_id', true);
            if ($Company_id != "") {
                $Company_set = $this->common_model->get_value("company", "company_name", "id=" . $Company_id);
                $Heading = "Compnay Name : " . $Company_set->company_name;
            } else {
                $Heading = "Compnay Name : ALL";
            }
        }

        $Start_date = ($this->input->post('user_start_date') ? $this->input->post('user_start_date') : '');
        $End_date = ($this->input->post('user_end_date') ? $this->input->post('user_end_date') : '');

        // if ($Start_date != "") {
        //     $dtWhere .= " AND date(du.addeddate) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        // }

        $this->load->library('PHPExcel_CI');
        $objPHPExcel = new PHPExcel_CI();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()
            // ->setCellValue('A1', $Heading)
            ->setCellValue('A1', 'CMS + Device Users')
            ->setCellValue('A2', 'Employee Code')
            ->setCellValue('B2', 'First Name')
            ->setCellValue('C2', 'Last Name')
            ->setCellValue('D2', 'Email')
            ->setCellValue('E2', 'Mobile No.')
            ->setCellValue('F2', 'Department/Division')
            ->setCellValue('G2', 'Region/Branch')
            ->setCellValue('H2', 'Designation')
            ->setCellValue('I2', 'Registration Date')
            ->setCellValue('J2', 'Modified Date')
            ->setCellValue('K2', 'Login Date & Time')
            ->setCellValue('L2', 'Status');
        // $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFill()->applyFromArray(array(
        //     'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //     'startcolor' => array(
        //         'rgb' => 'efe935fc'
        //     )
        // ));
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'ffffff'),
                'size' => 12,
                'name' => 'Calibri'
            )
        );

        $styleArray_header = array(
            'font' => array(
                'color' => array('rgb' => '990000'),
                'border' => 1
            )
        );
        $objPHPExcel->getActiveSheet()->mergeCells('A1:C1');
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);

        $objPHPExcel->getActiveSheet()->getStyle('A2:L2')->applyFromArray($styleArray_header);
        $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);

        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("A2:L2")->applyFromArray($styleArray_body);
        $i = 2;
        $UserSet = $this->billing_module_model->ExportUsersData($Start_date, $End_date);
        $j = 0;
        foreach ($UserSet as $Trainee) {

            $i++;
            $j++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$i", $Trainee->emp_id)
                ->setCellValue("B$i", $Trainee->firstname)
                ->setCellValue("C$i", $Trainee->lastname)
                ->setCellValue("D$i", $Trainee->email)
                ->setCellValue("E$i", $Trainee->mobile)
                ->setCellValue("F$i", $Trainee->department)
                ->setCellValue("G$i", $Trainee->region_name)
                ->setCellValue("H$i", $Trainee->designation)
                ->setCellValue("I$i", $Trainee->registration_date)
                ->setCellValue("J$i", $Trainee->modifieddate)
                ->setCellValue("K$i", $Trainee->login_dttm)
                ->setCellValue("L$i", $Trainee->user_status);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:L$i")->applyFromArray($styleArray_body);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="CMS_Device_Users.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');
    }
    public function fetch_statistics()
    {
        $company_id = $this->input->post('company_id', true);
        $start_date = $this->input->post('start_date', true);
        $end_date = $this->input->post('end_date', true);
        $box_i_statistics       = 0;
        $box_ii_statistics      = 0;
        $box_iii_statistics     = 0;
        
        $box_result = $this->billing_module_model->count_max_active_user($start_date, $end_date, $company_id);
        // if (count((array)$box_result) == 0) {
        //     $box_result = $this->common_model->get_selected_values("users_statistics", "*", "company_id=$company_id", "active_users DESC LIMIT 0,1");
        // }
        if (count((array) $box_result) > 0) {
            $box_i_statistics = isset($box_result[0]->total_users) ? $box_result[0]->total_users : 0;
            $box_ii_statistics = isset($box_result[0]->active_users) ? $box_result[0]->active_users : 0;
            $box_iii_statistics = isset($box_result[0]->inactive_users) ? $box_result[0]->inactive_users : 0;
        }
        $response = [
            'box_i_statistics' => $box_i_statistics,
            'box_ii_statistics' => $box_ii_statistics,
            'box_iii_statistics' => $box_iii_statistics,
        ];
        echo json_encode($response);
    }
}