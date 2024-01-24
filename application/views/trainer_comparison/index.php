<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>        
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <style>
            table tr {
                background-color: #ffffff;
            }
            .table.table-light thead tr th{
                color: #000000 !important;
            }
            .table.table-light tbody tr td{
                color: #000000 !important;
            }
            .scroll {
  border: 0;
  border-collapse: collapse;
}

.scroll tr {
  display: flex;
}

.scroll td {
  padding: 3px;
  flex: 1 auto;
  border: 1px solid #aaa;
  width: 1px;
  word-wrap: break;
}

.scroll thead tr:after {
  content: '';
  overflow-y: scroll;
  visibility: hidden;
  height: 0;
}

.scroll thead th {
  flex: 1 auto;
  display: block;
  border: 1px solid #000;
}

.scroll tbody {
  display: block;
  width: 100%;
  overflow-y: auto;
  height: 400px;
}
        </style>
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white">
        <div class="page-wrapper">
            <?php $this->load->view('inc/inc_header'); ?>
            <div class="clearfix"> </div>
            <div class="page-container">
                <?php $this->load->view('inc/inc_sidebar'); ?>
                <div class="page-content-wrapper">
                    <div class="page-content">

                        <!-- PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li>
                                    <a href="javascript:;">Trainer Reports</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Trainer Comparison report</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">


                                <!-- <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                    <i class="icon-calendar"></i>&nbsp;
                                    <span class="thin uppercase hidden-xs"></span>&nbsp;
                                    <i class="fa fa-angle-down"></i>
                                </div> -->
                            </div>
                        </div>
                        <!-- PAGE BAR -->
                        <h1 class="page-title"> Trainer Comparison report
                            <!-- <small>- overview statistics, charts, recent workshop and reports</small> -->
                        </h1>
                        
                        <div class="row">
                            <div class="col-lg-12 col-xs-12 col-sm-12">
                                <div class="panel-group accordion" id="accordion3">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2">
                                                   Filter Set </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_3_2" class="panel-collapse ">
                                            <div class="panel-body" >
                                                <form id="frmFilterDashboard" name="frmFilterDashboard" method="post">
                                                    <div class="row margin-bottom-10">
                                                        <?php if ($company_id == "") { ?>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Company&nbsp;<span class="required"> * </span></label></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getCompanyTrainer();">
                                                                        <option value="" >Please select</option>
                                                                            <?php foreach ($company_array as $cmp) { ?>
                                                                            <option value="<?php echo $cmp->id; ?>" ><?php echo $cmp->company_name; ?></option>
                                                                            <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                         <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Trainer&nbsp;<span class="required"> * </span></label></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="user_id" name="user_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" onchange="getWorkshop();">
                                                                        <option value="0">All Trainer</option>
                                                                        <?php
                                                                        if (isset($TrainerResult)) {
                                                                        foreach ($TrainerResult as $trainer) {
                                                                            ?>
                                                                                <option value="<?= $trainer->userid; ?>" <?php echo ($trainer->userid == $trainer_id ? 'selected' : ''); ?>><?php echo $trainer->fullname; ?></option>
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
                                                                <label class="control-label col-md-3">Workshop Type&nbsp;</label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_type_id" name="workshop_type_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" onchange="getWorkshop();">
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
                                                                    <select id="workshop_subtype" name="workshop_subtype" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%" onchange="getWSubTypewiseData();">
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
                                                                    <select id="wregion_id" name="wregion_id" class="form-control input-sm select2_rpt2" placeholder="Please select" style="width: 100%" onchange="getWorkshop();">
                                                                        <option value="0">All Region</option>
                                                                        <?php
                                                                        if (isset($RegionData)) {
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
                                                                <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label></label>
                                                                <div class="col-md-9" style="padding:0px;">
                                                                    <select id="workshop_id" name="workshop_id" class="form-control input-sm select2_rpt2" placeholder="Please select"  style="width: 100%" >
                                                                        <option value="">Please select</option>
                                                                        <?php
                                                                        if (isset($WorkshopResultSet)) {
                                                                        foreach ($WorkshopResultSet as $wd) {
                                                                            ?>
                                                                            <option value="<?= $wd->workshop_id; ?>"><?php echo $wd->workshop_name; ?></option>
                                                                            <?php
                                                                        }
}
?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="clearfix margin-top-20"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="col-md-offset-10 col-md-2 text-right">
                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="dashboard_refresh()">Add Set</button>
                                                                <!-- <button type="button" class="btn blue-hoki btn-sm" onclick="dashboard_reset()">Reset</button> -->
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
                        <!-- STAT FIRST ROW -->
                        <div class="row">

                            <!-- STAT BOX -->
                            <div class="col-sm-12">
                                <div class="portlet light bordered" style="padding: 12px 20px 10px !important;">
                                    <div class="portlet-title potrait-title-mar">
                                        <div class="caption col-lg-12 col-xs-12 col-sm-12">
                                            <i class="icon-bar-chart font-dark hide"></i>
                                            <span class="caption-subject font-dark bold uppercase">Workshop Set</span>
                                            <span style='float:right;font-size:13px;font-weight:bold;color:red;'>(Add workshop set from above filter set panel)</span>
                                        </div>

                                    </div>
                                    <div class="portlet-body">
                                        <div class="table-scrollable table-scrollable-borderless" style="height:200px;">
                                            <table class="table table-hover table-light" id="wksh-list">
                                                <thead style="display: block;">
                                                    <tr class="uppercase">
                                                        <th width="24%">WORKSHOP NAME</th>
                                                        <th width="16%">TRAINER NAME</th>
                                                        <th width="12%">PRE ACCURACY</th>
                                                        <th width="12%">POST ACCURACY</th>
                                                        <th width="12%">C.E</th>
                                                        <th width="28%">ACTIONS</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="display: block;height: 165px;overflow-y: auto;overflow-x: hidden;">
                                                    <tr>
                                                        <td colspan="5">
                                                            Please select workshop set from above filter set panel.
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- STAT BOX -->
                        </div>    
                            <!-- WORKSHOP COMPARISON  -->
                            <div class="row" id="comparison-table">
                                
                            </div>
                            <!-- WORKSHOP COMPARISON  -->


                            <!-- CHART MODAL -->
                            <div id="chart-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="800">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                            <h4 class="modal-title" id="modal_title"></h4>
                                        </div>
                                        <div class="modal-body" id="popupchart">
                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-md-12 text-right ">  
                                                <button type="button" class="btn btn-orange" class="close" data-dismiss="modal" aria-hidden="true">
                                                    <span class="ladda-label">Close</span>
                                                </button>
                                                
                                            </div>
                                        </div>
                                    </div>    
                                </div>    
                            </div>
                            <!-- CHART MODAL -->

                        
                        <!-- STAT FIRST ROW -->

                </div>
        <?php //$this->load->view('inc/inc_quick_sidebar');  ?>
            </div>
<?php //$this->load->view('inc/inc_footer');  ?>
        </div>
 </div>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script>
            var trainer_id = "<?php echo $trainer_id; ?>";
            var company_id = "<?php echo $Supcompany_id; ?>";
            var cnt = 1;
            function Redirect(url)
            {
                // window.location = url;
                window.open(url, '_blank');
            }
            jQuery(document).ready(function () {
                $(".select2_rpt").select2({
                placeholder: 'All Select',
                width: '100%'
            });
            $(".select2_rpt2").select2({
                placeholder: 'Please Select',
                width: '100%'
            });
                if (trainer_id != '') {
                    $('#company_id').select2("val", "" + company_id);
                }
                // $('#company_id').val(< ?php //echo $company_id;?>).trigger('change');
                // $('#workshop_type_id').val(< ?php //echo $workshop_type_id;?>).trigger('change');
                // $('#user_id').val(< ?php //echo $trainer_id;?>).trigger('change');
                // $('#workshop_id').val(< ?php //echo $workshop_id;?>).trigger('change');
                // dashboard_refresh();


                
            });
            function getCompanyTrainer() {
                $('#workshop_id').empty();
                $('#wsubregion_id').empty();
                $('#workshop_subtype').empty();
                $('#workshop_id').empty();
                var compnay_id = $('#company_id').val();
                if (compnay_id == "") {
                    $('#user_id').empty();
                    $('#workshop_type_id').empty();
                    $('#wregion_id').empty();
                    return false;
                }
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val()},
                    //async: false,
                    url: "<?php echo $base_url; ?>common_controller/ajax_companywise_data/0",
                    beforeSend: function () {
                            customBlockUI();
                        },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            $('#user_id').empty();
                            $('#user_id').append(Oresult['TrainerData']);
                            $('#wregion_id').empty();
                            $('#wregion_id').append(Oresult['RegionData']);
                            $('#workshop_type_id').empty();
                            $('#workshop_type_id').append(Oresult['WTypeData']);
                            $('#workshop_id').append(Oresult['WorkshopData']);
                        }
                        customunBlockUI(); 
                    }
                });
            }
            function getWorkshop() {
                $('#workshop_id').empty();
                $('#wsubregion_id').empty();
                $('#workshop_subtype').empty();
                var compnay_id = $('#company_id').val();
                if (compnay_id == "") {
                    return false;
                }
                var workshop_type = $('#workshop_type_id').val();
                var workshop_region = $('#wregion_id').val();
                var user_id = $('#user_id').val();
                $.ajax({
                    type: "POST",
                    data: {company_id: compnay_id, workshoptype_id: workshop_type, region_id: workshop_region, user_id: user_id},
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
            function getWSubTypewiseData() {
                $('#topic_id').empty();
                            $('#workshop_id').empty();
                var compnay_id = $('#company_id').val();
                if (compnay_id == "") {
                    return false;
                        }
                var workshoptype_id = $('#workshop_type_id').val();
                var region_id = $('#wregion_id').val();
                var subregion_id = $('#wsubregion_id').val();
                var workshopsubtype_id = $('#workshop_subtype').val();
                var user_id = $('#user_id').val();
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(), region_id: region_id, workshoptype_id: workshoptype_id, workshopsubtype_id: workshopsubtype_id, subregion_id: subregion_id, user_id: user_id},
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
            function dashboard_refresh() {
                if ($('#company_id').val() == "") {
                        ShowAlret("Please select Company.!!", 'error');
                        return false;
                } else if (!$('#user_id').val() || $('#user_id').val() == "") {
                        ShowAlret("Please select Trainer.!!", 'error');
                        return false;
                } else if (!$('#workshop_id').val() || $('#workshop_id').val() == "") {
                        ShowAlret("Please select Workshop.!!", 'error');
                        return false;                        
                    }
                var tdata = {company_id: $('#company_id').val(), user_id: $('#user_id').val(), workshop_type_id: $('#workshop_type_id').val(), workshop_id: $('#workshop_id').val()};
                    $.ajax({
                        type: "POST",
                        data: tdata,
                        //async: false,
                    url: "<?php echo $base_url; ?>trainer_comparison/load_workshop_table/" + cnt,
                        beforeSend: function () {
                            customBlockUI();
                        },
                        success: function (response) {
                            if (response != '') {
                            var json = jQuery.parseJSON(response);
                            var wksh_list = json['wksh_list'];
                                var comparison_panels = json['comparison_panels'];
                            if (cnt == 1) {
                                     $('#wksh-list tbody').empty();
                                }
                            if (comparison_panels != '') {
                                    $('#comparison-table').append(comparison_panels);
                                }
                            if (wksh_list != '') {
                                    $('#wksh-list tbody').append(wksh_list);
                                    cnt++;
                            } else {
                                    ShowAlret("No Data Found for selected Workshop.!!", 'error');
                                    
                                } 
                            }
                            customunBlockUI(); 
                        }
                    });                                    
            }
            function remove_workshop(cnt) {
                $('#tdata' + cnt).remove();
                $('#rdata' + cnt).remove();
            }
        </script>
    </body>
</html>