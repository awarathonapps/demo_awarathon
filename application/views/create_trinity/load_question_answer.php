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
                        <!-- <th>Compulsory</th> -->
                        <th>ID</th>
                        <th>LANGUAGE</th>
                        <th>QUESTIONS</th>
                        <th>ANSWER</th>
                        <th>MAPPING GOAL/WEIGHTS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $key = 0;
                    if (count((array)$fetch_question_answer) > 0) {
                        foreach ($fetch_question_answer as $pdata) {
                            $key++;
                            $company_id      = $pdata->company_id;
                            $id              = $pdata->id;
                            $question        = $pdata->question;
                            $answer          = $pdata->answer;
                            $script_id       = $pdata->script_id;
                            if (isset($c_question)) {
                                if (in_array($id, explode(',', $c_question))) {
                                    $checked = "checked";
                                } else {
                                    $checked = "";
                                }
                            } else {
                                $checked = '';
                            }
                    ?>
                            <tr id="Row-<?php echo $key; ?>">
                                <!-- <td> -->
                                <!-- <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" <? //php echo $checked; 
                                                                ?> class="checkboxes is_default" id="is_default <? //php echo $id 
                                                                                                                ?>" name="is_default[<? //php echo $id 
                                                                                                                                        ?>]" value="1" />
                                        <input type="hidden" name="question_id" id="question_id" value="<? //php echo $id; 
                                                                                                        ?>">
                                        <span></span>
                                    </label> -->
                                <!-- </td> -->
                                <td><?php echo $pdata->id; ?></td>
                                <td>
                                    <select id="language_id<?php echo $key; ?>" name="language_id<?php echo $key; ?>" class="form-control input-sm select2 ValueUnq language_id" style="width:100%" placeholder="Please select" style="width:100px;">
                                        <?php
                                        if (count($language_result) > 0) {
                                            foreach ($language_result as $language_data) {
                                        ?>
                                                <option value="<?php echo $language_data->id; ?>"><?php echo $language_data->name; ?></option>
                                        <?php
                                            }
                                        } ?>
                                    </select>
                                </td>
                                <td><?php echo $question; ?></td>
                                <td><?php echo $answer; ?></td>
                                <td>
                                    <div id="paramsub<?php echo $key; ?>"></div>
                                    <select id="parameter_id<?php echo $key; ?>" name="Old_parameter_id<?php echo $tr_id->id; ?>[]" multiple="" style="display:none;" onchange="getUnique_paramters()">
                                        <?php if (count($Parameter) > 0) {
                                            foreach ($Parameter as $p) { ?>
                                                <option value="<?php echo $p->id; ?>"><?php echo $p->description; ?></option>
                                                <!-- <option value="<? //php echo $p->id; 
                                                                    ?>" <? //php echo (in_array($p->id, $parameter_array[$tr_id->question_id]) ? 'selected' : '') 
                                                                        ?>><? //php echo $p->description; 
                                                                            ?></option> -->
                                        <?php
                                            }
                                        } ?>
                                    </select>
                                </td>
                                <td>
                                    <?php if ($view == 'view') { ?>
                                        <a class="btn btn-success btn-sm" href="<?php echo  base_url() . 'create_trinity/add_goal/' . $key . '/' . $pdata->id; ?>" accesskey="" data-target=" #LoadModalFilter" data-toggle="modal" disabled>Manage Goals </a>
                                        <a class="btn btn-success btn-sm" href="<?php echo base_url() . '/create_trinity/que_ans_edit/' . base64_encode($id); ?>" accesskey="" data-target="#LoadModalFilter-view" data-toggle="modal" disabled><i class="fa fa-pencil"></i> </a>
                                        <button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="DelQueAns(<?php echo $id; ?>)" href="javascript:void(0)"><i class="fa fa-times" disabled></i></button>
                                    <?php } else { ?>
                                        <a class="btn btn-success btn-sm" href="<?php echo base_url() . 'create_trinity/add_goal/' . $key . '/' . $pdata->id; ?>" accesskey="" data-target="#LoadModalFilter" data-toggle="modal">Manage Goals</a>
                                        <a class="btn btn-success btn-sm" href="<?php echo base_url() . '/create_trinity/que_ans_edit/' . base64_encode($id); ?>" accesskey="" data-target="#LoadModalFilter-view" data-toggle="modal"><i class="fa fa-pencil"></i></a>
                                        <button type="button" id="remove" name="remove" class="btn btn-danger btn-sm delete" onclick="DelQueAns(<?php echo $id; ?>)" href="javascript:void(0)"><i class="fa fa-times"></i></button>
                                    <?php } ?>

                                    <!-- <div class="btn-group">
                                        <?php if ($view == 'view') { ?>
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
                                                <a href="<?php echo base_url() . '/create_trinity/que_ans_edit/' . base64_encode($id); ?>" data-target="#LoadModalFilter-view" data-toggle="modal">
                                                    <i class="fa fa-eye"></i>&nbsp;Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a onclick="DelQueAns(<?php echo $id; ?>)" href="javascript:void(0)">
                                                    <i class="fa fa-trash-o"></i>&nbsp; Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div> -->
                                </td>
                            </tr>
                        <?php
                            $tr_id++;
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
    jQuery(document).ready(function() {
        $('.language_id').on('select2:select', function(e) {
            var data = e.params.data;
            $(".txt_trno").each(function() {
                let temp_language_id = "#language_id" + $(this).val();
                $(temp_language_id).val(data.id).trigger('change');
            });
        });
    });

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
                            url: "<?php echo base_url(); ?>create_trinity/delete_que_ans",
                            data: {
                                deleteid: Id
                            },
                            beforeSend: function() {
                                customBlockUI();
                            },
                            success: function(response_json) {
                                var response = JSON.parse(response_json);
                                ShowAlret(response.message, response.alert_type);
                                DatatableRefresh(<?php echo $script_id; ?>);
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