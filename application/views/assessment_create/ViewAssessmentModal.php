<div class="modal-header" style=" padding: 10px;">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo ($mode==2 ? '<i class="fa fa-unlock"></i>':'<i class="fa fa-lock"></i>'); ?> ASSESSMENT OF CANDIDATE</h4>
    
</div>
<div class="modal-body" style="background-color: #dfdede;padding: 10px;">
    <div class="portlet-body">
        <div class="alert alert-success display-hide" id="successDiv">
        <button class="close" data-close="alert"></button>
        <span id="SuccessMsg"></span>
        </div>
        <div class="alert alert-danger  display-hide" id="modalerrordiv">
            <button class="close" data-close="alert"></button>
            <span id="modalerrorlog"></span>
        </div>
        <form name="VideoPitchingForm" id="VideoPitchingForm">         
                <div class=" cust_container">
                    <div class="col-md-6 left-col no-padding" >                                                                                                                      
                        <div class="col-md-12 border border-dark" style="background-color: #FFF;min-height: 85px">
                            <div class="col-md-2">
                            <?php if($UserData->avatar != NULL && $UserData->avatar !='') { ?>
                            <img alt="" class="img-circle" src="<?php echo base_url(); ?>assets/uploads/avatar/<?php echo $UserData->avatar; ?>" style="max-width: 50px;max-height: 90px;margin-top: 15px">
                            <?php }else { ?>
                            <img alt="" class="img-circle" src="<?php echo base_url(); ?>assets/uploads/no-avatar.jpg" style="max-width: 50px;max-height: 90px;margin-top: 15px">
                            <?php } ?>
                            </div>
                            <div class="col-md-6 margin-top-20" style="border-right: 1px solid #c1c1c1;">
                                <strong><?php echo $UserData->username; ?></strong><br>
                                <strong><i class="fa fa-envelope"></i>&nbsp;&nbsp;<?php echo $UserData->email; ?></strong><br>
                                <strong>Manager : <?php echo $trainer_name->trainer_name; ?></strong>
                            </div>
                            <div class="col-md-4 margin-top-5" style="padding: inherit;">                                
                                <h5><b>Overall Score</b></h5>
                                <b>Team Rating &nbsp;| &nbsp;<strong id="team_rating" name="team_rating"><?php echo $team_rating; ?></strong></b><br/>
                                <b>Your Rating &nbsp;| &nbsp;<strong id="your_rating" name="your_rating"><?php echo $your_rating; ?></strong></b>
                            </div>
                        </div>
                        <div class="col-md-12 margin-top-10 border border-dark" style="background-color: #FFF;    padding-bottom: 500em; margin-bottom: -500em;"> 
                            <h4><b>Situation and Questions</b></h4>  
                            <table  class="table table-striped table-bordered table-hover margin-top-10" id="question_table" name="question_table">                                                                                                                                                                                
                                <col width="700px" />
                                <?php $first_qid =0;
                                if(count($Questions) > 0) { $first_qid = $Questions[0]->question_id; ?>
                                <tbody>        
                                    <?php foreach($Questions as $key=>$Q) { $srno = $key+1;?>
                                        <tr>
                                            <td style="cursor:pointer;"	onclick="getparameter(<?php echo $Q->question_id.','.$srno.','.count($Questions) ?>)"><?php echo $srno.". ".$Q->question; ?></td>                                       
                                        </tr>    
                                    <?php } ?>
                                </tbody>
                                <?php } ?>
                            </table>
                            <h5><b>Overall Comments</b></h5>
                            <textarea id="que_remark" name="que_remark" cols="72" rows="4" style="resize: none;" <?php echo ($mode == 2 ? '' : 'disabled'); ?>><?php echo $remarks; ?></textarea>
                        </div> 
                    </div>                    
                    <div class="col-md-6 border border-dark right-col no-padding" style="background-color: #FFF;">                       
                        <span  id="parameter_table_div"></span> 
                        <div class="col-md-12">
                            <h5><b>Question Comments</b></h5>
                            <textarea id="remark_que" name="remark_que" cols="72" rows="4" style="resize: none;" <?php echo ($mode == 2 ? '' : 'disabled'); ?>></textarea>
                        </div> 
                    </div>
                    <input type="hidden" id="trainer_id" name="trainer_id" value="<?php echo $trainer_id; ?>"/>
                    <input type="hidden" id="assessment_type" name="assessment_type" value="<?php echo $assessment_type; ?>"/>
                    <input type="hidden" id="assessment_id" name="assessment_id" value="<?php echo $assessment_id; ?>"/>
                    <input type="hidden" id="company_id" name="company_id" value="<?php echo $company_id; ?>"/>
                    <input type="hidden" id="question_id" name="question_id" value=""/>
                                        
                    <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>"/>
                </div>
        </form>
    </div>
        <div class="modal-footer">
            <?php if($mode == 2){ ?>
<!--            <button type="button" class="btn btn blue-hoki " onclick="Retake_confirm();" ><i class="fa fa-repeat"></i> Retake</button>-->
            <button type="button" class="btn btn-orange sh-btn" onclick="Save_final_rating(1);" style="display:none;">Submit</button>
            <?php } ?>
            <button type="button" id="close_btn" data-dismiss="modal" class="btn btn blue-hoki">Close</button>
        </div>
    
