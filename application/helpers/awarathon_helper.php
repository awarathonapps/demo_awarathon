<?php


function DT_RenderColumns($aColumns) {
	
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
		for ($i = 0; $i < count((array)$aColumns); $i++) {
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
	for ($i = 0; $i < count((array)$aColumns); $i++) {
		if (trim($aColumns[$i]) != '') {
			if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
				if ($dtWhere == "") {
					$dtWhere = "WHERE ";
				}
				else {
					$dtWhere .= " AND ";
				}
				$dtWhere .= $aColumns[$i] . " LIKE '%" . stripslashes(trim($_GET['sSearch_' . $i])) . "%' ";
			}
		}
	}
	$data['dtWhere'] = $dtWhere;
	return $data;
}
function UserAvatar($user_id){
	$CI = & get_instance();
	$LcSqlStr = "SELECT avatar FROM users WHERE status=1 and userid=".$user_id;
	$query = $CI->db->query($LcSqlStr);
	$ResultSet = $query->row();
	if (count((array)$ResultSet) > 0){
		return base_url().$ResultSet->avatar;
	}else{
		return base_url()."assets/layouts/layout/img/avatar.png";
	} 
}
function CheckSidebarRights($acces_management){
    $CI = & get_instance();
    $Company_id=$acces_management['company_id'];
    $login_type =$acces_management['login_type'];
    $user_id = $acces_management['user_id'];
    if($login_type==3){
        $LcSqlStr = "SELECT c.modulename,c.modulegroup FROM company_role_modules a LEFT JOIN company_modules c ON
            a.moduleid=c.moduleid  WHERE a.allow_access=1 
            AND c.status=1 and a.roleid= ".$acces_management['role']." order by c.modulegroup";
    }else if($Company_id==""){
        $LcSqlStr = "SELECT c.modulename,c.modulegroup FROM access_role_modules a LEFT JOIN 
            users b ON a.roleid =b.role LEFT JOIN access_modules c ON a.moduleid=c.moduleid  WHERE a.allow_access=1 
            AND c.status=1 and b.userid=".$user_id ." order by c.modulegroup";
    }else{
        $LcSqlStr = "SELECT c.modulename,c.modulegroup FROM company_role_modules a LEFT JOIN 
            company_users b ON a.roleid =b.role LEFT JOIN company_modules c ON a.moduleid=c.moduleid  WHERE a.allow_access=1 
            AND c.status=1 and b.userid=".$user_id ." AND b.company_id=".$Company_id ." order by c.modulegroup";
    }
    $query = $CI->db->query($LcSqlStr);
    $ResultSet = $query->result();
//    echo "<pre>";
//    print_r($ResultSet);
//    exit;
    $RightsArray=array();
    $GroupArray=array();
    $GroupName='';
    foreach ($ResultSet as $value) {
        if($GroupName !=$value->modulegroup){
            $GroupArray[$value->modulegroup]=1;
            $GroupName = $value->modulegroup;
        }
        $RightsArray[$value->modulename]=1;
    }
    $data['GroupArray'] = $GroupArray;
    $data['RightsArray'] = $RightsArray;
    return $data;
}
function CheckRights($user_id = '', $menu = '', $Mode = '') {
	$CI = & get_instance();
	$apps_session = $CI->session->userdata('awarathon_session');
	$superaccess = $apps_session['superaccess'];
	
	if ($superaccess == true) {
		$ResultSet = array(
		            'role' => 1,
		            'allow_access' => 1,
		            'allow_add' => 1,
		            'allow_view' => 1,
		            'allow_edit' => 1,
		            'allow_delete' => 1,
		            'allow_print' => 1,
		            'allow_import' => 1,
		            'allow_export' => 1);
		return (object) $ResultSet;
	}
	else {
		$CI = & get_instance();
                $login_type =$apps_session['login_type'];
                if($login_type==3){
                    $LcSqlStr = "SELECT a.roleid as role,a.allow_access,allow_add,allow_view,allow_edit,allow_delete,allow_print,allow_import,allow_export FROM company_role_modules a "
                        . "LEFT JOIN company_modules c ON a.moduleid=c.moduleid "
                        . " WHERE a.roleid= ".$apps_session['role']." AND  c.status=1 and c.modulename='" . $menu . "'";
                }
                else if($apps_session['company_id']==""){
                    $LcSqlStr = "SELECT b.role,a.allow_access,allow_add,allow_view,allow_edit,allow_delete,allow_print,allow_import,allow_export FROM access_role_modules a LEFT JOIN users b ON a.roleid =b.role "
		                . "LEFT JOIN access_modules c ON a.moduleid=c.moduleid  WHERE c.status=1 and c.modulename='" . $menu . "' AND b.userid=" . $user_id;
                }else{
                    $LcSqlStr = "SELECT b.role,a.allow_access,allow_add,allow_view,allow_edit,allow_delete,allow_print,allow_import,allow_export FROM company_role_modules a LEFT JOIN company_users b ON a.roleid =b.role "
		                . "LEFT JOIN company_modules c ON a.moduleid=c.moduleid  WHERE c.status=1 and c.modulename='" . $menu . "' AND b.userid=" . $user_id;
                }
		$query = $CI->db->query($LcSqlStr);
		$ResultSet = $query->row();
		if ($Mode <> '') {
			if (count((array)$ResultSet) > 0 && $ResultSet->$Mode == 1) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return $ResultSet;
		}
	}
}

