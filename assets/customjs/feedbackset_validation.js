var QTableForm = document.QTableForm;
var frmQuestion = $('#frmFeedbackSet');
var form_error = $('.alert-danger', frmQuestion);
var form_success = $('.alert-success', frmQuestion);
frmQuestion.validate({
    errorElement: 'span',
    errorClass: 'help-block help-block-error',
    focusInvalid: false,
    ignore: "",
    rules: {
        company_id: {
            required: true
        },
        feedback_name: {
            required: true,
            feedbackCheck: true
        },
        no_of_question: {
            required: true,
            digits: true,
            questionCheck: true
        },
        powered_by: {
            required: true
        },
        status: {
            required: true
        },
        feedback_type: {
            required: true
        },
        url: {
            validateUrl: true
        },
        'New_subtype_id[]': {
            required: true
        },
        'New_ftype_id[]': {
            required: true,
            notEqualToGroup: ['.ValueUnq']
        },
        language_id :{
            required: true,
        }
    },
    invalidHandler: function (event, validator) {
        form_success.hide();
        form_error.show();
        App.scrollTo(form_error, -200);
    },
    errorPlacement: function (error, element) { // render error placement for each input type
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
        Ladda.bind('button[id=questionset-submit]');
        form.submit();
    }
});
$('.select2,.select2-multiple').on('change', function () {
    $(this).valid();
});
jQuery.validator.addMethod("validateUrl", function (val, element) {
    if (val.length == 0) {
        return true;
    }
    if (!/^(https?|ftp):\/\//i.test(val)) {
        val = 'http://' + val; // set both the value
        $(element).val(val); // also update the form element
    }
    return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&amp;'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(val);
}, "Please enter valid url");
jQuery.validator.addMethod("feedbackCheck", function (value, element) {
    var isSuccess = false;
    var lEncode_id = Encode_id;
    if (AddEdit == 'C') {
        lEncode_id = "";
    }
    $.ajax({
        type: "POST",
        data: {feedback: value, company_id: $('#company_id').val(), feedback_id: lEncode_id},
        url: Base_url + "Feedback_set/Check_feedback",
        async: false,
        success: function (msg) {
            isSuccess = msg != "" ? false : true;
        }
    });
    return isSuccess;
}
, "Feedback already exists!!!");
jQuery.validator.addMethod("isRequirdSubTopic", function (value, element) {
    var subtopic = element.id;
    var RowID = subtopic.substring(8);
    if (value != null) {
        return true;
    }
    var topic_id = $('#topic_id' + RowID).val();
    if (topic_id == null || topic_id == "") {
        return true;
    }
    var isSuccess = false;
    $.ajax({
        type: "POST",
        data: {data: topic_id},
        url: Base_url + "Questionset/ajax_topic_subtopic",
        async: false,
        success: function (Odata) {
            isSuccess = Odata.length > 0 ? false : true;
        }
    });
    return isSuccess;
}
, "This field is required.");
$.validator.addMethod("notEqualToGroup", function (value, element, options) {
    // get all the elements passed here with the same class
    var elems = $(element).parents('form').find(options[0]);
    // the value of the current element
    var valueToCompare = value;
    // count
    var matchesFound = 0;
    // loop each element and compare its value with the current value
    // and increase the count every time we find one
    $.each(elems, function () {
        var thisVal = $(this).val();
        if (thisVal == valueToCompare) {
            matchesFound++;
        }
    });
    // count should be either 0 or 1 max
    if (this.optional(element) || matchesFound <= 1) {
        //elems.removeClass('error');
        return true;
    } else {
        //elems.addClass('error');
    }
}, $.validator.format("Same Type selected."));
jQuery.validator.addMethod("questionCheck", function (value, element) {
    if (value > 0) {
        return true;
    } else {
        return false;
    }
}
, "Please enter the Greater than zero!!!");
function SaveFeedbackSet() {
    if (!$('#frmFeedbackSet').valid()) {
        return false;
    }
    if (Totaltrainer == 1) {
        ShowAlret("Please Add Feedback Type & Sub Type..", "error");
        return false;
    }
    if (AddEdit == 'A') {
        var url = Base_url + 'feedback_set/submit/';
    } else if (AddEdit == 'C') {
        url = Base_url + 'feedback_set/submit/' + Encode_id;
    } else {
        url = Base_url + 'feedback_set/update/' + Encode_id
    }
    $.ajax({
        type: "POST",
        url: url,
        data: $('#frmFeedbackSet').serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                if (AddEdit == 'A' || AddEdit == 'C') {
                    setTimeout(function () {// wait for 5 secs(2)
                        window.location.href = Base_url + 'feedback_set/edit/' + Data['id'] + "/2";
                    }, 500);
                } else {
                    questionTable();
                }
            } else {
                $('#errordiv').show();
                $('#errorlog').html(Data['Msg']);
                App.scrollTo(form_error, -200);
            }
            customunBlockUI();
        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
            ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
        }
    });
}
function feedbackTypeData() {
    $.ajax({
        type: "POST",
        data: "data=" + $('#company_id').val(),
        async: false,
        url: Base_url + "feedback_set/ajax_company_feedbackType",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var FeedbackTypeMSt = Oresult['result'];
                var option = '<option value="">Please Select</option>';
                for (var i = 0; i < FeedbackTypeMSt.length; i++) {
                    option += '<option value="' + FeedbackTypeMSt[i]['id'] + '">' + FeedbackTypeMSt[i]['description'] + '</option>';
                }
                $('#feedback_type').empty();
                $('#feedback_type').append(option);
            }
            customunBlockUI();
        }
    });
}
function getTypewiseSubtype(ltype_id) {
    if ($('#ftype_id' + ltype_id).val() == "") {
        return false;
    }
    $.ajax({
        type: "POST",
        data: "data=" + $('#ftype_id' + ltype_id).val(),
        async: false,
        url: Base_url + "feedback_set/ajax_type_subtype",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var SubTypeMSt = Oresult['result'];
                var option = '';
                for (var i = 0; i < SubTypeMSt.length; i++) {
                    option += '<option value="' + SubTypeMSt[i]['id'] + '" selected>' + SubTypeMSt[i]['description'] + '</option>';
                }
                $('#subtype' + ltype_id).empty();
                $('#subtype' + ltype_id).append(option);
            }
            customunBlockUI();
        }
    });
}
function ConfirmType() {
    if ($('#company_id').val() != '') {
        $.ajax({
            url: Base_url + "feedback_set/gettype/" + Totaltrainer,
            type: 'POST',
            data: {cmp_id: $('#company_id').val(), selected_type: SelectedArrray},
            beforeSend: function () {
                customBlockUI();
            },
            success: function (Odata) {
                var Data = $.parseJSON(Odata);
                $('#typeDatatable').append(Data['htmlData']);
                $('#subtype' + Totaltrainer).select2();
                $('#ftype_id' + Totaltrainer).select2();
                Totaltrainer++;
                customunBlockUI();
            }

        });

    } else {
        ShowAlret("Please Select Company!!", "error");
        return false;
    }
}
function CompanyChange() {
    if (Totaltrainer > 1) {
        TrainerArrray = [];
        $("#typeDatatable tbody tr").remove();
    }
}
function StatusChange()
{
    $('input[name="status_switch[]"]').on('switchChange.bootstrapSwitch', function (event, st) {
        var Q_id = this.value;
        var status = this.checked;
        $.ajax({
            type: "POST",
            url: Base_url + 'feedback_set/StatusUpdate/' + Encode_id,
            data: {Qstatus: status, Q_id: Q_id},
            success: function (Odata) {
                var Data = $.parseJSON(Odata);
                if (Data['success']) {
                    ShowAlret(Data['Msg'], 'success');
                } else {
                    $('#errordiv').show();
                    $('#errorlog').html(Data['Msg']);
                }
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
            }
        });
    });
}

