<?php
defined('BASEPATH') or exit('No direct script access allowed');
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
    <style>
        #que_ans_datatable_paginate {
            float: right;
        }

        #que_ans_datatable_filter {
            float: right;
        }

        #myProgress {
            width: 100%;
            background-color: #f5f5f5;
            display: none;
            height: 1.5rem;
        }

        #myBar {
            width: 1%;
            height: inherit;
            background-color: #db1f48;
            border-radius: 1.25rem !important;
        }

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
                                <span>Upload Script</span>
                                <i class="fa fa-circle"></i>
                            </li>
                            <li>
                                <span>Edit Script</span>
                            </li>
                        </ul>
                        <div class="page-toolbar">
                            <a href="<?php echo $base_url ?>upload_script" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                        Edit Script
                                        <div class="tools"> </div>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <form id="frmScriptData" name="frmScriptData" method="POST">
                                        <input type="hidden" id="company_id" name="company_id" value="<?php echo $Company_id; ?>">
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
                                                            <label>Title of Script<span class="required"> * </span></label>
                                                            <input type="text" name="script_title" id="script_title" maxlength="255" class="form-control input-sm" value="<?php echo $script_title; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Question Limit</label>
                                                            <input type="text" name="question_limit" id="question_limit" maxlength="255" value="<?php echo $question_limit; ?>" class="form-control input-sm">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" id="text_area">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Add Script <span class="required"> * </span></label>
                                                            <textarea type="text" name="script" id="script" cols="3" rows="3" class="form-control input-sm"><?php echo $script; ?>
                                                            </textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Add Situation<span class="required"> *<p style="position: relative;font-size: 12px;top: -20px;left: 3%;">(It will be visible for rep at starting of conversation)</p></span></label>
                                                            <textarea type="text" name="situation" id="situation" cols="3" rows="3" class="form-control input-sm"><?php echo $situation; ?>
                                                            </textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 text-right">
                                                <button type="button" id="script-submit" name="script-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="update_script()">
                                                    <span class="ladda-label">Update</span>
                                                    <!-- <span class="ladda-label">Generate question and answer</span> -->
                                                </button>
                                                <a href="<?php echo site_url("upload_script"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    Generated Questions: <span id="qno">0</span> out of <?= $question_limit ?>
                    <div id="myProgress" style="border-radius: 1.25rem !important;">
                        <div id="myBar"></div>
                    </div>

                    <!-- Data table load -->
                    <div class="row margin-top-10">
                        <div class="col-lg-12" id="question_ans_table"> </div>
                    </div>
                    <div class="modal fade" id="LoadModalFilter-view" role="basic" aria-hidden="true" data-width="400">
                        <div class="modal-dialog modal-lg" style="width:1024px;">
                            <div class="modal-content">
                                <div class="modal-body" id="modal-body">
                                    <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                                    <span>
                                        &nbsp;&nbsp;Loading... </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Data Table Load -->
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('inc/inc_footer_script'); ?>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/ckeditor.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>

    <script>
        var base_url = "<?php echo $base_url; ?>";
        var frmScriptData = $('#frmScriptData');
        var form_error = $('.alert-danger', frmScriptData);
        var form_success = $('.alert-success', frmScriptData);
        jQuery(document).ready(function() {

            checkProgress($("#question_limit").val(),<?php echo $script_id; ?>);
            // DatatableRefresh(<?php echo $script_id; ?>);

            CKEDITOR.replace('script', {
                toolbar: [{
                        name: 'styles',
                        items: ['Styles', 'Format']
                    },
                    {
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat']
                    },
                    {
                        name: 'paragraph',
                        items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
                    },
                    {
                        name: 'links',
                        items: ['Link', 'Unlink', 'Anchor']
                    }
                ],
            });
            CKEDITOR.replace('situation', {
                toolbar: [{
                        name: 'styles',
                        items: ['Styles', 'Format']
                    },
                    {
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat']
                    },
                    {
                        name: 'paragraph',
                        items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
                    },
                    {
                        name: 'links',
                        items: ['Link', 'Unlink', 'Anchor']
                    }
                ],
            });
            CKEDITOR.config.autoParagraph = false;
            frmScriptData.validate({
                errorElement: 'span',
                errorClass: 'help-block help-block-error',
                focusInvalid: false,
                ignore: "",
                rules: {
                    script_title: {
                        required: true,
                        script_titleCheck: true
                    },
                    script: {
                        required: true
                    },
                    situation: {
                        required: true
                    }
                },
                invalidHandler: function(event, validator) {
                    form_success.hide();
                    form_error.show();
                    App.scrollTo(form_error, -200);
                },
                errorPlacement: function(error, element) { // render error placement for each input type
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
                    Ladda.bind('button[id=script-submit]');
                    form.submit();
                }
            });
            jQuery.validator.addMethod("script_titleCheck", function(value, element) {
                var isSuccess = false;
                $.ajax({
                    type: "POST",
                    data: {
                        script_title: value,
                        script_id: <?php echo $script_id; ?>
                    },
                    url: "<?php echo base_url(); ?>upload_script/Check_script_title",
                    async: false,
                    success: function(msg) {
                        isSuccess = msg != "" ? false : true;
                    }
                });
                return isSuccess;
            }, "Script Title already exists!!!");

        });

        function update_script() {
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            if (!$('#frmScriptData').valid()) {
                return false;
            }
            $.ajax({
                type: "POST",
                url: '<?php echo base_url(); ?>upload_script/update/<?php echo base64_encode($script_id) ?>',
                data: $('#frmScriptData').serialize(),
                beforeSend: function() {
                    customBlockUI();
                },
                success: function(Odata) {
                    var Data = $.parseJSON(Odata);
                    if (Data['success']) {
                        ShowAlret(Data['Msg'], 'success');
                        $("#script_title").prop('disabled', true);
                        $("#question_limit").prop('disabled', true);
                        CKEDITOR.instances['script'].setReadOnly(true);
                        CKEDITOR.instances['situation'].setReadOnly(true);
                        DatatableRefresh(<?php echo $script_id; ?>);
                        // window.location.href = base_url + 'upload_script';
                    } else {
                        $('#errordiv').show();
                        $('#errorlog').html(Data['Msg']);
                        App.scrollTo(form_error, -200);
                    }
                    customunBlockUI();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    ShowAlret("Status: " + textStatus + " ,Contact Awarathon for technical support!");
                }
            });
        }

        function checkProgress(total, script_id){
            $('#myProgress').show();
            var i = 0;
            if (i == 0) {
                i = 1;
                var elem = document.getElementById("myBar");
                var width = 1;
                var no = 0;
                var id = setInterval(frame, 10000);
                // console.log('total='+total);
                function frame() {
                    if (Number(no) >= Number(total)) {
                        // console.log('in if'+no+' '+total);
                        clearInterval(id);
                        i = 0;
                        DatatableRefresh(script_id);
                    } else {
                        $.ajax({
                            type: "POST",
                            url: '<?php echo base_url(); ?>upload_script/check_qna_progress/'+total+'/'+script_id,
                            data: $('#frmScriptData').serialize(),
                            success: function(Odata) {
                                no = Odata;
                                $('#qno').text(no);
                                width = (no*100)/total;
                                elem.style.width = width + "%";
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                ShowAlret("Status: " + textStatus + " ,Contact Awarathon for technical support!");
                            }
                        });
                    }
                }
            }
        }

        function DatatableRefresh(script_id) {
            var _company_id = $("#company_id").val();
            if (script_id == "" || _company_id == "") {
                ShowAlret("Please add Script", 'error');
            } else {
                var form_data = new FormData();
                form_data.append('company_id', _company_id);
                form_data.append('script_id', script_id);
                form_data.append('type', 'edit');
                $.ajax({
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    url: base_url + "/upload_script/fetch_question_answer",
                    data: form_data,
                    beforeSend: function() {
                        customBlockUI();
                    },
                    success: function(Odata) {
                        var json = $.parseJSON(Odata);
                        if (json.success == "true") {
                            $('#question_ans_table').show();
                            $('#question_ans_table').html(json['html']);
                            $('#que_ans_datatable').DataTable({
                                destroy: true,
                                "language": {
                                    "aria": {
                                        "sortAscending": ": activate to sort column ascending",
                                        "sortDescending": ": activate to sort column descending"
                                    },
                                    "emptyTable": "No data available in table",
                                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                                    "infoEmpty": "No records found",
                                    "infoFiltered": "(filtered1 from _MAX_ total records)",
                                    "lengthMenu": "Show _MENU_",
                                    "search": "Search:",
                                    "zeroRecords": "No matching records found",
                                    "paginate": {
                                        "previous": "Prev",
                                        "next": "Next",
                                        "last": "Last",
                                        "first": "First"
                                    }
                                },
                                "bStateSave": false,
                                "lengthMenu": [
                                    [5, 10, 15, 20, -1],
                                    [5, 10, 15, 20, "All"]
                                ],
                                "pageLength": 10,
                                "pagingType": "bootstrap_full_number",
                                "processing": true,
                                //"serverSide": true,
                                "columnDefs": [{
                                        'width': '3%',
                                        'orderable': true,
                                        'searchable': true,
                                        'targets': [0]
                                    },
                                    {
                                        'width': '23%',
                                        'orderable': true,
                                        'searchable': true,
                                        'targets': [1]
                                    },
                                    {
                                        'width': '23%',
                                        'orderable': false,
                                        'searchable': false,
                                        'targets': [2]
                                    },
                                    {
                                        'width': '5%',
                                        'orderable': false,
                                        'searchable': false,
                                        'targets': [3]
                                    },

                                ],
                                "order": [
                                    [0, "desc"]
                                ],
                            });
                            customunBlockUI();
                        }
                        customunBlockUI();
                    },
                    error: function(e) {
                        customunBlockUI();
                    }
                });
            }
        }
    </script>
</body>

</html>