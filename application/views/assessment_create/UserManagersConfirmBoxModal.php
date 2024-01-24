
<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Please Select Mapping Manager</h4>
</div>
<div class="modal-body">
    <form name="UserManagerConfirmForm" id="UserManagerConfirmForm">
        <div class="portlet light">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12">       
                        <div class="form-group">
                            <label class="control-label col-md-4">Manager Name<span class="required">*</span></label>
                            <div class="col-md-8" style="padding:0px;">
                                <select id="trainer_id" name="trainer_id[]" class="form-control input-sm select2 " placeholder="Please select"  style="width: 100%" multiple="">
                                    <option value="">Please Select</option>
                                    <?php 
                                        if(count($Trainer)>0){
                                            foreach ($Trainer as $value) { ?>
                                                <option value="<?= $value->trainer_id; ?>"><?php echo $value->name; ?></option>
                                    <?php } } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="assessment_id" id="assessment_id" value="<?php echo $assessment_id ?>" />
                    <?php foreach ($user_id_array as $val) { ?>
                    <input type="hidden" name="user_id_array[]" id="user_id_array" value="<?php echo $val ?>" />
                    <?php } ?>
                </div>                                
            </div>
        </div>          
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-orange" onclick="RemoveMappingUserManager();">Remove</button>
</div>
<script type="text/javascript">
    var UserManagerConfirmForm=document.UserManagerConfirmForm;
    jQuery(document).ready(function () {
        $(".select2").select2({
            placeholder: 'Please Select',
            width: '100%'
        });          
    });
</script>            
