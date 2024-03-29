<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
 <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />

<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Device Users Active/Inactive</h4>
</div>
<div class="modal-body">
    <!-- <div class="page-content-wrapper">
        <div class="page-content"> -->
    <!-- <div class="row mt-10"> -->
    <!-- <div class="col-md-12"> -->
    <?php if ($this->session->flashdata('flash_message')) { ?>
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
            <?php echo $this->session->flashdata('flash_message'); ?>
        </div>
    <?php } ?>
    <div class="portlet light bordered">
        <form id="FrmDeviceUsers" name="FrmDeviceUsers" method="POST" enctype="multipart/form-data">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="">Company Name<span class="required"> * </span></label>
                                        <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
                                            <option value="">Please Select</option>
                                            <?php
                                            foreach ($CompnayResultSet as $cmp) { ?>
                                                <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Choose File<span class="required"> * </span></label>
                                    <div class="form-control fileinput fileinput-new" style="width: 100%;border: none;height:auto;" data-provides="fileinput">
                                        <div class="input-group input-large">
                                            <div class="form-control uneditable-input span3" data-trigger="fileinput">
                                                <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                </span>
                                            </div>
                                            <span class="input-group-addon btn default btn-file">
                                                <span class="fileinput-new">
                                                    Select file </span>
                                                <span class="fileinput-exists">
                                                    Change </span>
                                                <input type="file" name="filename" id="filename">
                                            </span>
                                            <a href="javascript:;" id="RemoveFile" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                Remove </a>
                                        </div>
                                    </div><br />
                                    <span class="text-muted">(only .xlsx and .xls allowed)</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <a href="<?php echo base_url() . 'device_users/import_userssamplexls' ?>" class="form-control" style="    border: none;height:auto;"><strong>Download Sample Xls File</strong></a>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h3 class="panel-title">Notes</h3>
                            </div>
                            <div class="panel-body">
                                <ul>
                                    <li>Upload Users Data through Xls file.</li>
                                    <li>xls file format must be same as sample xls format.</li>
                                    <li>Do not modify or delete the Columns of sample xls.</li>
                                    <li>In sample xls file * is mandatory Fields.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="button" id="deviceusers-submit" name="deviceusers-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="SubmitData();">
                        <span class="ladda-label"><i class="fa fa-upload"></i> Confirm</span>
                    </button>
                    <a href="<?php echo site_url("device_users"); ?>" class="btn btn-default btn-cons">Cancel</a>
                </div>
            </div>
        </form>
    </div>
    <!-- </div> -->
    <!-- </div> -->
    <!-- </div>
    </div> -->
</div>
<script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script>
    var FrmDeviceUsers = $('#FrmDeviceUsers');
    var form_error = $('.alert-danger', FrmDeviceUsers);
    var form_success = $('.alert-success', FrmDeviceUsers);
    jQuery(document).ready(function() {
        $('.select2me').select2({
            allowClear: true,
            placeholder: 'Please Select'
        });
        FrmDeviceUsers.validate({
            errorElement: 'span',
            errorClass: 'help-block help-block-error',
            focusInvalid: false,
            ignore: "",
            rules: {
                company_id: {
                    required: true
                },
                filename: {
                    required: true
                }
            },
            invalidHandler: function(event, validator) {
                form_success.hide();
                form_error.show();
                App.scrollTo(form_error, -200);
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
                Ladda.bind('button[id=deviceusers-submit]');
                form.submit();
            }
        });
        $(".select2, .select2-multiple", FrmDeviceUsers).change(function() {
            FrmDeviceUsers.validate().element($(this));
        });
    });

    function SubmitData() {
        $('#errordiv').hide();
        if (!$('#FrmDeviceUsers').valid()) {
            return false;
        }
        var file_data = $('#filename').prop('files')[0];
        var form_data = new FormData();
        form_data.append('filename', file_data);
        var other_data = $('#FrmDeviceUsers').serializeArray();
        $.each(other_data, function(key, input) {
            form_data.append(input.name, input.value);
        });
        $.ajax({
            cache: false,
            contentType: false,
            processData: false,
            type: "POST",
            url: '<?php echo site_url("device_users/UploadXls_user"); ?>',
            data: form_data,
            success: function(Odata) {
                //alert(result);
                var Data = $.parseJSON(Odata);
                $("#filename").replaceWith($("#filename").val("").clone(true));
                if (Data['success']) {
                    ShowAlret(Data['Msg'], 'success');
                    setTimeout(function() { // wait for 5 secs(2)
                        location.reload(); // then reload the page.(3)
                    }, 1000);
                } else {
                    $('#errordiv').show();
                    $('#errorlog').html(Data['Msg']);
                    App.scrollTo(form_error, -200);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
            }
        });
    }
</script>