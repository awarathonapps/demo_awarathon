AssessmentForm.validate({
    errorElement: 'span',
    errorClass: 'help-block help-block-error',
    focusInvalid: false,
    ignore: "",
    rules: {
        assessment_type: {
            required: true
        },
        assessment_name: {
            required: true,
            assessmentCheck: true,
            alphanumeric: true,
        },
        start_date: {
            required: true,
        },
        end_date: {
            required: true,
        },
        time_limit: {
            required: true,
        },
        // assessor_date: {
        //     required: true,
        // },
        // 'parameter_id[]': {
        //     required: true
        // },
        instruction: {
            required: true
        },
        number_attempts: {
            required: true
        },
        // ratingstyle: {
        //     required: true
        // },
        //        'New_parameter_id[]': {
        //            required: true
        //        },
        status: {
            required: true
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
        //Ladda.bind('button[id=questionset-submit]');
        form.submit();
    }
});
// $('.select2,.select2-multiple').on('change', function () {
//     $(this).valid();
// });
jQuery.validator.addMethod("assessmentCheck", function (value, element) {
    var Edit_id = Encode_id;
    if (AddEdit == 'C') {
        Edit_id = '';
    }
    var isSuccess = false;
    $.ajax({
        type: "POST",
        data: {
            assessment: value,
            assessment_type: $('#assessment_type').val(),
            company_id: $('#company_id').val(),
            assessment_id: Edit_id
        },
        url: Base_url + "create_trinity/Check_assessment",
        async: false,
        success: function (msg) {
            isSuccess = msg != "" ? false : true;
        }
    });
    return isSuccess;
}, "Assessment already exists!!!");
jQuery.validator.addMethod("alphanumeric", function (value, element) {
    return this.optional(element) || /^[a-zA-Z0-9-( )#_]+$/i.test(value);
}, "Only letters, numbers, round brackets and underscores please");

function RowDelete(r) {
    $.confirm({
        title: 'Confirm!',
        content: "Are you sure you want to remove this question.?",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $("#Row-" + r).remove();
                    Selected_QuestionArray.splice($.inArray(r, Selected_QuestionArray), 1);
                    //                    var index = Selected_QuestionArray.indexOf(r);
                    //                    if (index !== -1) Selected_QuestionArray.splice(index, 1);
                    // getUnique_paramters();
                    //Get Question count after removal of question 
                    var tempQuestions = [];
                    TempSubParameterArray = TempSubParameterArray.filter(function (obj) {
                        if (jQuery.inArray(parseInt(obj.txn_id), tempQuestions) === -1) {
                            tempQuestions.push(parseInt(obj.txn_id));
                        }
                        if ((parseInt(obj.txn_id) == parseInt(r))) {
                            return false;
                        } else {
                            return true;
                        }
                    });
                    Totalqstn = tempQuestions.length;
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}

function AssessmentChange() {
    if (Totalqstn > 1) {
        TotalqstnArray = [];
        $("#VQADatatable tbody tr").remove();
        Totalqstn = 1; //reset to 1
        TempSubParameterArray = [];
    }
}

// function getparameter(Q_id, srno, cnt) {
//     $.ajax({
//         url: Base_url + "create_trinity/getquestionwiseparameter/" + Q_id + "/" + srno,
//         type: 'POST',
//         data: {
//             company_id: $('#company_id').val(),
//             ass_result_id: $('#ass_result_id').val(),
//             assessment_id: $('#assessment_id').val(),
//             assessment_type: $('#assessment_type').val(),
//             user_id: $('#user_id').val(),
//             trainer_id: $('#trainer_id').val()
//         },
//         beforeSend: function () {
//             customBlockUI();
//         },
//         success: function (Odata) {
//             var Data = $.parseJSON(Odata);
//             $('#selectedquestion').html(Data['Question']);
//             $('#parameter_table_div').html(Data['QParameter_table']);
//             $("#question_id").val(Q_id);
//             $('#remark_que').val(Data['question_comments']);
//             //                $("#your_rating").html(Data['your_rating']);
//             if (srno == cnt && Data['cnt_rate'] == 0) {
//                 $('.sh-btn').show();
//             } else {
//                 $('.sh-btn').hide();
//             }
//             customunBlockUI();
//         }
//     });
// }

function UpdateAssessment() {
    form_error.hide();

    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
    if (!$('#AssessmentForm').valid()) {
        return false;
    }

    // if ($('#isweights').prop("checked") == true) {
    //     var total_percent = 0;
    //     $('.percent_cnt').each(function () {
    //         total_percent += parseFloat($(this).val()) || 0; // Or this.innerHTML, this.innerText
    //     });
    //     if (total_percent != '100' || total_percent != '100.00') {
    //         ShowAlret("Total weight of selected parameters must be 100%", "error");
    //         return false;
    //     }
    // }
    // if (TempSubParameterArray.length > 0) {
    //     var total_weight = check_overall_parameter_weights(TempSubParameterArray, Totalqstn);
    //     if (total_weight > 0 && total_weight != 100) {
    //         ShowAlret("Total weight of selected parameters for each question must be 100%", "error");
    //         return false;
    //     }
    // } else {
    //     ShowAlret("Please Add Parameter to the selected questions.", "error");
    //     return false;
    // }
    var form_data = {};
    var other_data = $('#AssessmentForm').serializeArray();
    $.each(other_data, function (key, input) {
        if (form_data.hasOwnProperty(input.name)) {
            form_data[input.name] = form_data[input.name] + "," + input.value;
        } else {
            form_data[input.name] = input.value;
        }
    });
    form_data['assessor_date'] = $("#assessor_date").val();
    // var x = 0;
    // var sub_parameter = [];
    // $.each(TempSubParameterArray, function (key, input) {
    //     sub_parameter[x] = input;
    //     x++;
    // });
    // form_data['sub_parameter'] = sub_parameter;
    // $('.language_id').each(function () {
    //     var id = this.id;
    //     var val = $("option:selected", this).val();
    //     form_data[id] = val;
    // });
    $.ajax({
        cache: false,
        async: true,
        type: "POST",
        url: Base_url + "create_trinity/update/" + Encode_id,
        dataType: "json",
        data: form_data,
        // data: $('#AssessmentForm').serialize() + "&sub_parameter=" + JSON.stringify(TempSubParameterArray),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            //var Data = $.parseJSON(Odata);
            var Data = Odata;
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                setTimeout(function () { // wait for 5 secs(2)
                    window.location.href = Base_url + 'create_trinity/edit/' + Encode_id + "/1.2";
                }, 500);
            } else {
                $('#errordiv').show();
                $('#errorlog').html(Data['Msg']);
                App.scrollTo(form_error, -200);
            }
            customunBlockUI();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
        }
    });
}

function on_questionchange(key) {
    // $("#parameter_id" + key).select2("val", "");
    $('#parameter_id option[value=""]').attr('selected', 'selected');
}
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
                        url: Base_url + "create_trinity/RemoveParticipantUser/" + Encode_id,
                        type: 'POST',
                        data: {
                            Remove_id: Remove_id
                        },
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
        "columnDefs": [{
                'width': '30px',
                'orderable': false,
                'searchable': false,
                'targets': [0]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [1]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [2]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [3]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [4]
            },
            {
                'width': '80px',
                'orderable': true,
                'searchable': true,
                'targets': [5]
            },
            {
                'width': '80px',
                'orderable': true,
                'searchable': false,
                'targets': [6]
            }
        ],
        "order": [
            [1, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": Base_url + "create_trinity/ParticipantUsers/" + Encode_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({
                name: '__mode',
                value: 'featuredimage.ajaxload'
            });
            aoData.push({
                name: 'fttrainer_id',
                value: $('#fttrainer_id').val()
            });
            aoData.push({
                name: 'ftroute_trainer_id',
                value: $('#ftroute_trainer_id').val()
            });

            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        },
        "fnFooterCallback": function (nRow, aData) {}
    });
}

function DatatableManagersRefresh() {
    var table = $('#ManagersTable');
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
        "columnDefs": [{
                'width': '30px',
                'orderable': false,
                'searchable': false,
                'targets': [0]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [1]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [2]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [3]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [4]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [5]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [6]
            }
        ],
        "order": [
            [1, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": Base_url + "create_trinity/MappingManagers/" + Encode_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({
                name: '__mode',
                value: 'featuredimage.ajaxload'
            });
            aoData.push({
                name: 'fttrainer_id',
                value: $('#fttrainer_id').val()
            });
            aoData.push({
                name: 'ftroute_trainer_id',
                value: $('#ftroute_trainer_id').val()
            });
            aoData.push({
                name: 'NewManagersArrray',
                value: NewManagersArrray
            });
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        },
        "fnFooterCallback": function (nRow, aData) {}
    });
}

function DatatableSupervisorRefresh() {
    var table = $('#SupervisorTable');
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
        "columnDefs": [{
                'width': '30px',
                'orderable': false,
                'searchable': false,
                'targets': [0]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [1]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [2]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [3]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [4]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [5]
            }
        ],
        "order": [
            [1, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": Base_url + "create_trinity/MappingSupervisors/" + Encode_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({
                name: '__mode',
                value: 'featuredimage.ajaxload'
            });
            aoData.push({
                name: 'fttrainer_id',
                value: $('#fttrainer_id').val()
            });
            aoData.push({
                name: 'ftroute_trainer_id',
                value: $('#ftroute_trainer_id').val()
            });
            aoData.push({
                name: 'NewSupervisorsArrray',
                value: NewSupervisorsArrray
            });
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        },
        "fnFooterCallback": function (nRow, aData) {}
    });
}

