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
            #ShowResultButtonDiv {
                text-align: center;
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
            .highcharts-data-labels{
                font-size: 11px;
                color: #FFFFFF;
                font-family: Verdana, sans-serif;
                fill: #FFFFFF;
            }
            #topic_wise_ce .highcharts-color-0 {
                fill: #0070c0 !important;
                stroke: #0070c0 !important;
            }
            .highcharts-color-1 {
                fill: #00ffcc;
                stroke: #00ffcc;
            }.highcharts-color-2 {
                fill: #ffc000;
                stroke: #ffc000;
            }
            .highcharts-negative{
                fill: #FF0000;
                stroke: #FF0000;
            }
            .selectedBox{
                    background: #ffa500!important;
            }
            .trClickeble{
                cursor: pointer;
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
                                    <span>Supervisor Reports</span>                                    
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Comparison Report</span>
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
                                    <div id="collapse_3_2" class="panel-collapse ">
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
                                                            <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshoptype_id" name="workshoptype_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" 
                                                                    onchange="getTypeWiseWorkshop();"   >
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
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Region&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="region_id" name="region_id" class="form-control input-sm select2_rpt" 
                                                                        placeholder="Please select"  style="width: 100%" onchange="getTypeWiseWorkshop();" >
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
                                                            <label class="control-label col-md-3">Sub-region &nbsp;</label>
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
                                                            <label class="control-label col-md-3">Trainer &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="trainer_id" name="trainer_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" onchange="getTypeWiseWorkshop();">
                                                                    <option value="0">All Trainer</option>
                                                                   <?php
                                                                    if (isset($TrainerResult)) {
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
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshop_id" name="workshop_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%">
                                                                    <option value="0">All Workshop</option>
                                                                    <?php
                                                                    if (isset($WorkshopResultSet)) {
                                                                        foreach ($WorkshopResultSet as $Type) {?>
                                                                            <option value="<?= $Type->workshop_id; ?>"><?php echo $Type->workshop_name; ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>  
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                </div>                                                    
                                                 <div class="clearfix margin-top-20"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="AddSet()">Add Set</button>
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
                                <div class="clearfix margin-top-20"></div>
                                <div class="row">
                                    <div id="AppendChart" class="col-md-10" >
                                            <table class="table table-hover table-light" id="CompTable" width="30%">
                                            <thead>                                                
                                            <tr class="uppercase" style="background-color: #e6f2ff;">                                            
                                                <th>Workshop Region</th> 
                                                <th>Workshop Sub-region</th>
                                                <th>Workshop Type </th>
                                                <th>Workshop Sub-type </th>
                                                <th>Trainer Name</th>
                                                <th>Workshop Name</th>
                                                <th width="7%"></th>
                                            </tr></thead><tbody>
                                                </tbody></table>
                                    </div>
                                </div>
                            <div id="ShowResultButtonDiv" class="row mt-10">

                            </div>
                            <div class="row mt-10" id="AppendDiv">
                                
                            </div>                        
                    </div>
                </div>                
            </div>            
        </div>        
<?php $this->load->view('inc/inc_footer_script'); ?>
<script src="<?php echo $asset_url;?>assets/global/scripts/Chart.bundle.js"></script>
<script src="<?php echo $asset_url;?>assets/global/highcharts/highcharts.src.js"></script>
 <?php if($acces_management->allow_print){ ?>
        <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
<?php } ?>
<script src="<?php echo $asset_url;?>assets/global/scripts/utils.js"></script>
<script>            
        var FilterFrm = $('#FilterFrm');
        var form_error = $('.alert-danger', FilterFrm);
        var form_success = $('.alert-success', FilterFrm);
        var Counter=1;
         $(".select2_rpt").select2({
            placeholder: 'All Select',
            width: '100%'
        });
        $(".select2_rpt2").select2({
            placeholder: 'Please Select',
            width: '100%'
        });
        function getTypeWiseWorkshop(){
                $('#workshop_id').empty();                
                $('#wsubregion_id').empty();
                $('#workshop_subtype').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
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
                            $('#wsubregion_id').empty();
                            $('#wsubregion_id').append(Oresult['WorkshopSubregionData']);
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
        function getCompanywiseData(){
             $('#workshop_subtype').empty();
             $('#wsubregion_id').empty();
            if($('#company_id').val() ==''){
                $('#region_id').empty();
                $('#workshoptype_id').empty();
                $('#workshop_id').empty();
                return false;
                }
                $.ajax({
                    type: "POST",
                    data: {data: $('#company_id').val()},
                    //async: false,
                    url: "<?php echo $base_url;?>supervisor_comparison/ajax_companywise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var RegionMSt = Oresult['RegionResult'];                              
                            var region_option = '<option value="0">All Region</option>';                           
                            for (var i = 0; i < RegionMSt.length; i++) {
                                region_option += '<option value="' + RegionMSt[i]['id'] + '">' + RegionMSt[i]['region_name'] + '</option>';
                            }                           
                            $('#region_id').empty();
                            $('#region_id').append(region_option);
                            
                            var WtypeMSt = Oresult['WtypeResult'];                              
                            var wtype_option = '<option value="0">All Type</option>';                           
                            for (var i = 0; i < WtypeMSt.length; i++) {
                                wtype_option += '<option value="' + WtypeMSt[i]['id'] + '">' + WtypeMSt[i]['workshop_type'] + '</option>';
                            }                           
                            $('#workshoptype_id').empty();
                            $('#workshoptype_id').append(wtype_option);
                            
                            var TrainerMSt = Oresult['TrainerResult'];                              
                            var trainer_option = '<option value="0">All </option>';                           
                            for (var i = 0; i < TrainerMSt.length; i++) {
                                trainer_option += '<option value="' + TrainerMSt[i]['userid'] + '">' + TrainerMSt[i]['fullname'] + '</option>';
                            }                           
                            $('#trainer_id').empty();
                            $('#trainer_id').append(trainer_option);
                            var WorkshopMSt = Oresult['WorkshopData'];                              
                            var workshop_option = '<option value="0">All Select</option>';                           
                            for (var i = 0; i < WorkshopMSt.length; i++) {
                                workshop_option += '<option value="' + WorkshopMSt[i]['workshop_id'] + '">' + WorkshopMSt[i]['workshop_name'] + '</option>';
                            }                            
                            $('#workshop_id').empty();
                            $('#workshop_id').append(workshop_option); 
                        }
                    customunBlockUI();    
                    }
                });
        }   
        function AddSet(){
            if ($('#company_id').val() == "") {
                ShowAlret("Please select Company.!!", 'error');
                return false;
            }
//            if ($('#workshoptype_id').val() == "") {
//                ShowAlret("Please select Workshop Type.!!", 'error');
//                return false;
//            }
//            if ($('#region_id').val() == "") {
//                ShowAlret("Please select Region.!!", 'error');
//                return false;
//            }
            var company_id = $('#company_id').val();
            var region = $('#region_id option:selected').text();
            var region_id = $('#region_id').val();
            var workshoptype = $('#workshoptype_id option:selected').text();
            var workshoptype_id = $('#workshoptype_id').val();
            var workshop = $('#workshop_id option:selected').text();
            var workshop_id = $('#workshop_id').val();
            var trainer = $('#trainer_id option:selected').text();
            var trainer_id = $('#trainer_id').val();
            var wsubtype = ($('#workshop_subtype').val() !='' ? $('#workshop_subtype option:selected').text() : '');
            var wsubtype_id = $('#workshop_subtype').val();
            var wsubregion = ($('#wsubregion_id').val() !='' ? $('#wsubregion_id option:selected').text() : '');
            var wsubregion_id = $('#wsubregion_id').val();
            var tablehtml = "<tr id='datatr_"+Counter+"' class='datatr trClickeble' \n\ >\n\
                            <td>"+region+"</td><td>"+wsubregion+"</td><td>"+workshoptype+"</td><td>"+wsubtype+"</td><td>"+trainer+"</td><td>"+workshop+"</td>\n\
                            <td><button id='button-filter'  class='btn btn-sm btn-small btn-danger' type='button' onclick='RemoveChart("+Counter+");'>X</button></td></tr>"
            $('#CompTable tr:last').after(tablehtml);
            RowData(Counter,company_id,region_id,workshoptype_id,workshop_id,trainer_id,wsubtype_id,wsubregion_id);
            Counter++;            
        }
        function RemoveChart(Row_id){   
            $('#childdiv_'+Row_id).remove();             
            $('#datatr_'+Row_id).remove();            
        }
        function RowData(rowID,company_id,region_id,workshoptype_id,workshop_id,trainer_id,wsubtype_id,wsubregion_id){            
            var Isseleted = $("#datatr_"+rowID ).hasClass("selectedBox");
                if(Isseleted){
                    $("#datatr_"+rowID).removeClass("selectedBox");                     
                    $('#childdiv_'+rowID).remove();
                    return true;
                }
            $.ajax({
                    type: "POST",
                    url: "<?php echo $base_url;?>supervisor_comparison/ComparisonTable",
                    data: {rowID:rowID,company_id:company_id,region_id : region_id,workshoptype_id:workshoptype_id,workshop_id:workshop_id,trainer_id:trainer_id,wsubtype_id:wsubtype_id,wsubregion_id:wsubregion_id},
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (Data) {
                        if (Data != '') {
                            var Oresult = jQuery.parseJSON(Data);
                            var TableData = Oresult['ChildTable'];                            
                            if(Oresult['Error']!=''){
                                ShowAlret(Oresult['Error'], 'error');
                            }else{                                                                   
                                $('#AppendDiv').append(TableData);
                                $('#datatr_'+rowID).addClass('selectedBox');
                            }
                        }                        
                    customunBlockUI();    
                    }
                });
        }
        </script>
    </body>
</html>