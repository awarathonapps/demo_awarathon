<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Search </h4>
</div>
<div class="modal-body">
                                <div id="responsive-modal" class="modal dont-fade" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true" tabindex="-1" data-width="760">
                                            <form id="frmModalForm" name="frmModalForm" onsubmit="return false;"> 
                                                <div class="modal-header">
                                                    <button type="button" class="close" onclick="resetDATA();" data-dismiss="modal" aria-hidden="true"></button>
                                                    <h4 class="modal-title">Create Topic</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div id='dsk' style="display: none">&nbsp;</div>
                                                    <div class="row">    
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Company Name<span class="required"> * </span></label>
                                                                <select id="company_id" name="company_id" class="form-control input-sm select2" placeholder="Please select" style="width:100%">
                                                                    <option value="">Please Select</option>
                                                                   
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">    
                                                        <div class="col-md-6">       
                                                            <div class="form-group">
                                                                <label class="">Topic<span class="required"> * </span></label>
                                                                <input type="text" name="description" id="description" maxlength="250" class="form-control input-sm" autocomplete="off">  
                                                                <input type="hidden" name="edit_id" id="edit_id" class="form-control input-sm" autocomplete="off" value="">                                                                
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">    
                                                            <div class="form-group last">
                                                                <label>Status</label>
                                                                <select id="status" name="status" class="form-control input-sm select2" placeholder="Please select" >
                                                                    <option value="1" selected>Active</option>
                                                                    <option value="0">In-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="col-md-12 text-right ">  
                                                        <button type="submit" id="modal-create-submit" name="modal-create-submit" data-loading-text="Please wait..." class="btn btn-orange mt-ladda-btn ladda-button mt-progress-demo" data-style="expand-right">
                                                            <span class="ladda-label">Submit</span>
                                                        </button>
                                                        <button type="button" data-dismiss="modal" onclick="resetDATA();" class="btn btn-default btn-cons">Cancel</button>
                                                    </div>
                                                </div>
                                            </form> 
                                    </div>
    
    <form name="subtopicadd" id="subtopicadd">
        <div class="portlet light form ">
            <div class="form-body">
                <table class="table table-striped table-bordered table-hover" id="sample_2" width="100%">
                    <thead>
                        <tr>
                            <th>ID #</th>  
                            <th>ID #</th>
                            <th>ID #</th>
                            <th>ID #</th>
                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>          
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn blue" onclick="ConfirmMedicine();" >Confirm</button>
</div>
<script type="text/javascript">
    var Questionset = document.questionset_form;
    var frm1 = document.subtopicadd;
    jQuery(document).ready(function () {        
        DataGridTable();
        $('.all').click(function () {
            if ($(this).is(':checked')) {
                $("input[name='id[]']").prop('checked', true);
            } else {
                $("input[name='id[]']").prop('checked', false);
            }
            $("input[name='id[]']").each(function (index) {
                
            });
        });
    });

    function DataGridTable() {      
            var table = $('#sample_2');
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
                        "infoFiltered": "(filtered1 from _MAX_ total records)",
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
                    //dom: 'Bfrtip',
                    //buttons: [
                    //    { extend: 'print', className: 'btn dark btn-outline' },
                    //    { extend: 'pdf', className: 'btn green btn-outline' },
                    //    { extend: 'csv', className: 'btn purple btn-outline ' }
                    //],
                    //buttons: [
                    //    'copy', 'csv', 'excel', 'pdf', 'print'
                    //],
                    //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                    "bStateSave": false,
                    "lengthMenu": [
                        [5,10,15,20, -1],
                        [5,10,15,20, "All"]
                    ],
                    "pageLength": 10,            
                    "pagingType": "bootstrap_full_number",
                    "columnDefs": [
 //                       {'width': '30px','orderable': false,'searchable': false,'targets': [0]}, 
//                        {'width': '40px','orderable': false,'searchable': false,'targets': [1]}, 
//                        {'width': '','orderable': false,'searchable': true,'targets': [2]}, 
//                        {'width': '300px','orderable': false,'searchable': true,'targets': [3]}, 
//                        {'width': '103px','orderable': false,'searchable': true,'targets': [4]}, 
//                        {'width': '30px','orderable': false,'searchable': false,'targets': [5]}, 
//                        {'width': '60px','orderable': false,'searchable': false,'targets': [6]}, 
                        
                    ],
                    "order": [
                        [1, "asc"]
                    ],
                    "processing": true,
                    "serverSide": true,
                    "sAjaxSource": "<?php echo base_url() . 'questionset/DatatableRefresh'; ?>",
                    "fnServerData": function (sSource, aoData, fnCallback) {
                        aoData.push({name: '__mode', value: 'featuredimage.ajaxload'});
                        $.getJSON(sSource, aoData, function (json) {
                            fnCallback(json);
                        });
                    },
                    "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                        return nRow;
                    }
                    , "fnFooterCallback": function (nRow, aData) {
                    }
                });
    }

</script>

<!-- END JAVASCRIPTS -->

</body>

<!-- END BODY -->

</html>