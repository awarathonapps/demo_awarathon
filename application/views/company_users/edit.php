<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$asset_url =$this->config->item('assets_url');
$base_url = base_url();
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>
        <!--datattable CSS  Start-->
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!--datattable CSS  End-->
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        
        <link href="<?php echo $asset_url;?>assets/global/plugins/cropper/cropper.css" rel="stylesheet" type="text/css" />
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
                                    <span>CMS Users</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url?>company_users" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                <div id="errordiv" class="alert alert-danger display-hide">
                                    <button class="close" data-close="alert"></button>
                                    You have some form errors. Please check below.
                                    <br><span id="errorlog"></span>
                                </div> 
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            Edit CMS User
                                           <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tabbable-line tabbable-full-width">
                                            <ul class="nav nav-tabs" id="tabs">
                                                <li <?php echo ($Step==1 ? 'class="active"':''); ?>>
                                                    <a href="#tab_overview" data-toggle="tab" id="tab1">Overview</a>
                                                </li>
                                                <!-- <li id="tab2" <?php echo ($Step==2 ? 'class="active"':''); ?>><a href="#tab_userrights" data-toggle="tab">Trainer rights</a></li>
                                                <li id="tab3" ><a href="#tab_workshoprights" data-toggle="tab">Workshop rights</a></li> -->
                                                <li id="tab4" <?php echo ($Step==2 ? 'class="active"':''); ?>><a href="#tab_avatar" data-toggle="tab">Change Avatar</a></li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane <?php echo ($Step==1 ? 'active':''); ?>" id="tab_overview"> 
                                                <form id="frmUsers" name="frmUsers" method="POST"  action="<?php echo $base_url;?>company_users/update/<?php echo base64_encode($result->userid);?>">    
                                                        <?php if ($this->mw_session['company_id'] == "") { ?>
                                                    <div class="row">
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Company Name<span class="required"> * </span></label>
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select"  disabled="">
                                                                    <option value="">Please Select</option>
                                                                    <?php foreach ($CompanySet as $cs) { ?>
                                                                            <option value="<?php echo $cs->id ?>" <?php echo ($result->company_id==$cs->id ? 'Selected':'') ?> ><?php echo $cs->company_name ?></option>
                                                                        <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    <div class="row">
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Employee Code</label>
                                                                <input type="text" name="emp_id" id="emp_id" maxlength="50" value="<?php echo isset($result->emp_id) ? $result->emp_id : ''; ?>" class="form-control input-sm">                                 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Region<span class="required"> * </span></label>
                                                                <select id="region_id" name="region_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                    <option value="">Please Select</option>
                                                                    <?php foreach ($TrainerRegionSet as $region) { ?>
                                                                    <option value="<?php echo $region->id ?>" <?php echo($region->id == $result->region_id ? 'selected' : '') ?>><?php echo $region->region_name ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Login Type<span class="required"> * </span></label>
                                                                <select id="login_type" name="login_type" class="form-control input-sm select2" placeholder="Please select"  >
                                                                    <?php foreach ($LoginType as $lt) { ?>
                                                                    <option value="<?php echo $lt->id ?>" <?php echo ($result->login_type==$lt->id)?'selected':'';?>><?php echo $lt->name ?></option>                                                                    
                                                                    <?php }?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">    
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Login ID<span class="required"> * </span></label>
                                                                <input type="text" name="loginid" id="loginid" maxlength="80" class="form-control input-sm" value="<?php echo $result->username; ?>">                                 
                                                                <input type="hidden" name="user_id" id="user_id" maxlength="80" class="form-control input-sm" value="<?php echo urlencode(base64_encode($result->userid)); ?>">                                 
                                                            </div>
                                                        </div>
                                                         <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Password</label>
                                                                    <input type="password" name="tpassword" id="tpassword" maxlength="50" class="form-control input-sm">                                 
                                                                    <span class="hint" style="font-size:10px;color: blue;">Leave blank if you don't want to change your CMS password.</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Confirm Password</label>
                                                                    <input type="password" name="tconfirmpassword" id="tconfirmpassword" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>        
                                                        </div>
                                                    <div class="row">
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Department<span class="required"> * </span></label>
                                                                <input type="text" name="depart" id="depart" maxlength="250" value="<?php echo $result->department; ?>" class="form-control input-sm">                                 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Designation<span class="required"> * </span></label>
                                                                <select id="designation" name="designation" class="form-control input-sm select2">
                                                                    <option value="">Please Select</option>
                                                                    <?php foreach ($DesignationResult as $desig) { ?>
                                                                        <option value="<?php echo $desig->id ?>" <?php echo($desig->id==$result->designation_id ? 'selected' : '') ?>><?php echo $desig->description ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Role<span class="required"> * </span></label>
                                                                <select id="roleid" name="roleid" class="form-control input-sm select2" placeholder="Please select"  >
                                                                    <option value="">Please Select</option>
                                                                    <?php foreach ($Role as $rl) { ?>
                                                                    <option value="<?php echo $rl->arid ?>" <?php echo($rl->arid==$result->role ? 'selected' : '') ?>><?php echo $rl->rolename ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Status<span class="required"> * </span></label>
                                                                <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                    <option value="1" <?php echo ($result->status==1)?'selected':'';?>>Active</option>
                                                                    <option value="0" <?php echo ($result->status==0)?'selected':'';?>>In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>                                                        
                                                    </div>
                                                    <div class="my-line"></div>
                                                    <div class="row"> 
                                                        <div class="col-md-1">    
                                                            <div class="form-group">
                                                                <label>Salutation</label>
                                                                <select id="salutation" name="salutation" class="form-control input-sm select2" placeholder="Please select" >
                                                                    <option value="Mr." <?php echo ($result->status=='Mr.')?'selected':'';?>>Mr.</option>
                                                                    <option value="Mrs." <?php echo ($result->status=='Mrs.')?'selected':'';?>>Mrs.</option>
                                                                    <option value="Miss" <?php echo ($result->status=='Miss')?'selected':'';?>>Miss</option>
                                                                    <option value="Dr." <?php echo ($result->status=='Dr.')?'selected':'';?>>Dr.</option>
                                                                    <option value="Prof." <?php echo ($result->status=='Prof.')?'selected':'';?>>Prof.</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">First name<span class="required"> * </span></label>
                                                                <input type="text" name="first_name" id="first_name" maxlength="50" class="form-control input-sm" value="<?php echo $result->first_name; ?>">                                 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Last name<span class="required"> * </span></label>
                                                                <input type="text" name="last_name" id="last_name" maxlength="50" class="form-control input-sm" value="<?php echo $result->last_name; ?>">                                 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Email<span class="required"> * </span></label>
                                                                <input type="text" name="email" id="email" maxlength="250" class="form-control input-sm" value="<?php echo $result->email; ?>">                                 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Mobile No.</label>
                                                                <input type="text" name="mobile" id="mobile" maxlength="50" class="form-control input-sm" value="<?php echo $result->mobile; ?>">                                 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Alternate Email</label>
                                                                <input type="text" name="email2" id="email2" maxlength="250" class="form-control input-sm" value="<?php echo $result->email2; ?>">                                 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Contact No</label>
                                                                <input type="text" name="contactno" id="contactno" maxlength="50" class="form-control input-sm" value="<?php echo $result->contactno; ?>">                                 
                                                            </div>
                                                        </div>
                                                    
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Fax</label>
                                                                <input type="text" name="fax" id="fax" maxlength="50" class="form-control input-sm" value="<?php echo $result->fax; ?>">                                 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="my-line"></div>
                                                    <div class="row">
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Address 1</label>
                                                                <input type="text" name="address" id="address" maxlength="250" class="form-control input-sm" value="<?php echo $result->address1; ?>">                                 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Address 2</label>
                                                                <input type="text" name="address2" id="address2" maxlength="250" class="form-control input-sm" value="<?php echo $result->address2; ?>">                                 
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
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">City</label>
                                                                <select id="city_id" name="city_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                    <option value="">Please Select</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Note</label>
                                                                <textarea rows="4" class="form-control input-sm" name="description" placeholder=""><?php echo $result->note; ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">      
                                                        <div class="col-md-12 text-right">  
                                                            <button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-right" onclick="SaveUserData();">
                                                                <span class="ladda-label">Update</span>
                                                            </button>
                                                            <a href="<?php echo site_url("company_users");?>" class="btn btn-default btn-cons">Cancel</a>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" class="avatar-path" name="avatar_path" id="avatar_path" value="<?php echo $result->avatar; ?>">
                                                </form>    
                                                </div>
                                                <!-- <div class="tab-pane < ?php echo ($Step==2 ? 'active':''); ?>" id="tab_userrights">
                                                    <form role="form" id="TrainerRightsFrm" name="TrainerRightsFrm">
                                                        <div class="form-body">
                                                            
                                                            <div class="row userrightsRow"  >  
                                                                <div class="form-group" style="margin-bottom: 0px;">
                                                                        <div class="col-md-6">
                                                                            <div class="radio-list">
                                                                                <label class="radio-inline" style="padding-left: 40px;">
                                                                                    <input type="radio" class="TrainerrightsOpt" name="userrights_type" id="userrights_type1" value="1" <?php echo ($result->userrights_type==1 || $result->userrights_type==0 ? 'checked':''); ?>>All Trainer</label>
                                                                                    <label class="radio-inline">
                                                                                    <input type="radio" class="TrainerrightsOpt" name="userrights_type" id="userrights_type2" value="2" <?php echo ($result->userrights_type==2 ? 'checked':''); ?>> Custom Select </label>
                                                                            </div>
                                                                        </div>
                                                                     <div class="col-md-6 trainer_panel" <?php echo ($result->userrights_type !=2 ? 'style=display:none' :""); ?>>
                                                                        <button type="button" id="confirm1" name="confirm1" style="float:right;" class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="AddTRightsMore()">
                                                                            <span class="ladda-label">Add More Rights</span>
                                                                        </button>
                                                                     </div>   
                                                                </div>
                                                            </div>
                                                            <div class="row margin-top-10 trainer_panel"  <?php echo ($result->userrights_type !=2 ? 'style=display:none' :""); ?> >
                                                                <div class="col-md-12">
                                                                    <table class="table table-striped table-bordered table-hover" id="TrainerRightstable" width="100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th width="20%">Region</th>
                                                                                <th width="50%">Trainer</th>
                                                                                <th width="10%">Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            < ?php if($TrainerhtmlData !=""){ 
                                                                                echo $TrainerhtmlData;
                                                                            }else{ ?>
                                                                            <tr id="TRow_0"><td colspan="4">No Trainer rights set</td></tr>
                                                                            < ?php } ?>
                                                                        </tbody>    
                                                                    </table>
                                                                </div>
                                                            </div>
                                                </div>
                                                <div class="row">      
                                                    <div class="col-md-12 text-right">
                                                        <button type="button" id="role-submit2" name="role-submit2" data-loading-text="Please wait..." onclick="UpdateUserRightsData(1)"
                                                                class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo userrightsRow" data-style="expand-right" >
                                                            <span class="ladda-label">Update</span>
                                                        </button>

                                                        <a href="< ?php echo site_url("company_users");?>" class="btn btn-default btn-cons">Cancel</a>
                                                    </div>
                                                </div>
                                                <h4 class="form-section" style=" border-bottom: 1px solid #eee;">Mapped Trainer List :</h4>   
                                                <div class="row margin-top-10">
                                                    <div class="col-md-12">
                                                    <table class="table  table-bordered table-hover table-checkable order-column" id="urights_table">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Region</th>
                                                                <th>Name</th>
                                                                <th>Email</th>
                                                                <th>Designation</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>

                                                    </table>
                                                    </div>
                                                </div>
                                                </form> 
                                            </div>
                                                <div class="tab-pane" id="tab_workshoprights">
                                                    <form role="form" id="WorkshopRightsFrm" name="WorkshopRightsFrm">
                                                        <div class="form-body">
                                                            <div class="row userrightsRow"  >
                                                                <div class="col-md-6">
                                                                <div class="form-group" style="margin-bottom: 0px;">
                                                                    <div class="radio-list">
                                                                        <label class="radio-inline" style="padding-left: 40px;"><input type="radio" class="WorkshoprOpt" name="workshoprights_type" id="workshopOpt1" value="1" <?php echo ($result->workshoprights_type==1 || $result->workshoprights_type==0 ? 'checked':''); ?>> All Workshop </label>
                                                                        <label class="radio-inline"><input type="radio" class="WorkshoprOpt" name="workshoprights_type" id="workshopOpt3" value="2" <?php echo ($result->workshoprights_type==2 ? 'checked':''); ?>> Custom Select </label>
                                                                    </div>      
                                                                </div>
                                                                </div>
                                                                <div class="col-md-6 workshop_panel" <?php echo ($result->workshoprights_type !=2 ? 'style=display:none' :""); ?>>
                                                                    <button type="button" id="confirm2" name="confirm2" style="float:right;" class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="AddWRightsMore()">
                                                                        <span class="ladda-label">Add More Rights</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="row margin-top-10 workshop_panel" <?php echo ($result->workshoprights_type !=2 ? 'style=display:none' :""); ?>>  
                                                                <div class="col-md-12">
                                                                    <table class="table table-striped table-bordered table-hover" id="WorkshopRightstable" width="100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th width="20%">Region</th>
                                                                                <th width="20%">Workshop Type</th>
                                                                                <th width="30%">Workshop</th>
                                                                                <th width="10%">Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php if($WorkshophtmlData !=""){ 
                                                                                echo $WorkshophtmlData;
                                                                            }else{ ?>
                                                                            <tr id="Row_0"><td colspan="4">No Workshop rights set</td></tr>
                                                                            <?php } ?>
                                                                        </tbody>    
                                                                    </table>
                                                                </div>
                                                            </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 text-right">
                                                        <button type="button" id="role-submit1" name="role-submit1" data-loading-text="Please wait..." onclick="UpdateUserRightsData(2)"
                                                                class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo userrightsRow" data-style="expand-right" >
                                                            <span class="ladda-label">Update</span>
                                                        </button>
                                                        <a href="<?php echo site_url("company_users");?>" class="btn btn-default btn-cons">Cancel</a>
                                                    </div>
                                                </div>
                                                <h4 class="form-section" style=" border-bottom: 1px solid #eee;">Mapped Workshop List :</h4>
                                                            <div class="row margin-top-10">
                                                                <div class="col-md-12">
                                                                <table class="table  table-bordered table-hover table-checkable order-column" id="workshop_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>ID</th>
                                                                            <th>Region</th>
                                                                            <th>Workshop Type</th>
                                                                            <th>Workshop Name</th>
                                                                            <th>Start Date</th>
                                                                            <th>End Date</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody></tbody>
                                                                </table>
                                                                </div>
                                                            </div>
                                                </form> 
                                                    </div> -->
                                                    <div class="tab-pane mar <?php echo ($Step==2 ? 'active':''); ?>" id="tab_avatar">
                                                    <div class="row">      
                                                        <div class="col-md-3"> 
                                                            <div id="crop-avatar">
                                                                <!-- Current avatar -->
                                                                <div class="avatar-view" title="Change the avatar">

                                                                    <img id="preview-existing-avatar" src="<?php echo $avatar_url; ?>" alt="Avatar">
                                                                </div>                                                                
                                                                <!-- Cropping modal -->
                                                                <div class="modal dont-fade" id="avatar-modal" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="960">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                    <form class="avatar-form" action="<?php echo $base_url .'company_users/temp_avatar_upload/'.base64_encode($result->userid);?>" enctype="multipart/form-data" method="post">
                                                                        <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                        <h4 class="modal-title" id="avatar-modal-label">Change Avatar</h4>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                        <div class="avatar-body">

                                                                            <!-- Upload image and data -->
                                                                            <div class="avatar-upload">
                                                                                <input type="hidden" class="avatar-src" name="avatar_src" id="avatar_src">
                                                                                <input type="hidden" class="avatar-data" name="avatar_data" id="avatar_data">
                                                                                <label for="avatarInput">Local upload</label>
                                                                                <input type="file" class="avatar-input" id="avatarInput" name="avatar_file">
                                                                            </div>

                                                                            <!-- Crop and preview -->
                                                                            <div class="row">
                                                                                <div class="col-md-9">
                                                                                    <div class="avatar-wrapper"></div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <div class="avatar-preview preview-lg"></div>
                                                                                    <div class="avatar-preview preview-md"></div>
                                                                                    <div class="avatar-preview preview-sm"></div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row avatar-btns">
                                                                                <div class="col-md-9">
                                                                                    <div class="btn-group">
                                                                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="-90" title="Rotate -90 degrees">Rotate Left</button>
                                                                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="-15">-15deg</button>
                                                                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="-30">-30deg</button>
                                                                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="-45">-45deg</button>
                                                                                    </div>
                                                                                    <div class="btn-group">
                                                                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="90" title="Rotate 90 degrees">Rotate Right</button>
                                                                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="15">15deg</button>
                                                                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="30">30deg</button>
                                                                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="45">45deg</button>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <button type="submit" class="btn btn-primary btn-block avatar-save">Done</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        </div>
                                                                        <!-- <div class="modal-footer">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                        </div> -->
                                                                        
                                                                    </form>
                                                                    </div>
                                                                </div>
                                                                </div><!-- /.modal -->

                                                                <!-- Loading state -->
                                                                <div class="loading" aria-label="Loading" role="img" tabindex="-1"></div>
                                                            </div>
                                                        </div>    
                                                    </div>    
                                                </div>
                                            </div> 
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/cropper/cropper.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/avatar_main.js" type="text/javascript"></script>
        <script>
            var NewUsersArrray = [];
            var oTable = null;
            var base_url = "<?php echo $base_url; ?>";
            var AddEdit = 'E';
            var NewUsersArrray = [];
            var NewWorkshopArray = [];
            var MainUsersArray=[];
            var WorkshopArray=[];
            var oTable = null;
            var oTable2 = null;
            var Encode_id= "<?php echo base64_encode($result->userid); ?>";
            var WRightsRow="<?php echo $TotalWRow; ?>";
            var TRightsRow="<?php echo $TotalTRow; ?>";
            
            $('.custClass' ).select2({
                    placeholder: 'All Rights'});
        </script>
        <script type="text/javascript" src="<?php echo $asset_url; ?>assets/customjs/cmsusers_validation.js"></script>
        <script>
            jQuery(document).ready(function() {
                loadurightsTable();
                loadworkshopTable();
                $('.TrainerrightsOpt').click(function () {
                    if ($(this).val()==1) {
                        $(".trainer_panel").hide();
                    } else {
                        $(".trainer_panel").show();
                    }
                    loadurightsTable();
                });
                $('.WorkshoprOpt').click(function () {
                    if ($(this).val()==1) {
                        $(".workshop_panel").hide();
                    } else {
                        $(".workshop_panel").show();
                    }
                    loadworkshopTable();
                });
                $('#country_id').select2({
                    placeholder: '',
                    
                    multiple:false,
                    separator: ',',
                    ajax: {
                        url: base_url+"company_users/ajax_populate_country",
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
                            return { results: data.results, more: more };
                        }
                    },
                    initSelection: function(element, callback) {
                        return $.getJSON(base_url+"company_users/ajax_populate_country?id=<?php echo $result->country; ?>", null, function(data) {
                            return callback(data);
                        });
                    }
                });
                
                $('#state_id').select2({
                    placeholder: '',
                    
                    multiple:false,
                    separator: ',',
                    ajax: {
                        url: base_url+"company_users/ajax_populate_state",
                        dataType: 'json',
                        quietMillis: 100,
                        data: function (term, page) {
                            return {
                                country_id:$('#country_id').val(),
                                search: term,
                                page_limit: 10
                            };
                        },
                        results: function (data, page) {
                            var more = (page * 30) < data.total_count;
                            return { results: data.results, more: more };
                        }
                    },
                    initSelection: function(element, callback) {
                        return $.getJSON(base_url+"company_users/ajax_populate_state?id=<?php echo $result->state; ?>", null, function(data) {
                            return callback(data);
                        });
                    }
                });
                
                $('#city_id').select2({
                    placeholder: '',
                    
                    multiple:false,
                    separator: ',',
                    ajax: {
                        url: base_url+"company_users/ajax_populate_city",
                        dataType: 'json',
                        quietMillis: 100,
                        data: function (term, page) {
                            return {
                                state_id:$('#state_id').val(),
                                search: term,
                                page_limit: 10
                            };
                        },
                        results: function (data, page) {
                            var more = (page * 30) < data.total_count;
                            return { results: data.results, more: more };
                        }
                    },
                    initSelection: function(element, callback) {
                        return $.getJSON(base_url+"company_users/ajax_populate_city?id=<?php echo $result->city; ?>", null, function(data) {
                            return callback(data);
                        });
                    }
                });
                
            });
    </script>
    </body>
</html>