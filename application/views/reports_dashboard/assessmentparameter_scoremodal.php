
<div class="modal-header no-padding" >
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class=""><b><?php echo $assessment_name->assessment; ?></b>  : OVERALL REPORT</h4>
</div>
<div class="row">
    <form name="AssessmentScore" id="AssessmentScore">    
			<div class="col-md-12 ">
				<div class="table-scrollable "style="margin: 10px" id="append_table">
					
				</div>
			</div>                     
	</form>   
</div> 
<script>    
    var base_url="<?php echo base_url(); ?>";
    var assessment_id="<?php echo $assessment_id; ?>";    
    var report_type="<?php echo $report_type; ?>";
    $(document).ready(function () {
        AssessmentParameterWiseTable(assessment_id, report_type);
    });
    function AssessmentParameterWiseTable(assessment_id,report_type) {
        $('.color-set').addClass("tr-background");
        $.ajax({
            url: base_url + "reports_dashboard/assessment_parameterwise_table",
            type: 'POST',
            data: {assessment_id: assessment_id, report_type: report_type},
            beforeSend: function () {
                customBlockUI();
            },
            success: function (data) {
                if (data != '') {
                    var json    = jQuery.parseJSON(data);                                                                                                                                                    
                    $('#append_table').html(json['assessmenttable_graph']);
                }                
                customunBlockUI();
            }
        });
    }

</script>
