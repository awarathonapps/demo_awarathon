<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Participants List
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="kt-section">
            <div class="kt-section__info" style="padding:0px 0px 10px 0px;">
                Wait while system process all the participants data.
                <div style="float:right;text-align:right;">
                    <button class="btn red btn-xs btn-solid" type="button" onclick="delete_all_task()"> 
                        <i class="fa fa-trash-o"></i>&nbsp;&nbsp;Delete All Task
                    </button>
                </div>
            </div>
            <div class="kt-section__content">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width:8%">User Id</th>
                                <th>User Name</th>
                                <th style="width:8%">Question</th>
                                <th style="width:5%">Task <br/>Schedule (Yes/No)</th>
                                <th style="width:5%">Video <br/>Process Status</th>
                                <th style="width:5%">Excel <br/>Status</th>
                                <th style="width:5%">Excel <br/>Imported</th>
                                <th style="width:5%">Watch <br/>Video</th>
                                <th style="width:8%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                if(count((array)$_participants_result)>0){
                                    foreach ($_participants_result as $pdata) { 
                                        $company_id      = $pdata->company_id;
                                        $assessment_id   = $pdata->assessment_id;
                                        $user_id         = $pdata->user_id;
                                        $trans_id        = $pdata->trans_id;
                                        $question_id     = $pdata->question_id;
                                        $portal_name     = $pdata->portal_name;
                                        $assessment      = $pdata->assessment;
                                        $user_name       = $pdata->user_name;
                                        $question_series = $pdata->question_series;
                                        $video_url       = isset($pdata->video_url)?$pdata->video_url:"";
                                        $vimeo_uri       = isset($pdata->vimeo_uri)?$pdata->vimeo_uri:"";
                                        $vimeo_id        = ($vimeo_uri != $video_url ? $vimeo_uri : $video_url);

                                        $schedule_icon   = "-";
                                        $status_icon     = "-";
                                        $report_icon     = "-";
                                        $import_icon     = "-";
                                        $schedule_input  = "";
                                        $status_input    = "";
                                        $report_input    = "";
                                        $import_input    = "";

                                        $ai_schedule_result = $this->common_model->get_value('ai_schedule', '*', 'company_id="'.$company_id.'" AND assessment_id="'.$assessment_id.'" AND user_id="'.$user_id.'" AND trans_id="'.$trans_id.'" AND question_id="'.$question_id.'"');
                                        if (isset($ai_schedule_result) AND count((array)$ai_schedule_result)>0){
                                            if ($ai_schedule_result->task_id!=="" AND $ai_schedule_result->task_id!=="FAILED") {
                                                $schedule_icon  = '<img src="'.base_url().'/assets/images/yes.png" style="height:16px;width:16px;" />';
                                                $schedule_input = 1;
                                            }
                                            if ($ai_schedule_result->task_status==1 OR $ai_schedule_result->task_status=="1") {
                                                $status_icon  = '<img src="'.base_url().'/assets/images/yes.png" style="height:16px;width:16px;" />';
                                                $status_input = 1;
                                            }
                                            if (($ai_schedule_result->xls_generated==1 OR $ai_schedule_result->xls_generated=="1") AND  $ai_schedule_result->xls_filename!='') {
                                                $report_icon  = '<img src="'.base_url().'/assets/images/yes.png" style="height:16px;width:16px;" />';
                                                $report_input = 1;
                                            }
                                            if ($ai_schedule_result->xls_imported==1 OR $ai_schedule_result->xls_imported=="1") {
                                                $import_icon  = '<img src="'.base_url().'/assets/images/yes.png" style="height:16px;width:16px;" />';
                                                $import_input = 1;
                                            }
                                        }
                            ?>
                                        <tr>
                                            <td><?php echo $pdata->user_id;?></td>
                                            <td><?php echo $pdata->user_name;?></td>
                                            <td><?php echo $pdata->question_series;?></td>
                                            <td><span id="schedule-icon-<?php echo $pdata->uid;?>"><?php echo $schedule_icon;?></span></td>
                                            <td><span id="status-icon-<?php echo $pdata->uid;?>"><?php echo $status_icon;?></span></td>
                                            <td><span id="report-icon-<?php echo $pdata->uid;?>"><?php echo $report_icon;?></span></td>
                                            <td><span id="import-icon-<?php echo $pdata->uid;?>"><?php echo $import_icon;?></span></td>
                                            <td>
                                                <?php if ($vimeo_id!='') { ?>
                                                    <button class="btn default btn-xs btn-solid" type="button" data-toggle="modal" onclick="play_video('<?php echo $vimeo_id;?>')"> 
                                                    <i class="fa fa-video-camera"></i>&nbsp;&nbsp;Play
                                                    </button>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                                        Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu pull-right" role="menu">
                                                        <li>
                                                            <a onclick="reschedule_single_task('<?php echo $pdata->uid;?>','<?php echo $company_id;  ?>','<?php echo $assessment_id;  ?>','<?php echo $user_id;  ?>','<?php echo $trans_id;  ?>','<?php echo $question_id;  ?>','<?php echo $portal_name;  ?>','<?php echo $assessment;  ?>','<?php echo $user_name;  ?>','<?php echo $question_series;  ?>')">
                                                            <i class="fa fa-tasks"></i>&nbsp;&nbsp;Reschedule
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a data-toggle="modal" onclick="task_error_log('<?php echo $pdata->uid;?>','<?php echo $company_id;  ?>','<?php echo $assessment_id;  ?>','<?php echo $user_id;  ?>','<?php echo $trans_id;  ?>','<?php echo $question_id;  ?>');">
                                                            <i class="fa fa-bug"></i>&nbsp;&nbsp;Error Logs
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a onclick="delete_single_task('<?php echo $pdata->uid;?>','<?php echo $company_id;  ?>','<?php echo $assessment_id;  ?>','<?php echo $user_id;  ?>','<?php echo $trans_id;  ?>','<?php echo $question_id;  ?>')">
                                                            <i class="fa fa-trash-o"></i>&nbsp;&nbsp;Delete Task
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <input type="hidden" id="schedule-<?php echo $pdata->uid;?>" name="schedule-<?php echo $pdata->uid;?>" value="<?php echo $schedule_input;?>"/>
                                            <input type="hidden" id="status-<?php echo $pdata->uid;?>" name="status-<?php echo $pdata->uid;?>" value="<?php echo $status_input;?>"/>
                                            <input type="hidden" id="report-<?php echo $pdata->uid;?>" name="report-<?php echo $pdata->uid;?>" value="<?php echo $report_input;?>"/>
                                            <input type="hidden" id="import-<?php echo $pdata->uid;?>" name="import-<?php echo $pdata->uid;?>" value="<?php echo $import_input;?>"/>
                                        </tr>
                            <?php                
                                    }
                                }else{
                                    ?>
                                    <tr>
                                        <td colspan="6">No Records Found</td>
                                    </tr>
                                    <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>