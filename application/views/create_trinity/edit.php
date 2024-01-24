<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">

<head>
    <style>
        /* .parent-div-container {
            max-width: 200px !important;
            min-width: 150px !important;
        } */

        .checked {
            color: orange;
        }

        #question_table,
        #rating_table {
            display: block;
            max-height: 350px;
            overflow-y: auto;
            table-layout: fixed;
        }

        .cust_container {
            overflow: hidden;
            width: 100%;
        }

        .left-col {
            padding-bottom: 500em;
            margin-bottom: -500em;
        }

        .right-col {
            margin-right: -1px;
            /* Thank you IE */
            padding-bottom: 500em;
            margin-bottom: -500em;
            background-color: #FFF;
        }

        .map-title {
            font-size: 16px;
            font-weight: 600;
        }

        .ps-container {
            display: inline-flex;
            width: 100%;
        }

        .ps-seperator {
            color: black;
            float: left;
            width: 1%;
            margin: 0.90px 0.25px 0.25px 0.25px;
        }

        .parameter-badge {
            color: black;
            float: left;
            width: 48%;
            border: 1px solid #aee5ea;
            padding: 1px 10px 1px 10px;
            background: #dbfcff;
            /* border-radius: 25px !important; */
            margin: 0.90px 0.25px 0.25px 0.25px;
        }

        .sub-parameter-badge {
            color: black;
            float: left;
            width: 48%;
            border: 1px solid #ffc9bc;
            padding: 1px 10px 1px 10px;
            background: #ffefeb;
            /* border-radius: 25px !important; */
            margin: 0.90px 0.25px 0.25px 0.25px;
        }

        .parameter-weight-badge {
            color: black;
            float: left;
            width: 20%;
            border: 1px solid #fff7bc;
            padding: 1px 10px 1px 10px;
            background: #feffef;
            /* border-radius: 25px !important; */
            margin: 0.90px 0.25px 0.25px 0.25px;
        }

        .keysent-badge {
            color: black;
            float: left;
            width: 48%;
            border: 1px solid #c5c5c5;
            padding: 1px 10px 1px 10px;
            background: #f9f9f9;
            /* border-radius: 25px !important; */
            margin: 0.90px 0.25px 0.25px 0.25px;
        }

        .custom-div{
            border: 1px solid #c2cad8;
            padding: 5px 10px;
        }
    </style>

    <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $asset_url; ?>assets/global/css/star-rating.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />
    <link href="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
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
                                <i class="fa fa-circle"></i>
                                <span>Assessment</span>
                            </li>
                            <li>
                                <i class="fa fa-circle"></i>
                                <span>Video Q/A</span>
                            </li>
                        </ul>
                        <div class="page-toolbar">
                            <a href="<?php echo $base_url ?>create_trinity" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                        Edit Assessment
                                        <div class="tools"> </div>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="tabbable-line tabbable-full-width">
                                        <ul class="nav nav-tabs" id="tabs">
                                            <li <?php echo ($step == 1 ? 'class="active"' : ''); ?>>
                                                <a href="#tab_overview" data-toggle="tab">Overview</a>
                                            </li>
                                            <!-- new tab -->
                                            <li class="tinity-content <?php echo ($step == 1.2 ? "active" : ''); ?>">
                                                <a href="#tab_map_persona" data-toggle="tab">Map Persona</a>
                                            </li>
                                            <li <?php echo ($step == 1.3 ? 'class="active"' : ''); ?>>
                                                <a href="#tab_mapping_script" data-toggle="tab">Mapping of Script</a>
                                            </li>
                                            <!-- <li class="tinity-content <?php echo ($step == 1.4 ? "active" : ''); ?>">
                                                <a href="#tab_mapping_goal" data-toggle="tab">Map a Goal</a>
                                            </li> -->
                                            <!-- New tab -->
                                            <li <?php echo ($step == 2 ? 'class="active"' : ''); ?>>
                                                <a href="#tab_mapping_manager" data-toggle="tab">Mapping Manager</a>
                                            </li>
                                            <li <?php echo ($step == 3 ? 'class="active"' : ''); ?>>
                                                <a href="#tab_allowed_user" data-toggle="tab">Mapping rep</a>
                                            </li>
                                            <li>
                                                <a href="#tab_user_mapping" data-toggle="tab">Rep Manager mapping</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane <?php echo ($step == 1 ? 'active"' : 'mar'); ?>" id="tab_overview">
                                                <form id="AssessmentForm" name="AssessmentForm" method="POST">
                                                    <div class="tab-pane active" id="tab_overview">
                                                        <?php
                                                        $errors = validation_errors();
                                                        if ($errors) {
                                                        ?>
                                                            <div style="display: block;" class="alert alert-danger display-hide">
                                                                <button class="close" data-close="alert"></button>
                                                                You have some form errors. Please check below.
                                                                <?php echo $errors; ?>
                                                            </div>
                                                        <?php
                                                        } ?>
                                                        <div id="errordiv" class="alert alert-danger display-hide">
                                                            <button class="close" data-close="alert"></button>
                                                            You have some form errors. Please check below.
                                                            <br><span id="errorlog"></span>
                                                        </div>
                                                        <fieldset>
                                                            <legend>General Information:</legend>
                                                            <div>
                                                                <?php if ($Company_id == "") { ?>
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label class="">Company Name<span class="required"> * </span></label>
                                                                                <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%" disabled="">
                                                                                    <option value="">Please Select</option>
                                                                                    <?php foreach ($cmp_result as $cmp) { ?>
                                                                                        <option value="<?= $cmp->id; ?>" <?php echo ($result->company_id == $cmp->id ? 'Selected' : ''); ?>><?= $cmp->company_name; ?> </option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                                <div class="row">
                                                                    <!-- <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label>One Time Code (OTC)</label>
                                                                            <input type="text" name="otc" value="<?php echo $result->code; ?>" id="otc" maxlength="6" class="form-control input-sm uppercase">
                                                                        </div>
                                                                    </div> -->
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Assessment Name<span class="required"> * </span></label>
                                                                            <input type="text" name="assessment_name" id="assessment_name" maxlength="72" class="form-control input-sm" value="<?php echo $result->assessment; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>Report Type<span class="required"> * </span></label>
                                                                            <select id="report_type" name="report_type" class="form-control input-sm select2" placeholder="Please select" <?php echo (count($question_play_array) > 0 ? 'disabled' : ''); ?>>
                                                                                <option value="">Please Select</option>
                                                                                <?php foreach ($report_type as $rt) { ?>
                                                                                    <option value="<?= $rt->id; ?>" <?php echo ($result->report_type == $rt->id ? 'selected' : '') ?>><?php echo $rt->description; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label class="control-label">Start Date<span class="required"> * </span></label>
                                                                            <div class="input-group date form_datetime">
                                                                                <input type="text" size="16" class="form-control" name="start_date" id="start_date" autocomplete="off" value="<?php echo date("d-m-Y H:i", strtotime($result->start_dttm)) ?>" <?php echo ($disabledflag ? 'disabled' : ''); ?>>
                                                                                <span class="input-group-btn">
                                                                                    <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label class="control-label">End Date<span class="required"> * </span></label>
                                                                            <div class="input-group date form_datetime">
                                                                                <input type="text" size="16" class="form-control" name="end_date" id="end_date" autocomplete="off" value="<?php echo date("d-m-Y H:i", strtotime($result->end_dttm)) ?>">
                                                                                <span class="input-group-btn">
                                                                                    <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>Time Limit<span class="required"> * </span></label>
                                                                            <input type="number" name="time_limit" id="time_limit" min="1" max="300" class="form-control input-sm" value="<?php echo $result->time_limit; ?>">
                                                                            <span class="text-muted" style="color:red" id="file_desc">(Time limit cannot exceed 300 seconds.)</span>
                                                                        </div>
                                                                    </div>
                                                                    <!-- <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label class="control-label">Last Assessor Date<span class="required"> * </span></label>
                                                                            <div class="input-group date form_datetime">
                                                                                <input type="text" size="16" class="form-control" name="assessor_date" id="assessor_date" autocomplete="off" value="<?php echo ($result->assessor_dttm != '0000-00-00 00:00:00' ? date("d-m-Y H:i", strtotime($result->assessor_dttm)) : '') ?>">
                                                                                <span class="input-group-btn">
                                                                                    <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div> -->
                                                                </div>
                                                                <div class="row">
                                                                    <!-- <div class="col-md-3">    
                                                                        <div class="form-group">
                                                                            <label>Rating Type<span class="required"> * </span></label>
                                                                            <select id="ratingstyle" name="ratingstyle" class="form-control input-sm select2 " placeholder="Please select" < ?php echo (count($question_play_array)>0 ? 'disabled':''); ?>>
                                                                                <option value="1" < ?php echo ($result->ratingstyle==1)?'selected':'';?>>Star Rating</option>
                                                                                <option value="2" < ?php echo ($result->ratingstyle==2)?'selected':'';?>>Slider</option>
                                                                            </select>
                                                                        </div>
                                                                    </div> -->
                                                                    <input type="hidden" name="ratingstyle" id="ratingstyle" class="form-control input-sm" value="<?php echo ($result->ratingstyle != '' ? $result->ratingstyle : '2') ?>">
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>Status<span class="required"> * </span></label>
                                                                            <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select">
                                                                                <option value="1" <?php echo ($result->status == 1) ? 'selected' : ''; ?>>Active</option>
                                                                                <option value="0" <?php echo ($result->status == 0) ? 'selected' : ''; ?>>In-Active</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>Number of attempts<span class="required"> * </span></label>
                                                                            <input type="number" name="number_attempts" id="number_attempts" min="1" class="form-control input-sm" value="<?php echo $result->number_attempts ?>">
                                                                        </div>
                                                                    </div>

                                                                    <!---Added-->

                                                                    <!-- <div class="col-md-3" style="margin-top: 25px;">    
                                                                        <div class="form-group">
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="is_preview"> Is preview?
                                                                                <input id="is_preview" name="is_preview" type="checkbox" value="1"  <?php echo ($result->is_preview == 1) ? 'checked' : ''; ?>><span></span>
                                                                            </label>
                                                                        </div>
                                                                    </div> -->

                                                                    <!-- <div class="col-md-1" style="margin-top: 25px;">    
                                                                        <div class="form-group">
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="ranking"> ranking
                                                                                <input id="ranking" name="ranking" type="checkbox" value="1"  <?php echo ($result->ranking == 1) ? 'checked' : ''; ?>><span></span>
                                                                            </label>
                                                                        </div>
                                                                    </div> -->
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Description</label>
                                                                            <textarea type="text" name="description" id="description" cols="3" rows="3" class="form-control input-sm"><?php echo $result->description ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Instruction<span class="required"> * </span></label>
                                                                            <textarea type="text" name="instruction" id="instruction" cols="3" rows="2" class="form-control input-sm"><?php echo $result->instruction ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                        </fieldset>

                                                        <div class="row">
                                                            <div class="col-md-12 text-right">
                                                                <button type="button" id="feedback-submit" name="questionset-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="UpdateAssessment();">
                                                                    <span class="ladda-label">Update</span>
                                                                </button>
                                                                <a href="<?php echo site_url("create_trinity"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="tab-pane  <?php echo ($step == 1.2 ? 'active' : ''); ?>" id="tab_map_persona">
                                                <!-- Map Persona Content -->
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label>Trinity Persona<span class="required"> * </span></label>
                                                            <select id="persona_option" name="persona_option" class="form-control input-sm select2" placeholder="Please select">\
                                                                <option value="">Please Select Persona</option>
                                                                <option value="0">Create new Persona</option>
                                                                <?php foreach ($persona as $per) { ?>
                                                                    <option value="<?= $per->id; ?>" <?php echo (!empty($persona_result) && $persona_result[0]->id == $per->id ? 'selected' : '') ?>><?php echo $per->persona_name; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <!-- image Upload -->
                                                        <div id="Emptytext" class="form-group" style="display:none;">
                                                            <label><span id="label_dyamic">Persona Name</span><span class="required"> * </span></label>
                                                            <input type="text" name="persona_name" id="persona_name" style="max-width: 750px;" class="form-control input-sm">
                                                            <span class="text-muted" style="color:red" id="file_desc">(Allowed: Extensions: .png , .gif, .jpg, .jpeg. Max-Width:750px, Max-Height:400px)</span>
                                                        </div>
                                                        <div id="EmptyImage" class="form-group" style="display:none;">
                                                            <input type="file" id="myFileInput" name="myFileInput" style="display:none;" accept="image/png, image/gif, image/jpeg" />
                                                            <div class="form-control fileinput fileinput-exists" style="border: none;height:auto;padding:0" data-provides="fileinput" onclick="document.getElementById('myFileInput').click()">
                                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 750px; max-height: 400px;">
                                                                    <img id="EmptyImg" src="<?php echo base_url() . 'assets/uploads/no_image.png'; ?>" width="250" height="200" alt="No Image" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Image Upload -->

                                                        <!-- Image Show -->
                                                        <div id="queImage" class="form-group" style="display:none;">
                                                            <div class="form-control fileinput fileinput-exists" style="border: none;height:auto;padding:0" data-provides="fileinput" onclick="document.getElementById('myFileInputv').click()">
                                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 750px; max-height: 400px;">
                                                                    <img id="question_preview" src="" width="250" height="200" alt="Persona Image" />
                                                                </div>
                                                            </div>
                                                            <span class="text-muted" style="color:red" id="file_desc1"></span>
                                                        </div>
                                                        <!-- Image Show -->
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 text-right">
                                                        <button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="map_persona();">
                                                            <span class="ladda-label">Update & Next</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- map persona content end here -->
                                            </div>
                                            <!-- Mapping Script -->
                                            <div class="tab-pane <?php echo ($step == 1.3 ? 'active' : ''); ?>" id="tab_mapping_script">

                                                <form id="MappingScript" name="MappingScript" method="POST">
                                                <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id;  ?>">
                                                    <?php
                                                    $errors = validation_errors();
                                                    if ($errors) {
                                                    ?>
                                                        <div style="display: block;" class="alert alert-danger display-hide">
                                                            <button class="close" data-close="alert"></button>
                                                            You have some form errors. Please check below.
                                                            <?php echo $errors; ?>
                                                        </div>
                                                    <?php
                                                    } ?>
                                                    <div id="ErorDiv" class="alert alert-danger display-hide">
                                                        <button class="close" data-close="alert"></button>
                                                        You have some form errors. Please check below.
                                                        <br><span id="Errorlog"></span>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Select Script<span class="required"> * </span></label>
                                                                <select name="script" id="script" class="form-control input-sm select2" onchange="script_based_situation()">
                                                                    <option value="">Please select</option>
                                                                    <?php if (count((array)$map_script) > 0) { ?>
                                                                        <?php foreach ($map_script as $ms) { ?>
                                                                            <option value="<?php echo $ms->id ?> " <?php echo (!empty($mapping_script) && $mapping_script[0]->script_id == $ms->id ? 'selected' : '') ?>><?php echo $ms->script_title ?></option>
                                                                    <?php }
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="row" id="sit-limit" style="display:none;">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Situation</label>
                                                                <!-- <?php if (count((array)$ass_situation) > 0) { ?>
                                                                    <textarea class="form-control input-sm" id="situation" name="situation" rows="7" cols="70" disabled =''><?php echo $ass_situation[0]->situation; ?></textarea>
                                                                <?php } else { ?>
                                                                    <textarea class="form-control input-sm" id="situation" name="situation" rows="7" cols="70"></textarea>
                                                                <?php } ?> -->
                                                                <div id="situation" name="situation" class="custom-div">
                                                                    <?php
                                                                    if(!empty($ass_situation)){
                                                                        echo $ass_situation[0]->situation;
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Map a non verbal parameters</label>
                                                                <select name="parameter_id[]" id="parameter_id" class="form-control myselect2" placeholder="Please select" multiple="">
                                                                    <?php if (count((array)$mapping_goal_parameter) > 0) { ?>
                                                                        <?php foreach ($mapping_goal_parameter as $mgp) { ?>
                                                                            <option value="<?php echo $mgp->id ?>" <?php if (isset($assessment_level_goal[0]->parameter_id)) {
                                                                                                                        echo (in_array($mgp->id, explode(',', $assessment_level_goal[0]->parameter_id)) ? 'selected' : '');
                                                                                                                    } ?>><?php echo $mgp->description ?></option>
                                                                    <?php }
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row" id="QTable" style="display:none; overflow-x: scroll;overflow-y: hidden;">
                                                        <div class="col-md-12" style="width:100%;">
                                                            <table class="table table-bordered table-hover" id="VQADatatable" name="VQADatatable" style="width:100%;min-width:1200px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th width="140px" id="label_dyamic">Questions</th>
                                                                        <th width="140px" id="label_dyamic">Answers</th>
                                                                        <th width="80px">Language</th>
                                                                        <th width="130px">Manage Goal</th>
                                                                        <th width="120px">Manage Question/Answer</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="QTable-body"></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 text-right" style="display:none;" id="add_weight">
                                                            <button type="button" id="add_parameter_weight" name="add_parameter_weight" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" onclick="add_weight()" <?php echo ($lockQue ? 'disabled' : ''); ?>>
                                                            <!-- <button type="button" id="add_parameter_weight" name="add_parameter_weight" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-target="#parameter_weight_modal" data-toggle="modal" data-style="expand-left" onclick="add_weight()"> -->
                                                                <span class="ladda-label">Map Questions</span>
                                                                <!-- <span class="ladda-label">Add Parameter Weight</span> -->
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <br>

                                                    <!-- KRISHNA --- HIDE WEIGHT MODULE -->
                                                    <!-- <div class="row margin-top-10">
                                                        <div class="col-lg-12" id="para_weight_table"> </div>
                                                    </div> -->

                                                    <div class="modal fade" id="LoadModalFilter-view" role="basic" aria-hidden="true" data-width="400">
                                                        <div class="modal-dialog modal-lg" style="width:1024px;">
                                                            <div class="modal-content">
                                                                <div class="modal-body" id="modal-body">
                                                                    <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                                                                    <span>
                                                                        &nbsp;&nbsp;Loading... </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- datatable reload -->
                                                    <!-- <div class="row">
                                                        <div class="col-md-12 text-right">
                                                            <button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="map_script();">
                                                                <span class="ladda-label">Update & Next</span>
                                                            </button>
                                                            <a href="<?php echo site_url("create_trinity"); ?>" class="btn btn-default btn-cons">Cancel</a>
                                                        </div>
                                                    </div> -->
                                                </form>
                                            </div>
                                            <!-- Mapping Script -->

                                            <div class="tab-pane <?php echo ($step == 3 ? 'active"' : ''); ?>" id="tab_allowed_user">
                                                <form role="form" id="ParticipantForm" name="ParticipantForm">
                                                    <div class="form-body">
                                                        <div class="row margin-bottom-10">
                                                            <div class="col-md-12 text-right"> <button type="button" id="send_notification2" name="send_notification2" data-loading-text="Please wait..." class="btn orange btn-sm btn-outline" data-style="expand-right" onclick="notification_send(2);" style="margin-right: 10px;">
                                                                    <span class="ladda-label"><i class="fa fa-envelope"></i>&nbsp; Send Notification</span>
                                                                </button>&nbsp;
                                                                <button type="button" id="custom_remove1" name="custom_remove1" data-loading-text="Please wait..." accesskey="" class="btn orange btn-sm btn-outline" data-style="expand-right" onclick="RemoveAllParticipant();" style="  margin-right: 10px;">
                                                                    <span class="ladda-label"><i class="fa fa-remove"></i>&nbsp; Remove</span>
                                                                </button>&nbsp;
                                                                <a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'create_trinity/importTrainee/' . base64_encode($result->id); ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-file-excel-o"></i>&nbsp;Import </a>
                                                                &nbsp;

                                                                <a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'create_trinity/addParticipant/' . base64_encode($result->id); ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus"></i>&nbsp;Add Participant User </a>
                                                            </div>
                                                        </div>
                                                        <div class="row ">
                                                            <div class="col-md-12" id="assessment_panel">
                                                                <table class="table  table-bordered table-hover table-checkable order-column" id="UsersTable">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>
                                                                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                                    <input type="checkbox" class="Participant_all group-checkable chk_tr" name="chk_tr" id="chk_tr" data-set="#UsersTable .checkboxes" />
                                                                                    <span></span>
                                                                                </label>
                                                                            </th>
                                                                            <th>User ID</th>
                                                                            <th>Name</th>
                                                                            <th>Email</th>
                                                                            <th>Mobile No</th>
                                                                            <th>Trainee Region</th>
                                                                            <th>Area</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody></tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane <?php echo ($step == 2 ? 'active"' : ''); ?>" id="tab_mapping_manager">
                                                <form role="form" id="MappingForm" name="MappingForm">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">Last Assessor Date<span class="required"> * </span></label>
                                                                <div class="input-group date form_datetime">
                                                                    <input type="text" size="16" class="form-control" name="assessor_date" id="assessor_date" autocomplete="off" value="<?php echo ($result->assessor_dttm != '0000-00-00 00:00:00' ? date("d-m-Y H:i", strtotime($result->assessor_dttm)) : '') ?>">
                                                                    <span class="input-group-btn">
                                                                        <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-body">
                                                        <div class="row margin-bottom-10">
                                                            <span class="col-md-2 map-title">
                                                                Mapping Manager
                                                            </span>
                                                            <div class="col-md-10 text-right">
                                                                <button type="button" id="send_notification" name="send_notification" data-loading-text="Please wait..." class="btn orange btn-sm btn-outline" data-style="expand-right" onclick="notification_send(1);" style="margin-right: 10px;">
                                                                    <span class="ladda-label"><i class="fa fa-envelope"></i>&nbsp; Send Notification</span>
                                                                </button>&nbsp;
                                                                <button type="button" id="custom_remove2" name="custom_remove2" data-loading-text="Please wait..." accesskey="" class="btn orange btn-sm btn-outline" data-style="expand-right" onclick="RemoveAllMappingManagers();" style="  margin-right: 10px;">
                                                                    <span class="ladda-label"><i class="fa fa-remove"></i>&nbsp; Remove</span>
                                                                </button>&nbsp;
                                                                <a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'create_trinity/importManager/' . base64_encode($result->id); ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-file-excel-o"></i>&nbsp;Import </a>
                                                                &nbsp;

                                                                <a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'create_trinity/addManagers/' . base64_encode($result->id); ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus"></i>&nbsp;Add Manager </a>
                                                            </div>
                                                        </div>
                                                        <div class="row ">
                                                            <div class="col-md-12" id="assessment_panel">
                                                                <table class="table  table-bordered table-hover table-checkable order-column" id="ManagersTable">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>
                                                                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                                    <input type="checkbox" class="Mapping_all group-checkable chk_mg" name="chk_mg" id="chk_mg" data-set="#ManagersTable .checkboxes" />
                                                                                    <span></span>
                                                                                </label>
                                                                            </th>
                                                                            <th>Trainer ID</th>
                                                                            <th>Trainer Region</th>
                                                                            <th>Username</th>
                                                                            <th>Name</th>
                                                                            <th>Email</th>
                                                                            <th>Designation</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody></tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="row margin-bottom-20"> </div>
                                                <form role="form" id="MappingSuperForm" name="MappingSuperForm">
                                                    <div class="form-body">
                                                        <div class="row margin-bottom-10">
                                                            <span class="col-md-3 map-title">
                                                                Mapping Supervisor
                                                            </span>
                                                            <div class="col-md-9 text-right">
                                                                <button type="button" id="custom_remove3" name="custom_remove3" data-loading-text="Please wait..." accesskey="" class="btn orange btn-sm btn-outline" data-style="expand-right" onclick="RemoveAllMappingSupervisors();" style="  margin-right: 10px;">
                                                                    <span class="ladda-label"><i class="fa fa-remove"></i>&nbsp; Remove</span>
                                                                </button>&nbsp;
                                                                <a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'create_trinity/importSupervisor/' . base64_encode($result->id); ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-file-excel-o"></i>&nbsp;Import </a>
                                                                &nbsp;

                                                                <a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'create_trinity/addSupervisors/' . base64_encode($result->id); ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus"></i>&nbsp;Add Supervisor </a>
                                                            </div>
                                                        </div>
                                                        <div class="row ">
                                                            <div class="col-md-12" id="assessment_panel">
                                                                <table class="table  table-bordered table-hover table-checkable order-column" id="SupervisorTable">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>
                                                                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                                    <input type="checkbox" class="Mappsuper_all group-checkable chk_sp" name="chk_sp" id="chk_sp" data-set="#SupervisorTable .checkboxes" />
                                                                                    <span></span>
                                                                                </label>
                                                                            </th>
                                                                            <th>Trainer ID</th>
                                                                            <th>Username</th>
                                                                            <th>Name</th>
                                                                            <th>Email</th>
                                                                            <th>Designation</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody></tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="tab-pane" id="tab_user_mapping">
                                                <form role="form" id="UserMappingForm" name="UserMappingForm">
                                                    <div class="form-body">
                                                        <div class="row margin-bottom-10">
                                                            <div class="col-md-12 text-right">
                                                                <a class="btn btn-orange btn-sm" id="btnaddpanel3" onclick="RemoveUserMappingPopup()"><i class="fa fa-minus"></i>&nbsp;Remove </a>
                                                                &nbsp;
                                                                <a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'create_trinity/addUserManagers/' . base64_encode($result->id); ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-plus"></i>&nbsp;Add Assessor </a>
                                                                <a class="btn btn-orange btn-sm" id="btnaddpanel3" href="<?php echo base_url() . 'create_trinity/import_user_manager/' . base64_encode($result->id); ?>" data-target="#LoadModalFilter" data-toggle="modal"><i class="fa fa-file-excel-o"></i>&nbsp;Import </a>
                                                            </div>
                                                        </div>
                                                        <div class="row ">
                                                            <div class="col-md-12" id="assessment_panel">
                                                                <table class="table  table-bordered table-hover table-checkable order-column" id="UserManagersTable">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>
                                                                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                                                    <input type="checkbox" class="UserMapping_all group-checkable chk_um" name="chk_um" id="chk_um" data-set="#UserManagersTable .checkboxes" />
                                                                                    <span></span>
                                                                                </label>
                                                                            </th>
                                                                            <th>ID</th>
                                                                            <th>Name</th>
                                                                            <th>Trainee Region</th>
                                                                            <th>Assessor Name</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody></tbody>
                                                                </table>
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
            </div>
        </div>
    </div>
    <div class="modal fade" id="LoadModalFilter" role="basic" aria-hidden="true" data-width="400">
        <div class="modal-dialog modal-lg" style="width:1024px;">
            <div class="modal-content">
                <div class="modal-body" id="modal-body">
                    <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                    <span>
                        &nbsp;&nbsp;Loading... </span>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="LoadModalFilter1" role="basic" aria-hidden="true" data-width="400">
        <div class="modal-dialog modal-lg" style="width:524px;">
            <div class="modal-content">
                <div class="modal-body" id="modal-body1">
                    <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                    <span>
                        &nbsp;&nbsp;Loading... </span>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="modal fade" id="que_ans_modal" role="basic" aria-hidden="true" data-width="400">
        <div class="modal-dialog modal-lg" style="width:1024px;">
            <div class="modal-content">
                <div class="modal-body" id="modal-body1">
                    <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                    <span>
                        &nbsp;&nbsp;Loading... </span>
                </div>
            </div>
        </div>
    </div> -->
    <!-- <div class="modal fade" id="Mymodalid" role="basic" aria-hidden="true" data-width="400">
        <div class="modal-dialog modal-lg" style="width:524px;">
            <div class="modal-content">
                <div class="modal-body" id="modal-body1">
                    <img src="<?php echo $asset_url; ?>/assets/uploads/avatar/loading.gif" alt="" class="loading">
                    <span>
                        &nbsp;&nbsp;Loading... </span>
                </div>
            </div>
        </div>
    </div> -->
    <div class="modal fade" id="Mymodalid" role="basic" aria-hidden="true" data-width="400">
        <div class="modal-dialog modal-lg" style="width:524px;">
            <div class="modal-content">
                <span class="close">&times;</span>
                <p>Some text in the Modal..</p>
            </div>
        </div>
    </div>
    <?php $this->load->view("create_trinity/parameter_weight_modal"); ?>
    <?php $this->load->view('inc/inc_footer_script'); ?>
    <script type="text/javascript" src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script src="<?php echo $asset_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/ckeditor.js" type="text/javascript"></script>
    <script src="<?php echo $asset_url; ?>assets/global/plugins/cke-editor/adapters/jquery.js"></script>
    <script>
        var AddEdit = "E";
        var NewUsersArrray = [];
        var NewManagersArrray = [];
        var NewSupervisorsArrray = [];
        var NewUserManagersArrray = [];
        var NewQuestionArray = [];
        var Selected_QuestionArray = [];
        var company_id = '<?php echo $Company_id; ?>';
        var AssessmentForm = $('#AssessmentForm');
        var form_error = $('.alert-danger', AssessmentForm);
        var form_success = $('.alert-success', AssessmentForm);
        var TrainerArrray = [];
        var Totalqstn = <?= isset($key) ? $key + 1 : 0 ?>;
        var Base_url = "<?php echo base_url(); ?>";
        var Encode_id = "<?php echo base64_encode($result->id); ?>";
        var ParticipantForm = document.ParticipantForm;
        var MappingForm = document.MappingForm;
        var MappingSuperForm = document.MappingSuperForm;
        var UserMappingForm = document.UserMappingForm;
        var Unique_paramters = [];
        var TempSubParameterArray = [];
    </script>
    <script src="<?php echo $asset_url; ?>assets/customjs/create_trinity.js" type="text/javascript"></script>
    <script>
        <?php
        if (count((array)$mapping_goal) > 0) {
            foreach ($mapping_goal as $pstran) {
        ?>
                var push_value = {};
                push_value['id'] = "<?php echo $pstran->id; ?>";
                push_value['assessment_id'] = "<?php echo $pstran->assessment_id; ?>";
                push_value['script_id'] = "<?php echo $pstran->script_id; ?>";
                push_value['question_id'] = "<?php echo $pstran->question_id; ?>";
                push_value['question'] = "<?php echo $pstran->question; ?>";
                push_value['answer'] = "<?php echo $pstran->answer; ?>";
                TempSubParameterArray.push(push_value);

        <?php
            }
        }
        ?>
        <?php
        if (count((array)$parameter_subparameter) > 0) {
            foreach ($parameter_subparameter as $pstran) {
        ?>
                var push_value = {};
                push_value['language_id'] = "<?php echo $pstran->language_id; ?>";
                push_value['txn_id'] = "<?php echo $pstran->txn_id; ?>";
                push_value['parameter_id'] = "<?php echo $pstran->parameter_id; ?>";
                push_value['parameter_name'] = "<?php echo $pstran->parameter_name; ?>";
                push_value['parameter_label_id'] = "<?php echo $pstran->parameter_label_id; ?>";
                push_value['parameter_label_name'] = "<?php echo $pstran->parameter_label_name; ?>";
                push_value['subparameter_id'] = "<?php echo $pstran->sub_parameter_id; ?>";
                push_value['subparameter_name'] = "<?php echo $pstran->sub_parameter_name; ?>";
                push_value['type_id'] = "<?php echo $pstran->type_id > 0 ? $pstran->type_id : ''; ?>";
                push_value['type_name'] = "<?php echo $pstran->type_name; ?>";
                push_value['sentence_keyword'] = "<?php echo htmlspecialchars($pstran->sentence_keyword); ?>";
                push_value['parameter_weight'] = "<?php echo $pstran->parameter_weight; ?>";
                TempSubParameterArray.push(push_value);
                div_html = printonscreen_keyword_sentence(<?php echo $pstran->txn_id; ?>, <?php echo $pstran->parameter_id; ?>);
                var div_element = "#paramsub" + <?php echo $pstran->txn_id; ?>;
                $(div_element).empty();
                $(div_element).html('');
                $(div_element).html(div_html);

        <?php }
        } ?>
    </script>
    <script>
        jQuery(document).ready(function() {
            $(".myselect2").select2({
                // placeholder: 'Please select',
                dropdownAutoWidth: true,
                width: '100%',
                allowClear: true,
            });

            $(".language_id").select2({
                // placeholder: 'Please select',
                dropdownAutoWidth: true,
                width: '100%',
                allowClear: true,
            });

            $(".form_datetime").datetimepicker({
                autoclose: true,
                format: "dd-mm-yyyy hh:ii"
            });
            CKEDITOR.replace('description', {
                toolbar: [{
                        name: 'styles',
                        items: ['Styles', 'Format']
                    },
                    {
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat']
                    },
                    {
                        name: 'paragraph',
                        items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
                    },
                    {
                        name: 'links',
                        items: ['Link', 'Unlink', 'Anchor']
                    }
                ],
            });
            CKEDITOR.replace('instruction', {
                toolbar: [{
                        name: 'styles',
                        items: ['Styles', 'Format']
                    },
                    {
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat']
                    },
                    {
                        name: 'paragraph',
                        items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
                    },
                    {
                        name: 'links',
                        items: ['Link', 'Unlink', 'Anchor']
                    }
                ]
            });

            CKEDITOR.config.autoParagraph = false;
            DatatableUsersRefresh();
            DatatableManagersRefresh();
            DatatableSupervisorRefresh();
            DatatableUserManagersRefresh();
            // mapping goal
            Mapping_Goal_Refresh();
            // mapping goal
            $('.chk_mg').click(function() {
                if ($(this).is(':checked')) {
                    $("input[name='Mapping_all[]']").prop('checked', true);
                } else {
                    $("input[name='Mapping_all[]']").prop('checked', false);
                }

            });
            $('.chk_sp').click(function() {
                if ($(this).is(':checked')) {
                    $("input[name='Mappsuper_all[]']").prop('checked', true);
                } else {
                    $("input[name='Mappsuper_all[]']").prop('checked', false);
                }

            });
            $('.chk_tr').click(function() {
                if ($(this).is(':checked')) {
                    $("input[name='Participant_all[]']").prop('checked', true);
                } else {
                    $("input[name='Participant_all[]']").prop('checked', false);
                }

            });
            $('.chk_um').click(function() {
                if ($(this).is(':checked')) {
                    $("input[name='UserMapping_all[]']").prop('checked', true);
                } else {
                    $("input[name='UserMapping_all[]']").prop('checked', false);
                }

            });
            get_weight();
            // getUnique_paramters();

            $('input:checkbox').click(function() {
                $('input:checkbox').not(this).prop('checked', false);
            });

        });

        // mapping goal

        function Mapping_Goal_Refresh() {
            var table = $('#mapping_goal');
            table.dataTable({
                destroy: true,
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "emptyTable": "No data available in table",
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                    "infoEmpty": "No records found",
                    "infoFiltered": "(filtered1 from _MAX_ total records)",
                    "lengthMenu": "Show _MENU_",
                    "search": "Search:",
                    "zeroRecords": "No matching records found",
                    "paginate": {
                        "previous": "Prev",
                        "next": "Next",
                        "last": "Last",
                        "first": "First"
                    }
                },
                "bStateSave": false,
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "All"]
                ],
                "pageLength": 10,
                "pagingType": "bootstrap_full_number",
                "columnDefs": [{
                        'width': '15px',
                        'orderable': true,
                        'searchable': false,
                        'targets': [0]
                    },
                    {
                        'width': '30px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [1]
                    },
                    {
                        'width': '30px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [2]
                    },
                    {
                        'width': '30px',
                        'orderable': true,
                        'searchable': true,
                        'targets': [3]
                    }
                ],
                "order": [
                    [0, "desc"]
                ],
            });

        }
        // mapping goal
    </script>

</body>

</html>