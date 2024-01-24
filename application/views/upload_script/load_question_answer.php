<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Question Answer List
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="kt-section__content">
            <!-- <div class="table-responsive"> -->
            <table class="table table-bordered" id="que_ans_datatable">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Questions</th>
                        <th>Answer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count((array)$mapped_id) > 0) {
                        $sc_arr = array();
                        foreach($mapped_id as $m){
                            $sc_arr[] = $m->question_id;
                        }
                    } else {
                        $sc_arr[] = 0;
                    }
                    if (count((array)$fetch_question_answer) > 0) {
                        foreach ($fetch_question_answer as $pdata) {
                            $company_id      = $pdata->company_id;
                            $id              = $pdata->id;
                            $question         = $pdata->question;
                            $answer       = $pdata->answer;
                            $script_id = $pdata->script_id;

                    ?>
                            <tr>
                                <td><?php echo $pdata->id; ?></td>
                                <td><?php echo $pdata->question; ?></td>
                                <td><?php echo $pdata->answer; ?></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if ($view == 'view') { ?>
                                            <button class="btn orange btn-xs btn-outline dropdown-toggle" id="act-btn" type="button" data-toggle="dropdown" aria-expanded="false" disabled="">
                                                Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                            </button>
                                        <?php } else if (in_array($id, $sc_arr)) { ?>
                                            <button class="btn orange btn-xs btn-outline dropdown-toggle" id="act-btn" type="button" data-toggle="dropdown" aria-expanded="false" disabled="">
                                                Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                            </button>
                                        <?php } else { ?>
                                            <button class="btn orange btn-xs btn-outline dropdown-toggle" id="act-btn" type="button" data-toggle="dropdown" aria-expanded="false">
                                                Actions&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                            </button>
                                        <?php } ?>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <li>
                                                <a href="<?php echo base_url() . '/upload_script/que_ans_edit/' . base64_encode($id); ?>" data-target="#LoadModalFilter-view" data-toggle="modal">
                                                    <i class="fa fa-eye"></i>&nbsp;Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a onclick="DelQueAns(<?php echo $id; ?>)" href="javascript:void(0)">
                                                    <i class="fa fa-trash-o"></i>&nbsp; Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php
                    }
                    } else {
                        ?>
                        <!-- <tr>
                            <td colspan="100">No Records Found</td>
                        </tr> -->
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function DelQueAns(Id) {
        $.confirm({
            title: 'Confirm!',
            content: " Are you sure you want to delete Question and Answer? ",
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-orange',
                    keys: ['enter', 'shift'],
                    action: function() {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url(); ?>upload_script/delete_que_ans",
                            data: {
                                deleteid: Id
                            },
                            beforeSend: function() {
                                customBlockUI();
                            },
                            success: function(response_json) {
                                var response = JSON.parse(response_json);
                                ShowAlret(response.message, response.alert_type);
                                customunBlockUI();
                            }
                        });
                    }
                },
                cancel: function() {
                    this.onClose();
                }
            }
        });
    }
</script>