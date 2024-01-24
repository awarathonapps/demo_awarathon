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
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <style>
            .portlet.light>.portlet-title {
                min-height: 0px; 
           }
           .tabbable-line>.tab-content {
                padding: 20px 0;
            }
            .bulkaction{
              margin-left:133px
              
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
                                    <span>Feedback</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Feedback Set</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>feedback_set" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Edit Feedback Set
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body"> 
                                        <div class="tabbable-line tabbable-full-width">
                                            <ul class="nav nav-tabs" id="tabs">
                                                <li <?php echo ($step == 1 ? 'class="active"' : ''); ?>>
                                                    <a href="#tab_general" data-toggle="tab">General</a>
                                                </li>
                                                <li <?php echo ($step == 2 ? 'class="active"' : ''); ?>>
                                                    <a href="#tab_data" data-toggle="tab">Manage Questions</a>
                                                </li>
                                            </ul>
                                        <div class="tab-content">
                                        <div class="tab-pane <?php echo ($step == 1 ? 'active"' : ''); ?>" id="tab_general">
                                        <form id="frmFeedbackSet" name="frmFeedbackSet" method="POST"  action="<?php echo $base_url;?>feedback_set/update/<?php echo base64_encode($result->id);?>"> 
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_overview">    
                                                    <div id="errordiv" class="alert alert-danger display-hide">
                                                        <button class="close" data-close="alert"></button>
                                                        You have some form errors. Please check below.
                                                        <br><span id="errorlog"></span>
                                                    </div>
                                                  
                                                    <div class="row">  
                                                        <?php if ($Company_id == "") { ?>
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Company Name<span class="required"> * </span></label>
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" disabled="">
                                                                    <option value="">Please Select</option>
                                                                     <?php foreach ($cmp_result as $cmp) { ?>
                                                                        <option value="<?= $cmp->id; ?>"  <?php echo ($result->company_id==$cmp->id ? 'Selected': ''); ?>><?= $cmp->company_name; ?> </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Feedback Set Title<span class="required"> * </span></label>
                                                                <input type="text" name="feedback_name" id="feedback_name" maxlength="255" class="form-control input-sm" value="<?php echo $result->title; ?>" >   
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">    
                                                            <div class="form-group">
                                                                <label>Powered By<span class="required"> * </span></label>
                                                                <input type="text" name="powered_by" id="powered_by" maxlength="255" class="form-control input-sm" value="<?php echo $result->powered_by; ?>" >   
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        
                                                        <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Language<span class="required"> * </span></label>
                                                                        <select id="language_id" name="language_id" class="form-control input-sm select2me" placeholder="Please select" style="width:100%" <?php echo($LockFlag ? 'disabled':''); ?> >
                                                                            <?php if(isset($language_mst)){
                                                                                    foreach ($language_mst as $Row) { ?>
                                                                                        <option value="<?php echo $Row->id ?>" <?php echo ($result->language_id == $Row->id ? 'Selected' : ''); ?> ><?php echo $Row->name ?></option>
                                                                                <?php  }
                                                                            } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Timer (In Sec.)</label>
                                                                <input type="number" name="timer" id="timer" maxlength="255" class="form-control input-sm" value="<?php echo $result->timer ; ?>" <?php echo($LockFlag ? 'disabled':''); ?> >
                                                                <span class="text-muted" style="color:red">(This sets the maximum time in seconds,Zero means no timer)</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group">
                                                                <label>Status<span class="required"> * </span></label>
                                                                <select id="status" name="status" class="form-control input-sm " placeholder="Please select" <?php echo($LockFlag ? 'disabled':''); ?> >
                                                                    <option value="1" <?php echo ($result->status==1)?'selected':'';?>>Active</option>
                                                                    <option value="0" <?php echo ($result->status==0)?'selected':'';?>>In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                              
                                            <fieldset <?php echo($LockFlag ? 'disabled':''); ?>>
                                                <legend>Mapping Type:</legend>
                                                    <?php if(!$LockFlag) { ?>
                                                    <div class="row margin-bottom-10">      
                                                        <div class="col-md-12 text-right">  
                                                            <button type="button" id="confirm" name="confirm" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmType()">
                                                                <span class="ladda-label">Add Feedback Type</span>
                                                            </button>                                               
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    <div class="row">  
                                                        <div class="col-md-12">
                                                            <table class="table table-striped table-bordered table-hover" id="typeDatatable" name="typeDatatable" width="100%">
                                                                <thead>
                                                                    <tr>                                                                        
                                                                        <th width="20%">Type</th>
                                                                        <th width="30%">Subtype</th>
                                                                        <th width="5%"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $EditType = count($TypeSubtypeArray);
                                                                    $key=0;
                                                                    if ($EditType > 0) {
                                                                        foreach ($TypeSubtypeArray as $tr_id) {
                                                                            $key++;
                                                                            ?>
                                                                            <tr id="Row-<?php echo $key; ?>">
                                                                                <td><select id="ftype_id<?php echo $key; ?>" name="New_ftype_id[]" class="form-control input-sm select2 ValueUnq" placeholder="Please select" onchange="getTypewiseSubtype(<?php echo $key; ?>);" style="width:100%" <?php echo($LockFlag ? 'disabled':''); ?>>    
                                                                                        <?php if (count($FTypeResultSet) > 0) { ?>
                                                                                            <?php foreach ($FTypeResultSet as $tr) { ?>
                                                                                                <option value="<?php echo $tr->id; ?>" <?php echo ($tr_id['feedback_type_id'] == $tr->id ? 'Selected' : '') ?>><?php echo $tr->description; ?></option>
                                                                                            <?php }
                                                                                        } ?>
                                                                                    </select> </td>
                                                                                <td>
                                                                                     <input type="hidden" value="<?php echo $key; ?>" name="TotalSubTopic[]">
                                                                                    <select id="subtype<?php echo $key; ?>" name="New_subtype_id<?php echo $key; ?>[]" class="form-control input-sm select2" placeholder="Please select" style="width:100%" multiple="" <?php echo($LockFlag ? 'disabled':''); ?>>    
                                                                                        <?php if (count($tr_id['feedback_subtype_id']) > 0) { ?>
                                                                                        <?php foreach ($tr_id['feedback_subtype_id'] as $sub) { ?>
                                                                                                <option value="<?php echo $sub->id; ?>" <?php echo ($sub->feedback_subtype_id ==$sub->id ? 'Selected' : '') ?>><?php echo $sub->description; ?></option>
                                                                                          <?php }
                                                                                            } ?>
                                                                                    </select></td>
                                                                                <td>
                                                                                    <button type="button" id="remove" name="remove" class="btn btn-danger btn-sm" onclick="RowDelete(<?php echo $key; ?>)"><i class="fa fa-times"></i></button></td>
                                                                            </tr>
                                                                            <?php }
                                                                            } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>                                                      
                                                </div>
                                                </fieldset>        
                                                    <div class="row">                                                      
                                                        <div class="col-md-12 text-right">  
                                                            
                                                            <button type="button" id="feedback-submit" name="feedback-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="SaveFeedbackSet();">
                                                                <span class="ladda-label">Update</span>
                                                            </button>
                                                           
                                                            <a href="<?php echo site_url("feedback_set"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                        </div>
                                                    </div>   
                                                 </div>   
                                                </div> 
                                                </form>
                                                </div>
                                             <div class="tab-pane <?php echo ($step == 2 ? 'active"' : ''); ?>" id="tab_data">                                           
                                             <div class="portlet light bg-inverse">
                                                        <div class="portlet-title">
							<div class="caption font-purple-plum">
								<i class="fa fa-search font-purple-plum"></i>
								<span class="caption-subject bold uppercase"> Advanced Filter</span>
							</div>
						</div>
                                                    <form id="FilterFrm" name="FilterFrm" method="post" action="<?php echo $base_url; ?>feedback_set/export_question/<?php echo base64_encode($result->id); ?>">
                                                        <div class="row">                                                
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                            <label>Feedback Type&nbsp;</label>
                                                                <select id="search_type" name="search_type" class="form-control input-sm select2 filter_list" placeholder="Please select"  style="width: 100%" onchange="getSearchSubType()">
                                                                    <option value="">All Type</option>
                                                                    <?php 
                                                                        foreach ($FType as $tp) { ?>
                                                                        <option value="<?= $tp->id; ?>"><?php echo $tp->description; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                        </div>
                                                            </div>                                                
                                                            <div class="col-md-3">
                                                                 <div class="form-group">
                                                            <label >Sub-Type&nbsp;</label>
                                                                <select id="search_subtype" name="search_subtype" class="form-control input-sm select2 filter_list" placeholder="Please select"  style="width: 100%">
                                                                    <option value="">All Sub-Type</option>
                                                                    
                                                                </select>
                                                                </div>
                                                            </div> 
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label >Question Type&nbsp;</label>
                                                                        <select id="question_type" name="question_type" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%">
                                                                            <option value="">Select</option>
                                                                            <option value="0">Multiple choice</option>
                                                                            <option value="1">Text</option>
                                                                        </select>
                                                                </div>
                                                            </div>
															<div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label >Status&nbsp;</label>
                                                                        <select id="search_status" name="search_status" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%">
                                                                            <option value="">Select</option>
                                                                            <option value="1">Active</option>
                                                                            <option value="2">In-Active</option>
                                                                        </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                        <div class=" col-md-12 text-right">
                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="questionTable()"><i class="fa fa-search "></i>&nbsp;Search</button>
                                                                <button type="submit" class="btn blue-hoki btn-sm" ><i class="fa fa-file-excel-o"></i>&nbsp;Export</button>
                                                                <button type="button" class="btn blue-hoki btn-sm" onclick="ResetFilter()">&nbsp;Reset</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    </div>
                                            <form id="QTableForm" name="QTableForm" method="post">
                                                <?php if(!$LockFlag) { ?>
                                                        <div class="row margin-top-10 ">
                                                             <div class="col-md-12">
                                                            <div class="actions">
                                                                <div class="btn-group pull-right  ">
                                                                    <button type="button" class="btn orange btn-sm btn-outline dropdown-toggle" data-toggle="dropdown">Bulk Actions
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu pull-right" role="menu">
                                                                        <?php if ($acces_management->allow_add OR $acces_management->allow_edit) { ?>
                                                                            <li>
                                                                                <a id="bulk_active" href="javascript:;">
                                                                                    <i class="fa fa-check"></i> Active
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a id="bulk_inactive" href="javascript:;">
                                                                                    <i class="fa fa-close"></i> In Active 
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>

                                                                    </ul>
                                                                </div>
                                                            </div></div>
                                                        </div>
                                                <?php } ?>
                                            <div class="row margin-top-10">
                                                <div class="col-md-12">
                                                <table class="table  table-bordered table-hover table-checkable order-column" id="question_table">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                    <input type="checkbox" class="all group-checkable" name="check" id="check" data-set="#question_table .checkboxes" />
                                                                    <span></span>
                                                                </label>
                                                            </th>
                                                            <th>Question ID.</th>
                                                            <th>Type</th>
                                                            <th>Sub-Type</th>
															<th>Question Type.</th>
                                                            <th>Question</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>

                                                </table>
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
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar');  ?>
            </div>
            <?php //$this->load->view('inc/inc_footer');  ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav'); ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script>
            var TrainerArrray = [];
            var Totaltrainer = <?php echo $key + 1; ?>;
            var Base_url = "<?php echo base_url(); ?>";
            var Encode_id = "<?php echo base64_encode($result->id); ?>";
            var AddEdit='<?php echo($LockFlag ? 'V':'E'); ?>';
            var SelectedArrray = []; 
        </script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/customjs/feedbackset_validation.js"></script>
        <script>
            jQuery(document).ready(function () {
                $('.all').click(function () {
                    if ($(this).is(':checked')) {
                        $("input[name='id[]']").prop('checked', true);
                    } else {
                        $("input[name='id[]']").prop('checked', false);
                    }
                });
                questionTable();
                $("#bulk_active").click(function () {
                    ValidCheckbox(1);
                });
                $("#bulk_inactive").click(function () {
                    ValidCheckbox(2);
                });
            });
        </script>
    </body>
</html>