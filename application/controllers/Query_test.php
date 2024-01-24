<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Query_test extends CI_Controller {

    public function __construct() {

        parent::__construct();
        if ($this->session->userdata('awarathon_session') == FALSE) {
            redirect('index');
        } else {
            $this->mw_session = $this->session->userdata('awarathon_session');
            $acces_management = CheckRights($this->mw_session['user_id'], 'feedback_form');
            if (!$acces_management->allow_access) {
                redirect('dashboard');
            }
            $this->acces_management = $acces_management;
            $this->load->model('feedback_form_model');  
            $this->load->model('common_model');  
        }
    }  
    public function index1() {        
        $query = " select imei,user_id from device_info di where id IN( select max(id) from device_info where imei !='' group by user_id)";
        $result = $this->db->query($query);
        $Q_data = $result->result();
        if(count((array)$Q_data) > 0){
            foreach($Q_data as $val){
                $Udata['imei'] = $val->imei;
                $this->common_model->update('device_users', 'user_id', $val->user_id, $Udata);
            }
        }
    }
    public function index2() {        
        $query = " select * from(SELECT questionset_id,workshop_id FROM
                    (SELECT distinct wqpr.questionset_id,wqpr.workshop_id from workshop_questionset_pre wqpr
                    UNION ALL
                    SELECT distinct wqps.questionset_id,wqps.workshop_id from workshop_questionset_post wqps
                    )A
            group by questionset_id,workshop_id) as uwq ";
        $result = $this->db->query($query);
        $Q_data = $result->result();        
        if(count((array)$Q_data) > 0){
            foreach($Q_data as $val){
                $qry = " UPDATE  workshop_questions upwq
                        LEFT JOIN
                                (select (@cnt := @cnt + 1) AS rowNumber,wq.question_id,wq.questionset_id,wq.workshop_id 
                                    from workshop_questions wq CROSS JOIN  (SELECT @cnt := 0) AS dummy 
                                WHERE wq.questionset_id = ".$val->questionset_id." AND wq.workshop_id = ".$val->workshop_id." ) as wq
                        ON      upwq.questionset_id = wq.questionset_id AND upwq.workshop_id = wq.workshop_id AND wq.question_id = upwq.question_id
                        SET     upwq.sorting = rowNumber
                        WHERE   upwq.questionset_id = ".$val->questionset_id." AND upwq.workshop_id = ".$val->workshop_id;
                
                $query = $this->db->query($qry);                                
            }
        }
    }

}
