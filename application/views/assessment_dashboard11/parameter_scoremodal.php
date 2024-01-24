<div class="modal-header no-padding">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Parameter-Wise Assessment Score Report </h4>
</div>
    <div class="row">
            <form name="AssessmentScore" id="AssessmentScore">    
                <div class="col-md-12 ">
                    <div class="col-md-5 ">
                       <div class="table-scrollable "style="margin: 10px">
                        <table class="table table-hover table-light" id="assessment_table" width="100%" >
                            <thead>
                                <tr class="tr-background">
                                    <th colspan="2" class="font-dark bold"><h5>Parameter Name : <?php echo $assessment_result[0]->parameter ?></h5></th>
                                </tr>
                                <tr class="uppercase ">
                                    <th class="font-dark bold" onclick="assessment_usercount();" style="cursor: pointer;">Assessment Name</th>
                                    <th class="font-dark bold">Score</th>
                                </tr>
                            </thead>
                            <tbody class="">
                               <?php 
                                $assessmentset=array();
                                foreach ($assessment_result as $key => $value) { $assessmentset[]=$value->assessment_id ?>
                            <tr class="tr-background color-set" style="cursor: pointer;" onclick="assessment_usercount(<?php echo $value->assessment_id ?>);" id="assessment<?php echo $value->assessment_id ?>">  
                                    <td class="wksh-td"><?php echo $value->assessment ?></td>
                                    <td class="wksh-td bold"><?php echo $value->result ?></td>
                                </tr>
                               <?php } ?>
                            </tbody>
                        </table>
                       </div>
                    </div> 
                    <div class="col-md-7 ">
                      <div class="portlet light " style="padding: 0px 20px 10px !important;">
                        <div class="portlet-title potrait-title-mar">
                            <div class="caption">
                                <i class="icon-bar-chart font-dark hide"></i>
                                <span class="caption-subject font-dark bold uppercase">Question-wise Report</span>
                            </div>
                        </div>
                        <div class="portlet-body" style="padding: 0px !important"> 
                        <div style="margin-bottom: 20px">
                                <div style="margin-bottom:10px;padding:inherit;" class="bold">OverAll Count : <span id="overall_user"></span></div>
                            
                                <div><span class="col-md-4 bold" style="padding: inherit;">Assessment Complete : <span id="complte_user"></span></span> <span class="col-md-4 bold">Assessment Incomplete : <span id="incomplte_user"></span></span></div>
                        </div>
                        <div class=" table-scrollable-borderless" style="margin-top: 40px">
                                <table class="table table-bordered table-striped" id="questionscore_table" width="100%" >
                                    <thead>
                                        <tr class="uppercase">
                                            <th>Question</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>                                       
                          </div>
                        <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id ?>">
                        <input type="hidden" name="regionid" id="regionid" value="<?php echo $region_id ?>"/>
                        <input type="hidden" name="parameter_id" id="parameter_id" value="<?php echo $parameter_id ?>"> 
                        <input type="hidden" name="report_type" id="report_type" value="<?php echo $report_type ?>"> 
                        <input type="hidden" name="assessmentstr" id="assessmentstr" value="<?php echo implode(',', $assessmentset) ?>"> 
                    </div> 
                </div>
               </div> 
         </form>
    </div>    
<script>
    var table = $('#questionscore_table');
    var base_url="<?php echo base_url(); ?>";
    $(document).ready(function () {
     assessment_usercount();
    });
    function assessment_usercount(assessment_id) {
         var assess_id=assessment_id;
            if(assess_id==undefined || assess_id==''){
             assess_id = $('#assessmentstr').val();
         }
            $.ajax({
                url: base_url + "Assessment_dashboard/assessment_usercount",
                type: 'POST',
                data: {assessment_id: assess_id,StartDate:StartDate,EndDate:EndDate},
                beforeSend: function () {
                    customBlockUI();
                },
                success: function (odata) {
                    var data   = jQuery.parseJSON(odata);  
                    $('#overall_user').text(data['total_user']);
                    $('#complte_user').text(data['complete_user']);
                    $('#incomplte_user').text(data['incomplete_user']);
                    QuestionDatatableRefresh(assessment_id);
                    customunBlockUI();
                }
            });
        }
    function QuestionDatatableRefresh(assessment_id) {
                $('.color-set').addClass("tr-background");
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
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "order": [
                        [0, "asc"]
                    ],
                    "pageLength": 10,
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
//                      {'width': '30px','orderable': true,'searchable': true,'targets': [0],"visible":false},
			{'className': 'dt-head-left dt-body-left','width': '130px','orderable': true,'searchable': false,'targets': [0]},
                        {'className': 'dt-head-left dt-body-left','width': '100px','orderable': true,'searchable': true,'targets': [1]}
                    ],
                    
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'Assessment_dashboard/QuestionScoreDatatable/'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        aoData.push({name: 'company_id', value: $('#company_id').val()});
                        aoData.push({name: 'assessment_id', value: assessment_id});
                        aoData.push({name: 'region_id', value: $('#regionid').val()});
                        aoData.push({name: 'parameter_id', value: $('#parameter_id').val()});
                        aoData.push({name: 'StartDate', value: StartDate });
                        aoData.push({name: 'EndDate', value: EndDate });
                        aoData.push({name: 'report_type', value: $('#report_type').val()});
                        $.getJSON(sSource, aoData, function (json) {
                          fnCallback(json);
                        });
                    },
                    "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                          if(assessment_id!=''){
                                $('#assessment'+assessment_id).removeClass("tr-background");
                          }
                        return nRow;;
                    }
                    , "fnFooterCallback": function (nRow, aData) {
                    },
                    "initComplete": function(settings, json) {
                        $('thead > tr> th:nth-child(1)').css({ 'min-width': '80px', 'max-width': '150px' });
                        $('thead > tr> th:nth-child(2)').css({ 'min-width': '100px', 'max-width': '150px' });
                    }
                });
            }

</script>
