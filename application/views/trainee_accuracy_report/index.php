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
                                    <span> Trainee Reports</span>                                    
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Trainee Accuracy Report</span>
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
                                                    <div class="row margin-bottom-10">
                                                        <?php if ($company_id == "") { ?>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                                    <div class="col-md-9" style="padding:0px;">
                                                                        <select id="company_id" name="company_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getCompanywiseData();">
                                                                            <option value="">All Company</option>
                                                                            <?php foreach ($company_array as $cmp) { ?>
                                                                                <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="row margin-top-10"></div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Trainee-Region &nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="trainee_region_id" name="trainee_region_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" onchange="getTRegionwiseData()">
                                                                        <option value="0">All Trainee-region</option>
                                                                        <?php
                                                                        if (isset($TraineeRegionData)) {
                                                                            foreach ($TraineeRegionData as $TR) {
                                                                                ?>
                                                                                <option value="<?= $TR->id; ?>"><?php echo $TR->region_name; ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php if($this->mw_session['login_type'] !=3){ ?>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Trainee &nbsp;<span class="required"> * </span></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="trainee_id" name="trainee_id" class="form-control input-sm select2_rpt" 
                                                                            placeholder="Please select" style="width: 100%" onchange="getTraineeWiseData()">
                                                                        <option value="">Please select</option>
                                                                        <?php
                                                                        if (isset($Trainee)) {
                                                                            foreach ($Trainee as $Type) {?>
                                                                                <option value="<?= $Type->user_id; ?>"><?php echo $Type->traineename; ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>   
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                    </div>                                                
                                                    <div class="row margin-top-10">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Workshop Type</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshoptype_id" name="workshoptype_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%"
                                                                        onchange="getTraineeWiseData()">
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
                                                                <label class="control-label col-md-3">Workshop Sub-type</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_subtype" name="workshop_subtype" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%" onchange="getWSubTypewiseData();">
                                                                        <option value="">All Sub-type</option>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row margin-bottom-10">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3"> Workshop Region &nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="wregion_id" name="wregion_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getTraineeWiseData();">
                                                                        <option value="0">All Region</option>
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
                                                                <label class="control-label col-md-3">Workshop Sub-region &nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="wsubregion_id" name="wsubregion_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getWSubTypewiseData();">
                                                                        <option value="">Select Sub-region</option>

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
                                                                    <select id="workshop_id" name="workshop_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%">
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
                                                                <label class="control-label col-md-3">Workshop Session</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_session" name="workshop_session" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%">
                                                                        
                                                                        <option value="PRE">PRE</option>
                                                                        <option value="POST">POST</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>   
                                                        </div> 
                                                    <div class="clearfix margin-top-20"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="ShowChart()">Show Report</button>
                                                                <!--<button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter()">Reset</button>-->
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
            placeholder: 'Please Select',
            width: '100%'
        });
        $(".select2_rpt2").select2({
            placeholder: 'All Select',
            width: '100%'
        });
        
        function getCompanywiseData(){

                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    $('#trainee_id').empty();
                    $('#workshop_id').empty();
                    $('#wregion_id').empty();
                    $('#workshoptype_id').empty();
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
                            $('#trainee_id').empty();
                            $('#trainee_id').append(Oresult['TraineeData']); 
                            $('#wregion_id').empty();
                            $('#wregion_id').append(Oresult['RegionData']);
                            $('#workshoptype_id').empty();
                            $('#workshoptype_id').append(Oresult['WTypeData']);
                            $('#trainee_region_id').empty();
                            $('#trainee_region_id').append(Oresult['TraineeRegionData']); 
                        }
                        customunBlockUI();
                    }
                });
            }
        function getTraineeWiseData(){
             $('#workshop_id').empty();                
             $('#workshop_subtype').empty();
             $('#wsubregion_id').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
            var trainee_id= $('#trainee_id').val();
            var workshoptype_id = $('#workshoptype_id').val();
            var workshop_region = $('#wregion_id').val();
            $.ajax({
                type: "POST",
                data: {company_id: $('#company_id').val(),trainee_id: trainee_id,workshoptype_id:workshoptype_id,region_id:workshop_region},
                //async: false,
                url: "<?php echo $base_url; ?>trainee_accuracy_report/ajax_traineewtypewise_data",
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
                        $('#wsubregion_id').empty();
                        $('#wsubregion_id').append(Oresult['WorkshopSubregionData']);
                    }
                    customunBlockUI();
                }
            });
        }
