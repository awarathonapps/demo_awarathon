<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Candidate Details</h4>
    <div style="float:right;font-size:11px;font-weight:400;">
		<span style="height: 15px;width: 15px;background: #db1f48;padding: 9px;color: #ffffff;">SP</span> Schedule Pending
		<span style="height: 15px;width: 15px;background: #004369;padding: 9px;color: #ffffff;">PP</span> PDF Pending  
		<span style="height: 15px;width: 15px;background: #36c6d3;padding: 9px;color: #ffffff;">RP</span> Rating Pending
    </div>
</div>
<div class="modal-body">
    <form name="CandidateForm" id="CandidateForm">
		<input type="hidden" name="assessment_id" id="assessment_id" value="<?= $assessment_id; ?>" />
        <div class="portlet light">
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="CandidateFilterTable" width="100%">
                    <thead>
						<?php if($is_send){ ?>
						<tr>
							<button type="button" id="candidate_email_send" name="candidate_email_send" data-loading-text="Please wait..." class="btn btn-orange btn-sm btn-outline margin-bottom-10" data-style="expand-right" style="float:right">
								<span class="ladda-label"><i class="fa fa-envelope"></i>&nbsp; Send</span>
							</button>
						</tr>
						<?php } ?>
                        <tr>
							<th>
								<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
									<input type="checkbox" class="all group-checkable candidate_check" name="candidate_check" id="candidate_check" data-set="#CandidateFilterTable .checkboxes" />
									<span></span>
								</label>
							</th>
                            <th>ID #</th>
                            <th>Candidate Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
							<?php if ($report_type==1 OR $report_type==3 OR $report_type==0){ ?>
								<th>AI</th>
							<?php } ?>
							<?php if ($report_type==2 OR $report_type==3 OR $report_type==0){ ?>
								<th>Manual</th>
							<?php } ?>
							<?php if ($report_type==3 OR $report_type==0){ ?>
								<th>AI + Manual</th>
							<?php } ?>
							<?php if($is_send){ ?>
								<th>Status</th>
							<?php } ?>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>          
    </form>
</div>
<script type="text/javascript">
	jQuery(document).ready(function () {
		var assessment_id = $('#assessment_id').val();
		var report_type = "<?= $report_type ?>";
		var is_send_tab = "<?= $is_send ?>";
		CandidateDatatableRefresh(assessment_id,report_type,is_send_tab); 
		select_candidates = [];
		$('#candidate_email_send').click(function(){
			var select_candidates = $.map($(':checkbox[name=candidate_id\\[\\]]:checked'), function(n, i){
				  return n.value;
			}).join(',');
			if(!select_candidates.trim()){
				ShowAlret('Please select the candidates!', 'error');
			}else{
				// console.log(select_candidates);
				scheduleCandidateEmail($('#company_id').val(),assessment_id,select_candidates);
			}
		});
	});
</script>