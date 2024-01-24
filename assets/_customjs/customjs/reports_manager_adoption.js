function dashboard_refresh() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    if ($('#report_by').val() == 1) {
        $(".th-desc").text("Parameter");
    } else {
        $(".th-desc").text("Assessment");
    }
    customunBlockUI();
}

function getWeek() {
    $.ajax({
        type: "POST",
        data: {
            year: $('#year').val(),
            month: $('#month').val()
        },
        async: false,
        url: base_url + "reports_manager_adoption/ajax_getWeeks",
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


function VideoUploadedProcessed(IsCustom) {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            report_by: $('#report_by').val(),
            demo: $('#demo').val(),
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
        url: base_url + "reports_manager_adoption/get_uploaded_processed",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#uploadedProcessed").html(json['Uploaded_processed_video']);

            }
            customunBlockUI();
        }
    });
}

function Adoption_by_module(){
    
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#new_assessment_id").val();
    if (AssessmentId) {
        $('#responsive-modal-module').modal('toggle');
        var lencount = AssessmentId.length;
        if (lencount == "1") {
            ShowAlret("Must Be Select Two Assessment .!!", 'error');
            // $('#responsive-modal-module').modal('toggle');
            return false;
        }
       else if(lencount > "5"){
            ShowAlret("Please select Only Five Assessment .!!", 'error');
            // $('#responsive-modal-module').modal('toggle');
            return false;
        }
    }
   
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId
        },
        //async: false,
        url: base_url + "reports_manager_adoption/adoption_by_module",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#reports_manager_adoption_by_module").html(json['adoption_by_modules']);
            }
            customunBlockUI();
        }
    });
}
function RepsPlayedCompleted(IsCustom) {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            report_by: $('#report_by').val(),
            demo: $('#demo').val(),
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
        url: base_url + "reports_manager_adoption/get_raps_played_completed",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#Unplayed_Played").html(json['Played_Raps_Completed']);

            }
            customunBlockUI();
        }
    });
}

function Total_Report_Sent(IsCustom) {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            report_by: $('#report_by').val(),
            report_type: $('#report_type').val(),
            assessment_id1: $('#assessment_id1').val(),
            month: $('#month').val(),
            dyear: $('#dyear').val(),
            week: $('#week').val(),
            StartDate: StartDate,
            EndDate: EndDate,
            region_id: $('#region_id').val(),
            store_id: $('#store_id').val(),
            IsCustom: IsCustom
        },
        //async: false,
        url: base_url + "reports_manager_adoption/total_report_sent",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#total_reports_sent").html(json['total_report_sent']);
            }
            customunBlockUI();
        }
    });
}

function Total_Question_mapped(IsCustom) {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            report_by: $('#report_by').val(),
            report_type: $('#report_type').val(),
            assessment_id1: $('#assessment_id1').val(),
            month: $('#month').val(),
            dyear: $('#dyear').val(),
            week: $('#week').val(),
            StartDate: StartDate,
            EndDate: EndDate,
            region_id: $('#region_id').val(),
            store_id: $('#store_id').val(),
            IsCustom: IsCustom
        },
        //async: false,
        url: base_url + "reports_manager_adoption/total_questions_mapped",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#total_questions").html(json['total_question_mapped']);
            }
            customunBlockUI();
        }
    });
}

function reps_mapped_user(IsCustom) {
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
        url: base_url + "reports_manager_adoption/get_raps_mapped_user",
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

function Adoption_by_reps(){
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#am_id").val();
    var ManagerID = $("#assesment_users").val();
    if (AssessmentId != "") {
        $('#responsive-modal-reps').modal('toggle');
        var lencount = ManagerID.length;
        if (ManagerID == "") {
            ShowAlret("Please select Users.!!", 'error');
            // $('#responsive-modal-reps').modal('toggle');
            return false;
        }
        else if(lencount > "5"){
            ShowAlret("Please select Only Five Users .!!", 'error');
            // $('#responsive-modal-reps').modal('toggle');
            return false;
        }
    }
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,
            manager_id: ManagerID
        },
        //async: false,
        url: base_url + "reports_manager_adoption/adoption_by_reps",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#adoption_by_reps").html(json['adoption_by_reps']);
            }
            customunBlockUI();
        }
    });
}

// End Here