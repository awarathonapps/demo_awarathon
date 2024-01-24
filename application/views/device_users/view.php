<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
$asset_url =$this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>
	<link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
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
                                    <span>Administrator</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Device Users</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url?>device_users" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            View Device User
                                           <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tabbable-line tabbable-full-width">
                                            <ul class="nav nav-tabs" id="tabs">
                                                <li class="active">
                                                    <a href="#tab_overview" data-toggle="tab">Overview</a>
                                                </li>
                                                <li>
                                                    <a href="#tab_deviceinfo" data-toggle="tab">Device Information</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_overview"> 
                                                <form id="deviceUsers" name="deviceUsers" method="POST"  action="<?php echo $base_url;?>device_users/update/<?php echo base64_encode($result->user_id);?>">    
                                                    
                                                    <div class="row">
                                                        <?php if ($Company_id == "") { ?>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Company<span class="required"> * </span></label>
                                                                <select id="company_id" name="company_id" class="form-control input-sm " disabled="">
                                                                <option value="">Please Select </option>
                                                               <?php if(count($CompnayResultSet)>0){
                                                                        foreach ($CompnayResultSet as $key => $value) { ?>
                                                                <option value="<?php echo $value->id ?>" <?php echo($value->id==$result->company_id ? 'selected' : '')?>><?php echo $value->company_name ?> </option>
                                                                <?php }  } ?>
                                                            </select>   
                                                        </div>
                                                        </div>
                                                        <?php } ?>
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Registration Date</label>
                                                                <input type="text" name="registration_date" id="registration_date" maxlength="50" value="<?php echo date('d-m-Y',  strtotime($result->registration_date)); ?>" class="form-control input-sm" readonly="">                                 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="my-line"></div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Employee Code</label>
                                                                <input type="text" name="emp_id" id="emp_id" maxlength="50" value="<?php echo $result->emp_id; ?>" class="form-control input-sm" disabled="">                                 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">First name<span class="required"> * </span></label>
                                                                <input type="text" name="first_name" id="first_name" maxlength="50" class="form-control input-sm" value="<?php echo $result->firstname; ?>" disabled="">                                 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Last name<span class="required"> * </span></label>
                                                                <input type="text" name="last_name" id="last_name" maxlength="50" class="form-control input-sm" value="<?php echo $result->lastname; ?>" disabled="">                                 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Email<span class="required"> * </span></label>
                                                                <input type="text" name="email" id="email" maxlength="250" class="form-control input-sm" value="<?php echo $result->email; ?>" disabled="">                                 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="">L1 Manager<span class="required"> * </span></label>
                                                                <select id="l1_manager" name="l1_manager" class="form-control input-sm select2" placeholder="Please select" style="width: 100%" disabled="">
                                                                    <option value="">Select L1 Manager</option>
                                                                    <?php
                                                                    foreach ($ManagerData as $md) { ?>
                                                                            <option value="<?= $md->userid; ?>" <?php echo ($md->userid == $result->trainer_id ? 'Selected' : ''); ?>><?php echo $md->emp_id!='' ? '['.$md->emp_id.'] '.$md->manager_name : $md->manager_name; ?></option>
                                                                        <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="">L2 Manager<span class="required"> * </span></label>
                                                                <select id="l2_manager" name="l2_manager" class="form-control input-sm select2" placeholder="Please select" style="width: 100%" disabled="">
                                                                    <option value="">Select L2 Manager</option>
                                                                    <?php
                                                                    foreach ($ManagerData as $md) { ?>
                                                                            <option value="<?= $md->userid; ?>" <?php echo ($md->userid == $result->trainer_id_i ? 'Selected' : ''); ?>><?php echo $md->emp_id!='' ? '['.$md->emp_id.'] '.$md->manager_name : $md->manager_name; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Mobile No.<span class="required"> * </span></label>
                                                                <input type="text" name="mobile" id="mobile" maxlength="50" class="form-control input-sm" value="<?php echo $result->mobile; ?>" disabled="">                                 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Employment Year</label>
                                                                <input type="text" name="empyear" id="empyear" maxlength="250" value="<?php echo $result->employment_year; ?>" class="form-control input-sm" disabled="">                                 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">       
                                                               <div class="form-group">
                                                                   <label class="">Education Background</label>
                                                                   <input type="text" name="edubg" id="edubg" maxlength="250" value="<?php echo $result->education_background; ?>" class="form-control input-sm" disabled="">                                 
                                                               </div>
                                                        </div>
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Department</label>
                                                                <input type="text" name="depart" id="depart" maxlength="250" value="<?php echo $result->department; ?>" class="form-control input-sm" disabled="">                                 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Region</label>
                                                                <select id="region_id" name="region_id" class="form-control input-sm " disabled="">
                                                                    <option value="">Select Region</option>
                                                                    <?php if(count($RegionData)>0){
                                                                        foreach ($RegionData as $Rgn) { ?>
                                                                        <option value="<?= $Rgn->id; ?>"<?php echo ($Rgn->id==$result->region_id ? 'Selected' :''); ?>><?php echo $Rgn->region_name; ?></option>
                                                                    <?php } }?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Designation</label>
                                                                <select id="designation_id" name="designation_id" class="form-control input-sm " disabled="">
                                                                    <option value="">Select Designation</option>
                                                                    <?php if(count($DesignationData)>0){
                                                                        foreach ($DesignationData as $dsg) { ?>
                                                                        <option value="<?= $dsg->id; ?>"<?php echo ($dsg->id==$result->designation_id ? 'Selected' :''); ?>><?php echo $dsg->description; ?></option>
                                                                    <?php } }?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="">Area.<span class="required"> * </span></label>
                                                                <input type="text" name="area" id="area" maxlength="50" value="<?php echo $result->area; ?>" class="form-control input-sm" disabled="">                                 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Status<span class="required"> * </span></label>
                                                                <select id="status" name="status" class="form-control input-sm " disabled="" >
                                                                    <option value="1" <?php echo ($result->status==1)?'selected':'';?>>Active</option>
                                                                    <option value="0" <?php echo ($result->status==0)?'selected':'';?>>In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="row">
                                                        <div class="col-md-3" style="margin-top: 25px;">    
                                                            <div class="form-group">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="block"> Block?
                                                                    <input id="block" name="block" type="checkbox" value="1" <?php echo ($result->block ? 'checked':''); ?> disabled=""/><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" style="margin-top: 25px;">    
                                                            <div class="form-group">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="fb_registration">fb Registration Allow
                                                                    <input id="fb_registration" name="fb_registration" type="checkbox" value="1" <?php echo ($result->fb_registration ? 'checked':''); ?> disabled="" /><span></span>
                                                                </label>
                                                            </div>
                                                        </div> 
                                                    </div>     -->
                                                    <div class="row">      
                                                        <div class="col-md-12 text-right">  
                                                            <a href="<?php echo site_url("device_users");?>" class="btn btn-default btn-cons">Cancel</a>
                                                        </div>
                                                    </div>
                                                </form>    
                                                </div> 
                                                
                                                <div class="tab-pane" id="tab_deviceinfo"> 
                                                    <table class="table  table-bordered table-hover table-checkable order-column" id="info_table" >
                                                        <thead>
                                                            <tr>                                                                
                                                                
                                                                <th>Version No.</th>
                                                                <th>IMEI</th>
                                                                <th>UUID</th>
                                                                <th>Model</th>
                                                                <th>Version</th>
                                                                <th>Serial</th>
                                                                <th>Manufacturer</th>
                                                                <th>Platform</th>
                                                                <th>System Date/Time</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if(count($device_info) > 0){                                                                 
                                                                foreach ($device_info as $val) { ?>
                                                                <tr>
                                                                    
                                                                    <td><?php echo $val->version_number ?></td>
                                                                    <td><?php echo $val->imei ?></td>
                                                                    <td><?php echo $val->uuid ?></td>
                                                                    <td><?php echo $val->model ?></td>
                                                                    <td><?php echo $val->version ?></td>
                                                                    <td><?php echo $val->serial ?></td>
                                                                    <td><?php echo $val->manufacturer ?></td>
                                                                    <td><?php echo $val->platform ?></td>
                                                                    <td><?php echo $val->info_dttm ?></td>
                                                                </tr>    
                                                            <?php } } ?>
                                                        </tbody>
                                                    </table>    
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
		<script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
		<script>
			jQuery(document).ready(function() {       
				var table = $('#info_table');
                table.dataTable({
                    destroy: true,                    
                    "pageLength": 10,            
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
                         
                        {'width': '200px','orderable': true,'searchable': true,'targets': [0]}, 
                        {'width': '200px','orderable': true,'searchable': true,'targets': [1]},
                        {'width': '85px','orderable': true,'searchable': true,'targets': [2]},
                        {'width': '130px','orderable': true,'searchable': true,'targets': [3]},
                        {'width': '130px','orderable': false,'searchable': false,'targets': [4]}, 
                        {'width': '65px','orderable': false,'searchable': false,'targets': [5]},
                        {'width': '65px','orderable': false,'searchable': false,'targets': [6]},
                        {'width': '65px','orderable': false,'searchable': false,'targets': [7]},
                        {'width': '165px','orderable': false,'searchable': false,'targets': [8]}
                    ]
                });
			});	
		</script>
    </body>
</html>