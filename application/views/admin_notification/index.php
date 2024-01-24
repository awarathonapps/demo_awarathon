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
                                    <span>Organisation</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Admin Notification</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Listing</span>
                                </li>
                            </ul>
                            <?php if($supperAccess){ ?>
                            <div class="page-toolbar">
                                <a data-toggle="modal" onclick="LoadCreateModal();" class="btn btn-sm btn-orange pull-right">Create New</a>&nbsp;
                            </div>
                            <?php } ?>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                            <div class="panel-group accordion" id="accordion3">
                                <!-- <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                Advanced Search </a>
                                        </h4>
                                    </div>

                                    <div id="collapse_3_2" class="panel-collapse collapse">
                                        <div class="panel-body" >
                                            <form id="FilterFrm" name="FilterFrm" method="post">
                                                <div class="row">
                                                    <?php if ($Company_id == "") { ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Company &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="filter_company_id" name="filter_company_id" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%" >
                                                                    <option value="">All</option>
                                                                    <?php 
                                                                        foreach ($CompanyData as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Status&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="filter_status" name="filter_status" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%">
                                                                    <option value=""selected="">All <option>
                                                                    <option value="1">Active</option>
                                                                    <option value="0">In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                </div>
                                                 <div class="clearfix margin-top-20"></div>
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
                                </div> -->
                                </div>
                                <?php if ($this->session->flashdata('flash_message')) { ?> 
                                    <div class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                        <?php echo $this->session->flashdata('flash_message'); ?>
                                    </div>
                                <?php } ?>
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            Manage Admin Notification
                                           <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <form id="frmNotification" name="frmNotification" method="post">
                                            <table class="table  table-bordered table-hover table- order-column" id="index_table" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th >ID</th>
                                                        <!-- <th >Company</th> -->
                                                        <th >Notification</th>
                                                        <th width="10%">Added Date</th>
                                                        <th width="10%">Status</th>
                                                        <th width="10%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody> </tbody>
                                            </table>
                                        </form>
                                        <div id="responsive-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true"  data-width="760">
                                            <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                            <form id="frmModalForm" name="frmModalForm" onsubmit="return false;"> 
                                                <div class="modal-header">
                                                    <button type="button" class="close" onclick="resetDATA();" data-dismiss="modal" aria-hidden="true"></button>
                                                    <h4 class="modal-title">Add Notification</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div id='dsk' style="display: none">&nbsp;</div>
                                                    <!-- <?php if ($Company_id == "") { ?>
                                                    <div class="row">    
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Company Name<span class="required"> * </span></label>
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
                                                                    <option value="">Please Select</option>
                                                                     <?php 
                                                                        foreach ($CompanyData as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php } ?> -->
                                                    <div class="row">    
                                                        <div class="col-md-10">       
                                                            <div class="form-group">
                                                                <label class="">Notification Message<span class="required"> * </span></label>
                                                                <input type="text" name="notification_message" id="notification_message" maxlength="100" class="form-control input-sm" autocomplete="off">
                                                                <!-- <input type="hidden" name="edit_id" id="edit_id" class="form-control input-sm" autocomplete="off" value=""> -->
                                                            </div>
                                                        </div>                                                        
                                                        <div class="col-md-2">       
                                                            <div class="form-group last">
                                                                <label>Status</label>
                                                                <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                    <option value="1" selected>Active</option>
                                                                    <option value="0">In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="col-md-12 text-right ">  
                                                        <button type="submit" id="modal-create-submit" name="modal-create-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-right">
                                                            <span class="ladda-label">Submit</span>
                                                        </button>
                                                        <button type="button" data-dismiss="modal" onclick="resetDATA();" class="btn btn-default btn-cons">Cancel</button>
                                                    </div>
                                                </div>
                                            </form>
                                            </div>    
                                            </div>    
                                        </div>    
                                    </div>
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
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <script>
            // var region_edit_id='';
            var frmModalForm = $('#frmModalForm');
            var form_error = $('.alert-danger', frmModalForm);
            var form_success = $('.alert-success', frmModalForm);
            //Ladda.bind('button[id=modal-create-submit]');
            var mcs= Ladda.create( document.querySelector('#modal-create-submit'));
            function Redirect(url)
            {
                window.location = url;
            }
            jQuery(document).ready(function() {
                
                frmModalForm.validate({
                    errorElement: 'span',
                    errorClass: 'help-block help-block-error',
                    focusInvalid: false,
                    ignore: "",
                    rules: {
                        notification_message:{
                            required        : true
                        },
                        status: {
                            required: true
                        }
                    },
                    invalidHandler: function (event, validator) {             
                        form_success.hide();
                        form_error.show();
                        App.scrollTo(form_error, -200);
                    },
                    errorPlacement: function(error, element) {
                        if(element.hasClass('form-group')) {
                            error.appendTo(element.parent().find('.has-error'));
                        }
                        else if(element.parent('.form-group').length) {
                            error.appendTo(element.parent());
                        }
                        else {
                            error.appendTo(element);
                        }
                    },
                    highlight: function (element) {
                        $(element).closest('.form-group').addClass('has-error');
                    },
                    unhighlight: function (element) {
                        $(element).closest('.form-group').removeClass('has-error');
                    },
                    success: function (label) {
                        label.closest('.form-group').removeClass('has-error');
                    },
                    submitHandler: function (form) {
                        mcs.start();
                        form_success.show();
                        form_error.hide();
                        $.post('<?php echo base_url();?>index.php/admin_notification/submit', $("#frmModalForm").serialize(), function (data) {
                            if (data.alert_type == "success") {
                                if (data.mode=='add'){
                                    document.getElementById('dsk').innerHTML = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>' + data.message + '</div>';
                                    document.getElementById('dsk').style.display = "block";
                                    setTimeout('document.getElementById("dsk").style.display = "none"', 2000);
                                    $("#status").select2("val", "1");
                                    $('#notification_message').focus();
                                    $('#notification_message').val("");
                                }
                                ShowAlret(data.message,data.alert_type);
                                $('#responsive-modal').modal('hide');
                                DatatableRefresh();
                            }else{
                                document.getElementById('dsk').innerHTML = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>' + data.message + '</div>';
                                document.getElementById('dsk').style.display = "block";
                            }
                            mcs.stop();
                        }, "json");
                    },
                    messages: {
                        notification_message: {
                            required: "This field is required"
                        },
                        status: "This field is required."
                    }
                });
                DatatableRefresh();
            });
            // $('#company_id').on('change', function() {
            //         $(this).valid();
            //     });                        
            function resetDATA(){
            //  $("#company_id").select2("trigger", "select", { 
            //     data: { id: "",text:"" } 
            //     });
            }            
            function LoadCreateModal(){
                $('.modal-title').html('Create Region').show();
                $('#frmModalForm').trigger("reset");
                $("#frmModalForm").validate().resetForm();
                $("#status").select2("val", "1");                
                $('#responsive-modal').modal('show');
                // region_edit_id="";
                $('#edit_id').val("");
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
                        {'width': '30px','orderable': true,'searchable': false,'targets': [0]}, 
                        {'width': '300px','orderable': true,'searchable': true,'targets': [1]},
                        {'width': '30px','orderable': true,'searchable': true,'targets': [2]},
                        {'width': '30px','orderable': true,'searchable': false,'targets': [3]},
                        {'width': '30px','orderable': false,'searchable': false,'targets': [4]}
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'admin_notification/DatatableRefresh'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        // aoData.push({name: 'filter_company_id', value: $('#filter_company_id').val()});
                        // aoData.push({name: 'filter_status', value: $('#filter_status').val()});
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

            function updateStatus($rec_id, $status){
                $.ajax({
                    url : "<?php echo base_url() . 'admin_notification/updateStatus'; ?>",
                    type: 'POST',
                    data: 'id='+$rec_id+'&status='+$status,
                    datatype: 'json',
                    success: function(data){
                        ShowAlret(data.message,data.alert_type);
                        DatatableRefresh();
                    }
                });
            }
        </script>
    </body>
</html>