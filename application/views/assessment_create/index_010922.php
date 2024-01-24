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
        <style>
            th{vertical-align: middle!important; text-align: center;}            
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
                                    <span>Assessment</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Assessment Listing</span>
                                </li>
                            </ul>
                            <?php if($acces_management->allow_add){ ?>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url.'assessment_create/create_assessment' ;?>" class="btn btn-sm btn-orange pull-right">Create New</a>&nbsp;
                            </div>
                            <?php } ?>
                        </div>
                        <div class="row mt-10">
                        <div class="col-md-12">
                            <div class="panel-group accordion" id="accordion3">
                                <div class="panel panel-default">

                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                Advanced Search </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_3_2" class="panel-collapse collapse">
                                        <div class="panel-body" >
                                            <form id="FilterFrm" name="FilterFrm" method="post" > 
                                                <div class="row">
                                                    <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Assessment Type</label>
                                                                <select name="assessment_type" id="assessment_type" class="form-control input-sm select2">
                                                                    <option value="" selected="">All <option>
                                                                    <?php foreach ($assessment_type as $val) { ?>
                                                                        <option value="<?php echo $val->id ?>" ><?php echo $val->description ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
													<div class="col-md-3">    
														<div class="form-group">
															<label>Question type</label>
															<select id="question_type" name="question_type" class="form-control input-sm select2" placeholder="Please select">
																<option value="" selected="">All <option>
																<option value="0" >Question</option>
																<option value="1">Situation</option>
															</select>
														</div>
													</div>	
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="">Status&nbsp;</label>                                                            
                                                                <select id="filter_status" name="filter_status" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%">
                                                                    <option value="">All Status</option>
                                                                    <option value="1">Live</option>
                                                                    <option value="2">Expired</option>
                                                                    <option value="3">Active</option>
                                                                    <option value="4">In-Active</option>
                                                                </select>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="DatatableRefresh()">Search</button>
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
                                <?php if ($this->session->flashdata('flash_message')) { ?> 
                                    <div class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                        <?php echo $this->session->flashdata('flash_message'); ?>
                                    </div>
                                <?php } ?>
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            Manage Assessment
                                        <div class="tools"> </div>  
                                        </div>
                                        <?php if ($acces_management->allow_edit){?>
                                        <div class="actions">
                                            <div class="btn-group pull-right">
                                                <button type="button" class="btn orange btn-sm btn-outline dropdown-toggle" data-toggle="dropdown">Bulk Actions
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu pull-right" role="menu">
                                                
                                                    <li>
                                                        <a id="bulk_active" href="javascript:;" onclick="ValidCheckbox(1)">
                                                            <i class="fa fa-check"></i> Active
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a id="bulk_inactive" href="javascript:;" onclick="ValidCheckbox(2)">
                                                            <i class="fa fa-close"></i> In Active 
                                                        </a>
                                                    </li>
                                                    <?php if ($acces_management->allow_delete){?>
                                                    <li>
                                                        <a id="bulk_delete" href="javascript:;" onclick="ValidCheckbox(3)">
                                                            <i class="fa fa-trash-o"></i> Delete 
                                                        </a>
                                                    </li>
                                                    <?php } ?>
													<?php if ($acces_management->allow_print){?>
                                                    <li>
                                                        <a id="bulk_excel" href="javascript:;"  onclick="ValidCheckbox(4)"  >
                                                            <i class="fa fa-file-excel-o"></i> Export to Excel 
                                                        </a>
                                                    </li>
                                                    <?php } ?>	
                                                </ul>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="portlet-body">
                                        <form id="frmAssessment" name="frmAssessment" method="post" action="<?php echo base_url() . 'assessment_create/export_assessment/' ?>">
                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_table">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                <input type="checkbox" class="all group-checkable" name="check" id="check" data-set="#index_table .checkboxes" />
                                                                <span></span>
                                                            </label>
                                                        </th>
                                                        <th>ID</th>
                                                        <th>Type</th>                                                        
                                                        <th>Assessment</th>
                                                        <th>Start Date/Time</th>
                                                        <th>End Date/Time</th>
                                                        <th>Status</th>                                                        
                                                        <th>Actions</th>
                                                    </tr>                                                    
                                                </thead>
                                                <tbody>
                                                    
                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>            
        </div>  
        <div class="modal fade" id="LoadModalFilter" role="basic" aria-hidden="true" data-width="400">
            <div class="modal-dialog modal-lg" style="width:1024px;">
                <div class="modal-content">
                    <div class="modal-body" id="modal-body">
                        <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                        <span>
                            &nbsp;&nbsp;Loading... </span>
                    </div>
                </div>
            </div>
        </div>
    <div class="modal fade" id="stack2" role="basic" aria-hidden="true" data-width="200">
        <div class="modal-dialog modal-sm" style="width:800px;">
            <div class="modal-content">
                <div class="modal-body" id="modal-body">
                    <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                    <span>
                        &nbsp;&nbsp;Loading... </span>
                </div>
            </div>
        </div>
    </div>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <script>            
            var frmAssessment = document.frmAssessment;
            var Base_url   = "<?php echo base_url(); ?>";
        </script>    
    <script>
            jQuery(document).ready(function() {                
                $('.all').click(function () {
                    if ($(this).is(':checked')) {
                        $("input[name='id[]']").prop('checked', true);
                    } else {
                        $("input[name='id[]']").prop('checked', false);
                    }
                });
                DatatableRefresh();
            });            
            function LoadConfirmDialog(content,opt){
                $.confirm({
                    title: 'Confirm!',
                    content: content,
                    buttons: {
                        confirm:{
                        text: 'Confirm',
                        btnClass: 'btn-orange',
                        keys: ['enter', 'shift'],
                        action: function(){
							if(opt !=4){
								$.ajax({
									type: "POST",
									data: $('#frmAssessment').serialize(),
									url: "<?php echo base_url(); ?>assessment_create/record_actions/"+opt,
									success: function (response_json) {
										var response= JSON.parse(response_json);
										ShowAlret(response.message,response.alert_type);
										DatatableRefresh();
									}
								});
							}else{
								document.frmAssessment.submit();
							}
                        }
                    },
                    cancel: function () {
                         this.onClose();
                    }
                    }
                });
            }
            function ValidCheckbox(opt){
                var Check = getCheckCount();
                if (Check > 0){
                    if( opt == 1 ){
                       LoadConfirmDialog("Confirm Active Status ?",opt);  
                    }
                    else if( opt == 2 ){
                        LoadConfirmDialog("Confirm InActive Status ?",opt);
                    }
                    else if( opt == 3 ){
                        LoadConfirmDialog("Are you sure you want to delete ?",opt);
                    }else if( opt == 4 ){
                        LoadConfirmDialog("Are you sure you want to Export to excel. ?",opt);
                    }     
                }else{
                    ShowAlret("Please select record from the list.",'error');
                    return false;
                }   
            }
            function LoadDeleteDialog(Id){
                $.confirm({
                    title: 'Confirm!',
                    content: " Are you sure you want to delete Assessment ? ",
                    buttons: {
                        confirm:{
                        text: 'Confirm',
                        btnClass: 'btn-orange',
                        keys: ['enter', 'shift'],
                        action: function(){
                            $.ajax({
                                type: "POST",
                                url: "<?php echo base_url();?>assessment_create/remove/"+Id,
                                success: function (response_json) {
                                    var response= JSON.parse(response_json);
                                    ShowAlret(response.message,response.alert_type);
                                    DatatableRefresh();
                                }
                            });
                        }
                    },
                    cancel: function () {
                         this.onClose();
                    }
                    }
                });
            }
            function getCheckCount(){
                var x = 0;
                for (var i = 0; i < frmAssessment.elements.length; i++){
                    if (frmAssessment.elements[i].checked == true){
                        x++;
                    }
                }
                return x;
            }
            function ResetFilter() {                
                document.FilterFrm.reset();
                DatatableRefresh();
            }
            function DatatableRefresh() {
//                if (!jQuery().dataTable) {
//                    return;
//                }
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
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
                        {'width': '15px','orderable': false,'searchable': false,'targets': [0]}, 
                        {'width': '30px','orderable': true,'searchable': true,'targets': [1]}, 
                        {'width': '30px','orderable': false,'searchable': false,'targets': [2]},                         
                        {'width': '140px','orderable': true,'searchable': true,'targets': [3]},
                        {'width': '100px','orderable': true,'searchable': true,'targets': [4]},
                        {'width': '100px','orderable': true,'searchable': true,'targets': [5]},
                        {'width': '30px','orderable': false,'searchable': false,'targets': [6]},
                        {'width': '30px','orderable': false,'searchable': false,'targets': [7]}
                    ],
                    "order": [
                        [1, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'assessment_create/DatatableRefresh'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: 'filter_status', value: $('#filter_status').val()});
                        aoData.push({name: 'assessment_type', value: $('#assessment_type').val()});
			aoData.push({name: 'question_type', value: $('#question_type').val()});
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
            function ResetFilter() {
                $('.select2me').val(null).trigger('change');
                document.FilterFrm.reset();
                DatatableRefresh();
            }
        </script>
    </body>
</html>