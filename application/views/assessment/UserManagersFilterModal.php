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
                            <select id="user_id" name="user_id" class="form-control input-sm select2 " placeholder="Please select"  style="width: 100%" >
                                <option value="">Please Select</option>
                                <?php 
                                    if(count($assessor_users)>0){
                                    foreach ($assessor_users as $user) { ?>
                                    <option value="<?= $user->userid; ?>" ><?php echo $user->name; ?></option>
                                <?php } }?>
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="margin-bottom: 20px;">       
                        <button type="button" id="mapp_assessor" name="mapp_assessor" class="btn btn-orange btn-sm"  onclick="add_mappassessor();">
                               <i class="fa fa-plus"></i>&nbsp; Mapp Assessor
                        </button>
                    </div>
                </div>
                <table class="table table-striped table-bordered table-hover" id="UserManagersFilterTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Trainee Region</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    
                </table>
            </div>
        </div>          
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-orange" onclick="SaveMappingRegion();" >Confirm</button>
</div>

<script type="text/javascript">
            var frm1=document.ManagerForm;
            var user_row = 1;
            jQuery(document).ready(function () {
//                LoadFilterUserManagerData();
                $('.select2').select2();     
            });
        </script>
