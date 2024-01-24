<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Knowledge_assessment_dashboard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    public function get_HighestLowestAvgCE($Company_id, $trainer_id = "0", $RightsFlag,$WRightsFlag, $StartDate = "", $EndDate = "",$wrktype_id="0",$wsubtype_id="",$flt_region_id="0",$subregion_id="") {
        $Login_id =$this->mw_session['user_id'];
            $query = "SELECT FORMAT(IFNULL(MAX(ls.no_ce),0),2) AS maxce, FORMAT(IFNULL(MIN(ls.no_ce),0),2) AS mince, IFNULL(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions) - SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2),0) AS avgce
            FROM (select SUM(tr.pre_correct) as pre_correct,SUM(tr.pre_total_questions) as pre_total_questions,SUM(tr.post_correct) as post_correct,SUM(tr.post_total_questions) as post_total_questions,
            FORMAT(FORMAT(SUM(tr.post_correct)*100/ SUM(tr.post_total_questions),2) - FORMAT(SUM(tr.pre_correct)*100/ SUM(tr.pre_total_questions),2),2) as ce,
            (SUM(tr.post_correct)*100/ SUM(tr.post_total_questions)) - (SUM(tr.pre_correct)*100/ SUM(tr.pre_total_questions)) as no_ce";
        $query .= " FROM trainer_result tr "
                . " left join workshop w
                    on w.id = tr.workshop_id 
                    where tr.company_id=$Company_id ";
        if($wrktype_id !="0"){
            $query .= " AND w.workshop_type =".$wrktype_id ;
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (tr.trainer_id = $Login_id OR tr.trainer_id IN(select trainer_id FROM temp_trights where user_id= $Login_id))";
            }
        } else {
            $query .= " AND tr.trainer_id=" . $trainer_id;
        }
        if (!$WRightsFlag && $trainer_id != $Login_id) {
            $query .= " AND tr.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $Login_id)";
        }
        if ($StartDate != "" && $EndDate != "") {
            $query .= " AND tr.workshop_date between '$StartDate' AND '$EndDate'";
        }
        $query .= " group by tr.workshop_id) as ls";
//        echo $query;
//        exit;
//        $query = "SELECT IFNULL(MAX(ls.ce),0) AS maxce, IFNULL(MIN(ls.ce),0) AS mince, IFNULL(FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions) - SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2),0) AS avgce
//            FROM (select SUM(pre_correct) as pre_correct,SUM(pre_total_questions) as pre_total_questions,SUM(post_correct) as post_correct,SUM(post_total_questions) as post_total_questions,
//            FORMAT(FORMAT(SUM(post_correct)*100/ SUM(post_total_questions),2) - FORMAT(SUM(pre_correct)*100/ SUM(pre_total_questions),2),2) as ce";
//        $query .= " FROM trainer_result where company_id=$Company_id ";
//        if ($trainer_id == "0") {
//            if (!$RightsFlag) {
//                $query .= " AND (trainer_id = $Login_id OR trainer_id IN(select trainer_id FROM temp_trights where user_id= $Login_id))";
//            }
//        } else {
//            $query .= " AND trainer_id=" . $trainer_id;
//        }
//        if (!$WRightsFlag && $trainer_id != $Login_id) {
//            $WRegion_rights = $this->checkWRegion_rights($Login_id);
//            if($WRegion_rights){
//                $query .= " AND (region_id IN(select region_id FROM cmsusers_wregion_rights where userid= $Login_id)";
//                $WType_rights = $this->checkWorkshopType_rights($Login_id);
//                if ($WType_rights) {
//                    $query .= " AND workshop_type IN(select workshop_type_id FROM cmsusers_wtype_rights where userid= $Login_id)";
//                    $Workshop_rights = $this->checkWorkshop_rights($Login_id);
//                    if ($Workshop_rights) {
//                        $query .= " AND workshop_id IN(select workshop_id FROM cmsusers_workshop_rights where userid= $Login_id )";
//                    }
//                    $query .= " OR workshop_id IN(select distinct workshop_id FROM atom_results where trainer_id= $login_id))";
//                }
//            }else{
//                $query .= " AND workshop_id IN(select distinct workshop_id FROM atom_results where trainer_id= $Login_id)";
//            }
//        }
//        if ($StartDate != "" && $EndDate != "") {
//            $query .= " AND workshop_date between '$StartDate' AND '$EndDate'";
//        }
//        $query .= " group by workshop_id) as ls";

        $result = $this->db->query($query);
        $MaxCE = 0;
        $MinCE = 0;
        $Avg = 0;
        $RowSet = $result->row();
        if (count((array)$RowSet) > 0) {
            $MaxCE = $RowSet->maxce;
            if ($MaxCE != $RowSet->mince) {
                $MinCE = $RowSet->mince;
            }
            $Avg = $RowSet->avgce;
        }
        $data['MaxCE'] = $MaxCE;
        $data['MinCE'] = $MinCE;
        $data['Avg'] = $Avg;
        return $data;
    }

    public function get_TotalWorkshop($Company_id, $trainer_id, $RightsFlag,$WRightsFlag, $StartDate = "", $EndDate = "",$wrktype_id="0",$wsubtype_id="",$flt_region_id="0",$subregion_id="") {
        $Login_id =$this->mw_session['user_id'];
        $query = "select ifnull(count(distinct(ar.workshop_id)),0) as total_workshop "
                . " from atom_results ar left join workshop w "
                . " on w.id = ar.workshop_id"
                . " where ar.company_id=$Company_id";
        if($wrktype_id !="0"){
            $query .= " AND w.workshop_type =".$wrktype_id ;
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if ($StartDate != "") {
            $query .= " AND date(w.start_date) between '$StartDate' AND '$EndDate'";
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (ar.trainer_id = $Login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $Login_id))";
            }
        } else {
            $query .= " AND ar.trainer_id=" . $trainer_id;
        }
        if (!$WRightsFlag && $trainer_id != $Login_id) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $Login_id)";
        }
		
        $result = $this->db->query($query);
        $RowSet = $result->row();
        $TotalWk = 0;
        if (count((array)$RowSet) > 0) {
            $TotalWk = $RowSet->total_workshop;
        }
        return $TotalWk;
    }

    public function get_WorstRegion($Company_id, $trainer_id, $RightsFlag,$WRightsFlag, $MaxCE, $StartDate = "", $EndDate = "",$wrktype_id="0",$wsubtype_id="",$flt_region_id="0",$subregion_id="") {
        $Region = '-';
        $Login_id =$this->mw_session['user_id'];
        $query  = " SELECT a.region_id,b.region_name , FORMAT(SUM(post_correct)*100/ SUM(post_total_questions) - SUM(pre_correct)*100/ SUM(pre_total_questions),2) as ce,"
                . " SUM(post_correct)*100/ SUM(post_total_questions) as post_accuracy";
        $query .= " FROM trainer_result as a LEFT JOIN  region as b ON b.id=a.region_id "
                . " LEFT JOIN workshop w ON w.id = a.workshop_id "
                . " where a.company_id=$Company_id";
        if($wrktype_id !="0"){
            $query .= " AND w.workshop_type =".$wrktype_id ;
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $Login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $Login_id))";
            }
        } else {
            $query .= " AND a.trainer_id = $trainer_id ";
        }
        if (!$WRightsFlag && $trainer_id != $Login_id) {
            $query .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $Login_id)";
        }
        if ($StartDate != "") {
            $query .= " AND a.workshop_date between '$StartDate' AND '$EndDate'";
        }        
        $query .= " group by workshop_id having ce='$MaxCE' order by post_accuracy asc  LIMIT 1";
		
