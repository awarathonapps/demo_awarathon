function dashboard_refresh() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    // if ($('#report_by').val() == 1) {
    //     $(".th-desc").text("Parameter");
    // } else {
    //     $(".th-desc").text("Assessment");
    // }
    // customunBlockUI();
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
    // Adoption_by_team();
    // Adoption_by_region();
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
    if (AssessmentId != null && ManagerID == null) {
        ShowAlret("Please select Managers ..!!", 'error');
        return false;
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
            $('#responsive-modal-adoption').modal('hide');
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#AdbTeam").html(json['adb_team']);
            }
            customunBlockUI();
        }
    });
}

function Adoption_by_division(IsCustom) {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var manager_id = $("#manager_id").val();
    var AssessmentId = $("#assessment_id").val();
    var DivisionSet = $("#division_id").val();

    if (AssessmentId != null && DivisionSet == null) {
        ShowAlret("Please Select Division ..!!", 'error');
        return false;
    }
    if (manager_id != null && DivisionSet == null) {
        ShowAlret("Please Select Division ..!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,
            DivisionSet: DivisionSet,
            Manager_id: manager_id,
            StartDate: StartDate,
            EndDate: EndDate,
            IsCustom: IsCustom
        },
        //async: false,
        url: base_url + "adoption/adoption_by_divison",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                var assessment_id = json.assessment_set;
                var division_set = json.division_set;
                if (assessment_id != '' && division_set != '') {
                    $('#assessment_id').val(assessment_id);
                    $('#assessment_id').trigger('change');
                    get_div_manager(division_set);
                }
                $("#adoption_by_division").html(json['adoption_by_division']);
            }
            $('#responsive-modal-division').modal('hide');
            customunBlockUI();
        }
    });
}
//  By Bhautik Rana 05-01-2022 adoption Comments changes 

// function Adoption_by_module() {

//     if ($('#company_id').val() == "") {
//         ShowAlret("Please select Company.!!", 'error');
//         return false;
//     }

//     var AssessmentId = $("#new_assessment_id").val();
//     var trainer_id = $("#trainer_id").val();
//     var region_id = $("#regionId").val();
//     var division_id = $("#divsionId").val();
//     var is_custom = $("#is_custom").val();

//     $.ajax({
//         type: "POST",
//         data: {
//             assessment_id: AssessmentId,
//             trainer_id: trainer_id,
//             region_id: region_id,
//             division_id: division_id,
//             StartDate: StartDate,
//             EndDate: EndDate,
//             is_custom: is_custom
//         },
//         //async: false,
//         url: base_url + "adoption/adoption_by_module",
//         beforeSend: function () {
//             customBlockUI();
//         },
//         success: function (response) {
//             $('#responsive-modal-module').modal('hide');
//             if (response != '') {
//                 var json = jQuery.parseJSON(response);

//                 var trainer_id = json.trainer_id;
//                 var region_id = json.region_id;
//                 var division_id = json.division_id;
//                 var assessment_id = json.assessment_id;
//                 if (assessment_id != '') {
//                     $('#new_assessment_id').val(assessment_id);
//                     $('#new_assessment_id').trigger('change');
//                 }
//                 if (assessment_id != '') {
//                     if (trainer_id != '' || region_id != '' || division_id != '') {
//                         region_division_manager_assessment_wise(trainer_id, region_id, division_id);
//                     }
//                 }
//                 $("#adoption_by_module").html(json['adoption_by_modules']);
//             }
//             $('#responsive-modal-module').modal('hide');
//             customunBlockUI();
//         }
//     });
// }

//  By Bhautik Rana 05-01-2022 adoption Comments changes 
function adoption_by_region(IsCustom) {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#am_id").val();
    var RegoionId = $("#region_id").val();
    var ManagerId = $("#managerid").val();
    if (ManagerId != null) {
        var count_manager = ManagerId.length;
        if (count_manager >= 100) {
            ShowAlret("Please select maximum 100 Managers .!!", 'error');
            return false;
        }
    }
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,
            region_id: RegoionId,
            st_date: StartDate,
            end_date: EndDate,
            IsCustom: IsCustom,
            manager_id: ManagerId
        },
        //async: false,
        url: base_url + "adoption/adoption_by_region",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                var last_assessment_id = json.last_assessment_id;
                var rg_id = json.region_id;
                if (last_assessment_id != '' && rg_id != '') {
                    $('#am_id').val(last_assessment_id);
                    $('#am_id').trigger('change');
                    Getassessmentregion(rg_id);
                }
                $("#adoption_by_region").html(json['adoption_by_region']);
                $('#responsive-modal-region').modal('hide');
            }
            customunBlockUI();
        }
    });
}
//  By Bhautik Rana 05-01-2022 adoption Comments changes 

// By Bhautik Rana 09-01-2023 for Module graph Changes 
function adoptionbymodule() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }

    var AssessmentId = $("#new_assessment_id").val();
    if (AssessmentId != null) {
        $('#responsive-modal-module').modal('hide');
    }
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,
        },
        //async: false,
        url: base_url + "adoption/adoption_by_module_new",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                var assessment_id = json.assessment_id;
                if (assessment_id != '') {
                    $('#new_assessment_id').val(assessment_id);
                    $('#new_assessment_id').trigger('change');
                }
                $("#adoption_by_module").html(json['adoption_by_modules']);
            }
            $('#responsive-modal-module').modal('hide');
            customunBlockUI();
        }
    });
}

