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
        <link href="<?php echo $base_url; ?>assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
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
                                    <span>Create New Company</span>
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
                                <div class="alert alert-danger display-hide" id="errordiv">
                                    <button class="close" data-close="alert"></button>
                                    You have some form errors. Please check below.
                                    <br><span id="errorlog"></span>
                                </div>
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            Create Company
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
                                                    <a href="javascrip:void(0);" data-toggle="tab">Trainee List</a>
                                                </li>
                                                <li>
                                                    <a href="javascrip:void(0);" data-toggle="tab">Mail Settings</a>
                                                </li>
                                                <!--                                                    <li>
                                                                                                        <a href="javascrip:void(0);" data-toggle="tab">Attachments</a>
                                                                                                    </li>-->

                                                <?php if ($this->mw_session['superaccess']) { ?>            
                                                    <li>
                                                        <a href="javascrip:void(0);" data-toggle="tab">Settings</a>
                                                    </li>
                                                <?php } ?>                                                            
                                            </ul>
                                            <form id="frmCompany" name="frmCompany" method="POST"  action="<?php echo $base_url; ?>company/submit"> 

                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="tab_overview">    
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Company Prefix</label>
                                                                    <input type="text" name="company_code" id="company_code" maxlength="10" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>   
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Company Name<span class="required"> * </span></label>
                                                                    <input type="text" name="company_name" id="company_name" maxlength="250" class="form-control input-sm">                                 
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
                                                                    <input type="text" name="portal_name" id="portal_name" minlength="4" maxlength="30" class="form-control input-sm">
                                                                    <div class="text-muted">https://<span class="text-info">mediaworks</span>.awarathon.com</div>                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">One Time Code (OTC)<span class="required"> * </span></label>
                                                                    <input type="text" name="otp" id="otp" maxlength="250" value="<?php echo $OtpCode; ?>" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Industry<span class="required"> * </span></label>
                                                                    <select id="industry_type_id" name="industry_type_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                        <?php if (count((array)$IndustryType) > 0) {
                                                                            foreach ($IndustryType as $value) {
                                                                                ?>
                                                                                <option value="<?php echo $value->id ?>"><?php echo $value->description ?></option>
                                                                                <?php
                                                                            }
                                                                         } ?>
                                                                    </select>
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
<!--                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label class="mt-checkbox mt-checkbox-outline" for="corporate_partner"> It Is Corporate Partner?
                                                                        <input id="corporate_partner" name="corporate_partner" type="checkbox" value="1" /><span></span>
                                                                    </label>
                                                                </div>
                                                            </div>                                                        -->
                                                            <div class="col-md-6">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="empcode_restrict"> On Registration required Employee code?
                                                                    <input id="empcode_restrict" name="empcode_restrict" type="checkbox" value="1"  onclick="SettingsCheck('empcode_restrict', 'empcode');"/><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label class="mt-checkbox mt-checkbox-outline" for="eotp_required"> Employee OTP Verification Required?
                                                                    <input id="eotp_required" name="eotp_required" type="checkbox" value="1"   /><span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="my-line"></div>
                                                        <div class="row">
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Address 1</label>
                                                                    <input type="text" name="address_i" id="address_i" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Address 2</label>
                                                                    <input type="text" name="address_ii" id="address_ii" maxlength="250" class="form-control input-sm">                                 
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
                                                                    <input type="text" name="pincode" id="pincode" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Contact Person</label>
                                                                    <input type="text" name="contact_person" id="contact_person" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Contact No</label>
                                                                    <input type="text" name="contact_no" id="contact_no" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>                                                            
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Email</label>
                                                                    <input type="text" name="email" id="email" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Website</label>
                                                                    <input type="text" name="website" id="website" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Note</label>
                                                                    <textarea rows="4" class="form-control input-sm" id="remarks" name="remarks" placeholder=""></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Company Logo</label>
                                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                                        <div class="input-group input-large">
                                                                            <div class="form-control uneditable-input" data-trigger="fileinput">
                                                                                <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                                                </span>
                                                    </div>                                                      
                                                                            <span class="input-group-addon btn default btn-file">
                                                                            <span class="fileinput-new">
                                                                            Select file </span>
                                                                            <span class="fileinput-exists">
                                                                            Change </span>
                                                                            <input type="file" name="company_logo" id="company_logo" >
                                                                            </span>
                                                                            <a href="javascript:;" id="removeImage" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">Remove </a>
                                                </div>  
                                                                    </div>
                                                                    <span class="text-muted">(Extensions allowed: .png , .gif, .jpg, .jpeg, .bmp)  width:266px, height:144px)</span>
                                                                </div>
                                                            </div>
                                                        </div>    
                                                        
                                                    </div>
                                                    </div>                                                      
                                                </div>  
                                                <div class="row">      
                                                    <div class="col-md-12 text-right">  
                                                        <button type="button" id="company-submit" name="company-submit" data-loading-text="Please wait..." 
                                                                class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-right" onclick="CreateCompany();">
                                                            <span class="ladda-label">Save & Next</span>
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
    <?php $this->load->view('inc/inc_footer_script'); ?>
    <script src="<?php echo $base_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
    <script>
        var str = '';
        var frmCompany = $('#frmCompany');
        var form_error = $('.alert-danger', frmCompany);
        var form_success = $('.alert-success', frmCompany);
        var base_url = "<?php echo $base_url; ?>";
        jQuery(document).ready(function () {
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
                    return $.getJSON(base_url+"company/ajax_populate_country?id=" + (element.val()), null, function (data) {
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
                    return $.getJSON(base_url+"company/ajax_populate_country?id=" + (element.val()), null, function (data) {
                        return callback(data);
                    });
                }
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
                    return $.getJSON(base_url+"company/ajax_populate_city?id=" + (element.val()), null, function (data) {
                        return callback(data);
                    });
                }
            });
            jQuery.validator.addMethod("validateMobile", function (mobile_number, element) {
                mobile_number = mobile_number.replace(/\s+/g, "");
                return this.optional(element) || mobile_number.length > 9 &&
                        mobile_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
            }, "Please specify a valid mobile number");
            jQuery.validator.addMethod("validateContact", function (contact_number, element) {
                contact_number = contact_number.replace(/\s+/g, "");
                return this.optional(element) || contact_number.length > 7 &&
                        contact_number.match(/^[0-9]*$/);
            }, "Please specify a valid contact number");
