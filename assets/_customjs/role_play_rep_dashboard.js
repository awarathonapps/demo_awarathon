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
    if($('#user_id').val()=="")
    {
        ShowAlret("Please select Trainee Name.!!", 'error');
        return false;
    }
    $.ajax({
        type: "POST",
        data: {company_id: $('#company_id').val(), report_by: $('#report_by').val(),report_type: $('#report_type').val(),rpt_period:$('#rpt_period').val(),parameter_id:$('#parameter_id').val(),
            month:$('#month').val(),year:$('#year').val(),week:$('#week').val(),StartDate: StartDate, EndDate: EndDate, user_id: $('#user_id').val()},
        //async: false,
        url: base_url + "role_play_rep_dashboard/getdashboardData",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (data) {
            if (data != '') {
                var json = jQuery.parseJSON(data);
                $('#total_assessment').attr('data-value', json['Total_Assessment']);
                $('#total_assessment').counterUp();
                $('#question_answer').attr('data-value', json['question_answer']);
                $('#question_answer').counterUp();
                $('#total_time').attr('data-value', json['Total_Time']);
                $('#total_time').counterUp();
                $('#average_accuracy').attr('data-value', json['Avg_Accuracy']+'%');
                $('#average_accuracy').counterUp();
                $('#highest_accuracy').attr('data-value', json['high_Accuracy']+'%');
                $('#highest_accuracy').counterUp();
                $('#lowest_accuracy').attr('data-value', json['low_Accuracy']+'%');
                $('#lowest_accuracy').counterUp();
                
                if (json['para_top_five_html'] != '') {
                    $('#asmnt-top-five tbody').empty();
                    $('#asmnt-top-five tbody').append(json['para_top_five_html']);
                }
                if (json['para_bottom_five_table'] != '') {
                    $('#asmnt-bottom-five tbody').empty();
                    $('#asmnt-bottom-five tbody').append(json['para_bottom_five_table']);
                }
                $('#parameter_id').empty();
                $('#parameter_id').append(json['parahtml']);
                
                $("#assessment_index").html(json['index_graph']);
                $("#parameter_index").html(json['index_paragraph']);
                customunBlockUI();
            }
        }
    });
}
function getWeek(){
    $.ajax({
        type: "POST",
        data: {year: $('#year').val(),month: $('#month').val()},
        async: false,
        url: base_url +"role_play_rep_dashboard/ajax_getWeeks",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {                        
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);                                                        
                var WStartEndDate = Oresult['WStartEnd'];                            
                var week_option = '<option value="">All Week</option>';                            
                    for (var i = 0; i < WStartEndDate.length; i++) {
                        week_option += '<option value="' + WStartEndDate[i] + '">' +'Week-'+ (i+1) + '</option>';
                    }                             
                $('#week').empty();
                $('#week').append(week_option);
            }
        customunBlockUI();    
        }
    });                               
}
function assessment_index_refresh(){
    if ($('#company_id').val() == "") {
        ShowAlret("Please select Company.!!", 'error');
        return false;
    } 
    $.ajax({
        type: "POST",
         data: {company_id: $('#company_id').val(), report_by: $('#report_by').val(), rpt_period:$('#rpt_period').val(),parameter_id:$('#parameter_id').val(),
			month:$('#month').val(),year:$('#year').val(),week:$('#week').val(),StartDate: StartDate, EndDate: EndDate, user_id: $('#user_id').val()},
        //async: false,
        url: base_url +"role_play_rep_dashboard/load_assessment_index",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#assessment_index").html(json['index_graph']);
				if($('#parameter_id').val() !=''){
					$("#parameter_index").html(json['index_paragraph']);
				}
            }
            customunBlockUI();
        }
    });
}
function parameter_index_refresh(){
    $.ajax({
        type: "POST",
         data: {report_by: $('#report_by').val(), rpt_period:$('#rpt_period').val(),parameter_id:$('#parameter_id').val(), report_type: $('#report_type').val(),
             month:$('#month').val(),year:$('#year').val(),week:$('#week').val(),StartDate: StartDate, EndDate: EndDate, user_id: $('#user_id').val()},
        //async: false,
        url: base_url +"role_play_rep_dashboard/load_parameter_index",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (response) {
            if (response != '') {
                var json = jQuery.parseJSON(response);
                $("#parameter_index").html(json['index_paragraph']);
            }
            customunBlockUI();
        }
    });
}
       