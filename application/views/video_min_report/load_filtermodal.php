
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Filter</h4>
                    </div>
                    <div class="modal-body">
                        <form id="frmModalForm" name="frmModalForm" onsubmit="return false;"> 
                            <div class="row">    
                                <div class="col-md-11">    
                                    <div class="form-group last">
                                        <label>Report By</label>
                                        <select id="rpt_period" name="rpt_period" class="form-control input-sm dropselect2" placeholder="Please select" >
											<?php 
											$default = 'yearly';
											if(isset($_SESSION['company_id'][$company_id])){
												$default = $_SESSION['company_id'][$company_id]['rpt_period'];
											} ?>
                                            <option value="all" <?php echo ($default=='all' ? 'selected' : '' ) ?>>All</option>
                                            <option value="weekly" <?php echo ($default=='weekly' ? 'selected' : '' ) ?>>Weekly</option>
                                            <option value="monthly" <?php echo ($default=='monthly' ? 'selected' : '' ) ?>>Monthly</option>
                                            <option value="yearly" <?php echo ($default=='yearly' ? 'selected' : '' ) ?>>Yearly</option>
                                        </select>
                                    </div>
                                </div>
                            </div>                                               
                            <div class="row">
                                <div class="col-md-11">    
                                    <div class="form-group last">
                                        <label>Month</label>
                                        <select id="month" name="month" class="form-control input-sm dropselect2" placeholder="Please select" onchange="getWeek()">
                                            <!--<option value="">Please select</option>-->
                                            <?php foreach (range(1, 12) as $month):
                                            $monthPadding = str_pad($month, 2, "0", STR_PAD_LEFT);
                                            $fdate = date("F", strtotime(date('Y') . "-$monthPadding-01"));
                                            echo '<option value="' . $monthPadding . '" '.(isset($_SESSION['company_id'][$company_id]) ? (($_SESSION['company_id'][$company_id]['month']==$monthPadding) ? 'selected' : '') : ($monthPadding==date('m') ? 'selected':'')).'>' . $fdate . '</option>';
//                                            echo '<option value="' . $monthPadding . '" '.($monthPadding==date('m') ? 'selected':'').'>' . $fdate . '</option>';
//                                            echo '<option value="' . $monthPadding . '" >' . $fdate . '</option>';
                                            endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-11">    
                                    <div class="form-group last">
                                        <label>Week</label>
                                        <select id="week" name="week" class="form-control input-sm dropselect2" placeholder="Please select">

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">    
                                <div class="col-md-11">    
                                    <div class="form-group last">
                                        <label>Year</label>
                                        <select id="year" name="year" class="form-control input-sm dropselect2" placeholder="Please select" >
                                            <option value="<?php echo date('Y') ?>"><?php echo date('Y') ?></option>
                                        </select>
                                    </div>
                                </div> 
                            </div>
                            <input type="hidden" name='company_id' id='company_id' value='<?php echo $company_id ?>'>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 text-right ">  
                            <button type="button" class="btn btn-orange" id="btnIndexFilter" onclick="setcompany_chart()">
                                <span class="ladda-label">Apply</span>
                            </button>
                        </div>
                    </div>
<script>
    var company_id='<?php echo $company_id ?>';
    var week_id="<?php echo (isset($_SESSION['company_id'][$company_id]) ? $_SESSION['company_id'][$company_id]['week'] : '') ?>";
    $(document).ready(function () {
     getWeek(week_id);
     $('.dropselect2').select2({
            placeholder: " All",
            width: '100%',
            allowClear: true
        });
    });
    function setcompany_chart() {
        $.ajax({
            type: "POST",
            data: $('#frmModalForm').serialize(),
            url: "<?php echo base_url() ?>video_min_report/get_user_data",
            success: function (msg) {
                var Oresult = jQuery.parseJSON(msg);                    
                if (msg != '') {                                                
                  $('#minvideo'+company_id).html(Oresult);
                  $('#responsive-modal').modal('toggle');
                }
            }
        });

    }
</script>
