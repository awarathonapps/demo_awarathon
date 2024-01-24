//AI Process functions ----------------------------------------------------------------------------------------------------------------------
const dcp_schedule_task_timer  = ms => new Promise(res => setTimeout(res, ms));
const dcp_task_status_timer    = ms => new Promise(res => setTimeout(res, ms));
const dcp_report_status_timer  = ms => new Promise(res => setTimeout(res, ms));
const dcp_import_excel_timer   = ms => new Promise(res => setTimeout(res, ms));
function clearTimer(){
    clearTimeout(dcp_schedule_task_timer);
    clearTimeout(dcp_task_status_timer);
    clearTimeout(dcp_report_status_timer);
    clearTimeout(dcp_import_excel_timer);
    $("#process_assessment_id").prop("disabled", false);
    // $("#btn_clear_process").prop("disabled", true);
}
function fetch_process_participants(){
    var _assessment_id = $("#process_assessment_id").val(); 
    var _company_id    = $("#company_id").val(); 
    if (_assessment_id=="" || _company_id==""){
        $('#process_participants_table').html("");
        ShowAlret("Please select assessment", 'error');
    }else{
        var form_data = new FormData();
        form_data.append('assessment_id', _assessment_id);
        $.ajax({
            cache      : false,
            contentType: false,
            processData: false,
            type       : 'POST',
            url        : base_url+"/ai_reports/fetch_process_participants/",
            data       : form_data,
            beforeSend: function () {
                customBlockUI();
            },
            success: function (Odata) {
                var json = $.parseJSON(Odata); 
                if (json.success=="true"){
                    $('#process_participants_table').html(json['html']);
                    json_participants = json['_participants_result'];
                    $("#aiprocess_tb").dataTable({ 
                        destroy: true, 
                        "language": {
                            "aria": {
                                "sortAscending": ": activate to sort column ascending",
                                "sortDescending": ": activate to sort column descending"
                            },
                            "emptyTable": "No data available in table",
                            "info": "Showing _START_ to _END_ of _TOTAL_ records",
                            "infoEmpty": "No records found",
                            "infoFiltered": "(filtered1 from _MAX_ total records)",
                            "lengthMenu": "Show _MENU_",
                            "search": "Search:",
                            "zeroRecords": "No matching records found",
                            "paginate": {
                                "previous":"Prev",
                                "next": "Next",
                                "last": "Last",
                                "first": "First"
                            }
                        }, 
                        "lengthMenu": [
                            [5,10,15,20, -1],
                            [5,10,15,20, "All"]
                        ],
                        // "searching": true, 
                        "pageLength": 10,
                        // "paging": true,
                        "pageLength": 10,            
                        "pagingType": "bootstrap_full_number",
                        "serverSide": false
                        // "bInfo" : false ,
                        // "bLengthChange" : false
                    });
                    // if (json['_cronjob_result']==1 || json['_cronjob_result']=="1"){
                        // $("#process_assessment_id").prop("disabled", true);
                        // $("#btn_clear_process").prop("disabled", false);
                        setTimeout(function () {
                            task_status();
                            report_status();
                            import_excel();
                            check_schedule_completed(_company_id,_assessment_id);
                        },1000);
                    // }else{
                    //     $("#process_assessment_id").prop("disabled", false);
                    // }
                }else if (json.success=="false" && json.message=='CRONJOB_SCHEDULED'){
                    ShowAlret('One assessment is already scheduled. you can schedule only one assessment at a time.', 'error');
                    $("#process_assessment_id").prop("disabled", false);
                }
                customunBlockUI();
            },
            error: function(e){
                customunBlockUI();
            }
        });
    }
}
async function task_status(){
    var schedule_status_timer = setInterval(async function () {
        var total_video = Object.keys(json_participants).length;
        for (var i = 0; i < total_video; i++) {
            var data             = json_participants[i];
            if (Object.keys(data).length>0){
                var company_id       = data.company_id;
                var assessment_id    = data.assessment_id;
                var user_id          = data.user_id;
                var trans_id         = data.trans_id;
                var question_id      = data.question_id;
                var question_series  = data.question_series;
                var uid              = data.uid;
                var icon_element     = '#status-icon-'+uid;
                var schedule_element = '#schedule-'+uid;
                var status_element   = '#status-'+uid;
                if (($(schedule_element).val()==1 || $(schedule_element).val()=="1") && ($(status_element).val()=="")){
                    $.ajax({
                        url           : base_url+"/ai_reports/task_status/",
                        data          : {
                            'company_id'     : company_id, 
                            'assessment_id'  : assessment_id, 
                            'user_id'        : user_id, 
                            'trans_id'       : trans_id, 
                            'question_id'    : question_id, 
                            'question_series': question_series, 
                            'uid'            : uid
                        },
                        type          : 'POST',
                        dataType      : 'json',
                        beforeSend: function () {
                        },
                        success: function (json) {
                            if (json.success=="true" && json.message=="Completed"){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/yes.png?t=' + timestamp +'" style="height:16px;width:16px;" />');
                                $(status_element).val(1);
                            }else if (json.success=="false" && (json.message=="Active")){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/working.png?t=' + timestamp +'" style="height:16px;width:16px;"/>');
                                $(status_element).val("");
                            }else if (json.success=="false" && (json.message=="Running")){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/editing.png?t=' + timestamp +'" style="height:20px;width:20px;"/>');
                                $(status_element).val("");
                            }else if (json.success=="false" && json.message=="Failed"){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/no.png?t=' + timestamp +'" style="height:18px;width:auto;"/>');
                                $(status_element).val(0);
                            }else if (json.success=="false" && json.message=="Update failed"){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/no.png?t=' + timestamp +'" style="height:18px;width:auto;"/>');
                                $(status_element).val(0);
                            }else {
                                $(icon_element).html("");
                                $(status_element).val("");
                            }
                        },
                        error: function(e){
                        }
                    });
                }
            }
            await dcp_task_status_timer(300);
        }
    },180000);
}
function task_error_log(uid,company_id,assessment_id,user_id,trans_id,question_id){
    var status_element  = '#status-'+uid;
    if (($(status_element).val() !== "") && ($(status_element).val()==0 || $(status_element).val()=="0")){
        $.ajax({
            url           : base_url+"/ai_reports/task_error_log/",
            data          : {
                'company_id'     : company_id, 
                'assessment_id'  : assessment_id, 
                'user_id'        : user_id, 
                'trans_id'       : trans_id, 
                'question_id'    : question_id, 
            },
            type          : 'POST',
            dataType      : 'json',
            beforeSend: function () {
                customBlockUI();
            },
            success: function (json) {
                customunBlockUI();
                $('.modal-title').html('Error Logs').show();
                $('#mdl_error_log').html(json.message);
                $('#responsive-modal').modal('show');
            },
            error: function(e){
                customunBlockUI();
            }
        });
    }else{
        ShowAlret("The error log can be displayed only if the task/video process status failed.", 'error');
    }
}
async function report_status(){
    var schedule_status_timer = setInterval(async function () {
        var total_video = Object.keys(json_participants).length;
        for (var i = 0; i < total_video; i++) {
            var data             = json_participants[i];
            if (Object.keys(data).length>0){
                var company_id      = data.company_id;
                var assessment_id   = data.assessment_id;
                var user_id         = data.user_id;
                var trans_id        = data.trans_id;
                var question_id     = data.question_id;
                var question_series = data.question_series;
                var uid             = data.uid;
                var icon_element    = '#report-icon-'+uid;
                var status_element  = '#status-'+uid;
                var report_element  = '#report-'+uid;

                if (($(status_element).val()==1 || $(status_element).val()=="1") && ($(report_element).val()=="")){
                    $.ajax({
                        url           : base_url+"/ai_reports/report_status/",
                        data          : {
                            'company_id'     : company_id, 
                            'assessment_id'  : assessment_id, 
                            'user_id'        : user_id, 
                            'trans_id'       : trans_id, 
                            'question_id'    : question_id, 
                            'question_series': question_series, 
                            'uid'            : uid
                        },
                        type          : 'POST',
                        dataType      : 'json',
                        beforeSend: function () {
                        },
                        success: function (json) {
                            if (json.success=="true"){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/yes.png?t=' + timestamp +'" style="height:16px;width:16px;" />');
                                $(report_element).val(1);
                            }else if (json.success=="false"){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/no.png?t=' + timestamp +'" style="height:18px;width:auto;"/>');
                                $(report_element).val(0);
                            }else {
                                $(icon_element).html("");
                                $(report_element).val("");
                            }
                        },
                        error: function(e){
                        }
                    });
                }
            }
            await dcp_report_status_timer(300);
        }
    },180000);
}
async function import_excel(){
    var import_excel_timer = setInterval(async function () {
        var total_video = Object.keys(json_participants).length;
        for (var i = 0; i < total_video; i++) {
            var data             = json_participants[i];
            if (Object.keys(data).length>0){
                var company_id      = data.company_id;
                var assessment_id   = data.assessment_id;
                var user_id         = data.user_id;
                var trans_id        = data.trans_id;
                var question_id     = data.question_id;
                var question_series = data.question_series;
                var uid             = data.uid;
                var icon_element    = '#import-icon-'+uid;
                var import_element  = '#import-'+uid;
                var report_element  = '#report-'+uid;
                if (($(report_element).val()==1 || $(report_element).val()=="1") && ($(import_element).val()=="")){
                    $.ajax({
                        url           : base_url+"/ai_reports/import_excel/",
                        data          : {
                            'company_id'     : company_id, 
                            'assessment_id'  : assessment_id, 
                            'user_id'        : user_id, 
                            'trans_id'       : trans_id, 
                            'question_id'    : question_id, 
                            'question_series': question_series, 
                            'uid'            : uid
                        },
                        type          : 'POST',
                        dataType      : 'json',
                        beforeSend: function () {
                        },
                        success: function (json) {
                            if (json.success=="true"){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/yes.png?t=' + timestamp +'" style="height:16px;width:16px;" />');
                                $(import_element).val(1);
                            }else if (json.success=="false" && json.message=="FILE_NOT_FOUND"){
                                $(icon_element).html("-");
                                $(import_element).val("");
                            }else if (json.success=="false"){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/no.png?t=' + timestamp +'" style="height:18px;width:auto;"/>');
                                $(import_element).val(0);
                            }else {
                                $(icon_element).html("");
                                $(import_element).val("");
                            }
                        },
                        error: function(e){
                        }
                    });
                }
            }
            await dcp_import_excel_timer(300);
        }  
    },180000);
}
function check_schedule_completed(company_id,assessment_id){
    var check_schedule_completed_timer = setInterval(function () {
        $.ajax({
            url           : base_url+"/ai_reports/check_schedule_completed/",
            data          : {
                'company_id'     : company_id, 
                'assessment_id'  : assessment_id
            },
            type          : 'POST',
            dataType      : 'json',
            beforeSend: function () {
            },
            success: function (json) {
                if (json.success=="true"){
                    clearTimer();
                }
            },
            error: function(e){
            }
        });
    },180000);
}

