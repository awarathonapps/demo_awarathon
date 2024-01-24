<?php $base_url= base_url(); ?>
<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
		<?= view('auth/signin-head'); ?>
		<link href="<?=$base_url.'/assets/css/google-btn.css';?>" rel="stylesheet" type="text/css" />
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
				
				<!--begin::Aside-->
				<div class="signup-aside d-flex flex-column flex-row-auto" style="background-color: #004369;">
					<!--begin::Aside Top-->
					<div class="d-flex flex-column-auto flex-column pt-15">
						<div class="signup-blockquote">
							<blockquote>
								Sales training initiatives in a healthcare organization like ours are critical due to the complexity of our offering itself, mixed with regulatory and legal requirements. So the ability to train sales teams virtually and practice on these parameters is useful for us. Awarathon's competency maps have been especially helpful to assess what isn’t working as well as what corrective steps need to be taken.
							</blockquote>
							<p class="blockquote-author">Arjun Udani<br/><span>Executive Director, Me Cure Group</span></p>
						</div>
					</div>
					<!--end::Aside Top-->

					<!--begin::Aside Bottom-->
					
					<div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center signup-aside-img">
						<div class="d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center signup-aside-brand-img"></div>
						<div class="footer-text">Trusted by leading brands Globally.</div>
					</div>
					<!--end::Aside Bottom-->
				</div>
				<!--begin::Aside-->
				
				<!--begin::Content-->
				<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
					
					<!--begin::Content body-->
					<div class="d-flex flex-column-fluid flex-center">
						<!--begin::Signin-->
						<div class="login-form login-signin">
							<!--begin::Form-->
							    <?php $attributes = ['class' => 'form','novalidate'=>"novalidate", 'id' => 'kt_login_signup_form'];
								echo form_open('signup',$attributes); ?>
								<!--begin::Title-->
								<div class="pb-15 pt-lg-0 pt-5 text-center">
									<!-- <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg signin-heading">Sign in</h3> -->
									<img src="<?= $base_url;?>/assets/media/logos/AargonLogo-black-cropped.jpg" class="max-h-55px" alt="" />
								</div>
								<!--begin::Title-->
								<div class="pb-5 pt-lg-0 pt-5">
									<h3 class="font-weight-bolder font-size-h4 font-size-h1-lg signin-heading">Sign up for a 15-day free trial</h3>
									<h6 class="font-weight-normal font-size-h6 font-size-h6-lg signup-sub-heading">Our AI-enabled, video roleplay platform ensures your sales teams hit the right notes. Consistently.</h6>
								</div>
								<!--begin::Title-->
                                <?= view('layout/notifications') ?>
								<!--begin::Form group-->
								<div class="form-group">
									<label class="font-size-h8 font-weight-bolder text-dark star">First name</label>
									<input class="form-control form-control-solid h-auto py-3 px-3" type="text" id="first_name" name="first_name" autocomplete="off" />
								</div>
								<div class="form-group">
									<label class="font-size-h8 font-weight-bolder text-dark star">Last name</label>
									<input class="form-control form-control-solid h-auto py-3 px-3" type="text" id="last_name" name="last_name" autocomplete="off" />
								</div>
								<div class="form-group">
									<label class="font-size-h8 font-weight-bolder text-dark star"><?= lang('Auth.email') ?></label>
									<input class="form-control form-control-solid h-auto py-3 px-3" type="email" id="email" name="email" autocomplete="off" onchange="get_domain();"  />
								</div>
								<input class="form-control form-control-solid h-auto py-3 px-3" type="hidden" id="company_domain" name="company_domain" autocomplete="off" />
								<div class="form-group">
									<label class="font-size-h8 font-weight-bolder text-dark star"><?= lang('Auth.CompanyName') ?></label>
									<input class="form-control form-control-solid h-auto py-3 px-3" type="text" id="company_name" name="company_name" autocomplete="off" />
								</div>
								<div class="form-group">
									<label class="font-size-h8 font-weight-bolder text-dark star"><?= lang('Auth.PhonNo') ?></label>
									<input class="form-control form-control-solid h-auto py-3 px-3" type="text" id="phone_no" name="phone_no" autocomplete="off" />
								</div>
								<!--end::Form group-->
								<!--begin::Action-->
								<div class="pb-lg-0 pb-5">
									<button type="button" id="kt_login_signup_submit" class="form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3">Signup for free</button>
								</div>
								<div class="form-group text-center pt-1">
                                    Have an account?&nbsp;<a href="<?= $base_url;?>" class="text-pink font-size-h8 font-weight-bolder text-hover-pink pt-5" ><?= lang('Auth.signin') ?></a>
                                </div>
								<hr>
                                <?php if($googel_url !=''){ ?>
                                    <div class="pb-lg-0 pb-5">
                                        <div class="gSignInWrapper">
                                            <a href="<?php echo $googel_url; ?>"  class="btn google-button  font-weight-bolder font-size-h6 ">
                                              <div class="GoogleOAuthButtonstyles">
                                                <img version="dark" alt="" class="google-icon" src="<?=$base_url;?>/assets/media/logos/google.svg">
                                              </div>
                                              <span class="buttonText">Sign up with Google</span>
                                            </a>
                                        </div>    
                                    </div>
                                <?php } ?>
                                <br/>
								<div class="text-center pb-lg-0 pb-5 terms-service">
									<p class="font-weight-normal font-size-sm text-dark">By creating a Awarathon account, you're agreeing to accept the <a class="text-pink" target="_blank" href="https://awarathon.com/terms-conditions/">Awarathon Customer Terms of Service</a>.</p>
								</div>
                                <!--end::Action-->
							<?php echo form_close(); ?>
							<!--end::Form-->
							<br>
							<div class="separator separator-dashed"></div>
						</div>
						<!--end::Signin-->
						
					</div>
					<!--end::Content body-->
					<!--begin::Content footer-->
					
					<div class="d-flex justify-content-lg-center justify-content-center align-items-end py-7 py-lg-0">
                        <div class="text-justify font-weight-normal font-size-sm text-dark">
							We're committed to your privacy. Awarathon uses the information you provide to us to contact you about our relevant content, products, and services. You may unsubscribe from these communications at any time. For more information, check out our <a tabindex="0" target="_blank" href="https://awarathon.com/privacy-policy/" class="privacy-policy-text text-pink">Privacy Policy <span><i class="fa fas fa-external-link-alt privacy-policy-icon"></i></span></a>.
                        </div>
					</div>
					<!--end::Content footer-->
				</div>
				<!--end::Content-->
			</div>
			<!--end::Login-->
		</div>
		<!--end::Main-->
		
		<?= view('auth/signin-js'); ?>
	</body>
	<!--end::Body-->
</html>