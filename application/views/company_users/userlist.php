<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Search Trainer</h4>
</div>
<div class="modal-body">
    <form name="UserForm" id="UserForm">
        <div class="portlet light">
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="TrainerTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>Designation</th>                                              
                            <th class="table-checkbox ">
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes all" name="chk"  id="chk" />
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
    <button type="button" class="btn btn-orange" onclick="ConfirmUsers();" >Confirm</button>
</div>
        <script type="text/javascript">
            var frm1=document.UserForm;
            jQuery(document).ready(function () {
                DataGridTable(1);
                $('.all').click(function () {
                    if ($(this).is(':checked')) {
                        $("input[name='id[]']").prop('checked', true);                                                
                    } else {
                        $("input[name='id[]']").prop('checked', false);
                    }
                    $("input[name='id[]']").each(function( index ) {
                        SelectedUsers($( this ).val());
                        //console.log( index + ": " + $( this ).val() );
                    });
                });            
            });
        </script>
