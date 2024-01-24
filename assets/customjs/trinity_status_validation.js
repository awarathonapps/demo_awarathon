function Save_rating(){
    var q_id =$('#question_id').val();
        $.ajax({
            // url: Base_url+"assessment/save_rating/0",        DARSHIL COMMENTED
            url: Base_url+"view_trinity/save_rating/0",
            type: 'POST',
            data: $('#VideoPitchingForm').serializeArray(),
            beforeSend: function () {
                customBlockUI();
            },
            success: function (Odata) {
                var Data = $.parseJSON(Odata); 
                if(Data['success']){
                    ShowAlret(Data['Msg'],'success');
                    $("#team_rating").html(Data['team_rating']);
                    $("#your_rating").html(Data['your_rating']);
                    $("#cross_tick"+q_id).html(Data['cross_tick']); 
                }                              
                customunBlockUI();
            }
        });
    }
function Save_final_rating(){
        $.confirm({
            title: 'Confirm!',
            content: " Are you sure you want to Submit ? ",
            buttons: {
                confirm:{
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function(){
                    $.ajax({
                        // url: Base_url+"assessment/save_rating/1",        DARSHIL COMMENTED
                        url: Base_url+"view_trinity/save_rating/1",
                        type: 'POST',
                        data: $('#VideoPitchingForm').serializeArray(),
                        beforeSend: function () {
                            customBlockUI();
                        },
                        success: function (Odata) {
                            var Data = $.parseJSON(Odata); 
                            if(Data['success']){
                                ShowAlret(Data['Msg'],'success');
                                $("#team_rating").html(Data['team_rating']);
                                $("#your_rating").html(Data['your_rating']);  
                                   $('.sh-btn').hide();
                                   location.reload();
                            }else{
                                ShowAlret(Data['Msg'],'error');
                            }                              
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
function Retake_confirm(){
        $.confirm({
            title: 'Confirm!',
            content: " Are you sure you want to Retake Assessment ? ",
            buttons: {
                confirm:{
                text: 'Confirm',
                btnClass: 'btn-orange',
                keys: ['enter', 'shift'],
                action: function(){
                    $.ajax({
                        type: "POST",
                        // url: Base_url+"assessment/save_retake",      DARSHIL COMMENTED
                        url: Base_url+"view_trinity/save_retake",
                        data: {company_id:$('#company_id').val(),assessment_id :$('#assessment_id').val(),assessment_type :$('#assessment_type').val(),user_id :$('#user_id').val()},
                        success: function (Odata) {
                            var Data = $.parseJSON(Odata); 
                            if(Data['success']){
                                ShowAlret(Data['Msg'],'success');
				AssessmentUsersRefresh();
                            }                              
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
    function save_question_remark(){
        $.ajax({
            // url: Base_url+"assessment/save_question_remark",     DARSHIL COMMENTED
            url: Base_url+"view_trinity/save_question_remark",
            type: 'POST',
            data: $('#VideoPitchingForm').serializeArray(),
            beforeSend: function () {
                //customBlockUI();
            },
            success: function (Odata) {
//                var Data = $.parseJSON(Odata);                                              
//                customunBlockUI();
            }
        });
    }
function getparameter(Q_id,srno,q_cnt){
    $.ajax({
        // url: Base_url+"assessment/getquestionwiseparameter/" + Q_id+"/"+srno,        DARSHIL COMMENTED
        url: Base_url+"view_trinity/getquestionwiseparameter/" + Q_id+"/"+srno,
        type: 'POST',
        data: $('#VideoPitchingForm').serializeArray(),
        beforeSend: function () {
            customBlockUI();
        },
        success: function (Odata) {
            var Data = $.parseJSON(Odata);               
            $('#selectedquestion').html(Data['Question']);
            $('#parameter_table_div').html(Data['QParameter_table']);     
            $("#question_id").val(Q_id);
            $('#remark_que').val(Data['question_comments']);
            
            if(Data['assessor_guide']!=''){
                $("#assessor_guide").val(Data['assessor_guide']);
                $('.sh_guide').show();
            }else{
                $('.sh_guide').hide();
            }
			if(srno==q_cnt){
                $('.sh-btn').show();
            }else{
                $('.sh-btn').hide();
            }
            customunBlockUI();
        }
    });
}
function get_star(starid) {
        if(starid !=''){
        $('#' + starid).parent().find("label").css({"background-color": "#D8D8D8"});
        $('#' + starid).css({"background-color": "#eb3a12"});
        $('#' + starid).nextAll().css({"background-color": "#eb3a12"});
        }

    }
function CandidateDatatableRefresh(assessment_id) {
//  if (!jQuery().dataTable) {
//      return;
//  }
    var table = $('#CandidateFilterTable');
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
            {'width': '15px','orderable': false,'searchable': false,'targets': [0],'className': "text-center"}, 
            {'width': '130px','orderable': false,'searchable': true,'targets': [1],'className': "text-center"}, //KRISHNA -- Added search column for EMP ID
            {'width': '130px','orderable': false,'searchable': true,'targets': [2],'className': "text-center"}, 
            {'width': '130px','orderable': false,'searchable': false,'targets': [3],'className': "text-center"},
            {'width': '140px','orderable': false,'searchable': true,'targets': [4],'className': "text-center"},
            // DARSHIL COMMENTED BELOW COLUMNS
            // {'width': '80px','orderable': false,'searchable': false,'targets': [5],'className': "text-center"},
            // {'width': '80px','orderable': false,'searchable': false,'targets': [6],'className': "text-center"} 
        ],
        "order": [
            [1, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        // "sAjaxSource": Base_url+"assessment/CandidateDatatableRefresh/"+assessment_id,   DARSHIL CHANGED
        "sAjaxSource": Base_url+"view_trinity/CandidateDatatableRefresh/"+assessment_id,
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
function AssessorDatatableRefresh(assessment_id) {        
//      if (!jQuery().dataTable) {
//         return;
//      }        
    oTable=table.dataTable({
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
            {'width': '15px','orderable': false,'searchable': false,'targets': [0],'className': "text-center"}, 
            {'width': '20px','orderable': false,'searchable': true,'targets': [1],'className': "text-center"}, 
            {'width': '60px','orderable': false,'searchable': false,'targets': [2],'className': "text-center"}, 
            {'width': '30px','orderable': false,'searchable': true,'targets': [3],'className': "text-center"},
            {'width': '30px','orderable': false,'searchable': true,'targets': [4],'className': "text-center"}                
        ],
        "order": [
            [1, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        // "sAjaxSource": Base_url+"assessment/AssessorDatatableRefresh/"+assessment_id,        DARSHIL CHANGED
        "sAjaxSource": Base_url+"view_trinity/AssessorDatatableRefresh/"+assessment_id,
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
function AssessorSubDatatableRefresh(assessment_id,trainer_id) {        
//      if (!jQuery().dataTable) {
//         return;
//      }        
    oTable=table.dataTable({
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
            {'width': '15px','orderable': false,'searchable': true,'targets': [0],'className': "text-center"}, 
            {'width': '20px','orderable': false,'searchable': true,'targets': [1],'className': "text-center"}, 
            {'width': '60px','orderable': false,'searchable': false,'targets': [2],'className': "text-center"},
            {'width': '40px','orderable': false,'searchable': false,'targets': [3],'className': "text-center"}
        ],
        "order": [
            [1, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        // "sAjaxSource": Base_url+"assessment/AssessorSubDatatableRefresh/"+assessment_id+'/'+trainer_id,      DARSHIL CHANGED
        "sAjaxSource": Base_url+"view_trinity/AssessorSubDatatableRefresh/"+assessment_id+'/'+trainer_id,
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