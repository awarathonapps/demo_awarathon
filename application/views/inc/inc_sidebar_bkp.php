<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = base_url();
$acces_management = $this->session->userdata('awarathon_session');
$userID = $acces_management['user_id'];
$roleID = $acces_management['role'];
$masters_module_access=true;
$administrator_module_access = false;

if (CheckRights($userID, 'roles', 'allow_access') OR
    CheckRights($userID, 'users', 'allow_access')){
    $administrator_module_access = true;
}
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
            
            <?php if (CheckRights($userID, 'dashboard', 'allow_access')) { ?>
            <li class="nav-item start <?php echo (($module_id == '1.0') ? 'active open' : ''); ?>">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">Dashboard</span>
                    <span class="arrow" <?php echo (($module_id == '1.0') ? 'open' : ''); ?>></span>
                </a>
            </li>
            <?php } ?>

            <li class="nav-item start <?php echo (($module_id == '1.01' OR $module_id == '1.02' OR $module_id == '1.03' OR $module_id == '1.04' OR $module_id == '1.05') ? 'active open' : ''); ?>">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-university"></i>
                    <span class="title">Organisation</span>
                    <span class="arrow <?php echo (($module_id == '1.01' OR $module_id == '1.02' OR $module_id == '1.03' OR $module_id == '1.04' OR $module_id == '1.05') ? 'open' : ''); ?>"></span>
                </a>
                <ul class="sub-menu">
                    <?php if (CheckRights($userID, 'industry_type', 'allow_access')) { ?>
                    <li class="nav-item start <?php echo ($module_id == '1.05' ? 'active open' : ''); ?>">
                        <a href="<?php echo site_url("industry_type"); ?>" class="nav-link ">
                            <i class="fa fa-building-o"></i>
                            <span class="title">Industry Type</span>
                            <?php echo ($module_id == '1.05' ? '<span class="selected"></span>' : ''); ?> 
                        </a>
                    </li>
                    <?php } ?>
                    <?php if (CheckRights($userID, 'company', 'allow_access')) { ?>
                    <li class="nav-item start <?php echo ($module_id == '1.01' ? 'active open' : ''); ?>">
                        <a href="<?php echo site_url("company"); ?>" class="nav-link ">
                            <i class="fa fa-building-o"></i>
                            <span class="title">Company</span>
                            <?php echo ($module_id == '1.01' ? '<span class="selected"></span>' : ''); ?> 
                        </a>
                    </li>
                    <?php } ?>
                    <?php if (CheckRights($userID, 'company_roles', 'allow_access')) { ?>
                    <li class="nav-item start <?php echo ($module_id == '1.02' ? 'active open' : ''); ?>">
                        <a href="<?php echo site_url("company_roles"); ?>" class="nav-link ">
                            <i class="icon-user"></i>
                            <span class="title">Roles</span>
                            <?php echo ($module_id == '1.02' ? '<span class="selected"></span>' : ''); ?> 
                        </a>
                    </li>
                    <?php } ?>
                    <?php if (CheckRights($userID, 'company_users', 'allow_access')) { ?>
                    <li class="nav-item start <?php echo ($module_id == '1.03' ? 'active open' : ''); ?>">
                        <a href="<?php echo site_url("company_users"); ?>" class="nav-link ">
                            <i class="icon-users"></i>
                            <span class="title">Users</span>
                            <?php echo ($module_id == '1.03' ? '<span class="selected"></span>' : ''); ?> 
                        </a>
                    </li>
                    <?php } ?>
                    <?php if (CheckRights($userID, 'feedback_form', 'allow_access')) { ?>
                    <li class="nav-item start <?php echo ($module_id == '1.04' ? 'active open' : ''); ?>">
                        <a href="<?php echo site_url("feedback_form"); ?>" class="nav-link ">
                            <i class="icon-feed"></i>
                            <span class="title">Feedback Form</span>
                            <?php echo ($module_id == '1.04' ? '<span class="selected"></span>' : ''); ?> 
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </li>
            
            <?php if (CheckRights($userID, 'reward', 'allow_access')) { ?>
            <li class="nav-item start <?php echo (($module_id == '0.0') ? 'active open' : ''); ?>">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">Reward</span>
                    <span class="arrow" <?php echo (($module_id == '0.0') ? 'open' : ''); ?>></span>
                </a>
            </li>
            <?php } ?>

            <?php if (CheckRights($userID, 'workshop', 'allow_access')) { ?>
            <li class="nav-item start <?php echo (($module_id == '0.0') ? 'active open' : ''); ?>">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">Workshop</span>
                    <span class="arrow" <?php echo (($module_id == '0.0') ? 'open' : ''); ?>></span>
                </a>
            </li>
            <?php } ?>
            
            <?php if (CheckRights($userID, 'feedback', 'allow_access')) { ?>
            <li class="nav-item start <?php echo (($module_id == '0.0') ? 'active open' : ''); ?>">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">Feedback</span>
                    <span class="arrow" <?php echo (($module_id == '0.0') ? 'open' : ''); ?>></span>
                </a>
            </li>
            <?php } ?>
            
            <?php if (CheckRights($userID, 'app_users', 'allow_access')) { ?>
            <li class="heading">
                <h3 class="uppercase">Android/IOS</h3>
            </li>
            <li class="nav-item start <?php echo (($module_id == '0.0' OR $module_id == '0.0') ? 'active open' : ''); ?>">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">Application</span>
                    <span class="arrow <?php echo (($module_id == '0.0' OR $module_id == '0.0') ? 'open' : ''); ?>"></span>
                </a>
                <ul class="sub-menu">
                    <?php if (CheckRights($userID, 'users', 'allow_access')) { ?>
                    <li class="nav-item start <?php echo ($module_id == '0.0' ? 'active open' : ''); ?>">
                        <a href="<?php echo site_url("users"); ?>" class="nav-link ">
                            <i class="icon-users"></i>
                            <span class="title">Users</span>
                            <span class="badge badge-success ">1</span>
                            <?php echo ($module_id == '0.0' ? '<span class="selected"></span>' : ''); ?> 
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </li>    
            <?php } ?>

            <?php if (CheckRights($userID, 'feedback', 'allow_access')) { ?>
                <li class="heading">
                    <h3 class="uppercase">Reports</h3>
                </li>
                <?php if (CheckRights($userID, 'feedback', 'allow_access')) { ?>
                <li class="nav-item start <?php echo (($module_id == '0.0') ? 'active open' : ''); ?>">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-home"></i>
                        <span class="title">Workshop Reports</span>
                        <span class="arrow" <?php echo (($module_id == '0.0') ? 'open' : ''); ?>></span>
                    </a>
                </li>
                <?php } ?>
                <?php if (CheckRights($userID, 'feedback', 'allow_access')) { ?>
                <li class="nav-item start <?php echo (($module_id == '0.0') ? 'active open' : ''); ?>">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-home"></i>
                        <span class="title">Feedback Reports</span>
                        <span class="arrow" <?php echo (($module_id == '0.0') ? 'open' : ''); ?>></span>
                    </a>
                </li>
                <?php } ?>
            <?php } ?>

            <?php if ($administrator_module_access==true OR $masters_module_access==true) { ?>
            <li class="heading">
                <h3 class="uppercase">Settings</h3>
            </li>
            <?php if ($masters_module_access==true) { ?>
            <li class="nav-item start <?php echo (($module_id == '98.01' OR $module_id == '98.02' OR $module_id == '98.03' OR $module_id == '98.04') ? 'active open' : ''); ?>">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">Masters</span>
                    <span class="arrow <?php echo (($module_id == '98.01' OR $module_id == '98.02' OR $module_id == '98.03'OR $module_id == '98.04') ? 'open' : ''); ?>"></span>
                </a>
                <ul class="sub-menu">
                    <?php if (CheckRights($userID, 'country', 'allow_access')) { ?>
                    <li class="nav-item start <?php echo ($module_id == '98.01' ? 'active open' : ''); ?>">
                        <a href="<?php echo site_url("country"); ?>" class="nav-link ">
                            <i class="icon-globe"></i>
                            <span class="title">Country</span>
                            <?php echo ($module_id == '98.01' ? '<span class="selected"></span>' : ''); ?> 
                        </a>
                    </li>
                    <?php } ?>
                    <?php if (CheckRights($userID, 'state', 'allow_access')) { ?>
                    <li class="nav-item start <?php echo ($module_id == '98.02' ? 'active open' : ''); ?>">
                        <a href="<?php echo site_url("state"); ?>" class="nav-link ">
                            <i class="icon-flag"></i>
                            <span class="title">State</span>
                            <?php echo ($module_id == '98.02' ? '<span class="selected"></span>' : ''); ?> 
                        </a>
                    </li>
                    <?php } ?>
                    <?php if (CheckRights($userID, 'city', 'allow_access')) { ?>
                    <li class="nav-item start <?php echo ($module_id == '98.03' ? 'active open' : ''); ?>">
                        <a href="<?php echo site_url("city"); ?>" class="nav-link ">
                            <i class="icon-map"></i>
                            <span class="title">City</span>
                            <?php echo ($module_id == '98.03' ? '<span class="selected"></span>' : ''); ?> 
                        </a>
                    </li>
                    <?php } ?>
                    <?php if (CheckRights($userID, 'salutation', 'allow_access')) { ?>
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
            <?php if ($administrator_module_access==true) { ?>
            <li class="nav-item start <?php echo (($module_id == '99.01' OR $module_id == '99.02') ? 'active open' : ''); ?>">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">Administrator</span>
                    <span class="arrow <?php echo (($module_id == '99.01' OR $module_id == '99.02') ? 'open' : ''); ?>"></span>
                </a>
                <ul class="sub-menu">
                    <?php if (CheckRights($userID, 'roles', 'allow_access')) { ?>
                    <li class="nav-item start <?php echo ($module_id == '99.01' ? 'active open' : ''); ?>">
                        <a href="<?php echo site_url("roles"); ?>" class="nav-link ">
                            <i class="icon-user"></i>
                            <span class="title">Roles</span>
                            <?php echo ($module_id == '99.01' ? '<span class="selected"></span>' : ''); ?> 
                        </a>
                    </li>
                    <?php } ?>
                    <?php if (CheckRights($userID, 'users', 'allow_access')) { ?>
                    <li class="nav-item start <?php echo ($module_id == '99.02' ? 'active open' : ''); ?>">
                        <a href="<?php echo site_url("users"); ?>" class="nav-link ">
                            <i class="icon-users"></i>
                            <span class="title">Users</span>
                            <span class="badge badge-success ">1</span>
                            <?php echo ($module_id == '99.02' ? '<span class="selected"></span>' : ''); ?> 
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
