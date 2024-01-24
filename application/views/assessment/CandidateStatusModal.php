<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Candidate Status Details</h4>
</div>
<div class="modal-body">
    <form name="CandidateForm" id="CandidateForm">
        <div class="portlet light">
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="CandidateFilterTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>EMP ID #</th>   <!-- KRISHNA -- Added search column for EMP ID -->
                            <th>Candidate Name</th>
                            <th>Candidate Status</th>
                            <th>Assessor Name</th>
                            <th>Assessor Status</th>
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
            var frm1=document.CandidateForm;
            jQuery(document).ready(function () {
                CandidateDatatableRefresh(<?php echo $assessment_id; ?>);        
            });
        </script>
