<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>        
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>assets/global/highcharts/css/highcharts.css" />
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
                                    <span>Trainee Reports</span>                                    
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Trainee Comparison Report</span>
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
                                                <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                <div class="col-md-9" style="padding:0px;">
                                                    <select id="workshoptype_id" name="workshoptype_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" 
                                                            onchange="getTypeWiseWorkshop();"   >
                                                        <option value="">All Type</option>
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
                                                        <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label>
                                                        <div class="col-md-9" style="padding:0px;">
                                                            <select id="workshop_id" name="workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getTrainee();">
                                                                <option value="">All Workshop</option>
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
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Trainee&nbsp;</label>
                                                        <div class="col-md-9" style="padding:0px;">
                                                            <select id="trainee_id" name="trainee_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" >
                                                                <option value="">All Trainee</option>
                                                                
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                                 <div class="clearfix margin-top-20"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="ShowChart()">Add Set</button>
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
                                <div class="row" >
                                <div id="AppendChart" class="col-md-10" >
                                        <table class="table table-hover table-light" id="CEtable" width="30%">
                                        <thead >
                                            <tr  style="background-color: #e6f2ff;"><td colspan="5" style="text-align: left;">Compare Workshop(Click on Workshop title to generate Traineewise details)</td></tr>    
                                        <tr class="uppercase" style="background-color: #e6f2ff;">                                            
                                            <th>Workshop Name</th>                        
                                            <th>Pre</th>
                                            <th>Post</th>
                                            <th>C.E</th>
                                            <th width="7%"></th>
                                        </tr></thead><tbody>
                                            </tbody></table>
                                </div>
                            </div>
                            <div id="TraineeChart" class="row mt-10" ></div>
                        
                    </div>
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar');  ?>
            </div>
            <?php //$this->load->view('inc/inc_footer');  ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav');  ?>
<?php $this->load->view('inc/inc_footer_script'); ?>
<script src="<?php echo $base_url;?>assets/global/scripts/Chart.bundle.js"></script>
<script src="<?php echo $base_url;?>assets/global/highcharts/highcharts.src.js"></script>
 <?php if($acces_management->allow_print){ ?>
                <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
<script src="<?php echo $base_url;?>assets/global/scripts/utils.js"></script>
<script>            
        var FilterFrm = $('#FilterFrm');
        var form_error = $('.alert-danger', FilterFrm);
        var form_success = $('.alert-success', FilterFrm);
        var TotalWkshop=1;
        function getTrainee(){
                if($('#workshop_id').val() ==''){                    
                    $('#trainee_id').empty();
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),workshop_id:$('#workshop_id').val()},
                    async: false,
                    url: "<?php echo $base_url;?>trainee_comparison_report/WorkshopwiseTrainee",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var TraineeMSt = Oresult['TraineeData'];                              
                            var trainee_option = '<option value="">Please Select</option>';                           
                            for (var i = 0; i < TraineeMSt.length; i++) {
                                trainee_option += '<option value="' + TraineeMSt[i]['user_id'] + '">' + TraineeMSt[i]['username'] + '</option>';
                            }                            
                            $('#trainee_id').empty();
                            $('#trainee_id').append(trainee_option);                            
                        }
                    customunBlockUI();    
                    }
                });
        }
        function getTypeWiseWorkshop(){        
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),workshop_type:$('#workshoptype_id').val()},
                    async: false,
                    url: "<?php echo $base_url;?>trainee_comparison_report/ajax_wtypewise_workshop",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var WorkshopMSt = Oresult['WorkshopData'];                              
                            var workshop_option = '<option value="">Please Select</option>';                           
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
        function getCompanywiseData(){
            if($('#company_id').val() ==''){
                $('#workshoptype_id').empty();
                $('#workshop_id').empty();
                return false;
            }
                $.ajax({
                    type: "POST",
                    data: {data: $('#company_id').val()},
                    async: false,
                    url: "<?php echo $base_url;?>trainee_comparison_report/ajax_companywise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var WtypeMSt = Oresult['WtypeResult'];                              
                            var wtype_option = '<option value="">All Type</option>';                           
                            for (var i = 0; i < WtypeMSt.length; i++) {
                                wtype_option += '<option value="' + WtypeMSt[i]['id'] + '">' + WtypeMSt[i]['workshop_type'] + '</option>';
                            }                           
                            $('#workshoptype_id').empty();
                            $('#workshoptype_id').append(wtype_option);
                            var WorkshopMSt = Oresult['WorkshopData'];                              
                            var workshop_option = '<option value="">Please Select</option>';                           
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
        function traineeTableData(workshop_id,RowId,trainee_id){
            var Isseleted = $( "#datatr_"+RowId ).hasClass( "selectedBox" );
                if(Isseleted){
                    $("#datatr_"+RowId ).removeClass("selectedBox");
                    $('#childdiv_'+RowId).remove();
                    return true;
                }
                $.ajax({
                    type: "POST",
                    data: {workshop_id: workshop_id,RowId:RowId,trainee_id:trainee_id},
                    async: false,
                    url: "<?php echo $base_url;?>trainee_comparison_report/ajax_traineeWiseData",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var TraineeMSt = Oresult['TraineeTable'];     
                            if(Oresult['Error']!=''){                            
                                ShowAlret(Oresult['Error'], 'error');          
                            }else{                                                                
                                $('#TraineeChart').append(TraineeMSt);
                                $('#datatr_'+RowId).addClass('selectedBox');
                            }

                        }
                    customunBlockUI();    
                    }
                });
        }        
        function ShowChart(){
            if ($('#company_id').val() == "") {
                ShowAlret("Please select Company.!!", 'error');
                return false;
            }
            if ($('#workshop_id').val() == "") {
                ShowAlret("Please select Workshop.!!", 'error');
                return false;
            }
            $.ajax({
                    type: "POST",
                    url: "<?php echo $base_url;?>trainee_comparison_report/ComparisonWorkshopTable/"+TotalWkshop,
                    data: $('#FilterFrm').serialize(),
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (Data) {
                        if (Data != '') {
                            var Oresult = jQuery.parseJSON(Data);
                            var TableData = Oresult['ChartTable'];                            
                            if(Oresult['Error']!=''){
                                ShowAlret(Oresult['Error'], 'error');
                            }else{                                                                   
                                $('table#CEtable tbody').append(TableData);
                            }
                        }
                        //$('#workshop_id').val(null).trigger('change');
                        traineeTableData($('#workshop_id').val(),TotalWkshop,$('#trainee_id').val())
                        TotalWkshop++;
                    customunBlockUI();    
                    }
                });
        }
        function RemoveChart(Row_id){
            $('#datatr_'+Row_id).remove();
            $('#childdiv_'+Row_id).remove();
        }        
        </script>
    </body>
</html>