<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Device_users extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('device_users');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->common_db = $this->common_model->connect_db2();
        $this->acces_management = $acces_management;
        $this->load->model('device_users_model');
    }

    public function index()
    {
        $data['module_id'] = '22.1';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('device_users/index', $data);
    }

    public function create()
    {
        $data['module_id'] = '22.1';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('device_users');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
            $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 and company_id=' . $Company_id);
            $data['DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'status=1 and company_id=' . $Company_id);
            $this->db->select('cu.userid, cu.emp_id, CONCAT(cu.first_name, " ",cu.last_name) as manager_name');
            $this->db->from('company_users as cu');
            $this->db->where('cu.status', '1');
            $this->db->where('cu.company_id', $Company_id);
            $data['ManagerData'] = $this->db->get()->result();
        }
        $data['Company_id'] = $Company_id;

        $this->load->view('device_users/create', $data);
    }

    public function ajax_region()
    {
        $company_id = $this->input->post('company_id');
        $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 and company_id=' . $company_id);
        $data['DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'status=1 and company_id=' . $company_id);
        echo json_encode($data);
    }

    public function import()
    {
        $data['module_id'] = '22.1';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('device_users');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('device_users/import', $data);
    }

    // Bhautik Rana Import user Data || 04-07-2023
    public function import_user()
    {
        $data['module_id'] = '22.1';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('device_users');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            // $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
            $this->db->select('id,company_name');
            $this->db->from('company');
            $this->db->where('status', '1');
            $data['CompnayResultSet'] = $this->db->get()->result();
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('device_users/import_user', $data);
    }
    // Bhautik Rana Import user Data || 04-07-2023


    public function samplexls()
    {
        //$this->load->library('PHPExcel_CI');
        $Excel = new Spreadsheet();
        // $Excel = new PHPExcel_CI;
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('User_List');
        $Excel->createSheet();
        $Excel->getActiveSheet()
            ->setCellValue('A1', 'Do not modify or delete the Columns.');
        $Excel->getActiveSheet()->getStyle('A1:D1')->getFill()->applyFromArray(
            array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => 'FF0000'
                )
            )
        );
        //merge cell A1 until D1
        $Excel->getActiveSheet()->mergeCells('A1:D1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $Excel->getActiveSheet()
            ->setCellValue('A2', 'First Name*')
            ->setCellValue('B2', 'Last Name*')
            ->setCellValue('C2', 'Email*')
            ->setCellValue('D2', 'Mobile No.*');

        $Excel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:D2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('B')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('C')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('D')->setWidth("30");

        $Excel->getActiveSheet()->getStyle('A2:D2')->getFill()->applyFromArray(
            array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => 'eb3a12'
                )
            )
        );
        //set aligment to center for that merged cell (A1 to D1)
        $filename = "Device_User_Import.xls";
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        ob_end_clean();
        $objWriter->save('php://output');
    }

    public function UploadXls()
    {
        $Message = '';
        $SuccessFlag = 1;
        $manager1Flag = 1;
        $manager2Flag = 1;
        $this->load->model('company_model');
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('filename', '', 'callback_file_check');
        }
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            $FileData = $_FILES['filename'];
            //$this->load->library('PHPExcel_CI');
            //$objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
            $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($FileData['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            //print_r($highestRow);
            $highestColumm = $worksheet->getHighestColumn();
            //$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumm);
            //print_r($highestColumnIndex);
            if ($highestRow < 2) {
                $Message .= "Excel row/column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($highestRow == 2) {
                $Message .= "Excel file cannot be empty.";
                $SuccessFlag = 0;
            }
            if ($highestColumnIndex < 18) {
                $Message .= "Excel column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                for ($row = 3; $row <= $highestRow; $row++) {
                    $Emp_code = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    if ($Emp_code == '') {
                        //print_r($row);
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Employee Code is Empty. </br> ";
                        continue;
                    } else {
                        $EMP_code_check = $this->device_users_model->DuplicateEmployeeCode_deviceuser($Emp_code);
                        if (count((array) $EMP_code_check) > 0) {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row,Employee Code Already exists.!<br/>";
                            continue;
                        }
                    }
                    $First_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    if ($First_name == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, First Name is Empty. </br> ";
                        continue;
                    }
                    $Last_name = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    if ($Last_name == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Last Name is Empty. </br> ";
                        continue;
                    }
                    $Email = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                    if ($Email == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Email is Empty. </br> ";
                        continue;
                    } else {
                        $EmailDuplicateCheck = $this->common_model->DuplicateEmail($Email, $Company_id);
                        if (count((array) $EmailDuplicateCheck) > 0) {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row,Email Already exists.!<br/>";
                            continue;
                        }
                    }
                    $Pwd = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                    if ($Pwd == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Password is Empty. </br> ";
                        continue;
                    }
                    $dept = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                    if ($dept != '') {
                        $this->db->select('id')->from('division_mst')->where('division_name LIKE', $dept);
                        if ($Company_id != "") {
                            $this->db->where('company_id', $Company_id);
                        }
                        $division_id = $this->db->get()->row();
                        if (count((array) $division_id) == 0) {
                            if (preg_match('/^[a-zA-Z0-9 .\-]+$/i', $dept)) {
                                $now = date('Y-m-d H:i:s');
                                $division_data = array(
                                    'company_id' => $Company_id,
                                    'division_name' => $dept,
                                    'status' => '1',
                                    'addeddate' => $now,
                                    'addedby' => $this->mw_session['user_id'],
                                    'deleted' => 0
                                );
                                $this->common_model->insert('division_mst', $division_data);
                            } else {
                                $SuccessFlag = 0;
                                $Message .= "Row No. $row, Please enter valid Division with alphabets,numbers and space only!<br/>";
                                continue;
                            }
                        }
                    } else {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Department/Division is Empty. </br> ";
                        continue;
                    }
                    $region = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
                    if ($region != '') {
                        // $regionId = $this->common_model->get_value('region', 'id', "region_name LIKE '" . $region . "' AND company_id=" . $Company_id);
                        $this->db->select('id')->from('region')->where('region_name LIKE', $region);
                        if ($Company_id != "") {
                            $this->db->where('company_id', $Company_id);
                        }
                        $regionId = $this->db->get()->row();
                        if (count((array) $regionId) == 0) {
                            if (preg_match('/^[a-zA-Z0-9 .\-]+$/i', $region)) {
                                $now = date('Y-m-d H:i:s');
                                $region_data = array(
                                    'company_id' => $Company_id,
                                    'region_name' => $region,
                                    'status' => '1',
                                    'addeddate' => $now,
                                    'addedby' => $this->mw_session['user_id'],
                                    'deleted' => 0
                                );
                                $this->common_model->insert('region', $region_data);
                            } else {
                                $SuccessFlag = 0;
                                $Message .= "Row No. $row, Please enter valid Region with alphabets,numbers and space only!<br/>";
                                continue;
                            }
                        }
                    } else {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Region/Branch is Empty. </br> ";
                        continue;
                    }
                    $designationchk = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
                    if ($designationchk != '') {
                        $this->db->select('id')->from('designation_trainee')->like('description', $designationchk);
                        if ($Company_id != "") {
                            $this->db->where('company_id', $Company_id);
                        }
                        $designationId = $this->db->get()->row();
                        if (count((array) $designationId) == 0) {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row,Invalid Designation,Please enter valid Designation!<br/>";
                            continue;
                        }
                    }

                    // Manager1 Validation
                    $ec_of_l1 = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $l1_fname = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                    $l1_lname = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    $l1_email_id = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                    if ($ec_of_l1 == '' && $l1_fname == '' && $l1_lname == '' && $l1_email_id == '') {
                        $manager1Flag = 0;
                    } else {
                        if ($ec_of_l1 != '') {
                            // $Emp_idDuplicateCheck1 = $this->device_users_model->DuplicateEmployeeCode($ec_of_l1);
                            // if (count((array) $Emp_idDuplicateCheck1) > 0) {
                            //     $SuccessFlag = 0;
                            //     $Message .= "Row No. $row, EC of L1 Code Already exists.!<br/>";
                            //     $manager1Flag = 0;
                            //     continue;
                            // }
                        } else {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row, EC of L1 Code is Empty. </br>";
                            $manager1Flag = 0;
                            continue;
                        }
                        if ($l1_fname == '') {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row, L1 First Name is Empty. <br/>";
                            $manager1Flag = 0;
                            continue;
                        }
                        if ($l1_lname == '') {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row, L1 Last Name is Empty. <br/>";
                            $manager1Flag = 0;
                            continue;
                        }
                        if ($l1_email_id != '') {
                            $MangEmailDuplicateCheck1 = $this->common_model->get_value('company_users', 'userid', "email LIKE '" . $l1_email_id . "' AND emp_id NOT LIKE '" . $ec_of_l1 . "'");
                            if (count((array) $MangEmailDuplicateCheck1) > 0) {
                                $SuccessFlag = 0;
                                $Message .= "Row No. $row,L1 Email ID Already exists.!<br/>";
                                $manager1Flag = 0;
                                continue;
                            }
                        } else {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row, L1 Email ID is Empty. </br>";
                            $manager1Flag = 0;
                            continue;
                        }
                    }
                    // L2 Validation start here
                    $ec_of_l2 = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    $l2_fname = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                    $l2_lname = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                    $l2_email_id = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                    if ($ec_of_l2 == '' && $l2_fname == '' && $l2_lname == '' && $l2_email_id == '') {
                        $manager2Flag = 0;
                    } else {
                        if ($ec_of_l2 != '') {
                            // $Emp_idDuplicateCheck2 = $this->device_users_model->DuplicateEmployeeCode($ec_of_l2);
                            // if (count((array) $Emp_idDuplicateCheck2) > 0) {
                            //     $SuccessFlag = 0;
                            //     $Message .= "Row No. $row, EC of L2 Code Already exists.!<br/>";
                            //     $manager2Flag = 0;
                            //     continue;
                            // }
                        } else {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row, EC of L2 Code is Empty. </br>";
                            $manager2Flag = 0;
                            continue;
                        }
                        if ($l2_fname == '') {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row, L2 First Name is Empty. <br/>";
                            $manager2Flag = 0;
                            continue;
                        }
                        if ($l2_lname == '') {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row, L2 Last Name is Empty. <br/>";
                            $manager2Flag = 0;
                            continue;
                        }
                        if ($l2_email_id != '') {
                            $MangEmailDuplicateCheck2 = $this->common_model->get_value('company_users', 'userid', "email LIKE '" . $l2_email_id . "' AND emp_id NOT LIKE '" . $ec_of_l2 . "'");
                            if (count((array) $MangEmailDuplicateCheck2) > 0) {
                                $SuccessFlag = 0;
                                $Message .= "Row No. $row, L2 Email ID Already exists.!<br/>";
                                $manager2Flag = 0;
                                continue;
                            }
                        } else {
                            $SuccessFlag = 0;
                            $Message .= "Row No. $row, L2 Email ID is Empty. </br>";
                            $manager2Flag = 0;
                            continue;
                        }
                    }
                }
            }
            if ($SuccessFlag) {
                $now = date('Y-m-d H:i:s');
                $Counter = 0;
                for ($row = 3; $row <= $highestRow; $row++) {
                    $manager1Flag = 1;
                    $manager2Flag = 1;
                    $cms_id1 = 0;
                    $cms_id2 = 0;
                    $First_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $pwd = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                    $dept = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                    $region = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
                    $designationId = 0;
                    $regionId = 0;
                    $division_id = 0;
                    if ($region != '') {
                        $this->db->select('id')->from('region')->where('region_name LIKE', $region);
                        if ($Company_id != "") {
                            $this->db->where('company_id', $Company_id);
                        }
                        $region_set = $this->db->get()->row();
                        if (count((array) $region_set) > 0) {
                            $regionId = $region_set->id;
                        }
                    }
                    if ($dept != '') {
                        $this->db->select('id')->from('division_mst')->where('division_name LIKE', $dept);
                        if ($Company_id != "") {
                            $this->db->where('company_id', $Company_id);
                        }
                        $division_set = $this->db->get()->row();
                        if (count((array) $division_set) > 0) {
                            $division_id = $division_set->id;
                        }
                    }
                    $designation = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
                    if ($designation != '') {
                        $this->db->select('id')->from('designation_trainee')->where('description LIKE', $designationchk);
                        if ($Company_id != "") {
                            $this->db->where('company_id', $Company_id);
                        }
                        $designation_set = $this->db->get()->row();
                        if (count((array) $designation_set) > 0) {
                            $designationId = $designation_set->id;
                        }
                    }
                    $Counter++;
                    // CMS user insert code here 
                    $ec_of_l1 = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $l1_fname = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                    $l1_lname = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    $l1_email_id = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                    if ($ec_of_l1 == '' && $l1_fname == '' && $l1_lname == '' && $l1_email_id == '') {
                        $manager1Flag = 0;
                    }
                    if ($manager1Flag) {
                        $check_cms_user = $this->device_users_model->DuplicateEmployeeCode($ec_of_l1);
                        if (count((array) $check_cms_user) > 0) {
                            $cms_id1 = $check_cms_user['0']->userid;
                            // continue;
                            // $data_cmp = array(
                            //     'company_id'          => $Company_id,
                            //     'username'            => $ec_of_l1,
                            //     'password'            => $pwd,
                            //     'login_type'          => 1,
                            //     'role'                => 2,
                            //     'designation_id'      => 13,
                            //     'region_id'           => $regionId,
                            //     'salutation'          => '',
                            //     'division_id'         => $division_id,
                            //     'first_name'          => $l1_fname,
                            //     'last_name'           => $l1_lname,
                            //     'email'               => $l1_email_id,
                            //     'mobile'              => '',
                            //     'contactno'           => '',
                            //     'status'              => 1,
                            //     'addeddate'           => $now,
                            //     'addedby'             => $this->mw_session['user_id'],
                            //     'deleted'             => 0,
                            //     'userrights_type'     => 1,
                            //     'workshoprights_type' => 1
                            // );
                            // $this->common_model->update('company_users', 'userid', $cms_id1, $data_cmp);
                        } else {
                            $data_cmp = array(
                                'company_id' => $Company_id,
                                'emp_id' => strtoupper($ec_of_l1),
                                'username' => $ec_of_l1,
                                'password' => $this->common_model->encrypt_password($pwd),
                                'login_type' => 1,
                                'role' => 2,
                                'designation_id' => '',
                                'region_id' => $regionId,
                                'salutation' => '',
                                'department' => $dept,
                                'division_id' => $division_id,
                                'first_name' => $l1_fname,
                                'last_name' => $l1_lname,
                                'email' => $l1_email_id,
                                'mobile' => '',
                                'contactno' => '',
                                'status' => 1,
                                'addeddate' => $now,
                                'addedby' => $this->mw_session['user_id'],
                                'deleted' => 0,
                                'userrights_type' => 1,
                                'workshoprights_type' => 1
                            );
                            $cms_id1 = $this->common_model->insert('company_users', $data_cmp);
                        }
                    }

                    // CMS user2 insert code here 
                    $ec_of_l2 = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    $l2_fname = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                    $l2_lname = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                    $l2_email_id = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                    if ($ec_of_l2 == '' && $l2_fname == '' && $l2_lname == '' && $l2_email_id == '') {
                        $manager2Flag = 0;
                    }
                    if ($manager2Flag) {
                        $check_cms_user2 = $this->device_users_model->DuplicateEmployeeCode($ec_of_l2);
                        if (count((array) $check_cms_user2) > 0) {
                            $cms_id2 = $check_cms_user2['0']->userid;
                            // continue;
                            // $data_cmp = array(
                            //     'company_id'          => $Company_id,
                            //     'username'            => $ec_of_l2,
                            //     'password'            => $pwd,
                            //     'login_type'          => 1,
                            //     'role'                => 2,
                            //     'designation_id'      => 13,
                            //     'region_id'           => $regionId,
                            //     'salutation'          => '',
                            //     'division_id'         => $division_id,
                            //     'first_name'          => $l2_fname,
                            //     'last_name'           => $l2_lname,
                            //     'email'               => $l2_email_id,
                            //     'mobile'              => '',
                            //     'contactno'           => '',
                            //     'status'              => 1,
                            //     'addeddate'           => $now,
                            //     'addedby'             => $this->mw_session['user_id'],
                            //     'deleted'             => 0,
                            //     'userrights_type'     => 1,
                            //     'workshoprights_type' => 1
                            // );
                            // $this->common_model->update('company_users', 'userid', $cms_id2, $data_cmp);
                        } else {
                            $data_cmp = array(
                                'company_id' => $Company_id,
                                'emp_id' => strtoupper($ec_of_l2),
                                'username' => $ec_of_l2,
                                'password' => $this->common_model->encrypt_password($pwd),
                                'login_type' => 1,
                                'role' => 2,
                                'designation_id' => '',
                                'region_id' => $regionId,
                                'salutation' => '',
                                'department' => $dept,
                                'division_id' => $division_id,
                                'first_name' => $l2_fname,
                                'last_name' => $l2_lname,
                                'email' => $l2_email_id,
                                'mobile' => '',
                                'contactno' => '',
                                'status' => 1,
                                'addeddate' => $now,
                                'addedby' => $this->mw_session['user_id'],
                                'deleted' => 0,
                                'userrights_type' => 1,
                                'workshoprights_type' => 1
                            );
                            $cms_id2 = $this->common_model->insert('company_users', $data_cmp);
                        }
                    }
                    if ($SuccessFlag) {
                        $Emp_code = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        if ($Emp_code != '') {
                            $data = array(
                                'company_id' => $Company_id,
                                'emp_id' => strtoupper($worksheet->getCellByColumnAndRow(1, $row)->getValue()),
                                'firstname' => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
                                'lastname' => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                                'email' => $worksheet->getCellByColumnAndRow(12, $row)->getValue(),
                                'password' => $this->common_model->encrypt_password($pwd),
                                'mobile' => $worksheet->getCellByColumnAndRow(14, $row)->getValue(),
                                'employment_year' => $worksheet->getCellByColumnAndRow(15, $row)->getValue(),
                                'education_background' => $worksheet->getCellByColumnAndRow(16, $row)->getValue(),
                                'department' => $worksheet->getCellByColumnAndRow(17, $row)->getValue(),
                                'region_id' => $regionId,
                                'designation_id' => $designationId,
                                'otp_verified' => 1,
                                'area' => $worksheet->getCellByColumnAndRow(19, $row)->getValue(),
                                'trainer_id' => $cms_id1,
                                'trainer_id_i' => $cms_id2,
                                'status' => 1,
                                'registration_date' => $now,
                                'addeddate' => $now,
                                'addedby' => $this->mw_session['user_id'],
                            );
                            $Inserted_id = $this->common_model->insert('device_users', $data);
                        }
                        if ($Inserted_id != "") {
                            $Udata = array('token' => $Inserted_id . "." . base64_encode(openssl_random_pseudo_bytes(32)));
                            $this->common_model->update('device_users', 'user_id', $Inserted_id, $Udata);

                            $data2 = array(
                                'company_id' => $Company_id,
                                'emp_id' => strtoupper($worksheet->getCellByColumnAndRow(1, $row)->getValue()),
                                'user_id' => $Inserted_id,
                                'firstname' => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
                                'lastname' => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                                'email' => $worksheet->getCellByColumnAndRow(12, $row)->getValue(),
                                'password' => $this->common_model->encrypt_password($pwd),
                                'mobile' => $worksheet->getCellByColumnAndRow(14, $row)->getValue(),
                                'otp_verified' => 1,
                                'status' => 1,
                                'registration_date' => $now,
                                'addeddate' => $now,
                                'addedby' => $this->mw_session['user_id']
                            );
                            $this->common_model->insert_db2('device_users', $data2);
                            $this->device_users_model->update_userdb2($Inserted_id, $Company_id, $Udata);
                        }
                    }
                }
                $Message = $Counter . " Device Users Imported successfully.";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    // Bhautik Rana Import user Data || 04-07-2023 Start
    public function import_userssamplexls()
    {
        //  $this->load->library('PHPExcel_CI');
        // $Excel = new PHPExcel_CI;
        $Excel = new Spreadsheet();
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('User_List');
        $Excel->createSheet();
        $Excel->getActiveSheet()
            ->setCellValue('A1', 'Do not modify or delete the Columns.');
        $Excel->getActiveSheet()->getStyle('A1:C1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
        //merge cell A1 until D1
        $Excel->getActiveSheet()->mergeCells('A1:C1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $Excel->getActiveSheet()
            // ->setCellValue('A2', 'User Id*')
            ->setCellValue('A2', 'Employee Code*')
            ->setCellValue('B2', 'Email*')
            ->setCellValue('C2', 'Status* (Active/Inactive)');
        $Excel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:C2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('B')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('C')->setWidth("30");
        // $Excel->getActiveSheet()->getColumnDimension('D')->setWidth("30");
        $Excel->getActiveSheet()->getStyle('A2:C2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('eb3a12');
        //set aligment to center for that merged cell (A1 to D1)
        $filename = "Device_User_Status.xls";
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //$objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($Excel, "Xls");
        //force user to download the Excel file without writing it to server's HD
        if (ob_get_length())
            ob_end_clean();
        $objWriter->save('php://output');
        //unload($objWriter);
        unset($objWriter);
    }
    public function UploadXls_user()
    {
        $Message = '';
        $SuccessFlag = 1;
        $this->load->model('company_model');
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                // $Company_id = $this->security->xss_clean($this->input->post('company_id'));
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('filename', '', 'callback_file_check');
        }
        if ($this->form_validation->run() == FALSE) {
            $Message = validation_errors();
            $SuccessFlag = 0;
        } else {
            $FileData = $_FILES['filename'];
            //$this->load->library('PHPExcel_CI');
            //$objPHPExcel = PHPExcel_IOFactory::load($FileData['tmp_name']);
            $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($FileData['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumm = $worksheet->getHighestColumn();
            //$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumm);
            //print_r($highestColumnIndex);
            if ($highestRow < 3) {
                $Message .= "Excel row/column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($highestRow == 2) {
                $Message .= "Excel file cannot be empty.";
                $SuccessFlag = 0;
            }
            if ($highestColumnIndex < 3) {
                $Message .= "Excel column mismatch,Please download sample file.";
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                $Counter = 0;
                for ($row = 3; $row <= $highestRow; $row++) {
                    // $User_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    // if ($User_id == '') {
                    //     //print_r($row);
                    //     $SuccessFlag = 0;
                    //     $Message .= "Row No. $row, User Id is Empty. </br> ";
                    //     continue;
                    // }
                    $emp_code = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    if ($emp_code == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Employee Code is Empty. </br> ";
                        continue;
                    }
                    $email = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    if ($email == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, Email is Empty. </br> ";
                        continue;
                    }
                    $status = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    if ($status == '') {
                        $SuccessFlag = 0;
                        $Message .= "Row No. $row, status is Empty. </br> ";
                        continue;
                    }
                    $this->db->select('user_id,email')->from('device_users');
                    $this->db->where('email', $email);
                    if ($Company_id != "") {
                        $this->db->where('company_id', $Company_id);
                    }
                    $this->db->where('emp_id', $emp_code);
                    $EmailDuplicateCheck = $this->db->get()->row();
                    if (count((array) $EmailDuplicateCheck) > 0) {
                        $status_id = trim($status) == 'Inactive' ? 0 : 1;
                        $userid = $EmailDuplicateCheck->user_id;
                        $Counter++;
                        $data = array(
                            'status' => $status_id,
                            'modifieddate' => date('Y-m-d H:i:s'),
                            'modifiedby' => $this->mw_session['user_id']
                        );
                        $this->common_model->update('device_users', 'user_id', $userid, $data);
                        $this->device_users_model->update_userdb2($userid, $Company_id, $data);
                        $Message = $Counter . " Device Users status updated successfully.</br> ";
                    } else {
                        $Message .= "Row No. $row, Record Not Found...!! </br> ";
                        $SuccessFlag = 0;
                    }
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }


    function validate_mobile($mobile)
    {
        return preg_match('/^[0-9]{10}+$/', $mobile);
    }

    public function file_check($str)
    {
        $allowed_mime_type_arr = array('application/excel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/octet-stream');
        $mime = $_FILES['filename']['type'];
        if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != "") {
            if (in_array($mime, $allowed_mime_type_arr)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only .xlsx or.xls file.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please select xls to import.');
            return false;
        }
    }

    public function edit($id)
    {
        $user_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('device_users');
            return;
        }
        $data['module_id'] = '22.1';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->device_users_model->fetch_user($user_id);
        $this->db->select('cu.userid,cu.emp_id,CONCAT(cu.first_name, " ",cu.last_name) as manager_name');
        $this->db->from('company_users as cu');
        $this->db->where('cu.status', '1');
        $this->db->where('cu.company_id', $this->mw_session['company_id']);
        $data['ManagerData'] = $this->db->get()->result();
        $data['device_info'] = $this->device_users_model->user_device_info($user_id);
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        if ($data['result']->company_id != "") {
            $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 and company_id=' . $data['result']->company_id);
            $data['DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'status=1 and company_id=' . $data['result']->company_id);
        }
        $this->load->view('device_users/edit', $data);
    }

    public function view($id)
    {
        $user_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('device_users');
            return;
        }
        $this->load->helper('form');
        $data['module_id'] = '22.1';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->device_users_model->fetch_user($user_id);
        $data['device_info'] = $this->device_users_model->user_device_info($user_id);
        $this->db->select('cu.userid,cu.emp_id,CONCAT(cu.first_name, " ",cu.last_name) as manager_name');
        $this->db->from('company_users as cu');
        $this->db->where('cu.status', '1');
        $this->db->where('cu.company_id', $this->mw_session['company_id']);
        $data['ManagerData'] = $this->db->get()->result();
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        if ($data['result']->company_id != "") {
            $data['RegionData'] = $this->common_model->get_selected_values('region', 'id,region_name', 'status=1 and company_id=' . $data['result']->company_id);
            $data['DesignationData'] = $this->common_model->get_selected_values('designation_trainee', 'id,description', 'status=1 and company_id=' . $data['result']->company_id);
        }
        $this->load->view('device_users/view', $data);
    }

    public function userssamplexls()
    {
        //  $this->load->library('PHPExcel_CI');
        // $Excel = new PHPExcel_CI;
        $Excel = new Spreadsheet();
        $Excel->setActiveSheetIndex(0);
        $Excel->getActiveSheet()->setTitle('User_List');
        $Excel->createSheet();
        $Excel->getActiveSheet()
            ->setCellValue('A1', 'Do not modify or delete the Columns.');
        /*  $Excel->getActiveSheet()->getStyle('A1:D1')->getFill()->applyFromArray(array(
              //'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
              'startcolor' => array(
                  'rgb' => 'FF0000'
              )
          ));*/
        $Excel->getActiveSheet()->getStyle('A1:D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');
        //merge cell A1 until D1
        $Excel->getActiveSheet()->mergeCells('A1:D1');
        $styleArray = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'ffffff'),
                'size' => 11,
                'name' => 'Calibri'
            )
        );
        $Excel->getActiveSheet()
            ->setCellValue('A2', 'Employee Code*')
            ->setCellValue('B2', 'First Name*')
            ->setCellValue('C2', 'Last Name*')
            ->setCellValue('D2', 'EC of L1')
            ->setCellValue('E2', 'L1 First name')
            ->setCellValue('F2', 'L1 Last Name')
            ->setCellValue('G2', 'L1 Email ID')
            ->setCellValue('H2', 'EC of L2')
            ->setCellValue('I2', 'L2 First Name')
            ->setCellValue('J2', 'L2 Last Name')
            ->setCellValue('K2', 'L2 Email ID')
            ->setCellValue('L2', 'Email*')
            ->setCellValue('M2', 'Password*')
            ->setCellValue('N2', 'Mobile No.')
            ->setCellValue('O2', 'Employment Year')
            ->setCellValue('P2', 'Education Background')
            ->setCellValue('Q2', 'Department/Division*')
            ->setCellValue('R2', 'Region/Branch*')
            ->setCellValue('S2', 'Area')
            ->setCellValue('T2', 'Designation');


        $Excel->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getStyle("A2:T2")->applyFromArray($styleArray);
        $Excel->getActiveSheet()->getColumnDimension('A')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('B')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('C')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('D')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('E')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('F')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('G')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('H')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('I')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('J')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('K')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('L')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('M')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('N')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('O')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('P')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('Q')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('R')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('S')->setWidth("30");
        $Excel->getActiveSheet()->getColumnDimension('T')->setWidth("30");
        /*$Excel->getActiveSheet()->getStyle('A2:L2')->getFill()->applyFromArray(array(
            //'type' => PHPExcel_Style_Fill::FILL_SOLID,        
            'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'eb3a12'
            )
        ));*/
        $Excel->getActiveSheet()->getStyle('A2:T2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('eb3a12');
        //set aligment to center for that merged cell (A1 to D1)
        $filename = "Device_User_Import.xls";
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //$objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($Excel, "Xls");
        //force user to download the Excel file without writing it to server's HD
        if (ob_get_length())
            ob_end_clean();
        $objWriter->save('php://output');
        //unload($objWriter);
        unset($objWriter);
    }

    public function DatatableRefresh()
    {
        $dtSearchColumns = array('u.user_id', 'u.user_id', 'c.company_name', 'u.emp_id', 'CONCAT(u.firstname, " ",u.lastname)', 'u.email', 'CONCAT(cu.first_name," ",cu.last_name)', 'CONCAT(cu2.first_name," ",cu2.last_name)', 'rg.region_name', 'u.designation', 'u.registration_date', 'u.status');
        // $dtSearchColumns = array('u.user_id','u.user_id','c.company_name','u.emp_id','CONCAT(u.firstname, " ",u.lastname)','u.email','rg.region_name','designation','u.registration_date','u.status');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];
        // if ($this->input->get('sSearch') != '') {
        //     $dtWhere .= ($dtWhere <> '') ? " AND " : " WHERE ";
        // 	// $dtWhere .= " CONCAT(u.firstname,' ',u.lastname) LIKE '%".$this->input->get('sSearch')."%'";
        //     $dtWhere .= "u.user_id LIKE '%".$this->input->get('sSearch')."%' OR u.emp_id LIKE '%".$this->input->get('sSearch')."%' OR CONCAT(u.firstname,' ',u.lastname) LIKE '%".$this->input->get('sSearch')."%' OR u.email LIKE '%".$this->input->get('sSearch')."%' OR rg.region_name LIKE '%".$this->input->get('sSearch')."%' OR DATE_FORMAT(u.registration_date,'%d-%m-%Y') LIKE '%".$this->input->get('sSearch')."%'  ";
        //     // $search_Result = explode(' ', trim($this->input->get('sSearch')));
        //     // if (count((array)$search_Result) > 1) {
        //         // $dtWhere = " WHERE ((u.firstname like '%" . $search_Result[0] . "%' AND u.lastname like '%" . $search_Result[1] . "%')) ";
        //     // }
        // }
        $status = $this->input->get('status');
        if ($status != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.status  = '" . $status . "'";
            } else {
                $dtWhere .= " WHERE u.status  = '" . $status . "'";
            }
        }
        if ($this->mw_session['company_id'] == "") {
            $company_id = ($this->input->get('company_id') ? $this->input->get('company_id') : '');
            if ($company_id != "") {
                if ($dtWhere <> '') {
                    $dtWhere .= " AND u.company_id  = " . $company_id;
                } else {
                    $dtWhere .= " WHERE u.company_id  = " . $company_id;
                }
            }
        } else {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.company_id  = " . $this->mw_session['company_id'];
            } else {
                $dtWhere .= " WHERE u.company_id  = " . $this->mw_session['company_id'];
            }
        }
        $Start_date = ($this->input->get('start_date') ? $this->input->get('start_date') : '');
        $End_date = ($this->input->get('end_date') ? $this->input->get('end_date') : '');
        if ($Start_date != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND u.registration_date between '" . date('Y-m-d', strtotime($Start_date)) .
                    "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
            } else {
                $dtWhere .= " WHERE u.registration_date between '" . date('Y-m-d', strtotime($Start_date)) .
                    "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
            }
        }
        $dtWhere .= " AND u.istester=0 ";
        $DTRenderArray = $this->device_users_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        // $dtDisplayColumns = array('checkbox', 'user_id', 'company_name', 'name', 'email', 'otp', 'otp_last_attempt', 'registration_date', 'status', 'Actions');
        $dtDisplayColumns = array('checkbox', 'user_id', 'company_name', 'emp_id', 'name', 'email', 'l1_manager', 'l2_manager', 'region_name', 'designation', 'registration_date', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array) $dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "otp_last_attempt") {
                    $row[] = ($dtRow['otp_last_attempt'] != '' ? date('d-m-Y H:i', strtotime($dtRow['otp_last_attempt'])) : '');
                } else if ($dtDisplayColumns[$i] == "status") {
                    if ($dtRow['status'] == 1) {
                        $status = '<span class="label label-sm label-info status-active" > Active </span>';
                    } else {
                        $status = '<span class="label label-sm label-danger status-inactive" > In Active </span>';
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['user_id'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "l1_manager") {
                    $row[] = $dtRow['l1_empid'] != "" ? "[" . $dtRow['l1_empid'] . "] " . $dtRow['l1_manager'] : $dtRow['l1_manager'];
                } else if ($dtDisplayColumns[$i] == "l2_manager") {
                    $row[] = $dtRow['l2_empid'] != "" ? "[" . $dtRow['l2_empid'] . "] " . $dtRow['l2_manager'] : $dtRow['l2_manager'];
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if ($acces_management->allow_view or $acces_management->allow_edit or $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_view) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'device_users/view/' . base64_encode($dtRow['user_id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'device_users/edit/' . base64_encode($dtRow['user_id']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_delete) {
                            $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\'' . base64_encode($dtRow['user_id']) . '\');" href="javascript:void(0)">
                                        <i class="fa fa-trash-o"></i>&nbsp;Delete
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_view) {
                            $action .= '<li ></li><li>
                                        <a onclick="LoadDeviceInfo(\'' . base64_encode($dtRow['user_id']) . '\');" href="javascript:void(0)">
                                        <i class="fa fa-mobile"></i>&nbsp;Device Info
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

    public function submit()
    {
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('first_name', 'First name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('last_name', 'Last name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[50]|valid_email');
            $this->form_validation->set_rules('l1_manager', 'L1 Manager', 'required');
            $this->form_validation->set_rules('l2_manager', 'L2 Manager', 'required');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|max_length[50]');
            // $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('depart', 'Department', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('region_id', 'Region', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('confirmpassword', 'Confirm password', 'trim|required|max_length[50]');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {
                $Email = $this->input->post('email');
                $DuplicateFlag = $this->common_model->DuplicateEmail($Email, $Company_id);
                if (count((array) $DuplicateFlag) > 0) {
                    $Message = "Email ID already exists.!!!";
                    $SuccessFlag = 0;
                }
                if ($SuccessFlag) {
                    $now = date('Y-m-d H:i:s');
                    $pwd = $this->input->post('password');
                    $data = array(
                        'password' => $this->common_model->encrypt_password($pwd),
                        'company_id' => $Company_id,
                        'firstname' => ucwords($this->input->post('first_name')),
                        'lastname' => ucwords($this->input->post('last_name')),
                        'email' => $Email,
                        'mobile' => $this->input->post('mobile'),
                        'registration_date' => $now,
                        'otp_verified' => 1,
                        'block' => 0,
                        'fb_registration' => 1,
                        'status' => $this->input->post('status'),
                        'employment_year' => $this->input->post('empyear'),
                        'education_background' => $this->input->post('edubg'),
                        'department' => $this->input->post('depart'),
                        'region_id' => $this->input->post('region_id'),
                        'trainer_id' => $this->input->post('l1_manager'),
                        'trainer_id_i' => $this->input->post('l2_manager'),
                        'designation_id' => $this->input->post('designation_id'),
                        'area' => $this->input->post('area'),
                        'emp_id' => strtoupper($this->input->post('emp_id')),
                        'addeddate' => $now,
                        'addedby' => $this->mw_session['user_id'],
                    );
                    $Insert_ID = $this->common_model->insert('device_users', $data);
                    if ($Insert_ID != '') {
                        $data = array(
                            'password' => $this->common_model->encrypt_password($pwd),
                            'company_id' => $Company_id,
                            'user_id' => $Insert_ID,
                            'firstname' => ucwords($this->input->post('first_name')),
                            'lastname' => ucwords($this->input->post('last_name')),
                            'email' => $Email,
                            'mobile' => $this->input->post('mobile'),
                            'registration_date' => $now,
                            'otp_verified' => 1,
                            'block' => 0,
                            'fb_registration' => 1,
                            'status' => $this->input->post('status'),
                            'addeddate' => $now,
                            'addedby' => $this->mw_session['user_id']
                        );
                        $this->common_model->insert_db2('device_users', $data);
                        $Message = "Device user created successfully.";
                        $Rdata['id'] = base64_encode($Insert_ID);
                    } else {
                        $Message = "Error while creating User,Contact Mediaworks for technical support.!";
                        $SuccessFlag = 0;
                    }
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function update($Encode_id)
    {
        $id = base64_decode($Encode_id);
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message = "You have no rights to Edit,Contact Administrator for rights.";
        } else {
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('first_name', 'First name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('last_name', 'Last name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[50]|valid_email');
            $this->form_validation->set_rules('l1_manager', 'L1 Manager', 'required');
            $this->form_validation->set_rules('l2_manager', 'L2 Manager', 'required');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|max_length[50]');
            // $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('depart', 'Department', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('region_id', 'Region', 'trim|required|max_length[50]');
            $Email = $this->input->post('email');
            $DuplicateFlag = $this->common_model->DuplicateEmail($Email, $Company_id, $id);
            if (count((array) $DuplicateFlag) > 0) {
                $Message = "Email ID already exists.!!!";
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                $now = date('Y-m-d H:i:s');
                $Block = $this->input->post('block');
                $fb_registBlock = $this->input->post('fb_registration');
                $data = array(
                    'company_id' => $Company_id,
                    'emp_id' => strtoupper($this->input->post('emp_id')),
                    'firstname' => ucwords($this->input->post('first_name')),
                    'lastname' => ucwords($this->input->post('last_name')),
                    'email' => $this->input->post('email'),
                    'trainer_id' => $this->input->post('l1_manager'),
                    'trainer_id_i' => $this->input->post('l2_manager'),
                    'mobile' => $this->input->post('mobile'),
                    'employment_year' => $this->input->post('empyear'),
                    'education_background' => $this->input->post('edubg'),
                    'department' => $this->input->post('depart'),
                    'region_id' => $this->input->post('region_id'),
                    'designation_id' => $this->input->post('designation_id'),
                    // 'block' => (isset($Block) ? 1 : 0),
                    // 'fb_registration' => (isset($fb_registBlock) ? 1 : 0),
                    'area' => $this->input->post('area'),
                    'status' => $this->input->post('status'),
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']
                );
                $new_password = '';
                if ($this->input->post('tpassword') != '') {
                    $new_password = $this->common_model->encrypt_password($this->input->post('tpassword'));
                    $data['password'] = $new_password;
                }
                $this->common_model->update('device_users', 'user_id', $id, $data);
                if ($id != '') {
                    $data2 = array(
                        'firstname' => ucwords($this->input->post('first_name')),
                        'lastname' => ucwords($this->input->post('last_name')),
                        'email' => $this->input->post('email'),
                        'mobile' => $this->input->post('mobile'),
                        'status' => $this->input->post('status'),
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']
                    );
                    if ($new_password != '') {
                        $data2['password'] = $new_password;
                    }
                    $this->device_users_model->update_userdb2($id, $Company_id, $data2);
                    $Message = "Device user data updated successfully.";
                } else {
                    $Message = "Error while updating user,Contact Mediaworks for technical support.!";
                    $SuccessFlag = 0;
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function remove()
    {
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $deleted_id = base64_decode($this->input->Post('deleteid'));
            $OldData = $this->common_model->get_value('workshop_registered_users', 'id', "user_id=" . $deleted_id);
            if (count((array) $OldData) == 0) {
                $OldData = $this->common_model->get_value('assessment_allow_users', 'id', "user_id=" . $deleted_id);
            }
            if ($this->mw_session['company_id'] == "") {
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            if (count((array) $OldData) > 0) {
                $message = "User cannot be deleted. Reference of User found in other module!<br/>";
                $alert_type = 'error';
            } else {
                $this->device_users_model->delete_user($Company_id, $deleted_id);
                $message = "Device User deleted successfully.";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function record_actions($Action)
    {
        $action_id = $this->input->Post('id');
        if (count((array) $action_id) == 0) {
            echo json_encode(array('message' => "Please select record from the list", 'alert_type' => 'error'));
            exit;
        }
        $now = date('Y-m-d H:i:s');
        $alert_type = 'success';
        $message = '';
        if ($this->mw_session['company_id'] == "") {
            $Company_id = $this->input->post('company_id');
        } else {
            $Company_id = $this->mw_session['company_id'];
        }
        if ($Action == 1) {
            foreach ($action_id as $id) {
                $data = array(
                    'status' => 1,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']
                );
                $this->common_model->update('device_users', 'user_id', $id, $data);
                $this->device_users_model->update_userdb2($id, $Company_id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = true;
            foreach ($action_id as $id) {
                $userData = $this->common_model->get_value('assessment_allow_users', 'id', "user_id=" . $id);
                if (count((array) $userData) == 0) {
                    // if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']
                    );
                    $this->common_model->update('device_users', 'user_id', $id, $data);
                    $this->device_users_model->update_userdb2($id, $Company_id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $SuccessFlag = false;
                    $message = "Status cannot be change. User(s) assigned to role!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $OldData = $this->common_model->get_value('workshop_registered_users', 'id', "user_id=" . $id);
                if (count((array) $OldData) == 0) {
                    $OldData = $this->common_model->get_value('assessment_allow_users', 'id', "user_id=" . $id);
                }
                if (count((array) $OldData) == 0) {
                    $this->device_users_model->delete_user($Company_id, $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $SuccessFlag = false;
                    $message = "User cannot be deleted. Reference of User found in other module!<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'User(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function validate()
    {
        $status = $this->device_users_model->validate($this->input->post());
        echo $status;
    }

    public function Check_emailid()
    {
        $emailid = $this->input->post('email_id', true);
        $user = $this->input->post('user', true);
        if ($user != "") {
            $user = base64_decode($user);
        }
        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->post('company_id', TRUE);
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        $EmailDuplicateCheck = $this->common_model->DuplicateEmail($emailid, $cmp_id, $user);
        echo (count((array) $EmailDuplicateCheck) > 0 ? true : false);
    }

    public function validate_edit()
    {
        $status = $this->device_users_model->validate_edit($this->input->post());
        echo $status;
    }

    public function temp_avatar_upload()
    {
        $crop = new CropAvatar(
            $this->input->post('avatar_src'), $this->input->post('avatar_data'), $_FILES['avatar_file']
        );
        $response = array(
            'state' => 200,
            'message' => $crop->getMsg(),
            'result' => $crop->getResult(),
            'preview' => $crop->getTemporery()
        );

        echo json_encode($response);
    }

    public function send_otp($type)
    {
        //$Company_id = base64_decode($Encode_id);
        $action_id = $this->input->Post('id');
        if ($type == 1) {
            $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='on_otp_request'");
        } else {
            $emailTemplate = $this->common_model->get_value('auto_emails', '*', "status=1 and alert_name='on_otc_request'");
        }

        $SuccessFlag = 1;
        $Message = '';
        if (count((array) $emailTemplate) > 0) {
            foreach ($action_id as $id) {
                $UserData = $this->common_model->get_value('device_users', 'firstname,email,company_id', " user_id=" . $id);
                $CompanyData = $this->common_model->get_value('company', 'otp', "id=" . $UserData->company_id);
                //$six_digit_otp = mt_rand(100000, 999999);
                if (count((array) $UserData) > 0) {
                    $pattern[0] = '/\[CUSTOMER_NAME\]/';
                    $pattern[1] = '/\[ONETIME_CODE\]/';
                    $pattern[2] = '/\[SUBJECT\]/';
                    $replacement[0] = $UserData->firstname;
                    if ($type == 1) {
                        $six_digit_otp = mt_rand(100000, 999999);
                    } else {
                        $six_digit_otp = $CompanyData->otp;
                    }
                    $replacement[1] = $six_digit_otp;
                    $subject = $emailTemplate->subject;
                    $replacement[2] = $subject;
                    $message = $emailTemplate->message;
                    $body = preg_replace($pattern, $replacement, $message);
                    //$ToName ="Sameer Mansuri";
                    //$recipient ="sameer@mworks.in";
                    $ToName = $UserData->firstname;
                    $recipient = $UserData->email;
                    $Company_id = $UserData->company_id;
                    $ReturnArray = $this->common_model->sendPhpMailer($Company_id, $ToName, $recipient, $subject, $body);
                    if ($ReturnArray['sendflag']) {
                        if ($type == 1) {
                            $Message = "OTP send successfully.";
                            $data = array(
                                'otp' => $six_digit_otp,
                                'modifieddate' => date('Y-m-d H:i:s'),
                                'otp_last_attempt' => date('Y-m-d H:i:s'),
                                'modifiedby' => $this->mw_session['user_id']
                            );
                            $this->common_model->update(' device_users', 'user_id', $id, $data);
                        } else {
                            $Message = "OTC send successfully.";
                        }
                    } else {
                        $Message .= "Error while sending email,Plese try again..";
                        $Message .= '<br/>' . $ReturnArray['sendflag'];
                        $SuccessFlag = 0;
                    }
                } else {
                    $Message .= " some user data not found..";
                }
            }
        } else {
            $SuccessFlag = 0;
            $Message = 'Email Template not defined,Contact Adminstrator for technical support';
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }

    public function export_trainee()
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
            $dtWhere .= " AND u.company_id  = " . $Company_id;
        }

        $status = $this->input->post('status', true);
        if ($status != "") {
            $dtWhere .= " AND u.status  = '" . $status . "'";
        }
        $Start_date = ($this->input->post('start_date') ? $this->input->post('start_date') : '');
        $End_date = ($this->input->post('end_date') ? $this->input->post('end_date') : '');
        if ($Start_date != "") {
            $dtWhere .= " AND u.registration_date between '" . date('Y-m-d', strtotime($Start_date)) .
                "' AND '" . date('Y-m-d', strtotime($End_date)) . "'";
        }
        $id_list = $this->input->post('id', true);
        if (count((array) $id_list) > 0) {
            $dtWhere .= " AND u.user_id IN(" . implode(',', $id_list) . ")";
        }
        $export_type = $this->input->post('export_type', true);

        $this->load->library('PHPExcel_CI');
        $objPHPExcel = new PHPExcel_CI();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', $Heading)
            ->setCellValue('A2', 'Trainee Id')
            ->setCellValue('B2', 'Employee Code')
            ->setCellValue('C2', 'First Name')
            ->setCellValue('D2', 'Last Name')
            ->setCellValue('E2', 'Email')
            ->setCellValue('F2', 'L1 Manager')
            ->setCellValue('G2', 'L2 Manager')
            ->setCellValue('H2', 'Mobile No.')
            ->setCellValue('I2', 'Employment Year')
            ->setCellValue('J2', 'Education Background')
            ->setCellValue('K2', 'Department/Division')
            ->setCellValue('L2', 'Region/Branch')
            ->setCellValue('M2', 'Designation')
            ->setCellValue('N2', 'Registration Date')
            ->setCellValue('O2', 'Area')
            ->setCellValue('P2', 'Status');
        if ($export_type == 2) {
            $objPHPExcel->getActiveSheet()
                ->setCellValue('Q2', 'Platform')
                ->setCellValue('R2', 'Model')
                ->setCellValue('S2', 'IMEI')
                ->setCellValue('T2', 'Serial No')
                ->setCellValue('U2', 'Device Change Date');
        }
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
        if ($export_type == 2) {
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getStyle('A2:U2')->applyFromArray($styleArray_header);
        } else {
            $objPHPExcel->getActiveSheet()->getStyle('A2:P2')->applyFromArray($styleArray_header);
        }
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);

        $styleArray_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $i = 2;
        $TraineeSet = $this->device_users_model->ExportDeviceUsers($dtWhere, $export_type);
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
                ->setCellValue("F$i", $Trainee->l1_empid != '' ? '[' . $Trainee->l1_empid . '] ' . $Trainee->l1_manager : $Trainee->l1_manager)
                ->setCellValue("G$i", $Trainee->l2_empid != '' ? '[' . $Trainee->l2_empid . '] ' . $Trainee->l2_manager : $Trainee->l2_manager)
                ->setCellValue("H$i", $Trainee->mobile)
                ->setCellValue("I$i", $Trainee->employment_year)
                ->setCellValue("J$i", $Trainee->education_background)
                ->setCellValue("K$i", $Trainee->department)
                ->setCellValue("L$i", $Trainee->region_name)
                ->setCellValue("M$i", $Trainee->designation)
                ->setCellValue("N$i", $Trainee->registration_date)
                ->setCellValue("O$i", $Trainee->area)
                ->setCellValue("P$i", ($Trainee->status ? 'Active' : 'In-Active'));
            if ($export_type == 2) {
                $objPHPExcel->getActiveSheet()
                    ->setCellValue("Q$i", $Trainee->platform)
                    ->setCellValue("R$i", $Trainee->model)
                    ->setCellValue("S$i", $Trainee->imei)
                    ->setCellValue("T$i", $Trainee->serial)
                    ->setCellValue("U$i", ($Trainee->info_dttm != '00-00-0000 12:00 AM' ? $Trainee->info_dttm : ''));
                $objPHPExcel->getActiveSheet()->getStyle("A$i:U$i")->applyFromArray($styleArray_body);
            } else {
                $objPHPExcel->getActiveSheet()->getStyle("A$i:P$i")->applyFromArray($styleArray_body);
            }
        }
        //Set active sheet index to the first sheet, so Excel opens this as the first sheet

        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="DeviceUser.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');
        // Sending headers to force the user to download the file
    }

    public function deviceinfo_data()
    {
        $id = $this->input->post('user_id');
        $user_id = base64_decode($id);
        $device_info = $this->device_users_model->user_device_info($user_id);
        $User = $this->common_model->get_value("device_users", "concat(firstname,' ',lastname) as user_name", "user_id=" . $user_id);
        $data['User'] = $User->user_name;
        $data['device_info'] = $device_info;
        echo $this->load->view('device_users/device_info', $data, TRUE);
    }

    public function imei_primary()
    {
        $msg = '';
        $msg_type = '';
        $user_id = base64_decode($this->input->post('Encode_id', true));
        $imei_id = $this->input->post('imeival', true);
        if ($imei_id != '') {
            $dataY = array('isprimary_imei' => 1);
            $this->common_model->update(' device_info', 'id', $imei_id, $dataY);
            $dataN = array('isprimary_imei' => 0);
            $this->device_users_model->update_imei($imei_id, $dataN, $user_id);
            $msg = "Primary IMEI change successfully";
            $msg_type = 'success';
        } else {
            $msg = "IMEI Primary not set";
            $msg_type = 'error';
        }
        $Rdata['msg_type'] = $msg_type;
        $Rdata['msg'] = $msg;
        echo json_encode($Rdata);
    }

    public function fetch_statistics()
    {
        $company_id = $this->input->post('company_id', true);
        $start_date = $this->input->post('start_date', true);
        $end_date = $this->input->post('end_date', true);
        $box_i_statistics = 0;
        $box_ii_statistics = 0;
        $box_iii_statistics = 0;
        $box_result = $this->device_users_model->count_max_active_user($start_date, $end_date, $company_id);
        if (isset($box_result) and count((array) $box_result) > 0) {
            $box_i_statistics = isset($box_result->total_users) ? $box_result->total_users : 0;
            $box_ii_statistics = isset($box_result->active_users) ? $box_result->active_users : 0;
            $box_iii_statistics = isset($box_result->inactive_users) ? $box_result->inactive_users : 0;
        }
        $response = [
            'box_i_statistics' => $box_i_statistics,
            'box_ii_statistics' => $box_ii_statistics,
            'box_iii_statistics' => $box_iii_statistics,
        ];
        echo json_encode($response);
    }
    // ---------Ajit 13-10-2022 Start ----------
    public function cronjob_device_inactive()
    {
        $current_date = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $date_array = array();
        $user_data = $this->common_model->get_selected_values('device_users', 'user_id,company_id,addeddate', 'status=1', 'user_id');
        if (count((array) $user_data) > 0) {
            foreach ($user_data as $udate) {
                $start_date = date('Y-m-d', strtotime($udate->addeddate));
                $end_date = date('Y-m-d', strtotime("+30 days", strtotime($start_date)));

                // $date1=date_create($start_date); $date2=date_create($end_date);
                // $diff=date_diff($date1,$date2); $no_of_days = $diff->format("%a");

                if (strtotime($current_date) > strtotime($end_date)) {
                    // $date_array[] = $start_date.' to '.$end_date;
                    $inactive_data = array('status' => 0, 'modifieddate' => $now);
                    $this->common_model->update('device_users', 'user_id', $udate->user_id, $inactive_data);
                    $this->device_users_model->update_userdb2($udate->user_id, $udate->company_id, $inactive_data);
                }
            }
        }
    }
    // ---------Ajit 13-10-2022 End ----------

}