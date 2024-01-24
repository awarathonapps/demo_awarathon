<div class="kt-portlet">
    <div class="kt-portlet__body">
        <div class="kt-section__content">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width:8%">Series</th>
                            <th>Question</th>
                            <th style="width:5%">Watch <br/>Video</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            if(count((array)$_participants_result)>0){
                                foreach ($_participants_result as $pdata) { 
                                    $question_series = $pdata->question_series;
                                    $question        = $pdata->question;
                                    $vimeo_id        = isset($pdata->vimeo_uri)?$pdata->vimeo_uri:"";

                        ?>
                                    <tr>
                                        <td><?php echo $question_series;?></td>
                                        <td><?php echo $question;?></td>
                                        <td>
                                            <button class="btn default btn-xs btn-solid" type="button" data-toggle="modal" onclick="play_video('<?php echo $vimeo_id;?>',2)"> 
                                                <i class="fa fa-video-camera"></i>&nbsp;&nbsp;Play
                                            </button>
                                        </td>
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