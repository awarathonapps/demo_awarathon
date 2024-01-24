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
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link href="<?php echo $asset_url; ?>assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/cropper/cropper.css" rel="stylesheet" type="text/css" />
        <style>
            .sticky-bar{
                /* top: 75px; */
                position: fixed !important;
                z-index: 10000;
                right: 10px;
                left: 255px;
            }
            .my-line{
                width: 100%;
                height: 1px;
                border-bottom: 1px solid;
                border-color: #e7ecf1;
                margin-bottom: 20px;
            }
            .modal{
                left: 285px;
                top: 80px;
            }
            .margin-bottom-50{
                margin-bottom: 50px !important;
            }
            .loading {
                display: none;
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;               
                opacity: .75;
                filter: alpha(opacity=75);
                z-index: 20140628;
            }
        </style>
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
                                    <span>Feedback Form</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Edit Feedback Form</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>feedback_form" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Edit Feedback Form
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">   
                                        <form id="feedbackForm" name="feedbackForm" method="POST"  action="<?php echo $base_url; ?>feedback_form/update/<?php echo base64_encode($HeadResult->id); ?>"> 
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_overview">    

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
                                                    <?php } 
                                                    if ($customr_errors !="") {
                                                        ?>
                                                        <div style="display: block;" class="alert alert-danger display-hide">
                                                            <button class="close" data-close="alert"></button>
                                                            You have some form errors. Please check below.
                                                            <?php echo $customr_errors; ?>
                                                        </div>
                                                    <?php } ?>
                                                    <div id="errordiv" class="alert alert-danger display-hide">
                                                        <button class="close" data-close="alert"></button>
                                                        You have some form errors. Please check below.
                                                        <br><span id="errorlog"></span>
                                                    </div>    
                                                    <div class="row">    
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Company Name<span class="required"> * </span></label>
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" disabled="">
                                                                    <option value="">Please Select</option>
                                                                    <?php foreach ($SelectCompany as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"  <?php echo ($HeadResult->company_id == $cmp->id ? 'Selected' : ''); ?>><?= $cmp->company_name; ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>                                                    
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Title<span class="required"> * </span></label>
                                                                <input type="text" name="form_name" id="form_name" maxlength="255" class="form-control input-sm" value="<?php echo $HeadResult->form_name; ?>" >   
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="row">                                                               
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Short Description</label>
                                                                <textarea rows="4" class="form-control input-sm" id="short_description" maxlength="150" name="short_description" placeholder="" ><?php echo $HeadResult->short_description; ?></textarea>
                                                                <span class="text-muted">(Max 150 Characters)</span>
                                                            </div>
                                                        </div> 
                                                        <div class="col-md-2">    
                                                            <div class="form-group">
                                                                <label>Status<span class="required"> * </span></label>
                                                                <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                    <option value="1" <?php echo ($HeadResult->status == 1) ? 'selected' : ''; ?>>Active</option>
                                                                    <option value="0" <?php echo ($HeadResult->status == 0) ? 'selected' : ''; ?>>In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">      
                                                        <div class="col-md-12 text-right">  
                                                            <button type="button" id="confirm" name="confirm" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmField()">
                                                                <span class="ladda-label">Add Field</span>
                                                            </button>                                               
                                                        </div>
                                                    </div>
                                                    <div class="row">  
                                                        <div class="col-md-12">
                                                            <table class="table table-striped table-bordered table-hover" id="FieldDatatable" width="100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th width="20%">Field Name</th>
                                                                        <th width="20%">Display Name</th>
                                                                        <th width="20%">Type</th>
                                                                        <th width="20%">Data</th>
                                                                        <th width="5%">is Required</th>
                                                                        <th width="10%">Status</th>
                                                                        <th width="5%"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $EditField = count($Result);
                                                                    $key=0;
                                                                    if ($EditField > 0) {
                                                                        foreach ($Result as $fr) {
                                                                            $key++;
                                                                            ?>
                                                                            <tr id="Row-<?php echo $key; ?>">
                                                                                <td><input type="text" name="field_name[<?php echo $fr->id; ?>]" id="field_name<?php echo $key; ?>" value="<?php echo $fr->field_name ?>" class="form-control input-sm" maxlength="255" style="width:100%"> </td>
                                                                                <td><input type="text" name="disp_name[<?php echo $fr->id; ?>]" id="disp_name<?php echo $key; ?>" value="<?php echo $fr->field_display_name ?>" class="form-control input-sm" maxlength="255" style="width:100%"> </td>
                                                                                <td><select id="field_type<?php echo $key; ?>" name="field_type[<?php echo $fr->id; ?>]" class="form-control input-sm select2" style="width:100%" onchange="addDATA(<?php echo $key; ?>)">    
                                                                                        <option value="">Please Select</option>
                                                                                        <?php foreach ($SelectType as $ftype) { ?>
                                                                                        <option value="<?= $ftype->name; ?>"  <?php echo ($fr->field_type == $ftype->name ? 'Selected' : ''); ?>><?= $ftype->name; ?> </option>
                                                                                        <?php } ?>
                                                                                    </select></td>
                                                                                    <td><textarea rows="3" class="form-control input-sm" id="data_area<?php echo $key; ?>" maxlength="150" name="data_area[<?php echo $fr->id; ?>]" <?php echo ($fr->field_type=='dropdown'?'':'disabled') ?>><?php echo $fr->default_value ?></textarea></td>    
                                                                                    <td><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                                    <input type="checkbox" id="required_id<?php echo $key; ?>" class="checkboxes" name="required_id[<?php echo $fr->id; ?>]" value="1" <?php echo($fr->is_required ? 'Checked':''); ?>/>
                                                                                    <span></span>
                                                                                    </label></td> 
                                                                                    <td><select id="field_old_status<?php echo $key; ?>" name="field_old_status[<?php echo $fr->id; ?>]" class="form-control input-sm select2" style="width:100%">
                                                                                                    <option value="1" <?php echo($fr->status ? 'Selected':''); ?>>Active</option>
                                                                                                    <option value="0" <?php echo(!$fr->status ? 'Selected':''); ?>>In-Active</option>
                                                                                    </select>
                                                                                    </td>    
                                                                                <td>
                                                                                    <button type="button" id="remove" name="remove" class="btn btn-danger btn-sm" onclick="RowDelete(<?php echo $key; ?>)"><i class="fa fa-times"></i></button></td>
                                                                            </tr>
                                                                        <?php }
                                                                            } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">      
                                                        <div class="col-md-12 text-right">  
                                                            <button type="submit" id="feedback-submit" name="questionset-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left">
                                                                <span class="ladda-label">Submit</span>
                                                            </button>
                                                            <a href="<?php echo site_url("feedback_form"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                        </div>
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
<?php //$this->load->view('inc/inc_quick_sidebar');  ?>
    </div>
<?php //$this->load->view('inc/inc_footer');   ?>
</div>
<?php //$this->load->view('inc/inc_quick_nav');  ?>
<?php $this->load->view('inc/inc_footer_script'); ?>
<!-- <script src="<?php //echo $asset_url;     ?>assets/global/scripts/jquery-1.12.4.min.js" type="text/javascript"></script> -->
<!-- <script src="<?php //echo $asset_url;     ?>assets/global/scripts/bootstrap.min.js" type="text/javascript"></script> -->
<script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo $asset_url; ?>assets/global/plugins/cropper/cropper.js" type="text/javascript"></script>
<script src="<?php echo $asset_url; ?>assets/global/scripts/avatar_main.js" type="text/javascript"></script>
<script>
                var TrainerArrray = [];
                var TotalField = <?php echo $key+1; ?>;

                jQuery(document).ready(function () {

                    $('#form_name').maxlength({
                        limitReachedClass: "label label-danger",
                        threshold: 50
                    });

                    var feedbackForm = $('#feedbackForm');
                    var form_error = $('.alert-danger', feedbackForm);
                    var form_success = $('.alert-success', feedbackForm);
                    feedbackForm.validate({
                        errorElement: 'span',
                        errorClass: 'help-block help-block-error',
                        focusInvalid: false,
                        ignore: "",
                        rules: {
                            company_id: {
                                required: true
                            },
                            form_name: {
                                required: true,
                                formCheck:true
                            },
                            'New_field_name[]': {
                                required: true,
                                Nospace:true,
                                Field_nameCheck:true
                            },
                            'New_disp_name[]':{
                                required: true
                            },
                            'New_fieldtype_id[]':{
                              required:true  
                            },
                            status: {
                                required: true
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
                                            if(TotalField==1){
                                                $('#errorlog').text("Please Add Field");
                                                $('#errordiv').show();
                                                return false;
                                               }
                                            Ladda.bind('button[id=questionset-submit]');
                                            form.submit();
                                        }
                                    });
                                    $(".select2, .select2-multiple", feedbackForm).change(function () {
                                        feedbackForm.validate().element($(this));
                                    });
                                    
                                    jQuery.validator.addMethod("formCheck", function (value, element) {

                                    var isSuccess = false;
                                    $.ajax({
                                        type: "POST",
                                        data: {form_name: value, company_id: $('#company_id').val(),form_id:<?php echo $HeadResult->id ?>},
                                        url: "<?php echo base_url(); ?>feedback_form/Check_form",
                                        async: false,
                                        success: function (msg) {
                                            isSuccess = msg != "" ? false : true;
                                        }
                                    });
                                    return isSuccess;
                                }
                                , "Feedback Form already exists!!!");

                             jQuery.validator.addMethod("Nospace", function(value, element) {
                                var returnVal = true;
                                   if(/^[a-zA-Z0-9- ]*$/.test(value) == false ||(value.indexOf(" ") > 0 && value != "")) {                                                               
                                      returnVal = false;
                                       }
                                   return returnVal; 
                               }, "Space/Junk/Bad character not allowed");  

                            jQuery.validator.addMethod("Field_nameCheck", function (value, element) {

                                    var isSuccess = false;
                                    $.ajax({
                                        type: "POST",
                                        data: {field_name: value, form_name: $('#form_name').val()},
                                        url: "<?php echo base_url(); ?>feedback_form/Check_fieldDuplicate",
                                        async: false,
                                        success: function (msg) {
                                            isSuccess = msg != "" ? false : true;
                                        }
                                    });
                                    return isSuccess;
                                }
                                , "Field Name already exists!!!");
                                    
                                });
                                                            
                                function ConfirmField() {                                    
                                    $.ajax({
                                        url: "<?php echo base_url() . 'feedback_form/getfield/'; ?>"+TotalField,
                                        type: 'POST',
                                        //data: "cmp_id=" +<?php  ?>,
                                        success: function (Odata) {    
                                            var Data = $.parseJSON(Odata);                                                                                                                                                                                                                         
                                            $('#FieldDatatable').append(Data['htmlData']);                                            
                                            $('#field_type'+TotalField).select2();    
                                            $('#field_status'+TotalField).select2();
                                             TotalField++;  
                                        }
                                    });

                                }
                                function RowDelete(r) {
                                    $("#Row-" + r).remove();
                                    //TotalField--;
                                }
                                function addDATA(r_id) {
                                    var dropdown=$('#field_type'+r_id).val();
                                        if(dropdown=='dropdown'){
                                            $("#data_area"+r_id).prop('disabled', false);
                                        }else{
                                            $("#data_area"+r_id).val("");
                                            $("#data_area"+r_id).prop('disabled', true);
                                        }
                                }
                                $('#company_id').select2({
                                    placeholder: 'Please Select',
                                    separator: ',',
                                    ajax: {
                                        url: "<?php echo base_url(); ?>feedback_form/ajax_feedback_company",
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
                                        return $.getJSON("<?php echo base_url(); ?>questionset/ajax_feedback_company?id=" + (element.val()), null, function (data) {
                                            return callback(data);
                                        });
                                    }
                                });
</script>
</body>
</html>