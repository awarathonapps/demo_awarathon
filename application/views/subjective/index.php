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
        <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
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
                                    <span>Workshop Reports</span>
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Subjective </span>
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
                                            <a class="accordion-toggle accordion-toggle-styled " data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                               Report </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_3_2" class="panel-collapse">
                                        <div class="panel-body" >
                                            <form id="FilterFrm" name="FilterFrm" method="post" action="<?php echo $base_url; ?>subjective/sample_xls">
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                           <label class="control-label">Workshop Type</label>
                                                           <div class="col-md-12" style="padding:0px;">
                                                           <select id="workshop_type" name="workshop_type" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%"  onchange="getWTypewiseData();" >
                                                                <?php if(count($WTypeData)>0){
                                                                    echo '<option value="0">All Type</option>';
                                                                    foreach ($WTypeData as $Rgn) { ?>
                                                                    <option value="<?= $Rgn->id; ?>" ><?php echo $Rgn->workshop_type; ?></option>
                                                                <?php } }?>
                                                            </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">       
                                                            <div class="form-group">
                                                                <label class="control-label">Workshop Sub-type</label>
                                                                <div class="col-md-12" style="padding:0px;">
                                                                <select id="workshop_subtype" name="workshop_subtype" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%" onchange="getWSubTypewiseData();">
                                                                    <option value="">All Sub-type</option>

                                                                </select>
                                                                </div>
                                                            </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label"> Workshop Region &nbsp;</label>
                                                                <div class="col-md-12" style="padding:0px;">
                                                                    <select id="wregion_id" name="wregion_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getWTypewiseData();">
                                                                              <?php
                                                                            if (isset($RegionData)) {
                                                                                echo '<option value="0">All Region</option>';
                                                                                foreach ($RegionData as $Rdata) {?>
                                                                                    <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
                                                                                  <?php
                                                                                }
                                                                            }
                                                                            ?> 
                                                                    </select>
                                                                </div>
                                                            </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Workshop Sub-region &nbsp;</label>
                                                            <div class="col-md-12" style="padding:0px;">
                                                                <select id="wsubregion_id" name="wsubregion_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getWSubRegionwiseData();">
                                                                    <option value="">Select Sub-region</option>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                     </div>                                                     
                                                </div>
                                                <div class="row margin-bottom-10">
                                                <div class="col-md-3">
                                                       <div class="form-group">
                                                            <label class="control-label">Workshop&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-12" style="padding:0px;">
                                                                <select id="workshop_id" name="workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getWorkshopwiseData()">
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
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Trainee Region &nbsp;</label>
                                                            <div class="col-md-12" style="padding:0px;">
                                                                <select id="region_id" name="region_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getTregionwiseData();" >
                                                                          <?php
                                                                        if (isset($RegionData)) {
                                                                            echo '<option value="0">All Region</option>';
                                                                            foreach ($RegionData as $Rdata) {
                                                                                ?>
                                                                                <option value="<?= $Rdata->id; ?>"><?php echo $Rdata->region_name; ?></option>
                                                                              <?php
                                                                            }
                                                                        }
                                                                        ?> 
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Trainee&nbsp;</label>
                                                            <div class="col-md-12" style="padding:0px;">
                                                                <select id="user_id" name="user_id" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%">
                                                                    <option value="">Select Trainee</option>
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
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">    
                                                         <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <div class="col-md-12" style="padding:0px;">
                                                                <button type="button" onclick="sample_data()" class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Download Excel</button>
                                                            </div>
                                                         </div>
                                                     </div>
                                                </div>                                              
                                                
                                                </form> 
                                                    <form id="frmReorts" name="frmReorts" method="post" action="<?php echo $base_url; ?>subjective/exportReport" enctype="multipart/form-data">
                                                        <div class="col-md-3 ">    
                                                            <div class="form-group col-md-12">
                                                                <label class="control-label col-md-12">Upload File &nbsp;<span class="required"> * </span></label>
                                                                        <div class="form-control fileinput fileinput-new col-md-6" style="width: 100%;border: none;height:auto;" data-provides="fileinput">
                                                                                <div class="input-group input-large">
                                                                                        <div class="form-control uneditable-input span3" data-trigger="fileinput">
                                                                                                <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                                                                </span>
                                                                                        </div>
                                                                                        <span class="input-group-addon btn default btn-file">
                                                                                        <span class="fileinput-new">
                                                                                        Select file </span>
                                                                                        <span class="fileinput-exists">
                                                                                        Change </span>
                                                                                        <input type="file" name="filename" id="filename" >
                                                                                        </span>
                                                                                        <a href="javascript:;" id="RemoveFile" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                                                        Remove </a>
                                                                                </div>
                                                                        </div><br/>
                                                                    <span class="text-muted">(only .xlsx and .xls allowed)</span>
                                                            </div>
                                                        </div>
                                                        </form>
                                               
                                                <div class="clearfix margin-top-20"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="upload_data()">Submit</button>
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
                                <div class="portlet light bordered">
                                    
                                        <div class="portlet-title">
                                            <div class="caption caption-font-24">
                                               Subjective Report
                                               <div class="tools"> </div>
                                            </div>
                                            <?php if($acces_management->allow_export){ ?>
                                            <div class="actions">
<!--                                                 <div class="col-md-3">    
                                                            <div class="form-group col-md-12">
                                                                <label class="control-label col-md-6">Choose File &nbsp;<span class="required"> * </span></label>
                                                                        <div class="form-control fileinput fileinput-new col-md-6" style="width: 100%;border: none;height:auto;" data-provides="fileinput">
                                                                                <div class="input-group input-large">
                                                                                        <div class="form-control uneditable-input span3" data-trigger="fileinput">
                                                                                                <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                                                                </span>
                                                                                        </div>
                                                                                        <span class="input-group-addon btn default btn-file">
                                                                                        <span class="fileinput-new">
                                                                                        Select file </span>
                                                                                        <span class="fileinput-exists">
                                                                                        Change </span>
                                                                                        <input type="file" name="filename" id="filename" >
                                                                                        </span>
                                                                                        <a href="javascript:;" id="RemoveFile" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                                                        Remove </a>
                                                                                </div>
                                                                        </div><br/>
                                                                    <span class="text-muted">(only .xlsx and .xls allowed)</span>
                                                            </div>
                                                     </div>-->
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
                                                        <th>Sr. No.</th>
                                                        <th>Employee Code</th>
                                                        <th>Employee Name</th>
                                                        <th>Designation</th>
                                                        <th>Region</th>
                                                        <th>Online Test </th>
                                                        <th>Out Off</th>
                                                        <th>Recitement</th>
                                                        <th>Out Off</th>
                                                        <th>Demo</th>
                                                        <th>Out Off</th>
                                                        <th>Written Test</th>
                                                        <th>Out Off</th>
                                                        <th>Total</th>
                                                        <th>Per</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="workshodata"></tbody>
                                            </table>
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
        <?php $this->load->view('inc/inc_footer_script');?>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script>
            var search=1;
            var frmReorts = document.frmReorts;
             var frmdata = document.FilterFrm;
//             var oTable = null;
               
             $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            }); 

             var oTable = $("#index_table").dataTable({
                 "bAutoWidth": true,
                    "bServerSide": false,
                    "bScrollCollapse": true, 
                    "searching": true,
                    "bPaginate": true,
                    "ordering": false,
                    "bDestroy": true,
                    //"bLengthChange": false,
                    "columnDefs": [
                        {"orderable": false, "targets": [0]},
                        {"orderable": true, "targets": [0]}
                    ],
                    "aLengthMenu": [[-1, 25, 50, -1], [10, 25, 50, "All"]],
                    'iDisplayLength': '-1',
    //                                                            "sScrollY": "500px",
    //                                                            "sScrollX": "100%",
    //                                                            "sScrollXInner": "100%",

             });