//                            jQuery.validator.addMethod("validateFax", function (fax_number, element) {
//                                fax_number = fax_number.replace(/\s+/g, "");
//                                return this.optional(element) || fax_number.length > 7 &&
//                                        fax_number.match(/^\+?[0-9]{7,}$/);
//                            }, "Please specify a valid fax number");

            jQuery.validator.addMethod("companyCheck", function (value, element) {
                var isSuccess = false;
                $.ajax({
                    type: "POST",
                    data: {company: value},
                    url: base_url+"Company/Check_company",
                    async: false,
                    success: function (msg) {
                        isSuccess = msg != "" ? false : true;
                    }
                });
                return isSuccess;
            }
            , "Company already exists!!!");

            jQuery.validator.addMethod("Special_CharCheck", function (value, element) {
                var return_result = true;
                if (/^[a-zA-Z0-9- ]*$/.test(value) == false || (value.indexOf(" ") > 0 && value != "")) {
                    return_result = false;
                }
                return return_result;
            }, "Special Characters And Space are Not Allowed");

            jQuery.validator.addMethod("portalCheck", function (value, element) {
                var isSuccess = false;
                $.ajax({
                    type: "POST",
                    data: {portal: value},
                    url: base_url+"Company/Check_portal",
                    async: false,
                    success: function (msg) {
                        isSuccess = msg != "" ? false : true;
                    }
                });
                return isSuccess;
            }
            , "Portal already exists!!!");
            frmCompany.validate({
                errorElement: 'span',
                errorClass: 'help-block help-block-error',
                focusInvalid: false,
                ignore: "",
                rules: {
                    company_name: {
                        required: true,
                        companyCheck: true
                    },
                    portal_name: {
                        required: true,
                        portalCheck: true,
                        Special_CharCheck: true
                    },
                    industry_type_id: {
                        required: true
                    },
                    status: {
                        required: true
                    },
                    email: {
                        email: true
                    },
                    mobile: {
                        validateMobile: true
                    },
                    contact_no: {
                        validateContact: true
                    },
                    contact_person: {
                        // digits: false
                    },
                    app_users_count: {
                        digits: true
                    },
                    portal_users_count: {
                        digits: true
                    },
                    otp: {
                        required: true
                    },
                    workshop_count: {
                        digits: true
                    },
                    feedback_count: {
                        digits: true
                    },
                    workshop_question_count: {
                        digits: true
                    },
                    feedback_question_count: {
                        digits: true
                    }
                },
                invalidHandler: function (event, validator) {
                    form_success.hide();
                    form_error.show();
                    if (validator.errorList.length) {
                        $('#tabs a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show');
                    }
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
            $(".select2, .select2-multiple", frmCompany).change(function () {
                frmCompany.validate().element($(this));
            });
            // $('#users_restrict').click(function (){
            //     if ($(this).is(':checked')){
            //     }else{
            //     }
            // });
        });
        function CreateCompany() {
            if (!frmCompany.valid()) {
                return false;
            }
            
            $.confirm({
            title: 'Confirm Company!',
            content: '<div class="form-group">'+
                '<h5>Company Name : <strong>' +$('#company_name').val()+'</strong></h5>'+
                '<h5>Portal Name: <strong>' +$('#portal_name').val()+'</strong></h5>'+
                '<h5>OTC: <strong>' +$('#otp').val()+'</strong></h5>'+
                '</div>',
            buttons: {
                confirm:{
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function(){
                            var file_data = $('#company_logo').prop('files')[0];
                    //console.log(file_data);
                    var form_data = new FormData();                  
                    form_data.append('company_logo', file_data);
                    var other_data = $('#frmCompany').serializeArray();
                    $.each(other_data,function(key,input){
                        form_data.append(input.name,input.value);
                    });
                    $.ajax({
                        type: "POST",
                        url: base_url + "company/submit",
                        data: form_data,
                        contentType: false,
                        cache : false,
                        processData:false,
                        beforeSend: function () {
                            customBlockUI();
                        },
                        success: function (Odata) {
                            var Data = $.parseJSON(Odata);
                            if (Data['success']) {
                                ShowAlret(Data['Msg'], 'success');
                                window.location.href = base_url + 'company/edit/' + Data['id'] + "/2";
                            } else {
                                $('#errordiv').show();
                                $('#errorlog').html(Data['Msg']);
                                App.scrollTo(form_error, -200);
                            }
                            customunBlockUI();
                        }
                    });
                }
            },
            cancel: function () {
                 this.onClose();
            }
            }
        });
        }
        function SettingsCheck(chkElement, element)
        {
            if ($('#' + chkElement).prop("checked") == true) {
                $('#' + element).removeAttr('disabled');
            } else {
                if ($('#' + element).is("select")) {

                } else {
                    $('#' + element).val('');
                }
                $('#' + element).attr('disabled', 'disabled');
            }
        }
    </script>
    </body>
    </html>