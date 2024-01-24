<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class View_trinity_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit)
    {
        $query = "  SELECT am.id,am.company_id,am.assessment_type,
                    am.assessment,at.description AS assessment_type, 
                    CASE WHEN am.assessment_type=3 THEN 'Trinity' END AS ass_type,      -- DARSHIL - added this condition for displaying trinity assessments only
                    IF(is_situation=1,'Situation','Question') AS question_type,
                        am.status,DATE_FORMAT(am.start_dttm,'%d-%m-%Y %H:%i') AS start_dttm, 
                        DATE_FORMAT(am.end_dttm,'%d-%m-%Y %H:%i') AS end_dttm,DATE_FORMAT(am.assessor_dttm,'%d-%m-%Y %H:%i') AS assessor_dttm                          
                 FROM assessment_mst  am
                                LEFT JOIN assessment_type at ON at.id = am.assessment_type ";
        $query .= " $dtWhere GROUP BY am.id $dtOrder $dtLimit ";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        // print_r($data['ResultSet']);die;
        $data['dtPerPageRecords'] = count((array) $data['ResultSet']);

        $query = " SELECT  am.id as total  FROM  assessment_mst  am "
            . " LEFT JOIN assessment_type at ON at.id = am.assessment_type"
            . " $dtWhere GROUP BY am.id ";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = count((array) $data_array);


        return $data;
    }
    public function getAssessmentStatus($assessment_id, $trainer_id = '')
    {
        $query = "  SELECT A.assessment_id,A.user_id,A.is_candidate_complete,A.trainer_id,
                        IF(B.user_id IS NULL,0,1) as assessor_status
                             FROM
                                    (
                                SELECT user_id,sum(is_completed) as is_candidate_complete,assessment_id,trainer_id 
				 FROM
                                    (SELECT aa.user_id,aa.is_completed,aa.assessment_id,asm.trainer_id
                    FROM assessment_mapping_user as asm
					 LEFT JOIN  assessment_attempts  aa on aa.assessment_id=asm.assessment_id and aa.user_id = asm.user_id
                                            -- LEFT JOIN assessment_managers amg ON amg.assessment_id = aa.assessment_id 
						WHERE asm.assessment_id = $assessment_id ";
        if ($trainer_id != '') {
            $query .= " AND asm.trainer_id =" . $trainer_id;
        }

        $query .= " UNION ALL
									
                                    SELECT au.user_id,0 AS is_completed,au.assessment_id,amg.trainer_id
					 FROM  assessment_allow_users  au
                                            LEFT JOIN assessment_managers amg ON amg.assessment_id = au.assessment_id
						WHERE au.assessment_id = $assessment_id ";
        if ($trainer_id != '') {
            $query .= " AND amg.trainer_id =" . $trainer_id;
        }

        $query .= " AND au.user_id NOT IN (select user_id  from  assessment_attempts  where assessment_id=$assessment_id)) AA
				GROUP BY AA.user_id,AA.trainer_id	
                            ) A	
                            LEFT JOIN
                            (
                                SELECT acr.trainer_id,acr.assessment_id,acr.user_id
                                     FROM  assessment_complete_rating acr 
                                        WHERE acr.assessment_id = $assessment_id";
        if ($trainer_id != '') {
            $query .= " AND acr.trainer_id =" . $trainer_id;
        }
        $query .= " )B
                            ON B.assessment_id = A.assessment_id AND B.trainer_id = A.trainer_id AND B.user_id = A.user_id 
                            WHERE A.assessment_id = $assessment_id ";
        if ($trainer_id != '') {
            $query .= " AND A.trainer_id =" . $trainer_id;
        }
        $query .= " ORDER BY is_candidate_complete,assessor_status limit 0,1 ";

        $result = $this->db->query($query);
        return $result->row();
    }

    //Spotlight Changes start
    public function get_questions_user_details($company_id, $assessment_id, $user_id)
    {
        $query1 = "SELECT id FROM device_users WHERE user_id='$user_id' AND company_id='$company_id'";
        $result1 = $this->common_db->query($query1);
        $data1 = $result1->result();

        $query2 = "SELECT question_limits FROM assessment_mst WHERE id='$assessment_id'";
        $result2 = $this->db->query($query2);
        $data2 = $result2->result();
        $question_limits = $data2[0]->question_limits;
        $res = array();
        if (isset($data1[0]->id)) {
            $u_id = $data1[0]->id;

            $query3 = "SELECT MAX(attempts) as attempts FROM ai_cosine_score WHERE assessment_id = '$assessment_id' AND user_id='$user_id'";
            // $query3 = "SELECT MAX(attempts) as attempts FROM ai_cosine_score WHERE assessment_id = '$assessment_id' AND user_id='$u_id'";
            $result3 = $this->db->query($query3);
            $data3 = $result3->result();
            $attempts = isset($data3[0]->attempts) ? $data3[0]->attempts : 0;

            $query = "SELECT ac.user_id,ac.assessment_id,ac.current_question_id as question_id,ac.cosine_score,ac.audio_totext,aq.question,ac.added_at,ac.next_question_id,em.embeddings
                FROM `ai_cosine_score` as ac 
                LEFT JOIN assessment_question as aq on aq.id=ac.current_question_id 
                LEFT JOIN ai_embeddings as em on em.question_id=ac.current_question_id
                WHERE ac.assessment_id = '$assessment_id' AND ac.user_id='$user_id' AND ac.attempts=$attempts GROUP BY ac.current_question_id ORDER BY ac.id ASC LIMIT $question_limits";
            // WHERE ac.assessment_id = '$assessment_id' AND ac.user_id='$u_id' AND ac.attempts=$attempts GROUP BY ac.current_question_id ORDER BY ac.id ASC LIMIT $question_limits";

            $result = $this->db->query($query);
            $res = $result->result();
        }
        return $res;
    }
    //Spotlight changes end

    public function LoadAssessmentUsers($dtWhere, $dtOrder, $dtLimit)
    {
        /*$query = " SELECT w.id,u.user_id,CONCAT(u.firstname,' ',u.lastname) as name,u.area,"
                . " u.email,u.mobile,tr.region_name,w.assessment_id,w.is_completed,"
                . " IF(acr.trainer_id IS NOT NULL,count(acr.trainer_id),0) as complete_assesor,"
                . " count(am.trainer_id) as assessment_mng "
                . " FROM device_users as u 
                    LEFT JOIN assessment_attempts as w ON w.user_id=u.user_id
                    LEFT JOIN region as tr ON tr.id=u.region_id 
                    LEFT JOIN assessment_complete_rating acr ON acr.user_id=w.user_id AND acr.assessment_id = w.assessment_id 
                    LEFT JOIN assessment_managers as am ON am.assessment_id=w.assessment_id
                    LEFT JOIN assessment_mapping_user amu ON amu.assessment_id = w.assessment_id AND amu.user_id = u.user_id ";
        $query .= " $dtWhere group by w.user_id $dtOrder $dtLimit ";*/
        $query = "SELECT w.id,u.user_id,CONCAT(u.firstname,' ',u.lastname) as name,u.area, u.email,u.mobile,tr.region_name,amu.assessment_id,"
            . "IFnull(w.is_completed, 0) as is_completed,IF(acr.trainer_id IS NOT NULL,count(acr.trainer_id),0) as complete_assesor, count(am.trainer_id) as assessment_mng "
            . " FROM  assessment_mapping_user  amu LEFT JOIN device_users u on amu.user_id=u.user_id "
            . "LEFT JOIN region as tr ON tr.id=u.region_id "
            . "LEFT JOIN assessment_attempts as w ON w.user_id=u.user_id and w.assessment_id=amu.assessment_id "
            . "LEFT JOIN assessment_complete_rating acr ON acr.user_id=w.user_id AND acr.assessment_id = w.assessment_id "
            . "LEFT JOIN assessment_managers as am ON am.assessment_id=w.assessment_id";
        $query .= " $dtWhere group by u.user_id $dtOrder $dtLimit ";
        // echo $query;exit;
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array) $data['ResultSet']);

        /*$query = "  SELECT COUNT(distinct w.user_id) as total FROM device_users as u 
                    LEFT JOIN assessment_attempts as w ON w.user_id=u.user_id
                    LEFT JOIN region as tr ON tr.id=u.region_id
                    LEFT JOIN assessment_complete_rating acr ON acr.user_id=w.user_id AND acr.assessment_id = w.assessment_id 
                    LEFT JOIN assessment_managers as am ON am.assessment_id=w.assessment_id
                    LEFT JOIN assessment_mapping_user amu ON amu.assessment_id = w.assessment_id AND amu.user_id = u.user_id ";*/
        $query = "SELECT COUNT(distinct u.user_id) as total "
            . " FROM  assessment_mapping_user  amu LEFT JOIN device_users u on amu.user_id=u.user_id "
            . "LEFT JOIN region as tr ON tr.id=u.region_id "
            . "LEFT JOIN assessment_attempts as w ON w.user_id=u.user_id and w.assessment_id=amu.assessment_id "
            . "LEFT JOIN assessment_complete_rating acr ON acr.user_id=w.user_id AND acr.assessment_id = w.assessment_id "
            . "LEFT JOIN assessment_managers as am ON am.assessment_id=w.assessment_id";
        $query .= " $dtWhere ";
        $result = $this->db->query($query);
        $data_array = $result->result_array();
        $data['dtTotalRecords'] = $data_array[0]['total'];
        return $data;
    }
    public function get_TraineeRegionList($company_id)
    {
        $query = "SELECT id,region_name  FROM  region  where status=1 AND company_id = $company_id AND"
            . " id IN(select distinct region_id  FROM  device_users  where company_id=$company_id) order by region_name ";
        $query = $this->db->query($query);
        return $query->result();
    }
    public function get_TrainerRegionList($company_id)
    {
        $query = "SELECT id,region_name  FROM  region  where status=1 AND company_id = $company_id AND"
            . " id IN(select distinct region_id  FROM  company_users  where company_id=$company_id) order by region_name ";
        $query = $this->db->query($query);
        return $query->result();
    }
    public function get_userid($company_id, $Emp_code)
    {
        $query = " SELECT user_id  from  device_users  where emp_id LIKE " . $this->db->escape($Emp_code);
        if ($company_id != "") {
            $query .= " AND company_id=" . $company_id;
        }
        $query = $this->db->query($query);
        return $query->row();
    }
    public function LoadCandidateDataTableRefresh($dtWhere, $dtOrder, $dtLimit, $assessment_id)
    {
        //Old query
        // $query = "  SELECT A.user_id,A.is_completed,A.assessment_id,A.trainer_id,
        // CONCAT(u.firstname,' ',u.lastname) AS trainee_name, 
        // CONCAT(cm.first_name,' ',cm.last_name) AS trainer_name,
        // IF(A.is_completed,'Completed','Incomplete') as candidate_status,
        // IF(B.trainer_id IS NOT NULL,'Completed','Incomplete') as assessor_status
        // FROM (
        // SELECT r.user_id,sum(r.is_completed) as is_completed ,r.assessment_id,r.trainer_id
        // from (
        // SELECT aa.user_id,aa.is_completed,aa.assessment_id,amg.trainer_id
        // FROM assessment_attempts aa
        // LEFT JOIN assessment_managers amg ON amg.assessment_id = aa.assessment_id
        // WHERE aa.assessment_id = $assessment_id 						

        // UNION ALL		

        // SELECT au.user_id,0 AS is_completed,au.assessment_id,amg.trainer_id
        // FROM assessment_allow_users  au
        // LEFT JOIN assessment_managers amg ON amg.assessment_id = au.assessment_id
        // WHERE au.assessment_id = $assessment_id ) as r group by r.user_id,r.assessment_id,r.trainer_id
        // ) A			
        // LEFT JOIN	
        // (
        // SELECT acr.trainer_id,acr.assessment_id,acr.user_id
        // FROM assessment_complete_rating acr 
        // WHERE acr.assessment_id = $assessment_id
        // )B
        // ON A.user_id =B.user_id AND A.assessment_id = B.assessment_id AND A.trainer_id = B.trainer_id
        // INNER JOIN device_users AS u ON u.user_id=A.user_id
        // LEFT JOIN company_users AS cm ON cm.userid=A.trainer_id ";	
        //New query
        $query = "	SELECT DISTINCT A.user_id,A.emp_id,A.is_completed,A.assessment_id,A.trainer_id, 
					CONCAT(u.firstname,' ',u.lastname) AS trainee_name, 
					CONCAT(cm.first_name,' ',cm.last_name) AS trainer_name, 
					IF(A.is_completed,'Completed','Incomplete') as candidate_status, 
					IF(B.trainer_id IS NOT NULL,'Completed','Incomplete') as assessor_status 
						 FROM ( 
						SELECT r.user_id,r.emp_id,sum(r.is_completed) as is_completed ,r.assessment_id,r.trainer_id 
						 from ( 
						SELECT m.user_id,du.emp_id,a.is_completed,m.assessment_id,m.trainer_id 
							 FROM assessment_mapping_user  m 
                             LEFT JOIN device_users du ON du.user_id=m.user_id
								LEFT JOIN  `assessment_attempts` a on a.user_id=m.user_id AND a.assessment_id=m.assessment_id 
									WHERE m.`assessment_id` = $assessment_id
					UNION ALL 
						SELECT amu.user_id,du.emp_id,0 AS is_completed,amu.assessment_id,amu.trainer_id 
							 FROM  assessment_mapping_user  amu 
                             LEFT JOIN device_users du ON du.user_id=amu.user_id
								WHERE amu.assessment_id = $assessment_id ) as r group by r.user_id,r.assessment_id,r.trainer_id 
								) A 
							LEFT JOIN 
							( SELECT acr.trainer_id,acr.assessment_id,acr.user_id 
								 FROM  assessment_complete_rating  acr 
									WHERE acr.assessment_id = $assessment_id 
							)B 
						ON A.user_id =B.user_id AND A.assessment_id = B.assessment_id AND A.trainer_id = B.trainer_id 
						INNER JOIN device_users AS u ON u.user_id=A.user_id 
						LEFT JOIN company_users AS cm ON cm.userid=A.trainer_id ";
        $query .= " $dtWhere ";

        $query1 = $query;
        $query .= " $dtOrder $dtLimit";
        // echo $query;exit;

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array) $data['ResultSet']);

        $result = $this->db->query($query1);
        $data_array = $result->result_array();
        if (count((array) $data_array) > 0) {
            $data['dtTotalRecords'] = count((array) $data_array);
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }
    public function LoadAssessorDataTableRefresh($dtWhere, $dtOrder, $dtLimit, $assessment_id, $trainer_id = '')
    {
        $query = "  SELECT A.assessment_id,A.total_candidate as total_user,A.is_candidate_complete as is_completed,A.trainer_id,
                    IF(B.complete_candidate=total_candidate,'Completed','Incomplete') as assessor_status,
                    CONCAT(cm.first_name,' ',cm.last_name) AS trainer_name
                         FROM
                            (	
				SELECT user_id,count(is_completed) as total_candidate,
                                sum(is_completed) as is_candidate_complete,assessment_id,trainer_id 
                                     FROM
					( SELECT aa.user_id,aa.is_completed,aa.assessment_id,amg.trainer_id
                                             FROM assessment_attempts  aa
						LEFT JOIN assessment_managers amg 
                                                    ON amg.assessment_id = aa.assessment_id 
						WHERE aa.assessment_id =" . $assessment_id;
        if ($trainer_id != '') {
            $query .= " AND amg.trainer_id =$trainer_id ";
        }

        $query .= " UNION ALL
												
					SELECT au.user_id,0 AS is_completed,au.assessment_id,amg.trainer_id
                                            FROM assessment_allow_users  au
						LEFT JOIN assessment_managers amg 
                                                    ON amg.assessment_id = au.assessment_id
						WHERE au.assessment_id =" . $assessment_id;
        if ($trainer_id != '') {
            $query .= " AND amg.trainer_id =$trainer_id ";
        }
        $query .= "	AND au.user_id NOT IN (select user_id  from  assessment_attempts  where assessment_id=$assessment_id)) AA 
				GROUP BY AA.trainer_id 	
                            ) A	
                                    LEFT JOIN
                                    (
                            SELECT acr.trainer_id,acr.assessment_id,acr.user_id,count(acr.user_id) as complete_candidate
                                 FROM  assessment_complete_rating  acr 
                                    WHERE acr.assessment_id =" . $assessment_id;
        if ($trainer_id != '') {
            $query .= " AND acr.trainer_id =$trainer_id ";
        }
        $query .= ")B
                        ON B.assessment_id = A.assessment_id AND B.trainer_id = A.trainer_id AND B.user_id = A.user_id 
                        LEFT JOIN company_users AS cm ON cm.userid=A.trainer_id
                        WHERE A.assessment_id =" . $assessment_id;
        if ($trainer_id != '') {
            $query .= " AND A.trainer_id =$trainer_id ";
        }

        $query .= " $dtWhere ";

        $query .= " GROUP BY A.trainer_id  ";

        $query1 = $query;
        $query .= " $dtOrder $dtLimit ";


        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array) $data['ResultSet']);


        $result = $this->db->query($query1);
        $data_array = $result->result_array();
        if (count((array) $data_array) > 0) {
            $data['dtTotalRecords'] = count((array) $data_array);
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }
    public function LoadAssessorSubDataTableRefresh($dtWhere, $dtOrder, $dtLimit, $assessment_id, $trainer_id)
    {
        $query = "  SELECT A.user_id,A.assessment_id,A.trainer_id,
		CONCAT(u.firstname,' ',u.lastname) AS trainee_name, 
		IF(A.is_completed,'Completed','Incomplete') as candidate_status,
		IF(B.trainer_id IS NOT NULL,'Completed','Incomplete') as assessor_status
                         FROM
			(SELECT aa.user_id,aa.is_completed,aa.assessment_id,amg.trainer_id
                             FROM  assessment_attempts  aa
                                LEFT JOIN assessment_managers amg ON amg.assessment_id = aa.assessment_id
                                    WHERE aa.assessment_id = $assessment_id AND amg.trainer_id = $trainer_id
						
				UNION ALL		
					
			SELECT au.user_id,0 AS is_completed,au.assessment_id,amg.trainer_id
                             FROM  assessment_allow_users  au
                                LEFT JOIN assessment_managers amg ON amg.assessment_id = au.assessment_id
                                    WHERE au.assessment_id = $assessment_id AND amg.trainer_id = $trainer_id
									AND au.user_id NOT IN (select user_id  from  assessment_attempts  where assessment_id=$assessment_id)
                            ) A		
			LEFT JOIN 	
				(
                                    SELECT acr.trainer_id,acr.assessment_id,acr.user_id
                                         FROM  assessment_complete_rating  acr 
                                            WHERE acr.assessment_id = $assessment_id AND acr.trainer_id = $trainer_id
				)B
			ON A.user_id =B.user_id AND A.assessment_id = B.assessment_id AND A.trainer_id = B.trainer_id
                        INNER JOIN device_users AS u ON u.user_id=A.user_id
                        LEFT JOIN company_users AS cm ON cm.userid=A.trainer_id ";
        $query .= " $dtWhere ";

        $query1 = $query;
        $query .= " $dtOrder $dtLimit ";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array) $data['ResultSet']);

        $result = $this->db->query($query1);
        $data_array = $result->result_array();
        if (count((array) $data_array) > 0) {
            $data['dtTotalRecords'] = count((array) $data_array);
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }
    //  public function LoadAssessmentQuestions($company_id){
