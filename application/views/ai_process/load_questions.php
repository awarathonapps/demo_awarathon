<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Participants List 
                <div style="float:right;font-size:11px;font-weight:400;">
                    <span style="height: 15px;width: 15px;background: #db1f48;padding: 9px;color: #ffffff;">SP</span> Schedule Pending
                    <span style="height: 15px;width: 15px;background: #004369;padding: 9px;color: #ffffff;">PP</span> PDF Pending  
                    <span style="height: 15px;width: 15px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span> Rating Pending
                </div>
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
            <div class="kt-section__content">
                <!-- <div class="table-responsive"> -->
                <table class="table table-bordered" id="participants_datatable">
                        <thead>
                            <tr>
                                <th>User Id</th>
                                <th>User Name</th>
								<th>Email</th>
                                <th>Mobile</th>
                                <?php if ($report_type==1 OR $report_type==3){ ?>
                                    <th>Report <br/>(AI)</th>
                                <?php } ?>
                                <?php if ($report_type==2 OR $report_type==3){ ?>
                                    <th>Report <br/>(Manual)</th>
                                <?php } ?>
                                <?php if ($report_type==3){ ?>
                                    <th>Report <br/>(AI + Manual)</th>
                                <?php } ?>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                if(count((array)$_participants_result)>0){
									$user_rating_array = json_decode(json_encode($user_rating), true);
									$userid_rating_array = array_column($user_rating_array, 'user_id');
                                    foreach ($_participants_result as $pdata) { 
                                        $company_id      = $pdata->company_id;
                                        $assessment_id   = $pdata->assessment_id;
                                        $user_id         = $pdata->user_id;
                                        $user_name       = $pdata->user_name;
										$email       = $pdata->email;
                                        $mobile       = $pdata->mobile;

                                        $_score_imported = false;
                                        $_xls_results     = $this->common_model->get_value('ai_schedule', 'count(*) as total', 'task_status="1" AND xls_generated="1" AND xls_filename!="" AND xls_imported="1" AND company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'"');
                                        if (isset($_xls_results) AND count((array)$_xls_results)>0){
                                            if ((int)$_xls_results->total>0){
                                                $_score_imported = true;
                                            }
                                        }
                                                                
                                        $pdf_icon = "";
                                        $mpdf_icon = "";
                                        $cpdf_icon = "";
                                        if ($show_reports_flag==false){
                                            $pdf_icon        = '<span style="height: 25px;width: 25px;background: #004369;padding: 9px;color: #ffffff;">PP</span>';
                                        }else if ($show_reports_flag==true AND $show_ai_pdf==true AND $_score_imported==true){
                                            $pdf_icon        = '<a href="'.base_url().'/ai_reports/view_ai_reports/'.$company_id.'/'.$assessment_id.'/'. $user_id.'" target="_blank"><img src="'.base_url().'/assets/images/pdf2.png" style="height:21px;width:21px;" /></a>';
                                        }else{
                                            $pdf_icon        = '<span style="height: 25px;width: 25px;background: #db1f48;padding: 9px;color: #ffffff;">SP</span>';
                                        }

                                        if ($show_reports_flag==false){
                                            $mpdf_icon        = '<span style="height: 25px;width: 25px;background: #004369;padding: 9px;color: #ffffff;">PP</span>';
                                        }else if ($show_reports_flag== true AND $show_manual_pdf){
											if(in_array($user_id, $userid_rating_array)){
												$mpdf_icon       = '<a href="'.base_url().'/ai_reports/view_manual_reports/'.$company_id.'/'.$assessment_id.'/'. $user_id.'" target="_blank"><img src="'.base_url().'/assets/images/pdf2.png" style="height:21px;width:21px;" /></a>';
											}else{
												$mpdf_icon        = '<span style="height: 25px;width: 25px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span>';        
											}
										}else{
                                            $mpdf_icon        = '<span style="height: 25px;width: 25px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span>';
                                        }
                                        if ($show_reports_flag==false){
                                            $cpdf_icon        = '<span style="height: 25px;width: 25px;background: #004369;padding: 9px;color: #ffffff;">PP</span>';
                                        }else if ($show_reports_flag==true AND $show_ai_pdf==true AND $_score_imported==true){
                                            if ($show_manual_pdf){
												if(in_array($user_id, $userid_rating_array)){
													$cpdf_icon       = '<a href="'.base_url().'/ai_reports/view_combine_reports/'.$company_id.'/'.$assessment_id.'/'. $user_id.'" target="_blank"><img src="'.base_url().'/assets/images/pdf2.png" style="height:21px;width:21px;" /></a>';
												}else{
													$cpdf_icon        = '<span style="height: 25px;width: 25px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span>';        
												}
                                            }else{
                                                $cpdf_icon        = '<span style="height: 25px;width: 25px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span>';
                                            }
                                        }else{
                                            $cpdf_icon        = '<span style="height: 25px;width: 25px;background: #db1f48;padding: 9px;color: #ffffff;">SP</span>';
                                        }
                                        
                            ?>
                                        <tr>
                                            <td><?php echo $pdata->user_id;?></td>
                                            <td><?php echo $pdata->user_name;?></td>
											<td><?php echo $pdata->email;?></td>
                                            <td><?php echo $pdata->mobile;?></td>
                                            <?php if ($report_type==1 OR $report_type==3){ ?>
                                            <td><?php echo $pdf_icon;?></td>
                                            <?php } ?>
                                            <?php if ($report_type==2 OR $report_type==3){ ?>
                                            <td><?php echo $mpdf_icon;?></td>
                                            <?php } ?>
                                            <?php if ($report_type==3){ ?>
                                            <td><?php echo $cpdf_icon;?></td>
                                            <?php } ?>
                                            <td>
                                                <button class="btn default btn-xs btn-solid" type="button" data-toggle="modal" onclick="load_questions('<?php echo $company_id;?>','<?php echo $assessment_id;?>','<?php echo $user_id;?>')"> 
                                                    <i class="fa fa-question"></i>&nbsp;&nbsp;View Questions
                                                </button>
                                            </td>
                                        </tr>
                            <?php                
                                    }
                                }else{
                                    ?>
                                    <!-- <tr>
                                        <td colspan="100">No Records Found</td>
                                    </tr> -->
                                    <?php
                                }
                            ?>
                        </tbody>
                    </table>
                <!-- </div> -->
            </div>
        </div>
    </div>
</div>