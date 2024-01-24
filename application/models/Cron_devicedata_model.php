<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Cron_devicedata_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function update_userdb2($id, $Company_id, $data2) {  
        $this->common_db->where('user_id', $id);
        $this->common_db->where('company_id', $Company_id);
        $this->common_db->update('device_users', $data2);
        return true;
    }
}
