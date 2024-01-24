<div class="modal-header">
    <button type="button" id="CloseModalBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Device Info( <?php echo $User ?> )</h4>
</div>
<div class="modal-body">
    <form name="DeviceInfoForm" id="DeviceInfoForm">
        <div class="portlet light">
            <div class="form-body">
                <table class="table  table-bordered " id="info_table" >
                    <thead>
                        <tr>                                                                
                            <th>Platform</th>
                            <th>Version Number</th>
                            <th>IMEI</th>
                            <th>UUID</th>
                            <th>Model</th>
                            <th>Version</th>
                            <th>Serial</th>
                            <th>Manufacturer</th>                            
                            <th>System Date/Time</th>
                        </tr>
                </thead>
                <tbody>
                    <?php if(count($device_info) > 0) { 
                        foreach ($device_info as $val){ ?>
                    <tr <?php echo ($val->isprimary_imei==1 ? 'class="success"' :''); ?>>                
                        <td><?php echo $val->platform ?></td>
                        <td><?php echo $val->version_number ?></td>
                        <td><?php echo $val->imei ?></td>
                        <td><?php echo $val->uuid ?></td>
                        <td><?php echo $val->model ?></td>
                        <td><?php echo $val->version ?></td>
                        <td><?php echo $val->serial ?></td>
                        <td><?php echo $val->manufacturer ?></td>                        
                        <td><?php echo ($val->info_dttm == '0000-00-00 00:00:00' ? '': $val->info_dttm )?></td>
                    </tr>
                    <?php } } ?>
                </tbody>
                </table>
                    
            </div>
        </div>          
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() { 
        var table = $('#info_table');
            table.dataTable({
                destroy: true,                    
                "pageLength": 10,            
                "pagingType": "bootstrap_full_number",
                "columnDefs": [
                    {'width': '30px','orderable': false,'searchable': false,'targets': [0]}, 
                    {'width': '30px','orderable': true,'searchable': true,'targets': [1]}, 
                    {'width': '200px','orderable': true,'searchable': true,'targets': [2]}, 
                    {'width': '200px','orderable': true,'searchable': true,'targets': [3]}, 
                    {'width': '200px','orderable': true,'searchable': true,'targets': [4]},
                    {'width': '85px','orderable': true,'searchable': true,'targets': [5]},
                    {'width': '130px','orderable': true,'searchable': true,'targets': [6]},
                    {'width': '130px','orderable': false,'searchable': false,'targets': [7]}, 
                    {'width': '65px','orderable': false,'searchable': false,'targets': [8]}                    
                ]
            });
    });
</script>
