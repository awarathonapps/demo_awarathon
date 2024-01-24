<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
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
    <?php $this->load->view('inc/inc_htmlhead'); ?>
	<style>
        .dashboard-stat.aiboxes {
            color: #232323;
            background-color: #e8e8e8;
        }
        .dashboard-stat.aiboxes .more{
            color: #232323;
            background-color: #dcdcdc;
        }
        .dashboard-stat .details .number{
            padding-top: 10px !important;
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
                                <span>Reports</span>
                            </li>
                            <li>
                                <i class="fa fa-circle"></i>
                                <span>Email Reports</span>
                            </li>
                        </ul>
                    </div>
					<!-- <div class="row margin-top-15">
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_i_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                        Total <br/>Assessment
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_ii_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                        Total <br/>User Mapped
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_iii_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                        Total <br/>User Played
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_iv_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                    Total Video <br/>Uploaded
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_v_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                    Total Video <br/>Processed
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
						<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                            <div class="dashboard-stat aiboxes">
                                <div class="visual">&nbsp;</div>
                                <div class="details">
                                    <div class="number" id="box_vi_statistics">
                                        0
                                    </div>
                                    <div class="desc">
                                    Total Report <br/>Sent
                                    </div>
                                </div>
                                <a class="more" href="#">&nbsp;</a>
                            </div>
                        </div>
                    </div> -->
                    <div class="row margin-top-10 ">
						<div class="col-md-12">
							<input type="hidden" id="company_id" name="company_id" value="<?php echo $company_id;?>" />
							<div class="portlet light bordered">
								<div class="portlet-title">
									<div class="caption caption-font-24">
										Email Report Schedule
										<div class="tools"> </div>  
									</div>
								</div>
								<div class="portlet-body">   
									<div class="tabbable-line tabbable-full-width">
										<ul class="nav nav-tabs" id="tabs">
											<!-- <li <?php echo ($step == 0 ? 'class="active"' : ''); ?>>
												<a href="#tab_assessment" data-toggle="tab">Preview</a>
											</li> -->
											<li <?php echo ($step == 1 ? 'class="active"' : ''); ?>>
												<a href="#section-candidates" data-toggle="tab">Preview</a>
											</li>
											<li <?php echo ($step == 3 ? 'class="active"' : ''); ?>>
											<a href="#ideal-video" data-toggle="tab">Ideal Video</a>
											</li>
											<li <?php echo ($step == 4 ? 'class="active"' : ''); ?>>
												<a href="#tab_template" data-toggle="tab">Email Template</a>
											</li>
											<li>
												<a href="#tab_email_send" data-toggle="tab">Send</a>
											</li>                                                
										</ul>
										<div class="tab-content">
											<div class="tab-pane <?php echo ($step == 0 ? 'active' : 'mar'); ?>" id="tab_assessment">  
												<!-- <div class="portlet-body"> -->
													<form role="form" id="frmAssessment" name="frmAssessment" method="post" action="">
														<div class="form-body">
														<!--	<div class="row margin-bottom-10">      
																<div class="col-md-12 text-right">    
																	<button type="button" id="schedule_mail" name="schedule_mail" data-loading-text="Please wait..." 
																			class="btn btn-orange btn-sm btn-outline" data-style="expand-right" onclick="schedule_for_assessment()" style="margin-right: 10px;">
																	   <span class="ladda-label"><i class="fa fa-envelope"></i>&nbsp; Next</span>
																	</button>
																</div>
															</div> -->
															<div class="row ">
																<div class="col-md-12" id="assessment_panel">
																	<table class="table  table-bordered table-hover table-checkable order-column" id="index_table">
																		<thead>
																			<tr>
																		<!--		<th>
																					<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
																						<input type="checkbox" class="all group-checkable assessment_check" name="assessment_check" id="assessment_check" data-set="#index_table .checkboxes" />
																						<span></span>
																					</label>
																				</th>-->
																				<th>ID</th>
																				<th>Assessment</th>
																				<th>Assessment Type</th>
																				<th>Start Date/Time</th>
																				<th>End Date/Time</th>
																				<th>Status</th>
																				<th>User Mapped</th>
																				<th>User Played</th>
																				<th>Video Uploaded</th>
																				<th>Video Processed</th>																																										
																			</tr>                                                    
																		</thead>
																		<tbody>																	
																		</tbody>
																	</table>
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
													</form>
												<!-- </div> -->
											</div>
											<div class="tab-pane <?php echo ($step == 1 ? 'active' : 'mar'); ?>"  id="section-candidates"> 
												<form role="form" id="frmAssessment_view" name="frmAssessment_view" method="post" action="">
													<div class="form-body">
														<!--<div class="row margin-bottom-10">      
															<div class="col-md-12 text-right">    
																<button type="button" id="schedule_mail" name="schedule_mail" data-loading-text="Please wait..." 
																		class="btn btn-orange btn-sm btn-outline" data-style="expand-right" onclick="save_ai_cronreports()" style="margin-right: 10px;">
																   <span class="ladda-label"><i class="fa fa-envelope"></i>&nbsp; Save</span>
																</button>
															</div>
														</div> -->
														<div class="row ">
															<div class="col-md-12" id="assessment_panel_view">
																<table class="table  table-bordered table-hover table-checkable order-column" id="index_table_view">
																	<thead>
																		<tr>
																			<!--<th>
																				<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
																					<input type="checkbox" class="all group-checkable assessment_check" name="assessment_check" id="assessment_check" data-set="#index_table .checkboxes" />
																					<span></span>
																				</label>
																			</th>-->
																			<th>ID</th>
																			<th>Assessment</th>
																			<th>Assessment Type</th>
																			<th>Start Date/Time</th>
																			<th>End Date/Time</th>
																			<th>Status</th>
																			<th>User <br/> Mapped</th>
																			<th>User <br/> Played</th>
																			<th>Video <br/> Uploaded</th>
																			<th>Video <br/> Processed</th>
																			<th>Ranking</th>
																			<th>Manager <br/>Dashboard</th>
																			<th>Report</th>
																			<th>PWA</th>																																																																																																								
																		</tr>                                                    
																	</thead>
																	<tbody>																	
																	</tbody>
																</table>
															</div>
														</div>
													</div>
													<!--<div class="modal fade" id="LoadModalFilter-view" role="basic" aria-hidden="true" data-width="400">
														<div class="modal-dialog modal-lg" style="width:1024px;">
															<div class="modal-content">
																<div class="modal-body" id="modal-body">
																	<img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
																	<span>
																		&nbsp;&nbsp;Loading... </span>
																</div>
															</div>
														</div>
													</div> -->
												</form>
											</div> 
											
											<div class="tab-pane <?php echo ($step == 3 ? 'active' : 'mar'); ?>" id="ideal-video">  
												<form role="form" id="frmAssessment_ideal" name="frmAssessment_ideal" method="post" action="">
													<div class="form-body">
														<div class="row ">
															<div class="col-md-12" id="assessment_panel">
																<table class="table table-bordered table-hover table-checkable order-column" id="index_table_ideal">
																	<thead>
																		<tr>
																			<th>ID</th>
																			<th>Assessment</th>
																			<th>Assessment Type</th>
																			<th>Start Date/Time</th>
																			<th>End Date/Time</th>
																			<th>Status</th>
																			<th>Question <br/> Mapped</th>
																			<th>User <br/> Mapped</th>
																			<th>User <br/> Played</th>
																			<th>Video <br/> Uploaded</th>
																			<th>Video <br/> Processed</th>																																										
																		</tr>                                                    
																	</thead>
																	<tbody>																	
																	</tbody>
																</table>
															</div>
														</div>
													</div>
													<div class="modal fade" id="LoadModalFilter_ideal" role="basic" aria-hidden="true" data-width="400">
														<div class="modal-dialog modal-lg" style="width:1024px;">
															<div class="modal-content">
																<div class="modal-body" id="modal-body-ideal">
																	<img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
																	<span>
																		&nbsp;&nbsp;Loading... </span>
																</div>
															</div>
														</div>
													</div>
												</form>
											</div>
											<div class="tab-pane <?php echo ($step == 4 ? 'active' : 'mar'); ?>" id="tab_template">  
											</div>
											<div class="tab-pane <?php echo ($step == 5 ? 'active' : 'mar'); ?>" id="tab_email_send">  
											<div class="form-body">
												<div class="row margin-bottom-10">      
													<div class="col-md-12 text-right">    
														<button type="button" id="schedule_mail" name="schedule_mail" data-loading-text="Please wait..." 
																class="btn btn-orange btn-sm btn-outline" data-style="expand-right"  style="margin-right: 10px;">
														   <span class="ladda-label"><i class="fa fa-envelope"></i>&nbsp; Send </span>
														</button>
													</div>
												</div>
												<div class="row ">
													<div class="col-md-12" id="assessment_panel_send">
														<table class="table  table-bordered table-hover table-checkable order-column" id="index_table_send">
															<thead>
																<tr>
																	<th>
																		<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
																			<input type="checkbox" class="all group-checkable assessment_check" name="assessment_check" id="assessment_check" data-set="#index_table .checkboxes" />
																			<span></span>
																		</label>
																	</th>
																	<th>ID</th>
																	<th>Assessment</th>
																	<th>Assessment Type</th>
																	<th>Start Date/Time</th>
																	<th>End Date/Time</th>
																	<th>Status</th>
																	<th>Question <br/>Mapped</th>
																	<th>User <br/>Mapped</th>
																	<th>User <br/>Played</th>
																	<th>Video <br/>Uploaded</th>
																	<th>Video <br/>Processed</th>
																	<th>Email <br/>Status</th>																																																																																																																							
																	<th>Send</th>																																																																																																																							
																</tr>                                                    
															</thead>
															<tbody>																	
															</tbody>
														</table>
													</div>
												</div>
												<!-- <div class="modal fade" id="LoadModalFilter-send" role="basic" aria-hidden="true" data-width="400">
													<div class="modal-dialog modal-lg" style="width:1024px;">
														<div class="modal-content">
															<div class="modal-body" id="modal-body">
																<img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
																<span>
																	&nbsp;&nbsp;Loading... </span>
															</div>
														</div>
													</div>
												</div> -->
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
                    </div>

                    <div class="row margin-top-10">
                        <div class="col-md-12" id="participants_table">
                        </div>
                    </div>
					<div class="modal fade" id="LoadModalFilter-view" role="basic" aria-hidden="true" data-width="400">
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
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('inc/inc_footer_script'); ?>
    <script type="text/javascript" src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
	<script src="<?php echo $asset_url; ?>assets/customjs/ai_email_schedule.js" type="text/javascript"></script>
    <script>
        var json_participants = [];
        var select_assessments = '';
        var base_url = '<?= base_url(); ?>';
        jQuery(document).ready(function() {
			//DatatableRefresh();
			datatable_view();
			DatatableRefresh_Ideal();
			setemailbody();
			DatatableRefresh_send();
			
			$('.assessment_check').click(function () {
               if ($(this).is(':checked')) {
                   $("input[name='id[]']").prop('checked', true);                                                
               } else {
                   $("input[name='id[]']").prop('checked', false);
               }
			});
			
			$('#schedule_mail').click(function(){
				// var oTable = $('#index_table_send').dataTable();
				// var rowcollection =  oTable.$(".checkboxes:checked");
				// if(rowcollection.length === 0){
					// ShowAlret(data.message, 'Please select the assessment!');
				// }else{
					// select_assessments = rowcollection.join(',');
					// console.log(select_assessments);
					// // rowcollection.each(function(index,elem){
						// // var checkbox_value = $(elem).val();
						// // console.log(checkbox_value);
						// // //Do something with 'checkbox_value'
					// // });
				// }
				var select_assessments = $.map($(':checkbox[name=id\\[\\]]:checked'), function(n, i){
					  return n.value;
				}).join(',');
				if(!select_assessments.trim()){
					ShowAlret('Please select the assessment!', 'error');
				}else{
					console.log(select_assessments);
					scheduleEmail($('#company_id').val(),select_assessments,1); //send to all candidates of the selected assessments
				}
			});
        });
    </script>
</body>

</html>