//Statictics Code 
async function statistics(){
    var _company_id = $("#company_id").val();
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
		url           : base_url+"/ai_dashboard/fetch_statistics/",
		data          : {
			'company_id'           : _company_id, 
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

//Email Schedule Code
function datatable_view() {
	var table = $('#index_table_view');
	table.dataTable({
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
		"columnDefs": [
			{'width': '3%','orderable': true,'searchable': true,'targets': [0]}, 
			{'width': '20%','orderable': true,'searchable': true,'targets': [1]}, 
			{'width': '10%','orderable': true,'searchable': false,'targets': [2]},                         
			{'width': '10%','orderable': true,'searchable': true,'targets': [3]},
			{'width': '10%','orderable': true,'searchable': true,'targets': [4]},
			{'width': '10%','orderable': true,'searchable': false,'targets': [5]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [6]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [7]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [8]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [9]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [10]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [11]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [12]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [13]},
		],
		"order": [
			[0, "desc"]
		],
		"processing": true,
		"serverSide": true,
		"sAjaxSource": 'ai_dashboard/fetch_assessment',
		"fnServerData": function (sSource, aoData, fnCallback) {
			// aoData.push({name: 'assessment_selected', value: $assessment_selected});
			// aoData.push({name: 'assessment_type', value: $('#assessment_type').val()});
			// aoData.push({name: 'question_type', value: $('#question_type').val()});
			$.getJSON(sSource, aoData, function (json) {
				fnCallback(json);
			});
		},
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			return nRow;
		},
		"fnFooterCallback": function (nRow, aData) {
		}
	});
}

function DatatableRefresh_Ideal() {
	var table = $('#index_table_ideal');
	table.dataTable({
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
		"autoWidth": false,
		"pageLength": 10,            
		"pagingType": "bootstrap_full_number",
		"columnDefs": [
			{'width': '5%','orderable': true,'searchable': true,'targets': [0]}, 
			{'width': '20%','orderable': true,'searchable': true,'targets': [1]}, 
			{'width': '15%','orderable': true,'searchable': false,'targets': [2]},                         
			{'width': '10%','orderable': true,'searchable': true,'targets': [3]},
			{'width': '10%','orderable': true,'searchable': true,'targets': [4]},
			{'width': '10%','orderable': true,'searchable': false,'targets': [5]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [6]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [7]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [8]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [9]},
			{'width': '50px','orderable': false,'searchable': false,'targets': [10]},
		],
		"order": [
			[0, "desc"]
		],
		"processing": true,
		"serverSide": true,
		"sAjaxSource": 'ai_dashboard/DatatableRefresh_ideal',
		"fnServerData": function (sSource, aoData, fnCallback) {
			// aoData.push({name: 'filter_status', value: $('#filter_status').val()});
			// aoData.push({name: 'assessment_type', value: $('#assessment_type').val()});
			// aoData.push({name: 'question_type', value: $('#question_type').val()});
			$.getJSON(sSource, aoData, function (json) {
				fnCallback(json);
			});
		},
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			return nRow;
		}
		, "fnFooterCallback": function (nRow, aData) {
		}
	});				
}
	
