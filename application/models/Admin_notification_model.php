<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin_notification_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit) {
        $query  = "SELECT an.id,an.message,DATE_FORMAT(an.addeddate,'%d-%m-%Y %H:%i') as addeddate,an.status FROM admin_notification an $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet']        = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query  = "SELECT COUNT(an.id) as total FROM admin_notification as an $dtWhere ";
        $result = $this->db->query($query);
        $data_array             = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
}