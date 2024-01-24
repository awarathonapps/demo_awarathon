<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
$asset_url =$this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>
        <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
    <?php $this->load->view('inc/inc_htmlhead'); ?>
        <style>
            input[type="color"] {border: none;padding: 0px;}
        </style>
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <div class="page-wrapper">
            <?php $this->load->view('inc/inc_header'); ?>
            <div class="clearfix"> </div>
            <div class="page-container">
                <?php $this->load->view('inc/inc_sidebar'); ?>
                <div class="page-content-wrapper">
                    <div class="page-content">
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li>
                                    <span>Administrator</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Configuration</span>
                                </li>
                            </ul>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                            <?php if ($this->session->flashdata('flash_message')) { ?> 
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                    <?php echo $this->session->flashdata('flash_message'); ?>
                                </div>
                            <?php } ?>
                                <div id="errordiv" class="alert alert-danger display-hide">
                                    <button class="close" data-close="alert"></button>
                                    You have some form errors. Please check below.
                                    <br><span id="errorlog"></span>
                                </div>
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            Configuration
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">                                                                                            
                                    <div class="tab-content">
                                        <div class="table_body">
                                            <form action="" id="UploadForm" method="post" enctype="multipart/form-data" accept-charset="utf-8" >
                                                      <div class="form-group clearfix">
                                                         <label for="title" class="col-md-3">Company Name</label>
                                                         <div class="col-md-9">
                                                             <input type="text" class="form-control" name="company_id" value="<?php echo $CompanyData->company_name; ?>" id="company_id" placeholder="Company Name..." >
                                                         </div>
                                                     </div>
                                                     <div class="form-group clearfix">
                                                         <label for="title" class="col-md-3">Site Title</label>
                                                         <div class="col-md-9">
                                                             <input type="text" class="form-control" name="title" value="<?php echo $CompanyData->sitetitle; ?>" id="title" placeholder="Title...">
                                                         </div>
                                                     </div>
                                                     <div class="form-group clearfix">
                                                         <label for="description" class="col-md-3">Description</label>
                                                         <div class="col-md-9">
                                                             <textarea class="form-control" id="description" value="<?php echo $CompanyData->description; ?>" name="description" rows="3"></textarea>
                                                         </div>
                                                     </div>
                                                     <div class="form-group clearfix">
                                                         <label for="copyright" class="col-md-3">Copyright</label>
                                                         <div class="col-md-9">
                                                             <input type="text" class="form-control" name="copyright" value="<?php echo $CompanyData->copyright; ?>" id="copyright" placeholder="copyright...">
                                                         </div>
                                                     </div>
                                                     <div class="form-group clearfix">
                                                         <label for="contact" class="col-md-3">Contact</label>
                                                         <div class="col-md-9">
                                                             <input type="number" class="form-control" name="contact" value="<?php echo $CompanyData->contact_no; ?>" id="contact" placeholder="contact...">
                                                         </div>
                                                     </div>
                                                     <div class="form-group clearfix">
                                                         <label for="email" class="col-md-3">System Email</label>
                                                         <div class="col-md-9">
                                                             <input type="text" class="form-control" name="email" id="email" value="<?php echo $CompanyData->email; ?>" placeholder="email...">
                                                         </div>
                                                     </div>
                                                     <div class="form-group clearfix">
                                                         <label for="address" class="col-md-3">Address</label>
                                                         <div class="col-md-9">
                                                             <input type="text" class="form-control" name="address" id="address" value="<?php echo $CompanyData->address_i.','.$CompanyData->address_ii; ?>" placeholder="address...">
                                                         </div>
                                                     </div>
                                                     <div class="col-md-6"> 
                                                         <h4><strong>Threshold:</strong></h4>
                                                         <table class="table table-bordered table-striped" id="range_table">
                                                             <thead>
                                                                 <tr>
                                                                     <th>Threshold Range&nbsp;(%)</th>
                                                                     <th>Color</th>
                                                                     <th><button class="btn btn-primary btn-xs btn-mini " type="button" onclick="add_rangeslot();"><i class="fa fa-plus"></i></button></th>
                                                                 </tr>
                                                             </thead>
                                                             <tbody>
                                                             <?php
                                                               $slot_row = 1;
                                                               if (count($ThresholdData) > 0) {
                                                                   foreach ($ThresholdData as $rng) {  
                                                             ?>
                                                                 <tr id="rng_<?php echo $slot_row; ?>">
                                                                 <td style="width:250px;">
                                                                     <div class="col-md-6" style="padding-left:0px;"><input  class="form-control input-sm " id="range_from<?php echo $slot_row; ?>" name="range_from[]" placeholder="" type="text" value="<?php echo ($rng->range_from !='' ? $rng->range_from : ''); ?>"></div>
                                                                     <div class="col-md-6" style="padding-left:0px;"><input  class="form-control input-sm " id="range_to<?php echo $slot_row; ?>" name="range_to[]" placeholder="" type="text" value="<?php echo ($rng->range_to !='' ? $rng->range_to : ''); ?>"></div>
                                                                 </td>
                                                                 <td><input type="color" id="range_color<?php echo $slot_row; ?>" name="range_color[]" class="form-control input-sm " value="<?php echo ($rng->range_color != '' ? $rng->range_color : ''); ?>"></td>
                                                                 <td><button class="btn btn-danger btn-xs btn-mini " type="button" onclick="remove_rangelot(<?php echo $slot_row; ?>);"><i class="fa fa-times"></i></button></td>
                                                                 </tr>
                                                                 <?php
                                                               $slot_row++;
                                                                 }
                                                             }
                                                             ?>
                                                             </tbody>   
                                                         </table>
                                                      </div>
                                                    <div class="col-md-6">  
                                                         <h4><strong>Pass/Fail Value:</strong></h4>
                                                         <table class="table table-bordered table-striped" id="result_table">
                                                             <thead>
                                                                 <tr>
                                                                     <th>Status</th>
                                                                     <th>Threshold Range&nbsp;(%)</th>
                                                                     <th>Color </th>
                                                                 </tr>
                                                             </thead>
                                                             <tbody>
                                                             <?php
                                                               $result_row = 1;
                                                               if (count($ResultData) > 0) {
                                                                   foreach ($ResultData as $key => $rng) {  
                                                               ?>
                                                                 <tr id="rsl_<?php echo $result_row; ?>">
                                                                 <td>
                                                                    <?php echo $rng->assessment_status ?>
                                                                 </td>
                                                                 <?php if($key < 2){ ?> 
                                                                 <td style="width:250px;">
                                                                     <div class="col-md-6" style="padding-left:0px;"><input  class="form-control input-sm " id="result_from<?php echo $result_row; ?>" name="result_from[]" placeholder="" type="text" value="<?php echo ($rng->result_from !='' ? $rng->result_from : ''); ?>"></div>
                                                                     <div class="col-md-6" style="padding-left:0px;"><input  class="form-control input-sm " id="result_to<?php echo $result_row; ?>" name="result_to[]" placeholder="" type="text" value="<?php echo ($rng->result_to !='' ? $rng->result_to : ''); ?>"></div>
                                                                 </td> 
                                                                 <?php }else{ ?>
                                                                 <td></td>
                                                                 <?php } ?>
                                                                 <td><input type="color" id="result_color<?php echo $result_row; ?>" name="result_color[]" class="form-control input-sm " value="<?php echo ($rng->result_color != '' ? $rng->result_color : ''); ?>"></td>
                                                                 </tr>
                                                                 <?php
                                                               $result_row++;
                                                                 }
                                                             }
                                                             ?>
                                                             </tbody>   
                                                         </table>
                                                   </div>
                                                <div class="form-group clearfix">
                                                    <div class="col-md-offset-9 pull-right">
                                                        <input type="hidden" name="id" value="<?php echo $CompanyData->id; ?>" />
                                                        <button type="button" name="submit" id="btnSubmit" class="btn btn-orange" onclick="SubmitData();">Submit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    </div>                                                           
                                </div>                                                    
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </div>
<?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script>
    var base_url = "<?php echo $base_url; ?>";
    var slot_row ='<?php echo $slot_row; ?>';
    var frmsetting = $('#UploadForm');
    var form_error = $('.alert-danger', frmsetting);
    var form_success = $('.alert-success', frmsetting);
