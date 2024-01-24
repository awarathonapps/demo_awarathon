
        <table class="table table-bordered table-striped" id="region_table">
            <thead>
                <tr class="uppercase bold">
                    <th>Name</th>
                <?php foreach ($user_list as $key => $value) { 
                    ?>
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
                            $TotalRating +=$reg[$parameter_id]->rating;
                            $TotalStar +=$reg[$parameter_id]->total_rate;
                        }else {
                            $lchtml .='>--- </td>';
                        }
                    echo $lchtml; }
                    if($ratingstyle==2){
                        $avg = number_format($TotalRating / $TotalStar,2);
                    }else{
                        $avg = number_format($TotalRating*100/$TotalStar,2);
                    }
                    echo '<td class="bold" style="'. get_graphcolor($avg,2).'">'.$avg.'%</td>'
                    ?>
                    
                </tr>
                    <?php } ?>
                <tr class='bold'>
                    <td>Average</td>
                   <?php if(count($horizontal_set)>0){
                       foreach ($horizontal_set as  $value) {
                           if($ratingstyle==2){
                               echo '<td style="'.get_graphcolor(number_format($value['TotalRating'] / $value['TotalStar'],2),2).'">'.number_format($value['TotalRating']/$value['TotalStar'],2).'%</td>';    
                           } else {
                               echo '<td style="'.get_graphcolor(number_format($value['TotalRating']*100/$value['TotalStar'],2),2).'">'.number_format($value['TotalRating']*100/$value['TotalStar'],2).'%</td>';    
                           }
                   } }
                   if(isset($last_avg)){
                       echo '<td style="'.get_graphcolor($last_avg,2).'">'.$last_avg.'%</td>';
                   }
                   ?>
                </tr>
            </tbody>
        </table>
    
<script>
    table = $('#region_table'); 
</script>
