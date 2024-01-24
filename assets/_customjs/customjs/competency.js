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
    // console.log( $('#FilterFrm').serialize());
    // $.ajax({
    //     type: "POST",
    // 	//data: $('#FilterFrm').serialize(),
    //     data: {company_id: $('#company_id').val(), report_by: $('#report_by').val(), region_id: $('#region_id').val(),
    //        store_id: $('#store_id').val(),StartDate: StartDate, EndDate: EndDate, supervisor_id: $('#supervisor_id').val(), 
    // 		assessment_id1: $('#assessment_id1').val(), report_type: $('#report_type').val()},
    //     //async: false,
    //     url: base_url + "competency/getdashboardData",
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


    //                 $('#region_id').empty();
    //                $('#region_id').append(json['Regionchtml']);
    // customunBlockUI();
    //         }
    //     }
    // });
}

function getWeek() {
    $.ajax({
        type: "POST",
        data: {
            year: $('#year').val(),
            month: $('#month').val()
        },
        async: false,
        url: base_url + "competency/ajax_getWeeks",
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

function performance_comparison() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#assessments_id").val();
    if (AssessmentId) {
        $('#responsive-modal-comparison').modal('toggle');
        if (AssessmentId == null) {

            ShowAlret("Please select Modules .!!", 'error');
            return false;
        }
        //var lencount = AssessmentId.length;
        // if (lencount > "5") {
        //     ShowAlret("Please select Only Five Modules .!!", 'error');
        //     return false;
        // }
    }

    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId
        },
        //async: false,
        url: base_url + "competency/performance_comparison",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#performance_comparison_graph").html(json['performance_comparison_graph']);
            }
            customunBlockUI();
        }
    });
}



function performance_division() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#assessment_id").val();
    var Dvison_Id = $("#division_id").val();

    // if (AssessmentId != "") {
    //     var myArray = AssessmentId.split(",");
    //     var assessment_id = myArray[0];
    //     var Report_Type = myArray[1];
    // if (assessment_id == null) {
    //     ShowAlret("Please select modules .!!", 'error');
    //     return false;
    // }
    // if (Dvison_Id == null) {
    //     ShowAlret("Please select dvisons.!!", 'error');
    //     return false;
    // }
    // }

    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,
            dvisonid_set: Dvison_Id
        },
        //async: false,
        url: base_url + "competency/performance_comparison_by_division",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            $('#responsive-modal-dvision').modal('hide');
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#performance_comparison_by_division").html(json['performance_comparison_by_division']);
            }
            customunBlockUI();
        }
    });
}
// Competency_by_division
function competency_by_division(IsCustom) {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#new_ass_id").val();
    var Divison_Id = $("#div_id").val();
    var manager_id = $("#man_id").val();
    var IsCustom = $('#is_custom_by_div').val();
   
    if(AssessmentId != null && Divison_Id == null){
        ShowAlret("Please select Division .!!", 'error');
        return false;
    }
    if(manager_id != null && Divison_Id == null){
        ShowAlret("Please select Division .!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,
            divisonid_set: Divison_Id,
            manager_id: manager_id,
            StartDate: StartDate,
            EndDate: EndDate,
            IsCustom:IsCustom
        },
        //async: false,
        url: base_url + "competency/competency_by_division",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            $('#competency_by_division_model').modal('hide');
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#competency_by_division").html(json['competency_graph']);
            }
            customunBlockUI();
        }
    });
}
// Competency_by_division

// By Bhautik Rana (02 jan 2023) - new Graph 
// Competency_by_region
function competency_by_region(IsCustom) {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#ass_gp_id").val();
    var reg_id = $("#reg_gp_id").val();
    var manager_id = $("#man_gp_id").val();
    var IsCustom = $('#is_custom_by_reg').val();

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
            IsCustom: IsCustom
        },
        //async: false,
        url: base_url + "competency/competency_by_region",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#competency_by_region").html(json['competency_graph']);
                $('#competency_by_region_model').modal('hide');
            }
            customunBlockUI();
        }
    });
}
// Competency_by_region

// By Bhautik Rana (02 jan 2023) - new Graph 
function Competency_understanding_graph() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#process_assessment_id").val();
    // if (AssessmentId != "") {
    //     var myArray = AssessmentId.split(",");
    //     var assessment_id = myArray[0];
    //     var Report_Type = myArray[1];
    //     if (assessment_id == null) {
    //         ShowAlret("Please select modules .!!", 'error');
    //         return false;
    //     }
    // }

    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId
        },
        //async: false,
        url: base_url + "competency/Competency_understanding_graph",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            $('#responsive-modal-accuracy').modal('hide');
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#competency_understanding_graph").html(json['competency_understanding_graph']);
            }
            customunBlockUI();
        }
    });
}

