<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo ($AddEdit=='E' ? 'Edit':'Create New'); ?> Trainee</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="alert alert-success MedicineReturnSuccess display-hide" id="successDiv">
        <button class="close" data-close="alert"></button>
        <span id="SuccessMsg"></span>
    </div>
    <div class="alert alert-danger  display-hide" id="modalerrordiv">
        <button class="close" data-close="alert"></button>
        <span id="modalerrorlog"></span>
    </div>
        <form name="UserDeviceForm" id="UserDeviceForm" >
            <div class="form-body">
                <div class="row">
                    <div class="col-md-3">       
                        <div class="form-group">
                            <label class="">Employee Code<span class="required"> * </span></label>
                            <input type="text" name="emp_id" id="emp_id" maxlength="50" value="<?php echo ($AddEdit=='E' ? $result->emp_id :''); ?>" class="form-control input-sm">                                 
                        </div>
                    </div>
                    <div class="col-md-3">       
                        <div class="form-group">
                            <label class="">First name<span class="required"> * </span></label>
                            <input type="text" name="first_name" id="first_name" maxlength="50" value="<?php echo ($AddEdit=='E' ? $result->firstname :''); ?>" class="form-control input-sm">                                 
                        </div>
                    </div>
                    <div class="col-md-3">       
                        <div class="form-group">
                            <label class="">Last name<span class="required"> * </span></label>
                            <input type="text" name="last_name" id="last_name" maxlength="50" value="<?php echo ($AddEdit=='E' ? $result->lastname :''); ?>" class="form-control input-sm">                                 
                        </div>
                    </div>
                </div>
                <div class="row">                    
                    <div class="col-md-3">       
                        <div class="form-group">
                            <label class="">Mobile No.<span class="required"> * </span></label>
                            <input type="number" name="mobile" id="mobile" value="<?php echo ($AddEdit=='E' ? $result->mobile :''); ?>" maxlength="50" class="form-control input-sm">                                 
                        </div>
                    </div>
                    <div class="col-md-3">       
                        <div class="form-group">
                            <label class="">Email<span class="required"> * </span></label>
                            <input type="text" name="email" id="email" maxlength="250" value="<?php echo ($AddEdit=='E' ? $result->email :''); ?>" class="form-control input-sm">                                 
                        </div>
                    </div>
                    <div class="col-md-3">       
                        <div class="form-group">
                            <label class="" <?php echo ($AddEdit=='E' ? 'hidden':''); ?>>Password<span class="required"> * </span></label>
                            <input type="<?php echo ($AddEdit=='E' ? 'hidden':'password'); ?>" name="password" id="password" maxlength="50" class="form-control input-sm">                                 
                        </div>
                    </div>
                </div>
                 <div class="row">                    
                    <div class="col-md-3">       
                        <div class="form-group">
                            <label class="">Employment Year</label>
                            <input type="text" name="empyear" id="empyear" maxlength="250" value="<?php echo ($AddEdit=='E' ? $result->employment_year :''); ?>" class="form-control input-sm">                                 
                        </div>
                    </div>
                     <div class="col-md-3">       
                        <div class="form-group">
                            <label class="">Education Background</label>
                            <input type="text" name="edubg" id="edubg" maxlength="250" value="<?php echo ($AddEdit=='E' ? $result->education_background :''); ?>" class="form-control input-sm">                                 
                        </div>
                    </div>
                     <div class="col-md-3">       
                        <div class="form-group">
                            <label class="">Department</label>
                            <input type="text" name="depart" id="depart" maxlength="250" value="<?php echo ($AddEdit=='E' ? $result->department :''); ?>" class="form-control input-sm">                                 
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">       
                        <div class="form-group">
                            <label class="">Region</label>
                            <select id="region_id" name="region_id" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%" >
                                <option value="">Select Region</option>
                                <?php 
                                    foreach ($RegionData as $Rgn) { ?>
                                    <option value="<?= $Rgn->id; ?>"<?php echo ($AddEdit=='E' && $Rgn->id==$result->region_id ? 'Selected' :''); ?>><?php echo $Rgn->region_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">       
                        <div class="form-group">
                            <label class="">Area</label>
                            <input type="text" name="area" id="area" maxlength="50" value="<?php echo ($AddEdit=='E' ? $result->area :''); ?>" class="form-control input-sm">                                 
                        </div>
                    </div>
                    <div class="col-md-3">    
                        <div class="form-group">
                            <label>Status<span class="required"> * </span></label>
                            <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                <option value="1" <?php echo ($AddEdit=='E' && $result->status ? 'Selected' :''); ?>>Active</option>
                                <option value="0" <?php echo ($AddEdit=='E' && !$result->status ? 'Selected' :''); ?>>In-Active</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3" style="margin-top: 15px">    
                        <div class="form-group">
                            <label class="mt-checkbox mt-checkbox-outline" for="istester"> Is it Tester?
                                <input id="istester" name="istester" <?php echo ($AddEdit=='E' && $result->istester ? 'checked' :''); ?> type="checkbox" value="1" /><span></span>
                            </label>
                        </div>
                    </div>
                </div>  
            </div>          
        </form>
    </div>
    <div class="modal-footer">
        <?php if($AddEdit=='E'){ ?>
            <button type="button" class="btn btn-orange" onclick="UpdateUserdata(<?php echo $result->user_id ?>)" >Update</button>
        <?php } else{ ?>
            <button type="button" class="btn btn-orange" onclick="UpdateUserdata(0);" >Save</button>
        <?php } ?>
    </div>
