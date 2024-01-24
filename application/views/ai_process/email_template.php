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
			<input name="label" type="text" id="label" value="<?php echo $emailbodys[0]->alert_title; ?>" size="50" maxlength="200" class="form-control input-sm" readonly="true" /></div>
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
			<input name="fromname" type="text" id="fromname" value="<?php echo $emailbodys[0]->fromname; ?>" size="50" maxlength="200" class="form-control input-sm" readonly="true"/></div>
			</td>
		</tr>
		<tr>
			<td ><label for="fromemail" class="b-10">From/Sender Email:</label></td>
			<td colspan="3"><div class="col-md-5">
				<input name="fromemail" type="text" id="fromemail" value="<?php echo $emailbodys[0]->fromemail; ?>" size="50" maxlength="200" class="form-control input-sm" readonly="true"/></div>
			</td>
		</tr>
		<tr class="trclass">
			<td colspan="4">
				<div class="col-sm-9 col-sm-offset-3">
					<div align="left">
						<input type="hidden" name="alert_name" id="alert_name" value="<?php echo $emailbodys[0]->alert_name; ?>" />
						<input type="button" name="submit" value="Update" class="btn btn-sm btn-primary btn-orange" onclick="update()">                                                 
					</div>
				</div>
			</td>
		</tr>						
	</table> 
	<?php } ?>										 
</form>
<script src="<?php echo base_url();?>assets/global/plugins/cke-editor/ckeditor.js"></script>
<script src="<?php echo base_url();?>assets/global/plugins/cke-editor/adapters/jquery.js"></script>
<!-- <script src="<?php echo base_url();?>assets/global/plugins/editor.js"></script>-->
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
	CKEDITOR.config.autoParagraph = false;
	function update() { 
		for (instance in CKEDITOR.instances) {
			CKEDITOR.instances[instance].updateElement();
		}
        var Base_Url = '<?php echo base_url(); ?>';
        $.ajax({
            type: "POST",
            url: '<?= base_url() ?>ai_process/update_template/',
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