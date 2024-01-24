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
        <link rel="stylesheet" type="text/css" href="<?php echo $asset_url;?>assets/global/highcharts/css/highcharts.css" />
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
                                    <span>Trainer Reports</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                            </div>
                        </div>
                        <!-- PAGE BAR -->
                        <h1 class="page-title"> Trainer Accuracy
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
                                        <div id="collapse_3_2" class="panel-collapse">
                                            <div class="panel-body" >
                                                <form id="frm_accuracy" name="frm_accuracy" method="post" action="<?php echo base_url() . 'trainer_accuracy/export_workshop' ?>">
                                                    <div class="row margin-bottom-10">
                                                    <?php if($company_id==""){ ?>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getCompanyTrainer();">
                                                                        <option value="" >Please select</option>
                                                                        <?php
                                                                            foreach ($company_array as $cmp) {?>
                                                                            <option value="<?php echo $cmp->id; ?>" ><?php echo $cmp->company_name; ?></option>
                                                                        <?php }?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Trainer&nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="user_id" name="user_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" onchange="getWorkshop();">
                                                                        
                                                                        <?php
                                                                            if(isset($Trainer_array)){
                                                                                echo '<option value="0" >All Trainer</option>';
                                                                            foreach ($Trainer_array as $cmp) {?>
                                                                                <option value="<?php echo $cmp->userid; ?>" <?php echo ($cmp->userid==$trainer_id ? 'selected':''); ?> ><?php echo $cmp->fullname; ?></option>
                                                                            <?php } } ?>
                                                                    </select>
                                                    </div>
                                                            </div>
                                                        </div>
                                                         
                                                    </div>
                                                    <div class="row margin-bottom-10">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_type_id" name="workshop_type_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" onchange="getWorkshop();">
                                                                        <?php
                                                                            if(isset($wksh_type_array)){
                                                                                echo '<option value="0" >All Type</option>';
                                                                            foreach ($wksh_type_array as $cmp) {?>
                                                                                <option value="<?php echo $cmp->id; ?>" ><?php echo $cmp->workshop_type; ?></option>
                                                                            <?php } } ?>
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
                                                                    <select id="wregion_id" name="wregion_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getWorkshop();">
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
                                                    <div class="row margin-bottom-10">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_id" name="workshop_id" class="form-control input-sm select2_rpt" 
                                                                            placeholder="Please select"  style="width: 100%" onchange="getTrainee();" >
                                                                        <?php
                                                                            if(isset($workshop_array)){
                                                                                echo '<option value="0" >Please select</option>';
                                                                            foreach ($workshop_array as $cmp) {?>
                                                                                <option value="<?php echo $cmp->workshop_id; ?>" ><?php echo $cmp->workshop_name; ?></option>
                                                                            <?php } }?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Session&nbsp;<span class="required"> * </span></label></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_session" name="workshop_session" class="form-control input-sm select2_rpt" placeholder="Please select"  
                                                                            style="width: 100%" onchange="getTrainee();" >
                                                                        <option value="PRE">PRE</option>
                                                                        <option value="POST">POST</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="clearfix margin-top-20"></div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Trainee Region&nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="trainee_region_id" name="trainee_region_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" onchange="getTrainee()">
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
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Trainee &nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="trainee_id" name="trainee_id" class="form-control input-sm select2_rpt2" 
                                                                            placeholder="Please select" style="width: 100%">
                                                                        <option value="0">All Trainee</option>   
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix margin-top-20"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="col-md-offset-9 col-md-2 text-right">
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

                            <div class="clearfix"></div>

                            <!-- TOP 5 TRAINEE -->
                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Top 5 Trainee</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important"> 
                                        <div class="table-scrollable table-scrollable-borderless">
                                            <table class="table table-hover table-light" id="trainee-top-five">
                                                <thead>
                                                    <tr class="uppercase">
                                                        <th class="wksh-td" width="80%"> TRAINEE NAME </th>
                                                        <th class="wksh-td" width="20%"> OVERALL ACCURACY </th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- TOP 5 TRAINEE -->


                            <!-- BOTTOM 5 TRAINEE -->
                            <div class="col-lg-6 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Bottom 5 Trainee</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="padding: 0px !important"> 
                                        <div class="table-scrollable table-scrollable-borderless">
                                            <table class="table table-hover table-light" id="trainee-bottom-five">
                                                <thead>
                                                    <tr class="uppercase">
                                                        <th class="wksh-td" width="80%"> TRAINEE NAME </th>
                                                        <th class="wksh-td" width="20%"> OVERALL ACCURACY </th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- BOTTOM 5 TRAINEE -->

                            <div class="clearfix"></div>

                            <!-- STAT BOX -->
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption col-lg-12 col-xs-12 col-sm-12">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">TOPIC + SUB-TOPIC WISE</span>
                                        </div>

                                    </div>
                                    <div class="portlet-body" id='topic-subtopic-chart'>
                                    </div>
                                </div>
                            </div>
                            <!-- STAT BOX -->

                            <!-- TRAINEE STATISTICS -->
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption col-lg-8 col-xs-8 col-sm-8">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">TRAINEE STATISTICS</span>
                                        </div>
                                    <?php if($acces_management->allow_export){ ?>
                                            <div class="actions">
                                                <div class="btn-group pull-right">
                                                    <button type="button" onclick="exportConfirm()" name="export_excel" id="export_excel"  class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                    &nbsp;&nbsp;
                                                </div>
                                            </div>
                                            <?php } ?>        
                                    </div>
                                    <div class="portlet-body">
                                        <div class="table-scrollable table-scrollable-borderless">
                                            <table class="table table-hover table-light" id="wksh-list">
                                                <thead>
                                                    <tr class="uppercase">
                                                        <th width="28%">TRAINEE NAME</th>
                                                        <th width="12%">TOTAL PLAYED</th>
                                                        <th width="12%">CORRECT</th>
                                                        <th width="12%">WRONG</th>
                                                        <th width="12%">RESULT</th>
                                                        <th width="12%">RANK</th>
                                                        <th width="12%">STATUS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="7">
                                                            Please select workshop set from above filter set panel.
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- STAT BOX -->
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
            var trainer_id = "<?php echo $trainer_id ; ?>";
            var company_id = "<?php echo $Supcompany_id ; ?>";
            var frm_accuracy = document.frm_accuracy;
             $(".select2_rpt").select2({
                placeholder: 'All Select',
                width: '100%'
            });
            $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            });
            function Redirect(url)
            {
                window.open(url, '_blank');
            }
            jQuery(document).ready(function() {
                 if(trainer_id != ''){                    
                    $('#company_id').select2("val",""+company_id);
                }
            });
            function getCompanyTrainer() {
                $('#trainee_id').empty();
                $('#wsubregion_id').empty();
                $('#workshop_subtype').empty();
                var compnay_id = $('#company_id').val();
                if (compnay_id == "") {
                    $('#workshop_id').empty();
                    $('#user_id').empty();
                    $('#workshop_type_id').empty();
                    $('#wregion_id').empty();
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
                            $('#workshop_id').empty();
                            $('#workshop_id').append(Oresult['WorkshopData']);   
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TrainerData']);
                            $('#wregion_id').empty();
                            $('#wregion_id').append(Oresult['RegionData']);
                            $('#workshop_type_id').empty();
                            $('#workshop_type_id').append(Oresult['WTypeData']);
                            $('#trainee_region_id').empty();
                            $('#trainee_region_id').append(Oresult['TraineeRegionData'])
                            }
                        customunBlockUI();
                    }
                });
            }
            function getWorkshop() {
                            $('#workshop_id').empty();
                $('#wsubregion_id').empty();
                $('#workshop_subtype').empty();
                var compnay_id = $('#company_id').val();
                if (compnay_id == "") {
                    return false;
                        }
                var workshop_type = $('#workshop_type_id').val();
                var workshop_region = $('#wregion_id').val();
                var user_id = $('#user_id').val();
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id, workshoptype_id: workshop_type, region_id: workshop_region, user_id: user_id},
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
                            $('#wsubregion_id').empty();
                            $('#wsubregion_id').append(Oresult['WorkshopSubregionData']);
                        }
                        customunBlockUI();   
                    }
                });
            }
            function getWSubTypewiseData() {
                $('#topic_id').empty();
                $('#workshop_id').empty();
                var compnay_id = $('#company_id').val();
                if (compnay_id == "") {
                    return false;
                }
                var workshoptype_id = $('#workshop_type_id').val();
                var region_id = $('#wregion_id').val();
                var subregion_id = $('#wsubregion_id').val();
                var workshopsubtype_id = $('#workshop_subtype').val();
                var user_id = $('#user_id').val();
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(), region_id: region_id, workshoptype_id: workshoptype_id, workshopsubtype_id: workshopsubtype_id, subregion_id: subregion_id, user_id: user_id},
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
            function getTrainee(){
                $('#trainee_id').empty();
                var compnay_id = $('#company_id').val();
                if (compnay_id == "") {
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id:compnay_id,workshop_id: $('#workshop_id').val(),workshop_session: $('#workshop_session').val(),
                           tregion_id:$('#trainee_region_id').val(),trainer_id:$('#user_id').val(),workshop_type: $('#workshop_type_id').val()},
                    url: "<?php echo $base_url; ?>common_controller/ajax_tregionwise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#trainee_id').empty();
                            $('#trainee_id').append(Oresult['AllSelectionTrainee']);                             
                        }
                        customunBlockUI(); 
                    }
                });
            }
            function dashboard_refresh(){
                $('#wksh-list tbody').empty();
                $('#trainee-top-five tbody').empty();
                $('#trainee-bottom-five tbody').empty();
                $('#topic-subtopic-chart').empty();
                if ( $('#company_id').val() == "") {
                    ShowAlret("Please select Company.!!", 'error');
                    return false;
                }else if ($('#workshop_id').val()=="0" ||  $('#workshop_id').val() == "") {
                    ShowAlret("Please select Workshop.!!", 'error');
                    return false;
                }else{
                    $.ajax({
                        type: "POST",
                        data: {company_id: $('#company_id').val(),user_id: $('#user_id').val(),
                        workshop_type_id: $('#workshop_type_id').val(),workshop_id: $('#workshop_id').val(),
                        workshop_session: $('#workshop_session').val(),trainee_id:$('#trainee_id').val(),
                        trainee_region_id:$('#trainee_region_id').val()},
                        //async: false,
                        url: "<?php echo $base_url; ?>trainer_accuracy/load_report",
                        beforeSend: function () {
                            customBlockUI();
                        },
                        success: function (response) {
                            if (response != '') {
                                var json                      = jQuery.parseJSON(response);
                                var wksh_list                 = json['wksh_list'];
                                var trainee_top_five_table    = json['trainee_top_five_table'];
                                var trainee_bottom_five_table = json['trainee_bottom_five_table'];

                                if (trainee_top_five_table!=''){
                                    $('#trainee-top-five tbody').empty();
                                    $('#trainee-top-five tbody').append(trainee_top_five_table);
                                } 
                                if (trainee_bottom_five_table!=''){
                                    $('#trainee-bottom-five tbody').empty();
                                    $('#trainee-bottom-five tbody').append(trainee_bottom_five_table);
                                }                                     
                                if (wksh_list!=''){
                                    $('#wksh-list tbody').empty();
                                    $('#wksh-list tbody').append(wksh_list);
                                } 
                                $('#topic-subtopic-chart').empty();
                                $("#topic-subtopic-chart").append(json['topic_subtopic_chart']);
                            }
                            customunBlockUI(); 
                        }
                    });
                }
            }
        function exportConfirm(){
            if ( $('#company_id').val() == "") {
                ShowAlret("Please select Company.!!", 'error');
                return false;
            }else if ($('#workshop_id').val()=="0" ||  $('#workshop_id').val() == "") {
                ShowAlret("Please select Workshop.!!", 'error');
                return false;
            }
                $.confirm({
                    title: 'Confirm!',
                    content: "Are you sure want to Export. ? ",
                    buttons: {
                        confirm:{
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function(){
                            frm_accuracy.submit();
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