function DatatableRefresh_send() {
	var table = $('#index_table_send');
	table.dataTable({
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
		"columnDefs": [
			{  'targets': 0,
			   'searchable':false,
			   'orderable':false,
			   'className': 'dt-body-center',
			   'render': function (data, type, full, meta){
				   // return '<input type="checkbox" name="id[]" value="' + data + '">';
				   return data;
				}
			},
			{'width': '3%','orderable': false,'searchable': false,'targets': [0]}, 
			{'width': '5%','orderable': true,'searchable': true,'targets': [1]}, 
			{'width': '20%','orderable': true,'searchable': true,'targets': [2]},                         
			{'width': '10%','orderable': true,'searchable': false,'targets': [3]},
			{'width': '10%','orderable': true,'searchable': true,'targets': [4]},
			{'width': '10%','orderable': true,'searchable': true,'targets': [5]},
			{'width': '7%','orderable': true,'searchable': false,'targets': [6]},
			{'width': '5%','orderable': false,'searchable': false,'targets': [7]},
			{'width': '5%','orderable': false,'searchable': false,'targets': [8]},
			{'width': '5%','orderable': false,'searchable': false,'targets': [9]},
			{'width': '5%','orderable': false,'searchable': false,'targets': [10]},
			{'width': '5%','orderable': false,'searchable': false,'targets': [11]},
			{'width': '5%','orderable': false,'searchable': false,'targets': [12]},
			{'width': '5%','orderable': false,'searchable': false,'targets': [13]},
		],
		"order": [
			[1, "desc"]
		],
		"processing": true,
		"serverSide": true,
		"sAjaxSource": 'ai_dashboard/DatatableRefresh_send',
		"fnServerData": function (sSource, aoData, fnCallback) {
			// aoData.push({name: 'filter_status', value: $('#filter_status').val()});
			// aoData.push({name: 'assessment_type', value: $('#assessment_type').val()});
			// aoData.push({name: 'question_type', value: $('#question_type').val()});
			$.getJSON(sSource, aoData, function (json) {
				fnCallback(json);
			});
		},
		"fnRowCallback": function (nRow, aData, iDisplayIndex) {
			return nRow;
		}
		, "fnFooterCallback": function (nRow, aData) {
		}
	});
}

function confirm_ranking(assessment_id){
	$.confirm({
		title: 'Confirm!',
		content: "Are you sure you want reports with Ranking? (once you confirm it can not to be changed to No Ranks)",
		buttons: {
			confirm:{
			text: 'Confirm',
			btnClass: 'btn-primary',
			keys: ['enter', 'shift'],
			action: function(){
				save_ai_cronreports(4, assessment_id);
			}
		},
		cancel: function () {
				this.onClose();
				$('#rank_id_'+assessment_id).prop('checked',false);
			}
		}
	});
}

function save_ai_cronreports(target_type, assessment_id){
	var target = (target_type == 1) ? 'md' : (target_type == 2 ? 'rp' : (target_type == 3 ? 'pwa' : 'rank'));
	var target_value = '';
	if($("#"+target+"_id_"+assessment_id).prop('checked') == true){
		target_value = 1; //checked
	}else{
		target_value = 0; //not checked
	}
	$.ajax({
		url: base_url+"ai_dashboard/save_ai_cronreports/"+assessment_id,
		type: 'POST',
		data: 'target='+target_type+'&value='+target_value,
		dataType: 'JSON',
		beforeSend: function () {
            customBlockUI();
        },
		success: function (data){
			//console.log(data);
			if (data.success) {
                ShowAlret(data.message, 'success');
            } else {
                ShowAlret(data.message, 'error');
            }
			if(target_type == 4){
				$("#"+target+"_id_"+assessment_id).prop('disabled', true); //disable the rank checkbox once the checkbox enabled
			}
            customunBlockUI();
		},error: function (data){
			console.log(data);
		}
	});
	return false;
}

