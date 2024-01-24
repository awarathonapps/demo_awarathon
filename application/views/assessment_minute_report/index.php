<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url =$this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <!--<link rel="stylesheet" type="text/css" href="< ?php echo $asset_url;?>assets/global/highcharts/css/highcharts.css" />-->
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <style>
            .table-scrollable>.table>tbody>tr>th, .table-scrollable>.table>tfoot>tr>td, .table-scrollable>.table>tfoot>tr>th, .table-scrollable>.table>thead>tr>th {
                white-space: normal;
            }
            .potrait-title-mar{
                margin-left: -9px;margin-right: -9px;
            }
            .dashboard-stat{
                -webkit-border-radius: 4px;-moz-border-radius: 4px;-ms-border-radius: 4px;-o-border-radius: 4px;
                border-radius: 4px;background: #fff;padding: 5px 5px 5px;border: 1px solid #eef1f5;border-radius: 5px !important;background: aliceblue;
            }
            .dashboard-stat .display {
                height: 70px;
            }
            .dashboard-stat .display .number {
                text-align: center;float: left;display: inline-block;width: 100%;
            }
            .dashboard-stat .display .number small{
                font-size: 12px;color: #777777;font-weight: 600;text-transform: uppercase;width: 100%;
            }
            .font-orange-sharp{
                color: #f1592a !important;margin: 0px !important;padding: 5px !important;
            }
            .no-padding{
                padding:0px !important;
            }
            .page-content-white .page-title {
                margin: 20px 0;font-size: 22px;font-weight: 300!important;
            }
        </style>
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white">
        <div class="page-wrapper">
            <?php $this->load->view('inc/inc_header');?>
            <div class="clearfix"> </div>
            <div class="page-container">
                <?php $this->load->view('inc/inc_sidebar');?>
                <div class="page-content-wrapper">
                    <div class="page-content">
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li>
                                    <span>Assessment Reports</span>
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Minutes Report</span>
                                </li>
                            </ul>
                            <div class="col-md-8 page-breadcrumb pull-right">
                                <div class="row">
                                 <?php if ($Company_id == "") { ?>
                                    <div class="col-md-5 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Company&nbsp;<span class="required"> * </span></label>
                                            <div class="col-md-8" style="padding:0px;">
                                                <select id="company_id" name="company_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getCompanywiseData();">
                                                    <option value="">All Company</option>
                                                   <?php 
                                                        foreach ($CompanyData as $cmp) { ?>
                                                        <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                 <?php } ?>                                                    
                                    <div class="col-md-8 pull-right">
                                        <div class="form-group">
                                            <label class="control-label col-md-4" >Assessment Year&nbsp;<span class="required"> * </span></label>
                                            <div class="col-md-8" style="padding:0px;">
                                                <select id="assessmin_id" name="assessmin_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" onchange="setMinuteData();">
                                                    <option value="">Please Select</option>
                                                    <?php
                                                    if (isset($assess_date_array)) {
                                                        foreach ($assess_date_array as $dt) {
                                                            ?>
                                                            <option value="<?= $dt->id; ?>"><?php echo $dt->assess_date; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>   
                                                </select>
                                            </div>
                                        </div>
                                    </div>                                                             
                                </div> 
                            </div> 
                        </div>
                        <!-- PAGE BAR -->      
                        <h1 class="page-title">
                         Minutes Report <small>chart &amp; list</small>
                        </h1>
                        <div class="clearfix margin-top-10"></div>
                        <div class="row">                            
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" >
<!--                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Quick Statistics</span>
                                        </div>
                                    </div>-->
                                    <div class="portlet-body">                                                                                                                        
                                        <div class="clearfix"></div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="row" > 
                                                    <div class="col-lg-6 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                        <div class="dashboard-stat">
                                                            <div class="display">
                                                                <div class="number">
                                                                    <h3 class="font-orange-sharp">
                                                                        <span data-counter="counterup" id="total_minute" data-value="0">0</span>
                                                                    </h3>
                                                                    <small>Billed Minutes</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                        <div class="dashboard-stat">
                                                            <div class="display">
                                                                <div class="number">
                                                                    <h3 class="font-orange-sharp">
                                                                        <span data-counter="counterup" id="total_utilized" data-value="0">0</span>
                                                                    </h3>
                                                                    <small>Utilized Minutes</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 5px;"> 
                                                    <div class="col-lg-6 col-md-3 col-sm-6 col-xs-12" style="padding: 0px 5px 5px 5px;">
                                                        <div class="dashboard-stat">
                                                            <div class="display">
                                                                <div class="number">
                                                                    <h3 class="font-orange-sharp">
                                                                        <span data-counter="counterup" id="total_left" data-value="0">0</span>
                                                                    </h3>
                                                                    <small>Minutes Left</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>  
                                            </div>
                                            <div class="col-md-4" style="border:1px solid #d4d4d4;height: 280px;" id="minute_performance">

                                            </div>
                                        </div>
                                        <div class="clearfix"></div>  
                                        <!--<hr/>-->
                                    </div>
                                </div>
                            </div>                            
                        </div>
                        <div class="row mt-10">
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" >
                                        <div class="portlet-title potrait-title-mar">
                                            <div class="caption">
                                                <i class="icon-bar-chart font-dark hide"></i>
                                                <span class="caption-subject font-dark bold uppercase">Assessment wise list </span>
                                            </div>
                                            <?php if($acces_management->allow_export){ ?>
                                                <div class="actions">
                                                    <div class="btn-group pull-right">
                                                        <button type="button" onclick="exportConfirm()"
                                                        autofocus="" accesskey="" name="export_excel" id="export_excel"  class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                        &nbsp;&nbsp;
                                                    </div>
                                                </div>
                                                <?php } ?>
                                        </div>
                                        <div class="portlet-body">                                                                                                                        
                                        <div class="clearfix"></div>
                                            <form id="frmReorts" name="frmReorts" method="post" action="<?php echo base_url() . 'assessment_minute_report/exportReport' ?>">
                                                <table class="table  table-bordered table-hover table-checkable order-column" id="index_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Assessment Name</th>
                                                            <th>Total users played</th>
                                                            <th>Total Utilized Minutes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </form>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
            </div>
            <?php //$this->load->view('inc/inc_footer'); ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav'); ?>
        <?php $this->load->view('inc/inc_footer_script');?>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/highcharts/highstock.js"></script>
<!--        <script src="< ?php echo $asset_url;?>assets/global/highcharts/highcharts.js"></script>-->
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <?php if ($acces_management->allow_print) { ?>
            <script src="<?php echo $asset_url; ?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
        <script>
            var search=1;
            var frmReorts = document.frmReorts;
            var table = $('#index_table');
            jQuery(document).ready(function () {
                 $(".select2_rpt").select2({
                        placeholder: 'Please Select',
                        width: '100%'
                  });
//                setMinuteData();
            });
            function setMinuteData() {
                if($('#assessmin_id').val()==""){
                     ShowAlret("Please select Assessment Date.!!", 'error');
                     return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),assessmin_id: $('#assessmin_id').val()},
                    //async: false,
                    url: "<?php echo base_url() ?>assessment_minute_report/get_minute_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (data) {
                        if (data != '') {
                            var json = jQuery.parseJSON(data);
                            $('#total_minute').attr('data-value', json['billed_minute']);
                            $('#total_minute').counterUp();
                            $('#total_utilized').attr('data-value', json['minute_utilized']);
                            $('#total_utilized').counterUp();
                            $('#total_left').attr('data-value', json['minute_left']);
                            $('#total_left').counterUp();
                            $('#minute_performance').html(json['minute_graph']);
                            DatatableRefresh();
                            customunBlockUI();
                        }
                    }
                });
            }
            function DatatableRefresh() {
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
                        "infoFiltered": "(filtered 1 from _MAX_ total records)",
                        "lengthMenu": "Show _MENU_",
                        "search": "Search:",
                        "zeroRecords": "No matching records found",
                        "paginate": {   
                            "previous":"Prev",
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
                    //"bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "order": [
                        [2, "desc"]
                    ],
                    "pageLength": 10,
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
//                      {'width': '30px','orderable': true,'searchable': true,'targets': [0],"visible":false},
			{'className': 'dt-head-left dt-body-left','width': '200px','orderable': true,'searchable': true,'targets': [0]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': false,'targets': [1]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': false,'targets': [2]}    
                    ],
                    
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'assessment_minute_report/DatatableRefresh/'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'company_id', value: $('#company_id').val()});
                        aoData.push({name: 'assessmin_id', value: $('#assessmin_id').val()});
                        $.getJSON(sSource, aoData, function (json) {
                            fnCallback(json);
                        });
                    },
                    "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                        return nRow;
                    }
                    , "fnFooterCallback": function (nRow, aData) {
                    },
                    "initComplete": function(settings, json) {
                        $('thead > tr> th:nth-child(1)').css({ 'min-width': '100px', 'max-width': '200px' });
                        $('thead > tr> th:nth-child(2)').css({ 'min-width': '50px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(3)').css({ 'min-width': '50px', 'max-width': '100px' });
                    }
                });
            }
            function exportConfirm(){
                $.confirm({
                    title: 'Confirm!',
                    content: "Are you sure want to Export? ",    
                    buttons: {
                        confirm:{
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function(){
                            frmReorts.submit();
                        }
                    },
                    cancel: function () {
                         this.onClose();
                    }
                  }
                });
            }
</script>
</body>
</html>