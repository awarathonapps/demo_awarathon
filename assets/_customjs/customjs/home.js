function dashboard_refresh() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
}

function getWeek() {
    $.ajax({
        type: "POST",
        data: {
            year: $('#year').val(),
            month: $('#month').val()
        },
        async: false,
        url: base_url + "home/ajax_getWeeks",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                var WStartEndDate = Oresult['WStartEnd'];
                var week_option = '<option value="">All Week</option>';
                for (var i = 0; i < WStartEndDate.length; i++) {
                    week_option += '<option value="' + WStartEndDate[i] + '">' + 'Week-' + (i + 1) + '</option>';
                }
                $('#week').empty();
                $('#week').append(week_option);
            }
            customunBlockUI();
        }
    });
}

function assessment_started(IsCustom) {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            report_by: $('#report_by').val(),
            assessment_report: $('#assessment_report').val(),
            report_type: $('#report_type').val(),
            assessment_id1: $('#assessment_id1').val(),
            month: $('#month').val(),
            newyear: $('#newyear').val(),
            week: $('#week').val(),
            StartDate: StartDate,
            EndDate: EndDate,
            region_id: $('#region_id').val(),
            store_id: $('#store_id').val(),
            IsCustom: IsCustom
        },
        url: base_url + "home/assessment_started",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#assessment_started").html(json['startcount']);

            }
            customunBlockUI();
        }
    });
}

function assessment_complted(IsCustom) {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            report_by: $('#report_by').val(),
            assessment_report_ended: $('#assessment_report_ended').val(),
            report_type: $('#report_type').val(),
            assessment_id1: $('#assessment_id1').val(),
            month: $('#month').val(),
            countyear: $('#countyear').val(),
            week: $('#week').val(),
            StartDate: StartDate,
            EndDate: EndDate,
            region_id: $('#region_id').val(),
            store_id: $('#store_id').val(),
            IsCustom: IsCustom
        },
        //async: false,
        url: base_url + "home/assessment_complted",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#assessment_complted").html(json['endcount']);
            }
            customunBlockUI();
        }
    });
}

function raps_mapped_user(IsCustom) {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            report_by: $('#report_by').val(),
            map_user: $('#map_user').val(),
            report_type: $('#report_type').val(),
            assessment_id1: $('#assessment_id1').val(),
            month: $('#month').val(),
            newyear: $('#newyear').val(),
            week: $('#week').val(),
            StartDate: StartDate,
            EndDate: EndDate,
            region_id: $('#region_id').val(),
            store_id: $('#store_id').val(),
            IsCustom: IsCustom
        },
        url: base_url + "home/get_raps_mapped_user",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#map_users").html(json['map_user']);

            }
            customunBlockUI();
        }
    });
}

function DatatableRefresh_Ideal() {
    var table = $('#index_table_ideal');
    table.dataTable({
        destroy: true,
        "language": {
            "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            },
            "emptyTable": "No data available in table",
            // "info": "Showing _START_ to _END_ of _TOTAL_ records",
            "infoEmpty": "No records found",
            // "infoFiltered": "(filtered1 from _MAX_ total records)",
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
        "dom": 'r',
        // "paging": false,
        "searching":false,
        "bStateSave": false,
        "lengthMenu": [
            [5, 10, 15, 20, -1],
            [5, 10, 15, 20, "All"]
        ],
        "autoWidth": false,
        "pageLength": 10,
        "pagingType": "bootstrap_full_number",
        "columnDefs": [{
                'width': '3%',
                'orderable': true,
                'searchable': true,
                'targets': [0]
            },
            {
                'width': '15%',
                'orderable': true,
                'searchable': true,
                'targets': [1]
            },
            {
                'width': '15%',
                'orderable': true,
                'searchable': false,
                'targets': [2]
            },
            {
                'width': '10%',
                'orderable': true,
                'searchable': true,
                'targets': [3]
            },
            {
                'width': '10%',
                'orderable': true,
                'searchable': true,
                'targets': [4]
            },
            {
                'width': '10%',
                'orderable': true,
                'searchable': false,
                'targets': [5]
            },
            {
                'width': '5%',
                'orderable': true,
                'searchable': false,
                'targets': [6]
            },
            {
                'width': '50px',
                'orderable': false,
                'searchable': false,
                'targets': [7]
            },
            {
                'width': '50px',
                'orderable': false,
                'searchable': false,
                'targets': [8]
            },
            {
                'width': '50px',
                'orderable': false,
                'searchable': false,
                'targets': [9]
            },
            // {
            //     'width': '50px',
            //     'orderable': false,
            //     'searchable': false,
            //     'targets': [10]
            // },
        ],
        "order": [
            [0, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        "sAjaxSource": 'home/DatatableRefresh_ideal',
        "fnServerData": function (sSource, aoData, fnCallback) {
            // aoData.push({name: 'filter_status', value: $('#filter_status').val()});
            // aoData.push({name: 'assessment_type', value: $('#assessment_type').val()});
            // aoData.push({name: 'question_type', value: $('#question_type').val()});
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
// By Bhautik Rana 01-02-2023 
function CandidateDatatableRefresh(assessment_id, report_type, is_send_tab) {
    var table = $('#CandidateFilterTable');
    table.dataTable({
        destroy: true,
        "language": {
            "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            },
            "emptyTable": "No reports generated for this assessment",
            "info": "Showing _START_ to _END_ of _TOTAL_ records",
            "infoEmpty": "No records found",
            "infoFiltered": "(filtered 1 from _MAX_ total records)",
            "lengthMenu": "Show _MENU_",
            "search": "Search:",
            "zeroRecords": "No reports generated for this assessment",
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
            // {'width': '','orderable': false,'searchable': false,'targets': [0]}, 
            // {'width': '','orderable': false,'searchable': true,'targets': [1]}, 
            // {'width': '','orderable': false,'searchable': true,'targets': [2]}, 
            // {'width': '','orderable': false,'searchable': true,'targets': [3]},
            // {'width': '','orderable': false,'searchable': false,'targets': [4]},
            // {'width': '','orderable': false,'searchable': false,'targets': [5]},
            // {'width': '','orderable': false,'searchable': false,'targets': [6]},
            // {'width': '','orderable': false,'searchable': false,'targets': [7]},
        ],
        "order": [
            // [1, "desc"]
        ],
        "processing": true,
        // "serverSide": true,
        "sAjaxSource": base_url + "home/CandidateDatatableRefresh/" + assessment_id + "/" + is_send_tab,
        "fnServerData": function (sSource, aoData, fnCallback) {
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

function showMessage() {
    ShowAlret("You don't have access to see this feature, please contact admin", 'error');
    return false;
}
function scheduleCandidateEmail(company_id,assessment_id,select_candidates){
	$.ajax({
		url: base_url+'ai_email_cron/schedule_data/',
		type: 'POST',
		data: 'company_id='+company_id+'&assessment_id='+assessment_id+'&trainee_id='+select_candidates+'&sendAll=1',
		dataType: 'JSON',
		beforeSend: function () {
            customBlockUI();
        },
		success: function (data){
			if (data.success) {
                ShowAlret(data.message, 'success');
            } else {
                ShowAlret(data.message, 'error');
            }
			//CandidateDatatableRefresh(assessment_id);
            customunBlockUI();
		},error: function (data){
			console.log(data);
		}
	});
}
// By Bhautik Rana 01-02-2023 