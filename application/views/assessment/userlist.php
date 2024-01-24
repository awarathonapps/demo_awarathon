<div class="modal-header">
    <button type="button" id="ClosetestModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Search Users</h4>
</div>
<div class="modal-body">
    <form name="UserForm" id="UserForm">
        <div class="portlet light">
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="ModalDeviceTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>NAME</th>
                            <th>EMAIL</th>                       
                            <th class="table-checkbox ">
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                    <input type="checkbox" class="checkboxes modall" name="chk"  id="chk" />
                                    <span></span>
                                </label>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>          
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-orange" onclick="ConfirmtestUsers();" >Confirm</button>
</div>
<script type="text/javascript">
    var NewtestUsersArrray=[];
    var frm1 = document.UserForm;
    var Company_id = '<?php echo $Company_id; ?>';
    jQuery(document).ready(function () {
        DataGridTable();
        $('.modall').click(function () {
            if ($(this).is(':checked')) {
                $("input[name='selected_id[]']").prop('checked', true);
            } else {
                $("input[name='selected_id[]']").prop('checked', false);
            }
            $("input[name='selected_id[]']").each(function (index) {
                SelectedtestUsers($(this).val());
                //console.log( index + ": " + $( this ).val() );
            });
        });
    });
    
</script>
