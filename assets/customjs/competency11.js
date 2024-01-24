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
        if(AssessmentId == null){

            ShowAlret("Please select Modules .!!", 'error');
            return false;
        }
        var lencount = AssessmentId.length;
        if (lencount > "5") {
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
    
    if (AssessmentId != "") {
        $('#responsive-modal-dvision').modal('toggle');
        var myArray = AssessmentId.split(",");
        var assessment_id = myArray[0];
        var Report_Type = myArray[1];
        if (assessment_id == null) {
            ShowAlret("Please select modules .!!", 'error');
            return false;
        }
        if (Dvison_Id == null) {
            ShowAlret("Please select dvisons.!!", 'error');
            return false;
        }
    }

    $.ajax({
        type: "POST",
        data: {
            assessment_id: assessment_id,
            report_type: Report_Type,
            dvisonid_set: Dvison_Id
        },
        //async: false,
        url: base_url + "competency/performance_comparison_by_division",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#performance_comparison_by_division").html(json['performance_comparison_by_division']);
            }
            customunBlockUI();
        }
    });
}

function Competency_understanding_graph() {
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#process_assessment_id").val();
    if (AssessmentId != "") {
        $('#responsive-modal-accuracy').modal('toggle');
        var myArray = AssessmentId.split(",");
        var assessment_id = myArray[0];
        var Report_Type = myArray[1];
        if (assessment_id == null) {
            ShowAlret("Please select modules .!!", 'error');
            return false;
        }
    }

    $.ajax({
        type: "POST",
        data: {
            assessment_id: assessment_id,
            report_type: Report_Type
        },
        //async: false,
        url: base_url + "competency/Competency_understanding_graph",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#competency_understanding_graph").html(json['competency_understanding_graph']);
            }
            customunBlockUI();
        }
    });
}

function performance_region(){
    
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#am_id").val();
    var RegoionId = $("#region_id").val();


    if (AssessmentId != "") {
        $('#responsive-modal-region').modal('toggle');
        var myArray = AssessmentId.split(",");
        var assessment_Id = myArray[0];
        var Report_Type = myArray[1];
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
            assessment_id: assessment_Id,
            region_id:RegoionId,
            report_type:Report_Type
        },
        //async: false,
        url: base_url + "competency/performance_comparison_by_region",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
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
    if (AssessmentId!="") {
        $('#responsive-region-performance').modal('toggle');
        var myArray = AssessmentId.split(",");
        var assessment_Id = myArray[0];
        var report_type = myArray[1];
        if(RegionId == null){
            ShowAlret("Please select Regions .!!", 'error');
            return false;
        }
        var lencount = RegionId.length;
         if(lencount > "5"){
            ShowAlret("Please select Only four Regions .!!", 'error');
            return false;
        }
    }
    $.ajax({
        type: "POST",
        data: {
            assessment_id: assessment_Id,
            report_type:report_type,
            region_id: RegionId
        },
        //async: false,
        url: base_url + "competency/region_wise_performance",
        beforeSend: function() {
            customBlockUI();
        },
        success: function(response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#region_performance").html(json['region_gp']);
            }
            customunBlockUI();
        }
    });
}
function get_top_five_region(){
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }

    $.ajax({
        type: "Post",
        data: {},
        //async: false,
        url: base_url + "competency/get_top_five_region_data",
        beforeSend: function(f) {
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