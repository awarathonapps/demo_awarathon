<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$asset_url =$this->config->item('assets_url');
$acces_management = $this->session->userdata('awarathon_session');
$userID = $acces_management['user_id'];
$roleID = $acces_management['role'];
$avatar = $acces_management['avatar'];
$Compnay_Name = $acces_management['company_name'];

$Compnay_logo= $asset_url.'assets/layouts/layout/img/Awarathon-Logo-RedGrey.png';

// $UserSegment = strtolower($this->uri->segment(1));
//style="filter: brightness(250%);" 
?>
<div class="page-header navbar navbar-fixed-top">
    <div class="page-header-inner ">
        <div class="page-logo">
            <a href="<?php echo site_url("home"); ?>">
                <img src="<?= $Compnay_logo ?>" alt="logo" class="logo-default">
			</a>
            <!-- <div class="menu-toggler sidebar-toggler">
                <span></span>
            </div> -->
        </div>
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
            <span></span>
        </a>
        <?php if($Compnay_Name !=""){ ?>
        <div class="page-actions" style="color:#ccc;margin: 3px 0 15px 330px;padding: 0;float: left;">
			<!-- <div class="btn-group">
                   <h4 class="caption-subject title bold uppercase"> <?php echo $Compnay_Name; ?></h4>
			</div> -->
		</div>
        <?php } ?>
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown dropdown-user">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img alt="" class="img-circle" src="<?php echo  $avatar;?>" />
                        <span class="username username-hide-on-mobile"> <?php echo  $acces_management['first_name']; ?>&nbsp;<?php echo $acces_management['last_name'] ?> </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                    <?php if($acces_management['login_type'] != 1){ ?>    
                        <li>
                            <a href="<?php echo site_url("profile"); ?>">
                                <i class="icon-user"></i> My Profile </a>
                        </li>
                    <?php } ?>
						<?php if($acces_management['superaccess']){ ?>
                            <li data-id="configuration" class="main"><a  href="<?php  echo base_url()?>configuration/site_settings"><i class="icon-settings mr10"></i> Configuration</a></li>
                        <?php } ?>
                        <li>
                            <a href="<?php echo base_url();?>login/logout">
                                <i class="icon-key"></i> Log Out </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
