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
                                    <span>Video Q&A/Situation</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>New Q/A</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>video_situation" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Create Video Q/A
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body"> 
                                        <form id="frmVideo_situation" name="frmVideo_situation" method="POST" > 
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_overview">                                                    
                                                    <?php
                                                    $errors = validation_errors();                                                    
                                                    if ($errors) { ?>
                                                        <div style="display: block;" class="alert alert-danger display-hide">
                                                            <button class="close" data-close="alert"></button>
                                                            You have some form errors. Please check below.
                                                            <?php echo $errors; ?>
                                                        </div>
                                                    <?php } ?>                                                       
                                                    <?php if ($Company_id == "") { ?>
                                                    <div class="row">
                                                        <div class="col-md-4">       
                                                            <div class="form-group">
                                                                <label class="">Company Name<span class="required"> * </span></label>
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" >
                                                                    <option value="">Please Select</option>
                                                                    <?php foreach ($CompnayResultSet as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>    
                                                    <?php } ?>
                                                    <div class="row">                                                        
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Assessment Type<span class="required"> * </span></label>
                                                                <select name="assessment_type" id="assessment_type" class="form-control input-sm select2">
                                                                    <?php foreach ($assessment_type as $val) { ?>
                                                                        <option value="<?php echo $val->id ?>" <?php echo ($val->default_selected ? 'selected':'') ?>><?php echo $val->description ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Question type<span class="required"> * </span></label>
                                                                <select id="question_type" name="question_type" class="form-control input-sm select2" placeholder="Please select" onchange="getquestion_type();" >
                                                                    <option value="0" selected>Question</option>
                                                                    <option value="1">Situation</option>
                                                                </select>
                                                            </div>
                                                        </div> -->
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Read Timer<span class="required"> * </span></label>
                                                                <input type="number" name="read_timer" id="read_timer" min="0"  class="form-control input-sm" value="0">
                                                                <span class="text-muted" style="color:red">(This sets the maximum time in seconds,Zero means no timer)</span>
                                                            </div>                                                            
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Response timer<span class="required"> * </span></label>
                                                                <input type="number" name="response_timer" id="timer" min="0" max="300"  class="form-control input-sm" value="0">
                                                                <span class="text-muted" style="color:red">(This sets the maximum time in seconds,Zero means no timer)</span>
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
                                                        <!-- <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Weightage<span class="required"> * </span></label>
                                                                <input type="number" name="weightage" id="weightage" min="0"  class="form-control input-sm">                                                                
                                                            </div>
                                                        </div> -->
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Question Format<span class="required"> * </span></label>
                                                                <select id="question_option" name="question_option" class="form-control input-sm select2" placeholder="Please select"  >
                                                                    <option value="0" selected>Text</option>
                                                                     <option value="1">Video</option> 
                                                                    <option value="2">Image</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div id="queText" class="form-group">
                                                                <label><span id="label_dyamic">Question</span><span class="required"> * </span></label>
                                                                <textarea type="text" name="question" id="question" cols="5" rows="3" class="form-control input-sm"></textarea> 
                                                            </div>  
                                                            <div id="queImage" class="form-group" style="display:none;">
                                                                <label><span id="label_dyamic">Question</span><span class="required"> * </span></label>
                                                                <!-- <input type="hidden" name="question" id="question" value="" class="form-control"/>    -->
                                                                <input type="file" id="myFileInput" name="myFileInput" style="display:none;" accept="image/png, image/gif, image/jpeg"/>
                                                                <div class="form-control fileinput fileinput-exists" style="border: none;height:auto;padding:0" data-provides="fileinput" onclick="document.getElementById('myFileInput').click()">
                                                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 750px; max-height: 400px;">
                                                                        <img  id="question_preview" src="<?php echo base_url() . 'assets/uploads/no_image.png'; ?>" alt="No Image"/>
                                                                    </div>
                                                                </div>
                                                                <span class="text-muted" style="color:red" id="file_desc"></span>
                                                            </div>
                                                            <div id="queVideo" class="form-group" style="display:none;">
                                                                <label><span id="label_dyamic">Question</span><span class="required"> * </span></label>
                                                                <!-- <input type="hidden" name="question" id="question" value="" class="form-control"/>    -->
                                                                <input type="file" id="myFileInputv" name="myFileInputv" style="display:none;" accept="video/mp4,video/x-m4v,video/*"/>
                                                                <div class="form-control fileinput fileinput-exists" style="border: none;height:auto;padding:0" data-provides="fileinput" onclick="document.getElementById('myFileInputv').click()">
                                                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 750px; max-height: 400px;">
                                                                        <img  id="question_preview" src="<?php echo base_url() . 'assets/uploads/no_videos.jpg'; ?>" width="250" height="200" alt="No Image"/>
                                                                    </div>
                                                                </div>
                                                                <span class="text-muted" style="color:red" id="file_desc1"></span>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                             <div id="queTital" class="form-group" style="display:none;">
                                                                <label><span id="label_dyamic">Question Title</span><span class="required"> * </span></label>
                                                                <textarea type="text" name="question_tital" id="question_tital" cols="5" rows="3" class="form-control input-sm"></textarea> 
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Assessor Guide</label>
                                                                <textarea type="text" name="assessor_guide" id="assessor_guide" cols="5" rows="3" class="form-control input-sm"></textarea>   
                                                            </div>                                                           
                                                        </div>                                            
                                                    </div>   
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Slide Heading (For Spotlight App)</label>
                                                                <textarea type="text" name="slide_heading" id="slide_heading" cols="5" rows="3" class="form-control input-sm"></textarea>   
                                                            </div>                                                           
                                                        </div>   
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Slide Description (For Spotlight App)</label>
                                                                <textarea type="text" name="slide_description" id="slide_description" cols="5" rows="3" class="form-control input-sm"></textarea>   
                                                            </div>                                                           
                                                        </div>                                           
                                                    </div>
                                                </div>                                                           
                                            </div>                                            
                                            <div class="row">      
                                                <div class="col-md-12 text-right">  
                                                    <button type="button" id="situation-submit" name="situation-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="save_video_situation();">
                                                        <span class="ladda-label">Submit</span>
                                                    </button>
                                                    <a href="<?php echo site_url("video_situation"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
        <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/ckeditor.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/adapters/jquery.js"></script>
        <script>         
        var frmVideo_situation = $('#frmVideo_situation');            
        var form_error = $('.alert-danger', frmVideo_situation);
        var form_success = $('.alert-success', frmVideo_situation);
        var baseURL ="<?php echo $base_url; ?>";
        jQuery(document).ready(function() {    
            CKEDITOR.replace('question', {
				toolbar: [
                    // {
					// 	name: 'styles',
					// 	items: ['Styles', 'Format']
					// },
					{
						name: 'basicstyles',
						items: ['Bold','-','Italic']
					}
					// {
					// 	name: 'paragraph',
					// 	items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent']
					// }
					// {
					// 	name: 'links',
					// 	items: ['Link', 'Unlink', 'Anchor']
					// }
				],
			});
			CKEDITOR.replace('question_tital', {
				toolbar: [
                    // {
					// 	name: 'styles',
					// 	items: ['Styles', 'Format']
					// },
					{
						name: 'basicstyles',
						items: ['Bold', 'Italic']
					}
					// {
					// 	name: 'paragraph',
					// 	items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent']
					// }
					// {
					// 	name: 'links',
					// 	items: ['Link', 'Unlink', 'Anchor']
					// }
				],
			});    
            CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR; // pressing the ENTER KEY input <br/>
            CKEDITOR.config.autoParagraph = false;      
            var queFormat = '';
            $('#question_option').change(function(){
                var queFormat = $('#question_option').val();
                if(queFormat == '0'){
                    showQueText();
                }else if(queFormat == '2'){
                   showQueImage();
                }else if(queFormat == '1'){
                   showQueVideo();
                }
            });
            $("#myFileInput").change(function()
            {
                var queFormat = $('#question_option').val();
                var formData = new FormData();
                if(queFormat=='1')
                {
                     var file_data = $('#myFileInputv').prop('files')[0]; 
                }
                else
                {
                    var file_data = $('#myFileInput').prop('files')[0];
                }
               
                formData.append('file', file_data);
                formData.append('isUpdate', 0);
                var fileType = (queFormat=='1') ? 'selectvideo' : 'selectImage';
                $.ajax({
                    url : '<?php echo base_url(); ?>video_situation/'+fileType,
                    type : 'POST',
                    data : formData,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,  // tell jQuery not to set contentType
                    success : function(Odata) {
                        var Data = $.parseJSON(Odata);
                        if (Data['Success'] && Data['Message']!='') {
                            ShowAlret(Data['Message'], 'success'); 
                            // $("#question").val(Data['que_image']);
                            CKEDITOR.instances['question'].setData(Data['que_image']);
                            $('#question_preview').prop('src', baseURL+'assets/uploads/questions/'+Data['que_image']);
                        } else {
                            ShowAlret(Data['Message'], 'error');
                            $('#errordiv').show();
                            $('#errorlog').html(Data['Message']);
                        }
                    },
                    complete: function(){
                        
                    }
                });
            });
            function showQueText(){
            $('#queText').show();
            $('#queImage').hide();
            $('#queVideo').hide();
            $('#queTital').hide();
            
            $("#file_desc").html('');
            $("#file_desc1").html('');
        }
        function showQueImage(){
            CKEDITOR.instances['question'].setData('');
            $('#queText').hide();
            $('#queVideo').hide();
            $('#queImage').show();
            $('#queTital').show();
            $("#file_desc1").html('');
            $("#file_desc").html('(Allowed: Extensions: .png , .gif, .jpg, .jpeg. Max-Width:750px, Max-Height:400px)');
        }
        function showQueVideo(){
            CKEDITOR.instances['question'].setData('');
            $('#queText').hide();
            $('#queImage').hide();
            $('#queVideo').show();
            $("#file_desc").html('');
            $('#queTital').show();
            $("#file_desc1").html('(Allowed: Extensions: .mp4 , .m4v.)');
            
        }    
            frmVideo_situation.validate({
                errorElement: 'span',
                errorClass: 'help-block help-block-error',
                focusInvalid: false,
                ignore: "",
                rules: {
                    company_id: {
                        required: true
                    },
                    assessment_type: {
                        required: true
                    },
                    question: {
                        required: function(element) {
                            return ($('#question_option').val() == "0");
                        },
                        questionCheck: true
                    },
                    question_tital:{
                         required: function(element) {
               
                               return ($('#question_option').val() == "1" || $('#question_option').val()=="2");
                           }
                    },
                    question_type:{
                        required: true
                    },
                    myFileInput:{
                        required: function(element) {
                            return $('#question_option').val() == "2";
                        }
                    },
                    myFileInputv:{
                        required: function(element) {
                            return $('#question_option').val() == "1";
                        }
                    },
                    // question_type:{
                    //     required: true
                    // },
                    // weightage: {
                    //     required: true                                 
                    // },
                    timer :{
                        required: true
                    },
                    status: {
                        required: true
                    }                    
                },
                messages: {
                    myFileInput: "Please select Image",
                    myFileInputv: "Please select Video",
                },
                invalidHandler: function (event, validator) {
                    form_success.hide();
                    form_error.show();
                    App.scrollTo(form_error, -200);
                },
                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.hasClass('.form-group')) {
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
                    Ladda.bind('button[id=situation-submit]');
                    form.submit();
                }
            });
            jQuery.validator.addMethod("questionCheck", function (value, element) {
                var isSuccess = false;                                
                $.ajax({
                    type: "POST",
                    data: {question: value, assessment_type: $('#assessment_type').val()},
                    url: "<?php echo base_url(); ?>video_situation/Check_question",
                    async: false,
                    success: function (msg) {
                        isSuccess = msg != "" ? false : true;
                    }
                });
                return isSuccess;
            }, "Question already exists!!!");
        });
        function getquestion_type(){
            if($('#question_type').val()==1){
                $('#label_dyamic').text('Situation');
            }else{
                $('#label_dyamic').text('Question');
            }
        }
        function getExtension(filename) {
          var parts = filename.split('.');
          return parts[parts.length - 1];
        }
        function isVideo(filename) {
          var ext = getExtension(filename);
          switch (ext.toLowerCase()) {
            case 'm4v':
            case 'avi':
            case 'mpg':
            case 'mp4':
              // etc
              return true;
          }
          return false;
        }

        function save_video_situation() {
            var queFormat = $('#question_option').val();
            if(queFormat=='1')
            {
                CKEDITOR.instances['question'].setData('test it');
            }
           
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            if (!$('#frmVideo_situation').valid()) {
                return false;
            }  
            var fd = new FormData();
            
               var queFormat = $('#question_option').val();
               
                if(queFormat=='1')
                {

                   var file_data = $('#myFileInputv')[0].files;
                   console.log(file_data.length);
                //   return false;
                   if(file_data.length!='0')
                   {
                       fd.append("file", file_data[0]);
                       console.log(file_data[0]['name']);
                       var is_video= isVideo(file_data[0]['name']);
                       if(!is_video)
                       {
                           ShowAlret('Selected File Formate is not valid', 'error');
                           return false;
                       }
                   }
                   else
                   {
                        ShowAlret('Please Select Video', 'error');
                        return false;
                   }
                   

                }
 
                              
              var other_data = $('#frmVideo_situation').serializeArray();
                 $.each(other_data,function(key,input){
                 fd.append(input.name,input.value);
             });                      
            $.ajax({
                type: "POST",
                url: '<?php echo base_url(); ?>video_situation/submit',
                //data: $('#frmVideo_situation').serialize(),
                data:fd,
                processData: false, 
                contentType: false,
                beforeSend: function () {
                    customBlockUI();
                },
                success: function (Odata) {
                    var Data = $.parseJSON(Odata);
                    if (Data['success']) {
                        ShowAlret(Data['Msg'], 'success');   
                        window.location.href = '<?php echo base_url(); ?>video_situation/index';
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
        </script>                
    </body>
</html>