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
        <link href="<?php echo $asset_url; ?>assets/layouts/layout/css/profile.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $asset_url;?>assets/global/plugins/cropper/cropper.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <div class="page-wrapper">
            <?php $this->load->view('inc/inc_header'); ?>
            <div class="clearfix"> </div>
            <div class="page-container">
                <?php $this->load->view('inc/inc_sidebar'); ?>
                <div class="page-content-wrapper">
                    <div class="page-content">
                        <!-- BEGIN PAGE CONTENT-->
                        <div class="row">
                            <div class="col-md-12">
                                <!-- BEGIN PROFILE SIDEBAR -->
                                 <?php if ($this->session->flashdata('flash_message')) { ?> 
                                    <div class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                        <?php echo $this->session->flashdata('flash_message'); ?>
                                    </div>
                                <?php } ?>   
                                <div class="profile-sidebar" style="width:250px;">
                                    <!-- PORTLET MAIN -->
                                    <div class="portlet light profile-sidebar-portlet">
                                        <!-- SIDEBAR USERPIC -->
                                        <div class="profile-userpic">
                                            <img src="<?php echo base_url() . 'assets/uploads/' . ($RowSet->avatar != '' ? 'avatar/' . $RowSet->avatar : 'no_image.png'); ?>" class="img-responsive" alt="">
                                        </div>
                                        <!-- END SIDEBAR USERPIC -->
                                        <!-- SIDEBAR USER TITLE -->
                                        <div class="profile-usertitle">
                                            <div class="profile-usertitle-name">
                                                <?php echo $RowSet->first_name . ' ' . $RowSet->last_name ?>
                                            </div>
                                        </div>
                                        <!-- END SIDEBAR USER TITLE -->
                                        <!-- SIDEBAR MENU -->
                                        <div class="profile-usermenu">
                                            <ul class="nav">
                                                <li class="active">
                                                    <a href="javascript:void(0)">
                                                        <i class="icon-settings"></i>
                                                        Account Settings </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">
                                                        <i class="icon-info"></i>
                                                        Help </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- END MENU -->
                                    </div>
                                    <!-- END PORTLET MAIN -->
                                    <!-- PORTLET MAIN -->
                                    <!-- END PORTLET MAIN -->
                                </div>
                                <!-- END BEGIN PROFILE SIDEBAR -->
                                <!-- BEGIN PROFILE CONTENT -->
                                <div class="profile-content">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet light">
                                                <div class="portlet-title tabbable-line">
                                                    <div class="caption caption-md">
                                                        <i class="icon-globe theme-font hide"></i>
                                                        <span class="caption-subject font-blue-madison bold uppercase">Profile Account</span>
                                                    </div>
                                                    <ul class="nav nav-tabs">
                                                        <li class="active">
                                                            <a href="#tab_1_1" data-toggle="tab">Personal Info</a>
                                                        </li>
                                                        <li>
                                                            <a href="#tab_1_2" data-toggle="tab">Change Avatar</a>
                                                        </li>
                                                        <li>
                                                            <a href="#tab_1_3" data-toggle="tab">Change Password</a>
                                                        </li>

                                                    </ul>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="tab-content">
                                                        <!-- PERSONAL INFO TAB -->
                                                        <div class="tab-pane active" id="tab_1_1">
                                                            <form role="form" id="user_form" name="user_form" method="POST" action="<?php echo $base_url; ?>profile/submit">
                                                                
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
                                                                    <?php } ?>
                                                                    <div class="alert alert-danger display-hide">
                                                                        <button class="close" data-close="alert"></button>
                                                                        You have some form errors. Please check below.
                                                                    </div> 
                                                                                                                                
                                                                <div class="form-group">
                                                                    <label class="control-label">Login ID</label>
                                                                    <input type="text" name="login_id" id="login_id" placeholder="Login ID" class="form-control" value="<?php echo $RowSet->username; ?>" style="text-transform: lowercase;"/>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label">First Name</label>
                                                                    <input type="text" name="first_name" id="first_name" placeholder="First Name" class="form-control" value="<?php echo $RowSet->first_name; ?>"/>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label">Last Name</label>
                                                                    <input type="text" name="last_name" id="last_name" value="<?php echo $RowSet->last_name; ?>" class="form-control"/>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label">Mobile Number</label>
                                                                    <input type="text" name="mobile" id="mobile" placeholder="+1 646 580 DEMO (6284)" value="<?php echo $RowSet->mobile; ?>" class="form-control"/>
                                                                </div>
                                                                <?php if(isset($RowSet->email)){ ?>
                                                                <div class="form-group">
                                                                    <label class="control-label">Email</label>
                                                                    <input type="text" name="email" id="email" value="<?php echo $RowSet->email; ?>" class="form-control"/>
                                                                </div>
                                                                <?php } ?>
                                                                <div class="margin-top-10 pull-right">
                                                                    <button type="submit" id="modal-user-submit" name="modal-user-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo">
                                                                        <span class="ladda-label">Save Changes</span>
                                                                    </button>
                                                                    <a href="<?php echo site_url("dashboard"); ?>" class="btn default">
                                                                        Cancel 
                                                                    </a>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <!-- END PERSONAL INFO TAB -->
                                                        <!-- CHANGE AVATAR TAB -->
                                                        <div class="tab-pane" id="tab_1_2">
                                                            <form name="image_form" id=image_form" method="POST" action="<?php echo $base_url; ?>profile/upload_image" role="form" enctype="multipart/form-data">
                                                                <div class="form-group">
                                                                    <div class="fileinput fileinput-exists" data-provides="fileinput">
                                                                        <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                                            <img src="<?php echo base_url().'assets/uploads/no_image.png'?>" alt=""/>
                                                                        </div>
                                                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                                        <img src="<?php echo base_url(). 'assets/uploads/' . ($RowSet->avatar != '' ? 'avatar/' . $RowSet->avatar : 'no_image.png'); ?>" alt=""/>
                                                                        </div>
                                                                        <div>
                                                                            <span class="btn default btn-file">
                                                                                <span class="fileinput-new">
                                                                                    Select image </span>
                                                                                <span class="fileinput-exists">
                                                                                    Change </span>
                                                                                <input type="file" name="profile_image" id="profile_image">
                                                                            </span>
                                                                            <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput" onclick="RemoveWkImage();">
										Remove </a>
                                                                                <input type="hidden" name="RemoveWrkImage" id="RemoveWrkImage" value="0"> 
                                                                        </div>
                                                                    </div>
                                                                    <div class="clearfix margin-top-10">
                                                                        <span class="label label-danger">NOTE! </span>
                                                                        <span>(Extensions allowed: .png , .gif, .jpg, .jpeg, .bmp) </span>
                                                                    </div>
                                                                </div>
                                                                <div class="margin-top-10 pull-right">
                                                                    <button type="submit" id="modal-upload" name="modal-upload" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" >
                                                                        <span class="ladda-label">Upload</span>
                                                                    </button>
                                                                    <a href="<?php echo site_url("dashboard"); ?>" class="btn default">
                                                                        Cancel </a>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <!-- END CHANGE AVATAR TAB -->
                                                        <!-- CHANGE PASSWORD TAB -->
                                                        <div class="tab-pane" id="tab_1_3">
                                                            <div id="errordiv" class="alert alert-danger display-hide">
                                                                <button class="close" data-close="alert"></button>
                                                                You have some form errors. Please check below.
                                                                <br><span id="errorlog"></span>
                                                            </div> 
                                                            <form name="FrmChangePass" id="FrmChangePass">
                                                                <div class="form-group">
                                                                    <label class="control-label">Current Password</label>
                                                                    <input type="password" class="form-control" name="oldpassword" id="oldpassword"/>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label">New Password</label>
                                                                    <input type="password" class="form-control" name="newpassword" id="newpassword"/>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label">Re-type New Password</label>
                                                                    <input type="password" class="form-control" id="confirmpassword" name="confirmpassword"/>
                                                                </div>
                                                                <div class="margin-top-10 pull-right">
                                                                    <button type="button" id="modal-create-submit" name="modal-create-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" onclick="ChangePassword();">
                                                                        <span class="ladda-label">Change Password</span>
                                                                    </button>
                                                                    <a href="<?php echo site_url("dashboard"); ?>" class="btn default">
                                                                        Cancel </a>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <!-- END CHANGE PASSWORD TAB -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END PROFILE CONTENT -->
                            </div>
                        </div>
                        <!-- END PAGE CONTENT-->
                    </div>
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
            </div>
            <?php //$this->load->view('inc/inc_footer'); ?>
        </div>
