<div class="modal-header" style=" padding: 10px;">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"> Candidate Of <?php echo $assessment_data->assessment; ?></h4>
    
</div>
<div class="modal-body" style="background-color: #dfdede; padding: 10px;">
    <div class="portlet-body">
        <form name="VideoPitchingForm" id="VideoPitchingForm">
            <div class="cust_container ">
                <div class="col-md-6 left-col no-padding" >                                                                                                                      
                    <div class="col-md-12 margin-top-10 border border-dark" style="background-color: #FFF; min-height: 85px; max-height: 315px; overflow: auto; margin-bottom: 0px;"> 
                          <h4><b><?php echo $status ?> Candidates</b></h4>  
                        <table  class="table table-striped table-bordered table-hover margin-top-10 margin-bottom-5" id="userstable" name="userstable">                                                                                                                                                                                
                            <col width="700px" />
                            <tbody>        
                                <?php if(count($Userlist) > 0) { foreach($Userlist as $key=>$user) { $srno = $key+1;?>
                                <tr class="tr-background">
                                    <td class="bg-remove" id="user<?php echo $user->user_id ?>" style="cursor:pointer; " onclick="set_userid(<?php echo $user->user_id ?>,<?php echo $Questions[0]->question_id ?>)"><?php echo $user->username; ?></td>                                       
                                </tr>    
                                <?php } } ?>
                            <input type="hidden" id="userid" name="userid" value="1">
                            </tbody>
                        </table> 
                    </div>
                    <div class="col-md-12 margin-top-10 border border-dark" style="background-color: #FFF;    padding-bottom: 500em; margin-bottom: -500em;"> 
                        <h4><b>Situation and Questions</b></h4>  
                        <table  class="table table-striped table-bordered table-hover margin-top-10 margin-bottom-5" id="question_table" name="question_table">                                                                                                                                                                                
                            <col width="700px" />
                            <?php $first_qid =0;
                                $total_ques = count($Questions);
                            if($total_ques > 0) { $first_qid = $Questions[0]->question_id; ?>
                            <tbody>        
                                <?php foreach($Questions as $key=>$Q) { $srno = $key+1;?>
                                    <tr class="tr-background">
                                        <td style="cursor:pointer;" class="bg-question" id="qut<?php echo $Q->question_id ?>" onclick="get_uservideo(<?php echo $Q->question_id; ?>)"><?php echo $srno.". ".$Q->question; ?></td>                                       
                                    </tr>    
                                <?php } ?>
                            </tbody>
                            <?php } ?>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 right-col no-padding" >

                    <span id="video_details"></span>
                </div> 
            </div>
        </form>
    </div>
    
    <div class="modal-footer " >
       <button type="button" id="close_btn" data-dismiss="modal" class="btn btn blue-hoki" >Close</button>
    </div>
</div>
<script>
    jQuery(document).ready(function () {  
        set_userid(<?php echo $Userlist[0]->user_id ?>,<?php echo $Questions[0]->question_id ?>)
        get_uservideo(<?php echo $Questions[0]->question_id ?>);
    });
</script>



