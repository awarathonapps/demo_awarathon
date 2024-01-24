<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
$array = json_decode(json_encode($lang_result), True);
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">

<head>
    <!--link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous"-->
    <?php $this->load->view('inc/inc_htmlhead'); ?>
    <link href="<?=$base_url.'/assets/layouts/auth/css/googleTranslate.css';?>" rel="stylesheet" type="text/css" />
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
                                <span>Language</span>
                            </li>
                        </ul>
                        <div class="col-md-1 page-breadcrumb"></div>
                        <div class="page-toolbar">
                            <!-- <div id="dashboard-report-range" name="daterange" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
								<i class="icon-calendar"></i>&nbsp;
								<span class="thin uppercase hidden-xs"></span>&nbsp;
								<i class="fa fa-angle-down"></i>
							</div> -->
                        </div>
                    </div>

                    <div class="row margin-top-10">
                        <div class="col-md-12">

                            <div class="portlet light bordered">
    <div class="portlet-body">
        <div class="tabbable-line tabbable-full-width">
            <!-- Put here new code -->
            <form id="frmAIMethod" name="frmAIMethod" method="POST">
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-12 mid-space">
                    <div class="right-content aw-dashboard">
                        <div class="row"><div id='dsk' style="display: none">&nbsp;</div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h4 class="toph4">Default Language</h4>
                                <div class="api-details">
                                    <p class="bottomPR" style="font-size:15px;">Select the default language you want to be available in.</p>
                                    <div class="bottomsel">
                                        <select id="filter_topic_id" name="filter_topic_id" class="form-control input-sm lanselect" placeholder="Please select">
                                            <option value="en" selected>English</option>
                                        </select>
                                    </div>
                                </div>
 
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h4 class="toph4">MultiLanguage  
                                    <input type="checkbox" name="checkbox" class="cm-toggle" style="display: inline-grid;">        
                                </h4>
                                <?php //  <i class='fas fa-crown' style="color:#0bcf6c;"></i> ?>
                                <p class="bottomPR" style="font-size:15px;">By enabling multilanguage option you add more additional languages to this portal.</p>

                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <button type="button" id="send_otp" name="send_otp" class="btn btn-sm btn-outline addbutton" data-style="expand-right">
                                <span class="ladda-label"><i class="fa fa-plus-circle" style="color: #1d4fab;font-size: 20px;"></i>&nbsp; Add Additional Language</span></button>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12" style="padding-left: 30px!important;">
                                <div class="col-lg-4 col-md-4 col-sm-4" style="padding-left: 30px!important;font-size: larger;font-weight: 600;">Languages</div>
                                <div class="col-lg-8 col-md-8 col-sm-8" style="padding-left: 30px!important;text-align: center;font-size: larger;font-weight: 600;">Access</div>   
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12" style="padding-left: 30px!important;">                 
                                <div class="col-lg-4 col-md-4 col-sm-4 shortname_lang">
                                    <div class="shortname">
                                        <span>En</span>
                                    </div>
                                    <label class="shortname_label">English</label>  
                                </div>              
                                <div class="col-lg-8 col-md-8 col-sm-8 short_check">
                                    <input type="checkbox" name="hindi" class="cm-toggle" style="display:grid;">
                                </div>               
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12" style="padding-left: 30px!important;">                   
                                <div class="col-lg-4 col-md-4 col-sm-4 shortname_lang">
                                    <div class="shortname">
                                        <span>Hi</span> 
                                    </div>
                                    <label class="shortname_label">Hindi</label>   
                                </div>                    
                                <div class="col-lg-8 col-md-8 col-sm-8 short_check">
                                    <input type="checkbox" name="hindi" class="cm-toggle" style="display:grid;">
                                </div>             
                            </div>

                            <input type="hidden" name="status" id="status" value="<?php echo $array[0]['status']; ?>" class="form-control input-sm">
                            <input type="hidden" name="addedby" id="addedby" value="<?php echo $array[0]['addedby']; ?>" class="form-control input-sm">
                            <input type="hidden" name="lan_id" id="lan_id" value="<?php echo base64_encode($array[0]['lan_id']); ?>" class="form-control input-sm">
                                                
                            <div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:30px;">
                                <h4 class="bottomh4">Login Page Language</h4>
                                <div class="api-details">
                                    <p class="bottomPR">Your login page will be visible in this language.</p>
                                    <div class="bottomsel">
                                        <select id="log_page_lang" name="log_page_lang" class="form-control input-sm lanselect" placeholder="Please select">
                                            <option value="">Language Select</option>
                                            <option value="en" <?php echo ($array[0]['login_page'] == 'en')?'selected':'';?>>English</option>
                                            <option value="hi" <?php echo ($array[0]['login_page'] == 'hi')?'selected':'';?>>Hindi</option>
                                            <option value="gu" <?php echo ($array[0]['login_page'] == 'gu')?'selected':'';?>>Gujarati</option>
                                            <option value="mr" <?php echo ($array[0]['login_page'] == 'mr')?'selected':'';?>>Marathi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:15px;">
                                <h4 class="bottomh4">Backend System Language</h4>
                                <div class="api-details">
                                    <p class="bottomPR">Your Admin/Manager backend system will be visible in this language.</p>
                                    <div class="bottomsel">
                                        <select id="back_lang" name="back_lang" class="form-control input-sm lanselect" placeholder="Please select">
                                            <option value="">Language Select</option>
                                            <option value="en" <?php echo ($array[0]['backend_page'] == 'en')?'selected':'';?>>English</option>
                                            <option value="hi" <?php echo ($array[0]['backend_page'] == 'hi')?'selected':'';?>>Hindi</option>
                                            <option value="gu" <?php echo ($array[0]['backend_page'] == 'gu')?'selected':'';?>>Gujarati</option>
                                            <option value="mr" <?php echo ($array[0]['backend_page'] == 'mr')?'selected':'';?>>Marathi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:15px;">
                                <h4 class="bottomh4">PWA Language</h4>
                                <div class="api-details">
                                    <p class="bottomPR">Your Participation page for Users will be visible in this language.</p>
                                    <div class="bottomsel">
                                        <select id="pwa_lang" name="pwa_lang" class="form-control input-sm lanselect" placeholder="Please select">
                                            <option value="">Language Select</option>
                                            <option value="en" <?php echo ($array[0]['pwa_page'] == 'en')?'selected':'';?>>English</option>
                                            <option value="hi" <?php echo ($array[0]['pwa_page'] == 'hi')?'selected':'';?>>Hindi</option>
                                            <option value="gu" <?php echo ($array[0]['pwa_page'] == 'gu')?'selected':'';?>>Gujarati</option>
                                            <option value="mr" <?php echo ($array[0]['pwa_page'] == 'mr')?'selected':'';?>>Marathi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="col-lg-12 col-md-12 col-sm-12" style="margin-top:15px;">
                            <?php if(count($sel_Lang)>0){ ?>
                                <button type="button" id="lang-submit" name="lang-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="update_aimethods();">
                                    <span class="ladda-label">Update</span>
                                </button>
                                <?php }else{ ?>
                                <button type="button" id="lang-submit" name="lang-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-left" onclick="save_aimethods();">
                                    <span class="ladda-label">Save</span>
                                </button>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
            <!-- End here new code -->
        </div>
    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('inc/inc_footer_script'); ?>
    <!--script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script-->

<script>
    var base_url     = "<?php echo $base_url; ?>";       
    var frmAIMethod  = $('#frmAIMethod');            
    var form_error   = $('.alert-danger', frmAIMethod);
    var form_success = $('.alert-success', frmAIMethod);
    jQuery(document).ready(function() {   
        //alert('test form');
    });
        
    function save_aimethods() {             
        
        if (!$('#frmAIMethod').valid()) {
            return false;
        }else{   }                 
        $.ajax({
            type: "POST",
            url: '<?php echo base_url(); ?>multilang/submit',
            data: $('#frmAIMethod').serialize(),
            success: function(data) {
                var data= JSON.parse(data);
                // Ajax call completed successfully
                document.getElementById('dsk').innerHTML = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>' + data.message + '</div>';
                document.getElementById('dsk').style.display = "block";
                $('html, body').animate({ scrollTop: 0 }, 'slow');
                setTimeout('document.getElementById("dsk").style.display = "none"', 2000);
                setTimeout(function(){ window.location.reload(); },2000);  
            },
            error: function(data) {
                    
                // Some error in ajax call
                document.getElementById('dsk').innerHTML = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>some error !</div>';
                document.getElementById('dsk').style.display = "block";
                setTimeout('document.getElementById("dsk").style.display = "none"', 2000);
            }
        });
    }   

    function update_aimethods() {               //alert('update_aimethods');
        var lan_id = $('#lan_id').val();
        if (!$('#frmAIMethod').valid()) {
            return false;
        }else{ //alert('else valid');  
        }          //alert(base_url); alert(lan_id);                
        $.ajax({
            type: "POST",
            url: '<?php echo base_url(); ?>multilang/edit',
            data: $('#frmAIMethod').serialize(),
            success: function(data) {
                var data= JSON.parse(data);
                  // alert(data.message);
                //alert("Language Update Successfully olddd");
                document.getElementById('dsk').innerHTML = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>' + data.message + '</div>';
                document.getElementById('dsk').style.display = "block";
                //window.location.href = base_url + 'multilang';   
                $('html, body').animate({ scrollTop: 0 }, 'slow');
                setTimeout('document.getElementById("dsk").style.display = "none"', 1000);
                setTimeout(function(){ window.location.reload(); },1000);
                
            },
            error: function(data) {
                    
                // Some error in ajax call
                //alert("some Error");
                document.getElementById('dsk').innerHTML = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>some error !</div>';
                document.getElementById('dsk').style.display = "block";
                window.location.href = base_url + 'multilang'; 
                $('html, body').animate({ scrollTop: 0 }, 'slow');
                setTimeout('document.getElementById("dsk").style.display = "none"', 2000);
            }
        });
    }   
</script>
</body>
</html>