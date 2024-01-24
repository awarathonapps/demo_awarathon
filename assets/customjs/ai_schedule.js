const dcp_schedule_task_timer  = ms => new Promise(res => setTimeout(res, ms));
const dcp_task_status_timer    = ms => new Promise(res => setTimeout(res, ms));
const dcp_report_status_timer  = ms => new Promise(res => setTimeout(res, ms));
const dcp_import_excel_timer   = ms => new Promise(res => setTimeout(res, ms));
function turnon_reports_flags(){
    var _assessment_id = $("#assessment_id").val(); 
    var _company_id    = $("#company_id").val();
    if (_assessment_id=="" || _company_id==""){
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
            url        : base_url+"/ai_schedule/turnon_reports_flags/",
            data       : form_data,
            beforeSend: function () {
                customBlockUI();
            },
            success: function (Odata) {
                var json = $.parseJSON(Odata); 
                if (json.success=="true"){
                    ShowAlret('The report generation process is started successfully', 'error');
                }else if (json.success=="false"){
                    ShowAlret(json.message, 'error');
                }
                customunBlockUI();
            },
            error: function(e){
                customunBlockUI();
            }
        });
    }
}
function fetch_participants(){
    var _assessment_id = $("#assessment_id").val(); 
    var _company_id    = $("#company_id").val(); 
    if (_assessment_id=="" || _company_id==""){
        $('#participants_table').html("");
        ShowAlret("Please select assessment", 'error');
    }else{
        var form_data = new FormData();
        form_data.append('assessment_id', _assessment_id);

        $.ajax({
            cache      : false,
            contentType: false,
            processData: false,
            type       : 'POST',
            url        : base_url+"/ai_schedule/fetch_participants/",
            data       : form_data,
            beforeSend: function () {
                customBlockUI();
            },
            success: function (Odata) {
                var json = $.parseJSON(Odata); 
                if (json.success=="true"){
                    $('#participants_table').html(json['html']);
                    json_participants = json['_participants_result'];
                    if (json['_cronjob_result']==1 || json['_cronjob_result']=="1"){
                        // document.getElementById("btn_run_schedule_new").disabled = true;
                        $("#assessment_id").prop("disabled", true);
                        setTimeout(function () {
                            schedule_task();
                            task_status();
                            report_status();
                            import_excel();
                            check_schedule_completed(_company_id,_assessment_id);
                        },1000);
                    }else{
                        // document.getElementById("btn_run_schedule_new").disabled = false;
                        $("#assessment_id").prop("disabled", false);
                    }
                }else if (json.success=="false" && json.message=='CRONJOB_SCHEDULED'){
                    ShowAlret('One assessment is already scheduled. you can schedule only one assessment at a time.', 'error');
                    // document.getElementById("btn_run_schedule_new").disabled = false;
                    $("#assessment_id").prop("disabled", false);
                }
                customunBlockUI();
            },
            error: function(e){
                customunBlockUI();
            }
        });
    }
}