//        if($trainer_id =="0"){
//           if(!$RightsFlag){
//               $trainer_id =$trainer_id =$this->mw_session['user_id']; 
//                $query = "select a.region_id,b.region_name FROM trainer_result as a LEFT JOIN  region as b ON b.id=a.region_id
//                where a.company_id=$Company_id and a.ce IN(select min(ce) from trainer_result where company_id=$Company_id ";   
//                $query .= " AND (a.trainer_id = $trainer_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $trainer_id))";
//           }else{
//               $query = "select a.region_id,b.region_name FROM workshop_result as a LEFT JOIN  region as b ON b.id=a.region_id
//                where a.company_id=$Company_id and a.ce IN(select min(ce) from workshop_result where company_id=$Company_id ";
//           }
//        }else{
//            $query = "select a.region_id,b.region_name FROM trainer_result as a LEFT JOIN  region as b ON b.id=a.region_id
//                where a.company_id=$Company_id and a.ce IN(select min(ce) from trainer_result where company_id=$Company_id ";   
//                $query .= " AND a.trainer_id = $trainer_id ";
//        }
//        if ($StartDate != "") {
//            $query .= " AND workshop_date between '$StartDate' AND '$EndDate') "
//                    . " AND workshop_date between '$StartDate' AND '$EndDate'";
//        } else {
//            $query .= ")";
//        }
//        $query .= "  order by a.post,a.pre ,a.post_avgtime desc LIMIT 1";

        $result = $this->db->query($query);
        $RowSet = $result->row();
		
        if (count((array)$RowSet) > 0) {
            $Region = $RowSet->region_name;
        }
        return $Region;
    }
    public function get_BestRegion($Company_id, $trainer_id, $RightsFlag,$WRightsFlag, $MaxCE, $StartDate = "", $EndDate = "",$wrktype_id="0",$wsubtype_id="",$flt_region_id="0",$subregion_id="") {
        $Region = '-';
        $Login_id =$this->mw_session['user_id'];
        $query  = " SELECT a.region_id,b.region_name , FORMAT(SUM(post_correct)*100/ SUM(post_total_questions) - SUM(pre_correct)*100/ SUM(pre_total_questions),2) as ce,"
                . " SUM(post_correct)*100/ SUM(post_total_questions) as post_accuracy";
        $query .= " FROM trainer_result as a LEFT JOIN  region as b ON b.id=a.region_id "
                . " LEFT JOIN workshop w ON w.id = a.workshop_id "
                . " where a.company_id=$Company_id";
        if($wrktype_id !="0"){
            $query .= " AND w.workshop_type =".$wrktype_id ;
        }
        if($wsubtype_id !=""){
            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
        if($flt_region_id !="0"){
            $query .= " AND w.region =".$flt_region_id ;
        }
        if($subregion_id !=""){
            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (a.trainer_id = $Login_id OR a.trainer_id IN(select trainer_id FROM temp_trights where user_id= $Login_id))";
            }
        } else {
            $query .= " AND a.trainer_id = $trainer_id ";
        }
        if (!$WRightsFlag && $trainer_id != $Login_id) {
            $query .= " AND a.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $Login_id)";
        }
        if ($StartDate != "") {
            $query .= " AND a.workshop_date between '$StartDate' AND '$EndDate'";
        }        
        $query .= " group by a.workshop_id having ce='$MaxCE' order by post_accuracy desc  LIMIT 1";
        // echo $query;
        // exit;
        $result = $this->db->query($query);
        $RowSet = $result->row();
        if (count((array)$RowSet) > 0) {
            $Region = $RowSet->region_name;
        }
        return $Region;
    }

    public function get_RegionWisePerformance($Company_id, $trainer_id, $RightsFlag,$WRightsFlag, $start_date, $end_date,$wrktype_id="0",$wsubtype_id="",$flt_region_id="0",$subregion_id="") {
        $Login_id =$this->mw_session['user_id'];
        $query = "  select a.region_id,a.lifetime,b.monthly,";                   
                    $query .=" rg.region_name FROM (
                            select w.workshopsubregion_id,tr.region_id,format(sum(tr.post_correct)*100/sum(tr.post_total_questions) -sum(tr.pre_correct)*100/sum(tr.pre_total_questions),2 ) as lifetime 
                            FROM trainer_result tr "
                        . " LEFT JOIN workshop w "
                        . " ON w.id = tr.workshop_id where tr.company_id=$Company_id ";
                        if($wrktype_id !="0"){
                            $query .= " AND tr.workshop_type =".$wrktype_id ;
                        }
                        if($wsubtype_id !=""){
                            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
                        }
                        if($flt_region_id !="0"){
                            $query .= " AND tr.region_id =".$flt_region_id ;
                        }
                        if($subregion_id !=""){
                            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
                        }    
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                                $query .= "  AND (tr.trainer_id = $Login_id OR tr.trainer_id IN(select trainer_id FROM temp_trights where user_id= $Login_id))";
            }
        } else {
                            $query .= " AND tr.trainer_id=" . $trainer_id;
        }
        if (!$WRightsFlag && $trainer_id != $Login_id) {
                            $query .= " AND tr.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $Login_id)";
        }
                        if($subregion_id !=""){
                            $query .= " group by w.workshopsubregion_id ) as a ";
                        }else{
                            $query .= " group by tr.region_id ) as a ";
                        }
            
            $query .= " LEFT JOIN (
                        select w.workshopsubregion_id,trs.region_id,format(sum(trs.post_correct)*100/sum(trs.post_total_questions) -sum(trs.pre_correct)*100/sum(trs.pre_total_questions),2 ) as monthly 
                        FROM trainer_result trs "
                        . " LEFT JOIN workshop w "
                        . " ON w.id = trs.workshop_id where trs.company_id=$Company_id ";
                        if($wrktype_id !="0"){
                            $query .= " AND trs.workshop_type =".$wrktype_id ;
                        }
                        if($wsubtype_id !=""){
                            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
                        }
                        if($flt_region_id !="0"){
                            $query .= " AND trs.region_id =".$flt_region_id ;
                        }
                        if($subregion_id !=""){
                            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
                        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                                $query .= "  AND (trs.trainer_id = $Login_id OR trs.trainer_id IN(select trainer_id FROM temp_trights where user_id= $Login_id))";
            }
        } else {
                            $query .= "  AND trs.trainer_id=" . $trainer_id;
        }
        if (!$WRightsFlag && $trainer_id != $Login_id) {
                            $query .= " AND trs.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $Login_id)";
        }
                    $query .= " AND trs.workshop_date between '$start_date' AND '$end_date' ";
                    if($subregion_id !=""){
                        $query .= " group by w.workshopsubregion_id ) as b ";
                    }else{
                        $query .= " group by trs.region_id ) as b ";
                    }
                    $query .= " ON a.region_id=b.region_id LEFT JOIN region as rg ON rg.id=a.region_id";
                   
                    // if($subregion_id !=""){
                    //     $query .= " LEFT JOIN workshopsubregion_mst as srg ON srg.id=a.workshopsubregion_id";                        
                    // }
                    
                    
        $result = $this->db->query($query);
        return $result->result();
    }

    public function get_WorkshoptypeWisePerformance($Company_id, $trainer_id, $RightsFlag,$WRightsFlag, $start_date, $end_date,$wrktype_id="0",$wsubtype_id="",$flt_region_id="0",$subregion_id="") {
        $Login_id =$this->mw_session['user_id'];
        $query = "select a.workshop_type,a.lifetime,b.monthly,";
                // if($wsubtype_id !=""){
                //     $query .=" wst.description as workshop_type ";                                
                // }else{
                //     $query .=" wt.workshop_type ";
                // }
                 
            $query .=" wt.workshop_type FROM (
                    select w.workshopsubtype_id,trs.workshop_type,format(sum(trs.post_correct)*100/sum(trs.post_total_questions) -sum(trs.pre_correct)*100/sum(trs.pre_total_questions),2 ) as lifetime 
                    FROM trainer_result trs 
                    LEFT JOIN workshop w "
                    . " ON w.id = trs.workshop_id where trs.company_id=$Company_id ";
                        if($wrktype_id !="0"){
                            $query .= " AND trs.workshop_type =".$wrktype_id ;
                        }
                        if($wsubtype_id !=""){
                            $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
                        }
                        if($flt_region_id !="0"){
                            $query .= " AND trs.region_id =".$flt_region_id ;
                        }
                        if($subregion_id !=""){
                            $query .= " AND w.workshopsubregion_id =".$subregion_id ;
                        }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                        $query .= " AND (trs.trainer_id = $Login_id OR trs.trainer_id IN(select trainer_id FROM temp_trights where user_id= $Login_id))";
            }
        } else {
                    $query .= "  AND trs.trainer_id=" . $trainer_id;
        }
        if (!$WRightsFlag && $trainer_id != $Login_id) {
                    $query .= " AND trs.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $Login_id)";
        }
                $query .= " group by trs.workshop_type
        ) as a
        LEFT JOIN (
                select w.workshopsubtype_id,tr.workshop_type,format(sum(tr.post_correct)*100/sum(tr.post_total_questions) -sum(tr.pre_correct)*100/sum(tr.pre_total_questions),2 ) as monthly 
                FROM trainer_result tr LEFT JOIN workshop w "
                . " ON w.id = tr.workshop_id where tr.company_id=$Company_id ";
                if($wrktype_id !="0"){
                    $query .= " AND tr.workshop_type =".$wrktype_id ;
                }
                if($wsubtype_id !=""){
                    $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
                }
                if($flt_region_id !="0"){
                    $query .= " AND tr.region_id =".$flt_region_id ;
                }
                if($subregion_id !=""){
                    $query .= " AND w.workshopsubregion_id =".$subregion_id ;
                }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                        $query .= "   AND (tr.trainer_id = $Login_id OR tr.trainer_id IN(select trainer_id FROM temp_trights where user_id= $Login_id))";
            }
        } else {
                    $query .= " AND tr.trainer_id=" . $trainer_id;
        }
        if (!$WRightsFlag && $trainer_id != $Login_id) {
                    $query .= " AND tr.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $Login_id)";
        }
            $query .= " AND tr.workshop_date between '$start_date' AND '$end_date' ";

            if($wsubtype_id !=""){
                $query .= " group by w.workshopsubtype_id ) as b ";
            }else{
                $query .= " group by tr.workshop_type ) as b ";
            }
            $query .= " ON a.workshop_type=b.workshop_type LEFT JOIN workshoptype_mst as wt ON wt.id=a.workshop_type";
            
        $result = $this->db->query($query);
        return $result->result();
    }
    public function histogram_range() {
        $query = "SELECT * FROM histogram_range ORDER BY from_range,to_range";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function supervisor_index_weekly_monthly($company_id = '', $trainer_id, $RightsFlag,$WRightsFlag,$StartDate, $EndDate,$wrktype_id="0",$wsubtype_id="",$flt_region_id="0",$subregion_id="") {
        $RegionWhereClause = '';
        $WtypeWhereClause = '';
        $Login_id =$this->mw_session['user_id'];
//        if ($region_id != "") {
//            $RegionWhereClause = " AND wr.region_id='" . $region_id . "'";
//        }
//        if ($wtype_id != "") {
//            $WtypeWhereClause = " AND wr.workshop_type='" . $wtype_id . "'";
//        }
        
        $query = " SELECT w.start_date,DATE_FORMAT(w.start_date,'%d') wday,"
                . " format(sum(wr.post_correct)*100/sum(wr.post_total_questions) -sum(wr.pre_correct)*100/sum(wr.pre_total_questions),2 ) as avg_ce"
                . " FROM trainer_result wr "
                . " left join workshop w on w.id = wr.workshop_id and w.company_id = wr.company_id "
                . " where wr.company_id=$company_id ";
                if($wrktype_id !="0"){
                    $query .= " AND wr.workshop_type =".$wrktype_id ;
        }
                if($wsubtype_id !=""){
                    $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
        }
                if($flt_region_id !="0"){
                    $query .= " AND wr.region_id =".$flt_region_id ;
                }
                if($subregion_id !=""){
                    $query .= " AND w.workshopsubregion_id =".$subregion_id ;
                }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= "  AND (trainer_id = $Login_id OR trainer_id"
                        . " IN(select trainer_id FROM temp_trights where user_id= $Login_id))";
            }
        } else {
            $query .= "  AND wr.trainer_id=" . $trainer_id;
        }
        if (!$WRightsFlag && $trainer_id != $Login_id) {
            $query .= " AND wr.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $Login_id)";
        }
        $query .= " and w.start_date BETWEEN '$StartDate' AND '$EndDate' 
                GROUP BY w.start_date ";

        
        $result = $this->db->query($query);
        $CE = $result->result();
        $ResultArray = array();
        if (count((array)$CE) > 0) {
            foreach ($CE as $value) {
                $ResultArray[$value->wday] = $value->avg_ce;
            }
        }
        return $ResultArray;
    }
    public function supervisor_index_yearly($company_id = '', $trainer_id, $RightsFlag,$WRightsFlag,$StartDate = '', $EndDate = '',$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $RegionWhereClause = '';
        $WtypeWhereClause = '';
        $Login_id =$this->mw_session['user_id'];
//        if ($region_id != "") {
//            $RegionWhereClause = " AND wr.region_id='" . $region_id . "'";
//        }
//        if ($wtype_id != "") {
//            $WtypeWhereClause = " AND wr.workshop_type='" . $wtype_id . "'";
//        }
        $TodayDt = date('Y-m-d H:i:s');
        $query = "SELECT month(w.start_date) as wmonth,DATE_FORMAT(w.start_date,'%d') wday,
            format(sum(wr.post_correct)*100/sum(wr.post_total_questions) -sum(wr.pre_correct)*100/sum(wr.pre_total_questions),2 ) as avg_ce 
            FROM trainer_result wr left join workshop w on w.id = wr.workshop_id and w.company_id = wr.company_id "
            . " where wr.company_id=$company_id "; 
                if($wrktype_id !="0"){
                    $query .= " AND wr.workshop_type =".$wrktype_id ;
                }
                if($wsubtype_id !=""){
                    $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
                }
                if($flt_region_id !="0"){
                    $query .= " AND wr.region_id =".$flt_region_id ;
                }
                if($subregion_id !=""){
                    $query .= " AND w.workshopsubregion_id =".$subregion_id ;
                }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= "  AND (trainer_id = $Login_id OR trainer_id"
                        . " IN(select trainer_id FROM temp_trights where user_id= $Login_id))";
            }
        } else {
            $query .= " AND wr.trainer_id=" . $trainer_id;
        }
        if (!$WRightsFlag && $trainer_id != $Login_id) {
            $query .= " AND wr.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $Login_id)";
        }
        $query .= " and w.start_date BETWEEN '$StartDate' AND '$EndDate' 
                GROUP BY month(w.start_date) ";
    
        $result = $this->db->query($query);
        $AvgCE = $result->result();
        $ResultArray = array();
        if (count((array)$AvgCE) > 0) {
            foreach ($AvgCE as $value) {
                $ResultArray[$value->wmonth] = $value->avg_ce;
            }
        }
        return $ResultArray;
    }
    public function trainer_histogram_range($company_id,$trainer_id, $RightsFlag,$WRightsFlag, $WeekStartDate = '', $WeekEndDate = '',$wrktype_id='0',$wsubtype_id='',$flt_region_id='0',$subregion_id='') {
        $TodayDt = date('Y-m-d H:i:s');
        $RegionWhereClause = '';
        $WtypeWhereClause = '';
        $Login_id =$this->mw_session['user_id'];
//        if ($region_id != "") {
//            $RegionWhereClause = " AND trs.region_id='" . $region_id . "'";
//        }
//        if ($wtype_id != "") {
//            $WtypeWhereClause = " AND trs.workshop_type='" . $wtype_id . "'";
//        }

        $query = "select hr.from_range,hr.to_range,if(tr.trainer_id != '' ,COUNT(DISTINCT tr.trainer_id),null) as TrainerCount 
                            FROM histogram_cerange as hr LEFT JOIN 
            (select IFNULL(format(sum(post_correct)*100/sum(post_total_questions) -sum(pre_correct)*100/sum(pre_total_questions),0 ),0) as ce,trainer_id
            from trainer_result trs left join workshop w on w.id = trs.workshop_id and w.company_id = trs.company_id
            where trs.company_id=$company_id  ";
            if($wrktype_id !="0"){
                $query .= " AND trs.workshop_type =".$wrktype_id ;
            }
            if($wsubtype_id !=""){
                $query .= " AND w.workshopsubtype_id =".$wsubtype_id ;
            }
            if($flt_region_id !="0"){
                $query .= " AND trs.region_id =".$flt_region_id ;
            }
            if($subregion_id !=""){
                $query .= " AND w.workshopsubregion_id =".$subregion_id ;
            }
        if ($trainer_id == "0") {
            if (!$RightsFlag) {
                $query .= " AND (trs.trainer_id = $Login_id OR trs.trainer_id"
                    . " IN(select trainer_id FROM temp_trights where user_id= $Login_id))";
            }
        }else{
            $query .= " AND trs.trainer_id = $trainer_id";
        }
        if (!$WRightsFlag && $trainer_id != $Login_id) {
            $query .= " AND trs.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $Login_id)";
        }
//      if ($WeekStartDate != '' && $WeekEndDate != '') {
//          $query .= " and workshop_date between '$WeekStartDate' AND '$WeekEndDate'  ";
//      }
        $query .= " group by trs.trainer_id ) as tr on (tr.ce between hr.from_range and hr.to_range) OR (tr.ce BETWEEN hr.to_range and hr.from_range)
            group by hr.from_range ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function SynctopicResult($company_id, $Workshop_id = "") {
        $CurrentTime = date('Y-m-d H:i');
        $query = "INSERT INTO topicwise_result (company_id,workshop_id,topic_id,pre_correct,pre_played_questions,pre_total_questions,post_correct,post_played_questions,post_total_questions,pre_time_taken,
post_time_taken,liveflag,trainer_id,last_sync) 
            SELECT a.company_id,a.workshop_id,a.topic_id, SUM(a.pre_correct_ans) AS pre_correct,SUM(a.pre_played_question) AS pre_played_question, SUM(a.pre_total_questions) AS pre_total_questions,
            SUM(a.post_correct_ans) AS post_correct,SUM(a.post_played_question) AS post_played_question, SUM(a.post_total_questions) AS post_total_questions,
            IFNULL(SUM(a.pre_acrtime),0) AS pre_acrtime, IFNULL(SUM(a.post_acrtime),0) AS post_acrtime,IF(SUM(a.liveflag)>0,1,0) AS liveflag,a.trainer_id,'$CurrentTime' as lastSync
            FROM ( 
            SELECT ar.company_id,ar.trainer_id,ar.topic_id,'PRE' AS sessions,ar.correct_ans AS pre_correct_ans,ar.played_question as pre_played_question, 
            IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)) AS pre_total_questions,
            0 AS post_correct_ans,0 AS post_total_questions,0 as post_played_question, wq.workshop_id, wq.total_question, ar.correct_ans AS correct_ans,
            ar.total_seconds_taken AS pre_acrtime,0 AS post_acrtime,wq.liveflag
            FROM (
            SELECT a.user_id,a.company_id,a.workshop_id,a.topic_id, SUM(a.is_correct) AS correct_ans, COUNT(DISTINCT a.user_id) Totalusers, SUM(a.seconds) AS total_seconds_taken,a.trainer_id, COUNT(a.question_id) AS played_question
            FROM atom_results a
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
            LEFT JOIN workshop AS wk ON wk.id=a.workshop_id
            WHERE a.company_id=$company_id AND a.workshop_session='PRE' ";
        if ($Workshop_id != "") {
            $query .=" AND a.workshop_id=" . $Workshop_id;
        }
        $query .=" AND wtu.tester_id IS NULL AND wk.ce_calculated=0 AND a.user_id IN (
            SELECT user_id FROM atom_results WHERE workshop_session='POST' ";
        if ($Workshop_id != "") {
            $query .=" AND workshop_id=" . $Workshop_id;
        }else{
            $query .=" AND workshop_id=a.workshop_id";
        }
        $query .="  )
            GROUP BY a.workshop_id,a.trainer_id,a.topic_id
            ) ar
            INNER JOIN (
            SELECT c.company_id,c.workshop_id,c.topic_id, IF(CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >= '$CurrentTime', 1, 0) AS liveflag, COUNT(DISTINCT c.question_id) AS total_question,c.trainer_id
            FROM workshop_questions AS c
            INNER JOIN workshop_questionset_pre AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
            INNER JOIN workshop AS w ON w.id=c.workshop_id
            WHERE c.company_id=$company_id AND w.ce_calculated=0 ";
        if ($Workshop_id != "") {
            $query .=" AND c.workshop_id=" . $Workshop_id;
        }
        $query .="
            GROUP BY c.workshop_id,c.trainer_id,c.topic_id) wq ON wq.workshop_id=ar.workshop_id AND wq.trainer_id=ar.trainer_id AND wq.topic_id=ar.topic_id UNION ALL
            SELECT ar.company_id,ar.trainer_id,ar.topic_id,'POST' AS sessions,0 AS pre_correct_ans,0 as pre_played_question, 0 AS pre_total_questions,
            ar.correct_ans AS post_correct_ans, IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)) AS post_total_questions,ar.played_question as post_played_question,
             wq.workshop_id, wq.total_question, ar.correct_ans AS correct_ans,0 AS pre_acrtime,
            ar.total_seconds_taken AS post_acrtime,wq.liveflag
            FROM (
            SELECT a.user_id,a.trainer_id,a.company_id,a.workshop_id,a.topic_id, SUM(a.is_correct) AS correct_ans, COUNT(DISTINCT a.user_id) Totalusers, SUM(a.timer) AS total_seconds, SUM(a.seconds) AS total_seconds_taken, COUNT(a.question_id) AS played_question
            FROM atom_results a
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
            LEFT JOIN workshop AS wk ON wk.id=a.workshop_id
            WHERE a.company_id=$company_id AND a.workshop_session='POST' ";
        if ($Workshop_id != "") {
            $query .=" AND a.workshop_id=" . $Workshop_id;
        }
        $query .=" AND wtu.tester_id IS NULL AND wk.ce_calculated=0 AND a.user_id IN (
            SELECT user_id
            FROM atom_results
            WHERE workshop_session='PRE' ";
        if ($Workshop_id != "") {
            $query .=" AND workshop_id=" . $Workshop_id;
        }else{
            $query .=" AND workshop_id=a.workshop_id";
        }
        $query .=" )
            GROUP BY a.workshop_id,a.trainer_id,a.topic_id) ar
            INNER JOIN (
            SELECT c.company_id,c.trainer_id,c.workshop_id,c.topic_id, COUNT(DISTINCT c.question_id) AS total_question, 
            IF(CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >= '$CurrentTime',1,0) AS liveflag
            FROM workshop_questions AS c
            INNER JOIN workshop_questionset_post AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
            INNER JOIN workshop AS w ON w.id=c.workshop_id
            WHERE c.company_id=$company_id AND w.ce_calculated=0 ";
        if ($Workshop_id != "") {
            $query .=" AND c.workshop_id=" . $Workshop_id;
        }
        $query .=" GROUP BY c.workshop_id,c.trainer_id,c.topic_id
            ) wq ON wq.workshop_id=ar.workshop_id AND wq.trainer_id=ar.trainer_id AND wq.topic_id=ar.topic_id) a
            LEFT JOIN workshop AS w ON w.id=a.workshop_id
            WHERE a.workshop_id NOT IN(SELECT distinct workshop_id FROM topicwise_result where company_id=$company_id)
            GROUP BY a.workshop_id,a.trainer_id,a.topic_id";
        //echo $query;
        //exit;
        $this->db->query($query);
        
        //
        // $UpdateQur = "UPDATE workshop as a INNER JOIN trainer_result as wr ON wr.workshop_id=a.id 
        //         set a.ce_calculated = if(wr.liveflag=0,1,0)
        //         where a.company_id=$company_id AND a.ce_calculated=0 ";
        // $this->db->query($UpdateQur);
        
        return true;
    }
    public function SyncTrainerResult($company_id, $Workshop_id = "",$end_date="") {
        $CurrentTime = date('Y-m-d H:i');
        $query = "INSERT INTO trainer_result (company_id,workshop_id,pre_correct,pre_total_questions,post_correct,post_total_questions,pre,post,ce,pre_avgtime,post_avgtime,region_id, workshop_type,workshop_date,liveflag,trainer_id,last_sync)
            SELECT a.company_id,a.workshop_id, SUM(a.pre_correct_ans) AS pre_correct, SUM(a.pre_total_questions) AS pre_total_questions, SUM(a.post_correct_ans) AS post_correct, SUM(a.post_total_questions) AS post_total_questions,
            FORMAT(IFNULL(SUM(a.pre_accuracy),0),2) AS pre_accuracy, FORMAT(IFNULL(SUM(a.post_accuracy),0),2) AS post_accuracy, FORMAT(SUM(a.post_accuracy)- SUM(a.pre_accuracy),2) AS ce, FORMAT(IFNULL(SUM(a.pre_acrtime),0),2) AS pre_acrtime, FORMAT(IFNULL(SUM(a.post_acrtime),0),2) AS post_acrtime, w.region,w.workshop_type,w.start_date,
             IF(SUM(a.liveflag)>0,1,0) AS liveflag,a.trainer_id,'$CurrentTime' as lastSync
            FROM (
            SELECT ar.company_id,ar.trainer_id,'PRE' AS sessions,ar.correct_ans AS pre_correct_ans, IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)) AS pre_total_questions,
            0 AS post_correct_ans,0 AS post_total_questions, FORMAT(ar.correct_ans*100/ IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)),2) AS pre_accuracy, NULL AS post_accuracy, wq.workshop_id, wq.total_question, ar.correct_ans AS correct_ans,
            (ar.total_seconds_taken*100/ar.total_seconds) AS pre_acrtime,0 AS post_acrtime,wq.liveflag
            FROM (
            SELECT a.user_id,a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans, COUNT(DISTINCT a.user_id) Totalusers, SUM(a.timer) AS total_seconds, SUM(a.seconds) AS total_seconds_taken,a.trainer_id, COUNT(a.question_id) AS played_question
            FROM atom_results a
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
            LEFT JOIN workshop AS wk ON wk.id=a.workshop_id
            WHERE a.company_id=$company_id AND a.workshop_session='PRE' ";
        if ($Workshop_id != "") {
            $query .=" AND a.workshop_id=" . $Workshop_id;
        }
        $query .=" AND wtu.tester_id IS NULL AND wk.ce_calculated=0 AND a.user_id IN (
            SELECT user_id FROM atom_results WHERE workshop_session='POST' ";
        if ($Workshop_id != "") {
            $query .=" AND workshop_id=" . $Workshop_id;
        }else{
            $query .=" AND workshop_id=a.workshop_id";
        }
        $query .="  )
            GROUP BY a.workshop_id,a.trainer_id
            ) ar
            INNER JOIN (
            SELECT c.company_id,c.workshop_id, IF(CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >= '$CurrentTime', 1, 0) AS liveflag, COUNT(DISTINCT c.question_id) AS total_question,c.trainer_id
            FROM workshop_questions AS c
            INNER JOIN workshop_questionset_pre AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
            INNER JOIN workshop AS w ON w.id=c.workshop_id
            WHERE c.company_id=$company_id AND w.ce_calculated=0 ";
        if ($Workshop_id != "") {
            $query .=" AND c.workshop_id=" . $Workshop_id;
        }
        $query .="
            GROUP BY c.workshop_id,c.trainer_id) wq ON wq.workshop_id=ar.workshop_id AND wq.trainer_id=ar.trainer_id UNION ALL
            SELECT ar.company_id,ar.trainer_id,'POST' AS sessions,0 AS pre_correct_ans,0 AS pre_total_questions, 
            ar.correct_ans AS post_correct_ans, IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)) AS post_total_questions, NULL AS pre_accuracy, FORMAT(ar.correct_ans*100/ IF(wq.liveflag=1,ar.played_question,
             (wq.total_question*ar.Totalusers)),2) AS post_accuracy,
             wq.workshop_id, wq.total_question, ar.correct_ans AS correct_ans,0 AS pre_acrtime,
            (ar.total_seconds_taken*100/ar.total_seconds) AS post_acrtime,wq.liveflag
            FROM (
            SELECT a.user_id,a.trainer_id,a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans, COUNT(DISTINCT a.user_id) Totalusers, SUM(a.timer) AS total_seconds, SUM(a.seconds) AS total_seconds_taken, COUNT(a.question_id) AS played_question
            FROM atom_results a
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
            LEFT JOIN workshop AS wk ON wk.id=a.workshop_id
            WHERE a.company_id=$company_id AND a.workshop_session='POST' ";
        if ($Workshop_id != "") {
            $query .=" AND a.workshop_id=" . $Workshop_id;
        }
        $query .=" AND wtu.tester_id IS NULL AND wk.ce_calculated=0 AND a.user_id IN (
            SELECT user_id
            FROM atom_results
            WHERE workshop_session='PRE' ";
        if ($Workshop_id != "") {
            $query .=" AND workshop_id=" . $Workshop_id;
        }else{
            $query .=" AND workshop_id=a.workshop_id";
        }
        $query .=" )
            GROUP BY a.workshop_id,a.trainer_id) ar
            INNER JOIN (
            SELECT c.company_id,c.trainer_id,c.workshop_id, COUNT(DISTINCT c.question_id) AS total_question, 
            IF(CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >= '$CurrentTime',1,0) AS liveflag
            FROM workshop_questions AS c
            INNER JOIN workshop_questionset_post AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
            INNER JOIN workshop AS w ON w.id=c.workshop_id
            WHERE c.company_id=$company_id AND w.ce_calculated=0 ";
        if ($Workshop_id != "") {
            $query .=" AND c.workshop_id=" . $Workshop_id;
        }
        $query .=" GROUP BY c.workshop_id,c.trainer_id
            ) wq ON wq.workshop_id=ar.workshop_id AND wq.trainer_id=ar.trainer_id) a
            LEFT JOIN workshop AS w ON w.id=a.workshop_id
            WHERE a.workshop_id NOT IN(SELECT workshop_id FROM trainer_result)
            GROUP BY a.workshop_id,a.trainer_id";
