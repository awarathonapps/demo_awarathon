<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$asset_url =$this->config->item('assets_url');
$base_url = base_url();

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <head>
        <?php $this->load->view('inc/inc_htmlhead_login'); ?>
    </head>
    <body class="login">
        <div class="user-login-5">
            <div class="row bs-reset">
                <div class="col-md-6 bs-reset mt-login-5-bsfix">
                    <div class="login-bg" style="background-image:url(<?php echo $asset_url; ?>assets/images/login_bg.png)">                    
                    </div>
                </div>
                <div class="col-md-6 login-container bs-reset mt-login-5-bsfix">
                    <div class="login-content">
                        <h1>Awarathon Sign In</h1>
                        <p>
                            Awarathon provide custom-made solutions for trainers and corporates seeking to measure the impact of training.
                            <br/><br/>Through a first-of-its-kind digital platform, we gather real-time data in classroom sessions and convert it into an analysis report to optimise internal and external development.
                        </p>
                        <form id="frmLogin" name="frmLogin" class="login-form" action="<?php echo $base_url; ?>login/service" method="post">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button>
                                <span>Enter any username and password</span>
                            </div>
                            <?php if ($error!==''){ ?>
                            <div class="alert alert-danger display-block">
                                <button class="close" data-close="alert"></button>
                                <span><?php echo $error;?></span>
                            </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-xs-6">
                                    <input class="form-control form-control-solid placeholder-no-fix form-group" type="text" autocomplete="off" 
                                           placeholder="Username" id="username" name="username" value="<?php echo (isset($member_username) ? $member_username : set_value('username')); ?>" required/>
                                </div>
                                <div class="col-xs-6">
                                    <input class="form-control form-control-solid placeholder-no-fix form-group" type="password" autocomplete="off" placeholder="Password" 
                                           id="password" name="password" value="<?php echo (isset($member_password) ? $member_password : set_value('password')); ?>" required/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="rem-password">
                                        <label class="rememberme mt-checkbox mt-checkbox-outline">
                                            <input type="checkbox" id="remember" <?php echo (isset($member_username) ? 'checked': ''); ?> name="remember" value="1" /> Remember me
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-8 text-right">
                                    <div class="forgot-password">
                                        <a href="javascript:;" id="forget-password" class="forget-password">Forgot Password?</a>
                                    </div>
                                    <button class="btn btn-orange" type="submit">Sign In</button>
                                </div>
                            </div>
                        </form>
                        <!-- BEGIN FORGOT PASSWORD FORM -->
                        <form id="forget-form" class="forget-form" action="#" method="post">
							<div id="forget-alert" class="alert display-block display-hide" style="margin:0">
                                <button class="close" data-close="alert"></button>
                                <span></span>
                            </div>
                            <h3 class="font-login">Forgot Password ?</h3>
                            <p> Enter your e-mail address below to reset your password. </p>
                            <div class="form-group">
                                <input class="form-control placeholder-no-fix form-group" type="text" autocomplete="off" placeholder="Email" name="email" /> </div>
                            <div class="form-actions">
                                <button type="button" id="back-btn" class="btn orange btn-outline">Back</button>
                                <button type="button" id="submit-btn" class="btn btn-orange uppercase pull-right">Submit</button>
                            </div>
                        </form>
                        <!-- END FORGOT PASSWORD FORM -->
                    </div>
                    <div class="login-footer">
                        <div class="row bs-reset">
                            <div class="col-xs-12 bs-reset">
                                <div class="login-copyright text-right">
                                    <p>Copyright &copy; Awarathon <?= date('Y') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view('inc/inc_login_script'); ?>
        <script>
            jQuery(document).ready(function() {
                $('.login-bg').backstretch([
                    "<?php echo $asset_url;?>assets/images/login_bg.png"
                    ], {
                      fade: 1000,
                      duration: 8000
                    }
                );
                $('.forget-form').hide();
                
                $('.login-form').validate({
                    errorElement: 'span',
                    errorClass: 'help-block',
                    focusInvalid: false,
                    rules: {
                        username: {
                            required: true
                        },
                        password: {
                            required: true
                        },
                        remember: {
                            required: false
                        }
                    },
                    messages: {
                        username: {
                            required: "Username is required."
                        },
                        password: {
                            required: "Password is required."
                        }
                    },
                    invalidHandler: function(event, validator) {  
                        $('.alert-danger', $('.login-form')).show();
                    },
                    highlight: function(element) {
                        $(element).closest('.form-group').addClass('has-error');
                    },
                    success: function(label) {
                        label.closest('.form-group').removeClass('has-error');
                        label.remove();
                    },
                    errorPlacement: function(error, element) {
                        error.insertAfter(element.closest('.input-icon'));
                    },
                    submitHandler: function(form) {
                        form.submit();
                    }
                });
                
                
                $('.login-form input').keypress(function(e) {
                    if (e.which == 13) {
                        if ($('.login-form').validate().form()) {
                            $('.login-form').submit();
                        }
                        return false;
                    }
                });

                $('.forget-form input').keypress(function(e) {
                    if (e.which == 13) {
                        if ($('.forget-form').validate().form()) {
                            $('.forget-form').submit();
                        }
                        return false;
                    }
                });

                $('#forget-password').click(function(){
                    $('.login-form').hide();
                    $('.forget-form').show();
                });

                $('#back-btn').click(function(){
                    $('.login-form').show();
                    $('.forget-form').hide();
                });
				
				$('#submit-btn').click(function(){
					$.ajax({
						url: "<?= $base_url; ?>login/forget_password",
						type: "POST",
						data: $('#forget-form').serialize(),
						dataType: 'JSON',
						success: function(data){
							$('#forget-alert').show().addClass('alert-success').html('New password sent to you on Email.');
							if(data.success == 0){
								$('#forget-alert').show().addClass('alert-danger').html(data.message);
							}else{
								$('#forget-alert').show().addClass('alert-success').html(data.message);
							}
						}
					});
				});
                
            });
        </script>
    </body>
</html>