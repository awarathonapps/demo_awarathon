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
        <style>
        .checked {
          color: orange;
        }
        #question_table,#rating_table {
            display: block;
            max-height: 350px;
            overflow-y: auto;
            table-layout:fixed;
        }
        .cust_container {
            overflow: hidden;
            width: 100%;
        }
        .left-col {
            padding-bottom: 500em;
            margin-bottom: -500em;
        }
        .right-col {
            margin-right: -1px; /* Thank you IE */
            padding-bottom: 500em;
            margin-bottom: -500em;
            background-color: #FFF;
        }      
        </style>
        <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />                
        <link href="<?php echo $asset_url; ?>assets/global/css/star-rating.css" rel="stylesheet" type="text/css" /> 
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
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
                                    <i class="fa fa-circle"></i>
                                    <span>Assessment</span>                                    
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Rating</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>assessment_candidate_rpt" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                        <div class=" caption-font-24">
                                           Participant Trainee List
                                           <h5> Assessment Name : <?php echo $Rowset->assessment; ?> </h5>
                                        </div>
                                    </div>
                                    <div class="portlet-body">   
                                        <div class="tabbable-line tabbable-full-width">
                                            
                                            <form role="form" id="AssessmentUserForm" name="AssessmentUserForm">
                                                <div class="form-body">                                                            
                                                    <div class="row ">
                                                        <div class="col-md-12" id="assessment_panel" >
                                                            <table class="table  table-bordered table-hover table-checkable order-column" id="AssessmentUsersTable">
                                                                <thead>
                                                                    <tr>                                                                                
                                                                        <th>User ID</th>
                                                                        <th>Name</th>
                                                                        <th>Email</th>
                                                                        <th>Mobile No</th>
                                                                        <th>Trainee Region</th>
									<th>Candidate Status</th>
                                                                        <th>Assessor Status</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
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
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>                                                        
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>        
        
        <script>            
            var AddEdit = "E";    
            var NewUsersArrray=[];
            var AssessmentForm = $('#AssessmentForm');
            var form_error = $('.alert-danger', AssessmentForm);
            var form_success = $('.alert-success', AssessmentForm);
            var TrainerArrray = [];                     
            var Base_url   = "<?php echo base_url(); ?>";
            var Encode_id  = "<?php echo base64_encode($assessment_id); ?>";            
        </script>         
        <script>
        jQuery(document).ready(function () {            
            AssessmentUsersRefresh();
        });           
        function AssessmentUsersRefresh() {
            var table = $('#AssessmentUsersTable');
            oTable = table.dataTable({
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
                        "previous": "Prev",
                        "next": "Next",
                        "last": "Last",
                        "first": "First"
                    }
                },
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [
                    {'width': '40px', 'orderable': true, 'searchable': true, 'targets': [0]},
                    {'width': '100px', 'orderable': true, 'searchable': true, 'targets': [1]},
                    {'width': '90px', 'orderable': false, 'searchable': true, 'targets': [2]},
                    {'width': '70px', 'orderable': false, 'searchable': true, 'targets': [3]},
                    {'width': '80px', 'orderable': false, 'searchable': true, 'targets': [4]},
                    {'width': '80px', 'orderable': false, 'searchable': false, 'targets': [5]},
                    {'width': '80px', 'orderable': false, 'searchable': false, 'targets': [6]},
                    {'width': '50px', 'orderable': false, 'searchable': false, 'targets': [7]}            
                ],
                "order": [
                    [0, "desc"]
                ],
                "processing": true,
                "serverSide": true,
                "sAjaxSource": Base_url + "assessment_candidate_rpt/AssessmentUsers/" + Encode_id ,
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                    aoData.push({name: 'fttrainer_id', value: $('#fttrainer_id').val()});
                    aoData.push({name: 'ftroute_trainer_id', value: $('#ftroute_trainer_id').val()});

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
        function getparameter(Q_id,srno,cnt){
            $.ajax({
                url: Base_url+"assessment_candidate_rpt/getquestionwiseparameter/" + Q_id+"/"+srno,
                type: 'POST',
                data: {company_id:$('#company_id').val(),ass_result_id:$('#ass_result_id').val(), 
                assessment_id :$('#assessment_id').val(),assessment_type :$('#assessment_type').val(),
                user_id :$('#user_id').val(),trainer_id :$('#trainer_id').val()},
                beforeSend: function () {
                    customBlockUI();
                },
                success: function (Odata) {
                    var Data = $.parseJSON(Odata);               
                    $('#selectedquestion').html(Data['Question']);
                    $('#parameter_table_div').html(Data['QParameter_table']);     
                    $("#question_id").val(Q_id);
                    $('#remark_que').val(Data['question_comments']);    
                    if(srno==cnt && Data['cnt_rate']==0){
                        $('.sh-btn').show();
                    }else{
                        $('.sh-btn').hide();
                    }
                    customunBlockUI();
                }
            });
        }
        </script>
    </body>
</html>