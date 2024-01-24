<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Configuration extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('configuration');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('common_model');
    }
    public function site_settings() {       
        $id='';
        $data['module_id'] = '33.01';
        $company_id = $this->mw_session['company_id'];
        $data['CompanyData'] = $this->common_model->get_value('company', '*', 'id=' . $company_id);
        $data['ThresholdData'] = $this->common_model->get_selected_values('company_threshold_range', '*', 'company_id=' . $company_id);
        $data['ResultData'] = $this->common_model->get_selected_values('company_threshold_result', '*', 'company_id=' . $company_id);
        $data['company_name'] = $this->mw_session['company_name'];
        $data['acces_management'] = $this->acces_management;
        $this->load->view('configuration/site_settings', $data);
    } 
    public function update_site_setting() {
        $SuccessFlag = 1;
        $Message = '';
        $company_id = $this->mw_session['company_id'];
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message = "You have no rights to add,Contact administrator for rights";
            $SuccessFlag = 0;
        }else{
            $this->load->library('form_validation');
//            $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
            $this->form_validation->set_rules('title','Site Title', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('range_from[]','Threshold Range', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('range_to[]','Threshold Range', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('result_from[]','Pass/Fail Range', 'trim|required|max_length[250]');
            $this->form_validation->set_rules('result_to[]','Pass/Fail Range', 'trim|required|max_length[250]');
            if ($this->form_validation->run() == FALSE) {
                $Message = validation_errors();
                $SuccessFlag = 0;
            } else {      
                $now = date('Y-m-d H:i:s');
                $upload_path =  './assets/uploads/company/';
                
                $asset_url = base_url();
                $profile_image='';
                if (isset($_FILES['img_url']['name']) && $_FILES['img_url']['size'] > 0) {

                    if ($profile_image != "") {
                        $Path = $upload_path . $profile_image;
                        if (file_exists($Path)) {
                            unlink($Path);
                        }
                    }
                    $config = array();
                    $profile_image = time();
                    $config['upload_path'] = $upload_path;
                    $config['overwrite'] = FALSE;
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
        //            $config['max_width'] = 750;
        //            $config['max_height'] = 400;
        //            $config['min_width'] = 750;
        //            $config['min_height'] = 400;
                    $config['file_name'] = $profile_image;
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('img_url')) {
                        $Message = $this->upload->display_errors();
                        $SuccessFlag=0;
                    }
                    $ImgArrays = explode('.', $_FILES['img_url']['name']);
                    $profile_image .="." . $ImgArrays[1];
                }
                $threshold_array = $this->input->post('range_color', true);
                $threshold_result = $this->input->post('result_color', true);
                if (count((array)$threshold_array) > 0 && count((array)$threshold_result) > 0) {
                    foreach ($threshold_array as $key1 => $color) {
                        $range1=$this->input->post('range_from')[$key1];
                        $range2=$this->input->post('range_to')[$key1];
                      if($range1 < 100 && $range2 <= 100){
                       if($range1 > $range2){
                           $Message = '2nd Threshold percentage field not be less than 1st';
                           $SuccessFlag=0;
                       }else{
                           $j=$key1-1;
                           for($i=$j;$i>=0; $i--){
                               if(($range1 >= $this->input->post('range_from')[$i] && $range1 <=$this->input->post('range_to')[$i]) || ($this->input->post('range_from')[$i] >= $range1 && $this->input->post('range_from')[$i] <= $range2)){
                                   $Message = 'You entered same percentage Threshold range';
                                   $SuccessFlag=0;
                               }
                           }
                       }
                     }else{
                         $Message = 'Percentage range not start with 100% and not greater than 100%';
                         $SuccessFlag=0;
                     }
                    }
                    foreach ($threshold_result as $key2 => $color) {
                                if($key2 < 2){
                                 $result1 = $this->input->post('result_from')[$key2];
                                 $result2= $this->input->post('result_to')[$key2];
                                    if($result1 < 100 && $result2 <= 100){
                                        if($result1 > $result1){
                                            $Message = '2nd Pass/Fail %age value field not be less than 1st';
                                            $SuccessFlag=0;
                                        }else{
                                            $i=$key2-1;
                                            if($i >=0){
                                                if(($result1 >= $this->input->post('result_from')[$i] && $result1 <=$this->input->post('result_to')[$i]) || ($this->input->post('result_from')[$i] >= $result1 && $this->input->post('result_from')[$i] <= $result2)){
                                                    $Message = 'You entered same %age Pass/Fail value';
                                                    $SuccessFlag=0;
                                                }
                                            }
                                        }
                                    }else{
                                        $Message = 'Pass/Fail %age range not start with 100% and not Greater than 100%';
                                        $SuccessFlag=0;
                                    }
                                }
                         }
                }else{
                    $Message = 'Please Enter Threshold Range';
                    $SuccessFlag=0;
                }
                 $threshold_result = $this->input->post('result_color', true);
                        if (count((array)$threshold_result) > 0) {
                            foreach ($threshold_result as $key2 => $color) {
                                   if($key2 < 2){
                                    $data1['result_from'] = ($this->input->post('result_from')[$key2] !='' && ($key2 < 2) ? $this->input->post('result_from')[$key2] : '');
                                    $data1['result_to'] = ($this->input->post('result_to')[$key2] !='' && ($key2 < 2) ? $this->input->post('result_to')[$key2] : '');
                                   }else{
                                    $data1['result_from'] = '';
                                    $data1['result_to'] = '';
                                   }
                                    $data1['result_color'] = $color;
                                $this->common_model->update('company_threshold_result', 'id',$key2+1,$data1);
                            }
                        }
                if($SuccessFlag){
                    if(count((array)$company_id)>0){
                            $data = array(
                            'sitetitle' => $this->input->post('title'),
							'company_name' => $this->input->post('company_id'),
                            'description' => $this->input->post('description'),
                            'copyright' => $this->input->post('copyright'),
                            'contact_no' => $this->input->post('contact'),
                            //'currency' => $this->input->post('currency'),
                            'address'=> $this->input->post('address'),
                            //'symbol' => $this->input->post('symbol'),
                            'email' => $this->input->post('email'),
                            //'captcha_required'=> $this->input->post('captcha_required',true),    
                            'modifieddate' => $now,
                            'modifiedby' => $this->mw_session['user_id'],                                              
                        );
                        if($profile_image !=""){
                            $data['company_logo']= $profile_image;
                        }
                        $this->common_model->update('company','id',$company_id, $data);
                        
                        $this->common_model->delete('company_threshold_range', 'company_id', $company_id);
                        $threshold_array = $this->input->post('range_color', true);
                        if (count((array)$threshold_array) > 0) {
                            foreach ($threshold_array as $key1 => $color) {
                                $data = array(
                                    'company_id' => $company_id,
                                    'range_from' => ($this->input->post('range_from')[$key1] !='' ? $this->input->post('range_from')[$key1] : ''),
                                    'range_to' => ($this->input->post('range_to')[$key1] !='' ? $this->input->post('range_to')[$key1] : ''),
                                    'range_color' => $color
                                );
                                $this->common_model->insert('company_threshold_range', $data);
                            }
                        }
                        $threshold_result = $this->input->post('result_color', true);
                        if (count((array)$threshold_result) > 0) {
                            foreach ($threshold_result as $key2 => $color) {
                                   if($key2 < 2){
                                    $data1['result_from'] = ($this->input->post('result_from')[$key2] !='' && ($key2 < 2) ? $this->input->post('result_from')[$key2] : '');
                                    $data1['result_to'] = ($this->input->post('result_to')[$key2] !='' && ($key2 < 2) ? $this->input->post('result_to')[$key2] : '');
                                   }else{
                                    $data1['result_from'] = '';
                                    $data1['result_to'] = '';
                                   }
                                    $data1['result_color'] = $color;
                                $this->common_model->update('company_threshold_result', 'id',$key2+1,$data1);
                            }
                        }
                        $Message = "Company details updated successfully";
                    }  
                }
                      
            }
        }
        $Rdata['Success'] = $SuccessFlag;
        $Rdata['Message'] = $Message;
        echo json_encode($Rdata);
    }
    
    public function get_rangeslot() {
        $success = 1;
        $Msg = '';
        $lchtml = '';
        $slot_row = $this->input->post('slot_row');
        if ($success) {
            $lchtml .='<tr id="rng_' . $slot_row . '">';
            $lchtml .='<td style="width:250px;"><div class="col-md-6" style="padding-left:0px;">'
                    . '<input class="form-control input-sm " id="range_from'.$slot_row.'" name="range_from[]" placeholder="" type="text" value=""></div><div class="col-md-6" style="padding-left:0px;">'
                    . '<input class="form-control input-sm " id="range_to'.$slot_row.'" name="range_to[]" placeholder="" type="text" value=""></div></td>';
            $lchtml .='<td><input type="color" id="range_color'.$slot_row.'" name="range_color[]" class="form-control input-sm " value=""></td>';
            $lchtml .='<td><button class="btn btn-danger btn-xs btn-mini " type="button" onclick="remove_rangelot(' . $slot_row . ');" ><i class="fa fa-times"></i></button></td>';
            $lchtml .='</tr>';
        }
        $response['html'] = $lchtml;
        $response['Success'] = $success;
        $response['Msg'] = $Msg;
        echo json_encode($response);
    }
}