function ValidCheckbox(opt) {
    var Check = getCheckCount();
    if (Check > 0) {
        if (opt == 1) {
            LoadConfirmDialog("Confirm Active Status ?", opt);
        } else if (opt == 2) {
            LoadConfirmDialog("Confirm InActive Status ?", opt);
        }
    } else {
        ShowAlret("Please select record from the list.", 'error');
        return false;
    }
}
function getCheckCount() {
    var x = 0;
    $(".leftchk").each(function (index) {
        if ($(this).is(':checked')) {
            x++;
        }
    });
    return x;
}
function LoadConfirmDialog(content, opt) {
    $.confirm({
        title: 'Confirm!',
        content: content,
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        type: "POST",
                        data: $('#QTableForm').serialize(),
                        url: Base_url + "feedback_set/QuestionTable_actions/" + Encode_id + '/' + opt,
                        success: function (response_json) {
                            var response = JSON.parse(response_json);
                            ShowAlret(response.message, response.alert_type);
                            questionTable();
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
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });

    //Totaltrainer--;
}

function ResetFilter() {
    $('#search_type,#search_subtype,#search_status').val(null).trigger('change');
    questionTable();
}
//$('#company_id').select2({
//    placeholder: 'Please Select',
//    separator: ',',
//    ajax: {
//        url: Base_url + "feedback_set/ajax_feedback_company",
//        dataType: 'json',
//        quietMillis: 100,
//        data: function (term, page) {
//            return {
//                search: term,
//                page_limit: 10
//            };
//        },
//        results: function (data, page) {
//            var more = (page * 30) < data.total_count;
//            return {results: data.results, more: more};
//        }
//    },
//    initSelection: function (element, callback) {
//        return $.getJSON(Base_url + "feedback_set/ajax_feedback_company?id=" + (element.val()), null, function (data) {
//            return callback(data);
//        });
//    }
//});
function questionTable() {
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
            {'width': '60px', 'orderable': true, 'searchable': true, 'targets': [2]},
            {'width': '', 'orderable': true, 'searchable': true, 'targets': [3]},
            {'width': '', 'orderable': true, 'searchable': true, 'targets': [4]},
            {'width': '', 'orderable': false, 'searchable': false, 'targets': [5]}
        ],
        "order": [
            [1, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": Base_url + "feedback_set/Question_tableRefresh/" + Encode_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
            aoData.push({name: 'search_type', value: $('#search_type').val()});
            aoData.push({name: 'search_subtype', value: $('#search_subtype').val()});
            aoData.push({name: 'search_status', value: $('#search_status').val()});
            aoData.push({name: 'language_id', value: $('#language_id').val()});
            aoData.push({name: 'AddEdit', value: AddEdit});
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
                $(".make-switch").bootstrapSwitch();
                StatusChange();
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        }
        , "fnFooterCallback": function (nRow, aData) {
        }
    });
}
function getSearchSubType() {
    if($('#search_type').val()==""){
        $('#search_subtype').empty();
        return false;
    }
    $.ajax({
        type: "POST",
        data: "data=" + $('#search_type').val(),
        async: false,
        url: Base_url + "feedback_set/ajax_type_subtype",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var SubTypeMSt = Oresult['result'];
                var option = '<option value="">All Select</option>';
                for (var i = 0; i < SubTypeMSt.length; i++) {
                    option += '<option value="' + SubTypeMSt[i]['id'] + '" >' + SubTypeMSt[i]['description'] + '</option>';
                }
                $('#search_subtype').empty();
                $('#search_subtype').append(option);
            }
            customunBlockUI();
        }
    });
}