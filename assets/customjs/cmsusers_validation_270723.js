var frmUsers = $('#frmUsers');
var form_error = $('.alert-danger', frmUsers);
var form_success = $('.alert-success', frmUsers);
frmUsers.validate({
    errorElement: 'span',
    errorClass: 'help-block help-block-error',
    focusInvalid: false,
    ignore: "",
    rules: {
        loginid: {
            required: true,
            noSpace: true,
            loginIDCheck: true
        },
        company_id: {
            required: true
        },
        login_type: {
            required: true
        },
        region_id: {
            required: true
        },
        password: {
            required: true
        },
        confirmpassword: {
            required: true,
            equalTo: "#password"
        },
        status: {
            required: true
        },
        roleid: {
            required: true
        },
        first_name: {
            required: true,
            htmlTagCheck: true
            //first_lastCheck: true
        },
        last_name: {
            required: true,
            htmlTagCheck: true
            //first_lastCheck: true
        },
        email: {
            required: true,
            email: true,
            htmlTagCheck: true
            //emailCheck: true
        },
        email2: {
            email: true,
            htmlTagCheck: true
        },
        depart:{
            required: true
        },
        emp_id:{
            noSpace: true,
            empcodeCheck: true
        },
        mobile: {
            // required: true,
            validateMobile: true
        },
        contactno: {
            validateContact: true
        },
        fax: {
            validateFax: true
        },
        address:{
            htmlTagCheck: true
        },
        address2:{
            htmlTagCheck: true
        },
        avatar_path:{
            htmlTagCheck: true
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
        form_success.show();
        form_error.hide();
        Ladda.bind('button[id=role-submit]');
        form.submit();
    }
});
$('.select2,.select2-multiple').on('change', function () {
    $(this).valid();
});
jQuery.validator.addMethod("emailCheck", function (value, element) {
    var lEncode_id = Encode_id;
    if (AddEdit == 'C') {
        lEncode_id = "";
    }
    var isSuccess = false;
    $.ajax({
        type: "POST",
        data: {email_id: value,cmp_user:lEncode_id,company_id:$('#company_id').val()},
        url: base_url + "Company_users/Check_emailid",
        async: false,
        success: function (msg) {
            isSuccess = msg != "" ? false : true;
        }
    });
    return isSuccess;
}
, "Email id already exists!!!");
jQuery.validator.addMethod("empcodeCheck", function (value, element) {
    var lEncode_id = Encode_id;
    if (AddEdit == 'C') {
        lEncode_id = "";
    }
    var isSuccess = false;
    $.ajax({
        type: "POST",
        data: {emp_id: value,cmp_user:lEncode_id,company_id:$('#company_id').val()},
        url: base_url + "Company_users/Check_empcode",
        async: false,
        success: function (msg) {
            isSuccess = msg != "" ? false : true;
        }
    });
    return isSuccess;
}
, "Employee code already exists!!!");
jQuery.validator.addMethod("loginIDCheck", function (value, element) {
    var isSuccess = false;
    var lEncode_id = Encode_id;
    if (AddEdit == 'C') {
        lEncode_id = "";
    }
    $.ajax({
        type: "POST",
        data: {login_id: value, company_id: $('#company_id').val(),userid:lEncode_id},
        url: base_url + "Company_users/Check_loginID",
        async: false,
        success: function (msg) {
            isSuccess = msg != "" ? false : true;
        }
    });
    return isSuccess;
}
, "Login ID already exists!!!");

jQuery.validator.addMethod("noSpace", function (value, element) {
    return value.indexOf(" ") < 0 && value != "";
}, "Space are not allowed");
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
jQuery.validator.addMethod("validateFax", function (fax_number, element) {
    fax_number = fax_number.replace(/\s+/g, "");
    return this.optional(element) || fax_number.length > 7 &&
            fax_number.match(/^\+?[0-9]{7,}$/);
}, "Please specify a valid fax number");

//KRISHNA --- VAPT - NOT ALLOW HTML OR JAVASCRIPT TAGS
jQuery.validator.addMethod("htmlTagCheck", function (value, element) {
    var isSuccess = true;
    if(value.indexOf("<") > 0 || value.indexOf(">") > 0 || value.indexOf("/") > 0){
        isSuccess = false;
    }
    return isSuccess;
}, "Value is not valid!!!");

function SaveUserData() {
    if (!frmUsers.valid()) {
        return false;
    }
    if (AddEdit == 'A') {
        var url = base_url + 'company_users/submit/';
    } else if (AddEdit == 'C') {
        url = base_url + 'company_users/submit/' + Encode_id;
    } else {
        url = base_url + 'company_users/update/' + Encode_id
    }
    $.ajax({
        type: "POST",
        url: url,
        data: frmUsers.serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                if (AddEdit == 'A' || AddEdit == 'C') {
                    setTimeout(function () {// wait for 5 secs(2)
                        window.location.href = base_url + 'company_users/edit/' + Data['id'] + "/2";
                    }, 500);
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
function Rolechange() {
    $.ajax({
        type: "POST",
        beforeSend: function () {
            customBlockUI();
        },
        data: "data=" + $('#company_id').val(),
        async: false,
        url: base_url + "company_users/ajax_populate_roles",
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var RoleMSt = Oresult['roleResult'];
                var DesignationMSt = Oresult['designationResult'];
                var RegionMSt = Oresult['regionResult'];
                // console.log(TopicMSt);
                var option = '<option value="">Please Select</option>';
                for (var i = 0; i < RoleMSt.length; i++) {
                    option += '<option value="' + RoleMSt[i]['arid'] + '" >' + RoleMSt[i]['rolename'] + '</option>';
                }
                var option1 = '<option value="">Please Select</option>';
                for (var i = 0; i < DesignationMSt.length; i++) {
                    option1 += '<option value="' + DesignationMSt[i]['id'] + '" >' + DesignationMSt[i]['description'] + '</option>';
                }
                var option2 = '<option value="">Please Select</option>';
                for (var i = 0; i < RegionMSt.length; i++) {
                    option2 += '<option value="' + RegionMSt[i]['id'] + '" >' + RegionMSt[i]['region_name'] + '</option>';
                }
                $('#roleid').empty();
                $('#roleid').append(option);
                $('#designation').empty();
                $('#designation').append(option1);
                $('#region_id').empty();
                $('#region_id').append(option2);
                //$("#topic_id").trigger("change");
            }
            customunBlockUI();
        }
    });
}
function UpdateUserRightsData(Module) {
    if (Module == 1) {
        var sdata = $('#TrainerRightsFrm').serialize();
    } else {
        sdata = $('#WorkshopRightsFrm').serialize();
    }
    $.ajax({
        url: base_url + "Company_users/UpdateUserRights/"+Module+"/" + Encode_id,
        type: 'POST',
        data: sdata,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['Success']) {
                ShowAlret(Data['Msg'], 'success');
                if(Module==1){
                    loadurightsTable();
                }else{
                    loadworkshopTable();
                }
            } else {
                ShowAlret(Data['Msg'], 'error');
            }
            customunBlockUI();
        }
    });
}
function LoadCustomWorkshop(RowID){
    $.ajax({
        url: base_url + "Company_users/getWorkshopList/" + Encode_id,
        type: 'POST',
        data: {company_id:$('#company_id').val(),workshop_region: $('#WRegion_id'+RowID).val(),
        workshop_type:$('#Workshop_type_id'+RowID).val(),custom_workshop:$('#Workshop_id'+RowID).val()},
        beforeSend: function () {
            customBlockUI();
        },
        success: function (html) {
            $('#Workshop_id'+RowID).empty();
            $('#Workshop_id'+RowID).append(html);
            customunBlockUI();
        }
    });
}
function LoadCustomTrainer(RowID){
    $.ajax({
        url: base_url + "Company_users/getTrainerList/" + Encode_id,
        type: 'POST',
        data: {company_id:$('#company_id').val(),Trainer_region: $('#Tregion_id'+RowID).val()},
        beforeSend: function () {
            customBlockUI();
        },
        success: function (html) {
            $('#TRTrainer_id'+RowID).empty();
            $('#TRTrainer_id'+RowID).append(html);
            customunBlockUI();
        }
    });
}

function AddTRightsMore(){
    $.ajax({
        url: base_url + "Company_users/getTrainerRow/" + Encode_id,
        type: 'POST',
        data: {company_id:$('#company_id').val(),TRightsRow:TRightsRow},
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            //TrainerArrray.push(Data['trainerData']);
            if(TRightsRow==1){
                $("#TRow_0").remove();
            }
            $('#TrainerRightstable tbody').append(Data['htmlData']);
            $('#Tregion_id' + TRightsRow).select2();
            $('#TRTrainer_id' + TRightsRow).select2({
                    placeholder: 'All Rights'});
            TRightsRow++;
            customunBlockUI();

        }
    });
}
function AddWRightsMore(){
    $.ajax({
        url: base_url + "Company_users/getWorkshopRow/" + Encode_id,
        type: 'POST',
        data: {company_id:$('#company_id').val(),WRightsRow:WRightsRow},
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            //TrainerArrray.push(Data['trainerData']);
            if(WRightsRow==1){
                $("#Row_0").remove();
            }
            $('#WorkshopRightstable tbody').append(Data['htmlData']);
            $('#WRegion_id' + WRightsRow).select2();
            $('#Workshop_type_id' + WRightsRow).select2({
                    placeholder: 'All Rights'});
            $('#Workshop_id' + WRightsRow).select2({
                    placeholder: 'All Rights'});
            WRightsRow++;
            customunBlockUI();

        }
    });
}
function RowDelete(r) {
    $.confirm({
        title: 'Confirm!',
        content: "Are you sure you want to remove this.?",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $("#Row_" + r).remove();
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}
function TrainerRowDelete(r) {
    $.confirm({
        title: 'Confirm!',
        content: "Are you sure you want to remove this.?",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $("#TRow_" + r).remove();
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}

function loadworkshopTable() {
    var Table = $('#workshop_table');
    var Url = base_url + "company_users/WorkshopRights_table/" + Encode_id;
    oTable2 = Table.dataTable({
        destroy: true,
        "language": {
            "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            },
            "emptyTable": "No any workshop rights are set.",
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
            {'width': '30px', 'orderable': true, 'searchable': false, 'targets': [0]},
        ],
        "order": [
            [1, "asc"]
        ],
        "processing": true,
        "serverSide": false,
        "sAjaxSource": Url,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
            aoData.push({name: 'CheckedValue', value: $('input[name=workshoprights_type]:checked').val()});
            //aoData.push({name: 'workshop_region', value: $('#workshop_region').val()});
            //aoData.push({name: 'workshop_type', value: $('#workshop_type').val()});
            //aoData.push({name: 'custom_workshop', value: $('#custom_workshop').val()});
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

function loadurightsTable() {
    oTable = $('#urights_table').dataTable({
        destroy: true,
        "language": {
            "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            },
            "emptyTable": "No any Trainer rights are set.",
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
            {'width': '30px', 'orderable': true, 'searchable': false, 'targets': [0]},
        ],
        "order": [
            [1, "asc"]
        ],
        "processing": true,
        "serverSide": false,
        "sAjaxSource": base_url + "company_users/UserRights_tableRefresh/" + Encode_id,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
            aoData.push({name: 'CheckedValue', value: $('input[name=userrights_type]:checked').val()});
            aoData.push({name: 'trainer_region', value: $('#trainer_region').val()});
            aoData.push({name: 'cust_trainer', value: $('#cust_trainer').val()});
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