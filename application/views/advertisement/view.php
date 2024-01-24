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
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        
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
                                    <span>Advertisement</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>View Advertisement</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>advertisement" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            View Advertisement
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tabbable-line tabbable-full-width">                                           
                                             <form id="frmUsers" name="frmUsers" method="POST"  action="<?php echo $base_url;?>advertisement/update/<?php echo base64_encode($result->id);?>" enctype="multipart/form-data"> 
                                            
                                                  <?php
                                                $errors = validation_errors();
                                                //echo $errors;

                                                if ($errors) {?>
                                                    <div style="display: block;" class="alert alert-danger display-hide">
                                                        <button class="close" data-close="alert"></button>
                                                        You have some form errors. Please check below.
                                                        <?php echo $errors;?>
                                                    </div>
                                                    <?php } ?>
                                                    <div class="alert alert-danger display-hide">
                                                        <button class="close" data-close="alert"></button>
                                                        You have some form errors. Please check below.
                                                    </div>
                                                 
                                                 <div class="tab-content">
                                            
                                                <div class="tab-pane active" id="tab_overview"> 
                                                    <?php if ($Company_id == "") { ?>
                                                        <div class="row">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Company<span class="required"> * </span></label>
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" disabled="">
                                                                        <option value="">Please Select </option>
                                                                        <?php if(count($CompnayResultSet)>0){
                                                                        foreach ($CompnayResultSet as $cmp) { ?>
                                                                        <option value="<?php echo $cmp->id ?>" <?php echo($cmp->id==$result->company_id ? 'selected' : '') ?>><?php echo $cmp->company_name ?> </option>                                                                       
                                                                        <?php }  } ?>
                                                                </select>   
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                        <div class="row">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Title<span class="required"> * </span></label>
                                                                    <input type="text" name="advt_name" id="advt_name" maxlength="255" class="form-control input-sm" value="<?php echo $result->advt_name ?>" disabled>   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>Start/End Date</label>
                                                                    <div class="input-group input-large date-picker input-daterange" data-date="" data-date-format="dd/mm/yyyy">
                                                                        <input type="text" class="form-control input-sm" id="start_date" name="start_date" value="<?php echo ($result->start_date=="01-01-1970" ? '':$result->start_date) ?>" disabled>
                                                                        <span class="input-group-addon"> to </span>
                                                                        <input type="text" class="form-control input-sm" id="end_date" name="end_date" value="<?php echo ($result->end_date=="01-01-1970" ? '':$result->end_date) ?>" disabled>
                                                                    </div>
                                                                </div>
                                                            </div>                                                            
                                                        </div>                                                                                                                                                                                                                                
                                                        <div class="row">
                                                            <div class="col-md-4">    
                                                                <div class="form-group">
                                                                    <label>URL</label>
                                                                    <input type="text" name="url" id="url" maxlength="255" class="form-control input-sm" value="<?php echo $result->url ?>" disabled>   
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>By Sorting</label>
                                                                    <input type="text" name="sorting" id="sorting" maxlength="255" class="form-control input-sm" value="<?php echo $result->sorting ?>" disabled>                                                                    
                                                                </div>
                                                            </div>
                                                        </div>  
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Banner Image</label>
                                                                    <div class="fileinput fileinput-exists" data-provides="fileinput">
												<div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                                                                    <img src="<?php echo base_url().'assets/uploads/advertisement/no_image.jpg'?>" alt=""/>
												</div>
												<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                                                                    <img src="<?php echo base_url().'assets/uploads/advertisement/'.($result->thumbnail_image!='' ?$result->thumbnail_image : 'no_image.jpg'); ?>" alt=""/>
												</div>
												
											</div>
                                                                    <span class="text-muted">((Extensions allowed: .png , .gif, .jpg, .jpeg, .bmp)  width:750px, height:400px)</span>
                                                                </div>
                                                            </div>
                                                        </div>                                                        
                                                    <div class="row">
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Remarks</label>
                                                                <textarea rows="4" class="form-control input-sm" id="remarks" maxlength="150" name="remarks" placeholder="" value="" disabled><?php echo $result->remarks; ?></textarea>
                                                                <span class="text-muted">(Max 150 Characters)</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        <div class="row">
                                                            <div class="col-md-2">    
                                                                <div class="form-group">
                                                                    <label>Status<span class="required"> * </span></label>
                                                                    <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" disabled>
                                                                        <option value="1" <?php echo ($result->status==1)?'selected':'';?>>Active</option>
                                                                        <option value="0" <?php echo ($result->status==0)?'selected':'';?>>In-Active</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>                                                                                                                                                                                                                                                                                                                                                                                             
                                                </div>                                                                                                                                                       
                                                <div class="row">      
                                                    <div class="col-md-12 text-right">  
                                                        
                                                        </button>
                                                        <a href="<?php echo site_url("advertisement"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
            </div>
            <?php //$this->load->view('inc/inc_footer'); ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav'); ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
    </body>
</html>