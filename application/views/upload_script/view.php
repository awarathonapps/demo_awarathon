<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
<!DOCTYPE html>
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
                                <span>View Script</span>
                            </li>
                        </ul>
                        <div class="page-toolbar">
                            <a href="<?php echo $base_url ?>upload_script" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-12">
                            <div class="portlet light bordered">
                                <div class="portlet-title">
                                    <div class="caption caption-font-24">
                                        View Script
                                        <div class="tools"> </div>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <form id="frmScriptData" name="frmScriptData" method="POST">
                                        <input type="hidden" id="script_id" name="script_id" value="<?php echo $script_id; ?>">
                                        <input type="hidden" id="company_id" name="company_id" value="<?php echo $Company_id; ?>">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_overview">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Title of Script<span class="required"> * </span></label>
                                                            <input type="text" name="script_title" id="script_title" maxlength="255" class="form-control input-sm" value="<?php echo $script_title; ?>" disabled=''>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Question Limit</label>
                                                            <input type="text" name="question_limit" id="question_limit" maxlength="255" class="form-control input-sm" value="<?php echo $question_limit; ?>" disabled=''>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" id="text_area">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Add Script <span class="required"> * </span></label>
                                                            <textarea type="text" name="script" id="script" cols="3" rows="3" class="form-control input-sm" disabled=''><?php echo $script; ?>
                                                            </textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Add Situation<span class="required"> *<p style="position: relative;font-size: 12px;top: -20px;left: 3%;">(It will be visible for rep at starting of conversation)</p></span></label>
                                                            <textarea type="text" name="situation" id="situation" cols="3" rows="3" class="form-control input-sm" disabled=""><?php echo $situation; ?>
                                                            </textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 text-right">
                                                <a href="<?php echo site_url("upload_script"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Data table load -->
                    <div class="row margin-top-10">
                        <div class="col-lg-12" id="question_ans_table"> </div>
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
        jQuery(document).ready(function() {
            DatatableRefresh($("#script_id").val());

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
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
        });


        function DatatableRefresh(script_id) {
            var _company_id = $("#company_id").val();
            if (script_id == "" || _company_id == "") {
                ShowAlret("Please add Script", 'error');
            } else {
                var form_data = new FormData();
                form_data.append('company_id', _company_id);
                form_data.append('script_id', script_id);
                form_data.append('type', 'view');
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
                                    [2, "desc"]
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