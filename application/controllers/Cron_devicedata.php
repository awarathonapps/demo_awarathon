<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cron_devicedata extends CI_Controller {
    public $common_db;
    public function __construct() {
        parent::__construct();
        $this->load->model('common_model');
        $this->common_db = $this->common_model->connect_db2();
        $this->load->model('cron_devicedata_model');
    }
    public function index(){
        echo "Welcome to the Awarathon";
        exit;
    }
    public function cronjob_device_inactive(){
        $current_date = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $date_array = array();
        $user_data = $this->common_model->get_selected_values('device_users', 'user_id,company_id,addeddate', 'status=1', 'user_id');
        if (count((array)$user_data)>0){
            foreach($user_data as $udate){
                $start_date = date('Y-m-d',strtotime($udate->addeddate));
                $end_date = date('Y-m-d',strtotime("+30 days", strtotime($start_date)));

                // $date1=date_create($start_date); $date2=date_create($end_date);
                // $diff=date_diff($date1,$date2); $no_of_days = $diff->format("%a");

                if(strtotime($current_date) > strtotime($end_date)){
                    // $date_array[] = $start_date.' to '.$end_date;
                    $inactive_data = array('status' => 0,'modifieddate' => $now);
                    $this->common_model->update('device_users', 'user_id', $udate->user_id, $inactive_data);
                    $this->cron_devicedata_model->update_userdb2($udate->user_id, $udate->company_id, $inactive_data);
                }
            }
        }
    }
}