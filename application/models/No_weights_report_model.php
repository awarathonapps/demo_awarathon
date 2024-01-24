<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class No_weights_report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    public function get_TraineeData($company_id = '', $workshop_id = '') {
        $query = "select distinct af.user_id,concat(du.firstname,' ',du.lastname, '(',du.email,' )') as traineename 
		 from atom_feedback af
		 LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
		 on du.user_id = af.user_id 
		 where af.company_id = $company_id AND wtu.tester_id IS NULL ";
        if ($workshop_id != '') {
            $query .= " and  af.workshop_id = $workshop_id ";
        }

        $result = $this->db->query($query);
        return $result->result();
    }

    public function getFeedbackData($company_id = '', $workshop_id = '', $trainee_id = '') {
        $query = " select distinct af.feedbackset_id ,af.company_id,f.title as feedback_name 
                    from atom_feedback af left join feedback f on af.feedbackset_id = f.id
                    where af.company_id = $company_id and af.workshop_id = $workshop_id and af.user_id =$trainee_id ";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function getWorkshopFeedbackData($company_id = '', $workshop_id = '') {
        $query = " select distinct af.feedbackset_id ,af.company_id,f.title as feedback_name,af.workshop_id 
                from atom_feedback af left join feedback f on af.feedbackset_id = f.id
            where af.company_id = $company_id and af.workshop_id =$workshop_id ";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function getNoWeightWorkshopTableData($company_id = '', $dtLimit = '', $dtWhere = '') {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "SELECT res.company_id,res.workshop_id,workshop_name, AVG(res.score_avg) AS order_score, 
		 FORMAT(AVG(res.score_avg),2) AS total_score, 		 
		 COUNT(DISTINCT user_id) AS no_of_trainee
		 FROM (
                        SELECT af.company_id,af.workshop_id,af.feedback_id,af.user_id,IFNULL(w.workshop_name,'Not Found') AS workshop_name,  
                            @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore, 
                            @max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
                            @total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight, 
                            FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
                                FROM atom_feedback af
                                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                                INNER JOIN workshop_feedback_questions wfq 
                                    ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id
                                INNER JOIN workshop w ON af.workshop_id = w.id  ";
//                                if($dtWhere !=''){
//                                    $query .=" $dtWhere AND af.company_id=".$company_id; 
//                                }else{
//                                   $query .=" where af.company_id=".$company_id;
//                                } 

        $query .= " $dtWhere AND wtu.tester_id IS NULL GROUP BY af.feedback_id,af.workshop_id,af.user_id
				) AS res GROUP BY workshop_id ORDER BY order_score DESC $dtLimit";

//      echo $query;exit;
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query1 = "select  COUNT(distinct af.workshop_id) AS total,IFNULL(w.workshop_name,'Not Found') as workshop_name,
                    IFNULL(SUM(if(af.option_a = '1',af.weight_a,0) + if(af.option_b = '1',af.weight_b,0) + if(af.option_c = '1',af.weight_c,0) +
                    if(af.option_d = '1',af.weight_d,0) + if(af.option_e = '1',af.weight_e,0) + if(af.option_f = '1',af.weight_f,0)),0) as total_weight,
                    count(distinct af.user_id)as no_of_trainee					
                    from atom_feedback af 
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                    INNER join workshop w on af.workshop_id = w.id $dtWhere AND wtu.tester_id IS NULL";
//                    if($dtWhere !=''){
//                       $query1 .=" $dtWhere AND af.company_id=".$company_id; 
//                    }else{
//                       $query1 .=" where af.company_id=".$company_id;
//                    }

        $result1 = $this->db->query($query1);
        $data_array = $result1->result_array();
        if (count((array)$data_array) > 0) {
            $data['dtTotalRecords'] = $data_array[0]['total'];
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }

    public function getNoWeightIndTraineeData($company_id = '', $dtLimit = '', $dtWhere = '') {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "  SELECT distinct af.user_id,af.company_id,af.workshop_id,
                    count(distinct af.feedback_id) as no_of_question_atmpt,
                    concat(du.firstname,' ',du.lastname) as trainee_name
                        FROM atom_feedback af                                
                        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                        INNER JOIN workshop w  ON af.workshop_id = w.id
                        INNER JOIN device_users du ON du.user_id = af.user_id  ";
//                            if($dtWhere !=''){
//                                    $query .=" $dtWhere AND af.company_id=".$company_id; 
//                                }else{
//                                    $query .=" where af.company_id=".$company_id;
//                                }      
        $query .=" $dtWhere AND wtu.tester_id IS NULL group by af.user_id $dtLimit";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query1 = " select count(user_id) as total
                    from 
                    (SELECT distinct af.user_id,af.company_id,af.workshop_id,
                    count(distinct af.feedback_id) as no_of_question_atmpt,
                    concat(du.firstname,' ',du.lastname) as trainee_name
                        FROM atom_feedback af 
                        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                        INNER JOIN workshop w ON af.workshop_id = w.id
                        INNER JOIN device_users du ON du.user_id = af.user_id $dtWhere AND wtu.tester_id IS NULL";
        
        $query1 .=" group by af.user_id ) res";

        $result1 = $this->db->query($query1);
        $data_array = $result1->result_array();
        if (count((array)$data_array) > 0) {
            $data['dtTotalRecords'] = $data_array[0]['total'];
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }

    public function getFeedbackQueAns($dtOrder = '', $dtLimit = '', $dtWhere = '') {
        $TodayDt = date('Y-m-d H:i:s');
        $query = " select af.workshop_id,af.company_id,af.user_id,af.feedback_id,wfq.question_title,
		af.option_a,af.option_b,af.option_c,af.option_d,af.option_e,af.option_f,
		ifnull(wfq.option_a,' ') as option_a_title,ifnull(wfq.option_b,' ') as option_b_title,
		ifnull(wfq.option_c,' ') as option_c_title,ifnull(wfq.option_d,' ') as option_d_title,
		ifnull(wfq.option_e,' ') as option_e_title,ifnull(wfq.option_f,' ') as option_f_title
		from atom_feedback af
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                        INNER join workshop w ON w.id = af.workshop_id
			INNER JOIN workshop_feedback_questions wfq 
				ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id ";
        if ($dtWhere != '') {
            $query .=" $dtWhere AND wtu.tester_id IS NULL";
        }

        $query .= "  $dtLimit";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query1 = "select count(af.feedback_id) as total  from atom_feedback af
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                    INNER join workshop w ON w.id = af.workshop_id
                    INNER JOIN workshop_feedback_questions wfq 
            ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id ";
        if ($dtWhere != '') {
            $query1 .=" $dtWhere AND wtu.tester_id IS NULL";
        }
        
        $result1 = $this->db->query($query1);
        $data_array = $result1->row();
        if (count((array)$data_array) > 0) {
            $data['dtTotalRecords'] = $data_array->total;
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }

    public function getComparisonFeedbcakQueData($company_id = '', $workshop_id = '', $wtype_id = '0', $trainee_id = '', $feedbackset_id = '', $WRightsFlag, $login_id,$workshop_subtype='',$region_id='0',$subregion_id='',$cmptab_tregion_id='0') {
        $dtWhere = "";
        $query = "select af.workshop_id,af.company_id,af.user_id,af.feedback_id,wfq.question_title,
                    af.option_a,af.option_b,af.option_c,af.option_d,af.option_e,af.option_f,
                    ifnull(wfq.option_a,' ') as option_a_title,ifnull(wfq.option_b,' ') as option_b_title,
                    ifnull(wfq.option_c,' ') as option_c_title,ifnull(wfq.option_d,' ') as option_d_title,
                    ifnull(wfq.option_e,' ') as option_e_title,ifnull(wfq.option_f,' ') as option_f_title
                    from atom_feedback af
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                    INNER JOIN workshop_feedback_questions wfq                     
                        ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id 
                    INNER join workshop w
                        ON w.id = af.workshop_id
                    INNER JOIN device_users du
                        ON du.user_id = af.user_id
                    WHERE  wtu.tester_id IS NULL";
        if ($company_id != '') {
            $dtWhere .= " AND af.company_id = $company_id";
        }
        if ($workshop_id != '') {
            $dtWhere .= " and af.workshop_id = $workshop_id";
        }
        if ($wtype_id != '0') {
            $dtWhere .= " and w.workshop_type = $wtype_id";
        }
        if ($trainee_id != '') {
            $dtWhere .= " and af.user_id = $trainee_id";
        }
        if ($feedbackset_id != '') {
            $dtWhere .= " and af.feedbackset_id = $feedbackset_id";
        }
        if ($workshop_subtype != '') {
            $query .= " and w.workshopsubtype_id = $workshop_subtype";
        }
        if ($region_id != '0') {
            $query .= " and w.region = $region_id";
        }
        if ($subregion_id != '') {
            $query .= " and w.workshopsubregion_id = $subregion_id";
        }
        if ($cmptab_tregion_id != '0') {
            $query .= " and du.region_id = $cmptab_tregion_id";
        }
        if (!$WRightsFlag) {
            $trainer_id =$this->mw_session['user_id'];
            $dtWhere .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights WHERE user_id= $trainer_id ) ";
        }
        $query .= " $dtWhere GROUP BY af.feedback_id ";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function getWorkshopDataChartValue($company_id = '', $workshop_id = '', $feedbackset_id = '', $WRightsFlag, $login_id) {
        $dtWhere = "";
        $query = " select total_users ,option_a_selected,option_b_selected,option_c_selected,option_d_selected,
                option_e_selected,option_f_selected,feedback_id,multiple_allow,
		Format(if(multiple_allow = 0,(option_a_selected * 100 / total_users),(option_a_selected * 100 / @totaloptresponce)),2) as opt_a_selected,
		Format(if(multiple_allow = 0,(option_b_selected * 100 / total_users),(option_b_selected * 100 / @totaloptresponce)),2) as opt_b_selected,
		Format(if(multiple_allow = 0,(option_c_selected * 100 / total_users),(option_c_selected * 100 / @totaloptresponce)),2) as opt_c_selected,
		Format(if(multiple_allow = 0,(option_d_selected * 100 / total_users),(option_d_selected * 100 / @totaloptresponce)),2) as opt_d_selected,
		Format(if(multiple_allow = 0,(option_e_selected * 100 / total_users),(option_e_selected * 100 / @totaloptresponce)),2) as opt_e_selected,
		Format(if(multiple_allow = 0,(option_f_selected * 100 / total_users),(option_f_selected * 100 / @totaloptresponce)),2) as opt_f_selected,
                ifnull(option_a,'') as option_a,ifnull(option_b,'') as option_b,ifnull(option_c,'') as option_c,ifnull(option_d,'') as option_d,
		ifnull(option_e,'') as option_e,ifnull(option_f,'') as option_f
                from (select feedback_id,option_a_selected,option_b_selected,option_c_selected,option_d_selected,option_e_selected,option_f_selected,
                       total_users,multiple_allow,user_id,
                       @totaloptresponce := option_a_selected + option_b_selected + option_c_selected + option_d_selected + option_e_selected + option_f_selected as totalresponce,
                       option_a,option_b,option_c,option_d,option_e,option_f
                        from (
                                select af.user_id,wfq.multiple_allow,af.feedback_id,
                                count(af.user_id) as total_users,
                                sum(af.option_a) as option_a_selected,
                                sum(af.option_b) as option_b_selected,
                                sum(af.option_c) as option_c_selected,
                                sum(af.option_d) as option_d_selected,
                                sum(af.option_e) as option_e_selected,
                                sum(af.option_f) as option_f_selected,
                                wfq.option_a,wfq.option_b,wfq.option_c,wfq.option_d,wfq.option_e,wfq.option_f
                                    from atom_feedback af
                                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                                        INNER join workshop w
                                            ON w.id = af.workshop_id
                                        inner join workshop_feedback_questions wfq
                                            on wfq.company_id = af.company_id and af.workshop_id = wfq.workshop_id and wfq.feedbackset_id = af.feedbackset_id and af.feedback_id = wfq.question_id 
                                        INNER JOIN device_users du 
                                            ON du.user_id = af.user_id WHERE wtu.tester_id IS NULL ";
                                        if ($company_id != '') {
                                            $dtWhere .= " AND af.company_id = $company_id";
                                        }
                                        if ($workshop_id != '') {
                                            $dtWhere .= " and af.workshop_id = $workshop_id";
                                        }
                                        if ($feedbackset_id != '') {
                                            $dtWhere .= " and af.feedbackset_id = $feedbackset_id";
                                        }
                                        if (!$WRightsFlag) {
                                            $dtWhere .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights WHERE user_id= $login_id ) ";
                                        }
                                        $query .= " $dtWhere group by af.feedback_id 
                                            ) as res						
                                        group by res.feedback_id	
                                        ) final ";

        //echo $query;exit;
        $result = $this->db->query($query);
        return $result->result();
    }
    
    public function getTraineeOptionChartValue($company_id = '', $workshop_id = '', $feedbackset_id = '', $trainee_id = '', $WRightsFlag, $login_id) {
        $dtWhere = "";
        $query = " SELECT feedback_id,multiple_allow, 
		 FORMAT((option_a_selected * 100 / total_question),2) AS opt_a_selected, 
		 FORMAT((option_b_selected * 100 / total_question),2) AS opt_b_selected, 
		 FORMAT((option_c_selected * 100 / total_question),2) AS opt_c_selected, 
		 FORMAT((option_d_selected * 100 / total_question),2) AS opt_d_selected, 
		 FORMAT((option_e_selected * 100 / total_question),2) AS opt_e_selected, 
		 FORMAT((option_f_selected * 100 / total_question),2) AS opt_f_selected
                    FROM (
			SELECT feedback_id,option_a_selected,option_b_selected,option_c_selected,
                            option_d_selected,option_e_selected,option_f_selected,(2*total_question) total_question,
				multiple_allow
				FROM (
                                    SELECT af.user_id,wfq.multiple_allow,af.feedback_id, 
                                        (SELECT COUNT(DISTINCT feedback_id) FROM atom_feedback WHERE workshop_id = $workshop_id AND feedbackset_id = $feedbackset_id AND company_id = $company_id AND user_id = $trainee_id) AS total_question, 
                                        SUM(af.option_a) AS option_a_selected, 
                                        SUM(af.option_b) AS option_b_selected, 
                                        SUM(af.option_c) AS option_c_selected, 
                                        SUM(af.option_d) AS option_d_selected, 
                                        SUM(af.option_e) AS option_e_selected, 
                                        SUM(af.option_f) AS option_f_selected
                                            FROM atom_feedback af
                                            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                                                INNER join workshop w ON w.id = af.workshop_id
                                                INNER JOIN workshop_feedback_questions wfq 
                                                ON wfq.company_id = af.company_id AND af.workshop_id = wfq.workshop_id AND wfq.feedbackset_id = af.feedbackset_id AND af.feedback_id = wfq.question_id ";
        $dtWhere .= " WHERE af.company_id = $company_id AND wtu.tester_id IS NULL AND af.workshop_id = $workshop_id AND af.feedbackset_id = $feedbackset_id AND af.user_id = $trainee_id ";

        if (!$WRightsFlag) {
            $dtWhere .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights WHERE user_id= $login_id ) ";
        }
        $query .= " $dtWhere ) AS res
			) final	";
        
        $result = $this->db->query($query);
        return $result->result();
    }
    public function get_TraineeRegionData($company_id=''){
        $lcSqlStr = "select du.region_id,r.region_name,r.id FROM device_users du "
                . " LEFT JOIN region as r "
                . " ON du.region_id = r.id where 1=1";
        if ($company_id != "") {
            $lcSqlStr .=" AND du.company_id=" . $company_id;
        }        
        $lcSqlStr .=" group by r.id ";
        
        $result = $this->db->query($lcSqlStr);
        return $result->result();
    }
}
