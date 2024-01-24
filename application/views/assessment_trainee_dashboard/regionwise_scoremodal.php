
<div class="modal-header no-padding">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">ASSESSMENT-WISE REPORT : OVERALL REPORT</h4>
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
    var region_id="<?php echo $region_id; ?>";
    $(document).ready(function () {
        RegionWiseTable(assessment_id,region_id);
    });
    function RegionWiseTable(assessment_id,region_id) {
        $('.color-set').addClass("tr-background");
        $.ajax({
            url: base_url + "assessment_trainee_dashboard/regionwise_table",
            type: 'POST',
            data: {assessment_id: assessment_id, region_id: region_id},
            beforeSend: function () {
                customBlockUI();
            },
            success: function (data) {
                if (data != '') {
                    var json    = jQuery.parseJSON(data);                                                                                                                                                    
                    $('#append_table').html(json['regiontable_graph']);
                }                
                customunBlockUI();
            }
        });
    }

</script>
