<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Feedback Questions</h4>
</div>
<div class="modal-body">
    <form name="modalForm" id="modalForm">
        <div class="row margin-bottom-10">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-md-3">Feedback&nbsp;</label>
                    <div class="col-md-9" style="padding:0px;">
                        <select id="feedbackset_id" name="feedbackset_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="FeedbackQuestionScore()" >
                            <option value="">Select</option>
                            <?php foreach ($FeedbackSet as $value) {?>
                                <option value="<?=$value->feedbackset_id;?>"><?php echo $value->feedback_name; ?></option>
                            <?php }?>
                        </select>
                        <input type="hidden" id="fcmp_id" value="<?php echo $Company_id ?>"/>
                        <input type="hidden" id="ftrainee_id" value="<?php echo($Trainee_id !='' ? $Trainee_id :'') ?>"/>
                        <input type="hidden" id="fworkshop_id" value="<?php echo($Workshop_id !='' ? $Workshop_id :'') ?>"/>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">    
                <div class="form-group">
                    <label class="control-label col-md-3">Graph Type</label>
                    <div class="col-md-9" style="padding:0px;">
                    <select id="graphtype_id" name="graphtype_id" class="form-control input-sm select2" placeholder="Please select" onchange="FeedbackQuestionScore()">
                        <option value="1">Pie Chart</option>
                        <option value="2">Bar Graph</option>                        
                    </select>
                    </div>
                </div>
            </div>
        
<!--            <div class="col-md-6">
                <div class="form-group">
                    <button type="button" class="btn btn-orange" onclick="FeedbackQuestionScore();">Search</button>
                </div>
            </div>    -->
        </div>
        <div class="row mt-10">
            <div class="col-md-12" id="AppendChart" >
            </div>
        </div>
    </form>
</div>
        <script type="text/javascript">
            var modalForm = document.modalForm;  
            var TotalChart = 1;
            jQuery(document).ready(function () {
                
            });
            function FeedbackQuestionScore(){   
                
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#fcmp_id').val(),workshop_id: $('#fworkshop_id').val(),feedbackset_id :$('#feedbackset_id').val(),RowCount:RowCount,graphtype_id:$('#graphtype_id').val()},
                    async: false,
                    url: "<?php echo base_url();?>no_weights_report/ajax_NoWeightWorkshopDataChart/"+TotalChart,
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var ChartMSt = Oresult['HtmlData'];                            
                            if(Oresult['Error']!=''){                            
                                ShowAlret(Oresult['Error'], 'error');          
                            }else{    
                                $('#AppendChart').empty();
                                $('#AppendChart').append(ChartMSt);                                
                            }

                        }
                        customunBlockUI();    
                    }
                });
            }                                        
        </script>
