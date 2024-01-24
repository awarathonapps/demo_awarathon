<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Trainee_played_result_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    public function LoadDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = " select ar.id,c.company_name,w.workshop_name,rg.region_name,rgn.region_name as tregion_name,
                    ar.workshop_session,qs.title as questionset,
                    concat(cu.first_name,' ',cu.last_name) as trainername,qt.description as topicname,
                    qst.description as subtopicname,concat('(',ar.question_id,') ',q.question_title) as question_title,
                    concat(du.firstname,' ',du.lastname) as traineename,du.user_id,
                    srg.description as sub_region,wst.description as workshop_subtype,wt.workshop_type,dt.description as designation,
                    CASE ar.correct_answer
                                        WHEN 'a' THEN q.option_a
                                        WHEN 'b' THEN q.option_b
                                        WHEN 'c' THEN q.option_c
                                        WHEN 'd' THEN q.option_d
                                        ELSE ''
                                        END as correct_answer,
                    CASE ar.option_clicked
                                        WHEN 'a' THEN q.option_a
                                        WHEN 'b' THEN q.option_b
                                        WHEN 'c' THEN q.option_c
                                        WHEN 'd' THEN q.option_d
                                        ELSE ''
                                        END as user_answer,ar.is_correct,
                    CASE 1
                                        WHEN ar.is_correct THEN 'Correct'
                                        WHEN ar.is_wrong THEN 'Wrong'
                                        WHEN ar.is_timeout THEN 'Time Out'                   
                                        ELSE ''
                                        END as question_result,                    
                    DATE_FORMAT(ar.start_dttm, '%d/%m/%Y %H:%i:%s') as start_dttm,
                    DATE_FORMAT(ar.end_dttm, '%d/%m/%Y %H:%i:%s') as end_dttm,
                    ar.seconds,ar.timer from atom_results ar
                    LEFT join company c on c.id=ar.company_id
                    LEFT join workshop w on w.id=ar.workshop_id
                    LEFT join question_set qs on qs.id=ar.questionset_id
                    LEFT join company_users cu on cu.userid=ar.trainer_id
                    LEFT join question_topic qt on qt.id=ar.topic_id
                    LEFT join question_subtopic qst on qst.id=ar.subtopic_id
                    LEFT join questions q on q.id=ar.question_id
                    LEFT join device_users du on du.user_id=ar.user_id
                    LEFT join region rgn on rgn.id=du.region_id
                    LEFT join region rg on rg.id=w.region
                    LEFT join workshoptype_mst wt on wt.id=w.workshop_type
                    LEFT join workshopsubregion_mst srg on srg.id=w.workshopsubregion_id
                    LEFT join workshopsubtype_mst wst on wst.id=w.workshopsubtype_id
                    LEFT join designation_trainee dt on dt.id=du.designation_id $dtWhere ";
//            if($dtWhere!=""){
//                $query .=" AND du.istester=0 ";
//            }else{
//                $query .=" WHERE du.istester=0 ";
//            }            
        if(!$WRightsFlag){
           $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }   
        $query .= " $dtOrder $dtLimit ";
