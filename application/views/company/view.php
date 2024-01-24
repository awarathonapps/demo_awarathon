<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>
        <link href="<?php echo $base_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $base_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $base_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <?php $this->load->view('inc/inc_htmlhead'); ?>
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <div class="page-wrapper">
            <?php $this->load->view('inc/inc_header'); ?>
            <div class="clearfix"> </div>
            <div class="page-container">
                <?php $this->load->view('inc/inc_sidebar'); ?>
                <div class="page-content-wrapper">
                    <div class="page-content">

                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li>
                                    <span>Organisation</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Company</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Edit Company</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>company" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">                                                                                               
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            View Company
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">                                        
                                            <div class="tabbable-line tabbable-full-width">
                                                <ul class="nav nav-tabs" id="tabs">
                                                    <li <?php echo ($step == 1 ? 'class="active"' : ''); ?>>
                                                        <a href="#tab_overview" data-toggle="tab">Overview</a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab_users" data-toggle="tab">Trainee List</a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab_mailsettings" data-toggle="tab">Mail Settings</a>
                                                    </li>
<!--                                                    <li>
                                                        <a href="#tab_attachments" data-toggle="tab">Attachments</a>
                                                    </li>-->
                                                    <?php if ($this->mw_session['superaccess']) { ?>        
                                                        <li <?php echo ($step == 2 ? 'class="active"' : ''); ?>>
                                                            <a href="#tab_settings" data-toggle="tab">Settings</a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane <?php echo ($step == 1 ? 'active"' : 'mar'); ?>" id="tab_overview">    
                                                        <form id="frmCompany" name="frmCompany" method="POST"  action=""> 
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Company Prefix</label>
                                                                    <input type="text" name="company_code" id="company_code" maxlength="10" class="form-control input-sm" value="<?php echo $result->company_code; ?>" disabled="">                                 
                                                                    <input type="hidden" name="id" id="id" maxlength="20" class="form-control input-sm" value="<?php echo urlencode(base64_encode($result->id)); ?>">                                 
                                                                </div>
                                                            </div>   
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Company Name<span class="required"> * </span></label>
                                                                    <input type="text" name="company_name" id="company_name" maxlength="250" class="form-control input-sm" value="<?php echo $result->company_name; ?>" disabled="">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Portal Name<span class="required"> * </span>&nbsp;&nbsp;&nbsp;
                                                                        <a data-title="Company can access this URL to view their data. Portal name should be 5 to 30 characters, all lowercase without any special characters.">
                                                                            <i class="icon-info font-black sub-title"></i>
                                                                        </a>
                                                                    </label>
                                                                    <input type="text" name="portal_name" id="portal_name" minlength="4" maxlength="30" class="form-control input-sm" value="<?php echo $result->portal_name; ?>" readonly="">
                                                                    <div class="text-muted">https://<span class="text-info">mediaworks</span>.awarathon.com</div>                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">One Time Code (OTC)<span class="required"> * </span></label>
                                                                    <input type="text" name="otp" id="otp" maxlength="250" class="form-control input-sm" value="<?php echo $result->otp; ?>" disabled="">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Industry<span class="required"> * </span></label>
                                                                    <select id="industry_type_id" name="industry_type_id" class="form-control input-sm " placeholder="Please select" disabled="">
                                                                        <option value="">Please Select</option>
                                                                        <?php foreach ($IndustryType as $IT) { ?>
                                                                            <option value="<?php echo $IT->id ?>" <?php echo($IT->id == $result->industry_type_id ? 'selected' : '') ?>><?php echo $IT->description ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Status<span class="required"> * </span></label>
                                                                    <select id="status" name="status" class="form-control input-sm " placeholder="Please select" disabled="">
                                                                        <option value="1" <?php echo ($result->status == 1) ? 'selected' : ''; ?>>Active</option>
                                                                        <option value="0" <?php echo ($result->status == 0) ? 'selected' : ''; ?>>In-Active</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Trainee Role</label>
                                                                <select id="deviceuser_role" name="deviceuser_role" class="form-control input-sm " placeholder="Please select" disabled=""  >
                                                                    <option value="">Please Select</option>
                                                                    <?php foreach ($roleResult as $rl) { ?>
                                                                    <option value="<?php echo $rl->arid ?>" <?php echo($rl->arid==$result->deviceuser_role ? 'selected' : '') ?>><?php echo $rl->rolename ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <div class="row">                                                      
                                                            <div class="col-md-6">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="empcode_restrict"> On Registration required Employee code?
                                                                    <input id="empcode_restrict" name="empcode_restrict" type="checkbox" value="1" <?php echo $result->empcode_restrict == 1 ? 'checked' : ''; ?>  onclick="SettingsCheck('empcode_restrict', 'empcode');" disabled=""/><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="eotp_required"> Employee OTP Verification Required?
                                                                    <input id="eotp_required" name="eotp_required" type="checkbox" value="1" <?php echo $result->eotp_required == 1 ? 'checked' : ''; ?>  disabled="" /><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="my-line"></div>
                                                        <div class="row">
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Address 1</label>
                                                                    <input type="text" name="address_i" id="address_i" maxlength="250" class="form-control input-sm" value="<?php echo $result->address_i; ?>" disabled="">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Address 2</label>
                                                                    <input type="text" name="address_ii" id="address_ii" maxlength="250" class="form-control input-sm" value="<?php echo $result->address_ii; ?>" disabled="">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Country</label>
                                                                    <select id="country_id" name="country_id" class="form-control input-sm select2" placeholder="Please select" disabled="">
                                                                        <option value="">Please Select</option
                                                                        <?php foreach ($Country as $con) { ?>
                                                                            <option value="<?php echo $con->id ?>" <?php echo($con->id == $result->country_id ? 'selected' : '') ?>><?php echo $con->description ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>State</label>
                                                                    <select id="state_id" name="state_id" class="form-control input-sm select2" placeholder="Please select" disabled="">
                                                                        <option value="">Please Select</option>
                                                                        <?php foreach ($State as $st) { ?>
                                                                            <option value="<?php echo $st->id ?>" <?php echo($st->id == $result->state_id ? 'selected' : '') ?>><?php echo $st->description ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">City</label>
                                                                    <select id="city_id" name="city_id" class="form-control input-sm select2" placeholder="Please select" disabled="">
                                                                        <option value="">Please Select</option>
                                                                        <?php foreach ($City as $ct) { ?>
                                                                            <option value="<?php echo $ct->id ?>" <?php echo($ct->id == $result->city_id ? 'selected' : '') ?>><?php echo $ct->description ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Pin Code</label>
                                                                    <input type="text" name="pincode" id="pincode" maxlength="250" class="form-control input-sm" value="<?php echo $result->pincode; ?>" disabled="">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Contact Person</label>
                                                                    <input type="text" name="contact_person" id="contact_person" maxlength="50" class="form-control input-sm"  value="<?php echo $result->contact_person; ?>" disabled="">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Contact No</label>
                                                                    <input type="text" name="contact_no" id="contact_no" maxlength="50" class="form-control input-sm" value="<?php echo $result->contact_no; ?>" disabled="">                                 
                                                                </div>
                                                            </div>                                                            
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Email</label>
                                                                    <input type="text" name="email" id="email" maxlength="250" class="form-control input-sm"  value="<?php echo $result->email; ?>" disabled="">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">website</label>
                                                                    <input type="text" name="website" id="website" maxlength="250" class="form-control input-sm"  value="<?php echo $result->website; ?>" disabled="">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Note</label>
                                                                    <textarea rows="4" class="form-control input-sm" id="remarks" name="remarks" placeholder="" disabled=""><?php echo $result->remarks; ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">      
                                                            <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Company Logo</label>
                                                                <div class="form-control fileinput fileinput-exists" style="    border: none;height:auto;" data-provides="fileinput">
                                                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                                        <img src="<?php echo base_url().'assets/uploads/'.($result->company_logo!='' ?'company/'.$result->company_logo : 'no_image.png'); ?>" alt=""/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <div class="row">      
                                                            <div class="col-md-12 text-right">                                                                  
                                                                <a href="<?php echo site_url("company"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                            </div>
                                                        </div>
                                                      </form>
                                                    </div>    
                                                    <div class="tab-pane <?php echo ($step == 2 ? 'active"' : 'mar'); ?>" id="tab_attachments">


                                                    </div>
                                                    <div class="tab-pane  <?php echo ($step == 2 ? 'active"' : 'mar'); ?>" id="tab_mailsettings">
                                                        <form id="frmSMTP" name="frmSMTP" method="POST"  action=""> 
                                                        <div class="portlet light bordered">                                                            
                                                            <div class="portlet-body">                                                                                            
                                                                <div class="tab-content">                                                                                                                                                 
                                                                    <div class="row">    
                                                                        <div class="col-md-6">       
                                                                            <div class="form-group">
                                                                                <label class="">SMTP Server Host Name<span class="required"> * </span></label>
                                                                                <input type="text" name="host_name" id="host_name" maxlength="250" class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails)>0 ? $smtpDetails->smtp_ipadress : '') ?>" disabled="">  
                                                                                <input type="hidden" name="edit_id" id="edit_id" class="form-control input-sm" autocomplete="off" value="">  
                                                                            </div>
                                                                        </div>    
                                                                        <div class="col-md-6">       
                                                                            <div class="form-group">
                                                                                <label class="">Smtp Secure (SSL/TLS)<span class="required"> * </span></label>
                                                                                <select id="smtp_secure" name="smtp_secure" class="form-control input-sm select2" placeholder="Please select" disabled="">                                                                                    
                                                                                    <option value="">Please Select</option>
                                                                                    <option value="1" <?php echo (count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_secure == 1 ? 'selected' : '' : 'selected'); ?>>SSL</option>
                                                                                    <option value="2" <?php echo (count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_secure == 2 ? 'selected' : '' : 'selected'); ?>>TLS</option>
                                                                                </select>                                                                                                                              
                                                                            </div>
                                                                        </div>
                                                                    </div>  
                                                                    <div class="row">    
                                                                        <div class="col-md-6">       
                                                                            <div class="form-group">
                                                                                <label class="">Port No<span class="required"> * </span></label>
                                                                                <input type="text" name="port_no" id="port_no" maxlength="250" class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails)>0 ? $smtpDetails->smtp_portno : '') ?>" disabled="">                                                                  
                                                                            </div>
                                                                        </div>                                                            
                                                                        <div class="col-md-6">    
                                                                            <div class="form-group last">
                                                                                <label>Authentication</label>
                                                                                <select id="authentication" name="authentication" class="form-control input-sm select2" placeholder="Please select" disabled="">
                                                                                    <option value="">Please Select</option>
                                                                                    <option value="1" <?php echo (count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_authentication == 1 ? 'selected' : '' : 'selected'); ?>>Yes</option>
                                                                                    <option value="2" <?php echo (count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_authentication == 2 ? 'selected' : '' : 'selected'); ?>>No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">    
                                                                        <div class="col-md-6">       
                                                                            <div class="form-group">
                                                                                <label class="">User Name<span class="required"> * </span></label>
                                                                                <input type="text" name="user_name" id="user_name" maxlength="250" class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails)>0 ? $smtpDetails->smtp_username : '') ?>" disabled="">                                                                  
                                                                            </div>
                                                                        </div>                                                        
                                                                        <div class="col-md-6">       
                                                                            <div class="form-group">
                                                                                <label class="">Password<span class="required"> * </span></label>
                                                                                <input type="password" name="password" id="password" maxlength="12" class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails)>0 ? $smtpDetails->smtp_password : '') ?>" disabled="">                                                                  
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">    
                                                                        <div class="col-md-6">       
                                                                            <div class="form-group">
                                                                                <label class="">Alias Name<span class="required"> * </span></label>
                                                                                <input type="text" name="alias_name" id="alias_name" maxlength="250" class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails)>0 ? $smtpDetails->smtp_alias : '') ?>" disabled="">                                                                  
                                                                            </div>
                                                                        </div>                                                                                                                   
                                                                        <div class="col-md-4">    
                                                                            <div class="form-group last">
                                                                                <label>Status</label>
                                                                                <select id="stmptstatus" name="status" class="form-control input-sm select2" placeholder="Please select" disabled="">
                                                                                    <option value="1" <?php echo (count((array)$smtpDetails) > 0 ? $smtpDetails->status == 1 ? 'selected' : '' : 'selected'); ?>>Active</option>
                                                                                    <option value="0" <?php echo (count((array)$smtpDetails) > 0 ? ($smtpDetails->status == 0) ? 'selected' : '' : ''); ?>>In-Active</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>    
                                                                    
                                                                    <div class="row">      
                                                                        <div class="col-md-12 text-right">                                                                              
                                                                            <a href="<?php echo site_url("company"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>                                                           
                                                        </div>
                                                        </form>    
                                                    </div>
                                                    <div class="tab-pane  <?php echo ($step == 2 ? 'active"' : ''); ?>" id="tab_users">
                                                        <form role="form">
                                                        <div class="form-body">
                                                            <div class="row margin-bottom-10">
                                                                <div class="col-md-3" >    
                                                                    <div class="form-group">
                                                                        <label class="mt-checkbox mt-checkbox-outline" for="testerfilter"> Testing Team
                                                                            <input id="testerfilter" name="testerfilter" type="checkbox" value="1" onclick="DatatableUsersRefresh();" /><span></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row ">
                                                                <div class="col-md-12" id="workshop_panel" >
                                                                    <table class="table  table-bordered table-hover table-checkable order-column" id="UsersTable">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>ID</th>
                                                                                <th>Employee ID</th>
                                                                                <th> Name</th>
                                                                                <th>Email</th>
                                                                                <th>Phone No</th>
                                                                                <th>Area</th>
                                                                                <th>Tester</th>
                                                                                <th>Status</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody></tbody>
                                                                    </table>
                                                                </div>
                                                                 </div>   
                                                                <div>
                                                            </div>
                                                </div>    
                                                </form> 
                                                    </div>
                                                    <div class="tab-pane " id="tab_settings">
                                                        <form id="frmSetting" name="frmSetting" method="POST"  action=""> 
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="users_restrict"> On Android/IOS Application Restrict Users?
                                                                    <input id="users_restrict" name="users_restrict" type="checkbox" value="1" <?php echo ($result->users_restrict == 1 ? 'checked' : ''); ?> onclick="SettingsCheck('users_restrict', 'app_users_count');" disabled=""/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">No. Of Users Allowed<span class="required"> * </span></label>
                                                                    <input type="text" name="app_users_count" id="app_users_count" maxlength="250" class="form-control input-sm" disabled value="<?php echo $result->app_users_count; ?>" disabled="">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="portal_restrict"> On Portal Application Restrict Users?
                                                                    <input id="portal_restrict" name="portal_restrict" type="checkbox" value="1" <?php echo ($result->portal_restrict == 1 ? 'checked' : ''); ?> onclick="SettingsCheck('portal_restrict', 'portal_users_count');" disabled=""/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">No. Of Users Allowed<span class="required"> * </span></label>
                                                                    <input type="text" name="portal_users_count" id="portal_users_count" maxlength="250" class="form-control input-sm" disabled  value="<?php echo $result->portal_users_count; ?>" disabled="">                                 
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="personal_form_required"> On Android/IOS Application Show Personal Details Form?
                                                                    <input id="personal_form_required" name="personal_form_required" type="checkbox" value="1" <?php echo ($result->personal_form_required == 1 ? 'checked' : ''); ?> onclick="SettingsCheck('personal_form_required', 'form_id');" disabled=""/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>Personal Detail Form Name<span class="required"> * </span></label>
                                                                    <select id="form_id" name="form_id" class="form-control input-sm select2" placeholder="Please select" disabled="">
                                                                        <option value="">Please Select</option>
                                                                        <?php foreach ($FeedbackForm as $frm) { ?>
                                                                            <option value="<?php echo $frm->id ?>" <?php echo ($result->form_id == $frm->id ? 'selected' : ''); ?>><?php echo $frm->form_name ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="restrict_workshop"> Restrict to create no. of Workshop?
                                                                    <input id="restrict_workshop" name="restrict_workshop" type="checkbox" value="1" <?php echo ($result->restrict_workshop == 1 ? 'checked' : ''); ?> onclick="SettingsCheck('restrict_workshop', 'workshop_count');" disabled=""/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>No. of Workshop Allowed<span class="required"> * </span></label>
                                                                    <input type="text" name="workshop_count" id="workshop_count" maxlength="250" class="form-control input-sm" disabled  value="<?php echo $result->workshop_count; ?>" disabled="" />                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="restrict_workshop_users"> Restrict to create no. of Users in a each Workshop?
                                                                    <input id="restrict_workshop_users" name="restrict_workshop_users" type="checkbox" value="1" <?php echo ($result->restrict_workshop_users == 1 ? 'checked' : ''); ?> onclick="SettingsCheck('restrict_workshop_users', 'workshop_users_count');" disabled=""/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>No. of Users Allowed in a each Workshop<span class="required"> * </span></label>
                                                                    <input type="text" name="workshop_users_count" id="workshop_users_count" maxlength="250" class="form-control input-sm" value="<?php echo $result->workshop_users_count; ?>" disabled="" />                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="restrict_workshop_question"> Restrict to create/import no. of Question in a each Workshop?
                                                                    <input id="restrict_workshop_question" name="restrict_workshop_question" type="checkbox" value="1" <?php echo ($result->restrict_workshop_question == 1 ? 'checked' : ''); ?> onclick="SettingsCheck('restrict_workshop_question', 'workshop_question_count');" disabled=""/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>No. of Question Allowed in a each Workshop<span class="required"> * </span></label>
                                                                    <input type="text" name="workshop_question_count" id="workshop_question_count" maxlength="250" class="form-control input-sm" disabled value="<?php echo $result->workshop_question_count; ?>">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="restrict_feedback"> Restrict to create no. of Feedback?
                                                                    <input id="restrict_feedback" name="restrict_feedback" type="checkbox" value="1" <?php echo ($result->restrict_feedback == 1 ? 'checked' : ''); ?> onclick="SettingsCheck('restrict_feedback', 'feedback_count');" disabled="" /><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>No. of Feedback Allowed<span class="required"> * </span></label>
                                                                    <input type="text" name="feedback_count" id="feedback_count" maxlength="250" class="form-control input-sm" disabled value="<?php echo $result->feedback_count; ?>" disabled="" />                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="restrict_feedback_users"> Restrict to create no. of Users in a each Feedback?
                                                                    <input id="restrict_feedback_users" name="restrict_feedback_users" type="checkbox" value="1" <?php echo ($result->restrict_feedback_users == 1 ? 'checked' : ''); ?>  onclick="SettingsCheck('restrict_feedback_users', 'feedback_users_count');" disabled=""/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>No. of Users Allowed In in a each Feedback<span class="required"> * </span></label>
                                                                    <input type="text" name="feedback_users_count" id="feedback_users_count" maxlength="250" class="form-control input-sm" value="<?php echo $result->feedback_users_count; ?>"  disabled />                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="restrict_feedback_question"> Restrict to create/import no. of Question in a each Feedback?
                                                                    <input id="restrict_feedback_question" name="restrict_feedback_question" type="checkbox" value="1" <?php echo ($result->restrict_feedback_question == 1 ? 'checked' : ''); ?>  onclick="SettingsCheck('restrict_feedback_question', 'feedback_question_count');" disabled=""/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>No. of Question Allowed in a each Feedback<span class="required"> * </span></label>
                                                                    <input type="text" name="feedback_question_count" id="feedback_question_count" maxlength="250" class="form-control input-sm" disabled value="<?php echo $result->feedback_question_count; ?>" disabled=""/>                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                            <div class="row">      
                                                                <div class="col-md-12 text-right">                                                                      
                                                                    <a href="<?php echo site_url("company"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                                </div>
                                                            </div>
                                                        </form>    
                                                    </div>    
                                                </div>    
                                            </div>                                                                                            
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
            </div>
            <?php //$this->load->view('inc/inc_footer'); ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav'); ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $base_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <script>
            jQuery(document).ready(function () {
                DatatableUsersRefresh();
            });
            function DatatableUsersRefresh() {
        var table = $('#UsersTable');
        table.dataTable({
            destroy: true,
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "infoFiltered": "(filtered1 from _MAX_ total records)",
                "lengthMenu": "Show _MENU_",
                "search": "Search:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "previous":"Prev",
                    "next": "Next",
                    "last": "Last",
                    "first": "First"
                }
            },
            "bStateSave": false,
            "lengthMenu": [
                [5,10,15,20, -1],
                [5,10,15,20, "All"]
            ],
            "pageLength": 10,            
            "pagingType": "bootstrap_full_number",
            "columnDefs": [
                {'width': '30px','orderable': true,'searchable': true,'targets': [0]},
                {'width': '30px','orderable': true,'searchable': true,'targets': [1]},
                {'width': '','orderable': true,'searchable': true,'targets': [2]}, 
                {'width': '90px','orderable': true,'searchable': true,'targets': [3]}, 
                {'width': '90px','orderable': true,'searchable': true,'targets': [4]}, 
                {'width': '90px','orderable': true,'searchable': true,'targets': [5]}, 
                {'width': '60px','orderable': false,'searchable': false,'targets': [6]},                 
            ],
            "order": [
                [0, "desc"]
            ],
            "processing": true,
            "serverSide": true,
            "sAjaxSource": "<?php echo base_url() . 'company/UsersDatatableRefresh/'.base64_encode($result->id); ?>",
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                aoData.push({name: 'testerfilter', value: $('#testerfilter').is(":checked")});
                $.getJSON(sSource, aoData, function (json) {
                    fnCallback(json);
                });
            },
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                return nRow;
            }
            , "fnFooterCallback": function (nRow, aData) {
            }
        });
    }
        </script>
    </body>
</html>