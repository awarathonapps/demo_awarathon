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
        <!--datattable CSS  Start-->
        <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!--datattable CSS  End-->
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
                                    <span>Workshop</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Question Set</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>questionset" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Edit Question Set
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
                                                <div class="tab-pane <?php echo ($step == 1 ? 'active"' : 'mar'); ?>" id="tab_general">        
                                                    <form id="frmQuestion" name="frmQuestion" method="POST"  action="<?php echo $base_url; ?>questionset/update/<?php echo base64_encode($result->id); ?>">                                             
                                                        <div class="tab-pane active" id="tab_general">    

                                                            <?php
                                                            $errors = validation_errors();
                                                            //echo $errors;
                                                            if ($errors) {
                                                                ?>
                                                                <div style="display: block;" class="alert alert-danger display-hide">
                                                                    <button class="close" data-close="alert"></button>
                                                                    You have some form errors. Please check below.
                                                                    <?php echo $errors; ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            if ($customr_errors != "") {
                                                                ?>
                                                                <div style="display: block;" class="alert alert-danger display-hide">
                                                                    <button class="close" data-close="alert"></button>
                                                                    You have some form errors. Please check below.
                                                                    <?php echo $customr_errors; ?>
                                                                </div>
                                                            <?php } ?>
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
                                                                            <?php foreach ($SelectCompany as $cmp) { ?>
                                                                                <option value="<?= $cmp->id; ?>"  <?php echo ($result->company_id == $cmp->id ? 'Selected' : ''); ?>><?= $cmp->company_name; ?> </option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <?php } ?>
                                                                <div class="col-md-6">    
                                                                    <div class="form-group">
                                                                        <label>Question Set Title<span class="required"> * </span></label>
                                                                        <input type="text" name="feedback_name" id="feedback_name" maxlength="255" class="form-control input-sm" value="<?php echo $result->title; ?>" >   
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Powered By<span class="required"> * </span></label>
                                                                        <input type="text" name="powered_by" id="powered_by" maxlength="255" class="form-control input-sm" value="<?php echo $result->powered_by; ?>" >   
                                                                    </div>
                                                                </div>
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
                                                            </div>                                                    
                                                            <div class="row">
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Reward Multiplier</label>
                                                                        <input type="number" name="reward" id="reward" maxlength="255" class="form-control input-sm" value="<?php echo $result->reward; ?>" <?php echo($basic_lock ? 'disabled':''); ?>>   
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Timer (In Sec.)</label>
                                                                        <input type="number" name="timer" id="timer" maxlength="255" class="form-control input-sm" value="<?php echo $result->timer; ?>" <?php echo($basic_lock ? 'disabled':''); ?>>
                                                                        <span class="text-muted" style="color:red">(This sets the maximum time in seconds,Zero means no timer)</span>
                                                                    </div>
                                                                </div>                               
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Weight</label>
                                                                        <input type="number "name="weight" id="weight" maxlength="255" class="form-control input-sm" value="<?php echo $result->weight; ?>" <?php echo($basic_lock ? 'disabled':''); ?>>   
                                                                    </div>
                                                                </div>                                                    
                                                                <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Status<span class="required"> * </span></label>
                                                                        <select id="status" name="status" class="form-control input-sm " placeholder="Please select" <?php echo($LockFlag ? 'disabled':''); ?>>
                                                                            <option value="1" <?php echo ($result->status == 1) ? 'selected' : ''; ?>>Active</option>
                                                                            <option value="0" <?php echo ($result->status == 0) ? 'selected' : ''; ?>>In-Active</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        
                                            <fieldset <?php echo($LockFlag ? 'disabled':''); ?>>
                                                 <legend>Mapping Topic-Trainer:</legend>
                                                            <?php  if(!$LockFlag) {?>
                                                            <div class="row  margin-bottom-10">      
                                                                <div class="col-md-12 text-right">  
                                                                    <button type="button" id="confirm" name="confirm" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="ConfirmTrainer()">
                                                                        <span class="ladda-label">Add Trainer</span>
                                                                    </button>                                               
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                            <div class="row">  
                                                                <div class="col-md-12">
                                                                    <table class="table table-striped table-bordered table-hover" id="trainerDatatable" width="100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th width="20%">Trainer</th>
                                                                                <th width="20%">Topic</th>
                                                                                <th width="30%">Subtopic</th>
                                                                                <th width="10%">Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            $EditTrainer = count($TrainerArray);
                                                                            $key = 0;
                                                                            if ($EditTrainer > 0) {
                                                                                foreach ($TrainerArray as $tr_id) {
                                                                                    $key++;
                                                                                    ?>
                                                                                    <tr id="Row-<?php echo $key; ?>"><td>
                                                                                            <select id="trainer_id<?php echo $key; ?>" name="New_trainer_id[]" class="form-control input-sm select2" placeholder="Please select" style="width:100%" <?php echo($LockFlag ? 'disabled':''); ?>>    
                                                                                                <?php if (count($TrainerResult) > 0) { ?>
                                                                                                    <?php foreach ($TrainerResult as $tr) { ?>
                                                                                                        <option value="<?php echo $tr->userid; ?>" <?php echo ($tr_id['trainer_id'] == $tr->userid ? 'Selected' : '') ?>><?php echo $tr->username; ?></option>
                                                                                                    <?php
                                                                                                    }
                                                                                                }
                                                                                                ?>
                                                                                            </select> </td>
                                                                                        <td><select id="topic_id<?php echo $key; ?>" name="New_topic_id[]" class="form-control input-sm select2 ValueUnq" placeholder="Please select" onchange="getTopicwiseSubtopic(<?php echo $key; ?>);" style="width:100%" <?php echo($LockFlag ? 'disabled':''); ?>>    
                                                                                                <?php if (count($TopicResultSet) > 0) { ?>
                                                                                                    <?php foreach ($TopicResultSet as $tr) { ?>
                                                                                                        <option value="<?php echo $tr->topic_id; ?>" <?php echo ($tr_id['topic_id'] == $tr->topic_id ? 'Selected' : '') ?>><?php echo $tr->description; ?></option>
                                                                                                    <?php
                                                                                                    }
                                                                                                }
                                                                                                ?>
                                                                                            </select> </td>
                                                                                        <td>
                                                                                            <input type="hidden" value="<?php echo $key; ?>" name="TotalSubTopic[]">
                                                                                            <select id="subtopic<?php echo $key; ?>" name="New_subtopic_id<?php echo $key; ?>[]" class="form-control input-sm select2" placeholder="Please select" style="width:100%" multiple="" <?php echo($LockFlag ? 'disabled':''); ?>>    
                                                                                                <?php if (count($tr_id['subtopicSet']) > 0) { ?>
                                                                                                    <?php foreach ($tr_id['subtopicSet'] as $sub) { ?>
                                                                                                        <option value="<?php echo $sub->id; ?>" <?php echo ($sub->subtopic_id != '' ? 'Selected' : '') ?>><?php echo $sub->description; ?></option>
                                                                                                    <?php
                                                                                                    }
                                                                                                }
                                                                                                ?>
                                                                                            </select></td>
                                                                                        <td>
                                                                                            <button type="button" id="remove" name="remove" class="btn btn-danger btn-sm" onclick="RowDelete(<?php echo $key; ?>)"><i class="fa fa-times"></i></button></td>
                                                                                    </tr>
                                                                                <?php
//                                                                                     echo '<script>$( "#subtopic'.$key.'" ).rules( "add", {
//                                                                                        required: true
//                                                                                    });</script>';
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                
                                                            </div>
                                                    </fieldset>
                                                            <div class="row">      
                                                                <div class="col-md-12 text-right">
                                                                    
                                                                    <button type="button" id="feedback-submit" name="questionset-submit" data-loading-text="Please wait..." 
                                                                            class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left"
                                                                            onclick="SaveQuestionSet('E');">
                                                                        <span class="ladda-label">Update</span>
                                                                    </button>
                                                                    
                                                                    <a href="<?php echo site_url("questionset"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
                                                        <form id="FilterFrm" name="FilterFrm" method="post" action="<?php echo $base_url; ?>questionset/export_question/<?php echo base64_encode($result->id); ?>">
                                                        <div class="row">                                                
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label >Topic&nbsp;</label>
                                                                        <select id="search_topic" name="search_topic" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%" onchange="getSubtopic()">
                                                                            <option value="">All Topic</option>
                                                                        <?php foreach ($SelectedTopic as $tp) { ?>
                                                                                <option value="<?= $tp->topic_id; ?>"><?php echo $tp->description; ?></option>
                                                                        <?php } ?>
                                                                        </select>
                                                                </div>
                                                            </div>                                                
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label >Sub-Topic&nbsp;</label>
                                                                        <select id="search_subtopic" name="search_subtopic" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%">
                                                                            <option value="">All Sub-Topic</option>
                                                                        </select>
                                                                </div>
                                                            </div>                                                    
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label >Trainer&nbsp;</label>
                                                                        <select id="search_trainer" name="search_trainer" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%" >
                                                                            <option value="">All Topic</option>
                                                                        <?php foreach ($SelectedTrainer as $tp) { ?>
                                                                                <option value="<?= $tp->trainer_id; ?>"><?php echo $tp->trainer_name; ?></option>
                                                                        <?php } ?>
                                                                        </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label>Status&nbsp;</label>
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
                                                        <div class="row margin-top-10 ">
                                                            <div class="col-md-12">
                                                                <table class="table table-bordered table-hover table-checkable order-column" id="question_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>
                                                                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                                    <input type="checkbox" class="all group-checkable" name="check" id="check" data-set="#question_table .checkboxes" />
                                                                                    <span></span>
                                                                                </label>
                                                                            </th>
                                                                            <th>Question ID.</th>
                                                                            <th>Trainer</th>
                                                                            <th>Topic</th>
                                                                            <th>Sub-Topic</th>
                                                                            <th>Question</th>
                                                                            <th>Answer</th>
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
        <?php //$this->load->view('inc/inc_quick_sidebar');  ?>
            </div>
<?php //$this->load->view('inc/inc_footer');     ?>
        </div>
<?php //$this->load->view('inc/inc_quick_nav');    ?>
<?php $this->load->view('inc/inc_footer_script'); ?>
        <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <!--<script src="< ?php echo $asset_url; ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>-->
        <script>
            var TrainerArrray = [];
            var Totaltrainer = <?php echo $key + 1; ?>;
            var Base_url = "<?php echo base_url(); ?>";
            var Encode_id = "<?php echo base64_encode($result->id); ?>";
            var AddEdit='<?php echo($LockFlag ? 'V':'E'); ?>';
        </script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/customjs/questionset_validation.js"></script>
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