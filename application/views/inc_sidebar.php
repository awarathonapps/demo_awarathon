<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//echo '<pre>';
$base_url = base_url();
$acces_management = $this->session->userdata('awarathon_session');
$SupperAccess = false;
$Company_id = $acces_management['company_id'];
$login_type = $acces_management['login_type'];
if ($acces_management['superaccess']) {
    $SupperAccess = true;
} else {
    $userID = $acces_management['user_id'];
    $roleID = $acces_management['role'];
    $ReturnSet = CheckSidebarRights($acces_management);
    $SideBarDataSet = $ReturnSet['RightsArray'];
    $GrouprightSet = $ReturnSet['GroupArray'];
    // echo "<pre>";
    // print_r($SideBarDataSet);
    // exit;
}
$masters_module_access = false;
$administrator_module_access = false;
if ($Company_id == "") {
    if (isset($GrouprightSet['Administrator'])) {
        $masters_module_access = true;
    }
    if (isset($SideBarDataSet['roles']) || isset($SideBarDataSet['users'])) {
        $administrator_module_access = true;
    }
}
$TraineeReports = false;
if (isset($SideBarDataSet['trainee_accuracy_report']) || isset($SideBarDataSet['trainee_post_accuracy_table']) ||
        isset($SideBarDataSet['trainee_comparison_report']) ) {
    $TraineeReports = true;
}
$Trainereports = false;
if (isset($SideBarDataSet['trainer_workshop']) || isset($SideBarDataSet['trainer_comparison']) || isset($SideBarDataSet['trainer_accuracy'])) {
    $Trainereports = true;
}
$Supervisorreports = false;
if (isset($SideBarDataSet['supervisor_reports']) || isset($SideBarDataSet['supervisor_comparison']) || isset($SideBarDataSet['supervisor_accuracy'])) {
    $Supervisorreports = true;
}
$MainModuleArray = explode('.', $module_id);
$MainModule_id = $MainModuleArray[0];
?>
<style>
    .badge {
        height: 18px !important;
        padding: 4px 8px !important;
    }
