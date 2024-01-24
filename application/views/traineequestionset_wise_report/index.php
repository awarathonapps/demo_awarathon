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
                                    <span>Workshop Reports</span>
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Trainee Questions Set Report</span>
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
                                                                <label class="control-label col-md-3">Workshop Type</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshop_type" name="workshop_type" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" onchange="getWTypewiseData();">
                                                                    
                                                                    <?php if(count($WTypeData)>0){
                                                                        echo '<option value="0">All Type</option>';
                                                                        foreach ($WTypeData as $Rgn) { ?>
                                                                        <option value="<?= $Rgn->id; ?>" ><?php echo $Rgn->workshop_type; ?></option>
                                                                    <?php } }?>
                                                                </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Workshop Sub-type</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshop_subtype" name="workshop_subtype" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getWSubTypewiseData();">
                                                                    <option value="">All Sub-type</option>

                                                                </select>
                                                                </div>
                                                            </div>
                                                    </div>    
                                                </div>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3"> Workshop Region &nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
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
                                                    
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop Sub-region &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="wsubregion_id" name="wsubregion_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getWSubTypewiseData();">
                                                                    <option value="">Select Sub-region</option>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                     </div>                                                     
                                                </div>
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshop_id" name="workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getWorkshopwiseData();" >
                                                                    <option value="">Please Select</option>
                                                                    <?php 
                                                                    if(isset($WorkshopData)){
                                                                        foreach ($WorkshopData as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"><?php echo $cmp->workshop_name; ?></option>
                                                                    <?php }} ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                     </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainee Region &nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="tregion_id" name="tregion_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getTregionwiseData();" >
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
                                                </div>    
                                                <div class="row margin-bottom-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainee&nbsp;</label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="user_id" name="user_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%">
                                                                    <option value="">All Trainee</option>
                                                                    <?php 
                                                                    if(isset($TraineeData)){
                                                                        foreach ($TraineeData as $cmp) { ?>
                                                                        <option value="<?= $cmp->user_id; ?>"><?php echo $cmp->traineename; ?></option>
                                                                    <?php }} ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>                                                                                                                                                                                                                
                                                </div>
                                                <div class="clearfix margin-top-10"></div>
                                                <div class="col-md-12">
                                                    <div class="col-md-offset-10 col-md-2 text-right">
                                                        <button type="button" class="btn blue-hoki btn-sm" onclick="setFilter()">Search</button>
<!--                                                        <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter()">Reset</button>-->
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
                                    <form id="frmReorts" name="frmReorts" method="post" action="<?php echo base_url() . 'traineequestionset_wise_report/exportReport' ?>">
                                        <div class="portlet-title">
                                            <div class="caption caption-font-24">
                                              Trainee Questions Set Report
                                               <div class="tools"> </div>
                                            </div>
                                            <?php if($acces_management->allow_export){ ?>
                                            <div class="actions">
                                                <div class="btn-group pull-right">
                                                    <button type="button" onclick="exportConfirm()"
                                                    autofocus="" accesskey="" name="export_excel" id="export_excel"  class="btn orange btn-sm btn-outline"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                    &nbsp;&nbsp;

                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div class="clearfix margin-top-20"></div>
                                        <div class="portlet-body" id="AppendTable">
                                            
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
        <script>
            var search=1;
			var search_flag=0;
            var frmReorts = document.frmReorts;
            var table = $('#index_table');
            $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            });                        
            function getCompanywiseData(){
                var compnay_id =$('#company_id').val();
                if(compnay_id==""){
                    $('#user_id').empty();
                    $('#workshop_id').empty();
                    $('#wregion_id').empty();
                    $('#tregion_id').empty();                    
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
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TraineeData']); 
                            $('#wregion_id').empty();
                            $('#wregion_id').append(Oresult['RegionData']);
                            $('#tregion_id').empty();
                            $('#tregion_id').append(Oresult['RegionData'])
                            $('#workshop_type').empty();
                            $('#workshop_type').append(Oresult['WTypeData']);                            
                        }
                        customunBlockUI();
                    }
                });
            }
            function ResetFilter() {
                $('.select2me,.select2_rpt2').select("val","");
                //$('.select2me,.select2_rpt2').val(null).trigger('change');
                document.FilterFrm.reset();
                setFilter();
            }
            function getTregionwiseData(){
               $('#user_id').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }                     
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id,region_id: $('#tregion_id').val(),workshop_type:$('#workshop_type').val()},
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
            function getWorkshopwiseData(){                
                $('#user_id').empty();
                var compnay_id = $('#company_id').val();
                if(compnay_id==""){
                    return false;
                }
                var workshop_id = $('#workshop_id').val();
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id,workshop_id: workshop_id},
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
            function getWTypewiseData(){                
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
                var subregion_id = $('#wsubregion_id').val();
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
            function setFilter() {                
                var company_id = $('#company_id').val();
                var workshop_id =$('#workshop_id').val();                 
                var trainee_id = $('#user_id').val();
                if(company_id==""){
                    ShowAlret("Please select Company first.!!", 'error');
                    return false;
                }
                if(workshop_id == '' || workshop_id== null){                    
                    ShowAlret("Please select Workshop.!!", 'error');
                    return false;
                }                                                
                $.ajax({
                    type: "POST",
                    //async: false,
                    data: {company_id:company_id,workshop_id:workshop_id,trainee_id:trainee_id},
                    url: "<?php echo $base_url . 'traineequestionset_wise_report/getqset_tablecolumn'; ?>",
                    success: function (msg) {
                        $("#AppendTable").html(msg);
                         $('#AppendTable').unblock();
                        // DatatableRefresh();    
                    }
                });
            }
            function DatatableRefresh() {
				search_flag = 1;
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
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "order": [
                        [0, "asc"]
                    ],
					 
                    "pageLength": 10,
                    "pagingType": "bootstrap_full_number",                    
                    "processing": true,
                    "serverSide": false,
                    "sAjaxSource": "<?php echo $base_url . 'traineequestionset_wise_report/DatatableRefresh'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'company_id', value: $('#company_id').val()});
                        aoData.push({name: 'user_id', value: $('#user_id').val()});
                        aoData.push({name: 'workshop_id', value: $('#workshop_id').val()});                                                                                                                        
                        $.getJSON(sSource, aoData, function (json) {                 
                            fnCallback(json['output']);                            
                        });
                    },
                    "fnRowCallback": function (nRow, aData, iDisplayIndex) {                        
                        return nRow;
                    }
                    , "fnFooterCallback": function (nRow, aData) {
                    }
                });
            }
            function exportConfirm(){
				if(search_flag){
					var compnay_id =$('#company_id').val();
					if(compnay_id==""){
						ShowAlret("Please select Company first.!!", 'error');
						return false;
					}
					var workshop_id =$('#workshop_id').val();
					if(workshop_id == '' || workshop_id== null){                    
						ShowAlret("Please select Workshop.!!", 'error');
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
				}else{
					ShowAlret("First Search the record.!!", 'error');
					return false;
				}
                
            }
</script>
</body>
</html>