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
                                    <i class="fa fa-circle"></i>
                                    <span>Workshop Reports</span>
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Trainer Consolidated Reports</span>
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

                                    <div id="collapse_3_2" class="panel-collapse <?php echo ($Company_id!="" ? 'collapse' :''); ?>">
                                        <div class="panel-body" >
                                            <form id="FilterFrm" name="FilterFrm" method="post">
                                                <div class="row margin-bottom-10">
                                                    <?php if ($Company_id == "") { ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getCompanywiseData();">
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
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainer &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="trainer_id" name="trainer_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%">
                                                                   
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
                                                                                                      
                                                </div>
                                                <div class="row margin-top-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="wtype_id" name="wtype_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" onchange="getWTypewiseData();">
                                                                    
                                                                    <?php
                                                                        if (isset($WTypeData)) {
                                                                            echo '<option value="0">All Type</option>';
                                                                            foreach ($WTypeData as $WRType) {
                                                                                ?>
                                                                                <option value="<?= $WRType->id; ?>"><?php echo $WRType->workshop_type; ?></option>
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
                                                                <select id="workshop_subtype" name="workshop_subtype" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getWSubTypewiseData();">
                                                                    <option value="">All Sub-type</option>

                                                                </select>
                                                                </div>
                                                            </div>
                                                    </div>
                                                </div>
                                                <div class="row margin-top-10">
                                                    
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop Region &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="region_id" name="region_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getWTypewiseData();">
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
                                                            <label class="control-label col-md-3">Workshop&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshop_id" name="workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getWorkshopwiseData();">
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
                                                                <select id="session_id" name="session_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%">
                                                                    <option value="">Select Session</option>
                                                                    <option value="PRE">PRE</option>
                                                                    <option value="POST">POST</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row margin-top-10">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-4">Topic&nbsp;</label>
                                                            <div class="col-md-8" style="padding:0px;">
                                                                <select id="topic_id" name="topic_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getTopicwiseData();">
                                                                    <option value="">Select Topic</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-4">SubTopic &nbsp;</label>
                                                            <div class="col-md-8" style="padding:0px;">
                                                                <select id="subtopic_id" name="subtopic_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                    <option value="">All Subtopic</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Result Range&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="result_range" name="result_range" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                    <option value="">All</option>
                                                                    <option value="0-10">0-10%</option>
                                                                    <option value="10-20">10-20%</option>
                                                                    <option value="20-30">20-30%</option>
                                                                    <option value="30-40">30-40%</option>
                                                                    <option value="40-50">40-50%</option>
                                                                    <option value="50-60">50-60%</option>
                                                                    <option value="60-70">60-70%</option>
                                                                    <option value="70-80">70-80%</option>
                                                                    <option value="80-90">80-90%</option>
                                                                    <option value="90-100">90-100%</option>
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
                                    <form id="frmReorts" name="frmReorts" method="post" action="<?php echo base_url() . 'trainer_consolidated_report/exportReport' ?>">
                                        <div class="portlet-title">
                                            <div class="caption caption-font-24">
                                                Trainer Consolidated Report
                                               <div class="tools"> </div>
                                            </div>
                                            <?php if($acces_management->allow_export){ ?>
                                            <div class="actions">
                                                <div class="btn-group pull-right">
                                                    <button type="button" onclick="exportConfirm()"
                                                    autofocus="" accesskey="" name="export_excel" id="export_excel"  class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
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
                                                        <th>Workshop Region</th>
                                                        <th>Workshop Sub-region</th>
                                                        <th>Workshop type</th>
                                                        <th>Workshop Sub-type</th>
                                                        <th>Workshop Name</th>
                                                        <th>Trainer name</th>
                                                        <th>Topics</th>
                                                        <th>Subtopic</th>
                                                        <th>No of Unique Questions</th>
                                                        <th>No of Trainee played</th>
                                                        <th>Total Questions played</th>
                                                        <th>Total Correct answers</th>
                                                        <th>Result %</th>
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
                DatatableRefresh();
                $(".select2_rpt2").select2({
                    placeholder: 'Please Select',
                    width: '100%'
                });
//                getCompanywiseData();
            });
            function ResetFilter() {
                $('.select2me,.select2_rpt2').select("val","");
                $('.select2me,.select2_rpt2').val(null).trigger('change');
                document.FilterFrm.reset();
                DatatableRefresh();
            }
            function SetFilter(){
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    ShowAlret("Please select Company first.!!", 'error');
                    return false;
                }else{
                    DatatableRefresh();
                }
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
                        {'className': 'dt-head-left dt-body-left','width': '230px','orderable': true,'searchable': true,'targets': [0]},
                        {'className': 'dt-head-left dt-body-left','width': '250px','orderable': false,'searchable': true,'targets': [1]},
                        {'className': 'dt-head-left dt-body-left','width': '250px','orderable': true,'searchable': true,'targets': [2]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': true,'searchable': true,'targets': [3]},
                        {'className': 'dt-head-left dt-body-left','width': '120px','orderable': false,'searchable': true,'targets': [4]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': true,'targets': [5]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [6]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': false,'targets': [7]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': false,'targets': [8]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': false,'targets': [9]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': false,'targets': [10]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': false,'targets': [11]}
                    ],
                    "order": [
                        [1, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'trainer_consolidated_report/DatatableRefresh/'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'company_id', value: $('#company_id').val()});
                        aoData.push({name: 'workshop_id', value: $('#workshop_id').val()});
                        aoData.push({name: 'wtype_id', value: $('#wtype_id').val()});
                        aoData.push({name: 'topic_id', value: $('#topic_id').val()});
                        aoData.push({name: 'subtopic_id', value: $('#subtopic_id').val()});
                        aoData.push({name: 'region_id', value: $('#region_id').val()});                        
                        aoData.push({name: 'trainer_id', value: $('#trainer_id').val()});
                        aoData.push({name: 'result_range', value: $('#result_range').val()});
                        aoData.push({name: 'wsubregion_id', value: $('#wsubregion_id').val()});
                        aoData.push({name: 'workshop_subtype', value: $('#workshop_subtype').val()});
						aoData.push({name: 'session_id', value: $('#session_id').val()});
//                        aoData.push({name: 'from_range', value: $('#from_range').val()});
//                        aoData.push({name: 'to_range', value: $('#to_range').val()});
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
                        $('thead > tr> th:nth-child(1)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(2)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(3)').css({ 'min-width': '200px', 'max-width': '200px' });
                        $('thead > tr> th:nth-child(4)').css({ 'min-width': '60px', 'max-width': '200px' });
                        $('thead > tr> th:nth-child(5)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(6)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(7)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(8)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(9)').css({ 'min-width': '200px', 'max-width': '200px' });
                        $('thead > tr> th:nth-child(10)').css({ 'min-width': '200px', 'max-width': '200px' });
                        $('thead > tr> th:nth-child(11)').css({ 'min-width': '200px', 'max-width': '200px' });
                        $('thead > tr> th:nth-child(12)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(13)').css({ 'min-width': '120px', 'max-width': '120px' });
                        $('thead > tr> th:nth-child(14)').css({ 'min-width': '80px', 'max-width': '80px' });
                        $('thead > tr> th:nth-child(15)').css({ 'min-width': '80px', 'max-width': '80px' });
                        $('thead > tr> th:nth-child(16)').css({ 'min-width': '150px', 'max-width': '150px' });
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
                    $('#workshop_id').empty();
                    $('#region_id').empty();
                    $('#tregion_id').empty();
                    $('#wtype_id').empty();
                    $('#topic_id').empty();
                    $('#subtopic_id').empty();
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
                            $('#trainer_id').empty();
                            $('#trainer_id').append(Oresult['TrainerData']); 
                            $('#workshop_id').empty();
                            $('#workshop_id').append(Oresult['WorkshopData']);   
                            $('#region_id').empty();
                            $('#region_id').append(Oresult['RegionData']);
                            $('#wtype_id').empty();
                            $('#wtype_id').append(Oresult['WTypeData']);
                        }
                        customunBlockUI();
                    }
                });
            }
            function getWTypewiseData(){
                $('#topic_id').empty();
                $('#subtopic_id').empty();
                $('#workshop_id').empty();                
                $('#wsubregion_id').empty();
                $('#workshop_subtype').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshop_type = $('#wtype_id').val();
                var workshop_region = $('#region_id').val();
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
                $('#subtopic_id').empty();
                $('#workshop_id').empty();                                
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshopsubtype_id = $('#workshop_subtype').val();
                var workshoptype_id = $('#wtype_id').val();
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
             function getWorkshopwiseData(){
                $('#topic_id').empty();
                $('#subtopic_id').empty();
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
                            $('#topic_id').empty();
                            $('#topic_id').append(Oresult['TopicData']);
                        }
                        customunBlockUI();
                    }
                });
            }
            function getTopicwiseData(){
                $('#subtopic_id').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {topic_id: $('#topic_id').val(),company_id: $('#company_id').val()},
                    async: false,
                    url: "<?php echo $base_url; ?>trainer_consolidated_report/ajax_topicwise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            $('#subtopic_id').empty();
                            $('#subtopic_id').append(msg);
                        }
                    customunBlockUI();   
                    }
                });
            }
</script>
</body>
</html>