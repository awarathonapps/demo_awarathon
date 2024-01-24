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
        
        <?php $this->load->view('inc/inc_htmlhead'); ?>
        <link href="<?php echo $base_url;?>assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $base_url;?>assets/global/plugins/cropper/cropper.css" rel="stylesheet" type="text/css" />
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
                                    <span>Administrator</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Users</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>View User</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url?>users" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            View User
                                           <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tabbable-line tabbable-full-width">
                                            <ul class="nav nav-tabs" id="tabs">
                                                <li class="active">
                                                    <a href="#tab_overview" data-toggle="tab">Overview</a>
                                                </li>
                                                <li>
                                                    <a href="#tab_avatar" data-toggle="tab">Change Avatar</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_overview"> 
                                                    <form id="frmUsers" name="frmUsers">    
                                                        <div class="row">    
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Login ID<span class="required"> * </span></label>
                                                                    <input type="text" name="loginid" id="loginid" maxlength="20" class="form-control input-sm" value="<?php echo $result->username; ?>" disabled>                                 
                                                                    <input type="hidden" name="user_id" id="user_id" maxlength="20" class="form-control input-sm" value="<?php echo urlencode(base64_encode($result->userid)); ?>">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Status<span class="required"> * </span></label>
                                                                    <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" disabled>
                                                                        <option value="1" <?php echo ($result->status==1)?'selected':'';?>>Active</option>
                                                                        <option value="0" <?php echo ($result->status==0)?'selected':'';?>>In-Active</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Role<span class="required"> * </span></label>
                                                                    <select id="roleid" name="roleid" class="form-control input-sm select2" placeholder="Please select" disabled>
                                                                        <option value="">Please Select</option>
                                                                        <?php foreach ($Role as $rl) { ?>
                                                                    <option value="<?php echo $rl->arid ?>" <?php echo ($rl->arid==$result->role ? 'selected' : '') ?>><?php echo $rl->rolename ?></option>
                                                                    <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="my-line"></div>
                                                        <div class="row"> 
                                                            <div class="col-md-1">    
                                                                <div class="form-group">
                                                                    <label>Salutation</label>
                                                                    <select id="saluation" name="saluation" class="form-control input-sm select2" placeholder="Please select" disabled>
                                                                        <option value="Mr." <?php echo ($result->status=='Mr.')?'selected':'';?>>Mr.</option>
                                                                        <option value="Mrs." <?php echo ($result->status=='Mrs.')?'selected':'';?>>Mrs.</option>
                                                                        <option value="Miss" <?php echo ($result->status=='Miss')?'selected':'';?>>Miss</option>
                                                                        <option value="Dr." <?php echo ($result->status=='Dr.')?'selected':'';?>>Dr.</option>
                                                                        <option value="Prof." <?php echo ($result->status=='Prof.')?'selected':'';?>>Prof.</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">First name<span class="required"> * </span></label>
                                                                    <input type="text" name="first_name" id="first_name" maxlength="50" class="form-control input-sm" value="<?php echo $result->first_name; ?>" disabled>                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Last name<span class="required"> * </span></label>
                                                                    <input type="text" name="last_name" id="last_name" maxlength="50" class="form-control input-sm" value="<?php echo $result->last_name; ?>" disabled>                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Email<span class="required"> * </span></label>
                                                                    <input type="text" name="email" id="email" maxlength="250" class="form-control input-sm" value="<?php echo $result->email; ?>" disabled>                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Mobile No.<span class="required"> * </span></label>
                                                                    <input type="text" name="mobile" id="mobile" maxlength="50" class="form-control input-sm" value="<?php echo $result->mobile; ?>" disabled>                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Alternate Email</label>
                                                                    <input type="text" name="email2" id="email2" maxlength="250" class="form-control input-sm" value="<?php echo $result->email2; ?>" disabled>                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Contact No</label>
                                                                    <input type="text" name="contactno" id="contactno" maxlength="50" class="form-control input-sm" value="<?php echo $result->contactno; ?>" disabled>                                 
                                                                </div>
                                                            </div>
                                                        
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Fax</label>
                                                                    <input type="text" name="fax" id="fax" maxlength="50" class="form-control input-sm" value="<?php echo $result->fax; ?>" disabled>                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="my-line"></div>
                                                        <div class="row">
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Address 1</label>
                                                                    <input type="text" name="address" id="address" maxlength="250" class="form-control input-sm" value="<?php echo $result->address1; ?>" disabled>                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Address 2</label>
                                                                    <input type="text" name="address2" id="address2" maxlength="250" class="form-control input-sm" value="<?php echo $result->address2; ?>" disabled>                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Country</label>
                                                                    <select id="country_id" name="country_id" class="form-control input-sm select2" placeholder="Please select" disabled>
                                                                        <option value="">Please Select</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>State</label>
                                                                    <select id="state_id" name="state_id" class="form-control input-sm select2" placeholder="Please select" disabled>
                                                                        <option value="">Please Select</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">City</label>
                                                                    <select id="city_id" name="city_id" class="form-control input-sm select2" placeholder="Please select" disabled>
                                                                        <option value="">Please Select</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Note</label>
                                                                    <textarea rows="4" class="form-control input-sm" name="description" placeholder="" disabled><?php echo $result->note; ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    </div>  
                                                    <div class="tab-pane mar" id="tab_avatar">
                                                        <div class="row">    
                                                            <div class="col-md-3"> 
                                                                <div class="container" id="crop-avatar">
                                                                    <!-- Current avatar -->
                                                                    <div class="avatar-view" title="Change the avatar">
                                                                        <?php 
                                                                            if (file_exists($result->avatar)){ 
                                                                                $preview_image = $base_url.$result->avatar;
                                                                            }else{
                                                                                $preview_image = "<?php echo $base_url;?>assets/uploads/avatar/no-avatar.jpg";
                                                                            }
                                                                        ?>
                                                                        <img id="preview-existing-avatar" src="<?php  echo $preview_image;?>" alt="Avatar">
                                                                    </div>
                                                                    <!-- Loading state -->
                                                                    <div class="loading" aria-label="Loading" role="img" tabindex="-1"></div>
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
                </div>
                <?php //$this->load->view('inc/inc_quick_sidebar'); ?>
            </div>
            <?php //$this->load->view('inc/inc_footer'); ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav'); ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        
        <script>
            jQuery(document).ready(function() {
                
                $('#roleid').select2({
                    placeholder: '',
                    
                    multiple:false,
                    separator: ',',
                    ajax: {
                        url: "<?php echo base_url();?>index.php/users/ajax_populate_roles",
                        dataType: 'json',
                        quietMillis: 100,
                        data: function (term, page) {
                            return {
                                search: term,
                                page_limit: 10
                            };
                        },
                        results: function (data, page) {
                            var more = (page * 30) < data.total_count;
                            return { results: data.results, more: more };
                        }
                    },
                    initSelection: function(element, callback) {
                        return $.getJSON("<?php echo base_url();?>index.php/users/ajax_populate_roles?id=" +<?php echo $result->role; ?>, null, function(data) {
                            return callback(data);
                        });
                    }
                });
                $('#country_id').select2({
                    placeholder: '',
                    
                    multiple:false,
                    separator: ',',
                    ajax: {
                        url: "<?php echo base_url();?>index.php/users/ajax_populate_country",
                        dataType: 'json',
                        quietMillis: 100,
                        data: function (term, page) {
                            return {
                                search: term,
                                page_limit: 10
                            };
                        },
                        results: function (data, page) {
                            var more = (page * 30) < data.total_count;
                            return { results: data.results, more: more };
                        }
                    },
                    initSelection: function(element, callback) {
                        return $.getJSON("<?php echo base_url();?>index.php/users/ajax_populate_country?id=" +<?php echo $result->country; ?>, null, function(data) {
                            return callback(data);
                        });
                    }
                });
                $('#state_id').select2({
                    placeholder: '',
                    
                    multiple:false,
                    separator: ',',
                    ajax: {
                        url: "<?php echo base_url();?>index.php/users/ajax_populate_state",
                        dataType: 'json',
                        quietMillis: 100,
                        data: function (term, page) {
                            return {
                                country_id:$('#country_id').val(),
                                search: term,
                                page_limit: 10
                            };
                        },
                        results: function (data, page) {
                            var more = (page * 30) < data.total_count;
                            return { results: data.results, more: more };
                        }
                    },
                    initSelection: function(element, callback) {
                        return $.getJSON("<?php echo base_url();?>index.php/users/ajax_populate_state?id=" + <?php echo $result->state; ?>, null, function(data) {
                            return callback(data);
                        });
                    }
                });
                $('#city_id').select2({
                    placeholder: '',
                    
                    multiple:false,
                    separator: ',',
                    ajax: {
                        url: "<?php echo base_url();?>index.php/users/ajax_populate_city",
                        dataType: 'json',
                        quietMillis: 100,
                        data: function (term, page) {
                            return {
                                state_id:$('#state_id').val(),
                                search: term,
                                page_limit: 10
                            };
                        },
                        results: function (data, page) {
                            var more = (page * 30) < data.total_count;
                            return { results: data.results, more: more };
                        }
                    },
                    initSelection: function(element, callback) {
                        return $.getJSON("<?php echo base_url();?>index.php/users/ajax_populate_city?id=" + <?php echo $result->city; ?>, null, function(data) {
                            return callback(data);
                        });
                    }
                });
                                
                var frmUsers = $('#frmUsers');
                var form_error = $('.alert-danger', frmUsers);
                var form_success = $('.alert-success', frmUsers);
                
                $(".select2, .select2-multiple", frmUsers).change(function () {
                    frmUsers.validate().element($(this));
                });
            });
        </script>
    </body>
</html>