//        function getWorkshoTypeWiseData(){
//                $('#workshop_id').empty();                
//                $('#wsubregion_id').empty();
//                $('#workshop_subtype').empty();
//                var compnay_id = $('#company_id').val();
//                if(compnay_id==""){
//                    return false;
//                }
//                var workshop_type = $('#workshoptype_id').val();
//                var workshop_region = $('#wregion_id').val();
//                $.ajax({
//                    type: "POST",
//                    data: {company_id: compnay_id,workshoptype_id: workshop_type,region_id:workshop_region},
//                    async: false,
//                    url: "< ?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
//                    beforeSend: function () {
//                        customBlockUI();
//                    },
//                    success: function (msg) {
//                        if (msg != '') {
//                            var Oresult = jQuery.parseJSON(msg);
//                            $('#workshop_id').empty();
//                            $('#workshop_id').append(Oresult['WorkshopData']);
//                            $('#workshop_subtype').empty();
//                            $('#workshop_subtype').append(Oresult['WorkshopSubtypeData']);
//                            $('#wsubregion_id').empty();
//                            $('#wsubregion_id').append(Oresult['WorkshopSubregionData']);
//                            }
//                        customunBlockUI();
//                    }
//                });
//            }
            function getWSubTypewiseData(){
                $('#workshop_id').empty();                                
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshopsubtype_id = $('#workshop_subtype').val();
                var workshoptype_id = $('#workshoptype_id').val();
                var region_id       = $('#wregion_id').val();
                var subregion_id = $('#wsubregion_id').val();
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),region_id:region_id,workshoptype_id: workshoptype_id,workshopsubtype_id:workshopsubtype_id,subregion_id:subregion_id},
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
        
        //        function ResetFilter() {
        //            $('.select2me').val(null).trigger('change');
        //            $('#ChartDiv_'+TotalChart).remove();
        //            FilterFrm.reset();            
        //        }

        function ShowChart() {
            var TableMSt = '';
            var trainee_id = $('#trainee_id').val();
            var company_id = $('#company_id').val();
            if (company_id == "") {
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            }
            if (trainee_id == "") {
                ShowAlret("Please select Trainee.!!", 'error');
                return false;
            }
            if ($('#workshop_id').val() == "") {
                ShowAlret("Please select Workshop.!!", 'error');
                return false;
            }
            $.ajax({
                type: "POST",
                url: "<?php echo $base_url; ?>trainee_accuracy_report/ajax_chart/" + TotalChart,
                data: $('#FilterFrm').serialize(),
                beforeSend: function () {
                    customBlockUI();
                },
                success: function (Data) {
                    if (Data != '') {
                        var Oresult = jQuery.parseJSON(Data);
                        var ChartMSt = Oresult['HtmlData'];
                        if (successflag) {
                            TableMSt += '<table class="table table-hover table-light" id="ranktable" width="50%">\n\
                            <thead><tr class="uppercase" style="background-color: #e6f2ff;">\n\
                            <th>Workshop</th><th>Overall Accuracy</th><th>Rank</th><th>Action</th style="width: 15%;"></tr></thead><tbody>';
                            successflag = 0;
                            TableMSt += Oresult['OverallTable'];
                        }
                        $('#ranktable tr:last').after(Oresult['OverallTable']);
                        TableMSt += "</tbody></table>";
                        if (Oresult['Error'] != '') {
                            ShowAlret(Oresult['Error'], 'error');
                        } else {
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
        function getTRegionwiseData(){ 
            var compnay_id = $('#company_id').val();
            if(compnay_id==""){
                return false;
            }
            $.ajax({
                type: "POST",
                data: {company_id: compnay_id,trainee_region_id:$('#trainee_region_id').val()},
                //async: false,
                url: "<?php echo $base_url; ?>trainee_accuracy_report/getTraineeData",
                beforeSend: function () {
                    customBlockUI();
                },
                success: function (msg) {
                    if (msg != '') {
                        var Oresult = jQuery.parseJSON(msg);
                        $('#trainee_id').empty();
                        $('#trainee_id').append(Oresult['TraineeData']);                             
                    }
                    customunBlockUI();
                }
            });
        }
        </script>
    </body>
</html>