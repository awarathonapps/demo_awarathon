<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public $mw_session;
    public $common_db;

    function __construct() {
        parent::__construct();
        $this->mw_session = $this->session->userdata('awarathon_session');
        if (!isset($this->mw_session) || $this->mw_session['user_id'] == '') {
            redirect(base_url());
        } else {
            $this->load->model('common_model');
        }
        $this->common_db = null;
    }

    function check_rights($menu, $user_id = '', $Mode = '') {
        $superaccess = $this->mw_session['superaccess'];
        if ($superaccess) {
            $ResultSet = array(
                'role' => 1, 'allow_access' => 1,
                'allow_add' => 1, 'allow_view' => 1,
                'allow_edit' => 1, 'allow_delete' => 1,
                'allow_print' => 1, 'allow_import' => 1,
                'allow_export' => 1);
            $ReturnData = (object) $ResultSet;
        } else {
            if ($user_id == "") {
                $user_id = $this->mw_session['user_id'];
            }
            $ReturnData = $this->common_model->check_user_rights($menu, $user_id, $Mode);
        }
        return $ReturnData;
    }

    public function ajax_populate_company() {
        return $this->common_model->fetch_company_data($this->input->get());
    }

    public function ajax_populate_roles() {
        return $this->common_model->fetch_roles_data($this->input->get());
    }

    public function ajax_populate_country() {
        return $this->common_model->fetch_country_data($this->input->get());
    }

    public function ajax_populate_state() {
        return $this->common_model->fetch_state_data($this->input->get());
    }

    public function ajax_populate_city() {
        return $this->common_model->fetch_city_data($this->input->get());
    }

}
