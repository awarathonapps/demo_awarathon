<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api_logs extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('api_logs');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->common_db = $this->common_model->connect_db2();
        $this->acces_management = $acces_management;
        $this->load->model('api_logs_model');
    }

    public function index()
    {

        $data['module_id'] = '104';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $this->db->select('id,company_name');
            $this->db->from('company');
            $this->db->where('status', '1');
            $data['CompnayResultSet'] = $this->db->get()->result();
        } else {
            $data['CompnayResultSet'] = array();
        }
        $this->common_db->select('id,company_name');
        $this->common_db->from('company');
        $this->common_db->where('status', '1');
        $data['Company_set'] = $this->common_db->get()->result();
        $data['start_date'] = date('d-m-Y');
        $data['end_date'] = date("d-m-Y");

        $data['Company_id'] = $Company_id;
        $this->load->view('api_logs/index', $data);
    }

    public function view($id)
    {
        $id = $this->security->xss_clean(base64_decode($id));
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('api_logs');
            return;
        }
        $this->load->helper('form');
        $data['module_id'] = '104';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;

        $this->common_db->select('a.*, c.portal_name');
        $this->common_db->from('api_logs as a');
        $this->common_db->join('company AS c ', 'c.id = a.company_id', 'left');
        $this->common_db->where('a.id', $id);
        $data['company_data'] =  $this->common_db->get()->row();
        $this->load->view('api_logs/view', $data);
    }


    public function DatatableRefresh()
    {
        $dtSearchColumns = array('a.id', 'a.company_id', 'c.portal_name', 'a.api_name', 'a.api_parameter', 'a.date_time', 'a.ip_address', 'a.status_msg');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($this->input->get('company_id') != "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
            if ($company_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND a.company_id  = " . $company_id;
                } else {
                    $dtWhere .= " WHERE a.company_id  = " . $company_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE a.company_id  = " . $this->mw_session['company_id'];
            }
        }
        $Start_date = ($this->input->get('start_date') ? $this->input->get('start_date') : '');
        $End_date = ($this->input->get('end_date') ? $this->input->get('end_date') : '');
        if ($Start_date != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND a.date_time between '" . date('Y-m-d', strtotime($Start_date)) .
                    "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
            } else {
                $dtWhere .= " WHERE a.date_time between '" . date('Y-m-d', strtotime($Start_date)) .
                    "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
            }
        }
        $DTRenderArray = $this->api_logs_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        // $dtDisplayColumns = array('checkbox', 'id', 'company_id','portal_name', 'api_name', 'api_parameter', 'date_time', 'ip_address', 'status_msg', 'Actions');
        $dtDisplayColumns = array('id', 'company_id', 'portal_name', 'api_name', 'api_parameter', 'date_time', 'ip_address', 'status_msg', 'Actions');

        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                // if ($dtDisplayColumns[$i] == "checkbox") {
                //     $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                //                 <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['id'] . '"/>
                //                 <span></span>
                //             </label>';
                // } else
                if ($dtDisplayColumns[$i] == "api_parameter") {
                    $row[] = "<textarea style='width:100%;height:94px;' col='3' row='3' readonly=''>" . $dtRow['api_parameter'] . "</textarea>";
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if ($acces_management->allow_add or $acces_management->allow_view or $acces_management->allow_edit or $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_view) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'api_logs/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }

                        $action .= '</ul>
                            </div>';
                    } else {
                        $action = '<button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                Locked&nbsp;&nbsp;<i class="fa fa-lock"></i>
                            </button>';
                    }
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function export_api_logs()
    {
        $dtWhere = " WHERE 1=1 ";
        $Heading = "";
        $Company_id = $this->security->xss_clean($this->input->post('company_id', true));
        if ($Company_id != "") {
            $this->db->select('company_name')->from('company');
            $this->db->where('id', $Company_id);
            $Company_set = $this->db->get()->row();
            $Heading = "Compnay Name : " . $Company_set->company_name;
        } else {
            $Company_id = $this->mw_session['company_id'];
            $Heading = "Compnay Name : ALL";
        }

        if ($Company_id != "") {
            $dtWhere .= " AND a.company_id  = " . $Company_id;
        }

        $Start_date = $this->security->xss_clean(($this->input->post('start_date') ? $this->input->post('start_date') : ''));
        $End_date = $this->security->xss_clean(($this->input->post('end_date') ? $this->input->post('end_date') : ''));
        if ($Start_date != "") {
            $dtWhere .= " AND a.date_time between '" . date('Y-m-d', strtotime($Start_date)) .
                "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
        }
        $id_list = $this->input->post('id', true);
        if (count((array)$id_list) > 0) {
            $dtWhere .= " AND a.id IN (" . implode(',', $id_list) . ")";
        }
        $export_type = $this->input->post('export_type', true);
        $this->load->library('PHPExcel_CI');
        $objPHPExcel = new PHPExcel_CI();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', $Heading)
            ->setCellValue('A2', 'Company Id')
            ->setCellValue('B2', 'Company Name')
            ->setCellValue('C2', 'Api Name')
            ->setCellValue('D2', 'Api Parameter')
            ->setCellValue('E2', 'Date Time')
            ->setCellValue('F2', 'ip_address')
            ->setCellValue('G2', 'status_msg');

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
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(300);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($styleArray_header);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $i = 2;
        $ApiSet = $this->api_logs_model->ExportApiLogs($dtWhere, $export_type);
        $j = 0;
        foreach ($ApiSet as $api) {
            $i++;
            $j++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue("A$i", $api->company_id)
                ->setCellValue("B$i", $api->portal_name)
                ->setCellValue("C$i", $api->api_name)
                ->setCellValue("D$i", $api->api_parameter)
                ->setCellValue("E$i", $api->date_time)
                ->setCellValue("F$i", $api->ip_address)
                ->setCellValue("G$i", $api->status_msg);

            $objPHPExcel->getActiveSheet()->getStyle("A$i:F$i")->applyFromArray($styleArray_body);
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet

        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Api_Logs.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');
    }
}