function DatatableUserManagersRefresh() {
    var table = $('#UserManagersTable');
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
        "columnDefs": [{
                'width': '30px',
                'orderable': false,
                'searchable': true,
                'targets': [0]
            },
            {
                'width': '30px',
                'orderable': true,
                'searchable': true,
                'targets': [1]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [2]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [3]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [4]
            }
        ],
        "order": [
            [0, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": Base_url + "create_trinity/UserMappingManagers/" + Encode_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({
                name: '__mode',
                value: 'featuredimage.ajaxload'
            });
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        },
        "fnFooterCallback": function (nRow, aData) {}
    });
}

function getCheckCount() {
    var x = 0;
    for (var i = 0; i < ParticipantForm.elements.length; i++) {
        if (ParticipantForm.elements[i].checked == true) {
            x++;
        }
    }
    return x;
}

function getCheckCnt(iscreate_mode) {
    var x = 0;
    if (iscreate_mode == 1) {
        var checkbox = document.getElementsByName('Mapping_all[]');
        for (var i = 0; i < checkbox.length; i++) {
            if (checkbox[i].checked) {
                x++;
            }
        }
    } else {
        for (var i = 0; i < MappingForm.elements.length; i++) {
            if (MappingForm.elements[i].checked == true) {
                x++;
            }
        }
    }
    return x;
}

function getCheckCt(iscreate_mode) {
    var x = 0;
    if (iscreate_mode == 1) {
        var checkbox = document.getElementsByName('Mappsuper_all[]');
        for (var i = 0; i < checkbox.length; i++) {
            if (checkbox[i].checked) {
                x++;
            }
        }
    } else {
        for (var i = 0; i < MappingSuperForm.elements.length; i++) {
            if (MappingSuperForm.elements[i].checked == true) {
                x++;
            }
        }
    }
    return x;
}

