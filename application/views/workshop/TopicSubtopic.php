<?php if ($AddEdit == 'A') { ?>
<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo ($type==1 ? 'Questionset :':'Feedback Questionset :').$QuestionSet_data->title; ?></h4>
</div>
<?php } ?>
<div class="alert alert-danger display-hide" id="errordiv1">
    <button class="close" data-close="alert"></button>
    You have some form errors. Please check below.<br/>
    <span id="errorlog1"></span>
</div>
<div class="tabbable-line ">
    <?php if ($AddEdit == 'E' || $AddEdit == 'V' ) { ?>

        <ul class="nav nav-tabs" id="tabs">
            <li class="active">
                <a href="#tab_trainer" data-toggle="tab">Trainer</a>
            </li>

            <li>
                <a href="#tab_question" data-toggle="tab" >Manage Questions </a>
            </li> 
        </ul>
    <div class="col-md-8" style="    margin-top: -33px;
         float: right;">
        <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><?php echo ($type == 1 ? 'Questionset :' : 'Feedback Questionset :') . $QuestionSet_data->title; ?></h4>
    </div> 
    <?php } ?>  
       
    <div class="tab-content">
        <div class="tab-pane active" id="tab_trainer"> 
            <form name="TrainerDataForm" id="TrainerDataForm">
                <input type="hidden" id="qus_lockflag" name="qus_lockflag" value="<?php echo $disabled_selected ?>">
                <input type="hidden" id="Qsession" name="Qsession" value="<?php echo $Session ?>">
                <input type="hidden" id="Qset_id" name="Qset_id" value="<?php echo $QuestionSet_id ?>">
                <input type="hidden" id="token_key" name="token_key" value="<?php echo $token_key ?>">      
                <div class="row">
                    <div class="col-md-12 ">
                        <table class="table table-striped table-bordered table-hover" id="TopicFilterTable" width="100%" >
                            <thead>
                                <tr>
                                    <th>Topic</th>
                                    <th>Sub-Topic</th>
                                    <th>Total Questions</th>
                                    <?php
                                    echo $type == 1 ?
                                            '<th>Trainer</th>' : '';
                                    ?>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                if (count($TopicSubtopic) > 0) {
                                    $Total = 0;
                                    $i = 0;
                                    foreach ($TopicSubtopic as $key => $value) {
                                        $Total +=$value['totalqsn'];
                                        $i++;
                                        ?>
                                        <tr>  
                                            <td><?php echo $value['topic'] ?></td>
                                            <td><?php echo $value['subtopic'] ?></td>
                                            <td><?php echo $value['totalqsn'] ?></td>
                                            <?php if ($type == 1) { ?>
                                        <input type="hidden" id="workshop_Ques_tra_id" name="workshop_Ques_tra_id[]" value="<?php echo $value['id'] ?>">
                                        <input type="hidden" id="Qsettrainertableid" name="Qsettrainertableid[]" value="<?php echo $value['qsettrainertable_id'] ?>">
                                        <td>
                                            <select id="qset_trainer_<?php echo $i; ?>" name="qset_trainer[]" placeholder="Please select"  
                                            <?php
                                            if ($i == 1) {
                                                echo 'class="form-control input-sm select2_me "';
                                                echo 'onchange="set_same_trainer(' . $i . ')"';
                                            } else {
                                                echo 'class="form-control input-sm select2_me trainerclass "';
                                            } echo ($disabled_selected ? 'disabled' : '');
                                            ?>
                                                    >
                                                        <?php foreach ($Trainer as $t) { ?>
                                                    <option value="<?php echo $t->userid ?>" 
                                                    <?php
                                                    if ($istrainer_changable && $df_trainer_id != "") {
                                                        if ($t->userid == $df_trainer_id) {
                                                            echo 'selected';
                                                        }
                                                    } elseif (count($Qus_Session_Array) > 0 && isset($Qus_Session_Array[$value['qsettrainertable_id']])) {
                                                        echo ($t->userid == $Qus_Session_Array[$value['qsettrainertable_id']]['trainer_id'] ? 'selected' : '');
                                                    } elseif ($df_trainer_id == "" && $t->userid == $value['trainer_id']) {
                                                        echo 'selected';
                                                    }
                                                    ?>
                                                            ><?php echo $t->trainer ?></option>    
                                                        <?php } ?>
                                            </select> 
                                        </td>
                                    <?php } ?> 
                                    </tr>
                                    <?php
                                }
                                echo "<tr><th colspan=2> Total</th><th>" . $Total . "</th>" . ($type == 1 ? '<th></th>' : '') . '</tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                        <input type="hidden" value="<?php echo $workshop_id ?>" name="ques_workshop_id" id="ques_workshop_id">    
                    </div>    
                </div>
                <div class="row"> 
                    <?php if($AddEdit != 'V'){ ?>
                    <div class="col-md-11 text-right">  
                        <button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." 
                                class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="QsetTrainerSave(<?php echo $type; ?>);">
                            <span class="ladda-label">Confirm</span>
                        </button>                    
                    </div>
                    <?php } ?>
                </div>       
            </form>
        </div>
         <?php if ($AddEdit == 'E' || $AddEdit == 'V' ) { ?>
        <div class="tab-pane " id="tab_question">
            <form id="QTableForm" name="QTableForm" method="post">
                <input type="hidden" id="qus_lockflag2" name="qus_lockflag" value="<?php echo $disabled_selected ?>">
                <input type="hidden" id="Qsession2" name="Qsession" value="<?php echo $Session ?>">
                <input type="hidden" id="Qset_id2" name="Qset_id" value="<?php echo $QuestionSet_id ?>">
                <input type="hidden" id="token_key" name="token_key" value="<?php echo $token_key ?>">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label class="control-label col-md-4">Question Order:</label>
                                <div class="col-md-8">
                                    <select class="form-control input-sm " name="qus_orderby" id="qus_orderby" onchange="enabled_sorting();" <?php echo ($AddEdit =='V' ? 'disabled':''); ?>>
                                        <option value="1" <?php echo ($Qus_Orders == 1 ? 'selected' : ''); ?>>Random</option>
                                        <option value="2" <?php echo ($Qus_Orders == 2 ? 'selected' : ''); ?>>Sequence</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-hover table-checkable order-column" id="question_table" style="width:100%; z-index: 9999999999 !important;">
                            <thead>
                                <tr>
                                    <th>Seq.</th>
                                    <th>Que.ID.</th>                                    
                                    <?php if($type==1){ ?>
                                    <th>Trainer</th>
                                    <th>Topic</th>
                                    <th>Sub-Topic</th>
                                    <?php } else{ ?>
                                    <th>Type</th>
                                    <th>Sub-Type</th>
                                    <th>Question Type</th>
                                    <?php } ?>
                                    <th>Question</th>                                                                
                                    <th>Answer</th>
                                </tr>
                            </thead>
                            <tbody></tbody>

                        </table>
                    </div> 
                    <div class="row"> 
                    
                    <div class="col-md-11 text-right">
					<?php if($AddEdit != 'V'){ ?>
                        <button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." 
                                class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="UpdateSorting(<?php echo $type ?>)">
                            <span class="ladda-label">Confirm</span>
                        </button>&nbsp; &nbsp;
						<?php } ?>
                        <button type="button" id="role-submit" class="btn btn-default btn-cons "  data-dismiss="modal" aria-hidden="true" >
                            <span class="ladda-label">Close</span>
                        </button> 
                    </div>
                    
                </div> 
                </div>
            </form>
        </div>
         <?php } ?>
    </div>    
</div>    

<script>
    $(document).ready(function () {
        $('.select2_me').select2({
            width: "100%"
        });
         <?php if ($AddEdit == 'E' || $AddEdit == 'V' ) { ?>
        enabled_sorting();
         <?php } ?>
    });
//    $('#tabs a').click(function (e) {
//        //e.preventDefault();
//        $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
//    });
</script>
