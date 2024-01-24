<table class="table  table-bordered table-hover table-checkable order-column" id="index_table">
    <thead id="tablehead">
        <tr>
            <th>Trainee ID</th>
            <th>Trainee Region</th>
            <th>Trainee Name</th>
            <th>Area(HQ)</th>
            <th>Workshop Region</th>                    
            <th>Workshop type</th>                    
            <th>Workshop Name</th>                                                        
            <th>Pre</th>    
            <th id="qset" colspan="<?php echo count($qset['qset']) ?>">Post(Question SetWise)</th>
            <th>POST</th>
        </tr>
        <tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
            <?php if(count($qset['qset']) > 0){ 
                foreach($qset['qset'] as $val){ ?>
                    <th><?php echo $val['questionset'] ?></th>                
            <?php } }else{?>
                <th></th>
            <?php } ?>    
            <th></th>
        </tr>    
    </thead>
    <tbody></tbody>
</table>
<script>
    table = $('#index_table');
    $(document).ready(function () {
        DatatableRefresh();
    });
</script>
