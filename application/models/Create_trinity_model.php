<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Create_trinity_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit)
    {
        //KRISHNA -- Spotlight changes 
        $query = "  SELECT am.id,am.company_id,am.assessment_type as assessment_type_id,
                    -- IF(am.assessment_type=2,'Spotlight','Roleplay') AS ass_type,
                    CASE WHEN am.assessment_type=2 THEN 'Spotlight' WHEN  am.assessment_type=3 THEN 'Trnity' ELSE 'Roleplay' END AS ass_type,
                    am.assessment,at.description AS assessment_type, 
                    IF(is_situation=1,'Situation','Question') AS question_type, 
                    am.status,DATE_FORMAT(am.start_dttm,'%d-%m-%Y %H:%i') AS start_dttm, 
                    DATE_FORMAT(am.end_dttm,'%d-%m-%Y %H:%i') AS end_dttm
                    FROM assessment_mst am
                    -- LEFT JOIN assessment_trainer_result atr ON atr.assessment_id = am.id
                    LEFT JOIN assessment_type as at ON at.id = am.assessment_type ";
        // -- LEFT JOIN assessment_results ar ON ar.assessment_id = atr.assessment_id ";
        $query .= " $dtWhere GROUP BY am.id $dtOrder $dtLimit ";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = " SELECT  am.id as total FROM assessment_mst am "
            // . " LEFT JOIN assessment_trainer_result atr ON atr.assessment_id = am.id "
            . " LEFT JOIN assessment_type at ON at.id = am.assessment_type"
            // . " LEFT JOIN assessment_results ar ON ar.assessment_id = atr.assessment_id "
            . " $dtWhere GROUP BY am.id ";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = count((array)$data_array);
        return $data;
    }
    public function check_assessment($Company_id = '', $assessment, $assessment_type = '', $assessment_id = '')
    {
        $querystr = "Select assessment from assessment_mst where assessment like '" . str_replace("'", "\'", $assessment) . "'";
        if ($Company_id != '') {
            $querystr .= " and company_id=" . $Company_id;
        }
        // if ($assessment_type != '') {
        //     $querystr.=" and assessment_type=" . $assessment_type;
        // }
        if ($assessment_id != '') {
            $querystr .= " and id!=" . $assessment_id;
        }
        // echo $querystr;
        $query = $this->db->query($querystr);
        return (count((array)$query->row()) > 0 ? true : false);
    }

    public function get_mapped_qna($company_id, $assessment_id, $script_id, $question_id=0){
        $query = "SELECT DISTINCT asq.id, asq.question as question, asq.answer as q_answer, tts.sentence_keyword as answer FROM assessment_script_qna as asq 
                LEFT JOIN trinity_trans as tt ON tt.question_id=asq.id AND tt.script_id=asq.script_id 
                LEFT JOIN trinity_trans_sparam as tts ON tts.assessment_id=tt.assessment_id AND tts.question_id=tt.question_id
                WHERE tt.assessment_id=$assessment_id";
        if(!empty($question_id) && $question_id!==0){
            $query = " AND tt.question_id=$question_id ";
        }
        $query = $this->db->query($query);
        $result = $query->result();

        if(empty($result)){
            $query = "SELECT id, question, answer FROM assessment_script_qna WHERE company_id=$company_id AND script_id=$script_id";
            $query = $this->db->query($query);
            $result = $query->result();
        }
        return $result;
    }

    public function get_TraineeRegionList($company_id)
    {
        $query = "SELECT id,region_name FROM region where status=1 AND company_id = $company_id AND"
            . " id IN(select distinct region_id FROM device_users where company_id=$company_id) order by region_name ";
        $query = $this->db->query($query);
        return $query->result();
    }
    public function get_TrainerRegionList($company_id)
    {
        $query = "SELECT id,region_name FROM region where status=1 AND company_id = $company_id AND"
            . " id IN(select distinct region_id FROM company_users where company_id=$company_id) order by region_name ";
        $query = $this->db->query($query);
        return $query->result();
    }
    public function LoadUsersDataTable($dtWhere, $dtOrder, $dtLimit)
    {
        $query = "SELECT u.user_id,u.company_id,CONCAT(u.firstname,' ',u.lastname) as name,u.emp_id,u.area,"
            . " u.email,u.mobile,u.otp,u.otp_last_attempt,u.status,u.istester,tr.region_name,u.department "
            . " FROM device_users as u "
            . " LEFT JOIN region as tr ON tr.id=u.region_id   ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT count(u.user_id) as total FROM device_users as u "
            . " LEFT JOIN region as tr ON tr.id=u.region_id   ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->row();
        $data['dtTotalRecords'] = $data_array->total;
        return $data;
    }
    public function LoadManagersDataTable($dtWhere, $dtOrder, $dtLimit)
    {
        $query = "SELECT u.userid,u.company_id,CONCAT(u.first_name,' ',u.last_name) as name,"
            . " tr.region_name,u.email,u.username,d.description as designation "
            . " FROM company_users as u "
            . " LEFT JOIN region as tr ON tr.id=u.region_id LEFT JOIN designation as d on d.id=u.designation_id   ";
        $query .= "$dtWhere $dtOrder $dtLimit";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT count(u.userid) as total FROM company_users as u "
            . " LEFT JOIN region as tr ON tr.id=u.region_id LEFT JOIN designation as d on d.id=u.designation_id   ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->row();
        $data['dtTotalRecords'] = $data_array->total;
        return $data;
    }
    public function LoadCandidateDataTable($dtWhere, $dtOrder, $dtLimit)
    {
        $query = "SELECT atr.user_id,atr.trainer_id,CONCAT(u.firstname,' ',u.lastname) as trainee_name, 
                CONCAT(cm.first_name,' ',cm.last_name) as trainer_name
                FROM assessment_trainer_result atr
                LEFT JOIN device_users as u ON u.user_id=atr.user_id
                LEFT JOIN company_users as cm ON cm.userid=atr.trainer_id
                group by assessment_id,user_id,trainer_id order by atr.user_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT count(atr.trainer_id) as total FROM assessment_trainer_result as atr 
                 LEFT JOIN device_users as u ON u.user_id=atr.user_id
                 LEFT JOIN company_users as cm ON cm.userid=atr.trainer_id
                 group by assessment_id,user_id,trainer_id order by atr.user_id";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->row();
        $data['dtTotalRecords'] = $data_array->total;
        return $data;
    }
    public function LoadAssessorDataTable($dtWhere, $dtOrder, $dtLimit)
    {
        $query = "SELECT atr.user_id,atr.trainer_id,CONCAT(u.firstname,' ',u.lastname) as trainee_name, 
                    CONCAT(cm.first_name,' ',cm.last_name) as trainer_name
                    FROM assessment_trainer_result atr
                    LEFT JOIN device_users as u ON u.user_id=atr.user_id
                    LEFT JOIN company_users as cm ON cm.userid=atr.trainer_id
                    group by assessment_id,user_id,trainer_id order by atr.user_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT count((array)atr.trainer_id) as total FROM assessment_trainer_result as atr 
                 LEFT JOIN device_users as u ON u.user_id=atr.user_id
                 LEFT JOIN company_users as cm ON cm.userid=atr.trainer_id
                 group by assessment_id,user_id,trainer_id order by atr.user_id";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->row();
        $data['dtTotalRecords'] = $data_array->total;
        return $data;
    }
    public function LoadParticipantUsers($dtWhere, $dtOrder, $dtLimit)
    {
        $query = " SELECT w.id,u.user_id,CONCAT(u.firstname,' ',u.lastname) as name,u.area,u.email,u.mobile,tr.region_name "
            . " FROM device_users as u LEFT JOIN assessment_allow_users as w ON w.user_id=u.user_id 
                    LEFT JOIN region as tr ON tr.id=u.region_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT count(u.user_id) as total FROM device_users as u LEFT JOIN assessment_allow_users as w ON w.user_id=u.user_id 
                    LEFT JOIN region as tr ON tr.id=u.region_id ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function LoadMappingManagers($dtWhere, $dtOrder, $dtLimit)
    {
        $query = " SELECT w.id,u.userid,CONCAT(u.first_name,' ',u.last_name) as name,tr.region_name,u.email,u.username,d.description as designation "
            . " FROM company_users as u LEFT JOIN assessment_managers as w ON w.trainer_id=u.userid 
                    LEFT JOIN region as tr ON tr.id=u.region_id 
                    LEFT JOIN designation as d on d.id=u.designation_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT count(u.userid) as total FROM company_users as u LEFT JOIN 
                assessment_managers as w ON w.trainer_id=u.userid 
                LEFT JOIN region as tr ON tr.id=u.region_id LEFT JOIN designation as d on d.id=u.designation_id ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function TempLoadMappingManagers($dtWhere, $dtOrder, $dtLimit)
    {
        $query = " SELECT u.userid as id,u.userid,CONCAT(u.first_name,' ',u.last_name) as name,"
            . " tr.region_name,u.email,"
            . " u.username,d.description as designation "
            . " FROM company_users as u                     
                    LEFT JOIN region as tr ON tr.id=u.region_id
                    LEFT JOIN designation as d on d.id=u.designation_id ";
        $query .= " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT count(u.userid) as total FROM company_users as u                 
                LEFT JOIN region as tr ON tr.id=u.region_id 
                LEFT JOIN designation as d on d.id=u.designation_id ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function LoadUserMappingManagers($dtWhere, $dtOrder, $dtLimit)
    {
        $query = " SELECT am.id,u.user_id,CONCAT(u.firstname,' ',u.lastname) as name,u.area,u.email,u.mobile,tr.region_name,"
            . " CONCAT(cm.first_name,' ',cm.last_name) as username "
            . " FROM device_users as u LEFT JOIN assessment_mapping_user as am ON am.user_id=u.user_id 
                    LEFT JOIN company_users cm ON cm.userid=am.trainer_id 
                    LEFT JOIN region as tr ON tr.id=u.region_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT count(am.user_id) as total 
                    FROM device_users as u LEFT JOIN assessment_mapping_user as am ON am.user_id=u.user_id 
                    LEFT JOIN company_users cm ON cm.userid=am.trainer_id 
                    LEFT JOIN region as tr ON tr.id=u.region_id ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function LoadMappingSupervisors($dtWhere, $dtOrder, $dtLimit)
    {
        $query = " SELECT w.id,u.userid,CONCAT(u.first_name,' ',u.last_name) as name,tr.region_name,u.email,u.username,d.description as designation "
            . " FROM company_users as u LEFT JOIN assessment_supervisors as w ON w.trainer_id=u.userid
                    LEFT JOIN region as tr ON tr.id=u.region_id
                    LEFT JOIN designation as d on d.id=u.designation_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT count(u.userid) as total FROM company_users as u LEFT JOIN 
                assessment_supervisors as w ON w.trainer_id=u.userid 
                LEFT JOIN region as tr ON tr.id=u.region_id LEFT JOIN designation as d on d.id=u.designation_id ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function TempLoadMappingSupervisors($dtWhere, $dtOrder, $dtLimit)
    {
        $query = " SELECT u.userid as id,u.userid,CONCAT(u.first_name,' ',u.last_name) as name,tr.region_name,u.email,u.username,d.description as designation "
            . " FROM company_users as u 
                    LEFT JOIN region as tr ON tr.id=u.region_id
                    LEFT JOIN designation as d on d.id=u.designation_id ";
        $query .= "$dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = "SELECT count(u.userid) as total FROM company_users as u 
                LEFT JOIN region as tr ON tr.id=u.region_id 
                LEFT JOIN designation as d on d.id=u.designation_id ";
        $query .= " $dtWhere";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function get_userid($company_id, $Emp_code)
    {
        // $query = " SELECT user_id from device_users where emp_id LIKE " . $this->db->escape($Emp_code);
        $query = " SELECT user_id from device_users where user_id LIKE " . $this->db->escape($Emp_code);
        if ($company_id != "") {
            $query .= " AND company_id=" . $company_id;
        }
        $query = $this->db->query($query);
        return $query->row();
    }
    public function get_assessment_userid($company_id, $Emp_code)
    {
        $query = " SELECT user_id from device_users where status=1 AND emp_id LIKE " . $this->db->escape($Emp_code) . " OR email LIKE " . $this->db->escape($Emp_code);
        if ($company_id != "") {
            $query .= " AND company_id=" . $company_id;
        }
        $query = $this->db->query($query);
        return $query->row();
    }
    public function get_assessment_managerid($manager_code, $company_id = '')
    {
        $query = " SELECT userid from company_users where username LIKE " . $this->db->escape($manager_code) . " OR email LIKE " . $this->db->escape($manager_code);
        if ($company_id != "") {
            $query .= " AND company_id=" . $company_id;
        }
        $query = $this->db->query($query);
        return $query->row();
    }
    public function get_managerid($company_id, $Trainer_id)
    {
        $query = " SELECT userid from company_users where email LIKE " . $this->db->escape($Trainer_id);
        if ($company_id != "") {
            $query .= " AND company_id=" . $company_id;
        }
        $query = $this->db->query($query);
        return $query->row();
    }
    public function get_managers($assessment_id, $user_id = '')
    {
        $query = "  SELECT u.userid,CONCAT(u.first_name,' ',u.last_name) as name, d.description as designation 
                    FROM assessment_managers as w LEFT JOIN company_users as u ON u.userid = w.trainer_id
                    LEFT JOIN designation as d on d.id=u.designation_id WHERE w.assessment_id  = " . $assessment_id;
        if ($user_id != '') {
            $query .= " AND w.trainer_id =" . $user_id;
        }
        $result = $this->db->query($query);
        return $result->result();;
    }
    public function LoadAssessmentUsers($dtWhere, $dtOrder, $dtLimit)
    {
        $query = " SELECT w.id,u.user_id,CONCAT(u.firstname,' ',u.lastname) as name,u.area,u.email,u.mobile,tr.region_name,"
            . " w.company_id,w.assessment_id,w.retake "
            . " FROM device_users as u LEFT JOIN assessment_results as w ON w.user_id=u.user_id
                    LEFT JOIN region as tr ON tr.id=u.region_id ";
        $query .= "$dtWhere group by w.user_id $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = " SELECT count(distinct w.user_id) as total FROM device_users as u LEFT JOIN assessment_results as w ON w.user_id=u.user_id 
                   LEFT JOIN region as tr ON tr.id=u.region_id ";
        $query .= " $dtWhere ";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function load_question_table($dtWhere, $dtOrder, $dtLimit)
    {
        $query = " SELECT a.id,a.question,a.weightage,a.read_timer,a.response_timer,a.is_situation "
            . " FROM assessment_question as a ";
        $query .= " $dtWhere $dtOrder $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query = " SELECT count(a.id) as total FROM assessment_question as a ";
        $query .= " $dtWhere ";
        $result = $this->db->query($query);
        $data_array = $result->row();
        $data['dtTotalRecords'] = $data_array->total;
        return $data;
    }
    //  public function LoadAssessmentQuestions($company_id){
    //      $query = " SELECT id,question FROM assessment_question aq where status=1 AND company_id =". $company_id;
    //      $query = $this->db->query($query);
    //      return $query->result();
    //  }
    public function LoadAssessmentQuestions($assessment_id)
    {
        $query = " SELECT art.id,art.question_id,art.is_default,aq.question,art.parameter_id FROM assessment_trans art "
            . " LEFT JOIN assessment_question aq ON aq.id=art.question_id "
            . " where art.assessment_id =" . $assessment_id;
        $query = $this->db->query($query);
        return $query->result();
    }
    public function LoadAssessmentQuestions_temp($assessment_id)
    {
        $query = " SELECT art.id,art.question_id ,aq.question,art.parameter_id FROM assessment_trans_temp art "
            . " LEFT JOIN assessment_question aq ON aq.id=art.question_id "
            . " where art.assessment_id =" . $assessment_id;
        $query = $this->db->query($query);
        return $query->result();
    }
    public function LoadUniqueAIMethods($assessment_id)
    {
        $query = "SELECT DISTINCT art.question_id,aq.question,art.txn_id,art.ai_methods,art.language_id
				FROM assessment_trans_sparam art
                LEFT JOIN assessment_question aq ON aq.id=art.question_id 
				WHERE art.question_id in (SELECT question_id FROM assessment_trans WHERE assessment_id = " . $assessment_id . ") 
				AND art.assessment_id =" . $assessment_id . " ORDER BY art.txn_id";
        $query = $this->db->query($query);
        return $query->result();
    }
    public function LoadParameterSubParameter($assessment_id)
    {
        $query = "SELECT
			art.question_id,
			aq.question,
			art.language_id,
			art.txn_id,
			art.ai_methods,
			art.parameter_id,
			p.description as parameter_name,
			art.parameter_label_id,
			l.description as parameter_label_name,
			art.sub_parameter_id,
			s.description as sub_parameter_name,
			art.type_id,
			IF(art.type_id=2,'Keyword',IF(art.type_id=1,'Sentence','')) as type_name,
			art.sentence_keyword,
            art.parameter_weight,
			IFNULL(s.has_sentences_keyword,0) as has_sentences_keyword
		FROM
			assessment_trans_sparam art
			LEFT JOIN assessment_question aq ON aq.id = art.question_id 
			LEFT JOIN parameter_mst p ON p.id = art.parameter_id
			LEFT JOIN parameter_label_mst l ON l.id = art.parameter_label_id
			LEFT JOIN subparameter_mst s ON s.id = art.sub_parameter_id
		WHERE
			art.assessment_id=" . $assessment_id . " 
		ORDER BY
			art.id,
			art.txn_id";
        $query = $this->db->query($query);
        return $query->result();
    }
    public function LoadParametrWeights($assessment_id)
    {
        $query = " SELECT apw.*,p.description as parameter_name FROM assessment_para_weights apw "
            . " LEFT JOIN parameter_mst p ON p.id=apw.parameter_id "
            . " where apw.assessment_id =" . $assessment_id;
        $query = $this->db->query($query);
        return $query->result();
    }
    public function get_question_parameter($parameter)
    {
        $query = " SELECT id,description as parameter,weight_value,weight_type,weight_range_from,weight_range_to "
            . " FROM parameter_mst WHERE id IN (" . $parameter . ")";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function get_your_rating($assessment_id, $user_id, $trainer_id)
    {
        $query = " select IFNULL(SUM(art.score),0) as total_score,IFNULL(SUM(pm.weight_value),0) as total_rating 
                    from assessment_results_trans art 
                    LEFT JOIN parameter_mst pm ON pm.id = art.parameter_id
                    where art.assessment_id=" . $assessment_id . " AND art.user_id=" . $user_id . " AND art.trainer_id=" . $trainer_id . " "
            . "  AND art.trainer_id IN (SELECT trainer_id FROM assessment_complete_rating WHERE assessment_id=" . $assessment_id . " AND user_id=" . $user_id . ")";

        $query = $this->db->query($query);
        return $query->row();
    }
    //    public function get_your_rating($ass_result_id, $user_id) {
    //        $query = " select IFNULL(SUM(art.score),0) as total_score,IFNULL(SUM(pm.weight_value),0) as total_rating 
    //                    from assessment_results_trans art 
    //                    LEFT JOIN parameter_mst pm ON pm.id = art.parameter_id
    //                    where art.result_id=" . $ass_result_id . " AND art.user_id=" . $user_id
    //                ;
    //        $query = $this->db->query($query);
    //        return $query->row();
    //    }

    public function get_team_rating($assessment_id, $user_id, $trainer_id)
    {
        $query =  " SELECT IFNULL(SUM(art.user_rating),0) AS total_rating, count(art.trainer_id) as total_trainer
                    FROM assessment_trainer_result art
                    WHERE art.assessment_id=" . $assessment_id . " AND art.user_id=" . $user_id . " AND art.trainer_id!=" . $trainer_id . "
                    AND art.trainer_id IN (SELECT trainer_id FROM assessment_managers WHERE assessment_id=" . $assessment_id . ")
                    group by art.user_id ";

        $query = $this->db->query($query);
        return $query->row();
    }
    public function update_assessment_results_trans($Table, $question_id, $result_id, $user_id, $trainer_id, $parameter_id, $data)
    {
        $this->db->where('question_id', $question_id);
        $this->db->where('result_id', $result_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('trainer_id', $trainer_id);
        $this->db->where('parameter_id', $parameter_id);
        $this->db->update($Table, $data);
        //echo $this->db->last_query();
        return true;
    }

    public function update_assessment_results($Table, $company_id, $assessment_id, $user_id, $data)
    {
        $this->db->where('company_id', $company_id);
        $this->db->where('assessment_id', $assessment_id);
        $this->db->where('user_id', $user_id);
        $this->db->update($Table, $data);
        return true;
    }

    public function get_trainerdata($assessment_id, $trainer_id = '')
    {
        $query = " SELECT ar.trainer_id,CONCAT(u.first_name,' ',u.last_name) as name 
                FROM assessment_managers ar
                LEFT JOIN company_users u ON u.userid=ar.trainer_id
                WHERE ar.assessment_id=" . $assessment_id;
        if ($trainer_id != '') {
            $query .= " AND ar.trainer_id=" . $trainer_id;
        }
        $query = $this->db->query($query);
        return $query->result();
    }

    //    public function update_assessment_trans($Table,$assessment_id,$question_id,$data){        
    //        $this->db->where('assessment_id', $assessment_id);
    //        $this->db->where('question_id', $question_id);        
    //        $this->db->update($Table, $data);
    //        return true;
    //    }
    public function get_assessment_users($assess_id)
    {
        $query = " SELECT w.id,u.user_id,CONCAT(u.firstname,' ',u.lastname) as name,u.firstname,u.email,"
            . " w.company_id,w.assessment_id,am.assessment,am.assessor_dttm "
            . " FROM device_users as u "
            . " LEFT JOIN assessment_results as w ON w.user_id=u.user_id"
            . " LEFT JOIN assessment_mst as am ON w.assessment_id=am.id ";
        $query .= " where w.assessment_id=" . $assess_id . " group by w.user_id ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment_manager($company_id, $assess_id)
    {
        $query = " SELECT amu.trainer_id,"
            . " CONCAT(cu.first_name,' ',cu.last_name) as name "
            . " FROM assessment_mapping_user as amu "
            . " LEFT JOIN company_users as cu ON cu.userid=amu.trainer_id"
            . " WHERE amu.assessment_id= " . $assess_id . ""
            . " GROUP BY amu.trainer_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function CopyAllowedUsers($assessment_Id, $Copy_id)
    {
        $query = " insert into assessment_allow_users(assessment_id,user_id) SELECT $assessment_Id as assessment_id,user_id "
            . "FROM assessment_allow_users where assessment_id=$Copy_id";
        $this->db->query($query);
        return true;
    }
    public function CopyAssessmentManagers($assessment_Id, $Copy_id)
    {
        $query = " insert into assessment_managers(assessment_id,trainer_id) SELECT $assessment_Id as assessment_id,trainer_id "
            . " FROM assessment_managers where assessment_id=$Copy_id";
        $this->db->query($query);
        return true;
    }
    public function CopyUserManagersMapping($assessment_Id, $Copy_id)
    {
        $query = " insert into assessment_mapping_user(assessment_id,user_id,trainer_id) SELECT $assessment_Id as assessment_id,user_id,trainer_id "
            . " FROM assessment_mapping_user where assessment_id=$Copy_id";
        $this->db->query($query);
        return true;
    }
    public function get_map_manager($assessment_id, $mapid)
    {
        $query = " SELECT w.id,u.userid,w.trainer_id,CONCAT(u.first_name,' ',u.last_name) as name 
                    FROM company_users as u LEFT JOIN assessment_managers as w ON w.trainer_id=u.userid
                    WHERE w.trainer_id=" . $mapid . " AND w.assessment_id=" . $assessment_id . " AND u.userid IN (select user_id FROM assessment_mapping_user where assessment_id=" . $assessment_id . ")";
        $query = $this->db->query($query);
        return $query->row();
    }
    public function get_assessment_trainer($assessment_id_str)
    {
        $query = " SELECT w.trainer_id,CONCAT(u.first_name,' ',u.last_name) as name 
                    FROM assessment_managers as w LEFT JOIN company_users as u ON w.trainer_id=u.userid
                    WHERE  w.assessment_id IN(" . $assessment_id_str . ") order by name";
        $query = $this->db->query($query);
        return $query->result();
    }
    public function assessment_export($dtWhere)
    {
        $query = " SELECT am.id,ar.user_id,am.company_id,am.assessment_type,am.assessment,ar.question_id,"
            . " CONCAT(du.firstname,' ',du.lastname) AS username,du.email,aq.question, am.status,ar.video_url,ar.vimeo_uri,ats.parameter_id "
            . " FROM assessment_mst am "
            . " LEFT JOIN assessment_results ar ON ar.assessment_id = am.id "
            . " LEFT JOIN device_users du ON du.user_id=ar.user_id"
            . " LEFT JOIN assessment_question aq ON aq.id=ar.question_id 
                    LEFT JOIN assessment_trans ats ON ats.assessment_id= am.id AND ats.question_id =ar.question_id";
        $query .= " $dtWhere group by am.id,ar.user_id,aq.question order by am.id,username,ar.trans_id";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment_trainer_score($assessment_id, $user_id)
    {
        $query = " SELECT ar.*,tr.remarks as question_remarks,atr.remarks as overall_comments
            FROM assessment_results_trans as ar LEFT JOIN assessment_trainer_remarks as tr ON tr.assessment_id=ar.assessment_id
            AND tr.user_id=ar.user_id AND tr.trainer_id=ar.trainer_id AND tr.question_id=ar.question_id
            LEFT JOIN assessment_trainer_result as atr ON atr.assessment_id=ar.assessment_id
            AND atr.user_id=ar.user_id AND atr.trainer_id=ar.trainer_id
            WHERE ar.assessment_id = $assessment_id AND ar.user_id = $user_id  ";
        $query = $this->db->query($query);
        $resultSet = $query->result();
        //echo "<pre>";
        //print_r($resultSet);
        $dataset = array();
        if (count((array)$resultSet) > 0) {
            foreach ($resultSet as $value) {
                $dataset[$value->trainer_id][$value->question_id][$value->parameter_id] = $value;
            }
        }
        return $dataset;
    }
    // preview pdf functions
    public function get_assessment_value($user_id)
    {
        $query = "SELECT id FROM `assessment_mst_temp` WHERE addedby = '" . $user_id . "' ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_parameter_value($temp_assessment_id)
    {
        $query = "SELECT * FROM `assessment_trans_sparam_temp` WHERE assessment_id = '" . $temp_assessment_id . "' ";
        $result = $this->db->query($query);
        return $result->result();
    }


    public function get_parameter_lable($temp_assessment_id)
    {
        // $query = "SELECT DISTINCT parameter_label_name FROM `assessment_trans_sparam_temp` WHERE assessment_id = '".$temp_assessment_id."' ";
        $query = "SELECT parameter_label_name as parameter_label
                  FROM `assessment_trans_sparam_temp` as ats
                  WHERE assessment_id = '" . $temp_assessment_id . "' group by ats.parameter_label_id";
        $result = $this->db->query($query);
        return $result->result();
    }


    public function get_parameter_id($temp_assessment_id)
    {
        $query = "SELECT id FROM `assessment_trans_sparam_temp` WHERE assessment_id = '" . $temp_assessment_id . "'  ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_assessment_data($user_id)
    {
        $query = "SELECT * FROM `assessment_mst_temp` WHERE addedby = '" . $user_id . "' ";
        $result = $this->db->query($query);
        return $result->result_array();
    }
    // new edit code
    public function LoadQUestionPreview($assessment_id)
    {
        $query = " SELECT art.id,art.question_id ,aq.question,art.parameter_id FROM assessment_trans_temp art "
            . " LEFT JOIN assessment_question aq ON aq.id=art.question_id "
            . " where art.assessment_id =" . $assessment_id;
        $query = $this->db->query($query);
        return $query->result();
    }

    public function LoadUniqueAIMethodsTemp($assessment_id)
    {
        $query = "SELECT DISTINCT art.question_id,aq.question,art.txn_id,art.ai_methods,art.language_id
                FROM assessment_trans_sparam_temp art
                LEFT JOIN assessment_question aq ON aq.id=art.question_id 
                WHERE art.question_id in (SELECT question_id FROM assessment_trans_sparam_temp WHERE assessment_id = " . $assessment_id . ") 
                AND art.assessment_id =" . $assessment_id . " ORDER BY art.txn_id";
        $query = $this->db->query($query);
        return $query->result();
    }
    public function LoadParameterSubParameter_temp($assessment_id)
    {
        $query = "SELECT
            art.question_id,
            aq.question,
            art.language_id,
            art.txn_id,
            art.ai_methods,
            art.parameter_id,
            p.description as parameter_name,
            art.parameter_label_id,
            l.description as parameter_label_name,
            art.sub_parameter_id,
            s.description as sub_parameter_name,
            art.type_id,
            IF(art.type_id=2,'Keyword',IF(art.type_id=1,'Sentence','')) as type_name,
            art.sentence_keyword,
            art.parameter_weight
            
        FROM
        assessment_trans_sparam_temp art
            LEFT JOIN assessment_question aq ON aq.id = art.question_id 
            LEFT JOIN parameter_mst p ON p.id = art.parameter_id
            LEFT JOIN parameter_label_mst l ON l.id = art.parameter_label_id
            LEFT JOIN subparameter_mst s ON s.id = art.sub_parameter_id
        WHERE
            art.assessment_id=" . $assessment_id . " 
        ORDER BY
            art.id,
            art.txn_id";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function LoadParametrWeights_temp($assessment_id)
    {
        $query = " SELECT p.id,p.assessment_id, p.parameter_id,p.parameter_name as parameter_name, p.parameter_weight as parameter_weight
        FROM assessment_trans_sparam_temp p where p.assessment_id=" . $assessment_id;
        $query = $this->db->query($query);
        return $query->result();
    }
    public function get_old_question_id($txn_id, $temp_assessment_id)
    {
        $query = "SELECT question_id from assessment_trans_sparam_temp where txn_id = '" . $txn_id . "' and assessment_id= '" . $temp_assessment_id . "' ";
        $query = $this->db->query($query);
        return $query->result();
    }
    // insert trans table value 
    public function get_tans_temp_data($temp_assessment_id, $question_id)
    {
        $query = "SELECT id,parameter_id from assessment_trans_temp where assessment_id= '" . $temp_assessment_id . "' and question_id= '" . $question_id . "' ";
        $query = $this->db->query($query);
        return $query->result_array();
    }


    public function get_question_delete($txn_id, $assessment_id)
    {
        $query = "SELECT DISTINCT question_id from assessment_trans_sparam_temp where assessment_id= '" . $assessment_id . "' and txn_id= '" . $txn_id . "' ";
        $query = $this->db->query($query);
        return $query->result();
    }
    // preview pdf functions
    public function get_DepartmentList($company_id)
    {
        $query = "SELECT distinct department FROM device_users where company_id=$company_id and department !='' order by department ";
        $query = $this->db->query($query);
        return $query->result();
    }

    // Changes By Bk- Trinity UI/UX
    public function load_script_question($script_id)
    {
        $query = "SELECT fs.*,a.question FROM script_mst as fs
        left join assessment_script_qna as a on a.script_id = fs.id
        where fs.id = $script_id";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function get_value($Table, $Column, $Clause) {
        $LcSqlStr = "SELECT " . $Column . " FROM " . $Table . " WHERE " . $Clause . " ";
        $query = $this->db->query($LcSqlStr);
        $row = $query->result();
        return $row;
    }
    // Changes by Bk -Trinity UI/UX 

    public function get_parameter_details($parameter_val)
    {
        $query = "select id,description,status from goal_mst where id IN  (" . implode(',', $parameter_val) . ") ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function get_parameter_dt($parameterArray)
    {
        //Exclude some subparameters tone from Body language, Product knowledge from Object handling, Objection handling from Product knowledge
        $query = "SELECT g.id as goal_id,g.parameter_id,g.description as goal_name,pm.description as parameter_name ,g.status , sm.id as subparameter_id 
                  FROM goal_mst AS g 
                  LEFT JOIN parameter_mst AS pm ON pm.id = g.parameter_id 
                  LEFT JOIN subparameter_mst AS sm ON sm.parameter_id=g.parameter_id AND sm.id != 6 AND sm.id!=9 AND sm.id!=3
                  WHERE g.id IN (" . implode(',', $parameterArray) . ")";
        $query = $this->db->query($query);
        return $query->result();
    }
}