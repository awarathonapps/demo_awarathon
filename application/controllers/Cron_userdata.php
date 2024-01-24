<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;

defined('BASEPATH') OR exit('No direct script access allowed');
class Cron_userdata extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('cron_userdata_model');
        $this->load->model('common_model');
        // $this->load->common_model('cron_userdata_model');
        $this->ftp_db = $this->cron_userdata_model->connect_ftp_db();
        $this->common_db = $this->cron_userdata_model->connect_common_db();
    }

    public function index(){
        echo "Welcome to the Awarathon - Sun Pharma";
        exit;
    }

    public function read_excel($company_id=0){
        $filename = "/var/www/html/awarathon.com/csv/training.xlsx";
        // $filename = "/var/www/html/awarathon.com/csv/sunpharmadata.xlsx";
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $worksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $successFlag = 1;
            $emp_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            $level = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
            $email = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

            //validation check
            // if(empty($level)){
            //     $successFlag = 0;  //skip employee data without level mentioned
            // }
            // $is_email_exist = $this->cron_userdata_model->get_value('employee_data', 'id', "email='$email' AND emp_id!='$emp_id'");
            // if($is_email_exist){
            //     $successFlag = 0;   //skip employee data with repeat email address
            // }

            if($successFlag){
                $is_exist = $this->cron_userdata_model->get_value('employee_data', 'id', "emp_id='$emp_id'");
                
                //read datetime from excel file
                $dateObj = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); 
                $doj= \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateObj);

                $data = array(
                    'emp_id' => $emp_id,
                    'salutation' => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
                    'firstname' => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                    'lastname' => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
                    'date_of_joining' => $doj->format('d-M-y'),
                    'reporting_manager' => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
                    'email' => $email,
                    'mobile' => $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
                    'department' => $worksheet->getCellByColumnAndRow(9, $row)->getValue(),
                    'designation' => $worksheet->getCellByColumnAndRow(10, $row)->getValue(),
                    'report_to_admin' => $worksheet->getCellByColumnAndRow(11, $row)->getValue(),
                    'level_no' => $worksheet->getCellByColumnAndRow(12, $row)->getValue(),
                    'level' => $level,
                    'hq' => $worksheet->getCellByColumnAndRow(14, $row)->getValue(),
                    'zone' => $worksheet->getCellByColumnAndRow(15, $row)->getValue(),
                    'region' => $worksheet->getCellByColumnAndRow(16, $row)->getValue(),
                );
                if($is_exist){
                    //Update employee data
                    $exist_id = $is_exist->id;
                    $this->cron_userdata_model->update_ftp_db('employee_data', 'id', $exist_id, $data);
                }else{
                    //Insert employee data
                    $this->cron_userdata_model->insert_ftp_db('employee_data', $data);
                }
            }
        }

        //import device user data 
        $this->import_device_users_data($company_id);
    }

    public function import_device_users_data($company_id){
        //fetch uploaded employee data by Client
        $employee_data = $this->cron_userdata_model->get_selected_values('employee_data','*',"id!=''",'','',$this->ftp_db);
        
        if(!empty($employee_data)){

            //DISABLE ALL EMPLOYEE FROM MWADMIN DOMAIN
            $device_user_status_data = array('status'=>0);
            $where_clause = array('company_id' => $company_id, 'addedby'=>'111111111'); 
            $update_status    = $this->cron_userdata_model->update('device_users', $where_clause, $device_user_status_data,$this->common_db);

            //DISABLE ALL EMPLOYEE FROM COMPANY DOMAIN
            $device_user_status_data = array('status' => 0);
            $where_clause = array('company_id' => $company_id, 'addedby'=>'111111111'); 
            $update_status    = $this->cron_userdata_model->update('device_users', $where_clause, $device_user_status_data,$this->db);
            
            //DISABLE ALL MANAGER FROM COMPANY DOMAIN
            $company_user_status_data = array('status' => 0);
            $where_clause = array('company_id' => $company_id, 'addedby'=>'111111111'); 
            $update_status    = $this->cron_userdata_model->update('company_users', $where_clause, $company_user_status_data,$this->db);


            foreach($employee_data as $employee){
                // echo "<pre>";
                // print_r($employee);
                $emp_code             = $employee->emp_id;
                $salutation           = ucfirst(strtolower($employee->salutation));
                $first_name           = ucfirst(strtolower($employee->firstname));
                $last_name            = ucfirst(strtolower($employee->lastname));
                $email_id             = strtolower($employee->email);
                $mobile_number        = $employee->mobile;
                $head_quarter         = $employee->hq;
                $department           = $employee->department;
                $designation          = $employee->designation;
                $doj                  = $employee->date_of_joining;
                $zone                 = $employee->zone;
                $region               = $employee->region;
                $level_no             = $employee->level_no;
                $level                = $employee->level;
                $now                  = date('Y-m-d H:i:s');

                // if(empty($level) || empty($email_id)){
                //     continue;   //skip the record without level and email address info
                // }

                $region_id = 0;
                if (isset($region) AND $region != ""){
                    //CHECK REGION
                    $where_clause = array('company_id' => $company_id,'region_name' => $region,'status' => 1,'deleted' => 0); 
                    $region_details = $this->cron_userdata_model->fetch_record('region',$where_clause,$this->db);
                    if (isset($region_details) AND !empty($region_details)){
                        $region_id   = $region_details->id;
                    }else{
                        $json_region_details = array(
                            'company_id'  => $company_id,
                            'region_name' => $region,
                            'status'      => 1,
                            'deleted'     => 0,
                            'addeddate'   => $now,
                            'addedby'     => 0
                        );
                        $region_id =  $this->cron_userdata_model->insert('region',$json_region_details,$this->db); 
                    }
                }

                // $zone_id = 0;
                // if (isset($zone) AND $zone != ""){
                //     //CHECK ZONE
                //     $where_clause = array('company_id' => $company_id,'area_name' => $zone,'status' => 1, 'deleted' => 0); 
                //     $zone_details = $this->cron_userdata_model->fetch_record('area',$where_clause,$this->db);
                //     if (isset($zone_details) AND !empty($zone_details)){
                //         $zone_id   = $zone_details->id;
                //     }else{
                //         $json_zone_details = array(
                //             'company_id'  => $company_id,
                //             'area_name' => $zone,
                //             'status'      => 1,
                //             'deleted'     => 0,
                //             'addeddate'   => $now,
                //             'addedby'     => 0
                //         );
                //         $zone_id =  $this->cron_userdata_model->insert('area',$json_zone_details,$this->db); 
                //     }
                // }

                $designation_id = 0;
                if (isset($designation) AND $designation != ""){
                    //CHECK ZONE
                    $where_clause = array('company_id' => $company_id,'description' => $designation,'status' => 1, 'deleted' => 0); 
                    $designation_details = $this->cron_userdata_model->fetch_record('designation',$where_clause,$this->db);
                    if (isset($designation_details) AND !empty($designation_details)){
                        $designation_id   = $designation_details->id;
                    }else{
                        $json_designation_details = array(
                            'company_id'  => $company_id,
                            'description' => $designation,
                            'status'      => 1,
                            'deleted'     => 0,
                            'addeddate'   => $now,
                            'addedby'     => 0
                        );
                        $designation_id =  $this->cron_userdata_model->insert('designation',$json_designation_details,$this->db); 
                    }
                }

                if(empty($email_id)){
                    $email_id = "$emp_code@sunpharma.com";  //default email address in case of empty email
                }
                //CHECK DUPLICATE EMAIL ADDRESS IN MWADMIN DB
                $exist_user_details = $this->cron_userdata_model->get_selected_values('device_users','*',"emp_id!='$emp_code' AND email='$email_id'",'','',$this->common_db);
                if(!empty($exist_user_details)){
                    continue;       //skip record to be updated for repeat email address
                }

                if($level=='FLE' || $level=='FLM' || $level=='SLM' || $level=='TLM'){   //Add device users 

                    //SEARCH USER - COMPANY DOMAIN
                    $where_clause = array('company_id' => $company_id,'emp_id' => $emp_code);
                    $user_details = $this->cron_userdata_model->fetch_record('device_users',$where_clause,$this->db);
                    if (isset($user_details) AND !empty($user_details)){
                        $device_user_id   = $user_details->user_id;
                        $json_device_user_details = array(
                            'company_id'    => $company_id,
                            // 'emp_id'        => $emp_code,
                            'firstname'     => $salutation.' '.$first_name,
                            'lastname'      => $last_name,
                            'email'         => $email_id,
                            'mobile'        => $mobile_number,
                            'department'    => $department,
                            'designation_id'=> $designation_id,
                            'region_id'     => $region_id,
                            'hq'            => $head_quarter,
                            'area'          => $zone,
                            'joining_date'  => date('Y-m-d', strtotime($doj)),
                            'level_no'      => $level_no,
                            'level'         => $level,
                            'trainer_id'    => 0,
                            'trainer_id_i'  => 0,
                            'trainer_id_ii' => 0,
                            'status'        => 1,
                            'block'         => 0,
                            'modifieddate'  => $now,
                            // 'addedby'       => '111111111'
                        );
                        $where_clause = array(
                            'company_id' => $company_id,
                            'user_id'    => $device_user_id
                        );
                        // echo "<br/> $emp_code - updated device user data";
                        $update_manager_i_status = $this->cron_userdata_model->update('device_users',$where_clause,$json_device_user_details,$this->db);
                    }else{
                        $json_device_user_details = array(
                            'company_id'           => $company_id,
                            'emp_id'               => $emp_code,
                            'firstname'            => $salutation.' '.$first_name,
                            'lastname'             => $last_name,
                            'email'                => $email_id,
                            'password'             => $this->cron_userdata_model->encrypt_password('sunpharma@2022'),
                            'mobile'               => $mobile_number,
                            'employment_year'      => '',
                            'education_background' => '',
                            'department'           => $department,
                            'designation_id'       => $designation_id,
                            'region_id'            => $region_id,
                            'hq'                   => $head_quarter,
                            'area'                 => $zone,
                            'level_no'             => $level_no,
                            'level'                => $level,
                            'trainer_id'           => 0,
                            'trainer_id_i'         => 0,
                            'trainer_id_ii'        => 0,
                            'designation_id'       => 0,
                            'otp_verified'         => 1,
                            'status'               => 1,
                            'block'                => 0,
                            'joining_date'         => date('Y-m-d', strtotime($doj)),
                            'registration_date'    => $now,
                            'addeddate'            => $now,
                            'addedby'              => '111111111'
                        );
                        // echo "<br/> $emp_code - Added device user data";
                        $device_user_id = $this->cron_userdata_model->insert('device_users',$json_device_user_details,$this->db);
                    }

                    //SEARCH USER - MWADMIN
                    if (isset($device_user_id) AND $device_user_id!=""){
                        $where_clause = array('company_id' => $company_id,'emp_id' => $emp_code);
                        $user_details = $this->cron_userdata_model->fetch_record('device_users',$where_clause,$this->common_db);
                        if (isset($user_details) AND !empty($user_details)){
                            $json_device_user_details = array(
                                'company_id' => $company_id,
                                'user_id'    => $device_user_id,
                                'emp_id'     => $emp_code,
                                'istester'   => 0,
                                'firstname'  => $salutation.' '.$first_name,
                                'lastname'   => $last_name,
                                'email'      => $email_id,
                                'mobile'     => $mobile_number,
                                'status'     => 1,
                                'block'      => 0,
                                'modifieddate' => $now,
                                // 'addedby'    => '111111111'
                            );
                            $where_clause = array(
                                'company_id' => $company_id,
                                'user_id'    => $device_user_id
                            );
                            // echo " - updated device user mwadmin data";
                            $update_duser_status = $this->cron_userdata_model->update('device_users',$where_clause,$json_device_user_details,$this->common_db);
                        }else{
                            $json_device_user_details = array(
                                'user_id'           => $device_user_id,
                                'company_id'        => $company_id,
                                'emp_id'            => $emp_code,
                                'istester'          => 0,
                                'firstname'         => $salutation.' '.$first_name,
                                'lastname'          => $last_name,
                                'email'             => $email_id,
                                'password'          => $this->cron_userdata_model->encrypt_password('sunpharma@2022'),
                                'mobile'            => $mobile_number,
                                'avatar'            => '',
                                'otp_verified'      => 1,
                                'area'              => '',
                                'status'            => 1,
                                'block'             => 0,
                                'fb_registration'   => 0,
                                'token'             => 0,
                                'registration_date' => $now,
                                'addeddate'         => $now,
                                'addedby'           => '111111111'
                            );
                            // echo " - added device user mwadmin data";
                            $mw_device_user_id = $this->cron_userdata_model->insert('device_users',$json_device_user_details,$this->common_db);
                        }
                    }
                }

                if($level!=='FLE'){     //Add CMS users
                    //CMS USER
                    $where_clause = array('company_id' => $company_id,'username' => $emp_code); 
                    // $where_clause = array('company_id' => $company_id,'email' => $email_id); 
                    $manager_details = $this->cron_userdata_model->fetch_record('company_users',$where_clause,$this->db);
                    if (isset($manager_details) AND !empty($manager_details)){
                        $manager_id = $manager_details->userid;
                        $json_manager_details = array(
                            'company_id'    => $company_id,
                            // 'username'      => $emp_code,
                            'salutation'    => $salutation,
                            'first_name'    => $first_name,
                            'last_name'     => $last_name,
                            'email'         => $email_id,
                            'mobile'        => $mobile_number,
                            'designation_id'=> $designation_id,
                            'region_id'     => $region_id,
                            'status'        => 1,
                            'modifieddate'  => $now,
                            // 'addedby'       => '111111111'
                        );
                        $where_clause = array(
                            'company_id' => $company_id,
                            'userid'     => $manager_id
                        );
                        // echo "<br/> $emp_code -  Updated cms user data";
                        $update_manager_status = $this->cron_userdata_model->update('company_users', $where_clause,$json_manager_details,$this->db);
                    } else{
                        $json_manager_details = array(
                            'company_id'          => $company_id,
                            'username'            => $emp_code,
                            'password'            => $this->cron_userdata_model->encrypt_password('sunpharma@2022'),
                            'login_type'          => 1,
                            'role'                => 2,
                            'designation_id'      => $designation_id,
                            'region_id'           => $region_id,
                            'salutation'          => $salutation,
                            'first_name'          => $first_name,
                            'last_name'           => $last_name,
                            'email'               => $email_id,
                            'mobile'              => $mobile_number,
                            'contactno'           => '',
                            'status'              => 1,
                            'addeddate'           => $now,
                            'addedby'             => '111111111',
                            'deleted'             => 0,
                            'userrights_type'     => 1,
                            'workshoprights_type' => 1
                        );
                        // echo "<br/> $emp_code -  Added cms user data";
                        $manager_id =  $this->cron_userdata_model->insert('company_users',$json_manager_details,$this->db);
                    }
                }
            }

            //Map Device users and Their managers
            $reporting_managers = array_column($employee_data, 'reporting_manager');
            $report_to_admins = array_column($employee_data, 'report_to_admin');
            $manager_details = $this->cron_userdata_model->get_manager_ids(array_merge($reporting_managers,$report_to_admins));
            $manager_ids = [];
            if(!empty($manager_details)){
                foreach($manager_details as $manager){
                    $manager_ids[$manager->username] = $manager->userid;
                }
            }
            foreach($employee_data as $employee){
                $emp_code             = $employee->emp_id;
                $email_id             = strtolower($employee->email);
                $level                = $employee->level;
                $reporting_manager    = $employee->reporting_manager;
                $report_to_admin      = $employee->report_to_admin;

                if($level=='FLE' || $level=='FLM' || $level=='SLM' || $level=='TLM'){   //Map device users with managers
                    $where_clause = array('company_id' => $company_id,'email' => $email_id); 
                    $user_details = $this->cron_userdata_model->fetch_record('device_users',$where_clause,$this->db);
                    if (isset($user_details) AND !empty($user_details)){
                        $device_user_id = $user_details->user_id;
                        $json_device_user_details = array(
                            'trainer_id'    => isset($manager_ids[$reporting_manager]) ? $manager_ids[$reporting_manager] : 0,
                            'trainer_id_i'  => isset($manager_ids[$report_to_admin]) ? $manager_ids[$report_to_admin] : 0
                        );
                        $where_clause = array(
                            'company_id' => $company_id,
                            'user_id'    => $device_user_id
                        );                        
                        // echo "<br/> $emp_code - $reporting_manager - $report_to_admin - updated device user data";
                        $update_manager_status = $this->cron_userdata_model->update('device_users',$where_clause,$json_device_user_details,$this->db);
                    }
                }
            }
        }
    }

    public function read_userdata(){
        $csv = '/var/www/html/awarathon.com/csv/Sample Sun Pharma Employee Data CVS File.csv';

        $handle = fopen($csv,"r");
        $i = 0;
        while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row values
        {
            print_r($row); //rows in array
            if($i>0){
                $emp_id = $row[0];
                $data = [
                    'emp_id' => $row[0],    //EMPCODE
                    'salutation' => $row[1],    //SALUTATION
                    'firstname' => $row[2],    //FIRSTNAME
                    'lastname' => $row[3],    //LASTNAME
                    'date_of_joining' => $row[4],    //DATEOFJOINING
                    'reporting_manager' => $row[5],    //REPORTINGMANAGER
                    'email' => $row[6],    //OFFICIALEMAILADDRESS
                    'mobile' => $row[7],    //PRESENTMOBILENUMBER
                    'department' => $row[8],    //DEPARTMENT
                    'designation' => $row[9],    //DESIGNATION
                    'level' => $row[10],    //LEVEL
                ];

                $is_exist = $this->cron_userdata_model->get_value('employee_data', 'id', "emp_id='$emp_id'");
                if($is_exist){
                    //Update employee data
                    $exist_id = $is_exist->id;
                    $this->cron_userdata_model->update_ftp_db('employee_data', 'id', $exist_id, $data);
                }else{
                    //Insert employee data
                    $this->cron_userdata_model->insert_ftp_db('employee_data', $data);
                }
            }
            $i++;
        }
    }

}