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
        <link href="<?php echo $base_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
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
                                <?php if ($this->session->flashdata('flash_message')) { ?> 
                                    <div class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                        <?php echo $this->session->flashdata('flash_message'); ?>
                                    </div>
                                <?php } ?>
                                <?php
                                $errors = validation_errors();
                                //echo $errors;

                                if ($errors) {
                                    ?>
                                    <div style="display: block;" class="alert alert-danger display-hide">
                                        <button class="close" data-close="alert"></button>
                                        You have some form errors. Please check below.
                                        <?php echo $errors; ?>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="alert alert-danger display-hide" id="errordiv">
                                    <button class="close" data-close="alert"></button>
                                    You have some form errors. Please check below.
                                    <br><span id="errorlog"></span>
                                </div>
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            Edit Company
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">                                        
                                            <div class="tabbable-line tabbable-full-width">
                                                <ul class="nav nav-tabs" id="tabs">
                                                    <li <?php echo ($step == 1 ? 'class="active"' : ''); ?>>
                                                        <a href="#tab_overview" data-toggle="tab">Overview</a>
                                                    </li>
                                                    <li <?php echo ($step == 2 ? 'class="active"' : ''); ?>>
                                                        <a href="#tab_users" data-toggle="tab">Trainee List</a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab_mailsettings" data-toggle="tab">Mail Settings</a>
                                                    </li>
<!--                                                    <li>
                                                        <a href="#tab_attachments" data-toggle="tab">Attachments</a>
                                                    </li>-->
                                                    <?php if ($this->mw_session['superaccess']) { ?>        
                                                        <li >
                                                            <a href="#tab_settings" data-toggle="tab">Settings</a>
                                                        </li>
                                                    <?php } ?>
                                                    <li>
                                                        <a href="#tab_minute" data-toggle="tab">Assessment Minute</a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane <?php echo ($step == 1 ? 'active"' : 'mar'); ?>" id="tab_overview">    
                                                        <form id="frmCompany" name="frmCompany" method="POST"  action="<?php echo $base_url; ?>company/update/<?php echo base64_encode($result->id); ?>"> 
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Company Prefix</label>
                                                                    <input type="text" name="company_code" id="company_code" maxlength="10" class="form-control input-sm" value="<?php echo $result->company_code; ?>">                                 
                                                                    <input type="hidden" name="id" id="id" maxlength="20" class="form-control input-sm" value="<?php echo urlencode(base64_encode($result->id)); ?>">                                 
                                                                </div>
                                                            </div>   
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Company Name<span class="required"> * </span></label>
                                                                    <input type="text" name="company_name" id="company_name" maxlength="250" class="form-control input-sm" value="<?php echo $result->company_name; ?>">                                 
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
                                                                    <input type="text" name="otp" id="otp" maxlength="250" class="form-control input-sm" value="<?php echo $result->otp; ?>" readonly="">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Industry<span class="required"> * </span></label>
                                                                    <select id="industry_type_id" name="industry_type_id" class="form-control input-sm select2" placeholder="Please select" >
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
                                                                    <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="1" <?php echo ($result->status == 1) ? 'selected' : ''; ?>>Active</option>
                                                                        <option value="0" <?php echo ($result->status == 0) ? 'selected' : ''; ?>>In-Active</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Trainee Role</label>
                                                                <select id="deviceuser_role" name="deviceuser_role" class="form-control input-sm select2" placeholder="Please select"  >
                                                                    <option value="">Please Select</option>
                                                                    <?php foreach ($roleResult as $rl) { ?>
                                                                    <option value="<?php echo $rl->arid ?>" <?php echo($rl->arid==$result->deviceuser_role ? 'selected' : '') ?>><?php echo $rl->rolename ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <div class="row">
<!--                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label class="mt-checkbox mt-checkbox-outline" for="corporate_partner"> It Is Corporate Partner?
                                                                        <input id="corporate_partner" name="corporate_partner" type="checkbox" value="1" < ?php echo $result->corporate_partner == 1 ? 'checked' : ''; ?> /><span></span>
                                                                    </label>
                                                                </div>
                                                            </div>                                                        -->
                                                            <div class="col-md-6">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="empcode_restrict"> On Registration required Employee code?
                                                                    <input id="empcode_restrict" name="empcode_restrict" type="checkbox" value="1" <?php echo $result->empcode_restrict == 1 ? 'checked' : ''; ?>  onclick="SettingsCheck('empcode_restrict', 'empcode');"/><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="eotp_required"> Employee OTP Verification Required?
                                                                    <input id="eotp_required" name="eotp_required" type="checkbox" value="1" <?php echo $result->eotp_required == 1 ? 'checked' : ''; ?>  /><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="my-line"></div>
                                                        <div class="row">
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Address 1</label>
                                                                    <input type="text" name="address_i" id="address_i" maxlength="250" class="form-control input-sm" value="<?php echo $result->address_i; ?>">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Address 2</label>
                                                                    <input type="text" name="address_ii" id="address_ii" maxlength="250" class="form-control input-sm" value="<?php echo $result->address_ii; ?>">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Country</label>
                                                                    <select id="country_id" name="country_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                        
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>State</label>
                                                                    <select id="state_id" name="state_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                        
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">City</label>
                                                                    <select id="city_id" name="city_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                        
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Pin Code</label>
                                                                    <input type="text" name="pincode" id="pincode" maxlength="250" class="form-control input-sm" value="<?php echo $result->pincode; ?>">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Contact Person</label>
                                                                    <input type="text" name="contact_person" id="contact_person" maxlength="50" class="form-control input-sm"  value="<?php echo $result->contact_person; ?>">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Contact No</label>
                                                                    <input type="text" name="contact_no" id="contact_no" maxlength="50" class="form-control input-sm" value="<?php echo $result->contact_no; ?>">                                 
                                                                </div>
                                                            </div>                                                            
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Email</label>
                                                                    <input type="text" name="email" id="email" maxlength="250" class="form-control input-sm"  value="<?php echo $result->email; ?>">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">website</label>
                                                                    <input type="text" name="website" id="website" maxlength="250" class="form-control input-sm"  value="<?php echo $result->website; ?>">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Note</label>
                                                                    <textarea rows="4" class="form-control input-sm" id="remarks" name="remarks" placeholder=""><?php echo $result->remarks; ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">      
                                                            <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Company Logo</label>
                                                                <div class="form-control fileinput fileinput-exists" style="    border: none;height:auto;" data-provides="fileinput">
                                                                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                                                <img src="<?php echo base_url().'assets/uploads/no_image.png'?>" alt=""/>
                                                                            </div>
                                                                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                                                <img src="<?php echo base_url().'assets/uploads/'.($result->company_logo!='' ?'company/'.$result->company_logo : 'no_image.png'); ?>" alt=""/>
                                                                            </div>
                                                                            <div>
                                                                                <span class="btn default btn-file">
                                                                                <span class="fileinput-new">
                                                                                Select image </span>
                                                                                <span class="fileinput-exists">
                                                                                Change </span>
                                                                                <input type="file" name="company_logo" id="company_logo">
                                                                                </span>
                                                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput" onclick="removeLogo();">
                                                                                    Remove 
                                                                                </a>
                                                                                <input type="hidden" name="removeLogo" id="removeLogo" value="0"> 
                                                                            </div>
                                                                    </div><br/>
                                                                <span class="text-muted">(Extensions allowed: .png , .gif, .jpg, .jpeg, .bmp)  width:266px, height:144px)</span>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        
                                                        <div class="row">      
                                                            <div class="col-md-12 text-right">  
                                                                <button type="button" id="company-submit" name="company-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" 
                                                                                                      data-style="expand-right" onclick="SaveCompanyData();">
                                                                    <span class="ladda-label">Update</span>
                                                                </button>
                                                                <a href="<?php echo site_url("company"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                            </div>
                                                        </div>
                                                      </form>
                                                    </div>    
                                                    <div class="tab-pane " id="tab_attachments">
                                                    </div>
                                                    <div class="tab-pane  <?php echo ($step == 2 ? 'active"' : ''); ?>" id="tab_users">
                                                        <form role="form" id="frmTrainee" name="frmTrainee" action="<?php echo base_url() . 'company/export_trainee/'.base64_encode($result->id); ?>" method="post">
                                                        <div class="form-body">
                                                            <div class="row margin-bottom-10">
                                                                <div class="col-md-3" >    
                                                                    <div class="form-group">
                                                                        <label class="mt-checkbox mt-checkbox-outline" for="testerfilter"> Testing Team
                                                                            <input id="testerfilter" name="testerfilter" type="checkbox" value="1" onclick="DatatableUsersRefresh();" /><span></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-9 text-right">
                                                                    <button type="button" id="send-otp" name="send-otp" data-loading-text="Please wait..." 
                                                                        class="btn btn-orange btn-sm" data-style="expand-right" onclick="SendOTP(1);">
                                                                        <span class="ladda-label"><i class="fa fa-envelope"></i>&nbsp; Send OTP</span>
                                                                    </button>
                                                                    <button type="button" id="send-opt" name="send-opt" data-loading-text="Please wait..." 
                                                                        class="btn btn-orange btn-sm" data-style="expand-right" onclick="SendOTP(2);">
                                                                        <span class="ladda-label"><i class="fa fa-envelope"></i>&nbsp; Send OTC</span>
                                                                    </button>
                                                                    <button type="button" id="exportcm" name="exportcm" data-loading-text="Please wait..." 
                                                                        class="btn btn-orange btn-sm" data-style="expand-right"  onclick="exportConfirm()">
                                                                        <span class="ladda-label"><i class="fa fa-file-excel-o"></i>&nbsp; Export</span>
                                                                    </button>
                                                                    <a class="btn btn-orange btn-sm" id="btnaddpanel3"  href="<?php echo base_url() . 'company/NewUserAdd/'.base64_encode($result->id).'/1'; ?>" 
                                                    data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-file-excel-o"></i>&nbsp;Import </a>
                                                                    <a class="btn btn-orange btn-sm" id="btnaddpanel3"  href="<?php echo base_url() . 'company/NewUserAdd/'.base64_encode($result->id).'/2'; ?>" 
                                                    data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus"></i>&nbsp;Add New Trainee </a>
                                                                </div>
                                                            </div>
                                                            <div class="row ">
                                                                <div class="col-md-12" id="workshop_panel" >
                                                                    <table class="table  table-bordered table-hover table-checkable order-column" id="UsersTable">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>
                                                                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                                        <input type="checkbox" class="all group-checkable" name="check" id="check" data-set="#UsersTable .checkboxes" />
                                                                                        <span></span>
                                                                                    </label>
                                                                                </th>
                                                                                <th>ID</th>
                                                                                <th>Employee ID</th>
                                                                                <th> Name</th>
                                                                                <th>Email</th>
                                                                                <th>Phone No</th>
                                                                                <th>OTP</th>
                                                                                <th>OTP Last Attempt</th>
                                                                                <th>Area</th>
                                                                                <th>Tester</th>
                                                                                <th>Status</th>
                                                                                <th>Actions</th>
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
                                                    <div class="tab-pane  " id="tab_mailsettings">
                                                        <form id="frmSMTP" name="frmSMTP" method="POST"  action="<?php echo $base_url; ?>company/Smtp_save/<?php echo base64_encode($result->id); ?>">                                                                                           
                                                                <div class="tab-content">                                                                                                                                                 
                                                                    <div class="row">    
                                                                        <div class="col-md-3">       
                                                                            <div class="form-group">
                                                                                <label class="">SMTP Server Host Name<span class="required"> * </span></label>
                                                                                <input type="text" name="host_name" id="host_name" maxlength="250" class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails)>0 ? $smtpDetails->smtp_ipadress : '') ?>">  
                                                                                <input type="hidden" name="edit_id" id="edit_id" class="form-control input-sm" autocomplete="off" value="">  
                                                                                <span class="help-block">Ex. smtp.gmail.com/smtp.mail.yahoo.com</span>
                                                                            </div>
                                                                        </div>    
                                                                        <div class="col-md-3">       
                                                                            <div class="form-group">
                                                                                <label class="">Smtp Secure (SSL/TLS)<span class="required"> * </span></label>
                                                                                <select id="smtp_secure" name="smtp_secure" class="form-control input-sm " placeholder="Please select" >                                                                                    
                                                                                    <option value="">Please Select</option>
                                                                                    <option value="ssl" <?php echo count((array)$smtpDetails) > 0 && $smtpDetails->smtp_secure == 'ssl' ? 'selected' : '' ; ?>>SSL</option>
                                                                                    <option value="tls" <?php echo count((array)$smtpDetails) > 0 && $smtpDetails->smtp_secure == 'tls' ? 'selected' : ''; ?>>TLS</option>
                                                                                </select>                                                                                                                              
                                                                            </div>
                                                                        </div>   
                                                                        <div class="col-md-3">       
                                                                            <div class="form-group">
                                                                                <label class="">Port No<span class="required"> * </span></label>
                                                                                <input type="text" name="port_no" id="port_no"  class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails)>0 ? $smtpDetails->smtp_portno : '') ?>">
                                                                                <span class="help-block">465 for SSL, 587 for TLS</span>
                                                                            </div>
                                                                        </div>                                                            
                                                                        <div class="col-md-3">    
                                                                            <div class="form-group last">
                                                                                <label>Authentication</label>
                                                                                <select id="authentication" name="authentication" class="form-control input-sm " placeholder="Please select" >
                                                                                    <option value="">Please Select</option>
                                                                                    <option value="1" <?php echo count((array)$smtpDetails) > 0 && $smtpDetails->smtp_authentication == 1 ? 'selected' : '' ; ?>>Yes</option>
                                                                                    <option value="0" <?php echo count((array)$smtpDetails) > 0 && $smtpDetails->smtp_authentication == 0 ? 'selected' : ''; ?>>No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">    
                                                                        <div class="col-md-3">       
                                                                            <div class="form-group">
                                                                                <label class="">User Name<span class="required"> * </span></label>
                                                                                <input type="text" name="user_name" id="user_name" maxlength="250" placeholder="Sender email address" class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails)>0 ? $smtpDetails->smtp_username : '') ?>">
                                                                                <span class="help-block">Your full email address (name@domain.com.)</span>
                                                                            </div>
                                                                        </div>                                                        
                                                                        <div class="col-md-3">       
                                                                            <div class="form-group">
                                                                                <label class="">Password<span class="required"> * </span></label>
                                                                                <input type="password" name="password" id="password" placeholder="Sender email password"  class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails)>0 ? $smtpDetails->smtp_password : '') ?>">
                                                                                <span class="help-block">Your account's password.</span>
                                                                            </div>
                                                                        </div>
                                                                     
                                                                        <div class="col-md-3">       
                                                                            <div class="form-group">
                                                                                <label class="">Alias Name<span class="required"> * </span></label>
                                                                                <input type="text" name="alias_name" id="alias_name" maxlength="250" placeholder="From Name" class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails)>0 ? $smtpDetails->smtp_alias : '') ?>">                                                                  
                                                                            </div>
                                                                        </div>                                                                                                                   
                                                                        <div class="col-md-3">    
                                                                            <div class="form-group last">
                                                                                <label>Status</label>
                                                                                <select id="smtp_status" name="status" class="form-control input-sm " placeholder="Please select" >
                                                                                    <option value="1" <?php echo count((array)$smtpDetails) > 0 && $smtpDetails->status == 1 ? 'selected' : '' ; ?>>Active</option>
                                                                                    <option value="0" <?php echo count((array)$smtpDetails) > 0 && ($smtpDetails->status == 0) ? 'selected' : ''; ?>>In-Active</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>    
                                                                    <div class="row">      
                                                                        <div class="col-md-12 text-right">  
                                                                            <button type="button" id="mailsettings-submit" name="mailsettings-submit" data-loading-text="Please wait..." 
                                                                                    class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-right" onclick="SaveSmtpData()">
                                                                                <span class="ladda-label">Update</span>
                                                                            </button>
                                                                            <button type="button" id="mailsettings-submit" name="mailsettings-submit" data-loading-text="Please wait..." 
                                                                                    class="btn btn-default btn-cons ladda-button mt-progress-demo" data-style="expand-right" onclick="ResetSmtpData()">
                                                                                <span class="ladda-label">Reset</span>
                                                                            </button>
                                                                            
                                                                        </div>
                                                                    </div>
                                                                    <div class="panel panel-success margin-top-10">
						<div class="panel-heading">
							<h3 class="panel-title">Note</h3>
						</div>
						<div class="panel-body">
							<ul>
								<li>Before you start the configuration, make sure that Less secure apps is enabled for the desired account.</li>
                                                                <li>Connect to smtp.gmail.com on port 465, if you’re using SSL. (Connect on port 587 if you’re using TLS.) <a target="_blank" href="https://support.google.com/a/answer/176600?hl=en" >more details</a></li>
                                                                <li>Make sure all settings have been saved.</li>
							</ul>
						</div>
					</div>
                                                                    <h4 class="form-section " style="border-bottom: 1px solid #eee;"><span class="required">Test Mail</span> :</h4>
                                                                    <div class="row">     
                                                                        <div class="col-md-4">       
                                                                            <div class="form-group">
                                                                                <input type="text" name="testmail" id="testmail" maxlength="250" class="form-control input-sm" placeholder="Email-Id" value="">                                                                  
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2">  
                                                                            <button type="button" id="modal-create-submit" name="modal-create-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" onclick="sendTestMail();">
                                                                                Send </button>                                                        
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            
                                                        </form>
                                                        
                                                    </div>
                                                    <div class="tab-pane" id="tab_settings">
                                                        <form id="frmSetting" name="frmSetting" method="POST"  action="<?php echo $base_url; ?>company/SettingUpdate/<?php echo base64_encode($result->id); ?>"> 
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="users_restrict"> On Android/IOS Application Restrict Users?
                                                                    <input id="users_restrict" name="users_restrict" type="checkbox" value="1" <?php echo $result->users_restrict == 1 ? 'checked' : ''; ?> onclick="SettingsCheck('users_restrict', 'app_users_count');"/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">No. Of Users Allowed<span class="required"> * </span></label>
                                                                    <input type="text" name="app_users_count" id="app_users_count" maxlength="250" class="form-control input-sm" disabled value="<?php echo $result->app_users_count; ?>">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="portal_restrict"> On Portal Application Restrict Users?
                                                                    <input id="portal_restrict" name="portal_restrict" type="checkbox" value="1" <?php echo $result->portal_restrict == 1 ? 'checked' : ''; ?> onclick="SettingsCheck('portal_restrict', 'portal_users_count');"/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">No. Of Users Allowed<span class="required"> * </span></label>
                                                                    <input type="text" name="portal_users_count" id="portal_users_count" maxlength="250" class="form-control input-sm" disabled  value="<?php echo $result->portal_users_count; ?>">                                 
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="personal_form_required"> On Android/IOS Application Show Personal Details Form?
                                                                    <input id="personal_form_required" name="personal_form_required" type="checkbox" value="1" <?php echo $result->personal_form_required == 1 ? 'checked' : ''; ?> onclick="SettingsCheck('personal_form_required', 'form_id');"/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>Personal Detail Form Name<span class="required"> * </span></label>
                                                                    <select id="form_id" name="form_id" class="form-control input-sm select2" placeholder="Please select" disabled  >
                                                                        <option value="">Please Select</option>
                                                                        <?php foreach ($FeedbackForm as $frm) { ?>
                                                                            <option value="<?php echo $frm->id ?>" <?php echo ($result->form_id == $frm->id) ? 'selected' : ''; ?>><?php echo $frm->form_name ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="restrict_workshop"> Restrict to create no. of Workshop?
                                                                    <input id="restrict_workshop" name="restrict_workshop" type="checkbox" value="1" <?php echo $result->restrict_workshop == 1 ? 'checked' : ''; ?> onclick="SettingsCheck('restrict_workshop', 'workshop_count');"/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>No. of Workshop Allowed<span class="required"> * </span></label>
                                                                    <input type="text" name="workshop_count" id="workshop_count" maxlength="250" class="form-control input-sm" disabled  value="<?php echo $result->workshop_count; ?>">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="restrict_workshop_users"> Restrict to create no. of Users in a each Workshop?
                                                                    <input id="restrict_workshop_users" name="restrict_workshop_users" type="checkbox" value="1" <?php echo $result->restrict_workshop_users == 1 ? 'checked' : ''; ?> onclick="SettingsCheck('restrict_workshop_users', 'workshop_users_count');"/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>No. of Users Allowed in a each Workshop<span class="required"> * </span></label>
                                                                    <input type="text" name="workshop_users_count" id="workshop_users_count" maxlength="250" class="form-control input-sm" value="<?php echo $result->workshop_users_count; ?>" disabled>                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="restrict_workshop_question"> Restrict to create/import no. of Question in a each Workshop?
                                                                    <input id="restrict_workshop_question" name="restrict_workshop_question" type="checkbox" value="1" <?php echo $result->restrict_workshop_question == 1 ? 'checked' : ''; ?> onclick="SettingsCheck('restrict_workshop_question', 'workshop_question_count');"/><span></span>
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
                                                                    <input id="restrict_feedback" name="restrict_feedback" type="checkbox" value="1" <?php echo $result->restrict_feedback == 1 ? 'checked' : ''; ?> onclick="SettingsCheck('restrict_feedback', 'feedback_count');"/><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>No. of Feedback Allowed<span class="required"> * </span></label>
                                                                    <input type="text" name="feedback_count" id="feedback_count" maxlength="250" class="form-control input-sm" disabled value="<?php echo $result->feedback_count; ?>">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="restrict_feedback_users"> Restrict to create no. of Users in a each Feedback?
                                                                    <input id="restrict_feedback_users" name="restrict_feedback_users" type="checkbox" value="1" <?php echo $result->restrict_feedback_users == 1 ? 'checked' : ''; ?>  onclick="SettingsCheck('restrict_feedback_users', 'feedback_users_count');" /><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>No. of Users Allowed In in a each Feedback<span class="required"> * </span></label>
                                                                    <input type="text" name="feedback_users_count" id="feedback_users_count" maxlength="250" class="form-control input-sm" value="<?php echo $result->feedback_users_count; ?>"  disabled>                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="restrict_feedback_question"> Restrict to create/import no. of Question in a each Feedback?
                                                                    <input id="restrict_feedback_question" name="restrict_feedback_question" type="checkbox" value="1" <?php echo $result->restrict_feedback_question == 1 ? 'checked' : ''; ?>  onclick="SettingsCheck('restrict_feedback_question', 'feedback_question_count');" /><span></span>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label>No. of Question Allowed in a each Feedback<span class="required"> * </span></label>
                                                                    <input type="text" name="feedback_question_count" id="feedback_question_count" maxlength="250" class="form-control input-sm" disabled value="<?php echo $result->feedback_question_count; ?>">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                            <div class="row">      
                                                                <div class="col-md-12 text-right">  
                                                                    <button type="button" id="settings-submit" name="settings-submit" data-loading-text="Please wait..." 
                                                                            class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-right" onclick="SettingUpdate();">
                                                                        <span class="ladda-label">Update</span>
                                                                    </button>
                                                                    <a href="<?php echo site_url("company"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                                </div>
                                                            </div>
                                                        </form>    
                                                    </div>    
                                                    <div class="tab-pane " id="tab_minute">
                                                    <form id="frmMinute" name="frmMinute" method="POST" >    
                                                    <div class="row"> 
                                                        <div class="col-md-12">    
                                                            <table class="table table-bordered table-striped" id="company_minute">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Assessment Date</th>
                                                                        <th>Billed Minute</th>
                                                                        <th>Unbilled Minute</th>
                                                                        <th>Allocated Minute</th>
                                                                        <th><button class="btn btn-primary btn-xs btn-mini " type="button" onclick="add_dateminute();"><i class="fa fa-plus"></i></button></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $m_row = 1;
                                                                    if (count((array)$CmpMinuteResult) > 0) {
                                                                        foreach ($CmpMinuteResult as $k => $cm) {  
                                                                    ?>
                                                                    <tr id="dtrow_<?php echo $m_row ?>">
                                                                        <td class="form-group">
                                                                            <div class="input-group date-picker input-daterange <?php echo (!$exist_assess ? 'date_range' : ''); ?>" data-date="" data-date-format="dd-mm-yyyy">
                                                                                <input type="text"  class="form-control " id="from_date<?php echo $m_row; ?>" name="from_date[]" autocomplete="off" value="<?php echo date('d-m-Y',  strtotime($cm->from_date))?>"  placeholder="DD-MM-YYYY" <?php echo ($exist_assess ? 'readonly' : ''); ?>>
                                                                                 <span class="input-group-addon">To</span>
                                                                                <input type="text"  class="form-control " id="to_date<?php echo $m_row; ?>" name="to_date[]" autocomplete="off" value="<?php echo date('d-m-Y',  strtotime($cm->to_date))?>"  placeholder="DD-MM-YYYY" <?php echo ($exist_assess ? 'readonly' : ''); ?>>
                                                                            </div>
                                                                        </td>
                                                                        <td class="form-group">
                                                                            <input type="number"  class="form-control " id="billed_min<?php echo $m_row; ?>" name="billed_min[]" autocomplete="off" value="<?php echo $cm->billed_minute ?>"> 
                                                                        </td>
                                                                        <td class="form-group">
                                                                            <input type="number"  class="form-control " id="unbilled_min<?php echo $m_row; ?>" name="unbilled_min[]" autocomplete="off" value="<?php echo $cm->unbilled_minute ?>"> 
                                                                        </td>
                                                                        <td class="form-group">
                                                                            <input type="number"  class="form-control " id="allocated_min<?php echo $m_row; ?>" name="allocated_min[]" autocomplete="off" value="<?php echo $cm->allocated_minute ?>" onchange="check_allocate(<?php echo $m_row; ?>);"> 
                                                                        </td>
                                                                        <td>
                                                                            <button class="btn btn-danger btn-xs btn-mini " type="button" onclick="remove_dateminute(<?php echo $m_row; ?>);" ><i class="fa fa-times"></i></button>
                                                                        </td>
                                                                        <input type="hidden" name="row_id[]" value="<?php echo $cm->id; ?>">
                                                                    </tr>
                                                                    <?php 
                                                                     $m_row++;
                                                                      }
                                                                  }else{ ?>
                                                                    <tr id="dtrow_<?php echo $m_row ?>">
                                                                        <td class="form-group">
                                                                            <div class="input-group date-picker input-daterange date_range" data-date="" data-date-format="dd-mm-yyyy">
                                                                                <input type="text"  class="form-control " id="from_date<?php echo $m_row; ?>" name="from_date[]" autocomplete="off" value=""  placeholder="DD-MM-YYYY" >
                                                                                 <span class="input-group-addon">To</span>
                                                                                <input type="text"  class="form-control " id="to_date<?php echo $m_row; ?>" name="to_date[]" autocomplete="off" value=""  placeholder="DD-MM-YYYY" >
                                                                            </div>
                                                                        </td>
                                                                        <td class="form-group">
                                                                            <input type="number"  class="form-control " id="billed_min<?php echo $m_row; ?>" name="billed_min[]" autocomplete="off" value=""> 
                                                                        </td>
                                                                        <td class="form-group">
                                                                            <input type="number"  class="form-control " id="unbilled_min<?php echo $m_row; ?>" name="unbilled_min[]" autocomplete="off" value=""> 
                                                                        </td>
                                                                        <td class="form-group">
                                                                            <input type="number"  class="form-control " id="allocated_min<?php echo $m_row; ?>" name="allocated_min[]" autocomplete="off" value=""> 
                                                                        </td>
                                                                        <td>
                                                                            <!--<button class="btn btn-danger btn-xs btn-mini " type="button" onclick="remove_dateminute(< ?php echo $m_row; ?>);" ><i class="fa fa-times"></i></button>-->
                                                                        </td>
                                                                        <input type="hidden" name="row_id[]" value="">
                                                                    </tr>
                                                                  <?php 
                                                                    $m_row++;
                                                                  } ?>
                                                                </tbody>   
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">  
                                                        <div class="col-md-12 text-right">  
                                                            <button type="button" id="minute-submit" name="minute-submit" data-loading-text="Please wait..." 
                                                                    class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-right" onclick="MinuteUpdate();">
                                                                <span class="ladda-label">Update</span>
                                                            </button>
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
<div class="modal fade" id="LoadModalFilter" role="basic" aria-hidden="true" data-width="400">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <img src="<?php echo base_url(); ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                <span>
                    &nbsp;&nbsp;Loading... </span>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('inc/inc_footer_script'); ?>
<script src="<?php echo $base_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script>
        var NewUsersArrray = [];
        var oTable = null;
        var row_no = '<?php echo $m_row; ?>';
        var base_url = "<?php echo $base_url; ?>";
        var EncodeEdit_id = "<?php echo base64_encode($result->id); ?>";
        var AddEdit='E';
        $('.all').click(function () {
                    if ($(this).is(':checked')) {
                        $("input[name='id[]']").prop('checked', true);
                    } else {
                        $("input[name='id[]']").prop('checked', false);
                    }
                   
//                    $( ".date_range" ).datepicker({
//                        rtl: App.isRTL(),
//                        orientation: "left",
//                        autoclose: true,
//                        format: 'dd-mm-yyyy',
//                        startDate: '+0d'
//                    })                        
         });
                
    </script>
    <script type="text/javascript" src="<?php echo $base_url; ?>assets/customjs/company_validation.js"></script>
        <script>
            jQuery(document).ready(function () {
                 if (jQuery().datepicker) {
                        $('.date_range').datepicker({
                            rtl: App.isRTL(),
                            orientation: "left",
                            autoclose: true,
                            format: 'dd-mm-yyyy'
                        });
                    }
                DatatableUsersRefresh();
                        $('#country_id').select2({
                            placeholder: '',
                            multiple: false,
                            separator: ',',
                            ajax: {
                                url: base_url+"company/ajax_populate_country",
                                dataType: 'json',
                                quietMillis: 100,
                                data: function (term, page) {
                                    return {
                                        search: term,
                                        page_limit: 10
                                    };
                                },
                                results: function (data, page) {
                                    var more = (page * 30) < data.total_count;
                                    return {results: data.results, more: more};
                                }
                            },
                            initSelection: function (element, callback) {
                                return $.getJSON(base_url+"company/ajax_populate_country?id=<?php echo $result->country_id; ?>", null, function (data) {
                                                    return callback(data);
                                                });
                                            }
                                        });
                                        $('#state_id').select2({
                                            placeholder: '',

                                            multiple: false,
                                            separator: ',',
                                            ajax: {
                                                url: base_url+"company/ajax_populate_state",
                                                dataType: 'json',
                                                quietMillis: 100,
                                                data: function (term, page) {
                                                    return {
                                                        country_id: $('#country_id').val(),
                                                        search: term,
                                                        page_limit: 10
                                                    };
                                                },
                                                results: function (data, page) {
                                                    var more = (page * 30) < data.total_count;
                                                    return {results: data.results, more: more};
                                                }
                                            },
                                            initSelection: function (element, callback) {
                                                return $.getJSON(base_url+"company/ajax_populate_state?id=<?php echo $result->state_id; ?>", null, function (data) {
                                                                    return callback(data);
                                                                });
                                                            }
                                                        });
                                                        $("#state_id").select2("trigger", "select", {
                                                            data: {id: "<?php echo $result->state_id; ?>", text: "<?php echo $result->state_name; ?>"}
                                                        });
                                                        $('#city_id').select2({
                                                            placeholder: '',

                                                            multiple: false,
                                                            separator: ',',
                                                            ajax: {
                                                                url: base_url+"company/ajax_populate_city",
                                                                dataType: 'json',
                                                                quietMillis: 100,
                                                                data: function (term, page) {
                                                                    return {
                                                                        state_id: $('#state_id').val(),
                                                                        search: term,
                                                                        page_limit: 10
                                                                    };
                                                                },
                                                                results: function (data, page) {
                                                                    var more = (page * 30) < data.total_count;
                                                                    return {results: data.results, more: more};
                                                                }
                                                            },
                                                            initSelection: function (element, callback) {
                                                                return $.getJSON("<?php echo base_url(); ?>index.php/company/ajax_populate_city?id=<?php echo $result->city_id; ?>", null, function (data) {
                                                                                    return callback(data);
                                                                                });
                                                                            }
                                                                        });
    if ($('#users_restrict').is(':checked')) {
        SettingsCheck('users_restrict', 'app_users_count');
    }
    ;
    if ($('#portal_restrict').is(':checked')) {
        SettingsCheck('portal_restrict', 'portal_users_count');
    }
    ;
    if ($('#otp_required').is(':checked')) {
        SettingsCheck('otp_required', 'otp');
    }
    ;
    if ($('#personal_form_required').is(':checked')) {
        SettingsCheck('personal_form_required', 'form_id');
    }
    ;
    if ($('#restrict_workshop').is(':checked')) {
        SettingsCheck('restrict_workshop', 'workshop_count');
    }
    ;
    if ($('#restrict_feedback').is(':checked')) {
        SettingsCheck('restrict_feedback', 'feedback_count');
    }
    ;
    if ($('#restrict_workshop_question').is(':checked')) {
        SettingsCheck('restrict_workshop_question', 'workshop_question_count');
    }
    ;
    if ($('#restrict_feedback_question').is(':checked')) {
        SettingsCheck('restrict_feedback_question', 'feedback_question_count');
    }
    ;
    if ($('#restrict_workshop_users').is(':checked')) {
        SettingsCheck('restrict_workshop_users', 'workshop_users_count');
    }
    ;
    if ($('#restrict_feedback_users').is(':checked')) {
        SettingsCheck('restrict_feedback_users', 'feedback_users_count');
    }
    ;

    }); 
    
        </script>
    </body>
</html>