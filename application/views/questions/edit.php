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
                                    <span>Workshop</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Edit Question</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>questions" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Edit Question
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">   
                                        <form id="FrmQns" name="FrmQns" method="POST" > 
                                        <div class="portlet-body">                                                                                            
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_overview">    

                                                    <?php
                                                    $errors = validation_errors();
                                                    if ($errors) {
                                                        ?>
                                                        <div style="display: block;" class="alert alert-danger display-hide">
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
                                                    <?php if ($Company_id == "") { ?>
                                                    <div class="row">    
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Company Name<span class="required"> * </span></label>
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" onchange="getComapnywiseTopic();">
                                                                     <?php foreach ($SelectCompany as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"  <?php echo ($RowSet->company_id == $cmp->id ? 'Selected' : ''); ?>><?= $cmp->company_name; ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    <div class="row">
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Topic<span class="required"> * </span></label>
                                                                <select id="topic_id" name="topic_id" class="form-control input-sm select2me" placeholder="Please select" style="width:100%" onchange="getTopicwiseSubtopic()">
                                                                     <?php foreach ($TopicResultSet as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"  <?php echo ($RowSet->topic_id == $cmp->id ? 'Selected' : ''); ?>><?= $cmp->description; ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Sub Topic<span class="required"> * </span></label>
                                                                <select id="subtopic_id" name="subtopic_id" class="form-control input-sm select2me" placeholder="Please select" style="width:100%" >
                                                                    <?php foreach ($SubTopicResultSet as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"  <?php echo ($RowSet->subtopic_id == $cmp->id ? 'Selected' : ''); ?>><?= $cmp->description; ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Language<span class="required"> * </span></label>
                                                                <select id="language_id" name="language_id" class="form-control input-sm select2me" placeholder="Please select" style="width:100%" >
                                                                    <?php if(isset($language_mst)){
                                                                            foreach ($language_mst as $Row) { ?>
                                                                                <option value="<?php echo $Row->id ?>" <?php echo ($RowSet->language_id == $Row->id ? 'Selected' : ''); ?> ><?php echo $Row->name ?></option>
                                                                        <?php  }
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Question<span class="required"> * </span></label>
                                                                <textarea rows="2" class="form-control input-sm" id="question_title"  name="question_title" placeholder=""><?php echo $RowSet->question_title;  ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Option A<span class="required"> * </span></label>
                                                                <input type="text" name="option_a" id="option_a" value="<?php echo $RowSet->option_a;  ?>" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Option B<span class="required"> * </span></label>
                                                                <input type="text" name="option_b" id="option_b" value="<?php echo $RowSet->option_b;  ?>" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Option C</label>
                                                                <input type="text" name="option_c" id="option_c" value="<?php echo $RowSet->option_c;  ?>" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Option D</label>
                                                                <input type="text" name="option_d" id="option_d" value="<?php echo $RowSet->option_d;  ?>" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Correct Answer<span class="required"> * </span></label>
                                                                <select id="correct_answer" name="correct_answer" class="form-control input-sm " placeholder="Please select" onchange="CheckValidAnswer();" >
                                                                    <option value="">Please Select</option>
                                                                    <option value="a" <?php echo ($RowSet->correct_answer == 'a' ? 'Selected' : ''); ?>>Option A</option>
                                                                    <option value="b" <?php echo ($RowSet->correct_answer == 'b' ? 'Selected' : ''); ?>>Option B</option>
                                                                    <option value="c" <?php echo ($RowSet->correct_answer == 'c' ? 'Selected' : ''); ?>>Option C</option>
                                                                    <option value="d" <?php echo ($RowSet->correct_answer == 'd' ? 'Selected' : ''); ?>>Option D</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Tip</label>
                                                                <textarea rows="2" class="form-control input-sm" id="tip" maxlength="150" name="tip" placeholder="Tip"><?php echo $RowSet->tip;  ?></textarea>
                                                                <span class="text-muted">(Max 150 Characters)</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Question Image</label>
                                                                <div class="form-control fileinput fileinput-exists" style="    border: none;height:auto;" data-provides="fileinput">
                                                                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                                                <img src="<?php echo base_url().'assets/uploads/no_image.png'?>" alt=""/>
                                                                            </div>
                                                                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                                                <img src="<?php echo base_url().'assets/uploads/'.($RowSet->hint_image!='' ?'questions/'.$RowSet->hint_image : 'no_image.png'); ?>" alt=""/>
                                                                            </div>
                                                                            <div>
                                                                                    <span class="btn default btn-file">
                                                                                    <span class="fileinput-new">
                                                                                    Select image </span>
                                                                                    <span class="fileinput-exists">
                                                                                    Change </span>
                                                                                    <input type="file" name="hint_image" id="hint_image">
                                                                                    </span>
                                                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput" onclick="RemoveHintImage();">
                                                                                    Remove 
                                                                                </a>
                                                                                <input type="hidden" name="RemoveHintImage" id="RemoveHintImage" value="0"> 
                                                                            </div>
                                                                    </div><br/>
                                                                <span class="text-muted">(Extensions allowed: .png , .gif, .jpg, .jpeg, .bmp)  width:750px, height:400px)</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Youtube Link</label>
                                                                <input type="text" name="youtube_url" id="youtube_url" placeholder="Youtube Url" value="<?php echo $RowSet->youtube_link;  ?>" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">    
                                                            <div class="form-group">
                                                                <label>Status<span class="required"> * </span></label>
                                                                <select id="status" name="status" class="form-control input-sm " placeholder="Please select" >
                                                                    <option value="1" <?php echo ($RowSet->status == 1) ? 'selected' : ''; ?>>Active</option>
                                                                    <option value="0" <?php echo ($RowSet->status == 0) ? 'selected' : ''; ?>>In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                                           
                                            </div>                                      
                                        </div>
                                        <div class="row">      
                                            <div class="col-md-12 text-right">  
                                                <button type="button" id="questionset-submit" name="questionset-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="SubmitData();" >
                                                    <span class="ladda-label">Update</span>
                                                </button>
                                                <a href="<?php echo site_url("questions"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
<?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script>
    var FrmQns = $('#FrmQns');
    var form_error = $('.alert-danger', FrmQns);
    var form_success = $('.alert-success', FrmQns);
    jQuery(document).ready(function () {
    jQuery.validator.addMethod("validateUrl", function (val, element) {
        //var re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;
        if (val.length == 0) {
            return true;
        }
        if (!/^(https?|ftp):\/\//i.test(val)) {
            val = 'http://' + val; // set both the value
            $(element).val(val); // also update the form element
        }
        return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(val);
    }, "Please enter valid url");
    
    FrmQns.validate({
        errorElement: 'span',
        errorClass: 'help-block help-block-error',
        focusInvalid: false,
        ignore: "",
        rules: {
            company_id: {
                required: true
            },
            topic_id: {
                required: true
            },
            subtopic_id: {
                required: true
            },
            question_title: {
                required: true
            },
            option_a: {
                required: true
            },
            option_b: {
                required: true
            },
            correct_answer: {
                required: true
            },
            status: {
                required: true
            },                                                                
            youtube_url: {
                validateUrl: true
            },
            language_id:{
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
            Ladda.bind('button[id=questionset-submit]');
            form.submit();
        }
    });                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
    $('.select2me,.select2-multiple').on('change', function() {
        $(this).valid();
    });
});
function CheckValidAnswer(){
    var Select = $('#correct_answer').val();
    if($('#option_'+Select).val()==""){
        ShowAlret("Please select Valid Option", 'warning');
        $('#correct_answer').val("");
        //$('#correct_answer').select2("val","");
    }
}
function SubmitData() {  
    $('#errordiv').hide();
    
    if (!$('#FrmQns').valid()) {
            return false;
    }
    var form_data = new FormData(); 
    if($('#hint_image').prop('files') !=undefined){
        var file_data = $('#hint_image').prop('files')[0];                  
        form_data.append('hint_image', file_data);
    }
    var other_data = $('#FrmQns').serializeArray();
    $.each(other_data,function(key,input){
        form_data.append(input.name,input.value);
    });
        $.ajax({
            url: "<?php echo base_url() . 'index.php/questions/update/'.  base64_encode($RowSet->id); ?>",
            type: 'POST',
            data:  form_data,
            contentType: false,
            cache: false,
            processData:false,
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
        return true;
    }
function getComapnywiseTopic() {
    $.ajax({
        type: "POST",
        data: "data=" + $('#company_id').val(),
        url: "<?php echo base_url(); ?>questionset/ajax_company_topic",
        beforeSend: function () {
        	customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var TopicMSt = Oresult['result'];
                var option = '<option value="">Please Select</option>';
                for (var i = 0; i < TopicMSt.length; i++) {
                    option += '<option value="' + TopicMSt[i]['id'] + '">' + TopicMSt[i]['description'] + '</option>';
                }
                $('#topic_id').empty();
                $('#topic_id').append(option);
                $('#subtopic_id').empty();
            }
            customunBlockUI();
        }
    });
}
function getTopicwiseSubtopic() {
    var Topic = $('#topic_id').val();
    if(Topic==""){
        $('#subtopic_id').empty();
        return false;
    }
    $.ajax({
        type: "POST",
        data: "data=" + Topic,
        async: false,
        url: "<?php echo base_url(); ?>questionset/ajax_topic_subtopic",
        beforeSend: function () {
        	customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var SubTopicMSt = Oresult['result'];
                var option = '<option value="">Please Select</option>';
                for (var i = 0; i < SubTopicMSt.length; i++) {
                    option += '<option value="' + SubTopicMSt[i]['id'] + '"' + (SubTopicMSt.length > 1 ? "" : "Selected")+ '>' + SubTopicMSt[i]['description'] + '</option>';
                }
                $('#subtopic_id').empty();
                $('#subtopic_id').append(option);
                //$("#topic_id").trigger("change");
            } 
            customunBlockUI();
        }
    });
}

function RemoveHintImage() {
    $('#RemoveHintImage').val(1);
}
    </script>
</body>
</html>