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
                                    <span>Email Template</span>                                    
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
                                    
                                    <div class="portlet-body">                                                                                            
                                        <div class="tab-content">
                                            <div class="row">      
                                                <div class="col-md-12">
                                                    <div class="panel panel-default">                                                    
                                                    <div class="panel-heading" style="padding: 1px -1px 12px 14px;background-color: #f36a40">
                                                        <h3 class="panel-title"><strong>Manage </strong>Email Template</h3>
                                                        
                                                    </div>
                                                    <div class="panel-body">                                                        
                                                        <div class="col-md-12 col-sm-12 col-xs-12">                                                            
                                                            <div class="col-md-12">
                                                                <form id="form1" name="form1" method="post" action="<?php echo base_url();?>emailtemplate/getemailbody" enctype="multipart/form-data" >
                                                                <table class="table table-bordered" width="100%" style="background-color: #F7F7F7;">
                                                                <?php if (empty($emailbodys)) { ?>
                                                                <tr>
                                                                    <td><label for="alertemail" class="b-10" id="alertemail">Please select an email to edit :</label></td>
                                                                    <td colspan="3">
                                                                        <div class="col-md-8">
                                                                            <select name="alert_name" class='form-control' onchange="form1.submit();">
                                                                                <option value="">Please Select</option>
                                                                                <?php foreach ($emailtemplates as $data) { ?>
                                                                                    <option value="<?php echo $data->alert_name;?>"><?php echo $data->alert_title; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php } else { ?>
                                                                <tr>
                                                                    <td><label for="alertemail" class="b-10" >Editing Email :</label></td>
                                                                    <td colspan="3">
                                                                        <div class="col-md-8">
                                                                            <select name="alert_name" class='form-control' onchange="form1.submit();">
                                                                                <option value="">Please Select</option>
                                                                                <?php foreach ($emailtemplates as $data) { ?>
                                                                                    <option value="<?php echo $data->alert_name;?>" <?php if($emailbodys[0]->alert_name == $data->alert_name) {echo "selected='selected'";} ?> ><?php echo $data->alert_title; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php } ?>
                                                                </table>
                                                                </form>

                                                                <form id="addform" name="addform" method="post" action="<?php echo base_url();?>emailtemplate/update" enctype="multipart/form-data" >
                                                                    <?php if (!empty($emailbodys) ){ ?>
                                                                <table class="table table-bordered" width="100%" style="background-color: #F7F7F7;">
                                                                        <tr>
                                                                            <td colspan="3"><div class="col-md-12">
                                                                                <?php echo $emailbodys[0]->description; ?></div>
                                                                            </td>
                                                                        </tr>
                                                                    <tr>
                                                                        <td width="21%" ><label for="Subject" class="b-10">Email Label :<span class="required">*</span></label></td>
                                                                        <td width="79%" colspan="3"><div class="col-md-5">
                                                                        <input name="label" type="text" id="label" value="<?php echo $emailbodys[0]->alert_title; ?>" size="50" maxlength="200" class="form-control input-sm"/></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="21%" ><label for="Subject" class="b-10">Subject :<span class="required">*</span></label></td>
                                                                        <td width="79%" colspan="3"><div class="col-md-5">
                                                                            <input name="subject" type="text" id="subject" value="<?php echo $emailbodys[0]->subject; ?>" size="50" maxlength="200" class="form-control input-sm"/></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td ><label for="ingredient" class="b-10">Message:<span class="required">*</span></label></td>
                                                                        <td colspan="3">
                                                                            <textarea cols="80" id="editor1" name="message" rows="10" class="cke-editor"><?php echo $emailbodys[0]->message; ?>
                                                                            </textarea>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><label for="fromname" class="b-10">From/Sender name:</label></td>
                                                                        <td colspan="3"><div class="col-md-5">
                                                                        <input name="fromname" type="text" id="fromname" value="<?php echo $emailbodys[0]->fromname; ?>" size="50" maxlength="200" class="form-control input-sm"/></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td ><label for="fromemail" class="b-10">From/Sender Email:</label></td>
                                                                        <td colspan="3"><div class="col-md-5">
                                                                            <input name="fromemail" type="text" id="fromemail" value="<?php echo $emailbodys[0]->fromemail; ?>" size="50" maxlength="200" class="form-control input-sm"/></div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="trclass">
                                                                        <td colspan="4">
                                                                            <div class="col-sm-9 col-sm-offset-3">
                                                                                <div align="left">
                                                                                    <input type="hidden" name="alert_name" id="alert_name" value="<?php echo $emailbodys[0]->alert_name; ?>" />
                                                                                    <input type="button" name="submit" value="Update" class="btn btn-sm btn-primary btn-orange" <?php echo (!$accessRights->allow_edit ? 'disabled':'')?> onclick="update()">                                                 
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>						
                                                                </table> 
                                                                <?php } ?>										 
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>                
<?php $this->load->view('inc/inc_footer_script'); ?>
   <script src="<?php echo base_url();?>assets/global/plugins/cke-editor/ckeditor.js"></script>
   <script src="<?php echo base_url();?>assets/global/plugins/cke-editor/adapters/jquery.js"></script>
   <script src="<?php echo base_url();?>assets/global/plugins/editor.js"></script>
   <script type="text/javascript">
	CKEDITOR.replace( 'editor1',
            {
                toolbar :
		[
			{ name: 'styles', items : [ 'Styles','Format' ] },
                        { name: 'basicstyles', items : [ 'Bold','Italic','Strike','-','RemoveFormat' ] },
                        { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote' ] },
                        { name: 'links', items : [ 'Link','Unlink','Anchor' ] }
		],
            });
			CKEDITOR.replace( 'description',
            {
                toolbar :
		[
			{ name: 'styles', items : [ 'Styles','Format' ] },
                        { name: 'basicstyles', items : [ 'Bold','Italic','Strike','-','RemoveFormat' ] },
                        { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote' ] },
                        { name: 'links', items : [ 'Link','Unlink','Anchor' ] }
		],
            });
			
            CKEDITOR.config.autoParagraph = false;
    function update() {        
        var alert_name = $('#alert_name').val();
		for (instance in CKEDITOR.instances) {
			CKEDITOR.instances[instance].updateElement();
		}
        var Base_Url = '<?php echo base_url(); ?>';
        $.ajax({
            type: "POST",
            url: Base_Url+"emailtemplate/update/"+alert_name,
            data: $('#addform').serialize(),
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
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                ShowAlret("Status: " + textStatus + " ,Contact Atomapp for technical support!");
            }
        });
    }
    
   </script>
</body>
</html>