<?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
        <script>
            var base_url = "<?php echo $base_url; ?>";
            jQuery(document).ready(function () {
                var frmpwd = $('#FrmChangePass');
                var form_error = $('.alert-danger', frmpwd);
                var form_success = $('.alert-success', frmpwd);
                frmpwd.validate({
                    errorElement: 'span',
                    errorClass: 'help-block help-block-error',
                    focusInvalid: false,
                    ignore: "",
                    rules: {
                        oldpassword: {
                            required: true
                        },
                        newpassword: {
                            required: true
                        },
                        confirmpassword: {
                            required: true,
                            equalTo: "#newpassword"
                        }
                    },
                    invalidHandler: function (event, validator) {
                        form_success.hide();
                        form_error.show();
                        App.scrollTo(form_error, -200);
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
                        Ladda.bind('button[id=role-submit]');
                        form.submit();
                    }
                });
                jQuery.validator.addMethod("noSpace", function (value, element) {
                    return value.indexOf(" ") < 0 && value != "";
                }, "Space are not allowed");
                jQuery.validator.addMethod("loginIDCheck", function (value, element) {
                    var isSuccess = false;
                    $.ajax({
                        type: "POST",
                        data: {login_id: value},
                        url: base_url + "profile/check_loginid",
                        async: false,
                        success: function (msg) {
                            isSuccess = msg != "" ? false : true;
                        }
                    });
                    return isSuccess;
                }
                , "Login ID already exists!!!");
                $('#user_form').validate({
                    errorElement: 'span',
                    errorClass: 'help-block help-block-error',
                    focusInvalid: false,
                    ignore: "",
                    rules: {
                        login_id:{
                            required: true,
                            noSpace: true,
                            loginIDCheck: true,
                            <?php echo ($login_type==3 ? "email:true" :"");  ?>
                        },
                        first_name: {
                            required: true
                        },
                        last_name: {
                            required: true
                        },
                        mobile: {
                            required: true,
                            digits:true
                        },
                        address1: {
                            required: true
                        },
                        country_id: {
                            required: true
                        },
                        state_id: {
                            required: true
                        },
                        city_id: {
                            required: true
                        },
                        email:{
                            required: true,
                            email:true
                        }
                    },
                    invalidHandler: function (event, validator) {
                        form_success.hide();
                        form_error.show();
                        App.scrollTo(form_error, -200);
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
                        Ladda.bind('button[id=modal-user-submit]');
                        form.submit();
                    }
                });

            });
            function RemoveWkImage(){
                $('#RemoveWrkImage').val(1);
            }
            function ChangePassword() {
                $('#errordiv').hide();
                if (!$('#FrmChangePass').valid()) {
                    return false;
                }
                $.ajax({
                    url: base_url+"profile/ChangePassword/",
                    type: 'POST',
                    data: $('#FrmChangePass').serialize(),
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (Odata) {
                        var Data = $.parseJSON(Odata);
                        if (Data['success']) {
                            ShowAlret(Data['Msg'], 'success');
                            document.FrmChangePass.reset();
                        } else {
                            $('#errordiv').show();
                            $('#errorlog').html(Data['Msg']);
                        }
                        customunBlockUI();
                    }
                });
            }
        </script>
    </body>
</html>