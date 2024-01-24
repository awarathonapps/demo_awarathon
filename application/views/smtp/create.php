<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
//echo "<pre>";
//print_r($result);exit;
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>

        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link href="<?php echo $base_url; ?>assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $base_url; ?>assets/global/plugins/cropper/cropper.css" rel="stylesheet" type="text/css" />

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
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>SMTP Settings</span>                                    
                                </li>                                
                            </ul>                            
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
                                            SMTP Settings
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">                                                                                            
                                        <div class="tab-content">

                                            <form id="frmSMTP" name="frmSMTP" method="POST"  >                                                                                           
                                                <div class="tab-content">                                                                                                                                                 
                                                    <div class="row">    
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">SMTP Server Host Name<span class="required"> * </span></label>
                                                                <input type="text" name="host_name" id="host_name" maxlength="250" class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_ipadress : '') ?>">  
                                                                <input type="hidden" name="edit_id" id="edit_id" class="form-control input-sm" autocomplete="off" value="">  
                                                            </div>
                                                        </div>    
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Smtp Secure (SSL/TLS)<span class="required"> * </span></label>
                                                                <select id="smtp_secure" name="smtp_secure" class="form-control input-sm select2" placeholder="Please select" >                                                                                    
                                                                    <option value="">Please Select</option>
                                                                    <option value="ssl" <?php echo count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_secure == 'ssl' ? 'selected' : '' : 'selected'; ?>>SSL</option>
                                                                    <option value="tls" <?php echo count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_secure == 'tls' ? 'selected' : '' : 'selected'; ?>>TLS</option>
                                                                </select>                                                                                                                              
                                                            </div>
                                                        </div>
                                                    </div>  
                                                    <div class="row">    
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Port No<span class="required"> * </span></label>
                                                                <input type="text" name="port_no" id="port_no" maxlength="250" class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_portno : '') ?>">                                                                  
                                                            </div>
                                                        </div>                                                            
                                                        <div class="col-md-6">    
                                                            <div class="form-group last">
                                                                <label>Authentication</label>
                                                                <select id="authentication" name="authentication" class="form-control input-sm select2" placeholder="Please select" >
                                                                    <option value="">Please Select</option>
                                                                    <option value="1" <?php echo count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_authentication == 1 ? 'selected' : '' : 'selected'; ?>>Yes</option>
                                                                    <option value="0" <?php echo count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_authentication == 0 ? 'selected' : '' : 'selected'; ?>>No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">    
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">User Name<span class="required"> * </span></label>
                                                                <input type="text" name="user_name" id="user_name" maxlength="250" class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_username : '') ?>">                                                                  
                                                            </div>
                                                        </div>                                                        
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Password<span class="required"> * </span></label>
                                                                <input type="password" name="password" id="password"  class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_password : '') ?>">                                                                  
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">    
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Alias Name<span class="required"> * </span></label>
                                                                <input type="text" name="alias_name" id="alias_name" maxlength="250" class="form-control input-sm" autocomplete="off" value="<?php echo (count((array)$smtpDetails) > 0 ? $smtpDetails->smtp_alias : '') ?>">                                                                  
                                                            </div>
                                                        </div>                                                                                                                   
                                                        <div class="col-md-4">    
                                                            <div class="form-group last">
                                                                <label>Status</label>
                                                                <select id="smtp_status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                    <option value="1" <?php echo count((array)$smtpDetails) > 0 ? $smtpDetails->status == 1 ? 'selected' : '' : 'selected'; ?>>Active</option>
                                                                    <option value="0" <?php echo count((array)$smtpDetails) > 0 ? ($smtpDetails->status == 0) ? 'selected' : '' : ''; ?>>In-Active</option>
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
                                    </div>                                                           
                                </div>                                                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php //$this->load->view('inc/inc_quick_sidebar');  ?>
    </div>
    <?php //$this->load->view('inc/inc_footer');  ?>
</div>
<?php //$this->load->view('inc/inc_quick_nav'); ?>
<?php $this->load->view('inc/inc_footer_script'); ?>
<script>
    var base_url = "<?php echo $base_url; ?>";
    var frmSMTP = $('#frmSMTP');
    var form_error = $('.alert-danger', frmSMTP);
    var form_success = $('.alert-success', frmSMTP);
    jQuery(document).ready(function () {
        frmSMTP.validate({
            errorElement: 'span',
            errorClass: 'help-block help-block-error',
            focusInvalid: false,
            ignore: "",
            rules: {
                authentication: {
                    required: true
                },
                host_name: {
                    required: true
                },
                alias_name: {
                    required: true
                },
                port_no: {
                    required: true
                },
                smtp_secure: {
                    required: true
                },
                status: {
                    required: true
                },
                user_name: {
                    required: true
                },
                password: {
                    required: true
                }
            },
            invalidHandler: function (event, validator) {
                form_success.hide();
                form_error.show();
                App.scrollTo(form_error, -200);
            },
            errorPlacement: function (error, element) {
                if (element.hasClass('form-group')) {
                    error.appendTo(element.parent().find('.has-error'));
                } else if (element.parent('.form-group').length) {
                    error.appendTo(element.parent());
                } else {
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
                Ladda.bind('button[id=role-submit]');
                form.submit();
            }
        });
        $('.select2,.select2-multiple').on('change', function () {
            $(this).valid();
        });

    });
    function SaveSmtpData() {
    if (!frmSMTP.valid()) {
        return false;
    }
    $.ajax({
        type: "POST",
        url: base_url + "Smtpsetting/submit/",
        data: frmSMTP.serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
            } else {
                $('#errordiv').show();
                $('#errorlog').html(Data['Msg']);
                App.scrollTo(form_error, -200);
            }
            customunBlockUI();
        }
    });
}
function sendTestMail() {
    if ($('#testmail').val() == "") {
        ShowAlret("Please enter valid email ID.", 'error');
        return false;
    }
    if (!frmSMTP.valid()) {
        return false;
    }
    $.ajax({
        type: "POST",
        url: base_url + "Smtpsetting/Testmail/",
        data: {testmail :$('#testmail').val()},
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
            } else {
                ShowAlret(Data['Msg'], 'error');
            }
            customunBlockUI();
        }
    });
}
</script>
</body>
</html>