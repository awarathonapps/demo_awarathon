<div class="modal-header">
    <button type="button" class="close" onclick="resetDATA();" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo ($AddEdit == 'E' ? 'Edit' : 'Create'); ?> Workshop Sub-Region</h4>
</div>
<form id="frmModalForm" name="frmModalForm">
    <div class="modal-body">

<!--        <div class="alert alert-danger  display-hide" id="modalerrordiv">
            <button class="close" data-close="alert"></button>
            <span id="modalerrorlog"></span>
        </div>-->
        <?php if ($Company_id == "") { ?>
            <div class="row">    
                <div class="col-md-6">       
                    <div class="form-group">
                        <label class="">Company Name<span class="required"> * </span></label>
                        <select id="company_id" name="company_id" class="form-control input-sm select2me" placeholder="Please select" style="width:100%" onchange="getComapnywiseWorkshopRegion();">
                            <option value="">Please Select </option>
                            <?php
                            if (count($CompnayResultSet) > 0) {
                                foreach ($CompnayResultSet as $key => $value) {
                                    ?>
                                    <option value="<?php echo $value->id ?>" <?php echo ($AddEdit == 'E' && $value->id == $result[0]['company_id'] ? 'selected' : '' ) ?>><?php echo $value->company_name ?> </option>

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
            <div class="col-md-6">       
                <div class="form-group">
                    <label class="">Region<span class="required"> * </span></label>
                    <select id="region_id" name="region_id" class="form-control input-sm select2me" placeholder="Please select" style="width:100%">
                        <option value="">Please Select</option>
                        <?php
                        if (count($Region) > 0) {
                            foreach ($Region as $Row) {
                                ?>
                                <option value="<?php echo $Row->id ?>" <?php echo ($AddEdit == 'E' && $Row->id == $result[0]['region_id'] ? 'selected' : '' ) ?>><?php echo $Row->region_name ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">    
            <div class="col-md-9">       
                <div class="form-group">
                    <label class="">Workshop Sub-Region<span class="required"> * </span></label>
                    <input type="text" name="description" id="description" maxlength="250" class="form-control input-sm" autocomplete="off" value="<?php echo ($AddEdit == 'E' ? $result[0]['description'] : '' ) ?>">  
                    <input type="hidden" name="edit_id" id="edit_id" class="form-control input-sm" autocomplete="off" value="">                                                                
                </div>
            </div>
            <div class="col-md-3">    
                <div class="form-group last">
                    <label>Status</label>
                    <select id="status" name="status" class="form-control input-sm " placeholder="Please select" >
                        <option value="1" <?php echo ($AddEdit == 'E' && $result[0]['status']==1 ? 'selected' : '' ) ?>>Active</option>
                        <option value="0" <?php echo ($AddEdit == 'E' && $result[0]['status']==0 ? 'selected' : '' ) ?>>In-Active</option>
                    </select>
                </div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <div class="col-md-12 text-right ">  
            <button type="submit" id="modal-create-submit" name="modal-create-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" >
                <span class="ladda-label">Submit</span>
            </button>
            <button type="button" id="CloseModalBtn" data-dismiss="modal" class="btn btn-default btn-cons">Cancel</button>
        </div>
    </div>
</form>
<script>
    var frmModalForm = $('#frmModalForm');
    var form_error = $('.alert-danger', frmModalForm);
    var form_success = $('.alert-success', frmModalForm);
    var edit_id = "<?php echo ($AddEdit == 'E' ? base64_encode($result[0]['id']) : '') ?>";
    var mcs = Ladda.create(document.querySelector('#modal-create-submit'));
    jQuery.validator.addMethod("subregionCheck", function (value, element) {
        var isSuccess = false;
        $.ajax({
            type: "POST",
            data: {wsubregion: value, company_id: $('#company_id').val(), region_id: $('#region_id').val(), $wsubregion_id: edit_id},
            url: "<?php echo base_url(); ?>workshopsubregion/Check_workshopsubregion",
            async: false,
            success: function (msg) {
                isSuccess = msg != "" ? false : true;
            }
        });
        return isSuccess;
    }
    , "Workshop Sub-Region already exists!!!");

    $('.select2me').select2();

    frmModalForm.validate({
        errorElement: 'span',
        errorClass: 'help-block help-block-error',
        focusInvalid: false,
        ignore: "",
        rules: {
            description: {
                required: true,
                subregionCheck: true,
            },
            company_id: {
                required: true
            },
            region_id: {
                required: true
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
        errorPlacement: function (error, element) {
            if (element.hasClass('form-group')) {
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
            mcs.start();
            form_success.show();
            form_error.hide();
            $.post('<?php echo base_url(); ?>index.php/workshopsubregion/submit/'+edit_id, $("#frmModalForm").serialize(), function (data) {
                if (data.success) {
                        if(edit_id==""){
                            $('#description').val("");
                            $('#description').focus();
                        }
                        ShowAlret(data.message,"success");
                        DatatableRefresh();
                    }else{
                        ShowAlret(data.message, "error");
                    }
                mcs.stop();
            }, "json");
        },
        messages: {
            description: {
                required: "This field is required",
                remote: "This description already exists. Please try another description.",
            },
            status: "This field is required."
        }
    });

    $(".select2me, .select2-multiple", frmModalForm).change(function () {
        frmModalForm.validate().element($(this));
    });
</script>