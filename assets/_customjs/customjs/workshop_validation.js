var WorkshopForm = $('#WorkshopForm');
var form_error = $('.alert-danger', WorkshopForm);
var form_success = $('.alert-success', WorkshopForm);
var ParticipantForm = document.ParticipantForm;
$('.timepicker').timepicker({
    timeFormat: 'h:mm p',
    interval: 60,
    dynamic: false,
    dropdown: true,
    scrollbar: true
});

WorkshopForm.validate({
    errorElement: 'span',
    errorClass: 'help-block help-block-error',
    focusInvalid: false,
    ignore: "",
    //ignore: ':hidden:not("#WorkshopForm")',
    rules: {
        workshop_name: {
            required: true,
            workshopCheck: true,
            workshopQuotes: true
        },
        company_id: {
            required: true
        },
        powered_by: {
            required: true
        },
        workshop_url: {
            required: true,
            complete_url: true
        },
        'pre_question_set[]': {
            //do not change any class name we have call custom function
            require_from_group: [1, ".groupSelectClass"]
        },
        'post_question_set[]': {
            require_from_group: [1, ".groupSelectClass"]
        },
        'pre_feedback_id[]': {
            require_from_group: [1, ".groupSelectClass2"]
        },
        'post_feedback_id[]': {
            require_from_group: [1, ".groupSelectClass2"]
        },
        'reward_id[]': {
            required: function () {
                return $('#payback_option').val() == 1;
            }
        },
        creation_date: {
            required: true
        },
        heading: {
            required: true
        },
        message: {
            required: true
        },
        wktype: {
            required: true
        },
        /*otp: {
            required: true
        },*/
        region: {
            required: true
        },
        time: {
            digits: true
        },
        language_id: {
            required: true
        },
        start_date: {
            required: function () {
                return $('#pre_question_set').val() != null || $('#pre_feedback_id').val() != null;
            },
            PreStartDateCheck: true
        },
        start_time: {
            required: function () {
                return $('#pre_question_set').val() != null || $('#pre_feedback_id').val() != null;
            },
        },
        end_time: {
            required: function () {
                return $('#pre_question_set').val() != null || $('#pre_feedback_id').val() != null;
            },
        },
        end_date: {
            required: function () {
                return $('#pre_question_set').val() != null || $('#pre_feedback_id').val() != null;
            },
        },
        post_start_date: {
            required: function () {
                return $('#post_question_set').val() != null || $('#post_feedback_id').val() != null;
            },
            PostStartDateCheck: true
        },
        post_end_date: {
            required: function () {
                return ($('#post_question_set').val() != null || $('#post_feedback_id').val() != null);
            },
            PostEndDateCheck: true
        },
        post_start_time: {
            required: function () {
                return ($('#post_question_set').val() != null || $('#post_feedback_id').val() != null);
            }
        },
        post_end_time: {
            required: function () {
                return ($('#post_question_set').val() != null || $('#post_feedback_id').val() != null);
            }
        },
        status: {
            required: true
        },
        point_multiplier: {
            required: true
        },
        prefeedback_trigger: {
            required: function () {
                var Returnflag = true;
                if ($('#pre_question_set').val() == 1) {
                    if ($('#pre_feedback_id').val() != null) {
                        Returnflag = false;
                    }
                }
                return Returnflag;
            }
        },
        postfeedback_trigger: {
            required: function () {
                var Returnflag = true;
                if ($('#pre_question_set').val() == 1) {
                    if ($('#post_feedback_id').val() != null) {
                        Returnflag = false;
                    }
                }
                return Returnflag;
            }
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
    errorPlacement: function (error, element) {
        if (element.parent(".input-icon").size() > 0) {
            error.insertAfter(element.parent(".input-icon"));
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
        form.submit();
    }
});
$('.select2,.select2-multiple').on('change', function () {
    var defId = $(this).attr('id');
    if(typeof defId=='undefined'){
        return false;
    }
    if (defId == 'pre_question_set' || defId == 'post_question_set') {
        LoadPrepostValidation();
        $('#pre_question_set').valid();
        $('#post_question_set').valid();
    } else if (defId == 'pre_feedback_id' || defId == 'post_feedback_id') {
        LoadPrepostValidation();
        $('#pre_feedback_id').valid();
        $('#post_feedback_id').valid();
    } else {
        $(this).valid();
    }
});
$('#pre_question_set,#post_question_set,#pre_feedback_id,#post_feedback_id').on('select2:unselecting', function (e) {
    var removed_id = e.params.args.data.id;
    var module_id = $(this).attr('id');
    var session = '';
    var returnflag = true;
    if (module_id == 'pre_question_set') {
        session = 1;
    } else if (module_id == 'post_question_set') {
        session = 2;
    } else if (module_id == 'pre_feedback_id') {
        session = 3;
    } else if (module_id == 'post_feedback_id') {
        session = 4;
    }
    if (AddEdit == 'E') {
        $.ajax({
            url: base_url + "workshop/remove_questionset/" + EncodeEdit_id,
            type: 'POST',
            data: {question_set: removed_id, module_id: session, AddEdit: AddEdit},
            async: false,
            beforeSend: function () {
                customBlockUI();
            },
            success: function (Odata) {
                var Data = $.parseJSON(Odata);
                customunBlockUI();
                if (!Data['success']) {
                    ShowAlret(Data['Msg'], 'error');
                    returnflag = false;
                }
            }
        });
    }
    if (returnflag) {
        if (session == '1') {
            $('#QPre' + removed_id).remove();
        } else if (session == '2') {
            $('#QPost' + removed_id).remove();
        } else if (session == '3') {
            $('#FPre' + removed_id).remove();
        } else if (session == '4') {
            $('#FPost' + removed_id).remove();
        }
    }
    return returnflag;
});

$('#pre_question_set,#post_question_set,#pre_feedback_id,#post_feedback_id').on('select2:selecting', function (e) {
    var module_id = $(this).attr('id');
    var selected_id = e.params.args.data.id;
    var returnflag = true;
    if (module_id == 'pre_question_set') {
        returnflag = selectPreQuestionSet('pre', selected_id);
    } else if (module_id == 'post_question_set') {
        returnflag = selectPreQuestionSet('post', selected_id);
    } else if (module_id == 'pre_feedback_id') {
        returnflag = selectPreFeedbackSet('pre', selected_id);
    } else if (module_id == 'post_feedback_id') {
        returnflag = selectPreFeedbackSet('Post', selected_id);
    }
    return returnflag;
});
function LoadPrepostValidation() {
    var Q_type = $('#pre_question_type').val();
    if (Q_type == 1) {
        if ($('#pre_question_set').val() == null) {
            //$("#pre_feedback_id").val(null).trigger("change");
            $(".PreSessionTime").prop('disabled', true);
            $('#start_date').val("");
            $('#end_date').val("");
            $('#start_time').val("");
            $('#end_time').val("");
            $('#Pretime_status').attr('checked', false); // Checks it
            $('#pre_feedback_id').prop('disabled', true);

        } else {
            var isDisabled = $('#pre_question_set').prop('disabled');
            if (!isDisabled) {
                $(".PreSessionTime").prop('disabled', false);
            }
        }
        if ($('#post_question_set').val() == null) {
            $(".PostSessionTime").prop('disabled', true);
            $('#post_start_date').val("");
            $('#post_end_date').val("");
            $('#post_start_time').val("");
            $('#post_end_time').val("");
            $('#Posttime_status').attr('checked', false); // Checks it
            //$('#post_feedback_id').prop('disabled', true);
            //$("#post_feedback_id").val(null).trigger("change");
        } else {
            var isDisabled = $('#post_question_set').prop('disabled');
            if (!isDisabled) {
                $(".PostSessionTime").prop('disabled', false);
            }
        }
        var isDisabled = $('#pre_feedback_id').prop('disabled');
        if (!isDisabled && $('#pre_feedback_id').val() != null) {
            $(".PreFeedbackSession").prop('disabled', false);
        } else {
            $(".PreFeedbackSession").prop('disabled', true);
            if ($('#pre_feedback_id').val() == null) {
                $('#prefeedback_trigger').val("");
            }
        }
        isDisabled = $('#post_feedback_id').prop('disabled');
        if (!isDisabled && $('#post_feedback_id').val() != null) {
            $(".PostFeedbackSession").prop('disabled', false);
        } else {
            $(".PostFeedbackSession").prop('disabled', true);
            if ($('#post_feedback_id').val() == null) {
                $('#postfeedback_trigger').val("");
            }
        }
    } else {
        if ($('#pre_feedback_id').val() == null) {
            $(".PreSessionTime").prop('disabled', true);
            $('#start_date').val("");
            $('#end_date').val("");
            $('#start_time').val("");
            $('#end_time').val("");
            $('#Pretime_status').attr('checked', false); // Checks it
            $("#pre_feedback_id").prop('disabled', false);
        } else {
            $(".PreSessionTime").prop('disabled', false);
        }
        if ($('#post_feedback_id').val() == null) {
            $(".PostSessionTime").prop('disabled', true);
            $('#post_start_date').val("");
            $('#post_end_date').val("");
            $('#post_start_time').val("");
            $('#post_end_time').val("");
            $('#Posttime_status').attr('checked', false); // Checks it
            $("#post_feedback_id").prop('disabled', false);
        } else {
            $(".PostSessionTime").prop('disabled', false);
        }
        $(".PreFeedbackSession").prop('disabled', true);
        $('#prefeedback_trigger').val("");
        $(".PostFeedbackSession").prop('disabled', true);
        $('#postfeedback_trigger').val("");
    }
    if ($('#pre_feedback_id').val() == null && $('#post_feedback_id').val() == null) {
        $('#play_all_feedback').attr('checked', false);
    }

}
$('.date-picker2,.date-picker,.timepicker,#start_time,#end_time').change(function () {
    $(this).valid();
});
jQuery.validator.addMethod("workshopQuotes", function (value, element) {

    var onlyValidCharacters = /^[^'"]*$/.test(value);
    if (!onlyValidCharacters) {
        return false;
    } else {
        return true;
    }
}
, "Single and Double quotes are not allowed!!!");
jQuery.validator.addMethod("complete_url", function (val, elem) {
    // if no url, don't do anything
    if (val.length == 0) {
        return true;
    }
    if (!/^(https?|ftp):\/\//i.test(val)) {
        val = 'http://' + val; // set both the value
        $(elem).val(val); // also update the form element
    }
    return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(val);
}, "Please enter valid url");
jQuery.validator.addMethod("require_from_group", function (value, element, options) {
    var numberRequired = options[0];
    var selector = options[1];
    if (selector == ".groupSelectClass2" && $('#pre_question_type').val() == 1) {
        return true;
    }
    if (selector == ".groupSelectClass" && $('#pre_question_type').val() == 2) {
        return true;
    }
    //console.log(selector);
    var fields = $(selector, element.form);
    var filled_fields = fields.filter(function () {
        // it's more clear to compare with empty string
        return  $(this).val() != null;
    });
    var empty_fields = fields.not(filled_fields);
    //console.log(filled_fields.length);
    // we will mark only first empty field as invalid
    if (filled_fields.length < numberRequired) {
        return false;
    }
    return true;
    // {0} below is the 0th item in the options field
}, "Please fill out at least Pre/Post Session.");
jQuery.validator.addMethod("PrePostStartTimecheck", function (value, element) {
    var pre_start_date = $("#start_date").datepicker('getDate');
    var post_start_date = $("#post_start_date").datepicker('getDate');

    if (pre_start_date == null || post_start_date == null) {
        return true;
    }
    if (pre_start_date == post_start_date) {
        var pre_start_time = $('#start_time').val();
        var post_start_time = $('#post_start_time').val();

        if (pre_start_time == post_start_time) {
            return false;
        } else {
            return true;
        }
    } else {
        return true;
    }
}
, "Pre Start Time & Post Start Time cannot be same..!!!");
jQuery.validator.addMethod("PrePostEndTimecheck", function (value, element) {
    var pre_End_date = $("#end_date").datepicker('getDate');
    var post_End_date = $("#post_end_date").datepicker('getDate');
    if (pre_End_date == null || post_End_date == null) {
        return true;
    }
    if (pre_End_date == post_End_date) {
        var pre_End_time = $('#end_time').val();
        var post_End_time = $('#post_end_time').val();
        if (pre_End_time == post_End_time) {
            return false;
        } else {
            return true;
        }
    } else {
        return true;
    }
}
, "Pre End Time & Post End Time cannot be same..!!!");
jQuery.validator.addMethod("EndTimeCheck", function (value, element) {
    var sdate = $("#start_date").datepicker('getDate');
    var edate = $("#end_date").datepicker('getDate');
    if (sdate == null || edate == null) {
        return true;
    }
    var diff = sdate - edate;
    var days = diff / 1000 / 60 / 60 / 24;
    var start_time = $('#start_time').val();
    var st = getHour24(start_time);
    var et = getHour24(value);
    if (days == 0) {
        if (st >= et) {
            return false;
        } else {
            return true;
        }
    } else {
        return true;
    }
}
, "Must be greater than Start Time!!!");

jQuery.validator.addMethod("PostEndTimeCheck", function (value, element) {
    var sdate = $("#post_start_date").datepicker('getDate');
    var edate = $("#post_end_date").datepicker('getDate');
    var diff = sdate - edate;
    var days = diff / 1000 / 60 / 60 / 24;
    var start_time = $('#post_start_time').val();
    var st = getHour24(start_time);
    var et = getHour24(value);
    if (days == 0) {
        if (st > et) {
            return false;
        } else {
            return true;
        }
    } else {
        return true;
    }
}
, "Must be greater than Start Time!!!");

jQuery.validator.addMethod("EndDateCheck", function (value, element) {
    var sdate = $("#start_date").datepicker('getDate');
    var edate = $("#end_date").datepicker('getDate');
    var diff = sdate - edate;
    var days = diff / 1000 / 60 / 60 / 24;
    if (days > 0) {
        return false;
    } else {
        return true;
    }
}
, "Must be greater than Start Date!!!");

jQuery.validator.addMethod("PostEndDateCheck", function (value, element) {
    var sdate = $("#post_start_date").datepicker('getDate');
    var edate = $("#post_end_date").datepicker('getDate');
    var diff = sdate - edate;
    var days = diff / 1000 / 60 / 60 / 24;
    if (days > 0) {
        return false;
    } else {
        return true;
    }
}
, "Must be greater than Start Date!!!");
jQuery.validator.addMethod("PostStartDateCheck", function (value, element) {
    if ($('#post_question_set').val() == null && $('#post_feedback_id').val() == null) {
        return true;
    }
    var sdate = $("#post_start_date").datepicker('getDate');
    var pre_sdate = $("#start_date").datepicker('getDate');
    var pre_edate = $("#end_date").datepicker('getDate');
    var diff1 = sdate - pre_sdate;
    var diff2 = sdate - pre_edate;
    var days1 = diff1 / 1000 / 60 / 60 / 24;
    var days2 = diff2 / 1000 / 60 / 60 / 24;
    //console.log(days1);
    //console.log(days2);
    if (days1 < 0 || days2 < 0) {
        return false;
    } else {
        return true;
    }
}
, "Date cannot be in-between Pre Start/End datetime.");

jQuery.validator.addMethod("PreStartDateCheck", function (value, element) {
    if ($('#pre_question_set').val() == null && $('#pre_feedback_id').val() == null) {
        return true;
    }
    var post_sdate = $("#post_start_date").datepicker('getDate');
    var post_edate = $("#post_end_date").datepicker('getDate');
    var pre_sdate = $("#start_date").datepicker('getDate');
    //var pre_edate = $("#end_date").datepicker('getDate');
    var diff1 = pre_sdate - post_sdate;
    var diff2 = post_edate - post_sdate;
    var days1 = diff1 / 1000 / 60 / 60 / 24;
    var days2 = diff2 / 1000 / 60 / 60 / 24;
    if (days1 > 0 && days2 > 0) {
        return false;
    } else {
        return true;
    }
}
, "Pre Start Date cannot be in between Post Start/End datetime..!!!");
jQuery.validator.addMethod("workshopCheck", function (value, element) {
    var Edit_id = EncodeEdit_id;
    if (AddEdit == 'C') {
        Edit_id = '';
    }
    var isSuccess = false;
    $.ajax({
        type: "POST",
        data: {workshop_name: value, company_id: $('#company_id').val(), workshop_id: Edit_id},
        url: base_url + "Workshop/Check_workshop",
        async: false,
        success: function (msg) {
            isSuccess = msg != "" ? false : true;
        }
    });
    return isSuccess;
}
, "Workshop already exists!!!");
$('#UsersTable').on('click', '.delete', function (e) {
    e.preventDefault();
    var nRow = $(this).parents('tr')[0];
    var Remove_id = $(this).val();
    $.confirm({
        title: 'Confirm!',
        content: 'Are you sure you want to remove this Region ?',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-primary',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        url: base_url + "workshop/RemoveParticipantUser/" + EncodeEdit_id,
                        type: 'POST',
                        data: {Remove_id: Remove_id},
                        success: function (Odata) {
                            var Data = $.parseJSON(Odata);
                            if (Data['success']) {
                                oTable.fnDeleteRow(nRow);
                                ShowAlret(Data['Msg'], 'success');
                            } else {
                                $('#errordiv').show();
                                $('#errorlog').html(Data['Msg']);
                            }
                        }
                    });
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
});
function UpdateBannerData() {
    $.ajax({
        type: "POST",
        url: base_url + 'workshop/Banner_update/' + EncodeEdit_id,
        data: $('#WorkshopBannerForm').serialize(),
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
            } else {
                $('#errordiv').show();
                $('#errorlog').html(Data['Msg']);
            }
        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
            ShowAlret("Status: " + textStatus + " ,Contact Atomapp for technical support!");
        }
    });
}
function getHour24(timeString)
{
    time = null;
    var matches = timeString.match(/^(\d{1,2}):00 (\w{2})/);
    if (matches != null && matches.length == 3)
    {
        time = parseInt(matches[1]);
        if (matches[2] == 'PM')
        {
            time += 12;
        } else if (time == 12) {
            time = 0;
        }
    }
    return time;
}
function RemoveImage(ImgId) {
    $.confirm({
        title: 'Confirm!',
        content: " Are you sure you want to delete this banner ? ",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        type: "POST",
                        data: "ImageId=" + ImgId,
                        url: base_url + "workshop/RemoveBanner",
                        success: function (Flag) {
                            if (Flag) {
                                $('#Img' + ImgId).remove();
                                ShowAlret('Banner image deleted successfully', 'success');
                            }
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
function ConfirmSave(AddEdit) {
    if (!$('#WorkshopForm').valid()) {
        return false;
    }
    $.confirm({
        title: 'Confirm Workshop!',
        content: '<div class="form-group">' +
                (company_id == "" ? '<h5>Company : <strong>' + $("#company_id option:selected").text() + '</strong></h5>' : '') +
                '<h5>Workshop Name: <strong>' + $('#workshop_name').val() + '</strong></h5>' +
                '<h5>OTC: <strong>' + $('#otp').val() + '</strong></h5>' +
                '</div>',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    SaveWorkshop(AddEdit);
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}
function Update_QuestionsetForm() {
    $('#errordiv').hide();
    if (!$('#WorkshopForm').valid()) {
        return false;
    }
    $.ajax({
        url: base_url + "workshop/tab2_update/" + EncodeEdit_id,
        type: 'POST',
        data: $('#sessionForm').serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                setTimeout(function () {// wait for 5 secs(2)
                    window.location.href = base_url + 'workshop/edit/' + EncodeEdit_id + '/2';
                }, 500);
            } else {
                $('#errordiv2').show();
                $('#errorlog2').html(Data['Msg']);
                App.scrollTo(form_error, -200);
            }
            customunBlockUI();
        }
    });
}
function SaveWorkshop(AddEditMode) {
    $('#errordiv').hide();
    if (AddEditMode == 'E') {
        if (!$('#WorkshopForm').valid()) {
            return false;
        }
    }
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
    var form_data = new FormData();
    if (typeof $('#workshop_image').prop('files') !== 'undefined') {
        var file_data = $('#workshop_image').prop('files')[0];
        form_data.append('workshop_image', file_data);
    }
    var other_data = $('#WorkshopForm').serializeArray();
    $.each(other_data, function (key, input) {
        form_data.append(input.name, input.value);
    });
    if (AddEditMode == 'A') {
        var url = base_url + "workshop/submit/";
    } else if (AddEditMode == 'C') {
        url = base_url + "workshop/submit/" + EncodeEdit_id;
    } else {
        url = base_url + "workshop/tab1_update/" + EncodeEdit_id;
    }
    $.ajax({
        url: url,
        type: 'POST',
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                if (AddEditMode == 'A' || AddEditMode == 'C') {
                    setTimeout(function () {// wait for 5 secs(2)
                        window.location.href = base_url + 'workshop/edit/' + Data['Workshop_id'] + "/3";
                    }, 500);
                }
            } else {
                $('#errordiv,#errordiv2').show();
                $('#errorlog,#errorlog2').html(Data['Msg']);
                App.scrollTo(form_error, -200);
            }
            customunBlockUI();
        }
    });
}
function LoadFilterUserData() {
    $('#UserFilterTable').dataTable({
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
        "lengthMenu": [
            [5, 10, 15, 20, -1],
            [5, 10, 15, 20, "All"]
        ],
        "pageLength": 10,
        "pagingType": "bootstrap_full_number",
        "columnDefs": [
            {'width': '30px', 'orderable': false, 'searchable': false, 'targets': [0]},
            {'width': '200px', 'orderable': true, 'searchable': true, 'targets': [1]},
            {'width': '100px', 'orderable': true, 'searchable': true, 'targets': [2]},
            {'width': '70px', 'orderable': true, 'searchable': true, 'targets': [3]},
            {'width': '80px', 'orderable': true, 'searchable': true, 'targets': [4]},
            {'width': '80px', 'orderable': true, 'searchable': true, 'targets': [5]},
            {'width': '80px', 'orderable': true, 'searchable': true, 'targets': [6]},
        ],
        "order": [
            [0, "asc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": base_url + "workshop/UsersFilterTable/" + EncodeEdit_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
            aoData.push({name: 'region', value: $('#region').val()});
            aoData.push({name: 'wktype', value: $('#wktype').val()});
            aoData.push({name: 'company_id', value: $('#company_id').val()});
            aoData.push({name: 'flt_region_id', value: $('#flt_region_id').val()});

            aoData.push({name: 'NewUsersArrray', value: NewUsersArrray});
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
function SelectedUsers(id) {
    if ($('#chk' + id).prop('checked')) {
        NewUsersArrray.push(id);
    } else {
        NewUsersArrray.splice($.inArray(id, NewUsersArrray), 1);
    }
}
function ConfirmUsers() {
    if (NewUsersArrray.length == 0) {
        ShowAlret("Please select Checkbox.", 'error');
        return false;
    }
    $.ajax({
        url: base_url + "workshop/SaveParticipantUsers/" + EncodeEdit_id,
        type: 'POST',
        data: {NewUsersArrray: NewUsersArrray},
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                DatatableUsersRefresh();
                $('#LoadModalFilter').data('modal', null);
                NewUsersArrray = [];
                $('#CloseModalBtn').click();
                ShowAlret(Data['Msg'], 'success');
            } else {
                $('#errordiv').show();
                $('#errorlog').html(Data['Msg']);
                ShowAlret(Data['Msg'], 'error');
            }
            customunBlockUI();
        }
    });
}
function SelectedQuestionSet() {
    var Q_type = $('#pre_question_type').val();
    if (Q_type == 2) {
        //$("#post_feedback_id option:selected").prop("selected", false);
        $('.prePostQuestionRow').hide();
        $("#post_feedback_id,#pre_feedback_id").prop('disabled', false);
        $("#pre_feedback_id").val(null).trigger("change");
        $("#pre_question_set").val(null).trigger("change");
        $("#post_question_set").val(null).trigger("change");
    } else {
        $('.prePostQuestionRow').show();
    }
    $("#pre_feedback_id").val(null).trigger("change");
    $("#post_feedback_id").val(null).trigger("change");
}
function ShowRedeem() {
    var payback_option = $('#payback_option').val();
    if (payback_option == 1) {
        $('#RewardBox').show();
    } else {
        $('#RewardBox').hide();
    }
}
function getRegionwisedata() {
    $.ajax({
        type: "POST",
        data: {company_id: $('#company_id').val(), region: $('#region').val()},
        async: false,
        url: base_url + "workshop/ajax_regionwise_subregion",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var SubregionMSt = Oresult['SubRegion'];
                var subregion_option = '<option value="">Please Select</option>';
                for (var i = 0; i < SubregionMSt.length; i++) {
                    subregion_option += '<option value="' + SubregionMSt[i]['id'] + '">' + SubregionMSt[i]['sub_region'] + '</option>';
                }
                $('#subregion').empty();
                $('#subregion').append(subregion_option);
            }
            customunBlockUI();
        }
    });
}
function getWsubtypedata() {
    $.ajax({
        type: "POST",
        data: {company_id: $('#company_id').val(), wktype: $('#wktype').val()},
        async: false,
        url: base_url + "workshop/ajax_wrktypewise_subtype",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var WSubTypeMSt = Oresult['WSubType'];
                var subtype_option = '<option value="">Please Select</option>';
                for (var i = 0; i < WSubTypeMSt.length; i++) {
                    subtype_option += '<option value="' + WSubTypeMSt[i]['id'] + '">' + WSubTypeMSt[i]['sub_type'] + '</option>';
                }
                $('#workshop_subtype').empty();
                $('#workshop_subtype').append(subtype_option);
            }
            customunBlockUI();
        }
    });
}
function question_set() {
    $.ajax({
        type: "POST",
        data: {data: $('#company_id').val(), pre_question_type: $('#pre_question_type').val()},
        async: false,
        url: base_url + "workshop/ajax_company_questionset",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var FeedbackMSt = Oresult['feedbackset_Qresult'];
                var FeedbackFormMSt = Oresult['feedback_form'];
                var WorkshopTypeMSt = Oresult['WorkshopType'];
                var RegionMSt = Oresult['Region'];
                var RewardMSt = Oresult['RewardResult'];
                var df_trainer_mst = Oresult['df_trainer_list'];
                var feedbackoption = '';
                var feedback_formoption = '<option value="">Please Select</option>';
                var workshop_typeoption = '<option value="">Please Select</option>';
                var region_option = '<option value="">Please Select</option>';
                var reward_option = '<option value="">Please Select</option>';
                var df_trainer_option = '<option value="">Please Select</option>';
                for (var i = 0; i < df_trainer_mst.length; i++) {
                    df_trainer_option += '<option value="' + df_trainer_mst[i]['userid'] + '">' + df_trainer_mst[i]['trainer'] + '</option>';
                }
                $('#df_trainer_option').empty();
                $('#df_trainer_option').append(df_trainer_option);

                for (var i = 0; i < FeedbackMSt.length; i++) {
                    feedbackoption += '<option value="' + FeedbackMSt[i]['id'] + '">' + FeedbackMSt[i]['title'] + '</option>';
                }
                for (var i = 0; i < FeedbackFormMSt.length; i++) {
                    feedback_formoption += '<option value="' + FeedbackFormMSt[i]['id'] + '">' + FeedbackFormMSt[i]['form_name'] + '</option>';
                }
                for (var i = 0; i < WorkshopTypeMSt.length; i++) {
                    workshop_typeoption += '<option value="' + WorkshopTypeMSt[i]['id'] + '">' + WorkshopTypeMSt[i]['workshop_type'] + '</option>';
                }
                for (var i = 0; i < RegionMSt.length; i++) {
                    region_option += '<option value="' + RegionMSt[i]['id'] + '">' + RegionMSt[i]['region_name'] + '</option>';
                }
                for (var i = 0; i < RewardMSt.length; i++) {
                    reward_option += '<option value="' + RewardMSt[i]['id'] + '">' + RewardMSt[i]['reward_name'] + '</option>';
                }
                var preOption = '';
                //var postOption='';
                var PreQuestionMSt = Oresult['question_Qresult'];
                //var PostQuestionMSt = Oresult['Post_Qresult'];
                for (var i = 0; i < PreQuestionMSt.length; i++) {
                    preOption += '<option value="' + PreQuestionMSt[i]['id'] + '">' + PreQuestionMSt[i]['title'] + '</option>';
                }
//                            for (var i = 0; i < PostQuestionMSt.length; i++) {
//                                postOption += '<option value="' + PostQuestionMSt[i]['id'] + '">' + PostQuestionMSt[i]['title'] + '</option>';
//                            }
                $('#pre_question_set').empty();
                $('#pre_question_set').append(preOption);
                $('#post_question_set').empty();
                $('#post_question_set').append(preOption);

                $('#pre_feedback_id').empty();
                $('#pre_feedback_id').append(feedbackoption);

                $('#post_feedback_id').empty();
                $('#post_feedback_id').append(feedbackoption);
                $('#feedback_form').empty();
                $('#feedback_form').append(feedback_formoption);
                $('#region').empty();
                $('#region').append(region_option);
                $('#reward_id').empty();
                $('#reward_id').append(reward_option);
                $('#wktype').empty();
                $('#wktype').append(workshop_typeoption);
            }
            customunBlockUI();
        }
    });
}
function selectPreFeedbackSet(Session, selected_id) {
    var val = [];
    if (Session == "pre") {
        $('.prefeedstatus_switch').each(function (i) {
            if (this.checked == false) {
                val[i] = $(this).val();
            }
        });
        var data = {question_set: selected_id, session: 1, status_switch: val, LockFlag: PreLock};
    } else {
        $('.postfeedstatus_switch').each(function (i) {
            if (this.checked == false) {
                val[i] = $(this).val();
            }
        });
        data = {question_set: selected_id, session: 2, status_switch: val, LockFlag: PostLock};
    }
    var workshop_id = '';
    if (AddEdit == 'E' || AddEdit == 'V') {
        workshop_id = EncodeEdit_id;
    }
    var returnflag = true;
    $.ajax({
        url: base_url + "workshop/ChangeFeedbackset/" + workshop_id,
        type: 'POST',
        data: data,
        async: false,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                if (Session == 'pre') {
                    $('#PreFeedTbody').append(Data['HtmlData']);
                } else {
                    $('#PostFeedTbody').append(Data['HtmlData']);
                }
                $(".make-switch").bootstrapSwitch();
            } else {
                ShowAlret(Data['Msg'], 'error');
                returnflag = false;
            }
            customunBlockUI();
        }
    });
    return returnflag;
}
function selectPreQuestionSet(Session, selected_id) {
    var val = [];
    var val2 = [];
    if (typeof token_key != 'undefined') {
        var token_key = '';
    }
    var returnflag = true;
    if (Session == "pre") {
        $('.preqstatus_switch').each(function (i) {
//            console.log(this.checked)
            if (this.checked == false) {
                val[i] = $(this).val();
            }
        });
        $('.prehide_answer').each(function (i) {
            if (this.checked == false) {
                val2[i] = $(this).val();
            }
        });
        var data = {question_set: selected_id, session: 1, status_switch: val,
            LockFlag: PreLock, show_answer: val2, 'token_key': token_key};
    } else {
        $('.postqstatus_switch').each(function (i) {
            if (this.checked == false) {
                val[i] = $(this).val();
            }
        });
        $('.posthide_answer').each(function (i) {
            if (this.checked == false) {
                val2[i] = $(this).val();
            }
        });
        data = {question_set: selected_id, session: 2, status_switch: val, LockFlag: PostLock,
            show_answer: val2, 'token_key': token_key};
    }
    var workshop_id = '';
    if (AddEdit == 'E' || AddEdit == 'V') {
        workshop_id = EncodeEdit_id;
    }
    $.ajax({
        url: base_url + "workshop/ChangeQuestionset/" + workshop_id,
        type: 'POST',
        data: data,
        async: false,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                if (Session == 'pre') {
                    $('#PreTbody').append(Data['HtmlData']);
                } else {
                    $('#PostTbody').append(Data['HtmlData']);
                }
                $(".make-switch").bootstrapSwitch();
            } else {
                ShowAlret(Data['Msg'], 'error');
                returnflag = false;
            }
            customunBlockUI();
        }
    });
    return returnflag;
}
function RemoveWkImage() {
    $('#RemoveWrkImage').val(1);
}
function DatatableUsersRefresh() {
    var table = $('#UsersTable');
    oTable = table.dataTable({
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
            {'width': '100px', 'orderable': true, 'searchable': true, 'targets': [1]},
            {'width': '100px', 'orderable': true, 'searchable': true, 'targets': [2]},
            {'width': '100px', 'orderable': false, 'searchable': true, 'targets': [3]},
            {'width': '100px', 'orderable': false, 'searchable': true, 'targets': [4]},
            {'width': '80px', 'orderable': false, 'searchable': true, 'targets': [5]},
            {'width': '80px', 'orderable': false, 'searchable': false, 'targets': [6]}
            //{'width': '80px', 'orderable': false, 'searchable': false, 'targets': [7]},
            //{'width': '80px', 'orderable': false, 'searchable': false, 'targets': [8]}			
        ],
        "order": [
            [1, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": base_url + "workshop/ParticipantUsers/" + EncodeEdit_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
            aoData.push({name: 'fttrainer_id', value: $('#fttrainer_id').val()});
            aoData.push({name: 'ftroute_trainer_id', value: $('#ftroute_trainer_id').val()});

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
function DataGridTable() {
    $('#ModalDeviceTable').dataTable({
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
        "lengthMenu": [
            [5, 10, 15, 20, -1],
            [5, 10, 15, 20, "All"]
        ],
        "pageLength": 5,
        "pagingType": "bootstrap_full_number",
        "columnDefs": [
            {'orderable': false, 'searchable': false, 'targets': [0]},
            {'orderable': false, 'searchable': true, 'targets': [1]},
            {'orderable': false, 'searchable': true, 'targets': [2]},
            {'orderable': false, 'searchable': true, 'targets': [3]}
        ],
        "order": [
            [0, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": base_url + "workshop/LoadDeviceUsersTable/1",
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
            aoData.push({name: 'modalcompany_id', value: Company_id});
            aoData.push({name: 'workshop_id', value: EncodeEdit_id});
            aoData.push({name: 'NewtestUsersArrray', value: NewtestUsersArrray});
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
function SelectedtestUsers(id) {
    if ($('#modalchk' + id).prop('checked')) {
        NewtestUsersArrray.push(id);
    } else {
        NewtestUsersArrray.splice($.inArray(id, NewtestUsersArrray), 1);
    }
}
function ConfirmtestUsers() {
    if (NewtestUsersArrray.length == 0) {
        ShowAlret("Please select Device users.", 'error');
        return false;
    }
    $.ajax({
        url: base_url + "workshop/users_submit",
        type: 'POST',
        data: {NewtestUsersArrray: NewtestUsersArrray, workshop_id: EncodeEdit_id},
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                DatatableTestusers();
                NewtestUsersArrray = [];
                ShowAlret(Data['Msg'], 'success');
                $('#ClosetestModalBtn').click();
            } else {
                ShowAlret(Data['Msg'], 'error');
            }
            customunBlockUI();
        }
    });
}
function DatatableTestusers() {
    $('#UserstestTable').dataTable({
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
        "lengthMenu": [
            [5, 10, 15, 20, -1],
            [5, 10, 15, 20, "All"]
        ],
        "pageLength": 5,
        "pagingType": "bootstrap_full_number",
        "columnDefs": [
            {'orderable': false, 'searchable': false, 'targets': [0]},
            {'orderable': false, 'searchable': true, 'targets': [1]},
            {'orderable': false, 'searchable': true, 'targets': [2]},
            {'orderable': false, 'searchable': true, 'targets': [3]}
        ],
        "order": [
            [0, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": base_url + "workshop/LoadDeviceUsersTable/2",
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
            aoData.push({name: 'workshop_id', value: EncodeEdit_id});
            aoData.push({name: 'PreDatedisabled', value: PreDatedisabled});
            aoData.push({name: 'PostDatedisabled', value: PostDatedisabled});
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
function DatatableTesterview() {
    $('#TesterviewTable').dataTable({
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
        "lengthMenu": [
            [5, 10, 15, 20, -1],
            [5, 10, 15, 20, "All"]
        ],
        "pageLength": 5,
        "pagingType": "bootstrap_full_number",
        "columnDefs": [
            {'orderable': false, 'searchable': false, 'targets': [0]},
            {'orderable': false, 'searchable': true, 'targets': [1]},
            {'orderable': false, 'searchable': true, 'targets': [2]}
        ],
        "order": [
            [0, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": base_url + "workshop/LoadDeviceUsersTable/3",
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
            aoData.push({name: 'workshop_id', value: EncodeEdit_id});
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
function UserDeleteDialog(Id) {
    $.confirm({
        title: 'Confirm!',
        content: " Are you sure you want to remove this user ? ",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        type: "POST",
                        url: base_url + "workshop/remove_user",
                        data: {deleteid: Id, workshop_id: EncodeEdit_id},
                        success: function (response_json) {
                            var response = JSON.parse(response_json);
                            ShowAlret(response.message, response.alert_type);
                            DatatableTestusers();
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
var handleImages = function () {
    // see http://www.plupload.com/
    var uploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: document.getElementById('tab_images_uploader_pickfiles'), // you can pass in id...
        container: document.getElementById('tab_images_uploader_container'), // ... or DOM Element itself

        url: base_url + "/workshop/UploadBanner/" + EncodeEdit_id,
        filters: {
            max_file_size: '10mb',
            mime_types: [
                {title: "Image files", extensions: "jpg,gif,jpeg,png"}
            ]
        },
        // Flash settings
        flash_swf_url: 'assets/plugins/plupload/js/Moxie.swf',
        // Silverlight settings
        silverlight_xap_url: 'assets/plugins/plupload/js/Moxie.xap',
        init: {
            PostInit: function () {
                $('#tab_images_uploader_filelist').html("");

                $('#tab_images_uploader_uploadfiles').click(function () {
                    uploader.start();
                    return false;
                });

                $('#tab_images_uploader_filelist').on('click', '.added-files .remove', function () {
                    uploader.removeFile($(this).parent('.added-files').attr("id"));
                    $(this).parent('.added-files').remove();
                });
            },
            FilesAdded: function (up, files) {
                plupload.each(files, function (file) {
                    $('#tab_images_uploader_filelist').append('<div class="alert alert-warning added-files" id="uploaded_file_' + file.id + '">' + file.name + '(' + plupload.formatSize(file.size) + ') <span class="status label label-info"></span>&nbsp;<a href="javascript:;" style="margin-top:-5px" class="remove pull-right btn btn-sm red"><i class="fa fa-times"></i> remove</a></div>');
                });
            },
            UploadProgress: function (up, file) {
                $('#uploaded_file_' + file.id + ' > .status').html(file.percent + '%');
            },
            FileUploaded: function (up, file, response) {
                var response = $.parseJSON(response.response);

                if (response.result && response.result == 'OK') {
                    var id = response.id; // uploaded file's unique name. Here you can collect uploaded file names and submit an jax request to your server side script to process the uploaded files and update the images tabke

                    $('#uploaded_file_' + file.id + ' > .status').removeClass("label-info").addClass("label-success").html('<i class="fa fa-check"></i> Done');
                    var html = '<tr id="Img' + response.NewId + '">';
                    html += '<td><a href="' + response.image + '" class="fancybox-button" data-rel="fancybox-button"><img class="img-responsive" src="' + response.image + '" alt=""></a></td>'
                    html += '<td><input type="text" class="form-control" name="url[' + response.NewId + ']" value=""></td>'
                    html += '<td><input type="number" class="form-control" name="sort[' + response.NewId + ']" value="' + response.NewSortNo + '"></td>'
                    html += '<td><a href="javascript:;" class="btn red btn-sm" onclick="RemoveImage(' + response.NewId + ')"><i class="fa fa-times"></i> Remove </a></td>';
                    html += '<tr>';
                    $('#ImageTable tr:last').after(html);
                    // set successfull upload
                } else {
                    $('#uploaded_file_' + file.id + ' > .status').removeClass("label-info").addClass("label-danger").html('<i class="fa fa-warning"></i> Failed'); // set failed upload
                    ShowAlret(response.result.error, 'error');
                }
            },
            Error: function (up, err) {
                ShowAlret(err.message, 'error');
            }
        }
    });
    uploader.init();
}
$('#play_all_feedback').click(function () {
    if ($('#pre_feedback_id').val() == null && $('#post_feedback_id').val() == null) {
        ShowAlret("Please select Pre/Post Feedbackset", "error");
        $('#play_all_feedback').attr('checked', false);
        return false;
    }
})
function getCheckCount()
{
    var x = 0;
    for (var i = 0; i < ParticipantForm.elements.length; i++)
    {
        if (ParticipantForm.elements[i].checked == true)
        {
            x++;
        }
    }
    return x;
}
function RemoveAllParticipant() {
    if (getCheckCount() == 0) {
        ShowAlret("Please select record from the list.", 'error');
        return false;
    }
    $.confirm({
        title: 'Confirm!',
        content: 'Remove Selected User(s) ?',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        type: "POST",
                        data: $('#ParticipantForm').serialize(),
                        url: base_url + "workshop/Removeall_participants/" + EncodeEdit_id,
                        success: function (response_json) {
                            var response = JSON.parse(response_json);
                            ShowAlret(response.message, response.alert_type);
                            DatatableUsersRefresh();
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
function UploadXlsTrainee() {
    $('#modalerrordiv').hide();
    if ($('#filename').val() == "") {
        ShowAlret("Please select xls file.!", 'error');
        return false;
    }
    var file_data = $('#filename').prop('files')[0];
    var form_data = new FormData();
    form_data.append('filename', file_data);
    form_data.append('company_id', company_id);
    var other_data = $('#ImportForm').serializeArray();
    $.each(other_data, function (key, input) {
        form_data.append(input.name, input.value);
    });
    $.ajax({
        cache: false,
        contentType: false,
        processData: false,
        type: "POST",
        url: base_url + 'workshop/UploadTraineeXls/' + EncodeEdit_id,
        data: form_data,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
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
            ShowAlret("Status: " + textStatus + " ,Contact Atomapp for technical support!");
        }
    });
}
function QsetTrainerSave(type) {
    if (type == 1) {
        var surl = base_url + "workshop/ConfirmQuestionsetTrainer/" + token_key;
    } else {
        surl = base_url + "workshop/confirm_feedback_set/" + token_key;
    }
    $.ajax({
        url: surl,
        type: 'POST',
        data: $('#TrainerDataForm').serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                $('#LoadModalFilter').data('modal', null);
                $('#CloseModalBtn').click();
			} else {
                //$('#errordiv').show();
                ShowAlret(Data['Msg'], 'error');
                //$('#errorlog').html(Data['Msg']);
            }
            customunBlockUI();
        }
    });
}
function getTopic_subtopic(QuestionSet_id, session, isnew) {
    var lockflag = 0;
    if (session == 2) {
        $.each($("#pre_question_set option:selected"), function () {
            if ($(this).val() == QuestionSet_id) {
                lockflag = 1;
                return false;
            }
        });
    }
    var workshop_id = '';
    if (AddEdit == 'E' || AddEdit == 'V') {
        workshop_id = EncodeEdit_id;
    }
    if (isnew) {
        var surl = base_url + "workshop/topicSubtCreat/" + workshop_id;

    } else {
        surl = base_url + "workshop/topicSubtopic/" + workshop_id;
    }
    $.ajax({
        url: surl,
        type: 'POST',
        data: {QuestionSet_id: QuestionSet_id, lockflag: lockflag, token_key: token_key, session: session, type: 1
            , df_trainer_id: $('#df_trainer_id').val(), AddEdit: AddEdit},
        beforeSend: function () {
            customBlockUI();
        },
        success: function (html) {
            $('#modal-body').html(html);
            $('#LoadModalFilter').modal();
            if (AddEdit == 'E') {
                questionTable(QuestionSet_id, session, isnew, 1);
            }
            customunBlockUI();
        }
    });
}
function get_feeback_subtopic(QuestionSet_id, session, isnew) {
    var lockflag = 0;
//        if(session==2){
//            $.each($("#pre_feedback_id option:selected"), function(){            
//                if($(this).val()==QuestionSet_id){
//                    lockflag=1;
//                    return false; 
//                }
//            });
//        }
    var workshop_id = '';
    if (AddEdit == 'E' || AddEdit == 'V') {
        workshop_id = EncodeEdit_id;
    }
    if (isnew) {
        var surl = base_url + "workshop/getfeedback_set/" + workshop_id;
    } else {
        surl = base_url + "workshop/edit_feedback_set/" + workshop_id;
    }
    $.ajax({
        url: surl,
        type: 'POST',
        data: {QuestionSet_id: QuestionSet_id, lockflag: lockflag, token_key: token_key, session: session, type: 1, AddEdit: AddEdit},
        beforeSend: function () {
            customBlockUI();
        },
        success: function (html) {
            $('#modal-body').html(html);
            $('#LoadModalFilter').modal();
            if (AddEdit == 'E' || AddEdit == 'V') {
                questionTable(QuestionSet_id, session, isnew, 2);
            }
            customunBlockUI();
        }
    });
}
function set_same_trainer(key) {
    var trainer = $('#qset_trainer_' + key).val();
    //$('#qset_trainer_2').val(trainer);
    $('.trainerclass').val(trainer).trigger('change');

}
function questionTable(Qset_id, session, isnew, type) {
    var url = "";
    var visibility = '';
    //var Qset_id = $('#Qset_id').val();
    //var disabled = $('#qus_lockflag').val();
    var lockflag = 0;
    if (session == 2) {
        $.each($("#pre_question_set option:selected"), function () {
            if ($(this).val() == Qset_id) {
                lockflag = 1;
                return false;
            }
        });
    }
    if (lockflag == "") {
        lockflag = 0;
    }
    if (type == 1) {
        url = base_url + "workshop/Question_tableRefresh/" + EncodeEdit_id + "/" + Qset_id + "/" + session + "/" + isnew + "/" + lockflag;
        visibility = true;
    } else {
        url = base_url + "workshop/FeedbackQuestion_tableRefresh/" + EncodeEdit_id + "/" + Qset_id + "/" + session + "/" + isnew + "/" + lockflag;
        ;
        visibility = false;
    }
    oTable = $('#question_table').dataTable({
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
        //"bStateSave": true,
        "lengthMenu": [
            [50, 100, 500, -1],
            [50, 10, 500, "All"]
        ],
        "dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
        //scrollY:        '50vh',
        rowReorder: true,
//         rowReorder: {
//            selector: 'tr'
//        },

        select: true,
        //"sScrollXInner": "100%",

        "pageLength": 50,
        "pagingType": "bootstrap_full_number",
        "columnDefs": [
            {'width': '50px', 'orderable': true, 'searchable': false, 'targets': [0], className: 'reorder'},
            {'width': '50px', 'orderable': false, 'searchable': true, 'targets': [1]},
            {'width': '50px', 'orderable': false, 'searchable': true, 'targets': [2]},
            {'width': '50px', 'orderable': false, 'searchable': true, 'targets': [3]},
            {'width': '100px', 'orderable': false, 'searchable': true, 'targets': [4]},
            {'width': '', 'orderable': false, 'searchable': true, 'targets': [5]},
            {'width': '180px', 'orderable': false, 'searchable': true, 'targets': [6], 'visible': visibility}
        ],
        scrollCollapse: true,
        paging: false,
        "order": [
            [0, "asc"]
        ],
        //"ordering": false,
        "sAjaxSource": url,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
                //$(".make-switch").bootstrapSwitch();
                enabled_sorting();
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        }
    });

}
function UpdateSorting(type) {
    $.ajax({
        type: "POST",
        url: base_url + "workshop/update_sorting/" + EncodeEdit_id + "/" + type,
        data: $('#QTableForm').serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
				//questionTable(type);                
            } else {
                $('#errordiv1').show();
                $('#errorlog1').html(Data['Msg']);
                App.scrollTo(form_error, -200);
            }
            customunBlockUI();
        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
            ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
        }
    });
}
function enabled_sorting() {
    var qus_orderby = $('#qus_orderby').val();
    var table = $('#question_table').DataTable();
    if (qus_orderby == 1) {
        table.rowReorder.disable();
        // oTable.rowReorder.disable();
    } else {
        table.rowReorder.enable();
    }
}
