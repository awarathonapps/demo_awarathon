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
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css">
        <!--<link rel="stylesheet" type="text/css" href="< ?php echo $asset_url;?>assets/global/highcharts/css/highcharts.css" />-->
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
                                    <i class="fa fa-circle"></i>
                                    <span>Reports</span>
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Video Minute Report</span>
                                </li>
                            </ul>
                        </div>
                        <div class="row margin-top-10 ">
                            <div class="col-md-4 pull-right">
                                <div class="form-group">
                                    <!--<label class="control-label col-md-3">&nbsp;</label>-->
                                    <!-- <div class="col-md-12" style="padding:0px;">
                                        <select id="company_id" name="company_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                            <option value="">All Company</option>
                                            <?php //foreach ($cmpdata as $cmp) { ?>
                                                <option value="<?php //echo $cmp->id; ?>"><?php //echo $cmp->company_name; ?></option>
                                            <?php //} ?>
                                        </select>
                                    </div> -->
                                </div>
                            </div> 
                        </div>               
                        <div class="row margin-top-10">
                            <div class=" col-md-12 " >
                                <div class=" col-md-8 col-sm-8 " id="statistics_minvideo"></div>
                                <div class=" col-md-4 col-sm-4 " id="piechart_minvideo"></div>
                            </div>	
                        </div>	
                    </div>
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
            </div>
            <?php //$this->load->view('inc/inc_footer'); ?>
        </div>
        <div id="responsive-modal" class="modal fade bs-modal-lg" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" data-width="200">
            <div class="modal-dialog modal-sm">
                <div class="modal-content" id="load_modeldata">
                
                </div>    
            </div>    
        </div>
        <?php //$this->load->view('inc/inc_quick_nav'); ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url;?>assets/global/highcharts/highstock.js"></script>
        <?php if($acces_management->allow_print){ ?>
        <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
     
        <script>
            var base_url = '<?php echo base_url(); ?>';
            jQuery(document).ready(function () {
                // initiate layout and plugins
                // $('#company_id').select2({
                //     placeholder: " All Company",
                //     width: '100%',
                //     allowClear: true
                // });
                // $("#company_id").change(function() { 
                  statistic_chart();
                  minute_piechart();
                // }).change();
//                getWeek();
            });
            function getfiltermodal(company_id,assesid) {
                $.ajax({                
                    type: "POST",
                    data: {company_id:company_id},
                    url: "<?php echo $base_url;?>video_min_report/add_filtermodel/"+assesid,
                    success: function (lcHtml) {
                        $('#load_modeldata').html(lcHtml);
                        $('#responsive-modal').modal('toggle');
                    }
                });
            }
            function getWeek(week_id=''){
                $.ajax({
                    type: "POST",
                    data: {year: $('#year').val(),month: $('#month').val()},
                    async: false,
                    url: "<?php echo $base_url;?>video_min_report/ajax_getWeeks",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);                                                        
                            var WStartEndDate = Oresult['WStartEnd'];                            
                            var week_option = '<option value="">Select week</option>';                            
                                for (var i = 0; i < WStartEndDate.length; i++) {
                                    week_option += '<option value="' + WStartEndDate[i] + '" '+ (WStartEndDate[i]==week_id ? "selected" : "")+'>' +'Week-'+ (i+1) + '</option>';
                                }                             
                            $('#week').empty();
                            $('#week').append(week_option);
                        }
                    customunBlockUI();    
                    }
                });                               
            }
            function statistic_chart() {
                $.ajax({
                    type: "POST",
                    data: {company_id: <?php echo $company_id;?>},
                    url: base_url+"video_min_report/get_user_data",
                    success: function (msg) {
                        var Oresult = jQuery.parseJSON(msg);  
                        if (msg != '') {                   
                            $('#statistics_minvideo').html(Oresult);
                        }
                    }
                });
            }
            function minute_piechart() {
                $.ajax({
                    type: "POST",
                    data: {company_id: <?php echo $company_id;?>},
                    url: base_url+"video_min_report/get_piechart_data",
                    success: function (msg) {
                        var Oresult = jQuery.parseJSON(msg);                    
                        if (msg != '') {                                                
                          $('#piechart_minvideo').html(Oresult);
                        }
                    }
                });
            }
        </script>
    </body>
</html>