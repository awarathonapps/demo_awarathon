<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Assessor Status Details</h4>
</div>
<div class="modal-body">
    <form name="AssessorForm" id="AssessorForm">
        <div class="portlet light">
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="AssessorStatusTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>Assessor Name</th>
                            <th>Assessor Status</th>
                            <th>Completed Candidate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>          
    </form>
</div>
        <script type="text/javascript">
    var frm1=document.AssessorForm;    
    var table=$('#AssessorStatusTable');
        jQuery(document).ready(function () {
            AssessorDatatableRefresh(<?php echo $assessment_id; ?>);        
        });
       
        </script>
