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
                                            View Feedback Set
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body"> 
                                        <div class="tabbable-line tabbable-full-width">
                                            <ul class="nav nav-tabs" id="tabs">
                                                <li class="active">
                                                    <a href="#tab_general" data-toggle="tab">General</a>
                                                </li>
                                                <li>
                                                    <a href="#tab_data" data-toggle="tab">Manage Questions</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_general">
                                                    <form id="frmFeedbackSet" name="frmFeedbackSet" method="POST"  action="<?php echo $base_url; ?>feedback_set/update/<?php echo base64_encode($result->id); ?>"> 
                                                        <div class="tab-content">
                                                            <div class="tab-pane active" id="tab_overview">    
                                                                <div id="errordiv" class="alert alert-danger display-hide">
                                                                    <button class="close" data-close="alert"></button>
                                                                    You have some form errors. Please check below.
                                                                    <br><span id="errorlog"></span>
                                                                </div>    
                                                                <div class="row">                                                     
                                                                    <div class="col-md-6">    
                                                                        <div class="form-group">
                                                                            <label>Feedback Set Title<span class="required"> * </span></label>
                                                                            <input type="text" name="feedback_name" id="feedback_name" maxlength="255" class="form-control input-sm" value="<?php echo $result->title; ?>" disabled="" >   
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">    
                                                                        <div class="form-group">
                                                                            <label>Powered By<span class="required"> * </span></label>
                                                                            <input type="text" name="powered_by" id="powered_by" maxlength="255" class="form-control input-sm" value="<?php echo $result->powered_by; ?>" disabled="">   
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-3">    
                                                                    <div class="form-group">
                                                                        <label>Language<span class="required"> * </span></label>
                                                                        <select id="language_id" name="language_id" class="form-control input-sm " placeholder="Please select" disabled >
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
                                                                <input type="number" name="timer" id="timer" maxlength="255" class="form-control input-sm" value="<?php echo $result->timer; ?>" disabled="">   
                                                            </div>
                                                        </div>
                                                                    <div class="col-md-3">    
                                                                        <div class="form-group">
                                                                            <label>Status<span class="required"> * </span></label>
                                                                            <select id="status" name="status" class="form-control input-sm " disabled="" >
                                                                                <option value="1" <?php echo ($result->status == 1) ? 'selected' : ''; ?>>Active</option>
                                                                                <option value="0" <?php echo ($result->status == 0) ? 'selected' : ''; ?>>In-Active</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
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
                                                                                $key = 0;
                                                                                if ($EditType > 0) {
                                                                                    foreach ($TypeSubtypeArray as $tr_id) {
                                                                                        $key++;
                                                                                        ?>
                                                                                        <tr id="Row-<?php echo $key; ?>">
                                                                                            <td><select id="ftype_id<?php echo $key; ?>" name="New_ftype_id[]" class="form-control input-sm select2" placeholder="Please select" disabled="" style="width:100%">    
                                                                                                    <?php if (count($FTypeResultSet) > 0) { ?>
                                                                                                        <?php foreach ($FTypeResultSet as $tr) { ?>
                                                                                                            <option value="<?php echo $tr->id; ?>" <?php echo ($tr_id['feedback_type_id'] == $tr->id ? 'Selected' : '') ?>><?php echo $tr->description; ?></option>
                                                                                                            <?php
                                                                                                        }
                                                                                                    }
                                                                                                    ?>
                                                                                                </select> </td>
                                                                                            <td>
                                                                                                <input type="hidden" value="<?php echo $key; ?>" name="TotalSubTopic[]">
                                                                                                <select id="subtype<?php echo $key; ?>" name="New_subtype_id<?php echo $key; ?>[]" class="form-control input-sm select2" placeholder="Please select" style="width:100%" disabled="" multiple="">    
                                                                                                    <?php if (count($tr_id['feedback_subtype_id']) > 0) { ?>
                                                                                                        <?php foreach ($tr_id['feedback_subtype_id'] as $sub) { ?>
                                                                                                            <option value="<?php echo $sub->id; ?>" <?php echo ($sub->feedback_subtype_id == $sub->id ? 'Selected' : '') ?>><?php echo $sub->description; ?></option>
                                                                                                            <?php
                                                                                                        }
                                                                                                    }
                                                                                                    ?>
                                                                                                </select></td>
                                                                                                <td></td>
                                                                                        </tr>
                                                                                        <?php
                                                                                    }
                                                                                }
                                                                                ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>                                                      
                                                                    <div class="row">      
                                                                    </div>                                                      
                                                                    <div class="col-md-12 text-right">  
                                                                        <a href="<?php echo site_url("feedback_set"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                                    </div>
                                                                </div>
                                                            </div>   
                                                        </div> 
                                                    </form>
                                                </div>
                                                <div class="tab-pane" id="tab_data">                                          
                                                    <div class="portlet light bg-inverse">
                                                        <div class="portlet-title">
                                                            <div class="caption font-purple-plum">
                                                                <i class="fa fa-search font-purple-plum"></i>
                                                                <span class="caption-subject bold uppercase"> Advanced Filter</span>
                                                            </div>
                                                        </div>
                                                        <form id="FilterFrm" name="FilterFrm" method="post">
                                                            <div class="row">                                                
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-md-3">Feedback Type&nbsp;</label>
                                                                        <div class="col-md-9" style="padding:0px;">
                                                                            <select id="search_type" name="search_type" class="form-control input-sm select2 filter_list" placeholder="Please select"  style="width: 100%" onchange="getSearchSubType()">
                                                                                <option value="">All Type</option>
                                                                                <?php foreach ($FType as $tp) { ?>
                                                                                    <option value="<?= $tp->id; ?>"><?php echo $tp->description; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>                                                
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-md-3">Sub-Type&nbsp;</label>
                                                                        <div class="col-md-9" style="padding:0px;">
                                                                            <select id="search_subtype" name="search_subtype" class="form-control input-sm select2 filter_list" placeholder="Please select"  style="width: 100%">
                                                                                <option value="">All Sub-Type</option>

                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>                                                    
                                                            </div>
                                                            <div class="clearfix margin-top-20"></div>
                                                            <div class="row">                                                
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-md-3">Status&nbsp;</label>
                                                                        <div class="col-md-9" style="padding:0px;">
                                                                            <select id="search_status" name="search_status" class="form-control input-sm select2" placeholder="Please select"  style="width: 100%">
                                                                                <option value="">Select</option>
                                                                                <option value="1">Active</option>
                                                                                <option value="2">In-Active</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class=" col-md-12 text-right">
                                                                        <button type="button" class="btn btn-orange  btn-sm" onclick="questionTable()">Search</button>
                                                                        <button type="button" class="btn btn-orange  btn-sm" onclick="ResetFilter()">Reset</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <form id="QTableForm" name="QTableForm" method="post">
                                                        <div class="row margin-top-10">
                                                            <div class="col-md-12">
                                                                <table class="table  table-bordered table-hover table-checkable order-column" id="question_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>
                                                                               
                                                                            </th>
                                                                            <th>Sr No.</th>
                                                                            <th>Type</th>
                                                                            <th>Sub-Type</th>
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
        <?php //$this->load->view('inc/inc_quick_sidebar');   ?>
    </div>
    <?php //$this->load->view('inc/inc_footer');  ?>
</div>
<?php //$this->load->view('inc/inc_quick_nav');   ?>
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
    var AddEdit = 'V';
    var SelectedArrray = [];
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/customjs/feedbackset_validation.js"></script>
<script>
    jQuery(document).ready(function () {
        questionTable();
    });
</script>
</body>
</html>