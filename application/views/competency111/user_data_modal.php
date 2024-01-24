<style>

/* Important part */
.modal-dialog{
    overflow-y: initial !important
}
.modal-body-main{
    height: 80vh;
    overflow-y: auto;
}
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body-main" id="user_data_modal">
    <form name="data_user" id="data_user" method="POST" action="<?php echo base_url() . 'competency/export_raps_data' ?>">
        <input type="hidden" name='trainer_id' id ='trainer_id' value="<?php echo $trainer_id; ?>" >
        <input type="hidden" name='is_custom' id ='is_custom' value="<?php echo $is_custom; ?>" >
        <input type="hidden" name='assessment_id' id ='assessment_id' value="<?php echo $assessment_id; ?>" >
        <input type="hidden" name='SDate' id ='SDate' value="<?php echo $SDate; ?>" >
        <input type="hidden" name='EDate' id ='EDate' value="<?php echo $EDate; ?>" >
        <div class="portlet light">
            <div class="form-body">
                <a id="export_button" class="btn orange btn-sm btn-outline" style="margin: 0px 0px 8px 771px;" onclick="export_raps_data()">
                    <i class="fa fa-file-excel-o"></i> Export</a>
                </a>
                <table class="table table-striped table-bordered table-hover" id="user_data_modal" width="100%">
                    <thead>
                        <tr>
                            <th>Assessment Name</th>
                            <th>Employee ID</th>
                            <th>Manager Name</th>
                            <th>Learner Name</th>
                            <th>Scores</th>
                        </tr>
                    </thead>
                    <tbody id='user_data_modal_body'>
                        <?php echo $html_data; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
    function export_raps_data() {
        var compnay_id = Company_id;
        if (compnay_id == "") {
            ShowAlret("Please select Company first.!!", 'error');
            return false;
        }
        $.confirm({
            title: 'Confirm!',
            content: "Are you sure want to Export. ? ",
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-primary',
                    keys: ['enter', 'shift'],
                    action: function() {
                        data_user.submit();
                    }
                },
                cancel: function() {
                    this.onClose();
                }
            }
        });
    }
</script>