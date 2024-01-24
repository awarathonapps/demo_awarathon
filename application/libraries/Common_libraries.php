<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Common_libraries
{

    private $ci;            // para CodeIgniter Super Global Referencias o variables globales

    function __construct()
    {
        $this->ci = &get_instance();    // get a reference to CodeIgniter.
    }

    function check_menu_rights($acces_management)
    {
        $CI = &get_instance();
        $Company_id = $acces_management['company_id'];
        $login_type = $acces_management['login_type'];
        $user_id = $acces_management['user_id'];
        if ($login_type == 3) {
            $LcSqlStr = "SELECT c.modulename,c.modulegroup FROM company_role_modules a LEFT JOIN company_modules c ON
            a.moduleid=c.moduleid  WHERE a.allow_access=1 
            AND c.status=1 and a.roleid= " . $acces_management['role'] . " order by c.modulegroup";
        } else if ($Company_id == "") {
            $LcSqlStr = "SELECT c.modulename,c.modulegroup FROM access_role_modules a LEFT JOIN 
            users b ON a.roleid =b.role LEFT JOIN access_modules c ON a.moduleid=c.moduleid  WHERE a.allow_access=1 
            AND c.status=1 and b.userid=" . $user_id . " order by c.modulegroup";
        } else {
            $LcSqlStr = "SELECT c.modulename,c.modulegroup FROM company_role_modules a LEFT JOIN 
            company_users b ON a.roleid =b.role LEFT JOIN company_modules c ON a.moduleid=c.moduleid  WHERE a.allow_access=1 
            AND c.status=1 and b.userid=" . $user_id . " AND b.company_id=" . $Company_id . " order by c.modulegroup";
        }
        $query = $CI->db->query($LcSqlStr);
        $ResultSet = $query->result();
        //    echo "<pre>";
        //    print_r($ResultSet);
        //    exit;
        $RightsArray = array();
        $GroupArray = array();
        $GroupName = '';
        foreach ($ResultSet as $value) {
            if ($GroupName != $value->modulegroup) {
                $GroupArray[$value->modulegroup] = 1;
                $GroupName = $value->modulegroup;
            }
            $RightsArray[$value->modulename] = 1;
        }
        $data['GroupArray'] = $GroupArray;
        $data['RightsArray'] = $RightsArray;
        return $data;
    }
    function Custom_Emailer($AlertName, $Patterns, $Replacements, $Id, $Type)
    {
        if ($Type == 'F') {
            $CustomerInfo = $this->ci->common_model->getCustomerInfo($Id);
            $To = $CustomerInfo->email;
            $Toname = $CustomerInfo->firstname . ' ' . $CustomerInfo->lastname;
        }
        $EmailTemplate = $this->ci->common_model->email($AlertName);
        $Fromname = $EmailTemplate->fromname;
        $From = $EmailTemplate->fromemail;
        $Subject = $EmailTemplate->subject;
        $Message = $EmailTemplate->message;

        $SMTP = $this->ci->common_model->smtp();
        $SMTPHost = $SMTP->hostname;
        $SMTPAuth = $SMTP->smtpauth;
        $SMTPUsername = $SMTP->username;
        $SMTPPassword = $SMTP->password;
        $SMTPSecure = $SMTP->smtpsecure;
        $SMTPPort = $SMTP->port;

        $Body = preg_replace($Patterns, $Replacements, $Message);

        $this->ci->load->library('My_PHPMailer');
        $mail = new PHPMailer;
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $SMTPHost;
        $mail->SMTPAuth = $SMTPAuth;
        $mail->Username = $SMTPUsername;
        $mail->Password = $SMTPPassword;
        $mail->SMTPSecure = $SMTPSecure;
        $mail->XMailer = ' ';

        $mail->setFrom($From, $Fromname);
        $mail->addAddress($To, $Toname);

        $mail->isHTML(true);
        $mail->SMTPKeepAlive = true;
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $Subject;
        $mail->Body = $Body;
        $mail->AltBody = $Subject;
        $mail->Mailer = "smtp";

        $Flag = $mail->send();

        $now = date('Y-m-d H:i:s');
        $EmailData = array(
            'custid' => $Id,
            'eto' => $To,
            'subject' => $Subject,
            'body' => $Body,
            'status' => $Flag,
            'addeddate' => $now
        );
        $this->ci->common_model->insert('emaillog', $EmailData);

        return $Flag;
    }

    function DT_RenderColumns($aColumns)
    {

        /* Paging */
        $dtLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $dtLimit = "LIMIT " . stripslashes($_GET['iDisplayStart']) . ", " .
                stripslashes($_GET['iDisplayLength']);
        }
        $data['dtLimit'] = $dtLimit;


        /* Ordering */
        $dtOrder = '';
        if (isset($_GET['iSortCol_0'])) {
            $dtOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $dtOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . "
				 	" . stripslashes($_GET['sSortDir_' . $i]) . ", ";
                }
            }

            $dtOrder = substr_replace($dtOrder, "", -2);
            if ($dtOrder == "ORDER BY") {
                $dtOrder = "";
            }
        }
        $data['dtOrder'] = $dtOrder;


        /*
	* Filtering
	     * NOTE this does not match the built-in DataTables filtering which does it
	     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
	     */
        $dtWhere = "";
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
            $dtWhere = "WHERE (";
            for ($i = 0; $i < count($aColumns); $i++) {
                if (trim($aColumns[$i]) != '') {
                    if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true") {
                        $dtWhere .= $aColumns[$i] . " LIKE '%" . stripslashes(trim($_GET['sSearch'])) . "%' OR ";
                    }
                }
            }
            $dtWhere = substr_replace($dtWhere, "", -3);
            $dtWhere .= ')';
        }


        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (trim($aColumns[$i]) != '') {
                if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
                    if ($dtWhere == "") {
                        $dtWhere = "WHERE ";
                    } else {
                        $dtWhere .= " AND ";
                    }
                    $dtWhere .= $aColumns[$i] . " LIKE '%" . stripslashes(trim($_GET['sSearch_' . $i])) . "%' ";
                }
            }
        }
        $data['dtWhere'] = $dtWhere;
        return $data;
    }
    function DT_RenderColumns_qbuilder($aColumns)
    {

        /* Paging */
        // $dtLimit = "";
        // if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        //     // $dtLimit = stripslashes($_GET['iDisplayLength']);
        //     // $dtLimit = stripslashes($_GET['iDisplayLength']). ", " .stripslashes($_GET['iDisplayStart']);
        //     $dtLimit = " " . stripslashes($_GET['iDisplayStart']) . ", " .stripslashes($_GET['iDisplayLength']);
        // }
        // $data['dtLimit'] = $dtLimit;

        $dtLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $dtLimitLength = stripslashes($_GET['iDisplayLength']);
            $dtLimitStart = stripslashes($_GET['iDisplayStart']);
            // $dtLimit = stripslashes($_GET['iDisplayStart']) . ", " .stripslashes($_GET['iDisplayLength']);
            // $dtLimit = "LIMIT " . stripslashes($_GET['iDisplayStart']) . ", " .stripslashes($_GET['iDisplayLength']);
        }
        $data['dtLimitLength'] = $dtLimitLength;
        $data['dtLimit'] = $dtLimitStart;


        /* Ordering */
        $dtOrder = '';
        if (isset($_GET['iSortCol_0'])) {
            $dtOrder = "";
            // $dtOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $dtOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . "
                    " . stripslashes($_GET['sSortDir_' . $i]) . ", ";
                }
            }

            $dtOrder = substr_replace($dtOrder, "", -2);
            if ($dtOrder == "ORDER BY") {
                $dtOrder = "";
            }
        }
        $data['dtOrder'] = $dtOrder;


        /*
    * Filtering
        * NOTE this does not match the built-in DataTables filtering which does it
        * word by word on any field. It's possible to do here, but concerned about efficiency
    * on very large tables, and MySQL's regex functionality is very limited
        */
        $dtWhere = "";
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
            $dtWhere = "WHERE (";
            for ($i = 0; $i < count($aColumns); $i++) {
                if (trim($aColumns[$i]) != '') {
                    if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true") {
                        $dtWhere .= $aColumns[$i] . " LIKE '%" . stripslashes(trim($_GET['sSearch'])) . "%' OR ";
                    }
                }
            }
            $dtWhere = substr_replace($dtWhere, "", -3);
            $dtWhere .= ')';
        }


        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (trim($aColumns[$i]) != '') {
                if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
                    if ($dtWhere == "") {
                        $dtWhere = "WHERE ";
                    } else {
                        $dtWhere .= " AND ";
                    }
                    $dtWhere .= $aColumns[$i] . " LIKE '%" . stripslashes(trim($_GET['sSearch_' . $i])) . "%' ";
                }
            }
        }
        $data['dtWhere'] = $dtWhere;
        return $data;
    }
}
