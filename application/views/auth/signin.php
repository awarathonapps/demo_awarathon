<?php $base_url= base_url(); ?>
<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
		<?php $this->load->view('auth/signin-head'); ?>
		<link href="<?=$base_url.'/assets/layouts/auth/css/google-btn.css';?>" rel="stylesheet" type="text/css" />
		<link href="<?= $base_url;?>/assets/layouts/layout/fonts/fonts.css" rel="stylesheet" type="text/css" />
		<link href="<?= $base_url;?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
		<link href="<?= $base_url;?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
		<style>
		.alert-forget{
			display: none;
		}
		.icon-user{
			font-size:25px;
			top: 8px;
			position: relative;
		}
		</style>

	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
				
				<!--begin::Content-->
				<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden mx-auto">
					<!--begin::Content body-->
					<div class="d-flex flex-column-fluid flex-center">

						<!--begin::prelogin-->
						<div class="login-form login-prelogin">
							<!--begin::Form-->
							<?php $attributes = ['class' => 'form','novalidate'=>"novalidate", 'id' => 'kt_login_prelogin_form'];
                            echo form_open('',$attributes); ?>
								<!--begin::Title-->
								<div class="pb-15 pt-lg-0 pt-5 text-center" >
									<img src="<?= $base_url;?>/assets/layouts/auth/media/Awarathon-Logo2020-RedBlack-Crop.png" class="max-h-55px" alt="" />
								</div>
								<div class="form-group ">
									<div><span class="col-md-2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><label class="col-md-10 font-size-h8 font-weight-bolder text-dark ">Are you a Learner?</label></div>
									<div><span class="col-md-2"><i style="color: #db1f48;" class="icon-user font-black sub-title"></i></span><button type="button" id="login_type" name="login_type" class="col-md-10 form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3" onclick="window.location.href='https:/\/pwa.awarathon.com'">Click here to Play</button></div>
									<!-- <div class="input-group"><span class="col-md-2 input-group-prepend"><i style="color: #ffff;background-color: #db1f48;" class="icon-user font-black sub-title input-group-text"></i></span><button type="button" id="login_type" name="login_type" class="col-md-10 form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3" onclick="window.location.href='https:/\/pwa.awarathon.com'">Click here to Play</button></div> -->
                                </div>
								<div class="form-group ">
									<div><span class="col-md-2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><label class="col-md-10 font-size-h8 font-weight-bolder text-dark ">Are you Manager or Admin?</label></div>
									<div><span class="col-md-2"><i style="color: #db1f48;" class="icon-user font-black sub-title"></i></span><a href="<?= $base_url.'login' ?>" class="col-md-10 form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3" >Click here to Give Rating or Create</a></div>
                                </div>
								<!--end::Form group-->
							<?php echo form_close(); ?>
							<!--end::Form-->
						</div>
						<!--end::prelogin-->

						<!--begin::Signin-->
						<div class="login-form login-signin">
							<!--begin::Form-->
							<?php $attributes = ['class' => 'form', 'novalidate'=>"novalidate", 'id' => 'kt_login_signin_form'];
        						echo form_open('login/service',$attributes); ?>
								<!--begin::Title-->
								<div class="pb-15 pt-lg-0 pt-5 text-center">
									<!-- <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg signin-heading">Sign in</h3> -->
									<img src="<?= $base_url;?>/assets/layouts/auth/media/Awarathon-Logo2020-RedBlack-Crop.png" class="max-h-55px" alt="" />
								</div>
								<?php if ($error!==''){ ?>
								<div class="alert alert-danger display-block">
									<button class="close" data-close="alert"></button>
									<span><?php echo $error;?></span>
								</div>
								<?php } ?>
								<!--begin::Title-->
								<!--begin::Form group-->
								<!-- <div style="margin-bottom: 1rem !important;">
										<select class="form-group form-control form-control-solid h-auto py-3 px-3 select2tp" id="login_type" name="login_type" onchange="RedirectToPlay();">
											<option value="1">Manager or Admin</option>
											<option value="2">Learner</option>
										</select>
									<a href="javascript:;" data-toggle="tooltip" data-placement="right" data-html="true" title="<span class='tooltip-bx'><li>Select <b>Manager or Admin</b> For Rating or Create.</li><li>Select <b>Learner</b> For Play.</li></span>"><i style="color: #db1f48;" class="icon-info font-black sub-title"></i>
								    </a>
								</div> -->
								<div class="form-group">
									<label class="font-size-h8 font-weight-bolder text-dark">Username</label>
									<input class="form-control form-control-solid h-auto py-3 px-3" type="text" name="username" id="username" autocomplete="off" value="<?= (isset($member_username) ? $member_username : set_value('username')); ?>" />
								</div>
								<!--end::Form group-->
								<!--begin::Form group-->
								<div class="form-group">
									<div class="d-flex justify-content-between mt-n5 lbl-password">
										<label class="font-size-h8 font-weight-bolder text-dark pt-5">Password</label>
                                    </div>
                                    <small class="password-help">
                                        <a href="javascript:;" id="show-password" onclick="togglePassword();" class=" text-pink font-size-h8 font-weight-bolder text-hover-pink pt-4">Show Password</a>
                                        </small>
									<input class="form-control form-control-solid h-auto py-3 px-3" type="password" name="password" id="txt-password" autocomplete="off" value="<?= (isset($member_password) ? $member_password : set_value('password')); ?>"/>
								</div>
                                <!--end::Form group-->

                                <div class="form-group">
                                    <!--<input class="" type="checkbox" name="remember_me"/>
                                    <label class="font-size-h8 font-weight-bolder text-dark pt-0" style="margin:0px 0px 0px 5px;padding:0px;">Remember me</label> -->
									<label class="rememberme mt-checkbox mt-checkbox-outline">
										<input type="checkbox" id="remember" <?php echo (isset($member_username) ? 'checked': ''); ?> name="remember" value="1" /> Remember me
										<span></span>
									</label>
                                </div>

								<!--begin::Action-->
								<div class="pb-lg-0 pb-1">
									<button type="button" id="kt_login_signin_submit" class="form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3">Log in</button>
								</div>
                                <!--end::Action-->
								<div class="pb-5 form-group">
                                    <a href="javascript:;" class="text-pink font-size-h8 font-weight-bolder text-hover-pink pt-5" id="kt_login_forgot">Forgot Password?</a>
                                </div>
							
							<?php echo form_close(); ?>
							<!--end::Form-->
						</div>
						<!--end::Signin-->

						<!--begin::Forgot-->
						<div class="login-form login-forgot" style="padding: 15px;">
							<!--begin::Form-->
							<?php $attributes = ['class' => 'form','novalidate'=>"novalidate", 'id' => 'kt_login_forgot_form'];
                            echo form_open('login/forget_password',$attributes); ?>
								<!--begin::Title-->
								<div class="pb-15 pt-lg-0 pt-5 text-center" >
									<!-- <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg signin-heading">Sign in</h3> -->
									<img src="<?= $base_url;?>/assets/layouts/auth/media/Awarathon-Logo2020-RedBlack-Crop.png" class="max-h-55px" alt="" />
								</div>
								
								<div class="pb-5 pt-lg-0 pt-5 text-center" >
									<h3 class="font-weight-bolder text-blue-dark font-size-h4 font-size-h1-lg">Forgot Password?</h3>
									<p class="text-blue-light font-weight-bold font-size-h6">We just need your registered Email Id to send you password reset instruction</p>
								</div>
								<!--end::Title-->
								<!--begin::Form group-->
								<div class="alert alert-danger alert-forget" >
									<button class="close" data-close="alert"></button>
									<span class="error-msg"> </span>
								</div>
								<div class="alert alert-success alert-forget" >
									<button class="close" data-close="alert"></button>
									<span class="success-msg"> </span>
								</div>
								<div class="form-group">
									<label class="font-size-h8 font-weight-bolder text-dark">Email Address</label>
									<input class="form-control form-control-solid h-auto py-3 px-3" type="email" name="email" autocomplete="off" />
								</div>
								<!--end::Form group-->
								<!--begin::Form group-->
								<div class="pb-lg-0 pb-5" >
									<button type="button" id="kt_login_forgot_submit" class="form-control btn btn-blue-dark font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3">RESET PASSWORD</button>
								</div>
								<div class="form-group text-center pt-8">
									<a href="<?= $base_url.'login' ?>" class="text-pink font-size-h6 font-weight-bolder text-hover-pink pt-5" ><< Back</a>
									<!-- <a href="javascript:;" id="kt_login_forgot_cancel" class="text-pink font-size-h6 font-weight-bolder text-hover-pink pt-5" ><< Back</a> -->
                                </div>
								<!--end::Form group-->
							<?php echo form_close(); ?>
							<!--end::Form-->
						</div>		
						<!--end::Forgot-->
						<!--begin::Reset password-->
						<div class="login-form login-reset" >
							<!--begin::Form-->
							<?php $attributes = ['class' => 'form', 'novalidate'=>"novalidate", 'id' => 'kt_login_reset_form'];
        						echo form_open('login/reset_password',$attributes); ?>
								<!--begin::Title-->
								<div class="pb-15 pt-lg-0 pt-5 text-center" >
									<!-- <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg signin-heading">Sign in</h3> -->
									<img src="<?= $base_url;?>/assets/layouts/auth/media/Awarathon-Logo2020-RedBlack-Crop.png" class="max-h-55px" alt="" />
								</div>
								<!--begin::Title-->
								<!--begin::Form group-->
								<div class="alert alert-danger alert-forget" >
									<button class="close" data-close="alert"></button>
									<span class="error-msg"> </span>
								</div>
								<div class="form-group">
									<label class="font-size-h8 font-weight-bolder text-dark">New Password</label>
									<input class="form-control form-control-solid h-auto py-3 px-3" type="password" name="new_password" id="new_password" autocomplete="off" value="" />
								</div>
								<!--end::Form group-->
								<!--begin::Form group-->
								<div class="form-group pb-5">
									<label class="font-size-h8 font-weight-bolder text-dark">Confirm Password</label>
									<input class="form-control form-control-solid h-auto py-3 px-3" type="password" name="confirm_password" id="confirm_password" autocomplete="off" value="" />
								</div>
								<input class="form-control" type="hidden" name="user_id" id="user_id" value="<?= isset($user_id) ? $user_id : ''; ?>" />
                                <!--end::Form group-->
								<!--begin::Action-->
								<div class="pb-lg-0 pb-1" >
									<button type="button" id="kt_login_reset_submit" class="form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3" <?= ($error == 'HTTP 429') ? "disabled" : "" ?>>OK</button>
								</div>
								<div class="form-group text-center pt-8">
									<a href="<?= $base_url ?>" class="text-pink font-size-h6 font-weight-bolder text-hover-pink pt-5" ><< Home </a>
									<!-- <a href="javascript:;" id="kt_login_reset_cancel" class="text-pink font-size-h6 font-weight-bolder text-hover-pink pt-5" ><< Home </a> -->
                                </div>
                                <!--end::Action-->
                                
							<?php echo form_close(); ?>
							<!--end::Form-->
						</div>
						<!--end::Reset password-->
						<!--begin::Changed-->
						<div class="login-form login-changed" style="padding: 15px;">
							<!--begin::Form-->
							<?php $attributes = ['class' => 'form','novalidate'=>"novalidate", 'id' => 'kt_login_changed_form'];
                            echo form_open('',$attributes); ?>
								<!--begin::Title-->
								<div class="pb-15 pt-lg-0 pt-5 text-center" >
									<!-- <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg signin-heading">Sign in</h3> -->
									<img src="<?= $base_url;?>/assets/layouts/auth/media/Awarathon-Logo2020-RedBlack-Crop.png" class="max-h-55px" alt="" />
								</div>
								<div class="pb-5 pt-lg-0 pt-5 text-center" >
									<p class="text-blue-light font-weight-bold font-size-h6">Your password has been changed successfully!</p>
								</div>
								<!--end::Title-->
								
								<div class="form-group text-center">
									<a href="<?= $base_url.'login' ?>" class="form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3" >Click here to login</a>
									<!-- <a href="javascript:;" id="kt_login_changed" class="form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3" >Click here to login</a> -->
                                </div>
								<!--end::Form group-->
							<?php echo form_close(); ?>
							<!--end::Form-->
						</div>		
						<!--end::Changed-->
					</div>
					<!--end::Content body-->
					<!--begin::Content footer-->
					<div class="d-flex justify-content-lg-center justify-content-center align-items-end py-7 py-lg-0">
                        <div class="copyright">
							© <?= date('Y') ?>. Awarathon Awareness Initiatives Pvt. Ltd. All rights reserved.
							<br/>
							<a tabindex="0" target="_blank" href="https://awarathon.com/privacy-policy/" class="privacy-policy-text text-pink">Privacy Policy <span><i class="fa fas fa-external-link-alt privacy-policy-icon"></i></span></a>
                        </div>
					</div>
					<!--end::Content footer-->
				</div>
				<!--end::Content-->

				<!--begin::Aside-->
				<div class="login-aside d-flex flex-column flex-row-auto" style="background-color: #004369;">
					<!--begin::Aside Top-->
					<div class="text-center d-flex flex-column-auto flex-column dp-pt-lg-20 pt-15">
						
						<div class="login-aside-left-box">
							<!--begin::Aside title-->
							<h3 class="banner-heading">Pitch Perfect. Always.</h3>
							<h5 class="banner-sub-heading">Our AI-enabled, video roleplay platform ensures <br/>your teams hit the right notes. Consistently.</h5>
							<!--end::Aside title-->

							<!--begin::Know More-->
							<div class="pb-lg-0 pb-5">
								<a target="_blank" href="https://awarathon.com"><button type="button" class="form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3 btn-know-more">KNOW MORE</button></a>
							</div>
							<!--end::Know More-->
						</div>
						
					</div>
					<!--end::Aside Top-->
					
					
				</div>
				<!--begin::Aside-->
			</div>
			<!--end::Login-->
		</div>
		<!--end::Main-->
		<?php 
			$data['is_change'] = isset($is_sign) ? $is_sign : '';
			$this->load->view('auth/signin-js',$data); 
		?>			
	</body>
	<!--end::Body-->
</html>