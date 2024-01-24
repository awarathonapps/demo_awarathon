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
        
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url;?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />        
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <style>
            .row-details{
                color: #659be0 !important;
                    margin-top: 3px;
                    display: inline-block;
                    cursor: pointer;
                    height:6px
            }
            .table.table-light thead tr th{
                color: #000000 !important;
            }
            .table.table-light tbody tr td{
                color: #000000 !important;
            }
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
                                    <span>Supervisor Reports</span>                                    
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Region wise report</span>
                                </li>
                            </ul>                            
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <div class="alert alert-danger display-hide" id="errordiv">
                                    <button class="close" data-close="alert"></button>
                                    You have some form errors. Please check below.
                                    <br><span id="errorlog"></span>
                                </div>
                                <div class="panel-group accordion" id="accordion3">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                               Report Search </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_3_2" class="panel-collapse ">
                                        <div class="panel-body" >
                                            <form id="FilterFrm" name="FilterFrm" method="post">
                                                
                                                <div class="row margin-bottom-10">
                                                    <?php if ($Company_id == "") { ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2_rpt" placeholder="Please select" style="width: 100%"  onchange="getCompanywiseData();">
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
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Reports By&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                            <select id="reportsby_id" name="reportsby_id" class="form-control input-sm select2_rpt" placeholder="Please select"  style="width: 100%">
                                                                <option value="1" selected="">Region</option>
                                                                    <option value="2">Workshop Type</option>
                                                                    <option value="3">Trainer</option>
                                                                    <option value="4">Workshop</option>
                                                            </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                                                                        
                                                </div>                                                                                                
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshoptype_id" name="workshoptype_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" onchange="getTypeWiseWorkshop();" >
                                                                    <option value="0">All Type</option>
                                                                <?php
                                                                if (isset($WtypeResult)) {
                                                                    foreach ($WtypeResult as $Type) {
                                                                        ?>
                                                                        <option value="<?= $Type->id; ?>"><?php echo $Type->workshop_type; ?></option>
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
                                                            <label class="control-label col-md-3">Workshop Sub-type</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshop_subtype" name="workshop_subtype" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" >
                                                                    <option value="">All Sub-type</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Region&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="wregion_id" name="wregion_id" class="form-control input-sm select2_rpt2" 
                                                                        placeholder="Please select"  style="width: 100%" onchange="getTypeWiseWorkshop();" >
                                                                    <option value="0">All Region</option>
                                                                     <?php
                                                                    if (isset($RegionResult)) {
                                                                        foreach ($RegionResult as $region) {
                                                                            ?>
                                                                            <option value="<?= $region->id; ?>"><?php echo $region->region_name; ?></option>
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
                                                            <label class="control-label col-md-3">Sub-region &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="wsubregion_id" name="wsubregion_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" >
                                                                    <option value="">Select Sub-region</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>    
                                                </div>
                                                 <div class="clearfix margin-top-20"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="TableRefresh()">Search</button>
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
                            <div class="portlet-body">                            
                                <table class="table  table-bordered table-checkable order-column" id="ReportTable">                                                                    
                                     <thead>
                                        <tr>
                                            <th class="table-checkbox"></th>
                                            <th id="name_head">
                                                Region Name
                                            </th>
                                            <th id="second_name_head">
                                                Total Workshop 
                                            </th>
                                            <th>
                                              	Trainee Trained
                                            </th>
                                            <th>
                                               Avg CE %
                                            </th>
                                            <th id="height_ce">
                                                Highest CE %
                                            </th>
                                            <th id="lowest_ce">
                                                Lowest CE %                                            
                                            </th>
                                            <th>
                                                Actions                                           
                                            </th>
                                        </tr>
                                     </thead>
                                <tbody>     
                                </tbody>
                                </table>
                            </div>                            
                            <div id="TableDiv" class="row mt-10" >

                        </div>                        
                    </div>
                </div>                
            </div>            
        </div>        
    <div class="modal fade" id="LoadModalFilter" role="basic" aria-hidden="true" data-width="200">
    <div class="modal-dialog modal-lg">
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
        var FilterFrm = $('#FilterFrm');
        var form_error = $('.alert-danger', FilterFrm);
        var form_success = $('.alert-success', FilterFrm);
        var TotalWkshop=1;
        var oTable=null;
        var table=$('#ReportTable');
        var Company_id = "<?php echo $Company_id; ?>";
            $(".select2_rpt2").select2({
                placeholder: 'All Select',
                width: '100%'
            });
            $(".select2_rpt").select2({
                placeholder: 'Please Select',
                width: '100%'
            });
        jQuery(document).ready(function() {            
            $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
            });
                if (jQuery().datepicker) {
                    $('.date-picker').datepicker({
                        rtl: App.isRTL(),
                        orientation: "left",
                        autoclose: true,
                        format: 'dd-mm-yyyy'
                    });
                }
                if(Company_id !=""){
                    TableRefresh();
                }
        });
        
    table.on('click', ' tbody td .row-details', function () {
        var nTr = $(this).parents('tr')[0];
        if (oTable.fnIsOpen(nTr)) {
            /* This row is already open - close it */
            //$(this).addClass("row-details-close").removeClass("row-details-open");
            oTable.fnClose(nTr);
        } else {
            /* Open this row */
            //$(this).addClass("row-details-open").removeClass("row-details-close");
            oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr), 'details');

        }            
    });
    function getCompanywiseData(){
    
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    $('#wregion_id').empty();
                    $('#workshoptype_id').empty();
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
                            $('#wregion_id').empty();
                            $('#wregion_id').append(Oresult['RegionData']);
                            $('#workshoptype_id').empty();
                            $('#workshoptype_id').append(Oresult['WTypeData']);
                        }
                        customunBlockUI();
                    }
                });
            }
            function getTypeWiseWorkshop(){     
                $('#wsubregion_id').empty();
                $('#workshop_subtype').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshop_type = $('#workshoptype_id').val();
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
                            $('#workshop_subtype').empty();
                            $('#workshop_subtype').append(Oresult['WorkshopSubtypeData']);
                            $('#wsubregion_id').empty();
                            $('#wsubregion_id').append(Oresult['WorkshopSubregionData']);
                            }
                        customunBlockUI();
                    }
                });
            }
    function fnFormatDetails(oTable, nTr) {
        var aData = oTable.fnGetData(nTr);   
        var data='';
        $.ajax({
            type: "POST",
            data: {id: aData[0],company_id :$('#company_id').val(),reportby_id :$('#reportsby_id').val(),workshoptype_id :$('#workshoptype_id').val(),wregion_id :$('#wregion_id').val(),
                    wsubregion_id :$('#wsubregion_id').val(),workshop_subtype :$('#workshop_subtype').val()},
            url: "<?php echo base_url() . 'supervisor_reports/getTrainerData'; ?>",
            beforeSend: function () {
                               customBlockUI();
                            },
            async:false,
            success: function (Data) {
                 var Oresult = jQuery.parseJSON(Data);                        
                        var Table = Oresult['Table']; 
                data=Table;
                customunBlockUI();
            }
        });
        return data;
    }
        function TableRefresh(){            
            $('#trainertable').remove();
            var actionbtn=false;
            if($('#company_id').val() == ''){
                ShowAlret("Please select Company first.!!", 'error');
                return false;
            }
            if($('#reportsby_id').val() == ''){                                                
                $('#reportsby_id').select2("val","1");
                $("#height_ce").html(" Highest CE %");
                $("#lowest_ce").html("Lowest CE % ");
            }
            if($('#reportsby_id').val() == 1){
                $("#name_head").html("Region Name");
                $("#second_name_head").html("Total Workshop");
                $("#height_ce").html(" Highest CE %");
                $("#lowest_ce").html("Lowest CE % ");
            }
            if($('#reportsby_id').val() == 2){
                $("#name_head").html("Workshop Type Name");
                $("#second_name_head").html("Total Workshop");
                $("#height_ce").html(" Highest CE %");
                $("#lowest_ce").html("Lowest CE % ");
            }
            if($('#reportsby_id').val() == 3){
                $("#name_head").html("Trainer Name");
                $("#second_name_head").html("Total Workshop");
                $("#height_ce").html(" Highest CE %");
                $("#lowest_ce").html("Lowest CE % ");
                //oTable.column( 1 ).visible( false );
                actionbtn=true;
            }
            
            if($('#reportsby_id').val() == 4){
                $("#name_head").html("Workshop");
                $("#second_name_head").html("Workshop Type Name");
                $("#height_ce").html("Best Topic");
                $("#lowest_ce").html("Worst Topic");
                
                
            }
                oTable =table.dataTable({
                    destroy: true,
                    "language": {
                        "aria": {
                            "sortAscending": ": activate to sort column ascending",
                            "sortDescending": ": activate to sort column descending"
                        },
                        "emptyTable": "No Workshop data available in table",
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
                    "bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "pageLength": 10,            
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
                        {'width': '80px','orderable': true,'searchable': true,'targets': [0],visible:false}, 
                        {'width': '200px','orderable': false,'searchable': false,'targets': [1]}, 
                        {'width': '80px','orderable': false,'searchable': false,'targets': [2]}, 
                        {'width': '80px','orderable': false,'searchable': false,'targets': [3]}, 
                        {'width': '60px','orderable': false,'searchable': false,'targets': [4]},
                        {'width': '60px','orderable': false,'searchable': false,'targets': [5]},
                        {'width': '60px','orderable': false,'searchable': false,'targets': [6]},
                        {'width': '60px','orderable': false,'searchable': false,'targets': [7],visible:actionbtn}
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'supervisor_reports/getReportTableData'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'company_id', value: $('#company_id').val()});
                        aoData.push({name: 'reportsby_id', value: $('#reportsby_id').val()});
                        aoData.push({name: 'workshoptype_id', value: $('#workshoptype_id').val()});
                        aoData.push({name: 'wregion_id', value: $('#wregion_id').val()});
                        aoData.push({name: 'workshop_subtype', value: $('#workshop_subtype').val()});
                        aoData.push({name: 'wsubregion_id', value: $('#wsubregion_id').val()});
                        
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
    function getTrainerDashboard(trainer_id,cmp_id,reportby_id){        
        if(trainer_id !='' && cmp_id !=''){         
            location.href = "Trainer_dashboard/load_quick_statistics?company_id=" + cmp_id + "&user_id="+trainer_id;
        }    
   }
   function get_trainerlist(sub_id,id){      
         $.ajax({
                                type: "POST",
                                data: $('#FilterFrm').serialize(),
//                                async: false,
                                url: "<?php echo $base_url; ?>supervisor_reports/trainer_list/" + sub_id + "/" + id,
                                success: function (msg) {
                                  $("#modal-body").html(msg);
                                  $("#LoadModalFilter").modal();
                                }
                            });
   }
    </script>
</body>
</html>