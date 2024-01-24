<?php $base_url = base_url(); ?>
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

			<!--begin::Aside-->
			<div class="signup-aside d-flex flex-column flex-row-auto" style="background-color: #004369;">
				<!--begin::Aside Top-->
				<div class="d-flex flex-column-auto flex-column  pt-15">
					<div class="signup-blockquote">
						<blockquote>
							Sales training initiatives in a healthcare organization like ours are critical due to the complexity of our offering itself, mixed with regulatory and legal requirements. So the ability to train sales teams virtually and practice on these parameters is useful for us. Awarathon's competency maps have been especially helpful to assess what isn’t working as well as what corrective steps need to be taken.
						</blockquote>
						<p class="blockquote-author">Arjun Udani<br /><span>Executive Director, Me Cure Group</span></p>
					</div>
				</div>
				<!--end::Aside Top-->

				<!--begin::Aside Bottom-->

				<!-- <div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center signup-aside-img" style="background-image: url(<?= $base_url; ?>/assets/media/bg/signup-aside-bottom-brand.png);min-height: 300px !important;background-size: 340px;">
					<div class="footer-text">Trusted by leading brands Globally.</div>
				</div> -->
				<div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center signup-aside-img">
					<div class="d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center signup-aside-brand-img"></div>
					<div class="footer-text">Trusted by leading brands Globally.</div>
				</div>
				<!--end::Aside Bottom-->
			</div>
			<!--begin::Aside-->

			<!--begin::Content-->
			<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden mx-auto">
				<div class="steps">
					Step <?= $Step; ?> of 3
				</div>
				<div class="d-flex flex-column-fluid flex-center " style="    margin-bottom: 40%;">
					<!--begin::Signin-->
					<div class="login-form login-signin">
						<div class="pb-15 pt-lg-0 pt-5 text-center">
							<img src="<?= $base_url; ?>/assets/media/logos/Awarathon-Logo2020-RedBlack-Crop.png" class="max-h-55px" alt="" />
						</div>
						<?= view('layout/notifications') ?>
						<?php if ($Step == 3) {
							$attributes = ['class' => 'form', 'novalidate' => "novalidate", 'id' => 'kt_login_signup_form'];
							if($company_set['company_name'] !=''){?>

							<!--begin::Form-->
							<?php echo form_open('signup/complete_verification', $attributes); ?>
							<!--begin::Title-->
							<div class="pb-13 pt-lg-0 pt-5">
								<h3 class="font-weight-bolder text-dark font-size-h5 font-size-h2-lg signin-heading"><?= esc($Message) ?></h3>
							</div>
							<!--begin::Title-->
							<!--begin::Form group-->
							<div class="form-group">
								<label class="font-size-h8 font-weight-bolder text-dark star"><?= lang('Auth.email') ?></label>
								<input type="hidden" name="company_id" value="<?= base64_encode($company_set['id']) ?>">
								<input type="hidden" name="user_id" value="<?= base64_encode($company_user_set['user_id']); ?>">
								<input readonly class="form-control form-control-solid h-auto py-3 px-3" type="email" id="email" name="email" value="<?= $company_user_set['email'] ?>" autocomplete="off" />
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
								<button type="button" id="kt_login_signup_submit" class="form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3"><?= lang('Auth.finishup') ?></button>
							</div>
							<div class="text-center pb-lg-0 pb-5 terms-service">
								<p class="font-weight-normal font-size-sm text-dark">By creating a Awarathon account, you're agreeing to accept the <a class="text-pink" target="_blank" href="https://awarathon.com/terms-conditions/">Awarathon Customer Terms of Service</a>.</p>
							</div>
							<!--end::Action-->
							<?php echo form_close(); 
							} else { ?>
							<!--begin::Form-->
							<?php echo form_open('signup/company-domain', $attributes); ?>
							<!--begin::Title-->
							<div class="pb-13 pt-lg-0 pt-5">
								<h3 class="font-weight-bolder text-dark font-size-h5 font-size-h2-lg signin-heading"><?= esc($Message) ?></h3>
							</div>
							<!--begin::Title-->
							<!--begin::Form group-->
							<div class="form-group">
								<label class="font-size-h8 font-weight-bolder text-dark star"><?= lang('Auth.email') ?></label>
								<input type="hidden" name="company_id" value="<?= base64_encode($company_set['id']) ?>">
								<input type="hidden" name="user_id" value="<?= base64_encode($company_user_set['user_id']); ?>">
								<input readonly class="form-control form-control-solid h-auto py-3 px-3" type="email" id="email" name="email" value="<?= $company_user_set['email'] ?>" autocomplete="off" />
							</div>
							<div class="form-group">
								<label class="font-size-h8 font-weight-bolder text-dark star"><?= lang('Auth.CompanyName') ?></label>
								<?php $TArray =explode(".",$company_set['company_domain']); ?>
								<input class="form-control form-control-solid h-auto py-3 px-3" type="text" id="company_name" name="company_name" value="<?=$TArray[0];?>" autocomplete="off" />
							</div>
							<div class="form-group">
								<label class="font-size-h8 font-weight-bolder text-dark star"><?= lang('Auth.PhonNo') ?></label>
								<input class="form-control form-control-solid h-auto py-3 px-3" type="number" id="phone_no" name="phone_no" autocomplete="off" />
							</div>
							<!--end::Form group-->
							<!--begin::Action-->
							<div class="pb-lg-0 pb-5">
								<button type="button" id="kt_login_signup_submit" class="form-control btn btn-pink font-weight-bolder font-size-h6 px-8 py-3 my-3 mr-3"><?= lang('Auth.finishup') ?></button>
							</div>
							<!--end::Action-->
							<?php echo form_close(); 
							}  ?>
							<!--end::Form-->
						<?php } else {
							$attributes = ['id' => 'resend_email_form']; ?>
							<?php echo form_open('signup/resend', $attributes); ?>
							<input type="hidden" name="company_id" value="<?= base64_encode($company_set['id']) ?>">
							<input type="hidden" name="user_id" value="<?= base64_encode($company_user_set['user_id']); ?>">
							<div class="text-center pb-10">
								<!-- <img alt="" src="<?//echo $base_url;?>/assets/media/svg/email-confirmation.svg"/> -->
								<svg width="172" height="138" viewBox="0 0 172 138" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
									<rect width="172" height="138" fill="url(#pattern0)" />
									<defs>
										<pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
											<use xlink:href="#image0" transform="translate(0 -0.123188) scale(0.00195312 0.00243433)" />
										</pattern>
										<image id="image0" width="512" height="512" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAIACAYAAAD0eNT6AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAAF1FJREFUeJzt3WuQ3XV9x/HvObsRQsItxABpJbQjaL2USyBc1KpTQKQzDkrpA6vWTjtjlTrVCigKQrX1Uq+ACiregVqEOto+sSqIQYEQDEKrtQUU5FISRqiIkGw4pw/CwSRsds/td/7//+/3ej3iyf7Pd6Ps501OshsBAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACbUm9DrtQw4/+pSI7itane4eE3pNAGiMbrv1YKvVvvwHa753QUR0Ur9e8gA49NDnHdld0P1Guxu7pX4tAMjA/7U6m45du3btDSlfJGkAHHHEEQfMPNb6Uavdnk75OgCQk07ETMzsdMC6dVfdkeo12qkeHBGxqTv1L8YfAAbTjljQmt741cSvkcbKlSt3aXfj2ameDwA5a7W6B5988slPSfX8ZAHQai08PFoT+0OGAJCZVusnd965KtXTEwZALEz1bAAowfRj7YNSPTtZAHS77ftSPRsAStBeEFcne3aqB3e7M4+kejYAlOCxbvfRVM9O+rcAAIB6EgAAUKBG/B39F/72rnH0vourPgMA5rX67ofimnt+VfUZ82pEAFx776/ipKfvGUcvFwEA1Nfqux+K6//34arP6Esj3gLY9Fg3Tl398/h+A4oKgDKtvvuhOH31XTHT6VZ9Sl8aEQARIgCA+mra+Ec0KAAiRAAA9dPE8Y9oWABEiAAA6qOp4x/RwACIEAEAVK/J4x9RkwBotQb/mUEiAICqjDL+Q0xeErUIgL2eujSmFywY+ONEAACTNsr4L3jKgthz6dIEVw2uFgGwcOHCOO2st4sAAGpt1PE//eyzYuedd05w2eBqEQARESuPWCUCAKitcYz/IYetTHDZcGoTABEiAIB6ym38I2oWABEiAIB6yXH8I2oYABEiAIB6yHX8I2oaABEiAIBq5Tz+ETUOgAgRAEA1ch//iJoHQIQIAGCyShj/iAYEQIQIAGAyShn/iIYEQIQIACCtksY/okEBECECAEijtPGPaFgARIgAAMarxPGPaGAARIgAAMaj1PGPaGgARIgAAEZT8vhHNDgAIkQAAMMpffwjGh4AESIAgMEY/y0aHwARIgCA/hj/38giACJEAABzM/7byiYAIkQAALMz/k+WVQBEiAAAtmX8Z5ddAESIAAC2MP47lmUARIgAgNIZ/7llGwARIgCgVMZ/flkHQIQIACiN8e9P9gEQIQIASmH8+1dEAESIAIDcGf/BFBMAESIAIFfGf3BFBUCECADIjfEfTnEBECECAHJh/IdXZABEiACApjP+oyk2ACJEAEBTGf/RFR0AESIAoGmM/3hMV31AHfQi4APvfk9snpkZ6GM3PdaNU7/78/jH5QfEUYv3SHQhABER1/zqwXjb3XfHTNf4j0oAPG6kCOh04/S7bo33/eLgOHLj0kQXApTtezttiLcvuTVmojPwxxr/Jyv+LYCtjfR2QHTibUtuiut2uj/BZQBl2zL+PzT+YyQAtiMCAOrF+KchAGYhAgDqwfinIwB2QAQAVMv4pyUA5iACAKph/NMTAPMQAQCTZfwnQwD0QQQATIbxnxwB0CcRAJCW8Z8sATAAEQCQhvGfPAEwIBEAMF7GvxoCYAgiAGA8jH91ahEArVar6hMGJgIARlPq+Ndl8WoRANPtdiycbt7PJRIBAMMpdfwXLlgQ0+1aTG89AiAiYtmiRSIAoAAlj/+yRYuqPuMJtQmAVqslAgAyV/r41+W3/yNqFAARIkAEADkz/vVSqwCIEAEiAMiR8a+f2gVAhAgQAUBOjH891TIAIkSACAByYPzrq7YBECECRADQZMa/3modABEiQAQATWT866/2ARAhAkQA0CTGvxkaEQARIkAEAE1g/JujMQEQIQJEAFBnxr9ZGhUAESJABAB1ZPybp3EBECECRABQJ8a/mRoZABEiQAQAdWD8m6uxARAhAkQAUCXj32yNDoAIESACgCoY/+ZrfABEiAARAEyS8c9DFgEQIQJEADAJxj8f2QRAhAgQAUBKxj8vWQVAhAgQAUAKxj8/2QVAhAgQAcA4Gf88ZRkAESJABADjYPzzlW0ARIgAEQCMwvjnLesAiBABIgAYhvHPX/YBECECRAAwCONfhiICIEIEiACgH8a/HMUEQIQIEAHAXIx/WYoKgAgRIAKA2Rj/8hQXABEiQAQAWzP+ZSoyACJEgAgAIox/qeMfUXAARIgAEQBlM/5lKzoAIkSACIAyGX+KD4AIESACoCzGnwgB8AQRIAKgBMafHgGwFREgAiBnxp+tCYDtiAARADky/mxPAMxCBIgAyInxZzYCYAdEgAiAHBh/dkQAzEEEiABoMuPPXATAPESACIAmMv7MRwD0QQSIAGgS408/BECfRIAIgCYw/vRLAAxABIgAqDPjzyAEwIBEgAiAOjL+DEoADEEEiACoE+PPMATAkESACIA6MP4MSwCMQASIAKiS8WcUAmBEIkAEQBWMP6MSAGMgAkQATJLxZxwEwJiIABEAk2D8GRcBMEYiQARASqWO/y7GPwkBMGYiQARACiWP/1ONfxICIAERIAJgnIw/KQiARESACIBxMP6kIgASEgEiAEZh/ElJACQmAkQADMP4k5oAmAARIAJgEMafSRAAEyICRAD0w/gzKQJggkSACIC5GH8mSQBMmAgQATAb48+kCYAKiAARAFsz/lRBAFREBIgAiDD+xr86AqBCIkAEUDbjT5UEQMVEgAigTMafqgmAGhABIoCyGH/qQADUhAgQAZTB+FMXAqBGRIAIIG/GnzoRADUjAkQAeTL+1I0AqCERIALIi/GnjgRATYkAEUAejD91JQBqTASIAJrN+FNnAqDmRIAIoJmMP3UnABpABIgAmsX40wQCoCFEgAigGYw/TSEAGkQEiADqzfjTJAKgYUSACKCejD9NIwAaSASIAOrF+NNEAqChRIAIoB6MP00lABpMBIgAqmX8aTIB0HAiQARQDeNP0wmADIgAEcBkGX9yIAAyIQJEAJNh/MmFAMiICBABpGX8yYkAyIwIEAGkYfzJjQDIkAgQAYyX8SdHAiBTIkAEMB7Gn1wJgIyJABHAaIw/ORMAmRMBIoDhGH9yJwAKIAJEAIMx/pRAABRCBIgA+mP8KYUAKIgIEAHMzfhTEgFQGBEgApid8ac0AqBAIkAEsC3jT4kEQKFEgAhgC+NPqQRAwUSACCid8adkAqBwIkAElMr4UzoBgAgQAcUx/iAAeJwIEAGlMP6whQDgCSJABOTO+MNvCAC2IQJEQK6MP2xLAPAkIkAE5Mb4w5MJAGYlAkRALow/zE4AsEMiQAQ0nfGHHRMAzEkEiICmMv4wNwHAvESACGga4w/zEwD0RQSIgKYw/tAfAUDfRIAIqDvjD/0TAAxEBIiAujL+MBgBwMBEgAioG+MPgxMADEUEiIC6MP4wHAHA0ESACKia8YfhCQBGIgJEQFWMP4xGADAyESACJs34w+gEAGMhAkTApBh/GA8BwNiIABGQmvGH8REAjJUIEAGpGH8YLwHA2IkAETBuxh/GTwCQhAgQAeNi/CENAUAyIkAEjMr4QzoCgKREgAgYlvGHtAQAyYkAETAo4w/pCQAmQgSIgH4Zf5gMAcDEiAARMB/jD5MjAJgoESACdsT4w2QJACZOBIiA7Rl/mDwBQCVEgAjoMf5QDQFAZUSACDD+UB0BQKVEQLkRYPyhWgKAyomA8iLA+EP1BAC1IALKiQDjD/UgAKgNEZB/BBh/qA8BQK2IgHwjwPhDvQgAakcE5BcBxh/qRwBQSyIgnwgw/lBPAoDaEgHNjwDjD/UlAKg1EdDcCDD+UG8CgNoTAc2LAOMP9ScAaAQR0JwIMP7QDAKAxhAB9Y8A4w/N0byvpBStFwHrH344Htm8uepzBtKLgA+8+z2xeWZmoI/tRcD7fnFwHLlxaaILR2P86+Wee++Na675fqzfsCG63W7V5zTGVLsdK1bsF3/wghfErrsurvqcpAQAjSMC6hcBxr8+ZmZm4iPnnh+XX/HV6HQG/9+DLXbZZZd489+8MV5+4suqPiUZbwHQSN4OqM/bAca/PrrdbrzznHfFZV+5wviP6Ne//nX8w3vfH5ddfkXVpyQjAGgsEVB9BBj/ern6u6vjm9+6suozsvLRcz8WG+6v/t+1FAQAjSYCqvvCZPzr5+v/+m9Vn5CdTZs2xTe+8c2qz0hCANB4ImDyEWD86+nWW2+v+oQs/c+tt1V9QhICgCyIgMlFgPGvr/aUL+kpTLXz/HXN87OiSCIgfQQY/3p7xoEHVn1Clp75zGdUfUISAoCsiIB0EWD86++kV5xY9QnZWbRoUbzkuGOqPiMJAUB2RMD4I8D4N8Oqww+LPzn5pKrPyEar1Yp3nPHW2H333as+JQkBQJZEwPgiwPg3y6l/+6b46zf8VSxcuHPVpzTasmXL4sMffH8cd+wfVn1KMs376gh98h0DR/+Ogca/edrtdrz2z14df3zSy+OGtTfGffetj07XNwXq1/T0dOy/YkUcesjBMd3A/4AYRN6fHcUTAcNHgPFvtsWLF8eLX/TCqs+gxrwFQPa8HTD42wHGH/InACiCCOg/Aow/lEEAUAwRMH8EGH8ohwCgKCJgxxHw+UW3xelL1hl/KIQAoDgiYNsI6EY3ztrjh/Gp3W6L7hA3GX9oJgFAkUTAlgjoRjfesefN8e2F9w11i/GH5hIAFKv0CLh2p/vjHXveHN/Z2fhDiQQARSs5At6y5AfGHwomACheqREwLOMPeRAAECKgX8Yf8iEA4HEiYG5T09PGHzIiAGArImB2rVYrTj/7TOMPGREAsB0RsK1WtOKUU98cK1cdPrZnTorxhx0TADALEbBFK1rxhtPeFC86pnk/E934w9wEAOxA0yPg1DPPiFZ7+H/Fe+P/4mOOGeNlk2H8YX4CAObQ1Ajodrvx4//8UXQ7g39f/wjjDyVo1lc1qEAvAtY//HA8snlz1efMq9vtxsWf/Xx87bLLh/p445+HTqcTP/3pz2L9hg3R7Q7zUx7KNDU1FSv22y/22Wfvqk9JTgBAH5oSAcbf+EdEfPvKq+L8j18Qd911d9WnNNYhBx8Up73lzXHggQdUfUoy3gKAPtX97QDjb/wjIi659Mvx1jPONP4jWnfTD+PP//J18YN1N1V9SjICAAZQ1wgYdfynpqbiLWedYfwb7ic/+e849/yPV31GNjZu3BhnnnVOPProo1WfkoQAgAHVLQJGHf899tgjzr3owjjy+c8b82XpGf9tffmyr0RnyD/4yezWb9gQ3/zWlVWfkYQAgCHUJQJGHf+nLlsW7znvw7HP8uVjviw94/9kN9/yH1WfkKWbb7ml6hOSEAAwpKojYBzj/3cffF8s27t5f9rZ+M9u48aNVZ+QpVx/XQUAjKCqCDD+xn82+z3taVWfkKVcf10FAIxo0hHQ7Xbj4s98zvjzJMe/5NiqT8hOu92O445t3h+O7YcAgDGYVAQ8Mf5fuWKojzf+efujE14aBx30+1WfkZVXv+qVsd9+fgcAmEPqCDD+xn8+U1NT8ZEP/WMcdeQRVZ/SeK1WK17zqlfGKa9/XdWnJFOPv8cEmUj1HQONv/Hv12677hrnffRDccPaG+O7q6+J++5b768GDmB6ejr2339FvOS4Y+N3f2f/qs9JSgDAmI07Aoy/8R9Uq9WKVYcfFqsOP6zqU6gxbwFAAuN6O8D4G39IRQBAIqNGgPE3/pCSAICEho0A42/8ITUBAIkNGgGjjv+yvfc2/sC8BABMQL8RMI7xP+cD7zX+wLwEAEzIfBFg/I0/TJIAgAnaUQQYf+MPkyYAYMK2jwDjb/yhCgIAKtCLgJ2npox/1YdAoQQAVOiyz3/R+AOVEABQgW63G+d/7BPxxYsvHerjjT8wKgEAE2b8jT/UgQCACTL+xh/qQgDAhBh/4w91IgBgAoy/8Ye6EQCQmPE3/lBHo/2wcmBO3W43zjv/E/GlS4Yb/7333jvONv40VKfTic2bN1d9xkDa7XZMD/kjvJumjM8SKjDq+C/fd9+44BPnxfRuu8UjDfsiavzLtmbNDfGFL10S6266KTZtmqn6nIEtX75vnPDS4+M1r/7T2GXhwqrPScZbAJDAOMb/wgvOj99avnygHyVcB8a/bBd95nPxhje+Ka5fc0Mjxz8i4p577o2LPvO5eM1r/yLWb9hQ9TnJCAAYs3GN//J9942I/n+UcB0Y/7JdedXVceGnLqr6jLH52c/uiLe9/azodDpVn5KEAIAxGvf49zQhAow/n/x0PuPfc/PNt8R116+p+owkBACMSarx76lzBBh/Ntx/f9x22+1Vn5HEtddeX/UJSQgAGIPU499Txwgw/kREPPDAg1WfkMwDD+b5uQkAGNE4xv+TF35s3vHvqVMEGH96lu61pOoTklm6dK+qT0hCAMAIxjX+++6zz0AfV4cIMP5sbcmSJfGsZ/1e1Wck8YLnP6/qE5IQADCkqsa/p8oIMP7M5pTXv67qE8bu6KOOjEMPObjqM5IQADCEqse/p4oIMP7syBGrDo8z3npatNt5TMtzn/Ps+Pt3nR2tVp7/b6/+TURomLqMf08vAtY//HDy7xho/JnPSa84MZ773OfExZdcGmtvXBcPPfTLqk8ayPT0dOy/YkWc8NLj4+Unvizrbwuc72cGCdRt/HsmEQHGn34deMDT413nvLPqM5hHHr9PAxNQ1/HvSfl2gPGH/AgA6EPdx78nRQQYf8iTAIB5NGX8e8YZAcYf8iUAYA5NG/+ecUSA8Ye8CQDYgaaOf88oEWD8IX8CAGbR9PHvGSYCjD+UQQDAdnIZ/55BIsD4QzkEAGyl2+3Gued/PJvx7+knAow/lEUAwON643/xJf801MfXdfx75ooA4w/lEQAQ+Y9/z2wRYPyhTAKA4pUy/j1bR4Dxh3L5WQAUrbTx72m1WrFs8eIt/1zxLUA1BADFKnX8eww/lM1bABSp9PEHEAAUx/gDCAAKM/L4Lzf+QB4EAMUYy/hfYPyBPAgAimD8AbYlAMie8Qd4MgFA1ow/wOwEANky/gA7JgDIkvEHmJsAIDvGH2B+AoCsGH+A/ggAsmH8AfonAMiC8QcYjACg8Yw/wOAEAI1m/AGGIwBotK9+7evGH2AI01UfEBFx++0/jUu//M9Vn0HDdDqd+NSnPzvUx+62+25xwgnHx1XfuXrMVwHM7Y477qz6hIiIaKV68MqVRz0zplo/TvV8AMhdp905YN11192a4tneAgCAAgkAACiQAACAAgkAAChQsgBotaY2pXo2AJRgavOCjamenSwAFi2aurPT6TyU6vkAkLNOJ36xdu3qu1M9P1kAXH311ZtbrdYXUj0fAHLWbsVnI6KT7PmpHhwRMbPT1Ds70bkt5WsAQG46Ef811dr87pSvkTQAbrnmmgc67XhhdOLKlK8DALnodDr//pTWYy9as2bNL1O+TrLvBLj96xy66nlHRMSLu9Hda0KvCQCN0e52749O98obb7zuhojoVn0PAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAjOT/Afy7qMqQso9UAAAAAElFTkSuQmCC" />
									</defs>
								</svg>


							</div>
							<div class="pb-13 pt-lg-0 pt-5 text-center">
								<h3 class="font-weight-bolder text-dark font-size-h5 font-size-h2-lg"><?= lang('Auth.checkemail') ?></h3>
								<p class="text-dark font-weight-normal font-size-h7">We've sent an email to <strong class="text-pink"><?= $company_set['email'] ?></strong></p>
								<p class="text-dark font-weight-normal font-size-h7 py-10">Click the link in the email to confirm your address and activate your account.</p>
								<a href="javascript:;" class="text-pink font-size-h8 font-weight-bolder text-hover-pink pt-5" onclick="resend_email();">Click here</a> if you can't see the email.
							</div>
							<?php echo form_close(); ?>
						<?php } ?>
					</div>
					<!--end::Signin-->

				</div>
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