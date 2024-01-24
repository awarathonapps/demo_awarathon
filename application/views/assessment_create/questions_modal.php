<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Search Questions</h4>
</div>
<div class="modal-body">
    <form name="QuestionForm" id="QuestionForm">
        <div class="portlet light">
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="QuestionTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>Question</th>
                            <th>Weightage</th>
                            <th>Read Timer(sec.)</th>
                            <th>Response Timer(sec.)</th>
                            <th class="table-checkbox ">
                                <?php if($AddEdit=='A'){ ?>
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes question_all" name="question_all"  id="chk" />
                                <span></span>
                            </label>
                                <?php }else{
                                    echo 'Action';
                                } ?>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>          
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-orange" onclick="Confirm_questions('<?php echo $edit_id; ?>');" >Confirm</button>
</div>
        <script type="text/javascript">
            var frm1=document.QuestionForm;
            jQuery(document).ready(function () {
                Load_questions_table('<?php echo $AddEdit; ?>');
                $('.question_all').click(function () {
                    if ($(this).is(':checked')) {
                        $("input[name='Question_list[]']").prop('checked', true);                                                
                    } else {
                        $("input[name='Question_list[]']").prop('checked', false);
                    }
                    $("input[name='Question_list[]']").each(function( index ) {
                        selected_questions($( this ).val());
                        //console.log( index + ": " + $( this ).val() );
                    });
                });            
            });
        </script>