</div>
<script>
    var  first_qid = '<?php echo $first_qid; ?>';
    if(first_qid !=0){
        getparameter(first_qid,1,'');
    }    
    jQuery(document).ready(function () {    
     $('.sh-btn').hide();
    });
    function Save_rating(rate_flag){   
        $.ajax({
            url: "<?php echo base_url(); ?>assessment/save_rating/"+rate_flag,
            type: 'POST',
            data: $('#VideoPitchingForm').serializeArray(),
            beforeSend: function () {
                customBlockUI();
            },
            success: function (Odata) {
                var Data = $.parseJSON(Odata); 
                if(Data['success']){
                    ShowAlret(Data['Msg'],'success');
                    $("#team_rating").html(Data['team_rating']);
                    $("#your_rating").html(Data['your_rating']);
                }                              
                customunBlockUI();
            }
        });
    }
    function Save_final_rating(rate_flag){
        $.confirm({
            title: 'Confirm!',
            content: " Are you sure you want to Submit ? ",
            buttons: {
                confirm:{
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function(){
                    $.ajax({
                        url: "<?php echo base_url(); ?>assessment/save_rating/"+rate_flag,
                        type: 'POST',
                        data: $('#VideoPitchingForm').serializeArray(),
                        beforeSend: function () {
                            customBlockUI();
                        },
                        success: function (Odata) {
                            var Data = $.parseJSON(Odata); 
                            if(Data['success']){
                                ShowAlret(Data['Msg'],'success');
                                $("#team_rating").html(Data['team_rating']);
                                $("#your_rating").html(Data['your_rating']);  
                                   $('.sh-btn').hide();
                                   location.reload();
                            }                              
                            customunBlockUI();
                        }
                    });
                }
            },
            cancel: function () {
                 this.onClose();
            }
            }
        });
    }
    function Retake_confirm(){
        $.confirm({
            title: 'Confirm!',
            content: " Are you sure you want to Retake Assessment ? ",
            buttons: {
                confirm:{
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function(){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url();?>assessment/save_retake",
                        data: {company_id:$('#company_id').val(),assessment_id :$('#assessment_id').val(),assessment_type :$('#assessment_type').val(),user_id :$('#user_id').val()},
                        success: function (Odata) {
                            var Data = $.parseJSON(Odata); 
                            if(Data['success']){
                                ShowAlret(Data['Msg'],'success');
								AssessmentUsersRefresh();
                            }                              
                            customunBlockUI();
                        }
                    });
                }
            },
            cancel: function () {
                 this.onClose();
            }
            }
        });
    }
</script>
