var feedbackform = $('#feedbackform');
var form_error = $('.alert-danger', feedbackform);
var form_success = $('.alert-success', feedbackform);
feedbackform.validate({
    errorElement: 'span',
    errorClass: 'help-block help-block-error',
    focusInvalid: false,
    ignore: "",
    rules: {
        company_id: {
            required: true
        },
        form_name: {
            required: true,
            formCheck: true
        },
        'New_field_name[]': {
            required: true,
            Nospace: true,
            Field_nameCheck: true
        },
        'New_disp_name[]': {
            required: true
        },
        'New_fieldtype_id[]': {
            required: true
        },
        status: {
            required: true
        }
    },
    invalidHandler: function (event, validator) {
        form_success.hide();
        form_error.show();
        App.scrollTo(form_error, -200);
    },
    errorPlacement: function (error, element) {
        if (element.parent("td").size() > 0) {
            error.appendTo(element.parent("td"));
        } else if (element.hasClass('.form-group')) {
            error.appendTo(element.parent().find('.has-error'));
        } else if (element.parent('.form-group').length) {
            error.appendTo(element.parent());
        } else {
            error.appendTo(element);
        }
    },
    highlight: function (element) {
        $(element).closest('.form-group').addClass('has-error');
        $(element).closest('td').addClass('has-error');

    },
    unhighlight: function (element) {
        $(element).closest('.form-group').removeClass('has-error');
        $(element).closest('td').removeClass('has-error');
    },
    success: function (label) {
        label.closest('.form-group').removeClass('has-error');
        label.closest('td').removeClass('has-error');
    },
    submitHandler: function (form) {
        form_success.show();
        form_error.hide();
        Ladda.bind('button[id=feedback_form-submit]');
        form.submit();
    }
});
$(".select2, .select2-multiple", feedbackform).change(function () {
    $(this).valid();
});
jQuery.validator.addMethod("formCheck", function (value, element) {
    var lEncode_id = Encode_id;
    if (AddEdit == 'C') {
        lEncode_id = "";
    }
    var isSuccess = false;
    $.ajax({
        type: "POST",
        data: {form_name: value, company_id: $('#company_id').val(), form_id: lEncode_id},
        url: base_url + "information_form/Check_form",
        async: false,
        success: function (msg) {
            isSuccess = msg != "" ? false : true;
        }
    });
    return isSuccess;
}
, "Information Form already exists!!!");
jQuery.validator.addMethod("Nospace", function (value, element) {
    var returnVal = true;
    if (/^[a-zA-Z0-9- ]*$/.test(value) == false || (value.indexOf(" ") > 0 && value != "")) {
        returnVal = false;
    }

    return returnVal;
}, "Space/Junk/Bad character not allowed");

jQuery.validator.addMethod("Field_nameCheck", function (value, element) {
    var isSuccess = false;
    $.ajax({
        type: "POST",
        data: {field_name: value, form_name: $('#form_name').val()},
        url: base_url + "information_form/Check_fieldDuplicate",
        async: false,
        success: function (msg) {
            isSuccess = msg != "" ? false : true;
        }
    });
    return isSuccess;
}
, "Field Name already exists!!!");
function ConfirmField() {
    trainer_no = Totalfield;
    $.ajax({
        url: base_url + "information_form/getfield/" + Totalfield,
        type: 'POST',
        data: {cmp_id: $('#company_id').val(), selected_topic: SelectedArrray},
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            //console.log(Data);
            $('#FieldDatatable').append(Data['htmlData']);
            $('#field_type' + Totalfield).select2();
            $('#field_status' + Totalfield).select2();
            customunBlockUI();
            Totalfield++;
        }
    });
}
function RowDelete(r) {
    $.confirm({
        title: 'Confirm!',
        content: "Are you sure you want to remove.?",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $("#Row-" + r).remove();
                    //console.log(FieldArrray);
                    for (var i = 0; i < FieldArrray.length; i++) {
                        if (FieldArrray[i] == r) {
                            FieldArrray.splice(i, 1);
                            break;
                        }
                    }
//                    if (FieldArrray.length == 0) {
//                        Totalfield = 1;
//                    }
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}
function addDATA(r_id) {
    var dropdown = $('#field_type' + r_id).val();
    if (dropdown == 'dropdown') {
        $("#data_area" + r_id).prop('readonly', false);
    } else {
        $("#data_area" + r_id).val("");
        $("#data_area" + r_id).prop('readonly', true);
    }
}
function setCheckBoxValue(id) {
    if ($("#chk" + id).is(':checked')) {
        $('#required_id' + id).val(1);
    } else {
        $('#required_id' + id).val(0);
    }
}
function SaveFormData() {
    if (!feedbackform.valid()) {
        return false;
    }
    if (AddEdit == 'A') {
        var url = base_url + 'information_form/submit/';
    } else if (AddEdit == 'C') {
        url = base_url + 'information_form/submit/' + Encode_id;
    } else {
        url = base_url + 'information_form/update/' + Encode_id
    }
    $.ajax({
        type: "POST",
        url: url,
        data: feedbackform.serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                if (AddEdit == 'A' || AddEdit == 'C') {
                    window.location.href = base_url + 'information_form';
                }else{
                    setTimeout(function () {// wait for 5 secs(2)
                        window.location.href = base_url + 'information_form/edit/' + Encode_id;
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