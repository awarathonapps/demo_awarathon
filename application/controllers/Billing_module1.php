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
        $data['module_id'] = '14.03';
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
        $dtSearchColumns = array('id', 'system_dttm', 'MONTHNAME(system_dttm)', 'total_users', 'active_users', 'last_added', 'inactive_users');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        if ($this->input->get('sSearch') != '') {
            $dtWhere .= ($dtWhere <> '') ? " AND " : " WHERE ";
        }
        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
            if ($company_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND company_id  = " . $company_id;
                } else {
                    $dtWhere .= " company_id  = " . $company_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE company_id  = " . $this->mw_session['company_id'];
            }
        }

        // filter data
        $Start_date = ($this->input->get('start_date') ? $this->input->get('start_date') : '');
        $End_date = ($this->input->get('end_date') ? $this->input->get('end_date') : '');
        if ($Start_date != "") {
            // if ($dtWhere <> '') {
            //     $dtWhere .= " AND system_dttm BETWEEN '" . date('Y-m-d', strtotime($Start_date)) . ' 00:00:00' . "'  
            //                   and '" . date('Y-m-d', strtotime($End_date)) . ' 23:59:59' . "'";
            // } else {
            //     $dtWhere .= "  WHERE system_dttm BETWEEN '" . date('Y-m-d', strtotime($Start_date)) . ' 00:00:00' . "'  
            //     and '" . date('Y-m-d', strtotime($End_date)) . ' 23:59:59' . "'";
            // }
            $dtWhere .= " AND active_users = (SELECT MAX(active_users) FROM users_statistics WHERE system_dttm BETWEEN '" . date('Y-m-d', strtotime($Start_date)) . ' 00:00:00' . "'  
                              and '" . date('Y-m-d', strtotime($End_date)) . ' 23:59:59' . "')";
        }
        $DTRenderArray = $this->billing_module_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('year', 'month', 'total_users', 'active_users', 'user_per_month', 'inactive_users');
        // $dtDisplayColumns = array('checkbox', 'year', 'month', 'total_users', 'active_users', 'user_per_month', 'inactive_users');
        $site_url = base_url();
        $acces_management = $this->acces_management;
        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]"/>
                                <span></span>
                            </label>';
                }else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }


    public function export_data()
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
        if ($Company_id != "") {
            $dtWhere .= " AND company_id  = " . $Company_id;
        }

        $Start_date = ($this->input->post('start_date') ? $this->input->post('start_date') : '');
        $End_date = ($this->input->post('end_date') ? $this->input->post('end_date') : '');
        if ($Start_date != "") {
            // $dtWhere .= " AND system_dttm between '" . date('Y-m-d', strtotime($Start_date)) .
            //     "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
            $dtWhere .= " AND active_users = (SELECT MAX(active_users) FROM users_statistics WHERE system_dttm BETWEEN '" . date('Y-m-d', strtotime($Start_date)) . ' 00:00:00' . "'  
                              and '" . date('Y-m-d', strtotime($End_date)) . ' 23:59:59' . "')";
        }
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
            ->setCellValue('C2', 'User Ids Generated Cumulative')
            ->setCellValue('D2', 'Live User IDs Cumulative')
            ->setCellValue('E2', 'Live User IDs per Month')
            ->setCellValue('F2', 'Suspended User IDs Cumulative');
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);

        $objPHPExcel->getActiveSheet()->getStyle('A2:N2')->applyFromArray($styleArray_header);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $i = 2;
        $TraineeSet = $this->billing_module_model->ExportDeviceUsers($dtWhere, $export_type);
        $j = 0;
        foreach ($TraineeSet as $Trainee) {
            $i++;
            $j++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$i", $Trainee->year)
                ->setCellValue("B$i", $Trainee->month)
                ->setCellValue("C$i", $Trainee->total_users)
                ->setCellValue("D$i", $Trainee->active_users)
                ->setCellValue("E$i", $Trainee->user_per_month)
                ->setCellValue("F$i", $Trainee->inactive_users);
            $objPHPExcel->getActiveSheet()->getStyle("A$i:F$i")->applyFromArray($styleArray_body);
        }

        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="UserStatistic.xls"');
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
        if (count($box_result) == 0) {
            $box_result = $this->common_model->get_selected_values("users_statistics", "*", "company_id=$company_id", "active_users DESC LIMIT 0,1");
        }
        if (isset($box_result) and count((array)$box_result) > 0) {
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