//      $query = " SELECT id,question FROM assessment_question aq where status=1 AND company_id =". $company_id;
//      $query = $this->db->query($query);
//      return $query->result();
//  }
    public function LoadAssessmentQuestions($assessment_id)
    {
        $query = " SELECT art.id,art.question_id ,aq.question,art.parameter_id  FROM  assessment_trans  art "
            . " LEFT JOIN assessment_question aq ON aq.id=art.question_id "
            . " where art.assessment_id =" . $assessment_id;
        $query = $this->db->query($query);
        return $query->result();
    }
    public function LoadParameterQuestions($assessment_id, $user_id, $trainer_id, $assessment_type)
    {
        $query = " SELECT art.id,art.question_id ,aq.question,art.parameter_id,ats.total_para FROM  assessment_trans  art "
            . " LEFT JOIN assessment_question aq ON aq.id=art.question_id ";

        if ($assessment_type == 2) { //Spotlight
            $query .= " RIGHT JOIN assessment_results ar ON ar.question_id=art.question_id AND ar.user_id='$user_id' AND ar.assessment_id=art.assessment_id AND ar.assessment_id!='' ";
        }

        $query .= " LEFT JOIN (SELECT COUNT(distinct parameter_id) AS total_para,question_id FROM assessment_results_trans  where assessment_id=" . $assessment_id . " AND user_id=" . $user_id . " AND trainer_id=" . $trainer_id . " AND score != 0 GROUP BY question_id) ats ON ats.question_id=art.question_id "
            . " where art.assessment_id =" . $assessment_id . " order by art.id";
        $query = $this->db->query($query);
        return $query->result();
    }

    //Spotlight changes start
    public function get_audio_data($assessment_id, $user_id, $question_id)
    {
        $query = "SELECT a.id,b.audio_id FROM `assessment_results` as a
                LEFT JOIN ai_cosine_score as b on a.user_id=b.user_id AND a.assessment_id=b.assessment_id AND a.question_id=b.current_question_id 
                AND b.attempts IN (SELECT MAX(attempts) FROM ai_cosine_score WHERE current_question_id='$question_id' AND user_id='$user_id' AND assessment_id='$assessment_id')
                where a.question_id='$question_id' AND a.user_id='$user_id' AND a.assessment_id='$assessment_id' order by a.id desc";
        $query = $this->db->query($query);
        return $query->row();
    }
    //Spotlight changes end

    public function LoadTotalParameter($assessment_id, $user_id, $trainer_id)
    {
        $query1 = "SELECT assessment_type FROM assessment_mst WHERE id=$assessment_id";
        $result1 = $this->db->query($query1);

        $ass_data = $result1->result();
        if (isset($ass_data) and count((array) $ass_data) > 0) {
            foreach ($ass_data as $row) {
                $assessment_type = $row->assessment_type;
            }
        }

        $query = " SELECT GROUP_CONCAT(art.parameter_id SEPARATOR ',') as para_list,SUM(ats.total_para) as tot_para  FROM assessment_trans  art "
            . " LEFT JOIN (SELECT COUNT(distinct parameter_id) AS total_para,question_id  FROM  assessment_results_trans "
            . " WHERE assessment_id=" . $assessment_id . " AND user_id=" . $user_id . " AND trainer_id=" . $trainer_id . "  GROUP BY question_id) ats ON ats.question_id=art.question_id ";

        if ($assessment_type == 2) { //Spotlight
            $query .= " AND art.question_id IN (SELECT ar.question_id FROM assessment_results as ar WHERE ar.assessment_id =$assessment_id and ar.user_id = $user_id)";
        }
        //AND score != 0
        $query .= " WHERE art.assessment_id =" . $assessment_id . " AND ats.question_id IS NOT NUll";
        //    echo $query;exit;
        $query = $this->db->query($query);
        return $query->row();
    }
    public function get_question_parameter($parameter, $q_id = "", $user_id = "", $trainer_id = "", $assessment_id = "")
    {
        if ($assessment_id != '') {
            $query = " SELECT DISTINCT
            a.id,b.percentage as score,
            a.description as parameter,
            plm.description AS parameter_label_name,
            a.weight_value,
            a.weight_type,
            a.weight_range_from,
			a.weight_range_to 
             FROM parameter_mst  as a 
            LEFT JOIN assessment_trans_sparam as ats ON ats.parameter_id = a.id AND ats.assessment_id = " . $assessment_id . " AND ats.question_id=" . $q_id . " 
            LEFT JOIN parameter_label_mst as plm ON ats.parameter_id = plm.parameter_id AND ats.parameter_label_id = plm.id 
            LEFT JOIN assessment_results_trans as b	ON b.parameter_id=a.id AND b.question_id=" . $q_id . "  AND b.user_id=" . $user_id .
                " AND b.trainer_id=" . $trainer_id . " AND b.assessment_id=" . $assessment_id . "
			WHERE a.id IN (" . $parameter . ")";
        } else {
            $query = " SELECT id,description as parameter,weight_value,weight_type,weight_range_from,weight_range_to "
                . " FROM parameter_mst WHERE id IN (" . $parameter . ")";
        }
        $query = $this->db->query($query);
        return $query->result();
    }
    public function get_user_rating($assessment_id, $user_id, $trainer_id)
    {
        $query = " select IFNULL(SUM(art.score),0) as total_score,IFNULL(SUM(pm.weight_value),0) as total_rating 
                     from assessment_results_trans  art 
                    LEFT JOIN parameter_mst pm ON pm.id = art.parameter_id
                    where art.assessment_id=" . $assessment_id . " AND art.user_id=" . $user_id . " AND art.trainer_id=" . $trainer_id;
        $query = $this->db->query($query);
        return $query->row();
    }
    public function get_your_rating($assessment_id, $user_id, $trainer_id, $is_weights = 0)
    {
        // $query = "SELECT is_weights,ratingstyle from assessment_mst where id=".$assessment_id;
        // $result = $this->db->query($query);
        // $Assessment_set = $result->row();
        // if($is_weights==1){
        // 	///100
        // 	$query = " SELECT FORMAT(sum(parameter_wetg),2) as total_rating FROM (
        // 	select IF(am.ratingstyle=2, IFNULL(FORMAT(((SUM(art.percentage)/ count(pm.id))/100)* SUM(aw.parameter_weight),2),0),
        // 	IFNULL(FORMAT((SUM(art.score)/SUM(pm.weight_value))*SUM(aw.parameter_weight),2),0)) as parameter_wetg  from  assessment_results_trans  art 
        // 	LEFT JOIN assessment_mst as am on am.id=art.assessment_id 
        // 	LEFT JOIN parameter_mst pm ON pm.id = art.parameter_id
        // 	LEFT JOIN assessment_trans_sparam as aw ON aw.assessment_id=art.assessment_id AND aw.parameter_id=art.parameter_id 
        // 	where art.assessment_id=" . $assessment_id . " AND art.user_id=" . $user_id. " AND art.trainer_id=".$trainer_id;
        // 	$query .= " group by art.parameter_id) as a";
        // }else{
        // 	///100
        //     $query = " select IF(am.ratingstyle=2, IFNULL(FORMAT(((SUM(art.percentage)/ count(pm.id))),2),0),
        // 	IFNULL(FORMAT((SUM(art.score)*100/SUM(pm.weight_value)),2),0)) as total_rating  
        //      from  assessment_results_trans  as art 
        // 	LEFT JOIN assessment_mst as am on am.id=art.assessment_id
        // 	LEFT JOIN parameter_mst pm ON pm.id = art.parameter_id
        // 	where art.assessment_id=" . $assessment_id . " AND art.user_id=" . $user_id. " AND art.trainer_id=".$trainer_id;

        //     // $query = " SELECT FORMAT(sum(parameter_wetg),2) as total_rating FROM(
        //     //     select IF(am.ratingstyle=2, IFNULL(FORMAT((SUM(art.percentage)/ count(pm.id)),2),0),
        //     //     IFNULL(FORMAT((SUM(art.score)/SUM(pm.weight_value)),2),0)) as parameter_wetg from assessment_results_trans art 
        //     //     LEFT JOIN assessment_mst as am on am.id=art.assessment_id 
        //     //     LEFT JOIN parameter_mst pm ON pm.id = art.parameter_id
        //     //     where art.assessment_id=" . $assessment_id . " AND art.user_id=" . $user_id. " AND art.trainer_id=".$trainer_id;
        //     //     $query .= " group by art.parameter_id) as a";
        // }
        $query = "SELECT ROUND( IF(ps.weighted_percentage=0, SUM(ps.percentage)/count(ps.question_id), SUM(ps.weighted_percentage)/count(DISTINCT ps.question_id) ) ,2) AS total_rating 
            FROM assessment_results_trans AS ps 
            WHERE ps.user_id= $user_id AND ps.assessment_id = $assessment_id AND ps.trainer_id= $trainer_id";
        $query = $this->db->query($query);
        return $query->row();
    }
    public function get_your_rating_old1($assessment_id, $user_id, $trainer_id, $is_weights = 0)
    {
        $query = "SELECT is_weights,ratingstyle from assessment_mst where id=" . $assessment_id;
        $result = $this->db->query($query);
        $Assessment_set = $result->row();
        if ($is_weights == 1) {
            ///100
            $query = " SELECT FORMAT(sum(parameter_wetg),2) as total_rating FROM(
			select IF(am.ratingstyle=2, IFNULL(FORMAT(((SUM(art.percentage)/ count(pm.id))/100)* aw.percentage,2),0),
			IFNULL(FORMAT((SUM(art.score)/SUM(pm.weight_value))*aw.percentage,2),0)) as parameter_wetg  from assessment_results_trans  art 
			LEFT JOIN assessment_mst as am on am.id=art.assessment_id
			LEFT JOIN parameter_mst pm ON pm.id = art.parameter_id
			LEFT JOIN assessment_para_weights as aw ON aw.assessment_id=art.assessment_id AND aw.parameter_id=art.parameter_id
			where art.assessment_id=" . $assessment_id . " AND art.user_id=" . $user_id . " AND art.trainer_id=" . $trainer_id;
            $query .= " group by art.parameter_id) as a";
        } else {
            ///100
            $query = " select IF(am.ratingstyle=2, IFNULL(FORMAT(((SUM(art.percentage)/ count(pm.id))),2),0),
			IFNULL(FORMAT((SUM(art.score)*100/SUM(pm.weight_value)),2),0)) as total_rating  from assessment_results_trans  art 
			LEFT JOIN assessment_mst as am on am.id=art.assessment_id
			LEFT JOIN parameter_mst pm ON pm.id = art.parameter_id
			where art.assessment_id=" . $assessment_id . " AND art.user_id=" . $user_id . " AND art.trainer_id=" . $trainer_id;
        }

        /*$query = " select if(b.is_weights=1,FORMAT(SUM(art.accuracy),2),IFNULL(FORMAT(SUM(art.score)/ SUM(art.weight_value),2),0) ) AS total_rating from assessment_trainer_weights art LEFT JOIN assessment_mst as b on b.id=art.assessment_id
                    where art.assessment_id=" . $assessment_id . " AND art.user_id=" . $user_id. " AND art.trainer_id=".$trainer_id*/;
        $query = $this->db->query($query);
        return $query->row();
    }
    public function get_team_rating($assessment_id, $user_id, $trainer_id, $iscompletedrate = 1)
    {
        $query = " select if(b.is_weights=1,FORMAT(SUM(art.accuracy),2),IFNULL(FORMAT(SUM(art.score)/ SUM(art.weight_value),2),0) ) as total_rating,
		count(distinct art.trainer_id) as total_trainer  from assessment_trainer_weights  art LEFT JOIN assessment_mst as b on b.id=art.assessment_id
                    where art.assessment_id=" . $assessment_id . " AND art.user_id=" . $user_id . " AND art.trainer_id !=" . $trainer_id;
        $query = $this->db->query($query);
        return $query->row();
    }
    public function get_your_rating_old($assessment_id, $user_id, $trainer_id, $iscompletedrate = 1)
    {
        $query = " select IFNULL(SUM(art.score),0) as total_score,IFNULL(SUM(art.percentage),0) as avg_percentage,IFNULL(count(pm.id),0) as total_param,IFNULL(SUM(pm.weight_value),0) as total_rating,
                    IFNULL(SUM(art.score*aw.percentage/10),0) as w_score,IFNULL(SUM(art.percentage*aw.percentage/10),0) as w_percentage,SUM(aw.percentage/10) as weights
         
                         from assessment_results_trans  art 
                        LEFT JOIN parameter_mst pm ON pm.id = art.parameter_id
                        LEFT JOIN assessment_para_weights as aw ON aw.assessment_id=art.assessment_id AND aw.parameter_id=art.parameter_id
                        where art.assessment_id=" . $assessment_id . " AND art.user_id=" . $user_id . " AND art.trainer_id=" . $trainer_id;
        if ($iscompletedrate) {
            $query .= " AND art.trainer_id IN (SELECT trainer_id FROM assessment_complete_rating WHERE assessment_id=" . $assessment_id . " AND user_id=" . $user_id . ")";
        }
        $query = $this->db->query($query);
        return $query->row();
    }
    public function get_team_rating_old($assessment_id, $user_id, $trainer_id, $iscompletedrate = 1)
    {
        $query = " SELECT IFNULL(SUM(art.user_rating),0) AS total_rating, count(art.trainer_id) as total_trainer
			FROM assessment_trainer_result art
			WHERE art.assessment_id=" . $assessment_id . " AND art.user_id=" . $user_id . " AND art.trainer_id!=" . $trainer_id;
        if ($iscompletedrate) {
            $query .= " AND art.trainer_id IN (SELECT trainer_id FROM assessment_managers WHERE assessment_id=" . $assessment_id . ")";
        }
        $query .= " group by art.user_id";
        $resultset = $this->db->query($query);
        return $resultset->row();
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
                 FROM assessment_managers  ar
                LEFT JOIN company_users u ON u.userid=ar.trainer_id
                WHERE ar.assessment_id=" . $assessment_id;
        if ($trainer_id != '') {
            $query .= " AND ar.trainer_id=" . $trainer_id;
        }
        $query .= " order by ar.trainer_id";
        $query = $this->db->query($query);
        return $query->result();
    }
    public function get_trainerdata_new($assessment_id, $trainer_id = '')
    {
        $query = " SELECT ar.trainer_id,CONCAT(u.first_name,' ',u.last_name) as name,ar.user_id 
                FROM assessment_mapping_user ar
                --  FROM assessment_managers  ar
                LEFT JOIN company_users u ON u.userid=ar.trainer_id
                WHERE ar.assessment_id=" . $assessment_id;
        if ($trainer_id != '') {
            $query .= " AND ar.trainer_id=" . $trainer_id;
        }
        $query .= " order by ar.trainer_id";
        $query = $this->db->query($query);
        $user_trainer = $query->result();
        $trainer_array = array();
        if (count((array) $user_trainer) > 0) {
            foreach ($user_trainer as $value) {
                $trainer_array[$value->user_id][] = array('trainer_id' => $value->trainer_id,'name' => $value->name);
            }
        }
        return $trainer_array;
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
            . " FROM device_users as  u "
            . " LEFT JOIN assessment_results as w ON w.user_id=u.user_id"
            . " LEFT JOIN assessment_mst as am ON w.assessment_id=am.id ";
        $query .= " where w.assessment_id=" . $assess_id . " group by w.user_id ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function isCompletedAssessor($assessment_id, $trainer_id, $user_id)
    {
        $query = " SELECT id FROM assessment_complete_rating  "
            . " WHERE assessment_id= " . $assessment_id . " AND trainer_id=" . $trainer_id . " AND user_id=" . $user_id;
        $result = $this->db->query($query);
        if (count((array) $result->row()) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function get_assessment_parameter($assessment_id)
    {
        $query = " SELECT @a:=@a+1 qes_no,id,question_id,
		parameter_id FROM assessment_trans, (SELECT @a:= 0) AS a 
		WHERE assessment_id=$assessment_id ORDER by id ";
        $query = $this->db->query($query);
        $resultset = $query->result();
        $Question_set = array();
        if (count((array) $resultset) > 0) {
            foreach ($resultset as $value) {
                // $query = " select id,description,weight_value from parameter_mst  
                // where id IN(".$value->parameter_id." )";
                $query = "SELECT DISTINCT
                pm.id,
                pm.description,
                pm.weight_value,
                plm.id as parameter_label_id,
                plm.description as parameter_label_name 
                 FROM parameter_mst  as pm
                LEFT JOIN parameter_label_mst as plm ON pm.id = plm.parameter_id
                WHERE pm.id IN(" . $value->parameter_id . " )";

                $query2 = $this->db->query($query);
                $resultset2 = $query2->result();
                $tempArray = array();
                foreach ($resultset2 as $value2) {
                    $tempArray[trim(strtolower($value2->parameter_label_name))] = $value2;
                }
                $Question_set['Q' . $value->qes_no] = array('trans_id' => $value->id, 'question_id' => $value->question_id, 'parameterset' => $tempArray);
            }
        }
        return $Question_set;
    }
    public function insert_parameterwise_data($assessment_id, $ratingstyle, $is_weights, $user_id = '')
    {
        $lcsqlstr = "INSERT INTO  assessment_trainer_weights(user_id,assessment_id,parameter_id,parameter_label_id,trainer_id,given_rating,total_rating,score,weight_value,accuracy) 
            SELECT ar.user_id,$assessment_id as assessment_id,art.parameter_id,art.parameter_label_id,art.trainer_id,";
        /*if($is_weights && $ratingstyle==2){
                  $lcsqlstr .= "SUM(art.percentage),COUNT(pm.id),FORMAT(SUM(art.percentage)/ COUNT(pm.id),2),COUNT(pm.id),IFNULL(FORMAT(((SUM(art.percentage)/ COUNT(pm.id))/100)*aw.percentage,2),0) as accuracy";
              }elseif($is_weights && $ratingstyle==1){
                  $lcsqlstr .="SUM(art.score),SUM(pm.weight_value),FORMAT((SUM(art.score)*100/SUM(pm.weight_value)),2),aw.percentage,
                  IFNULL(FORMAT((SUM(art.score)/SUM(pm.weight_value))*aw.percentage,2),0) as accuracy";
              }else*/
        if ($ratingstyle == 2) {
            // $lcsqlstr .= "SUM(art.percentage),COUNT(pm.id),SUM(art.percentage),COUNT(pm.id),
            // IFNULL(FORMAT(SUM(art.percentage)/ COUNT(pm.id),2),0) as accuracy";
            $lcsqlstr .= "IF(art.weighted_percentage=0,SUM(art.percentage),SUM(art.weighted_percentage)), COUNT(pm.id),
                        IF(art.weighted_percentage=0,SUM(art.percentage),SUM(art.weighted_percentage)),COUNT(pm.id),
                        ROUND( IF(art.weighted_percentage=0, SUM(art.percentage)/count(pm.id), SUM(art.percentage*(aw.parameter_weight))/SUM(aw.parameter_weight) ) ,2) as accuracy ";
        } else {
            $lcsqlstr .= "SUM(art.score),SUM(pm.weight_value),SUM(art.score)*100 ,SUM(pm.weight_value),
			IFNULL(FORMAT(SUM(art.score)*100/ SUM(pm.weight_value),2),0) as accuracy";
        }
        $lcsqlstr .= "  FROM  assessment_results  ar LEFT JOIN assessment_results_trans art ON art.result_id=ar.id
			AND art.assessment_id=ar.assessment_id AND art.user_id=ar.user_id
			INNER JOIN assessment_complete_rating AS cr ON cr.assessment_id=art.assessment_id AND cr.user_id=ar.user_id AND cr.trainer_id=art.trainer_id
			LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
			LEFT JOIN assessment_trans_sparam as aw ON aw.assessment_id=ar.assessment_id AND aw.parameter_id=art.parameter_id AND aw.question_id=art.question_id
			WHERE cr.assessment_id =$assessment_id AND cr.issync=0 ";
        if ($user_id != '') {
            $lcsqlstr .= " AND ar.user_id=" . $user_id;
        }
        $lcsqlstr .= " GROUP BY ar.user_id,art.trainer_id,art.parameter_id,art.parameter_label_id";
        $this->db->query($lcsqlstr);

        $this->insert_assessmentwise_data($assessment_id, $ratingstyle, $is_weights, $user_id);
        return true;
    }
    public function insert_assessmentwise_data($assessment_id, $ratingstyle, $is_weights, $user_id = '')
    {
        if ($user_id != '') {
            $query = "delete  FROM  assessment_final_results where assessment_id=" . $assessment_id . " AND user_id=" . $user_id;
            $this->db->query($query);
            $lcsqlStr2 = " Update assessment_complete_rating set issync=0 where assessment_id=$assessment_id AND user_id =" . $user_id;
            $this->db->query($lcsqlStr2);
        }
        $lcsqlstr = "INSERT INTO assessment_final_results(user_id,assessment_id,given_rating,total_rating,accuracy) ";
        if ($is_weights) {
            $lcsqlstr .= "SELECT a.user_id,$assessment_id as assessment_id,sum(a.given_rating),sum(a.total_rating),sum(accuracy)  FROM
			(SELECT ar.user_id,";
            if ($ratingstyle == 2) {
                // $lcsqlstr .= "SUM(art.percentage) as given_rating,COUNT(pm.id) as total_rating,IFNULL(FORMAT(((SUM(art.percentage)/ COUNT(pm.id))/100)*aw.percentage,2),0) as accuracy";
                $lcsqlstr .= "SUM(art.weighted_percentage) as given_rating,COUNT(pm.id) as total_rating,
                IFNULL(ROUND( AVG(art.weighted_percentage) ,2), 0) as accuracy ";
            } else {
                $lcsqlstr .= "SUM(art.score) as given_rating,SUM(pm.weight_value) as total_rating,IFNULL(FORMAT((SUM(art.score)*100/SUM(pm.weight_value)),2),0) as accuracy ";
            }
            $lcsqlstr .= "  FROM  assessment_results  ar LEFT JOIN assessment_results_trans art ON art.result_id=ar.id
			AND art.assessment_id=ar.assessment_id AND art.user_id=ar.user_id
			INNER JOIN assessment_complete_rating AS cr ON cr.assessment_id=art.assessment_id AND cr.user_id=ar.user_id AND cr.trainer_id=art.trainer_id
			LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
			LEFT JOIN assessment_trans_sparam as aw ON aw.assessment_id=ar.assessment_id AND aw.parameter_id=art.parameter_id AND aw.question_id=art.question_id
			WHERE cr.assessment_id =$assessment_id AND cr.issync=0 GROUP BY ar.user_id,art.parameter_id,art.parameter_label_id) as a group by a.user_id";
        } else {
            $lcsqlstr .= "SELECT ar.user_id,$assessment_id as assessment_id,";
            if ($ratingstyle == 2) {
                $lcsqlstr .= "SUM(art.percentage)as given_rating,COUNT(pm.id) as total_rating,IFNULL(FORMAT(SUM(art.percentage)/ COUNT(pm.id),2),0) as accuracy ";
            } else {
                $lcsqlstr .= "SUM(art.score) as given_rating,SUM(pm.weight_value) as total_rating,IFNULL(FORMAT(SUM(art.score)*100/ SUM(pm.weight_value),2),0) as accuracy ";
            }
            $lcsqlstr .= "  FROM  assessment_results  ar LEFT JOIN assessment_results_trans art ON art.result_id=ar.id
			AND art.assessment_id=ar.assessment_id AND art.user_id=ar.user_id
			INNER JOIN assessment_complete_rating AS cr ON cr.assessment_id=art.assessment_id AND cr.user_id=ar.user_id AND cr.trainer_id=art.trainer_id
			LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
			WHERE cr.assessment_id =$assessment_id AND cr.issync=0 GROUP BY ar.user_id";
        }
        $this->db->query($lcsqlstr);

        $lcsqlStr2 = " Update assessment_complete_rating set issync=1 
		where assessment_id=$assessment_id AND trainer_id IN(select distinct trainer_id  FROM  assessment_trainer_weights  where assessment_id=$assessment_id)";
        $this->db->query($lcsqlStr2);

        return true;
    }
    public function get_parameter_label($assessment_id, $question_id)
    {
        $query = "SELECT
        ats.id,
        ats.assessment_id,
        ats.question_id,
        ats.ai_methods,
        ats.language_id,
        ats.txn_id,
        ats.parameter_id,
        pm.description as parameter_name,
        ats.parameter_label_id,
        plm.description as parameter_label_name,
        ats.sub_parameter_id,
        ats.type_id,
        ats.sentence_keyword 
         FROM
        assessment_trans_sparam  AS ats
        LEFT JOIN parameter_mst AS pm ON ats.parameter_id = pm.id
        LEFT JOIN parameter_label_mst AS plm ON ats.parameter_label_id = plm.id
        WHERE 
        ats.assessment_id='" . $assessment_id . "' AND ats.question_id='" . $question_id . "'";
        $result = $this->db->query($query);
        return $result->result();
    }
}