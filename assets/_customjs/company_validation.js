var frmCompany = $('#frmCompany');
var form_error = $('.alert-danger', frmCompany);
var form_success = $('.alert-success', frmCompany);
frmCompany.validate({
    errorElement: 'span',
    errorClass: 'help-block help-block-error',
    focusInvalid: false,
    ignore: "",
    rules: {
        company_name: {
            required: true,
            companyCheck: true
        },
        portal_name: {
            required: true,
            portalCheck: true,
            Special_CharCheck: true
        },
        industry_type_id: {
            required: true
        },
        otp: {
            required: true
        },
        status: {
            required: true
        },
        email: {
            email: true
        },
        mobile: {
            validateMobile: true
        },
        contact_no: {
            validateContact: true
        },
        contact_person: {
            // validateFax: true
        },
        app_users_count: {
            digits: true
        },
        portal_users_count: {
            digits: true
        },
        pincode: {
            digits: true
        },
        workshop_count: {
            digits: true
        },
        feedback_count: {
            digits: true
        },
        workshop_question_count: {
            digits: true
        },
        feedback_question_count: {
            digits: true
        }
    },
    invalidHandler: function (event, validator) {
        form_success.hide();
        form_error.show();
        if (validator.errorList.length) {
            $('#tabs a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show');
        }
        App.scrollTo(form_error, -200);
    },
    highlight: function (element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function (element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
    success: function (label) {
        label.closest('.form-group').removeClass('has-error');
    },
    submitHandler: function (form) {
        form_success.show();
        form_error.hide();
        Ladda.bind('button[id=company-submit]');
        form.submit();
    }
});

function removeLogo() {
    $('#removeLogo').val(1);
}

var frmSMTP = $('#frmSMTP');
var form_error1 = $('.alert-danger', frmSMTP);
var form_success1 = $('.alert-success', frmSMTP);
frmSMTP.validate({
    errorElement: 'span',
    errorClass: 'help-block help-block-error',
    focusInvalid: false,
    ignore: "",
    rules: {
        authentication: {
            required: true
        },
        host_name: {
            required: true
        },
        alias_name: {
            required: true
        },
        port_no: {
            required: true,
            digits: true
        },
        smtp_secure: {
            required: true
        },
        status: {
            required: true
        },
        user_name: {
            required: true,
            email: true
        },
        password: {
            required: true
        }
    },
    invalidHandler: function (event, validator) {
        form_success1.hide();
        form_error1.show();
        if (validator.errorList.length) {
            $('#tabs a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show');
        }

        App.scrollTo(form_error, -200);
    },
    highlight: function (element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function (element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
    success: function (label) {
        label.closest('.form-group').removeClass('has-error');
    },
    submitHandler: function (form) {
        form_success1.show();
        form_error1.hide();
        Ladda.bind('button[id=mailsettings-submit]');
        form.submit();
    }
});
var frmSetting = $('#frmSetting');
var form_error2 = $('.alert-danger', frmSetting);
var form_success2 = $('.alert-success', frmSetting);
frmSetting.validate({
    errorElement: 'span',
    errorClass: 'help-block help-block-error',
    focusInvalid: false,
    ignore: "",
    rules: {
        app_users_count: {
            digits: true
        },
        portal_users_count: {
            digits: true
        },
        workshop_count: {
            digits: true
        },
        feedback_count: {
            digits: true
        },
        workshop_users_count: {
            digits: true
        },
        workshop_question_count: {
            digits: true
        },
        feedback_question_count: {
            digits: true
        }
    },
    invalidHandler: function (event, validator) {
        form_success2.hide();
        form_error2.show();
        if (validator.errorList.length) {
            $('#tabs a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show');
        }
        App.scrollTo(form_error, -200);
    },
    errorPlacement: function (error, element) {
        if (element.hasClass('form-group')) {
            error.appendTo(element.parent().find('.has-error'));
        } else if (element.parent('.form-group').length) {
            error.appendTo(element.parent());
        } else {
            error.appendTo(element);
        }
    },
    highlight: function (element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function (element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
    success: function (label) {
        label.closest('.form-group').removeClass('has-error');
    },
    submitHandler: function (form) {
        form_success2.show();
        form_error2.hide();
        Ladda.bind('button[id=settings-submit]');
        form.submit();
    }
});
var frmMinute = $('#frmMinute');
var form_error3 = $('.alert-danger', frmMinute);
var form_success3 = $('.alert-success', frmMinute);
frmMinute.validate({
    errorElement: 'span',
    errorClass: 'help-block help-block-error',
    focusInvalid: false,
    ignore: "",
    rules: {
        'from_date[]': {
            required: true
        },
        'to_date[]': {
            required: true
        },
        'billed_min[]': {
            required: true,
            min:1
        },
        'allocated_min[]': {
            required: true,
            min:1,
        }
    },
    invalidHandler: function (event, validator) {
        form_success3.hide();
        form_error3.show();
        if (validator.errorList.length) {
            $('#tabs a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show');
        }
        App.scrollTo(form_error, -200);
    },
    highlight: function (element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function (element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
    success: function (label) {
        label.closest('.form-group').removeClass('has-error');
    },
    submitHandler: function (form) {
        form_success3.show();
        form_error3.hide();
        Ladda.bind('button[id=minute-submit]');
        form.submit();
    }
});
$(".select2, .select2-multiple").change(function () {
    $(this).valid();
});

function DatatableUsersRefresh() {
    var table = $('#UsersTable');
    table.dataTable({
        destroy: true,
        "language": {
            "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            },
            "emptyTable": "No data available in table",
            "info": "Showing _START_ to _END_ of _TOTAL_ records",
            "infoEmpty": "No records found",
            "infoFiltered": "(filtered1 from _MAX_ total records)",
            "lengthMenu": "Show _MENU_",
            "search": "Search:",
            "zeroRecords": "No matching records found",
            "paginate": {
                "previous": "Prev",
                "next": "Next",
                "last": "Last",
                "first": "First"
            }
        },
        "bStateSave": false,
        "lengthMenu": [
            [5, 10, 15, 20, -1],
            [5, 10, 15, 20, "All"]
        ],
        "pageLength": 10,
        "pagingType": "bootstrap_full_number",
        "columnDefs": [
            {'width': '30px', 'orderable': false, 'searchable': false, 'targets': [0]},
            {'width': '30px', 'orderable': true, 'searchable': true, 'targets': [1]},
            {'width': '', 'orderable': true, 'searchable': true, 'targets': [2]},
            {'width': '80px', 'orderable': true, 'searchable': true, 'targets': [3]},
            {'width': '80px', 'orderable': true, 'searchable': true, 'targets': [4]},
            {'width': '60px', 'orderable': true, 'searchable': true, 'targets': [5]},
            {'width': '70px', 'orderable': true, 'searchable': true, 'targets': [6]},
            {'width': '60px', 'orderable': true, 'searchable': false, 'targets': [7]},
            {'width': '50px', 'orderable': false, 'searchable': false, 'targets': [8]},
            {'width': '60px', 'orderable': false, 'searchable': false, 'targets': [9]},
            {'width': '80px', 'orderable': false, 'searchable': false, 'targets': [10]},
            {'width': '80px', 'orderable': false, 'searchable': false, 'targets': [11]}
        ],
        "order": [
            [1, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": base_url + "company/UsersDatatableRefresh/" + EncodeEdit_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
            aoData.push({name: 'testerfilter', value: $('#testerfilter').is(":checked")});
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        }
        , "fnFooterCallback": function (nRow, aData) {
        }
    });
}
function UploadXlsUserData() {
    $('#modalerrordiv').hide();
    if ($('#filename').val() == "") {
        ShowAlret("Please select xls file.!", 'error');
        return false;
    }
    var file_data = $('#filename').prop('files')[0];
    var form_data = new FormData();
    form_data.append('filename', file_data);
    var other_data = $('#FrmDeviceUsers').serializeArray();
    $.each(other_data, function (key, input) {
        form_data.append(input.name, input.value);
    });
    $.ajax({
        cache: false,
        contentType: false,
        processData: false,
        type: "POST",
        url: base_url + 'company/UploadUsersXls/' + EncodeEdit_id,
        data: form_data,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            //alert(result);
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                $('#LoadModalFilter').data('modal', null);
                $('#CloseModalBtn').click();
                DatatableUsersRefresh();
            } else {
                $('#modalerrordiv').show();
                $('#modalerrorlog').html(Data['Msg']);
            }
            customunBlockUI();
        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
            ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
        }
    });
}
function UpdateUserdata(User_id) {
    $('#modalerrordiv').hide();
    $.ajax({
        type: "POST",
        url: base_url + 'company/UpdateUserData/' + EncodeEdit_id + '/' + User_id,
        data: $('#UserDeviceForm').serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            //alert(result);
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                $('#LoadModalFilter').data('modal', null);
                $('#CloseModalBtn').click();
                DatatableUsersRefresh();
            } else {
                $('#modalerrordiv').show();
                $('#modalerrorlog').html(Data['Msg']);
            }
            customunBlockUI();
        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
            ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
        }
    });
}
function LoadDeleteDialog(Id) {
    $.confirm({
        title: 'Confirm!',
        content: " Are you sure you want to delete user ? ",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        type: "POST",
                        url: base_url + "company/RemoveDeviceUser/" + Id,
                        beforeSend: function () {
                            customBlockUI();
                        },
                        success: function (response_json) {
                            var response = JSON.parse(response_json);
                            ShowAlret(response.message, response.alert_type);
                            DatatableUsersRefresh();
                            customunBlockUI();
                        }
                    });
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}
function SaveCompanyData() {
    if (!frmCompany.valid()) {
        return false;
    }
    var form_data = new FormData(); 
    
    if($('#company_logo').prop('files') !=undefined){
        var file_data = $('#company_logo').prop('files')[0];
        form_data.append('company_logo', file_data);
    }
    var other_data = frmCompany.serializeArray();
    $.each(other_data,function(key,input){
        form_data.append(input.name,input.value);
    });
    $.ajax({
        type: "POST",
        url : base_url + "company/update/" + EncodeEdit_id,
        data:  form_data,
        contentType: false,
        cache: false,
        async:false,
        processData:false,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                if (AddEdit == 'A' || AddEdit == 'C') {
                    setTimeout(function () {// wait for 5 secs(2)
                        window.location.href = base_url + 'company';
                    }, 500);
                }
            } else {
                $('#errordiv').show();
                $('#errorlog').html(Data['Msg']);
                App.scrollTo(form_error, -200);
            }
            customunBlockUI();
        }
    });
}

function SettingUpdate() {
    if (!frmSetting.valid()) {
        return false;
    }
    $.ajax({
        type: "POST",
        url: base_url + "company/SettingUpdate/" + EncodeEdit_id,
        data: frmSetting.serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
            } else {
                $('#errordiv').show();
                $('#errorlog').html(Data['Msg']);
                App.scrollTo(form_error, -200);
            }
            customunBlockUI();
        }
    });
}
function ResetSmtpData(){
    $.confirm({
        title: 'Confirm!',
        content: "are you sure you want to reset smtp setting.?",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        type: "POST",
                        data: $('#frmSMTP').serialize(),
                        url: base_url + "company/reset_smtp/" + EncodeEdit_id,
                        beforeSend: function () {
                            customBlockUI();
                        },
                        success: function (Odata) {
                            var Data = $.parseJSON(Odata);
                            if (Data['success']) {
                                $('#frmSMTP')[0].reset();
                            } else {
                                ShowAlret(Data['Msg'], 'error');
                            }
                            customunBlockUI();
                        }
                    });
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
        
    }
    function add_dateminute() {
        $.ajax({
            type: "POST",
            data: {row_no: row_no},
            //async: false,
            url: base_url + "company/get_dateminute",
            beforeSend: function () {
                customBlockUI();
            },
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if (data['Success']) {
                    $('#company_minute tbody').append(data['html']);
                    $('.date_range').datepicker({
                        rtl: App.isRTL(),
                        orientation: "left",
                        autoclose: true,
                        format: 'dd-mm-yyyy'
                    });
                    row_no++;
                } else {
                    ShowAlret(data['Msg'], 'warning');
                }
                customunBlockUI();
            }
        });
    }
    function remove_dateminute(remove_id) {
        $.confirm({
            title: 'Confirm!',
            content: " Are you sure you want to remove ? ",
            buttons: {
                confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-danger',
                    keys: ['enter', 'shift'],
                    action: function () {
                        $('#dtrow_' + remove_id).remove();
                    }
                },
                cancel: function () {
                    this.onClose();
                }
            }
        });
    }
