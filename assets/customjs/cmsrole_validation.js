var frmRole = $('#frmRole');
var form_error = $('.alert-danger', frmRole);
var form_success = $('.alert-success', frmRole);
frmRole.validate({
    errorElement: 'span',
    errorClass: 'help-block help-block-error',
    focusInvalid: false,
    ignore: "",
    rules: {
        company_id: {
            required: true
        },
        name: {
            required: true,
            roleCheck: true,
        }
    },
    invalidHandler: function (event, validator) {
        form_success.hide();
        form_error.show();
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
jQuery.validator.addMethod("roleCheck", function (value, element) {
    var isSuccess = false;
    var lEncode_id = Encode_id;
    if (AddEdit == 'C') {
        lEncode_id = "";
    }
    $.ajax({
        type: "POST",
        data: {role: value, company: $('#company_id').val(),role_id:lEncode_id},
        url: base_url+"/Check_role",
        async: false,
        success: function (msg) {
            isSuccess = msg != "" ? false : true;
        }
    });
    return isSuccess;
}
, "Role name already exists!!!");
function SaveRoleData() {
    if (!frmRole.valid()) {
        return false;
    }
    if (AddEdit == 'A') {
        var url = base_url + '/submit/';
    } else if (AddEdit == 'C') {
        url = base_url + '/submit/' + Encode_id;
    } else {
        url = base_url + '/update/' + Encode_id
    }
    $.ajax({
        type: "POST",
        url: url,
        data: frmRole.serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['success']) {
                ShowAlret(Data['Msg'], 'success');
                if (AddEdit == 'A' || AddEdit == 'C') {
                    setTimeout(function () {// wait for 5 secs(2)
                        window.location.href = base_url;
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
var role_table = $('#role_table');
role_table.dataTable({
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
    "columnDefs": [
        {'width': '70px', 'orderable': false, 'searchable': false, 'targets': [0]},
        {'width': 'auto', 'orderable': false, 'searchable': true, 'targets': [1]},
        {'width': '70px', 'orderable': false, 'searchable': false, 'targets': [2]},
        {'width': '50px', 'orderable': false, 'searchable': false, 'targets': [3]},
        {'width': '50px', 'orderable': false, 'searchable': false, 'targets': [4]},
        {'width': '50px', 'orderable': false, 'searchable': false, 'targets': [5]},
        {'width': '50px', 'orderable': false, 'searchable': false, 'targets': [6]},
        {'width': '50px', 'orderable': false, 'searchable': false, 'targets': [7]},
        {'width': '50px', 'orderable': false, 'searchable': false, 'targets': [8]},
        {'width': '50px', 'orderable': false, 'searchable': false, 'targets': [9]}
    ],
    "paging": false,
    "pageLength": -1,
    "pagingType": "bootstrap_full_number",
    "processing": true,
    //"aaSorting": [[0, "asc"]],
    "autoWidth": false,
    "searching": true,
    responsive: true,
    "ordering": false,
    "dom": '<"toolbar">frtip'
});
$("div.toolbar").html('<div class="col-md-3"><label class="mt-checkbox mt-checkbox-outline" for="access_all"> Select All<input id="access_all" type="checkbox" value="1" onclick="SelectAll_role()" /><span></span></label></div>');
function RoleCheckAll(module)
{
    if ($('#' + module).prop("checked") == true) {
        $("input[name='" + module + "_own[]']").prop('checked', true);
    } else {
        $("input[name='" + module + "_own[]']").prop('checked', false);
    }
}
function SelectAll_role() {
    if ($('#access_all').prop("checked") == true) {
        $(".checkRole").prop('checked', true);
    } else {
        $(".checkRole").prop('checked', false);
    }
}