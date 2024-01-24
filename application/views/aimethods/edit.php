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
                                    <span>AI Methods</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Edit AI Methods</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>aimethods" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Edit AI Methods
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body"> 
                                        <form id="frmAIMethod" name="frmAIMethod" method="POST" > 
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
                                                    <div id="errordiv" class="alert alert-danger display-hide">
                                                        <button class="close" data-close="alert"></button>
                                                        You have some form errors. Please check below.
                                                        <br><span id="errorlog"></span>
                                                    </div> 
                                                    <div class="row">
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>AI Method<span class="required"> * </span></label>
                                                                <input type="text" name="description" id="description" maxlength="255" class="form-control input-sm" value="<?php echo $result->description ?>">   
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Status<span class="required"> * </span></label>
                                                                <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                    <option value="1" <?php echo ($result->status==1)?'selected':'';?>>Active</option>
                                                                    <option value="0" <?php echo ($result->status==0)?'selected':'';?>>In-Active</option>
                                                                </select>                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                                           
                                            </div>                                            
                                            <div class="row">      
                                                <div class="col-md-12 text-right">  
                                                    <button type="button" id="aimethods-submit" name="aimethods-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="update_parameter();">
                                                        <span class="ladda-label">Update</span>
                                                    </button>
                                                    <a href="<?php echo site_url("aimethods"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
        <script>         
		var base_url           = "<?php echo $base_url; ?>";
        var frmAIMethod  = $('#frmAIMethod');            
        var form_error   = $('.alert-danger', frmAIMethod);
        var form_success = $('.alert-success', frmAIMethod);
        jQuery(document).ready(function() {   
            frmAIMethod.validate({
                errorElement: 'span',
                errorClass: 'help-block help-block-error',
                focusInvalid: false,
                ignore: "",
                rules: {
                    company_id: {
                        required: true
                    },
					description: {
                        required: true,
                        aimethodsCheck: true
                    },
                    status: {
                        required: true
                    }                    
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
                    Ladda.bind('button[id=aimethods-submit]');
                    form.submit();
                }
            });
            jQuery.validator.addMethod("aimethodsCheck", function (value, element) {
                var isSuccess = false;                                
                $.ajax({
                    type: "POST",
                    data: {description: value, aimethod_id:<?php echo $result->id ?>},
                    url: "<?php echo base_url(); ?>aimethods/Check_aimethods",
                    async: false,
                    success: function (msg) {
                        isSuccess = msg != "" ? false : true;
                    }
                });
                return isSuccess;
            }
            , "AI Method already exists!!!");
        });   
        function update_parameter() {
            if (!$('#frmAIMethod').valid()) {
                return false;
            }                        
            $.ajax({
                type: "POST",
                url: '<?php echo base_url(); ?>aimethods/update/<?php echo base64_encode($result->id) ?>',
                data: $('#frmAIMethod').serialize(),
                beforeSend: function () {
                    customBlockUI();
                },
                success: function (Odata) {
                    var Data = $.parseJSON(Odata);
                    if (Data['success']) {
                        ShowAlret(Data['Msg'], 'success'); 
						window.location.href = base_url + 'aimethods';                                               
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