//    jQuery(document).ready(function () {
//    frmsetting.validate({
//        errorElement: 'span',
//        errorClass: 'help-block help-block-error',
//        focusInvalid: false,
//        ignore: "",
//        rules: {
//            title: {
//                required: true
//            }
//        },
//        invalidHandler: function (event, validator) {
//            form_success.hide();
//            form_error.show();
//            App.scrollTo(form_error, -200);
//        },
//        errorPlacement: function(error, element) {
//            if(element.hasClass('form-group')) {
//                error.appendTo(element.parent().find('.has-error'));
//            }
//            else if(element.parent('.form-group').length) {
//                error.appendTo(element.parent());
//            }
//            else {
//                error.appendTo(element);
//            }
//        },
//        highlight: function (element) {
//            $(element).closest('.form-group').addClass('has-error');
//        },
//        unhighlight: function (element) {
//            $(element).closest('.form-group').removeClass('has-error');
//        },
//        success: function (label) {
//            label.closest('.form-group').removeClass('has-error');
//        },
//        submitHandler: function (form) {
//            form_success.show();
//            form_error.hide();
////            form.submit();
//        }
//    });   
//});
function add_rangeslot() {
    $.ajax({
        type: "POST",
        data: {slot_row: slot_row},
        //async: false,
        url: base_url + "configuration/get_rangeslot",
        success: function (response) {
            var data = jQuery.parseJSON(response);
            if (data['Success']) { 
                 $('#range_table tbody').append(data['html']);
                    slot_row++;
            } else {
                ShowAlret(data['Msg'], 'error');
            }
        }
    });
}

function remove_rangelot(remove_id) {
    $.confirm({
        title: 'Confirm!',
        content: " are you sure you want to remove ? ",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $('#rng_' + remove_id).remove();
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}
function SubmitData() {  
    if (!$('#UploadForm').valid()) {
            return false;
    }  
   //var file_data = $('#img_url').prop('files')[0]; 
    var form_data = new FormData();                  
    //form_data.append('img_url', file_data);
    var other_data = $('#UploadForm').serializeArray();
    $.each(other_data,function(key,input){
        form_data.append(input.name,input.value);
    });	                                                                                                                               
        $.ajax({
            url: "<?php echo base_url() . 'configuration/update_site_setting'; ?>",
            type: 'POST',
            data:  form_data,
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function () {
                customBlockUI();
            },
            success: function (Odata) {
                var Data = $.parseJSON(Odata);
                if (Data['Success']) {
                    ShowAlret(Data['Message'], 'success');  
//                    location.reload();
                } else {
                    $('#errordiv').show();
                    $('#errorlog').html(Data['Message']);
                     App.scrollTo(form_error, -200);
                }
                customunBlockUI();
            }
        });
        return true;
    }

</script>
</body>
</html>