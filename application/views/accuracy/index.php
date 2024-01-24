<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> 
<html lang="en" class="ie8 no-js">
   <![endif]-->
<!--[if IE 9]> 
   <html lang="en" class="ie9 no-js">
      <![endif]-->
<!--[if !IE]><!-->
<html lang="en">

<head>
    <?php $this->load->view('inc/inc_htmlhead'); ?>
    <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
    <!--<link rel="stylesheet" type="text/css" href="< ?php echo $asset_url;?>assets/global/highcharts/css/highcharts.css" />-->
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
    <style>
        .arrow-row {
            width: 100%;
            height: auto;
        }

        .tr-background {
            background: #ffffff !important;
        }

        .wksh-td {
            color: #000000 !important;
            vertical-align: top !important;
        }

        .whsh-icon {
            float: right;
            position: absolute;
            top: 10px;
            right: 15px;
            color: #cccccc;
        }

        .potrait-title-mar {
            margin-left: -9px;
            margin-right: -9px;
        }

        .dashboard-stat {
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            -ms-border-radius: 4px;
            -o-border-radius: 4px;
            border-radius: 4px;
            background: #fff;
            padding: 5px 5px 5px;
            border: 1px solid #eef1f5;
            border-radius: 5px !important;
            background: aliceblue;
        }

        .dashboard-stat .display {
            height: 70px;
        }

        .dashboard-stat .display .number {
            text-align: center;
            /* float: left; */
            display: inline-block;
            width: 100%;
        }

        .dashboard-stat .display .number small {
            font-size: 12px;
            color: #777777;
            font-weight: 600;
            text-transform: uppercase;
            width: 100%;
        }

        .font-orange-sharp {
            /*color: #f1592a !important;*/
            margin: 0px !important;
            padding: 5px !important;
        }

        .tokenize-sample {
            width: 100%;
            height: auto
        }

        .theme-panel>.theme-options>.theme-option>span {
            width: 115px;
        }

        .no-padding {
            padding: 0px !important;
        }

        .page-content-white .page-title {
            margin: 20px 0;
            font-size: 22px;
            font-weight: 300 !important;
        }

        /* Assessment List Css */
        .table-scrollable>.table>tbody>tr>th,
        .table-scrollable>.table>tfoot>tr>td,
        .table-scrollable>.table>tfoot>tr>th,
        .table-scrollable>.table>thead>tr>th {
            white-space: normal;
        }

        .select2-results__option--highlighted[aria-selected] {
            background-color: #d9d9d9 !important;
            color: #fff !important;

        }

        .opt-green {
            color: #004369;
        }

        .opt-green:hover {
            background-color: #d9d9d9;
            color: #004369;
        }

        .opt-red {
            color: #db1f48;
        }

        .opt-red:hover {
            background-color: #d9d9d9;
            color: #db1f48;
        }

      /* Assessment List Css */
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
                
                    <form id="FilterFrm" name="FilterFrm" method="post">
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li>
                                    <a href="#">Dashboard</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Admin</span>
                                    <i class="fa fa-circle"></i>
                                    <span>Accuracy</span>
                                </li>
                            </ul>
                            <div class="col-md-1 page-breadcrumb"></div>
                        </div>
                        <!-- PAGE BAR -->

                        <div class="clearfix margin-top-20"></div>

                        <div class="row">
                            <!-- INDEX CHART -->
                        </div>
                        <!-- New Module -->
                        <style>
                            .btn.btn-outline.blue-oleo.active {
                                background-color: #004369;
                            }

                            .portlet>.portlet-title {
                                border-bottom: none;
                            }

                            .portlet.light.bordered>.portlet-title {
                                border-bottom: none;
                            }

                            .title-bar {
                                background: white;
                                padding: 0px 0px 47px 8px;
                                font-size: 17px;
                                position: static;
                            }

                            .header-right {
                                float: right;
                            }
                        </style>
                        <!-- Start Here-->
                            <div class='row'>
                                <div class='col-md-5'>
                                    <div class='title-bar'>
                                        <span class="caption-subject font-dark bold" style="font-family:'Catamaran'; position: absolute; top: 22px; left: 23px;">Competency understanding graph
                                            <a data-title="Competency understanding graph">
                                                <i class="icon-info font-black sub-title"></i>
                                            </a>
                                        </span>
                                        <div class="btn-group btn-group-devided header-right" data-toggle="buttons" style="margin-bottom: 0px; margin-top: 16px; margin-right: 5px;">
                                            <a data-toggle="modal" class="btn btn-transparent blue-oleo btn-no-border btn-outline btn-circle btn-sm active" href="#responsive-modal-accuracy">
                                                Filter
                                            </a>
                                        </div>
                                    </div>
                                    <div id='competency_understanding_graph'></div>
                                    <br>
                                </div>                        
                            </div>
                        <br>
                        <!--End Here-->

                    </form>
                </div>
            </div>
        </div>
     </div>
    <!-- SETTINGS BOX -->

    <!-- SETTINGS BOX -->
    <div class="modal fade" id="LoadModalFilter" role="basic" aria-hidden="true" data-width="600">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-body" id="modal-body">
                <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                <span>
                    &nbsp;&nbsp;Loading... </span>
                </div>
            </div>
        </div>
    </div>

    <!--Adoption By Team Filter Model  -->
    <div id="responsive-modal-accuracy" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="frmModalForm" name="frmModalForm" onsubmit="return false;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Filter</h4>
                </div>
                <div class="modal-body">
                    <div id='dsk' style="display: none">&nbsp;</div>
                    <div class="row">
                        <div class="col-md-11">
                            <div class="form-group last">
                            <label>Modules</label>
                            <select id="process_assessment_id" name="process_assessment_id" class="form-control input-sm select2" placeholder="Please select">
                                <option value="">Select</option>
                                <?php
                                if (isset($assessment)) {
                                    foreach ($assessment as $adata) {
                                ?>
                                        <option value="<?php echo  $adata->assessment_id; ?><?php echo ',' . $adata->report_type; ?>"><?php echo $adata->assessment . ' - [' . $adata->status . ']'; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="col-md-12 text-right ">
                        <!-- <button type="button" class="btn btn-orange" id="btnIndexadoptionFilter"> -->
                        <button type="button" class="btn btn-orange" onclick="Competency_understanding_graph()">
                            <span class="ladda-label">Apply</span>
                        </button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Here -->

    <!-- SETTINGS BOX -->
    <div id="responsive-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="200">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="frmModalForm" name="frmModalForm" onsubmit="return false;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Filter</h4>
                </div>
                <div class="modal-body">
                    <div id='dsk' style="display: none">&nbsp;</div>
                    <div class="row">
                        <div class="col-md-11">
                            <div class="form-group last">
                            <label>Month</label>
                            <select id="month" name="month" class="form-control input-sm select2" placeholder="Please select" onchange="getWeek()">
                                <?php foreach (range(1, 12) as $month) :
                                    $monthPadding = str_pad($month, 2, "0", STR_PAD_LEFT);
                                    $fdate = date("F", strtotime(date('Y') . "-$monthPadding-01"));
                                    echo '<option value="' . $monthPadding . '" ' . ($monthPadding == date('m') ? 'selected' : '') . '>' . $fdate . '</option>';
                                endforeach; ?>
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-11">
                            <div class="form-group last">
                            <label>Week</label>
                            <select id="week" name="week" class="form-control input-sm select2" placeholder="Please select">
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-11">
                            <div class="form-group last">
                            <label>Year</label>
                            <select id="year" name="year" class="form-control input-sm select2" placeholder="Please select">
                                <option value="<?php echo date('Y') ?>"><?php echo date('Y') ?></option>
                                <option value="<?php echo '2021' ?>"><?php echo '2021' ?></option>
                                <option value="<?php echo '2020' ?>"><?php echo '2020' ?></option>
                                <option value="<?php echo '2019' ?>"><?php echo '2019' ?></option>
                            </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 text-right ">
                        <button type="button" class="btn btn-orange" id="btnIndexchartFilter">
                            <span class="ladda-label">Apply</span>
                        </button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- SETTINGS BOX -->
    <?php $this->load->view('inc/inc_footer_script'); ?>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/highcharts/highstock.js"></script>

    <!-- Graph Export js -->
    <script src='<?php echo $asset_url; ?>assets/global/highcharts/modules/exporting.js'></script>
    <script src='<?php echo $asset_url; ?>assets/global/highcharts/modules/export-data.js'></script>
    <!-- End Here -->

    <!-- <script src="< ?php echo $asset_url;?>assets/global/highcharts/highcharts.js"></script>-->
    <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <!-- <script src="< ?php echo $asset_url; ?>assets/global/highcharts/modules/exporting.js"></script> -->

    <script>
        var firsttimeload = 1;
        var Company_id = '<?php echo $company_id ?>';
        var StartDate = "<?php echo $start_date; ?>";
        var EndDate = "<?php echo $end_date; ?>";
        var base_url = "<?php echo $base_url; ?>";
        var step = 0;
    </script>
    <script src="<?php echo $asset_url; ?>assets/customjs/accuracy.js"></script>
    <script>
        getWeek();
        Competency_understanding_graph();
    </script>
</body>

</html>