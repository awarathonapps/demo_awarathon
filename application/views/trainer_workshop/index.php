<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $asset_url;?>assets/global/highcharts/css/highcharts.css"" />
        <style>
            table tr {
                background-color: #ffffff;
            }
            .table.table-light thead tr th{
                color: #000000 !important;
            }
            .table.table-light tbody tr td{
                color: #000000 !important;
            }
            .highcharts-data-labels{
                font-size: 11px;
                color: #FFFFFF;
                font-family: Verdana, sans-serif;
                fill: #FFFFFF;
            }
            .highcharts-color-0 {
                fill: #0070c0;
                stroke: #0070c0;
            }
            .highcharts-color-1 {
                fill: #00ffcc;
                stroke: #00ffcc;
            }.highcharts-color-2 {
                fill: #ffff00;
                stroke: #ffff00;
            }
            .highcharts-negative{
                fill: #FF0000;
                stroke: #FF0000;
            }
        </style>
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white">
        <div class="page-wrapper">
            <?php $this->load->view('inc/inc_header'); ?>
            <div class="clearfix"> </div>
            <div class="page-container">
                <?php $this->load->view('inc/inc_sidebar'); ?>
                <div class="page-content-wrapper">
                    <div class="page-content">

                        <!-- PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li>
                                    <a href="javascript:;">Trainer Reports</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Trainer Workshop</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">


                                <!-- <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                    <i class="icon-calendar"></i>&nbsp;
                                    <span class="thin uppercase hidden-xs"></span>&nbsp;
                                    <i class="fa fa-angle-down"></i>
                                </div> -->
                            </div>
                        </div>
                        <!-- PAGE BAR -->
                        <h1 class="page-title"> Trainer Workshop
                            <!-- <small>- overview statistics, charts, recent workshop and reports</small> -->
                        </h1>
                        <?php if($trainer_id == '') { ?>
                        <div class="row">
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="panel-group accordion" id="accordion3">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled " data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                   Filter Report </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_3_2" class="panel-collapse ">
                                            <div class="panel-body" >
                                                <form id="frmFilterDashboard" name="frmFilterDashboard" method="post">

                                                    <div class="row margin-bottom-10">
                                                        <?php if ($Supcompany_id == "") { ?>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-4">Company&nbsp;<span class="required"> * </span></label>
                                                                    <div class="col-md-8" style="padding:0px;">
                                                                        <select id="company_id" name="company_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getCompanyTrainer()">
                                                                            <option value="">All Company</option>
                                                                            <?php
                                                                                foreach ($company_array as $cmp) {?>
                                                                                <option value="<?=$cmp->id;?>"><?php echo $cmp->company_name; ?></option>
                                                                            <?php }?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php }?>
<!--                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                 <label class="control-label col-md-3">From Date</label>                                                    
                                                                 <div class="col-md-9 input-group input-large date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy">
                                                                     <input type="text" class="form-control input-sm" id="start_date" name="start_date" value="< ?php echo date('01-m-Y'); ?>" >
                                                                    <span class="input-group-addon"> to </span>
                                                                    <input type="text" class="form-control input-sm" id="end_date" name="end_date" value="< ?php echo date('t-m-Y'); ?>">
                                                                </div>                                                   
                                                            </div>
                                                        </div>-->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4">Trainer&nbsp;</label>
                                                                <div class="col-md-8" style="padding:0px;">
                                                                    <select id="user_id" name="user_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" >
                                                                        <option value="0">All Trainer</option>
                                                                        <?php if (isset($TrainerResult)) {
                                                                        foreach ($TrainerResult as $trainer) {?>
                                                                            <option value="<?= $trainer->userid; ?>"><?php echo $trainer->fullname; ?></option>
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
                                                                <label class="control-label col-md-4">Workshop Type&nbsp;</label>
                                                                <div class="col-md-8" style="padding:0px;">
                                                                    <select id="workshop_type_id" name="workshop_type_id" class="form-control input-sm select2_rpt" placeholder="Please select"  
                                                                            style="width: 100%" onchange="getWTypewiseData();">
                                                                        <option value="0">All Type</option>
                                                                        <?php
                                                                        if (isset($WtypeResult)) {
                                                                            foreach ($WtypeResult as $Type) {?>
                                                                                <option value="<?= $Type->id; ?>"><?php echo $Type->workshop_type; ?></option>
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
                                                                <label class="control-label col-md-4">Workshop Sub-type</label>
                                                                <div class="col-md-8" style="padding:0px;">
                                                                <select id="workshop_subtype" name="workshop_subtype" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%" >
                                                                    <option value="">All Sub-type</option>

                                                                </select>
                                                                </div>
                                                            </div>
                                                    </div>
                                                    </div>    
                                                    <div class="row margin-bottom-10">    
                                                         <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-4"> Workshop Region &nbsp;</label>
                                                                <div class="col-md-8" style="padding:0px;">
                                                                    <select id="wregion_id" name="wregion_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getWTypewiseData();">
                                                                         <option value="0">All Region</option>
                                                                            <?php
                                                                            if (isset($RegionData)) {
                                                                                foreach ($RegionData as $Rdata) {?>
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
                                                            <label class="control-label col-md-4">Workshop Sub-region &nbsp;</label>
                                                            <div class="col-md-8" style="padding:0px;">
                                                                <select id="wsubregion_id" name="wsubregion_id" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%" >
                                                                    <option value="">Select Sub-region</option>
                                                        
                                                                </select>
                                                    </div>
                                                        </div>
                                                     </div>     
                                                    </div>
                                                    <div class="clearfix margin-top-20"></div>
                                                    <div class="row">
                                                        
                                                        <div class="col-md-12">
                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="dashboard_refresh(0)">Preview Report</button>
                                                                <!-- <button type="button" class="btn blue-hoki btn-sm" onclick="dashboard_reset()">Reset</button> -->
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
                        <?php } ?>
                        <!-- STAT FIRST ROW -->
                        <div class="row">

                            <!-- STAT BOX -->
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption col-lg-12 col-xs-8 col-sm-8">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Workshop Accuracy</span>
                                            
                                            <span style='float:right;font-size:13px;font-weight:bold;color:red;'>* NP - Not Played</span>
                                        </div>
<!--                                        <div class="col-md-3" style='float:right;'>
                                            <div class="form-group">
                                                <div class="col-md-12" >
                                                    <select id="workshop_id" name="workshop_id" class="form-control input-sm select2_rpt3" placeholder="Please select"  style="width: 100%" onchange="dashboard_refresh(1);" >
                                                        <option value="0">All Workshop</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>-->
                                    </div>
                                    <div class="portlet-body">
                                            <table class="table table-hover table-light" id="wksh-list">
                                                <thead>
                                                    <tr class="uppercase">
<!--                                                        <th width="22%">WORKSHOP DATE</th>-->
                                                        <th width="22%">ID</th>
                                                        <th width="22%">WORKSHOP NAME</th>
                                                        <th width="12%">NO. OF TOPIC</th>
                                                        <th width="12%">AVERAGE C.E</th>
                                                        <th width="12%">NO. OF TRAINEES</th>
                                                        <th width="32%">REPORT OPTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>

                                        
                                        <div class="table-scrollable table-scrollable-borderless" id="wksh-total" style="font-size:14px;font-weight:bold;">&nbsp;</div>
                                    </div>
                                </div>
                            </div>
                            <!-- STAT BOX -->

                            <!-- TOP 5 TABLE -->
                            <div class="col-lg-6 col-xs-6 col-sm-6">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Top 5 Participants</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="top_five_panel"> 
                                        <div id="top_five_loading" style="text-align: center;display: none;">
                                            <img src="<?php echo $asset_url;?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- TOP 5 TABLE -->

                            <!-- BOTTOM 5 TABLE -->
                            <div class="col-lg-6 col-xs-6 col-sm-6">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Bottom 5 Participants</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important" id="bottom_five_panel"> 
                                        <div id="bottom_five_loading" style="text-align: center;display: none;">
                                            <img src="<?php echo $asset_url;?>assets/global/img/loading.gif" alt="loading" /> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- BOTTOM 5 TABLE -->


                            <!-- CHART MODAL -->
                            <div id="chart-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="800">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                            <h5 class="modal-title" id="modal_title"></h5>
                                        </div>
                                        <div class="modal-body" id="popupchart">
                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-md-12 text-right ">  
                                                <button type="button" class="btn btn-orange" class="close" data-dismiss="modal" aria-hidden="true">
                                                    <span class="ladda-label">Close</span>
                                                </button>
                                                
                                            </div>
                                        </div>
                                    </div>    
                                </div>    
                            </div>
                            <!-- CHART MODAL -->



                        </div>
                        <!-- STAT FIRST ROW -->

                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
            </div>
            <?php //$this->load->view('inc/inc_footer'); ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav');  ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        
        
        <script src="<?php echo $asset_url;?>assets/global/highcharts/highstock.js"></script>
         <?php if($acces_management->allow_print){ ?>
                <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
        <script>
            var trainer_id = "<?php echo $trainer_id ; ?>";
            var company_id = "<?php echo $Supcompany_id ; ?>";
            $(".select2_rpt2").select2({
            placeholder: 'Please Select',
            width: '100%'
        });
         $(".select2_rpt").select2({
            placeholder: 'All Select',
            width: '100%'
        });
         $(".select2_rpt3").select2({
            placeholder: 'All Workshop',
            width: '100%'
        });
        //TableRefresh();
            function Redirect(url)
            {
                // window.location = url;
                window.open(url, '_blank');
            }
            jQuery(document).ready(function() {
                if(trainer_id != ''){
                    dashboard_refresh(0);
                }                
            });
            function getCompanyTrainer(){
                $('#wsubregion_id').empty();
                $('#workshop_subtype').empty();
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    $('#user_id').empty();
                    $('#workshop_type_id').empty();
                    $('#wregion_id').empty();
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data/0",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TrainerData']); 
                            $('#wregion_id').empty();
                            $('#wregion_id').append(Oresult['RegionData']);
                            $('#workshop_type_id').empty();
                            $('#workshop_type_id').append(Oresult['WTypeData']);
                        }
                        customunBlockUI();    
                    }
                });
            }
            function getWTypewiseData(){           
                $('#wsubregion_id').empty();
                $('#workshop_subtype').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshop_type = $('#workshop_type_id').val();
                var workshop_region = $('#wregion_id').val();
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id,workshoptype_id: workshop_type,region_id:workshop_region},
                    async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#workshop_subtype').empty();
                            $('#workshop_subtype').append(Oresult['WorkshopSubtypeData']);
                            $('#wsubregion_id').empty();
                            $('#wsubregion_id').append(Oresult['WorkshopSubregionData']);
                            }
                        customunBlockUI();
                    }
                });
            }
            function dashboard_refresh(){
                if(trainer_id == ''){
                    if ( $('#company_id').val() == "") {
                        ShowAlret("Please select Company.!!", 'error');
                        return false;
                    }
                }
                TableRefresh();
            }
            function TableRefresh() {
            var table = $('#wksh-list');
            table.dataTable({
                destroy: true,
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "emptyTable": "No Workshop data available in table",
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
                "columnDefs": [
                    {'width': '20px', 'orderable': true, 'searchable': true, 'targets': [0]},
                    {'width': '100px', 'orderable': true, 'searchable': true, 'targets': [1]},
                    {'width': '80px', 'orderable': false, 'searchable': false, 'targets': [2]},
                    {'width': '80px', 'orderable': false, 'searchable': false, 'targets': [3]},
                    {'width': '80px', 'orderable': false, 'searchable': false, 'targets': [4]},
                    {'width': '200px', 'orderable': false, 'searchable': false, 'targets': [5]}
                ],
                
                "order": [
                    [0, "desc"]
                ],
                //bFilter: false,
                "processing": true,
                "serverSide": true,
                "sAjaxSource": "<?php echo base_url() . 'trainer_workshop/load_workshop'; ?>",
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                    aoData.push({name: 'company_id', value: $('#company_id').val()});
                    if(trainer_id == ''){
                        aoData.push({name: 'user_id', value: $('#user_id').val()});
                    }else{
                        aoData.push({name: 'user_id', value: trainer_id});
                    }
                    aoData.push({name: 'workshop_type_id', value: $('#workshop_type_id').val()});
                    //aoData.push({name: 'WorkshopFlag', value: $('#user_id').val()});
                    aoData.push({name: 'wregion_id', value: $('#wregion_id').val()});
                    aoData.push({name: 'workshop_id', value: $('#workshop_id').val()});
                    aoData.push({name: 'wsubregion_id', value: $('#wsubregion_id').val()});
                    aoData.push({name: 'workshop_subtype', value: $('#workshop_subtype').val()});
                    $.getJSON(sSource, aoData, function (json) {
                        $('#top_five_panel').html(json['top_five_table']);
                        $('#bottom_five_panel').html(json['bottom_five_table']);
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
            function workshop_summary(workshop_id){
                $('#popupchart').empty();
                if(trainer_id == ''){
                    //start_date:$('#start_date').val(),end_date:$('#end_date').val()
                    var tdata = {company_id: $('#company_id').val(),user_id: $('#user_id').val(),
                        workshop_id:workshop_id};
                }else{
                     tdata = {company_id: company_id,user_id: trainer_id,workshop_id:workshop_id};
                    }
                $.ajax({
                    type: "POST",
                    data: tdata,
                    //async: false,
                    url: "<?php echo $base_url; ?>trainer_workshop/load_wksh_summary",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (response) {
                        if (response != '') {
                            var json              = jQuery.parseJSON(response);
                            $('#popupchart').empty();
                            $("#popupchart").append(json['summary_report']);
                            $('#modal_title').html(json['Modal_Title']);
                            $('#chart-modal').modal('show');
                        }
                        customunBlockUI();    
                    }
                });
            }
            function workshop_detail(workshop_id){
                $('#popupchart').empty();
                if(trainer_id == ''){
                    //start_date:$('#start_date').val(),end_date:$('#end_date').val()
                    var tdata = {company_id: $('#company_id').val(),user_id: $('#user_id').val(),
                        workshop_id:workshop_id};
                }else{
                     tdata = {company_id: company_id,user_id: trainer_id,workshop_id:workshop_id};
                    }
                $.ajax({
                    type: "POST",
                    data: tdata,
                    //async: false,
                    url: "<?php echo $base_url; ?>trainer_workshop/load_wksh_detail",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (response) {
                        if (response != '') {
                            var json              = jQuery.parseJSON(response);
                            $('#popupchart').empty();
                            $("#popupchart").append(json['detail_report']);
                            $('#modal_title').html(json['Modal_Title']);
                            $('#chart-modal').modal('show');
                        }
                        customunBlockUI();    
                    }
                });
            }
            function workshop_trainee(workshop_id){
                $('#popupchart').empty();
                if(trainer_id == ''){
                    //start_date:$('#start_date').val(),end_date:$('#end_date').val()
                    var tdata = {company_id: $('#company_id').val(),user_id: $('#user_id').val(),
                        workshop_id:workshop_id};
                }else{
                     tdata = {company_id: company_id,user_id: trainer_id,workshop_id:workshop_id};
                    }
                $.ajax({
                    type: "POST",
                    data: tdata,
                    //async: false,
                    url: "<?php echo $base_url; ?>trainer_workshop/load_wksh_trainee",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (response) {
                        if (response != '') {
                            var json              = jQuery.parseJSON(response);
                            $('#popupchart').empty();
                            $("#popupchart").append(json['trainee_report']);
                            $('#chart-modal').modal('show');
                            $('#modal_title').html(json['Modal_Title']);
                        }
                        customunBlockUI();    
                    }
                });
            }
            function getTrainnewiseData(workshop_id){
            if(trainer_id == ''){
                    //start_date:$('#start_date').val(),end_date:$('#end_date').val()
                    var tdata = {company_id: $('#company_id').val(),user_id: $('#user_id').val(),
                        workshop_id:workshop_id,trainee_id:$('#pop_trainee_id').val()};
                }else{
                     tdata = {company_id: company_id,user_id: trainer_id,workshop_id:workshop_id,
                         trainee_id:$('#pop_trainee_id').val()};
                    }
            $.ajax({
                    type: "POST",
                    data: tdata,
                    //async: false,
                    url: "<?php echo $base_url;?>trainer_workshop/gettraineewise_topic",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (lchtml) {                        
                        $('#container2').html(lchtml);
                    customunBlockUI();    
                    }
                }); 
        }
//        function workshop_individual(company_id,trainer_id,workshop_id,workshop_type_id){
//            var url = "< ?php echo base_url();?>trainer_individual/index/"+company_id+"/"+trainer_id+"/"+workshop_id+"/"+workshop_type_id;
//            Redirect(url);
//        }
        </script>
    </body>
</html>