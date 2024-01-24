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
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link href="<?php echo $asset_url;?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
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
                                    <a href="javascript:;">Home</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Trainer Individual</span>
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
                        <h1 class="page-title">Trainer Individual
                            <!-- <small>- overview statistics, charts, recent workshop and reports</small> -->
                        </h1>

                        <div class="row">
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="panel-group accordion" id="accordion3">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                   Filter Report </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_3_2" class="panel-collapse collapse ">
                                            <div class="panel-body" >
                                                <form id="frmFilterDashboard" name="frmFilterDashboard" method="post">

                                                    <div class="row margin-bottom-10">
                                                        <?php if ($company_id == "") { ?>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getCompanyTrainer();">
                                                                        <option value="">All Company</option>
                                                                        <?php
                                                                            foreach ($company_array as $cmp) {?>
                                                                            <option value="<?php echo $cmp->id; ?>" <?php echo ($cmp->id==$company_id ? 'selected':'');  ?> ><?php echo $cmp->company_name; ?></option>
                                                                        <?php }?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_type_id" name="workshop_type_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" onchange="getWorkshop();">
                                                                     <option value="0">All Type</option>
                                                                        <?php
                                                                        if (isset($WtypeResult)) {
                                                                            foreach ($WtypeResult as $Type) {?>
                                                                                <option value="<?= $Type->id; ?>" <?php echo ($Type->id==$workshop_type_id ? 'selected':'');  ?> ><?php echo $Type->workshop_type; ?></option>
                                                                                <?php
                                                                            }
                                                                        } ?>   
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row margin-bottom-10">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Trainer&nbsp;<span class="required"> * </span></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="user_id" name="user_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" >
                                                                    <option value="0">All Trainer</option>
                                                                        <?php if (isset($TrainerResult)) {
                                                                        foreach ($TrainerResult as $trainer) {?>
                                                                            <option value="<?= $trainer->userid; ?>" <?php echo ($trainer->userid==$trainer_id ? 'selected':'');  ?>><?php echo $trainer->fullname; ?></option>
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
                                                                <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_id" name="workshop_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" >
                                                                         <?php
                                                                        if (isset($workshop_array)) {
                                                                            foreach ($workshop_array as $Type) {?>
                                                                                <option value="<?= $Type->workshop_id; ?>" <?php echo ($Type->workshop_id==$workshop_id ? 'selected':'');  ?> ><?php echo $Type->workshop_name; ?></option>
                                                                                <?php
                                                                            }
                                                                        } ?>   
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="clearfix margin-top-20"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="dashboard_refresh()">Preview Report</button>
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

                        <!-- STAT FIRST ROW -->
                        <div class="row">

                            <!-- STAT BOX -->
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption col-lg-12 col-xs-12 col-sm-12">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Trainee Accuracy</span>
                                            <!-- <span style='float:right;font-size:13px;font-weight:bold;color:red;'>* NP - Not Played</span> -->
                                        </div>

                                    </div>
                                    <div class="portlet-body">
                                        <div class="table-scrollable table-scrollable-borderless" style="height:200px;">
                                            <table class="table table-hover table-light" id="trainee-list">
                                                <thead style="display: block;">
                                                    <tr class="uppercase">
                                                        <th width="34%">TRAINEE NAME</th>
                                                        <th width="12%">C.E</th>
                                                        <th width="12%">POST ACCURACY</th>
                                                        <th width="12%">NO. OF TOPICS</th>
                                                        <th width="28%">REPORT OPTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="display: block;height: 165px;overflow-y: auto;overflow-x: hidden;">
                                                    <tr>
                                                        <td colspan="5">
                                                            Please select filter option from above.
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- STAT BOX -->

                            <!-- TRAINEE + WORKSHOP GRAPH  -->
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption col-lg-12 col-xs-12 col-sm-12">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Workshop Post Competency</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" id="trainee-wksh-chart">
                                        
                                    </div>
                                </div>
                            </div>
                            <!-- TRAINEE + WORKSHOP GRAPH  -->


                            <!-- CHART MODAL -->
                            <div id="chart-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="800">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                            <h4 class="modal-title" id="modal_title"></h4>
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
        <script src="<?php echo $asset_url;?>assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url;?>assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>

        <script src="<?php echo $asset_url;?>assets/global/highcharts/highstock.js"></script>
         <?php if($acces_management->allow_print){ ?>
                <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
        <script>
            function Redirect(url)
            {
                // window.location = url;
                window.open(url, '_blank');
            }
            jQuery(document).ready(function() {

//                $('#company_id').val(< ?php echo $company_id;?>).trigger('change');
//                $('#workshop_type_id').val(< ?php echo $workshop_type_id;?>).trigger('change');
//                $('#user_id').val(< ?php echo $trainer_id;?>).trigger('change');
//                $('#workshop_id').val(< ?php echo $workshop_id;?>).trigger('change');
                $(".select2_rpt2").select2({
            placeholder: 'Please Select',
            width: '100%'
        });
         $(".select2_rpt").select2({
            placeholder: 'All Select',
            width: '100%'
        });
        dashboard_refresh();
            });
            function getCompanyTrainer(){
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val()},
                    async: false,
                    url: "<?php echo $base_url; ?>trainer_individual/ajax_company_trainer_type",
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult         = jQuery.parseJSON(msg);
                            var user_array      = Oresult['user_array'];
                            var wksh_type_array = Oresult['wksh_type_array'];

                            var user_option = '<option value="0">All Trainer</option>';
                            for (var i = 0; i < user_array.length; i++) {
                                user_option += '<option value="' + user_array[i]['userid'] + '">' + user_array[i]['fullname'] + '</option>';
                            }
                            $('#user_id').empty();
                            $('#user_id').append(user_option);

                            var wksh_type_option = '<option value="0">All</option>';
                            for (var i = 0; i < wksh_type_array.length; i++) {
                                wksh_type_option += '<option value="' + wksh_type_array[i]['id'] + '">' + wksh_type_array[i]['workshop_type'] + '</option>';
                            }
                            $('#workshop_type_id').empty();
                            $('#workshop_type_id').append(wksh_type_option);
                            
                        }
                    }
                });
            }
            function getWorkshop(){
                $.ajax({
                    type: "POST",
                    data: {company_id:$('#company_id').val(),workshop_type_id:$('#workshop_type_id').val(),user_id:$('#user_id').val()},
                    async: false,
                    url: "<?php echo $base_url.'trainer_individual/ajax_fetch_workshop/'.$company_id; ?>",
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult         = jQuery.parseJSON(msg);
                            var workshop_array      = Oresult['workshop_array'];

                            var wksh_option = '<option value="0">All</option>';
                            for (var i = 0; i < workshop_array.length; i++) {
                                wksh_option += '<option value="' + workshop_array[i]['workshop_id'] + '">' + workshop_array[i]['workshop_name'] + '</option>';
                            }
                            $('#workshop_id').empty();
                            $('#workshop_id').append(wksh_option);
                        }
                    }
                });
            }
            function dashboard_refresh(){
                $('#trainee-wksh-chart').empty();
                $('#popupchart').empty();
                $('#trainee-list tbody').empty();                
                if ($('#company_id').val() == "") {
                    ShowAlret("Please select Company.!!", 'error');
                    return false;
                }else if (!$('#user_id').val() ||  $('#user_id').val() == "") {
                    ShowAlret("Please select Trainer.!!", 'error');
                    return false;
                }else if (!$('#workshop_id').val() ||  $('#workshop_id').val() == "") {
                    ShowAlret("Please select Workshop.!!", 'error');
                    return false;
                }else{
                    $('#trainee-wksh-chart').empty();
                    $('#popupchart').empty();
                    $('#trainee-list tbody').empty();
                    $.ajax({
                        type: "POST",
                        data: {company_id: $('#company_id').val(),user_id: $('#user_id').val(),workshop_type_id: $('#workshop_type_id').val(),workshop_id: $('#workshop_id').val()},
                        async: false,
                        url: "<?php echo $base_url.'trainer_individual/load_trainee_table/'.$company_id; ?>",
                        success: function (response) {
                            if (response != '') {
                                var json              = jQuery.parseJSON(response);
                                var wksh_list         = json['wksh_list'];

                                if (wksh_list!=''){
                                    $('#trainee-list tbody').empty();
                                    $('#trainee-list tbody').append(wksh_list);
                                } 
                            }
                        }
                    });
                }
            }
            function workshop_detail(trainee_id){
                $('#trainee-wksh-chart').empty();
                $.ajax({
                    type: "POST",
                    data: {company_id:$('#company_id').val(),workshop_type_id:$('#workshop_type_id').val(),trainee_id: trainee_id,trainer_id:$('#user_id').val(),workshop_id: $('#workshop_id').val()},
                    async: false,
                    url: "<?php echo $base_url.'trainer_individual/load_wksh_detail/'.$company_id; ?>",
                    success: function (response) {
                        if (response != '') {
                            var json              = jQuery.parseJSON(response);
                            $('#trainee-wksh-chart').empty();
                            $("#trainee-wksh-chart").append(json['detail_report']);
                        }
                    }
                });
            }
            function topic_subtopic(trainee_id,workshop_id){
                $('#popupchart').empty();
                $.ajax({
                    type: "POST",
                    data: {trainee_id:trainee_id,workshop_id: workshop_id,trainer_id:$('#user_id').val()},
                    async: false,
                    url: "<?php echo $base_url.'trainer_individual/load_topic_subtopic/'.$company_id; ?>",
                    success: function (response) {
                        if (response != '') {
                            var json              = jQuery.parseJSON(response);
                            $('#popupchart').empty();
                            $("#popupchart").append(json['detail_report']);
                            $('#chart-modal').modal('show');
                        }
                    }
                });
            }
        </script>
    </body>
</html>