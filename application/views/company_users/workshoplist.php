<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Search Workshop</h4>
</div>
<div class="modal-body">
        <div class="portlet-body">
        <!-- BEGIN FORM-->
        <form id="FilterFrm" name="FilterFrm" method="post" >
            <div class="form-body">
                <div class="row">
<!--                    <div class="col-md-6">
                        <div class="form-group">
                             <label class="control-label col-md-3">Start Date</label>                                                    
                             <div class="col-md-9 input-group input-sm date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control input-sm" id="start_date" name="start_date" value="" >
                                <span class="input-group-addon"> to </span>
                                <input type="text" class="form-control input-sm" id="end_date" name="end_date" value="">
                            </div>                                                   
                        </div>
                    </div>-->
                    <div class="col-md-6">    
                        <div class="form-group">
                            <label>Region/Division</label>
                            <select id="region" name="region" id="region" class="form-control input-sm select2" placeholder="Please select" style="width:100%" >
                                <option value="">All Select</option>
                                    <?php foreach ($Region as $regiontpe) {?>
                                    <option value="<?php echo $regiontpe->id ?>"><?php echo $regiontpe->region_name?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">    
                        <div class="form-group">
                            <label>Workshop Type</label>
                            <select id="wktype" name="wktype" id="wktype" class="form-control input-sm select2" placeholder="Please select" style="width:100%" >
                                <option value="">All Select</option>
                                    <?php foreach ($WorkshopType as $wktype) {?>
                                    <option value="<?php echo $wktype->id ?>"><?php echo $wktype->workshop_type?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="row margin-bottom-10">
                    <div class=" col-md-6"></div>
                    <div class=" col-md-6 text-right">
                                <button type="button" class="btn blue-hoki btn-sm" onclick="DataGridTable(2)">Search</button>
                                <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter()">Reset</button>
                        </div>
                </div>    
            </div>
        </form>
        <!-- END FORM-->
        <form name="WorkshopForm" id="WorkshopForm" >
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="WorkshopTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>Workshop</th>
                            <th>Start Date</th>
                            <th>End Date</th>
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
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-orange" onclick="ConfirmWorkshop(3);" >Confirm</button>
</div>
<script type="text/javascript">
    var frm1=document.WorkshopForm;
    jQuery(document).ready(function () {
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true,
                format: 'dd-mm-yyyy'
            });
        }
        //$('#region,#wktype').select2();
        $('.select2').select2({
            allowClear:true,
            placeholder: 'All'
        });
        DataGridTable(2);
        $('.all').click(function () {
            if ($(this).is(':checked')) {
                $("input[name='id[]']").prop('checked', true);                                                
            } else {
                $("input[name='id[]']").prop('checked', false);
            }
            $("input[name='id[]']").each(function( index ) {
                SelectedWorkshop($( this ).val());
                //console.log( index + ": " + $( this ).val() );
            });
        });            
    });
    function ResetFilter(){
        $('.select2').val("");
        
        document.FilterFrm.reset();
        DataGridTable(2);
    }
</script>