//  By Bhautik Rana 10-01-2023 
function Adoption_by_division_overall(IsCustom) {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var manager_id = $("#manager_id_overall").val();
    var AssessmentId = $("#assessment_id_overall").val();
    var DivisionSet = $("#division_id_overall").val();
    var is_custom_overall = $("#is_custom_overall").val();

    if (AssessmentId != null && DivisionSet == null) {
        ShowAlret("Please select Division.!!", 'error');
        return false;
    }
    if (manager_id != null && DivisionSet == null) {
        ShowAlret("Please select Division.!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,
            DivisionSet: DivisionSet,
            Manager_id: manager_id,
            StartDate: StartDate,
            EndDate: EndDate,
            IsCustom: is_custom_overall
        },
        //async: false,
        url: base_url + "adoption/adoption_by_divison_overall",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                var assessment_id = json.assessment_id;
                if (assessment_id != null) {
                    $('#assessment_id_overall').val(assessment_id);
                    $('#assessment_id_overall').trigger('change');
                }
                var division_set = json.division_id;
                var manager_set = json.manager_id;
                if (division_set != null && manager_set != null) {
                    get_manager_div_overall(division_set, manager_set);
                }
                $("#adoption_by_division_overall").html(json['adoption_by_division_overall']);
            }
            $('#responsive-modal-division-overall').modal('hide');
            customunBlockUI();
        }
    });
}
//  By Bhautik Rana 10-01-2023 
// Adoption by team (overall) "01-09-2023" function start here
function check_manager_id() {
    var c_m_managers = $("#c_m_managers").val();
    if (c_m_managers == null) {
        ShowAlret("Please select Managers .!!", 'error');
        return false;
    } else {
        adoption_by_manager();
    }
}

Getassessment_wise_d_r_m();
$('#c_m_am_id').change(function () {
    Getassessment_wise_d_r_m();
});

function Getassessment_wise_d_r_m() {
    var AssessmentId = $('#c_m_am_id').val();
    if (Company_id == "") {
        return false;
    }

    $.ajax({
        type: "POST",
        data: {
            company_id: Company_id,
            assessmentid: AssessmentId
        },
        //async: false,
        url: base_url + "adoption/Getassessment_wise_d_r_m",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                $('#c_m_managers').empty();
                $('#c_m_managers').append(Oresult['cm_managers']);
            }
            customunBlockUI();
        }
    });
}
// Adoption by team (overall) "09-01-2023"  and Adoption by region (overall) "10-01-2023"  start here "Nirmal Gajjar"
function adoption_by_manager() {
    var assessment_id = $('#c_m_am_id').val();
    var cm_managers = $('#c_m_managers').val();

    if (Company_id == "") {
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            company_id: Company_id,
            assessmentid: assessment_id,
            manager_id: cm_managers,
            StartDate: StartDate,
            EndDate: EndDate,
            iscustom: $('#c_m_iscustom').val()
        },
        //async: false,
        url: base_url + "adoption/adoption_by_manager",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            $('#responsive_modal_manager').modal('hide');
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#a_b_managers").html(json['a_b_managers']);
            }
            customunBlockUI();
        }
    });
}

function ad_by_region() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#as_id").val();
    var reg_id = $("#ab_reg_id").val();
    var manager_id = $("#man_rg_id").val();
    var ad_by_IsCustom = $('#is_custom_by_reg').val();
    if (AssessmentId != null && reg_id == null) {
        ShowAlret("Please select Region .!!", 'error');
        return false;
    }
    if (manager_id != null && reg_id == null) {
        ShowAlret("Please select Region .!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,
            reg_id: reg_id,
            manager_id: manager_id,
            StartDate: StartDate,
            EndDate: EndDate,
            ad_by_IsCustom: ad_by_IsCustom
        },
        //async: false,
        url: base_url + "adoption/ad_by_region",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (MSG) {
            $('#adoption_by_region_model').modal('hide');

            if (MSG != '') {
                var gd = jQuery.parseJSON(MSG);
                var amt_id = gd.amt_id;
                var r_id = gd.rg_id;
                if (amt_id != '' && r_id != '') {
                    $('#as_id').val(amt_id);
                    $('#as_id').trigger('change');
                    load_ad_by_region_filter(r_id)
                }
                $("#ad_by_region_overall").html(gd['adption_by_region']);
            }
            customunBlockUI();
        }
    });
}
load_ad_by_region_filter();
$("#as_id").change(function () {
    load_ad_by_region_filter();
});

function load_ad_by_region_filter(r_id) {
    var AssessmentId = $('#as_id').val();
    if (Company_id == "") {
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            company_id: Company_id,
            assessmentid: AssessmentId
        },
        //async: false,
        url: base_url + "adoption/adoption_by_region_filters",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                $('#ab_reg_id').empty();
                $('#ab_reg_id').append(Oresult['a_b_region']);
                $('#man_rg_id').empty();
                $('#man_rg_id').append(Oresult['a_b_manager']);
                if (r_id != '') {
                    $('#ab_reg_id').val(r_id);
                    $('#ab_reg_id').trigger('change');
                }
            }
            customunBlockUI();
        }
    });
}
 // Adoption by team (overall) "09-01-2023"  and Adoption by region (overall) "10-01-2023"  end here "Nirmal Gajjar"