function getCheckUCnt() {
    var x = 0;
    for (var i = 0; i < UserMappingForm.elements.length; i++) {
        if (UserMappingForm.elements[i].checked == true) {
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
                        url: Base_url + "create_trinity/Removeall_participants/" + Encode_id,
                        success: function (response_json) {
                            var response = JSON.parse(response_json);
                            ShowAlret(response.message, response.alert_type);
                            DatatableUsersRefresh();
                            DatatableUserManagersRefresh();
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

function RemoveAllMappingManagers(iscreate_mode) {
    if (getCheckCnt(iscreate_mode) == 0) {
        ShowAlret("Please select record from the list.", 'error');
        return false;
    }
    if (iscreate_mode == 1) {
        var fdata = $('#AssessmentForm').serialize();
    } else {
        var fdata = $('#MappingForm').serialize();
    }
    $.confirm({
        title: 'Confirm!',
        content: 'Remove Selected Manager(s) ?',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        type: "POST",
                        data: fdata,
                        url: Base_url + "create_trinity/Removeall_mapping/" + Encode_id,
                        success: function (response_json) {
                            var response = JSON.parse(response_json);
                            ShowAlret(response.message, response.alert_type);
                            DatatableManagersRefresh();
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

function RemoveAllMappingSupervisors(iscreate_mode) {
    if (getCheckCt(iscreate_mode) == 0) {
        ShowAlret("Please select record from the list.", 'error');
        return false;
    }
    if (iscreate_mode == 1) {
        var fdata = $('#AssessmentForm').serialize();
    } else {
        var fdata = $('#MappingSuperForm').serialize();
    }
    $.confirm({
        title: 'Confirm!',
        content: 'Remove Selected Supervisor(s) ?',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        type: "POST",
                        data: fdata,
                        url: Base_url + "create_trinity/Removeall_supermapping/" + Encode_id,
                        success: function (response_json) {
                            var response = JSON.parse(response_json);
                            ShowAlret(response.message, response.alert_type);
                            DatatableSupervisorRefresh();
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

function ConfirmUsers() {
    if (NewUsersArrray.length == 0) {
        ShowAlret("Please select Checkbox.", 'error');
        return false;
    }
    $.ajax({
        url: Base_url + "create_trinity/SaveParticipantUsers/" + Encode_id,
        type: 'POST',
        data: {
            NewUsersArrray: NewUsersArrray
        },
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                DatatableUsersRefresh();
                DatatableManagersRefresh(); //REFRESH NEW MANAGERS
                DatatableUserManagersRefresh(); //REFRESH USER-MANAGER MAPPING
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

function ConfirmManagers() {
    if (NewManagersArrray.length == 0) {
        ShowAlret("Please select Checkbox.", 'error');
        return false;
    }
    $.ajax({
        url: Base_url + "create_trinity/SaveParticipantManagers/" + Encode_id,
        type: 'POST',
        data: {
            NewManagersArrray: NewManagersArrray
        },
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                DatatableManagersRefresh();
                $('#LoadModalFilter').data('modal', null);
                NewManagersArrray = [];
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

function ConfirmSupervisors() {
    if (NewSupervisorsArrray.length == 0) {
        ShowAlret("Please select Checkbox.", 'error');
        return false;
    }
    $.ajax({
        url: Base_url + "create_trinity/SaveParticipantSupervisors/" + Encode_id,
        type: 'POST',
        data: {
            NewSupervisorsArrray: NewSupervisorsArrray
        },
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                DatatableSupervisorRefresh();
                $('#LoadModalFilter').data('modal', null);
                NewSupervisorsArrray = [];
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

function SaveMappingUserAssessor() {
    if ($('#user_id').val() == '') {
        ShowAlret("Please select Manager.", 'error');
        return false;
    }
    $.ajax({
        url: Base_url + "create_trinity/SaveMappingUserAssessor/" + Encode_id,
        type: 'POST',
        data: $('#UserManagerForm').serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                DatatableUserManagersRefresh();
                $('#LoadModalFilter').data('modal', null);
                $('#CloseModalBtn').click();
                ShowAlret(Data['Msg'], 'success');
            } else {
                ShowAlret(Data['Msg'], 'error');
            }
            customunBlockUI();
        }
    });
}

function LoadFilterUserData(is_mapped) {
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
        "columnDefs": [{
                'width': '70px',
                'orderable': false,
                'searchable': false,
                'targets': [0]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [1]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [2]
            },
            {
                'width': '70px',
                'orderable': true,
                'searchable': true,
                'targets': [3]
            },
            {
                'width': '80px',
                'orderable': true,
                'searchable': true,
                'targets': [4]
            },
            {
                'width': '80px',
                'orderable': true,
                'searchable': true,
                'targets': [5]
            },
            {
                'width': '80px',
                'orderable': true,
                'searchable': true,
                'targets': [6]
            },
            {
                'width': '80px',
                'orderable': true,
                'searchable': true,
                'targets': [7]
            }
        ],
        "order": [
            [0, "asc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": Base_url + "create_trinity/UsersFilterTable/" + Encode_id + "/" + is_mapped,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({
                name: '__mode',
                value: 'featuredimage.ajaxload'
            });
            aoData.push({
                name: 'region',
                value: $('#region').val()
            });
            aoData.push({
                name: 'wktype',
                value: $('#wktype').val()
            });
            aoData.push({
                name: 'company_id',
                value: $('#company_id').val()
            });
            aoData.push({
                name: 'flt_region_id',
                value: $('#flt_region_id').val()
            });
            aoData.push({
                name: 'flt_department_id',
                value: $('#flt_department_id').val()
            });

            aoData.push({
                name: 'NewUsersArrray',
                value: NewUsersArrray
            });
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        },
        "fnFooterCallback": function (nRow, aData) {}
    });
}

function LoadFilterManagerData() {
    $('#ManagersFilterTable').dataTable({
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
        "columnDefs": [{
                'width': '30px',
                'orderable': false,
                'searchable': false,
                'targets': [0]
            },
            {
                'width': '200px',
                'orderable': true,
                'searchable': true,
                'targets': [1]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [2]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [3]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [4]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [5]
            },
            {
                'width': '70px',
                'orderable': false,
                'searchable': false,
                'targets': [6]
            }
        ],
        "order": [
            [0, "asc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": Base_url + "create_trinity/ManagersFilterTable/" + Encode_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({
                name: '__mode',
                value: 'featuredimage.ajaxload'
            });
            aoData.push({
                name: 'region',
                value: $('#region').val()
            });
            aoData.push({
                name: 'wktype',
                value: $('#wktype').val()
            });
            aoData.push({
                name: 'company_id',
                value: $('#company_id').val()
            });
            aoData.push({
                name: 'flt_tregion_id',
                value: $('#flt_tregion_id').val()
            });

            aoData.push({
                name: 'NewManagersArrray',
                value: NewManagersArrray
            });
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        },
        "fnFooterCallback": function (nRow, aData) {}
    });
}

function LoadFilterSupervisorData() {
    $('#SupervisorFilterTable').dataTable({
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
        "columnDefs": [{
                'width': '30px',
                'orderable': false,
                'searchable': false,
                'targets': [0]
            },
            {
                'width': '200px',
                'orderable': true,
                'searchable': true,
                'targets': [1]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [2]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [3]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [4]
            },
            {
                'width': '100px',
                'orderable': false,
                'searchable': false,
                'targets': [5]
            }
        ],
        "order": [
            [0, "asc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": Base_url + "create_trinity/SupervisorsFilterTable/" + Encode_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({
                name: '__mode',
                value: 'featuredimage.ajaxload'
            });
            aoData.push({
                name: 'region',
                value: $('#region').val()
            });
            aoData.push({
                name: 'wktype',
                value: $('#wktype').val()
            });
            aoData.push({
                name: 'company_id',
                value: $('#company_id').val()
            });
            aoData.push({
                name: 'flt_tregion_id',
                value: $('#flt_tregion_id').val()
            });

            aoData.push({
                name: 'NewSupervisorsArrray',
                value: NewSupervisorsArrray
            });
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        },
        "fnFooterCallback": function (nRow, aData) {}
    });
}

function LoadCandidateData() {
    $('#CandidateFilterTable').dataTable({
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
        "columnDefs": [{
                'width': '30px',
                'orderable': true,
                'searchable': true,
                'targets': [0]
            },
            {
                'width': '200px',
                'orderable': true,
                'searchable': true,
                'targets': [1]
            },
            {
                'width': '100px',
                'orderable': false,
                'searchable': false,
                'targets': [2]
            },
            {
                'width': '100px',
                'orderable': true,
                'searchable': true,
                'targets': [3]
            },
            {
                'width': '100px',
                'orderable': false,
                'searchable': false,
                'targets': [4]
            }
        ],
        "order": [
            [0, "asc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": Base_url + "create_trinity/CandidateDataTable/" + Encode_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({
                name: '__mode',
                value: 'featuredimage.ajaxload'
            });
            aoData.push({
                name: 'company_id',
                value: $('#company_id').val()
            });
            aoData.push({
                name: 'NewSupervisorsArrray',
                value: NewSupervisorsArrray
            });
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        },
        "fnFooterCallback": function (nRow, aData) {}
    });
}

function SelectedUsers(id) {
    if ($('#chk' + id).prop('checked')) {
        NewUsersArrray.push(id);
    } else {
        NewUsersArrray.splice($.inArray(id, NewUsersArrray), 1);
    }
}

function SelectedManagers(id) {

    if ($('#chk_' + id).prop('checked')) {
        NewManagersArrray.push(id);
    } else {
        NewManagersArrray.splice($.inArray(id, NewManagersArrray), 1);
    }

}

function SelectedSupervisors(id) {

    if ($('#ck_' + id).prop('checked')) {
        NewSupervisorsArrray.push(id);
    } else {
        NewSupervisorsArrray.splice($.inArray(id, NewSupervisorsArrray), 1);
    }
}

function selected_questions(id) {
    if ($('#ck_Question_id' + id).prop('checked')) {
        NewQuestionArray.push(id);
    } else {
        NewQuestionArray.splice($.inArray(id, NewUsersArrray), 1);
    }
}

// function selected_sub_parameters(id, txn_id) {
//     if ($('#chksp_id' + id).prop('checked')) {
//         var push_value = new Array();
//         push_value['txn_id'] = txn_id;
//         push_value['id'] = id;
//         TempSubParameterArray.push(push_value);
//     } else {
//         TempSubParameterArray = TempSubParameterArray.filter(function (obj) {
//             if ((obj.txn_id == txn_id) && (obj.id == id)) {
//                 return false;
//             } else {
//                 return true;
//             }
//         });
//         // TempSubParameterArray.splice($.inArray(id, TempSubParameterArray), 1);
//     }
// }

// function pm_delete() {
//     var txn_id = $('#txn_id').val();
//     var parameter_id = $('#dp_parameter_id').val();
//     if (txn_id == "") {
//         ShowAlret("Delete failed, Txn Id is missing.", 'error');
//         return false;
//     }
//     if (parameter_id == "") {
//         ShowAlret("Delete failed, Please select parameter name", 'error');
//         return false;
//     }
//     if (txn_id != '' && parameter_id != '') {
//         TempSubParameterArray = TempSubParameterArray.filter(function (obj) {
//             if ((parseInt(obj.txn_id) == parseInt(txn_id)) && (parseInt(obj.parameter_id) == parseInt(parameter_id))) {
//                 return false;
//             } else {
//                 return true;
//             }
//         });
//         div_html = printonscreen_keyword_sentence(txn_id, parameter_id);

//         var div_element = "#paramsub" + txn_id;
//         $(div_element).empty();
//         $(div_element).html('');
//         $(div_element).html(div_html);
//         $("#dp_parameter_id").val("").trigger('change');
//         $("#parameter_label_id").val("").trigger('change');
//         getUnique_paramters();
//         ShowAlret("Parameter and sub parameters deleted successfully...", 'error');
//     }
// }

// function pm_submit(flag, edit_id = '') {
//     var txn_id = $('#txn_id').val();
//     var parameter_id = $('#dp_parameter_id').val();
//     var parameter_label_id = $('#parameter_label_id').val();
//     var parameter_weight = $('#parameter_weight').val();

//     if (parseFloat(parameter_id) == 2 || parseFloat(parameter_id) == 6) {
//         chk_count = 0;
//         $("input[name='chksp_list[]']").each(function (index) {
//             var temp_id = $(this).val();
//             if ($('#chksp_id' + temp_id).prop('checked')) {
//                 chk_count++;
//             }
//         });
//         if (chk_count > 1) {
//             ShowAlret("You can choose only one sub-parameter", 'error');
//             return false;
//         }
//     }

//     if (txn_id == "") {
//         ShowAlret("Txn Id missing", 'error');
//         return false;
//     }
//     if (parameter_id == "") {
//         ShowAlret("Please select parameter name", 'error');
//         return false;
//     }
//     if (parameter_label_id == "") {
//         ShowAlret("Please select parameter label name", 'error');
//         return false;
//     }
//     if (parameter_weight != "") {
//         var isValid = isValidWeight(parameter_weight);
//         if (!isValid) {
//             ShowAlret("Please add valid parameter weight", 'error');
//             return false;
//         }
//     }
//     var is_subparam_ticked = false;
//     $("input[name='chksp_list[]']").each(function (index) {
//         var temp_id = $(this).val();
//         if ($('#chksp_id' + temp_id).prop('checked')) {
//             is_subparam_ticked = true;
//         }
//     });
//     if (is_subparam_ticked == false) {
//         ShowAlret("Please choose atleast one sub parameter.", 'error');
//         return false;
//     }
//     if (txn_id != '' && parameter_id != '') {
//         is_valid = 0;
//         $("input[name='chksp_list[]']").each(function (index) {
//             var temp_id = $(this).val();
//             if ($('#chksp_id' + temp_id).prop('checked')) {
//                 TempSubParameterArray = TempSubParameterArray.filter(function (obj) {
//                     if ((parseInt(obj.txn_id) == parseInt(txn_id)) && (parseInt(obj.parameter_id) == parseInt(parameter_id)) && (parseInt(obj.subparameter_id) == parseInt(temp_id))) {
//                         return false;
//                     } else {
//                         return true;
//                     }
//                 });

//                 var push_value = {};
//                 var parameter_data = $('#dp_parameter_id').select2('data');
//                 var parameter_name = parameter_data[0].text;

//                 var parameter_label_data = $('#parameter_label_id').select2('data');
//                 var parameter_label_name = parameter_label_data[0].text;
//                 var subparameter_name = $('#lblsp_id' + temp_id).html();
//                 var type_id = "";
//                 var type_name = "";
//                 var sentence_keyword = "";

//                 if ($('#type_id' + temp_id).css('display') == 'none' || $('#type_id' + temp_id).css("visibility") == "hidden") {} else {
//                     type_id = $('#type_id' + temp_id).val();
//                     type_name = $('#type_id' + temp_id + ' option:selected').text();
//                     sentence_keyword = $('#sentkey' + temp_id).val();
//                     if (type_id !== "" && sentence_keyword === "") {
//                         ShowAlret("Please add Sentence/Keyword for " + parameter_name, 'error'); //mandatory textarea value in case of selected sentence/keyword 
//                         is_valid = 1;
//                         return false;
//                     }
//                     sentence_keyword = sentence_keyword.replace(/\n/g, " ");
//                 }
//                 push_value['txn_id'] = txn_id;
//                 push_value['parameter_id'] = parameter_id;
//                 push_value['parameter_name'] = parameter_name;
//                 push_value['parameter_label_id'] = parameter_label_id;
//                 push_value['parameter_label_name'] = parameter_label_name;
//                 push_value['subparameter_id'] = temp_id;
//                 push_value['subparameter_name'] = subparameter_name;
//                 push_value['type_id'] = type_id;
//                 push_value['type_name'] = type_name;
//                 push_value['sentence_keyword'] = sentence_keyword;
//                 push_value['parameter_weight'] = parameter_weight;
//                 TempSubParameterArray.push(push_value);
//             } else {
//                 TempSubParameterArray = TempSubParameterArray.filter(function (obj) {
//                     if ((parseInt(obj.txn_id) == parseInt(txn_id)) && (parseInt(obj.parameter_id) == parseInt(parameter_id)) && (parseInt(obj.subparameter_id) == parseInt(temp_id))) {
//                         return false;
//                     } else {
//                         return true;
//                     }
//                 });
//             }
//         });
//         if (is_valid == 1) {
//             return false; //Abort from here in case of invalid sentence_keyword
//         }
//         // console.log(TempSubParameterArray);
//         var TempPSMArray = TempSubParameterArray;
//         var TempPSMCount = TempPSMArray.filter(function (objs) {
//             if ((parseInt(objs.txn_id) == parseInt(txn_id)) && (parseInt(objs.parameter_id) == parseInt(parameter_id))) {
//                 return true;
//             } else {
//                 return false;
//             }
//         });
//         if (Object.keys(TempPSMCount).length <= 0) {
//             var parameter_data = $('#dp_parameter_id').select2('data');
//             var parameter_name = parameter_data[0].text;
//             var push_value_ii = {};
//             push_value_ii['txn_id'] = txn_id;
//             push_value_ii['parameter_id'] = parameter_id;
//             push_value_ii['parameter_name'] = parameter_name;
//             push_value_ii['parameter_label_id'] = parameter_label_id;
//             push_value_ii['parameter_label_name'] = parameter_label_name;
//             push_value_ii['subparameter_id'] = "999999999999";
//             push_value_ii['subparameter_name'] = "";
//             push_value_ii['type_id'] = "";
//             push_value_ii['type_name'] = "";
//             push_value_ii['sentence_keyword'] = "";
//             push_value_ii['parameter_weight'] = parameter_weight;
//             TempSubParameterArray.push(push_value_ii);
//         }
//     }

//     div_html = printonscreen_keyword_sentence(txn_id, parameter_id);


//     if (flag == 0) {
//         var div_element = "#paramsub" + txn_id;
//         $(div_element).empty();
//         $(div_element).html('');
//         $(div_element).html(div_html);
//         $("#dp_parameter_id").val("").trigger('change');
//         $("#parameter_label_id").val("").trigger('change');
//         $("#parameter_weight").val("");
//         getUnique_paramters();
//     }
//     if (flag == 1) {
//         var div_element = "#paramsub" + txn_id;
//         $(div_element).empty();
//         $(div_element).html('');
//         $(div_element).html(div_html);
//         var total_weight = check_parameter_weights(TempSubParameterArray, txn_id);
//         if (total_weight > 0 && (total_weight < 100 || total_weight > 100)) {
//             ShowAlret("Total weight of selected parameters must be 100%", "error");
//             return false;
//         }
//         getUnique_paramters();
//         $('#CloseModalBtn').click();
//     }
// }

function isValidWeight(str) {
    var n = Math.floor(Number(str));
    return n !== Infinity && String(n) === str && n >= 0 && n <= 100;
}

function check_parameter_weights(TempSubParameterArray, txn_id = '') {
    var total_weight = 0;
    if (TempSubParameterArray.length > 0) {
        if (txn_id !== '') {
            $.each(TempSubParameterArray, function (key, value) {
                if (txn_id == value.txn_id) {
                    weight = (isNaN(value.parameter_weight)) ? 0 : parseInt(value.parameter_weight);
                    total_weight += weight;
                }
            });
        }
    }
    // console.log('Total weight: '+total_weight);
    return total_weight;
}

function check_overall_parameter_weights(TempSubParameterArray, TotalQue) {
    var total_weight = 0;
    if (TempSubParameterArray.length > 0) {
        var weight = [];
        var sum_weight = 0;
        $.each(TempSubParameterArray, function (key, value) {
            param_weight = (isNaN(value.parameter_weight)) ? 0 : parseInt(value.parameter_weight);
            sum_weight += param_weight;
            if (value.txn_id in weight) {
                var temp = weight[value.txn_id] + param_weight;
                weight[value.txn_id] = temp;
            } else {
                weight[value.txn_id] = param_weight;
            }
        });
        weight = weight.filter(function (v) {
            return v !== ''
        });
        total_weight = sum_weight / (TotalQue - 1);
    }
    return total_weight;
}

function printonscreen_keyword_sentence(txn_id, parameter_id) {
    var dcp_groupby = (keys) => (array) =>
        array.reduce((objectsByKeyValue, obj) => {
            var value = keys.map((key) => obj[key]).join("-");
            objectsByKeyValue[value] = (objectsByKeyValue[value] || []).concat(obj);
            return objectsByKeyValue;
        }, {});

    var parameter_counter = [];
    var temp_json = TempSubParameterArray.filter(function (obj) {
        if ((parseInt(obj.txn_id) == parseInt(txn_id))) {
            return true;
        } else {
            return false;
        }
    });;
    var groupby_parameter = dcp_groupby(["parameter_id"]);
    for (let [groupName, values] of Object.entries(groupby_parameter(temp_json))) {
        var parameter_pushval = {};
        parameter_pushval['parameter_id'] = `${groupName}`;
        parameter_pushval['length'] = `${values.length}`;
        parameter_counter.push(parameter_pushval);
    }

    var div_html = "";
    var temp_parameter_id = 0;
    var x = 0;
    var y = 0;
    var temp_weight = 0;
    var total_subparameter = 0;
    var subparameters_counter;
    var html_sentence_keyword = "";
    if (Object.keys(TempSubParameterArray).length > 0) {
        $("#parameter_id" + txn_id + " > option").attr("selected", false);
        TempSubParameterArray.forEach(function (item, index) {
            if (parseInt(txn_id) == parseInt(item.txn_id)) {
                $('#parameter_id' + item.txn_id + ' option[value="' + item.parameter_id + '"]').prop("selected", true);
                // div_html = div_html + '<div class="ps-container"><div class="parameter-badge">'+ item.parameter_name +'</div><div class="ps-seperator">&nbsp;</div><div class="sub-parameter-badge">'+ item.subparameter_name +'</div></div>';	
                if ((x > 0) && (parseInt(temp_parameter_id) != parseInt(item.parameter_id))) {
                    div_html = div_html + '</div>';
                }
                if (parseInt(temp_parameter_id) != parseInt(item.parameter_id)) {
                    subparameters_counter = parameter_counter.filter(function (obj) {
                        if ((parseInt(obj.parameter_id) == parseInt(item.parameter_id))) {
                            return true;
                        } else {
                            return false;
                        }
                    });
                    if (Object.keys(subparameters_counter).length >= 0 && typeof (subparameters_counter[0].length) != 'undefined') {
                        total_subparameter = subparameters_counter[0].length;
                    }
                    y = 0;
                    temp_parameter_id = item.parameter_id;
                    div_html = div_html + '<div class="ps-container"><div class="parameter-badge">' + item.parameter_label_name + '</div>';
                }
                if (y == 0) {
                    div_html = div_html + '<div class="ps-seperator">&nbsp;</div><div class="sub-parameter-badge">' + item.subparameter_name;
                    temp_weight = item.parameter_weight ? item.parameter_weight : '<span style="font-weight:bold">NA</span>';
                } else {
                    div_html = div_html + ',' + item.subparameter_name;
                    temp_weight += item.parameter_weight ? ',' + item.parameter_weight : '&nbsp;<span style="font-weight:bold">NA</span>';
                }
                if (item.type_name != "") {
                    html_sentence_keyword = html_sentence_keyword + '<span style="font-weight:bold;color:red;">' + item.type_name + ':</span><p style="font-weight:normal">' + item.sentence_keyword + '</p>';
                } else {
                    html_sentence_keyword = html_sentence_keyword + '<span style="font-weight:bold">NA</span>&nbsp;';
                }
                if ((parseInt(y) == parseInt(total_subparameter - 1))) {
                    div_html = div_html + '</div><div class="ps-seperator">&nbsp;</div><div class="parameter-weight-badge">' + temp_weight;
                    div_html = div_html + '</div><div class="ps-seperator">&nbsp;</div><div class="keysent-badge">' + html_sentence_keyword + '</div>';
                    html_sentence_keyword = "";
                }
                if ((parseInt(temp_parameter_id) !== parseInt(item.parameter_id)) || (Object.keys(temp_json).length == x)) {
                    div_html = div_html + '</div>';
                }

                x++;
                y++;
            }
        });
    }
    var html_selected_parameters = "";
    var temp_spjson = TempSubParameterArray.filter(function (obj) {
        if (parseInt(obj.txn_id) == parseInt($("#txn_id").val())) {
            return true;
        } else {
            return false;
        }
    });;
    temp_spjson.forEach(function (item, index) {
        var n = html_selected_parameters.search(item.parameter_name);
        if (n < 0) {
            html_selected_parameters = html_selected_parameters + '<span class="label label-primary">' + item.parameter_name + '</span>&nbsp;&nbsp;';
        }
    });
    $("#selected_parameters").html(html_selected_parameters);
    return div_html;
}

// function Confirm_questions(edit_id) {
//     var assessment_type = $('#assessment_type').val();
//     var start_date = $('#start_date').val();
//     if (assessment_type == "") {
//         ShowAlret("Please select Assessment Type!", 'error');
//         return false;
//     }
//     if (edit_id != '') {
//         if (!$("input:radio[name='rd_question_id']").is(":checked")) {
//             ShowAlret("Please select Question.", 'error');
//             return false;
//         } else {
//             var new_question_id = $("input[name='rd_question_id']:checked").val();
//         }
//         var turl = Base_url + "create_trinity/get_question_title/";
//         var tdata = {
//             question_id: new_question_id
//         };
//     } else {
//         if (NewQuestionArray.length == 0) {
//             ShowAlret("Please select Question.", 'error');
//             return false;
//         }
//         turl = Base_url + "create_trinity/append_questions/" + Totalqstn;
//         tdata = {
//             NewQuestionArray: NewQuestionArray,
//             Encode_id: Encode_id,
//             assessment_type: assessment_type,
//             start_date: start_date
//         };
//     }
//     $.ajax({
//         url: turl,
//         type: 'POST',
//         data: tdata,
//         beforeSend: function () {
//             customBlockUI();
//         },
//         success: function (Odata) {
//             var Data = $.parseJSON(Odata);
//             if (Data['lchtml'] != '') {
//                 if (edit_id != "") {
//                     var rm_id = $('#question_id' + edit_id).val();
//                     Selected_QuestionArray.splice($.inArray(rm_id, Selected_QuestionArray), 1);
//                     $('#question_id' + edit_id).val(new_question_id);
//                     $('#question_text_' + edit_id).text(Data['lchtml']);
//                     $(".select2").select2();
//                     Selected_QuestionArray.push(new_question_id);
//                 } else {
//                     if (Data['Msg'] == '') {
//                         if (Totalqstn == 1) {
//                             $("#Row-0").remove();
//                         }
//                         $('#VQADatatable').append(Data['lchtml']);
//                         $(".select2").select2();
//                         $('#LoadModalFilter').data('modal', null);
//                         Selected_QuestionArray = Selected_QuestionArray.concat(NewQuestionArray);
//                         NewQuestionArray = [];
//                         Totalqstn = Data['tr_no'];
//                         $('.language_id').on('select2:select', function (e) {
//                             var data = e.params.data;
//                             $(".txt_trno").each(function () {
//                                 let temp_language_id = "#language_id" + $(this).val();
//                                 $(temp_language_id).val(data.id).trigger('change');
//                             });
//                         });
//                     } else {
//                         ShowAlret(Data['Msg'], 'error');
//                         NewQuestionArray = [];
//                     }
//                 }

//                 $('#CloseModalBtn').click();
//             }
//             customunBlockUI();
//         }
//     });
// }

// function Load_questions_table(AddEdit) {
//     $('#QuestionTable').dataTable({
//         destroy: true,
//         "language": {
//             "aria": {
//                 "sortAscending": ": activate to sort column ascending",
//                 "sortDescending": ": activate to sort column descending"
//             },
//             "emptyTable": "No data available in table",
//             "info": "Showing _START_ to _END_ of _TOTAL_ records",
//             "infoEmpty": "No records found",
//             "infoFiltered": "(filtered1 from _MAX_ total records)",
//             "lengthMenu": "Show _MENU_",
//             "search": "Search:",
//             "zeroRecords": "No matching records found",
//             "paginate": {
//                 "previous": "Prev",
//                 "next": "Next",
//                 "last": "Last",
//                 "first": "First"
//             }
//         },
//         "lengthMenu": [
//             [5, 10, 15, 20, -1],
//             [5, 10, 15, 20, "All"]
//         ],
//         "pageLength": 10,
//         "pagingType": "bootstrap_full_number",
//         "columnDefs": [{
//                 'width': '30px',
//                 'orderable': true,
//                 'searchable': true,
//                 'targets': [0]
//             },
//             {
//                 'width': '200px',
//                 'orderable': true,
//                 'searchable': true,
//                 'targets': [1]
//             },
//             {
//                 'width': '100px',
//                 'orderable': true,
//                 'searchable': true,
//                 'targets': [2]
//             },
//             {
//                 'width': '80px',
//                 'orderable': true,
//                 'searchable': true,
//                 'targets': [3]
//             },
//             {
//                 'width': '80px',
//                 'orderable': true,
//                 'searchable': true,
//                 'targets': [4]
//             },
//             {
//                 'width': '40px',
//                 'orderable': false,
//                 'searchable': false,
//                 'targets': [5]
//             }
//         ],
//         "order": [
//             [0, "desc"]
//         ],
//         "processing": true,
//         "serverSide": true,
//         "sAjaxSource": Base_url + "create_trinity/load_question_table/" + Encode_id,
//         "fnServerData": function (sSource, aoData, fnCallback) {
//             aoData.push({
//                 name: 'company_id',
//                 value: $('#company_id').val()
//             });
//             aoData.push({
//                 name: 'assessment_type',
//                 value: $('#assessment_type').val()
//             });
//             aoData.push({
//                 name: 'question_type',
//                 value: $('#question_type').val()
//             });
//             aoData.push({
//                 name: 'Selected_QuestionArray',
//                 value: Selected_QuestionArray
//             });
//             aoData.push({
//                 name: 'AddEdit',
//                 value: AddEdit
//             });
//             $.getJSON(sSource, aoData, function (json) {
//                 fnCallback(json);
//             });
//         },
//         "fnRowCallback": function (nRow, aData, iDisplayIndex) {
//             return nRow;
//         },
//         "fnFooterCallback": function (nRow, aData) {}
//     });
// }

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
        url: Base_url + 'create_trinity/UploadTraineeXls/' + Encode_id,
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
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            ShowAlret("Status: " + textStatus + " ,Contact Atomapp for technical support!");
        }
    });
}

function UploadXlsUser_Manager() {
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
        url: Base_url + 'create_trinity/confirm_upload_user_manager/' + Encode_id,
        data: form_data,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success'] == '1') {
                ShowAlret(Data['Msg'], 'success');
                $('#LoadModalFilter').data('modal', null);
                $('#CloseModalBtn').click();
                DatatableUserManagersRefresh();
                DatatableUsersRefresh();
                DatatableManagersRefresh();
            } else {
                if (Data['success'] == 2) {
                    DatatableUserManagersRefresh();
                    DatatableUsersRefresh();
                    DatatableManagersRefresh();
                }
                $('#modalerrordiv').show();
                $('#modalerrorlog').html(Data['Msg']);
            }
            customunBlockUI();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            ShowAlret("Status: " + textStatus + " ,Contact Atomapp for technical support!");
        }
    });
}

function UploadXlsManager() {
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
        url: Base_url + 'create_trinity/UploadManagerXls/' + Encode_id,
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
                DatatableManagersRefresh();
            } else {
                $('#modalerrordiv').show();
                $('#modalerrorlog').html(Data['Msg']);
            }
            customunBlockUI();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            ShowAlret("Status: " + textStatus + " ,Contact Atomapp for technical support!");
        }
    });
}

function UploadXlsSupervisor() {
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
        url: Base_url + 'create_trinity/UploadSupervisorXls/' + Encode_id,
        data: form_data,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                DatatableSupervisorRefresh()
                $('#LoadModalFilter').data('modal', null);
                $('#CloseModalBtn').click();
                DatatableUsersRefresh();

            } else {
                $('#modalerrordiv').show();
                $('#modalerrorlog').html(Data['Msg']);
            }
            customunBlockUI();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            ShowAlret("Status: " + textStatus + " ,Contact Atomapp for technical support!");
        }
    });
}

function ConfirmSave(AddEdit) {
    if (!$('#AssessmentForm').valid()) {
        return false;
    }
    //KRISHNA -- Spotlight changes start
    var assessment_type = $('#assessment_type').val();
    // if (assessment_type == "2") {
    //     if ($(".is_default").is(":checked") == false) {
    //         ShowAlret("Please select default question.", 'error');
    //         return false;
    //     }
    //     var question_limit = $("#question_limit").val();
    //     var selected_question = Selected_QuestionArray.length;
    //     total_question = selected_question;
    //     if (AddEdit == 'C') {
    //         total_question = Totalqstn - 1;
    //     }
    //     if (question_limit != "") {
    //         if (total_question <= question_limit || total_question == 0) {
    //             ShowAlret("Please select question more than question limit.", 'error');
    //             return false;
    //         }
    //     }
    // }
    //KRISHNA -- Spotlight changes end
    $.confirm({
        title: 'Confirm Assessment!',
        content: '',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    SaveAssessment(AddEdit);
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}

function SaveAssessment(mode) {
    form_error.hide();
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
    if (!$('#AssessmentForm').valid()) {
        return false;
    }
    // if (Totalqstn == 1) {
    //     ShowAlret("Please Add Question & Parameter..", "error");
    //     return false;
    // }
    // if ($('#isweights').prop("checked") == true) {
    //     var total_percent = 0;
    //     $('.percent_cnt').each(function () {
    //         total_percent += parseFloat($(this).val()) || 0; // Or this.innerHTML, this.innerText
    //     });
    //     if (total_percent != '100' || total_percent != '100.00') {
    //         ShowAlret("Total weight of selected parameters must be 100%", "error");
    //         return false;
    //     }
    // }
    // if (TempSubParameterArray.length > 0) {
    //     var total_weight = check_overall_parameter_weights(TempSubParameterArray, Totalqstn);
    //     if (total_weight > 0 && total_weight != 100) {
    //         ShowAlret("Total weight of selected parameters for each question must be 100%", "error");
    //         return false;
    //     }
    // } else {
    //     ShowAlret("Please Add Parameter to the selected questions.", "error");
    //     return false;
    // }
    if (mode == 'C') {
        var url = Base_url + "create_trinity/submit/" + Encode_id;
    } else {
        url = Base_url + "create_trinity/submit";
    }


    // var form_data  = {};
    // var dummy_data = $('#AssessmentForm').serializeArray();
    // console.log(dummy_data);
    // // var other_data = {};
    // // $.each(dummy_data, function (key, input) {
    // // 	other_data[input.name] = input.value;
    // // });
    // form_data['other_data'] = JSON.stringify(dummy_data);
    // form_data['sub_parameter'] = JSON.stringify(TempSubParameterArray);
    // console.log(form_data);

    var form_data = {};
    var other_data = $('#AssessmentForm').serializeArray();
    // $.each(other_data, function (key, input) {
    // 	form_data[input.name] = input.value;
    // });
    // form_data['sub_parameter'] = JSON.stringify(TempSubParameterArray);
    // var form_data = {};
    // var other_data = $('#AssessmentForm').serializeArray();
    $.each(other_data, function (key, input) {
        if (form_data.hasOwnProperty(input.name)) {
            form_data[input.name] = form_data[input.name] + "," + input.value;
        } else {
            form_data[input.name] = input.value;
        }
    });
    //KRISHNA -- Spotlight changes start
    // if ($("#assessment_type").prop("disabled") == true) {
    form_data['assessment_type'] = '3';
    form_data['assessor_date'] = $("#assessor_date").val();
    // }
    //KRISHNA -- Spotlight changes end
    var x = 0;
    // var sub_parameter = [];
    // $.each(TempSubParameterArray, function (key, input) {
    //     sub_parameter[x] = input;
    //     x++;
    // });
    // form_data['sub_parameter'] = sub_parameter;
    // $('.language_id').each(function () {
    //     var id = this.id;
    //     var val = $("option:selected", this).val();
    //     form_data[id] = val;
    // });
    $.ajax({
        cache: false,
        async: true,
        type: "POST",
        url: url,
        dataType: "json",
        data: form_data,
        // data: $('#AssessmentForm').serialize() + "&sub_parameter=" + JSON.stringify(TempSubParameterArray),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            // var Data = $.parseJSON(Odata);
            var Data = Odata;
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                NewManagersArrray = [];
                NewSupervisorsArrray = [];
                if (AddEdit == 'A' || AddEdit == 'C') {
                    setTimeout(function () { // wait for 5 secs(2)
                        window.location.href = Base_url + 'create_trinity/edit/' + Data['id'] + "/1.2";
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
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
        }
    });
}

function getquestion_type() {
    if ($('#question_type').val() == 0) {
        $('#label_dyamic').text('Question');
    } else {
        $('#label_dyamic').text('Situation');
    }
    AssessmentChange();
}

function notification_send(action) {
    if (action == 2) {
        var totalSelected = getCheckCount();
        var ReqData = $('#ParticipantForm').serialize();
    } else {
        var totalSelected = getCheckCnt();
        var ReqData = $('#MappingForm').serialize();
    }
    if (totalSelected == 0) {
        ShowAlret("Please select record from the list.", 'error');
        return false;
    }
    $.confirm({
        title: 'Confirm!',
        content: 'are you sure you want to send Notification.?',
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        type: "POST",
                        data: ReqData,
                        url: Base_url + "create_trinity/send_notification/" + action + '/' + Encode_id,
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
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}

function LoadDeleteDialog_ass_user(Id) {
    $.confirm({
        title: 'Confirm!',
        content: " Are you sure you want to delete Assessment user ? ",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        type: "POST",
                        url: Base_url + "create_trinity/remove_assessmentuser/" + Id,
                        success: function (response_json) {
                            var response = JSON.parse(response_json);
                            ShowAlret(response.message, response.alert_type);
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

function add_mappassessor() {
    var user_id = $('#user_id').val();
    if (user_id == "") {
        ShowAlret("Please select Manager", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            user_id: user_id,
            assessment_id: Encode_id,
            user_row: user_row
        },
        //async: false,
        url: Base_url + "create_trinity/add_mappassessor",
        success: function (response_json) {
            var data = jQuery.parseJSON(response_json);
            if (data['Success']) {
                $('#UserManagersFilterTable tbody').append(data['html']);
                user_row++;
                $(".select2").select2();
            } else {
                ShowAlret(data['Msg'], 'error');
            }
        }
    });
}
/* ---   --- */
function remove_userrow(remove_id) {
    $.confirm({
        title: 'Confirm!',
        content: " are you sure you want to remove ? ",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $('#usr_' + remove_id).remove();
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}

function getCheckUserCt() {
    var x = 0;
    for (var i = 0; i < UserMappingForm.elements.length; i++) {
        if (UserMappingForm.elements[i].checked == true) {
            x++;
        }
    }
    return x;
}

function RemoveUserMappingPopup() {
    if (getCheckUserCt() == 0) {
        ShowAlret("Please select record from the list......", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: $('#UserMappingForm').serialize(),
        url: Base_url + "create_trinity/RemoveUserMappingPopup/" + Encode_id,
        success: function (response_json) {
            $('#modal-body1').html(response_json);
            $('#LoadModalFilter1').modal();
        }
    });
}

function RemoveMappingUserManager() {
    $.ajax({
        type: "POST",
        data: $('#UserManagerConfirmForm').serialize(),
        url: Base_url + "create_trinity/RemoveUserMappingManager/" + Encode_id,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                DatatableUserManagersRefresh();
                $('#CloseModalBtn').click();
            } else {
                ShowAlret(Data['Msg'], 'error');
            }
            customunBlockUI();
        }
    });
}

// function getUnique_paramters() {
//     // var form_data = new FormData();    
//     // form_data.append('hint_image', file_data);
//     // var other_data = $('#AssessmentForm').serializeArray();
//     // $.each(other_data,function(key,input){
//     //     form_data.append(input.name,input.value);
//     // });

//     $.ajax({
//         url: Base_url + "create_trinity/add_parameter_weights",
//         type: 'POST',
//         data: $('#AssessmentForm').serialize(),
//         beforeSend: function () {
//             customBlockUI();
//         },
//         success: function (Odata) {
//             var Data = $.parseJSON(Odata);
//             if (Data != '') {
//                 $('#weights_table tbody').html(Data['html']);
//             }
//             get_weight();
//             customunBlockUI();
//         }
//     });
// }

function get_weight() {
    var input_weight = 0;
    $('.percent_cnt').each(function () {
        input_weight += parseFloat($(this).val()) || 0;
    });
    $('#total_weight').val(input_weight);
}

function hide_unhide_keysent(id, has_sentences_keyword) {
    var ele_type_id = $('#type_id' + id);
    var ele_sentkey = $('#sentkey' + id);
    var ele_MybtnModal = $('#MybtnModal' + id);

    if (has_sentences_keyword == 1) {
        if ($('#chksp_id' + id).is(':checked')) {
            //alert("here there");
            ele_type_id.removeClass('hide');
            ele_type_id.addClass('show');
            ele_sentkey.removeClass('hide');
            ele_sentkey.addClass('show');
            ele_MybtnModal.removeClass('hide');
            ele_MybtnModal.addClass('show');
        } else {
            //alert("main else park");
            ele_type_id.removeClass('show');
            ele_type_id.addClass('hide');
            ele_sentkey.removeClass('show');
            ele_sentkey.addClass('hide');
            ele_MybtnModal.removeClass('show');
            ele_MybtnModal.addClass('hide');
        }
    } else {
        //alert("else part");
        ele_type_id.removeClass('show');
        ele_type_id.addClass('hide');
        ele_sentkey.removeClass('show');
        ele_sentkey.addClass('hide');
        ele_MybtnModal.removeClass('show');
        ele_MybtnModal.addClass('hide');
    }
}

function play_video(url) {
    //alert(url);
    //var url="https://www.youtube.com/embed/A6XUVjK9W4o";        
    //var url = "https://www.youtube.com/watch?v=8vL_2rF8JHU";
    //alert(vimeo_url);
    //alert(url);
    document.getElementById('dp-video').src = url;
    $('#responsive-video-modal').modal('show');
}

function stop_video(tab) {
    document.getElementById('dp-video').src = "";
}

// Changes By Bk -Trinity UI/UX

function script_based_situation() {
    var script_id = $('#script').val();
    $.ajax({
        type: "POST",
        data: {
            script_id: script_id
        },
        //async: false,
        url: Base_url + "Create_trinity/script_based_situation",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Oresult = $.parseJSON(Odata);
            if (Oresult) {
                $('#situation').empty();
                $('#situation').html(Oresult['situation']);
            }
            customunBlockUI();
        }
    });
}


function save_script() {
    var script_id = $('#script').val();
    var situation = $('#situation').val();
    var script_question = $('#script_question').val();
    var c_question_limit = $('#c_question_limit').val();
    if (script_id == '') {
        ShowAlret("Please Select Script..!!", "error");
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            script_id: script_id,
            situation: situation,
            script_question: script_question,
            c_question_limit: c_question_limit
        },
        //async: false,
        url: Base_url + "Create_trinity/save_script/" + Encode_id,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                location.reload(true);
            } else {
                ShowAlret(Data['Msg'], 'error');
            }
            customunBlockUI();
        }
    });
}

if ($('#persona_option').val() != '') {
    fetch_persona($('#persona_option').val());
}
// Map Persona code 
$('#persona_option').change(function () {
    // Fetch Image Code
    var queFormat = $('#persona_option').val();
    if (queFormat == '0') {
        showEmptyImg();
    } else {
        fetch_persona(queFormat);
    }
});

// Empty Image Show
function showEmptyImg() {
    $('#EmptyImage').show();
    $('#Emptytext').show();
    $('#question_preview').hide();
    $('#queImage').hide();
}
// Empty Image Show
// Persona Image and Name Show
function fetch_persona(queFormat) {
    $.ajax({
        type: "POST",
        data: {
            persona_id: queFormat
        },
        //async: false,
        url: Base_url + "Create_trinity/fetch_persona",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Oresult = $.parseJSON(Odata);
            if (Oresult) {
                $('#question_preview').empty();
                $('#question_preview').show();
                $('#question_preview').attr('src', Oresult['persona_image']);
                $('#queImage').show();
                $('#Emptytext').hide();
                $('#EmptyImage').hide();

            }
            customunBlockUI();
        }
    });
    // Fetch Image Code
}
// Persona Image and Name Show

$("#myFileInput").change(function () {
    var formData = new FormData();
    var file_data = $('#myFileInput').prop('files')[0];
    if (persona_name == '' || file_data == '') {
        ShowAlret("Please Select Persona Name & Persona image..!!", "error");
        return false;
    }
    formData.append('file', file_data);
    formData.append('isUpdate', 0);
    $.ajax({
        url: Base_url + "Create_trinity/selectImage",
        beforeSend: function () {
            customBlockUI();
        },
        type: 'POST',
        data: formData,
        processData: false, // tell jQuery not to process the data
        contentType: false, // tell jQuery not to set contentType
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['Success'] && Data['Message'] != '') {
                ShowAlret(Data['Message'], 'success');
                $('#EmptyImg').prop('src', Base_url + 'assets/uploads/trinity/' + Data['que_image']);
            } else {
                ShowAlret(Data['Message'], 'error');
                $('#errordiv').show();
                $('#errorlog').html(Data['Message']);
            }
            customunBlockUI();
        },
        complete: function () {}
    });
});

function map_persona() {
    var formData = new FormData();
    var persona_id = $('#persona_option').val();
    formData.append('is_create', 1);

    if (persona_id == 0 && persona_id != '') {
        persona_id = $('#persona_name').val();
        persona_image = $('#EmptyImg').attr('src');
        formData.append('is_create', 0);
    } else {
        var persona_image = $('#question_preview').attr('src');
    }
    if (persona_id == '' || persona_image == '') {
        ShowAlret("Please Select Persona Name & Persona image..!!", "error");
        return false;
    }

    formData.append('persona_id', persona_id);
    formData.append('file', persona_image);
    $.ajax({
        url: Base_url + "create_trinity/map_persona/" + Encode_id,
        beforeSend: function () {
            customBlockUI();
        },
        type: 'POST',
        data: formData,
        processData: false, // tell jQuery not to process the data
        contentType: false, // tell jQuery not to set contentType
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['Success'] && Data['Message'] != '') {
                ShowAlret(Data['Message'], 'success');
                setTimeout(function () { // wait for 5 secs(2)
                    window.location.href = Base_url + 'create_trinity/edit/' + Encode_id + "/1.3";
                }, 500);
            } else {
                ShowAlret(Data['Message'], 'error');
                $('#errordiv').show();
                $('#errorlog').html(Data['Message']);
            }
            customunBlockUI();
        },
        complete: function () {}
    });
}
// Map Persona code 


// Mapping script code
if ($('#script').val() != '') {
    $('#sit-limit').show();
    $('#CQid').show();
    $('#QTable').show();
    $('#add_weight').show();
    DatatableRefresh($('#script').val());
    regenerate_weight_table($('#edit_id').val());
}

$('#script').change(function () {
    $('#sit-limit').show();
    $('#CQid').show();
    $('#QTable').show();
    $('#add_weight').show();
    $("#parameter_id").select2();
    DatatableRefresh($('#script').val());
    regenerate_weight_table($('#edit_id').val());
});

function DatatableRefresh(script_id) {
    var Totalqstn = 1;
    if (script_id == "" || company_id == "") {
        ShowAlret("Please add Script", 'error');
    } else {
        var form_data = new FormData();
        form_data.append('company_id', company_id);
        form_data.append('script_id', script_id);
        form_data.append('edit_id', $('#edit_id').val());
        // form_data.append('c_question', $('#c_question').val());
        $.ajax({
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            url: Base_url + "/create_trinity/fetch_question_answer/" + Totalqstn,
            data: form_data,
            beforeSend: function () {
                customBlockUI();
            },
            success: function (Odata) {
                var Data = $.parseJSON(Odata);
                if (Data['lchtml'] != '') {
                    $('#QTable-body').empty();
                    $('#VQADatatable').append(Data['lchtml']);
                    $(".verbleparameter").select2();
                    $(".language_id").select2();
                    $("#que_ans_modal").modal('hide');
                    // $('#LoadModalFilter').data('modal', null);
                    Selected_QuestionArray = Selected_QuestionArray.concat(NewQuestionArray);
                    NewQuestionArray = [];
                    Totalqstn = Data['tr_no'];
                    $('.language_id').on('select2:select', function (e) {
                        var data = e.params.data;
                        $(".txt_trno").each(function () {
                            let temp_language_id = "#language_id" + $(this).val();
                            $(temp_language_id).val(data.id).trigger('change');
                        });
                    });
                } else {
                    $('#QTable-body').empty();
                    $('#VQADatatable').append(Data['lchtml']);
                // var json = $.parseJSON(Odata);
                // if (json.success == "true") {
                //     $('#question_ans_table').html(json['html']);
                //     $('#que_ans_datatable').DataTable({
                //         destroy: true,
                //         "language": {
                //             "aria": {
                //                 "sortAscending": ": activate to sort column ascending",
                //                 "sortDescending": ": activate to sort column descending"
                //             },
                //             "emptyTable": "No data available in table",
                //             "info": "Showing _START_ to _END_ of _TOTAL_ records",
                //             "infoEmpty": "No records found",
                //             "infoFiltered": "(filtered1 from _MAX_ total records)",
                //             "lengthMenu": "Show _MENU_",
                //             "search": "Search:",
                //             "zeroRecords": "No matching records found",
                //             "paginate": {
                //                 "previous": "Prev",
                //                 "next": "Next",
                //                 "last": "Last",
                //                 "first": "First"
                //             }
                //         },
                //         "bStateSave": false,
                //         "lengthMenu": [
                //             [5, 10, 15, 20, -1],
                //             [5, 10, 15, 20, "All"]
                //         ],
                //         "pageLength": 10,
                //         "pagingType": "bootstrap_full_number",
                //         "processing": true,
                //         //"serverSide": true,
                //         "columnDefs": [{
                //                 'width': '5px',
                //                 'orderable': false,
                //                 'searchable': false,
                //                 'targets': [0]
                //             },
                //             {
                //                 'width': '20px',
                //                 'orderable': true,
                //                 'searchable': true,
                //                 'targets': [1]
                //             },
                //             {
                //                 'width': '250px',
                //                 'orderable': false,
                //                 'searchable': false,
                //                 'targets': [2]
                //             },
                //             {
                //                 'width': '250px',
                //                 'orderable': true,
                //                 'searchable': true,
                //                 'targets': [3]
                //             }, {
                //                 'width': '20px',
                //                 'orderable': false,
                //                 'searchable': false,
                //                 'targets': [4]
                //             }

                //         ],
                //         "order": [
                //             [2, "desc"]
                //         ],
                //     });
                //     customunBlockUI();
                }
                // customunBlockUI();
            },
            error: function (e) {
                customunBlockUI();
            }
        });
    }
}



function add_weight() {
    if (!$('#MappingScript').valid()) {
        return false;
    }

    // if (TempSubParameterArray.length > 0) {
    // console.log(TempSubParameterArray);
    // console.log(Totalqstn);
    // var total_weight = check_overall_parameter_weights(TempSubParameterArray, Totalqstn);
    // if (total_weight > 0 && total_weight != 100) {
    //     ShowAlret("Total weight of selected parameters for each question must be 100%", "error");
    //     return false;
    // }
    // } else {
    //     ShowAlret("Please Add Parameter to the selected questions.", "error");
    //     return false;
    // }


    var form_data = {};
    var other_data = $('#MappingScript').serializeArray();
    $.each(other_data, function (key, input) {
        if (form_data.hasOwnProperty(input.name)) {
            form_data[input.name] = form_data[input.name] + "," + input.value;
        } else {
            form_data[input.name] = input.value;
        }
    });
    // var x = 0;
    // var sub_parameter = [];
    // $.each(TempSubParameterArray, function (key, input) {
    //     sub_parameter[x] = input;
    //     x++;
    // });
    // form_data['sub_parameter'] = sub_parameter;
    $('.language_id').each(function () {
        var id = this.id;
        var val = $("option:selected", this).val();
        form_data[id] = val;
    });

    // if ($(".is_default").is(":checked") == false) {
    //     ShowAlret("Please select default question.", 'error');
    //     return false;
    // }
    $.ajax({
        cache: false,
        async: true,
        type: "POST",
        url: Base_url + "create_trinity/mapping_script/" + Encode_id,
        dataType: "json",
        data: form_data,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            if (Odata['success'] == 1) {
                $('#parameterweight').empty();
                $('#editid').val(Odata['assessment_id']);
                $('#parameterweight').append(Odata['parameters']);
                ShowAlret(Odata['Msg'], 'success');
                
                //KRISHNA --- HIDE WEIGHT MODULE
                setTimeout(function () { // wait for 5 secs(2)
                    window.location.href = Base_url + 'create_trinity/edit/' + Odata['assessment_id'] + "/2";
                }, 500);
            } else {
                $("#parameter_weight_modal").modal('hide');
                $('#ErorDiv').show();
                $('#Errorlog').html(Odata['Msg']);
                window.scrollTo(0, 0);
            }
            customunBlockUI();
        },
        // error: function (XMLHttpRequest, textStatus, errorThrown) {
        //     ShowAlret("Status: " + textStatus + " ,Contact Mediaworks for technical support!");
        // }
    });
}

// mapping goal code start
function save_mapping_goal() {
    var qus_id = $('#map_question_id').val();
    if (qus_id == undefined || qus_id == "") {
        ShowAlret("Please Add Compulsory Questions ", "error");
        return false;

    }
    var x = 0;
    var q_id = [];
    $.each(TempSubParameterArray, function (key, input) {
        q_id[x] = input['question_id'];
        x++;
    });
    for (i = 0; i < q_id.length; i++) {
        var goal_id = $('#p_id' + q_id[i]).val();
        if (goal_id == null) {
            ShowAlret("Please Map A Goal For Question id " + q_id[i], "error");
            return false;
        }
    }
    var from_data = $('#MappingGoalFrom').serializeArray();
    $.ajax({
        url: Base_url + "create_trinity/save_mapping_goal/" + Encode_id,
        beforeSend: function () {
            customBlockUI();
        },
        type: 'POST',
        data: from_data,
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['Success'] && Data['Message'] != '') {
                ShowAlret(Data['Message'], 'success');
            } else {
                ShowAlret(Data['Message'], 'error');
                $('#errordiv').show();
                $('#errorlog').html(Data['Message']);
            }
            customunBlockUI();
        },
        complete: function () {}
    });

}

function pm_submit(flag, edit_id = '') {
    var txn_id = $('#txn_id').val();
    var parameter_id = $('#dp_parameter_id').val();
    var parameter_label_id = $('#parameter_label_id').val();
    var parameter_weight = $('#parameter_weight').val();

    if (parseFloat(parameter_id) == 2 || parseFloat(parameter_id) == 6) {
        chk_count = 0;
        $("input[name='chksp_list[]']").each(function (index) {
            var temp_id = $(this).val();
            if ($('#chksp_id' + temp_id).prop('checked')) {
                chk_count++;
            }
        });
        if (chk_count > 1) {
            ShowAlret("You can choose only one sub-parameter", 'error');
            return false;
        }
    }

    if (txn_id == "") {
        ShowAlret("Txn Id missing", 'error');
        return false;
    }
    if (parameter_id == "") {
        ShowAlret("Please select parameter name", 'error');
        return false;
    }
    if (parameter_label_id == "") {
        ShowAlret("Please select parameter label name", 'error');
        return false;
    }
    if (parameter_weight != "") {
        var isValid = isValidWeight(parameter_weight);
        if (!isValid) {
            ShowAlret("Please add valid parameter weight", 'error');
            return false;
        }
    }
    var is_subparam_ticked = false;
    $("input[name='chksp_list[]']").each(function (index) {
        var temp_id = $(this).val();
        if ($('#chksp_id' + temp_id).prop('checked')) {
            is_subparam_ticked = true;
        }
    });
    if (is_subparam_ticked == false) {
        ShowAlret("Please choose atleast one sub parameter.", 'error');
        return false;
    }
    if (txn_id != '' && parameter_id != '') {

        is_valid = 0;
        $("input[name='chksp_list[]']").each(function (index) {
            var temp_id = $(this).val();
            if ($('#chksp_id' + temp_id).prop('checked')) {
                TempSubParameterArray = TempSubParameterArray.filter(function (obj) {
                    if ((parseInt(obj.txn_id) == parseInt(txn_id)) && (parseInt(obj.parameter_id) == parseInt(parameter_id)) && (parseInt(obj.subparameter_id) == parseInt(temp_id))) {
                        return false;
                    } else {
                        return true;
                    }
                });

                var push_value = {};
                var parameter_data = $('#dp_parameter_id').select2('data');
                var parameter_name = parameter_data[0].text;

                var parameter_label_data = $('#parameter_label_id').select2('data');
                var parameter_label_name = parameter_label_data[0].text;
                var subparameter_name = $('#lblsp_id' + temp_id).html();
                var type_id = "";
                var type_name = "";
                var sentence_keyword = "";

                if ($('#type_id' + temp_id).css('display') == 'none' || $('#type_id' + temp_id).css("visibility") == "hidden") {} else {
                    type_id = $('#type_id' + temp_id).val();
                    type_name = $('#type_id' + temp_id + ' option:selected').text();
                    sentence_keyword = $('#sentkey' + temp_id).val();
                    if (type_id !== "" && sentence_keyword === "") {
                        ShowAlret("Please add Sentence/Keyword for " + parameter_name, 'error'); //mandatory textarea value in case of selected sentence/keyword 
                        is_valid = 1;
                        return false;
                    }
                    sentence_keyword = sentence_keyword.replace(/\n/g, " ");
                }
                push_value['txn_id'] = txn_id;
                push_value['parameter_id'] = parameter_id;
                push_value['parameter_name'] = parameter_name;
                push_value['parameter_label_id'] = parameter_label_id;
                push_value['parameter_label_name'] = parameter_label_name;
                push_value['subparameter_id'] = temp_id;
                push_value['subparameter_name'] = subparameter_name;
                push_value['type_id'] = type_id;
                push_value['type_name'] = type_name;
                push_value['sentence_keyword'] = sentence_keyword;
                push_value['parameter_weight'] = parameter_weight;
                TempSubParameterArray.push(push_value);
            } else {
                TempSubParameterArray = TempSubParameterArray.filter(function (obj) {
                    if ((parseInt(obj.txn_id) == parseInt(txn_id)) && (parseInt(obj.parameter_id) == parseInt(parameter_id)) && (parseInt(obj.subparameter_id) == parseInt(temp_id))) {
                        return false;
                    } else {
                        return true;
                    }
                });
            }
        });
        if (is_valid == 1) {
            return false; //Abort from here in case of invalid sentence_keyword
        }
        var TempPSMArray = TempSubParameterArray;
        var TempPSMCount = TempPSMArray.filter(function (objs) {
            if ((parseInt(objs.txn_id) == parseInt(txn_id)) && (parseInt(objs.parameter_id) == parseInt(parameter_id))) {
                return true;
            } else {
                return false;
            }
        });
        if (Object.keys(TempPSMCount).length <= 0) {
            var parameter_data = $('#dp_parameter_id').select2('data');
            var parameter_name = parameter_data[0].text;
            var push_value_ii = {};
            push_value_ii['txn_id'] = txn_id;
            push_value_ii['parameter_id'] = parameter_id;
            push_value_ii['parameter_name'] = parameter_name;
            push_value_ii['parameter_label_id'] = parameter_label_id;
            push_value_ii['parameter_label_name'] = parameter_label_name;
            push_value_ii['subparameter_id'] = "999999999999";
            push_value_ii['subparameter_name'] = "";
            push_value_ii['type_id'] = "";
            push_value_ii['type_name'] = "";
            push_value_ii['sentence_keyword'] = "";
            push_value_ii['parameter_weight'] = parameter_weight;
            TempSubParameterArray.push(push_value_ii);
        }
    }

    div_html = printonscreen_keyword_sentence(txn_id, parameter_id);
    if (flag == 0) {
        var div_element = "#paramsub" + txn_id;
        $(div_element).empty();
        $(div_element).html('');
        $(div_element).html(div_html);
        $("#dp_parameter_id").val("").trigger('change');
        $("#parameter_label_id").val("").trigger('change');
        $("#parameter_weight").val("");
        getUnique_paramters();
    }
    if (flag == 1) {
        var div_element = "#paramsub" + txn_id;
        $(div_element).empty();
        $(div_element).html('');
        $(div_element).html(div_html);
        var total_weight = check_parameter_weights(TempSubParameterArray, txn_id);
        if (total_weight > 0 && (total_weight < 100 || total_weight > 100)) {
            ShowAlret("Total weight of selected parameters must be 100%", "error");
            return false;
        }
        getUnique_paramters();
        $('#CloseModalBtn').click();
    }
}

function getUnique_paramters() {
    $.ajax({
        url: Base_url + "create_trinity/add_parameter_weights",
        type: 'POST',
        data: $('#MappingScript').serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data != '') {
                $('#weights_table tbody').html(Data['html']);
            }
            get_weight();
            customunBlockUI();
        }
    });
}
// mapping goal code end

