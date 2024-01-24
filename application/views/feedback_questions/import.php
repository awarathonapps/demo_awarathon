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
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li>
                                    <span>Feedback</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Import Questions</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>feedback_questions" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Import Feedback Questions
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <form id="FrmQns" name="FrmQns" method="POST"  enctype="multipart/form-data" > 
                                        <div class="portlet-body">                                                                                            
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_overview">    

                                                    <?php
                                                    $errors = validation_errors();
                                                    if ($errors) {
                                                        ?>
                                                        <div  class="alert alert-danger">
                                                            <button class="close" data-close="alert"></button>
                                                            You have some form errors. Please check below.
                                                            <?php echo $errors; ?>
                                                        </div>
                                                    <?php } ?>
                                                    <div id="errordiv" class="alert alert-danger display-hide">
                                                        <button class="close" data-close="alert"></button>
                                                        You have some form errors. Please check below.
                                                        <br><span id="errorlog"></span>
                                                    </div> 
                                                     <?php if($Company_id == ""){ ?>
                                                    <div class="row">                                                
                                                        <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Company Name<span class="required"> * </span></label>
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2me" placeholder="Please select"  onchange="feedbackTypeData();">
                                                                        <option value="">Please Select</option>
                                                                        <?php if(isset($CompanySet)){
                                                                                foreach ($CompanySet as $Row) { ?>
                                                                                    <option value="<?php echo $Row->id ?>"><?php echo $Row->company_name ?></option>
                                                                            <?php  }
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            </div> 
                                                    </div>                                               
                                                <?php } ?>
                                                    <div class="row">                                                
                                                    <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Feedback Type</label>
                                                                <select id="feedback_type" name="feedback_type" class="groupSelectClass form-control input-sm select2me" placeholder="Please select" onchange="getfeedbacksupType();">
                                                                    <option value="">Please Select</option>
                                                                    <?php if(isset($feedback_typeSet)){
                                                                            foreach ($feedback_typeSet as $Row) { ?>
                                                                                <option value="<?php echo $Row->id ?>"><?php echo $Row->description ?></option>
                                                                        <?php  }
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                    </div>
                                                    <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Feedback Sub Type</label>
                                                                <select id="feedback_subtype" name="feedback_subtype" class="groupSelectClass form-control input-sm select2me" placeholder="Please select" >
                                                                    <option value="">Please Select</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Question Type<span class="required"> * </span></label>
                                                                <select id="question_type" name="question_type" class=" form-control input-sm select2me" placeholder="Please select" >
                                                                    <option value="0">Multiple Options</option>
                                                                    <option value="1">Text</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                     <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Language<span class="required"> * </span></label>
                                                                <select id="language_id" name="language_id" class="form-control input-sm select2me" placeholder="Please select" style="width:100%" >
                                                                    <?php
                                                                    if (isset($language_mst)) {
                                                                        foreach ($language_mst as $Row) {
                                                                            ?>
                                                                            <option value="<?php echo $Row->id ?>"><?php echo $Row->name ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>    
                                                </div>
                                                    <div class="row">
                                                        <div class="col-md-5">    
                                                            <div class="form-group">
                                                                <label>Choose File<span class="required"> * </span></label>
                                                                        <div class="form-control fileinput fileinput-new" style="width: 100%;border: none;height:auto;" data-provides="fileinput">
                                                                                <div class="input-group input-large">
                                                                                        <div class="form-control uneditable-input span3" data-trigger="fileinput">
                                                                                                <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                                                                </span>
                                                                                        </div>
                                                                                        <span class="input-group-addon btn default btn-file">
                                                                                        <span class="fileinput-new">
                                                                                        Select file </span>
                                                                                        <span class="fileinput-exists">
                                                                                        Change </span>
                                                                                        <input type="file" name="filename" id="filename">
                                                                                        </span>
                                                                                        <a href="javascript:;" id="RemoveFile" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                                                        Remove </a>
                                                                                </div>
                                                                        </div><br/>
                                                            </div>
                                                        </div>
                                                         <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <a href="javascript:void(0);" onclick="download_sample();" class="form-control" style="    border: none;height:auto;" ><strong>Download Sample File</strong></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel panel-success">
						<div class="panel-heading">
							<h3 class="panel-title">Notes</h3>
						</div>
						<div class="panel-body">
							<ul>
								<li>Upload Questions Data through CSV/Xls file.</li>
                                                                <li>file format must be same as sample file.</li>
                                                                <li>Please select proper Languages.</li>
                                                                <li>Do not modify or delete the Columns of sample xls.</li>
                                                                <li>In sample file * is mandatory Fields.</li>
							</ul>
						</div>
					</div>
                                                </div>                                                           
                                            </div>                                      
                                        </div>
                                        <div class="row">      
                                            <div class="col-md-12 text-right">  
                                                <button type="button" id="questionset-submit" name="questionset-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="Confirm_imports();" >
                                                    <span class="ladda-label"><i class="fa fa-upload"></i> Confirm</span>
                                                </button>
                                                <a href="<?php echo site_url("feedback_questions"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script>
jQuery(document).ready(function () {
    var FrmQns = $('#FrmQns');
    var form_error = $('.alert-danger', FrmQns);
    var form_success = $('.alert-success', FrmQns);
    FrmQns.validate({
        errorElement: 'span',
        errorClass: 'help-block help-block-error',
        focusInvalid: false,
        ignore: "",
        rules: {
            company_id: {
                required: true
            },
            question_type: {
                required: true
            },
//            topic_id: {
//                required: true
//            },
//            subtopic_id: {
//                required: true
//            },
            filename: {
                required: true
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
            Ladda.bind('button[id=questionset-submit]');
            form.submit();
        }
    });                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
    $(".select2, .select2-multiple", FrmQns).change(function () {
        FrmQns.validate().element($(this));
    });
});
function download_sample(){
    var type = $('#question_type').val();
    var language_id =$('#language_id').val();
    if (type == 0 || type == '') {
        window.location.href = "<?php echo base_url().'feedback_questions/samplexls/' ?>"+language_id;
    } else {
        window.location.href = "<?php echo base_url().'feedback_questions/samplexls_text/' ?>"+language_id;
    }
}
function SubmitData() {  
     
    var file_data = $('#filename').prop('files')[0]; 
    var form_data = new FormData();                  
    form_data.append('filename', file_data);
    var other_data = $('#FrmQns').serializeArray();
    $.each(other_data,function(key,input){
        form_data.append(input.name,input.value);
    });	
    $.ajax({
        cache: false,
        contentType: false,
        processData: false,
        type: "POST",
        url: '<?php echo site_url("feedback_questions/confirm_xls_csv"); ?>',
        data:  form_data,
        beforeSend: function() {
        // setting a timeout
            $.blockUI({
            boxed: true
            });
        },
        success: function(Odata){
           //alert(result);
           var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success'); 
                setTimeout(function(){// wait for 5 secs(2)
                    location.reload(); // then reload the page.(3)
               }, 1000); 
            } else {
                $('#errordiv').show();
                $('#errorlog').html(Data['Msg']);
            }
            $.unblockUI();
         },error: function(XMLHttpRequest, textStatus, errorThrown) { 
            ShowAlret("Status: " + textStatus+ " ,Contact Mediaworks for technical support!"); 
        }
    });
    }
function Confirm_imports() {
    $('#errordiv').hide();
    if (!$('#FrmQns').valid()) {
            return false;
    } 
        $.confirm({
            title: 'Confirm Import!',
            content: '<div class="form-group">' +
                    '<h5>Question Type : <strong>' + $("#question_type option:selected").text() + '</strong></h5>' +
                    '<h5>Language : <strong>' + $("#language_id option:selected").text() + '</strong></h5>' +
                    '</div>',
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-orange',
                    keys: ['enter', 'shift'],
                    action: function () {
                        SubmitData();
                    }
                },
                cancel: function () {
                    this.onClose();
                }
            }
        });
    }
function feedbackTypeData(){
    var Company_id = $('#company_id').val();
    $('#feedback_type').empty();
    if(Company_id==""){
        return false;
    }
    $.ajax({
        type: "POST",
        data: "data=" + Company_id,
        async:false,
        url: "<?php echo base_url(); ?>feedback_questions/ajax_company_feedbackType",
            success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var FeedbackTypeMSt = Oresult['result'];                             
                var option='<option value="">Please Select</option>';                            
                for (var i = 0; i < FeedbackTypeMSt.length; i++) {
                    option += '<option value="' + FeedbackTypeMSt[i]['id'] + '">' + FeedbackTypeMSt[i]['description'] + '</option>';
                }                            
                $('#feedback_type').append(option); 

            } else {
                // $("#committment_amount").val(msg);
            }
        }
    });
}
function getfeedbacksupType(){
    var feedback_type = $('#feedback_type').val();
    $('#feedback_subtype').empty();
    if(feedback_type==""){
        return false;
    }
    $.ajax({
        type: "POST",
        data: "feedback_type=" + feedback_type,
        async:false,
        url: "<?php echo base_url(); ?>feedback_questions/ajax_feedback_subType",
            success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var FeedbackTypeMSt = Oresult['result'];                             
                var option='<option value="">Please Select</option>';                            
                for (var i = 0; i < FeedbackTypeMSt.length; i++) {
                    option += '<option value="' + FeedbackTypeMSt[i]['id'] + '"' + (FeedbackTypeMSt.length > 1 ? "" : "Selected")+ '>' + FeedbackTypeMSt[i]['description'] + '</option>';
                }                            
                $('#feedback_subtype').empty();
                $('#feedback_subtype').append(option); 

            } else {
                // $("#committment_amount").val(msg);
            }
        }
    });
}
    </script>
</body>
</html>