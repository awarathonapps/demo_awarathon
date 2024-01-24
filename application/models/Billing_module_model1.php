<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Billing_module_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit)
    {
        $query = "SELECT DATE_FORMAT(system_dttm,'%Y') as year,MONTHNAME(system_dttm) as month, total_users, active_users,sum(last_added) as user_per_month,inactive_users 
                  FROM users_statistics ";
        $query .= "$dtWhere";
        $query .= " group by MONTHNAME(system_dttm)";
        $query .= "  $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT count(month) as total from (SELECT DATE_FORMAT(system_dttm,'%Y') as year,MONTHNAME(system_dttm) as month, 
                  total_users, active_users,sum(last_added) as user_per_month, inactive_users 
                  FROM users_statistics group by month ORDER BY year) as main";

        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }

    public function ExportDeviceUsers($whereCond = "", $export_type = 1)
    {
        $query = "SELECT DATE_FORMAT(system_dttm,'%Y') as year,MONTHNAME(system_dttm) as month, total_users, 
                  active_users,sum(last_added) as user_per_month,inactive_users 
                  FROM users_statistics ";
        $query .= "$whereCond";
        $query .= " group by month";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function count_max_active_user($start_date, $end_date, $company_id)
    {
        $query = "SELECT total_users, active_users, inactive_users FROM `users_statistics` 
                      WHERE company_id =". $company_id." 
                      AND active_users =(SELECT MAX(active_users) FROM users_statistics  
                      WHERE system_dttm BETWEEN '" . $start_date . "' AND '" . $end_date . "')";
        $result = $this->db->query($query);
        return $result->result();
    }
}