//        echo $query;
//        exit;
        $this->db->query($query);
        
        //
        $TodayDate =date('Y-m-d H:i:s');
        if($end_date !="" && strtotime($end_date) <=strtotime($TodayDate)){
                $UpdateQur = "UPDATE workshop as a set a.ce_calculated = 1
                where a.company_id=$company_id AND a.ce_calculated=0 ";
        }else{
            $UpdateQur = "UPDATE workshop as a INNER JOIN trainer_result as wr ON wr.workshop_id=a.id 
                set a.ce_calculated = if(wr.liveflag=0,1,0)
                where a.company_id=$company_id AND a.ce_calculated=0 ";
        }
        if ($Workshop_id != "") {
            $query .=" AND a.workshop_id=" . $Workshop_id;
        }
        $this->db->query($UpdateQur);
        
        return true;
    }

    public function LiveDataSync($company_id) {
        //Update Live Data
        
        $CurrentTime = date('Y-m-d H:i');
        $lcSqlstr = "select ar.workshop_id,max(start_dttm) as lastSync,w.end_date from atom_results as ar INNER JOIN workshop as w ON w.id=ar.workshop_id
                where ar.company_id=$company_id AND w.ce_calculated=0 group by ar.workshop_id";
        //echo $lcSqlstr;
        //exit;
        $result = $this->db->query($lcSqlstr);
        $WorkshopSet = $result->result();
        if (count((array)$WorkshopSet) > 0) {
            foreach ($WorkshopSet as $value) {
                $Workshop_id = $value->workshop_id;
                $LcsqlStr = "select last_sync FROM trainer_result where workshop_id=" . $Workshop_id;
                $ObjectSet =$this->db->query($LcsqlStr);
                $StoredDataSet =$ObjectSet->row();
                if(count((array)$StoredDataSet)>0){ 
                    if((strtotime($value->lastSync) > strtotime($StoredDataSet->last_sync)) || (strtotime($value->end_date)<= strtotime($CurrentTime)) ){
                         
                        $lcDelete = "delete FROM trainer_result where workshop_id=" . $Workshop_id;
                        $this->db->query($lcDelete);
                        $lcDelete = "delete FROM topicwise_result where workshop_id=" . $Workshop_id;
                        $this->db->query($lcDelete);
                        
                        $this->SynctopicResult($company_id, $Workshop_id);
                        $this->SyncTrainerResult($company_id, $Workshop_id);
                    }
                }else{
                    $this->SynctopicResult($company_id, $Workshop_id);
                    $this->SyncTrainerResult($company_id, $Workshop_id,$value->end_date);
                }
            }
        }
    }

    public function SyncWorshopResult($company_id) {
        $CurrentTime = date('Y-m-d H:i');
        $query = "INSERT INTO workshop_result (company_id,workshop_id,pre_correct,pre_total_questions,post_correct,post_total_questions,pre,post,ce,pre_avgtime,post_avgtime,region_id,
            workshop_type,workshop_date,liveflag,total_users) 
            SELECT a.company_id,a.workshop_id, SUM(a.pre_correct_ans) AS pre_correct, SUM(a.pre_total_questions) AS pre_total_questions, SUM(a.post_correct_ans) AS post_correct,
            SUM(a.post_total_questions) AS post_total_questions, FORMAT(IFNULL(SUM(a.pre_accuracy),0),2) AS pre_accuracy, FORMAT(IFNULL(SUM(a.post_accuracy),0),2) AS post_accuracy, FORMAT(SUM(a.post_accuracy)- SUM(a.pre_accuracy),2) AS ce, FORMAT(IFNULL(SUM(a.pre_acrtime),0),2) AS pre_acrtime, FORMAT(IFNULL(SUM(a.post_acrtime),0),2) AS post_acrtime,w.region,w.workshop_type,w.start_date,
            IF(SUM(a.liveflag)>0,1,0) AS liveflag,a.total_users
            FROM (
            SELECT ar.company_id,'PRE' AS sessions,ar.Totalusers AS total_users,ar.correct_ans AS pre_correct_ans, IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)) AS pre_total_questions, 0 AS post_correct_ans,
            0 AS post_total_questions, FORMAT(ar.correct_ans*100/ IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)),2) AS pre_accuracy, NULL AS post_accuracy, wq.workshop_id, wq.total_question, ar.correct_ans AS correct_ans,
             (ar.total_seconds_taken*100/ar.total_seconds) AS pre_acrtime,0 AS post_acrtime, wq.liveflag
            FROM (
            SELECT a.user_id,a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans, COUNT(DISTINCT a.user_id) Totalusers, SUM(a.timer) AS total_seconds, SUM(a.seconds) AS total_seconds_taken, COUNT(a.question_id) AS played_question
            FROM atom_results a
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
            LEFT JOIN workshop AS wk ON wk.id=a.workshop_id
            WHERE a.company_id=$company_id AND a.workshop_session='PRE' AND wtu.tester_id IS NULL AND wk.ce_calculated=0 AND a.user_id IN (
            SELECT user_id
            FROM atom_results
            WHERE workshop_id=a.workshop_id AND workshop_session='POST')
            GROUP BY a.workshop_id) ar
            INNER JOIN (
            SELECT c.company_id,c.workshop_id, COUNT(DISTINCT c.question_id) AS total_question,
            IF(CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >= '$CurrentTime',1,0) AS liveflag
            FROM workshop_questions AS c
            INNER JOIN workshop_questionset_pre AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
            INNER JOIN workshop AS w ON w.id=c.workshop_id
            WHERE c.company_id=$company_id AND w.ce_calculated=0
            GROUP BY c.workshop_id) wq ON wq.workshop_id=ar.workshop_id UNION ALL
            SELECT ar.company_id,'POST' AS sessions,0 AS total_users, 0 AS pre_correct_ans,0 AS pre_total_questions,
             ar.correct_ans AS post_correct_ans, IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)) AS post_total_questions, NULL AS pre_accuracy, FORMAT(ar.correct_ans*100/ IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)),2) AS post_accuracy,
             wq.workshop_id, wq.total_question, ar.correct_ans AS correct_ans,0 AS pre_acrtime,(ar.total_seconds_taken*100/ar.total_seconds) AS post_acrtime, wq.liveflag
            FROM (
            SELECT a.user_id,a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans, COUNT(DISTINCT a.user_id) Totalusers, SUM(a.timer) AS total_seconds, SUM(a.seconds) AS total_seconds_taken, COUNT(a.question_id) AS played_question
            FROM atom_results a
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
            LEFT JOIN workshop AS wk ON wk.id=a.workshop_id
            WHERE a.company_id=$company_id AND a.workshop_session='POST' AND wtu.tester_id IS NULL AND wk.ce_calculated=0 AND a.user_id IN (
            SELECT user_id
            FROM atom_results
            WHERE workshop_id=a.workshop_id AND workshop_session='PRE')
            GROUP BY a.workshop_id) ar
            INNER JOIN (
            SELECT c.company_id,c.workshop_id, COUNT(DISTINCT c.question_id) AS total_question, IF(CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >= '$CurrentTime',1,0) AS liveflag
            FROM workshop_questions AS c
            INNER JOIN workshop_questionset_post AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
            INNER JOIN workshop AS w ON w.id=c.workshop_id
            WHERE c.company_id=$company_id AND w.ce_calculated=0
            GROUP BY c.workshop_id) wq ON wq.workshop_id=ar.workshop_id) a
            LEFT JOIN workshop AS w ON w.id=a.workshop_id
            WHERE a.workshop_id NOT IN(
            SELECT workshop_id
            FROM workshop_result)
            GROUP BY a.workshop_id";
