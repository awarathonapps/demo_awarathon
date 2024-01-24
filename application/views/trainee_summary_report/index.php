<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>        
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>assets/global/highcharts/css/highcharts.css" />
        <style>
            #tablecontainer {
                width: 50px;
                margin-left:700px;                
            }
            #maintablecontainer {
                width: 527px;
                margin-left:0px;
            }
            #headtr{
                width: 50px;
                border: 1px solid black; 
                background-color: steelblue;
            }
            #datatr{
                border: 1px solid black;
                background-color: white;
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
                                    <i class="fa fa-circle"></i>
                                    <span>Report</span>                                    
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>
                                    <span>Trainee Summary Report</span>
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
                                                    <?php } ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Workshop&nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="workshop_id" name="workshop_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="getWorkshopwiseData();">
                                                                    <option value="">All Workshop</option>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                </div>                                                
                                                <div class="row margin-top-10">                                                    
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3">Trainee &nbsp;<span class="required"> * </span></label>
                                                            <div class="col-md-9" style="padding:0px;">
                                                                <select id="trainee_id" name="trainee_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%">
                                                                        <option value="">All</option>
                                                                        
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                 <div class="clearfix margin-top-20"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-offset-10 col-md-2 text-right">
                                                            <button type="button" class="btn blue-hoki btn-sm" onclick="ShowChart()">Show Report</button>
                                                        </div>
                                                    </div>
                                                </div>                                                 
                                            </form> 
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix margin-top-20"></div>
                                <div id="AppendChart" style="margin-top: 20px !important; margin-left:70px; border-top: 1px solid #f1f2f7;" ></div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar');  ?>
            </div>
            <?php //$this->load->view('inc/inc_footer');  ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav');  ?>
<?php $this->load->view('inc/inc_footer_script'); ?>
<script src="<?php echo $base_url;?>assets/global/scripts/Chart.bundle.js"></script>
<script src="<?php echo $base_url;?>assets/global/highcharts/highcharts.src.js"></script>
 <?php if($acces_management->allow_print){ ?>
                <script src="<?php echo $asset_url;?>assets/global/highcharts/modules/exporting.js"></script>
        <?php } ?>
<script src="<?php echo $base_url;?>assets/global/scripts/utils.js"></script>
<script>            
        var FilterFrm = $('#FilterFrm');
        var form_error = $('.alert-danger', FilterFrm);
        var form_success = $('.alert-success', FilterFrm);
        
        FilterFrm.validate({
            errorElement: 'span',
            errorClass: 'help-block help-block-error',
            focusInvalid: false,
            ignore: "",
            rules: {    
                company_id: {
                    required: true
                },
                workshop_id: {
                    required: true
                },
                trainee_id: {
                    required: true
                }
            },
            invalidHandler: function (event, validator) {
                form_success.hide();
                form_error.show();
                if (validator.errorList.length) {
                    $('#tabs a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show');
                }
                App.scrollTo(form_error, -200);
            },
            errorPlacement: function(error, element) {
                if(element.hasClass('form-group')) {
                    error.appendTo(element.parent().find('.has-error'));
                }
                else if(element.parent('.form-group').length) {
                    error.appendTo(element.parent());
                }
                else {
                    error.appendTo(element);
                }
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
            },
            submitHandler: function (form) {
                form_success.show();
                form_error.hide();
                //Ladda.bind('button[id=reward-submit]');                                
                form.submit();
            }
        });
        $('.select2me,.select2-multiple').on('change', function() {
            $(this).valid();
        });
        function getCompanywiseData(){
            if($('#company_id').val() !=''){
                $.ajax({
                    type: "POST",
                    data: {data: $('#company_id').val()},
                    async: false,
                    url: "<?php echo $base_url;?>traineesummaryreport/ajax_companywise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var WorkshopMSt = Oresult['WorkshopData'];                              
                            var workshop_option = '<option value="">Please Select</option>';
                            var sessions_option = '<option value="">All</option><option value="0">PRE</option><option value="1">POST</option>';
                            for (var i = 0; i < WorkshopMSt.length; i++) {
                                workshop_option += '<option value="' + WorkshopMSt[i]['id'] + '">' + WorkshopMSt[i]['workshop_name'] + '</option>';
                            }                            
                            $('#workshop_id').empty();
                            $('#workshop_id').append(workshop_option);
                            $('#sessions').empty();
                            $('#sessions').append(sessions_option);
                        }
                    customunBlockUI();    
                    }
                });
            }
        }
        function getWorkshopwiseData(){
            if($('#workshop_id').val() != ''){
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#company_id').val(),workshop_id: $('#workshop_id').val()},
                    async: false,
                    url: "<?php echo $base_url;?>traineesummaryreport/ajax_workshopwise_data",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {                        
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var TraineeMSt = Oresult['TraineeData'];     

                            var trainee_option = '<option value="">Please Select</option>';                            
                            for (var i = 0; i < TraineeMSt.length; i++) {
                                trainee_option += '<option value="' + TraineeMSt[i]['user_id'] + '">' + TraineeMSt[i]['username'] + '</option>';
                            }
                            $('#trainee_id').empty();
                            $('#trainee_id').append(trainee_option);

                        }
                    customunBlockUI();    
                    }
                });
            }
        }        
        function ShowChart(){
            if(! $('#FilterFrm').valid()) {               
                return false;
            } 
            $.ajax({
                    type: "POST",
                    url: "<?php echo $base_url;?>traineesummaryreport/ajax_chart",
                    data: $('#FilterFrm').serialize(),
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (Data) {
                        if (Data != '') {
                            var Oresult = jQuery.parseJSON(Data);
                            var DataMSt = Oresult['ChartDataHtml'];                            
                            if(Oresult['Error']!=''){                            
                                $('#errordiv').show();
                                $('#errorlog').html(Oresult['Error']);
                                App.scrollTo(form_error, -200);            
                            }else{   
                                
                                $('#AppendChart').append(DataMSt);  
                                
                            }
                        } 
                    customunBlockUI();    
                    }
                });
        }
        
        
        </script>
    </body>
</html>