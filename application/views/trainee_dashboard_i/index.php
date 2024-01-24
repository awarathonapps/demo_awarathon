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
                                    <a href="javascript:;">Trainee Reports</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Trainee Workshop</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                            </div>
                        </div>
                        <!-- PAGE BAR -->
                        <h1 class="page-title"> Trainee Workshop
                            <!-- <small>- overview statistics, charts, recent workshop and reports</small> -->
                        </h1>
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
                                                <form id="FilterFrm" name="FilterFrm" method="post">
                                            
                                                <div class="row margin-bottom-10">
                                                    <?php if ($company_id == "") { ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%" onchange="getCompanyWorkshopType();">
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
                                                    <?php if ($login_type != 3) { ?>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Trainee Name&nbsp;<span class="required"> * </span></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="trainee_id" name="trainee_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%"  >
                                                                        <option value="">Select Trainer</option>
                                                                        <?php
                                                                        if (isset($Trainee)) {
                                                                            foreach ($Trainee as $Type) {
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
                                                    <?php } ?> 
                                                <div class="row margin-bottom-10"></div>
                                                <div class="col-md-6 <?php echo ($company_id == "" ? 'margin-top-20':''); ?>">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                        <div class="col-md-9" style="padding:0px;">
                                                            <select id="workshoptype_id" name="workshoptype_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%" onchange="getWTypewiseData();">
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
                                                 
                                                <div class="col-md-6 <?php echo ($company_id == "" ? 'margin-top-10':''); ?>">       
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop Sub-type</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                            <select id="workshop_subtype" name="workshop_subtype" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" >
                                                                <option value="">All Sub-type</option>

                                                            </select>
                                                            </div>
                                                        </div>
                                                </div>
                                                <div class="col-md-6 <?php echo ($company_id == "" ? 'margin-top-20':''); ?>">
                                                         <div class="form-group">
                                                             <label class="control-label col-md-3"> Workshop Region &nbsp;</label>
                                                             <div class="col-md-9" style="padding:0px;">
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
                                                <div class="col-md-6 <?php echo ($company_id == "" ? 'margin-top-10':''); ?>">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Workshop Sub-region &nbsp;</label>
                                                        <div class="col-md-9" style="padding:0px;">
                                                            <select id="wsubregion_id" name="wsubregion_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" >
                                                                <option value="">Select Sub-region</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                 </div>   
                                        </div>
                                            <?php echo ($company_id != "" ? '<div class="clearfix margin-top-20"></div>':''); ?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-offset-10 col-md-2 text-right">
                                                        <button type="button" class="btn blue-hoki btn-sm" onclick="TableRefresh()">Search</button>
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
                                            <table class="table table-hover table-light" id="table_index">
                                                <thead>
                                                    <tr class="uppercase">
                                                    <th>Workshop Date</th>
                                                    <th>Workshop Name</th>
                                                    <th>No. Of Topics</th>
                                                    <th>AVG Post</th>
                                                    <th>AVG Response Time</th>
                                                    <th width="28%">Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                    </div>
                                </div>
                            </div>
                            <!-- STAT BOX -->

                            


                            <!-- CHART MODAL -->
                            <div class="modal fade bs-modal-lg modal-scroll in" id="LoadModalFilter" role="basic" aria-hidden="true" data-width="550">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                                            <span>
                                                &nbsp;&nbsp;Loading... </span>
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
            var DefaultTrainee = '<?php echo $DefaultTrainee_id ?>';
            jQuery(document).ready(function () {
                TableRefresh(true);
            });
             $(".select2_rpt").select2({
                placeholder: 'All Select',
                width: '100%'
            });
            function TableRefresh(firstTimeLoad) {
                var trainee_id = $('#trainee_id').val();
                var company_id = $('#company_id').val();
                if (firstTimeLoad == undefined) {
                    if (company_id == "") {
                        ShowAlret("Please select Company first.!!", 'error');
                        return false;
                    }
                    if (trainee_id == "") {
                        ShowAlret("Please select Trainee.!!", 'error');
                        return false;
                    }
                } else {
                    trainee_id = DefaultTrainee;
                }
                var table = $('#table_index');
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
                        {'width': '80px', 'orderable': true, 'searchable': true, 'targets': [0]},
                        {'width': '200px', 'orderable': true, 'searchable': true, 'targets': [1]},
                        {'width': '80px', 'orderable': false, 'searchable': false, 'targets': [2]},
                        {'width': '80px', 'orderable': false, 'searchable': false, 'targets': [3]},
                        {'width': '60px', 'orderable': false, 'searchable': false, 'targets': [4]},
                        {'width': '60px', 'orderable': false, 'searchable': false, 'targets': [5]}
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'trainee_dashboard_i/ajax_getTraineeData'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'company_id', value: $('#company_id').val()});
                        aoData.push({name: 'trainee_id', value: $('#trainee_id').val()});
                        aoData.push({name: 'workshoptype_id', value: $('#workshoptype_id').val()});
                        aoData.push({name: 'wregion_id', value: $('#wregion_id').val()});
                        aoData.push({name: 'wsubregion_id', value: $('#wsubregion_id').val()});
                        aoData.push({name: 'workshop_subtype', value: $('#workshop_subtype').val()});
                        
                        $.getJSON(sSource, aoData, function (json) {
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
            function workshop_summary(workshop_id) {
                var company_id = $('#company_id').val();
                var trainee_id = $('#trainee_id').val();
                var workshoptype_id = $('#workshoptype_id').val();
                $.ajax({
                    type: "POST",
                    url: "<?php echo $base_url; ?>trainee_dashboard_i/ajax_chart",
                    data: {workshop_id: workshop_id, company_id: company_id, trainee_id: trainee_id, workshoptype_id: workshoptype_id},
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (Data) {
                        if (Data != '') {
                            var Oresult = jQuery.parseJSON(Data);
                            var DataMSt = Oresult['ChartDataHtml'];
                            if (Oresult['Error'] != '') {
                                $('#errordiv').show();
                                $('#errorlog').html(Oresult['Error']);
                                App.scrollTo(form_error, -200);
                            } else {
                               // console.log(DataMSt);
                                //$('#AppendChart').append(DataMSt);                                  
                            }
                        }
                        customunBlockUI();
                    }
                });

            }
            function ResetFilter() {
                $('.select2me').val(null).trigger('change');
                document.FilterFrm.reset();
                TableRefresh(true);
            }

            function getCompanyWorkshopType(){
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    $('#workshoptype_id').empty();
                    $('#wregion_id').empty();
                    $('#trainee_id').empty();
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
            function getWTypewiseData(){              
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
            function getTRegionwiseData(){ 
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id,trainee_region_id:$('#trainee_region_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>trainee_dashboard_i/getTraineeData",
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