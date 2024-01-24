<?php $base_url= base_url(); ?>
<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
		<?= view('auth/signin-head'); ?>
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
								<?php 
					                $attributes = ['class' => 'form', 'novalidate' => "novalidate", 'id' => 'kt_login_signup_form']; 
        						echo form_open('reset-password',$attributes); ?>
								<!--begin::Title-->
								<div class="pb-15 pt-lg-0 pt-5 text-center">
									<!-- <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg signin-heading">Sign in</h3> -->
									<img src="<?= $base_url;?>/assets/media/logos/Awarathon-Logo2020-RedBlack-Crop.png" class="max-h-55px" alt="" />
								</div>
								<div class="text-center pb-5">
										<h2 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg"><?= lang('Auth.resetPassword') ?></h2>
									</div>
								<?= view('layout/notifications') ?>
								<!--begin::Title-->
								 <?= csrf_field() ?>
							<!--begin::Form group-->
							<div class="form-group">
								<label class="font-size-h8 font-weight-bolder text-dark star"><?= lang('Auth.email') ?></label>
								 <input type="hidden" name="token" value="<?= $_GET['token'] ?>" />
								<input readonly class="form-control form-control-solid h-auto py-3 px-3" type="email" id="email" name="email" value="<?= $email ?>" autocomplete="off" />
							</div>
							<div class="form-group">
								<label class="font-size-h8 font-weight-bolder text-dark star"><?= lang('Auth.password') ?></label>
								<input class="form-control form-control-solid h-auto py-3 px-3" type="password" id="password" name="password" autocomplete="off" />
							</div>
							<div class="form-group">
								<label class="font-size-h8 font-weight-bolder text-dark star"><?= lang('Auth.passwordAgain') ?></label>
								<input class="form-control form-control-solid h-auto py-3 px-3" type="password" id="confirm_password" name="confirm_password" autocomplete="off" />
							</div>
							<!--end::Form group-->
							<!--begin::Action-->
							<div class="pb-lg-0 pb-5">
								<button type="submit"  class="form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3"><?= lang('Auth.resetPassword') ?></button>
							</div>
							
							<?php echo form_close(); ?>
							<!--end::Form-->
						</div>
						<!--end::Signin-->
					</div>
					<!--end::Content body-->
					<!--begin::Content footer-->
					<div class="d-flex justify-content-lg-center justify-content-center align-items-end py-7 py-lg-0">
                        <div class="copyright">
							© 2020. Awarathon Awareness Initiatives Pvt. Ltd. All rights reserved.
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
							<h5 class="banner-sub-heading">Our AI-enabled, video roleplay platform ensures <br/>your sales teams hit the right notes. Consistently.</h5>
							<!--end::Aside title-->

							<!--begin::Know More-->
							<div class="pb-lg-0 pb-5">
								<button type="button" id="kt_login_forgot_submit" class="form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3 btn-know-more">KNOW MORE</button>
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
		
		<?= view('auth/signin-js'); ?>

	</body>
	<!--end::Body-->
</html>