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
        url: base_url + "adoption/ajax_getWeeks",
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

//Graph 1 ,2 ,3, 4 Start Here
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
        url: base_url + "adoption/assessment_started",
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
    Adoption_by_team();
    Adoption_by_division();
    Adoption_by_module();
    Adoption_by_region();
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
        url: base_url + "adoption/assessment_complted",
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
        url: base_url + "adoption/get_raps_mapped_user",
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

function total_users_Ac_In(IsCustom) {
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
        url: base_url + "adoption/total_users_Ac_In",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#AIR_Users").html(json['AIR_Users']);
            }
            customunBlockUI();
        }
    });
}

function RapsPlayedCompleted(IsCustom) {
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
        url: base_url + "adoption/get_raps_played_completed",
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

function total_video_uploded(IsCustom) {
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
        url: base_url + "adoption/total_video_uploded",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#total_videos_uploaded").html(json['total_video_uploaded']);
            }
            customunBlockUI();
        }
    });
}

function total_video_processed(IsCustom) {
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
        url: base_url + "adoption/total_video_processed",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#total_videos_processed").html(json['total_video_processed']);
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
        url: base_url + "adoption/total_report_sent",
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

function Adoption_by_team() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#process_assessment_id").val();
    var ManagerID = $("#status_id_manager").val();
    if (AssessmentId != "") {
        $('#responsive-modal-adoption').modal('toggle');
        if(ManagerID== null){
            ShowAlret("Please select Managers ..!!", 'error');
            return false;
        }
        // var lencount = ManagerID.length;
        // if (lencount > "5") {
        //     ShowAlret("Please select Only Five Managers .!!", 'error');
        //     // $('#responsive-modal-adoption').modal('toggle');
        //     return false;
        // }
    }
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,
            manager_id: ManagerID
        },
        //async: false,
        url: base_url + "adoption/adoption_by_team",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#AdbTeam").html(json['adb_team']);
            }
            customunBlockUI();
        }
    });
}

function Adoption_by_division() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#assessment_id").val();
    var DivisionSet = $("#division_id").val();
    if (AssessmentId != "") {
        $('#responsive-modal-division').modal('toggle');
        if(DivisionSet== null){
            ShowAlret("Please select Division ..!!", 'error');
            return false;
        }
        var lencount = DivisionSet.length;
        if (lencount > "5") {
            ShowAlret("Please select Only Five Division ..!!", 'error');
            return false;
        }
    }
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,
            DivisionSet: DivisionSet
        },
        //async: false,
        url: base_url + "adoption/adoption_by_divison",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#adoption_by_division").html(json['adoption_by_division']);
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
            ShowAlret("Must Be Select Two Modules .!!", 'error');
            return false;
        }
        else if(lencount > "5"){
            ShowAlret("Please select Only Five Modules .!!", 'error');
            return false;
        }
    }
   
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId
        },
        //async: false,
        url: base_url + "adoption/adoption_by_module",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#adoption_by_module").html(json['adoption_by_modules']);
            }
            customunBlockUI();
        }
    });
}

function Adoption_by_region(){
    
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#am_id").val();
    var RegoionId = $("#region_id").val();
    
    if (AssessmentId != "") {
        $('#responsive-modal-region').modal('toggle');
        if(RegoionId == null){
            ShowAlret("Please select Regions .!!", 'error');
            return false;
        }
        var lencount = RegoionId.length;
         if(lencount > "5"){
            ShowAlret("Please select Only Five Regions .!!", 'error');
            return false;
        }
    }
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,region_id:RegoionId
        },
        //async: false,
        url: base_url + "adoption/adoption_by_region",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#adoption_by_region").html(json['adoption_by_region']);
            }
            customunBlockUI();
        }
    });
}

// End Here