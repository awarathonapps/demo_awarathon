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
                                    <span>Assessment Reports</span>
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Assessment Trainee Report</span>
                                </li>
                            </ul>
                        </div>
						<form id="FilterFrm" name="FilterFrm" method="post" method="post" action="<?php echo base_url() . 'assessment_trainee_report/exportReport' ?>">
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
                                    <div id="collapse_3_2" class="panel-collapse collapse in">
                                        <div class="panel-body" >
                                                <?php if ($Company_id == "") { ?>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Company&nbsp;<span class="required"> * </span></label>
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
                                                <?php } ?>
                                                <div class="row margin-bottom-10">
                                                <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label" > Report Type &nbsp;<span class="required" aria-required="true"> * </span></label>
                                                                <select id="report_type_catg" name="report_type_catg" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getReportwiseData()">
                                                                    <?php echo '<option value="0">Please Select</option>';?>
                                                                    <option value="1">AI</option>
                                                                    <option value="2">Manual</option>
                                                                </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label" >Assessment &nbsp;<span class="required" aria-required="true"> * </span></label>
                                                                <select id="assessment_id" name="assessment_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getAssessmentwiseData();">
                                                                        <?php
                                                                       if (isset($AssessmentData)) {
                                                                        echo '<option value="0">Please Select</option>';
                                                                        foreach ($AssessmentData as $Adata) { ?>
                                                                            <option value="<?= $Adata->id; ?>"><?php echo $Adata->assessment; ?></option>
                                                                    <?php }} ?> 
                                                                </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                      <div class="form-group">
                                                          <label class="control-label">Parameter&nbsp;</label>
                                                              <select id="parameter_id" name="parameter_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getDesigParawiseData();">
                                                                    <?php
                                                                    if (isset($ParameterData)) {
                                                                        echo '<option value="0">All Parameter</option>';
                                                                        foreach ($ParameterData as $Pdata) { ?>
                                                                            <option value="<?= $Pdata->id; ?>"><?php echo $Pdata->description; ?></option>
                                                                    <?php }} ?>
                                                              </select>
                                                      </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                      <div class="form-group">
                                                          <label class="control-label">Designation &nbsp;</label>
                                                              <select id="designation_id" name="designation_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getDesigParawiseData();">
                                                                  <?php
                                                                    if (isset($DesignationData)) {
                                                                        echo '<option value="0">All Designation</option>';
                                                                        foreach ($DesignationData as $Rdata) { ?>
                                                                        <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->description; ?></option>
                                                                    <?php }} ?>
                                                              </select>
                                                      </div>
                                                    </div>
													
                                                </div>                                                  
                                               <div class="row margin-bottom-10">
                                               <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Trainee Region &nbsp;</label>
                                                                <select id="tregion_id" name="tregion_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" >
                                                                        <?php
                                                                        if (isset($RegionData)) {
                                                                            echo '<option value="0">All Region</option>';
                                                                            foreach ($RegionData as $Rdata) { ?>
                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
                                                                        <?php }} ?> 
                                                                </select>
                                                        </div>
                                                    </div>  
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Trainee&nbsp;</label>
                                                                <select id="user_id" name="user_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%">
                                                                    <?php 
                                                                    if(isset($TraineeData)){
                                                                        echo '<option value="0">All Trainee</option>';
                                                                        foreach ($TraineeData as $cmp) { ?>
                                                                        <option value="<?= $cmp->user_id; ?>"><?php echo $cmp->traineename; ?></option>
                                                                    <?php }} ?>
                                                                </select>
                                                        </div>
                                                    </div> 
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Result Range&nbsp;</label>
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
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Report By;</label>
                                                                <select id="report_type" name="report_type" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 50%">
                                                                    <option value="1">Parameters</option>
                                                                    <option value="2">Question wise</option>
                                                                    <option value="3">Assessment wise</option>
                                                                </select>
                                                        </div>
                                                   </div>    
                                                <div class="col-md-3 text-left">
                                                        <button type="button" class="btn blue-hoki btn-sm" onclick="SetFilter()">Search</button>
                                                        <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter()">Reset</button>
                                                </div>
                                            
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
							</div>
						</div>	
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet light bordered">
                                        <div class="portlet-title">
                                            <div class="caption caption-font-24 col-sm-6">
                                              Assessment Trainee Report
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
                                                        <th>Assessment Name</th>
                                                        <th id="dynamic_col">Parameters</th>
                                                        <th>Total Rating</th>
                                                        <th>Rating Received</th>
                                                        <th>Result</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                </div>
                            </div>
                        </div>
						</form> 
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
            var frmReorts = document.FilterFrm;
            var table = $('#index_table');
            $(".select2_rpt2").select2({
                    placeholder: 'Please Select',
                    width: '100%'
                });
            //DatatableRefresh();
            
            function getCompanywiseData(){

                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    $('#user_id').empty();
                    $('#tregion_id').empty();
                    $('#designation_id').empty();
                    $('#parameter_id').empty();
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>assessment_trainee_report/ajax_companywise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TraineeData']); 
                            $('#tregion_id').empty();
                            $('#tregion_id').append(Oresult['RegionData'])
                            $('#designation_id').empty();
                            $('#designation_id').append(Oresult['DesignationData']);
                            $('#parameter_id').empty();
                            $('#parameter_id').append(Oresult['ParameterData']);
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
            function getReportwiseData()
            {
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }     
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id, report_type_catg: $('#report_type_catg').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>assessment_trainee_report/report_wise_assessment",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            
                            $('#assessment_id').empty();
                            $('#assessment_id').append(Oresult['assessment_list_data']);
                        }
                        customunBlockUI();
                    }
                });
            }
            function getAssessmentwiseData(){
                $('#user_id').empty();
                $('#tregion_id').empty();
                $('#designation_id').empty();
                $('#parameter_id').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }     
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id,assessment_id: $('#assessment_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>assessment_trainee_report/ajax_assessmentwise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TraineeData']); 
                            $('#tregion_id').empty();
                            $('#tregion_id').append(Oresult['RegionData'])
                            $('#designation_id').empty();
                            $('#designation_id').append(Oresult['DesignationData']);
                            $('#parameter_id').empty();
                            $('#parameter_id').append(Oresult['ParameterData']);
                        }
                        customunBlockUI();
                    }
                });
            }
            function getDesigParawiseData(){
                $('#user_id').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }     
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id,assessment_id: $('#assessment_id').val(),parameter_id: $('#parameter_id').val(),designation_id: $('#designation_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>assessment_trainee_report/ajax_desigparawise_data",
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
            function DatatableRefresh() {
               // if (!jQuery().dataTable) {
               //     return;
               // }
               if($("#report_type_catg").val()==0 || $("#report_type_catg").val()==""){
                ShowAlret("Please select Report Type.!!", 'error');
				   return false;
               }
			   if($("#assessment_id").val()==0 || $("#assessment_id").val()==""){
				   ShowAlret("Please select assessment first.!!", 'error');
				   return false;
			   }
              var report_type = $('#report_type').val();
              
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
                        [0, "asc"]
                    ],
                    "pageLength": 10,
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
//                      {'width': '30px','orderable': true,'searchable': true,'targets': [0],"visible":false},
			{'className': 'dt-head-left dt-body-left','width': '130px','orderable': true,'searchable': true,'targets': [0]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': true,'targets': [1]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': true,'targets': [2]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': true,'targets': [3]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': true,'targets': [4]},
                        {'className': 'dt-head-left dt-body-left','width': '180px','orderable': true,'searchable': true,'targets': [5]},
                        {'className': 'dt-head-left dt-body-left','width': '250px','orderable': true,'searchable': true,'targets': [6],'visible': (report_type==3 ? false : true)},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': false,'targets': [7]},
                        {'className': 'dt-head-left dt-body-left','width': '90px','orderable': false,'searchable': false,'targets': [8]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': false,'targets': [9]}
                        
                    ],
                    
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'assessment_trainee_report/DatatableRefresh/'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'company_id', value: $('#company_id').val()});
                        aoData.push({name: 'user_id', value: $('#user_id').val()});
                        aoData.push({name: 'range_id', value: $('#range_id').val()});
                        aoData.push({name: 'tregion_id', value: $('#tregion_id').val()});
                        aoData.push({name: 'report_type', value: $('#report_type').val()});
                        aoData.push({name: 'parameter_id', value: $('#parameter_id').val()});
                        aoData.push({name: 'designation_id', value: $('#designation_id').val()});
                        aoData.push({name: 'assessment_id', value: $('#assessment_id').val()});
                        aoData.push({name: 'report_type_catg', value: $('#report_type_catg').val()});
                        $.getJSON(sSource, aoData, function (json) {
                            fnCallback(json);
                            
                        });
                    },
                    "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                        if(report_type==1){
                            $('#dynamic_col').text("Parameters");
                        }else if(report_type==2){
                            $('#dynamic_col').text("Question");
                        }else{    
                           $('#dynamic_col').text("");
                        }
                        return nRow;
                    }
                    , "fnFooterCallback": function (nRow, aData) {
                    },
                    "initComplete": function(settings, json) {
                        $('thead > tr> th:nth-child(1)').css({ 'min-width': '80px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(2)').css({ 'min-width': '100px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(3)').css({ 'min-width': '100px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(4)').css({ 'min-width': '100px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(5)').css({ 'min-width': '100px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(6)').css({ 'min-width': '180px', 'max-width': '150px' });
                        if(report_type !=3){
                        $('thead > tr> th:nth-child(7)').css({ 'min-width': '300px', 'max-width': '150px' });
                        }  
                        $('thead > tr> th:nth-child(8)').css({ 'min-width': '80px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(9)').css({ 'min-width': '80px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(10)').css({ 'min-width': '80px', 'max-width': '150px' });
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
				if($("#assessment_id").val()==0 || $("#assessment_id").val()==""){
				   ShowAlret("Please select assessment first.!!", 'error');
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
</script>
</body>
</html>