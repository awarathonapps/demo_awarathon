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
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link href="<?php echo $asset_url; ?>assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/cropper/cropper.css" rel="stylesheet" type="text/css" />
        <style>
            .sticky-bar{
                /* top: 75px; */
                position: fixed !important;
                z-index: 10000;
                right: 10px;
                left: 255px;
            }
            .my-line{
                width: 100%;
                height: 1px;
                border-bottom: 1px solid;
                border-color: #e7ecf1;
                margin-bottom: 20px;
            }
            .modal{
                left: 285px;
                top: 80px;
            }
            .margin-bottom-50{
                margin-bottom: 50px !important;
            }
            .loading {
                display: none;
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;               
                opacity: .75;
                filter: alpha(opacity=75);
                z-index: 20140628;
            }
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
                                    <span>Feedback Form</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>View Feedback Form</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>feedback_form" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">
                                <?php if ($this->session->flashdata('flash_message')) { ?> 
                                    <div class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                        <?php echo $this->session->flashdata('flash_message'); ?>
                                    </div>
                                <?php } ?>
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            View Feedback Form
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">   
                                        <form id="feedbackForm" name="feedbackForm" method="POST" > 
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_overview">                                                          
                                                    <div class="row">    
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Company Name<span class="required"> * </span></label>
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" disabled="">
                                                                    <option value="">Please Select</option>
                                                                    <?php foreach ($SelectCompany as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"  <?php echo ($HeadResult->company_id == $cmp->id ? 'Selected' : ''); ?>><?= $cmp->company_name; ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>                                                    
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Title<span class="required"> * </span></label>
                                                                <input type="text" name="form_name" id="form_name" maxlength="255" class="form-control input-sm" value="<?php echo $HeadResult->form_name; ?>" disabled="">   
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="row">                                                               
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Short Description</label>
                                                                <textarea rows="4" class="form-control input-sm" id="short_description" maxlength="150" name="short_description" placeholder="" disabled=""><?php echo $HeadResult->short_description; ?></textarea>
                                                                <span class="text-muted">(Max 150 Characters)</span>
                                                            </div>
                                                        </div> 
                                                        <div class="col-md-2">    
                                                            <div class="form-group">
                                                                <label>Status<span class="required"> * </span></label>
                                                                <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" disabled="">
                                                                    <option value="1" <?php echo ($HeadResult->status == 1) ? 'selected' : ''; ?>>Active</option>
                                                                    <option value="0" <?php echo ($HeadResult->status == 0) ? 'selected' : ''; ?>>In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">  
                                                        <div class="col-md-12">
                                                            <table class="table table-striped table-bordered table-hover" id="FieldDatatable" width="100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th width="20%">Field Name</th>
                                                                        <th width="20%">Display Name</th>
                                                                        <th width="20%">Type</th>
                                                                        <th width="20%">Data</th>
                                                                        <th width="5%">is Required</th>
                                                                        <th width="10%">Status</th>
                                                                        <th width="5%"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $EditField = count($Result);
                                                                    $key=0;
                                                                    if ($EditField > 0) {
                                                                        foreach ($Result as $fr) {
                                                                            $key++;
                                                                            ?>
                                                                            <tr id="Row-<?php echo $key; ?>">
                                                                                <td><input type="text" name="field_name[<?php echo $fr->id; ?>]" id="field_name<?php echo $key; ?>" value="<?php echo $fr->field_name ?>" class="form-control input-sm" maxlength="255" style="width:100%" disabled=""> </td>
                                                                                <td><input type="text" name="disp_name[<?php echo $fr->id; ?>]" id="disp_name<?php echo $key; ?>" value="<?php echo $fr->field_display_name ?>" class="form-control input-sm" maxlength="255" style="width:100%" disabled=""> </td>
                                                                                <td><select id="field_type<?php echo $key; ?>" name="field_type[<?php echo $fr->id; ?>]" class="form-control input-sm select2" style="width:100%" disabled="">    
                                                                                        <option value="">Please Select</option>
                                                                                        <?php foreach ($SelectType as $ftype) { ?>
                                                                                        <option value="<?= $ftype->name; ?>"  <?php echo ($fr->field_type == $ftype->name ? 'Selected' : ''); ?>><?= $ftype->name; ?> </option>
                                                                                        <?php } ?>
                                                                                    </select></td>
                                                                                    <td><textarea rows="3" class="form-control input-sm" id="data_area<?php echo $key; ?>" maxlength="150" name="data_area[<?php echo $fr->id; ?>]" disabled=""><?php echo $fr->default_value ?></textarea></td>
                                                                                    <td><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                                            <input type="checkbox" id="required_id<?php echo $key; ?>" class="checkboxes" name="required_id[<?php echo $fr->id; ?>]" value="1" <?php echo($fr->is_required ? 'Checked':''); ?> disabled=""/>
                                                                                    <span></span>
                                                                                    </label></td> 
                                                                                    <td><select id="field_old_status<?php echo $key; ?>" name="field_old_status[<?php echo $fr->id; ?>]" class="form-control input-sm select2" style="width:100%" disabled="">
                                                                                                    <option value="1" <?php echo($fr->status ? 'Selected':''); ?>>Active</option>
                                                                                                    <option value="0" <?php echo(!$fr->status ? 'Selected':''); ?>>In-Active</option>
                                                                                    </select>
                                                                                    </td>    
                                                                                <td>
                                                                                    <button type="button" id="remove" name="remove" class="btn btn-danger btn-sm" onclick="RowDelete(<?php echo $key; ?>)" disabled=""><i class="fa fa-times" ></i></button></td>
                                                                            </tr>
                                                                        <?php }
                                                                            } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">      
                                                        <div class="col-md-12 text-right">                                                              
                                                            <a href="<?php echo site_url("feedback_form"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                        </div>
                                                    </div>
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
<?php //$this->load->view('inc/inc_quick_sidebar');  ?>
    </div>
<?php //$this->load->view('inc/inc_footer');   ?>
</div>
<?php //$this->load->view('inc/inc_quick_nav');  ?>
<?php $this->load->view('inc/inc_footer_script'); ?>

</body>
</html>