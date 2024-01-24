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

function Competency_understanding_graph(){
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    }
    var AssessmentId = $("#process_assessment_id").val();
    if (AssessmentId != "") {
        $('#responsive-modal-accuracy').modal('toggle');
        var myArray = AssessmentId.split(",");
        var assessment_id=myArray[0];
        var Report_Type=myArray[1];
        if (assessment_id == null) {
            ShowAlret("Please select assessment .!!", 'error');
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
        url: base_url + "accuracy/Competency_understanding_graph",
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


// End Here