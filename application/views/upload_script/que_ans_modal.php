<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Question Answer Edit</h4>
</div>
<div class="modal-body">
    <form name="QueAnsForm" id="QueAnsForm">
        <!-- <input type="hidden" name="QAid" id="QAid" value="<?//php echo $QAid; ?>" > -->
        <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>" >
        <input type="hidden" name="script_id" id="script_id" value="<?php echo $script_id; ?>" >
        <?php
        $errors = validation_errors();
        if ($errors) { ?>
            <div style="display: block;" class="alert alert-danger display-hide">
                <button class="close" data-close="alert"></button>
                You have some form errors. Please check below.
                <?php echo $errors; ?>
            </div>
        <?php } ?>
        <div class="portlet light">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="">Question<span class="required"> * </span></label>
                            <textarea type="text" name="question" id="question" cols="3" rows="4" class="form-control input-sm"><?php echo $question; ?>
                            </textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="">Answer<span class="required"> * </span></label>
                            <textarea type="text" name="answer" id="answer" cols="3" rows="4" class="form-control input-sm"><?php echo $answer; ?>
                            </textarea>
                        </div>
                    </div>
                    <div style="float:right;margin-right: 15px;">
                        <button type="button" class="btn btn-orange" id="qa_submit" onclick="update_que_ans();">Update Question Answer</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    var base_url = "<?php echo $base_url; ?>";
    var QueAnsForm = $('#QueAnsForm');
    var form_error = $('.alert-danger', QueAnsForm);
    var form_success = $('.alert-success', QueAnsForm);
    jQuery(document).ready(function() {
        QueAnsForm.validate({
            errorElement: 'span',
            errorClass: 'help-block help-block-error',
            focusInvalid: false,
            ignore: "",
            rules: {
                question: {
                    required: true
                },
                answer: {
                    required: true
                }
            },
            invalidHandler: function(event, validator) {
                form_success.hide();
                form_error.show();
                App.scrollTo(form_error, -200);
            },
            errorPlacement: function(error, element) {
                if (element.hasClass('.form-group')) {
                    error.appendTo(element.parent().find('.has-error'));
                } else if (element.parent('.form-group').length) {
                    error.appendTo(element.parent());
                } else {
                    error.appendTo(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
            },
            submitHandler: function(form) {
                form_success.show();
                form_error.hide();
                Ladda.bind('button[id=qa_submit]');
                form.submit();
            }
        });
    });

    function update_que_ans() {
        console.log($('#QueAnsForm').serialize());
        if (!$('#QueAnsForm').valid()) {
            return false;
        }
        $.ajax({
            type: "POST",
            url: '<?php echo base_url(); ?>upload_script/update_que_ans/<?php echo base64_encode($QAid) ?>',
            data: $('#QueAnsForm').serialize(),
            beforeSend: function() {
                customBlockUI();
            },
            success: function(Odata) {
                var Data = $.parseJSON(Odata);
                if (Data['success']) {
                    ShowAlret(Data['Msg'], 'success');
                    DatatableRefresh($('#script_id').val())
                    $('#LoadModalFilter-view').modal('toggle');
                } else {
                    $('#errordiv').show();
                    $('#errorlog').html(Data['Msg']);
                    App.scrollTo(form_error, -200);
                }
                customunBlockUI();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
            }
        });
    }
</script>