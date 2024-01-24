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
                                    <span>Workshop Reports</span>
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Trainee Reports</span>
                                </li>
                            </ul>
                        </div>
                        <div class="row mt-10">
                        <div class="col-md-12">
                            <div class="panel-group accordion" id="accordion3">
                                <div class="panel panel-default">

                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled <?php echo ($Company_id!="" ? 'collapsed' :''); ?>" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                               Report Search </a>
                                        </h4>
                                    </div>

                                    <div id="collapse_3_2" class="panel-collapse <?php echo ($Company_id !="" ? 'collapse' :''); ?>">
                                        <div class="panel-body" >
                                            <form id="FilterFrm" name="FilterFrm" method="post">
                                                <div class="row margin-bottom-10">
                                                    <?php if ($Company_id == "") { ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getCompanywiseData()">
                                                                    <option value="">All Company</option>
                                                                    <?php
                                                                        foreach ($CompanyData as $cmp) {?>
                                                                        <option value="<?=$cmp->id;?>"><?php echo $cmp->company_name; ?></option>
                                                                    <?php }?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php }?>
                                                </div>    
                                                <div class="row margin-bottom-10">    
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshoptype_id" name="workshoptype_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" onchange="getWTypewiseData();">
                                                                        <?php
                                                                        if (isset($WorkshopTypeData)) {
                                                                            echo '<option value="0">All Type</option>';
                                                                            foreach ($WorkshopTypeData as $WType) {
                                                                                ?>
                                                                                <option value="<?= $WType->id; ?>"><?php echo $WType->workshop_type; ?></option>
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
                                                            <label class="control-label col-md-3">Workshop Sub-Type&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshopsubtype_id" name="workshopsubtype_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getWSubTypewiseData()">
                                                                     <option value="">Select Workshop Type</option>                                                                          
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                </div>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop Region &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="region_id" name="region_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getWTypewiseData()">
                                                                        <?php
                                                                        if (isset($RegionData)) {
                                                                            echo '<option value="0">All Region</option>';
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
                                                            <label class="control-label col-md-3">Workshop Sub-Region &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="subregion_id" name="subregion_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getWSubRegionwiseData()">
                                                                    <option value="">Select SubRegion</option>                                                                           
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>    
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshop_id" name="workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getWorkshopwiseData()">
                                                                     <option value="">Select Workshop</option>
                                                                    <?php
                                                                        if (isset($WorkshopData)) {
                                                                            foreach ($WorkshopData as $WType) {
                                                                                ?>
                                                                                <option value="<?= $WType->id; ?>"><?php echo $WType->workshop_name; ?></option>
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
                                                            <label class="control-label col-md-3">Session&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="sessions" name="sessions" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%">
                                                                    <option value="">All Session</option>
                                                                    <option value="0">PRE</option>
                                                                    <option value="1">POST</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                </div>
                                                <div class="row margin-top-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Topic&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="topic_id" name="topic_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getTopicwiseData()">
                                                                    <option value="">Select Topic</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">SubTopic &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="subtopic_id" name="subtopic_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                    <option value="">All Subtopic</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row margin-top-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainer &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="trainer_id" name="trainer_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getTrainerwiseData()">
                                                                        <?php
                                                                        if (isset($TrainerData)) {
                                                                            echo '<option value="0">All Trainer</option>';
                                                                            foreach ($TrainerData as $TDype) {
                                                                                ?>
                                                                                <option value="<?= $TDype->userid; ?>"><?php echo $TDype->fullname; ?></option>
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
                                                            <label class="control-label col-md-3">Trainee Region &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="tregion_id" name="tregion_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getTrainerwiseData()">
                                                                     
                                                                          <?php
                                                                          
                                                                        if (isset($RegionData)) {
                                                                            echo '<option value="0">All Region</option>';
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
                                                </div>
                                                <div class="row margin-top-10">
                                                     <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainee &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="user_id" name="user_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" >
                                                                    
                                                                          <?php
                                                                        if (isset($TraineeData)) {
                                                                            echo '<option value="">All Trainee</option>';
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
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Designation &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="designation_id" name="designation_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%">
                                                                    <?php
                                                                          
                                                                        if (isset($DesignationData)) {
                                                                            echo '<option value="0">All Designation</option>';
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
                                                <div class="row margin-top-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Search By Result&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="result_search" name="result_search" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" >
                                                                    <option value="">All</option>
                                                                    <option value="1">Correct</option>
                                                                    <option value="2">Wrong</option>
                                                                    <option value="3">Time Out</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                </div>                                                
                                                <div class="clearfix margin-top-20"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="SetFilter()">Search</button>
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
                                    <form id="frmReorts" name="frmReorts" method="post" action="<?php echo base_url() . 'trainee_played_result/exportReport' ?>">
                                        <div class="portlet-title">
                                            <div class="caption caption-font-24">
                                                Trainee Played Results Report
                                               <div class="tools"> </div>
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
                                        <div class="clearfix margin-top-20"></div>
                                        <div class="portlet-body">
                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_table">
                                                <thead>
                                                    <tr>
                                                        <th>Trainee ID</th>
                                                        <th>Trainee Name</th>
                                                        <th>Designation</th>
                                                        <th>Workshop</th>
                                                        <th>Workshop Type</th>
                                                        <th>Workshop Sub-Type</th>
                                                        <th>Workshop Region</th>
                                                        <th>Sub-Region</th>
                                                        <th>Session</th>
                                                        <th>Question Set Name</th>
                                                        <th>Trainer Name</th>
                                                        <th>Trainee Region</th>
                                                        <th>Topic Name</th>
                                                        <th>Sub Topic Name</th>
                                                        <th>Question Id & Question Title</th>
                                                        <th>Correct Answer</th>
                                                        <th>User Answered</th>
                                                        <th>Start Date / Time</th>
                                                        <th>End Date / Time</th>
                                                        <th>Seconds</th>
                                                        <th>Timer</th>
                                                        <th>Correct/Wrong/Time Out</th>
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
            jQuery(document).ready(function() {   
                 $(".select2_rpt2").select2({
                    placeholder: 'Please Select',
                    width: '100%'
                });
                if (jQuery().datepicker) {
                    $('.date-picker').datepicker({
                        rtl: App.isRTL(),
                        orientation: "left",
                        autoclose: true,
                        format: 'dd-mm-yyyy'
                    });
                }
                DatatableRefresh();
            });
            function SetFilter(){
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    ShowAlret("Please select Company first.!!", 'error');
                    return false;
                }else{
                    DatatableRefresh();
                }
            }
            function ResetFilter() {
                $('.select2me,.select2_rpt2').select("val","");
                $('.select2me,.select2_rpt2').val(null).trigger('change');
                document.FilterFrm.reset();
                DatatableRefresh();
            }
            function DatatableRefresh() {
               // if (!jQuery().dataTable) {
               //     return;
               // }
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
                        "infoFiltered": "(filtered1 from _MAX_ total records)",
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
                        {'width': '30px','orderable': true,'searchable': true,'targets': [0]},
                        {'className': 'dt-head-left dt-body-left','width': '230px','orderable': true,'searchable': true,'targets': [1]},
                        {'className': 'dt-head-left dt-body-left','width': '250px','orderable': true,'searchable': true,'targets': [2]},
                        {'className': 'dt-head-left dt-body-left','width': '180px','orderable': true,'searchable': true,'targets': [3]},
                        {'className': 'dt-head-left dt-body-left','width': '180px','orderable': true,'searchable': true,'targets': [4]},
                        {'className': 'dt-head-left dt-body-left','width': '220px','orderable': true,'searchable': true,'targets': [5]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': true,'searchable': true,'targets': [6]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': true,'searchable': true,'targets': [7]},
                        {'className': 'dt-head-left dt-body-left','width': '120px','orderable': true,'searchable': true,'targets': [8]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [9]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [10]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [11]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [12]},
                        {'className': 'dt-head-left dt-body-left','width': '290px','orderable': false,'searchable': false,'targets': [13]},
                        {'className': 'dt-head-left dt-body-left','width': '290px','orderable': false,'searchable': false,'targets': [14]},
                        {'className': 'dt-head-left dt-body-left','width': '230px','orderable': false,'searchable': false,'targets': [15]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [16]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [17]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [18]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [19]}
                        
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'trainee_played_result/DatatableRefresh/'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'company_id', value: $('#company_id').val()});
                        aoData.push({name: 'workshoptype_id', value: $('#workshoptype_id').val()});
                        aoData.push({name: 'workshop_id', value: $('#workshop_id').val()});
                        aoData.push({name: 'sessions', value: $('#sessions').val()});
                        aoData.push({name: 'topic_id', value: $('#topic_id').val()});
                        aoData.push({name: 'subtopic_id', value: $('#subtopic_id').val()});
                        aoData.push({name: 'user_id', value: $('#user_id').val()});
                        aoData.push({name: 'result_search', value: $('#result_search').val()});
                        aoData.push({name: 'trainer_id', value: $('#trainer_id').val()});
                        aoData.push({name: 'region_id', value: $('#region_id').val()});
                        aoData.push({name: 'tregion_id', value: $('#tregion_id').val()});
                        aoData.push({name: 'workshopsubtype_id', value: $('#workshopsubtype_id').val()});
                        aoData.push({name: 'subregion_id', value: $('#subregion_id').val()});
                        aoData.push({name: 'designation_id', value: $('#designation_id').val()});
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
                        $('thead > tr> th:nth-child(1)').css({ 'min-width': '80px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(2)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(3)').css({ 'min-width': '180px', 'max-width': '200px' });
                        $('thead > tr> th:nth-child(4)').css({ 'min-width': '150px', 'max-width': '200px' });
                        $('thead > tr> th:nth-child(5)').css({ 'min-width': '100px', 'max-width': '200px' });
                        $('thead > tr> th:nth-child(6)').css({ 'min-width': '80px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(7)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(8)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(9)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(10)').css({ 'min-width': '150px', 'max-width': '400px' });
                        $('thead > tr> th:nth-child(11)').css({ 'min-width': '120px', 'max-width': '200px' });
                        $('thead > tr> th:nth-child(12)').css({ 'min-width': '120px', 'max-width': '200px' });
                        $('thead > tr> th:nth-child(13)').css({ 'min-width': '120px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(14)').css({ 'min-width': '180px', 'max-width': '120px' });
                        $('thead > tr> th:nth-child(15)').css({ 'min-width': '150px', 'max-width': '80px' });
                        $('thead > tr> th:nth-child(16)').css({ 'min-width': '150px', 'max-width': '80px' });
                        $('thead > tr> th:nth-child(17)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(18)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(19)').css({ 'min-width': '150px', 'max-width': '150px' });
                    }
                });
            }
            function exportConfirm(){
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    ShowAlret("Please select Company first.!!", 'error');
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
                            frmReorts.submit();
                        }
                    },
                    cancel: function () {
                         this.onClose();
                    }
                    }
                });
            }
            function getCompanywiseData(){
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    $('#trainer_id').empty();
                    $('#user_id').empty();
                    $('#topic_id').empty();
                    $('#subtopic_id').empty();
                    $('#region_id').empty();
                    $('#tregion_id').empty();
                    $('#workshop_id').empty();
                    $('#workshoptype_id').empty();
                    $('#designation_id').empty();
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
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TraineeData']);
                            $('#region_id').empty();
                            $('#region_id').append(Oresult['RegionData']);
                            $('#workshoptype_id').empty();
                            $('#workshoptype_id').append(Oresult['WTypeData']);
                            $('#workshop_id').empty();
                            $('#workshop_id').append(Oresult['WorkshopData']); 
                            $('#trainer_id').empty();
                            $('#trainer_id').append(Oresult['TrainerData']);
                            $('#tregion_id').empty();
                            $('#tregion_id').append(Oresult['RegionData']);
                            $('#designation_id').empty();
                            $('#designation_id').append(Oresult['DesignationData']);
                        }
                        customunBlockUI();
                    }
                });
            }
            function getWSubTypewiseData(){
                $('#topic_id').empty();
                $('#subtopic_id').empty();
                $('#workshop_id').empty();                                
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshopsubtype_id = $('#workshopsubtype_id').val();
                var workshoptype_id = $('#workshoptype_id').val();
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
            function getWSubRegionwiseData(){
                $('#topic_id').empty();
                $('#subtopic_id').empty();
                $('#workshop_id').empty();                                
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var subregion_id = $('#subregion_id').val();
                var workshopsubtype_id = $('#workshopsubtype_id').val();
                var workshoptype_id = $('#workshoptype_id').val();
                var region_id       = $('#region_id').val();                
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
            function getWTypewiseData(){
                $('#topic_id').empty();
                $('#subtopic_id').empty();
                $('#workshop_id').empty();                
                $('#subregion_id').empty();
                $('#workshopsubtype_id').empty();
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshopsubtype_id = $('#workshopsubtype_id').val();
                var workshoptype_id = $('#workshoptype_id').val();
                var region_id       = $('#region_id').val();                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),workshoptype_id: workshoptype_id,region_id:region_id,workshopsubtype_id:workshopsubtype_id},
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
                            $('#workshopsubtype_id').empty();
                            $('#workshopsubtype_id').append(Oresult['WorkshopSubtypeData']);
                            $('#subregion_id').empty();
                            $('#subregion_id').append(Oresult['WorkshopSubregionData']);
                        }
                        customunBlockUI();
                    }
                });
                
            }
            function getWorkshopwiseData(){
                $('#topic_id').empty();
                $('#subtopic_id').empty();
                $('#trainer_id').empty();     
                 var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshop_id = $('#workshop_id').val();                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),workshop_id: workshop_id},
                    //async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_workshopwise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#topic_id').empty();
                            $('#topic_id').append(Oresult['TopicData']);
                            $('#trainer_id').empty();
                            $('#trainer_id').append(Oresult['TrainerData']);
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TraineeData']);
                        }
                        customunBlockUI();
                    }
                });
            }
            function getTopicwiseData(){
                 var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                $('#subtopic_id').empty();                
                $.ajax({
                    type: "POST",
                    data: {topic_id: $('#topic_id').val(),company_id: $('#company_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_topicwise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#subtopic_id').empty();
                            $('#subtopic_id').append(Oresult['SubTopicData']);
                        }
                    customunBlockUI();   
                    }
                });
            }
            function getTrainerwiseData(){
                var trainer_id  = $('#trainer_id').val();
                var tregion_id  = $('#tregion_id').val();
                 var compnay_id =$('#company_id').val();
                 var workshoptype_id = $('#workshoptype_id').val();
                if(compnay_id==""){
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id,trainer_id:trainer_id,tregion_id:tregion_id,workshop_type: workshoptype_id},
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
</script>
</body>
</html>