function performance_region() {

    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#am_id").val();
    var RegoionId = $("#region_id").val();

    // if (AssessmentId != "") {
    //     if(RegoionId == null){
    //         ShowAlret("Please select Regions .!!", 'error');
    //         return false;
    //     }
    //     var lencount = RegoionId.length;
    //      if(lencount > "5"){
    //         ShowAlret("Please select Only Five Regions .!!", 'error');
    //         return false;
    //     }
    // }
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,
            region_id: RegoionId
        },
        //async: false,
        url: base_url + "competency/performance_comparison_by_region",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            $('#responsive-modal-region').modal('hide');
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#performance_comparison_by_region").html(json['performance_comparison_by_region']);
            }
            customunBlockUI();
        }
    });
}

function region_wise_performance() {

    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#as_id").val();
    var RegionId = $("#rg_id").val();
    $.ajax({
        type: "POST",
        data: {
            assessment_id: AssessmentId,
            region_id: RegionId
        },
        //async: false,
        url: base_url + "competency/region_wise_performance",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            $('#responsive-region-performance').modal('hide');

            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#region_performance").html(json['region_gp']);
            }
            customunBlockUI();
        }
    });
}
function get_top_five_region() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }

    $.ajax({
        type: "Post",
        data: {},
        //async: false,
        url: base_url + "competency/get_top_five_region_data",
        beforeSend: function (f) {
            $('#top_five_region tbody').html('Load Table ...');
            $('#top_five_region tbody').css('text-align', 'right');
            customBlockUI();
        },
        success: function (Odata) {
            if (Odata != '') {
                var jsonParsedArray = JSON.parse(Odata);
                $("#top_five_region tbody").html(jsonParsedArray);
            }
            customunBlockUI();
        }
    });
}
// End Here
// Bottom five region graph start here
function get_bottom_five_region() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    $.ajax({
        type: "Post",
        data: {},
        //async: false,
        url: base_url + "competency/get_bottom_five_region_data",
        beforeSend: function (f) {
            $('#bottom_five_region tbody').html('Load Table ...');
            $('#bottom_five_region tbody').css('text-align', 'right');
            // customBlockUI();
        },
        success: function (Odata) {
            if (Odata != '') {
                var jsonParsedArray = JSON.parse(Odata);
                $("#bottom_five_region tbody").html(jsonParsedArray);
            }
            customunBlockUI();
        }
    });
}
//End Here
// Hitmap Start Here
$('#hitmap_assessment_id').change(function () {
    Getheatmapregion();
});

function Getheatmapregion() {
    var ass_essment_id = $('#hitmap_assessment_id').val();
    if (Company_id == "") {
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            company_id: Company_id,
            ass_essment_id: ass_essment_id
        },
        url: base_url + "competency/heat_wise_region",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                $('#hitmap_region').empty();
                $('#hitmap_region').append(Oresult['heat_region']);
            }
            customunBlockUI();
        }
    });
}
function get_regionwisedata(assessment_id, region_id, report_type) {
    $.ajax({
        url: base_url + "competency/region_scoredata",
        type: 'POST',
        data: { assessment_id: assessment_id, region_id: region_id, report_type: report_type },
        beforeSend: function () {
            customBlockUI();
        },
        success: function (html) {
            $('#modal-body').html(html);
            $('#LoadModalFilter').modal();
            customunBlockUI();
        }
    });
}

// Hitmap End Here
// competency by manager function start here
function check_manager_id() {
    var c_m_managers = $("#c_m_managers").val();
    if (c_m_managers == null) {
       ShowAlret("Please select Managers .!!", 'error');
       return false;
    } else {
       competency_by_manager();
    }
 }
 
 Getassessment_wise_d_r_m();
 $('#c_m_am_id').change(function() {
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
       url:  base_url+"competency/Getassessment_wise_d_r_m",
       beforeSend: function() {
          customBlockUI();
       },
       success: function(msg) {
          if (msg != '') {
             var Oresult = jQuery.parseJSON(msg);
             $('#c_m_managers').empty();
             $('#c_m_managers').append(Oresult['cm_managers']);
          }
          customunBlockUI();
       }
    });
 }

function competency_by_manager() {
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
        url:  base_url+"competency/competency_by_manager",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
        $('#responsive-modal-manager').modal('hide');
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#competency_by_manager").html(json['c_m_managers']);
            }
            customunBlockUI();
        }
    });
}
function get_time_wise_manager(IsCustom){
    
    if (Company_id == "") {
        return false;
    }
    $.ajax({
        type: "POST",
        data: {
            company_id: Company_id,
            StartDate: StartDate,
            EndDate: EndDate,
            iscustom: IsCustom
        },
        //async: false,
        url:  base_url+"competency/time_wise_manager",
        beforeSend: function () {
            customBlockUI();
        },
        success: function(msg) {
            if (msg != '') {
               var Oresult = jQuery.parseJSON(msg);
               $('#c_m_managers').empty();
               $('#c_m_managers').append(Oresult['cm_managers']);
            }
            customunBlockUI();
         }
    });
}
//  End here