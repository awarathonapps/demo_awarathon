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
                $('#responsive-modal').modal('show');   
            }
            customunBlockUI();
        },
        error: function(e){
            customunBlockUI();
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
