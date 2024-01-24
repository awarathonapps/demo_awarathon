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
        <!--datattable CSS  Start-->
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!--datattable CSS  End-->
        <?php $this->load->view('inc/inc_htmlhead');?>
        <style>
            .table-scrollable>.table>tbody>tr>th, .table-scrollable>.table>tfoot>tr>td, .table-scrollable>.table>tfoot>tr>th, .table-scrollable>.table>thead>tr>th {
                white-space: normal;
            }
        </style>
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
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
                                    <i class="fa fa-circle"></i>
                                    <span>Workshop Reports</span>
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Device IMEI Report</span>
                                </li>
                            </ul>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="panel-group accordion" id="accordion3">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled " data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                               Report Search </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_3_2" class="panel-collapse ">
                                        <div class="panel-body" >
                                            <form id="FilterFrm" name="FilterFrm" method="post">
                                                <?php if ($Company_id == "") { ?>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Company&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
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
                                                </div>
                                                 <?php } ?>
                                                <div class="row margin-bottom-10">
                                                      <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainee Region &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="tregion_id" name="tregion_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getTrainerwiseData()"> 
                                                                    <option value="">All Region</option> 
                                                                    <?php 
                                                                        if (isset($RegionData)) {
                                                                            foreach ($RegionData as $Rdata) {
                                                                                ?>
                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
                                                                              <?php
                                                                            }
                                                                        }
                                                                        ?> 
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Designation &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="designation_id" name="designation_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                   <option value="">All Designation</option>
                                                                         <?php 
                                                                        if (isset($DesignationData)) {
                                                                            foreach ($DesignationData as $Rdata) {
                                                                                ?>
                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->description; ?></option>
                                                                              <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Device Users &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="user_id" name="user_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" >
                                                                    <option value="">All Users</option>
                                                                          <?php
                                                                        if (isset($TraineeData)) {
                                                                            foreach ($TraineeData as $Type) {
                                                                                ?>
                                                                                <option value="<?= $Type->user_id; ?>"><?php echo $Type->traineename; ?></option>
                                                                              <?php
                                                                            }
                                                                        }
                                                                        ?> 
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="col-md-offset-8 col-md-4 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="DatatableRefresh()">Search</button>
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter()">Reset</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                                                                
                                            </form> 
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="portlet light bordered">
                                    <form id="frmReorts" name="frmReorts" method="post" action="<?php echo base_url() . 'imei_report/exportReport' ?>">
                                        <div class="portlet-title">
                                            <div class="caption caption-font-24">
                                              Device IMEI Report 
                                               <div class="tools"> </div>
                                            </div>
                                            <?php if($acces_management->allow_export){ ?>
                                            <div class="actions">
                                                <div class="btn-group pull-right">
                                                    <button type="button"
                                                    onclick="exportConfirm()"
                                                    autofocus="" accesskey="" name="export_excel" id="export_excel"  class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                    &nbsp;&nbsp;

                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div class="clearfix margin-top-20"></div>
                                        <div class="portlet-body">
                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_table">
                                                <thead>
                                                    <tr>
                                                        <th>Trainee Id</th>
                                                        <th>Employee Code</th>
                                                        <th>First Name</th>
                                                        <th>Last Name</th>
                                                        <th>Email</th>
                                                        <th>Mobile No.</th>
                                                        <th>Employment Year</th>
                                                        <th>Education Background</th>
                                                        <th>Department/Division</th>
                                                        <th>Region/Branch</th>
                                                        <th>Designation</th>
                                                        <th>Area</th>
                                                        <th>Status</th>
                                                        <th>Platform</th>
                                                        <th>Model</th>
                                                        <th>IMEI</th>
                                                        <th>Serial No</th>
                                                        <th>Date & Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </form>
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
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <script>
            var search=1;
            var frmReorts = document.frmReorts;
            $(document).ready(function() {   
                 DatatableRefresh();
            });
               
            function DatatableRefresh() { 
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
                    "bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "pageLength": 10,
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
//                      {'width': '30px','orderable': true,'searchable': true,'targets': [0],"visible":false},
                        {'className': 'dt-head-left dt-body-left','width': '50px','orderable': true,'searchable': true,'targets': [0]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': true,'targets': [1]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': true,'targets': [2]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': true,'targets': [3]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': false,'targets': [4]},
                        {'className': 'dt-head-left dt-body-left','width': '120px','orderable': false,'searchable': false,'targets': [5]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [6]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': true,'targets': [7]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': true,'targets': [8]},
                        {'className': 'dt-head-left dt-body-left','width': '200px','orderable': false,'searchable': true,'targets': [9]},
                        {'className': 'dt-head-left dt-body-left','width': '200px','orderable': false,'searchable': true,'targets': [10]},
                        {'className': 'dt-head-left dt-body-left','width': '200px','orderable': false,'searchable': true,'targets': [11]},
                        {'className': 'dt-head-left dt-body-left','width': '180px','orderable': false,'searchable': true,'targets': [12]},
                        {'className': 'dt-head-left dt-body-left','width': '250px','orderable': false,'searchable': false,'targets': [13]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': false,'targets': [14]},
                        {'className': 'dt-head-left dt-body-left','width': '120px','orderable': false,'searchable': false,'targets': [15]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [16]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [17]}
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'imei_report/DatatableRefresh/'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'company_id', value: $('#company_id').val()});
                        aoData.push({name: 'tregion_id', value: $('#tregion_id').val()});
                        aoData.push({name: 'designation_id', value: $('#designation_id').val()});
                        aoData.push({name: 'user_id', value: $('#user_id').val()});
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
                        $('thead > tr> th:nth-child(1)').css({ 'min-width': '80px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(2)').css({ 'min-width': '100px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(3)').css({ 'min-width': '100px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(4)').css({ 'min-width': '120px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(5)').css({ 'min-width': '100px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(6)').css({ 'min-width': '100px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(7)').css({ 'min-width': '80px', 'max-width': '80px' });
                        $('thead > tr> th:nth-child(8)').css({ 'min-width': '80px', 'max-width': '80px' });
                        $('thead > tr> th:nth-child(9)').css({ 'min-width': '100px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(10)').css({ 'min-width': '100px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(11)').css({ 'min-width': '100px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(12)').css({ 'min-width': '100px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(13)').css({ 'min-width': '80px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(14)').css({ 'min-width': '80px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(15)').css({ 'min-width': '80px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(16)').css({ 'min-width': '100px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(17)').css({ 'min-width': '100px', 'max-width': '80px' });
                        $('thead > tr> th:nth-child(18)').css({ 'min-width': '100px', 'max-width': '80px' });
                    }
                });
            }
            function exportConfirm(){
                $.confirm({
                    title: 'Confirm!',
                    content: "Are you sure want to Export. ? ",                     
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
             function getTrainerwiseData(){
                $('#user_id').empty();
                var tregion_id  = $('#tregion_id').val();
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id,tregion_id:tregion_id},
                    //async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_tregionwise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TraineeData']);
                        }
                        customunBlockUI();
                    }
                });
            }
            function getCompanywiseData(){
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    $('#tregion_id').empty();
                    $('#user_id').empty();
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg); 
                            $('#tregion_id').empty();
                            $('#tregion_id').append(Oresult['RegionData']);
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TraineeData']);
                        }
                    customunBlockUI();    
                    }
                });
            }
            function ResetFilter() {
                $('.select2me,.select2_rpt2').select("val","");
                $('.select2me,.select2_rpt2').val(null).trigger('change');
                document.FilterFrm.reset();
                DatatableRefresh();
            }
</script>
</body>
</html>