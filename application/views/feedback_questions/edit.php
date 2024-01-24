<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
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
                                    <span>Question</span>
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
                                            Edit Feedback Question
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <form id="FrmFeedbackQns" name="FrmFeedbackQns" method="POST"  action="<?php echo $base_url; ?>feedback_questions/submit" enctype="multipart/form-data" > 
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
                                                    <?php if ($this->mw_session['company_id'] == "") { ?>
                                                        <div class="row">    
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Company Name<span class="required"> * </span></label>
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2me" placeholder="Please select" style="width:100%" onchange="feedbackTypeData();">
                                                                        <option value="">Please Select</option>
                                                                        <?php
                                                                        if (isset($CompanySet)) {
                                                                            foreach ($CompanySet as $Row) {
                                                                                ?>
                                                                                <option value="<?php echo $Row->id ?>" <?php echo ($RowSet->company_id == $Row->id ? 'Selected' : ''); ?> ><?php echo $Row->company_name ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div> 
                                                        </div>
                                                    <?php } ?>
                                                    <div class="row">    
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Feedback Type<span class="required"> * </span></label>
                                                                <select id="feedback_type" name="feedback_type" class="groupSelectClass form-control input-sm select2me" placeholder="Please select" onchange="getfeedbacksupType();">
                                                                    <option value="">Please Select</option>
                                                                    <?php
                                                                    if (isset($feedback_typeSet)) {
                                                                        foreach ($feedback_typeSet as $Row) {
                                                                            ?>
                                                                            <option value="<?php echo $Row->id ?>" <?php echo ($RowSet->feedback_type_id == $Row->id ? 'Selected' : ''); ?>><?php echo $Row->description ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Feedback Sub-Type</label>
                                                                <select id="feedback_subtype" name="feedback_subtype" class="groupSelectClass form-control input-sm select2me" placeholder="Please select" >
                                                                    <?php
                                                                    if (isset($feedback_subtypeSet)) {
                                                                        foreach ($feedback_subtypeSet as $Row) {
                                                                            ?>
                                                                            <option value="<?php echo $Row->id ?>" <?php echo ($RowSet->feedback_subtype_id == $Row->id ? 'Selected' : ''); ?>><?php echo $Row->description ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Question Type<span class="required"> * </span></label>
                                                                <select id="question_type" name="question_type" class=" form-control input-sm select2me" placeholder="Please select" onchange="valid_multiple_opt();" >
                                                                    <option value="0" <?php echo ($RowSet->question_type == 0 ? 'Selected' : ''); ?>>Multiple choice</option>
                                                                    <option value="1" <?php echo ($RowSet->question_type == 1 ? 'Selected' : ''); ?>>Text</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">    
                                                            <div class="form-group">
                                                                <label>Status<span class="required"> * </span></label>
                                                                <select id="status" name="status" class="form-control input-sm" placeholder="Please select" >
                                                                    <option value="1" <?php echo ($RowSet->status == 1 ? 'Selected' : ''); ?> >Active</option>
                                                                    <option value="0" <?php echo ($RowSet->status == 0 ? 'Selected' : ''); ?>>In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>                                                                                                   
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Question<span class="required"> * </span></label>
                                                                <textarea rows="2" class="form-control input-sm" id="question_title" name="question_title" placeholder="Question ?"><?php echo $RowSet->question_title; ?></textarea>
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
                                                    <div class="row text_opt">
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Min Length<span class="required"> * </span></label>
                                                                <input type="number" name="min_length" id="min_length" placeholder="Min Length" min="0"  class="form-control input-sm" value="<?php echo $RowSet->min_length; ?>">   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Max Length<span class="required"> * </span></label>
                                                                <input type="number" name="max_length" id="max_length" placeholder="Max Length" min="0"  class="form-control input-sm" value="<?php echo $RowSet->max_length; ?>">   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Weightage</label>
                                                                <input type="Number" name="text_weightage" id="text_weightage" min="0"  placeholder="Weightage" class="form-control input-sm" value="<?php echo ($RowSet->text_weightage == "0" ? '' : $RowSet->text_weightage); ?>" >   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Timer (In Sec.)</label>
                                                                <input type="number" name="question_timer" id="question_timer" maxlength="255" class="form-control input-sm" value="<?php echo $RowSet->question_timer; ?>">   
                                                            </div>
                                                            <span class="text-muted" style="color:red">(This sets the maximum time in seconds,Zero means no time)</span>
                                                        </div>
                                                    </div>
                                                    <div class="row multiple_opt">
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Option A<span class="required"> * </span></label>
                                                                <input type="text" name="option_a" id="option_a" maxlength="255" placeholder="Option A" value="<?php echo $RowSet->option_a; ?>" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">    
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <input type="Number" name="weight_a" id="weight_a" value="<?php echo $RowSet->weight_a; ?>" maxlength="255" placeholder="Weightage" class="form-control input-sm" >   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Option B</label>
                                                                <input type="text" name="option_b" id="option_b" placeholder="Option B" value="<?php echo $RowSet->option_b; ?>" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">    
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <input type="Number" name="weight_b" id="weight_b" min="0" maxlength="255" value="<?php echo ($RowSet->option_b != "" ? $RowSet->weight_b : ''); ?>" placeholder="Weightage" class="form-control input-sm" >   
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row multiple_opt">
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Option C</label>
                                                                <input type="text" name="option_c" id="option_c" placeholder="Option C" value="<?php echo $RowSet->option_c; ?>" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">    
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <input type="Number" name="weight_c" id="weight_c" min="0" maxlength="255" value="<?php echo ($RowSet->option_c != "" ? $RowSet->weight_c : ''); ?>" placeholder="Weightage" class="form-control input-sm" >   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Option D</label>
                                                                <input type="text" name="option_d" id="option_d" value="<?php echo $RowSet->option_d; ?>" placeholder="Option D" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">    
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <input type="Number" name="weight_d" id="weight_d" value="<?php echo ($RowSet->option_d != "" ? $RowSet->weight_d : ''); ?>" min="0" maxlength="255" placeholder="Weightage" class="form-control input-sm" >   
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row multiple_opt">
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Option E</label>
                                                                <input type="text" name="option_e" id="option_e" value="<?php echo $RowSet->option_e; ?>" placeholder="Option E" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">    
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <input type="Number" name="weight_e" id="weight_e"  value="<?php echo ($RowSet->option_e != "" ? $RowSet->weight_e : ''); ?>"min="0" maxlength="255" placeholder="Weightage" class="form-control input-sm" >   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Option F</label>
                                                                <input type="text" name="option_f" id="option_f" value="<?php echo $RowSet->option_f; ?>" placeholder="Option F" maxlength="255" class="form-control input-sm">   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">    
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <input type="Number" name="weight_f" id="weight_f" value="<?php echo ($RowSet->option_f != "" ? $RowSet->weight_f : ''); ?>" maxlength="255" min="0" placeholder="Weightage" class="form-control input-sm" >   
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row multiple_opt">
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Multiple Selection<span class="required"> * </span></label>
                                                                <select id="multiple_allow" name="multiple_allow" class="form-control input-sm " placeholder="Please select" >
                                                                    <option value="0" <?php echo ($RowSet->multiple_allow == 0 ? 'Selected' : ''); ?>>Only One</option>
                                                                    <option value="1" <?php echo ($RowSet->multiple_allow == 1 ? 'Selected' : ''); ?>>One or More</option>
                                                                </select>
                                                            </div>
                                                        </div>    
                                                    </div> 
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Tip</label>
                                                                <textarea rows="2" class="form-control input-sm" id="tip" maxlength="150" name="tip" placeholder="Tip" value="<?php echo $RowSet->tip; ?>"><?php echo $RowSet->tip; ?></textarea>
                                                                <span class="text-muted">(Max 150 Characters)</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Hint Image</label>
                                                                <div class="form-control fileinput fileinput-exists" style="    border: none;height:auto;" data-provides="fileinput">
                                                                    <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                                        <img src="<?php echo $asset_url . 'assets/uploads/no_image.png' ?>" alt=""/>
                                                                    </div>
                                                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                                        <img src="<?php echo $asset_url . 'assets/uploads/' . ($RowSet->hint_image != '' ? 'feedback_questions/' . $RowSet->hint_image : 'no_image.png'); ?>" alt=""/>
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
                                                    </div>    
                                                </div>                                                           
                                            </div>                                      
                                        </div>
                                        <div class="row">      
                                            <div class="col-md-12 text-right">  
                                                <button type="button" id="feedback_questions-submit" name="feedback_questions-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="SaveFeedbackQns();" >
                                                    <span class="ladda-label">Update</span>
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
                <?php //$this->load->view('inc/inc_quick_sidebar');     ?>
            </div>
            <?php //$this->load->view('inc/inc_footer');    ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav');   ?>
		<?php $this->load->view('inc/inc_footer_script'); ?>
		<script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script>
    jQuery(document).ready(function () {
//    $('.select2me').select2({
//        placeholder: "Please Select",
//        allowClear: true
//    });
    valid_multiple_opt();
    var FrmFeedbackQns = $('#FrmFeedbackQns');
    var form_error = $('.alert-danger', FrmFeedbackQns);
    var form_success = $('.alert-success', FrmFeedbackQns);
    FrmFeedbackQns.validate({
        errorElement: 'span',
        errorClass: 'help-block help-block-error',
        //focusInvalid: false,
        ignore: "",
        rules: {
            company_id: {
                required: true
            },            
            question_title: {
                required: true
            },
            feedback_type:{
                required: true
            },
            option_a: {
                required: function () {
                    return $('#question_type').val() != 1
                }
            },
            weight_a: {
                required: function () {
                    return $('#question_type').val() != 1
                }
            },
            question_type: {
                required: true
            },
            max_length:{
                required: function () {
                    return $('#question_type').val() == 1
                }
            },
            weight_b :{
               required: function () {
                    return $('#option_b').val() !=''
                }
            },
            weight_c :{
               required: function () {
                    return $('#option_c').val() !=''
                }
            },
            weight_d :{
               required: function () {
                    return $('#option_d').val() !=''
                }   
            },
            weight_e :{
               required: function () {
                    return $('#option_e').val() !=''
                }
            },
            weight_f :{
                required: function () {
                    return $('#option_f').val() !=''
                } 
            },
            option_b :{
               required: function () {
                    return $('#weight_b').val() !=''
                }
            },
            option_c :{
               required: function () {
                    return $('#weight_c').val() !=''
                }
            },
            option_d :{
               required: function () {
                    return $('#weight_d').val() !=''
                }   
            },
            option_e :{
               required: function () {
                    return $('#weight_e').val() !=''
                }
            },
            option_f :{
                required: function () {
                    return $('#weight_f').val() !=''
                } 
            },
            status: {
                required: true
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
            Ladda.bind('button[id=feedback_questions-submit]');
            form.submit();
        }
    });                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
     $('.select2me,.select2-multiple').on('change', function() {
        $(this).valid();
    });
});
function SaveFeedbackQns(){
    if(!$('#FrmFeedbackQns').valid()){
        return false;
    }
	var form_data = new FormData(); 
    if($('#hint_image').prop('files') !=undefined){
        var file_data = $('#hint_image').prop('files')[0];                  
        form_data.append('hint_image', file_data);
    }
    var other_data = $('#FrmFeedbackQns').serializeArray();
    $.each(other_data,function(key,input){
        form_data.append(input.name,input.value);
    });
    $.ajax({
        type: "POST",
        url: '<?php echo site_url("feedback_questions/update/".base64_encode($RowSet->id)); ?>',
        data:  form_data,
		contentType: false,
        cache: false,
        processData:false,
         beforeSend: function () {
            customBlockUI();
        },
        success: function(Odata){
           //alert(result);
           var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');  
            } else {
                $('#errordiv').show();
                $('#errorlog').html(Data['Msg']);
            }
             customunBlockUI();
         },error: function(XMLHttpRequest, textStatus, errorThrown) { 
            ShowAlret("Status: " + textStatus+ " ,Contact Mediaworks for technical support!"); 
        }
    });
}
function FormReset(){
    $('#question_title').val("");
    var str = "abcdef";
    for(var i=0; i<str.length; i++)
    {
       var nextChar = str.charAt(i);
       $('#option_'+nextChar).val("");
       $('#weight_'+nextChar).val("");
    }
    
}
//function addOptions(){
//    alpha=alpha+1;
//    var txt1 = "<label>Option "+String.fromCharCode(alpha)+"<span class='required'> * </span></label>\n\
//        <input type='text' name='option_"+String.fromCharCode(alpha)+"' id='option_"+String.fromCharCode(alpha)+"' maxlength='255' placeholder='Option "+String.fromCharCode(alpha)+"' class='form-control input-sm'>";            
//    var txt2= "<label>Weightage<span class='required'> * </span></label>\n\
//        <input type='number' name='weightage_"+String.fromCharCode(alpha)+"' id='weightage_"+String.fromCharCode(alpha)+"' maxlength='255' placeholder='Weightage' class='form-control input-sm'>";         
//    count=count+1; 
//   if(count%2==0){
//        $(".addopt2").append(txt1);
//        $(".weight2").append(txt2);
//    }else{
//        $(".addopt1").append(txt1);
//        $(".weight1").append(txt2);
//    }
//}
function feedbackTypeData(){
    var company_id =$('#company_id').val();
    if(company_id==""){
        $('#feedback_type').empty();
        return false;
    }
    $.ajax({
        type: "POST",
        data: "data=" +company_id,
        async:false,
        beforeSend: function () {
            customBlockUI();
        },
        url: "<?php echo base_url(); ?>feedback_questions/ajax_company_feedbackType",
            success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var FeedbackTypeMSt = Oresult['result'];                             
                var option='<option value="">Please Select</option>';                            
                for (var i = 0; i < FeedbackTypeMSt.length; i++) {
                    option += '<option value="' + FeedbackTypeMSt[i]['id'] + '">' + FeedbackTypeMSt[i]['description'] + '</option>';
                }                            
                $('#feedback_type').empty();
                $('#feedback_type').append(option); 
                $('#feedback_subtype').empty();
            }
            customunBlockUI();
        }
    });
}
function getfeedbacksupType(){
var ftype =$('#feedback_type').val();
if(ftype==""){
    $('#feedback_subtype').empty();
    return false;
}
    $.ajax({
        type: "POST",
        data: "feedback_type=" + ftype,
        async:false,
         beforeSend: function () {
            customBlockUI();
        },
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

            }
            customunBlockUI();
        }
    });
}
function valid_multiple_opt() {
    var type = $('#question_type').val();
    if (type == 0 || type == '') {
        $('.multiple_opt').show();
        $('.text_opt').hide();
    } else {
        $('.multiple_opt').hide();
        $('.text_opt').show();
    }
}
function RemoveHintImage() {
    $('#RemoveHintImage').val(1);
}
</script>
</body>
</html>