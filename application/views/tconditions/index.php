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
        <style>
            .fa{color:#fff;}
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
                                    <span>Administrator</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Terms & Conditions</span>
                                </li>
                            </ul>
                            
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div id="errordiv" class="alert alert-danger display-hide">
                                    <button class="close" data-close="alert"></button>
                                    You have some form errors. Please check below.
                                    <br><span id="errorlog"></span>
                                </div>
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            Create Terms & Conditions
                                           <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <form id="frmTemplate" name="frmTemplate" method="POST"  action="">    
                                            <div class="row">    
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Please select an Template name :<span class="required" aria-required="true"> * </span></label>
                                                        <div class="col-md-6">
                                                            <select name="template_name" id="template_name" class='form-control select2me' onchange="fetchTemplateData()">
                                                                <option value="">Please select</option>
                                                                <?php
                                                                    foreach ($Template as $data) {?>
                                                                        <option value="<?php echo $data->id; ?>"><?php echo $data->terms_name; ?></option>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br><br>
                                            
                                            <div class="row">
                                                <div class="col-md-11">    
                                                    <div class="form-group">
                                                        <label>Template Body</label>
                                                        <textarea cols="80" id="template_body" name="template_body" rows="10" class="form-control input-sm cke-editor"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">      
                                                <div class="col-md-12 text-right margin-top-20">  
                                                    <button type="button" id="template-submit" name="template-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left"  onclick="updateTemplate();">
                                                        <span class="ladda-label">Update</span>
                                                    </button>
                                                    <a href="<?php echo site_url("templates");?>" class="btn btn-default btn-cons">Cancel</a>
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
<script src="<?php echo $base_url; ?>assets/global/plugins/cke-editor/ckeditor.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/cke-editor/adapters/jquery.js"></script>

<script>
    $(document).ready(function () {
        //'<?php echo site_url("Tconditions/fetchTemplateData"); ?>'
        CKEDITOR.replace( 'template_body', 
            { customConfig : '<?php echo site_url("assets/customjs/ckeditorcustomconfig.js") ?>' } 
        );
        
//        //Example of preserving a JavaScript event for inline calls.
//            /****  CKE Editor  ****/
//            CKEDITOR.replace( 'template_body', {
//                    // Define the toolbar groups as it is a more accessible solution.
//                    toolbarGroups: [
//                            {"name":"basicstyles","groups":["basicstyles"]},
//                            {"name":"links","groups":["links"]},
//                            {"name":"paragraph","groups":["list","blocks"]},
//                            {"name":"document","groups":["mode"]},
//                            {"name":"insert","groups":["insert"]},
//                            {"name":"styles","groups":["styles"]}
//
//                    ],
//                    // Remove the redundant buttons from toolbar groups defined above.
//                    removeButtons: 'Strike,Subscript,Superscript,Anchor,Styles,Specialchar'
//            } );

        var frmTemplate = $('#frmTemplate');
        var error3      = $('.alert-danger', frmTemplate);
        var success3    = $('.alert-success', frmTemplate);

        
        frmTemplate.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                template_name: {
                    required: true
                }
            },
            messages: {// custom messages for radio buttons and checkboxes

            },
            errorPlacement: function (error, element) { // render error placement for each input type
                if (element.parent(".input-group").size() > 0) {
                    error.insertAfter(element.parent(".input-group"));
                } else if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else if (element.parents('.radio-list').size() > 0) {
                    error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                } else if (element.parents('.radio-inline').size() > 0) {
                    error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                } else if (element.parents('.checkbox-list').size() > 0) {
                    error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                } else if (element.parents('.checkbox-inline').size() > 0) {
                    error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit   
                success3.hide();
                error3.show();
                //Metronic.scrollTo(error3, -200);
            },
            highlight: function (element) { // hightlight error inputs
                $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            success: function (label) {
                label
                        .closest('.form-group').removeClass('has-error'); // set success class to the control group
            },
            submitHandler: function (form) {
                success3.show();
                error3.hide();
                form[0].submit(); // submit the form
            }

        });
         
    });
    
    function fetchTemplateData(){
        $.ajax({
            type : "POST",
            data : {template_id :$('#template_name').val()},
            url  : '<?php echo site_url("Tconditions/fetchTemplateData"); ?>',
            success : function(Odata){   
                var Data = $.parseJSON(Odata);
                
                if (Data['TemplateBody']) {
                    CKEDITOR.instances['template_body'].setData(Data['TemplateBody']);
                }
  
            },error: function(XMLHttpRequest, textStatus, errorThrown) { 
                ShowAlret("Status: " + textStatus+ " ,Contact Mediaworks for technical support!"); 
            }
        });
    }
    
    function updateTemplate(){
        if(!$('#frmTemplate').valid()){
            return false;
        }
        for ( instance in CKEDITOR.instances )
        CKEDITOR.instances[instance].updateElement();

        var data = $('#frmTemplate').serializeArray();
        $.ajax({
            type : "POST",
            data :  data,
            url  : '<?php echo site_url("Tconditions/updateTemplate"); ?>',
            success : function(Odata){   
               
                var Data = $.parseJSON(Odata);
                if (Data['success']) {
                    ShowAlret(Data['msg'], 'success');
                } else {
                    $('#errordiv').show();
                    $('#errorlog').html(Data['msg']);
                }
             },error: function(XMLHttpRequest, textStatus, errorThrown) { 
                ShowAlret("Status: " + textStatus+ " ,Contact Mediaworks for technical support!"); 
            }
        });
    }
   
</script>    
</body>
</html>

