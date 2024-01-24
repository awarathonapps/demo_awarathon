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
        
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link href="<?php echo $base_url;?>assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $base_url;?>assets/global/plugins/cropper/cropper.css" rel="stylesheet" type="text/css" />
        
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
                                    <span>Users</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Create New User</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url?>users" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            Create User
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
                                                        <a href="#tab_avatar" data-toggle="tab">Change Avatar</a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tab_overview">    
                                                    <form id="frmUsers" name="frmUsers" method="POST"  action="<?php echo $base_url;?>users/submit"> 
                                                        <div class="row">    
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Login ID<span class="required"> * </span></label>
                                                                    <input type="text" name="loginid" id="loginid" maxlength="20" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Password<span class="required"> * </span></label>
                                                                    <input type="password" name="password" id="password" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Confirm Password<span class="required"> * </span></label>
                                                                    <input type="password" name="confirmpassword" id="confirmpassword" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row"> 
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Status<span class="required"> * </span></label>
                                                                    <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="1" selected>Active</option>
                                                                        <option value="0">In-Active</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Role<span class="required"> * </span></label>
                                                                    <select id="roleid" name="roleid" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="my-line"></div>
                                                        <div class="row"> 
                                                            <div class="col-md-1">    
                                                                <div class="form-group">
                                                                    <label>Salutation</label>
                                                                    <select id="saluation" name="saluation" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="Mr." selected="selected">Mr.</option>
                                                                        <option value="Mrs.">Mrs.</option>
                                                                        <option value="Miss">Miss</option>
                                                                        <option value="Dr.">Dr.</option>
                                                                        <option value="Prof.">Prof.</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
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
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Email<span class="required"> * </span></label>
                                                                    <input type="text" name="email" id="email" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Mobile No.<span class="required"> * </span></label>
                                                                    <input type="text" name="mobile" id="mobile" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Alternate Email</label>
                                                                    <input type="text" name="email2" id="email2" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Contact No</label>
                                                                    <input type="text" name="contactno" id="contactno" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Fax</label>
                                                                    <input type="text" name="fax" id="fax" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="my-line"></div>
                                                        <div class="row">
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Address 1</label>
                                                                    <input type="text" name="address" id="address" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Address 2</label>
                                                                    <input type="text" name="address2" id="address2" maxlength="250" class="form-control input-sm">                                 
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
                                                                    <textarea rows="4" class="form-control input-sm" name="description" placeholder=""></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">      
                                                                <div class="col-md-12 text-right">  
                                                                    <button type="submit" id="role-submit" name="role-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-right">
                                                                        <span class="ladda-label">Submit</span>
                                                                    </button>
                                                                    <a href="<?php echo site_url("roles");?>" class="btn btn-default btn-cons">Cancel</a>
                                                                </div>
                                                        </div>
                                                        <input type="hidden" class="avatar-path" name="avatar_path" id="avatar_path" value="">
                                                    </form>
                                                    </div>    
                                                    <div class="tab-pane mar" id="tab_avatar">
                                                        <div class="row">    
                                                            <div class="col-md-3"> 
                                                                <div class="container" id="crop-avatar">
                                                                    <!-- Current avatar -->
                                                                    <div class="avatar-view" title="Change the avatar">
                                                                        <img id="preview-existing-avatar" src="<?php echo $base_url;?>assets/uploads/avatar/no-avatar.jpg" alt="Avatar">
                                                                    </div>
                                                                    <!-- Cropping modal -->
                                                                    <div class="modal dont-fade" id="avatar-modal" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="960" style="width:960px !important;top: -200px;">
                                                                    <div class="modal-dialog modal-lg">
                                                                        <div class="modal-content">
                                                                        <form class="avatar-form" action="<?php echo $base_url;?>users/temp_avatar_upload" enctype="multipart/form-data" method="post">
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
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
            </div>
            <?php //$this->load->view('inc/inc_footer'); ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav'); ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        
        <script src="<?php echo $base_url; ?>assets/global/plugins/cropper/cropper.js" type="text/javascript"></script>
        <script src="<?php echo $base_url; ?>assets/global/scripts/avatar_main.js" type="text/javascript"></script>
        <script>
                
            jQuery(document).ready(function() {
                //$(".page-bar").stick_in_parent({offset_top:'50px !important'});
                $('#roleid').select2({
                    placeholder: '',
                    multiple:false,
                    separator: ',',
                    ajax: {
                        url: "<?php echo base_url();?>index.php/users/ajax_populate_roles",
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
                        return $.getJSON("<?php echo base_url();?>index.php/users/ajax_populate_roles?id=" + (element.val()), null, function(data) {
                            return callback(data);
                        });
                    }
                });
                $('#country_id').select2({
                    placeholder: '',
                    
                    multiple:false,
                    separator: ',',
                    ajax: {
                        url: "<?php echo base_url();?>index.php/users/ajax_populate_country",
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
                        return $.getJSON("<?php echo base_url();?>index.php/users/ajax_populate_country?id=" + (element.val()), null, function(data) {
                            return callback(data);
                        });
                    }
                });
                $('#state_id').select2({
                    placeholder: '',
                    
                    multiple:false,
                    separator: ',',
                    ajax: {
                        url: "<?php echo base_url();?>index.php/users/ajax_populate_state",
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
                        return $.getJSON("<?php echo base_url();?>index.php/users/ajax_populate_country?id=" + (element.val()), null, function(data) {
                            return callback(data);
                        });
                    }
                });
                $('#city_id').select2({
                    placeholder: '',
                    
                    multiple:false,
                    separator: ',',
                    ajax: {
                        url: "<?php echo base_url();?>index.php/users/ajax_populate_city",
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
                        return $.getJSON("<?php echo base_url();?>index.php/users/ajax_populate_city?id=" + (element.val()), null, function(data) {
                            return callback(data);
                        });
                    }
                });

                
                jQuery.validator.addMethod("validateMobile", function(mobile_number, element) {
                    mobile_number = mobile_number.replace(/\s+/g, "");
                    return this.optional(element) || mobile_number.length > 9 && 
                    mobile_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
                }, "Please specify a valid mobile number");
                jQuery.validator.addMethod("validateContact", function(contact_number, element) {
                    contact_number = contact_number.replace(/\s+/g, "");
                    return this.optional(element) || contact_number.length > 7 && 
                    contact_number.match(/^[0-9]*$/);
                }, "Please specify a valid contact number");
                jQuery.validator.addMethod("validateFax", function(fax_number, element) {
                    fax_number = fax_number.replace(/\s+/g, "");
                    return this.optional(element) || fax_number.length > 7 && 
                    fax_number.match(/^\+?[0-9]{7,}$/);
                }, "Please specify a valid fax number");
                
                var frmUsers = $('#frmUsers');
                var form_error = $('.alert-danger', frmUsers);
                var form_success = $('.alert-success', frmUsers);
                frmUsers.validate({
                    errorElement: 'span',
                    errorClass: 'help-block help-block-error',
                    focusInvalid: false,
                    ignore: "",
                    rules: {
                        loginid: {
                            required: true,
                            LoginId_Check:true,
                            noSpace:true
                        },
                        password: {
                            required: true
                        },
                        confirmpassword: {
                            required: true,
                            equalTo: "#password"
                        },
                        status: {
                            required: true
                        },
                        roleid: {
                            required: true
                        },
                        first_name: {
                            required: true,
                            first_lastCheck:true
                        },
                        last_name: {
                            required: true,
                            first_lastCheck:true
                        },
                        email: {
                            required: true,
                            email: true,
                            emailCheck:true
                        },
                        email2: {
                            email: true
                        },
                        mobile: {
                            required: true,
                            validateMobile: true
                        },
                        contactno: {
                            validateContact: true
                        },
                        fax: {
                            validateFax: true
                        }
                    },
                    invalidHandler: function (event, validator) {             
                        form_success.hide();
                        form_error.show();
                        if(validator.errorList.length){
                            $('#tabs a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show');
                        }

                        App.scrollTo(form_error, -200);
                    },
//                    errorPlacement: function (error, element) {
//                        if (element.parents('.mt-radio-list') || element.parents('.mt-checkbox-list')) {
//                            if (element.parents('.mt-radio-list')[0]) {
//                                error.appendTo(element.parents('.mt-radio-list')[0]);
//                            }
//                            if (element.parents('.mt-checkbox-list')[0]) {
//                                error.appendTo(element.parents('.mt-checkbox-list')[0]);
//                            }
//                        } else if (element.parents('.mt-radio-inline') || element.parents('.mt-checkbox-inline')) {
//                            if (element.parents('.mt-radio-inline')[0]) {
//                                error.appendTo(element.parents('.mt-radio-inline')[0]);
//                            }
//                            if (element.parents('.mt-checkbox-inline')[0]) {
//                                error.appendTo(element.parents('.mt-checkbox-inline')[0]);
//                            }
//                        } else if (element.parent(".input-group").size() > 0) {
//                            error.insertAfter(element.parent(".input-group"));
//                        } else if (element.attr("data-error-container")) { 
//                            error.appendTo(element.attr("data-error-container"));
//                        } else {
//                            error.insertAfter(element);
//                        }
//                    },
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
                $(".select2, .select2-multiple", frmUsers).change(function () {
                    frmUsers.validate().element($(this));
                });
                jQuery.validator.addMethod("noSpace", function (value, element) {
    return value.indexOf(" ") < 0 && value != "";
}, "Space are not allowed");
                jQuery.validator.addMethod("emailCheck", function(value, element){
                  
                var isSuccess = false;   
                $.ajax({
                    type: "POST",
                    data: {email_id:value},
                    url: "<?php echo base_url(); ?>users/Check_emailid",
                    async: false,
                    success: function (msg) {
                         isSuccess = msg != "" ? false : true;
                    }
                });
                return isSuccess;
                }             
            , "Email id already exists!!!"); 
            jQuery.validator.addMethod("LoginId_Check", function(value, element){
                  
                var isSuccess = false;   
                $.ajax({
                    type: "POST",
                    data: {login_id:value},
                    url: "<?php echo base_url(); ?>users/Check_loginid",
                    async: false,
                    success: function (msg) {
                         isSuccess = msg != "" ? false : true;
                    }
                });
                return isSuccess;
                }             
            , "Login Id already exists!!!");
            jQuery.validator.addMethod("first_lastCheck", function(value, element){                  
                var isSuccess = false;   
                $.ajax({
                    type: "POST",
                    data: {firstname:$('#first_name').val(),lastname:$('#last_name').val()},
                    url: "<?php echo base_url(); ?>users/Check_firstlast",
                    async: false,
                    success: function (msg) {
                         isSuccess = msg != "" ? false : true;
                    }
                });
                return isSuccess;
                }             
            , "Name is already exists!!!");
                
            });
        </script>
    </body>
</html>