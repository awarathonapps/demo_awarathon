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
        <link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/global/css/jquery.timepicker.min.css"/>
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
                                    <span>Workshop Play Reports</span>
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Play Time</span>
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
                                    <div id="collapse_3_2" class="panel-collapse <?php echo ($Company_id !="" ? '' :''); ?>">
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
                                                            <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshop_id" name="workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="get_datetime()">
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
                                                            <label class="control-label col-md-3">Workshop Session&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="session_id" name="session_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" onchange="get_datetime()">
                                                                    <option value="PRE">PRE</option>
                                                                    <option value="POST">POST</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row margin-bottom-10">
												<div class="col-md-6">
                                                    <div class="col-md-6 " style="padding:0;">
                                                        <div class="form-group">
                                                            <label class="control-label input-sm col-md-6">From Date:</label>
                                                            <div class="col-md-6" style="padding:0;">
                                                                <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                    <input placeholder="DD-MM-YYYY" id="from_date" name="from_date" class="form-control date-picker2 input-sm PreSessionTime" size="18" type="text" value="" 
                                                                           data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years"></div>
                                                            </div>
                                                        </div>
                                                    </div>
													<div class="col-md-6" style="padding:0;">
                                                        <div class="form-group">
                                                            <label class="control-label input-sm col-md-6" style="text-align:right">To Date:</label>
                                                            <div class="col-md-6" style="padding:0;">
                                                                <div class="input-icon"><i class="fa fa-calendar"></i>
                                                                    <input placeholder="DD-MM-YYYY" id="to_date" name="to_date" class="form-control date-picker2 input-sm PostSessionTime" size="18" type="text" value="" 
                                                                           data-date="12-02-2012" data-date-format="dd-mm-yyyy" data-date-viewmode="years"></div>
                                                            </div>
                                                        </div>
                                                    </div>
												</div>	
												<div class="col-md-3 rightborder">
													<div class="form-group">
														<label class="control-label input-sm col-md-6" >From Time:</label>
														<div class="col-md-6" style="padding:0;">
															<div class="input-icon"><i class="fa fa-clock-o"></i>
																<input type="text" placeholder="h:mm" id="from_time" name="from_time" class="form-control timepicker timepicker-no-seconds PreSessionTime">                                                                            
															</div>
														</div>
													</div>
												</div>                                                      
												<div class="col-md-3">
													<div class="form-group">
														<label class="control-label input-sm col-md-6" style="text-align:right">To Time:</label>
														<div class="col-md-6" style="padding:0;">
															<div class="input-icon"><i class="fa fa-clock-o"></i>
																<input type="text" placeholder="h:mm" id="to_time" name="to_time" class="form-control timepicker timepicker-no-seconds PostSessionTime">        
															</div>
														</div>
													</div>
												</div>
                                                </div>
                                                <div class="clearfix margin-top-20"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="SetFilter()">Search</button>
<!--                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter()">Reset</button>-->
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
                                    <form id="frmReorts" name="frmReorts" method="post" action="">                                        
                                        <div class="clearfix margin-top-20"></div>
                                        <div class="portlet-body">
                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_table">
                                                <thead>
                                                    <tr>
                                                        <th>Trainee ID</th>
                                                        <th>Employee Code</th>
                                                        <th>Trainee Name</th>                                                        
                                                        <th>Reg.Date/Time</th>
                                                        <th>Session Close Date/Time</th>
														<th>Time Spend</th>                                                       
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
            </div>            
        </div>        
        <?php $this->load->view('inc/inc_footer_script');?>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="<?php echo $asset_url; ?>assets/global/scripts/jquery.timepicker.min.js"></script>
        <script>
            var search=1;
            var frmReorts = document.frmReorts;
            jQuery(document).ready(function() {
                $('.timepicker').timepicker({
                    timeFormat: 'h:mm p',
                    interval: 60,
                    dynamic: false,
                    dropdown: true,
                    scrollbar: true
                });
                $('.date-picker2').datepicker({
                    rtl: App.isRTL(),
                    orientation: "left",
                    autoclose: true,
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    //startDate: '+0d'
                });                               
                $(".select2_rpt2").select2({
                    placeholder: 'Please Select',
                    width: '100%'
                });
            });
            function ResetFilter() {
                $('.select2me,.select2_rpt2').select("val","");
                $('.select2me,.select2_rpt2').val(null).trigger('change');
                document.FilterFrm.reset();                
            }
            function SetFilter(){
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    ShowAlret("Please select Company first.!!", 'error');
                    return false;
                }else if($('#workshop_id').val() == ""){
                    ShowAlret("Please select Workshop.!!", 'error');
                    return false;
                }
                else{
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
                    "bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "pageLength": 10,
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': false,'searchable': true,'targets': [0]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': false,'searchable': true,'targets': [1]},
                        {'className': 'dt-head-left dt-body-left','width': '250px','orderable': false,'searchable': true,'targets': [2]},
                        {'className': 'dt-head-left dt-body-left','width': '250px','orderable': false,'searchable': true,'targets': [3]},
                        {'className': 'dt-head-left dt-body-left','width': '250px','orderable': false,'searchable': true,'targets': [4]},
						{'className': 'dt-head-left dt-body-left','width': '150px','orderable': false,'searchable': true,'targets': [5]}	
                    ],
                    "order": [
                        [3, "asc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'workshop_play_attendence/DatatableRefresh/'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'company_id', value: $('#company_id').val()});                        
                        aoData.push({name: 'workshop_id', value: $('#workshop_id').val()});
                        aoData.push({name: 'session_id', value: $('#session_id').val()});
                        aoData.push({name: 'from_date', value: $('#from_date').val()});
                        aoData.push({name: 'from_time', value: $('#from_time').val()});
                        aoData.push({name: 'to_date', value: $('#to_date').val()});
                        aoData.push({name: 'to_time', value: $('#to_time').val()});                        
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
                        $('thead > tr> th:nth-child(1)').css({ 'min-width': '60px', 'max-width': '50px' });
                        $('thead > tr> th:nth-child(2)').css({ 'min-width': '100px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(3)').css({ 'min-width': '200px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(4)').css({ 'min-width': '120px', 'max-width': '100px' });
                        $('thead > tr> th:nth-child(5)').css({ 'min-width': '120px', 'max-width': '150px' });
						$('thead > tr> th:nth-child(6)').css({ 'min-width': '120px', 'max-width': '150px' });	
                    }
                });
            }            
            function getCompanywiseData(){
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    $('#workshop_id').empty();                    
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
                        }
                    customunBlockUI();    
                    }
                });
            }
            function get_datetime(){
                if($('#workshop_id').val() == ""){                       
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),workshop_id:$('#workshop_id').val(),session_id:$('#session_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>workshop_play_attendence/get_workshop_datetime",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);                            
                            
                            $('#from_date').val(Oresult['from_date']);                             
                            $('#from_time').val(Oresult['from_time']);
							$('#to_date').val(Oresult['from_date']);                             
                            $('#to_time').val(Oresult['from_time']);							
                        }
                    customunBlockUI();    
                    }
                });
            }
</script>
</body>
</html>