function CandidateDatatableRefresh(assessment_id,report_type,is_send_tab) {
    var table = $('#CandidateFilterTable');
    table.dataTable({
        destroy: true,
        "language": {
            "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            },
            "emptyTable": "No reports generated for this assessment",
            "info": "Showing _START_ to _END_ of _TOTAL_ records",
            "infoEmpty": "No records found",
            "infoFiltered": "(filtered 1 from _MAX_ total records)",
            "lengthMenu": "Show _MENU_",
            "search": "Search:",
            "zeroRecords": "No reports generated for this assessment",
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
        "columnDefs": [
            // {'width': '','orderable': false,'searchable': false,'targets': [0]}, 
            // {'width': '','orderable': false,'searchable': true,'targets': [1]}, 
            // {'width': '','orderable': false,'searchable': true,'targets': [2]}, 
            // {'width': '','orderable': false,'searchable': true,'targets': [3]},
            // {'width': '','orderable': false,'searchable': false,'targets': [4]},
            // {'width': '','orderable': false,'searchable': false,'targets': [5]},
			// {'width': '','orderable': false,'searchable': false,'targets': [6]},
			// {'width': '','orderable': false,'searchable': false,'targets': [7]},
        ],
        "order": [
            // [1, "desc"]
        ],
        "processing": true,
        // "serverSide": true,
        "sAjaxSource": base_url+"ai_dashboard/CandidateDatatableRefresh/"+assessment_id+"/"+is_send_tab,
        "fnServerData": function (sSource, aoData, fnCallback) {                        
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        }
        , "fnFooterCallback": function (nRow, aData) {
        }
    });
}

function IdealQuestionDatatable(assessment_id) {
    var table = $('#Question_Table');
    table.dataTable({
        destroy: true,
        "language": {
            "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            },
            "emptyTable": "No data available in table",
            "info": "Showing _START_ to _END_ of _TOTAL_ records",
            "infoEmpty": "No records found",
            "infoFiltered": "(filtered 1 from _MAX_ total records)",
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
        "columnDefs": [
			/*{'width': '5%','orderable': true,'searchable': false,'targets': [0]}, 
			{'width': '35%','orderable': true,'searchable': false,'targets': [1]}, 
           	{'width': '30%','orderable': false,'searchable': false,'targets': [2]}, 
        	{'width': '30%','orderable': false,'searchable': false,'targets': [3]},*/
        ],
        "order": [
            [0, "desc"]
        ],
        "processing": true,
        "serverSide": false,
        "sAjaxSource": base_url+"ai_dashboard/QuestionDatatableRefresh/"+assessment_id,
        "fnServerData": function (sSource, aoData, fnCallback) {                        
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json);
            });
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
            return nRow;
        }
        , "fnFooterCallback": function (nRow, aData) {
        },
		"initComplete": function(settings, json) {
			$('thead > tr> th:nth-child(1)').css({ 'min-width': '150px', 'max-width': '150px' });
			$('thead > tr> th:nth-child(2)').css({ 'min-width': '50px', 'max-width': '70px' });
			$('thead > tr> th:nth-child(2)').css({ 'min-width': '50px', 'max-width': '70px' });
		}
    });
}

function fetch_participants(_assessment_id){
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
            url        : base_url+"/ai_dashboard/fetch_participants/",
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
                        $("#assessment_id").prop("disabled", true);
                        // setTimeout(function () {
                        //     schedule_task();
                        //     task_status();
                        //     report_status();
                        //     import_excel();
                        //     check_schedule_completed(_company_id,_assessment_id);
                        // },1000);
                    }else{
                        $("#assessment_id").prop("disabled", false);
                    }
                }else if (json.success=="false" && json.message=='CRONJOB_SCHEDULED'){
                    ShowAlret('One assessment is already scheduled. you can schedule only one assessment at a time.', 'error');
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

function setEmailBody(){
	$.ajax({
            cache      : false,
            contentType: false,
            processData: false,
            type       : 'POST',
            url        : base_url+"/ai_dashboard/getemailbody/",
			dataType   : 'json',
            beforeSend: function () {
                customBlockUI();
            },
			success: function(Odata){
				$('#tab_template').html(Odata.email_content);
				customunBlockUI();
			},
			error: function(e){
				console.log(e);
                customunBlockUI();
            }
	});
}

function scheduleEmail(company_id,assessment_id='',sendAll = 0){
	$.ajax({
		url: base_url+'ai_email_cron/schedule_data/',
		type: 'POST',
		data: 'company_id='+company_id+'&assessment_id='+assessment_id+'&sendAll='+sendAll,
		dataType: 'JSON',
		beforeSend: function () {
            customBlockUI();
        },
		success: function (data){
			if (data.success) {
                ShowAlret(data.message, 'success');
            } else {
                ShowAlret(data.message, 'error');
            }
			DatatableRefresh_send();
            customunBlockUI();
		},error: function (data){
			console.log(data);
		}
	});
}

function scheduleCandidateEmail(company_id,assessment_id,select_candidates){
	$.ajax({
		url: base_url+'ai_email_cron/schedule_data/',
		type: 'POST',
		data: 'company_id='+company_id+'&assessment_id='+assessment_id+'&trainee_id='+select_candidates+'&sendAll=1',
		dataType: 'JSON',
		beforeSend: function () {
            customBlockUI();
        },
		success: function (data){
			if (data.success) {
                ShowAlret(data.message, 'success');
            } else {
                ShowAlret(data.message, 'error');
            }
			//CandidateDatatableRefresh(assessment_id);
            customunBlockUI();
		},error: function (data){
			console.log(data);
		}
	});
}
