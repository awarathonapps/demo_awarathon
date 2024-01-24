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
                                    <i class="fa fa-circle"></i>
                                    <span>Reports</span>
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Skill Build Assessment Report</span>
                                </li>
                            </ul>
                            
                        </div>
                        <div class="row mt-10">
                        <ul class="nav nav-tabs" id="tabs">
                                            <li class="active">
													<a href="#tab_trainee" data-toggle="tab">Trainee-wise Report</a>
												</li>
												<li>
													<a href="#tab_mapping_manager" data-toggle="tab">Manager-wise Report</a>
												</li>
                                                <li>
													<a href="#tab_overview" data-toggle="tab">Skill build Report</a>
												</li>
												
									</ul>
                        </div>  
                                      
                        <div class="tab-content">
                    <div class="tab-pane active" id="tab_trainee">
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="panel-group accordion" id="accordion3">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled " data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                               Report Search </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_3_2" class="panel-collapse ">
                                    <div class="panel-body" >
                                    
                                    <form id="frmReorts_trainee" name="frmReorts_trainee" method="post" action="<?php echo base_url() . 'report_status/exportReport_trainee' ?>">
                                               
                                                <div class="row margin-bottom-10">
                                                <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label"> Assessment <span class="required" aria-required="true"> * </span></label>
                                                               
                                                                <select id="assessment_id_trainee" name="assessment_id_trainee[]" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" multiple="">
																		<option value="">Please Select</option>
                                                                         <?php 
                                                                        if (isset($assessment)) {
                                                                            foreach ($assessment as $adata) {
                                                                                ?>
                                                                                <option value="<?= $adata->assessment_id; ?>"><?php echo $adata->assessment; ?></option>
                                                                              <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                </select>
                                                            
                                                        </div>
                                                    </div>
                                                      
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label"> Status &nbsp;</label>
                                                            
                                                            <select id="status_id_trainee" name="status_id_trainee" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%"> 
                                                                        
                                                                    <option value = "0"> Completed</option>
                                                                    <option value="1">Incompleted</option>                                  
                                                                    <option value="2">Overall</option> 
                                                                </select>
                                                            
                                                        </div>
                                                    </div>
                                                </div>  
                                                
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                   
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="col-md-offset-8 col-md-4 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="DatatableRefresh_trainee()">Search</button>
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter_trainee()">Reset</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                                                                
                                         
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                       <div class="col-md-12" id="report_section_trainee">
                            <div class="portlet light bordered">
                                    
                                        <div class="portlet-title">
                                            <div class="caption caption-font-24">
                                              Assessment Report 
                                              <div class="tools"> </div>
                                            </div>
                                            <?php if($acces_management->allow_export){ ?>
                                            <div class="actions">
                                                <div class="btn-group pull-right">
                                                    <button type="button" onclick="exportConfirm_trainee()" name="export_excel_trainee" id="export_excel_trainee"  class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                    &nbsp;&nbsp;
                                                </div>
                                            </div>
                                            <?php } ?>
                                               
                                        <div class="clearfix margin-top-20"></div>
                                        <div class="portlet-body">
                                        <table class="table table-bordered table-hover table-checkable order-column" id="index_table_trainee">
                                                <thead>
                                                    <tr>
                                                        <th>E code</th>
                                                        <th>Employee Name</th>
                                                        <th>Date of Join</th>
                                                        <th>Email</th>
                                                        <th>Assessment</th>
                                                        <th>Status</th>
                                                        
                                                     </tr>   
                                            </table>
                                        </div>
                                    </form>
                                </div>
                             </div>
                        </div>
                        <div class="row mt-10" id="report_section_trainee">
                           
                        </div>             
                    </div>
                    <div class="tab-pane " id="tab_overview">
                            <div class="col-md-12">

                                <div class="panel-group accordion" id="tab_overview">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled " data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                               Report Search </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_3_2" class="panel-collapse ">
                                        <div class="panel-body" >
                                    <!--        <form id="FilterFrm" name="FilterFrm" method="post">-->
                                    <form id="frmReorts" name="frmReorts" method="post" action="<?php echo base_url() . 'report_status/exportReport' ?>">

                                   
                                                <!--<?php if ($Company_id == "") { ?>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Company&nbsp;</label>
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
                                                 <?php } ?>-->
                                                <div class="row margin-bottom-10">
                                                <div class="col-md-6" >
                                                        <div class="form-group">
                                                            <label class="control-label"> Assessment <span class="required" aria-required="true"> * </span></label>

                                                            
                                                                <select id="assessment_id" name="assessment_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getAssessmentwiseData()">
																		<option value="">Please Select</option>
                                                                         <?php 
                                                                        if (isset($assessment)) {
                                                                            foreach ($assessment as $adata) {
                                                                                ?>
                                                                                <option value="<?= $adata->assessment_id; ?>"><?php echo $adata->assessment; ?></option>
                                                                              <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                </select>
                                                            
                                                        </div>
                                                    </div>
                                                      <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Employee Name &nbsp;</label>
                                                            
                                                            <select id="user_id" name="user_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%"> 
                                                                    <option value="">All Employee</option> 
                                                                    <?php 
                                                                        if (isset($user_details)) {
                                                                            foreach ($user_details as $Rdata) {
                                                                                ?>
                                                                                <option value="<?= $Rdata->user_id; ?>"><?php echo $Rdata->user_name; ?></option>
                                                                              <?php
                                                                            }
                                                                        }
                                                                        ?> 
                                                                </select>
                                                            
                                                        </div>
                                                    </div>
                                                    
                                                </div>  
                                                <div class="row margin-bottom-10">
                                                <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label"> Status &nbsp;</label>
                                                            
                                                            <select id="status_id" name="status_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%"> 
                                                                    
                                                                    <option value = "0"> Completed</option>
                                                                    <option value="1">Incompleted</option>                                  
                                                                    <option value="2">Overall</option> 
                                                                </select>
                                                            
                                                        </div>
                                                    </div>
                                                    </div>
                                               
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                    <!--    <div class="form-group">
                                                            <label class="control-label col-md-3">Device Users &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="user_id" name="user_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" >
                                                                    <option value="">All Users</option>
                                                                          <?php
                                                                        if (isset($TraineeData)) {
                                                                            foreach ($TraineeData as $Type) {
                                                                                ?>
                                                                                <option value="<?= $Type->user_id; ?>"><?php echo $Type->traineename; ?></option>
                                                                              <?php
                                                                            }
                                                                        }
                                                                        ?> 
                                                                </select>
                                                            </div>
                                                        </div>-->
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="col-md-offset-8 col-md-4 text-right">
                                        <!--                    <button type="button" class="btn blue-hoki btn-sm" onclick="DatatableRefresh()">Search</button>-->
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="refreshTableColumn()">Search</button>
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter()">Reset</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                                                                
                                        
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-10" id="report_section">
                                <div class="col-md-12">
                                <div class="portlet light bordered">
                                    
                                        <div class="portlet-title">
                                            <div class="caption caption-font-24">
                                              Assessment Report 
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
                                               
                                        <div class="clearfix margin-top-20"></div>
                                        <div class="portlet-body" id="skill-table">
                                        
                                        </div>
                                    </form>
                                </div>
                            </div>
                    </div>
                </div> 
                            
                </div>
                <!------------------------------>
                <div class="tab-pane" id="tab_mapping_manager">
                    <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="panel-group accordion" id="accordion3">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled " data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                               Report Search </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_3_2" class="panel-collapse ">
                                    <div class="panel-body" >
                                    
                                    <form id="frmReorts_manager" name="frmReorts_manager" method="post" action="<?php echo base_url() . 'report_status/exportReport_manager' ?>">
                                               
                                                <div class="row margin-bottom-10">
                                                <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label"> Assessment <span class="required" aria-required="true"> * </span></label>

                                                                <select id="assessment_id_manager" name="assessment_id_manager[]" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" multiple="">
																		<option value="">Please Select</option>
                                                                         <?php 
                                                                        if (isset($assessment)) {
                                                                            foreach ($assessment_manager as $adata) {
                                                                                ?>
                                                                                <option value="<?= $adata->assessment_id; ?>"><?php echo $adata->assessment; ?></option>
                                                                              <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                </select>
                                                           
                                                        </div>
                                                    </div>
                                                    <div class="row margin-bottom-10">
                                                <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label"> Status &nbsp;</label>
                                                            
                                                            <select id="status_id_manager" name="status_id_manager" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%"> 
                                                                    
                                                                    <option value = "0"> Completed</option>
                                                                    <option value="1">Incompleted</option>                                  
                                                                    <option value="2">Overall</option> 
                                                                </select>
                                                            
                                                        </div>
                                                    </div>
                                                    </div>
                                               
                                                    
                                                </div>  
                                               
                                               
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                   
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="col-md-offset-8 col-md-4 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="DatatableRefresh_manager()">Search</button>
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter_manager()">Reset</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                                                                
                                         
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="report_section_manager">
                                <div class="portlet light bordered">
                                    
                                        <div class="portlet-title">
                                            <div class="caption caption-font-24">
                                              Assessment Report 
                                              <div class="tools"> </div>
                                            </div>
                                            <?php if($acces_management->allow_export){ ?>
                                            <div class="actions">
                                                <div class="btn-group pull-right">
                                                    <button type="button" onclick="exportConfirm_manager()" name="export_excel_manager" id="export_excel_manager"  class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                    &nbsp;&nbsp;
                                                </div>
                                            </div>
                                            <?php } ?>
                                               
                                        <div class="clearfix margin-top-20"></div>
                                        <div class="portlet-body">
                                        <table class="table  table-bordered table-hover table-checkable order-column" id="index_table_manager">
                                                <thead>
                                                    <tr>
                                                        <th>E code</th>
                                                        <th>Employee Name</th>
                                                        <th>Email</th>
                                                        <th>Employee Status</th>
                                                        <th>Manager Name</th>
                                                        <th>Manager Status</th>
                        
                                                        <th>Assessment Name</th>
                                                      
                                                  </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </form>
                                </div>
                            </div>
                    </div>
                    <div class="row mt-10" id="report_section_manager">
                           
                </div>                                          
            </div>
        </div>
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
         
            <?php //$this->load->view('inc/inc_footer'); ?>
        
        <?php //$this->load->view('inc/inc_quick_nav'); ?>
        <?php $this->load->view('inc/inc_footer_script');?>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <script>
            jQuery(document).ready(function(){
                $('#report_section_trainee').hide();
                $('#report_section').hide();
                $('#report_section_manager').hide();
                

            });
  //          var search=1;
            var frmReorts = document.frmReorts;
            var frmReorts_manager = document.frmReorts_manager;
            var frmReorts_trainee = document.frmReorts_trainee;
            function DatatableRefresh_trainee() {
              //  console.log($("#assessment_id_trainee").val());
                if($("#assessment_id_trainee").val()==0 || $("#assessment_id_trainee").val()=="" || $("#assessment_id_trainee").val()==null){
				   ShowAlret("Please select assessment first.!!", 'error');
				   return false;
			   }
                $('#report_section_trainee').show();
       //         $('#report_section_manager').hide();
               // if (!jQuery().dataTable) {
               //     return;
               // }
                var table = $('#index_table_trainee');
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
                    
                    "bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "pageLength": 10,
                    "paging": true,
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
                        {'className': 'dt-head-left dt-body-left','width': '50px','orderable': false,'searchable': true,'targets': [0]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': false,'searchable': true,'targets': [1]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': false,'searchable': true,'targets': [2]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': false,'searchable': true,'targets': [3]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': true,'targets': [4]},
                        {'className': 'dt-head-left dt-body-left','width': '120px','orderable': false,'searchable': true,'targets': [5]},
                        
                        
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    //"serverSide": true,
                    "serverSide": false,
                    "sAjaxSource": "<?php echo base_url() . 'report_status/generate_report_trainee'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        
                        aoData.push({name: 'assessment_id_trainee', value: $('#assessment_id_trainee').val()});
                        aoData.push({name: 'status_id_trainee', value: $('#status_id_trainee').val()});
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
                        $('thead > tr> th:nth-child(1)').css({ 'min-width': '100px', 'max-width': '100px' });
                    }
                });
            }   
            function DatatableRefresh() {
                if($("#assessment_id").val()==0 || $("#assessment_id").val()==""){
				   ShowAlret("Please select assessment first.!!", 'error');
				   return false;
			   }
                $('#report_section').show();
                $('#report_section_manager').hide();
               
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
                    
                    "bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "pageLength": 10,
                    "paging": true,
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
                        {'className': 'dt-head-left dt-body-left','width': '50px','orderable': true,'searchable': true,'targets': [0]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': true,'targets': [1]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': true,'targets': [2]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': true,'targets': [3]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': false,'targets': [4]},
                        {'className': 'dt-head-left dt-body-left','width': '120px','orderable': false,'searchable': false,'targets': [5]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [6]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': true,'targets': [7]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': true,'targets': [8]},
                        {'className': 'dt-head-left dt-body-left','width': '200px','orderable': false,'searchable': true,'targets': [9]},
                        {'className': 'dt-head-left dt-body-left','width': '200px','orderable': false,'searchable': true,'targets': [10]},
                        {'className': 'dt-head-left dt-body-left','width': '200px','orderable': false,'searchable': true,'targets': [11]},
                        {'className': 'dt-head-left dt-body-left','width': '180px','orderable': false,'searchable': true,'targets': [12]},
                        {'className': 'dt-head-left dt-body-left','width': '250px','orderable': false,'searchable': false,'targets': [13]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': false,'targets': [14]},
                        {'className': 'dt-head-left dt-body-left','width': '120px','orderable': false,'searchable': false,'targets': [15]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [16]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [17]}
                        
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    //"serverSide": true,
                    "serverSide": false,
                    "sAjaxSource": "<?php echo base_url() . 'report_status/generate_report'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: 'user_id', value: $('#user_id').val()});
                        aoData.push({name: 'assessment_id', value: $('#assessment_id').val()});
                        aoData.push({name: 'status_id', value: $('#status_id').val()});
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
                        $('thead > tr> th:nth-child(1)').css({ 'min-width': '100px', 'max-width': '100px' });
                    }
                });
            }   
            function DatatableRefresh_manager() {
				 $('#report_section_manager').hide();
                 console.log($("#assessment_id_manager").val());
				if($("#assessment_id_manager").val()==0 || $("#assessment_id_manager").val()=="" || $("#assessment_id_manager").val()==null){
				   ShowAlret("Please select assessment first.!!", 'error');
				   return false;
			   }
               
                $('#report_section_manager').show();
               
                var table = $('#index_table_manager');
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
                    
                    "bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "pageLength": 10,
                    "paging": true,
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
                        
                        
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                   // "serverSide": true,
                   "serverSide": false,
                    "sAjaxSource": "<?php echo base_url() . 'report_status/generate_report_manager'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: 'manager_id', value: $('#manager_id').val()});
                        aoData.push({name: 'assessment_id_manager', value: $('#assessment_id_manager').val()});
                        aoData.push({name: 'status_id_manager', value: $('#status_id_manager').val()});
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
                        $('thead > tr> th:nth-child(1 )').css({ 'min-width': '100px', 'max-width': '100px' });
                    }
                });
            }   

            

            function ResetFilter() {
                $('.select2me,.select2_rpt2').select("val","");
                $('.select2me,.select2_rpt2').val(null).trigger('change');
                document.frmReorts.reset();
           //     DatatableRefresh();
                
            }
            function ResetFilter_manager() {
                $('.select2me,.select2_rpt2').select("val","");
                $('.select2me,.select2_rpt2').val(null).trigger('change');
                document.frmReorts_manager.reset();
         //       DatatableRefresh_manager();
            }
            function ResetFilter_trainee() {
                $('.select2me,.select2_rpt2').select("val","");
                $('.select2me,.select2_rpt2').val(null).trigger('change');
                document.frmReorts_trainee.reset();
       //         DatatableRefresh_trainee();
            }
            //DatatableRefresh();

            function exportConfirm(){
                var compnay_id =$('#company_id').val();
                var assessment_id = $('#assessment_id').val();
                
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
            function exportConfirm_manager(){
                var compnay_id =$('#company_id').val();
                var assessment_id = $('#assessment_id_manager').val();
                
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
                            frmReorts_manager.submit();
                        }
                    },
                    cancel: function () {
                         this.onClose();
                    }
                    }
                });
            }
            function exportConfirm_trainee(){
                var compnay_id =$('#company_id').val();
                var assessment_id = $('#assessment_id_trainee').val();
                
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
                            frmReorts_trainee.submit();
                        }
                    },
                    cancel: function () {
                         this.onClose();
                    }
                    }
                });
            }

            function getAssessmentwiseData(){                
               
                $.ajax({
                    type: "POST",
                    data: {assessment_id: $('#assessment_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>report_status/ajax_assessmentwise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            
                            $('#user_id').empty();
                         $('#user_id').append(Oresult['assessment_list_data']);
                        }
                        customunBlockUI();
                    }
                });
            }
            function getAssessmentwiseData_manager(){                
                $.ajax({
                   type: "POST",
                   data: {assessment_id_manager: $('#assessment_id_manager').val()},
                   //async: false,
                   url: "<?php echo $base_url; ?>report_status/ajax_assessmentwise_data_manager",
                   beforeSend: function () {
                       customBlockUI();
                   },
                   success: function (msg) {
                       if (msg != '') {
                           var Oresult = jQuery.parseJSON(msg);
                           
                           $('#manager_id').empty();
                        $('#manager_id').append(Oresult['assessment_list_data']);
                       }
                       customunBlockUI();
                   }
               });
           }
           function refreshTableColumn(){
                if($("#assessment_id").val()==0 || $("#assessment_id").val()==""){
				   ShowAlret("Please select assessment first.!!", 'error');
				   return false;
			    }
                $.ajax({
                    url : '<?= base_url() . 'report_status/generate_header'; ?>',
                    type : 'POST',
                    data : 'assessment_id=' +$("#assessment_id").val(),
                    success: function(data){
                        $('#report_section').show();
                       // alert(data);
                        
                        $('#skill-table').html(data);
                        DatatableRefresh();
                    },
                    error: function(data){
                        alert(data);
                    }
                });               
            } 
</script>
</body>
</html>