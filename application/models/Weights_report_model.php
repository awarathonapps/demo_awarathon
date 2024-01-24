<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Weights_report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getWkshopFeedRightsList($company_id, $workshoptype_id = "0") {
        $trainer_id = $this->mw_session['user_id'];
        $query = "select id as workshop_id,workshop_name FROM workshop Where status=1 AND company_id= $company_id ";
        if ($workshoptype_id != "0") {
            $query .= " AND workshop_type =" . $workshoptype_id;
        }
        $query .=" AND (id IN(select workshop_id FROM cmsusers_workshop_rights WHERE userid= $trainer_id )  "
                . " OR id IN(select distinct workshop_id FROM atom_feedback where company_id= $company_id )) order by workshop_name  ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getFeedbackData($company_id = '', $trainee_id = '', $workshop_id = '') {
        $query = " select distinct af.feedbackset_id ,af.company_id,f.title as feedback_name 
                          from atom_feedback af
                                left join feedback f
                                on af.feedbackset_id = f.id
                                where af.company_id = $company_id and af.user_id =$trainee_id ";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function getWorkshopFeedbackData($company_id = '', $workshop_id = '') {
        $query = " select distinct af.feedbackset_id ,af.company_id,f.title as feedback_name 
                        from atom_feedback af
                        left join feedback f
                        on af.feedbackset_id = f.id
                        where af.company_id = $company_id and af.workshop_id =$workshop_id ";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_TraineeData($company_id = '', $workshop_id = '') {
        $query = "select distinct af.user_id,concat(du.firstname,' ',du.lastname) as traineename 
		from atom_feedback af left join device_users du on du.user_id = af.user_id 
		LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
		where af.company_id = $company_id and  af.workshop_id = $workshop_id and wtu.tester_id IS NULL ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_WrkattendedParticipated($company_id = '', $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "select count(distinct af.workshop_id) as workshop_attended,
		 count(distinct af.user_id) as no_of_participated from atom_feedback af
                 LEFT JOIN workshop w ON w.id = af.workshop_id
                 LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
		 where af.company_id = $company_id and wtu.tester_id IS NULL ";
        if (!$WRightsFlag) {
            $query .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $result = $this->db->query($query);
        
        return $result->row();
    }

    public function get_avgScore($company_id = '', $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "select Format(avg(wavg_score),2) as avg_score from 
		(select res.workshop_id,Format(avg(res.score_avg),2) as wavg_score from
		(select af.company_id,af.workshop_id,af.feedback_id,
		@totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore,
		@max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
		@total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight,
		FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
                    from atom_feedback af 
                        INNER JOIN workshop w 
                            on w.id = af.workshop_id 
			INNER JOIN workshop_feedback_questions wfq 
			    ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id    		 				                  
                        INNER JOIN device_users du ON du.user_id = af.user_id 
                            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                                where af.company_id = $company_id AND wtu.tester_id IS NULL ";
        if (!$WRightsFlag) {
            $query .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .=" group by af.feedback_id,af.workshop_id,af.user_id
				) as res
				
		) final_res";
		//echo $query;
		//exit;
        $result = $this->db->query($query);
        return $result->row();
    }

    public function get_bestWorkshop($company_id = '', $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "  select res.workshop_id,Format(avg(res.score_avg),2) as wavg_score,IFNULL(w.workshop_name,'Not Found') as workshop_name from
                    (select af.company_id,af.workshop_id,af.feedback_id,af.user_id,
                    @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore,
                    @max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
                    @total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight,
                    FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
                        from atom_feedback af 
                            INNER JOIN workshop_feedback_questions wfq 
                                ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id    		 				                  
                            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                    where af.company_id = $company_id AND wtu.tester_id IS NULL 
                    group by af.feedback_id,af.workshop_id,af.user_id
                        ) as res
                        inner join workshop w on res.workshop_id = w.id ";
        if (!$WRightsFlag) {
            $query .= " AND res.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .=" group by res.workshop_id
				order by wavg_score DESC
				limit 0,1  ";
        $result = $this->db->query($query);
        $RowSet = $result->row();
        $BestWk = 'Not Found';
        if (count((array)$RowSet) > 0) {
            $BestWk = $RowSet->workshop_name;
        }
        return $BestWk;
    }

    public function get_worstWorkshop($company_id = '', $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "  select res.workshop_id,Format(avg(res.score_avg),2) as wavg_score,IFNULL(w.workshop_name,'Not Found') as workshop_name from
                    (select af.company_id,af.workshop_id,af.feedback_id,af.user_id,
                    @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore,
                    @max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
                    @total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight,
                    FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
                        from atom_feedback af 
                            INNER JOIN workshop_feedback_questions wfq 
                                ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id 
                                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                        where af.company_id = $company_id AND wtu.tester_id IS NULL 
                        group by af.feedback_id,af.workshop_id,af.user_id
                    ) as res
                    inner join workshop w
                        on res.workshop_id = w.id ";
        if (!$WRightsFlag) {
            $query .= " AND res.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .= " group by res.workshop_id order by wavg_score  limit 0,1 ";
        $result = $this->db->query($query);
        $RowSet = $result->row();
        $WorstWk = 'Not Found';
        if (count((array)$RowSet) > 0) {
            $WorstWk = $RowSet->workshop_name;
        }
        return $WorstWk;
    }

    public function get_topFiveWorkshop($company_id = '', $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "select res.workshop_id,avg(res.score_avg) as order_score,IFNULL(Format(avg(res.score_avg),2),0) as wavg_score,
				IFNULL(w.workshop_name,'Not Found') as workshop_name from
				(select af.company_id,af.workshop_id,af.feedback_id,af.user_id,
                 @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore,
				@max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
				@total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight,
                                FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
			            from atom_feedback af 
                                        INNER JOIN workshop_feedback_questions wfq 
			                    ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id 
			                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                                    where af.company_id = $company_id AND wtu.tester_id IS NULL 
                                    group by af.feedback_id,af.workshop_id,af.user_id
                                    ) as res
                                    inner join workshop w on res.workshop_id = w.id ";
        if (!$WRightsFlag) {
            $query .= " AND res.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .= "group by res.workshop_id order by order_score DESC limit 0,5  ";

        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_bottomFiveWorkshop($company_id = '', $top_five_wksh_id = '', $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = " select res.workshop_id,avg(res.score_avg) as order_score,IFNULL(Format(avg(res.score_avg),2),0) as wavg_score,IFNULL(w.workshop_name,'Not Found') as workshop_name from
				(select af.company_id,af.workshop_id,af.feedback_id,af.user_id,
                                @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore,
				@max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
				@total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight,
                                FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
			              from atom_feedback af 
			              		INNER JOIN workshop_feedback_questions wfq 
			                       ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id 
			                       LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                                    where af.company_id = $company_id AND wtu.tester_id IS NULL
                      group by af.feedback_id,af.workshop_id,af.user_id
				) as res
				inner join workshop w on res.workshop_id = w.id
                WHERE res.workshop_id NOT IN (" . $top_five_wksh_id . ")";
        if (!$WRightsFlag) {
            $query .= " AND res.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .=" group by res.workshop_id
				order by order_score 
				limit 0,5  ";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function feedbackWeightsIndexWeeklyMonthly($company_id = '', $wtype_id = '', $StartDate = '', $EndDate = '', $WRightsFlag) {
        $WtypeWhereClause = '';
        $login_id = $this->mw_session['user_id'];
        if ($wtype_id != "") {
            $WtypeWhereClause = " AND w.workshop_type='" . $wtype_id . "'";
        }
        $TodayDt = date('Y-m-d H:i:s');
        $query = "  select Format(avg(wavg_score),2) as avg_score,wday,wstart_date from 
                    (select res.workshop_id,Format(avg(res.score_avg),2) as wavg_score,res.wday,res.wstart_date from
                    (select af.company_id,af.workshop_id,af.feedback_id,cast(w.start_date as date) as wstart_date,
                    DATE_FORMAT(w.start_date,'%d') wday ,
			@totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore,
			@max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
			@total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight,
			FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
                        from atom_feedback af INNER JOIN workshop w on w.id = af.workshop_id
                        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                        INNER JOIN workshop_feedback_questions wfq ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id
                        where af.company_id = $company_id AND wtu.tester_id IS NULL and w.start_date BETWEEN '$StartDate' AND '$EndDate' $WtypeWhereClause ";
        if (!$WRightsFlag) {
            $query .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .=" group by af.feedback_id,af.workshop_id,af.user_id
				) as res
				group by workshop_id
                    ) final_res
                    group by wstart_date ";
        $result = $this->db->query($query);
        $AvgScore = $result->result();

        $ResultArray = array();
        if (count((array)$AvgScore) > 0) {
            foreach ($AvgScore as $value) {
                $ResultArray[$value->wday] = $value->avg_score;
            }
        }
        return $ResultArray;
    }

    public function feedbackWeightsIndexYearly($company_id = '', $wtype_id = '', $StartDate = '', $EndDate = '', $WRightsFlag) {
        $WtypeWhereClause = '';
        $login_id = $this->mw_session['user_id'];
        if ($wtype_id != "") {
            $WtypeWhereClause = " AND w.workshop_type='" . $wtype_id . "'";
        }
        $TodayDt = date('Y-m-d H:i:s');
        $query = "  select Format(avg(wavg_score),2) as avg_score,wday,wstart_date,month(wstart_date) as wmonth from 
                    (select res.workshop_id,Format(avg(res.score_avg),2) as wavg_score,res.wday,res.wstart_date from
                    (select af.company_id,af.workshop_id,af.feedback_id,cast(w.start_date as date) as wstart_date,
                    DATE_FORMAT(w.start_date,'%d') wday ,
                    @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore,
                    @max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
                    @total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight,
                    FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
                        from atom_feedback af 
                        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                        INNER JOIN workshop w on w.id = af.workshop_id
                        INNER JOIN workshop_feedback_questions wfq 
                        ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id
                        where af.company_id = $company_id AND wtu.tester_id IS NULL and w.start_date BETWEEN '$StartDate' AND '$EndDate' $WtypeWhereClause ";
        if (!$WRightsFlag) {
            $query .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .=" group by af.feedback_id,af.workshop_id,af.user_id
				) as res
				
		) final_res
		GROUP BY month(wstart_date) ";
        $result = $this->db->query($query);
        $AvgScore = $result->result();

        $ResultArray = array();
        if (count((array)$AvgScore) > 0) {
            foreach ($AvgScore as $value) {
                $ResultArray[$value->wmonth] = $value->avg_score;
            }
        }
        return $ResultArray;
    }

    public function feedbackWeightsHistogram_range($company_id, $WeekStartDate = '', $WeekEndDate = '', $wtype_id = '', $WRightsFlag) {
        $TodayDt = date('Y-m-d H:i:s');
        $WtypeWhereClause = '';
        $login_id = $this->mw_session['user_id'];
        if ($wtype_id != "") {
            $WtypeWhereClause = " AND w.workshop_type='" . $wtype_id . "'";
        }
        $query = " SELECT hr.from_range,hr.to_range,avg_score, IF(workshop_id != 0, COUNT(DISTINCT workshop_id), NULL) AS workshop_count
                    FROM histogram_range hr
                    LEFT JOIN (
                    SELECT FORMAT(IFNULL((avg(score_avg)),0),0) AS avg_score,workshop_id, CAST(wstart_date AS DATE) AS wstart_date, DATE_FORMAT(wstart_date,'%d') wday
                    FROM(
			select af.company_id,af.workshop_id,af.feedback_id,cast(w.start_date as date) as wstart_date,
                        @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore,
			@max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
			@total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight,
                        FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
			    from atom_feedback af 
                            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                            INNER JOIN workshop w on w.id = af.workshop_id INNER JOIN workshop_feedback_questions wfq 
			            ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id   
                            where af.company_id = $company_id AND wtu.tester_id IS NULL $WtypeWhereClause ";
        if ($WeekStartDate != '' && $WeekEndDate != '') {
            $query .= " and w.start_date between '$WeekStartDate' AND '$WeekEndDate' ";
        }
        if (!$WRightsFlag) {
            $query .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .= " group by af.feedback_id,af.workshop_id,af.user_id
				) AS x
                    GROUP BY workshop_id
            ) AS Y ON avg_score BETWEEN hr.from_range AND hr.to_range
            GROUP BY hr.from_range ";
        
//        $query = "  SELECT hr.from_range,hr.to_range,avg_score,if(workshop_id != 0, COUNT(DISTINCT workshop_id),null) as workshop_count
//                    from histogram_cerange hr
//                        LEFT JOIN 
//			(select IFNULL(FORMAT(AVG(total_weight),2),0) AS avg_score,workshop_id, 
//                            CAST(start_date AS DATE) AS wstart_date, DATE_FORMAT(start_date,'%d') wday
//                            FROM (
//                                    SELECT af.workshop_id,w.start_date, SUM(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) +
//                                        IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + 
//                                        IF(af.option_f = '1',af.weight_f,0)) AS total_weight
//                                            FROM atom_feedback af
//                                            LEFT JOIN workshop w ON w.id = af.workshop_id AND w.company_id = af.company_id
//                                            WHERE  af.company_id = $company_id $WtypeWhereClause ";
//                                                if ($WeekStartDate != '' && $WeekEndDate != '') {
//                                                        $query .= " and w.start_date between '$WeekStartDate' AND '$WeekEndDate' ";
//                                                    }
//                            $query .= " GROUP BY af.workshop_id
//                                    ) AS x 	GROUP BY workshop_id
//			) as y 
//			on avg_score between hr.from_range and hr.to_range
//                        group by hr.from_range ";                


        $result = $this->db->query($query);
        return $result->result();
    }

    public function getWeightWorkshopTableData($company_id = '', $dtLimit = '', $dtWhere = '', $WRightsFlag) {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT res.company_id,res.workshop_id,workshop_name, IFNULL(AVG(res.score_avg),0) AS order_score, 
		 IFNULL(FORMAT(AVG(res.score_avg),2),0) AS total_score, 		 
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
                                INNER JOIN workshop w 
                                    ON af.workshop_id = w.id 
                                $dtWhere AND wtu.tester_id IS NULL";

        if (!$WRightsFlag) {
            $query .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .= " GROUP BY af.feedback_id,af.workshop_id,af.user_id
				) AS res	
                            GROUP BY workshop_id
                            ORDER BY order_score DESC $dtLimit";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query1 = "select  COUNT(distinct af.workshop_id) AS total,IFNULL(w.workshop_name,'Not Found') as workshop_name,
                    IFNULL(SUM(if(af.option_a = '1',af.weight_a,0) + if(af.option_b = '1',af.weight_b,0) + if(af.option_c = '1',af.weight_c,0) +
                    if(af.option_d = '1',af.weight_d,0) + if(af.option_e = '1',af.weight_e,0) + if(af.option_f = '1',af.weight_f,0)),0) as total_weight,
                    count(distinct af.user_id)as no_of_trainee					
                    from atom_feedback af
                    LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                    inner join workshop w on af.workshop_id = w.id $dtWhere AND wtu.tester_id IS NULL ";

        if (!$WRightsFlag) {
            $query1 .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }

        $result1 = $this->db->query($query1);
        $data_array = $result1->result_array();
        if (count((array)$data_array) > 0) {
            $data['dtTotalRecords'] = $data_array[0]['total'];
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }

    public function getWeightIndTableData($company_id = '', $dtLimit = '', $dtWhere = '', $WRightsFlag) {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = " select company_id,workshop_id,user_id ,trainee_name,IFNULL(Format(avg(score_avg),2),0) as score_avg from
                        (SELECT af.company_id,af.workshop_id,af.user_id, CONCAT(du.firstname,' ',du.lastname) AS trainee_name, 
                            @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore, 
                            @max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
                            @total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight, 
                            FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
                                FROM atom_feedback af
                                        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
					INNER JOIN workshop w ON af.workshop_id = w.id
					INNER JOIN workshop_feedback_questions wfq 
						ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id
					INNER JOIN device_users du 
						ON du.user_id = af.user_id  $dtWhere AND wtu.tester_id IS NULL ";

        if (!$WRightsFlag) {
            $query .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .= " GROUP BY af.feedback_id,af.user_id ) res
				group by user_id $dtLimit";


        
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query1 = "select  count(distinct af.user_id) as total,concat(du.firstname,' ',du.lastname) as trainee_name,
                @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore, @max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, @total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight,
 		FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
                    from atom_feedback af 
                        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
		 	inner join workshop w
                            on af.workshop_id = w.id
                        INNER JOIN workshop_feedback_questions wfq 
                            ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id    
		  	inner join device_users du
                            on du.user_id = af.user_id  $dtWhere AND wtu.tester_id IS NULL ";

        if (!$WRightsFlag) {
            $query1 .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }

        $result1 = $this->db->query($query1);
        $data_array = $result1->result_array();
        if (count((array)$data_array) > 0) {
            $data['dtTotalRecords'] = $data_array[0]['total'];
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }

    public function getFeedbackQueData($dtOrder = '', $dtLimit = '', $dtWhere = '', $WRightsFlag) {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = " select *,IFNULL(FORMAT(AVG(score_avg),2),0) as score_avg from 
                    (SELECT af.workshop_id,af.feedback_id,af.company_id,ft.description as type_name,af.feedbackset_id,
                        af.feedback_type_id,af.feedback_subtype_id,fst.description as subtype_name,wfq.question_title, 
                        @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore, 
                        @max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
                        @total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight, 
                        FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
			FROM atom_feedback af
                            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                            INNER JOIN workshop_feedback_questions wfq 
                                ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND
                                wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id
                            INNER JOIN workshop w ON af.workshop_id = w.id
                            INNER JOIN feedback_type ft ON ft.id = af.feedback_type_id
                            INNER JOIN feedback_subtype fst ON fst.id = af.feedback_subtype_id 
                        $dtWhere AND wtu.tester_id IS NULL ";
//            $query = "  select af.workshop_id,af.feedbackset_id,af.feedback_type_id,af.feedback_subtype_id,
//                        af.user_id,ft.description as feedbacktype_name,
//                        fst.description as feedbacksubtype_name,wfq.question_id,wfq.question_title,multiple_allow,
//                        @totalscore := IFNULL(if(af.option_a = '1',af.weight_a,0) + if(af.option_b = '1',af.weight_b,0) + if(af.option_c = '1',af.weight_c,0) +
//                        if(af.option_d = '1',af.weight_d,0) + if(af.option_e = '1',af.weight_e,0) + if(af.option_f = '1',af.weight_f,0),0) as totalscore,
//                        @max_weight := greatest(ifnull(wfq.weight_a,0),ifnull(wfq.weight_b,0),ifnull(wfq.weight_c,0),ifnull(wfq.weight_d,0),ifnull(wfq.weight_e,0),ifnull(wfq.weight_f,0)) as max_weight,
//                        @total_weight := (ifnull(wfq.weight_a,0)+ifnull(wfq.weight_b,0)+ifnull(wfq.weight_c,0)+ifnull(wfq.weight_d,0)+ifnull(wfq.weight_e,0)+ifnull(wfq.weight_f,0)) as total_weight,
//                        FORMAT(if(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) as score_avg    
//               
//                            from atom_feedback af 
//                                inner join feedback_type ft
//                                        on ft.id = af.feedback_type_id
//                                inner join feedback_subtype fst
//                                        on fst.id = af.feedback_subtype_id
//                                inner join workshop_feedback_questions wfq
//                                        on wfq.question_id = af.feedback_id and wfq.type_id = af.feedback_type_id and wfq.subtype_id = af.feedback_subtype_id and wfq.workshop_id = af.workshop_id ";

        if (!$WRightsFlag) {
            $query .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .= " group by af.feedback_id,af.user_id ) as res group by feedback_id $dtLimit";


        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query1 = "select wfq.question_id ,af.workshop_id,af.feedbackset_id,af.feedback_type_id,af.feedback_subtype_id,
                        af.user_id,ft.description as feedbacktype_name,
                        fst.description as feedbacksubtype_name,wfq.question_title,multiple_allow,
                        @totalscore := IFNULL(if(af.option_a = '1',af.weight_a,0) + if(af.option_b = '1',af.weight_b,0) + if(af.option_c = '1',af.weight_c,0) +
                        if(af.option_d = '1',af.weight_d,0) + if(af.option_e = '1',af.weight_e,0) + if(af.option_f = '1',af.weight_f,0),0) as totalscore,
                        @max_weight := greatest(ifnull(wfq.weight_a,0),ifnull(wfq.weight_b,0),ifnull(wfq.weight_c,0),ifnull(wfq.weight_d,0),ifnull(wfq.weight_e,0),ifnull(wfq.weight_f,0)) as max_weight,
                        @total_weight := (ifnull(wfq.weight_a,0)+ifnull(wfq.weight_b,0)+ifnull(wfq.weight_c,0)+ifnull(wfq.weight_d,0)+ifnull(wfq.weight_e,0)+ifnull(wfq.weight_f,0)) as total_weight,
                        FORMAT(if(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) as score_avg    
                            from atom_feedback af 
                            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                            inner join feedback_type ft on ft.id = af.feedback_type_id
                            inner join feedback_subtype fst on fst.id = af.feedback_subtype_id
                            inner join workshop_feedback_questions wfq
                                        on wfq.question_id = af.feedback_id and wfq.type_id = af.feedback_type_id and wfq.subtype_id = af.feedback_subtype_id and wfq.workshop_id = af.workshop_id 
                $dtWhere AND wtu.tester_id IS NULL ";
        if (!$WRightsFlag) {
            $query1 .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query1 .= " group by af.feedback_id ";

        $result1 = $this->db->query($query1);
        $data_array = $result1->result();
        if (count((array)$data_array) > 0) {
            $data['dtTotalRecords'] = count((array)$data_array);
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }

    public function getComparisonFeedbcakQueData($company_id = '', $workshop_id = '', $wtype_id = '0', $trainee_id = '0', $WRightsFlag,$workshop_subtype='',$region_id='0',$subregion_id='',$cmptab_tregion_id='0') {
        $login_id = $this->mw_session['user_id'];
        $query = "  select *,IFNULL(FORMAT(AVG(score_avg),2),0) as score_avg from
                    (SELECT af.workshop_id,af.feedback_id,af.company_id,ft.description as type_name,fst.description as subtype_name,wfq.question_title, 
                        @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore, 
                        @max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
                        @total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight, 
                        FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
			FROM atom_feedback af
                        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
                            INNER JOIN workshop_feedback_questions wfq 
                                ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id
                            INNER JOIN workshop w 
                                ON af.workshop_id = w.id
                            INNER JOIN feedback_type ft
                                ON ft.id = af.feedback_type_id
                            INNER JOIN feedback_subtype fst
                                ON fst.id = af.feedback_subtype_id
                            INNER JOIN device_users du
                                ON du.user_id = af.user_id    
                            WHERE wtu.tester_id IS NULL ";

        if ($company_id != '') {
            $query .= " AND af.company_id = $company_id";
        }
        if ($workshop_id != '') {
            $query .= " and af.workshop_id = $workshop_id";
        }
        if ($wtype_id != '0') {
            $query .= " and w.workshop_type = $wtype_id";
        }
        if ($trainee_id != '0') {
            $query .= " and af.user_id = $trainee_id";
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
            $query .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .= " GROUP BY af.feedback_id ,af.user_id ) as res group by feedback_id ";
        
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getComparisonFeedbcakOverallAvg($company_id = '', $workshop_id = '', $wtype_id = '0', $trainee_id = '0', $WRightsFlag,$workshop_subtype='',$region_id='0',$subregion_id='',$cmptab_tregion_id='0') {
        $login_id = $this->mw_session['user_id'];
        $query = "  Select IFNULL(FORMAT(AVG(score_avg),2),0) AS overall_score_avg from 
                        ( SELECT af.workshop_id,af.company_id,af.user_id, 
                        @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore, 
                        @max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
                        @total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight, 
                        FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
			FROM atom_feedback af
                        LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id
				INNER JOIN workshop_feedback_questions wfq 
                                    ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id
				INNER JOIN workshop w 
                                    ON af.workshop_id = w.id
                                INNER JOIN feedback_type ft
                                    ON ft.id = af.feedback_type_id
                                INNER JOIN feedback_subtype fst
                                    ON fst.id = af.feedback_subtype_id
                                INNER JOIN device_users du
                                    ON du.user_id = af.user_id    
                                WHERE wtu.tester_id IS NULL ";

        if ($company_id != '') {
            $query .= " AND af.company_id = $company_id";
        }
        if ($workshop_id != '') {
            $query .= " and af.workshop_id = $workshop_id";
        }
        if ($wtype_id != '0') {
            $query .= " and w.workshop_type = $wtype_id";
        }
        if ($trainee_id != '0') {
            $query .= " and af.user_id = $trainee_id";
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
            $query .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .= " GROUP BY af.feedback_id,af.workshop_id,user_id) as res";

        $result = $this->db->query($query);
        return $result->row();
    }

    public function getWorkshopFeedbackOverallScore($company_id = '', $workshop_id = '', $feedbackset_id = '', $trainee_id = '', $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "  Select company_id,workshop_id,workshop_name,
                    IFNULL(FORMAT(AVG(order_score),2),0) as order_score,IFNULL(FORMAT(AVG(total_score),2),0) as total_score,no_of_trainee from(
                    SELECT res.company_id,res.workshop_id,workshop_name, AVG(res.score_avg) AS order_score, 
                    FORMAT(AVG(res.score_avg),2) AS total_score, COUNT(DISTINCT user_id) AS no_of_trainee
			FROM (
                            SELECT af.company_id,af.workshop_id,af.feedback_id,af.user_id, IFNULL(w.workshop_name,'Not Found') AS workshop_name, 
                            @totalscore := IFNULL(IF(af.option_a = '1',af.weight_a,0) + IF(af.option_b = '1',af.weight_b,0) + IF(af.option_c = '1',af.weight_c,0) + IF(af.option_d = '1',af.weight_d,0) + IF(af.option_e = '1',af.weight_e,0) + IF(af.option_f = '1',af.weight_f,0),0) AS totalscore, 
                            @max_weight := GREATEST(IFNULL(wfq.weight_a,0), IFNULL(wfq.weight_b,0), IFNULL(wfq.weight_c,0), IFNULL(wfq.weight_d,0), IFNULL(wfq.weight_e,0), IFNULL(wfq.weight_f,0)) AS max_weight, 
                            @total_weight := (IFNULL(wfq.weight_a,0)+ IFNULL(wfq.weight_b,0)+ IFNULL(wfq.weight_c,0)+ IFNULL(wfq.weight_d,0)+ IFNULL(wfq.weight_e,0)+ IFNULL(wfq.weight_f,0)) AS total_weight, 
                            FORMAT(IF(wfq.multiple_allow = 0,@totalscore * 100/@max_weight,@totalscore * 100/@total_weight),2) AS score_avg
                                FROM atom_feedback af
                            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=af.workshop_id AND wtu.tester_id=af.user_id    
                            INNER JOIN workshop_feedback_questions wfq 
                                ON wfq.question_id = af.feedback_id AND wfq.type_id = af.feedback_type_id AND wfq.subtype_id = af.feedback_subtype_id AND wfq.workshop_id = af.workshop_id
                            INNER JOIN workshop w 
                                ON af.workshop_id = w.id 
                                WHERE wtu.tester_id IS NULL";
        if ($company_id != '') {
            $query .= " AND af.company_id = $company_id";
        }
        if ($workshop_id != '') {
            $query .= " AND af.workshop_id = $workshop_id";
        }
        if ($feedbackset_id != '') {
            $query .= " and af.feedbackset_id = $feedbackset_id";
        }
        if ($trainee_id != '') {
            $query .= " and af.user_id = $trainee_id";
        }
        if (!$WRightsFlag) {
            $query .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query .= " GROUP BY af.feedback_id,af.workshop_id,af.user_id
                                ) AS res
                            GROUP BY workshop_id,user_id 
                            ) as fin ";

        $result = $this->db->query($query);
        return $result->row();
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
    public function getFeedbackWorkshop($company_id,$WRightsFlag=1,$region_id="0",$workshoptype_id="0",$workshopsubtype_id="",$subregion_id=""){
        $lcSqlStr ="select distinct a.workshop_id ,b.workshop_name FROM workshop_feedback_questions as a "
                . "LEFT JOIN workshop as b ON b.company_id=a.company_id AND b.id=a.workshop_id where a.company_id=".$company_id;        
        if($region_id !="0"){
            $lcSqlStr .= " AND b.region =".$region_id;
        }
        if($workshoptype_id !="0"){
            $lcSqlStr .= " AND b.workshop_type =".$workshoptype_id;
        }
        if($workshopsubtype_id !=""){
            $lcSqlStr .= " AND b.workshopsubtype_id =".$workshopsubtype_id;
        }
        if($subregion_id !=""){
            $lcSqlStr .= " AND b.workshopsubregion_id =".$subregion_id;
        }
        if(!$WRightsFlag){
            $login_id = $this->mw_session['user_id'];
            $lcSqlStr .=" AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights WHERE user_id= $login_id )";
        }
        $lcSqlStr .=" order by workshop_name ";
        
        $result = $this->db->query($lcSqlStr);
        return $result->result();
    }
}
