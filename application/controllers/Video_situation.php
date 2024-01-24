<?php
use Vimeo\Vimeo;
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Video_situation extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $acces_management = $this->check_rights('video_situation');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('video_situation_model');
    }

    public function index() {
        $data['module_id'] = '13.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['assessment_type'] = $this->common_model->get_selected_values('assessment_type', 'id,description', 'status=1');
        $this->load->view('video_situation/index', $data);
    }

    public function create() {
        $data['module_id'] = '13.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('video_situation');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['assessment_type'] = $this->common_model->get_selected_values('assessment_type', 'id,description,default_selected', 'status=1');
        $this->load->view('video_situation/create', $data);
    }

    public function edit($id) {
        $Q_id = base64_decode($id);
        $data['module_id'] = '13.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('video_situation');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmp_result'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['cmp_result'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['result'] = $this->common_model->get_value('assessment_question', '*', 'id=' . $Q_id);
        $data['assessment_type'] = $this->common_model->get_selected_values('assessment_type', 'id,description,default_selected', 'status=1');
        $this->load->view('video_situation/edit', $data);
    }

    public function view($id, $step = 1) {
        $parameter_id = base64_decode($id);
        $data['module_id'] = '13.02';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('video_situation');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['cmp_result'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['cmp_result'] = array();
        }
        $data['Company_id'] = $Company_id;
        $data['result'] = $this->common_model->get_value('assessment_question', '*', 'id=' . $parameter_id);
        $data['assessment_type'] = $this->common_model->get_selected_values('assessment_type', 'id,description,default_selected', 'status=1');
        $this->load->view('video_situation/view', $data);
    }

    public function DatatableRefresh() {
        $dtSearchColumns = array('q.id', 'q.id', 'at.description', 'q.question', 'q.read_timer', 'q.response_timer', 'q.addeddate', 'q.status');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        if ($this->mw_session['company_id'] == "") {
            $cmp_id = $this->input->get('filter_company_id');
        } else {
            $cmp_id = $this->mw_session['company_id'];
        }
        if ($cmp_id != "") {
            if ($dtWhere <> '') {
                $dtWhere .= " AND q.company_id  = " . $cmp_id;
            } else {
                $dtWhere .= " WHERE q.company_id  = " . $cmp_id;
            }
        }
        $question_type = $this->input->get('question_type') !=null ? $this->input->get('question_type') : '';
        if ($question_type != "") {
            $dtWhere .= " AND q.is_situation  = " . $question_type;
        }
        $assessment_type = $this->input->get('assessment_type');
        if ($assessment_type != "") {
            $dtWhere .= " AND q.assessment_type  = " . $assessment_type;
        }
        $status = $this->input->get('filter_status');
        if ($status != "") {
            $dtWhere .= " AND q.status  = " . $status;
        }
        $DTRenderArray = $this->video_situation_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        // $dtDisplayColumns = array('checkbox', 'id', 'assessment_type', 'question', 'read_timer', 'response_timer', 'addeddate', 'status', 'Actions');
        $dtDisplayColumns = array('checkbox', 'id', 'assessment_type', 'question', 'embed_code' ,'read_timer', 'response_timer', 'addeddate', 'status', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $TotalHeader = count((array)$dtDisplayColumns);
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
                                <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['id'] . '"/>
                                <span></span>
                            </label>';
                } else if ($dtDisplayColumns[$i] == "embed_code") {
                    $row[] = "<textarea col='3' row='3'>".$dtRow['embeddings']."</textarea>";
                } else if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if ($acces_management->allow_add OR $acces_management->allow_view OR $acces_management->allow_edit OR $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_view) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'video_situation/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'video_situation/edit/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_delete) {
                            $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\'' . base64_encode($dtRow['id']) . '\');" href="javascript:void(0)">
                                        <i class="fa fa-trash-o"></i>&nbsp;Delete
                                        </a>
                                    </li>';
                        }
                        $action .= '</ul>
                            </div>';
                    } else {
                        $action = '<button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
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
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_add) {
            $Message .= "You have no rights to Add,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('assessment_type', 'Assessment Type', 'required');
            if($this->input->post('question_option')=='0')
            {
                 $this->form_validation->set_rules('question', 'Question', 'required');
            }
          //  $this->form_validation->set_rules('question', 'Question', 'required');
            // $this->form_validation->set_rules('question_type', 'Question type', 'required');
            // $this->form_validation->set_rules('weightage', 'Weightage', 'required');
            $this->form_validation->set_rules('status', 'Status', 'required');
            if ($this->form_validation->run() == FALSE) {
                $Message .= validation_errors();
                $SuccessFlag = 0;
            }
             // for video//
            $question_path='';
            if($this->input->post('question_option')=='1')
            {
                
                 $file_mimes = array('video/x-flv', 'video/mp4', 'application/x-mpegURL', 'video/MP2T', 'video/3gpp', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv');
                
                if(isset($_FILES['file']['name']) ) {
                    
                    if(!in_array($_FILES['file']['type'], $file_mimes)){
                        $Message = 'Invalid Video Format';
                        $SuccessFlag = 0;
                    }
                    else
                    {
                        $resposnse =$this->uploadon_vimeo($_FILES['file']['tmp_name']);
                        $question_path=$resposnse['video_id'];
                    }
                }
                else
                {
                      $Message .= 'Please select video';
                      $SuccessFlag = 0;
                }
                
            }
            
            if($this->input->post('question_option')=='2')
            {
               $question_path=$this->input->post('question');
            }

            if($this->input->post('response_timer') > 300){
                $Message .= 'Please enter a response time less than or equal to 300';
                $SuccessFlag = 0;
            }
            
            $question=($this->input->post('question_option')=='0')?$this->input->post('question'):$this->input->post('question_tital');
            $question = preg_replace("/\s+/", " ", preg_replace("/\&nbsp\;+/", " ", preg_replace("/\<p\>\&nbsp\;\<\/p\>/", "", trim($question))));
            //End Video//
            if ($SuccessFlag) {
                $now = date('Y-m-d H:i:s');
                $data = array(
                    'company_id' => $Company_id,
                    'assessment_type' => $this->input->post('assessment_type'),
                    'is_situation' => $this->input->post('question_type') !=null ? $this->input->post('question_type') : '0',
                    'question_format' => $this->input->post('question_option'),
                    'question' => htmlspecialchars_decode($question,ENT_QUOTES),
                    'question_path' => $question_path,
                    'weightage' => $this->input->post('weightage') !=null ? $this->input->post('weightage') : '1',
                    'read_timer' => $this->input->post('read_timer'),
                    'response_timer' => $this->input->post('response_timer'),
					'timer' => $this->input->post('response_timer'),
                    //'poweredby' => $this->input->post('powered_by'),
                    'assessor_guide' => $this->input->post('assessor_guide'),
                    'slide_heading' => $this->input->post('slide_heading'),
                    'slide_description' => $this->input->post('slide_description'),
                    'status' => $this->input->post('status'),
                    'addeddate' => $now,
                    'addedby' => $this->mw_session['user_id'],
                );
                $insert_id=$this->common_model->insert('assessment_question', $data);
             
                $Message .="Question Save Successfully..";
                //call python script for embeddings - SPOTLIGHT
                try {
                    $pyc_output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/embeddings.py --question_id='".$insert_id."' --question='".str_replace("'","",$question)."' --mode='INSERT' 2>&1"));
                }catch(Exception $e) {
                    $Message .= $e->getMessage();
                    $SuccessFlag = 0;
                }
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function embade_update()
    {
        $non_embading = $this->video_situation_model->non_embading();
        echo "<pre>";
        print_r($non_embading);
      
        $i=0;
        foreach($non_embading as $em)
        {
            $id=$em->id;
            $question=$em->question;

             $pyc_output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/ai/python/embeddings.py --question_id='".$id."' --question='".str_replace("'","",$question)."' --mode='INSERT' 2>&1"));
              
            $i++;
               
          
        }
        echo $i;
       
    }
    public function update($Encode_id) {
        $id = base64_decode($Encode_id);
        $SuccessFlag = 1;
        $Message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_edit) {
            $Message .= "You have no rights to Edit,Contact Administrator for rights.";
            $SuccessFlag = 0;
        } else {
            $this->load->library('form_validation');
            if ($this->mw_session['company_id'] == "") {
                $this->form_validation->set_rules('company_id', 'Company name', 'required');
                $Company_id = $this->input->post('company_id');
            } else {
                $Company_id = $this->mw_session['company_id'];
            }
            $this->form_validation->set_rules('assessment_type', 'Assessment Type', 'required');
            // $this->form_validation->set_rules('question_type', 'Question type', 'required');
            //$this->form_validation->set_rules('question', 'Question', 'required');
            if($this->input->post('question_option')=='0')
            {
                 $this->form_validation->set_rules('question', 'Question', 'required');
            }
            // $this->form_validation->set_rules('weightage', 'Weightage', 'required');
            $this->form_validation->set_rules('status', 'Status', 'required');
            if ($this->form_validation->run() == FALSE) {
                $Message .= validation_errors();
                $SuccessFlag = 0;
            }
            // for video//
             $question_path='';
            if($this->input->post('question_options')=='1')
            {
               
                 $file_mimes = array('video/x-flv', 'video/mp4', 'application/x-mpegURL', 'video/MP2T', 'video/3gpp', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv');
                
                if(isset($_FILES['file']['name']) ) {
                    
                    if(!in_array($_FILES['file']['type'], $file_mimes)){
                        $Message = 'Invalid Video Format';
                        $SuccessFlag = 0;
                    }
                    else
                    {
                        $resposnse =$this->uploadon_vimeo($_FILES['file']['tmp_name']);
                        $question_path=$resposnse['video_id'];
                    }
                }
                else
                {
                      $question_path=$this->input->post('question');
                }
                
            }
            
            if($this->input->post('question_options')=='2')
            {
               $question_path=$this->input->post('question');
            }

            if($this->input->post('response_timer') > 300){
                $Message .= 'Please enter a response time less than or equal to 300';
                $SuccessFlag = 0;
            }
            
            $question=($this->input->post('question_options')=='0')?$this->input->post('question'):$this->input->post('question_tital');
            $question = preg_replace("/\s+/", " ", preg_replace("/\&nbsp\;+/", " ", preg_replace("/\<p\>\&nbsp\;\<\/p\>/", "", trim($question))));
            if ($SuccessFlag) {
                $now = date('Y-m-d H:i:s');
                $data = array(
                    'company_id' => $Company_id,
                    // 'assessment_type' => $this->input->post('assessment_type'),
                    // 'is_situation' => $this->input->post('question_type') !=null ? $this->input->post('question_type') : '0',
                    'question' => htmlspecialchars_decode($question,ENT_QUOTES),
                    'question_path' => $question_path,
                    'weightage' => $this->input->post('weightage') !=null ? $this->input->post('weightage') : '1',
                    'read_timer' => $this->input->post('read_timer'),
                    'response_timer' => $this->input->post('response_timer'),
					'timer' => $this->input->post('response_timer'),
                    //'poweredby' => $this->input->post('powered_by'),
                    'assessor_guide' => $this->input->post('assessor_guide'),
                    'slide_heading' => $this->input->post('slide_heading'),
                    'slide_description' => $this->input->post('slide_description'),
                    'status' => $this->input->post('status'),
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id'],
                );
                $this->common_model->update('assessment_question', 'id', $id, $data);
                $Message .="Question Update Successfully..";
            }
            //call python script for embeddings - SPOTLIGHT
            try {
                $is_embedded = $this->common_model->get_value('ai_embeddings', 'id', 'question_id='.$id);
                if(!empty($is_embedded)){
                    $pyc_output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/embeddings.py --question_id='".$id."' --question='".str_replace("'","",$question)."' --mode='UPDATE' 2>&1"));
                }else{
                    $pyc_output = shell_exec(sprintf("python3.7 /var/www/html/awarathon.com/demo/python/embeddings.py --question_id='".$id."' --question='".str_replace("'","",$question)."' --mode='INSERT' 2>&1"));
                }
                // var_dump($pyc_output);
            }catch(Exception $e) {
                $Message .= $e->getMessage();
                $SuccessFlag = 0;
            } 
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function selectImage(){
        $Message = '';
        $Success = 1;
        $que_image = '';
        $isUpdate = $this->input->post('isUpdate');
        if (isset($_FILES['file']['name']) && $_FILES['file']['size'] > 0) {
            $config = array();
            $que_image = str_replace(' ','_',$_FILES['file']['name']);
            // $que_image = time();
            $upload_Path = './assets/uploads/questions';
            $config['upload_path'] = $upload_Path;
            $config['overwrite'] = FALSE;
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            // $config['max_width'] = 750;
            // $config['max_height'] = 400;
            // $config['min_width'] = 750;
            // $config['min_height'] = 400;
            $config['file_name'] = $que_image;
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('file')) {
                $Message = $this->upload->display_errors();
                $Success = 0;
            } else {
                if ($isUpdate) {
                    if($this->input->post('que_image') !=''){
                        $Path = $upload_Path . '/' . $this->input->post('que_image');
                        if (file_exists($Path)) {
                            unlink($Path);
                        }
                    }
                }
                $Message = 'Image uploaded successfully';
                // $ImgArrays = explode('.', $_FILES['file']['name']);
                // $que_image .="." . $ImgArrays[1];
            }
        }
        $data['Message'] = $Message;
        $data['Success'] = $Success;
        $data['que_image']= $que_image;
        echo json_encode($data);
    }
    public function selectvideo()
    {
        $Message       = '';
        $Success       = 1;
        $file_mimes = array('video/x-flv', 'video/mp4', 'application/x-mpegURL', 'video/MP2T', 'video/3gpp', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv');
        $isuploaded=0;
        $video_id='0';
        
        if(isset($_FILES['file']['name']) ) {
            $isuploaded=1;
            if(!in_array($_FILES['file']['type'], $file_mimes)){
                $Message = 'Invalid Video Format';
                $Success = 0;
            }else{
                //die("here there");
                //var_dump($_FILES['file']['tmp_name']);die;
                $resposnse = $this->uploadon_vimeo($_FILES['file']['tmp_name']);
                print_r($resposnse['video_id']);
                die;
                if(!$resposnse['Success'] || $resposnse['Success']==""){
                    die("here there");
                    $Message = $resposnse['Message'];
                    $Success = 0;
                    $video_id =0;
                }else{
                    $video_id = $resposnse['video_id'];
                }
            }
        }


        echo $video_id."  ".$Success."  ".$Message;die;
    }
    public function uploadon_vimeo($file_name){
	   
        $vimeo_client_id     = '6e257ab857cdfdbf0078f9314e7a7f3391df1110';
        $vimeo_client_secret = 'IyfnxxSFhzLoY/UZuzNOiFglDkr32w7cg5w7a36MX1yHv+ovdXY9I88kNc0eJrk7X2qddumo4nLiyKFoS0+gA5WATnF1BZXSFbIAObs5e9sifAEzzfMySTUG+gAd0zST';
        $vimeo_access_token  = 'd4ebc2f67ad07a412c2302238ea7fe4e';
        $lib = new Vimeo($vimeo_client_id, $vimeo_client_secret,$vimeo_access_token);
      
        $Success=1;
        $Message='';
        $video_id         = '';
        if (!empty($vimeo_access_token)) {
            $lib->setToken($vimeo_access_token);
        }
        try {
            $video_description = "Video Uploaded By Awarathon";
            // $file_name ='/var/www/html/awarathon.com/aarth/contents/videos/553551/1613133263_b840873c6ad6bb126673.mp4';
            // echo $file_name;
            $uri = $lib->upload($file_name, array(
                'name' => 'CDLLMS_'.time(),
                'description' => $video_description,
                'privacy'     => array(
                    "download" => "false",
                    "embed"    => "public",
                    "comments" => "nobody",
                    "view"     => "unlisted")
            ));
            
           
            $video_data = $lib->request($uri.'?fields=link');
            $temp_video_array = array();
			if(!empty($video_data)){
              
				if($video_data['status']== '200'){
					$temp_video_array  = explode("/", $video_data['body']['link']);
					$video_id = $temp_video_array[3].'?h='.$temp_video_array[4];
				}
			}
			

//             echo '<pre>'; print_r($video_data); die;
			//echo $video_id; exit;
            // if ($uri!=''){
                // $temp_video_array  = explode("/", $uri);
                // print_r($temp_video_array);
				// exit;
                // if (isset($temp_video_array[2])){
                    // $video_id = $temp_video_array[2];
                // }
            // }
            
        } catch (VimeoUploadException $e) {
            $Success=0;
            $Message='Error uploading ' . $file_name . "\n";
            $Message .= 'Server reported: ' . $e->getMessage() . "\n";
            // We may have had an error. We can't resolve it here necessarily, so report it to the user.
        } catch (VimeoRequestException $e) {
            $Message='There was an error making the request.' . "\n";
            $Message .= 'Server reported: ' . $e->getMessage() . "\n";
            $Success=0;
        }
        $data['Message'] =$Message;
        $data['Success'] =$Success;
        $data['video_id'] ='https://player.vimeo.com/video/'.$video_id;
        return $data;
	}
    public function remove() {
        $alert_type = 'success';
        $message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $deleted_id = $this->input->post('deleteid');
            $mapped_set = $this->common_model->get_value('assessment_trans', 'id', 'question_id=' . base64_decode($deleted_id));
            
            
            if (count((array)$mapped_set) == 0) {
                $this->common_model->delete('assessment_question', 'id', base64_decode($deleted_id));
               
                $message = "Question deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Question Already mapped to assessment,cannot be deleted.";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function record_actions($Action) {
        $action_id = $this->input->post('id');
        if (count((array)$action_id) == 0) {
            echo json_encode(array('message' => "Please select record from the list", 'alert_type' => 'error'));
            exit;
        }
        $now = date('Y-m-d H:i:s');
        $alert_type = 'success';
        $message = '';
        $title = '';
        if ($Action == 1) {
            foreach ($action_id as $id) {
                $data = array(
                    'status' => 1,
                    'modifieddate' => $now,
                    'modifiedby' => $this->mw_session['user_id']);
                $this->common_model->update('assessment_question', 'id', $id, $data);
            }
            $message = 'Status changed to active successfully.';
        } else if ($Action == 2) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $StatusFlag = 1;
                if ($StatusFlag) {
                    $data = array(
                        'status' => 0,
                        'modifieddate' => $now,
                        'modifiedby' => $this->mw_session['user_id']);
                    $this->common_model->update('assessment_question', 'id', $id, $data);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Status cannot be change. !<br/>";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Status changed to in-active sucessfully.';
            }
        } else if ($Action == 3) {
            $SuccessFlag = false;
            foreach ($action_id as $id) {
                $mapped_set = $this->common_model->get_value('assessment_trans', 'id', 'question_id=' . $id);
                if (count((array)$mapped_set) == 0) {
                    $this->common_model->delete('assessment_question', 'id', $id);
                    $SuccessFlag = true;
                } else {
                    $alert_type = 'error';
                    $message = "Question cannot be deleted.";
                }
            }
            if ($SuccessFlag) {
                $message .= 'Question deleted successfully.';
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    public function Check_question() {
        $assessment_type = $this->input->post('assessment_type', true);
        $question = $this->input->post('question', true);
        $question_id = $this->input->post('question_id', true);
        if ($assessment_type != '') {
            echo $this->video_situation_model->check_question_exist($assessment_type, $question, $question_id);
        }
    }

}
