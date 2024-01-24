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
        $query = "SELECT DATE_FORMAT(a.system_dttm,'%Y') as year, MONTHNAME(a.system_dttm) as month, a.total_users, 
            a.active_users, SUM(a.last_added) as user_per_month, a.inactive_users,YEAR(a.system_dttm) AS year_id,MONTH(system_dttm) AS month_id
            FROM users_statistics as a
            INNER JOIN (SELECT MAX(active_users) as active_users,month(system_dttm) as system_month,year(system_dttm) as system_year FROM users_statistics group by month(system_dttm),year(system_dttm)) as b on b.active_users = a.active_users and b.system_year = year(a.system_dttm) and b.system_month = month(system_dttm) ";
        $query .= " $dtWhere group by month(a.system_dttm),year(a.system_dttm) order by date(a.system_dttm) desc ";
//  Total Record
        $cntresult = $this->db->query($query);
        $data_array = $cntresult->result();
        $data['dtTotalRecords'] = count((array)$data_array);
//  Record per page with result
        // $query .= " $dtOrder $dtLimit ";
        $query .= " $dtLimit ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);
        return $data;
    }

    public function ExportDeviceUsers($whereCond = "", $export_type = 1)
    {
        $query = "SELECT DATE_FORMAT(a.system_dttm,'%Y') as year, MONTHNAME(a.system_dttm) as month, a.total_users, 
            a.active_users, SUM(a.last_added) as user_per_month, a.inactive_users,YEAR(a.system_dttm) AS year_id,MONTH(system_dttm) AS month_id
            FROM users_statistics as a
            INNER JOIN (SELECT MAX(active_users) as active_users,month(system_dttm) as system_month,year(system_dttm) as system_year FROM users_statistics group by month(system_dttm),year(system_dttm)) as b on b.active_users = a.active_users and b.system_year = year(a.system_dttm) and b.system_month = month(system_dttm) ";
        $query .= " $whereCond group by month(a.system_dttm),year(a.system_dttm) order by date(a.system_dttm) desc ";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function ExportUsersData($start_date='', $end_date='')
    {
        $query = "SELECT main1.*, rg.region_name,IFNULL(c.user_status,IF(main1.status=1,'Registered','Inactive')) as user_status,IF(c.login_dttm!='',DATE_FORMAT(c.login_dttm,'%d-%m-%Y'),'') as login_dttm 
        FROM (SELECT * FROM 
        (SELECT du.user_id,du.emp_id, du.firstname, du.lastname, du.email, du.mobile, du.department, du.region_id, IF(du.designation='',dt.description,du.designation) as designation, DATE_FORMAT(du.registration_date,'%d-%m-%Y') as registration_date, DATE_FORMAT(du.modifieddate,'%d-%m-%Y') as modifieddate, du.status 
        FROM device_users as du LEFT JOIN designation_trainee as dt on dt.id=du.designation_id WHERE du.istester=0 ";
        if($start_date!=''&& $end_date!=''){
            $query .=" AND (date(du.registration_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "') ";
        }
        $query .=" UNION ALL 
        SELECT cu.userid as user_id,cu.emp_id, cu.first_name, cu.last_name, cu.email, cu.mobile, cu.department, cu.region_id, d.description as designation, DATE_FORMAT(cu.addeddate,'%d-%m-%Y') as registration_date, DATE_FORMAT(cu.modifieddate,'%d-%m-%Y') as modifieddate, cu.status  
        FROM company_users as cu LEFT JOIN designation as d ON d.id=cu.designation_id ";
        if($start_date!=''&& $end_date!=''){
            $query .=" WHERE (date(cu.addeddate) BETWEEN '" . $start_date . "' AND '" . $end_date . "') ";
        }
        $query .=" ) as main ORDER BY main.status DESC
        ) as main1 
        LEFT JOIN region as rg on rg.id = main1.region_id
        LEFT JOIN (SELECT DISTINCT user_id FROM assessment_attempts) as b ON b.user_id = main1.user_id
        LEFT JOIN (SELECT user_id,MAX(info_dttm) as login_dttm, 'Active' as user_status FROM `device_info` group by user_id) as c ON c.user_id = main1.user_id
        GROUP BY main1.email ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function count_max_active_user($start_date, $end_date, $company_id)
    {
        $query = "SELECT total_users, active_users, inactive_users FROM `users_statistics` 
                      WHERE company_id =". $company_id." 
                      AND active_users = (SELECT MAX(active_users) FROM users_statistics  
                      WHERE date(system_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "')
                      AND date(system_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function monthwiseUsers($dtWhere)
    {
        $query = "SELECT SUM(a.last_added) as active_users FROM users_statistics as a $dtWhere ";
        $result = $this->db->query($query);
        return $result->row();
    }
}