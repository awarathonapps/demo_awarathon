<div class="alert alert-danger  display-hide" id="modalerrordiv"><span style="color:red">You have few Errors, please check below: </span><br><br>
    <button class="close" data-close="alert"></button>
    <span id="modalerrorlog"></span>
</div>
<form id="frmVehicleForm" name="frmVehicleForm" onsubmit="return false;">            
    <div class="row"> 
        <div class="col-md-12">
            <div class="form-group" id="eway">
                <table class="table table-bordered table-hover" id="eway_table">
                    <thead>
                        <tr>
                            <th>LR No</th>
                            <th>Container No</th>
                            <th>E Way Bill</th>                                    
                            <th width="15%">Add Vehicle</th>
                        </tr>
                    </thead>
                    <input type="hidden" id="shipment_id" name="shipment_id" value="<?php echo $shipment_id ?>" />
                    <tbody>
                        <?php if(count($ShipData) > 0) { 
                            foreach ($ShipData as $val) { ?>
                                <tr>                    
                                    <td><?php echo $val->lr_no ?></td>
                                    <td><?php echo $val->container_no ?></td>
                                    <td><?php echo $val->eway_bill ?></td>
                                    <input type="hidden" name="shiptransid[]" value="<?php echo $val->id ?>"/>
                                    <td><a class="btn btn-orange btn-sm btn_border" id="btnaddpanel3"  onclick="get_vendor_data(<?php echo $val->id ?>)" 
                                        >&nbsp;Vender Details </a></td>
                                </tr>
                        <?php } }?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>   
<div class="modal-footer">
    <div class="col-md-12 text-right ">  
        <button type="button" id="modal-create-submit" name="modal-create-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="update_eway_data()">
            <span class="ladda-label">Update</span>
        </button>
        <button type="button" id="CloseModalBtn" data-dismiss="modal" class="btn btn-default btn-cons">Cancel</button>
    </div>
</div>
<script>
    var frmVehicleForm = $('#frmVehicleForm');
    var form_error   = $('.alert-danger', frmVehicleForm);
    var form_success = $('.alert-success', frmVehicleForm);        
    jQuery(document).ready(function () {
        
    });        
    $('#despatch_date').datepicker({
        rtl: App.isRTL(),
        orientation: "left",
        autoclose: true,
        format: 'dd-mm-yyyy',
        todayHighlight: true
    });
    frmVehicleForm.validate({
        errorElement: 'span',
        errorClass: 'help-block help-block-error',
        focusInvalid: false,
        ignore: "",
        rules: {                        
            despatch_date: {
                required: true
            },
            assigner_id: {
                required: true
            },
            'eway_bill[]': {
                required: true
            }
        },
        invalidHandler: function (event, validator) {
            form_success.hide();
            form_error.show();
            App.scrollTo(form_error, -200);
        },
        errorPlacement: function (error, element) {
            if (element.hasClass('form-group')) {
                error.appendTo(element.parent().find('.has-error'));
            } else if (element.parent('.form-group').length) {
                error.appendTo(element.parent());
            } else {
                error.appendTo(element);
            }
        },
        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        success: function (label) {
            label.closest('.form-group').removeClass('has-error');
        },
         submitHandler: function (form) {
            form_success.show();
            form_error.hide();
         }
    });            
</script>