function run_schedule(mode) {
    $.confirm({
        title: 'Confirm!',
        content: "Do you want to schedule this assessment? ",
        buttons: {
            confirm:{
            text: 'Confirm',
            btnClass: 'btn-orange',
            keys: ['enter', 'shift'],
            action: function(){
                schedule_clean();
                var _company_id = $("#company_id").val(); 
                var _assessment_id = $("#assessment_id").val(); 
                if (_company_id=="" || _assessment_id==""){
                    ShowAlret("Please select assessment", 'error');
                }else{
                    // document.getElementById("btn_run_schedule_new").disabled = true;
                    $("#assessment_id").prop("disabled", true);
                    
                    $.ajax({
                        url           : base_url+"/ai_schedule/schedule_process/",
                        data          : {
                            'company_id'     : _company_id, 
                            'assessment_id'  : _assessment_id
                        },
                        type          : 'POST',
                        dataType      : 'json',
                        beforeSend: function () {
                            customBlockUI();
                        },
                        success: function (json) {
                            customunBlockUI();
                            if (json.success=="true"){
                                if (mode=='new'){
                                    setTimeout(function () {
                                        schedule_task();
                                        task_status();
                                        report_status();
                                        import_excel();
                                        check_schedule_completed(_company_id,_assessment_id);
                                    },1000);
                                }
                                if (mode=='pending'){
                                    setTimeout(function () {
                                        task_status();
                                        report_status();
                                        import_excel();
                                        check_schedule_completed(_company_id,_assessment_id);
                                    },1000);
                                }
                            }else if (json.success=="false" && json.message=='CRONJOB_SCHEDULED'){
                                ShowAlret('One assessment is already scheduled. you can schedule only one assessment at a time.', 'error');
                                // document.getElementById("btn_run_schedule_new").disabled = false;
                                $("#assessment_id").prop("disabled", false);
                                customunBlockUI();
                            }
                        },
                        error: function(e){
                            // document.getElementById("btn_run_schedule_new").disabled = false;
                            $("#assessment_id").prop("disabled", false);
                            customunBlockUI();
                        }
                    });
                }
            }
        },
        cancel: function () {
             this.onClose();
        }
        }
    });
}
function schedule_clean(){
    $.each(json_participants, function(index, data) {
        var uid                   = data.uid;
        var schedule_icon_element = '#schedule-icon-'+uid;
        var schedule_element      = '#schedule-'+uid;
        var status_icon_element   = '#status-icon-'+uid;
        var status_element        = '#status-'+uid;
        var report_icon_element   = '#report-icon-'+uid;
        var report_element        = '#report-'+uid;
        var import_icon_element   = '#import-icon-'+uid;
        var import_element        = '#import-'+uid;

        $(schedule_icon_element).html('');
        $(schedule_element).val("");
        $(status_icon_element).html("");
        $(status_element).val("");
        $(report_icon_element).html("");
        $(report_element).val("");
        $(import_icon_element).html("");
        $(import_element).val("");
    });
}
function single_schedule_task(){
    var total_video = Object.keys(json_participants).length;
    for (var i = 0; i < total_video; i++) {
        var data             = json_participants[i];
        if (Object.keys(data).length>0){
            var company_id      = data.company_id;
            var assessment_id   = data.assessment_id;
            var user_id         = data.user_id;
            var trans_id        = data.trans_id;
            var question_id     = data.question_id;
            var portal_name     = data.portal_name;
            var assessment      = data.assessment;
            var user_name       = data.user_name;
            var question_series = data.question_series;
            var uid             = data.uid;
            var schedule_elementi = '#schedule-'+uid;

            if ($(schedule_elementi).val()=="" || $(schedule_elementi).val()==0 || $(schedule_elementi).val()=="0"){
                $.ajax({
                    url           : base_url+"/ai_schedule/schedule_task/",
                    data          : {
                        'company_id'     : company_id, 
                        'assessment_id'  : assessment_id, 
                        'user_id'        : user_id, 
                        'trans_id'       : trans_id, 
                        'question_id'    : question_id, 
                        'portal_name'    : portal_name,
                        'assessment_name': assessment,
                        'user_name'      : user_name, 
                        'question_series': question_series,
                        'uid'            : uid
                    },
                    type          : 'POST',
                    dataType      : 'json',
                    beforeSend: function () {
                    },
                    success: function (json) {
                        var icon_element = '#schedule-icon-'+uid;
                        var schedule_element = '#schedule-'+uid;
                        
                        if (json.success=="true"){
                            var timestamp = new Date().getTime();
                            $(icon_element).html('<img src="'+base_url+'/assets/images/yes.png?t=' + timestamp +'" style="height:16px;width:16px;" />');
                            $(schedule_element).val(1);
                        }else if (json.success=="false"){
                            var timestamp = new Date().getTime();
                            $(icon_element).html('<img src="'+base_url+'/assets/images/no.png?t=' + timestamp +'" style="height:18px;width:auto;"/>');
                            $(schedule_element).val("");
                        }else {
                            var timestamp = new Date().getTime();
                            $(icon_element).html('<img src="'+base_url+'/assets/images/working.png?t=' + timestamp +'" style="height:20px;width:20px;"/>');
                            $(schedule_element).val("");
                        }
                    },
                    error: function(e){
                    }
                });
            }
        }
    }
}
async function schedule_task(){
    var schedule_taskjob_timer = setInterval(async function () {
        var total_video = Object.keys(json_participants).length;
        for (var i = 0; i < total_video; i++) {
            var data             = json_participants[i];
            if (Object.keys(data).length>0){
                var company_id      = data.company_id;
                var assessment_id   = data.assessment_id;
                var user_id         = data.user_id;
                var trans_id        = data.trans_id;
                var question_id     = data.question_id;
                var portal_name     = data.portal_name;
                var assessment      = data.assessment;
                var user_name       = data.user_name;
                var question_series = data.question_series;
                var uid             = data.uid;
                var schedule_elementi = '#schedule-'+uid;

                if ($(schedule_elementi).val()=="" || $(schedule_elementi).val()==0 || $(schedule_elementi).val()=="0"){
                    $.ajax({
                        url           : base_url+"/ai_schedule/schedule_task/",
                        data          : {
                            'company_id'     : company_id, 
                            'assessment_id'  : assessment_id, 
                            'user_id'        : user_id, 
                            'trans_id'       : trans_id, 
                            'question_id'    : question_id, 
                            'portal_name'    : portal_name,
                            'assessment_name': assessment,
                            'user_name'      : user_name, 
                            'question_series': question_series,
                            'uid'            : uid
                        },
                        type          : 'POST',
                        dataType      : 'json',
                        beforeSend: function () {
                        },
                        success: function (json) {
                            var icon_element = '#schedule-icon-'+uid;
                            var schedule_element = '#schedule-'+uid;
                            if (json.success=="true"){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/yes.png?t=' + timestamp +'" style="height:16px;width:16px;" />');
                                $(schedule_element).val(1);
                            }else if (json.success=="false"){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/no.png?t=' + timestamp +'" style="height:18px;width:auto;"/>');
                                $(schedule_element).val("");
                            }else {
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/working.png?t=' + timestamp +'" style="height:20px;width:20px;"/>');
                                $(schedule_element).val("");
                            }
                        },
                        error: function(e){
                        }
                    });
                }
            }
            await dcp_schedule_task_timer(300);
        }
    },60000);
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
                        url           : base_url+"/ai_schedule/task_status/",
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
    if (($(status_element).val() !== "") && ($(status_element).val()!==0 || $(status_element).val()!=="0")){
        $.ajax({
            url           : base_url+"/ai_schedule/task_error_log/",
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
function reschedule_single_task(uid,company_id,assessment_id,user_id,trans_id,question_id,portal_name,assessment,user_name,question_series){
    $.confirm({
        title: 'Confirm!',
        content: "Do you want to re-schedule this single task?",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        url           : base_url+"/ai_schedule/pending_task/",
                        data          : {
                            'company_id'     : company_id, 
                            'assessment_id'  : assessment_id, 
                            'user_id'        : user_id, 
                            'trans_id'       : trans_id, 
                            'question_id'    : question_id, 
                            'portal_name'    : portal_name,
                            'assessment_name': assessment,
                            'user_name'      : user_name, 
                            'question_series': question_series,
                            'uid'            : uid
                        },
                        type          : 'POST',
                        dataType      : 'json',
                        beforeSend: function () {
                        },
                        success: function (json) {
                            var schedule_icon_element = '#schedule-icon-'+uid;
                            var schedule_element      = '#schedule-'+uid;
                            var status_icon_element   = '#status-icon-'+uid;
                            var status_element        = '#status-'+uid;
                            var report_icon_element   = '#report-icon-'+uid;
                            var report_element        = '#report-'+uid;
                            var import_icon_element   = '#import-icon-'+uid;
                            var import_element        = '#import-'+uid;
                
                            $(schedule_icon_element).html('');
                            $(schedule_element).val("");
                            $(status_icon_element).html("");
                            $(status_element).val("");
                            $(report_icon_element).html("");
                            $(report_element).val("");
                            $(import_icon_element).html("");
                            $(import_element).val("");
                            
                            var icon_element = '#schedule-icon-'+uid;
                            if (json.success=="true"){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/yes.png?t=' + timestamp +'" style="height:16px;width:16px;" />');
                                $(schedule_element).val(1);
                            }else if (json.success=="false"){
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/no.png?t=' + timestamp +'" style="height:18px;width:auto;"/>');
                                $(schedule_element).val("");
                            }else {
                                var timestamp = new Date().getTime();
                                $(icon_element).html('<img src="'+base_url+'/assets/images/no.png?t=' + timestamp +'" style="height:18px;width:auto;"/>');
                                $(schedule_element).val("");
                            }
                        },
                        error: function(e){
                        }
                    });
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}
function delete_single_task(uid,company_id,assessment_id,user_id,trans_id,question_id){
    $.confirm({
        title: 'Confirm!',
        content: "Are you sure you want to delete this single task ? ",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    $.ajax({
                        url           : base_url+"/ai_schedule/delete_single_task/",
                        data          : {
                            'company_id'     : company_id, 
                            'assessment_id'  : assessment_id, 
                            'user_id'        : user_id, 
                            'trans_id'       : trans_id, 
                            'question_id'    : question_id, 
                            'uid'            : uid
                        },
                        type          : 'POST',
                        dataType      : 'json',
                        beforeSend: function () {
                            customBlockUI();
                        },
                        success: function (json) {
                            customunBlockUI();
                            if (json.success=="true"){
                                var schedule_icon_element = '#schedule-icon-'+uid;
                                var schedule_element      = '#schedule-'+uid;
                                var status_icon_element   = '#status-icon-'+uid;
                                var status_element        = '#status-'+uid;
                                var report_icon_element   = '#report-icon-'+uid;
                                var report_element        = '#report-'+uid;
                                var import_icon_element   = '#import-icon-'+uid;
                                var import_element        = '#import-'+uid;
                
                                $(schedule_icon_element).html('');
                                $(schedule_element).val("");
                                $(status_icon_element).html("");
                                $(status_element).val("");
                                $(report_icon_element).html("");
                                $(report_element).val("");
                                $(import_icon_element).html("");
                                $(import_element).val("");
                            }
                            if (json.success=="false"){
                                ShowAlret(json.message, 'error');
                            }
                        },
                        error: function(e){
                            customunBlockUI();
                        }
                    });                
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });
}
function delete_all_task(){
    $.confirm({
        title: 'Confirm!',
        content: "Are you sure you want to delete all scheduled task ? ",
        buttons: {
            confirm: {
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function () {
                    customBlockUI();
                    var totrec = Object.keys(json_participants).length;
                    var cnt = 0;
                    $.each(json_participants, function(index, data) {
                        var company_id      = data.company_id;
                        var assessment_id   = data.assessment_id;
                        var user_id         = data.user_id;
                        var trans_id        = data.trans_id;
                        var question_id     = data.question_id;
                        var uid             = data.uid;
                        
                        $.ajax({
                            url           : base_url+"/ai_schedule/delete_single_task/",
                            data          : {
                                'company_id'     : company_id, 
                                'assessment_id'  : assessment_id, 
                                'user_id'        : user_id, 
                                'trans_id'       : trans_id, 
                                'question_id'    : question_id, 
                                'uid'            : uid
                            },
                            type          : 'POST',
                            dataType      : 'json',
                            beforeSend: function () {
                                
                            },
                            success: function (json) {
                                if (json.success=="true"){
                                    var schedule_icon_element = '#schedule-icon-'+uid;
                                    var schedule_element      = '#schedule-'+uid;
                                    var status_icon_element   = '#status-icon-'+uid;
                                    var status_element        = '#status-'+uid;
                                    var report_icon_element   = '#report-icon-'+uid;
                                    var report_element        = '#report-'+uid;
                                    var import_icon_element   = '#import-icon-'+uid;
                                    var import_element        = '#import-'+uid;

                                    $(schedule_icon_element).html('');
                                    $(schedule_element).val("");
                                    $(status_icon_element).html("");
                                    $(status_element).val("");
                                    $(report_icon_element).html("");
                                    $(report_element).val("");
                                    $(import_icon_element).html("");
                                    $(import_element).val("");
                                }
                                if (json.success=="false"){
                                    ShowAlret(json.message, 'error');
                                }
                            },
                            error: function(e){
                            }
                        });
                        cnt++;
                        if (cnt >= totrec){
                            customunBlockUI();
                        }
                    }); 
                }
            },
            cancel: function () {
                this.onClose();
            }
        }
    });

    
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
                        url           : base_url+"/ai_schedule/report_status/",
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
                        url           : base_url+"/ai_schedule/import_excel/",
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
            url           : base_url+"/ai_schedule/check_schedule_completed/",
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
                    ShowAlret('AI schedule completed successfully.', 'success');
                    setTimeout(function () {
                        window.location.reload();
                    },1000);
                }
            },
            error: function(e){
            }
        });
    },180000);
}
async function statistics(){
    var _company_id    = $("#company_id").val();
    if (_company_id==""){
    }else{
		run_statistics(_company_id);
        var statistics_timer = setInterval(async function () {
            run_statistics(_company_id);
        },10000);
    }
}
function run_statistics(_company_id){
	$.ajax({
		url           : base_url+"/ai_schedule/fetch_statistics/",
		data          : {
			'company_id' : _company_id, 
			'statistics_start_date': statistics_start_date, 
			'statistics_end_date'  : statistics_end_date,
		},
		type          : 'POST',
		dataType      : 'json',
		beforeSend: function () {
		},
		success: function (json) {
			$('#box_i_statistics').html('0');
			$('#box_vi_statistics').html('0');
			$('#box_ii_statistics').html('0');
			$('#box_iii_statistics').html('0');
			$('#box_iv_statistics').html('0');
			$('#box_v_statistics').html('0');
			
			if (json.success=="true"){
				$('#box_i_statistics').html(json.box_i_statistics);
				$('#box_vi_statistics').html(json.box_vi_statistics);
				$('#box_ii_statistics').html(json.box_ii_statistics);
				$('#box_iii_statistics').html(json.box_iii_statistics);
				$('#box_iv_statistics').html(json.box_iv_statistics);
				$('#box_v_statistics').html(json.box_v_statistics);
			}
		},
		error: function(e){
		}
	});
}

function play_video(response){
    var vimeo_url = "https://player.vimeo.com/video/"+response+"&autoplay=1";
    document.getElementById('dp-video').src = vimeo_url;
    $('#responsive-video-modal').modal('show');   
}
function stop_video(){
    document.getElementById('dp-video').src ="";
}