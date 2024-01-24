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
        <link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/global/highcharts/css/highcharts.css" />
        <style>
            .tokenize-sample { width: 100%;height:auto }
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
            table tr {
                background-color: #ffffff;
            }
            .table.table-light thead tr th{
                color: #000000 !important;
            }
            .table.table-light tbody tr td{
                color: #000000 !important;
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
                                    <span> Supervisor Reports</span>                                    
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Accuracy Report</span>
                                </li>
                            </ul>                            
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="alert alert-danger display-hide" id="errordiv">
                                    <button class="close" data-close="alert"></button>
                                    You have some form errors. Please check below.
                                    <br><span id="errorlog"></span>
                                </div>
                                <div class="panel-group accordion" id="accordion3">
                                    <div class="panel panel-default">

                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                    Report Search </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_3_2" class="panel-collapse">
                                            <div class="panel-body" >
                                                <form id="FilterFrm" name="FilterFrm" method="post">
                                                    <?php if ($Company_id == "") { ?>
                                                    <div class="row margin-bottom-10">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                    <div class="col-md-9" style="padding:0px;">
                                                                        <select id="company_id" name="company_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getCompanywiseData();">
                                                                            <option value="">All Company</option>
                                                                            <?php foreach ($CompanyData as $cmp) { ?>
                                                                                <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    </div>
                                                    <?php } ?>
                                                    <div class="row margin-top-10">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Workshop Type &nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshoptype_id" name="workshoptype_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%"
                                                                        onchange="getWorkshoTypeWiseData()">
                                                                        <option value="0">All Type</option>
                                                                     <?php
                                                                        if (isset($WtypeResult)) {
                                                                            foreach ($WtypeResult as $Type) {
                                                                            ?>
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
                                                                <label class="control-label col-md-3">Workshop Sub-type</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_subtype" name="workshop_subtype" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%" onchange="getWSubTypewiseData();">
                                                                        <option value="">All Sub-type</option>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row margin-top-10">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Region &nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="region_id" name="region_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getWorkshoTypeWiseData()">
                                                                        <option value="0">All Region</option>
                                                                        <?php
                                                                    if (isset($RegionResult)) {
                                                                        foreach ($RegionResult as $region) {
                                                                                ?>
                                                                            <option value="<?= $region->id; ?>"><?php echo $region->region_name; ?></option>
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
                                                                <label class="control-label col-md-3">Trainer</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="trainer_id" name="trainer_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" onchange="getWorkshoTypeWiseData()">
                                                                        <option value="0">All Trainer</option>
                                                                        <?php if (isset($TrainerResult)) {
                                                                        foreach ($TrainerResult as $trainer) {
                                                                            ?>
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

                                                    <div class="row margin-top-10">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_id" name="workshop_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%">
                                                                        <option value="">All Workshop</option>
                                                                        <?php
                                                                            if (isset($WorkshopResultSet)) {
                                                                                foreach ($WorkshopResultSet as $Type) {?>
                                                                                    <option value="<?= $Type->workshop_id; ?>"><?php echo $Type->workshop_name; ?></option>
                                                                                    <?php
                                                                                }
                                                                            } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Session&nbsp;<span class="required"> * </span></label></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_session" name="workshop_session" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" >
                                                                        <option value="PRE">PRE</option>
                                                                        <option value="POST">POST</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row margin-top-10">
                                                        <div class="col-md-12">
                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="ShowChart()">Show Result</button>                                                                
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
                                <div class="col-md-12" id="AppendChart" >
                                </div>
                            </div>
                        
                            <div class="row mt-20 margin-top-20">
                                <div class="col-md-8" id="tablecontainer">
                                </div>
                            </div>
                       
                    </div>
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar');   ?>
            </div>
            <?php //$this->load->view('inc/inc_footer');   ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav');   ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url;?>assets/global/highcharts/highstock.js"></script>
         <?php if($acces_management->allow_print){ ?>
                <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
        <script>
        var TotalChart = 1;
        var successflag = 1;
         $(".select2_rpt").select2({
            placeholder: 'All Select',
            width: '100%'
        });
        $(".select2_rpt2").select2({
            placeholder: 'Please Select',
            width: '100%'
        });
        
        function getCompanywiseData() {
            var company_id= $('#company_id').val();
            if(company_id==""){
                $('#region_id').empty();
                $('#workshoptype_id').empty();
                $('#workshop_subtype').empty();
                return false;
            }
            $.ajax({
                type: "POST",
                data: {data: company_id},
                //async: false,
                url: "<?php echo $base_url; ?>supervisor_accuracy/ajax_companywise_data",
                beforeSend: function () {
                    customBlockUI();
                },
                success: function (msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        var WtypeMSt = Oresult['WtypeResult'];
                        var wtype_option = '<option value="0">All</option>';
                        for (var i = 0; i < WtypeMSt.length; i++) {
                            wtype_option += '<option value="' + WtypeMSt[i]['id'] + '">' + WtypeMSt[i]['workshop_type'] + '</option>';
                        }
                        $('#workshoptype_id').empty();
                        $('#workshoptype_id').append(wtype_option);
                        var RegionMSt = Oresult['RegionResult'];
                        var region_option = '<option value="0">All</option>';
                        for (var i = 0; i < RegionMSt.length; i++) {
                            region_option += '<option value="' + RegionMSt[i]['id'] + '">' + RegionMSt[i]['region_name'] + '</option>';
                        }
                        $('#region_id').empty();
                        $('#region_id').append(region_option);
                        var WorkshopMSt = Oresult['WorkshopData'];
                        var workshop_option = '<option value="">Please Select</option>';
                        for (var i = 0; i < WorkshopMSt.length; i++) {
                            workshop_option += '<option value="' + WorkshopMSt[i]['workshop_id'] + '">' + WorkshopMSt[i]['workshop_name'] + '</option>';
                        }
                        $('#workshop_id').empty();
                        $('#workshop_id').append(workshop_option);
                        var TrainerMSt = Oresult['TrainerResult'];
                        var trainer_option = '<option value="0" selected>All</option>';
                        for (var i = 0; i < TrainerMSt.length; i++) {
                            trainer_option += '<option value="' + TrainerMSt[i]['userid'] + '">' + TrainerMSt[i]['fullname'] + '</option>';
                        }
                        $('#trainer_id').empty();
                        $('#trainer_id').append(trainer_option);
                    }
                    customunBlockUI();
                }
            });
        }        
        function getWorkshoTypeWiseData(){
                $('#workshop_id').empty();                
                $('#workshop_subtype').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    ShowAlret("Please select Company first.!!", 'error');
                    return false;
            }
                var workshop_type = $('#workshoptype_id').val();
                var workshop_region = $('#region_id').val();
                var trainer_id = $('#trainer_id').val();
            $.ajax({
                type: "POST",
                    data: {company_id: compnay_id,workshoptype_id: workshop_type,region_id:workshop_region,user_id:trainer_id},
                    async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                beforeSend: function () {
                    customBlockUI();
                },
                success: function (msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                            $('#workshop_id').empty();
                            $('#workshop_id').append(Oresult['WorkshopData']);
                            $('#workshop_subtype').empty();
                            $('#workshop_subtype').append(Oresult['WorkshopSubtypeData']);
                        }
                        customunBlockUI();
                    }
                });
            }
            function getWSubTypewiseData(){
                        $('#workshop_id').empty();
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    return false;
                    }
                var workshoptype_id = $('#workshoptype_id').val();
                var workshopsubtype_id = $('#workshop_subtype').val();
                var region_id       = $('#region_id').val();
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),region_id:region_id,workshoptype_id: workshoptype_id,workshopsubtype_id:workshopsubtype_id},
                    //async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#workshop_id').empty();
                            $('#workshop_id').append(Oresult['WorkshopData']);                                                                                    
                        }
                    customunBlockUI();
                }
            });
        }
        function getTrainerData() {
            $.ajax({
                type: "POST",
                data: {company_id: $('#company_id').val(),region_id: $('#region_id').val()},
                //async: false,
                url: "<?php echo $base_url; ?>supervisor_accuracy/ajax_TrainerData",
                beforeSend: function () {
                    customBlockUI();
                },
                success: function (msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        var TrainerMSt = Oresult['TrainerResult'];
                        var trainer_option = '<option value="0" selected>All</option>';
                        for (var i = 0; i < TrainerMSt.length; i++) {
                            trainer_option += '<option value="' + TrainerMSt[i]['userid'] + '">' + TrainerMSt[i]['fullname'] + '</option>';
                        }
                        $('#trainer_id').empty();
                        $('#trainer_id').append(trainer_option);
                    }
                    customunBlockUI();
                }
            });
        }
        function ShowChart() {
            var TableMSt = '';            
            var company_id = $('#company_id').val();
            var wtype_id = $('#workshoptype_id').val();
            var workshop_id = $('#workshop_id').val();
            var region_id = $('#region_id').val();
            if (company_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            }            
            if (workshop_id == "") {
                ShowAlret("Please select Workshop.!!", 'error');
                return false;
            }
//            if (wtype_id == "") {
//                ShowAlret("Please select Workshop Type.!!", 'error');
//                return false;
//            }            
//            if (region_id == "") {
//                ShowAlret("Please select Region.!!", 'error');
//                return false;
//            }
            $.ajax({
                type: "POST",
                url: "<?php echo $base_url; ?>supervisor_accuracy/ajax_chart/" + TotalChart,
                data: $('#FilterFrm').serialize(),
                beforeSend: function () {
                    customBlockUI();
                },
                success: function (Data) {
                    if (Data != '') {
                        var Oresult = jQuery.parseJSON(Data);
                        var ChartMSt = Oresult['HtmlData'];
                        
                        if (Oresult['Error'] != '') {
                            ShowAlret(Oresult['Error'], 'error');
                        } else {
                            if (successflag) {
                                TableMSt += '<table class="table table-hover table-light" id="ranktable" width="50%">\n\
                                <thead><tr class="uppercase" style="background-color: #e6f2ff;">\n\
                                <th>Workshop</th><th>Overall Accuracy</th></tr></thead><tbody>';
                                successflag = 0;
                                TableMSt += Oresult['OverallTable'];
                            }
                            $('#ranktable tr:last').after(Oresult['OverallTable']);
                            TableMSt += "</tbody></table>";
                            $('#AppendChart').append(ChartMSt);
                            $('#tablecontainer').append(TableMSt);
                            TotalChart++;
                        }
                    }
                    customunBlockUI();
                }
            });
        }
        function RemoveChart(id) {
            $('#ChartDiv_' + id).remove();
            $('#datatr_' + id).remove();
        }
        </script>
    </body>
</html>