<?php
defined('BASEPATH') or exit('No direct script access allowed');
$base_url = base_url();
$asset_url = $this->config->item('assets_url');
?>
<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Ideal Videos</h4>   
</div>

<!--<div class="modal-body">-->
<div class="modal-body" id="modal-body-ideal">
    <form name="IdealForm" id="IdealForm">
        <input type="hidden" id="assessment_id" name="assessment_id" value="<?= $assessment_id ?>"/>
        <div class="portlet light">
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="Question_Table" width="100%">
                    <thead>
                        <tr>
                            <th style="width:5%">ID #</th>
                            <th style="width:35%">Question</th>
                            <th style="width:30%">Best Video</th>
                            <th style="width:30%">Ideal Video</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
				<div class="row">      
					<div class="col-md-12 text-center">    
						<button type="button" id="submit_ideal" name="submit_ideal" data-loading-text="Please wait..." class="btn btn-orange btn-sm btn-outline" data-style="expand-right" onclick="submit_video()">
							<span class="ladda-label"><i class="fa fa-envelope"></i>&nbsp; Submit</span>
						</button>
					</div>
				</div>
            </div>
        </div>          
    </form>
</div>

<script type="text/javascript">
    var base_url = '<?= base_url(); ?>';
	var frm1=document.IdealForm;
    var IdealForm = $('#IdealForm');
	jQuery(document).ready(function () {
		IdealQuestionDatatable(<?php echo $assessment_id; ?>);        
	});

    function submit_video()
    {
        $.ajax({
			type: "POST",
			data: IdealForm.serialize(),
			url: "<?php echo $base_url; ?>ai_dashboard/submit/",
			beforeSend: function () {
				 customBlockUI();
			},
			success: function (Odata) {
				var Data = $.parseJSON(Odata);
				if (Data['success']) {
					ShowAlret(Data['Msg'], 'success'); 
				} else {
					$('#errordiv').show();
					$('#errorlog').html(Data['Msg']);
				}
			customunBlockUI();
			}
        });
    }
</script>