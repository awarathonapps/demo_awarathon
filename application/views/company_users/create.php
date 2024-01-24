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
                                    <span>Organisation</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>CMS Users</span>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Create New User</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url ?>company_users" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                <div id="errordiv" class="alert alert-danger display-hide">
                                    <button class="close" data-close="alert"></button>
                                    You have some form errors. Please check below.
                                    <br><span id="errorlog"></span>
                                </div> 
                                <div class="portlet light bordered">
                                    <div class="portlet-title">
                                        <div class="caption caption-font-24">
                                            Create CMS User
                                            <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tabbable-line tabbable-full-width">
                                            <ul class="nav nav-tabs" id="tabs">
                                                <li class="active"><a href="#tab_overview" data-toggle="tab">Overview</a></li>
                                                <!-- <li><a href="javascript:void(0);" data-toggle="tab">Trainer rights</a></li>
                                                <li><a href="javascript:void(0);" data-toggle="tab">Workshop rights</a></li> -->
                                                <li><a href="javascript:void(0);" data-toggle="tab">Change Avatar</a></li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_overview">    
                                                    <form id="frmUsers" name="frmUsers" method="POST"  action="<?php echo $base_url; ?>company_users/submit"> 
                                                        <?php if ($this->mw_session['company_id'] == "") { ?>
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Company Name<span class="required"> * </span></label>
                                                                    <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" onchange="Rolechange();">
                                                                        <option value="">Please Select</option>
                                                                        <?php foreach ($CompanySet as $cs) { ?>
                                                                            <option value="<?php echo $cs->id ?>"><?php echo $cs->company_name ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                        <?php } ?>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Employee Code</label>
                                                                    <input type="text" name="emp_id" id="emp_id" maxlength="50" value="" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Region<span class="required"> * </span></label>
                                                                    <select id="region_id" name="region_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                        <?php
                                                                        if (count($regionResult) > 0) {
                                                                            foreach ($regionResult as $value) {
                                                                                ?>
                                                                                <option value="<?php echo $value->id ?>"><?php echo $value->region_name ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Login Type<span class="required"> * </span></label>
                                                                    <select id="login_type" name="login_type" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <?php foreach ($LoginType as $lt) { ?>
                                                                            <option value="<?php echo $lt->id ?>"><?php echo $lt->name ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="row">    
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Login ID<span class="required"> * </span></label>
                                                                    <input type="text" name="loginid" id="loginid" maxlength="80" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Password<span class="required"> * </span></label>
                                                                    <input type="password" name="password" id="password" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Confirm Password<span class="required"> * </span></label>
                                                                    <input type="password" name="confirmpassword" id="confirmpassword" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row"> 
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Department<span class="required"> * </span></label>
                                                                    <input type="text" name="depart" id="depart" maxlength="250" value="" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Designation</label>
                                                                    <select id="designation" name="designation" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                        <?php
                                                                        if (count($designationResult) > 0) {
                                                                            foreach ($designationResult as $value) {
                                                                                ?>
                                                                                <option value="<?php echo $value->id ?>"><?php echo $value->description ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Role<span class="required"> * </span></label>
                                                                    <select id="roleid" name="roleid" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                        <?php
                                                                        if (count($roleResult) > 0) {
                                                                            foreach ($roleResult as $value) {
                                                                                ?>
                                                                                <option value="<?php echo $value->arid ?>"><?php echo $value->rolename ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Status<span class="required"> * </span></label>
                                                                    <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="1" selected>Active</option>
                                                                        <option value="0">In-Active</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="my-line"></div>
                                                        <div class="row"> 
                                                            <div class="col-md-1">    
                                                                <div class="form-group">
                                                                    <label>Salutation</label>
                                                                    <select id="salutation" name="salutation" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="Mr." selected="selected">Mr.</option>
                                                                        <option value="Mrs.">Mrs.</option>
                                                                        <option value="Miss">Miss</option>
                                                                        <option value="Dr.">Dr.</option>
                                                                        <option value="Prof.">Prof.</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">First name<span class="required"> * </span></label>
                                                                    <input type="text" name="first_name" id="first_name" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Last name<span class="required"> * </span></label>
                                                                    <input type="text" name="last_name" id="last_name" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Email<span class="required"> * </span></label>
                                                                    <input type="text" name="email" id="email" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Mobile No.</label>
                                                                    <input type="text" name="mobile" id="mobile" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Alternate Email</label>
                                                                    <input type="text" name="email2" id="email2" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Contact No</label>
                                                                    <input type="text" name="contactno" id="contactno" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">Fax</label>
                                                                    <input type="text" name="fax" id="fax" maxlength="50" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="my-line"></div>
                                                        <div class="row">
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Address 1</label>
                                                                    <input type="text" name="address" id="address" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">       
                                                                <div class="form-group">
                                                                    <label class="">Address 2</label>
                                                                    <input type="text" name="address2" id="address2" maxlength="250" class="form-control input-sm">                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>Country</label>
                                                                    <select id="country_id" name="country_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">    
                                                                <div class="form-group">
                                                                    <label>State</label>
                                                                    <select id="state_id" name="state_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">       
                                                                <div class="form-group">
                                                                    <label class="">City</label>
                                                                    <select id="city_id" name="city_id" class="form-control input-sm select2" placeholder="Please select" >
                                                                        <option value="">Please Select</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">    
                                                                <div class="form-group">
                                                                    <label>Note</label>
                                                                    <textarea rows="4" class="form-control input-sm" name="description" placeholder=""></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">      
                                                            <div class="col-md-12 text-right">  
                                                                <button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" 
                                                                        data-style="expand-right" onclick="SaveUserData();">
                                                                    <span class="ladda-label">Save & Next</span>
                                                                </button>
                                                                <a href="<?php echo site_url("company_users"); ?>" class="btn btn-default btn-cons">Cancel</a>
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
                <?php //$this->load->view('inc/inc_quick_sidebar');   ?>
            </div>
            <?php //$this->load->view('inc/inc_footer');   ?>
        </div>
        <?php //$this->load->view('inc/inc_quick_nav');   ?>
        <?php $this->load->view('inc/inc_footer_script'); ?>
        <script>
            var NewUsersArrray = [];
            var oTable = null;
            var base_url = "<?php echo $base_url; ?>";
            var Encode_id = "";
            var AddEdit = 'A';
            $('#country_id').select2({
            placeholder: '',
            multiple: false,
            separator: ',',
            ajax: {
                url: base_url + "company_users/ajax_populate_country",
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
                    return {results: data.results, more: more};
                }
            },
            initSelection: function (element, callback) {
                return $.getJSON(base_url + "company_users/ajax_populate_country?id=" + (element.val()), null, function (data) {
                    return callback(data);
                });
            }
        });
        $('#state_id').select2({
            placeholder: '',
            multiple: false,
            separator: ',',
            ajax: {
                url: base_url + "company_users/ajax_populate_state",
                dataType: 'json',
                quietMillis: 100,
                data: function (term, page) {
                    return {
                        country_id: $('#country_id').val(),
                        search: term,
                        page_limit: 10
                    };
                },
                results: function (data, page) {
                    var more = (page * 30) < data.total_count;
                    return {results: data.results, more: more};
                }
            },
            initSelection: function (element, callback) {
                return $.getJSON(base_url + "company_users/ajax_populate_country?id=" + (element.val()), null, function (data) {
                    return callback(data);
                });
            }
        });
$('#city_id').select2({
    placeholder: '',
    multiple: false,
    separator: ',',
    ajax: {
        url: base_url + "company_users/ajax_populate_city",
        dataType: 'json',
        quietMillis: 100,
        data: function (term, page) {
            return {
                state_id: $('#state_id').val(),
                search: term,
                page_limit: 10
            };
        },
        results: function (data, page) {
            var more = (page * 30) < data.total_count;
            return {results: data.results, more: more};
        }
    },
    initSelection: function (element, callback) {
        return $.getJSON(base_url + "company_users/ajax_populate_city?id=" + (element.val()), null, function (data) {
            return callback(data);
        });
    }
});
        </script>
        <script type="text/javascript" src="<?php echo $asset_url; ?>assets/customjs/cmsusers_validation.js"></script>
        
    </body>
</html>