function check_allocate(row_id) {
    var billed_min = parseFloat($('#billed_min'+row_id).val())||0;
    var unbilled_min = parseFloat($('#unbilled_min'+row_id).val())||0;
    var allocated_min = parseFloat($('#allocated_min'+row_id).val())||0;
    var total_billed = billed_min + unbilled_min;
    if(allocated_min > total_billed){
         ShowAlret("Allocated minute not more than the sum of billed and unbilled minute", "error");
         return false;
    }
}
function MinuteUpdate() {
    if (!frmMinute.valid()) {
        return false;
    }
    $.ajax({
        type: "POST",
        url: base_url + "company/MinuteUpdate/" + EncodeEdit_id,
        data: frmMinute.serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
            } else {
                $('#errordiv').show();
                $('#errorlog').html(Data['Msg']);
                App.scrollTo(form_error, -200);
            }
            customunBlockUI();
        }
    });
}
function SaveSmtpData() {
    if (!frmSMTP.valid()) {
        return false;
    }
    $.ajax({
        type: "POST",
        url: base_url + "company/Smtp_save/" + EncodeEdit_id,
        data: frmSMTP.serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
            } else {
                $('#errordiv').show();
                $('#errorlog').html(Data['Msg']);
                App.scrollTo(form_error, -200);
            }
            customunBlockUI();
        }
    });
}
function sendTestMail() {
    if ($('#testmail').val() == "") {
        ShowAlret("Please enter valid email ID.", 'error');
        return false;
    }
    if (!frmSMTP.valid()) {
        return false;
    }
    $.ajax({
        type: "POST",
        url: base_url + "company/Testmail/" + EncodeEdit_id,
        data: {testmail: $('#testmail').val()},
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
            } else {
                ShowAlret(Data['Msg'], 'error');
            }
            customunBlockUI();
        }
    });
}
function getCheckCount() {
    var FormInput = document.frmTrainee;
    var x = 0;
    for (var i = 0; i < FormInput.elements.length; i++)
    {
        if (FormInput.elements[i].checked == true)
        {
            x++;
        }
    }
    return x;
}

