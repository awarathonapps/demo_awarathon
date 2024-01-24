<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Api_documantation extends MY_Controller {
	function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('ai_dashboard');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->common_db = $this->common_model->connect_db2();
        $this->load->model('common_model');
        $this->load->model('ai_dashboard_model');
        $this->load->model('api_documantation_model');
    }
	
	public function index() {
        $data['module_id'] = '17.01';
        $data['company_id'] = $this->mw_session['company_id'];
        $where_clause = array(
               'id'  => $data['company_id'],
               'status'   => 1
           );
     
       $portal_namme = $this->api_documantation_model->get_cmp_code('company',$where_clause);
       if($portal_namme!='')
       {
           $data['token'] = $this->get_tocken($portal_namme);
       }
       else
       {
           $data['token'] = array();
       }
		$data['step'] = 1;
        $this->load->view('api_document/introduction',$data);
    }
    public function get_tocken($portal_namme)
    {
        $post_array =array(
            'company_code'=>$portal_namme
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_array));
        curl_setopt($curl, CURLOPT_URL, 'https://restapi.awarathon.com/api/get_credential');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $result = curl_exec($curl);

        return json_decode( $result);


    }

   
}