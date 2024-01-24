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
         <link href="<?php echo $base_url; ?>assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $base_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $base_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
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
                                    <span>Roles</span>
                                    <a data-title="An application role comprises a set of privileges that determine what users can see and do after signing in">
                                        <i class="icon-info font-black sub-title"></i>
                                    </a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Edit Role</span>
                                </li>
                            </ul>
                            <div class="page-toolbar">
                                <a href="<?php echo $base_url?>roles" class="btn btn-sm btn-default pull-right">Back</a>&nbsp;
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
                                            Edit Role
                                           <div class="tools"> </div>  
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <form id="frmRole" name="frmRole" method="POST" action="<?php echo $base_url; ?>roles/update/<?php echo base64_encode($role_data->arid); ?>">    
                                            <div class="row">    
                                                <div class="col-md-3">       
                                                    <div class="form-group">
                                                        <label class="">Role Name<span class="required"> * </span></label>
                                                        <input type="text" name="name" id="name" maxlength="50" class="form-control input-sm" value="<?php echo $role_data->rolename; ?>" autocomplete="off"/>                                 
                                                        <input id="id" name="id" type="hidden" class="form-control" autocomplete="off" value="<?php echo urlencode(base64_encode($role_id));?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">    
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                            <option value="1" <?php echo $role_data->status==1?'selected':''; ?>>Active</option>
                                                            <option value="0" <?php echo $role_data->status==0?'selected':''; ?>>In-Active</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">      
                                                <div class="col-md-6">    
                                                    <div class="form-group">
                                                        <label>Remarks</label>
                                                        <textarea rows="4" class="form-control input-sm" name="description" placeholder=""><?php echo $role_data->description; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <table class="table table-bordered table-hover table-checkable order-column" id="role_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Module Name</th>
                                                            <th>Full Access</th>
                                                            <th>View</th>
                                                            <th>Add</th>
                                                            <th>Edit</th>
                                                            <th>Delete</th>
                                                            <th>Print</th>
                                                            <th>Import</th>
                                                            <th>Export</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            $SrNo = 0;
                                                            $temp_modulegroup ='';
                                                            foreach ($rows as $key=> $row) {
                                                                $SrNo++;
                                                                if($temp_modulegroup!==$row->modulegroup){
                                                                    $temp_modulegroup=$row->modulegroup;
                                                                ?>
                                                                    <tr class="role-heading">
                                                                        <td class="hidden">&nbsp;</td>
                                                                        <td colspan="10" class="role-title"><?php echo $row->modulegroup ?></td>
                                                                        <td class="hidden">&nbsp;</td>
                                                                        <td class="hidden">&nbsp;</td>
                                                                        <td class="hidden">&nbsp;</td>
                                                                        <td class="hidden">&nbsp;</td>
                                                                        <td class="hidden">&nbsp;</td>
                                                                        <td class="hidden">&nbsp;</td>
                                                                        <td class="hidden">&nbsp;</td>
                                                                        <td class="hidden">&nbsp;</td>
                                                                    </tr>
                                                                <?php
                                                                }
                                                                ?>
                                                                    <tr class="">
                                                                        <td> <?php echo $SrNo ?></td>
                                                                        <td> <?php echo $row->modulelabel; ?></td>
                                                                        <td class="td-full-access">
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="<?php echo $row->modulename ?>">
                                                                                <input type="checkbox" class="checkRole" onclick="RoleCheckAll('<?php echo $row->modulename ?>')" value="1" id="<?php echo $row->modulename ?>" <?php echo (isset($result[$row->moduleid]['allow_access']) && $result[$row->moduleid]['allow_access'] == 1 ? 'checked' : '') ?> />
                                                                                <span></span>
                                                                            </label>
                                                                        </td>
                                                                        <td>
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="<?php echo 'chk_view'.$key; ?>">
                                                                                <input type="checkbox" class="checkRole" name="<?php echo $row->modulename.'_own[]' ?>" value="2" id="<?php echo 'chk_view'.$key; ?>" <?php echo (isset($result[$row->moduleid]['allow_view']) && $result[$row->moduleid]['allow_view'] == 1 ? 'checked' : '') ?>/>
                                                                                <span></span>
                                                                            </label>
                                                                        </td>
                                                                        <td>
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="<?php echo 'chk_add'.$key; ?>">
                                                                                <input type="checkbox" class="checkRole" name="<?php echo $row->modulename.'_own[]' ?>" value="3" id="<?php echo 'chk_add'.$key; ?>" <?php echo (isset($result[$row->moduleid]['allow_add']) && $result[$row->moduleid]['allow_add'] == 1 ? 'checked' : '') ?>/>
                                                                                <span></span>
                                                                            </label>
                                                                        </td>
                                                                        <td>
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="<?php echo 'chk_edit'.$key; ?>">
                                                                                <input type="checkbox" class="checkRole" name="<?php echo $row->modulename.'_own[]' ?>" value="4" id="<?php echo 'chk_edit'.$key; ?>" <?php echo (isset($result[$row->moduleid]['allow_edit']) && $result[$row->moduleid]['allow_edit'] == 1 ? 'checked' : '') ?>/>
                                                                                <span></span>
                                                                            </label>
                                                                        </td>
                                                                        <td>
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="<?php echo 'chk_del'.$key; ?>">
                                                                                <input type="checkbox" class="checkRole" name="<?php echo $row->modulename.'_own[]' ?>" value="5" id="<?php echo 'chk_del'.$key; ?>" <?php echo (isset($result[$row->moduleid]['allow_delete']) && $result[$row->moduleid]['allow_delete'] == 1 ? 'checked' : '') ?>/>
                                                                                <span></span>
                                                                            </label>
                                                                        </td>
                                                                        <td>
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="<?php echo 'chk_print'.$key; ?>">
                                                                                <input type="checkbox" class="checkRole" name="<?php echo $row->modulename.'_own[]' ?>" value="6" id="<?php echo 'chk_print'.$key; ?>" <?php echo (isset($result[$row->moduleid]['allow_print']) && $result[$row->moduleid]['allow_print'] == 1 ? 'checked' : '') ?>/>
                                                                                <span></span>
                                                                            </label>
                                                                        </td>
                                                                        <td>
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="<?php echo 'chk_import'.$key; ?>">
                                                                                <input type="checkbox" class="checkRole" name="<?php echo $row->modulename.'_own[]' ?>" value="7" id="<?php echo 'chk_import'.$key; ?>" <?php echo (isset($result[$row->moduleid]['allow_import']) && $result[$row->moduleid]['allow_import'] == 1 ? 'checked' : '') ?>/>
                                                                                <span></span>
                                                                            </label>
                                                                        </td>
                                                                        <td>
                                                                            <label class="mt-checkbox mt-checkbox-outline" for="<?php echo 'chk_export'.$key; ?>">
                                                                                <input type="checkbox" class="checkRole" name="<?php echo $row->modulename.'_own[]' ?>" value="8" id="<?php echo 'chk_export'.$key; ?>" <?php echo (isset($result[$row->moduleid]['allow_export']) && $result[$row->moduleid]['allow_export'] == 1 ? 'checked' : '') ?>/>
                                                                                <span></span>
                                                                            </label>
                                                                        </td>
                                                                    </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="row">      
                                                <div class="col-md-12 text-right margin-top-20">     
                                                    <button type="button" id="role-submit" name="role-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="SaveRoleData();">
                                                        <span class="ladda-label">Update</span>
                                                    </button>
                                                    <a href="<?php echo site_url("roles");?>" class="btn btn-default btn-cons">Cancel</a>
                                                </div>
                                            </div>
                                        </form>    
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
        <script src="<?php echo $base_url; ?>assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="<?php echo $base_url; ?>assets/global/plugins/datatables/Buttons-1.3.1/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script>
             var NewUsersArrray = [];
            var oTable = null;
            var base_url = "<?php echo $base_url.'roles'; ?>";
            var Encode_id = "<?php echo base64_encode($role_data->arid); ?>";
            var AddEdit = 'E';
        </script>
    <script type="text/javascript" src="<?php echo $base_url; ?>assets/customjs/cmsrole_validation.js"></script>
    </body>
</html>