</style>    
<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 0px">
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>
			<?php if ($SupperAccess || isset($GrouprightSet['Dashboard'])) { ?>
                <li class="nav-item start <?php echo ($MainModule_id == '12' ? 'active open' : ''); ?>">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="fa fa-tachometer"></i>
                        <span class="title">Dashboard</span>
                        <span class="arrow <?php echo ($module_id == '12.01'  ? 'open' : ''); ?>"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php if ($SupperAccess || isset($SideBarDataSet['supervisor_dashboard'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '12.03') ? 'open' : ''); ?>">
                                <a href="<?php echo site_url("supervisor_dashboard"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">Supervisor Dashboard</span>
                                </a>
                            </li>
                        <?php } ?>
                        
                        <?php if ($SupperAccess || isset($SideBarDataSet['trainer_dashboard'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '12.01') ? 'open' : ''); ?>">
                                <a href="<?php echo site_url("trainer_dashboard"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">Trainer Dashboard</span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['trainee_dashboard'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '12.02' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("trainee_dashboard"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">Trainee Dashboard</span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>

                </li>
            <?php } ?>
            <?php if ($SupperAccess || isset($GrouprightSet['Go-live dashboard'])) { ?>
                <li class="nav-item start <?php echo ($MainModule_id == '44' ? 'active open' : ''); ?>">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="fa fa-industry"></i>
                        <span class="title">Go Live Dashboard</span>
                        <span class="arrow <?php echo ($module_id == '44.01' || $module_id == '44.02' || $module_id == '44.03' || $module_id=='44.04' ? 'open' : ''); ?>"></span>
                    </a>
                    <ul class="sub-menu">
						<?php if ($SupperAccess || isset($SideBarDataSet['assessment_dashboard'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '44.04' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("assessment_dashboard"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">Supervisor Dashboard</span>
                                </a>
                            </li>
                        <?php } ?>
                        
					
                        <?php if ($SupperAccess || isset($SideBarDataSet['manager_dashboard'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '44.05' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("manager_dashboard"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">Manager Dashboard</span>
                                </a>
                            </li>
                        <?php } ?>
						<?php if ($SupperAccess || isset($SideBarDataSet['assessment_trainee_dashboard'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '44.02' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("assessment_trainee_dashboard"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">Trainee Dashboard</span>
                                </a>
                            </li>
                        <?php } ?>
						<?php if ($SupperAccess || isset($SideBarDataSet['salesforce_dashboard'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '44.03' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("salesforce_dashboard"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-user"></i>
                                    <span class="title">Salesforce Optimisation</span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>

                </li>
            <?php } ?>
            <?php if ($SupperAccess || isset($GrouprightSet['Organisation'])) { ?>
                <li class="nav-item start <?php echo ($MainModule_id == '1' ? 'active open' : ''); ?>">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="fa fa-university"></i>
                        <span class="title">Organisation</span>
                        <span class="arrow <?php echo ($MainModule_id == '1' ? 'open' : ''); ?>"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php if ($Company_id == "") {
                            if ($SupperAccess || isset($SideBarDataSet['industry_type'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.05' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("industry_type"); ?>" class="nav-link ">
                                    <i class="fa fa-building-o"></i>
                                    <span class="title">Industry Type</span>
                                    <?php echo ($module_id == '1.05' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>
                            
                        <?php     
                            if ($SupperAccess || isset($SideBarDataSet['company'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.01' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("company"); ?>" class="nav-link ">
                                    <i class="fa fa-building-o"></i>
                                    <span class="title">Company</span>
                                    <?php echo ($module_id == '1.01' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php }} ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['company_roles'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.02' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("company_roles"); ?>" class="nav-link ">
                                    <i class="icon-user"></i>
                                    <span class="title">Roles</span>
                                    <?php echo ($module_id == '1.02' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['company_users'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.03' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("company_users"); ?>" class="nav-link ">
                                    <i class="icon-users"></i>
                                    <span class="title">CMS Users</span>
                                    <?php echo ($module_id == '1.03' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['information_form'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.04' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("information_form"); ?>" class="nav-link ">
                                    <i class="icon-feed"></i>
                                    <span class="title">Information Form</span>
                                    <?php echo ($module_id == '1.04' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['workshoptype'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.05' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("workshoptype"); ?>" class="nav-link ">
                                    <i class="fa fa-tasks"></i>
                                    <span class="title">Workshop Type</span>
                                    <?php echo ($module_id == '1.05' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['workshopsubtype'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.09' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("workshopsubtype"); ?>" class="nav-link ">
                                    <i class="fa fa-tasks"></i>
                                    <span class="title">Workshop Sub-Type</span>
                                    <?php echo ($module_id == '1.09' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>    
                        <?php if ($SupperAccess || isset($SideBarDataSet['region'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.06' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("region"); ?>" class="nav-link ">
                                    <i class="icon-map"></i>
                                    <span class="title">Region</span>
                                    <?php echo ($module_id == '1.06' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['workshopsubregion'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.10' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("workshopsubregion"); ?>" class="nav-link ">
                                    <i class="icon-map"></i>
                                    <span class="title">Sub-Region</span>
                                    <?php echo ($module_id == '1.10' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?> 
                        <?php if ($SupperAccess || isset($SideBarDataSet['store'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.08' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("store"); ?>" class="nav-link ">
                                    <i class="icon-map"></i>
                                    <span class="title">Store</span>
                                    <?php echo ($module_id == '1.08' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>    
                        <?php if ($SupperAccess || isset($SideBarDataSet['designation'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.07' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("designation/trainer_index"); ?>" class="nav-link ">
                                    <i class="icon-users"></i>
                                    <span class="title">Trainer Designation</span>
                                    <?php echo ($module_id == '1.07' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['designation'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.11' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("designation/trainee_index"); ?>" class="nav-link ">
                                    <i class="icon-users"></i>
                                    <span class="title">Trainee Designation</span>
                                    <?php echo ($module_id == '1.11' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>
						<?php     
                            if ($SupperAccess || isset($SideBarDataSet['emailtemplate'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.12' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("emailtemplate"); ?>" class="nav-link ">
                                    <i class="fa fa-building-o"></i>
                                    <span class="title">Email Template</span>
                                    <?php echo ($module_id == '1.13' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>
						<?php if ($SupperAccess || isset($SideBarDataSet['language'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '1.13' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("language"); ?>" class="nav-link ">
                                    <i class="fa fa-language"></i>
                                    <span class="title">Language</span>
                                    <?php echo ($module_id == '1.12' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>	
                    </ul>
                </li>
            <?php } ?>
            <!-- <?php if ($SupperAccess || isset($SideBarDataSet['reward'])) { ?>
                <li class="nav-item start <?php echo (($module_id == '6.00') ? 'active open' : ''); ?>">
                    <a href="<?php echo site_url("reward"); ?>" class="nav-link nav-toggle">
                        <i class="icon-trophy" ></i>                    
                        <span class="title" style="margin-left: -2px;">Reward</span>
                        <!-- <span class="arrow" <?php //echo (($module_id == '0.0') ? 'open' : '');      ?>></span> 
                    </a>
                </li>
            <?php } ?>
            <?php if ($SupperAccess || isset($SideBarDataSet['advertisement'])) { ?>
                <li class="nav-item start <?php echo (($module_id == '8.00') ? 'active open' : ''); ?>">
                    <a href="<?php echo site_url("advertisement"); ?>" class="nav-link nav-toggle">
                        <i class="icon-globe" ></i>
                        <span class="title" style="margin-left: -2px;">Advertisement</span>
                        <!-- <span class="arrow" <?php //echo (($module_id == '0.0') ? 'open' : '');      ?>></span> 
                    </a>
                </li>
            <?php } ?> -->
            <?php if ($SupperAccess || isset($GrouprightSet['Workshop'])) { ?>
                <li class="nav-item start <?php echo (($module_id == '4.00' OR $module_id == '4.01' OR $module_id == '4.02' OR $module_id == '4.03' OR $module_id == '4.04' OR $module_id == '4.05' OR $module_id == '4.06') ? 'active open' : ''); ?>">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-settings"></i>
                        <span class="title">Workshop</span>
                        <span class="arrow <?php echo (($module_id == '4.00' OR $module_id == '4.01' OR $module_id == '4.02' OR $module_id == '4.03' OR $module_id == '4.04' OR $module_id == '4.05' OR $module_id == '4.06') ? 'open' : ''); ?>"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php if ($SupperAccess || isset($SideBarDataSet['topics'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '4.01' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("topics"); ?>" class="nav-link ">
                                    <i class="icon-users"></i>
                                    <span class="title">Topics</span>
                                    <?php echo ($module_id == '4.01' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['subtopics'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '4.02' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("subtopics"); ?>" class="nav-link ">
                                    <i class="icon-users"></i>
                                    <span class="title">Sub-topics</span>
                                    <?php echo ($module_id == '4.02' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>  
                        <?php if ($SupperAccess || isset($SideBarDataSet['questions'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '4.06') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("questions"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Questions</span>
                                    <!-- <span class="arrow" <?php //echo (($module_id == '0.0') ? 'open' : '');      ?>></span> -->
                                </a>
                            </li>
                        <?php } ?> 
                        <?php if ($SupperAccess || isset($SideBarDataSet['questionset'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '4.04') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("questionset"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Question Set</span>
                                    <!-- <span class="arrow" <?php //echo (($module_id == '0.0') ? 'open' : '');      ?>></span> -->
                                </a>
                            </li>
                        <?php } ?>

                        <?php if ($SupperAccess || isset($SideBarDataSet['workshop'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '4.05') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("workshop"); ?>" class="nav-link ">
                                    <i class="fa fa-tasks"></i>
                                    <span class="title">Workshop</span>
                                </a>
                            </li>
                        <?php } ?> 
                    </ul>
                </li>
            <?php } ?>
            <?php if ($SupperAccess || isset($GrouprightSet['Feedback'])) { ?>
                <li class="nav-item start <?php echo (($module_id == '7.00' OR $module_id == '7.01' OR $module_id == '7.02' OR $module_id == '7.03') ? 'active open' : ''); ?>">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-settings"></i>
                        <span class="title">Feedback</span>
                        <span class="arrow <?php echo (($module_id == '7.00' OR $module_id == '7.01' OR $module_id == '7.02' OR $module_id == '7.03') ? 'open' : ''); ?>"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php if ($SupperAccess || isset($SideBarDataSet['feedback_type'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '7.01') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("feedback_type"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Feedback Type</span>
                                    <!-- <span class="arrow" <?php //echo (($module_id == '0.0') ? 'open' : '');      ?>></span> -->
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['feedback_subtype'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '7.03') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("feedback_subtype"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Feedback Sub-Type</span>
                                    <!-- <span class="arrow" <?php //echo (($module_id == '0.0') ? 'open' : '');      ?>></span> -->
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['feedback_questions'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '7.02') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("feedback_questions"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Feedback Questions</span>
                                    <!-- <span class="arrow" <?php //echo (($module_id == '0.0') ? 'open' : '');      ?>></span> -->
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['feedback_set'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '7.00') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("feedback_set"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Feedback Set</span>
                                    <!-- <span class="arrow" <?php //echo (($module_id == '0.0') ? 'open' : '');      ?>></span> -->
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li> 
            <?php } ?>
			<?php if ($SupperAccess || isset($GrouprightSet['Go Live'])) { ?>
                <li class="nav-item start <?php echo (($module_id == '13.01' OR $module_id == '13.02' OR $module_id == '13.03' OR $module_id == '13.04' OR $module_id == '13.05' OR $module_id == '13.06' OR $module_id == '13.07') ? 'active open' : ''); ?>">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-settings"></i>
                        <span class="title">Go Live</span>
                        <span class="arrow <?php echo (($module_id == '13.01' OR $module_id == '13.02' OR $module_id == '13.03' OR $module_id == '13.04' OR $module_id == '13.05' OR $module_id == '13.06' OR $module_id == '13.07') ? 'open' : ''); ?>"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php if ($SupperAccess || isset($SideBarDataSet['parameter'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '13.01') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("parameter"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Parameter</span>                                    
                                </a>
                            </li>
                        <?php } ?>
						<?php if ($SupperAccess || isset($SideBarDataSet['parameterlabel'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '13.06') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("parameterlabel"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Parameter Label</span>                                    
                                </a>
                            </li>
                        <?php } ?>						
                        <?php if ($SupperAccess || isset($SideBarDataSet['subparameter'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '13.07') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("subparameter"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Sub Parameter</span>                                    
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['video_situation'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '13.02') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("video_situation"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Video Q&A/Situation</span>                                    
                                </a>
                            </li>
                        <?php } ?>
						<?php if ($SupperAccess || isset($SideBarDataSet['assessment_create'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '13.04') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("assessment_create"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Go Live Creation</span>                                    
                                </a>
                            </li>
                        <?php } ?>
						<?php if ($SupperAccess || isset($SideBarDataSet['assessment_ai_score'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '13.05') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("assessment_ai_score"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Import AI Score</span>                                    
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['assessment'])) { ?>
                            <li class="nav-item start <?php echo (($module_id == '13.03') ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("assessment"); ?>" class="nav-link nav-toggle">
                                    <i class="icon-globe"></i>
                                    <span class="title">Video Assessment</span>                                    
                                </a>
                            </li>
                        <?php } ?>    
                    </ul>
                </li> 
            <?php } ?>
            <?php if ($SupperAccess || isset($SideBarDataSet['device_users']) || isset($SideBarDataSet['testing_users'])) { ?>
                <li class="heading">
                    <h3 class="uppercase">Android/IOS</h3>
                </li>
                <li class="nav-item start <?php echo (($module_id == '22.1' OR $module_id == '22.2') ? 'active open' : ''); ?>">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-settings"></i>
                        <span class="title">Application</span>
                        <span class="arrow <?php echo (($module_id == '22.1' OR $module_id == '22.2') ? 'open' : ''); ?>"></span>
                    </a>
                    <ul class="sub-menu">
                        <?php if ($SupperAccess || isset($SideBarDataSet['device_users'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '22.1' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("device_users"); ?>" class="nav-link ">
                                    <i class="icon-users"></i>
                                    <span class="title">Device Users</span>

                                    <?php echo ($module_id == '22.1' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['testing_users'])) { ?>
                            <li class="nav-item start <?php echo ($module_id == '22.2' ? 'active open' : ''); ?>">
                                <a href="<?php echo site_url("testing_users"); ?>" class="nav-link ">
                                    <i class="icon-users"></i>
                                    <span class="title">Testing Team</span>
                                    <?php echo ($module_id == '22.2' ? '<span class="selected"></span>' : ''); ?> 
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>    
            <?php } ?>
            <?php if ($SupperAccess || isset($GrouprightSet['Supervisor Reports'])
                    || isset($GrouprightSet['Trainer Reports']) || isset($GrouprightSet['Trainee Reports']) || isset($GrouprightSet['Feedback Reports']) 
                    || isset($GrouprightSet['Workshop Reports']) || isset($GrouprightSet['Assessment Reports'])) { ?>
                <li class="heading">
                    <h3 class="uppercase">Reports</h3>
                </li>
                <?php if ($SupperAccess || isset($GrouprightSet['Workshop Reports'])) { ?>
                    <li class="nav-item start <?php echo (($MainModule_id == '24') ? 'active open' : ''); ?>">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="icon-bar-chart"></i>
                            <span class="title">Workshop Reports</span>
                            <span class="arrow" <?php echo ($MainModule_id == '24' ? 'open' : ''); ?>></span>
                        </a>
                        <ul class="sub-menu">  
                            <?php if ($SupperAccess || isset($SideBarDataSet['trainee_played_result'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '24.1') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("trainee_played_result"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Trainee Played Results</span>
                                        <?php echo ($module_id == '24.1' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['trainee_wise_summary_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '24.4') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("trainee_wise_summary_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Trainee-wise Summary</span>
                                        <?php echo ($module_id == '24.4' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['traineetopic_wise_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '24.7') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("traineetopic_wise_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Trainee (Topic+ Questions Set)</span>
                                        <?php echo ($module_id == '24.7' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['trainer_wise_summary_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '24.5') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("trainer_wise_summary_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Trainer-wise Summary</span>
                                        <?php echo ($module_id == '24.5' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['trainer_consolidated_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '24.8') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("trainer_consolidated_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Trainer Consolidated</span>
                                        <?php echo ($module_id == '24.8' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['feedback_consolidated'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '24.9') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("feedback_consolidated_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Feedback Consolidated</span>
                                        <?php echo ($module_id == '24.9' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['attendence_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '24.2') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("attendence_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Attendance</span>
                                        <?php echo ($module_id == '24.2' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                             <?php if ($SupperAccess || isset($SideBarDataSet['store_wise_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '24.11') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("store_wise_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Store-wise</span>
                                        <?php echo ($module_id == '24.11' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>  
                            <?php if ($SupperAccess || isset($SideBarDataSet['workshop_wise_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '24.3') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("workshop_wise_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Workshop-wise</span>
                                        <?php echo ($module_id == '24.3' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['question_wise_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '24.6') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("question_wise_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Question-wise</span>
                                        <?php echo ($module_id == '24.6' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
							
                            <?php if ($SupperAccess || isset($SideBarDataSet['imei_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '24.7') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("imei_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Device imei report</span>
                                        <?php echo ($module_id == '24.7' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
							<?php if ($SupperAccess || isset($SideBarDataSet['device_changed_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '24.8') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("device_changed_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Device changed report</span>
                                        <?php echo ($module_id == '24.8' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
							
                            
                        </ul>
                    </li>
                <?php } ?>
				<?php if ($SupperAccess || isset($GrouprightSet['Assessment Reports'])) { ?>
                    <li class="nav-item start <?php echo (($MainModule_id == '27') ? 'active open' : ''); ?>">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="icon-bar-chart"></i>
                            <span class="title">Assessment Reports</span>
                            <span class="arrow" <?php echo ($MainModule_id == '27' ? 'open' : ''); ?>></span>
                        </a>
                        <ul class="sub-menu">  
                            <?php if ($SupperAccess || isset($SideBarDataSet['assessment_trainee_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '27.1') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("assessment_trainee_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title"> Trainee Reports</span>
                                        <?php echo ($module_id == '27.1' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
							<?php if ($SupperAccess || isset($SideBarDataSet['assessment_minute_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '27.3') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("assessment_minute_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title"> Minutes Reports</span>
                                        <?php echo ($module_id == '27.3' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
							<?php if ($SupperAccess || isset($SideBarDataSet['assessment_candidate_rpt'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '27.2') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("assessment_candidate_rpt"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title"> Candidate Level Reports</span>
                                        <?php echo ($module_id == '27.2' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($SupperAccess ||  isset($GrouprightSet['Feedback Reports']) ) { ?>
                <li class="nav-item start <?php echo ($MainModule_id== '16' ? 'active open' : ''); ?>">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-bar-chart"></i>
                        <span class="title">Feedback Reports</span>
                        <span class="arrow" <?php echo ($MainModule_id== '16' ? 'open' : ''); ?>></span>
                    </a>
                    <ul class="sub-menu">  
                        <?php if ($SupperAccess || isset($SideBarDataSet['weights_report']) ) { ?>
                        <li class="nav-item start <?php echo (($module_id == '16.1') ? 'active open' : ''); ?>">
                            <a href="<?php echo site_url("weights_report"); ?>" class="nav-link nav-toggle">
                                <i class="icon-globe"></i>
                                <span class="title">Weights</span>
                                <?php echo ($module_id == '16.1' ? '<span class="selected"></span>' : ''); ?> 
                            </a>
                        </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['no_weights_report']) ) { ?>
                        <li class="nav-item start <?php echo (($module_id == '16.2') ? 'active open' : ''); ?>">
                            <a href="<?php echo site_url("no_weights_report"); ?>" class="nav-link nav-toggle">
                                <i class="icon-globe"></i>
                                <span class="title">No Weights</span>
                                <?php echo ($module_id == '16.2' ? '<span class="selected"></span>' : ''); ?> 
                            </a>
                        </li>
                        <?php } ?>
                        <?php if ($SupperAccess || isset($SideBarDataSet['vark_report']) ) { ?>
                        <li class="nav-item start <?php echo (($module_id == '16.3') ? 'active open' : ''); ?>">
                            <a href="<?php echo site_url("vark_report"); ?>" class="nav-link nav-toggle">
                                <i class="icon-globe"></i>
                                <span class="title">VARK</span>
                                <?php echo ($module_id == '16.3' ? '<span class="selected"></span>' : ''); ?> 
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>
                <?php if ($SupperAccess || $Supervisorreports) { ?>
                    <li class="nav-item start <?php echo ( $MainModule_id== '15' ? 'active open' : ''); ?>">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="icon-bar-chart"></i>
                            <span class="title">Supervisor Reports</span>
                            <span class="arrow" <?php echo ($MainModule_id== '15' ? 'open' : ''); ?>></span>
                        </a>
                        <ul class="sub-menu">  
                            <?php if ($SupperAccess || isset($SideBarDataSet['supervisor_reports'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '15.1') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("supervisor_reports"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Supervisor Report</span>
                                        <?php echo ($module_id == '15.1' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['Supervisor_comparison'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '15.2') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("Supervisor_comparison"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Comparison</span>
                                        <?php echo ($module_id == '15.2' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['Supervisor_accuracy'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '15.3') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("supervisor_accuracy"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Accuracy Report</span>
                                        <?php echo ($module_id == '15.3' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($SupperAccess || $Trainereports) { ?>
                    <li class="nav-item start <?php echo (( $MainModule_id == '25' ) ? 'active open' : ''); ?>">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="icon-bar-chart"></i>
                            <span class="title">Trainer Reports</span>
                            <span class="arrow" <?php echo (($MainModule_id == '25') ? 'open' : ''); ?>></span>
                        </a>
                        <ul class="sub-menu">  
                            <?php if ($SupperAccess || isset($SideBarDataSet['trainer_workshop'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '25.1') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("trainer_workshop"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-bar-chart"></i>
                                        <span class="title">Trainer Workshop</span>
                                        <?php echo ($module_id == '25.1' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['trainer_comparison'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '25.2') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("trainer_comparison"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-bar-chart"></i>
                                        <span class="title">Trainer Comparison</span>
                                        <?php echo ($module_id == '25.2' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['trainer_accuracy'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '25.3') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("trainer_accuracy"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-bar-chart"></i>
                                        <span class="title">Trainer Accuracy</span>
                                        <?php echo ($module_id == '25.3' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($SupperAccess || $TraineeReports) { ?>
                    <li class="nav-item start <?php echo (( $MainModule_id == '26') ? 'active open' : ''); ?>">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="icon-bar-chart"></i>
                            <span class="title">Trainee Reports</span>
                            <span class="arrow" <?php echo ($MainModule_id == '26' ? 'open' : ''); ?>></span>
                        </a>
                        <ul class="sub-menu">
                            <?php if ($SupperAccess || isset($SideBarDataSet['trainee_post_accuracy_table'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '26.1') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("trainee_dashboard_i"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Trainee Workshop</span>
                                        <?php echo ($module_id == '26.1' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['trainee_comparison_report'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '26.2' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("trainee_comparison_report"); ?>" class="nav-link ">
                                        <i class="icon-bar-chart"></i>
                                        <span class="title">Trainee Comparison</span>
                                        <?php echo ($module_id == '26.2' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['trainee_accuracy_report'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '26.3') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("trainee_accuracy_report"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe"></i>
                                        <span class="title">Trainee Accuracy</span>
                                        <?php echo ($module_id == '26.3' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>


                        </ul>
                    </li>
                <?php } ?>
				<!-- <?php if ($SupperAccess || $administrator_module_access) { ?>
                    <li class="nav-item start <?php echo (($module_id == '14.01' || $module_id == '14.02' || $module_id == '14.03' || $module_id == '14.05') ? 'active open' : ''); ?>">
                        <a href="javascript:;" class="nav-link nav-toggle">
							<i class="icon-bar-chart"></i>
                            <span class="title">AI Reports</span>
                            <span class="arrow <?php echo (($module_id == '14.01' || $module_id == '14.02' || $module_id == '14.03' || $module_id == '14.05') ? 'open' : ''); ?>"></span>
                        </a>
                        <ul class="sub-menu">
                            <?php if ($SupperAccess || isset($SideBarDataSet['ai_schedule'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '14.02' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("ai_schedule"); ?>" class="nav-link ">
                                        <i class="icon-user"></i>
                                        <span class="title">AI Schedule</span>
                                        <?php echo ($module_id == '14.02' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['ai_reports'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '14.03' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("ai_reports"); ?>" class="nav-link ">
                                        <i class="icon-user"></i>
                                        <span class="title">AI Reports</span>
                                        <?php echo ($module_id == '14.03' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['report_status'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '14.04' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("report_status"); ?>" class="nav-link ">
                                        <i class="icon-user"></i>
                                        <span class="title"> Status Report</span>
                                        <?php echo ($module_id == '14.04' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
							<?php if ($SupperAccess || isset($SideBarDataSet['ai_email_schedule'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '14.05' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("ai_email_schedule"); ?>" class="nav-link ">
                                        <i class="icon-user"></i>
                                        <span class="title"> Email Schedule</span>
                                        <?php echo ($module_id == '14.05' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?> -->
                <?php if ($SupperAccess || $administrator_module_access) { ?>
                    <li class="nav-item start <?php echo (($module_id == '14.01' || $module_id == '14.02') ? 'active open' : ''); ?>">
                        <a href="javascript:;" class="nav-link nav-toggle">
							<i class="icon-globe"></i>
                            <span class="title">Jarvis</span>
                            <span class="arrow <?php echo (($module_id == '14.01' || $module_id == '14.02') ? 'open' : ''); ?>"></span>
                        </a>
                        <ul class="sub-menu">
                            <?php if ($SupperAccess || isset($SideBarDataSet['ai_dashboard'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '14.01' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("ai_dashboard"); ?>" class="nav-link ">
                                        <i class="icon-user"></i>
                                        <span class="title">AI Dashboard</span>
                                        <?php echo ($module_id == '14.01' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['ai_process_reports'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '14.02' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("ai_reports"); ?>" class="nav-link ">
                                        <i class="icon-user"></i>
                                        <span class="title">AI Reports</span>
                                        <?php echo ($module_id == '14.02' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
            <?php } ?>
			 	
            <?php if ($Company_id=="" && ($SupperAccess || $administrator_module_access || $masters_module_access)) { ?>
                <li class="heading">
                    <h3 class="uppercase">Settings</h3>
                </li>
                <?php if ($SupperAccess || $masters_module_access) { ?>
                    <li class="nav-item start <?php echo (($module_id == '98.01' OR $module_id == '98.02' OR $module_id == '98.03' OR $module_id == '98.04' OR $module_id == '98.07') ? 'active open' : ''); ?>">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">Masters</span>
                            <span class="arrow <?php echo (($module_id == '98.01' OR $module_id == '98.02' OR $module_id == '98.03'OR $module_id == '98.04' OR $module_id == '98.07' ) ? 'open' : ''); ?>"></span>
                        </a>
                        <ul class="sub-menu">

                            <?php if ($SupperAccess || isset($SideBarDataSet['country'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '98.01' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("country"); ?>" class="nav-link ">
                                        <i class="icon-globe"></i>
                                        <span class="title">Country</span>
                                        <?php echo ($module_id == '98.01' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['state'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '98.02' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("state"); ?>" class="nav-link ">
                                        <i class="icon-flag"></i>
                                        <span class="title">State</span>
                                        <?php echo ($module_id == '98.02' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['city'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '98.03' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("city"); ?>" class="nav-link ">
                                        <i class="icon-map"></i>
                                        <span class="title">City</span>
                                        <?php echo ($module_id == '98.03' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['salutation'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '98.04' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("salutation"); ?>" class="nav-link ">
                                        <i class="icon-users"></i>
                                        <span class="title">Salutation</span>
                                        <?php echo ($module_id == '98.04' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($SupperAccess || $administrator_module_access) { ?>
                    <li class="nav-item start <?php echo (($module_id == '99.01' OR $module_id == '99.02' OR $module_id == '99.03') ? 'active open' : ''); ?>">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">Administrator</span>
                            <span class="arrow <?php echo (($module_id == '99.01' OR $module_id == '99.02' OR $module_id == '99.03') ? 'open' : ''); ?>"></span>
                        </a>
                        <ul class="sub-menu">
                            <?php if ($SupperAccess || isset($SideBarDataSet['roles'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '99.01' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("roles"); ?>" class="nav-link ">
                                        <i class="icon-user"></i>
                                        <span class="title">Roles</span>
                                        <?php echo ($module_id == '99.01' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['users'])) { ?>
                                <li class="nav-item start <?php echo ($module_id == '99.02' ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("users"); ?>" class="nav-link ">
                                        <i class="icon-users"></i>
                                        <span class="title">Users</span>
                                        <?php echo ($module_id == '99.02' ? '<span class="selected"></span>' : ''); ?> 
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['smtp'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '99.03') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("smtpsetting"); ?>" class="nav-link nav-toggle">
                                        <i class="icon-globe" ></i>
                                        <span class="title" style="margin-left: -2px;">SMTP Setting</span>
                                        <!-- <span class="arrow" <?php //echo (($module_id == '0.0') ? 'open' : '');      ?>></span> -->
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($SupperAccess || isset($SideBarDataSet['tconditions'])) { ?>
                                <li class="nav-item start <?php echo (($module_id == '99.04') ? 'active open' : ''); ?>">
                                    <a href="<?php echo site_url("tconditions"); ?>" class="nav-link nav-toggle">
                                        <i class="fa fa-check-circle"></i>
                                        <span class="title" style="margin-left: -2px;">Terms & Conditions</span>
                                        <!-- <span class="arrow" <?php //echo (($module_id == '0.0') ? 'open' : '');      ?>></span> -->
                                    </a>
                                </li>
                            <?php } ?> 
                        </ul>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
</div>