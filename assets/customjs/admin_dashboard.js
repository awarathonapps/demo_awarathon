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
    // console.log( $('#FilterFrm').serialize());
    // $.ajax({
    //     type: "POST",
    // 	//data: $('#FilterFrm').serialize(),
    //     data: {company_id: $('#company_id').val(), report_by: $('#report_by').val(), region_id: $('#region_id').val(),
    //        store_id: $('#store_id').val(),StartDate: StartDate, EndDate: EndDate, supervisor_id: $('#supervisor_id').val(), 
    // 		assessment_id1: $('#assessment_id1').val(), report_type: $('#report_type').val()},
    //     //async: false,
    //     url: base_url + "admin_dashboard/getdashboardData",
    // beforeSend: function () {
    //     customBlockUI();
    // },
    //     success: function (data) {
    //         if (data != '') {
    //             var json = jQuery.parseJSON(data);
    //             $('#total_assessment').attr('data-value', json['Total_Assessment']);
    //             $('#total_assessment').counterUp();
    //             $('#candidate_count').attr('data-value', json['Candidate_Count']);
    //             $('#candidate_count').counterUp();
    //             $('#average_accuracy').attr('data-value', json['Avg_Accuracy']+'%');
    //             $('#average_accuracy').counterUp();
    //             $('#highest_accuracy').attr('data-value', json['high_Accuracy']+'%');
    //             $('#highest_accuracy').counterUp();
    //             $('#lowest_accuracy').attr('data-value', json['low_Accuracy']+'%');
    //             $('#lowest_accuracy').counterUp();
    //             $('#question_answer').attr('data-value', json['question_answer']);
    //             $('#question_answer').counterUp();

    //             if (json['para_top_five_html'] != '') {
    //                 $('#asmnt-top-five tbody').empty();
    //                 $('#asmnt-top-five tbody').append(json['para_top_five_html']);
    //             }
    //             if (json['para_bottom_five_table'] != '') {
    //                 $('#asmnt-bottom-five tbody').empty();
    //                 $('#asmnt-bottom-five tbody').append(json['para_bottom_five_table']);
    //             }
    //             $('#region_performance').html(json['overall_graph']);
    //             $('#region_data').html(json['region_graph']);
    //             $('#region_table').html(json['regiontable_graph']);
    //             $('#assessment_id').empty();
    //             $('#assessment_id').append(json['Assessmentchtml']);
    //             if(json['region_total'] > 3){
    //                 $('#btn-prev').show();
    //                 $('#btn-naxt').show();
    //             }else{
    //                 $('#btn-prev').hide();
    //                 $('#btn-naxt').hide();
    //             }

    // total_device_user();
    // assessment_wise_count();

    //                 $('#region_id').empty();
    //                $('#region_id').append(json['Regionchtml']);
    customunBlockUI();
    //         }
    //     }
    // });
}

function getWeek() {
    $.ajax({
        type: "POST",
        data: { year: $('#year').val(), month: $('#month').val() },
        async: false,
        url: base_url + "admin_dashboard/ajax_getWeeks",
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
            report_by: $('#report_by').val(), assessment_report: $('#assessment_report').val(), report_type: $('#report_type').val(), assessment_id1: $('#assessment_id1').val(),
            month: $('#month').val(), newyear: $('#newyear').val(), week: $('#week').val(), StartDate: StartDate, EndDate: EndDate, region_id: $('#region_id').val(), 
            store_id: $('#store_id').val(), IsCustom: IsCustom
},
        url: base_url + "admin_dashboard/assessment_started",
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
            report_by: $('#report_by').val(), assessment_report_ended: $('#assessment_report_ended').val(), report_type: $('#report_type').val(), assessment_id1: $('#assessment_id1').val(),
            month: $('#month').val(), countyear: $('#countyear').val(), week: $('#week').val(), StartDate: StartDate, EndDate: EndDate, region_id: $('#region_id').val(), 
            store_id: $('#store_id').val(),IsCustom: IsCustom
        },
        //async: false,
        url: base_url + "admin_dashboard/assessment_complted",
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
            report_by: $('#report_by').val(), map_user: $('#map_user').val(), report_type: $('#report_type').val(), assessment_id1: $('#assessment_id1').val(),
            month: $('#month').val(), newyear: $('#newyear').val(), week: $('#week').val(), StartDate: StartDate, EndDate: EndDate, region_id: $('#region_id').val(),
            store_id: $('#store_id').val(), IsCustom: IsCustom 
        },
        url: base_url + "admin_dashboard/get_raps_mapped_user",
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
            report_by: $('#report_by').val(), report_type: $('#report_type').val(), assessment_id1: $('#assessment_id1').val(),
            month: $('#month').val(), dyear: $('#dyear').val(), week: $('#week').val(), StartDate: StartDate, EndDate: EndDate, 
            region_id: $('#region_id').val(), store_id: $('#store_id').val(),IsCustom: IsCustom 
        },
        //async: false,
        url: base_url + "admin_dashboard/total_users_Ac_In",
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
            report_by: $('#report_by').val(), demo: $('#demo').val(), report_type: $('#report_type').val(), assessment_id1: $('#assessment_id1').val(),
            month: $('#month').val(), newyear: $('#newyear').val(), week: $('#week').val(), StartDate: StartDate, EndDate: EndDate, region_id: $('#region_id').val(),
            store_id: $('#store_id').val(), IsCustom: IsCustom 
        },
        url: base_url + "admin_dashboard/get_raps_played_completed",
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


function total_video_uploded(IsCustom){
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            report_by: $('#report_by').val(), report_type: $('#report_type').val(), assessment_id1: $('#assessment_id1').val(),
            month: $('#month').val(), dyear: $('#dyear').val(), week: $('#week').val(), StartDate: StartDate, EndDate: EndDate, 
            region_id: $('#region_id').val(), store_id: $('#store_id').val(),IsCustom: IsCustom 
        },
        //async: false,
        url: base_url + "admin_dashboard/total_video_uploded",
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

function total_video_processed(IsCustom){
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            report_by: $('#report_by').val(), report_type: $('#report_type').val(), assessment_id1: $('#assessment_id1').val(),
            month: $('#month').val(), dyear: $('#dyear').val(), week: $('#week').val(), StartDate: StartDate, EndDate: EndDate, 
            region_id: $('#region_id').val(), store_id: $('#store_id').val(),IsCustom: IsCustom 
        },
        //async: false,
        url: base_url + "admin_dashboard/total_video_processed",
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


// End Here