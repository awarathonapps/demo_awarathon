<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>
         <!--datattable CSS  Start-->
        <link href="<?php echo $base_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $base_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $base_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!--datattable CSS  End-->
        <?php $this->load->view('inc/inc_htmlhead'); ?>
    </head>
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <div class="page-wrapper">
            <?php $this->load->view('inc/inc_header'); ?>
            <div class="clearfix"> </div>
            <div class="page-container">
                <?php $this->load->view('layouts/inc_sidebar'); ?>
                <div class="page-content-wrapper">
                    <div class="page-content">
                        
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li>
                                    <span>Masters</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Utility</span>
<!--                                    <a data-title="An application role comprises a set of privileges that determine what users can see and do after signing in">
                                        <i class="icon-info font-black sub-title"></i>
                                    </a>-->
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Utility</span>
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
                                            Company Utility
                                           <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <form id="frmRole" name="frmRole" method="POST"  action="<?php echo $base_url;?>utility/submit">    
                                            <div class="row">    
                                                <div class="col-md-4">    
                                                    <div class="form-group">
                                                        <label>From<span class="required"> * </span></label>
                                                        <select id="from" name="from" class="form-control input-sm select2" placeholder="Please select" >
                                                            <option value="">Please Select Company</option>
                                                                <?php 
                                                                    foreach ($cmpdata as $cmp) { ?>
                                                                    <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">    
                                                    <div class="form-group">
                                                        <label>To<pan class="required"> * </span></label>
                                                        <select id="to" name="to" class="form-control input-sm select2" placeholder="Please select" >
                                                            <option value="">Please Select Company</option>
                                                                <?php 
                                                                    foreach ($cmpdata as $cmp) { ?>
                                                                    <option value="<?= $cmp->id; ?>"><?php echo $cmp->company_name; ?></option>
                                                                <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                 <div class="col-md-2">    
                                                    <div class="form-group">
                                                       <label class="mt-checkbox mt-checkbox-outline" for="all">
                                                           <input type="checkbox" class="checkall" name="all" onclick="" value="1" id="all"  />
                                                       <span></span>
                                                       </label>
                                                        <label>All</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2">    
                                                    <div class="form-group">
                                                        <label class="mt-checkbox mt-checkbox-outline" for="controllers">
                                                            <input type="checkbox" class="chkfolder" name="controllers" value="2" id="controllers" />
                                                        <span></span>
                                                        </label>
                                                        <label>Controllers</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">    
                                                    <div class="form-group">
                                                        <label class="mt-checkbox mt-checkbox-outline" for="models">
                                                            <input type="checkbox" class="chkfolder" name="models" value="3" id="models" />
                                                            <span></span>
                                                        </label>
                                                        <label>Models</label>
                                                    </div>
                                                </div>
                                                 <div class="col-md-2">    
                                                    <div class="form-group">
                                                        <label class="mt-checkbox mt-checkbox-outline" for="views">
                                                            <input type="checkbox" class="chkfolder" name="views" value="4" id="views" />
                                                            <span></span>
                                                        </label>
                                                         <label>Views</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">    
                                                    <div class="form-group">
                                                        <label class="mt-checkbox mt-checkbox-outline" for="helpers">
                                                            <input type="checkbox" class="chkfolder" name="helpers" value="5" id="helpers" />
                                                            <span></span>
                                                        </label>
                                                         <label>Helpers</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">    
                                                    <div class="form-group">
                                                        <label class="mt-checkbox mt-checkbox-outline" for="libraries">
                                                            <input type="checkbox" class="chkfolder" name="libraries" value="6" id="libraries" />
                                                            <span></span>
                                                        </label>
                                                         <label>Libraries</label>
                                                    </div>
                                                </div>
                                            </div>
                                                
                                            </div>
                                            <div class="row">      
                                                <div class="col-md-12 text-right margin-top-20">  
                                                    <button type="submit" id="role-submit" name="submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left">
                                                        <span class="ladda-label">Submit</span>
                                                    </button>
                                                    <!--<a href="<? php echo site_url("utility");?>" class="btn btn-default btn-cons">Cancel</a>-->
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
        <?php $this->load->view('inc/inc_footer_script'); ?>
         <script src="<?php echo $base_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script>
            var base_url = "<?php echo $base_url.'utility'; ?>";
  
            jQuery(document).ready(function() {
                $('.checkall').click(function () {
                    if ($(this).is(':checked')) {
                        $("input.chkfolder").attr('disabled', true);
                        $("input.chkfolder").attr('checked', false);
                    } else {
                        $("input.chkfolder").attr('disabled', false);
                    }
                });
            });
        </script>
    <!--<script type="text/javascript" src="<? php echo $base_url; ?>assets/customjs/cmsrole_validation.js"></script>-->
    </body>
</html>