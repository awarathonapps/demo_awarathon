<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Supervisor_reports_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getRegionWiseData($dtLimit, $dtWhere) {
        $query = "SELECT wr.region_id,wr.company_id,r.region_name,
        IFNULL(FORMAT(SUM(wr.post_correct)*100/ SUM(wr.post_total_questions) - SUM(wr.pre_correct)*100/ SUM(wr.pre_total_questions),2),0) AS avgce, COUNT(DISTINCT wr.workshop_id) AS total_workshop, 
        FORMAT(MAX(b.ce),2) AS highestce, FORMAT(MIN(b.ce),2) AS lowestce,count(distinct ar.user_id) as trainee_trained
                   FROM trainer_result AS wr LEFT JOIN region r ON r.id = wr.region_id 
                   left join atom_results ar on ar.trainer_id = wr.trainer_id AND ar.workshop_id=wr.workshop_id
                   LEFT JOIN (select wr.region_id,IFNULL(SUM(wr.post_correct)*100/ SUM(wr.post_total_questions) - SUM(wr.pre_correct)*100/ SUM(wr.pre_total_questions),0) AS ce
                    from trainer_result as wr LEFT JOIN region r ON r.id = wr.region_id  
                    LEFT JOIN workshop w ON w.id=wr.workshop_id 
                    LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                    LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type 
                    $dtWhere group by wr.workshop_id
            ) as b ON  b.region_id=wr.region_id 
                    LEFT JOIN workshop w ON w.id=wr.workshop_id 
                    LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                    LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type ";
        $query .= " $dtWhere group by wr.region_id $dtLimit";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();

        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query1 = "select count(distinct wr.region_id) as total                    
            from trainer_result as wr left join region r on r.id = wr.region_id 
                  LEFT JOIN workshop w ON w.id=wr.workshop_id 
                  LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                  LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $dtWhere";
        $result1 = $this->db->query($query1);
        $data_array = $result1->result_array();
        if (count((array)$data_array) > 0) {
            $data['dtTotalRecords'] = $data_array[0]['total'];
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }

    public function getWorkshopTypeWiseData($dtLimit, $dtWhere) {
        $TodayDt = date('Y-m-d H:i:s');
        $query = " select wr.workshop_type,wr.company_id,wtm.workshop_type as workshop_type_name,
                          IFNULL(FORMAT(SUM(wr.post_correct)*100/ SUM(wr.post_total_questions) - SUM(wr.pre_correct)*100/ SUM(wr.pre_total_questions),2),0) AS avgce, COUNT(DISTINCT wr.workshop_id) AS total_workshop, 
        FORMAT(MAX(b.ce),2) AS highestce, FORMAT(MIN(b.ce),2) AS lowestce,count(distinct ar.user_id) as trainee_trained
                          from trainer_result as wr left join workshoptype_mst wtm on wtm.id = wr.workshop_type 
                          left join atom_results ar on ar.trainer_id = wr.trainer_id AND ar.workshop_id=wr.workshop_id
                          LEFT JOIN (select wr.workshop_type,IFNULL(SUM(wr.post_correct)*100/ SUM(wr.post_total_questions) - SUM(wr.pre_correct)*100/ SUM(wr.pre_total_questions),0) AS ce 
                    from trainer_result as wr left join workshoptype_mst wtm on wtm.id = wr.workshop_type  
                    left join workshop w ON w.id=wr.workshop_id 
                    LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                    LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type 
                    $dtWhere group by wr.workshop_id
                    ) as b ON  b.workshop_type=wr.workshop_type 
                    LEFT JOIN workshop w ON w.id=wr.workshop_id 
                    LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                    LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type ";
        $query .= " $dtWhere group by wr.workshop_type $dtLimit";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);
        $query1 = "select count(distinct wr.workshop_type) as total                    
                    from trainer_result as wr left join workshoptype_mst wtm on wtm.id = wr.workshop_type 
                    LEFT JOIN workshop w ON w.id=wr.workshop_id 
                    LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                    LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $dtWhere";
        $result1 = $this->db->query($query1);
        $data_array = $result1->result_array();
        if (count((array)$data_array) > 0) {
            $data['dtTotalRecords'] = $data_array[0]['total'];
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }

    public function getWorkshopWiseData($dtLimit, $dtWhere) {
        $TodayDt = date('Y-m-d H:i:s');
        $query = "select wr.workshop_id,wr.company_id,w.workshop_name,wtm.workshop_type as workshop_type_name,
		IFNULL(format(format(sum(wr.post_correct)*100/sum(wr.post_total_questions),2) -format(sum(wr.pre_correct)*100/sum(wr.pre_total_questions),2),2 ),0) as avgce,count(distinct ar.user_id) as trainee_trained
		from trainer_result as wr left join workshop w on w.id = wr.workshop_id
                left join atom_results ar on ar.trainer_id = wr.trainer_id AND ar.workshop_id=wr.workshop_id
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                left join workshoptype_mst wtm on wtm.id = wr.workshop_type  
                $dtWhere ";
        $query .= " group by wr.workshop_id order by workshop_id desc $dtLimit  ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query1 = "select count( distinct wr.workshop_id) as total                    
            from trainer_result as wr left join workshop w on w.id = wr.workshop_id 
              LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
              LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
            left join workshoptype_mst wtm on wtm.id = wr.workshop_type $dtWhere";
        $result1 = $this->db->query($query1);
        $data_array = $result1->result_array();
        if (count($data_array) > 0) {
            $data['dtTotalRecords'] = $data_array[0]['total'];
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }

    public function getTrainerWiseData($dtLimit, $dtWhere) {
        $query = "select wr.workshop_id,wr.trainer_id,wr.company_id,count(distinct wr.workshop_id) as total_workshop,
		concat(cu.first_name,' ',cu.last_name) as trainer_name,
		IFNULL(FORMAT(SUM(wr.post_correct)*100/ SUM(wr.post_total_questions) - SUM(wr.pre_correct)*100/ SUM(wr.pre_total_questions),2),0) AS avgce,
        FORMAT(MAX(b.ce),2) AS highestce, FORMAT(MIN(b.ce),2) AS lowestce,count(distinct ar.user_id) as trainee_trained
		from trainer_result as wr left join company_users cu
		on cu.userid = wr.trainer_id 
                left join atom_results ar on ar.trainer_id = wr.trainer_id AND ar.workshop_id=wr.workshop_id
                LEFT JOIN (select wr.trainer_id,IFNULL(SUM(wr.post_correct)*100/ SUM(wr.post_total_questions) - SUM(wr.pre_correct)*100/ SUM(wr.pre_total_questions),0) AS ce
                    from trainer_result as wr left join company_users cu
		on cu.userid = wr.trainer_id  
                LEFT JOIN workshop w ON w.id=wr.workshop_id 
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                $dtWhere group by wr.workshop_id,wr.trainer_id
                    ) as b ON  b.trainer_id=wr.trainer_id  
                    LEFT JOIN workshop w ON w.id=wr.workshop_id 
                    LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                    LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type ";
        $query .= " $dtWhere group by wr.trainer_id $dtLimit";
        
        
        
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);

        $query1 = "select count(distinct wr.trainer_id) as total                    
                    from trainer_result as wr left join company_users cu
                    on cu.userid = wr.trainer_id 
                    LEFT JOIN workshop w ON w.id=wr.workshop_id 
                    LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                    LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $dtWhere";
        $result1 = $this->db->query($query1);
        $data_array = $result1->result_array();
        if (count((array)$data_array) > 0) {
            $data['dtTotalRecords'] = $data_array[0]['total'];
        } else {
            $data['dtTotalRecords'] = 0;
        }
        return $data;
    }

    public function getRegionWiseTrainer($region_id, $WhereCond) {
        
        $query = "SELECT ifnull(wsr.description,'No-sub region') as workshop_subregion ,count(distinct ar.user_id) as trainee_trained,wr.trainer_id,
        IFNULL(FORMAT(SUM(wr.post_correct)*100/ SUM(wr.post_total_questions) - SUM(wr.pre_correct)*100/ SUM(wr.pre_total_questions),2),0) AS avgce,
        FORMAT(MAX(b.ce),2) AS highestce, FORMAT(MIN(b.ce),2) AS lowestce, w.workshopsubregion_id,wr.region_id
                   FROM trainer_result AS wr LEFT JOIN workshop as w ON w.id=wr.workshop_id
                   LEFT JOIN workshopsubregion_mst as wsr ON wsr.region_id=w.region AND wsr.id=w.workshopsubregion_id
		left join atom_results ar on ar.trainer_id = wr.trainer_id AND ar.workshop_id=wr.workshop_id			
                   LEFT JOIN (select w.workshopsubregion_id,IFNULL(SUM(wr.post_correct)*100/ SUM(wr.post_total_questions) - SUM(wr.pre_correct)*100/ SUM(wr.pre_total_questions),0) AS ce
                    from trainer_result as wr LEFT JOIN workshop as w ON w.id=wr.workshop_id where wr.region_id=$region_id  $WhereCond 
                    group by wr.workshop_id
            ) as b ON  b.workshopsubregion_id=w.workshopsubregion_id ";
        $query .= "  where wr.region_id=$region_id  $WhereCond  group by w.workshopsubregion_id ";
        

        $result = $this->db->query($query);
        return $result->result();
    }

    public function getWTypeWiseTrainer($workshop_type_id, $WhereCond) {
        
         $query = "SELECT ifnull(wst.description,'No-sub type') as workshop_subtype ,count(distinct ar.user_id) as trainee_trained,wr.trainer_id,
        IFNULL(FORMAT(SUM(wr.post_correct)*100/ SUM(wr.post_total_questions) - SUM(wr.pre_correct)*100/ SUM(wr.pre_total_questions),2),0) AS avgce,
        FORMAT(MAX(b.ce),2) AS highestce, FORMAT(MIN(b.ce),2) AS lowestce,w.workshopsubtype_id,wr.workshop_type
                   FROM trainer_result AS wr LEFT JOIN workshop as w ON w.id=wr.workshop_id
                   left join workshopsubtype_mst as wst ON wst.workshoptype_id=wr.workshop_type AND wst.id=w.workshopsubtype_id
                   left join atom_results ar on ar.trainer_id = wr.trainer_id AND ar.workshop_id=wr.workshop_id
                   LEFT JOIN (select w.workshopsubtype_id,IFNULL(SUM(wr.post_correct)*100/ SUM(wr.post_total_questions) - SUM(wr.pre_correct)*100/ SUM(wr.pre_total_questions),0) AS ce
                    from trainer_result as wr LEFT JOIN workshop as w ON w.id=wr.workshop_id where wr.workshop_type=$workshop_type_id  $WhereCond 
                    group by wr.workshop_id
            ) as b ON  b.workshopsubtype_id=w.workshopsubtype_id ";
        $query .= "  where  wr.workshop_type=$workshop_type_id  $WhereCond  group by w.workshopsubtype_id ";
        
        $result = $this->db->query($query);
        return $result->result();
    }

    public function getWorkshopWiseTrainer($workshop_id, $WhereCond) {
        $query = " select wr.trainer_id,
		concat(cu.first_name,' ',cu.last_name) as trainer_name,
                count(distinct ar.user_id) as trainee_trained,
		IFNULL(format(sum(wr.post_correct)*100/sum(wr.post_total_questions) -sum(wr.pre_correct)*100/sum(wr.pre_total_questions),2 ),0) as avgce,
		max(wr.ce) as highestce,min(wr.ce) as lowestce
		from trainer_result as wr
		left join company_users cu
		on cu.userid = wr.trainer_id
		left join atom_results ar
		on ar.trainer_id = wr.trainer_id AND ar.workshop_id=wr.workshop_id		
                LEFT JOIN workshop as w ON w.id=wr.workshop_id
		where wr.workshop_id=$workshop_id $WhereCond
                group by wr.trainer_id ";

        $result = $this->db->query($query);
        return $result->result();
    }
    public function getWorkshopTopicdt($dtWhere,$workshop_id){
        $TodayDt = date('Y-m-d H:i');
        $query = " SELECT qt.description AS topic, 
                (SUM(ls.post_correct)*100/ SUM(ls.post_total_questions))-(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions)) AS ce
                FROM (
                SELECT wr.topic_id,sum(wr.pre_correct) as pre_correct ,sum(wr.pre_total_questions) as pre_total_questions ,sum(wr.post_correct) as post_correct,
                sum(wr.post_total_questions) as post_total_questions
                FROM trainee_result AS wr 
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=wr.workshop_id AND wtu.tester_id=wr.trainee_id 
                LEFT JOIN workshop w ON w.id=wr.workshop_id 
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type ";
                $query .= "  $dtWhere AND wtu.tester_id IS NULL AND (wr.ce_eligible=1 OR
                wr.trainee_id IN(SELECT distinct user_id FROM atom_results WHERE workshop_session='POST' AND workshop_id= $workshop_id)) and wr.pre_total_questions>0 ";
        $query .= " GROUP BY wr.topic_id UNION ALL
                SELECT wr.topic_id, SUM(wr.is_correct) AS pre_correct, COUNT(wr.question_id) AS pre_total_questions,
                0 AS post_correct, 0 AS post_total_questions
                FROM atom_results AS wr
                INNER JOIN workshop AS w ON w.id=wr.workshop_id
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=wr.workshop_id AND wtu.tester_id=wr.user_id
                $dtWhere and wr.workshop_session='PRE' AND CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >='$TodayDt'
                AND wr.user_id in(select distinct user_id FROM atom_results where workshop_session='POST'  AND workshop_id= $workshop_id  )
                AND wtu.tester_id IS NULL ";
        $query .= " GROUP BY wr.topic_id UNION ALL
                SELECT wr.topic_id,0 AS pre_correct,0 AS pre_total_questions, SUM(wr.is_correct) AS post_correct, COUNT(wr.question_id) AS post_total_questions
                FROM atom_results AS wr
                INNER JOIN workshop AS w ON w.id=wr.workshop_id 
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=wr.workshop_id AND wtu.tester_id=wr.user_id
                $dtWhere AND wr.workshop_session='POST' AND CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >='$TodayDt'
                AND wr.user_id in(select distinct user_id FROM atom_results where workshop_session='PRE'  AND workshop_id= $workshop_id  )
                AND wtu.tester_id IS NULL ";
        $query .= " GROUP BY wr.topic_id) AS ls
            INNER JOIN question_topic qt ON qt.id=ls.topic_id
                GROUP BY ls.topic_id HAVING ce is not null order by ce desc ";
        //echo $query;
        //exit;
        $result = $this->db->query($query);
        $ResultSet = $result->result();
        $BesicTopic='';
        $WorstTopic='';
        $totalTopic=count((array)$ResultSet);
        if($totalTopic>0){
            $BesicTopic =$ResultSet[0]->topic;
            if($totalTopic>1){
                $WorstTopic = $ResultSet[$totalTopic-1]->topic;
            }
        }
        $data['BesicTopic']=$BesicTopic;
        $data['WorstTopic']=$WorstTopic;
        return $data;
    }
      public function getsubregionWiseTrainer($Wherecond) {
      $query = " select wr.trainer_id,
		concat(cu.first_name,' ',cu.last_name) as trainer_name,
                count(distinct ar.user_id) as trainee_trained,
		IFNULL(format(sum(wr.post_correct)*100/sum(wr.post_total_questions) -sum(wr.pre_correct)*100/sum(wr.pre_total_questions),2 ),0) as avgce,
		max(wr.ce) as highestce,min(wr.ce) as lowestce
		from trainer_result as wr
		left join company_users cu on cu.userid = wr.trainer_id
		left join atom_results ar on ar.trainer_id = wr.trainer_id AND ar.workshop_id=wr.workshop_id		
		LEFT JOIN workshop w ON w.id=wr.workshop_id 
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                $Wherecond group by wr.trainer_id ";

        $result = $this->db->query($query);
        return $result->result();
}
}
