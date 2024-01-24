<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Multilang extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('multilang');
        //if (!$acces_management->allow_access) {
           // redirect('dashboard');
       // }
        $this->acces_management = $acces_management;
        $this->load->model('multilang_model');
    }
    
    public function index(){
        $data['module_id']        = '5.1';
        $data['username']         = $this->mw_session['username'];
        $data['user_id']         = $this->mw_session['user_id'];
        $data['acces_management'] = $this->acces_management;        
        $Company_id = $this->mw_session['company_id']; 
        $user_id= $this->mw_session['user_id'];
        if ($Company_id == "") {
            $data['cmpdata'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['cmpdata'] = array();
        }
        $data['Company_id'] = $Company_id;    
        
        $lang_result = $this->common_model->get_selected_values('ai_language', 'lan_id,addedby,status,company_id,login_page,backend_page,pwa_page', 'company_id="' . $Company_id . '"'); //AND addedby="'.$user_id.'"
        $data['lang_result'] = $lang_result;

        $query = "SELECT * FROM ai_language";
        $result = $this->db->query($query);
        $data['sel_Lang'] = $result->result_array();
 
        $this->load->view('multilang/index', $data);
    }

      
    public function submit()
    {
        $log_page_lang = $_POST['log_page_lang']; 
        $back_lang = $_POST['back_lang']; 
        $pwa_lang = $_POST['pwa_lang']; 
             
                $now = date('Y-m-d H:i:s');
                $addedby=$this->mw_session['user_id'];
                $Company_id = $this->mw_session['company_id'];

                $data = array(
                    'company_id'          => $Company_id,
                    'login_page'          => $log_page_lang,
                    'backend_page'           => $back_lang,
                    'pwa_page'           => $pwa_lang,
                    'status'                => '1',
                    'addeddate'             => $now,
                    'addedby'               => $addedby,
                );
                 $this->common_model->insert('ai_language', $data);
                $message = "Language Added Successfully..";
             
        echo json_encode(array('message' => $message, 'data' =>$data));
    } //-- submit

    public function edit()
    {
        $log_page_lang = $_POST['log_page_lang']; 
        $back_lang = $_POST['back_lang']; 
        $pwa_lang = $_POST['pwa_lang'];
        $modifiedby = $this->mw_session['user_id'];
        $lan_id = $_POST['lan_id'];

        $id = base64_decode($lan_id);

        $now = date('Y-m-d H:i:s');
        $data = array(
            'login_page'          => $log_page_lang,
            'backend_page'           => $back_lang,
            'pwa_page'           => $pwa_lang,
            'modifieddate'             => $now,
            'modifiedby'               => $modifiedby,
        );
        $insert_id = $this->common_model->update('ai_language', 'lan_id', $id, $data);
        $message = "Language Update Successfully..";

        echo json_encode(array('message' => $message));
    } //-- edit
}