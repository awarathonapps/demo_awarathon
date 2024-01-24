$('.select2,.select2-multiple').on('change', function () {
    $(this).valid();
});
function AssessmentChange() {
    if (Totalqstn > 1) {
        TotalqstnArray = [];
        $("#VQADatatable tbody tr").remove();
    }
}
function on_questionchange(key) {
    $("#parameter_id" + key).select2("val", "");
}
function getCheckCount() {
    var x = 0;
    for (var i = 0; i < ParticipantForm.elements.length; i++)
    {
        if (ParticipantForm.elements[i].checked == true)
        {
            x++;
        }
    }
    return x;
}
function getCheckCnt() {
    var x = 0;
    for (var i = 0; i < MappingForm.elements.length; i++)
    {
        if (MappingForm.elements[i].checked == true)
        {
            x++;
        }
    }
    return x;
}
function getCheckCt() {
    var x = 0;
    for (var i = 0; i < MappingSuperForm.elements.length; i++)
    {
        if (MappingSuperForm.elements[i].checked == true)
        {
            x++;
        }
    }
    return x;
}
function getCheckUCnt() {
    var x = 0;
    for (var i = 0; i < UserMappingForm.elements.length; i++)
    {
        if (UserMappingForm.elements[i].checked == true)
        {
            x++;
        }
    }
    return x;
}
function AssessmentUsersRefresh(assessment_type = 1) {
    var table = $('#AssessmentUsersTable');
    var columns = [
        {'width': '40px', 'orderable': true, 'searchable': true, 'targets': [0]},
        {'width': '100px', 'orderable': true, 'searchable': true, 'targets': [1]},
        {'width': '90px', 'orderable': false, 'searchable': true, 'targets': [2]},
        {'width': '70px', 'orderable': false, 'searchable': true, 'targets': [3]},
        {'width': '80px', 'orderable': false, 'searchable': true, 'targets': [4]},
        {'width': '80px', 'orderable': false, 'searchable': false, 'targets': [5]},
        // {'width': '80px', 'orderable': false, 'searchable': false, 'targets': [6]},      DARSHIL COMMENTED
        // {'width': '50px', 'orderable': false, 'searchable': false, 'targets': [7]},      DARSHIL COMMENTED
    ]
    // if(assessment_type == 2){   //Spotlight - Add extra column
    //     new_column = {'width': '50px', 'orderable': false, 'searchable': false, 'targets': [8]};
    //     columns.push(new_column);
    // }        DARSHIL COMMENTED
    oTable = table.dataTable({
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
                "previous": "Prev",
                "next": "Next",
                "last": "Last",
                "first": "First"
            }
        },
        "bStateSave": false,
        "lengthMenu": [
            [5, 10, 15, 20, -1],
            [5, 10, 15, 20, "All"]
        ],
        "pageLength": 10,
        "pagingType": "bootstrap_full_number",
        "columnDefs": columns,
        "order": [
            [0, "desc"]
        ],
        "processing": true,
        "serverSide": true,
        // "sAjaxSource": Base_url + "assessment/AssessmentUsers/" + Encode_id + '/' + 2+'/'+view_type,     DARSHIL COMMENTED
        "sAjaxSource": Base_url + "view_trinity/AssessmentUsers/" + Encode_id + '/' + 2+'/'+view_type,
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
            aoData.push({name: 'fttrainer_id', value: $('#fttrainer_id').val()});
            aoData.push({name: 'ftroute_trainer_id', value: $('#ftroute_trainer_id').val()});

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
function getquestion_type(){
	if($('#question_type').val()==1){
		$('#label_dyamic').text('Question');
	}else{
		$('#label_dyamic').text('Situation');
	}
	AssessmentChange();
}