function DelQueAns(Id) {
    $.confirm({
        title: 'Confirm!',
        content: " Are you sure you want to delete Question and Answer? ",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        type: "POST",
                        url: Base_url + "create_trinity/delete_que_ans",
                        data: {
                            deleteid: Id
                        },
                        beforeSend: function () {
                            customBlockUI();
                        },
                        success: function (response_json) {
                            var response = JSON.parse(response_json);
                            ShowAlret(response.message, response.alert_type);
                            $('#LoadModalFilterQue').hide();
                            DatatableRefresh($('#script').val());
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

function parameter_weight_submit() {
    console.log($('#parameter_form').serialize());
    $.ajax({
        url: Base_url + "create_trinity/add_parameter_weight/",
        type: 'POST',
        data: $('#parameter_form').serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['Success'] && Data['Message'] != '') {
                $('#para_weight_table').empty();
                $('#para_weight_table').append(Data['partameter_weight_table']);
                ShowAlret(Data['Message'], 'success');
                $("#parameter_weight_modal").modal('hide');
                setTimeout(function () { // wait for 5 secs(2)
                    window.location.href = Base_url + 'create_trinity/edit/' + Data['Encode_id'] + "/2";
                }, 500);
                $('#errordiv').hide();
                $('#errorlog').html('');
            } else {
                ShowAlret(Data['Message'], 'error');
                $('#errordiv').show();
                $('#errorlog').html(Data['Message']);
            }
            customunBlockUI();
        }
    });
}

function regenerate_weight_table(edit_id = '') {
    $.ajax({
        url: Base_url + "create_trinity/regenerate_weight_table/" + edit_id,
        type: 'POST',
        data: $('#parameter_form').serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['Success'] && Data['Message'] != '') {
                $('#para_weight_table').empty();
                $('#para_weight_table').append(Data['partameter_weight_table']);
                // ShowAlret(Data['Message'], 'success');
                $("#parameter_weight_modal").modal('hide');
            } else {
                ShowAlret(Data['Message'], 'error');
                $('#errordiv').show();
                $('#errorlog').html(Data['Message']);
            }
            customunBlockUI();
        }
    });
}