<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Status Details</h4>
</div>
<div class="modal-body">
    <form name="AssessorStatusSubForm" id="AssessorStatusSubForm">
        <div class="portlet light">
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="AssessorStatusSubTable" width="100%">
                    <thead>
                        <tr>                                                        
                            <th>Candidate Name</th>
                            <th>Candidate Status</th>
                            <th>Assessor Status</th>                            
                            <th>Action</th>                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>          
    </form>
</div>

<script type="text/javascript">
    var frm1=document.AssessorStatusSubTable;    
    var table=$('#AssessorStatusSubTable');
    jQuery(document).ready(function () {
        AssessorSubDatatableRefresh(<?php echo $assessment_id.','.$trainer_id; ?>);    
    });
        
</script>