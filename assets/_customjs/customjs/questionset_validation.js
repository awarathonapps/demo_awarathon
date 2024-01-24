var QTableForm = document.QTableForm;
var frmQuestion = $('#frmQuestion');
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
        timer: {
            digits: true
        },
        weight: {
            digits: true,
            min:0
        },
        reward: {
            digits: true,
            min:0
        },

        powered_by: {
            required: true
        },
        status: {
            required: true
        },
        url: {
            validateUrl: true
        },
        'topic_id[]': {
            required: true
                    //notEqualToGroup: ['.ValueUnq']
        },
        'subtopic_id[][]': {
            required: true
        },
        'trainer_id[]': {
            required: true
        },
        'language_id': {
            required: true
        },
//        'New_topic_id[]': {
//            required: true,
//            notEqualToGroup: ['.ValueUnq']
//        },
//        'New_subtopic_id[]': {
//            isRequirdSubTopic: true
//        },
        'New_trainer_id[]': {
            required: true
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
    var lEncode_id=Encode_id;
    if(AddEdit=='C'){
        lEncode_id ="";
    }
    var isSuccess = false;
    $.ajax({
        type: "POST",
        data: {questionset: value, company_id: $('#company_id').val(), questionset_id: lEncode_id},
        url: Base_url + "Questionset/Check_questionset",
        async: false,
        success: function (msg) {
            isSuccess = msg != "" ? false : true;
        }
    });
    return isSuccess;
}
, "QuestionSet already exists!!!");
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
}, $.validator.format("Same Topic selected."));
function SaveQuestionSet(lAddEdit) {
     form_error.hide();
    if (!$('#frmQuestion').valid()) {
        return false;
    }
    if (Totaltrainer == 1) {
        ShowAlret("Please Add Trainer & Topic..","error");
        return false;
    }
    if(AddEdit=='A'){
        var url = Base_url + 'questionset/submit/';
    }else if(AddEdit=='C'){
        url = Base_url + 'questionset/submit/'+ Encode_id;
    }else{
        url = Base_url + 'questionset/update/' + Encode_id
    }
    $.ajax({
        type: "POST",
        url: url,
        data: $('#frmQuestion').serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            //alert(result);
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                if(AddEdit=='A' || AddEdit=='C'){
                    setTimeout(function(){// wait for 5 secs(2)
                        window.location.href = Base_url+'questionset/edit/'+Data['id']+"/2";
                    }, 500); 
                }else{
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
function CompanyChange() {
    if(Totaltrainer >1){
        TrainerArrray = [];
        $("#trainerDatatable tbody tr").remove();
    }
}
function getComapnywiseTopic() {
    $.ajax({
        type: "POST",
        data: "data=" + $('#company_id').val(),
        async: false,
        url: Base_url + "questionset/ajax_company_topic",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var TopicMSt = Oresult['result'];
                var TrainerMSt = Oresult['trainerResult'];
                var option = '<option value="">Please Select</option>';
                var option1 = '<option value="">Please Select</option>';
                for (var i = 0; i < TopicMSt.length; i++) {
                    var isDisabled = '';
                    if (TrainerArrray.length > 0) {
                        for (var j = 0; j < TrainerArrray.length; j++) {
                            if (TrainerArrray[j]['data']['topic_id'] == TopicMSt[i]['id']) {
                                isDisabled = 'disabled';
                                break;
                            }
                        }
                    }
                    option += '<option value="' + TopicMSt[i]['id'] + '" ' + isDisabled + '>' + TopicMSt[i]['description'] + '</option>';
                }
                for (var i = 0; i < TrainerMSt.length; i++) {
                    option1 += '<option value="' + TrainerMSt[i]['userid'] + '">' + TrainerMSt[i]['username'] + '</option>';
                }

                $('#topic_id').empty();
                $('#topic_id').append(option);
                $('#trainer_id').empty();
                $('#trainer_id').append(option1);
            } 
            customunBlockUI();
        }
    });
}
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
            {'width': '50px', 'orderable': true, 'searchable': true, 'targets': [1]},
            {'width': '50px', 'orderable': true, 'searchable': true, 'targets': [2]},
            {'width': '50px', 'orderable': true, 'searchable': true, 'targets': [3]},
            {'width': '50px', 'orderable': true, 'searchable': true, 'targets': [4]},
            {'width': '150px', 'orderable': true, 'searchable': true, 'targets': [5]},
            {'width': '100px', 'orderable': false, 'searchable': false, 'targets': [6]},
            {'width': '100px', 'orderable': false, 'searchable': false, 'targets': [7]}
        ],
        "order": [
            [1, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": Base_url + "questionset/Question_tableRefresh/" + Encode_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
            aoData.push({name: 'CheckedValue', value: $('input[name=userrights_type]:checked').val()});
            aoData.push({name: 'search_topic', value: $('#search_topic').val()});
            aoData.push({name: 'search_subtopic', value: $('#search_subtopic').val()});
            aoData.push({name: 'search_trainer', value: $('#search_trainer').val()});
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
function StatusChange()
{
    $('input[name="status_switch[]"]').on('switchChange.bootstrapSwitch', function (event, st) {
        var Q_id = this.value;
        var status = this.checked;
        $.ajax({
            type: "POST",
            url: Base_url + 'questionset/StatusUpdate/' + Encode_id,
            data: {Qstatus: status, Q_id: Q_id},
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
                }
                 customunBlockUI();
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
                        url: Base_url + "questionset/QuestionTable_actions/" + Encode_id + '/' + opt,
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
function getTopicwiseSubtopic(CurrentTrainer) {
    var CurrentTopic = $('#topic_id' + CurrentTrainer).val();
    if (CurrentTopic == null) {
        return false;
    }
    $.ajax({
        type: "POST",
        data: "data=" + $('#topic_id' + CurrentTrainer).val(),
        async: false,
        url: Base_url + "questionset/ajax_topic_subtopic",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var SubTopicMSt = Oresult['result'];
                var option = '';
                if (SubTopicMSt.length > 0) {
                    for (var i = 0; i < SubTopicMSt.length; i++) {
                        option += '<option value="' + SubTopicMSt[i]['id'] + '" selected>' + SubTopicMSt[i]['description'] + '</option>';
                    }
                } else {
                    var option = '<option value="0" selected>No sub-topic</option>';
                }
                $('#subtopic' + CurrentTrainer).empty();
                $('#subtopic' + CurrentTrainer).append(option);
                //$("#topic_id").trigger("change");
            }
             customunBlockUI();
        }
    });
}
function ConfirmTrainer() {
    var Company_id = $('#company_id').val();
    if(Company_id==""){
        ShowAlret("Please select Company first..!", 'warning');
        return false;
    }
    $.ajax({
        url: Base_url + "questionset/gettrainer/" + Totaltrainer,
        type: 'POST',
        data: "cmp_id=" + Company_id,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            //TrainerArrray.push(Data['trainerData']);
            if(Totaltrainer==1){
                $("#Row-0").remove();
            }
            $('#trainerDatatable').append(Data['htmlData']);
            $('#subtopic' + Totaltrainer).select2();
            $('#topic_id' + Totaltrainer).select2();
            $('#trainer_id' + Totaltrainer).select2();
            $('.select2').on('change', function () {
                $(this).valid();
            });
            Totaltrainer++;
             customunBlockUI();
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
function getSubtopic() {
    $.ajax({
        type: "POST",
        data: "data=" + $('#search_topic').val(),
        async: false,
        url: Base_url+"questionset/ajax_topic_subtopic",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var SubTopicMSt = Oresult['result'];
                var option = '<option value="">Select All</option>';
                for (var i = 0; i < SubTopicMSt.length; i++) {
                    option += '<option value="' + SubTopicMSt[i]['id'] + '" >' + SubTopicMSt[i]['description'] + '</option>';
                }
                $('#search_subtopic').empty();
                $('#search_subtopic').append(option);
                //$("#topic_id").trigger("change");
                 customunBlockUI();
            }
        }
    });
}
function ResetFilter() {
    $('#search_topic,#search_subtopic,#search_status,#search_trainer').val(null).trigger('change');
    questionTable();
}
//$('#company_id1').select2({
//    placeholder: 'Please Select',
//    separator: ',',
//    ajax: {
//        url: Base_url + "questionset/ajax_feedback_company",
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
//        return $.getJSON(Base_url + "questionset/ajax_feedback_company?id=" + (element.val()), null, function (data) {
//            return callback(data);
//        });
//    }
//});