//        echo $query;
//        exit;
        $this->db->query($query);

        //Update Live Data
        $query2 = "UPDATE workshop_result AS wr
            INNER JOIN(
            SELECT a.company_id,a.workshop_id, SUM(a.pre_correct_ans) AS pre_correct, SUM(a.pre_total_questions) AS pre_total_questions, SUM(a.post_correct_ans) AS post_correct, SUM(a.post_total_questions) AS post_total_questions, FORMAT(IFNULL(SUM(a.pre_accuracy),0),2) AS pre_accuracy, FORMAT(IFNULL(SUM(a.post_accuracy),0),2) AS post_accuracy, FORMAT(SUM(a.post_accuracy)- SUM(a.pre_accuracy),2) AS ce, FORMAT(IFNULL(SUM(a.pre_acrtime),0),2) AS pre_acrtime, FORMAT(IFNULL(SUM(a.post_acrtime),0),2) AS post_acrtime,w.region,w.workshop_type,w.start_date, IF(SUM(a.liveflag)>0,1,0) AS liveflag,a.total_users
            FROM (
            SELECT ar.company_id,'PRE' AS sessions,ar.Totalusers AS total_users,ar.correct_ans AS pre_correct_ans, IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)) AS pre_total_questions, 0 AS post_correct_ans,
            0 AS post_total_questions, FORMAT(ar.correct_ans*100/ IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)),2) AS pre_accuracy, NULL AS post_accuracy, wq.workshop_id, wq.total_question, ar.correct_ans AS correct_ans,
             (ar.total_seconds_taken*100/ar.total_seconds) AS pre_acrtime,0 AS post_acrtime, wq.liveflag
            FROM (
            SELECT a.user_id,a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans, COUNT(DISTINCT a.user_id) Totalusers, SUM(a.timer) AS total_seconds, SUM(a.seconds) AS total_seconds_taken, COUNT(a.question_id) AS played_question
            FROM atom_results a
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
            INNER JOIN workshop_result AS wk ON wk.workshop_id=a.workshop_id

            WHERE a.company_id=$company_id AND a.workshop_session='PRE' AND wtu.tester_id IS NULL AND wk.liveflag=1 AND a.user_id IN (
            SELECT user_id
            FROM atom_results
            WHERE workshop_id=a.workshop_id AND workshop_session='POST')
            GROUP BY a.workshop_id) ar
            INNER JOIN (
            SELECT c.company_id,c.workshop_id, COUNT(DISTINCT c.question_id) AS total_question, IF(CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) >= '$CurrentTime',1,0) AS liveflag
            FROM workshop_questions AS c
            INNER JOIN workshop_questionset_pre AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id AND d.active=1
            INNER JOIN workshop AS w ON w.id=c.workshop_id
            INNER JOIN workshop_result AS wr ON wr.workshop_id=c.workshop_id
            WHERE c.company_id=$company_id AND w.ce_calculated=0 AND wr.liveflag=1
            GROUP BY c.workshop_id) wq ON wq.workshop_id=ar.workshop_id UNION ALL
            SELECT ar.company_id,'POST' AS sessions,0 AS total_users, 0 AS pre_correct_ans,0 AS pre_total_questions,
             ar.correct_ans AS post_correct_ans, IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)) AS post_total_questions, NULL AS pre_accuracy, FORMAT(ar.correct_ans*100/ IF(wq.liveflag=1,ar.played_question,(wq.total_question*ar.Totalusers)),2) AS post_accuracy,
             wq.workshop_id, wq.total_question, ar.correct_ans AS correct_ans,0 AS pre_acrtime,(ar.total_seconds_taken*100/ar.total_seconds) AS post_acrtime, wq.liveflag
            FROM (
            SELECT a.user_id,a.company_id,a.workshop_id, SUM(a.is_correct) AS correct_ans, COUNT(DISTINCT a.user_id) Totalusers, SUM(a.timer) AS total_seconds, SUM(a.seconds) AS total_seconds_taken, COUNT(a.question_id) AS played_question
            FROM atom_results a
            LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=a.workshop_id AND wtu.tester_id=a.user_id
            INNER JOIN workshop_result AS wr ON wr.workshop_id=a.workshop_id
            WHERE a.company_id=$company_id AND a.workshop_session='POST' AND wtu.tester_id IS NULL AND wr.liveflag=1 AND a.user_id IN (
            SELECT user_id
            FROM atom_results
            WHERE workshop_id=a.workshop_id AND workshop_session='PRE')
            GROUP BY a.workshop_id) ar
            INNER JOIN (
            SELECT c.company_id,c.workshop_id, COUNT(DISTINCT c.question_id) AS total_question, IF(CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) >= '$CurrentTime',1,0) AS liveflag
            FROM workshop_questions AS c
            INNER JOIN workshop_questionset_post AS d ON c.questionset_id=d.questionset_id AND c.workshop_id=d.workshop_id
            INNER JOIN workshop AS w ON w.id=c.workshop_id
            INNER JOIN workshop_result AS wr ON wr.workshop_id=c.workshop_id
            WHERE c.company_id=$company_id AND wr.liveflag=1
            GROUP BY c.workshop_id) wq ON wq.workshop_id=ar.workshop_id) a
            LEFT JOIN workshop AS w ON w.id=a.workshop_id
            WHERE a.workshop_id IN(SELECT workshop_id FROM workshop_result) 
            GROUP BY a.workshop_id) AS td ON td.workshop_id=wr.workshop_id 
            SET wr.pre_correct=td.pre_correct,wr.pre_total_questions=td.pre_total_questions,wr.post_correct=td.post_correct, wr.post_total_questions=td.post_total_questions, wr.pre=td.pre_accuracy,wr.post=td.post_accuracy,wr.ce=td.ce, wr.liveflag=td.liveflag
            WHERE wr.liveflag=1";
        $this->db->query($query2);
        //Update TrainerData
        $UpdateQur = "UPDATE workshop as a LEFT JOIN workshop_result as wr ON wr.workshop_id=a.id 
                set a.ce_calculated = if(wr.liveflag=0,1,0)
                where a.company_id=$company_id AND a.ce_calculated=0 ";
        $this->db->query($UpdateQur);
        return true;
    }
    public function requiredSyncData($company_id) {
       
        $ObjSet = $this->db->query("select id from workshop where id IN(select distinct workshop_id FROM atom_results "
        . "where company_id=$company_id ) AND ce_calculated=0 AND company_id=" . $company_id);
        $data = $ObjSet->row();
        $SyncFlag = false;
        if (count((array)$data) > 0) {
            $SyncFlag = true;
        }
        return $SyncFlag;
    }
    public function getWorkshopType($company_id='',$trainer_id='0') {
        $query = " select wq.workshop_id,w.workshop_type as wtype_id,wt.workshop_type 
                    from workshop_questions wq
                    left join workshop w
                    on w.id = wq.workshop_id                    
                    left join workshoptype_mst wt
                    on wt.id = w.workshop_type
                    where wq.company_id =". $company_id;
                    if($trainer_id !='0'){
                       $query .= " and wq.trainer_id =". $trainer_id; 
}
                    $query .= " group by w.workshop_type ";
        $result = $this->db->query($query);
        return $result->result();
    }
    public function getRegion($company_id='',$trainer_id='') {
        $query = " select wq.workshop_id,w.region as region_id,r.region_name 
                    from workshop_questions wq
                    left join workshop w
                    on w.id = wq.workshop_id
                    left join region r
                    on r.id = w.region                    
                    where wq.company_id =". $company_id;
                    if($trainer_id !='0'){
                       $query .= " and wq.trainer_id =". $trainer_id; 
                    }    
                    $query .= " group by w.region ";
        $result = $this->db->query($query);
        return $result->result();
    }
}
