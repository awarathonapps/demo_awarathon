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
                                    <span>Trinity</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>View Parameter</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>trinity_parameter" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-md-12">                                
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            View Parameter
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body"> 
                                        <form id="frmParameter" name="frmParameter" method="POST" > 
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_overview">                                                                                                         
                                                    <div class="row">
                                                         <?php if ($Company_id == "") { ?>
                                                        <div class="col-md-4">       
                                                            <div class="form-group">
                                                                <label class="">Company Name<span class="required"> * </span></label>
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" disabled="">
                                                                    <option value="">Please Select</option>
                                                                    <?php foreach ($cmp_result as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"  <?php echo ($result->company_id == $cmp->id ? 'Selected' : ''); ?>><?= $cmp->company_name; ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Assessment Type<span class="required"> * </span></label>
                                                                <select name="assessment_type" id="assessment_type" class="form-control input-sm select2" disabled="">
                                                                    <option value="">Please select</option>
                                                                    <?php foreach ($assessment_type as $val) { ?>
                                                                        <option value="<?php echo $val->id ?>" <?php echo($result->assessment_type == $val->id ? 'selected':'') ?>><?php echo $val->description ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Parameter<span class="required"> * </span></label>
                                                                <input type="text" name="parameter" id="parameter" maxlength="255" class="form-control input-sm" value="<?php echo $result->description ?>" disabled="">   
                                                            </div>
                                                        </div>
														<div class="col-md-4">       
															<div class="form-group">
																<label class="">Category<span class="required"> * </span></label>
																<select id="category_id" name="category_id" disabled="" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
																	<option value="">Please Select</option>
																	<?php foreach ($category_set as $cmp) { ?>
																		<option value="<?= $cmp->id; ?>" <?php echo ($result->category_id==$cmp->id ? 'selected' :'') ?>><?php echo $cmp->name; ?></option>
																	<?php } ?>
																</select>
															</div>
														</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">    
                                                            <div class="form-group">
                                                                <label>Status<span class="required"> * </span></label>
                                                                <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" disabled="">
                                                                    <option value="1" <?php echo ($result->status==1)?'selected':'';?>>Active</option>
                                                                    <option value="0" <?php echo ($result->status==0)?'selected':'';?>>In-Active</option>
                                                                </select>                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                                           
                                            </div>                                            
                                            <div class="row">      
                                                <div class="col-md-12 text-right">                                                      
                                                    <a href="<?php echo site_url("trinity_parameter"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script>         
            jQuery(document).ready(function() {   
                <?php if($result->weight_type == 1) { ?>
                    $('.number-class').show();    
                    $('.range-class').hide();
                <?php }else{ ?>
                    $('.range-class').show();
                    $('.number-class').hide();
                <?php } ?> 
            });        
        </script>                
    </body>
</html>