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
                        <select id="feedbackset_id" name="feedbackset_id" class="form-control input-sm select2me" placeholder="Please select" style="width: 100%" onchange="FeedbackQuestionScore();" >
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
            <div id="Overall_score" class="col-md-4 overallscore circle" >
                
            </div>
<!--            <div class="col-md-6">
                <div class="form-group">
                    <button type="button" class="btn btn-orange" onclick="FeedbackQuestionScore();">Search</button>
                </div>
            </div>    -->
        </div>
        <div class="portlet light">            
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="QscoreFilterTable" width="100%">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Sub-Type</th>
                            <th>Questions</th>
                            <th>Score </th>                                                        
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>          
    </form>
</div>
        <script type="text/javascript">
            var modalForm = document.modalForm;
            var Qscoretable = $('#QscoreFilterTable');
            jQuery(document).ready(function () {
                
            });
            function OverallQuestionScore(){
                $.ajax({
                    type: "POST",
                    data: {company_id: $('#fcmp_id').val(),workshop_id: $('#fworkshop_id').val(),feedbackset_id :$('#feedbackset_id').val(),trainee_id :$('#ftrainee_id').val()},
                    async: false,
                    url: "<?php echo base_url();?>weights_report/ajax_FeedbackOverallScore",
                    beforeSend: function () {
                        customBlockUI();
                    },
                    success: function (msg) {
                        if (msg != '') {
                            var Oresult = jQuery.parseJSON(msg);
                            var OScore = Oresult['OverallScore'];
                            
                            if(Oresult['Error']!=''){                            
                                ShowAlret(Oresult['Error'], 'error');          
                            }else{                           
                                $('#Overall_score').empty();
                                $('#Overall_score').append('<label class="control-label col-md-6">OverAll Score(Average): <strong>'+OScore+'%</strong></label>');                                
                            }

                        }
                    customunBlockUI();    
                    }
                });
            }
            function FeedbackQuestionScore(){
                if($('#feedbackset_id').val()== ''){
                    ShowAlret("Please select Feedback.!!", 'error');
                    return false;
                }
                OverallQuestionScore();
                Qscoretable.dataTable({
                    destroy: true,
                    "language": {
                        "aria": {
                            "sortAscending": ": activate to sort column ascending",
                            "sortDescending": ": activate to sort column descending"
                        },
                        "emptyTable": "No Workshop data available in table",
                        "info": "Showing _START_ to _END_ of _TOTAL_ records",
                        "infoEmpty": "No records found",
                        "infoFiltered": "(filtered 1 from _MAX_ total records)",
                        "lengthMenu": "Show _MENU_",
                        "search": "Search:",
                        "zeroRecords": "No matching records found",
                        "paginate": {
                            "previous":"Prev",
                            "next": "Next",
                            "last": "Last",
                            "first": "First"
                        }
                    },
                    "bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "pageLength": 10,            
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
                        {'width': '100px','orderable': false,'searchable': true,'targets': [0]}, 
                        {'width': '100px','orderable': false,'searchable': false,'targets': [1]},
                        {'width': '100px','orderable': false,'searchable': false,'targets': [2]},
                        {'width': '100px','orderable': false,'searchable': false,'targets': [3]},
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'weights_report/getQuestionScoreData'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'feedbackset_id', value: $('#feedbackset_id').val()});
                        aoData.push({name: 'fcmp_id', value: $('#fcmp_id').val()});
                        aoData.push({name: 'ftrainee_id', value: $('#ftrainee_id').val()});
                        aoData.push({name: 'fworkshop_id', value: $('#fworkshop_id').val()});
                        $.getJSON(sSource, aoData, function (json) {
                            fnCallback(json);
                        });
                    },
                    "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                        return nRow;
                    }
                    , "fnFooterCallback": function (nRow, aData) {
                    }
                });
            }                                        
        </script>
