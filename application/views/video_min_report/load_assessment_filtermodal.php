
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Filter</h4>
                    </div>
                    <div class="modal-body">
                        <form id="frmAssessModalForm" name="frmAssessModalForm" onsubmit="return false;"> 
                            <div class="row">    
                                <div class="col-md-11">    
                                    <div class="form-group last">
                                        <label>Assessment</label>
                                        <select id="assessment_id" name="assessment_id" class="form-control input-sm select2" placeholder="Please select" onchange="setassescompany_chart()">
                                        <option value="">All</option>
                                        <?php foreach ($assessment_set as $assess) { ?>
                                           <option value="<?= $assess->id; ?>" <?php echo ((isset($_SESSION['assessment_id'][$company_id]) && ($_SESSION['assessment_id'][$company_id]['assessment_id']==$assess->id)) ? 'selected' : '') ?>><?php echo $assess->assessment; ?></option>
                                        <?php } ?>      
                                        </select>
                                    </div>
                                </div>
                            </div>                                               
                            <input type="hidden" name='company_id' id='company_id' value='<?php echo $company_id ?>'>
                        </form>
                    </div>
<!--                    <div class="modal-footer">
                        <div class="col-md-12 text-right ">  
                            <button type="button" class="btn btn-orange" id="btnIndexFilter" onclick="setassescompany_chart()">
                                <span class="ladda-label">Apply</span>
                            </button>
                        </div>
                    </div>-->
<script>
    var company_id='<?php echo $company_id ?>';
    $('#assessment_id').select2({
                    placeholder: " All",
                    width: '100%',
                    allowClear: true
                });
    function setassescompany_chart() {
        $.ajax({
            type: "POST",
            data: $('#frmAssessModalForm').serialize(),
            url: "<?php echo base_url() ?>video_min_report/get_piechart_data",
            success: function (msg) {
                var Oresult = jQuery.parseJSON(msg);                    
                if (msg != '') {                                                
                  $('#assessminvideo'+company_id).html(Oresult);
                  $('#responsive-modal').modal('toggle');
                }
            }
        });

    }
</script>
