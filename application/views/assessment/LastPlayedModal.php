<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Spotlight Played</h4>
</div>
<div class="modal-body">
    <form name="CandidateForm" id="CandidateForm">
        <div class="portlet light">
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="CandidateFilterTable" width="100%">
                    <thead>
                        <tr>
                            <th>Question Id</th>
                            <th>Question </th>
                            <th>Cosine Score</th>
                            <th>Audio to text</th>
                            <th>Added At </th>
                           
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if(count((array)$Data_list) > 0){
                    foreach($Data_list as $dt){?>
                        <tr>
                            <td><?php echo $dt->question_id; ?> </td>
                            <td><?php echo $dt->question; ?></td>
                            <td><?php echo $dt->cosine_score; ?></td>
                            <td><?php echo $dt->audio_totext; ?></td>
                            <td><?php echo $dt->added_at; ?></td>
                        </tr>

                        <?php 
                    } } ?>
                    </tbody>
                </table>
            </div>
        </div>          
    </form>
</div>
        <script type="text/javascript">
            var frm1=document.CandidateForm;
            jQuery(document).ready(function () {
                LastplayedDatatableRefresh(<?php echo $assessment_id; ?>);   
                function LastplayedDatatableRefresh(assessment_id) {
//  if (!jQuery().dataTable) {
//      return;
//  }
      var assessment_id="<?php echo $assessment_id; ?>";
      var user_id="<?php echo $user_id; ?>";
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
    });
}     
});
        </script>
