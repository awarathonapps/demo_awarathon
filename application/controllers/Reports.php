<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Reports extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('reports');
        $rightrole = ($this->mw_session['role'] == 1 || $this->mw_session['role'] == 2) ? 1 : 0;
        if ((isset($acces_management->allow_access) && !$acces_management->allow_access) && !$rightrole) {
            redirect('home');
        }
        $this->acces_management = $acces_management;
    }

    public function index()
    {
        $data['module_id'] = '91';
        $data['acces_management'] = $this->acces_management;
        $data['company_id'] = $this->mw_session['company_id'];
        $data['role'] = $this->mw_session['role'];
        if ($data['company_id'] == "") {
            $data['CompanyData'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1', 'company_name');
        } else {
            $data['CompanyData'] = array();
        }
        $this->load->view('reports/index', $data);
    }

    
}
