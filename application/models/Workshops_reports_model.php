<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Workshops_reports_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    // ==========================================//* trainee_played_result Start here 10-04-2023 Nirmal Gajjar *//=====================================================================================================================================================================================
    public function Tpr_LoadDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag)
    {
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
        if (!$WRightsFlag) {
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
    public function export_Tpr_ToExcel($exportWhere = '', $RightsFlag, $WRightsFlag)
    {
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
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query = $this->db->query($excel_data);
        return $query->result();
    }

    // ==========================================//* trainee_played_result End *//=====================================================================================================================================================================================

    // ==========================================//* trainee_wise_summary_report Start here 10-04-2023 Nirmal Gajjar *//=====================================================================================================================================================================================
    public function TraineeSummaryLoadDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT cm.company_name,ar.user_id,concat(du.firstname,' ',du.lastname) as traineename,count(DISTINCT ar.workshop_id) AS TOTALworkshop,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,tr.region_name,
                  FORMAT(sum(ar.seconds)/COUNT(ar.id),2) as avg_resp_time,dt.description as designation
                  from atom_results ar
                    INNER JOIN device_users du ON du.user_id=ar.user_id
                    LEFT JOIN region tr ON tr.id=du.region_id
                    INNER JOIN company cm ON cm.id=ar.company_id 
                    LEFT JOIN workshop w ON w.id=ar.workshop_id
                    LEFT JOIN workshoptype_mst as wt ON wt.id=w.workshop_type 
                    LEFT join designation_trainee dt on dt.id=du.designation_id $dtWhere ";
        //            if($dtWhere!=""){
        //                $query .=" AND du.istester=0 ";
        //            }else{
        //                $query .=" WHERE du.istester=0 ";
        //            }
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $query .= " group by ar.user_id ";

        $query_count = $query . " $dthaving ";

        $query .= " $dthaving $dtOrder $dtLimit ";

        //        if($dtOrder ==""){
        //            $query .= " order by result desc  ";
        //        }
        //$query .= "  $dtLimit ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = $result->num_rows();

        $Countset = $this->db->query($query_count);
        $data['dtTotalRecords'] = $Countset->num_rows();
        return $data;
    }

    public function TraineeSummaryExportToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag, $dtOrder)
    {

        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT cm.company_name,ar.user_id,concat(du.firstname,' ',du.lastname) as traineename,count(DISTINCT ar.workshop_id) AS TOTALworkshop,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,tr.region_name,
                  FORMAT(sum(ar.seconds)/COUNT(ar.id),2) as avg_resp_time,dt.description as designation
                  from atom_results ar
                        INNER JOIN device_users du ON du.user_id=ar.user_id
                        LEFT JOIN region tr ON tr.id=du.region_id
                        INNER JOIN company cm ON cm.id=ar.company_id
                        LEFT JOIN workshop w ON w.id=ar.workshop_id
                        LEFT JOIN workshoptype_mst as wt ON wt.id=w.workshop_type
                        LEFT join designation_trainee dt on dt.id=du.designation_id $dtWhere ";
        //        if($dtWhere!=""){
        //            $excel_data .=" AND du.istester=0 ";
        //        }else{
        //            $excel_data .=" WHERE du.istester=0 ";
        //        }
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $excel_data .= " group by ar.user_id $dtOrder ";
        $excel_data .= " $dthaving ";
        //echo $excel_data;exit;
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* trainee_wise_summary_report End *//=====================================================================================================================================================================================


    // ==========================================//* traineetopic_wise_report Start here 11-04-2023 Nirmal Gajjar *//=====================================================================================================================================================================================
    public function Ttqwr_LoadDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag, $report_type)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT cm.company_name,ar.user_id,du.emp_id,concat(du.firstname,' ',du.lastname) as traineename, w.workshop_name,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,
                  wt.workshop_type,wr.region_name as workshop_region,rg.region_name as trainee_region, 
                  wsr.description as workshop_subregion,wst.description as workshop_subtype,dt.description as designation ";
        if ($report_type == 1) {
            $query .= " ,qt.description as title ";
        } else if ($report_type == 2) {
            $query .= ", qt.title ";
        } else {
            $query .= ", count(distinct ar.questionset_id ) as title ";
        }
        $query .= "
                from atom_results ar	
                LEFT JOIN device_users du ON du.user_id=ar.user_id
                LEFT JOIN region rg on rg.id=du.region_id
                LEFT JOIN company cm ON cm.id=ar.company_id 
                LEFT JOIN workshop w ON w.id=ar.workshop_id
                LEFT JOIN region wr on wr.id=w.region 
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshoptype_mst as wt ON wt.id=w.workshop_type 
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                LEFT join designation_trainee dt on dt.id=du.designation_id ";

        if ($report_type == 1) {
            $query .= " LEFT JOIN question_topic qt ON qt.id=ar.topic_id ";
        } else if ($report_type == 2) {
            $query .= " LEFT JOIN question_set qt ON qt.id=ar.questionset_id ";
        }
        $query .= "  $dtWhere ";
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $query .= " group by ar.user_id,ar.workshop_id ";
        if ($report_type == 1) {
            $query .= ", ar.topic_id ";
        } else if ($report_type == 2) {
            $query .= ", ar.questionset_id ";
        }
        $query .= " $dthaving ";
        $query_count = $query;
        $query .= " $dtOrder $dtLimit ";
        //        echo $query;exit;           
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = $result->num_rows();


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total = count((array)$data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function export_Ttqwr_ToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag, $report_type = 1)
    {
        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT cm.company_name,ar.user_id,du.emp_id,concat(du.firstname,' ',du.lastname) as traineename, w.workshop_name,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,
                  wt.workshop_type,wr.region_name as workshop_region,rg.region_name as trainee_region, 
                  wsr.description as workshop_subregion,wst.description as workshop_subtype,dt.description as designation ";
        if ($report_type == 1) {
            $excel_data .= " ,qt.description as title ";
        } else if ($report_type == 2) {
            $excel_data .= ", qt.title ";
        } else {
            $excel_data .= ", count(distinct ar.questionset_id ) as title ";
        }
        $excel_data .= " from atom_results ar	
                LEFT JOIN device_users du ON du.user_id=ar.user_id
                LEFT join region rg on rg.id=du.region_id
                LEFT JOIN company cm ON cm.id=ar.company_id 
                LEFT JOIN workshop w ON w.id=ar.workshop_id
                LEFT join region wr on wr.id=w.region
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshoptype_mst as wt ON wt.id=w.workshop_type 
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                LEFT join designation_trainee dt on dt.id=du.designation_id ";
        if ($report_type == 1) {
            $excel_data .= " LEFT JOIN question_topic qt ON qt.id=ar.topic_id ";
        } else if ($report_type == 2) {
            $excel_data .= " LEFT JOIN question_set qt ON qt.id=ar.questionset_id ";
        }
        $excel_data .= "  $dtWhere ";
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
        }
        $excel_data .= " group by ar.user_id,ar.workshop_id ";
        if ($report_type == 1) {
            $excel_data .= ", ar.topic_id ";
        } else if ($report_type == 2) {
            $excel_data .= ", ar.questionset_id ";
        }
        $excel_data .= " $dthaving order by workshop_name,traineename ";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* traineetopic_wise_report End  *//=====================================================================================================================================================================================

    // ==========================================//* trainer_wise_summary_report Start here 11-04-2023 Nirmal Gajjar *//=====================================================================================================================================================================================
    public function TrainerSummaryLoadDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT cm.company_name,ar.trainer_id,CONCAT(cu.first_name,' ', cu.last_name) as trainername,count(DISTINCT ar.user_id) AS TOTALtrainee,
                  count(DISTINCT ar.workshop_id) AS TOTALworkshop,
                  count(DISTINCT ar.topic_id) as TOTALtopic, count( DISTINCT if(ar.subtopic_id>0, ar.subtopic_id,null )) as TOTALsubtopic,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result
                  from atom_results ar	
                        INNER JOIN workshop w on w.id=ar.workshop_id
                        LEFT JOIN company_users cu ON cu.userid=ar.trainer_id
                        LEFT JOIN company cm ON cm.id=ar.company_id $dtWhere   ";
        //        if($dtWhere!=""){
        //            $query .=" AND du.istester=0 ";
        //        }else{
        //            $query .=" WHERE du.istester=0 ";
        //        }
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $query .= " group by ar.trainer_id ";

        $query_count = $query . " $dthaving $dtOrder ";
        $query .= " $dthaving $dtOrder $dtLimit ";
        //        echo $query;exit;           
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = $result->num_rows();


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total = count($data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function TrainerSummaryExportToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT cm.company_name,ar.trainer_id,CONCAT(cu.first_name,' ', cu.last_name) as trainername,count(DISTINCT ar.user_id) AS TOTALtrainee,count(DISTINCT ar.workshop_id) AS TOTALworkshop,
                  count(DISTINCT ar.topic_id) as TOTALtopic, count( DISTINCT if(ar.subtopic_id>0, ar.subtopic_id,null )) as TOTALsubtopic,
                  count(ar.id) as played_que,sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,
                  concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result
                  from atom_results ar	
                        INNER JOIN workshop w on w.id=ar.workshop_id
                        LEFT JOIN company_users cu ON cu.userid=ar.trainer_id
                        LEFT JOIN company cm ON cm.id=ar.company_id $dtWhere ";
        //            if($dtWhere!=""){
        //                $excel_data .=" AND du.istester=0 ";
        //            }else{
        //                $excel_data .=" WHERE du.istester=0 ";
        //            }            
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $excel_data .= " group by ar.trainer_id ";

        $excel_data .= " $dthaving ";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* trainer_wise_summary_report End  *//=====================================================================================================================================================================================


    // ==========================================//* trainer_consolidated_report_tab Start here 11-04-2023 Nirmal Gajjar  *//=====================================================================================================================================================================================
    public function TrainerConsolidatedLoadDataTable($dtWhere, $dtOrder, $dtLimit, $dtHaving, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $query = " SELECT ar.id,c.company_name,w.workshop_name,r.region_name,wt.workshop_type,ar.user_id,
		 CONCAT(cu.first_name,' ',cu.last_name) AS trainername,qt.description AS topicname, 
		 qst.description AS subtopicname , count(distinct ar.question_id) as total_question,
		 count(distinct ar.user_id) as total_trainee_played,count(ar.question_id) as total_question_played,
		 sum(ar.is_correct) as total_correct_ans ,wsr.description as workshop_subregion,wst.description as workshop_subtype,
		 FORMAT(IFNULL((sum(ar.is_correct) * 100 / (count(ar.question_id)) ),0),2) as result		 	 			
				FROM atom_results ar
						INNER JOIN company c ON c.id=ar.company_id
						INNER JOIN workshop w ON w.id=ar.workshop_id
						INNER JOIN region r ON r.id = w.region
						INNER JOIN workshoptype_mst wt ON wt.id = w.workshop_type
						INNER JOIN company_users cu ON cu.userid=ar.trainer_id
						INNER JOIN question_topic qt ON qt.id=ar.topic_id
						INNER JOIN question_subtopic qst ON qst.id=ar.subtopic_id 
                                                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                                                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $dtWhere ";
        //            if($dtWhere!=""){
        //                $query .=" AND du.istester=0 ";
        //            }else{
        //                $query .=" WHERE du.istester=0 ";
        //            }             
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }

        $query_count = $query . " group by ar.company_id,ar.workshop_id,ar.topic_id,ar.subtopic_id,ar.trainer_id $dtHaving $dtOrder ";

        $query .= " group by ar.company_id,ar.workshop_id,ar.topic_id,ar.subtopic_id,ar.trainer_id $dtHaving $dtOrder $dtLimit ";



        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total = count($data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function TrainerConsolidatedExportToExcel($exportWhere = '', $exportHaving = '', $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $excel_data = " SELECT ar.id,c.company_name,w.workshop_name,r.region_name,wt.workshop_type,ar.user_id,
		 CONCAT(cu.first_name,' ',cu.last_name) AS trainername,qt.description AS topicname, 
		 qst.description AS subtopicname , count( distinct ar.question_id) as total_question,
		 count(distinct ar.user_id) as total_trainee_played,count( ar.question_id) as total_question_played,
		 sum(ar.is_correct) as total_correct_ans ,wsr.description as workshop_subregion,wst.description as workshop_subtype,
		 FORMAT(IFNULL((sum(ar.is_correct) * 100 / (count(  ar.question_id) ) ),0),2) as result		 	 			
				FROM atom_results ar
						INNER JOIN company c ON c.id=ar.company_id
						INNER JOIN workshop w ON w.id=ar.workshop_id
						INNER JOIN region r ON r.id = w.region
						INNER JOIN workshoptype_mst wt ON wt.id = w.workshop_type
						INNER JOIN company_users cu ON cu.userid=ar.trainer_id
						INNER JOIN question_topic qt ON qt.id=ar.topic_id
						INNER JOIN question_subtopic qst ON qst.id=ar.subtopic_id 
                                                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                                                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $exportWhere ";
        //            if($exportWhere !=""){
        //                $excel_data .=" AND du.istester=0 ";
        //            }else{
        //                $excel_data .=" WHERE du.istester=0 ";
        //            }             
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $excel_data .= " group by ar.company_id,ar.workshop_id,w.region,ar.topic_id,ar.subtopic_id,ar.trainer_id $exportHaving order by workshop_name,trainername";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* trainer_consolidated_report_tab End*//=====================================================================================================================================================================================


    // ==========================================//* workshop_wise_report_tab Start here 11-04-2023 Nirmal Gajjar  *//=====================================================================================================================================================================================
    public function WorkshopWiseLoadDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT cm.company_name,r.region_name,wm.workshop_type,w.workshop_name,count(DISTINCT ar.questionset_id) as questionset,count(ar.id) as played_que,
            sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,wsr.description as workshop_subregion,wst.description as workshop_subtype
            from atom_results ar
            LEFT JOIN workshop w ON w.id=ar.workshop_id
            LEFT JOIN workshoptype_mst wm ON wm.id=w.workshop_type
            LEFT JOIN region r ON r.id=w.region  
            LEFT JOIN company cm ON cm.id=ar.company_id 
            LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
            LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $dtWhere ";
        //        if($dtWhere!=""){
        //            $query .=" AND du.istester=0 ";
        //        }else{
        //            $query .=" WHERE du.istester=0 ";
        //        }
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $query .= "group by ar.workshop_id";
        $query_count = $query . " $dthaving $dtOrder ";
        $query .= " $dthaving $dtOrder $dtLimit ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total = count($data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function WorkshopWiseExportToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT cm.company_name,r.region_name,wm.workshop_type,w.workshop_name,count(DISTINCT ar.questionset_id) as questionset,count(ar.id) as played_que,
            sum(ar.is_correct) as correct,sum(ar.is_wrong) as wrong,concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,wsr.description as workshop_subregion,wst.description as workshop_subtype

            from atom_results ar
            LEFT JOIN workshop w ON w.id=ar.workshop_id
            LEFT JOIN workshoptype_mst wm ON wm.id=w.workshop_type
            LEFT JOIN region r ON r.id=w.region  
            LEFT JOIN company cm ON cm.id=ar.company_id 
            LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
            LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $dtWhere ";
        //        if($dtWhere!=""){
        //            $excel_data .=" AND du.istester=0 ";
        //        }else{
        //            $excel_data .=" WHERE du.istester=0 ";
        //        }
        if (!$WRightsFlag) {
            $excel_data .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $excel_data .= " group by ar.workshop_id";

        $excel_data .= " $dthaving ";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* workshop_wise_report_tab End*//=====================================================================================================================================================================================


    // ==========================================//* question_wise_report_tab Start here 11-04-2023 Nirmal Gajjar*//=====================================================================================================================================================================================

    public function QuestionWiseLoadDataTable($dtWhere, $dtWhere2, $dtOrder, $dtLimit, $dtHaving = '')
    {
        $query = "SELECT a.*,c.company_name,qs.title AS questionset,r.region_name,wrk.question_title,wt.workshop_type,
                    CASE wrk.correct_answer WHEN 'a' THEN wrk.option_a WHEN 'b' THEN wrk.option_b WHEN 'c' THEN wrk.option_c WHEN 'd' THEN wrk.option_d ELSE '' END AS correct_answer
                FROM (
                SELECT wq.company_id,wq.question_id,wq.workshop_id,wq.questionset_id,w.workshop_name,w.workshop_type as workshop_type_id ,w.region, COUNT(DISTINCT wq.user_id) AS no_of_trainee_played, SUM(wq.is_correct) AS no_of_trainee_ans,
                FORMAT((SUM(wq.is_correct) * 100 / COUNT(wq.user_id)),2) AS result,wsr.description as workshop_subregion,wst.description as workshop_subtype
                FROM atom_results wq
                INNER JOIN workshop w ON w.id=wq.workshop_id
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type 
                $dtWhere
                GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.question_id $dtHaving
                ORDER BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.question_id
                ) AS a
                INNER JOIN company c ON c.id=a.company_id
                INNER JOIN question_set qs ON qs.id = a.questionset_id
                INNER JOIN workshoptype_mst wt ON wt.id = a.workshop_type_id
                INNER JOIN region r ON r.id = a.region
                LEFT JOIN workshop_questions wrk ON wrk.question_id=a.question_id AND wrk.workshop_id=a.workshop_id AND wrk.company_id=a.company_id AND
                wrk.questionset_id=a.questionset_id
                $dtWhere2 $dtOrder $dtLimit ";
        // echo $query;exit;
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);

        $query_count = " SELECT count(a.question_id) as counter
                FROM (
                SELECT wq.company_id,wq.question_id,wq.workshop_id,wq.questionset_id,w.workshop_name,w.workshop_type as workshop_type_id ,w.region, COUNT(DISTINCT wq.user_id) AS no_of_trainee_played, SUM(wq.is_correct) AS no_of_trainee_ans,
                FORMAT((SUM(wq.is_correct) * 100 / COUNT(wq.user_id)),2) AS result
                FROM atom_results wq
                INNER JOIN workshop w ON w.id=wq.workshop_id
                $dtWhere
                GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.question_id $dtHaving
                ORDER BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.question_id
                ) AS a
                INNER JOIN company c ON c.id=a.company_id
                INNER JOIN question_set qs ON qs.id = a.questionset_id
                INNER JOIN workshoptype_mst wt ON wt.id = a.workshop_type_id
                INNER JOIN region r ON r.id = a.region
                LEFT JOIN workshop_questions wrk ON wrk.question_id=a.question_id AND wrk.workshop_id=a.workshop_id AND wrk.company_id=a.company_id AND
                wrk.questionset_id=a.questionset_id
                $dtWhere2";
        $result = $this->db->query($query_count);
        $data_array = $result->row();
        $total = $data_array->counter;
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function QuestionWiseExportToExcel($dtWhere = '', $dtHaving = '')
    {
        $excel_data = " SELECT a.*,c.company_name,qs.title AS questionset,r.region_name,wrk.question_title,wt.workshop_type,
                    CASE wrk.correct_answer WHEN 'a' THEN wrk.option_a WHEN 'b' THEN wrk.option_b WHEN 'c' THEN wrk.option_c WHEN 'd' THEN wrk.option_d ELSE '' END AS correct_answer
                FROM (
                SELECT wq.company_id,wq.question_id,wq.workshop_id,wq.questionset_id,w.workshop_name,w.workshop_type as workshop_type_id ,w.region, COUNT(DISTINCT wq.user_id) AS no_of_trainee_played, SUM(wq.is_correct) AS no_of_trainee_ans,
                FORMAT((SUM(wq.is_correct) * 100 / COUNT(wq.user_id)),2) AS result,wsr.description as workshop_subregion,wst.description as workshop_subtype
                FROM atom_results wq
                INNER JOIN workshop w ON w.id=wq.workshop_id
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type 
                $dtWhere
                GROUP BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.question_id $dtHaving
                ORDER BY wq.company_id,wq.workshop_id,wq.questionset_id,wq.question_id
                ) AS a
                INNER JOIN company c ON c.id=a.company_id
                INNER JOIN question_set qs ON qs.id = a.questionset_id
                INNER JOIN workshoptype_mst wt ON wt.id = a.workshop_type_id
                INNER JOIN region r ON r.id = a.region
                LEFT JOIN workshop_questions wrk ON wrk.question_id=a.question_id AND wrk.workshop_id=a.workshop_id AND wrk.company_id=a.company_id AND
                wrk.questionset_id=a.questionset_id order by workshop_id desc,question_id  ";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* question_wise_report_tab End*//=====================================================================================================================================================================================

    // ==========================================//* imei_report_tab Start here 11-04-2023 Nirmal Gajjar*//=====================================================================================================================================================================================

    public function getWorkshopList($Company_id = "", $region_id = "")
    {
        $lcSqlStr = "select a.workshop_id,b.workshop_name FROM workshop_registered_users a "
            . "LEFT JOIN workshop as b "
            . "ON b.id=a.workshop_id where 1=1";
        if ($Company_id != "") {
            $lcSqlStr .= " AND b.company_id=" . $Company_id;
        }
        if ($region_id != "") {
            $lcSqlStr .= " AND b.region=" . $region_id;
        }
        $lcSqlStr .= " group by a.workshop_id order by b.start_date desc,b.workshop_name ";
        //echo $lcSqlStr;
        $result = $this->db->query($lcSqlStr);
        return $result->result();
    }

    public function ImeiDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT u.user_id,u.firstname,u.lastname,u.emp_id,u.area,DATE_FORMAT(di.info_dttm,'%d-%m-%Y %h:%i %p') as info_dttm, u.employment_year,
                u.education_background,u.department,di.model,di.platform,di.imei,di.serial, u.region_id,u.email,u.mobile,u.status,
                u.istester,rg.region_name,dr.description AS designation
                FROM device_users AS u
                LEFT JOIN device_info AS di ON di.user_id= u.user_id
                LEFT JOIN region AS rg ON rg.id=u.region_id
                LEFT JOIN designation_trainee AS dr ON dr.id=u.designation_id $dtWhere ";
        if (!$RightsFlag) {
            $query .= " AND (u.trainer_id = $login_id OR u.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $query .= " $dtOrder $dtLimit ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);

        $query_count = "SELECT count(u.user_id) as total_count
                FROM device_users AS u
                LEFT JOIN device_info AS di ON di.user_id= u.user_id
                LEFT JOIN region AS rg ON rg.id=u.region_id
                LEFT JOIN designation_trainee AS dr ON dr.id=u.designation_id $dtWhere ";
        if (!$RightsFlag) {
            $query_count .= " AND (u.trainer_id = $login_id OR u.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $result = $this->db->query($query_count);
        $total = $result->row();
        $data['dtTotalRecords'] = $total->total_count;
        return $data;
    }
    public function ImeiExportToExcel($dtWhere, $RightsFlag, $WRightsFlag)
    {
        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT u.user_id,u.firstname,u.lastname,u.emp_id,u.area,DATE_FORMAT(di.info_dttm,'%d-%m-%Y %h:%i %p') as info_dttm, u.employment_year,
                u.education_background,u.department,di.model,di.platform,di.imei,di.serial, u.region_id,u.email,u.mobile,u.status,
                u.istester,rg.region_name,dr.description AS designation
                FROM device_users AS u
                LEFT JOIN device_info AS di ON di.user_id= u.user_id
                LEFT JOIN region AS rg ON rg.id=u.region_id
                LEFT JOIN designation_trainee AS dr ON dr.id=u.designation_id $dtWhere ";
        if (!$RightsFlag) {
            $excel_data .= " AND (u.trainer_id = $login_id OR u.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $excel_data .= " ORDER BY u.user_id DESC,di.id desc";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    // ==========================================//* imei_report_tab End*//=====================================================================================================================================================================================


}
