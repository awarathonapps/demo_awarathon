<?php $site_url = base_url(); ?>
<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Trainer List</h4>
</div>
<div class="modal-body"> 
                <table class="table table-striped table-bordered table-hover" id="TopicFilterTable">
                    <thead>
                          <tr>
                                <th>Trainer Name</th>
                                <th>Avg C.E %</th>
                                <th>Trainee Trained</th>
                                <th>Highest C.E %</th>
                                <th>Lowest C.E %</th>
                                <th>Action</th>
                          </tr>
                    </thead>
                    <tbody>
                    <?php if(count($traineedata)>0){ 
                        $Total=0;
                        foreach ($traineedata as $key => $value) { 
                    ?>
                        <tr>
                            <td><?php echo $value->trainer_name ?></td>
                             <td><?php echo$value->avgce ?> %</td>
                            <td><?php echo$value->trainee_trained ?></td>
                            <td><?php echo $value->highestce ?> %</td>
                            <td><?php echo $rowvalue = ($value->highestce == $value->lowestce ? '-' : $value->lowestce . "%" )?></td>
                            <td><div class="btn-group">
                                <button class="btn orange btn-xs btn-outline dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> 
                                    Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">                                
                                    <li>
                                        <a href="<?php echo $site_url. 'trainer_dashboard/index/'. $value->trainer_id ?>" target="_blank">
                                        <i class="fa fa-bar-chart"></i>&nbsp;Trainer Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $site_url . 'trainer_workshop/index/' . $value->trainer_id ?>" target="_blank">
                                        <i class="fa fa-bar-chart"></i>&nbsp;Trainer Workshop
                                        </a>
                                    </li>                                                                
                                    <li>
                                        <a href="<?php echo $site_url . 'trainer_comparison/index/' . $value->trainer_id ?>" target="_blank">
                                        <i class="fa fa-bar-chart"></i>&nbsp;Trainer Comparison
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $site_url . 'trainer_accuracy/index/' . $value->trainer_id ?>" target="_blank">
                                        <i class="fa fa-bar-chart"></i>&nbsp;Trainer Accuracy
                                        </a>
                                    </li>
                                </ul>
                            </div></td>
                        </tr>
                    <?php }

                        } ?>
                    </tbody>
                </table>
</div>

