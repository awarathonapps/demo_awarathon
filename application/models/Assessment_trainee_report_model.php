<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Assessment_trainee_report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
	public function get_all_assessment($report_type_catg)
    {
		if($report_type_catg=="2" || $report_type_catg=="3")
		{
        	$query= "SELECT distinct ap.id as assessment_id, ap.assessment as assessment
        	from assessment_mst ap where ap.report_type='2' OR ap.report_type='3' 
        	group by ap.id
        	ORDER by ap.assessment";
           $result = $this->db->query($query);
           return $result->result();
		}
		elseif($report_type_catg=="1")
		{
			$query= "SELECT distinct ap.id as assessment_id, ap.assessment as assessment
        	from assessment_mst ap where ap.report_type='3' OR ap.report_type='1'
        	group by ap.id
        	ORDER by ap.assessment";
           $result = $this->db->query($query);
           return $result->result();
		}
    }
	public function LoadDataTable($assessment_id ,$dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag, $report_type, $parameter_id, $report_type_catg) {
		$login_id = $this->mw_session['user_id'];
		if($report_type_catg=="2")
		{
			if($assessment_id >0){
			$query = "SELECT is_weights,ratingstyle from assessment_mst where id=".$assessment_id;
			$result = $this->db->query($query);
			$Assessment_set = $result->row();
			if($report_type == 3 && $parameter_id == 0 && $Assessment_set->is_weights==1){
					$query = "select ar.user_id,ar.assessment_id,CONCAT(ar.accuracy,'%') as result,ar.accuracy as hv_range,IF(am.ratingstyle=2,'--',FORMAT(ar.total_rating,0)) as total_rating,IF(am.ratingstyle=2,'--',FORMAT(ar.given_rating,0)) as rating,CONCAT(du.firstname,' ',du.lastname) AS traineename,rg.region_name AS trainee_region,dt.description AS designation,am.assessment,
					'' as title,du.emp_id FROM assessment_final_results	as ar
					LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
					LEFT JOIN device_users du ON du.user_id=ar.user_id
					LEFT JOIN region rg ON rg.id=du.region_id
					LEFT JOIN designation_trainee dt ON dt.id=du.designation_id";
					$query .= " $dtWhere  ";
					if (!$RightsFlag) {
						$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
					}
				$query .=" $dthaving ";
				$query_count = $query;
				$query .= " $dtOrder $dtLimit ";	
			}else if($report_type == 2 && $Assessment_set->is_weights==1){
				$query = "SELECT a.assessment,a.trainee_region,a.user_id,a.traineename,a.emp_id,a.designation,sum(a.result),sum(a.parameter_Wt),CONCAT(
				FORMAT(((sum(a.result)/sum(a.parameter_Wt))*100),2),'%') as result,((sum(a.result)/sum(a.parameter_Wt))*100 ) as hv_range,sum(a.total_rating) as total_rating,sum(a.rating) as rating,
				a.title FROM(
					
				SELECT ar.assessment_id,art.question_id, b.total_parameter,art.parameter_id,IF(am.ratingstyle=2, IFNULL(((SUM(art.percentage)/ count(pm.id))/100)*(aw.percentage/b.total_parameter),0), IFNULL((SUM(art.score)/SUM(pm.weight_value))*(aw.percentage/b.total_parameter),0)) AS result,(aw.percentage/b.total_parameter) as parameter_Wt,am.assessment,
				IF(am.ratingstyle=2,'--',SUM(pm.weight_value)) AS total_rating,IF(am.ratingstyle=2,'--',SUM(art.score)) AS rating,
				du.user_id,du.emp_id, CONCAT(du.firstname,' ',du.lastname) AS traineename,aq.question as title,rg.region_name AS trainee_region,
				dt.description AS designation
				FROM assessment_results ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id 
				LEFT JOIN assessment_results_trans art ON art.result_id=ar.id and art.assessment_id=ar.assessment_id and art.user_id=ar.user_id 
				LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
				LEFT JOIN assessment_trans_sparam as ats ON ats.assessment_id = art.assessment_id AND ats.question_id=art.question_id AND ats.parameter_id = art.parameter_id 
				LEFT JOIN parameter_label_mst as plm ON plm.id = ats.parameter_label_id AND plm.parameter_id = ats.parameter_id
				LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=art.assessment_id 
				AND acr.user_id=art.user_id AND acr.trainer_id=art.trainer_id 
				LEFT JOIN assessment_para_weights AS aw ON aw.assessment_id=art.assessment_id AND aw.parameter_id=art.parameter_id
				LEFT JOIN device_users du ON du.user_id=ar.user_id
				LEFT JOIN region rg ON rg.id=du.region_id
				LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
				LEFT JOIN assessment_question aq ON aq.id=art.question_id
				LEFT JOIN (
				  select ar.user_id,count(DISTINCT ar.question_id) as total_parameter,ar.parameter_id
				  FROM assessment_results_trans ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id 
					  LEFT JOIN device_users du ON du.user_id=ar.user_id
					LEFT JOIN region rg ON rg.id=du.region_id
					LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
					LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=ar.assessment_id
					LEFT JOIN assessment_question aq ON aq.id=ar.question_id
						$dtWhere 
						group by ar.user_id,ar.assessment_id,ar.parameter_id) as b ON b.user_id=ar.user_id AND b.parameter_id=art.parameter_id";
				$query .= " $dtWhere AND art.question_id !='' AND acr.trainer_id !='' ";
				if (!$RightsFlag) {
					$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
				}
				$query .=" group by ar.user_id,ar.assessment_id,art.question_id,art.parameter_id $dtOrder
				  ) as a group by a.user_id,a.assessment_id,a.question_id ";
				  $query .=" $dthaving ";
				$query_count = $query;
				$query .= "  $dtLimit ";
			}else{
				$query = "SELECT cm.company_name,du.user_id,du.emp_id, CONCAT(du.firstname,' ',du.lastname) AS traineename, 
                IF(am.ratingstyle=2, CONCAT(IFNULL(FORMAT(SUM(art.percentage)/ COUNT(pm.id),2),0),'%'), CONCAT(IFNULL(FORMAT(SUM(art.score)*100/ SUM(pm.weight_value),2),0),'%')) AS result,                
                rg.region_name AS trainee_region,IF(am.ratingstyle=2,(FORMAT(SUM(art.percentage)/ count(pm.id),2)),(FORMAT(SUM(art.score)*100/ SUM(pm.weight_value),2))) AS hv_range,IF(am.ratingstyle=2,'--',SUM(pm.weight_value)) AS total_rating,IF(am.ratingstyle=2,'--',SUM(art.score)) AS rating,dt.description AS designation, 
                am.assessment ";
				if ($report_type == 1) {
					$query .=" ,pm.description as old_title,IFNULL(plm.description,pm.description) as title ";
				} else if ($report_type == 2) {
					$query .=", aq.question as title ";
				} else {
					$query .=",'' as title ";
				}
				$query .= " FROM assessment_results ar
						LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
						LEFT JOIN assessment_results_trans art ON art.result_id=ar.id and art.assessment_id=ar.assessment_id and art.user_id=ar.user_id
						LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
						LEFT JOIN assessment_trans_sparam as ats ON ats.assessment_id = art.assessment_id AND ats.question_id=art.question_id AND ats.parameter_id = art.parameter_id 
						LEFT JOIN parameter_label_mst as plm ON plm.id = ats.parameter_label_id AND plm.parameter_id = ats.parameter_id
						LEFT JOIN device_users du ON du.user_id=ar.user_id
						LEFT JOIN region rg ON rg.id=du.region_id
						LEFT JOIN company cm ON cm.id=ar.company_id
						LEFT JOIN designation_trainee dt ON dt.id=du.designation_id 
						LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=art.assessment_id AND acr.user_id=art.user_id AND acr.trainer_id=art.trainer_id";
				if ($report_type == 2) {
					$query .=" LEFT JOIN assessment_question aq ON aq.id=art.question_id ";
				}
				$query .= " $dtWhere AND art.question_id !='' AND acr.trainer_id !='' ";
				if (!$RightsFlag) {
					$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
				}
				$query .=" group by ar.user_id,ar.assessment_id ";
				if ($report_type == 1) {
					$query .=", art.parameter_id ";
				} else if ($report_type == 2) {
					$query .=", art.question_id ";
				}
				$query .=" $dthaving ";
				$query_count = $query;
				$query .= " $dtOrder $dtLimit ";
			}
			//echo $query;
			$result = $this->db->query($query);
			$data['ResultSet'] = $result->result_array();
			$data['dtPerPageRecords'] = $result->num_rows();

			$result = $this->db->query($query_count);
			$data_array = $result->result_array();
			$total = count((array)$data_array);
			$data['dtTotalRecords'] = $total;	
		}
     	   return $data;
		}
		elseif($report_type_catg=="1")
		{
			if($assessment_id >0){
				$query = "SELECT is_weights,ratingstyle from assessment_mst where id=".$assessment_id;
				$result = $this->db->query($query);
				$Assessment_set = $result->row();
				if($report_type == 3 && $parameter_id == 0 && $Assessment_set->is_weights==1){
						$query = "select ar.user_id,ar.assessment_id,CONCAT(SUM(ar.score)/(ar.question_id),'%') as result,ar.accuracy as hv_range,IF(am.ratingstyle=2,'--',FORMAT(ar.total_rating,0)) as total_rating,IF(am.ratingstyle=2,'--',FORMAT(ar.given_rating,0)) as rating,CONCAT(du.firstname,' ',du.lastname) AS traineename,rg.region_name AS trainee_region,dt.description AS designation,am.assessment,
						'' as title,du.emp_id FROM ai_subparameter_scor as ar
						LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
						LEFT JOIN device_users du ON du.user_id=ar.user_id
						LEFT JOIN region rg ON rg.id=du.region_id
						LEFT JOIN designation_trainee dt ON dt.id=du.designation_id";
						$query .= " $dtWhere  ";
						if (!$RightsFlag) {
							$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
						}
					$query .=" $dthaving ";
					$query_count = $query;
					$query .= " $dtOrder $dtLimit ";	
				}else if($report_type == 2 && $Assessment_set->is_weights==1){
					$query = "SELECT a.assessment,a.trainee_region,a.user_id,a.traineename,a.emp_id,a.designation,sum(a.result),sum(a.parameter_Wt),CONCAT(
					FORMAT(((sum(a.result)/sum(a.parameter_Wt))*100),2),'%') as result,((sum(a.result)/sum(a.parameter_Wt))*100 ) as hv_range,sum(a.total_rating) as total_rating,sum(a.rating) as rating,
					a.title FROM(
						
					SELECT ar.assessment_id,art.question_id, b.total_parameter,art.parameter_id,IF(am.ratingstyle=2, IFNULL(((SUM(art.percentage)/ count(pm.id))/100)*(aw.percentage/b.total_parameter),0), IFNULL((SUM(art.score)/SUM(pm.weight_value))*(aw.percentage/b.total_parameter),0)) AS result,(aw.percentage/b.total_parameter) as parameter_Wt,am.assessment,
					IF(am.ratingstyle=2,'--',SUM(pm.weight_value)) AS total_rating,IF(am.ratingstyle=2,'--',SUM(art.score)) AS rating,
					du.user_id,du.emp_id, CONCAT(du.firstname,' ',du.lastname) AS traineename,aq.question as title,rg.region_name AS trainee_region,
					dt.description AS designation
					FROM assessment_results ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id 
					LEFT JOIN assessment_results_trans art ON art.result_id=ar.id and art.assessment_id=ar.assessment_id and art.user_id=ar.user_id 
					LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
					LEFT JOIN assessment_trans_sparam as ats ON ats.assessment_id = art.assessment_id AND ats.question_id=art.question_id AND ats.parameter_id = art.parameter_id 
					LEFT JOIN parameter_label_mst as plm ON plm.id = ats.parameter_label_id AND plm.parameter_id = ats.parameter_id
					LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=art.assessment_id 
					AND acr.user_id=art.user_id AND acr.trainer_id=art.trainer_id 
					LEFT JOIN assessment_para_weights AS aw ON aw.assessment_id=art.assessment_id AND aw.parameter_id=art.parameter_id
					LEFT JOIN device_users du ON du.user_id=ar.user_id
					LEFT JOIN region rg ON rg.id=du.region_id
					LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
					LEFT JOIN assessment_question aq ON aq.id=art.question_id
					LEFT JOIN (
					  select ar.user_id,count(DISTINCT ar.question_id) as total_parameter,ar.parameter_id
					  FROM assessment_results_trans ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id 
						  LEFT JOIN device_users du ON du.user_id=ar.user_id
						LEFT JOIN region rg ON rg.id=du.region_id
						LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
						LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=ar.assessment_id
						LEFT JOIN assessment_question aq ON aq.id=ar.question_id
							$dtWhere 
							group by ar.user_id,ar.assessment_id,ar.parameter_id) as b ON b.user_id=ar.user_id AND b.parameter_id=art.parameter_id";
					$query .= " $dtWhere AND art.question_id !='' AND acr.trainer_id !='' ";
					if (!$RightsFlag) {
						$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
					}
					$query .=" group by ar.user_id,ar.assessment_id,art.question_id,art.parameter_id $dtOrder
					  ) as a group by a.user_id,a.assessment_id,a.question_id ";
					  $query .=" $dthaving ";
					$query_count = $query;
					$query .= "  $dtLimit ";
				}else{
					$query = "SELECT * from (SELECT cm.company_name,du.user_id,du.emp_id, ar.assessment_id ,ar.parameter_id, ar.question_id, CONCAT(du.firstname,' ',du.lastname) AS traineename, 
					IF(am.ratingstyle=2, CONCAT(IFNULL(FORMAT(SUM(ar.score)/ COUNT(ar.parameter_id),2),0),'%'), 
					CONCAT(IFNULL(FORMAT(SUM(ar.score)/count(ar.question_id),2),0),'%')) AS result,
					rg.region_name AS trainee_region,IF(am.ratingstyle=2,(FORMAT(SUM(ar.score)/ count(ar.question_id),2)),(FORMAT(SUM(ar.score)/ COUNT(ar.parameter_id),2))) AS hv_range,IF(am.ratingstyle=2,'--',SUM(pm.weight_value)) AS total_rating,IF(am.ratingstyle=2,'--',SUM(ar.score)) AS rating, 
					dt.description AS designation, 
					am.assessment ";
					if ($report_type == 1) {
						$query .=" ,pm.description as old_title,IFNULL(plm.description,pm.description) as title ";
					} else if ($report_type == 2) {
						$query .=", aq.question as title ";
					} else {
						$query .=",'' as title ";
					}
					$query .= " FROM ai_subparameter_score as ar 
							LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
							
							LEFT JOIN parameter_mst pm ON pm.id=ar.parameter_id
							
							LEFT JOIN parameter_label_mst as plm ON plm.id = ar.parameter_label_id AND plm.parameter_id = ar.parameter_id
							LEFT JOIN device_users du ON du.user_id=ar.user_id
							LEFT JOIN region rg ON rg.id=du.region_id
							LEFT JOIN company cm ON cm.id=ar.company_id
							LEFT JOIN designation_trainee dt ON dt.id=du.designation_id 
							LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=ar.assessment_id AND acr.user_id=ar.user_id";
					if ($report_type == 2) {
						$query .=" LEFT JOIN assessment_question aq ON aq.id=ar.question_id ";
					}
					$query .= " $dtWhere AND parameter_type = 'parameter' AND ar.question_id !='' AND acr.trainer_id !='' ";
					if (!$RightsFlag) {
						$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
					}
					//$query .=" group by ar.parameter_id, ar.user_id,ar.assessment_id, ar.parameter_label_id ,ar.question_id ORDER BY ar.user_id ) as a GROUP BY  a.user_id, a.parameter_id asc LIMIT 0, 10";
					if ($report_type == 1) {
						$query .=" group by ar.parameter_id, ar.user_id,ar.assessment_id, ar.parameter_label_id ,ar.question_id ORDER BY ar.user_id ) as a GROUP BY  a.user_id, a.parameter_id asc";
					} else if ($report_type == 2) {
						$query .=" group by ar.parameter_id, ar.user_id,ar.question_id ORDER BY ar.user_id ) as a GROUP BY  a.user_id, a.question_id asc";
					}
					else
					{
						$query.= "group by ar.user_id, ar.assessment_id ) as a group by a.user_id, a.assessment_id ORDER BY a.user_id asc";
					}
					
					$query .=" $dthaving ";
					$query_count = $query;
					$query .= "  $dtLimit ";
					
				}
				
				$result = $this->db->query($query);
				$data['ResultSet'] = $result->result_array();
				$data['dtPerPageRecords'] = $result->num_rows();
	
				$result = $this->db->query($query_count);
				$data_array = $result->result_array();
				$total = count((array)$data_array);
				$data['dtTotalRecords'] = $total;	
			}
				return $data;
		}
    }
    public function exportToExcel($assessment_id,$dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag, $report_type = 1,$parameter_id, $report_type_catg) {
        $login_id = $this->mw_session['user_id'];
		if($report_type_catg=="2")
		{
			if($assessment_id >0){
			$query = "SELECT is_weights,ratingstyle from assessment_mst where id=".$assessment_id;
			$result = $this->db->query($query);
			$Assessment_set = $result->row();
			if($report_type == 3 && $parameter_id == 0 && $Assessment_set->is_weights==1){
					$query = "select ar.user_id,ar.assessment_id,CONCAT(ar.accuracy,'%') as result,ar.accuracy as hv_range,IF(am.ratingstyle=2,'--',FORMAT(ar.total_rating,0)) as total_rating,IF(am.ratingstyle=2,'--',FORMAT(ar.given_rating,0)) as rating,CONCAT(du.firstname,' ',du.lastname) AS traineename,rg.region_name AS trainee_region,dt.description AS designation,am.assessment,
					'' as title,du.emp_id FROM assessment_final_results	as ar
					LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
					LEFT JOIN device_users du ON du.user_id=ar.user_id
					LEFT JOIN region rg ON rg.id=du.region_id
					LEFT JOIN designation_trainee dt ON dt.id=du.designation_id";
					$query .= " $dtWhere  ";
					if (!$RightsFlag) {
						$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
					}
				$query .=" $dthaving ";
				$query_count = $query;
				$query .= " $dtOrder $dtLimit ";	
			}else if($report_type == 2 && $Assessment_set->is_weights==1){
				$query = "SELECT a.assessment,a.trainee_region,a.user_id,a.traineename,a.emp_id,a.designation,sum(a.result),sum(a.parameter_Wt),CONCAT(
				FORMAT(((sum(a.result)/sum(a.parameter_Wt))*100),2),'%') as result,((sum(a.result)/sum(a.parameter_Wt))*100 ) as hv_range,sum(a.total_rating) as total_rating,sum(a.rating) as rating,
				a.title FROM(
					
				SELECT ar.assessment_id,art.question_id, b.total_parameter,art.parameter_id,IF(am.ratingstyle=2, IFNULL(((SUM(art.percentage)/ count(pm.id))/100)*(aw.percentage/b.total_parameter),0), IFNULL((SUM(art.score)/SUM(pm.weight_value))*(aw.percentage/b.total_parameter),0)) AS result,(aw.percentage/b.total_parameter) as parameter_Wt,am.assessment,
				IF(am.ratingstyle=2,'--',SUM(pm.weight_value)) AS total_rating,IF(am.ratingstyle=2,'--',SUM(art.score)) AS rating,
				du.user_id,du.emp_id, CONCAT(du.firstname,' ',du.lastname) AS traineename,aq.question as title,rg.region_name AS trainee_region,
				dt.description AS designation
				FROM assessment_results ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id 
				LEFT JOIN assessment_results_trans art ON art.result_id=ar.id and art.assessment_id=ar.assessment_id and art.user_id=ar.user_id 
				LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
				LEFT JOIN assessment_trans_sparam as ats ON ats.assessment_id = art.assessment_id AND ats.question_id=art.question_id AND ats.parameter_id = art.parameter_id 
				LEFT JOIN parameter_label_mst as plm ON plm.id = ats.parameter_label_id AND plm.parameter_id = ats.parameter_id
				LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=art.assessment_id 
				AND acr.user_id=art.user_id AND acr.trainer_id=art.trainer_id 
				LEFT JOIN assessment_para_weights AS aw ON aw.assessment_id=art.assessment_id AND aw.parameter_id=art.parameter_id
				LEFT JOIN device_users du ON du.user_id=ar.user_id
				LEFT JOIN region rg ON rg.id=du.region_id
				LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
				LEFT JOIN assessment_question aq ON aq.id=art.question_id
				LEFT JOIN (
				  select ar.user_id,count(DISTINCT ar.question_id) as total_parameter,ar.parameter_id
				  FROM assessment_results_trans ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id 
					  LEFT JOIN device_users du ON du.user_id=ar.user_id
					LEFT JOIN region rg ON rg.id=du.region_id
					LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
					LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=ar.assessment_id
					LEFT JOIN assessment_question aq ON aq.id=ar.question_id
						$dtWhere 
						group by ar.user_id,ar.assessment_id,ar.parameter_id) as b ON b.user_id=ar.user_id AND b.parameter_id=art.parameter_id";
				$query .= " $dtWhere AND art.question_id !='' AND acr.trainer_id !='' ";
				if (!$RightsFlag) {
					$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
				}
				$query .=" group by ar.user_id,ar.assessment_id,art.question_id,art.parameter_id $dtOrder
				  ) as a group by a.user_id,a.assessment_id,a.question_id ";
				  $query .=" $dthaving ";
				$query_count = $query;
				$query .= "  $dtLimit ";
			}else{
				$query = "SELECT cm.company_name,du.user_id,du.emp_id, CONCAT(du.firstname,' ',du.lastname) AS traineename, 
                IF(am.ratingstyle=2, CONCAT(IFNULL(FORMAT(SUM(art.percentage)/ COUNT(pm.id),2),0),'%'), CONCAT(IFNULL(FORMAT(SUM(art.score)*100/ SUM(pm.weight_value),2),0),'%')) AS result,                
                rg.region_name AS trainee_region,IF(am.ratingstyle=2,(FORMAT(SUM(art.percentage)/ count(pm.id),2)),(FORMAT(SUM(art.score)*100/ SUM(pm.weight_value),2))) AS hv_range,IF(am.ratingstyle=2,'--',SUM(pm.weight_value)) AS total_rating,IF(am.ratingstyle=2,'--',SUM(art.score)) AS rating,dt.description AS designation, 
                am.assessment ";
				if ($report_type == 1) {
					$query .=" ,pm.description as old_title,IFNULL(plm.description,pm.description) as title ";
				} else if ($report_type == 2) {
					$query .=", aq.question as title ";
				} else {
					$query .=",'' as title ";
				}
				$query .= " FROM assessment_results ar
						LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
						LEFT JOIN assessment_results_trans art ON art.result_id=ar.id and art.assessment_id=ar.assessment_id and art.user_id=ar.user_id
						LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
						LEFT JOIN assessment_trans_sparam as ats ON ats.assessment_id = art.assessment_id AND ats.question_id=art.question_id AND ats.parameter_id = art.parameter_id 
						LEFT JOIN parameter_label_mst as plm ON plm.id = ats.parameter_label_id AND plm.parameter_id = ats.parameter_id
						LEFT JOIN device_users du ON du.user_id=ar.user_id
						LEFT JOIN region rg ON rg.id=du.region_id
						LEFT JOIN company cm ON cm.id=ar.company_id
						LEFT JOIN designation_trainee dt ON dt.id=du.designation_id 
						LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=art.assessment_id AND acr.user_id=art.user_id AND acr.trainer_id=art.trainer_id";
				if ($report_type == 2) {
					$query .=" LEFT JOIN assessment_question aq ON aq.id=art.question_id ";
				}
				$query .= " $dtWhere AND art.question_id !='' AND acr.trainer_id !='' ";
				if (!$RightsFlag) {
					$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
				}
				$query .=" group by ar.user_id,ar.assessment_id ";
				if ($report_type == 1) {
					$query .=", art.parameter_id ";
				} else if ($report_type == 2) {
					$query .=", art.question_id ";
				}
				$query .=" $dthaving ";
				$query_count = $query;
				//$query .= " $dtOrder  ";
			}
			//echo $query;
			
		}
		$result = $this->db->query($query);
		return $result->result();	
		}
		elseif($report_type_catg=="1")
		{

			if($assessment_id >0){
				$query = "SELECT is_weights,ratingstyle from assessment_mst where id=".$assessment_id;
				$result = $this->db->query($query);
				$Assessment_set = $result->row();
				if($report_type == 3 && $parameter_id == 0 && $Assessment_set->is_weights==1){
						$query = "select ar.user_id,ar.assessment_id,CONCAT(SUM(ar.score)/(ar.question_id),'%') as result,ar.accuracy as hv_range,IF(am.ratingstyle=2,'--',FORMAT(ar.total_rating,0)) as total_rating,IF(am.ratingstyle=2,'--',FORMAT(ar.given_rating,0)) as rating,CONCAT(du.firstname,' ',du.lastname) AS traineename,rg.region_name AS trainee_region,dt.description AS designation,am.assessment,
						'' as title,du.emp_id FROM ai_subparameter_scor as ar
						LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
						LEFT JOIN device_users du ON du.user_id=ar.user_id
						LEFT JOIN region rg ON rg.id=du.region_id
						LEFT JOIN designation_trainee dt ON dt.id=du.designation_id";
						$query .= " $dtWhere  ";
						if (!$RightsFlag) {
							$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
						}
					$query .=" $dthaving ";
					$query_count = $query;
					$query .= " $dtOrder $dtLimit ";	
				}else if($report_type == 2 && $Assessment_set->is_weights==1){
					
					$query = "SELECT a.assessment,a.trainee_region,a.user_id,a.traineename,a.emp_id,a.designation,sum(a.result),sum(a.parameter_Wt),CONCAT(
					FORMAT(((sum(a.result)/sum(a.parameter_Wt))*100),2),'%') as result,((sum(a.result)/sum(a.parameter_Wt))*100 ) as hv_range,sum(a.total_rating) as total_rating,sum(a.rating) as rating,
					a.title FROM(
						
					SELECT ar.assessment_id,art.question_id, b.total_parameter,art.parameter_id,IF(am.ratingstyle=2, IFNULL(((SUM(art.percentage)/ count(pm.id))/100)*(aw.percentage/b.total_parameter),0), IFNULL((SUM(art.score)/SUM(pm.weight_value))*(aw.percentage/b.total_parameter),0)) AS result,(aw.percentage/b.total_parameter) as parameter_Wt,am.assessment,
					IF(am.ratingstyle=2,'--',SUM(pm.weight_value)) AS total_rating,IF(am.ratingstyle=2,'--',SUM(art.score)) AS rating,
					du.user_id,du.emp_id, CONCAT(du.firstname,' ',du.lastname) AS traineename,aq.question as title,rg.region_name AS trainee_region,
					dt.description AS designation
					FROM assessment_results ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id 
					LEFT JOIN assessment_results_trans art ON art.result_id=ar.id and art.assessment_id=ar.assessment_id and art.user_id=ar.user_id 
					LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
					LEFT JOIN assessment_trans_sparam as ats ON ats.assessment_id = art.assessment_id AND ats.question_id=art.question_id AND ats.parameter_id = art.parameter_id 
					LEFT JOIN parameter_label_mst as plm ON plm.id = ats.parameter_label_id AND plm.parameter_id = ats.parameter_id
					LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=art.assessment_id 
					AND acr.user_id=art.user_id AND acr.trainer_id=art.trainer_id 
					LEFT JOIN assessment_para_weights AS aw ON aw.assessment_id=art.assessment_id AND aw.parameter_id=art.parameter_id
					LEFT JOIN device_users du ON du.user_id=ar.user_id
					LEFT JOIN region rg ON rg.id=du.region_id
					LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
					LEFT JOIN assessment_question aq ON aq.id=art.question_id
					LEFT JOIN (
					  select ar.user_id,count(DISTINCT ar.question_id) as total_parameter,ar.parameter_id
					  FROM assessment_results_trans ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id 
						  LEFT JOIN device_users du ON du.user_id=ar.user_id
						LEFT JOIN region rg ON rg.id=du.region_id
						LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
						LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=ar.assessment_id
						LEFT JOIN assessment_question aq ON aq.id=ar.question_id
							$dtWhere 
							group by ar.user_id,ar.assessment_id,ar.parameter_id) as b ON b.user_id=ar.user_id AND b.parameter_id=art.parameter_id";
					$query .= " $dtWhere AND art.question_id !='' AND acr.trainer_id !='' ";
					if (!$RightsFlag) {
						$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
					}
					$query .=" group by ar.user_id,ar.assessment_id,art.question_id,art.parameter_id $dtOrder
					  ) as a group by a.user_id,a.assessment_id,a.question_id ";
					  $query .=" $dthaving ";
					$query_count = $query;
					$query .= "  $dtLimit ";
				}else{
					
					$query = "SELECT * from (SELECT cm.company_name,du.user_id,du.emp_id, ar.assessment_id ,ar.parameter_id, ar.question_id, CONCAT(du.firstname,' ',du.lastname) AS traineename, 
					IF(am.ratingstyle=2, CONCAT(IFNULL(FORMAT(SUM(ar.score)/ COUNT(ar.parameter_id),2),0),'%'), 
					CONCAT(IFNULL(FORMAT(SUM(ar.score)/count(ar.question_id),2),0),'%')) AS result,
					rg.region_name AS trainee_region,IF(am.ratingstyle=2,(FORMAT(SUM(ar.score)/ count(ar.question_id),2)),(FORMAT(SUM(ar.score)/ COUNT(ar.parameter_id),2))) AS hv_range,IF(am.ratingstyle=2,'--',SUM(pm.weight_value)) AS total_rating,IF(am.ratingstyle=2,'--',SUM(ar.score)) AS rating, 
					dt.description AS designation, 
					am.assessment ";
					if ($report_type == 1) {
						$query .=" ,pm.description as old_title,IFNULL(plm.description,pm.description) as title ";
					} else if ($report_type == 2) {
						$query .=", aq.question as title ";
					} else {
						$query .=",'' as title ";
					}
					$query .= " FROM ai_subparameter_score as ar 
							LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
							
							LEFT JOIN parameter_mst pm ON pm.id=ar.parameter_id
							
							LEFT JOIN parameter_label_mst as plm ON plm.id = ar.parameter_label_id AND plm.parameter_id = ar.parameter_id
							LEFT JOIN device_users du ON du.user_id=ar.user_id
							LEFT JOIN region rg ON rg.id=du.region_id
							LEFT JOIN company cm ON cm.id=ar.company_id
							LEFT JOIN designation_trainee dt ON dt.id=du.designation_id 
							LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=ar.assessment_id AND acr.user_id=ar.user_id";
					if ($report_type == 2) {
						$query .=" LEFT JOIN assessment_question aq ON aq.id=ar.question_id ";
					}
					$query .= " $dtWhere AND parameter_type = 'parameter' AND ar.question_id !='' AND acr.trainer_id !='' ";
					if (!$RightsFlag) {
						$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
					}
					//$query .=" group by ar.parameter_id, ar.user_id,ar.assessment_id, ar.parameter_label_id ,ar.question_id ORDER BY ar.user_id ) as a GROUP BY  a.user_id, a.parameter_id asc LIMIT 0, 10";
					if ($report_type == 1) {
						$query .=" group by ar.parameter_id, ar.user_id,ar.assessment_id, ar.parameter_label_id ,ar.question_id ORDER BY ar.user_id ) as a GROUP BY  a.user_id, a.parameter_id asc";
					} else if ($report_type == 2) {
						$query .=" group by ar.parameter_id, ar.user_id,ar.question_id ORDER BY ar.user_id ) as a GROUP BY  a.user_id, a.question_id asc";
					}
					else
					{
						$query.= "group by ar.user_id, ar.assessment_id ) as a group by a.user_id, a.assessment_id ORDER BY a.user_id asc ";
					}
					
					$query .=" $dthaving ";
					$query_count = $query;
					//$query .= " $dtLimit ";
					
				}
				
				
				$result = $this->db->query($query);
        		return $result->result();	
			}
			
		}
	}
		/*if($assessment_id >0){
			$query = "SELECT is_weights,ratingstyle from assessment_mst where id=".$assessment_id;
			$result = $this->db->query($query);
			$Assessment_set = $result->row();
			if($report_type == 3 && $parameter_id==0 && $Assessment_set->is_weights==1){
				$query = "select ar.user_id,ar.assessment_id,CONCAT(ar.accuracy,'%') as result,ar.accuracy as hv_range,IF(am.ratingstyle=2,'--',FORMAT(ar.total_rating,0)) as total_rating,IF(am.ratingstyle=2,'--',FORMAT(ar.given_rating,0)) as rating,CONCAT(du.firstname,' ',du.lastname) AS traineename,rg.region_name AS trainee_region,dt.description AS designation,am.assessment,
					'' as title,du.emp_id FROM assessment_final_results	as ar
					LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
					LEFT JOIN device_users du ON du.user_id=ar.user_id
					LEFT JOIN region rg ON rg.id=du.region_id
					LEFT JOIN designation_trainee dt ON dt.id=du.designation_id";
					$query .= " $dtWhere  ";
					if (!$RightsFlag) {
						$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
					}
			}else if($report_type == 2 && $Assessment_set->is_weights==1){
				$query = "SELECT a.assessment,a.trainee_region,a.user_id,a.traineename,a.emp_id,a.designation,sum(a.result),sum(a.parameter_Wt),CONCAT(
				FORMAT(((sum(a.result)/sum(a.parameter_Wt))*100),2),'%') as result,((sum(a.result)/sum(a.parameter_Wt))*100 ) as hv_range,sum(a.total_rating) as total_rating,sum(a.rating) as rating,
				a.title FROM(

				SELECT ar.assessment_id,art.question_id, b.total_parameter,art.parameter_id,IF(am.ratingstyle=2, IFNULL(((SUM(art.percentage)/ count(pm.id))/100)*(aw.percentage/b.total_parameter),0), IFNULL((SUM(art.score)/SUM(pm.weight_value))*(aw.percentage/b.total_parameter),0)) AS result,(aw.percentage/b.total_parameter) as parameter_Wt,am.assessment,
				IF(am.ratingstyle=2,'--',SUM(pm.weight_value)) AS total_rating,IF(am.ratingstyle=2,'--',SUM(art.score)) AS rating,
				du.user_id,du.emp_id, CONCAT(du.firstname,' ',du.lastname) AS traineename,aq.question as title,rg.region_name AS trainee_region,
				dt.description AS designation
				FROM assessment_results ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id 
				LEFT JOIN assessment_results_trans art ON art.result_id=ar.id and art.assessment_id=ar.assessment_id and art.user_id=ar.user_id 
				LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
				LEFT JOIN assessment_trans_sparam as ats ON ats.assessment_id = art.assessment_id AND ats.question_id=art.question_id AND ats.parameter_id = art.parameter_id 
				LEFT JOIN parameter_label_mst as plm ON plm.id = ats.parameter_label_id AND plm.parameter_id = ats.parameter_id
				LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=art.assessment_id 
				AND acr.user_id=art.user_id AND acr.trainer_id=art.trainer_id 
				LEFT JOIN assessment_para_weights AS aw ON aw.assessment_id=art.assessment_id AND aw.parameter_id=art.parameter_id
				LEFT JOIN device_users du ON du.user_id=ar.user_id
				LEFT JOIN region rg ON rg.id=du.region_id
				LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
				LEFT JOIN assessment_question aq ON aq.id=art.question_id
				LEFT JOIN (
				  select ar.user_id,count(DISTINCT ar.question_id) as total_parameter,ar.parameter_id
				  FROM assessment_results_trans ar LEFT JOIN assessment_mst am ON am.id=ar.assessment_id 
					  LEFT JOIN device_users du ON du.user_id=ar.user_id
					LEFT JOIN region rg ON rg.id=du.region_id
					LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
					LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=ar.assessment_id
					LEFT JOIN assessment_question aq ON aq.id=ar.question_id
						$dtWhere 
						group by ar.user_id,ar.assessment_id,ar.parameter_id) as b ON b.user_id=ar.user_id AND b.parameter_id=art.parameter_id";
				$query .= " $dtWhere AND art.question_id !='' AND acr.trainer_id !='' ";
				if (!$RightsFlag) {
					$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
				}
				$query .=" group by ar.user_id,ar.assessment_id,art.question_id,art.parameter_id 
				  ) as a group by a.user_id,a.assessment_id,a.question_id ";
			}else{
				$query = "SELECT cm.company_name,du.user_id,du.emp_id, CONCAT(du.firstname,' ',du.lastname) AS traineename, 
                IF(am.ratingstyle=2, CONCAT(IFNULL(FORMAT(SUM(art.percentage)/ COUNT(pm.id),2),0),'%'), CONCAT(IFNULL(FORMAT(SUM(art.score)*100/ SUM(pm.weight_value),2),0),'%')) AS result,                
                rg.region_name AS trainee_region,IF(am.ratingstyle=2,(FORMAT(SUM(art.percentage)/ count(pm.id),2)),(FORMAT(SUM(art.score)*100/ SUM(pm.weight_value),2))) AS hv_range,IF(am.ratingstyle=2,'--',SUM(pm.weight_value)) AS total_rating,IF(am.ratingstyle=2,'--',SUM(art.score)) AS rating,dt.description AS designation, 
                am.assessment ";
				if ($report_type == 1) {
					$query .=" ,pm.description as old_title,IFNULL(plm.description,pm.description) as title ";
				} else if ($report_type == 2) {
					$query .=", aq.question as title ";
				} else {
					$query .=",'' as title ";
				}
				$query .= " FROM assessment_results ar
						LEFT JOIN assessment_mst am ON am.id=ar.assessment_id
						
						LEFT JOIN assessment_results_trans art ON art.result_id=ar.id and art.assessment_id=ar.assessment_id and art.user_id=ar.user_id
						LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id
						LEFT JOIN parameter_label_mst as plm ON plm.id = ats.parameter_label_id AND plm.parameter_id = ats.parameter_id
						LEFT JOIN device_users du ON du.user_id=ar.user_id
						LEFT JOIN region rg ON rg.id=du.region_id
						LEFT JOIN company cm ON cm.id=ar.company_id
						LEFT JOIN designation_trainee dt ON dt.id=du.designation_id 
						LEFT JOIN assessment_complete_rating acr ON acr.assessment_id=art.assessment_id AND acr.user_id=art.user_id AND acr.trainer_id=art.trainer_id";
				if ($report_type == 2) {
					$query .=" LEFT JOIN assessment_question aq ON aq.id=art.question_id ";
				}
				$query .= " $dtWhere AND art.question_id !='' AND acr.trainer_id !='' ";
				if (!$RightsFlag) {
					$query .= " AND (art.trainer_id = $login_id OR art.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
				}
				$query .=" group by ar.user_id,ar.assessment_id ";
				if ($report_type == 1) {
					$query .=", art.parameter_id ";
				} else if ($report_type == 2) {
					$query .=", art.question_id ";
				}
			}
			echo $query;
			$query .=" $dthaving order by user_id ";
			$query = $this->db->query($query);
			return $query->result();
		}
    }*/
	public function getUserTraineeList($company_id,$assessment_id=0) {
        $query = "select distinct art.user_id,concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename FROM assessment_results as art "
                . " INNER JOIN device_users as du ON du.user_id=art.user_id"
                . " Where du.company_id=". $company_id;
        if($assessment_id !=0){
            $query .= " AND art.assessment_id=".$assessment_id;
        }
        $query .=" order by traineename ";
        $result = $this->db->query($query);
        return $result->result();		
    }
    public function getParaTraineeList($company_id,$assessment_id=0,$designation_id=0,$parameter_id=0) {
        $query = "select distinct art.user_id,concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename FROM assessment_results_trans as art "
                . " INNER JOIN device_users as du ON du.user_id=art.user_id"
                . " Where du.company_id=". $company_id;
        if($assessment_id !=0){
            $query .= " AND art.assessment_id=".$assessment_id;
        }
        if($designation_id !=0){
            $query .= " AND du.designation_id =".$designation_id;
        }
        if($parameter_id !=0){
            $query .= " AND art.parameter_id=".$parameter_id;
        }
        $query .=" order by traineename ";
        $result = $this->db->query($query);
        return $result->result();
    }
       public function getUserDesignationList($company_id,$assessment_id=0) {
        $query = "select distinct dt.id,dt.description FROM assessment_results as ar "
                . " INNER JOIN device_users as du ON du.user_id=ar.user_id "
                . " LEFT JOIN designation_trainee dt ON dt.id=du.designation_id "
                . " Where ar.company_id=". $company_id;
        if($assessment_id !=0){
            $query .= " AND ar.assessment_id=".$assessment_id;
        }
        $query .=" order by dt.description ";
        $result = $this->db->query($query);
        return $result->result();
    }
       public function getUserRegionList($company_id,$assessment_id=0) {
        $query = "select distinct rg.id,rg.region_name FROM assessment_results as ar "
                ." INNER JOIN device_users du ON du.user_id=ar.user_id "
                ." LEFT JOIN region rg ON rg.id=du.region_id "
                . " Where du.company_id=". $company_id;
        if($assessment_id !=0){
            $query .= " AND ar.assessment_id=".$assessment_id;
        }
        $query .=" order by region_name ";
        $result = $this->db->query($query);
        return $result->result();
    }
	public function getParametersList($company_id,$assessment_id=0,$trainee_id=0) {
        $query = "SELECT pm.id,pm.description,IFNULL(plm.description,pm.description) AS parameter_label_name FROM assessment_results_trans art "
                . " LEFT JOIN parameter_mst pm ON pm.id=art.parameter_id 
					LEFT JOIN assessment_trans_sparam as ats ON ats.assessment_id = art.assessment_id 
					AND ats.question_id=art.question_id AND ats.parameter_id = art.parameter_id 
					LEFT JOIN parameter_label_mst as plm ON plm.id = ats.parameter_label_id AND plm.parameter_id = ats.parameter_id"
                . " WHERE pm.company_id=".$company_id;
        if($assessment_id !=0){
            $query .= " AND art.assessment_id=".$assessment_id;
        }
        if($trainee_id !=0){
            $query .= " AND art.user_id=".$trainee_id;
        }
        $query .= " group by pm.description ";

		// echo $query;exit;
        $result = $this->db->query($query);
        return $result->result();
		
		

    }
	
}