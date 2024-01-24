function fetch_participants(){
    var _assessment_id = $("#assessment_id").val(); 
    var _company_id    = $("#company_id").val(); 
    if (_assessment_id=="" || _company_id==""){
        $('#participants_table').html("");
        ShowAlret("Please select assessment", 'error');
    }else{
        var form_data = new FormData();
        form_data.append('assessment_id', _assessment_id);
        form_data.append('company_id', _company_id);
		console.log(form_data);
        $.ajax({
            cache      : false,
            contentType: false,
            processData: false,
            type       : 'POST',
            url        : base_url+"/trainee_email_schedule/fetch_participants/",
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