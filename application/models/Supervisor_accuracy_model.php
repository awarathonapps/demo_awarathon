<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Supervisor_accuracy_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    public function getAccuracyDetails($workshop_id, $workshop_session, $trainer_id = "0", $RightsFlag) {
        $TodayDt = date('Y-m-d H:i:s');
        $login_id = $this->mw_session['user_id'];
        $query = "select w.workshop_name,CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) as pre_date,"
                . "CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) as post_date FROM workshop as w where w.id =" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet = $ObjSet->row();
        $liveFlag = false;
        if ($workshop_session == "PRE") {
            if (strtotime($LiveSet->pre_date) > strtotime($TodayDt)) {
                $liveFlag = true;
            }
        } else {
            if (strtotime($LiveSet->post_date) > strtotime($TodayDt)) {
                $liveFlag = true;
            }
        }
        if ($liveFlag) {
            $query = "SELECT qt.description AS topic,qst.description AS subtopic,
                FORMAT(SUM(arp.is_correct)*100/COUNT(arp.question_id),2) AS accuracy
                FROM atom_results AS arp
                INNER JOIN question_topic qt ON qt.id=arp.topic_id
                LEFT JOIN question_subtopic qst ON qst.id=arp.subtopic_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                WHERE wtu.tester_id IS NULL AND  arp.workshop_id=$workshop_id ";
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND arp.trainer_id= " . $trainer_id;
            }
            $query .= " and arp.workshop_session='$workshop_session' GROUP BY arp.topic_id,arp.subtopic_id order by accuracy desc ";
        } else {
            $query = "SELECT qt.description AS topic,qst.description AS subtopic,";
            if ($workshop_session == "PRE") {
                $query .= "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS accuracy";
            } else {
                $query .= "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS accuracy";
            }
            $query .= " FROM trainee_result AS ls INNER JOIN question_topic qt ON qt.id=ls.topic_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id
                LEFT JOIN question_subtopic qst ON qst.id=ls.subtopic_id";
            $query .= " WHERE wtu.tester_id IS NULL AND ls.workshop_id=$workshop_id  ";
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (ls.trainer_id = $login_id OR ls.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND ls.trainer_id= " . $trainer_id;
            }
            $query .= " GROUP BY ls.topic_id,ls.subtopic_id order by accuracy desc  ";
        }
        $result = $this->db->query($query);

        return $result->result();
    }

    public function get_PrepostAccuracy($workshop_id, $workshop_session = "PRE", $trainer_id = '0', $RightsFlag) {
        $login_id = $this->mw_session['user_id'];
        $TodayDt = date('Y-m-d H:i:s');
        $query = "select w.workshop_name,CONCAT(w.pre_end_date,' ', STR_TO_DATE(w.pre_end_time, '%l:%i %p')) as pre_date,"
                . "CONCAT(w.post_end_date,' ', STR_TO_DATE(w.post_end_time, '%l:%i %p')) as post_date FROM workshop as w where w.id =" . $workshop_id;
        $ObjSet = $this->db->query($query);
        $LiveSet = $ObjSet->row();
        $liveFlag = false;
        if ($workshop_session == "PRE") {
            if (strtotime($LiveSet->pre_date) > strtotime($TodayDt)) {
                $liveFlag = true;
            }
        } else {
            if (strtotime($LiveSet->post_date) > strtotime($TodayDt)) {
                $liveFlag = true;
            }
        }
        if ($liveFlag) {
            $query = "SELECT FORMAT(SUM(arp.is_correct)*100/COUNT(arp.question_id),2) AS accuracy,w.workshop_name
                FROM atom_results AS arp LEFT JOIN workshop as w ON w.id=arp.workshop_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=arp.workshop_id AND wtu.tester_id=arp.user_id
                WHERE arp.workshop_id=$workshop_id AND arp.workshop_session='$workshop_session' AND wtu.tester_id IS NULL ";
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (arp.trainer_id = $login_id OR arp.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND arp.trainer_id= " . $trainer_id;
            }
            $query .= " GROUP BY arp.workshop_id  ";
        } else {
            $query = "SELECT w.workshop_name, ";
            if ($workshop_session == "PRE") {
                $query .= "FORMAT(SUM(ls.pre_correct)*100/ SUM(ls.pre_total_questions),2) AS accuracy";
            } else {
                $query .= "FORMAT(SUM(ls.post_correct)*100/ SUM(ls.post_total_questions),2) AS accuracy";
            }
            $query .= " FROM trainee_result AS ls LEFT JOIN workshop as w ON w.id=ls.workshop_id
                LEFT JOIN workshop_tester_users as wtu ON wtu.workshop_id=ls.workshop_id AND wtu.tester_id=ls.trainee_id
                WHERE  ls.workshop_id=$workshop_id AND wtu.tester_id IS NULL";
            if ($trainer_id == "0") {
                if (!$RightsFlag) {
                    $query .= " AND (ls.trainer_id = $login_id OR ls.trainer_id IN(select trainer_id FROM temp_trights where user_id= $login_id))";
                }
            } else {
                $query .= " AND ls.trainer_id= " . $trainer_id;
            }
            $query .= " GROUP BY ls.workshop_id ";
        }
        $result = $this->db->query($query);
        return $result->row();
    }

}
