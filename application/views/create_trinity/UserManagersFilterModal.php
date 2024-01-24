<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Search Managers</h4>
</div>
<div class="modal-body">
    <form name="UserManagerForm" id="UserManagerForm">
        <div class="portlet light">
            <div class="form-body">
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-6">       
                        <div class="form-group">
                            <label class="control-label col-md-4">Manager Name</label>
                            <div class="col-md-8" style="padding:0px;">
                                <select id="user_id" name="user_id" class="form-control input-sm select2 " placeholder="Please select"  style="width: 100%">
                                <option value="">Please Select</option>
                                <?php 
                                    if(count((array)$assessor_users)>0){
                                    foreach ($assessor_users as $user) { ?>
                                    <option value="<?= $user->userid; ?>" ><?php echo $user->name; ?></option>
                                <?php } }?>
                            </select>
                            </div>
                        </div>
                    </div>
                    </div>
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-6">       
                        <div class="form-group">
                            <label class="control-label col-md-4">Trainee Region</label>
                            <div class="col-md-8" style="padding:0px;">
                            <select id="flt_region_id" name="flt_region_id" class="form-control input-sm select2me" placeholder="Please select"  style="width: 100%" onchange="LoadFilterUserData();" >
                                <option value="">All Region</option>
                                <?php if(count((array)$RegionList)>0){
                                    foreach ($RegionList as $Rgn) { ?>
                                    <option value="<?= $Rgn->id; ?>" ><?php echo $Rgn->region_name; ?></option>
                                <?php } }?>
                            </select>
                </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-bordered table-hover" id="UserFilterTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>Trainee Region</th>
                            <th>Department</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>Mobile No</th>
                            <th>Area</th>
                            <th class="table-checkbox ">
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes all" name="chk"  id="chk" />
                                <span></span>
                                </label>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>          
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-orange" onclick="SaveMappingUserAssessor();" >Confirm</button>
</div>
<script type="text/javascript">
    var frm1=document.UserManagerForm;
            jQuery(document).ready(function () {
                $(".select2").select2({
                    placeholder: 'Please Select',
                    width: '100%'
                    });
        LoadFilterUserData(1);
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