//            DatatableRefresh();
            //getCompanywiseData();
            function getCompanywiseData(){
                var compnay_id =$('#company_id').val();
                if(compnay_id=="" ){
                    $('#user_id').empty();
                    $('#designation_id').empty();
                    $('#wregion_id').empty();
                    $('#workshop_type').empty();
                    $('#region_id').empty();
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
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TraineeData']);
                            $('#region_id').empty();
                            $('#region_id').append(Oresult['RegionData']);
                            $('#workshop_type').empty();
                            $('#workshop_type').append(Oresult['WTypeData']);
                            $('#workshop_id').empty();
                            $('#workshop_id').append(Oresult['WorkshopData']); 
                            $('#wregion_id').empty();
                            $('#wregion_id').append(Oresult['RegionData']);
                            $('#designation_id').empty();
                            $('#designation_id').append(Oresult['DesignationData']);
                        }
                        customunBlockUI();
                    }
                });
            }
             function getTregionwiseData(){
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                     $('#user_id').empty();
                    return false;
                }
                var region_id = $('#region_id').val();
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id,region_id: $('#region_id').val(),workshop_type:$('#workshop_type').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_tregionwise_data",
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
             function getWTypewiseData(){      
                $('#wsubregion_id').empty();
                $('#workshop_subtype').empty();
                $('#workshop_id').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshop_type = $('#workshop_type').val();
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
                            $('#workshop_id').empty();
                            $('#workshop_id').append(Oresult['WorkshopData']); 
                            $('#workshop_subtype').empty();
                            $('#workshop_subtype').append(Oresult['WorkshopSubtypeData']);
                            $('#wsubregion_id').empty();
                            $('#wsubregion_id').append(Oresult['WorkshopSubregionData']);
                            }
                        customunBlockUI();
                    }
                });
            }
            function getWSubTypewiseData(){
                $('#workshop_id').empty();                                
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshopsubtype_id = $('#workshop_subtype').val();
                var workshoptype_id = $('#workshop_type').val();
                var region_id       = $('#wregion_id').val();                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),region_id:region_id,workshoptype_id: workshoptype_id,workshopsubtype_id:workshopsubtype_id},
                    //async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
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
            function getWSubRegionwiseData(){
                $('#workshop_id').empty();                                
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var subregion_id = $('#wsubregion_id').val();
                var workshopsubtype_id = $('#workshop_subtype').val();
                var workshoptype_id = $('#workshop_type').val();
                var region_id       = $('#wregion_id').val();                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),region_id:region_id,workshoptype_id: workshoptype_id,workshopsubtype_id:workshopsubtype_id,subregion_id:subregion_id},
                    //async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_workshoptypewise_data",
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
            function getWorkshopwiseData(){     
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshop_id = $('#workshop_id').val();                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),workshop_id: workshop_id},
                    //async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_workshopwise_data",
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
            function sample_data(){    

                var compnay_id =$('#company_id').val();
                var workshop_id =$('#workshop_id').val();

             if(compnay_id==""){
                  ShowAlret("Please select Company first.!!", 'error');
                  return false;
              }else if(workshop_id==""){
                  ShowAlret("Please select Workshop first.!!", 'error');
                  return false;
              }else {
                    $("#FilterFrm").submit();
                }
            }
           function upload_data() {
             
             var filename =$('#filename').val();
            
             if(filename==""){
                  ShowAlret("Please Choose file first.!!", 'error');
                  return false;
              }
                $('#errordiv').hide();
                var file_data = $('#filename').prop('files')[0];
                var form_data = new FormData();
                form_data.append('filename', file_data);
                var other_data = $('#FilterFrm').serializeArray();
                $.each(other_data, function (key, input) {
                    form_data.append(input.name, input.value);
                });
                $.ajax({
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: "POST",
                    url: '<?php echo base_url() . 'subjective/uploads_xls/'; ?>',
                    data: form_data,
                    success: function (Odata) {
                        //alert(result);
                        var Data = $.parseJSON(Odata);
                        if (Data['success']) {
//                             ShowAlret(Data['Msg'], 'success');
//                            oTable.clear();
                             oTable.fnClearTable();
                            $('#index_table tbody').append(Data['tdata']); 
//                             tableRefresh();
                        } else {
                            $('#errordiv').show();
                            $('#errorlog').html(Data['Msg']);
                          
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
                    }
                });
                }
                 function tableRefresh() {
                        $('#index_table').dataTable({
                            "bAutoWidth": true,
                            "bServerSide": false,
                            "bScrollCollapse": true, 
                            "searching": true,
                            "bPaginate": true,
                            "ordering": false,
                            "bDestroy": true,
                            //"bLengthChange": false,
                            "columnDefs": [
                                {"orderable": false, "targets": [0]},
                                {"orderable": true, "targets": [0]}
                            ],
                            "aLengthMenu": [[-1, 25, 50, -1], [10, 25, 50, "All"]],
                            'iDisplayLength': '-1',
                            "sScrollY": "500px",
                            "sScrollX": "100%",
//                                                           
                        });

                    }

            function ResetFilter() {
                $('.select2me,.select2_rpt2').select("val","");
                $('.select2me,.select2_rpt2').val(null).trigger('change');
                document.FilterFrm.reset();

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
</script>
</body>
</html>