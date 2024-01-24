<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
//class Ai_cronjob_model extends CI_Model {
    class Report_status_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function get_participants_distinct($assessment_id1)
    {        
        $query="SELECT c.emp_id, c.user_id, CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, c.registration_date,  rg.region_name, c.area, c.department, am.assessment, amu.trainer_id,
        b.is_completed as completed FROM `assessment_allow_users` as a 
        left join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id LEFT JOIN device_users as c on c.user_id=a.user_id 
        LEFT JOIN region as rg on rg.id= c.region_id  
        LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id
        left join assessment_mapping_user as amu on amu.user_id= a.user_id
        where a.assessment_id='".$assessment_id1."'
         order by user_name";
        /*
        
        $query="SELECT DISTINCT
        c.id,ar.assessment_id, am.assessment, ar.user_id, 
        CONCAT( du.firstname, ' ', du.lastname ) AS user_name, du.registration_date, du.email, rg.region_name, du.area,
        du.status, du.department
    FROM
        ai_subparameter_score AS ar
        LEFT JOIN company AS c ON ar.company_id = c.id
        LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id 
        LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
        LEFT JOIN region as rg on rg.id= du.region_id  
    WHERE
    ar.company_id = '".$company_id."' GROUP by ar.user_id order by ar.user_id";*/
        $result = $this->db->query($query);
        return $result->result();
    
    }
    public function status_check($company_id, $status_id, $dtwhere, $dtwhere1)
    {
     
          $query="SELECT c.emp_id as emp_id, c.user_id,CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, date_format(c.joining_date,'%d-%m-%Y') as joining_date,  rg.region_name, c.area, c.department, am.assessment,
            if(b.is_completed,'Completed ','Incomplete') as status_u FROM `assessment_allow_users` as a 
            LEFT join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id 
            LEFT JOIN device_users as c on c.user_id=a.user_id 
            LEFT JOIN region as rg on rg.id= c.region_id  
            LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id
            where  1=1 $dtwhere $dtwhere1 order by status_u, user_name
            ";
            
            $result = $this->db->query($query);
            return $result->result();
     
    }
    public function status_check_manager($dtwhere, $ismapped, $dtwhere1)
    {
			 $query="SELECT c.emp_id as emp_id, c.user_id,CONCAT(c.firstname,' ',c.lastname) as user_name, c.email, a.assessment_id as assessment_id, date_format(c.joining_date,'%d-%m-%Y') as joining_date,  			rg.region_name, c.area, c.department, am.assessment,
			   if(b.is_completed,'Completed ','Incomplete') as status1, CONCAT(cu.first_name,' ',cu.last_name) as trainer_name,
               if(cr.id is not null ,'Completed ','Incomplete') as trainer_status
				 FROM `assessment_allow_users` as a 
				LEFT join assessment_attempts as b on b.user_id=a.user_id and b.assessment_id=a.assessment_id 
				LEFT JOIN device_users as c on c.user_id=a.user_id 
				LEFT JOIN region as rg on rg.id= c.region_id  
            LEFT JOIN assessment_mst AS am ON a.assessment_id = am.id ";
			if($ismapped){
				$query .= "LEFT JOIN assessment_mapping_user as amu ON amu.assessment_id=a.assessment_id AND amu.user_id=a.user_id";
			}else{
				$query .= "LEFT JOIN assessment_managers as amu ON amu.assessment_id=a.assessment_id ";
			}
			$query .= " LEFT JOIN company_users as cu ON cu.userid=amu.trainer_id ";
            $query .= " LEFT JOIN assessment_complete_rating as cr On cr.assessment_id=a.assessment_id 
            and cr.trainer_id=amu.trainer_id AND cr.user_id=a.user_id where 1=1 $dtwhere $dtwhere1 order by trainer_status, user_name";
            $query;
            $result = $this->db->query($query);
            return $result->result();
           
        
    }
    public function get_ai_score($company_id, $assessment_id, $user_id)
    {
          $query  = "SELECT q.overall_score as overall_score FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  
            (SELECT CASE WHEN ROUND(main.overall_score,2) = previous THEN @cnt := @cnt
		    ELSE @cnt := @cnt + 1 END ) as final_rank,
		    @dcp := ROUND(main.overall_score,2) AS current
            FROM (
            SELECT ps.user_id,ROUND(sum(ps.score)/count(ps.question_id),2) AS overall_score 
            FROM ai_subparameter_score AS ps 
            WHERE ps.parameter_type='parameter' AND  ps.assessment_id = '".$assessment_id."'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0) AS qcounter) as q
            WHERE q.user_id='".$user_id."'";
        /*
        $query  = "SELECT * FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  @lastrank as last_rank,
            (SELECT 
            CASE 
            WHEN main.overall_score > previous THEN @cnt := @cnt + 1
            WHEN main.overall_score < previous THEN @cnt := @cnt + 1
            WHEN main.overall_score = previous THEN @cnt := @cnt
            ELSE @cnt := 1 END ) as rank,
            ( SELECT 
            CASE 
            WHEN @lastrank=0 THEN @lastrank := @cnt
            WHEN main.overall_score = @dcp AND @lastrank = @cnt THEN @lastrank := @lastrank
            WHEN main.overall_score < @dcp AND @lastrank != @cnt THEN @lastrank := @lastrank + 2
            WHEN main.overall_score < @dcp AND @lastrank = @cnt THEN @lastrank := @lastrank + 1
            END) as final_rank,
            @dcp := main.overall_score AS current
            FROM (
            SELECT ps.user_id,FORMAT((sum(ps.score)/count(DISTINCT ps.parameter_id)/count(DISTINCT ps.question_id)),2) AS overall_score 
            FROM ai_subparameter_score AS ps 
            WHERE ps.parameter_type='parameter' AND ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0, @lastrank := 0 ) AS qcounter) as q
            WHERE q.user_id='".$user_id."'";*/
        /*$query="SELECT sum(ps.score)/count(*) as ai_score FROM ai_subparameter_score as ps 
        WHERE ps.parameter_type ='parameter' AND
        ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."' AND ps.user_id = '".$user_id."'
        GROUP BY ps.user_id;";*/
        
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_all_parameters($company_id){
       $query="SELECT DISTINCT IF (pl.id=0, ps.description, pl.description) as parameter_name, ps.id as parameter_id, pl.id as parameter_label_id FROM parameter_mst as ps 
        
       left join parameter_label_mst as pl on pl.parameter_id= ps.id
      
     
       ORDER BY  ps.id, pl.id;";
       /* 
        $query="SELECT DISTINCT IF (parameter_label_id=0, p.description, pl.description) as parameter_name, ps.parameter_id, ps.parameter_label_id FROM ai_subparameter_score as ps 
        left join parameter_mst as p on ps.parameter_id = p.id
        left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
        WHERE ps.parameter_type ='parameter' 
        ORDER BY ps.parameter_id,ps.parameter_label_id";
        /*
        $query  = "SELECT DISTINCT ps.parameter_id,ps.parameter_label_id,p.description as parameter_name,pl.description as parameter_label_name FROM ai_subparameter_score as ps 
        left join parameter_mst as p on ps.parameter_id = p.id
        left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
        WHERE ps.parameter_type ='parameter' AND
        ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."'
        ORDER BY ps.parameter_id,ps.parameter_label_id";*/
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_parameters($company_id,$assessment_id){
        /*$query="SELECT DISTINCT IF (pl.id=0, ps.description, pl.description) as parameter_name, ps.id as parameter_id, pl.id as parameter_label_id FROM parameter_mst as ps 
        
        left join parameter_label_mst as pl on pl.parameter_id= ps.id
       

        ORDER BY  ps.id, pl.id";
        */
        $query="SELECT DISTINCT IF (pl.id=0, ps.description, pl.description) as parameter_name, ps.id as parameter_id, pl.id as parameter_label_id FROM parameter_mst as ps 
        
        left join parameter_label_mst as pl on pl.parameter_id= ps.id
        WHERE pl.id IN (SELECT parameter_label_id FROM `assessment_trans_sparam` where assessment_id='".$assessment_id."')
        ORDER BY  ps.id, pl.id;";
        /*
        $query  = "SELECT DISTINCT ps.parameter_id,ps.parameter_label_id,p.description as parameter_name,pl.description as parameter_label_name FROM ai_subparameter_score as ps 
        left join parameter_mst as p on ps.parameter_id = p.id
        left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
        WHERE ps.parameter_type ='parameter' AND
        ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."'
        ORDER BY ps.parameter_id,ps.parameter_label_id";*/
        $result = $this->db->query($query);
        return $result->result();
        
    }
    public function get_parameters_your_score($company_id,$assessment_id,$user_id,$parameter_id,$parameter_label_id){
        $query  = "SELECT round(sum(ps.score)/count(*),2) as score FROM ai_subparameter_score as ps 
        left join parameter_mst as p on ps.parameter_id = p.id
        left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
        WHERE ps.parameter_type ='parameter' AND
        ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."' AND ps.parameter_id ='".$parameter_id."' AND ps.parameter_label_id ='".$parameter_label_id."'
        ORDER BY ps.parameter_id,ps.parameter_label_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_parameters_your_score_old($company_id,$assessment_id,$user_id){
        $query  = "SELECT ps.score, p.id, p.description, ps.user_id  FROM ai_subparameter_score as ps 
        left join parameter_mst as p on ps.parameter_id = p.id
        left join parameter_label_mst as pl on ps.parameter_label_id = pl.id AND ps.parameter_id = pl.parameter_id    
        WHERE ps.parameter_type ='parameter' AND
        ps.company_id = '".$company_id."' AND ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."'
        GROUP BY ps.parameter_id, ps.parameter_label_id";
        $result = $this->db->query($query);
        //return $result->result();
        return $result->row();
    }

    public function get_manual_score($assessment_id,$user_id){
        $query="SELECT * FROM (
            SELECT main.*,ROUND(@dcp,2) AS previous,  @lastrank as last_rank,
            (SELECT 
            CASE 
            WHEN main.overall_score > previous THEN @cnt := @cnt + 1
            WHEN main.overall_score < previous THEN @cnt := @cnt + 1
            WHEN main.overall_score = previous THEN @cnt := @cnt
            ELSE @cnt := 1 END ) as rank,
            ( SELECT 
            CASE 
            WHEN @lastrank=0 THEN @lastrank := @cnt
            WHEN main.overall_score = @dcp AND @lastrank = @cnt THEN @lastrank := @lastrank
            WHEN main.overall_score < @dcp AND @lastrank != @cnt THEN @lastrank := @lastrank + 2
            WHEN main.overall_score < @dcp AND @lastrank = @cnt THEN @lastrank := @lastrank + 1
            END) as final_rank,
            @dcp := main.overall_score AS current
            FROM (
            SELECT ps.user_id,FORMAT((sum(ps.percentage)/count( ps.question_id)),2) AS overall_score 
            FROM assessment_results_trans AS ps 
            WHERE ps.user_id='".$user_id."' AND ps.assessment_id = '".$assessment_id."'
            GROUP BY user_id ORDER BY overall_score desc) as main 
            CROSS JOIN ( SELECT @cnt := 0 , @dcp := 0, @lastrank := 0 ) AS qcounter) as q
            WHERE q.user_id='".$user_id."'";
           
        $result = $this->db->query($query);
        return $result->row();
     /*   $query  = "SELECT sum(ps.percentage)/count(ps.parameter_id) as score, ps.user_id as score FROM assessment_results_trans as ps 
        WHERE 
        ps.assessment_id = '".$assessment_id."' AND ps.user_id ='".$user_id."' 
        GROUP BY ps.user_id";
        $result = $this->db->query($query);
        return $result->row();*/
        
    }
    public function sum_differnce($assessment_id,$user_id)
    {
        $query= "SELECT  main.user_id, (avg(main.ai)+avg(main.manual))/2 as total, (main.ai-main.manual) as diff   from (select ap.user_id, ap.score as ai, art.percentage as manual from ai_subparameter_score as ap LEFT JOIN assessment_results_trans as art on art.user_id = ap.user_id
        where parameter_type='parameter' AND ap.assessment_id='".$assessment_id."' AND ap.user_id='".$user_id."' 
        GROUP BY art.user_id, ap.user_id) as main GROUP by main.user_id";
        $result = $this->db->query($query);
        return $result->row();
    }
    public function get_all_assessment()
    {
        $query= "SELECT distinct ap.id as assessment_id, ap.assessment as assessment
        from assessment_mst ap 
        group by ap.id
        ORDER by ap.assessment";
           $result = $this->db->query($query);
           return $result->result();
    }
    public function get_all_assessment_manager()
    {
        $query= "SELECT distinct ap.id as assessment_id, ap.assessment as assessment
        from assessment_mst ap where ap.report_type='2' OR ap.report_type='3' 
        group by ap.id
        ORDER by ap.assessment";
           $result = $this->db->query($query);
           return $result->result();
    }
    public function get_distinct_manager($assessment_id)
    {
        $query= "SELECT am.assessment_id, am.trainer_id, CONCAT(cu.first_name,' ' ,cu.last_name) as fullname FROM assessment_managers as am 
        LEFT JOIN company_users as cu on cu.userid=am.trainer_id WHERE assessment_id='".$assessment_id."'";
        $result = $this->db->query($query);
        return $result->result();

    }
    public function get_participants($company_id, $dtwhere){
        $query="SELECT DISTINCT
			c.id,ar.assessment_id,ar.user_id, am.assessment, ar.user_id, 
			CONCAT( du.firstname, ' ', du.lastname ) AS user_name, du.registration_date, du.email, rg.region_name, du.area,
			du.status, du.department
		FROM
			ai_subparameter_score AS ar
			LEFT JOIN company AS c ON ar.company_id = c.id
			LEFT JOIN assessment_mst AS am ON ar.assessment_id = am.id AND ar.company_id = am.company_id 
			LEFT JOIN device_users AS du ON ar.user_id = du.user_id AND ar.company_id = du.company_id 
			LEFT JOIN region as rg on rg.id= du.region_id  
		WHERE 1=1
		 $dtwhere  order by ar.user_id";
		
		
		$result = $this->db->query($query);
		return $result->result();
	} 
    public function get_assessment($start_date, $end_date)
    {
        $query= "SELECT distinct ap.id as assessment_id, ap.assessment as assessment
        from assessment_mst ap where 1=1";
        
         //if ($start_date != '' && $end_date != '') {
            $query .=" AND date(ap.start_dttm) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
           // }
            
//            echo $query;
        $result = $this->db->query($query);
        return $result->result();
    }
}