//AI Report functions -----------------------------------------------------------------------------------------------------------------------
function fetch_participants(){
    var _assessment_id = $("#assessment_id").val(); 
    var _company_id    = $("#company_id").val(); 
    if (_assessment_id=="" || _company_id==""){
        $('#participants_table').html("");
        ShowAlret("Please select assessment", 'error');
    }else{
        var form_data = new FormData();
        form_data.append('company_id', _company_id);
        form_data.append('assessment_id', _assessment_id);

        $.ajax({
            cache      : false,
            contentType: false,
            processData: false,
            type       : 'POST',
            url        : base_url+"/ai_reports/fetch_participants/",
            data       : form_data,
            beforeSend: function () {
                customBlockUI();
            },
            success: function (Odata) {
                var json = $.parseJSON(Odata); 
                if (json.success=="true"){
                    $('#participants_table').html(json['html']);
                    json_participants = json['_participants_result'];
                    $('#participants_datatable').DataTable({
                        destroy: true,
                        "language": {
                            "aria": {
                                "sortAscending": ": activate to sort column ascending",
                                "sortDescending": ": activate to sort column descending"
                            },
                            "emptyTable": "No data available in table",
                            "info": "Showing _START_ to _END_ of _TOTAL_ records",
                            "infoEmpty": "No records found",
                            "infoFiltered": "(filtered1 from _MAX_ total records)",
                            "lengthMenu": "Show _MENU_",
                            "search": "Search:",
                            "zeroRecords": "No matching records found",
                            "paginate": {
                                "previous":"Prev",
                                "next": "Next",
                                "last": "Last",
                                "first": "First"
                            }
                        }, 
                        "bStateSave": false,
                        "lengthMenu": [
                            [5,10,15,20, -1],
                            [5,10,15,20, "All"]
                        ],
                        "pageLength": 10,            
                        "pagingType": "bootstrap_full_number",
                        "processing": true,
                        //"serverSide": true,
                        "columnDefs": [ 
                            // {'width': '12%','orderable': true,'searchable': true,'targets': [0]}, 
                            // {'width': '20%','orderable': true,'searchable': true,'targets': [1]},
                            // {'width': '15%','orderable': true,'searchable': true,'targets': [2]}, 
                            // {'width': '15%','orderable': true,'searchable': true,'targets': [3]},
                            // {'width': '7%','orderable': false,'searchable': false,'targets': [4]}, 
                            // {'width': '7%','orderable': false,'searchable': false,'targets': [5]}, 
                            // {'width': '7%','orderable': false,'searchable': false,'targets': [6]}, 
                            // {'width': '10%','orderable': false,'searchable': false,'targets': [7]},
                        ],
                    });
                }
                customunBlockUI();
            },
            error: function(e){
                customunBlockUI();
            }
        });
    }
}
function load_questions(company_id,assessment_id,user_id){
    var form_data = new FormData();
    form_data.append('company_id', company_id);
    form_data.append('assessment_id', assessment_id);
    form_data.append('user_id', user_id);
    $.ajax({
        cache      : false,
        contentType: false,
        processData: false,
        type       : 'POST',
        url        : base_url+"/ai_reports/fetch_questions/",
        data       : form_data,
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var json = $.parseJSON(Odata); 
            if (json.success=="true"){
                $('#mdl_questions').html(json['html']);
                $('#responsive-question-modal').modal('show');   
            }
            customunBlockUI();
        },
        error: function(e){
            customunBlockUI();
        }
    });
}
function play_video(response,tab){
    var vimeo_url = "https://player.vimeo.com/video/"+response+"&autoplay=1";
    document.getElementById('dp-video'+tab).src = vimeo_url;
    $('#responsive-video-modal'+tab).modal('show');   
}
function stop_video(tab){
    document.getElementById('dp-video'+tab).src ="";
}
