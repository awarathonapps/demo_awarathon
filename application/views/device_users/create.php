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
                                    <span>Application</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Device Users</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>New Device User</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url?>device_users" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="alert alert-danger display-hide" id="errordiv">
                                    <button class="close" data-close="alert"></button>
                                    You have some form errors. Please check below.
                                    <br><span id="errorlog"></span>
                                </div>
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            Create Device User
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
                                                        <a href="javascript:void(0)">Device Information</a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tab_overview">    
                                                    <form id="deviceUsers" name="deviceUsers" method="POST"  action="<?php echo $base_url;?>device_users/submit">                                                         
                                                        <?php if ($Company_id == "") { ?>
                                                        <div class="row"> 
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Company<span class="required"> * </span></label>
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" onchange="RegionData();">
                                                                        <option value="">Please Select </option>
                                                                        <?php if(count($CompnayResultSet)>0){
                                                                        foreach ($CompnayResultSet as $key => $value) { ?>
                                                                        <option value="<?php echo $value->id ?>"><?php echo $value->company_name ?> </option>                                                                       
                                                                        <?php }  } ?>
                                                                </select>   
                                                                </div>
                                                            </div>                                                                                                                        
                                                                                                                                                                                    
                                                        </div>
                                                        <?php } ?>
                                                        <div class="my-line"></div>                                                        
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Employee Code</label>
                                                                    <input type="text" name="emp_id" id="emp_id" maxlength="50" value="" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">First name<span class="required"> * </span></label>
                                                                    <input type="text" name="first_name" id="first_name" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Last name<span class="required"> * </span></label>
                                                                    <input type="text" name="last_name" id="last_name" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Email<span class="required"> * </span></label>
                                                                    <input type="text" name="email" id="email" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="" >Password<span class="required"> * </span></label>
                                                                    <input type="password" name="password" id="password" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="" >Confirm Password<span class="required"> * </span></label>
                                                                    <input type="password" name="confirmpassword" id="confirmpassword" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="">L1 Manager<span class="required"> *</span></label>
                                                                    <select id="l1_manager" name="l1_manager" class="form-control input-sm select2" placeholder="Please select" style="width: 100%">
                                                                        <option value="">Select Manager</option>
                                                                        <?php foreach ($ManagerData as $md) { ?>
                                                                            <option value="<?= $md->userid; ?>"><?php echo $md->emp_id!='' ? '['.$md->emp_id.'] '.$md->manager_name : $md->manager_name; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="">L2 Manager<span class="required"> *</span></label>
                                                                    <select id="l2_manager" name="l2_manager" class="form-control input-sm select2" placeholder="Please select" style="width: 100%">
                                                                        <option value="">Select L2 Manager</option>
                                                                        <?php foreach ($ManagerData as $md) { ?>
                                                                            <option value="<?= $md->userid; ?>"><?php echo $md->emp_id!='' ? '['.$md->emp_id.'] '.$md->manager_name : $md->manager_name; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Mobile No.</label>
                                                                    <input type="text" name="mobile" id="mobile" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                             <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Employment Year</label>
                                                                    <input type="text" name="empyear" id="empyear" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                             <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Education Background</label>
                                                                    <input type="text" name="edubg" id="edubg" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Department<span class="required"> * </span></label>
                                                                    <input type="text" name="depart" id="depart" maxlength="250" value="" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                             <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Trainee Region<span class="required"> * </span></label>
                                                                    <select id="region_id" name="region_id" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%" >
                                                                        <option value="">Select Region</option>
																		<?php 
                                                                            foreach ($RegionData as $rg) { ?>
                                                                                <option value="<?= $rg->id; ?>"><?php echo $rg->region_name; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Designation</label>
                                                                    <select id="designation_id" name="designation_id" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%" >
                                                                        <option value="">Select Designation</option>
																		<?php 
                                                                            foreach ($DesignationData as $dd) { ?>
                                                                                <option value="<?= $dd->id; ?>"><?php echo $dd->description; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>    
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label class="">Area</label>
                                                                    <input type="text" name="area" id="area" maxlength="50" value="" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Status<span class="required"> * </span></label>
                                                                    <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="1" selected>Active</option>
                                                                        <option value="0">In-Active</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12 text-right">  
                                                                <button type="button" id="device-submit" name="device-submit" data-loading-text="Please wait..." 
                                                                        class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-right" onclick="SaveUserData();">
                                                                    <span class="ladda-label">Submit</span>
                                                                </button>
                                                                <a href="<?php echo site_url("device_users");?>" class="btn btn-default btn-cons">Cancel</a>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" class="avatar-path" name="avatar_path" id="avatar_path" value="">
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
        
        <script>
            var deviceUsers = $('#deviceUsers');
            var form_error = $('.alert-danger', deviceUsers);
            var form_success = $('.alert-success', deviceUsers);
            var base_url ="<?php echo base_url(); ?>";
            jQuery(document).ready(function() {
                jQuery.validator.addMethod("validateMobile", function(mobile_number, element) {
                    mobile_number = mobile_number.replace(/\s+/g, "");
                    return this.optional(element) || mobile_number.length > 9 && 
                    mobile_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
                }, "Please specify a valid mobile number");
                jQuery.validator.addMethod("noSpace", function (value, element) {
                return value.indexOf(" ") < 0 && value != "";
            }, "Space are not allowed");
                                
                
                deviceUsers.validate({
                    errorElement: 'span',
                    errorClass: 'help-block help-block-error',
                    focusInvalid: false,
                    ignore: "",
                    rules: {
                        company_id: {
                            required: true
                        },
                        status: {
                            required: true
                        },                        
                        first_name: {
                            required: true
                        },
                        last_name: {
                            required: true
                        },
                        email: {
                            required: true,
                            email: true,
                            emailCheck:true
                        },
                        l1_manager: {
                            required: true,
                        },
                        l2_manager: {
                            required: true,
                        },
                        password: {
                            required: true
                        },
                        confirmpassword: {
                            required: true,
                            equalTo: "#password"
                        },
                        mobile: {
                            // required: true,
                            validateMobile: true
                        },
                        emp_id:{
                            noSpace: true
                        },
                        depart:{
                            required: true
                        },
                        region_id:{
                            required: true
                        }
                        
                    },
                    invalidHandler: function (event, validator) {             
                        form_success.hide();
                        form_error.show();
                        App.scrollTo(form_error, -200);
                    },
                    errorPlacement: function(error, element) {
                        if(element.hasClass('form-group')) {
                            error.appendTo(element.parent().find('.has-error'));
                        }
                        else if(element.parent('.form-group').length) {
                            error.appendTo(element.parent());
                        }
                        else {
                            error.appendTo(element);
                        }
                    },
                    highlight: function (element) {
                        $(element).closest('.form-group').addClass('has-error');
                    },
                    unhighlight: function (element) {
                        $(element).closest('.form-group').removeClass('has-error');
                    },
                    success: function (label) {
                        label.closest('.form-group').removeClass('has-error');
                    },
                    submitHandler: function (form) {
                        form_success.show();
                        form_error.hide();
                        Ladda.bind('button[id=device-submit]');
                        form.submit();
                    }
                });
                $('.select2,.select2-multiple').on('change', function() {
            		$(this).valid();
            	});
                jQuery.validator.addMethod("emailCheck", function(value, element){
                  
                var isSuccess = false;   
                $.ajax({
                    type: "POST",
                    data: {email_id:value,compnay_id:$('#company_id').val()},
                    url: base_url+"device_users/Check_emailid",
                    async: false,
                    success: function (msg) {
                         isSuccess = msg != "" ? false : true;
                    }
                });
                return isSuccess;
                }             
            , "Email id already exists!!!");       
            });
    function SaveUserData() {
    if (!deviceUsers.valid()) {
        return false;
    }
    $.ajax({
        type: "POST",
        url: base_url + 'device_users/submit/',
        data: deviceUsers.serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                setTimeout(function () {// wait for 5 secs(2)
                    window.location.href = base_url + 'device_users';
                }, 500);
            } else {
                $('#errordiv').show();
                $('#errorlog').html(Data['Msg']);
                App.scrollTo(form_error, -200);
            }
            customunBlockUI();
        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
            ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
        }
    });
}
    function RegionData() {
        if($('#company_id').val()==''){
            $('#region_id').empty();
            $('#designation_id').empty();
            return false;
        }
        $.ajax({
            type: "POST",
            data: {company_id:$('#company_id').val()},
            async: false,
            url: base_url + 'device_users/ajax_region/',
            beforeSend: function () {
                customBlockUI();
            },
            success: function (Odata) {
                if (Odata != '') {
                var Data = $.parseJSON(Odata);
                        var regionOption = '<option value="">Please Select</option>';
                        var RegionResultMst = Data['RegionData'];
                        for (var i = 0; i < RegionResultMst.length; i++) {
                            regionOption += '<option value="' + RegionResultMst[i]['id'] + '">' + RegionResultMst[i]['region_name'] + '</option>';
                        }
                        $('#region_id').empty();
                        $('#region_id').append(regionOption);
                        
                var designationOption = '<option value="">Please Select</option>';
                var DesignationMst = Data['DesignationData'];
                for (var i = 0; i < DesignationMst.length; i++) {
                    designationOption += '<option value="' + DesignationMst[i]['id'] + '">' + DesignationMst[i]['description'] + '</option>';
                }
                $('#designation_id').empty();
                $('#designation_id').append(designationOption);        
                   customunBlockUI();
               }
            } 
        }); 
    }
        </script>
    </body>
</html>