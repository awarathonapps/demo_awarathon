<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class CropAvatar {
    private $src;
    private $data;
    private $dst;
    private $type;
    private $extension;
    private $msg;
  
    function __construct($src, $data, $file) {
      $this->setSrc($src);
      $this->setData($data);
      $this->setFile($file);
      $this->crop($this -> src, $this -> dst, $this -> data);
    }
  
    private function setSrc($src) {
      if (!empty($src)) {
        $type = exif_imagetype($src);
  
        if ($type) {
          $this -> src = $src;
          $this -> type = $type;
          $this -> extension = image_type_to_extension($type);
          $this -> setDst();
        }
      }
    }
  
    private function setData($data) {
      if (!empty($data)) {
        $this -> data = json_decode(stripslashes($data));
      }
    }
  
    private function setFile($file) {
      $errorCode = $file['error'];
  
      if ($errorCode === UPLOAD_ERR_OK) {
        $type = exif_imagetype($file['tmp_name']);

        if ($type) {
          $extension = image_type_to_extension($type);
                    
          $src = 'assets/uploads/avatar/' . date('YmdHis') . '.original' . $extension;
          //$full_path = base_url().$src;  
          if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG) {
            if (file_exists($src)) {
              unlink($src);
            }
            $result = move_uploaded_file($file['tmp_name'], $src);
           
            if ($result) {
              $this->src = $src;
              $this->type = $type;
              $this->extension = $extension;
              $this->setDst();
            } else {
               $this -> msg = 'Failed to save file';
            }
          } else {
            $this -> msg = 'Please upload image with the following types: JPG, PNG, GIF';
          }
        } else {
          $this -> msg = 'Please upload image file';
        }
      } else {
        $this -> msg = $this->codeToMessage($errorCode);
      }
    }
  
    private function setDst() {
        $this->dst ='assets/uploads/avatar/' . date('YmdHis') . '.png';
    }
  
    private function crop($src, $dst, $data) {
      if (!empty($src) && !empty($dst) && !empty($data)) {
        switch ($this -> type) {
          case IMAGETYPE_GIF:
            $src_img = imagecreatefromgif($src);
            break;
  
          case IMAGETYPE_JPEG:
            $src_img = imagecreatefromjpeg($src);
            break;
  
          case IMAGETYPE_PNG:
            $src_img = imagecreatefrompng($src);
            break;
        }
  
        if (!$src_img) {
          $this -> msg = "Failed to read the image file";
          return;
        }
  
        $size = getimagesize($src);
        $size_w = $size[0]; // natural width
        $size_h = $size[1]; // natural height
  
        $src_img_w = $size_w;
        $src_img_h = $size_h;
  
        $degrees = $data -> rotate;
  
        // Rotate the source image
        if (is_numeric($degrees) && $degrees != 0) {
          // PHP's degrees is opposite to CSS's degrees
          $new_img = imagerotate( $src_img, -$degrees, imagecolorallocatealpha($src_img, 0, 0, 0, 127) );
  
          imagedestroy($src_img);
          $src_img = $new_img;
  
          $deg = abs($degrees) % 180;
          $arc = ($deg > 90 ? (180 - $deg) : $deg) * M_PI / 180;
  
          $src_img_w = $size_w * cos($arc) + $size_h * sin($arc);
          $src_img_h = $size_w * sin($arc) + $size_h * cos($arc);
  
          // Fix rotated image miss 1px issue when degrees < 0
          $src_img_w -= 1;
          $src_img_h -= 1;
        }
  
        $tmp_img_w = $data -> width;
        $tmp_img_h = $data -> height;
        $dst_img_w = 220;
        $dst_img_h = 220;
  
        $src_x = $data -> x;
        $src_y = $data -> y;
  
        if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
          $src_x = $src_w = $dst_x = $dst_w = 0;
        } else if ($src_x <= 0) {
          $dst_x = -$src_x;
          $src_x = 0;
          $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
        } else if ($src_x <= $src_img_w) {
          $dst_x = 0;
          $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
        }
  
        if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
          $src_y = $src_h = $dst_y = $dst_h = 0;
        } else if ($src_y <= 0) {
          $dst_y = -$src_y;
          $src_y = 0;
          $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
        } else if ($src_y <= $src_img_h) {
          $dst_y = 0;
          $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
        }
  
        // Scale to destination position and size
        $ratio = $tmp_img_w / $dst_img_w;
        $dst_x /= $ratio;
        $dst_y /= $ratio;
        $dst_w /= $ratio;
        $dst_h /= $ratio;
  
        $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);
  
        // Add transparent background to destination image
        imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
        imagesavealpha($dst_img, true);
  
        $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
  
        if ($result) {
          if (!imagepng($dst_img, $dst)) {
            $this -> msg = "Failed to save the cropped image file";
          }
        } else {
          $this -> msg = "Failed to crop the image file";
        }
  
        imagedestroy($src_img);
        imagedestroy($dst_img);
      }
    }
  
    private function codeToMessage($code) {
      $errors = array(
        UPLOAD_ERR_INI_SIZE =>'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE =>'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL =>'The uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE =>'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR =>'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE =>'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION =>'File upload stopped by extension',
      );
  
      if (array_key_exists($code, $errors)) {
        return $errors[$code];
      }
  
      return 'Unknown upload error';
    }
  
    public function getResult() {
      return !empty($this->data) ? $this->dst : $this->src;
    }
    public function getTemporery() {
      return !empty($this->data) ? base_url().$this->dst : $this->src;
    }
  
    public function getMsg() {
      return $this -> msg;
    }
}
class Users extends MY_Controller {
     public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('users');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('users_model');
        $this->load->library('form_validation');
        $this->load->library('upload');
    }
    
    public function ajax_populate_roles() {
        return $this->users_model->fetch_roles_data($this->input->get());
    }
    public function ajax_populate_country() {
        return $this->users_model->fetch_country_data($this->input->get());
    }
    public function ajax_populate_state() {
        return $this->users_model->fetch_state_data($this->input->get());
    }
    public function ajax_populate_city() {
        return $this->users_model->fetch_city_data($this->input->get());
    }
    public function index() {
        $data['module_id'] = '99.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['rows'] = $this->users_model->fetch_access_data();
        $this->load->view('users/index', $data);
    }
    public function create() {
        $data['module_id'] = '99.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $this->load->view('users/create', $data);
    }
    public function edit($id) {
        $user_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('users');
            return;
        }
        $this->load->helper('form');
        $data['module_id'] = '99.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->users_model->fetch_user($user_id);
        $data['Role'] = $this->common_model->get_selected_values('access_roles','rolename,arid','status=1');
        $this->load->view('users/edit', $data);
    }
    public function view($id) {
        $user_id = base64_decode($id);
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('users');
            return;
        }
        $this->load->helper('form');
        $data['module_id'] = '99.02';
        $data['username'] = $this->mw_session['username'];
         $data['acces_management'] = $this->acces_management;
        $data['result'] = $this->users_model->fetch_user($user_id);
        $data['Role'] = $this->common_model->get_selected_values('access_roles','rolename,arid','status=1');
        $this->load->view('users/view', $data);
    }
    public function DatatableRefresh() {
        $dtSearchColumns = array('userid','userid', 'first_name', 'email', 'rolename', 'ar.status', 'userid');

        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $DTRenderArray = $this->users_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('checkbox', 'userid', 'name', 'email', 'rolename', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
            $User_Name = $dtRow['saluation'] . ' ' . $dtRow['first_name'] . ' ' . $dtRow['last_name'];
            for ($i = 0; $i < $TotalHeader; $i++) {
                if ($dtDisplayColumns[$i] == "status") {
                    if ($dtRow['status'] == 1) {
                        $status = '<span class="label label-sm label-info status-active" > Active </span>';
                    } else {
                        $status = '<span class="label label-sm label-danger status-inactive" > In Active </span>';
                    }
                    $row[] = $status;
                } else if ($dtDisplayColumns[$i] == "checkbox") {
                    $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['userid'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action='';
                    if ($acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete){
                    $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                                if ($acces_management->allow_view){
                                    $action .= '<li>
                                        <a href="'.$site_url.'users/view/'.base64_encode($dtRow['userid']).'">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                                }
                                if ($acces_management->allow_edit){
//                                    echo $site_url.'users/edit/'.base64_encode($dtRow['userid']);exit;
                                    $action .= '<li>
                                        <a href="'.$site_url.'users/edit/'.base64_encode($dtRow['userid']).'">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                                }
                                if ($acces_management->allow_delete){
                                    $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\''.$User_Name.'\',\''.base64_encode($dtRow['userid']).'\');" href="javascript:void(0)">
                                        <i class="fa fa-trash-o"></i>&nbsp;Delete
                                        </a>
                                    </li>';
                                }
                                $action .= '</ul>
                            </div>';
                    }else{
                        $action='<button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                Locked&nbsp;&nbsp;<i class="fa fa-lock"></i>
                            </button>';
                    }
                    
                    $row[] = $action;
                } else if ($dtDisplayColumns[$i] != ' ') {
                    $row[] = $dtRow[$dtDisplayColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    public function submit() {
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            redirect('users');
            return;
        }
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['username'] = $this->mw_session['username'];
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
        $this->form_validation->set_rules('loginid', 'Username', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('confirmpassword', 'Confirm password', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('roleid', 'Role', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('first_name', 'First name', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('last_name', 'Last name', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|max_length[50]');
        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $now = date('Y-m-d H:i:s');
            $data = array(
                'username' => strtolower($this->input->post('loginid')),
                'password' => $this->common_model->encrypt_password($this->input->post('password')),
                'role' => $this->input->post('roleid'),
                'saluation' => $this->input->post('saluation'),
                'first_name' => ucwords($this->input->post('first_name')),
                'last_name' => ucwords($this->input->post('last_name')),
                'address1' => $this->input->post('address'),
                'address2' => $this->input->post('address2'),
                'city' =>$this->input->post('city_id'),
                'state' => $this->input->post('state_id'),
                'country' => $this->input->post('country_id'),
                'pincode' => $this->input->post('pincode'),
                'email' => $this->input->post('email'),
                'email2' => $this->input->post('email2'),
                'mobile' => $this->input->post('mobile'),
                'contactno' => $this->input->post('contactno'),
                'fax' => $this->input->post('fax'),
                'note' => $this->input->post('note'),
                'avatar' => $this->input->post('avatar_path'),
                'status' => $this->input->post('status'),
                'addeddate' => $now,
                'addedby' => $this->mw_session['user_id'],
                'deleted' => 0
            );
            $this->common_model->insert('users', $data);
            $this->session->set_flashdata('flash_message', "User created successfully.");
            redirect('users');
        }
    }
    public function update($id){
        $id = base64_decode($id);
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            redirect('users');
            return;
        }
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['username'] = $this->mw_session['username'];
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><strong>Error: </strong>', '</div>');
        $this->form_validation->set_rules('loginid', 'Username', 'trim|required|max_length[50]');
        //$this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[50]');
        //$this->form_validation->set_rules('confirmpassword', 'Confirm password', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('roleid', 'Role', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('first_name', 'First name', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('last_name', 'Last name', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|max_length[50]');
        $login_data = $this->users_model->check_user($this->input->post('loginid', TRUE), $id);
        if ($login_data) {
            $errors = 'Login ID is Already Exist';
            $this->edit(base64_encode($id), $errors);
            return;
        }
        if ($this->form_validation->run() == FALSE) {
            $this->edit(base64_encode($id));
            return;
        } else {
            $now = date('Y-m-d H:i:s');
            $data = array(
                'username' => $this->input->post('loginid'),
                //'password' => $this->common_model->encrypt_password($this->input->post('password')),
                'role' => $this->input->post('roleid'),
                'saluation' => $this->input->post('saluation'),
                'first_name' => ucwords($this->input->post('first_name')),
                'last_name' => ucwords($this->input->post('last_name')),
                'address1' => $this->input->post('address'),
                'address2' => $this->input->post('address2'),
                'city' =>$this->input->post('city_id'),
                'state' => $this->input->post('state_id'),
                'country' => $this->input->post('country_id'),
                'pincode' => $this->input->post('pincode'),
                'email' => $this->input->post('email'),
                'email2' => $this->input->post('email2'),
                'mobile' => $this->input->post('mobile'),
                'contactno' => $this->input->post('contactno'),
                'fax' => $this->input->post('fax'),
                'note' => $this->input->post('note'),
                'avatar' => $this->input->post('avatar_path'),
                'status' => $this->input->post('status'),
                'modifieddate' => $now,
                'modifiedby' => $this->mw_session['user_id'],
                'deleted' => 0
            );
            $this->common_model->update('users','userid',$id, $data);
            $this->session->set_flashdata('flash_message', "User updated successfully");
            redirect('users');
        }
    }
    public function remove(){
        $alert_type='success';
        $message='';
        $title='';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        }else{
            $deleted_id = $this->input->Post('deleteid');
            $this->users_model->remove(base64_decode($deleted_id));  
            $message = "User deleted successfully.";
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type));
        exit;
    }
    public function record_actions($Action) {
        $action_id = $this->input->Post('id');
        $now = date('Y-m-d H:i:s');
        $alert_type='success';
        $message='';
        $title='';
        if ($Action == 1) {
            foreach ($action_id as $id) {
                $data = array(
                    'status' => 1,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']);
                $this->common_model->update('users', 'userid', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                // $StatusFlag = $this->users_model->CheckUserAssignRole($id);
                $StatusFlag=true;
                if($StatusFlag){
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('users', 'userid', $id, $data);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Status cannot be change. User(s) assigned to role!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag=false;
            foreach ($action_id as $id) {
                // $DeleteFlag = $this->users_model->CheckUserAssignRole($id);
                $DeleteFlag=true;
                if($DeleteFlag){
                    $this->common_model->delete('users', 'userid', $id);
                    $SuccessFlag=true;
                }else{
                    $alert_type = 'error';
                    $message= "Role cannot be deleted. User(s) assigned to role!<br/>"; 
                }
            }
            if($SuccessFlag){
                $message .= 'Role(s) deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message,'alert_type'=>$alert_type));
        exit;
    }
    public function validate() {
        $status = $this->users_model->validate($this->input->post());
        echo $status;
    }
    public function Check_emailid() {
        $emailid = $this->input->post('email_id', true);        
        $user = $this->input->post('user', true);
        echo $this->users_model->check_email($emailid,$user);
    }
    public function Check_loginid() {
        $login_id = $this->input->post('login_id', true);        
        $user = $this->input->post('user', true);
        echo $this->users_model->check_user($login_id,$user);
    }
    public function Check_firstlast() {
        $fname = $this->input->post('firstname', true);
        $lname = $this->input->post('lastname', true);
        $user = $this->input->post('user', true);
        echo $this->users_model->check_firstlast($fname,$lname,$user);        
    }
    public function validate_edit() {
        $status = $this->users_model->validate_edit($this->input->post());
        echo $status;
    }
    public function temp_avatar_upload(){
        $crop = new CropAvatar(
            $this->input->post('avatar_src'),
            $this->input->post('avatar_data'),
            $_FILES['avatar_file']
        );
        $response = array(
            'state'  => 200,
            'message' => $crop->getMsg(),
            'result' => $crop->getResult(),
            'preview' => $crop->getTemporery()
        );
          
        echo json_encode($response);
    }
}