
         <!--<table class="table table-hover table-light" id="region_table">-->
        <table class="table table-bordered table-striped" id="region_table">
            <thead>
                <tr class="uppercase bold">
                    <th>Name</th>
                <?php foreach ($region_list as $key => $value) { ?>

                    <th><?php echo $value ?> </th> 
                    <?php } ?>
                    <th width="10%">Average</th>
                </tr>
            </thead>
            <tbody>
                <?php                 
                foreach ($para_assess as $parameter_id => $value) {
                    ?>
                <tr>
                    <td><?php echo $value ?></td>
                    <?php
                    $TotalRating=0;
                    $TotalStar=0;
                    foreach ($regiondata as $key1 => $reg) {
                    $lchtml ='<td width="20%" ';
                        if(isset($reg[$parameter_id])){                            
                            $lchtml .= 'style="'.get_graphcolor($reg[$parameter_id]->result,2).'" >';
                            $lchtml .=number_format($reg[$parameter_id]->result,2).'%</td>';
                        }else {
                            $lchtml .='>--- </td>';
                        }
                    echo $lchtml; }
                    $avg = $Vertical_avg[$parameter_id];
                    
                    echo '<td class="bold" style="'.get_graphcolor($avg,2).'">'.$avg.'%</td>'
                    ?>
                    
                </tr>
                    <?php } ?>
                <tr class='bold'>
                    <td>Average</td>
                   <?php if(count((array)$Horizontal_avg)>0){
                       foreach ($Horizontal_avg as  $value) {
                           echo '<td style="'.get_graphcolor($value,2).'">'.$value.'%</td>';    
                   } }
                   if(isset($Final_avg)){
                       echo '<td style="'.get_graphcolor($Final_avg,2).'">'.$Final_avg.'%</td>';
                   }
                   ?>
                </tr>
            </tbody>
        </table>
<script>
    table = $('#region_table');
    $(document).ready(function () {

    });  
</script>
