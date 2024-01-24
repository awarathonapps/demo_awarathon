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
    <!--datattable CSS  Start-->
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css"
        rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css"
        rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css"
        rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css"
        rel="stylesheet" type="text/css" />
    <!--datattable CSS  End-->
    <?php $this->load->view('inc/inc_htmlhead'); ?>
    <style>
        .dashboard-stat.aiboxes {
            color: #db1f48;
            background-color: #e8e8e8;
        }

        .dashboard-stat.aiboxes .more {
            color: #db1f48;
            background-color: #004369;
            opacity: 1;
        }

        .dashboard-stat.aiboxes .more:hover {
            opacity: 1;
        }

        .dashboard-stat .details .number {
            padding-top: 10px !important;
            font-size: 24px;
            font-weight: 600;
        }

        .dashboard-stat .visual {
            height: 60px;
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
                                <span>Application</span>
                                <i class="fa fa-circle"></i>
                            </li>
                            <li>
                                <span>Device Users</span>
                                <i class="fa fa-circle"></i>
                            </li>
                        </ul>
                        <div class="col-md-1 page-breadcrumb"></div>
                        <div class="page-toolbar">
                            <div id="dashboard-report-range" name="daterange" class="pull-right tooltips btn btn-sm"
                                data-container="body" data-placement="bottom">
                                <i class="icon-calendar"></i>&nbsp;
                                <span class="thin uppercase hidden-xs"></span>&nbsp;
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row margin-top-15">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_i_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                        Total Users
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_ii_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                        Total Active Users
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_iii_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                        Total Inactive Users
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                    </div>
                    <form id="deviceUsers" name="deviceUsers"
                        action="<?php echo base_url() . 'device_users/export_trainee/' ?>" method="post">
                        <input type="hidden" name="export_type" id="export_type" value="1">
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="panel-group accordion" id="accordion3">
                                    <div class="panel panel-default">

                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled collapsed"
                                                    data-toggle="collapse" data-parent="#accordion3"
                                                    href="#collapse_3_2">
                                                    Advanced Search </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_3_2" class="panel-collapse collapse">
                                            <div class="panel-body">
                                                <div class="row margin-bottom-10">
                                                    <?php if ($Company_id == "") { ?>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Company&nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="company_id" name="company_id"
                                                                        class="form-control input-sm select2"
                                                                        placeholder="Please select" style="width: 100%">
                                                                        <option value="">All Company</option>
                                                                        <?php
                                                                        foreach ($CompnayResultSet as $cmp) { ?>
                                                                            <option value="<?= $cmp->id; ?>">
                                                                                <?php echo $cmp->company_name; ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-4">Registration
                                                                Date</label>
                                                            <div class="col-md-8 input-group input-large date-picker input-daterange"
                                                                data-date="" data-date-format="dd-mm-yyyy">
                                                                <input type="text" class="form-control input-sm"
                                                                    id="start_date" name="start_date" value="">
                                                                <span class="input-group-addon"> to </span>
                                                                <input type="text" class="form-control input-sm"
                                                                    id="end_date" name="end_date" value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row ">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Status&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="status" name="status"
                                                                    class="form-control input-sm select2"
                                                                    placeholder="Please select" style="width: 100%">
                                                                    <option value="">All
                                                                    <option>
                                                                    <option value="1">Active</option>
                                                                    <option value="0">In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix margin-top-20"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm"
                                                                onclick="DatatableRefresh()">Search</button>
                                                            <button type="button" class="btn blue-hoki btn-sm"
                                                                onclick="ResetFilter()">Reset</button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-10">

                            <div class="col-md-12">
                                <?php if ($this->session->flashdata('flash_message')) { ?>
                                    <div class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true"></button>
                                        <?php echo $this->session->flashdata('flash_message'); ?>
                                    </div>
                                <?php } ?>
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            Manage Device Users
                                            <div class="tools"> </div>
                                        </div>
                                        <div class="actions">
                                            <div class="btn-group pull-right">
                                                <button type="button" id="send_otp" name="send_otp"
                                                    data-loading-text="Please wait..."
                                                    class="btn orange btn-sm btn-outline" data-style="expand-right"
                                                    onclick="SendOTP(1);" style="    margin-right: 10px;">
                                                    <span class="ladda-label"><i class="fa fa-envelope"></i>&nbsp; Send
                                                        OTP</span>
                                                </button>&nbsp;
                                                <button type="button" id="send-opt" name="send-opt"
                                                    data-loading-text="Please wait..."
                                                    class="btn orange btn-sm btn-outline" data-style="expand-right"
                                                    onclick="SendOTP(2);" style="    margin-right: 10px;">
                                                    <span class="ladda-label"><i class="fa fa-envelope"></i>&nbsp; Send
                                                        OTC</span>
                                                </button>&nbsp;
                                                <a href="<?php echo site_url("device_users/import"); ?>"
                                                    class="btn orange btn-sm btn-outline" style="margin-right: 10px;"><i
                                                        class="fa fa-file-excel-o"></i> Import</a>
                                                <button type="button"
                                                    class="btn orange btn-sm btn-outline dropdown-toggle"
                                                    data-toggle="dropdown">Bulk Actions
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu pull-right" role="menu">
                                                    <?php if ($acces_management->allow_edit) { ?>
                                                        <li>
                                                            <a id="bulk_active" href="javascript:;">
                                                                <i class="fa fa-check"></i> Active
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a id="bulk_inactive" href="javascript:;">
                                                                <i class="fa fa-close"></i> In Active
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if ($acces_management->allow_delete) { ?>
                                                        <li>
                                                            <a id="bulk_delete" href="javascript:;">
                                                                <i class="fa fa-trash-o"></i> Delete
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if ($acces_management->allow_print or $acces_management->allow_export) { ?>
                                                        <li class="divider"></li>

                                                        <?php if ($acces_management->allow_export) { ?>
                                                            <li>
                                                                <a id="bulk_excel" href="javascript:;"
                                                                    onclick="exportConfirm(1)">
                                                                    <i class="fa fa-file-excel-o"></i> Export to Excel
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if ($acces_management->allow_export) { ?>
                                                            <li>
                                                                <a id="bulk_excel" href="javascript:;"
                                                                    onclick="exportConfirm(2)">
                                                                    <i class="fa fa-file-excel-o"></i> IMEI No Export
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </ul>
                                                <?php if ($acces_management->allow_add) { ?>
                                                    <!-- <div class="page-toolbar"> -->
                                                    <a href="<?php echo $base_url ?>device_users/create"
                                                        class="btn btn-sm btn-orange pull-right">Create New</a>&nbsp;
                                                    <!-- </div> -->
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <table class="table  table-bordered table-hover table-checkable order-column"
                                            id="index_table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <label
                                                            class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                            <input type="checkbox" class="all group-checkable"
                                                                name="check" id="check"
                                                                data-set="#index_table .checkboxes" />
                                                            <span></span>
                                                        </label>
                                                    </th>
                                                    <th>ID</th>
                                                    <th>Company</th>
                                                    <th>Employee Code</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>L1 Manager</th>
                                                    <th>L2 Manager</th>
                                                    <th>Region</th>
                                                    <th>Designation</th>
                                                    <!--<th>OTP</th>														
                                                        <th>OTP Last Attempt</th>-->
                                                    <th>Registration Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
        </div>
        <?php //$this->load->view('inc/inc_footer'); ?>
    </div>
    <?php //$this->load->view('inc/inc_quick_nav'); ?>
    <div class="modal fade" id="add_newmodal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false"
        data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog  modal-lg" style="width:1050px;">
            <div class="modal-content" id="load_modeldata">

            </div>
        </div>
    </div>
    <?php $this->load->view('inc/inc_footer_script'); ?>
    <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js"
        type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
        type="text/javascript"></script>
    <script src="<?php echo $base_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js"
        type="text/javascript"></script>

    <script src="<?php echo $asset_url; ?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js"
        type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/counterup/jquery.waypoints.min.js"
        type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/counterup/jquery.counterup.min.js"
        type="text/javascript"></script>
    <script>
        // var statistics_start_date = moment();
        var statistics_start_date = moment(Date()).format("YYYY-MM-DD");
        // var statistics_end_date = moment();
        var statistics_end_date = moment(Date()).format("YYYY-MM-DD");
        var options = {};
        options.startDate = moment();
        // options.startDate           = moment(Date()).subtract(29, 'days').format("DD/MM/YYYY");
        options.endDate = moment();
        options.timePicker = false;
        options.showDropdowns = true;
        options.alwaysShowCalendars = true;
        options.autoApply = true;
        options.ranges = {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        };
        options.locale = {
            direction: 'ltr',
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Apply',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        };
        var base_url = "<?php echo $base_url; ?>";
        var deviceUsers = document.deviceUsers;
        jQuery(document).ready(function () {
            $('.all').click(function () {
                if ($(this).is(':checked')) {
                    $("input[name='id[]']").prop('checked', true);
                } else {
                    $("input[name='id[]']").prop('checked', false);
                }
            });
            if (jQuery().datepicker) {
                $('.date-picker').datepicker({
                    rtl: App.isRTL(),
                    orientation: "left",
                    autoclose: true,
                    format: 'dd-mm-yyyy'
                });
            }
            DatatableRefresh();
            DeviceUserStatistics();
        });
        function LoadConfirmDialog(content, opt) {
            $.confirm({
                title: 'Confirm!',
                content: content,
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-orange',
                        keys: ['enter', 'shift'],
                        action: function () {
                            $.ajax({
                                type: "POST",
                                data: $('#deviceUsers').serialize(),
                                url: base_url + "device_users/record_actions/" + opt,
                                success: function (response_json) {
                                    var response = JSON.parse(response_json);
                                    ShowAlret(response.message, response.alert_type);
                                    DatatableRefresh();
                                }
                            });
                        }
                    },
                    cancel: function () {
                        this.onClose();
                    }
                }
            });
        }
        function ValidCheckbox(opt) {
            var Check = getCheckCount();
            if (Check > 0) {
                if (opt == 1) {
                    LoadConfirmDialog("Confirm Active Status ?", opt);
                }
                else if (opt == 2) {
                    LoadConfirmDialog("Confirm InActive Status ?", opt);
                }
                else if (opt == 3) {
                    LoadConfirmDialog("Delete Selected User(s) ?", opt);
                }
            } else {
                ShowAlret("Please select record from the list.", 'error');
                return false;
            }
        }
        $(function () {
            $("#bulk_active").click(function () {
                ValidCheckbox(1);
            });
            $("#bulk_inactive").click(function () {
                ValidCheckbox(2);
            });
            $("#bulk_delete").click(function () {
                ValidCheckbox(3);
            });
        });
        function LoadDeleteDialog(Id) {
            $.confirm({
                title: 'Confirm!',
                content: " Are you sure you want to delete this user ? ",
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-orange',
                        keys: ['enter', 'shift'],
                        action: function () {
                            $.ajax({
                                type: "POST",
                                url: base_url + "device_users/remove",
                                data: { deleteid: Id },
                                success: function (response_json) {
                                    var response = JSON.parse(response_json);
                                    ShowAlret(response.message, response.alert_type);
                                    DatatableRefresh();
                                }
                            });
                        }
                    },
                    cancel: function () {
                        this.onClose();
                    }
                }
            });
        }
        function SendOTP(type) {
            var msg = '';
            if (type == 1) {
                msg = "are you sure you want to send OTP.?";
            } else {
                msg = "are you sure you want to send OTC.?";
            }
            if (getCheckCount() == 0) {
                ShowAlret("Please select record from the list.", 'error');
                return false;
            }
            $.confirm({
                title: 'Confirm!',
                content: msg,
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-orange',
                        keys: ['enter', 'shift'],
                        action: function () {
                            $.ajax({
                                type: "POST",
                                data: $('#deviceUsers').serialize(),
                                url: base_url + "device_users/send_otp/" + type,
                                beforeSend: function () {
                                    customBlockUI();
                                },
                                success: function (Odata) {
                                    var Data = $.parseJSON(Odata);
                                    if (Data['success']) {
                                        ShowAlret(Data['Msg'], 'success');
                                        DatatableRefresh();
                                    } else {
                                        ShowAlret(Data['Msg'], 'error');
                                    }
                                    customunBlockUI();
                                }
                            });
                        }
                    },
                    cancel: function () {
                        this.onClose();
                    }
                }
            });
        }
        function exportConfirm(module_id) {
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure you want to Export. ? ",
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function () {
                            $('#export_type').val(module_id);
                            document.deviceUsers.submit();
                        }
                    },
                    cancel: function () {
                        this.onClose();
                    }
                }
            });
        }
        function getCheckCount() {
            var x = 0;
            for (var i = 0; i < deviceUsers.elements.length; i++) {
                if (deviceUsers.elements[i].checked == true) {
                    x++;
                }
            }
            return x;
        }
        function DatatableRefresh() {
            //                if (!jQuery().dataTable) {
            //                    return;
            //                }
            var table = $('#index_table');
            table.dataTable({
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
                //dom: 'Bfrtip',
                //buttons: [
                //    { extend: 'print', className: 'btn dark btn-outline' },
                //    { extend: 'pdf', className: 'btn green btn-outline' },
                //    { extend: 'csv', className: 'btn purple btn-outline ' }
                //],
                //buttons: [
                //    'copy', 'csv', 'excel', 'pdf', 'print'
                //],
                //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [
                    { 'width': '30px', 'orderable': false, 'searchable': false, 'targets': [0] },
                    { 'width': '30px', 'orderable': true, 'searchable': true, 'targets': [1] },
                    { 'width': '200px', 'orderable': true, 'searchable': true, 'targets': [2], "visible": <?php echo ($Company_id == "" ? 'true' : 'false'); ?> },
                    { 'width': '200', 'orderable': true, 'searchable': true, 'targets': [3] },
                    { 'width': '', 'orderable': true, 'searchable': true, 'targets': [4] },
                    { 'width': '85px', 'orderable': true, 'searchable': true, 'targets': [5] },
                    { 'width': '130px', 'orderable': true, 'searchable': true, 'targets': [6] },
                    { 'width': '130px', 'orderable': true, 'searchable': true, 'targets': [7] },
                    { 'width': '65px', 'orderable': true, 'searchable': false, 'targets': [8] },
                    { 'width': '65px', 'orderable': true, 'searchable': false, 'targets': [9] },
                    { 'width': '65px', 'orderable': false, 'searchable': false, 'targets': [10] },
                    { 'width': '65px', 'orderable': false, 'searchable': false, 'targets': [11] },
                    { 'width': '65px', 'orderable': false, 'searchable': false, 'targets': [12] }
                ],
                "order": [
                    [1, "desc"]
                ],
                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'device_users/DatatableRefresh'; ?>",
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({ name: '__mode', value: 'featuredimage.ajaxload' });
                    aoData.push({ name: 'start_date', value: $('#start_date').val() });
                    aoData.push({ name: 'end_date', value: $('#end_date').val() });
                    aoData.push({ name: 'company_id', value: $('#company_id').val() });
                    aoData.push({ name: 'status', value: $('#status').val() });

                    $.getJSON(sSource, aoData, function (json) {
                        fnCallback(json);
                    });
                },
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    return nRow;
                }
                , "fnFooterCallback": function (nRow, aData) {
                }
            });
        }
        function ResetFilter() {
            $('.select2').val(null).trigger('change');
            document.deviceUsers.reset();
            DatatableRefresh();
        }
        function LoadDeviceInfo(user_id) {
            $.ajax({
                type: "POST",
                data: { user_id: user_id },
                url: '<?php echo base_url() . 'device_users/deviceinfo_data'; ?>',
                success: function (Odata) {
                    $('#load_modeldata').html(Odata);
                    $('#add_newmodal').modal('toggle');
                }
            });
        }
        function DeviceUserStatistics() {
            $.ajax({
                url: base_url + "/device_users/fetch_statistics/",
                data: {
                    'company_id': <?= $Company_id ?>,
                    'start_date': statistics_start_date,
                    'end_date': statistics_end_date
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function () { },
                success: function (json) {
                    $('#box_i_statistics').html(json.box_i_statistics);
                    $('#box_ii_statistics').html(json.box_ii_statistics);
                    $('#box_iii_statistics').html(json.box_iii_statistics);
                },
                error: function (e) { }
            });
        }
        $('#dashboard-report-range').daterangepicker(options, function (start, end, label) {
            if ($('#dashboard-report-range').attr('data-display-range') != '0') {
                $('#dashboard-report-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
            statistics_start_date = start.format('YYYY-MM-DD');
            statistics_end_date = end.format('YYYY-MM-DD');
        }).show();
        if ($('#dashboard-report-range').attr('data-display-range') != '0') {
            $('#dashboard-report-range span').html(moment().format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
        }
        $('#dashboard-report-range').on('apply.daterangepicker', function (ev, picker) {
            statistics_start_date = picker.startDate.format('YYYY-MM-DD');
            statistics_end_date = picker.endDate.format('YYYY-MM-DD');
            DeviceUserStatistics();
        });
    </script>
</body>

</html>