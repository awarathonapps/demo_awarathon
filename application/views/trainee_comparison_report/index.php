<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>        
        <?php $this->load->view('inc/inc_htmlhead'); ?>
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
            .scroll {
                border: 0;
                border-collapse: collapse;
            }

            .scroll tr {
                display: flex;
            }

            .scroll td {
                padding: 3px;
                flex: 1 auto;
                border: 1px solid #aaa;
                width: 1px;
                word-wrap: break;
            }

            .scroll thead tr:after {
                content: '';
                overflow-y: scroll;
                visibility: hidden;
                height: 0;
            }

            .scroll thead th {
                flex: 1 auto;
                display: block;
                border: 1px solid #000;
            }

            .scroll tbody {
                display: block;
                width: 100%;
                overflow-y: auto;
                max-height: 400px;
            }
            .selectedBox{
                /*                    background: #ffa500!important;*/
            }
            .trClickeble{
                cursor: pointer;
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
                                    <a href="javascript:;">Trainee Reports</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Trainee Comparison Report</span>
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
                        <h1 class="page-title"> Trainee Comparison Report
                            <!-- <small>- overview statistics, charts, recent workshop and reports</small> -->
                        </h1>

                        <div class="row">
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="panel-group accordion" id="accordion3">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                    Filter Set </a>
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
                                                                        <select id="company_id" name="company_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getCompanywiseData();">
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
                                                    <div class="row margin-bottom-10">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshoptype_id" name="workshoptype_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" 
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
                                                                <label class="control-label col-md-3"> Workshop Region &nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="wregion_id" name="wregion_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getTypeWiseWorkshop();">
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
                                                                <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_id" name="workshop_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" onchange="getTrainee();">
                                                                        <option value="">Please Workshop</option>
                                                                        <?php
                                                                        if (isset($WorkshopResultSet)) {
                                                                            foreach ($WorkshopResultSet as $Type) {
                                                                                ?>
                                                                                <option value="<?= $Type->workshop_id; ?>"><?php echo $Type->workshop_name; ?></option>
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
                                                    </div>
                                                    <div class="row margin-bottom-10">
                                                        <?php if ($Trainee_id == "") { ?>
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
                                                        <?php } ?>
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
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption col-lg-12 col-xs-12 col-sm-12">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Workshop Set</span>
                                            <span style='float:right;font-size:13px;font-weight:bold;color:red;'>(Add workshop set from above filter set panel)</span>
                                        </div>

                                    </div>
                                    <div class="portlet-body">
                                        <div id="AppendChart" class="table-scrollable table-scrollable-borderless" style="max-height:200px;">
                                            <table class="table table-hover table-light" id="CEtable" width="100%">
                                                <thead style="display: block;">
                                                    <tr class="uppercase">
                                                        <th width="25%">WORKSHOP NAME</th>
                                                        <th width="12%">PRE </th>
                                                        <th width="12%">POST </th>
                                                        <th width="12%">C.E</th>
                                                        <th width="9%">ACTIONS</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="display: block;height: 165px;overflow-y: auto;overflow-x: hidden;">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- STAT BOX -->
                        </div>    
                        <!-- WORKSHOP COMPARISON  -->
                        <div class="row" id="TraineeChart">

                        </div>
                        <!-- WORKSHOP COMPARISON  -->
                        <!-- STAT FIRST ROW -->

                    </div>
                <?php //$this->load->view('inc/inc_quick_sidebar');   ?>
                </div>
                <?php //$this->load->view('inc/inc_footer');  ?>
            </div>
        </div>
<?php $this->load->view('inc/inc_footer_script'); ?>
        <script>
            var FilterFrm = $('#FilterFrm');
            var form_error = $('.alert-danger', FilterFrm);
            var form_success = $('.alert-success', FilterFrm);
            var TotalWkshop = 1;
            $(".select2_rpt2").select2({
                placeholder: 'All Select',
                width: '100%'
            });
            $(".select2_rpt").select2({
                placeholder: 'Please Select',
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
                            $('#workshop_id').empty();
                            $('#workshop_id').append(Oresult['WorkshopData']);   
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
            function getTrainee(){
                $('#trainee_id').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshop_id = $('#workshop_id').val();
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id,workshop_id: workshop_id},
                    //async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_workshopwise_data",
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
            function getTypeWiseWorkshop(){
                $('#workshop_id').empty();                
                $('#wsubregion_id').empty();
                $('#workshop_subtype').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshop_type = $('#workshoptype_id').val();
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
                $('#topic_id').empty();
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
            function traineeTableData(workshop_id, RowId, trainee_id) {
                var Isseleted = $("#datatr_" + RowId).hasClass("selectedBox");
                if (Isseleted) {
                    $("#datatr_" + RowId).removeClass("selectedBox");
                    $('#childdiv_' + RowId).remove();
                    return true;
                }
                $.ajax({
                    type: "POST",
                    data: {workshop_id: workshop_id, RowId: RowId, trainee_id: trainee_id},
                    async: false,
                    url: "<?php echo $base_url; ?>trainee_comparison_report/ajax_traineeWiseData",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var TraineeMSt = Oresult['TraineeTable'];
                            if (Oresult['Error'] != '') {
                                ShowAlret(Oresult['Error'], 'error');
                            } else {
                                $('#TraineeChart').append(TraineeMSt);
                                $('#datatr_' + RowId).addClass('selectedBox');
                            }

                        }
                        customunBlockUI();
                    }
                });
            }
            function ShowChart() {
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
                    url: "<?php echo $base_url; ?>trainee_comparison_report/ComparisonWorkshopTable/" + TotalWkshop,
                    data: $('#FilterFrm').serialize(),
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (Data) {
                        if (Data != '') {
                            var Oresult = jQuery.parseJSON(Data);
                            var TableData = Oresult['ChartTable'];
                            if (Oresult['Error'] != '') {
                                ShowAlret(Oresult['Error'], 'error');
                            } else {
                                if (TableData != "") {
                                    $('table#CEtable tbody').append(TableData);
                                    traineeTableData($('#workshop_id').val(), TotalWkshop, $('#trainee_id').val());
                                    TotalWkshop++;
                                } else {
                                    ShowAlret("No Data Found for selected Workshop.!!", 'error');
                                }

                            }
                        }
                        //$('#workshop_id').val(null).trigger('change');

                        customunBlockUI();
                    }
                });
            }
            function RemoveChart(Row_id) {
                $('#datatr_' + Row_id).remove();
                $('#childdiv_' + Row_id).remove();
            }
            function remove_workshop(Row_id) {
                //$('#datatr_'+Row_id).remove();
                $("#datatr_" + Row_id).removeClass("selectedBox");
                $('#childdiv_' + Row_id).remove();
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
                    url: "<?php echo $base_url; ?>trainee_comparison_report/getTraineeData",
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