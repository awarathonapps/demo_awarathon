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
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
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
                                <span>Api Logs</span>
                                <i class="fa fa-circle"></i>
                            </li>
                            <li>
                                <span>Home</span>
                                <i class="fa fa-circle"></i>
                            </li>
                        </ul>

                    </div>

                    <form id="api_logs" name="api_logs" action="<?php echo base_url() . 'api_logs/export_api_logs/' ?>" method="post">
                        <input type="hidden" name="export_type" id="export_type" value="1">
                        <input type="hidden" name="StartDate" id="StartDate" value="<?php echo $start_date; ?>">
                        <input type="hidden" name="EndDate" id="EndDate" value="<?php echo $end_date; ?>">
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="panel-group accordion" id="accordion3">
                                    <div class="panel panel-default">

                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                    Advanced Search </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_3_2" class="panel-collapse collapse">
                                            <div class="panel-body">
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Company&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width: 100%">
                                                                    <option value="">All Company</option>
                                                                    <?php
                                                                    foreach ($Company_set as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-4">Registration Date</label>
                                                            <div class="col-md-8 input-group input-large date-picker input-daterange">
                                                                <input class="form-control input-sm" id="api_log_picker" value="" name="api_log_picker" readonly>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix margin-top-20"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="DatatableRefresh()">Search</button>
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter()">Reset</button>
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
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                        <?php echo $this->session->flashdata('flash_message'); ?>
                                    </div>
                                <?php } ?>
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            Api Logs
                                            <div class="tools"> </div>
                                        </div>
                                        <div class="actions">
                                            <div class="btn-group pull-right">
                                                <?php if ($acces_management->allow_export) { ?>
                                                    <a class="btn btn-sm btn-orange pull-right" id="bulk_excel" href="javascript:;" onclick="exportConfirm(1)">
                                                        <i class="fa fa-file-excel-o"></i> Export to Excel
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <table class="table  table-bordered table-hover table-checkable order-column" id="index_table">
                                            <thead>
                                                <tr>
                                                    <!-- <th>
                                                        <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                            <input type="checkbox" class="ap group-checkable" name="check" id="check" data-set="#index_table .checkboxes" />
                                                            <span></span>
                                                        </label>
                                                    </th> -->
                                                    <th>ID</th>
                                                    <th>Company Id</th>
                                                    <th>Company Name</th>
                                                    <th>Api Name</th>
                                                    <th>Api Parameter</th>
                                                    <th>Date Time</th>
                                                    <th>Ip Address</th>
                                                    <th>Status Msg</th>
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
            <?php //$this->load->view('inc/inc_quick_sidebar'); 
            ?>
        </div>
        <?php //$this->load->view('inc/inc_footer'); 
        ?>
    </div>
    <?php //$this->load->view('inc/inc_quick_nav'); 
    ?>
    <div class="modal fade" id="add_newmodal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog  modal-lg" style="width:1050px;">
            <div class="modal-content" id="load_modeldata">

            </div>
        </div>
    </div>
    <?php $this->load->view('inc/inc_footer_script'); ?>
    <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="<?php echo $base_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>

    <script src="<?php echo $asset_url; ?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script>
        var StartDate = "<?php echo $start_date; ?>";
        var EndDate = "<?php echo $end_date; ?>";
        jQuery(document).ready(function() {

            $('.ap').click(function() {
                if ($(this).is(':checked')) {
                    $("input[name='id[]']").prop('checked', true);
                } else {
                    $("input[name='id[]']").prop('checked', false);
                }
            });
            DatatableRefresh();
        });

        // 
        var quarter = moment().quarter();
        var year = moment().year();
        $('#api_log_picker').daterangepicker({
            "ranges": {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "autoApply": true,
            "mirrorOnCollision": true,
            "applyOnMenuSelect": true,
            "autoFitCalendars": true,
            "locale": {
                "format": "DD-MM-YYYY",
                "separator": " - ",
                "applyLabel": "Apply",
                "cancelLabel": "Cancel",
                "fromLabel": "From",
                "toLabel": "To",
                "customRangeLabel": "custom",
                "daysOfWeek": [
                    "Su",
                    "Mo",
                    "Tu",
                    "We",
                    "Th",
                    "Fr",
                    "Sa"
                ],
                "monthNames": [
                    "January",
                    "February",
                    "March",
                    "April",
                    "May",
                    "June",
                    "July",
                    "August",
                    "September",
                    "October",
                    "November",
                    "December"
                ],
                // "firstDay": 1
            },
            "startDate": moment().format("DD/MM/YYYY"),
            "endDate": moment().format("DD/MM/YYYY"),
            "drops": "down",
            // "opens": "right",
            opens: (App.isRTL() ? 'right' : 'left'),
        }, function(start, end, label) {
            sessionStorage.setItem("IsCustom", label);

        });
        if ($('#api_log_picker').attr('data-display-range') != '') {
            var thisYear = (new Date()).getFullYear();
            var thisMonth = (new Date()).getMonth() + 1;
            var start = new Date(thisMonth + "/1/" + thisYear);
        }
        $('#api_log_picker').on('apply.daterangepicker', function(ev, picker) {

            StartDate = picker.startDate.format('DD-MM-YYYY');
            EndDate = picker.endDate.format('DD-MM-YYYY');
            if (StartDate != "" && EndDate != "") {
                $('#date_lable').text(picker.chosenLabel);
            }
            sessionStorage.setItem("StartDate", StartDate);
            sessionStorage.setItem("EndDate", EndDate);
            let IsCustom = sessionStorage.getItem("IsCustom");
            $('#StartDate').val(StartDate);
            $('#EndDate').val(EndDate);
        });
        // 

        function exportConfirm(module_id) {
            $.confirm({
                title: 'Confirm!',
                content: "Are you sure you want to Export. ? ",
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function() {
                            $('#export_type').val(module_id);
                            document.api_logs.submit();
                        }
                    },
                    cancel: function() {
                        this.onClose();
                    }
                }
            });
        }

        function getCheckCount() {
            var x = 0;
            for (var i = 0; i < api_logs.elements.length; i++) {
                if (api_logs.elements[i].checked == true) {
                    x++;
                }
            }
            return x;
        }

        function DatatableRefresh() {
            var StartDate = $('#StartDate').val();
            var EndDate = $('#EndDate').val();
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
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [{
                        'width': '30px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [0]
                    },
                    {
                        'width': '85px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [1]
                    },
                    {
                        'width': '85px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [2]
                    },
                    {
                        'width': '85px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [3]
                    },
                    {
                        'width': '220px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [4]
                    },
                    {
                        'width': '130px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [5]
                    },
                    {
                        'width': '30px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [6]
                    },
                    {
                        'width': '60px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [7]
                    },
                    {
                        'width': '85px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [8]
                    }
                    // ,
                    // {
                    //     'width': '85px',
                    //     'orderable': false,
                    //     'searchable': false,
                    //     'targets': [9]
                    // }
                ],
                "order": [
                    [1, "desc"]
                ],
                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'api_logs/DatatableRefresh'; ?>",
                "fnServerData": function(sSource, aoData, fnCallback) {
                    aoData.push({
                        name: '__mode',
                        value: 'featuredimage.ajaxload'
                    });
                    aoData.push({
                        name: 'start_date',
                        value: StartDate
                    });
                    aoData.push({
                        name: 'end_date',
                        value: EndDate
                    });
                    aoData.push({
                        name: 'company_id',
                        value: $('#company_id').val()
                    });
                    aoData.push({
                        name: 'status',
                        value: $('#status').val()
                    });

                    $.getJSON(sSource, aoData, function(json) {
                        fnCallback(json);
                    });
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    return nRow;
                },
                "fnFooterCallback": function(nRow, aData) {}
            });
        }

        function ResetFilter() {
            $('.select2').val(null).trigger('change');
            document.api_logs.reset();
            DatatableRefresh();
        }
    </script>
</body>

</html>