function auto_email($AlertName, $Patterns, $Replacements, $Id, $Type) {
	$CI = & get_instance();
	
	if ($Type == 'F') {
		$CustomerInfo = $CI->common_model->getCustomerInfo($Id);
		$To = $CustomerInfo->email;
		$Toname = $CustomerInfo->firstname . ' ' . $CustomerInfo->lastname;
	}
	else {
		
	}
	
	
	$EmailTemplate = $CI->common_model->email($AlertName);
	$Fromname = $EmailTemplate->fromname;
	$From = $EmailTemplate->fromemail;
	$Subject = $EmailTemplate->subject;
	$Message = $EmailTemplate->message;
	
	$SMTP = $CI->common_model->smtp();
	$SMTPHost = $SMTP->hostname;
	$SMTPAuth = $SMTP->smtpauth;
	$SMTPUsername = $SMTP->username;
	$SMTPPassword = $SMTP->password;
	$SMTPSecure = $SMTP->smtpsecure;
	$SMTPPort = $SMTP->port;
	
	$Body = preg_replace($Patterns, $Replacements, $Message);
	
	$CI->load->library('My_PHPMailer');
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
	        'addeddate' => $now);
	$CI->common_model->insert('emaillog', $EmailData);
	
	return $Flag;
}
function generateRandomString($length = 10) {
    $characters = '0123456789';
    //ABCDEFGHIJKLMNOPQRSTUVWXYZ
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function CheckCustomizeModule($Company_id,$moduleid){
    $CI = & get_instance();
	$LcSqlStr = "SELECT moduleid FROM customize_modules WHERE company_id=$Company_id and moduleid=".$moduleid;
	$query = $CI->db->query($LcSqlStr);
	return (count((array)$query->row())>0 ? true:false);
}
function validate_mobile($mobile)
    {
      return preg_match('/^[0-9]{10}+$/', $mobile);
    }
function isValidEmail($email){ 
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
function get_graphcolor($avg,$action){
    $ceil_avg = ceil($avg);
    if($action == 1){
        $color_array = $_SESSION['Assessment_result_session'];
    }else{
        $color_array = $_SESSION['Assessment_threshold_session'];
    }
	$color_code='';
    if(count((array)$color_array) > 0){
        foreach ($color_array as $val){
			
            if($ceil_avg >= $val['range_from'] && $ceil_avg <= $val['range_to']){
                $color_code = 'background-color:'.$val['range_color'];
                break;
            }
        }
        return $color_code;
    }
}
?>