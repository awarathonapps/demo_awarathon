<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Upload_script extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $acces_management = $this->check_rights('upload_script');
        if (!$acces_management->allow_access) {
            redirect('dashboard');
        }
        $this->acces_management = $acces_management;
        $this->load->model('upload_script_model');
    }
    public function index()
    {
        $data['module_id'] = '89';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;
        $this->load->view('upload_script/index', $data);
    }
    public function create()
    {
        $data['module_id'] = '89';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_add) {
            redirect('upload_script');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $data['CompnayResultSet'] = $this->common_model->get_selected_values('company', 'id,company_name', 'status=1');
        } else {
            $data['CompnayResultSet'] = array();
        }
        $data['Company_id'] = $Company_id;

        $parameter_result = $this->common_model->get_selected_values('parameter_mst', 'id,description', 'status=1 AND company_id="' . $Company_id . '"');
        $data['parameter_result'] = $parameter_result;

        $this->load->view('upload_script/create', $data);
    }
    public function edit($id)
    {
        $id = $this->security->xss_clean(base64_decode($id));
        $data['module_id'] = '89';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_edit) {
            redirect('upload_script');
            return;
        }
        $Company_id = $this->mw_session['company_id'];

        if ($Company_id == "") {
            $this->db->select('id,company_name');
            $this->db->from('company');
            $this->db->where('status', '1');
            $data['cmp_result'] = $this->db->get()->result();
        } else {
            $data['cmp_result'] = array();
        }

        $data['Company_id'] = $Company_id;

        if (isset($id) && !empty($id)) {
            $this->db->select('*');
            $this->db->from('script_mst');
            $this->db->where('id', $id);
            $data['result'] = $this->db->get()->row();
        } else {
            $data['result'] = '';
        }
        if (isset($data['result']) and count((array)$data['result']) > 0) {
            $data['script_id'] = $data['result']->id;
            $data['script_title'] = $data['result']->script_title;
            $data['script'] = $data['result']->script;
            $data['situation'] = $data['result']->situation;
            $data['question_limit'] = $data['result']->question_limit;
        } else {
            $data['script_id'] = '';
            $data['script_title'] = '';
            $data['script'] = '';
            $data['situation'] = '';
            $data['question_limit'] = '';
        }

        $this->load->view('upload_script/edit', $data);
    }
    public function view($id)
    {
        $id = $this->security->xss_clean(base64_decode($id));
        $data['module_id'] = '89';
        $data['username'] = $this->mw_session['username'];
        $data['acces_management'] = $this->acces_management;
        if (!$data['acces_management']->allow_view) {
            redirect('upload_script');
            return;
        }
        $Company_id = $this->mw_session['company_id'];
        if ($Company_id == "") {
            $this->db->select('id,company_name');
            $this->db->from('company');
            $this->db->where('status', '1');
            $data['cmp_result'] = $this->db->get()->result();
        } else {
            $data['cmp_result'] = array();
        }
        $data['Company_id'] = $Company_id;
        // $data['result'] = $this->common_model->get_value('parameter_label_mst', '*', 'id=' . $subparameter_id);
        if (isset($id)) {
            $this->db->select('*');
            $this->db->from('script_mst');
            $this->db->where('id', $id);
            $data['result'] = $this->db->get()->row();
        } else {
            $data['result'] = '';
        }

        if (isset($data['result']) and count((array)$data['result']) > 0) {
            $data['script_id'] = $data['result']->id;
            $data['script_title'] = $data['result']->script_title;
            $data['script'] = $data['result']->script;
            $data['situation'] = $data['result']->situation;
            $data['question_limit'] = $data['result']->question_limit;
        } else {
            $data['script_id'] = '';
            $data['script_title'] = '';
            $data['script'] = '';
            $data['situation'] = '';
            $data['question_limit'] = '';
        }
        $this->load->view('upload_script/view', $data);
    }
    public function DatatableRefresh()
    {
        $dtSearchColumns = array('id', 'script_title');
        $DTRenderArray = $this->common_libraries->DT_RenderColumns($dtSearchColumns);
        $dtWhere = $DTRenderArray['dtWhere'];
        $dtOrder = $DTRenderArray['dtOrder'];
        $dtLimit = $DTRenderArray['dtLimit'];

        $DTRenderArray = $this->upload_script_model->LoadDataTable($dtWhere, $dtOrder, $dtLimit);
        $output = array(
            "sEcho" => $this->input->get('sEcho') ? $this->input->get('sEcho') : 0,
            "iTotalRecords" => $DTRenderArray['dtPerPageRecords'],
            "iTotalDisplayRecords" => $DTRenderArray['dtTotalRecords'],
            "aaData" => array()
        );
        $dtDisplayColumns = array('id', 'script_title', 'addeddate', 'number_of_qna', 'Actions');
        $site_url = base_url();
        $acces_management = $this->acces_management;

        foreach ($DTRenderArray['ResultSet'] as $dtRow) {
            $row = array();
            $title = $dtRow['script_title'];
            $TotalHeader = count((array)$dtDisplayColumns);
            for ($i = 0; $i < $TotalHeader; $i++) {
                // if ($dtDisplayColumns[$i] == "status") {
                //     if ($dtRow['status'] == 1) {
                //         $status = '<span class="label label-sm label-info status-active" > Active </span>';
                //     } else {
                //         $status = '<span class="label label-sm label-danger status-inactive" > In Active </span>';
                //     }
                //     $row[] = $status;
                // } else if ($dtDisplayColumns[$i] == "checkbox") {
                //     $row[] = '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                //                 <input type="checkbox" class="checkboxes" name="id[]" value="' . $dtRow['id'] . '"/>
                //                 <span></span>
                //             </label>';
                // } else
                if ($dtDisplayColumns[$i] == "Actions") {
                    $action = '';
                    if ($acces_management->allow_add or $acces_management->allow_view or $acces_management->allow_edit or $acces_management->allow_delete) {
                        $action = '<div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle"  type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">';
                        if ($acces_management->allow_view) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'upload_script/view/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-eye"></i>&nbsp;View
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_edit) {
                            $action .= '<li>
                                        <a href="' . $site_url . 'upload_script/edit/' . base64_encode($dtRow['id']) . '">
                                        <i class="fa fa-pencil"></i>&nbsp;Edit
                                        </a>
                                    </li>';
                        }
                        if ($acces_management->allow_delete) {
                            $action .= '<li class="divider"></li><li>
                                        <a onclick="LoadDeleteDialog(\'' . $title . '\',\'' . base64_encode($dtRow['id']) . '\');" href="javascript:void(0)">
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

    public function submit()
    {
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
            $this->form_validation->set_rules('script_title', 'Script Title', 'required');
            $this->form_validation->set_rules('script', 'script', 'required');
            $this->form_validation->set_rules('situation', 'Situation', 'required');
            if ($this->form_validation->run() == FALSE) {
                $Message .= validation_errors();
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                $now = date('Y-m-d H:i:s');
                $question_limit = ($this->input->post('question_limit') != null) ? $this->security->xss_clean($this->input->post('question_limit')) : 30;
                $data = array(
                    'script_title'          => $this->security->xss_clean($this->input->post('script_title')),
                    'question_limit'        => $question_limit,
                    'script'                => $this->security->xss_clean($this->input->post('script')),
                    'situation'             => $this->security->xss_clean($this->input->post('situation')),
                    'addeddate'             => $now,
                    'addedby'               => $this->mw_session['user_id'],
                );
                $insert_id = $this->common_model->insert('script_mst', $data);

                // Python Script Call
                // ini_set('max_execution_time', 900);
                // $output = shell_exec("source /var/www/html/awarathon.com/trinity/trinity/bin/activate");
                // $output = shell_exec(sprintf("/var/www/html/awarathon.com/trinity/trinity/bin/python /var/www/html/awarathon.com/trinity/rasa/gpt3_QA.py --company_id='".$Company_id."' --script_id='".$insert_id."' --noq='".$question_limit."' --dbname='atom_aidemo' 2>&1"));
                // $output = json_decode($output);
                // print_r($output);
                // Python Script Call end

                //Add data in table to generate QnA from CRON job
                $data = array(
                    'company_id'       => $Company_id,
                    'script_id'        => $insert_id,
                    'question_limit'   => $question_limit,
                    'addeddate'        => $now,
                    'modifieddate'     => $now,
                    'status'           => 0
                );
                $this->common_model->insert('script_generate_qna', $data);

                $Message .= "Script Save Successfully..Questions and Answers will be generated in sometime.";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        $Rdata['insert_id'] = $insert_id;

        echo json_encode($Rdata);
    }

    // public function generate_qna($question_limit, $script_id){
    //     if ($this->mw_session['company_id'] == "") {
    //         $this->form_validation->set_rules('company_id', 'Company name', 'required');
    //         $Company_id = $this->input->post('company_id');
    //     } else {
    //         $Company_id = $this->mw_session['company_id'];
    //     }
    //     $question_limit = isset($question_limit) ? $question_limit : 30;
    //     // Python Script Call
    //     ini_set('max_execution_time', 0);
    //     $output = shell_exec("source /var/www/html/awarathon.com/trinity/trinity/bin/activate");
    //     $output = shell_exec(sprintf("/var/www/html/awarathon.com/trinity/trinity/bin/python /var/www/html/awarathon.com/trinity/rasa/gpt3_QA.py --company_id='".$Company_id."' --script_id='".$script_id."' --noq='".$question_limit."' --dbname='atom_aidemo' 2>&1"));
    //     $output = json_decode($output);
    //     print_r($output);
    //     // Python Script Call end
    // }

    public function check_qna_progress($question_limit, $script_id){
        if ($this->mw_session['company_id'] == "") {
            $this->form_validation->set_rules('company_id', 'Company name', 'required');
            $company_id = $this->input->post('company_id');
        } else {
            $company_id = $this->mw_session['company_id'];
        }
        $question_limit = isset($question_limit) ? $question_limit : 30;

        $no_qna = 0;
        $is_qna_generated   = $this->upload_script_model->get_value('script_generate_qna', 'id', 'company_id="' . $company_id . '" AND script_id="' . $script_id . '" AND status=1');
        if(!empty($is_qna_generated)){
            $no_qna = $question_limit;  //return question limit as qna process completed
        }else{
            $no_qna_generated   = $this->upload_script_model->get_value('assessment_script_qna', 'COUNT(*) as count', 'company_id="' . $company_id . '" AND script_id="' . $script_id . '"');
            $no_qna = (!empty($no_qna_generated)) ? $no_qna_generated[0]->count : 0;
        }
        echo $no_qna;
    }

    public function update($Encode_id)
    {
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
            $this->form_validation->set_rules('script_title', 'Script Title', 'required');
            // $this->form_validation->set_rules('question_limit', 'Question Limit', 'required');
            $this->form_validation->set_rules('script', 'script', 'required');
            $this->form_validation->set_rules('situation', 'Situation', 'required');
            if ($this->form_validation->run() == FALSE) {
                $Message .= validation_errors();
                $SuccessFlag = 0;
            }
            if ($SuccessFlag) {
                $now = date('Y-m-d H:i:s');
                $question_limit = ($this->input->post('question_limit') != null) ? $this->security->xss_clean($this->input->post('question_limit')) : 0;
                $data = array(
                    'script_title'          => $this->security->xss_clean($this->input->post('script_title')),
                    'question_limit'        => $question_limit,
                    'script'                => $this->security->xss_clean($this->input->post('script')),
                    'situation'             => $this->security->xss_clean($this->input->post('situation')),
                    'modifiedate'           => $now,
                    'modifiedby'            => $this->mw_session['user_id'],
                );
                $insert_id = $this->common_model->update('script_mst', 'id', $id, $data);

                // Python Script Call
                // $output = shell_exec("source /var/www/html/awarathon.com/trinity/trinity/bin/activate");
                // $output = shell_exec(sprintf("/var/www/html/awarathon.com/trinity/trinity/bin/python /var/www/html/awarathon.com/trinity/gpt3_QA.py --company_id='".$Company_id."' --script_id='".$insert_id."' --noq='".$question_limit."' --dbname='atom_aidemo' 2>&1"));
                // $output = json_decode($output);
                // var_dump($output);
                // die();

                $Message .= "Script Update Successfully..";
            }
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }
    public function remove()
    {
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $deleted_id = $this->security->xss_clean($this->input->post('deleteid'));
            $DeleteFlag = 1;
            if ($DeleteFlag) {
                $this->common_model->delete('script_mst', 'id', base64_decode($deleted_id));
                $this->common_model->delete('script_generate_qna', 'script_id', base64_decode($deleted_id));
                $message = "Script deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Script cannot be deleted.";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }

    // Fetch Question Answer Datatable Refresh 
    public function fetch_question_answer()
    {
        $html               = '';
        $company_id         = $this->security->xss_clean($this->input->post('company_id', true));
        $script_id          = $this->security->xss_clean($this->input->post('script_id', true));
        $view               = ($this->input->post('type', true) ? $this->input->post('type', true) : '');
        if (isset($script_id) && !empty($script_id)) {
            $fetch_question_answer   = $this->upload_script_model->get_value('assessment_script_qna', '*', 'company_id="' . $company_id . '" AND script_id="' . $script_id . '"');
            $qna_id = [];
            foreach ($fetch_question_answer as $fq) {
                $qna_id[] = $fq->id;
            }
            if (count((array)$qna_id) > 0) {
                $data['mapped_id'] = $this->upload_script_model->get_value_result('trinity_trans', 'question_id', 'question_id IN (' . implode(',', $qna_id) . ')');
            } else {
                $data['mapped_id'] = [];
            }
            $data['fetch_question_answer'] = $fetch_question_answer;
            $data['view']                  = $view;
            $html                          = $this->load->view('upload_script/load_question_answer', $data, true);
        } else {
            $html = '';
        }
        $output['html']            = $html;
        $output['success']         = "true";
        $output['message']         = "";
        echo json_encode($output);
    }
    // Fetch Question Answer Datatable Refresh 


    public function que_ans_edit($Encode_id)
    {
        $data['QAid'] = base64_decode($Encode_id);
        if ($this->mw_session['company_id'] == "") {
            $this->db->select('company_id');
            $this->db->from('assessment_script_qna');
            $this->db->where('id', $data['QAid']);
            $Company = $this->db->get()->row();
            $company_id = $Company->company_id;
        } else {
            $company_id = $this->mw_session['company_id'];
        }

        if (isset($data['QAid']) && !empty($data['QAid'])) {
            $this->db->select('*');
            $this->db->from('assessment_script_qna');
            $this->db->where('company_id', $company_id);
            $this->db->where('id', $data['QAid']);
            $result = $this->db->get()->row();
        } else {
            $result = '';
        }
        if (isset($result) and count((array)$result) > 0) {
            $QAid = (int)$result->id;
            $question = $result->question;
            $answer = $result->answer;
            $script_id = $result->script_id;
        } else {
            $QAid = '';
            $question = '';
            $answer = '';
            $script_id = '';
        }
        $data['company_id'] = $company_id;
        $data['QAid'] = $QAid;
        $data['question'] = $question;
        $data['answer'] = $answer;
        $data['script_id'] = $script_id;
        $this->load->view('upload_script/que_ans_modal', $data);
    }


    public  function update_que_ans($QAid)
    {
        $Message = '';
        $SuccessFlag = 1;
        $QAid = base64_decode($QAid);
        if ($QAid != '' && isset($QAid)) {
            $SuccessFlag = 1;
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
                $this->form_validation->set_rules('question', 'Question', 'required');
                $this->form_validation->set_rules('answer', 'Answer', 'required');
                if ($this->form_validation->run() == FALSE) {
                    $Message .= validation_errors();
                    $SuccessFlag = 0;
                }
                if ($SuccessFlag) {
                    $data = array(
                        'question'  => $this->security->xss_clean($this->input->post('question')),
                        'answer'    => $this->security->xss_clean($this->input->post('answer'))
                    );
                    $update_id = $this->common_model->update('assessment_script_qna', 'id', $QAid, $data);
                    $Message .= "Question Answer Update Successfully..";
                }
            }
        } else {
            $Message .= "Question Answer Update Failed..!!";
            $SuccessFlag = 0;
        }
        $Rdata['success'] = $SuccessFlag;
        $Rdata['Msg'] = $Message;
        echo json_encode($Rdata);
    }


    public function delete_que_ans()
    {
        $alert_type = 'success';
        $message = '';
        $title = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $deleted_id = $this->security->xss_clean($this->input->post('deleteid'));
            $DeleteFlag = 1;
            if ($DeleteFlag) {
                $this->common_model->delete('assessment_script_qna', 'id', $deleted_id);
                $message = "Question and Answer deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Question and Answer cannot be deleted.";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }


    public function Check_script_title()
    {
        $script_title = $this->security->xss_clean($this->input->post('script_title', true));
        $script_id    = $this->security->xss_clean($this->input->post('script_id', true));

        if ($script_title != '') {
            $this->db->select('script_title')->from('script_mst');
            $this->db->where('script_title', $script_title);
            if ($script_id != '') {
                $this->db->where('id!= ', $script_id);
            }
            $check = $this->db->get()->row();
            echo (count((array)$check) > 0 ? true : false);
        }
    }

    public function remove_script()
    {
        $alert_type = 'success';
        $message = '';
        $acces_management = $this->acces_management;
        if (!$acces_management->allow_delete) {
            $alert_type = 'error';
            $message = 'You have no rights to delete,Contact Administrator for details.';
        } else {
            $deleted_id = $this->security->xss_clean($this->input->post('delete_id'));
            $DeleteFlag = 1;
            if ($DeleteFlag) {
                $this->common_model->delete('script_mst', 'id', $deleted_id);
                $this->common_model->delete('assessment_script_qna', 'script_id', $deleted_id);
                $this->common_model->delete('script_generate_qna', 'script_id', $deleted_id);
                $message = "Script deleted successfully.";
            } else {
                $alert_type = 'error';
                $message = "Script cannot be deleted.";
            }
        }
        echo json_encode(array('message' => $message, 'alert_type' => $alert_type));
        exit;
    }
}