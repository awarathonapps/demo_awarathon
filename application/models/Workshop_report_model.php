<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Workshop_report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function AttendanceLoadDataTable($dtWhere, $dtOrder, $dtLimit, $dtWhere2="") {
        $login_id = $this->mw_session['user_id'];
        $query = " SELECT  du.user_id,du.emp_id,DATE_FORMAT(a.registered_date_time,'%d-%m-%Y') as registration_date ,a.workshop_id,
                if(count(qf1.id) >0, sum(a.pre_session),3) as pre_session,
                if(count(qf2.id) >0, sum(a.post_session),3) as post_session,
                if(count(wf1.id) >0, sum(a.pre_feedback),3) as pre_feedback,
                if(count(wf2.id) >0, sum(a.post_feedback),3) as post_feedback,
                w.workshop_name,wm.workshop_type,tr.region_name as trainee_region,wsr.description as workshop_subregion,wst.description as workshop_subtype,
                r.region_name,s.store_name,CONCAT(du.firstname,' ',du.lastname) AS traineename,du.mobile,du.email,dt.description as designation
                 FROM (
                SELECT wru.user_id AS trainee_id,wru.registered_date_time,wru.workshop_session,wru.workshop_id,
                  (CASE WHEN  wru.all_questions_fired = 1 THEN '1' 
                    WHEN  wru.all_questions_fired = 0 && COUNT(ar.user_id)>0 THEN '2' ELSE '0' END) AS pre_session,
				 		
                 (CASE WHEN  wru.all_feedbacks_fired = 1 THEN '1' 
                    WHEN  wru.all_feedbacks_fired = 0 && COUNT(af.user_id)>0 THEN '2' ELSE '0' END) AS pre_feedback,
                 0 post_session, 0 AS post_feedback
				FROM workshop_registered_users wru
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                
                LEFT JOIN (
                SELECT wru.user_id,wru.workshop_id FROM atom_results as wru 
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                $dtWhere AND workshop_session='PRE' GROUP BY user_id,workshop_id
                ) AS ar ON ar.user_id=wru.user_id AND ar.workshop_id=w.id
                LEFT JOIN (
                SELECT wru.user_id,wru.workshop_id FROM atom_feedback as wru 
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                 $dtWhere AND workshop_session='PRE' GROUP BY user_id,workshop_id
                ) AS af ON af.user_id=wru.user_id AND af.workshop_id=w.id
                 $dtWhere AND wru.workshop_session = 'PRE'
                GROUP BY wru.user_id,wru.workshop_id UNION ALL
                SELECT wru.user_id AS trainee_id,wru.registered_date_time,wru.workshop_session,wru.workshop_id,
                 0 pre_session, 0 AS pre_feedback,
                 (CASE WHEN  wru.all_questions_fired = 1 THEN '1' 
                    WHEN wru.all_questions_fired = 0 && COUNT(ar.user_id)>0 THEN '2' ELSE '0' END) AS post_session, 

                 (CASE WHEN  wru.all_feedbacks_fired = 1   THEN '1' 
                    WHEN wru.all_feedbacks_fired = 0 && COUNT(af.user_id)>0 THEN '2' ELSE '0' END) AS post_feedback
                FROM workshop_registered_users wru
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                LEFT JOIN (
                SELECT wru.user_id,wru.workshop_id FROM atom_results as wru 
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                $dtWhere AND workshop_session='POST' GROUP BY user_id,workshop_id
                ) AS ar ON ar.user_id=wru.user_id AND ar.workshop_id=w.id
                LEFT JOIN (
                SELECT wru.user_id,wru.workshop_id FROM atom_feedback as wru 
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                 $dtWhere AND workshop_session='POST' GROUP BY user_id,workshop_id
                ) AS af ON af.user_id=wru.user_id AND af.workshop_id=w.id
                 $dtWhere AND wru.workshop_session = 'POST'
                GROUP BY wru.user_id,wru.workshop_id
                UNION ALL 
                    SELECT wru.user_id AS trainee_id,' ' as registered_date_time,'pre' as workshop_session,wru.workshop_id,
                    0 AS pre_session, 0 AS pre_feedback,0 post_session  , 0 as post_feedback
                    FROM workshop_users wru
                    INNER JOIN workshop as w ON w.id=wru.workshop_id
                    $dtWhere
                )
                as a
                LEFT JOIN workshop w ON w.id=a.workshop_id
                LEFT JOIN workshop_feedbackset_pre as wf1 ON wf1.workshop_id=w.id
                LEFT JOIN workshop_questionset_pre as qf1 ON qf1.workshop_id=w.id
                LEFT JOIN workshop_feedbackset_post as wf2 ON wf2.workshop_id=w.id
                LEFT JOIN workshop_questionset_post as qf2 ON qf2.workshop_id=w.id
					LEFT JOIN workshoptype_mst wm ON wm.id=w.workshop_type
					LEFT JOIN region r ON r.id=w.region
                LEFT JOIN device_users du ON du.user_id=a.trainee_id
                LEFT JOIN region tr ON tr.id=du.region_id
                LEFT JOIN store_mst as s ON s.id=du.store_id 
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                LEFT join designation_trainee dt on dt.id=du.designation_id $dtWhere2";               
               
                $query .= " group by a.workshop_id,a.trainee_id  ";
        $query .= "  $dtOrder $dtLimit ";
//        echo $query;
//        exit;
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count((array)$data['ResultSet']);
					
        $query_count ="select count(distinct a.user_id) as total_users FROM (
                SELECT wru.user_id,wru.workshop_id FROM workshop_registered_users wru
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                $dtWhere
                GROUP BY wru.user_id,wru.workshop_id UNION ALL
					
                SELECT wru.user_id, wru.workshop_id FROM workshop_users as wru
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                $dtWhere
                ) as a
                LEFT JOIN workshop w ON w.id=a.workshop_id 
                LEFT JOIN workshoptype_mst wm ON wm.id=w.workshop_type
                 LEFT JOIN region r ON r.id=w.region
                 LEFT JOIN device_users du ON du.user_id=a.user_id
                 LEFT JOIN region tr ON tr.id=du.region_id
                 LEFT JOIN store_mst as s ON s.id=du.store_id
                 LEFT join designation_trainee dt on dt.id=du.designation_id $dtWhere2";               
              
        $RowSet = $this->db->query($query_count);
        $data_array = $RowSet->row();
        $total = $data_array->total_users;
        $data['dtTotalRecords'] = $total;
        return $data;
    }
		
    public function AttendanceExportToExcel($dtWhere,$dtWhere2="") {
        $login_id = $this->mw_session['user_id'];
        $excel_data = " SELECT du.user_id,du.emp_id,DATE_FORMAT(a.registered_date_time,'%d-%m-%Y') as registration_date ,a.workshop_id,
                if(count(qf1.id) >0, sum(a.pre_session),3) as pre_session,
                if(count(qf2.id) >0, sum(a.post_session),3) as post_session,
                if(count(wf1.id) >0, sum(a.pre_feedback),3) as pre_feedback,
                if(count(wf2.id) >0, sum(a.post_feedback),3) as post_feedback,
                w.workshop_name,wm.workshop_type,tr.region_name as trainee_region,wsr.description as workshop_subregion,wst.description as workshop_subtype,
                r.region_name,s.store_name,CONCAT(du.firstname,' ',du.lastname) AS traineename,du.mobile,du.email,dt.description as designation
                 FROM (
                SELECT wru.user_id AS trainee_id,wru.registered_date_time,wru.workshop_session,wru.workshop_id,
                  (CASE WHEN  wru.all_questions_fired = 1 THEN '1' 
                    WHEN  wru.all_questions_fired = 0 && COUNT(ar.user_id)>0 THEN '2' ELSE '0' END) AS pre_session,

                 (CASE WHEN  wru.all_feedbacks_fired = 1 THEN '1' 
                    WHEN  wru.all_feedbacks_fired = 0 && COUNT(af.user_id)>0 THEN '2' ELSE '0' END) AS pre_feedback,
                 0 post_session, 0 AS post_feedback
				FROM workshop_registered_users wru
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
					
                LEFT JOIN (
                SELECT wru.user_id,wru.workshop_id FROM atom_results as wru 
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                $dtWhere AND workshop_session='PRE' GROUP BY user_id,workshop_id
                ) AS ar ON ar.user_id=wru.user_id AND ar.workshop_id=w.id
                LEFT JOIN (
                SELECT wru.user_id,wru.workshop_id FROM atom_feedback as wru 
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                 $dtWhere AND workshop_session='PRE' GROUP BY user_id,workshop_id
                ) AS af ON af.user_id=wru.user_id AND af.workshop_id=w.id
                 $dtWhere AND wru.workshop_session = 'PRE'
                GROUP BY wru.user_id,wru.workshop_id UNION ALL
                SELECT wru.user_id AS trainee_id,wru.registered_date_time,wru.workshop_session,wru.workshop_id,
                 0 pre_session, 0 AS pre_feedback,
                 (CASE WHEN  wru.all_questions_fired = 1 THEN '1' 
                    WHEN wru.all_questions_fired = 0 && COUNT(ar.user_id)>0 THEN '2' ELSE '0' END) AS post_session, 
			
                 (CASE WHEN  wru.all_feedbacks_fired = 1   THEN '1' 
                    WHEN wru.all_feedbacks_fired = 0 && COUNT(af.user_id)>0 THEN '2' ELSE '0' END) AS post_feedback
                FROM workshop_registered_users wru
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                LEFT JOIN (
                SELECT wru.user_id,wru.workshop_id FROM atom_results as wru 
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                $dtWhere AND workshop_session='POST' GROUP BY user_id,workshop_id
                ) AS ar ON ar.user_id=wru.user_id AND ar.workshop_id=w.id
                LEFT JOIN (
                SELECT wru.user_id,wru.workshop_id FROM atom_feedback as wru 
                INNER JOIN workshop AS w ON w.id=wru.workshop_id
                 $dtWhere AND workshop_session='POST' GROUP BY user_id,workshop_id
                ) AS af ON af.user_id=wru.user_id AND af.workshop_id=w.id
                 $dtWhere AND wru.workshop_session = 'POST'
                GROUP BY wru.user_id,wru.workshop_id
                UNION ALL 
                    SELECT wru.user_id AS trainee_id,' ' as registered_date_time,'pre' as workshop_session,wru.workshop_id,
                    0 AS pre_session, 0 AS pre_feedback,0 post_session  , 0 as post_feedback
                    FROM workshop_users wru
                    INNER JOIN workshop as w ON w.id=wru.workshop_id
                    $dtWhere
                )
                as a
                LEFT JOIN workshop w ON w.id=a.workshop_id
                LEFT JOIN workshop_feedbackset_pre as wf1 ON wf1.workshop_id=w.id
                LEFT JOIN workshop_questionset_pre as qf1 ON qf1.workshop_id=w.id
                LEFT JOIN workshop_feedbackset_post as wf2 ON wf2.workshop_id=w.id
                LEFT JOIN workshop_questionset_post as qf2 ON qf2.workshop_id=w.id
                LEFT JOIN workshoptype_mst wm ON wm.id=w.workshop_type
                LEFT JOIN region r ON r.id=w.region
                LEFT JOIN device_users du ON du.user_id=a.trainee_id
                LEFT JOIN region tr ON tr.id=du.region_id
                LEFT JOIN store_mst as s ON s.id=du.store_id 
                LEFT JOIN workshopsubregion_mst as wsr ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                LEFT JOIN workshopsubtype_mst as wst ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type
                LEFT join designation_trainee dt on dt.id=du.designation_id $dtWhere2";               
                $excel_data .= " group by a.workshop_id,a.trainee_id  order by traineename";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    public function StorewiseDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT cm.company_name,r.region_name,wm.workshop_type,w.workshop_name,count(DISTINCT ar.user_id) as participant,count(ar.id) as played_que,
            sum(ar.is_correct) as correct,sum(ar.is_wrong)+sum(ar.is_timeout) as wrong,concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,
            s.store_name
            from atom_results ar
            LEFT JOIN device_users du ON du.user_id=ar.user_id
            LEFT JOIN store_mst as s ON s.id=du.store_id
            LEFT JOIN workshop w ON w.id=ar.workshop_id
            LEFT JOIN workshoptype_mst wm ON wm.id=w.workshop_type
            LEFT JOIN region r ON r.id=w.region 
            LEFT JOIN company cm ON cm.id=ar.company_id $dtWhere ";
        if (!$WRightsFlag) {
            $query .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (ar.trainer_id = $login_id OR ar.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $query .= "group by ar.workshop_id,du.store_id";
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
    public function storeWiseExportToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT cm.company_name,r.region_name,wm.workshop_type,w.workshop_name,count(DISTINCT ar.user_id) as participant,count(ar.id) as played_que,
            sum(ar.is_correct) as correct,sum(ar.is_wrong)+sum(ar.is_timeout) as wrong,concat(format(sum(ar.is_correct)*100/count(ar.id),2),'%') as result,
            s.store_name
            from atom_results ar
            LEFT JOIN device_users du ON du.user_id=ar.user_id
            LEFT JOIN store_mst as s ON s.id=du.store_id
            LEFT JOIN workshop w ON w.id=ar.workshop_id
					LEFT JOIN workshoptype_mst wm ON wm.id=w.workshop_type
					LEFT JOIN region r ON r.id=w.region
            LEFT JOIN company cm ON cm.id=ar.company_id $dtWhere ";
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
        $excel_data .= " group by ar.workshop_id,du.store_id";

        $excel_data .= " $dthaving order by ar.workshop_id ";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
    public function WorkshopWiseLoadDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag) {
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

    public function WorkshopWiseExportToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag) {
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

    public function getWorkshopList($Company_id = "", $region_id = "") {
        $lcSqlStr = "select a.workshop_id,b.workshop_name FROM workshop_registered_users a "
                . "LEFT JOIN workshop as b "
                . "ON b.id=a.workshop_id where 1=1";
        if ($Company_id != "") {
            $lcSqlStr .=" AND b.company_id=" . $Company_id;
        }
        if ($region_id != "") {
            $lcSqlStr .=" AND b.region=" . $region_id;
        }
        $lcSqlStr .=" group by a.workshop_id order by b.start_date desc,b.workshop_name ";
        //echo $lcSqlStr;
        $result = $this->db->query($lcSqlStr);
        return $result->result();
    }

    public function TraineeSummaryLoadDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag) {
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

    public function TraineeSummaryExportToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag,$dtOrder) {
        
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

    public function TrainerSummaryLoadDataTable($dtWhere, $dthaving = '', $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag) {
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

    public function TrainerSummaryExportToExcel($dtWhere, $dthaving = '', $RightsFlag, $WRightsFlag) {
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
        $excel_data .=" group by ar.trainer_id ";

        $excel_data .= " $dthaving ";
        $query = $this->db->query($excel_data);
        return $query->result();
    }

    public function QuestionWiseLoadDataTable($dtWhere,$dtWhere2, $dtOrder, $dtLimit, $dtHaving = '') {
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

    public function QuestionWiseExportToExcel($dtWhere = '', $dtHaving = '') {
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

    public function TraineeConsolidatedLoadDataTable($dtWhere, $dtOrder, $dtLimit) {

        $query = " select ar.id,c.company_name,w.workshop_name,ar.workshop_session,qs.title as questionset,
                    concat(cu.first_name,' ',cu.last_name) as trainername,qt.description as topicname,
                    qst.description as subtopicname,concat('(',ar.question_id,') ',q.question_title) as question_title,
                    concat(du.firstname,' ',du.lastname) as traineename,
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
                    inner join company c on c.id=ar.company_id
                    inner join workshop w on w.id=ar.workshop_id
                    inner join question_set qs on qs.id=ar.questionset_id
                    inner join company_users cu on cu.userid=ar.trainer_id
                    inner join question_topic qt on qt.id=ar.topic_id
                    inner join question_subtopic qst on qst.id=ar.subtopic_id
                    inner join questions q on q.id=ar.question_id
                    inner join device_users du on du.user_id=ar.user_id";

        $query_count = $query . " $dtWhere $dtOrder ";
        $query .= " $dtWhere $dtOrder $dtLimit ";

        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total = count($data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function TraineeConsolidatedExportToExcel($exportWhere = '') {
        $excel_data = " select ar.id,c.company_name,w.workshop_name,ar.workshop_session,qs.title as questionset,
                    concat(cu.first_name,' ',cu.last_name) as trainername,qt.description as topicname,
                    qst.description as subtopicname,concat('(',ar.question_id,') ',q.question_title) as question_title,
                    concat(du.firstname,' ',du.lastname) as traineename,
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
                    inner join company c on c.id=ar.company_id
                    inner join workshop w on w.id=ar.workshop_id
                    inner join question_set qs on qs.id=ar.questionset_id
                    inner join company_users cu on cu.userid=ar.trainer_id
                    inner join question_topic qt on qt.id=ar.topic_id
                    inner join question_subtopic qst on qst.id=ar.subtopic_id
                    inner join questions q on q.id=ar.question_id 
                    inner join device_users du on du.user_id=ar.user_id
                    ";
        $excel_data .= " $exportWhere";
        $query = $this->db->query($excel_data);
        return $query->result();
    }

    public function get_traineeData($company_id = '',$RightFlag='1', $trainer_id = '0',$tregion_id='0',$workshop_type="0",$workshop_id="",$workshop_session="") {

        $login_id = $this->mw_session['user_id'];
        $querystr ="select distinct ar.user_id,concat(du.firstname,' ',du.lastname,' (',du.email,' )') as traineename 
            from atom_results ar LEFT JOIN workshop as w ON w.id =ar.workshop_id
            inner join device_users du on du.user_id=ar.user_id where ar.company_id=" .$company_id;
        if ($workshop_type != "0") {
            $querystr .=" AND w.workshop_type = " . $workshop_type;
        }
        if ($workshop_id != "") {
            $querystr .=" AND ar.workshop_id = " . $workshop_id;
        }
        if ($trainer_id != "0") {
            $querystr .=" AND ar.trainer_id = " . $trainer_id;
        }
        if($tregion_id !='0'){
            $querystr .=" AND du.region_id=$tregion_id ";
        }
        if($workshop_session !=''){
            $querystr .=" AND ar.workshop_session='".$workshop_session."'";
        }
        if(!$RightFlag){
            $querystr .= " AND ar.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        } 
        //$querystr .=" group by ar.user_id";
//        echo $querystr;
//        exit;
        $result = $this->db->query($querystr);
        return $result->result();
    }

    public function TrainerConsolidatedLoadDataTable($dtWhere, $dtOrder, $dtLimit, $dtHaving, $RightsFlag, $WRightsFlag) {
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

    public function TrainerConsolidatedExportToExcel($exportWhere = '', $exportHaving = '', $RightsFlag, $WRightsFlag) {
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

    public function FeedbackConsolidatedLoadDataTable($dtWhere, $dtOrder, $dtLimit, $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "  select af.company_id,af.user_id,af.workshop_id,af.feedbackset_id,
                    af.option_a,af.option_b,af.option_c,af.option_d,af.option_e,af.option_f,
                    af.weight_a,af.weight_b,af.weight_c,af.weight_d,af.weight_e,af.weight_f,
                    fq.option_a as doption_a,fq.option_b as doption_b,fq.option_c as doption_c,fq.option_d as doption_d,fq.option_e as doption_e,fq.option_f as doption_f,af.feedback_answer,af.question_type,af.weightage as text_weightage,
                    c.company_name,
                    af.feedback_id,concat(du.firstname,' ',du.lastname) as trainee_name,
                    du.email,du.mobile,w.workshop_name,f.title as feedback_set,fq.question_title, 
                    wt.workshop_type,wr.region_name as workshop_region,wsr.description as workshop_subregion,wst.description as workshop_subtype,
                    ft.description as feedbacktype,fst.description as feedbacksubtype,rg.region_name as tregion_name,
                    DATE_FORMAT(af.start_dttm, '%d/%m/%Y %H:%i:%s') as start_dttm,
                    DATE_FORMAT(af.end_dttm, '%d/%m/%Y %H:%i:%s')as end_dttm,af.seconds,
                    if(af.option_a = '1' || af.option_b = '1' || af.option_c = '1' || af.option_d = '1' || af.option_e = '1' || af.option_f = '1'|| af.feedback_answer != '' ,'Responce','Time Out') as feedback_status,
                    IF(fq.multiple_allow = 1,(ifnull(fq.weight_a,0)+ifnull(fq.weight_b,0)+ifnull(fq.weight_c,0)+ifnull(fq.weight_d,0)+ifnull(fq.weight_e,0)+ifnull(fq.weight_f,0)),GREATEST(ifnull(fq.weight_a,0),ifnull(fq.weight_b,0),ifnull(fq.weight_c,0),ifnull(fq.weight_d,0),ifnull(fq.weight_e,0),ifnull(fq.weight_f,0))) as max_weightage
                        from atom_feedback af
                            inner join device_users du
                                on du.user_id = af.user_id
                            inner join workshop w
                                on w.id = af.workshop_id
                            inner join feedback f
                                on f.id = af.feedbackset_id
                            inner join feedback_questions fq
                                on fq.id = af.feedback_id
                            inner join company c
				on c.id = af.company_id 
                            INNER JOIN feedback_type ft 
                                ON ft.id = af.feedback_type_id
                            INNER JOIN feedback_subtype fst 
                                ON fst.id = af.feedback_subtype_id 
                            LEFT join region rg 
                                ON rg.id = du.region_id 
                            LEFT JOIN region wr 
                                ON wr.id=w.region 
                            LEFT JOIN workshopsubregion_mst as wsr 
                                ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                            LEFT JOIN workshoptype_mst as wt 
                                ON wt.id=w.workshop_type 
                            LEFT JOIN workshopsubtype_mst as wst 
                                ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $dtWhere ";
//            if($dtWhere!=""){
//                $query .=" AND du.istester=0 ";
//            }else{
//                $query .=" WHERE du.istester=0 ";
//            }
        if (!$WRightsFlag) {
            $query .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        $query_count = $query . " group by af.workshop_id,af.feedback_id,af.user_id $dtOrder ";
//        $query .= " $dtWhere ";
        $query .= " group by af.workshop_id,af.feedback_id,af.user_id $dtOrder $dtLimit ";
//echo $query;exit;
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);


        $result = $this->db->query($query_count);
        $data_array = $result->result_array();
        $total = count($data_array);
        $data['dtTotalRecords'] = $total;
        return $data;
    }

    public function FeedbackConsolidatedExportToExcel($exportWhere = '', $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $excel_data = " select af.company_id,af.user_id,af.workshop_id,af.feedbackset_id,
                    af.option_a,af.option_b,af.option_c,af.option_d,af.option_e,af.option_f,
                    af.weight_a,af.weight_b,af.weight_c,af.weight_d,af.weight_e,af.weight_f,
                    fq.option_a as doption_a,fq.option_b as doption_b,fq.option_c as doption_c,fq.option_d as doption_d,fq.option_e as doption_e,fq.option_f as doption_f,af.feedback_answer,af.question_type,af.weightage as text_weightage,
                    c.company_name,
                    af.feedback_id,concat(du.firstname,' ',du.lastname) as trainee_name,
                    du.email,du.mobile,w.workshop_name,f.title as feedback_set,fq.question_title,                    
                    wt.workshop_type,wr.region_name as workshop_region,wsr.description as workshop_subregion,wst.description as workshop_subtype,
                    ft.description as feedbacktype,fst.description as feedbacksubtype,rg.region_name as tregion_name,
                    DATE_FORMAT(af.start_dttm, '%d/%m/%Y %H:%i:%s') as start_dttm,
                    DATE_FORMAT(af.end_dttm, '%d/%m/%Y %H:%i:%s')as end_dttm,af.seconds,
                    if(af.option_a = '1' || af.option_b = '1' || af.option_c = '1' || af.option_d = '1' || af.option_e = '1' || af.option_f = '1' || af.feedback_answer != '','Responce','Time Out') as feedback_status,
                    IF(fq.multiple_allow = 1,(ifnull(fq.weight_a,0)+ifnull(fq.weight_b,0)+ifnull(fq.weight_c,0)+ifnull(fq.weight_d,0)+ifnull(fq.weight_e,0)+ifnull(fq.weight_f,0)),GREATEST(ifnull(fq.weight_a,0),ifnull(fq.weight_b,0),ifnull(fq.weight_c,0),ifnull(fq.weight_d,0),ifnull(fq.weight_e,0),ifnull(fq.weight_f,0))) as max_weightage
                        from atom_feedback af
                            inner join device_users du
                                on du.user_id = af.user_id
                            inner join workshop w
                                on w.id = af.workshop_id
                            inner join feedback f
                                on f.id = af.feedbackset_id
                            inner join feedback_questions fq
                                on fq.id = af.feedback_id
                            inner join company c
				                on c.id = af.company_id 
				            INNER JOIN feedback_type ft 
                                ON ft.id = af.feedback_type_id
                            INNER JOIN feedback_subtype fst 
                                ON fst.id = af.feedback_subtype_id 
                            LEFT join region rg 
                                ON rg.id = du.region_id  
                            LEFT JOIN region wr 
                                ON wr.id=w.region 
                            LEFT JOIN workshopsubregion_mst as wsr 
                                ON wsr.id=w.workshopsubregion_id AND wsr.region_id=w.region
                            LEFT JOIN workshoptype_mst as wt 
                                ON wt.id=w.workshop_type 
                            LEFT JOIN workshopsubtype_mst as wst 
                                ON wst.id=w.workshopsubtype_id AND wst.workshoptype_id=w.workshop_type $exportWhere ";
//            if($exportWhere!=""){
//                $excel_data .=" AND du.istester=0 ";
//            }else{
//                $excel_data .=" WHERE du.istester=0 ";
//            }            
        if (!$WRightsFlag) {
            $excel_data .= " AND af.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
//        $excel_data .= " $exportWhere ";
        $excel_data .= " group by af.workshop_id,af.feedback_id,af.user_id order by workshop_name,trainee_name ";
        $query = $this->db->query($excel_data);
        return $query->result();
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
    public function workshopwise_trainerdata($company_id,$workshop_id=""){
        $LcSqlStr = " select a.trainer_id,concat(cu.first_name,'',cu.last_name)as trainer_name"
                . " from workshop_questions a left join company_users cu "
                . " ON cu.userid = a.trainer_id "
                . " where a.company_id=$company_id ";
                    if($workshop_id !=''){
                        $LcSqlStr .= " and a.workshop_id=".$workshop_id;                            
                    }
                $LcSqlStr .= " group by a.trainer_id ";

        $query = $this->db->query($LcSqlStr);
        $row = $query->result();
        return $row;
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
	 public function DeviceChangedAlertDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $query = "SELECT u.user_id,u.firstname,u.lastname,u.emp_id,u.area,DATE_FORMAT(di.info_dttm,'%d-%m-%Y %h:%i %p') as info_dttm, u.employment_year,
                u.education_background,u.department,di.model,di.platform,di.imei,di.serial, 
                u.region_id,u.email,u.mobile,u.status,u.istester,rg.region_name,dr.description AS designation
                FROM workshop_device_info wdi
                LEFT JOIN device_info AS di ON wdi.device_info_id=di.id and wdi.user_id=di.user_id 
                LEFT JOIN device_users AS u ON u.user_id=di.user_id
                LEFT JOIN region AS rg ON rg.id=u.region_id
                LEFT JOIN workshop w ON w.id=wdi.workshop_id
                LEFT JOIN designation_trainee AS dr ON dr.id=u.designation_id $dtWhere ";
        if (!$WRightsFlag) {
            $query .= " AND wdi.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query .= " AND (u.trainer_id = $login_id OR u.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $query_count = $query . " $dtOrder ";
        $query .= " $dtOrder $dtLimit ";
        $result = $this->db->query($query);
        $data['ResultSet'] = $result->result_array();
        $data['dtPerPageRecords'] = count($data['ResultSet']);

        $query_count = "SELECT count(u.user_id) as total_count
                FROM workshop_device_info wdi
                LEFT JOIN device_info AS di ON wdi.device_info_id=di.id and wdi.user_id=di.user_id 
                LEFT JOIN device_users AS u ON u.user_id=di.user_id
                LEFT JOIN region AS rg ON rg.id=u.region_id
                LEFT JOIN workshop w ON w.id=wdi.workshop_id
                LEFT JOIN designation_trainee AS dr ON dr.id=u.designation_id $dtWhere ";
        if (!$WRightsFlag) {
            $query_count .= " AND wdi.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $query_count .= " AND (u.trainer_id = $login_id OR u.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $result = $this->db->query($query_count);        
        $total = $result->row();
        $data['dtTotalRecords'] = $total->total_count;
        return $data;
    }
    public function DeviceChangedExportToExcel($dtWhere, $RightsFlag, $WRightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $excel_data = "SELECT u.user_id,u.firstname,u.lastname,u.emp_id,u.area,DATE_FORMAT(di.info_dttm,'%d-%m-%Y %h:%i %p') as info_dttm, u.employment_year,
                u.education_background,u.department,di.model,di.platform,di.imei,di.serial, 
                u.region_id,u.email,u.mobile,u.status,u.istester,rg.region_name,dr.description AS designation
                FROM workshop_device_info wdi
                LEFT JOIN device_info AS di ON wdi.device_info_id=di.id and wdi.user_id=di.user_id 
                LEFT JOIN device_users AS u ON u.user_id=di.user_id
                LEFT JOIN region AS rg ON rg.id=u.region_id
                LEFT JOIN workshop w ON w.id=wdi.workshop_id
                LEFT JOIN designation_trainee AS dr ON dr.id=u.designation_id $dtWhere ";

        if (!$WRightsFlag) {
            $excel_data .= " AND wdi.workshop_id IN(select distinct workshop_id FROM temp_wrights where user_id= $login_id)";
        }
        if (!$RightsFlag) {
            $excel_data .= " AND (u.trainer_id = $login_id OR u.trainer_id IN(select rightsuser_id FROM cmsusers_rights where userid= $login_id))";
        }
        $excel_data .= " ORDER BY u.firstname,di.id DESC";
        $query = $this->db->query($excel_data);
        return $query->result();
    }
	public function ImeiDataTable($dtWhere, $dtOrder, $dtLimit, $RightsFlag, $WRightsFlag) {
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
    public function ImeiExportToExcel($dtWhere, $RightsFlag, $WRightsFlag) {
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
}
