<?php $base_url= base_url(); ?>
<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
		<?php $this->load->view('auth/signin-head'); ?>
		<link href="<?=$base_url.'/assets/layouts/auth/css/google-btn.css';?>" rel="stylesheet" type="text/css" />
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
									<!-- KRISHNA --- VAPT - ENABLED MAX 3 ATTEMPT FOR LOGIN WITHIN 5 MINUTES -->
									<span><?php echo ($error == 'HTTP 429') ? 'Too many requests. Try again after 5 minutes' : $error;?></span>
								</div>
								<?php } ?>
								<!--begin::Title-->
								<!--begin::Form group-->
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
                                <!--<div class="form-group">
                                    <a href="javascript:;" class="text-pink font-size-h8 font-weight-bolder text-hover-pink pt-5" id="kt_login_forgot">Forgot my password?</a>
                                </div> -->

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
									<button type="button" id="kt_login_signin_submit" class="form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3" <?= ($error == 'HTTP 429') ? "disabled" : "" ?>>Log in</button>
								</div>
                                <!--end::Action-->
                                
							<?php echo form_close(); ?>
							<!--end::Form-->
						</div>
						<!--end::Signin-->
						<!--begin::Forgot-->
						
						<!--end::Forgot-->
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
		
		<?php $this->load->view('auth/signin-js'); ?>

	</body>
	<!--end::Body-->
</html>