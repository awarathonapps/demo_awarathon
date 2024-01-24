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
                                    <i class="fa fa-circle"></i>
                                    <span>Reports</span>
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Trainee Consolidated Reports</span>
                                </li>
                            </ul>
                        </div>
                        <div class="row mt-10">
                        <div class="col-md-12">
                            <div class="panel-group accordion" id="accordion3">
                                <div class="panel panel-default">

                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                               Report Search </a>
                                        </h4>
                                    </div>

                                    <div id="collapse_3_2" class="panel-collapse collapse">
                                        <div class="panel-body" >
                                            <form id="FilterFrm" name="FilterFrm" method="post">
                                                <div class="row margin-bottom-10">
                                                    <?php if ($Company_id == "") { ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Company&nbsp;</label>
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
                                                </div>    
                                                <div class="row margin-bottom-10">    
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainer &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="trainer_id" name="trainer_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="getTrainerwiseData()">
                                                                    <option value="">All</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainee &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="user_id" name="user_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                    <option value="">All</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                </div>
                                                <div class="row margin-top-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshop_id" name="workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%">
                                                                    <option value="">All Workshop</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Session&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="sessions" name="sessions" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" >
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
                                                                <select id="topic_id" name="topic_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getTopicwiseData();">
                                                                    <option value="">All Topic</option>
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
                                <div class="portlet light bordered">
                                    <form id="frmReorts" name="frmReorts" method="post" action="<?php echo base_url() . 'trainee_consolidated_report/exportReport' ?>">
                                        <div class="portlet-title">
                                            <div class="caption caption-font-24">
                                                Trainee Consolidated Report
                                               <div class="tools"> </div>
                                            </div>
                                            <div class="actions">
                                                <div class="btn-group pull-right">
                                                    <button type="button"
                                                    onclick="exportConfirm()
                                                    <?php echo ($acces_management->allow_print ? '':'javascript:void(alert(\'you have no rights to Add,Contact to Administrator!!!\'))');?>"
                                                    autofocus="" accesskey="" name="export_excel" id="export_excel"  class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                    &nbsp;&nbsp;

                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix margin-top-20"></div>
                                        <div class="portlet-body">
                                            <table class="table  table-bordered table-hover table-checkable order-column" id="index_table">
                                                <thead>
                                                    <tr>
                                                        <th>Id</th>
                                                        <th>Trainee Name</th>
                                                        <th>Company</th>
                                                        <th>Workshop</th>
                                                        <th>Session</th>
                                                        <th>Question Set Name</th>
                                                        <th>Trainer Name</th>
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
            function ResetFilter() {
                $('.select2me').select("val","");
                $('.select2me').val(null).trigger('change');
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
                        {'width': '30px','orderable': true,'searchable': true,'targets': [0],"visible":false},
                        {'className': 'dt-head-left dt-body-left','width': '230px','orderable': true,'searchable': true,'targets': [1]},
                        {'className': 'dt-head-left dt-body-left','width': '250px','orderable': false,'searchable': false,'targets': [2], "visible": <?php echo ($Company_id == "" ? 'true' : 'false'); ?>},
                        {'className': 'dt-head-left dt-body-left','width': '250px','orderable': true,'searchable': true,'targets': [3]},
                        {'className': 'dt-head-left dt-body-left','width': '80px','orderable': true,'searchable': true,'targets': [4]},
                        {'className': 'dt-head-left dt-body-left','width': '120px','orderable': false,'searchable': true,'targets': [5]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [6]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [7]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [8]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [9]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [10]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [1]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [12]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [13]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [14]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [15]},
                        {'className': 'dt-head-left dt-body-left','width': '130px','orderable': false,'searchable': false,'targets': [16]},
                    ],
                    "order": [
                        [1, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'trainee_consolidated_report/DatatableRefresh/'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'company_id', value: $('#company_id').val()});
                        aoData.push({name: 'workshop_id', value: $('#workshop_id').val()});
                        aoData.push({name: 'sessions', value: $('#sessions').val()});
                        aoData.push({name: 'topic_id', value: $('#topic_id').val()});
                        aoData.push({name: 'subtopic_id', value: $('#subtopic_id').val()});
                        aoData.push({name: 'user_id', value: $('#user_id').val()});
                        aoData.push({name: 'result_search', value: $('#result_search').val()});
                        aoData.push({name: 'trainer_id', value: $('#trainer_id').val()});
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
                        $('thead > tr> th:nth-child(9)').css({ 'min-width': '400px', 'max-width': '400px' });
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
                if(compnay_id=="" || compnay_id==null){
                    $('#trainer_id').empty();
                    $('#user_id').empty();
                    $('#topic_id').empty();
                    $('#subtopic_id').empty();
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val()},
                    async: false,
                    url: "<?php echo $base_url; ?>trainee_consolidated_report/ajax_companywise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var WorkshopMSt = Oresult['WorkshopData'];
                            var TopicMSt = Oresult['TopicData'];
                            var TraineeMSt = Oresult['TraineeData'];
                            var TrainerMSt = Oresult['TrainerData'];
                            
                            var workshop_option = '<option value="">Please Select</option>';
                            var topic_option = '<option value="">Please Select</option>';
                            var trainee_option = '<option value="">Please Select</option>';
                            var trainer_option = '<option value="">Please Select</option>';
                            for (var i = 0; i < WorkshopMSt.length; i++) {
                                workshop_option += '<option value="' + WorkshopMSt[i]['id'] + '">' + WorkshopMSt[i]['workshop_name'] + '</option>';
                            }
                            for (var i = 0; i < TopicMSt.length; i++) {
                            topic_option += '<option value="' + TopicMSt[i]['id'] + '">' + TopicMSt[i]['description'] + '</option>';
                            }
                                                        
                            for (var i = 0; i < TraineeMSt.length; i++) {
                                trainee_option += '<option value="' + TraineeMSt[i]['user_id'] + '">' + TraineeMSt[i]['traineename'] + '</option>';
                            }
                            
                            for (var i = 0; i < TrainerMSt.length; i++) {
                                trainer_option += '<option value="' + TrainerMSt[i]['userid'] + '">' + TrainerMSt[i]['trainername'] + '</option>';
                            }
                            $('#trainer_id').empty();
                            $('#trainer_id').append(trainer_option);
                            $('#user_id').empty();
                            $('#user_id').append(trainee_option);
                            $('#workshop_id').empty();
                            $('#workshop_id').append(workshop_option);
                            $('#topic_id').empty();
                            $('#topic_id').append(topic_option);
                        }
                        customunBlockUI();
                    }
                });
            }
            function getTopicwiseData(){
                if($('#topic_id').val()=="" || $('#topic_id').val()==null){
                    $('#subtopic_id').empty();
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {topic_id: $('#topic_id').val(),company_id: $('#company_id').val()},
                    async: false,
                    url: "<?php echo $base_url; ?>trainee_consolidated_report/ajax_topicwise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var SubtopicMSt = Oresult['SubTopicData'];
                            var subtopic_option = '<option value="">Please Select</option>';
                            for (var i = 0; i < SubtopicMSt.length; i++) {
                                subtopic_option += '<option value="' + SubtopicMSt[i]['id'] + '">' + SubtopicMSt[i]['description'] + '</option>';
                            }
                            $('#subtopic_id').empty();
                            $('#subtopic_id').append(subtopic_option);
                        }
                    customunBlockUI();   
                    }
                });
            }
            function getTrainerwiseData(){
                $.ajax({
                    type: "POST",
                    data: {trainer_id: $('#trainer_id').val(),company_id: $('#company_id').val()},
                    async: false,
                    url: "<?php echo $base_url; ?>trainee_consolidated_report/ajax_trainerwise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var TraineeMSt = Oresult['TraineeData'];
                            var trainee_option = '<option value="">Please Select</option>';
                            for (var i = 0; i < TraineeMSt.length; i++) {
                                trainee_option += '<option value="' + TraineeMSt[i]['user_id'] + '">' + TraineeMSt[i]['traineename'] + '</option>';
                            }
                            $('#user_id').empty();
                            $('#user_id').append(trainee_option);
                        }
                        customunBlockUI();
                    }
                });
            }
</script>
</body>
</html>