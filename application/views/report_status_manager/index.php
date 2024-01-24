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
                                    <!--        <form id="FilterFrm" name="FilterFrm" method="post">-->
                                    <form id="frmReorts" name="frmReorts" method="post" action="<?php echo base_url() . 'report_status/exportReport' ?>">
                                               
                                                <div class="row margin-bottom-10">
                                                <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3"> Assessment <span class="required" aria-required="true"> * </span></label>

                                                            <div class="col-md-9" style="padding:0px;">
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
                                                    </div>
                                                      <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Manager Name &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                            <select id="manager_id" name="manager_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%"> 
                                                                    <option value="">All</option> 
                                                                    <?php 
                                                                        if (isset($user_details)) {
                                                                            foreach ($user_details as $Rdata) {
                                                                                ?>
                                                                                <option value="<?= $Rdata->trainer_id; ?>"><?php echo $Rdata->fullname; ?></option>
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
                                                            <label class="control-label col-md-3"> Status &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                            <select id="status_id" name="status_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%"> 
                                                                    
                                                                    <option value = "0"> Complete</option>
                                                                    <option value="1">Incomplete</option>                                  
                                                                    <option value="2">Overall</option> 
                                                                </select>
                                                            </div>
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
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="DatatableRefresh()">Search</button>
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter()">Reset</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                                                                
                                          <!--  </form> -->
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                            
                        <div class="row mt-10" id="report_section">
                            <div class="col-md-12">
                                <div class="portlet light bordered">
                                    <!--<form id="frmReorts" name="frmReorts" method="post" action="<?php echo base_url() . 'report_status/exportReport' ?>">-->
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
                                        <div class="portlet-body">
                                        <table class="table  table-bordered table-hover table-checkable order-column" id="index_table">
                                                <thead>
                                                    <tr>
                                                        <th>E code</th>
                                                        <th>Employee Name</th>
                                                        <th>DoJ</th>
                                                        <th>Division</th>
                                                        <th>PC HQ</th>
                                                        <th>State</th>
                                                        <th>Zone</th>
                                                        <th>L+1 EC</th>
                                                        <th>L+1 Name</th>
                                                        <th>Email ID</th>
                                                        <th>Assessment Name</th>
                                                        <th>Employee Status</th>
                                                        <th>AI Score %</th>
                                                      
                                                        <?php foreach($parameter_score_result as $que){ ?>
                
                                                                   <th ><?php echo $que->parameter_name;?><?php echo "%";?></th>
                                                        <?php } ?>
                                                        <th>Assessor Rating %</th>
                                                        <th>Overall Avg (AI and Assessor) %</th>
                                                        <th>Diff (AI -Assesor)</th>
                                                        <th>Range AI</th>
                                                        <th>Range Asssesor</th>
                                                        <th>Joining Range</th> 
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
            jQuery(document).ready(function(){
                $('#report_section').hide();
            });
  //          var search=1;
            var frmReorts = document.frmReorts;
            function DatatableRefresh() {
				 $('#report_section').hide();
				if($("#assessment_id").val()==0 || $("#assessment_id").val()==""){
				   ShowAlret("Please select assessment first.!!", 'error');
				   return false;
			   }
                $('#report_section').show();
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
                    
                    "bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "pageLength": 10,
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
                        
                        
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'Report_status_manager/generate_report_manager'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: 'manager_id', value: $('#manager_id').val()});
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
                        $('thead > tr> th:nth-child(1 )').css({ 'min-width': '100px', 'max-width': '100px' });
                    }
                });
            }   

            function ResetFilter() {
                $('.select2me,.select2_rpt2').select("val","");
                $('.select2me,.select2_rpt2').val(null).trigger('change');
                document.FilterFrm.reset();
                DatatableRefresh();
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


            function getAssessmentwiseData(){                
               
                $.ajax({
                    type: "POST",
                    data: {assessment_id: $('#assessment_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>report_status_manager/ajax_assessmentwise_data_manager",
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

</script>
</body>
</html>