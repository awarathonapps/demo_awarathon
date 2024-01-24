                    <div class="col-md-12 margin-top-10 border border-dark " style="background-color: #FFF;">                                                                                                                      
                        <div class="row " >
                            <div class="col-md-6 margin-top-5" >
                                <!--<div class="col-md-6 margin-top-20" style="border-right: 1px solid #c1c1c1;">-->
                                <h5> 
                                    <label>Name :</label>
                                    <strong><?php echo $UserData->username; ?></strong><br>
                                 </h5>
                                <!--</div>-->
                            </div>
                        </div>  
                    </div>                    
                    <div class="col-md-12 border border-dark margin-top-10 " style="background-color: #FFF;">                       
                        <div class="row " >
                            <div class="col-md-12 ">
                                <?php if (count($video_data) > 0) { ?>
                                <iframe width="460" height="250"  src="https://player.vimeo.com/video/<?php echo $video_data->video_url; ?>?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=139753" frameborder="0" allowFullScreen mozallowfullscreen webkitAllowFullScreen style="margin-left: 10px;"></iframe>
                                    <input type="hidden" id="ass_result_id" name="ass_result_id" value="<?php echo $video_data->id; ?>"/>
                                    <?php
                                } else {
                                    echo "<h3 style='margin: 5px;'>No Video</h3>";
                                }
                                ?>                        
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 border border-dark margin-top-10" style="background-color: #FFF; min-height: 50px;">                                                                                                                      
                        <div class="row " >
                            <div class="col-md-6 margin-top-5" >
                                <h5> 
                                    <label>Input Number :</label>
                                    <strong><?php echo (count($sales_data) > 0 ? $sales_data->input : ''); ?></strong><br>
                                 </h5>
                            </div>
                        </div>  
                        <div class="row " >
                            <div class="col-md-10" >
                                <h5> 
                                    <label>Description :</label>
                                    <strong><?php echo (count($sales_data) > 0 ? $sales_data->description : ''); ?></strong><br>
                                </h5>
                            </div>
                        </div>
                    </div>  
