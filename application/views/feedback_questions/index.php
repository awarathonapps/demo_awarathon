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
                                    <span>Feedback</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Question Listing</span>
                                </li>
                            </ul>
                            <?php if($acces_management->allow_add){ ?>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url?>feedback_questions/create" class="btn btn-sm btn-orange pull-right">Create New</a>&nbsp;
                            </div>
                            <?php } ?>
                        </div>
                        <form id="FilterFrm" name="FilterFrm" action="<?php echo base_url() . 'feedback_questions/export_quest/' ?>" method="post">
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
                                                <?php if ($Company_id == "") { ?>
                                                <div class="row">                                                
                                                    <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Company Name<span class="required"> * </span></label>
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2me" placeholder="Please select"  onchange="feedbackTypeData();">
                                                                    <option value="">Please Select</option>
                                                                    <?php if(isset($CompanySet)){
                                                                            foreach ($CompanySet as $Row) { ?>
                                                                                <option value="<?php echo $Row->id ?>"><?php echo $Row->company_name ?></option>
                                                                        <?php  }
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        </div> 
                                                </div>                                               
                                                 <div class="clearfix margin-top-20"></div>
                                                <?php } ?>
                                                <div class="row">                                                
                                                    <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Feedback Type</label>
                                                                <select id="feedback_type" name="feedback_type" class="groupSelectClass form-control input-sm select2me" placeholder="Please select" onchange="getfeedbacksupType();">
                                                                    <option value="">Please Select</option>
                                                                    <?php if(isset($feedback_typeSet)){
                                                                            foreach ($feedback_typeSet as $Row) { ?>
                                                                                <option value="<?php echo $Row->id ?>"><?php echo $Row->description ?></option>
                                                                        <?php  }
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                    </div>
                                                    <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Feedback Sub Type</label>
                                                                <select id="feedback_subtype" name="feedback_subtype" class="groupSelectClass form-control input-sm select2me" placeholder="Please select" >
                                                                    <option value="">Please Select</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Question Type</label>
                                                                <select id="question_type" name="question_type" class=" form-control input-sm select2me" placeholder="Please select"  >
																	<option value="">ALL</option>
                                                                    <option value="0">Multiple choice</option>
                                                                    <option value="1">Text</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    <div class="col-md-3">    
                                                        <div class="form-group">
                                                            <label>Language</label>
                                                            <select id="language_id" name="language_id" class="form-control input-sm select2me" placeholder="Please select" style="width:100%" >
                                                                <option value="">All Language</option>
                                                                <?php if(isset($language_mst)){
                                                                        foreach ($language_mst as $Row) { ?>
                                                                            <option value="<?php echo $Row->id ?>"><?php echo $Row->name ?></option>
                                                                    <?php  }
                                                                } ?>
                                                            </select>
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
                                            Manage Feedback Questions  
                                           <div class="tools"> </div>  
                                        </div>
                                        <div class="actions">
                                            <div class="btn-group pull-right">
                                                <?php if($acces_management->allow_add){ ?>
                                                <a href="<?php echo site_url("feedback_questions/import");?>" class="btn orange btn-sm btn-outline" style="margin-right: 10px;"><i class="fa fa-file-excel-o"></i> Import</a>
                                                <?php } ?>
                                                <button type="button" class="btn orange btn-sm btn-outline dropdown-toggle" data-toggle="dropdown">Bulk Actions
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu pull-right" role="menu">
                                                <?php if ($acces_management->allow_add OR $acces_management->allow_edit){?>
                                                    <li>
                                                        <a id="bulk_active" href="javascript:;">
                                                            <i class="fa fa-check"></i> Active
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a id="bulk_inactive" href="javascript:;">
                                                            <i class="fa fa-close"></i> In Active 
                                                        </a>
                                                    </li>
                                                    <?php } ?>
                                                    <?php if ($acces_management->allow_delete){?>
                                                    <li>
                                                        <a id="bulk_delete" href="javascript:;">
                                                            <i class="fa fa-trash-o"></i> Delete 
                                                        </a>
                                                    </li>
                                                    <?php } ?>
                                                    <?php if ( $acces_management->allow_export){?>
                                                    <li class="divider"></li>
                                                    <?php if ($acces_management->allow_export){?>
                                                    <li>
                                                        <a id="bulk_excel" href="javascript:;" onclick="exportConfirm()">
                                                            <i class="fa fa-file-excel-o"></i> Export to Excel 
                                                        </a>
                                                    </li>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
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
                                                        <th>Company</th>   
                                                        <th>Language</th>
                                                        <th>Question</th>
                                                        <th>Question Type</th>
                                                        <th> Type</th>
                                                        <th> Sub-Type</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
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
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>

        <script>
            var FilterFrm = document.FilterFrm;
            jQuery(document).ready(function() {
                $('.select2me').select2({
                    placeholder: " Select All",
                    allowClear: true,
                    width:"100%"
                });                
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
                            $.ajax({
                                type: "POST",
                                data: $('#FilterFrm').serialize(),
                                url: "<?php echo $base_url; ?>feedback_questions/record_actions/"+opt,
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
                        LoadConfirmDialog("Delete Selected Feedback Questions) ?",opt);
                    }   
                }else{
                    ShowAlret("Please select record from the list.",'error');
                    return false;
                }   
            }
            $(function(){
                $("#bulk_active").click(function(){
                    ValidCheckbox(1);
                });  
                $("#bulk_inactive").click(function(){
                    ValidCheckbox(2);
                });  
                $("#bulk_delete").click(function(){
                    ValidCheckbox(3);
                });  
            });
            function LoadDeleteDialog(Id){
                $.confirm({
                    title: 'Confirm!',
                    content: " Are you sure you want to delete this Feedback Question? ",
                    buttons: {
                        confirm:{
                        text: 'Confirm',
                        btnClass: 'btn-orange',
                        keys: ['enter', 'shift'],
                        action: function(){
                            $.ajax({
                                type: "POST",
                                url: "<?php echo $base_url;?>feedback_questions/remove/"+Id,
                                beforeSend: function () {
                                    customBlockUI();
                                },
                                success: function (response_json) {
                                    var response= JSON.parse(response_json);
                                    ShowAlret(response.message,response.alert_type);
                                    if(response.alert_type=='success'){
                                        DatatableRefresh();
                                    }
                                     customunBlockUI();
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
            function getCheckCount()
            {
                var x = 0;
                for (var i = 0; i < FilterFrm.elements.length; i++)
                {
                    if (FilterFrm.elements[i].checked == true)
                    {
                        x++;
                    }
                }
                return x;
            }
            
             function exportConfirm() {
                $.confirm({
                    title: 'Confirm!',
                    content: "Are you sure you want to Export. ? ",
                    buttons: {
                        confirm: {
                            text: 'Confirm',
                            btnClass: 'btn-primary',
                            keys: ['enter', 'shift'],
                            action: function () {
                                document.FilterFrm.submit();
                            }
                        },
                        cancel: function () {
                            this.onClose();
                        }
                    }
                });
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
                        {'width': '30px','orderable': false,'searchable': false,'targets': [0]}, 
                        {'width': '40px','orderable': true,'searchable': true,'targets': [1]}, 
                        {'width': '200','orderable': true,'searchable': true,'targets': [2], "visible": <?php echo ($Company_id == "" ? 'true' : 'false'); ?>},
                        {'width': '40px','orderable': true,'searchable': true,'targets': [3]},
                        {'width': '100px','orderable': true,'searchable': true,'targets': [4]},
                        {'width': '150','orderable': false,'searchable': false,'targets': [5]}, 
                        {'width': '100','orderable': false,'searchable': false,'targets': [6]}, 
                        {'width': '60','orderable': false,'searchable': true,'targets': [7]},
                        {'width': '70','orderable': false,'searchable': false,'targets': [8]},
                        {'width': '70','orderable': false,'searchable': false,'targets': [9]}
                    ],
                    "order": [
                        [1, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'feedback_questions/DatatableRefresh'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {                        
                        aoData.push({name: 'company_id', value: $('#company_id').val()});
                        aoData.push({name: 'feedback_type', value: $('#feedback_type').val()});
                        aoData.push({name: 'feedback_subtype', value: $('#feedback_subtype').val()});
                        aoData.push({name: 'question_type', value: $('#question_type').val()});
                        aoData.push({name: 'language_id', value: $('#language_id').val()});
						
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
    $('.select2me').select("val","");
    $('.select2me').val(null).trigger('change');
    document.FilterFrm.reset();
    DatatableRefresh();
}
function feedbackTypeData(){
    var Company_id = $('#company_id').val();
    $('#feedback_type').empty();
    if(Company_id==""){
        return false;
    }
    $.ajax({
        type: "POST",
        data: "data=" + Company_id,
        async:false,
        url: "<?php echo base_url(); ?>feedback_questions/ajax_company_feedbackType",
         beforeSend: function () {
                                customBlockUI();
                            },
            success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var FeedbackTypeMSt = Oresult['result'];                             
                var option='<option value="">Please Select</option>';                            
                for (var i = 0; i < FeedbackTypeMSt.length; i++) {
                    option += '<option value="' + FeedbackTypeMSt[i]['id'] + '">' + FeedbackTypeMSt[i]['description'] + '</option>';
                }                            
                $('#feedback_type').append(option); 
            }
             customunBlockUI();
        }
    });
}
function getfeedbacksupType(){
    var feedback_type = $('#feedback_type').val();
    $('#feedback_subtype').empty();
    if(feedback_type==""){
        return false;
    }
    $.ajax({
        type: "POST",
        data: "feedback_type=" + feedback_type,
        async:false,
        url: "<?php echo base_url(); ?>feedback_questions/ajax_feedback_subType",
         beforeSend: function () {
                                customBlockUI();
                            },
            success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var FeedbackTypeMSt = Oresult['result'];                             
                var option='<option value="">Please Select</option>';                            
                for (var i = 0; i < FeedbackTypeMSt.length; i++) {
                    option += '<option value="' + FeedbackTypeMSt[i]['id'] + '">' + FeedbackTypeMSt[i]['description'] + '</option>';
                }                            
                $('#feedback_subtype').empty();
                $('#feedback_subtype').append(option); 
            }
             customunBlockUI();
        }
    });
}
function LoadUpdateDialog(id){
    var topic_id=$('#topic'+id).val();
    var subtopic_id=$('#subtopic'+id).val();
        $.confirm({
            title: 'Confirm!',
            content: " Are you sure want to Update this Subtopic? ",
            buttons: {
                confirm:{
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url() . 'feedback_questions/updateTopicSubtopic/' ?>"+id,
                        beforeSend: function () {
                            customBlockUI();
                        },
                        data:  {tp_id:topic_id,stp_id:subtopic_id},                
                                success: function (response_json) {
                                    var response= JSON.parse(response_json);
                                    ShowAlret(response.message,response.alert_type);
//                                    if(response.alert_type=='success'){
//                                        DatatableRefresh();
//                                    }
                            customunBlockUI();
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
function getSubType(Qid) {
var feedback_type=$('#topic'+Qid).val();
    $.ajax({
        type: "POST",
        data: "feedback_type=" + feedback_type,
        async: false,
        url: "<?php echo base_url(); ?>feedback_questions/ajax_feedback_subType/",
        beforeSend: function () {
	customBlockUI();
},
        success: function (Odata) {
            if (Odata != '') {
                var Oresult = jQuery.parseJSON(Odata);
                var SubTopicMSt = Oresult['result'];                
                var option = '<option value="">Select</option>';
                for (var i = 0; i < SubTopicMSt.length; i++) {
                    option += '<option value="' + SubTopicMSt[i]['id'] + '" >' + SubTopicMSt[i]['description'] + '</option>';
                }
                $('#subtopic'+Qid).empty();
                $('#subtopic'+Qid).append(option);
                //$("#topic_id").trigger("change");
            } 
            customunBlockUI();
        }
    });
    }
        </script>
    </body>
</html>