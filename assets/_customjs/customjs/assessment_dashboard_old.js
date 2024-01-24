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
    $.ajax({
        type: "POST",
        data: {company_id: $('#company_id').val(), report_by: $('#report_by').val(), region_id: $('#region_id').val(),
            store_id: $('#store_id').val(),StartDate: StartDate, EndDate: EndDate},
        //async: false,
        url: base_url + "assessment_dashboard/getdashboardData",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (data) {
            if (data != '') {
                var json = jQuery.parseJSON(data);
                $('#total_assessment').attr('data-value', json['Total_Assessment']);
                $('#total_assessment').counterUp();
                $('#candidate_count').attr('data-value', json['Candidate_Count']);
                $('#candidate_count').counterUp();
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
                $('#region_performance').html(json['overall_graph']);
                $('#region_data').html(json['region_graph']);
                $('#region_table').html(json['regiontable_graph']);
                $('#assessment_id').empty();
                $('#assessment_id').append(json['Assessmentchtml']);
                if(json['region_total'] > 3){
                    $('#btn-prev').show();
                    $('#btn-naxt').show();
                }else{
                    $('#btn-prev').hide();
                    $('#btn-naxt').hide();
                }
                assessment_index_refresh();
//                 $('#region_id').empty();
//                $('#region_id').append(json['Regionchtml']);
                customunBlockUI();
            }
        }
    });
}
function get_parameterdata(parameter_id, region_id) {

    $.ajax({
        url: base_url + "Assessment_dashboard/parameter_scoredata",
        type: 'POST',
        data: {company_id: Company_id, parameter_id: parameter_id, region_id: region_id,StartDate: StartDate, EndDate: EndDate},
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
function get_regionwisedata(assessment_id, region_id) {

    $.ajax({
        url: base_url + "Assessment_dashboard/region_scoredata",
        type: 'POST',
        data: {assessment_id: assessment_id, region_id: region_id},
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
function get_assessmentwisedata(assessment_id) {
    if (assessment_id == '') {
        return false;
    }
    $.ajax({
        url: base_url + "Assessment_dashboard/assessment_parameter_scoredata",
        type: 'POST',
        data: {assessment_id: assessment_id},
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
function get_regionlevel_graph(region_id) {
    $.ajax({
        url: base_url + "assessment_dashboard/region_level_scoredata",
        type: 'POST',
        data: {region_id: region_id,StartDate: StartDate, EndDate: EndDate},
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
function getDatewiseAssessment() {
    $.ajax({
        type: "POST",
        data: {StartDate: StartDate, EndDate: EndDate},
        //async: false,
        url: base_url + "assessment_dashboard/getDatewiseAssessment",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                $('#assessment_id').empty();
                $('#assessment_id').append(Oresult['Assessmentchtml']);
            }
            customunBlockUI();
        }
    });
}
function getDatewiseRegion() {
    $.ajax({
        type: "POST",
        data: {StartDate: StartDate, EndDate: EndDate},
        //async: false,
        url: base_url + "assessment_dashboard/getDatewiseRegion",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (msg) {
            if (msg != '') {
                var Oresult = jQuery.parseJSON(msg);
                $('#region_id').empty();
                $('#region_id').append(Oresult['Regionchtml']);
            }
            customunBlockUI();
        }
    });
}
function dashboard_region_refresh() {
    $.ajax({
        type: "POST",
        data: $('#frmModalForm').serialize() + "&StartDate=" + StartDate + "&EndDate=" + EndDate +
                "&report_by=" + $('#report_by').val() + "&region_id=" + $('#region_id').val() + "&store_id=" + $('#store_id').val(),
        //async: false,
        url: base_url + "assessment_dashboard/getFilterDashboardData",
        beforeSend: function () {
            customBlockUI();
        },
        success: function (data) {
            if (data != '') {
                var json = jQuery.parseJSON(data);
                $('#filter-modal').modal('toggle');
                $('#region_data').html(json['region_graph']);
                $('#region_table').html(json['regiontable_graph']);

                customunBlockUI();
            }
        }
    });
}
function dashboard_region_change(move_val) {
    if(move_val==1 && step > 3){
            step=step-3;
            $('#btn-prev').show();
        }else if(move_val==2){
            step=step+3;
            $('#btn-prev').show();
        }else{
            step=0;
            $('#btn-prev').hide();
        }
    $.ajax({
        type: "POST",
        data: $('#frmModalForm').serialize() + "&StartDate=" + StartDate + "&EndDate=" + EndDate +
                "&report_by=" + $('#report_by').val() + "&step=" + step + "&region_id=" + $('#region_id').val() + "&store_id=" + $('#store_id').val(),
        //async: false,
        url: base_url + "assessment_dashboard/getRegionChart",
     
        success: function (data) {
            if (data != '') {
                var json = jQuery.parseJSON(data);
                if(step < json['region_total']){
                        $('#btn-naxt').show();
                        $('#region_data').html(json['region_graph']);
                    }else{
                        step=json['region_total']-3;
                        $('#btn-naxt').hide();
                    }      
            }
        }
    });
}
function SubmitData() {
    $.ajax({
        url: base_url + "Assessment_dashboard/update_range",
        type: 'POST',
        data: $("#rangeform").serialize(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);
            if (Data['Success']) {
                ShowAlret(Data['Message'], 'success');
                dashboard_refresh();
            } else {
                ShowAlret(Data['Message'], 'error');
            }
            customunBlockUI();
        }
    });
}
function getWeek(){
    $.ajax({
        type: "POST",
        data: {year: $('#year').val(),month: $('#month').val()},
        async: false,
        url: base_url +"assessment_dashboard/ajax_getWeeks",
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
         data: {report_by: $('#report_by').val(), rpt_period:$('#rpt_period').val(),parameter_id:$('#parameter_id').val(),
             month:$('#month').val(),year:$('#year').val(),week:$('#week').val(),StartDate: StartDate, EndDate: EndDate, region_id: $('#region_id').val(),store_id: $('#store_id').val()},
        //async: false,
        url: base_url +"assessment_dashboard/load_assessment_index",
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
         data: {report_by: $('#report_by').val(), rpt_period:$('#rpt_period').val(),parameter_id:$('#parameter_id').val(),
             month:$('#month').val(),year:$('#year').val(),week:$('#week').val(),StartDate: StartDate, EndDate: EndDate, user_id: $('#user_id').val()},
        //async: false,
        url: base_url +"assessment_dashboard/load_parameter_index",
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