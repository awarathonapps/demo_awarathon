<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api_logs_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }



    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit)
    {

        $query = "SELECT a.id,a.company_id,c.portal_name,a.api_name,ifnull(a.ip_address,'') as ip_address,ifnull(a.status_msg,'') as status_msg,a.api_parameter,a.date_time 
        FROM api_logs as a 
        LEFT JOIN company as  c ON a.company_id = c.id $dtWhere $dtOrder $dtLimit";
        $result = $this->common_db->query($query);

        $result = $this->common_db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT COUNT(a.id) as total FROM api_logs as a LEFT JOIN company as  c ON a.company_id = c.id  ";
        $query .= " $dtWhere";
        // $result = $this->db->query($query);
        $result = $this->common_db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }


    public function ExportApiLogs($whereCond = "", $export_type = 1)
    {
        if ($export_type == 1) {
            $query = "SELECT a.id,a.company_id,c.portal_name,a.api_name,ifnull(a.ip_address,'') as ip_address ,ifnull(a.status_msg,'') as status_msg,a.api_parameter,a.date_time 
            FROM api_logs as a 
            LEFT JOIN company as  c ON a.company_id = c.id  $whereCond order by id ";
        } else {
            $query = "SELECT a.id,a.company_id,c.portal_name,a.api_name,ifnull(a.ip_address,'') as ip_address,ifnull(a.status_msg,'') as status_msg,a.api_parameter,a.date_time 
            FROM api_logs as a 
            LEFT JOIN company as  c ON a.company_id = c.id $whereCond order by id,company_id desc  ";
        }
        // $result = $this->db->query($query);
        $result = $this->common_db->query($query);
        return $result->result();
    }
}
