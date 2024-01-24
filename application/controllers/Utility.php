<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Utility extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('utility');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
//        $this->load->model('roles_model');
    }
    public function index() {
        $data['module_id'] = '98.09';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['cmpdata'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        $this->load->view('utility/index', $data);
    }
    public function update_workshop_trainer(){
        $lcSqlstr ="SELECT workshop_id,questionset_id FROM workshop_questionset_pre
                UNION ALL SELECT workshop_id,questionset_id FROM workshop_questionset_post";
	$result = $this->db->query($lcSqlstr);
        $ResultSet = $result->result();
        if(count((array)$ResultSet)>0){
            foreach ($ResultSet as $key => $value) {
                $Workshop_Id =$value->workshop_id;
                $workshop_setData = $this->common_model->get_value('workshop', 'id', 'id=' . $Workshop_Id);
                if(count((array)$workshop_setData)==0){
                    continue;
                }
                $questionset_id =$value->questionset_id;
                $Already_setData = $this->common_model->get_value('workshop_questionset_trainer', 'id', 'workshop_id=' . $Workshop_Id . ' AND questionset_id=' . $questionset_id);
                if (count((array)$Already_setData)== 0) {
                    $lcSqlstr ="INSERT INTO workshop_questionset_trainer (workshop_id,questionset_id,questions_trans_id,topic_id,subtopic_id,trainer_id)"
                        . "SELECT $Workshop_Id as workshop_id,$questionset_id as questionset_id,id,topic_id,subtopic_id,trainer_id FROM questionset_trainer"
                        . " where questionset_id= $questionset_id ";
                    $this->db->query($lcSqlstr);
                }
                
            }
        }
    }
     public function submit() {
        $base_path = $_SERVER['DOCUMENT_ROOT'].'/';
//        echo $base_path;exit;
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to Add,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
                    $from = $this->input->post('from',true);
                    $to = $this->input->post('to');
                    $all = $this->input->post('all');
                    $controllers = $this->input->post('controllers');
                    $models = $this->input->post('models');
                    $views= $this->input->post('views');
                    $helpers = $this->input->post('helpers');
                    $libraries= $this->input->post('libraries');
                    
            $this->load->library('form_validation');
            $this->form_validation->set_rules('from', 'Company', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('to', 'Company', 'trim|required|max_length[50]');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {               
                $fromdata = $this->common_model->get_value('company', 'portal_name', 'status=1 and id='.$from);
                $todata = $this->common_model->get_value('company', 'portal_name', 'status=1 and id='.$to);
//                echo $base_path.$fromdata->portal_name.'/test.php';
//                echo $base_path.$todata->portal_name.'/test.php';
//                echo $base_path.$fromdata->portal_name .'/tests/';
//                 echo $filecount = count((array)glob($base_path.$fromdata->portal_name .'/tests/'. "*.*")); 
//               exit;
                if (is_dir($base_path.$fromdata->portal_name)) {
                    if ($dh = opendir($base_path.$fromdata->portal_name)) {
                        while (($file = readdir($dh)) !== false) {
                            copy($base_path.$fromdata->portal_name.'/'.$file, $base_path.$todata->portal_name.'/'.$file);
                        }
                        closedir($dh);
                    }
                }
                if ($from != "" && $to != "") {
                        if($all==1){
                            
                            
//                                copy($base_path.$fromdata->portal_name.'/index.html', $base_path.$todata->portal_name.'/index.html');
//                                ("cp -r $base_path.$fromdata->portal_name $dest");

                        }else{
                               
                           }
                        $Message = "Folder copy successfully.";
                    } else {
                        $Message = "Error while creating Role,Contact Mediaworks for technical support.!";
                        $SuccessFlag = 0;
                    }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
}   