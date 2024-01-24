<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cron_userdata_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    function connect_ftp_db() {
        return $this->load->database('ftp_db', TRUE);
    }

    function connect_common_db() {
        return $this->load->database('common_db', TRUE);
    }

    public function get_value($Table, $Column, $Clause) {
        $LcSqlStr = "SELECT " . $Column . " FROM " . $Table . " WHERE " . $Clause . " ";
        $query = $this->ftp_db->query($LcSqlStr);
        $row = $query->row();
        return $row;
    }

    public function get_selected_values($Table, $Column, $Clause, $OrderBy = '', $limit = '', $atomdb) {
        $LcSqlStr = "SELECT " . $Column . " FROM " . $Table . " WHERE " . $Clause . " ";
        if ($OrderBy != "") {
            $LcSqlStr .= " Order By " . $OrderBy;
        }
        if($limit != ""){
            $LcSqlStr .= " LIMIT $limit";
        }
        $query = $atomdb->query($LcSqlStr);
        // $query = $this->db->query($LcSqlStr);
        $row = $query->result();
        return $row;
    }

    public function fetch_record($table,$where_clause,$atomdb=null){
        if ($atomdb==null){
            foreach ($where_clause as $key=>$value){
                $this->db->where($key, $value);
            }
            $query = $this->db->get($table);
        }else{
            foreach ($where_clause as $key=>$value){
                $atomdb->where($key, $value);
            }
            $query = $atomdb->get($table);
        }
        return $query->row();
    }

    public function insert($Table, $data,$atomdb=null) {
        $atomdb->insert($Table, $data);
        $insert_id = $atomdb->insert_id();
        return $insert_id;
    }

    public function insert_ftp_db($Table, $data) {
        $this->ftp_db->insert($Table, $data);
        $insert_id = $this->ftp_db->insert_id();
        return $insert_id;
    }

    public function update($table, $where_clause, $data,$atomdb=null) {
        if ($atomdb==null){
            $this->db->trans_start();
            foreach ($where_clause as $key=>$value){
                $this->db->where($key, $value);
            }
            $this->db->update($table, $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                return false;
            }else{
                return true;
            }
        }else{
            $atomdb->trans_start();
            foreach ($where_clause as $key=>$value){
                $atomdb->where($key, $value);
            }
            $atomdb->update($table, $data);
            $atomdb->trans_complete();
            if ($atomdb->trans_status() === FALSE){
                return false;
            }else{
                return true;
            }
        }
    }

    public function update_ftp_db($Table, $Column, $id, $data) {
        $this->ftp_db->where($Column, $id);
        $this->ftp_db->update($Table, $data);
        return true;
    }

    public function add($data){
        return $this->ftp_db->insert("employee_data", $data);
    }

    function encrypt_password($password) {
        $salt = substr(md5(uniqid(rand(), true)), 0, 8);
        $hash = $salt . md5($salt . $password);
        return $hash;
    }

    public function get_manager_ids($manager_codes){
        $query = "SELECT userid, username FROM company_users WHERE username IN ('".implode("','",$manager_codes)."')";
        $result = $this->db->query($query);
        return $result->result();
    }
}