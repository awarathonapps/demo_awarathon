<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html>
    <!--<![endif]-->
    <head>
        <?php
        $Base_url = base_url();
        $this->load->view('inc/inc_htmlhead');
        ?>
        <link href="<?php echo $Base_url; ?>assets/plugins/jquery-datatable/css/jquery.dataTables.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $Base_url ?>assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="<?= $Base_url; ?>assets/plugins/pickadate/themes/default.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?php echo $Base_url ?>assets/css/jquery-confirm.css"/>
    </head>
    <!-- END HEAD -->
    <!-- BEGIN BODY -->
    <body>
        <?php $this->load->view('inc/inc_top_header'); ?>
        <!-- END HEADER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container row-fluid">    
            <!-- BEGIN CONTAINER -->
            <!-- BEGIN SIDEBAR -->
            <?php $this->load->view('inc/inc_header'); ?>
            <!-- End SIDEBAR -->
            <div class="page-content">
                <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->

                <div class="clearfix"></div>
                <div class="content">
                    <div class="row">
                        <div class="col-md-12">
                          <form id="Filter_Frm" name="Filter_Frm" method="post" action="<?php echo base_url() . 'activity_data/exportData' ?>">
                            <div class="panel-group" id="accordion" data-toggle="collapse">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                                Advanced Search
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse">
                                        <div class="tab-content">
                                            <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Activity</label>
                                                                <div class="col-md-9">
                                                                    <select  name="activity_id" id="activity_id" class="dropdownselect2">
                                                                        <option value="">All</option>
                                                                        <?php
                                                                        if (count($ActivityList) > 0) {
                                                                            foreach ($ActivityList as $value) {
                                                                                ?>
                                                                                <option value="<?php echo $value->id ?>"><?php echo $value->name ?></option>
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
                                                                <label class="control-label col-md-3">Device Users</label>
                                                                <div class="col-md-9">
                                                                    <select  name="user_id" id="user_id" class="dropdownselect2">
                                                                        <option value="">All</option>
                                                                        <?php
                                                                        if (count($DeviceUser) > 0) {
                                                                            foreach ($DeviceUser as $value) {
                                                                                ?>
                                                                                <option value="<?php echo $value->loginid ?>"><?php echo $value->name ?></option>
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
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Area</label>
                                                                <div class="col-md-9">
                                                                    <select  name="area_id" id="area_id" class="dropdownselect2">
                                                                        <option value="">All</option>
                                                                        <?php
                                                                        if (count($AreaList) > 0) {
                                                                            foreach ($AreaList as $value) {
                                                                                ?>
                                                                                <option value="<?php echo $value->id ?>"><?php echo $value->area_name ?></option>
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
                                                                <label class="control-label col-md-3">Distributor</label>
                                                                <div class="col-md-9">
                                                                    <select  name="distribut_id" id="distribut_id" class="dropdownselect2">
                                                                        <option value="">All</option>
                                                                          <?php
                                                                        if (count($distrilist) > 0) {
                                                                            foreach ($distrilist as $value) {
                                                                                ?>
                                                                                <option value="<?php echo $value->distributor_name ?>"><?php echo $value->distributor_name ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--/span-->
                                                    </div>
                                                    <div class="clearfix margin-top-20"></div>
                                                    <div class="row">
                                                        <div class="col-md-6">          
                                                            <div class="form-group">
                                                                   <label class="control-label col-md-3">Visit Date</label> 
                                                                   <div class="col-md-9 input-group input-large date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy">
                                                                      <input type="text" class="form-control input-sm" id="start_date" name="start_date" value="">
                                                                      <span class="input-group-addon"> to </span>
                                                                      <input type="text" class="form-control input-sm" id="end_date" name="end_date" value="">
                                                                  </div>                                                   
                                                           </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="col-md-offset-3 col-md-9">
                                                                <button type="button" class="btn btn-primary btn-sm btn-small" onclick="setFilter()">Search</button>
                                                                <button type="button" class="btn  btn-sm btn-small" onclick="ResetFilter()">Reset</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix margin-top-20"></div>                                 
                                            </div></div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix margin-top-20"></div>
                            <div class="tab-content">
                                <div class="tab-pane active" >
                                    <div class="row">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                <div class="grid-title">
                                    <div class="caption caption-font-24">
                                    <h3> <span class="semi-bold">Activity Data</span></h3>
                                    </div>
                                    <div class="btn-group pull-right">
                                        <button type="button" onclick="exportConfirm();"
                                                autofocus="" accesskey="" name="export_excel" id="export_excel"  class="btn orange btn-sm btn-outline" style="margin-top:-50px;"><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                        &nbsp;&nbsp;
                                    </div>
                                </div>
                                <div class="clearfix margin-top-20"></div>
                               
                                <div class="grid-body">
                                        <table class="table table-bordered table-hover " id="example">
                                            <thead>
                                                <tr>
                                                    <th class="table-checkbox"><input type="checkbox" name="check" id="check" class="all group-checkable"></th>
                                                    <th>Activity Name </th>
                                                    <th>Device User </th>
                                                    <th>Distributor Name </th>
                                                    <th>Representative </th>                                                         
                                                    <th>City </th>
                                                    <th>Area </th>
                                                    <th>Visit Date </th>                                                   
                                                    <th>Assessor Details </th>
                                                    <th>sales Representative </th>
                                                    <th>Questions </th>
                                                    <th>Option </th>
                                                    <th>Weightage </th>   
                                                    <th>Images </th>
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
                    <br><br>
                </div>
                <!-- END PAGE -->
            </div>
        </div>
        <!-- END CONTAINER -->

        <?php $this->load->view('inc/inc_footer_script'); ?>
        <!-- BEGIN PAGE LEVEL JS -->
        <script src="<?php echo $Base_url ?>assets/plugins/jquery-datatable/js/jquery.dataTables.min.js" type="text/javascript" ></script>
        <script src="<?php echo $Base_url ?>assets/plugins/jquery-datatable/extra/js/dataTables.tableTools.min.js" type="text/javascript" ></script>
        <script type="text/javascript" src="<?php echo $Base_url ?>assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
        <script type="text/javascript" src="<?php echo $Base_url ?>assets/plugins/datatables-responsive/js/lodash.min.js"></script>
        <script src="<?php echo $Base_url ?>assets/js/datatables.js" type="text/javascript"></script>
        <script src="<?= $Base_url; ?>assets/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
        <script type="text/javascript"  src="<?php echo $Base_url ?>assets/js/jquery-confirm.js"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- END JAVASCRIPTS -->
        <script type="text/javascript">
             var frm = document.Filter_Frm;
            jQuery(document).ready(function () {
        <?php
        if ($this->session->flashdata('flash_message')) {
            echo "ShowAlret('" . $this->session->flashdata('flash_message') . "')";
        }
        ?>
        DatatableRefresh();
        $('.all').click(function () {
            if ($(this).is(':checked')) {
                $("input[name='id[]']").prop('checked', true);
            } else {
                $("input[name='id[]']").prop('checked', false);
            }
        });
            $("#start_date").datepicker({
                changeMonth: true,
                changeYear: true,
                format: 'dd-mm-yyyy'
            });
            $("#end_date").datepicker({
                changeMonth: true,
                changeYear: true,
                format: 'dd-mm-yyyy'
            });

        });

         function ResetFilter() {
            $(".dropdownselect2").select2("val", "");
            frm.reset();
            DatatableRefresh();
            }
            function setFilter() {
                DatatableRefresh();
            }
            function DatatableRefresh() {
            $('#example').dataTable({
                // Internationalisation.
                "aaSorting": [[1, "desc"]],
                "lengthMenu": [
                    [10, 50, 100, -1],
                    [10, 50, 100, "All"] // change per page values here
                ],
                // set the initial value
//                "pageLength": 10,
                "aoColumnDefs": [
                    {"bSortable": false, "aTargets": [0,12]}
                ],
                "oLanguage": {
                    "sLengthMenu": "_MENU_ ",
                    "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
                },
                "bScrollCollapse": true,
                "sPaginationType": "bootstrap",
                "bAutoWidth": true,
                "bServerSide": true,
                "bPaginate": true,
                //                    "sScrollY": "500px",
                                    "sScrollX": "500px",
//                                    "sScrollXInner": "100%",
                "bDestroy": true,
                "sAjaxSource": "<?php echo $Base_url . 'activity_data/DataTable_activity'; ?>",
                "fnServerData": function (sSource, aoData, fnCallback) {
    
                    aoData.push({name: 'activity_id', value: $('#activity_id').val()});
                    aoData.push({name: 'user_id', value: $('#user_id').val()});
                    aoData.push({name: 'area_id', value: $('#area_id').val()});
                    aoData.push({name: 'distribut_id', value: $('#distribut_id').val()});
                    aoData.push({name: 'start_date', value: $('#start_date').val()});
                    aoData.push({name: 'end_date', value: $('#end_date').val()});
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
                            frm.submit();
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
                    for (var i = 0; i < frm1.elements.length; i++)
                    {
                        if (frm1.elements[i].checked == true)
                        {
                            x++;
                        }
                    }
                    return x;
                }
        </script>
    </body>
    <!-- END BODY -->
</html>