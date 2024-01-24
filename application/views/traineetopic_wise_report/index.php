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
            .table-scrollable>.table>tbody>tr>th, .table-scrollable>.table>tfoot>tr>td, .table-scrollable>.table>tfoot>tr>th, .table-scrollable>.table>thead>tr>th {
    white-space: normal;
}
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
                                    <span>Trainee Report (Topic+ Questions Set)</span>
                                </li>
                            </ul>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="panel-group accordion" id="accordion3">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled <?php echo ($Company_id !="" ? 'collapsed' :''); ?>" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                               Report Search </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_3_2" class="panel-collapse <?php echo ($Company_id !="" ? 'collapse' :''); ?>">
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
                                                                <label class="control-label col-md-3">Workshop Type</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshop_type" name="workshop_type" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" onchange="getWTypewiseData();">
                                                                    
                                                                    <?php if(count((array)$WTypeData)>0){
                                                                        echo '<option value="0">All Type</option>';
                                                                        foreach ($WTypeData as $Rgn) { ?>
                                                                        <option value="<?= $Rgn->id; ?>" ><?php echo $Rgn->workshop_type; ?></option>
                                                                    <?php } }?>
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
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3"> Workshop Region &nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="wregion_id" name="wregion_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getWTypewiseData();">
                                                                              <?php
                                                                            if (isset($RegionData)) {
                                                                                echo '<option value="0">All Region</option>';
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
                                                                <select id="workshop_id" name="workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getWorkshopwiseData();" >
                                                                    <option value="">All Workshop</option>
                                                                    <?php 
                                                                    if(isset($WorkshopData)){
                                                                        foreach ($WorkshopData as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"><?php echo $cmp->workshop_name; ?></option>
                                                                    <?php }} ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                     </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainee Region &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="tregion_id" name="tregion_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getTregionwiseData();" >
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
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainee&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="user_id" name="user_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%">
                                                                    <option value="">All Trainee</option>
                                                                    <?php 
                                                                    if(isset($TraineeData)){
                                                                        foreach ($TraineeData as $cmp) { ?>
                                                                        <option value="<?= $cmp->user_id; ?>"><?php echo $cmp->traineename; ?></option>
                                                                    <?php }} ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                     <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Topics&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="topic_id" name="topic_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%">
                                                                    <option value="">Select</option>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                     </div>
                                                </div>                                               
                                               <div class="row margin-bottom-10">
                                               <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Result Range&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="range_id" name="range_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 50%">
                                                                    <option value="">Select</option>
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
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Session&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshop_session" name="workshop_session" class="form-control input-sm select2me" placeholder="Please select" style="width: 50%"  >
                                                                    <option value="">All</option>
                                                                    <option value="PRE">PRE</option>
                                                                    <option value="POST">POST</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Designation &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="designation_id" name="designation_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%">
                                                                    <?php
                                                                		if (isset($DesignationData)) {
                                                                		    echo '<option value="0">All Designation</option>';
                                                                			foreach ($DesignationData as $Rdata) { ?>
                                                                			    <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->description; ?></option>
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
                                                            <label class="control-label col-md-3">Report By;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="report_type" name="report_type" class="form-control input-sm " placeholder="Please select"  style="width: 50%">
                                                                    <option value="1">Topic wise</option>
                                                                    <option value="2">Question set wise</option>
                                                                    <option value="3">Workshop wise</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                   </div>    
                                                </div>
                                                <div class="clearfix margin-top-10"></div>
                                                <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="SetFilter()">Search</button>
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter()">Reset</button>
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
                                    <form id="frmReorts" name="frmReorts" method="post" action="<?php echo base_url() . 'traineetopic_wise_report/exportReport' ?>">
                                        <div class="portlet-title">
                                            <div class="caption caption-font-24">
                                              Trainee Report (Topic+ Questions Set)
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
                                                        <th>Trainee ID</th>
														<th>Employee ID</th>
                                                        <th>Trainee Region</th>
                                                        <th>Trainee Name</th>
                                                        <th>Designation</th>
                                                        <th>Workshop Region</th>
                                                        <th>Workshop Sub-region</th>
                                                        <th>Workshop type</th>
                                                        <th>Workshop Sub-type</th>
                                                        <th>Workshop Name</th>
                                                        <th id="dynamic_col">Topics</th>
                                                        <th>Questions Played</th>
                                                        <th>Correct</th>
                                                        <th>Wrong</th>
                                                        <th>Result</th>
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
            var table = $('#index_table');
            $(".select2_rpt2").select2({
                    placeholder: 'Please Select',
                    width: '100%'
                });
            DatatableRefresh();
            
            function getCompanywiseData(){

                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    $('#user_id').empty();
                    $('#workshop_id').empty();
                    $('#wregion_id').empty();
                    $('#tregion_id').empty();
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
                            $('#workshop_id').empty();
                            $('#workshop_id').append(Oresult['WorkshopData']);   
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TraineeData']); 
                            $('#wregion_id').empty();
                            $('#wregion_id').append(Oresult['RegionData']);
                            $('#tregion_id').empty();
                            $('#tregion_id').append(Oresult['RegionData'])
                            $('#workshop_type').empty();
                            $('#workshop_type').append(Oresult['WTypeData']);
                            $('#designation_id').empty();
                            $('#designation_id').append(Oresult['DesignationData']);
                        }
                        customunBlockUI();
                    }
                });
            }
            function ResetFilter() {
                $('.select2me,.select2_rpt2').select("val","");
                $('.select2me,.select2_rpt2').val(null).trigger('change');
                document.FilterFrm.reset();
                DatatableRefresh();
            }
            function getTregionwiseData(){
               $('#user_id').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }     
                var region_id = $('#region_id').val();
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id,region_id: $('#tregion_id').val(),workshop_type:$('#workshop_type').val()},
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
            function getWorkshopwiseData(){
                $('#topic_id').empty();
                $('#user_id').empty();
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
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TraineeData']);
                        }
                        customunBlockUI();
                    }
                });
            }
            function getWTypewiseData(){
                $('#topic_id').empty();
                $('#workshop_id').empty();                
                $('#wsubregion_id').empty();
                $('#workshop_subtype').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshop_type = $('#workshop_type').val();
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
                var workshoptype_id = $('#workshop_type').val();
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
            function DatatableRefresh() {
               // if (!jQuery().dataTable) {
               //     return;
               // }
              var report_type = $('#report_type').val();
              if(report_type==1){
                  $('#dynamic_col').text("Topic");
              }else if(report_type==2){
                  $('#dynamic_col').text("Question Set");
              }else{
                $('#dynamic_col').text("NO OF QUESTION SET");
              }
//                                if(report_type==3){
//                                     // Get the column API object
//                                     var column = table.column(5);
//                                     column.visible(! column.visible());
//                                }
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
                        "infoFiltered": "(filtered 1 from _MAX_ total records)",
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
                    //"bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "order": [
                        [5, "asc"]
                    ],
                    "pageLength": 10,
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
//                      {'width': '30px','orderable': true,'searchable': true,'targets': [0],"visible":false},
						{'className': 'dt-head-left dt-body-left','width': '130px','orderable': true,'searchable': true,'targets': [0]},
                        {'className': 'dt-head-left dt-body-left','width': '200px','orderable': false,'searchable': true,'targets': [1]},
                        {'className': 'dt-head-left dt-body-left','width': '200px','orderable': true,'searchable': true,'targets': [2]},
                        {'className': 'dt-head-left dt-body-left','width': '180px','orderable': true,'searchable': true,'targets': [3]},
                        {'className': 'dt-head-left dt-body-left','width': '150px','orderable': false,'searchable': true,'targets': [4]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': true,'searchable': true,'targets': [5]},
                        {'className': 'dt-head-left dt-body-left','width': '120px','orderable': true,'searchable': true,'targets': [6]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': true,'searchable': true,'targets': [7]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': true,'searchable': true,'targets': [8]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [9]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [10]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [11]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [12]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [13]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [14]}
                    ],
                    
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'traineetopic_wise_report/DatatableRefresh/'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'company_id', value: $('#company_id').val()});
                        aoData.push({name: 'user_id', value: $('#user_id').val()});
                        aoData.push({name: 'workshop_id', value: $('#workshop_id').val()});
                        aoData.push({name: 'topic_id', value: $('#topic_id').val()});
                        aoData.push({name: 'range_id', value: $('#range_id').val()});
                        aoData.push({name: 'wregion_id', value: $('#wregion_id').val()});
                        aoData.push({name: 'tregion_id', value: $('#tregion_id').val()});
                        aoData.push({name: 'workshop_type', value: $('#workshop_type').val()});
                        aoData.push({name: 'workshop_session', value: $('#workshop_session').val()});
                        aoData.push({name: 'report_type', value: $('#report_type').val()});
                        aoData.push({name: 'workshop_session', value: $('#workshop_session').val()});
                        aoData.push({name: 'report_type', value: $('#report_type').val()});
                        aoData.push({name: 'wsubregion_id', value: $('#wsubregion_id').val()});
                        aoData.push({name: 'workshop_subtype', value: $('#workshop_subtype').val()});
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
                        $('thead > tr> th:nth-child(2)').css({ 'min-width': '100px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(3)').css({ 'min-width': '120px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(4)').css({ 'min-width': '150px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(5)').css({ 'min-width': '120px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(6)').css({ 'min-width': '120px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(7)').css({ 'min-width': '100px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(8)').css({ 'min-width': '100px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(9)').css({ 'min-width': '80px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(10)').css({ 'min-width': '80px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(11)').css({ 'min-width': '80px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(12)').css({ 'min-width': '80px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(13)').css({ 'min-width': '80px', 'max-width': '150px' });
						$('thead > tr> th:nth-child(14)').css({ 'min-width': '80px', 'max-width': '150px' });
                    }
                });
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
              function exportConfirm(){
              var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    ShowAlret("Please select Company first.!!", 'error');
                    return false;
                }
//                var me = $('#company_id').val();
                $.confirm({
                    title: 'Confirm!',
                    content: "Are you sure want to Export. ? ",    
                    buttons: {
                        confirm:{
                        text: 'Confirm',
                        btnClass: 'btn-primary',
                        keys: ['enter', 'shift'],
                        action: function(){
//                            console.log(company_id);
                            frmReorts.submit();
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