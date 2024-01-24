<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Traineequestionset_wise_report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function LoadDataTable($dtWhere,$dtOrder, $dtLimit, $RightsFlag, $WRightsFlag,$dtWhere1) {
        $login_id = $this->mw_session['user_id'];        
        $query = " SELECT du.company_id,cm.company_name,du.user_id,du.emp_id,rg.region_name AS trainee_region,du.area as area,
                        CONCAT(du.firstname,' ',du.lastname) AS traineename,wr.region_name as workshop_region,
                        wt.workshop_type as workshop_type,w.workshop_name,
                        CONCAT(FORMAT(sum(pre),2),'%') as pre,CONCAT(FORMAT(sum(post),2),'%') as post,
                        sum(preplayed) as preplayed,sum(postplayed) as postplayed
                        FROM 
                            (SELECT ar.company_id,ar.user_id,ar.workshop_id,ar.questionset_id,		 
                                    FORMAT(SUM(ar.is_correct)*100/ COUNT(ar.id),2) AS pre,0 as post,1 as preplayed,0 as postplayed 
                                    FROM atom_results ar                                    
                                    $dtWhere ";
                                    if($dtWhere <> ''){
                                        $query .= " and ar.workshop_session='PRE' ";
                                    }else{
                                        $query .= " where ar.workshop_session='PRE' ";
                                    }
                                    if (!$WRightsFlag) {
                                        $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
                                    }
                                    if (!$RightsFlag) {
                                        $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                                    }
                                    $query .= " GROUP BY ar.user_id ";

                    $query .= " UNION ALL

                            SELECT ar.company_id,ar.user_id,ar.workshop_id,ar.questionset_id, 
                                    0 as pre, FORMAT(SUM(ar.is_correct)*100/ COUNT(ar.id),2) AS post,0 as preplayed,1 as postplayed                                    
                                    FROM atom_results ar                                    
                                    $dtWhere ";
                                    if($dtWhere <> ''){
                                        $query .= " and ar.workshop_session='POST' ";
                                    }else{
                                        $query .= " where ar.workshop_session='POST' ";
                                    }  
                                    if (!$WRightsFlag) {
                                        $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
                                    }
                                    if (!$RightsFlag) {
                                        $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                                    }
                                    $query .= " GROUP BY ar.user_id ";	 
                    $query .= " ) A
                                    LEFT JOIN device_users du ON du.user_id=A.user_id
                                    LEFT JOIN region rg ON rg.id=du.region_id
                                    LEFT JOIN company cm ON cm.id=A.company_id
                                    LEFT JOIN workshop w ON w.id=A.workshop_id
                                    LEFT JOIN region wr ON wr.id=w.region                                    
                                    LEFT JOIN workshoptype_mst AS wt ON wt.id=w.workshop_type                                    
                                    LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
                                    LEFT JOIN question_set qt ON qt.id=A.questionset_id
                            $dtWhere1 GROUP BY user_id  ";
                    $query_count = $query;
                    $query .= " $dtOrder $dtLimit ";  
                    //echo $query;exit;
                    $result = $this->db->query($query);
                    $data['ResultSet'] = $result->result_array();
                    $data['dtPerPageRecords'] = $result->num_rows();

                    $result = $this->db->query($query_count);
                    $data_array = $result->result_array();
                    $total = count((array)$data_array);
                    $data['dtTotalRecords'] = $total;
                    return $data;                                        
    }
    public function exportToExcel($dtWhere, $RightsFlag, $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = " SELECT du.company_id,cm.company_name,du.user_id,du.emp_id,rg.region_name AS trainee_region,du.area as area,
                        CONCAT(du.firstname,' ',du.lastname) AS traineename,wr.region_name as workshop_region,
                        wt.workshop_type as workshop_type,w.workshop_name,
                        CONCAT(FORMAT(sum(pre),2),'%') as pre,CONCAT(FORMAT(sum(post),2),'%') as post,
                        sum(preplayed) as preplayed,sum(postplayed) as postplayed
                        FROM 
                            (SELECT ar.company_id,ar.user_id,ar.workshop_id,ar.questionset_id,		 
                                    FORMAT(SUM(ar.is_correct)*100/ COUNT(ar.id),2) AS pre,0 as post,
                                    1 as preplayed,0 as postplayed
                                    FROM atom_results ar                                    
                                    $dtWhere ";
                                    if($dtWhere <> ''){
                                        $query .= " and ar.workshop_session='PRE' ";
                                    }else{
                                        $query .= " where ar.workshop_session='PRE' ";
                                    }
                                    if (!$WRightsFlag) {
                                        $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
                                    }
                                    if (!$RightsFlag) {
                                        $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                                    }
                                    $query .= " GROUP BY ar.user_id ";

                    $query .= " UNION ALL

                            SELECT ar.company_id,ar.user_id,ar.workshop_id,ar.questionset_id, 
                                    0 as pre, FORMAT(SUM(ar.is_correct)*100/ COUNT(ar.id),2) AS post,
                                    0 as preplayed,1 as postplayed
                                    FROM atom_results ar                                    
                                    $dtWhere ";
                                    if($dtWhere <> ''){
                                        $query .= " and ar.workshop_session='POST' ";
                                    }else{
                                        $query .= " where ar.workshop_session='POST' ";
                                    }  
                                    if (!$WRightsFlag) {
                                        $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
                                    }
                                    if (!$RightsFlag) {
                                        $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                                    }
                                    $query .= " GROUP BY ar.user_id ";	 
                    $query .= " ) A
                                    LEFT JOIN device_users du ON du.user_id=A.user_id
                                    LEFT JOIN region rg ON rg.id=du.region_id
                                    LEFT JOIN company cm ON cm.id=A.company_id
                                    LEFT JOIN workshop w ON w.id=A.workshop_id
                                    LEFT JOIN region wr ON wr.id=w.region                                    
                                    LEFT JOIN workshoptype_mst AS wt ON wt.id=w.workshop_type                                    
                                    LEFT JOIN designation_trainee dt ON dt.id=du.designation_id
                                    LEFT JOIN question_set qt ON qt.id=A.questionset_id
                            GROUP BY user_id ";                                         
                    //echo $query;exit;
                    $result = $this->db->query($query);                                    
                    return $result->result(); 
    }

    public function workshopwise_data($company_id, $workshop_id) {
        $LcSqlStr = "SELECT qt.id,qt.description as topic_name FROM workshop_questions wq LEFT JOIN question_topic qt ON qt.id=wq.topic_id and qt.company_id=wq.company_id "
                . " where wq.company_id=" . $company_id . " and wq.workshop_id=" . $workshop_id . " group by qt.id ";

        $query = $this->db->query($LcSqlStr);
        $row = $query->result();
        return $row;
    }
    public function QsetTable($company_id,$workshop_id='',$trainee_id=''){        
        $LcSqlStr = "SELECT qt.title as questionset,ar.questionset_id,du.user_id
                FROM atom_results ar
                LEFT JOIN device_users du ON du.user_id=ar.user_id		                                		
                LEFT JOIN question_set qt ON qt.id=ar.questionset_id
                WHERE ar.company_id = $company_id AND ar.workshop_id = $workshop_id AND ar.workshop_session='POST' ";
                if($trainee_id !=''){
                    $LcSqlStr .=" AND ar.user_id=".$trainee_id;
                }
            $LcSqlStr .=" GROUP BY ar.questionset_id ";            
            $result = $this->db->query($LcSqlStr);        
            $data['qset'] = $result->result_array();
            return $data;        
    }
    public function QsetWisePost($company_id,$workshop_id='',$trainee_id='',$qset_id=''){
        $LcSqlStr = "SELECT ar.questionset_id,            
                CONCAT(FORMAT(SUM(ar.is_correct)*100/ COUNT(ar.id),2),'%') AS qset_post	
                FROM atom_results ar                		                                		                
                WHERE ar.company_id = $company_id AND ar.workshop_id = $workshop_id AND ar.workshop_session='POST' ";
                if($trainee_id !=''){
                    $LcSqlStr .=" AND ar.user_id = $trainee_id ";
                }
                if($qset_id !=''){
                    $LcSqlStr .=" AND ar.questionset_id = $qset_id ";
                }
            $LcSqlStr .=" GROUP BY ar.questionset_id ";                          
            $result = $this->db->query($LcSqlStr);
            $data_res = $result->row();
            $qpost='Not Played';
            if(count((array)$data_res)>0){
               $qpost = $data_res->qset_post;
            }
            return $qpost;
    }
}