function SendOTP(type) {
    //alert(type);
    var msg = '';
    if(type == 1){
        msg = "are you sure you want to send OTP.?";
    }else{
        msg = "are you sure you want to send OTC.?";
    }
    if (getCheckCount() == 0) {
        ShowAlret("Please select record from the list.", 'error');
        return false;
    }
    $.confirm({
        title: 'Confirm!',
        content: msg,
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        type: "POST",
                        data: $('#frmTrainee').serialize(),
                        url: base_url + "company/send_otp/" + EncodeEdit_id +"/" + type ,
                        
                        beforeSend: function () {
                            customBlockUI();
                        },
                        success: function (Odata) {
                            var Data = $.parseJSON(Odata);
                            if (Data['success']) {
                                ShowAlret(Data['Msg'], 'success');
                                DatatableUsersRefresh();
                            } else {
                                ShowAlret(Data['Msg'], 'error');
                            }
                            customunBlockUI();
                        }
                    });
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}
function SettingsCheck(chkElement, element)
{
    if ($('#' + chkElement).prop("checked") == true) {
        $('#' + element).removeAttr('disabled');
    } else {
        if ($('#' + element).is("select")) {

        } else {
            $('#' + element).val('');
        }
        $('#' + element).attr('disabled', 'disabled');
    }
}
jQuery.validator.addMethod("validateMobile", function (mobile_number, element) {
    mobile_number = mobile_number.replace(/\s+/g, "");
    return this.optional(element) || mobile_number.length > 9 &&
            mobile_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
}, "Please specify a valid mobile number");
jQuery.validator.addMethod("validateContact", function (contact_number, element) {
    contact_number = contact_number.replace(/\s+/g, "");
    return this.optional(element) || contact_number.length > 7 &&
            contact_number.match(/^[0-9]*$/);
}, "Please specify a valid contact number");
jQuery.validator.addMethod("alpha_dash", function (value, element) {
    return this.optional(element) || /^[a-z0-9_ \-]+$/i.test(value);
}, "Alphanumerics, spaces, underscores & dashes only.");
jQuery.validator.addMethod("companyCheck", function (value, element) {

    var isSuccess = false;
    $.ajax({
        type: "POST",
        data: {company: value, cmp_id: EncodeEdit_id},
        url: base_url + "Company/Check_company",
        async: false,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            customunBlockUI();
            isSuccess = msg != "" ? false : true;
        }
    });
    return isSuccess;
}
, "Company already exists!!!");
jQuery.validator.addMethod("Special_CharCheck", function (value, element) {
    var return_result = true;
    if (/^[a-zA-Z0-9- ]*$/.test(value) == false || (value.indexOf(" ") > 0 && value != "")) {
        return_result = false;
    }
    return return_result;
}, "Special Characters And Space are Not Allowed");
jQuery.validator.addMethod("portalCheck", function (value, element) {
    var isSuccess = false;
    $.ajax({
        type: "POST",
        data: {portal: value, cmp_id: EncodeEdit_id},
        url: base_url + "Company/Check_portal",
        async: false,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            customunBlockUI();
            isSuccess = msg != "" ? false : true;
        }
    });
    return isSuccess;
}
, "Portal already exists!!!");
function exportConfirm() {
    $.confirm({
        title: 'Confirm!',
        content: "Are you sure you want to Export. ? ",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                keys: ['enter', 'shift'],
                action: function () {
                    document.frmTrainee.submit();
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}