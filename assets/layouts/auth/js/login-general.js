"use strict";
var auth2;
// Class Definition
var KTLogin = function() {
    var _login;
    var _btn_signin;
    var _btn_forgot;
    var _btn_signup;
    var _mw_notification;
    var _mw_notification_text;

    var _showForm = function(form) {
        var cls = 'login-' + form + '-on';
        var form = 'kt_login_' + form + '_form';

        _login.removeClass('login-forgot-on');
        _login.removeClass('login-signin-on');
        _login.removeClass('login-signup-on');

        _login.addClass(cls);

        KTUtil.animateClass(KTUtil.getById(form), 'animate__animated animate__backInUp');
    }

    var _handleSignInForm = function() {
        var validation;

        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validation = FormValidation.formValidation(
			KTUtil.getById('kt_login_signin_form'),
			{
				fields: {
					username: {
						validators: {
							notEmpty: {
								message: 'Username is required'
							},
							// emailAddress: {
								// message: 'The value is not a valid email address'
							// }
						}
					},
					password: {
						validators: {
							notEmpty: {
								message: 'Password is required'
							}
						}
					}
				},
				plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    //defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
					bootstrap: new FormValidation.plugins.Bootstrap({
						eleInvalidClass: '',
						eleValidClass: '',
					})
				}
			}
		);

        $('#kt_login_signin_submit').on('click', function (e) {
            e.preventDefault();
            // _mw_notification.style.display  = "none";
           
            validation.validate().then(function(status) {
		        if (status != 'Valid') {
                    setTimeout(function () {
                        KTUtil.btnRelease(_btn_signin);
                        unblock_ui();
                    }, 1000);
                    KTUtil.scrollTop();
				}else{
					$('#kt_login_signin_form').submit();
					
				    // $.ajax({
                        // url: $('#kt_login_signin_form').attr('action'),
                        // type: "POST",
                        // data: $('#kt_login_signin_form').serialize(),
                        // beforeSend: function () {
                            // KTUtil.btnWait(_btn_signin, "spinner spinner-right spinner-white pr-15", "Please wait");
                            // block_ui();
                        // },
                        // success: function(Odata) {
                            // var Data = $.parseJSON(Odata);
                            // if (Data['success']) {
                                // setTimeout(function () {
                                    // KTUtil.btnRelease(_btn_signin);
                                    // unblock_ui();
                                // }, 1000);
                                // window.location.href = Data['redirect_url'];
                            // }else{
                                // setTimeout(function () {
                                    // KTUtil.btnRelease(_btn_signin);
                                    // unblock_ui();
                                // }, 1000);
                                // signOut();
                               // AlertBox(Data['Msg'],"error");
                            // }
                        // },
                        // error: function(xhr, ajaxOptions, thrownError) {
                            // setTimeout(function () {
                                // KTUtil.btnRelease(_btn_signin);
                                // unblock_ui();
                            // }, 1000);
                            // signOut();
                            
                        // }
                    // });
				}
		    });
        });

        // Handle forgot button
        $('#kt_login_forgot').on('click', function (e) {
            e.preventDefault();
            _showForm('forgot');
        });

        // Handle signup
        // $('#kt_login_signup').on('click', function (e) {
        //     e.preventDefault();
        //     _showForm('signup');
        // });
    }

    var _handleSignUpForm = function(e) {
        var validation;
        var form = KTUtil.getById('kt_login_signup_form');

        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validation = FormValidation.formValidation(
			form,
			{
				fields: {
					first_name: {
						validators: {
							notEmpty: {
								message: 'First Name is required'
							}
						}
					},
					last_name: {
						validators: {
							notEmpty: {
								message: 'Last Name is required'
							}
						}
					},
					company_name: {
						validators: {
							notEmpty: {
								message: 'Company Name is required'
							}
						}
					},
					phone_no: {
						validators: {
							notEmpty: {
								message: 'Phone Number is required'
							}
						}
					},
					email: {
                        validators: {
							notEmpty: {
								message: 'Email address is required'
							},
                            emailAddress: {
								message: 'Invalid email address'
							}
						}
					},
                    password: {
                        validators: {
                            notEmpty: {
                                message: 'The password is required'
                            }
                        }
                    },
                    confirm_password: {
                        validators: {
                            notEmpty: {
                                message: 'The password confirmation is required'
                            },
                            identical: {
                                compare: function() {
                                    return form.querySelector('[name="password"]').value;
                                },
                                message: 'The password and its confirm are not the same'
                            }
                        }
                    },
                    agree: {
                        validators: {
                            notEmpty: {
                                message: 'You must accept the terms and conditions'
                            }
                        }
                    },
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap({
						eleInvalidClass: '',
						eleValidClass: '',
					})
				}
			}
		);

        $('#kt_login_signup_submit').on('click', function (e) {
            e.preventDefault();
            // _mw_notification.style.display  = "none";
            var register_form = $('#kt_login_signup_form');
            validation.validate().then(function(status) {
		        if (status == 'Valid') {
                    var url = register_form.attr('action');
                        $.ajax({
                            url: url,
                            type: "POST",
                            data: register_form.serialize(),
                            beforeSend: function () {
                                KTUtil.btnWait(_btn_signup, "spinner spinner-right spinner-white pr-15", "Please wait");
                                block_ui();
                            },
                            success: function(Odata) {
                                var Data = $.parseJSON(Odata);
                                setTimeout(function () {
                                    KTUtil.btnRelease(_btn_signup);
                                    unblock_ui();
                                }, 1000);
                                if(Data['Msg'] !=''){
                                    if (Data['success']) {
                                        AlertBox(Data['Msg'],"success");
                                    }else{
                                        AlertBox(Data['Msg'],"error");
                                    }
                                    // _mw_notification.classList.add("private-alert--danger");
                                    // _mw_notification_text.innerHTML = Data['Msg'];
                                    // _mw_notification.style.display  = "block";
                                }
                                if(Data['isDomainUser']){
                                    register_form[0].reset();
                                    $('.form-control').removeClass('is-valid');
                                }
                                if (Data['redirect_url'] !='') {
                                    window.location.href = Data['redirect_url'];
                                }
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                setTimeout(function () {
                                    KTUtil.btnRelease(_btn_signup);
                                    unblock_ui();
                                }, 1000);
                                AlertBox('Error on reloading the content. Please check your connection and try again.',"error");
                            }
                        });
				} else {
					setTimeout(function () {
                        KTUtil.btnRelease(_btn_signup);
                        unblock_ui();
                    }, 1000);
                    KTUtil.scrollTop();
				}
		    });
        });

    //     // Handle cancel button
    //     $('#kt_login_signup_cancel').on('click', function (e) {
    //         e.preventDefault();

    //         _showForm('signin');
    //     });
    }

    var _handleForgotForm = function(e) {
        var validation;

        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validation = FormValidation.formValidation(
			KTUtil.getById('kt_login_forgot_form'),
			{
				fields: {
					email: {
						validators: {
							notEmpty: {
								message: 'Email address is required'
							},
                            emailAddress: {
								message: 'Enter a valid email address'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
					bootstrap: new FormValidation.plugins.Bootstrap({
						eleInvalidClass: '',
						eleValidClass: '',
					})
				}
			}
		);

        // Handle submit button
        $('#kt_login_forgot_submit').on('click', function (e) {
            e.preventDefault();
            KTUtil.btnWait(_btn_forgot, "spinner spinner-right spinner-white pr-15", "Please wait");
            block_ui();
            validation.validate().then(function(status) {
		        if (status != 'Valid') {
                    // Submit form
                    setTimeout(function () {
                        KTUtil.btnRelease(_btn_forgot);
                        unblock_ui();
                    }, 1000);
                    KTUtil.scrollTop();
				}
		    });
        });

        // Handle cancel button
        $('#kt_login_forgot_cancel').on('click', function (e) {
            e.preventDefault();
            _showForm('signin');
        });
    }
    var google_init = function() {
    var cl = $('meta[name=google-signin-client_id]').attr('content');
        if(cl !=undefined){
            gapi.load('auth2', function(){
              // Retrieve the singleton for the GoogleAuth library and set up the client.
              //console.log(cl);
              auth2 = gapi.auth2.init({
                client_id: cl
              });
              
              if($('#kt_login_signin_form').length){
                auth2.attachClickHandler(document.getElementById('signin-button'), {}, onSignIn, onSignInFailure);
              }
              if($('#kt_login_signup_form').length){
                auth2.attachClickHandler(document.getElementById('signin-button'), {}, ongoogleSignUp, onSignInFailure);
              }
            });
        }
      };
    // Public Functions
    return {
        // public functions
        init: function() {
            _login = $('#kt_login');
            _btn_signin = KTUtil.getById("kt_login_signin_submit");
            // _btn_forgot = KTUtil.getById("kt_login_forgot_submit");
            _btn_signup = KTUtil.getById("kt_login_signup_submit");
            // _mw_notification = KTUtil.getById("mw-notification");
            // _mw_notification_text = KTUtil.getById("mw-notification-text");
            if($('#kt_login_signin_form').length){
                _handleSignInForm();
                google_init();
            }
            if($('#kt_login_signup_form').length){
                _handleSignUpForm();
                google_init();
            }
            // if($('#kt_login_forgot_form').length){
                // _handleForgotForm();
            // }
        }
    };
}();

// Class Initialization
jQuery(document).ready(function() {
    KTLogin.init();
    signOut();
    
});
function togglePassword(){
var paswd= $('#txt-password');
	if(paswd.attr("type")== "password"){
		paswd.attr("type","text");
		$('#show-password').text("Hide Password");
	}
	else{
		paswd.attr("type","password");
		$('#show-password').text("Show Password");
	}
}
function AlertBox(stext,type){
    KTUtil.scrollTop();
    // $('#mw-notification').show();
    // $('#mw-notification-text').html(stext);
    // if(type=='success'){
    //     $("#mw-notification").addClass("private-alert--success");
    // }else{
    //     $("#mw-notification").addClass("private-alert--danger");
    // }
    // setTimeout(function () {
    //     $('#mw-notification').hide();
    // }, 5000);
    
    swal.fire({
        html: stext,
        icon: type,
        buttonsStyling: false,
        confirmButtonText: "Ok!",
        customClass: {
			confirmButton: "btn font-weight-bold btn-light-primary"
		}
    }).then(function() {
		
	});
}

function signOut(){
    $('#mw-notification').hide();
    if (typeof(gapi) == "undefined" || typeof(gapi.auth2) == "undefined"){
        return false;
    }
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
    });
}
function onSignIn(googleUser) {
    signOut();
    var id_token = googleUser.getAuthResponse().id_token;
    $.ajax({
        url: HOST_URL+'/login/google',
        type: "POST",
        data: {'idtoken':id_token},
        beforeSend: function () {
            block_ui();
        },
        success: function(Odata) {
            unblock_ui();
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                window.location.href = Data['redirect_url'];
            }else{
                AlertBox(Data['Msg'],"error");
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            AlertBox('Error on reloading the content. Please check your connection and try again.',"error");
            unblock_ui();
        }
    });
}
function ongoogleSignUp(googleUser) {
    signOut();
    var id_token = googleUser.getAuthResponse().id_token;
    $.ajax({
        url: HOST_URL+'/signup/google',
        type: "POST",
        data: {'idtoken':id_token},
        beforeSend: function () {
            block_ui();
        },
        success: function(Odata) {
            unblock_ui();
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                if(Data['isDomainUser']){
                    signOut();
                }else{
                    window.location.href = Data['redirect_url'];   
                }
            }
            if(Data['Msg'] !=''){
                if(Data['success']){
                    AlertBox(Data['Msg'],"success");
                }else{
                    AlertBox(Data['Msg'],"error");
                }
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            AlertBox('Error on reloading the content. Please check your connection and try again.',"error");
            unblock_ui();
            signOut();
        }
    });
}
function resend_email() {
    $.ajax({
        url: HOST_URL+'/signup/resend_email',
        type: "POST",
        data: $('#resend_email_form').serialize(),
        beforeSend: function () {
            block_ui();
        },
        success: function(Odata) {
            var Data = $.parseJSON(Odata);
            if(Data['Msg'] !=''){
                AlertBox(Data['Msg'],"success");
            }
            if (Data['redirect_url']) {
                window.location.href = Data['redirect_url'];
            }
            unblock_ui();
        },
        error: function(xhr, ajaxOptions, thrownError) {
            AlertBox('Error on reloading the content. Please check your connection and try again.');
            unblock_ui();
            signOut();
        }
    });
}
function onSignInFailure(error){
     console.log(error);
}
function get_domain(){
    var email = $('#email').val();
    if(email !=''){
        var domain = email.substring(email.lastIndexOf("@") +1);
        if(domain !=''){
            $('#company_domain').val(domain);
            var company_name = email.substring(email.lastIndexOf("@")+1,email.lastIndexOf("."));
            $('#company_name').val(company_name);
        }else{
            $('#company_domain').val("");
            $('#company_name').val("");    
        }
    }else{
        $('#company_domain').val("");
        $('#company_name').val("");
    }
}