//           echo $query;exit;  
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = $result->num_rows();
        
        $query_count = "SELECT COUNT(ar.id) as counter from atom_results ar
                    LEFT join company c on c.id=ar.company_id
                    LEFT join workshop w on w.id=ar.workshop_id
                    LEFT join question_set qs on qs.id=ar.questionset_id
                    LEFT join company_users cu on cu.userid=ar.trainer_id
                    LEFT join question_topic qt on qt.id=ar.topic_id
                    LEFT join question_subtopic qst on qst.id=ar.subtopic_id
                    LEFT join questions q on q.id=ar.question_id
                    LEFT join device_users du on du.user_id=ar.user_id
                    LEFT join region rgn on rgn.id=du.region_id
                    LEFT join region rg on rg.id=w.region
                    LEFT join workshoptype_mst wt on wt.id=w.workshop_type
                    LEFT join workshopsubregion_mst srg on srg.id=w.workshopsubregion_id
                    LEFT join workshopsubtype_mst wst on wst.id=w.workshopsubtype_id
                    LEFT join designation_trainee dt on dt.id=du.designation_id $dtWhere ";

        $result = $this->db->query($query_count);
        $data_array = $result->row();
        $data['dtTotalRecords'] = $data_array->counter;
        return $data;
    }
    public function exportToExcel($exportWhere='',$RightsFlag,$WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $excel_data = " select ar.id,c.company_name,w.workshop_name,rg.region_name,rgn.region_name as tregion_name,ar.workshop_session,qs.title as questionset,
                    concat(cu.first_name,' ',cu.last_name) as trainername,qt.description as topicname,
                    qst.description as subtopicname,ar.question_id,q.question_title as question_title,
                    concat(du.firstname,' ',du.lastname) as traineename,du.user_id,dt.description as designation,
                    srg.description as sub_region,wst.description as workshop_subtype,wt.workshop_type,
                    CASE ar.correct_answer
                                        WHEN 'a' THEN q.option_a
                                        WHEN 'b' THEN q.option_b
                                        WHEN 'c' THEN q.option_c
                                        WHEN 'd' THEN q.option_d
                                        ELSE ''
                                        END as correct_answer,
                    CASE ar.option_clicked
                                        WHEN 'a' THEN q.option_a
                                        WHEN 'b' THEN q.option_b
                                        WHEN 'c' THEN q.option_c
                                        WHEN 'd' THEN q.option_d
                                        ELSE ''
                                        END as user_answer,ar.is_correct,
                    CASE 1
                                        WHEN ar.is_correct THEN 'Correct'
                                        WHEN ar.is_wrong THEN 'Wrong'
                                        WHEN ar.is_timeout THEN 'Time Out'                   
                                        ELSE ''
                                        END as question_result,                    
                    DATE_FORMAT(ar.start_dttm, '%d/%m/%Y %H:%i:%s') as start_dttm,
                    DATE_FORMAT(ar.end_dttm, '%d/%m/%Y %H:%i:%s') as end_dttm,ar.seconds 
                    from atom_results ar
                    LEFT join company c on c.id=ar.company_id
                    LEFT join workshop w on w.id=ar.workshop_id
                    LEFT join question_set qs on qs.id=ar.questionset_id
                    LEFT join company_users cu on cu.userid=ar.trainer_id
                    LEFT join question_topic qt on qt.id=ar.topic_id
                    LEFT join question_subtopic qst on qst.id=ar.subtopic_id
                    LEFT join questions q on q.id=ar.question_id 
                    LEFT join device_users du on du.user_id=ar.user_id
                    LEFT join region rgn on rgn.id=du.region_id
                    LEFT join region rg on rg.id=w.region
                    LEFT join workshoptype_mst wt on wt.id=w.workshop_type
                    LEFT join workshopsubregion_mst srg on srg.id=w.workshopsubregion_id
                    LEFT join workshopsubtype_mst wst on wst.id=w.workshopsubtype_id
                    LEFT join designation_trainee dt on dt.id=du.designation_id $exportWhere ";
//            if($exportWhere!=""){
//                $excel_data .=" AND du.istester=0 ";
//            }else{
//                $excel_data .=" WHERE du.istester=0 ";
//            }             
        if(!$WRightsFlag){
           $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }  
        $query = $this->db->query($excel_data);
        return $query->result();
    }
//    public function get_traineeData($company_id ='',$trainer_id ='') {  
//        
//        $querystr ="select ar.user_id,                    
//                    concat(du.firstname,' ',du.lastname) as traineename
//                    from atom_results ar
//                    inner join company c on c.id=ar.company_id                    
//                    inner join device_users du on du.user_id=ar.user_id
//                    where ar.company_id=$company_id ";
//                if($trainer_id!=""){
//                    $querystr .=" AND ar.trainer_id = ".$trainer_id;
//                }    
//                $querystr .=" group by ar.user_id";
//                        
//        $result = $this->db->query($querystr);        
//        return $result->result();
//    }
    public function get_traineeData($company_id,$trainer_id,$RightFlag,$tregion_id='') { 
        $login_id = $this->mw_session['user_id'];
        $querystr ="select distinct ar.user_id,concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename from atom_results ar                  
            inner join device_users du on du.user_id=ar.user_id  where ar.company_id=" .$company_id;
            if($trainer_id !='' || $trainer_id !=null){
                $querystr .=" AND ar.trainer_id = ".$trainer_id ;
            }
            if($tregion_id !='' || $tregion_id !=null){
                $querystr .=" AND du.region_id=$tregion_id ";
            } 
        if(!$RightFlag){
            $querystr .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }    
            $querystr .=" order by du.firstname";  
            
        $result = $this->db->query($querystr);        
        return $result->result();
    }
    public function workshoptypewise_data($company_id,$workshoptype_id='',$region_id=''){
        if($company_id !=''){
            $querystr = " select id,workshop_name from workshop where company_id = $company_id ";
        }
        if($workshoptype_id !='' && $workshoptype_id !=null){
            $querystr .= " and workshop_type=$workshoptype_id ";
        }
        if($region_id !='' && $region_id !=null){
            $querystr .= " and region = $region_id ";
        }
        $result = $this->db->query($querystr);        
        return $result->result();
    }
}
