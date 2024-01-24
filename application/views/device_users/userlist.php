<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Search Users</h4>
</div>
<div class="modal-body">
    <?php if ($Company_id == "") { ?>
    <form id="FilterFrm" name="FilterFrm" method="post" >
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">    
                        <div class="form-group">
                            <label>Company</label>
                            <select id="modalcompany_id" name="modalcompany_id"  class="form-control input-sm select2" placeholder="Please select" style="width:100%" >
                                <option value="">Please select</option>
                                    <?php foreach ($CompnayResultSet as $cs) { ?>
                                        <option value="<?php echo $cs->id ?>"  ><?php echo $cs->company_name ?></option>
                                    <?php } ?>
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="row margin-bottom-10">
                    <div class=" col-md-6"></div>
                    <div class=" col-md-6 text-right">
                        <button type="button" class="btn blue-hoki btn-sm" onclick="DataGridTable()">Search</button>
                        <button type="button" class="btn blue-hoki btn-sm" onclick="ModalResetFilter()">Reset</button>
                    </div>
                </div>    
            </div>
        </form>
    <?php } ?>
    <form name="UserForm" id="UserForm">
        <div class="portlet light">
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="ModalDeviceTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>Company</th>
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
    <button type="button" class="btn btn-orange" onclick="ConfirmUsers();" >Confirm</button>
</div>
<script type="text/javascript">
    var frm1 = document.UserForm;
    jQuery(document).ready(function () {
        DataGridTable();
        $('.modall').click(function () {
            if ($(this).is(':checked')) {
                $("input[name='selected_id[]']").prop('checked', true);
            } else {
                $("input[name='selected_id[]']").prop('checked', false);
            }
            $("input[name='selected_id[]']").each(function (index) {
                SelectedUsers($(this).val());
                //console.log( index + ": " + $( this ).val() );
            });
        });
    });
    
</script>
