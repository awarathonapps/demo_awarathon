
        <table class="table table-bordered table-striped" id="region_table">
            <thead>
                <tr class="uppercase bold">
                    <th>Name</th>
                <?php foreach ($region_list as $key => $value) { 
                    ?>
                    <th style="cursor:pointer" onclick="get_regionlevel_graph(<?php echo $key ?>)"><?php echo $value ?> </th> 
                    <?php } ?>
                    <th>Average</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                
                foreach ($para_assess as $parameter_id => $value) {
                    ?>
                <tr>
                    <?php if($report_by == 1){ ?>
                        <td ><?php echo $value ?></td>                    
                    <?php }else{ ?>
                        <td  style="cursor: pointer;" onclick="get_assessmentwisedata(<?php echo $parameter_id ?>)"><?php echo $value ?></td>
                    <?php } ?>    
                    <?php
                    $TotalRating=0;
                    $TotalStar=0;
                    $Total_result=0;
                    $cnt = 0;
                    foreach ($regiondata as $key1 => $reg) {
                    $lchtml ='<td ';
                        if(isset($reg[$parameter_id])){
                            $region_id= $reg[$parameter_id]->region_id;   
                            if($report_by == 1){
                                $lchtml .= 'style="cursor: pointer;'.get_graphcolor($reg[$parameter_id]->result,2).'" onclick="get_parameterdata('.$parameter_id.','.$region_id.');">';
                            }else{
                                $lchtml .= 'style="cursor: pointer; '.get_graphcolor($reg[$parameter_id]->result,2).'" onclick="get_regionwisedata('.$parameter_id.','.$region_id.');">';
                            }
                            $lchtml .= number_format($reg[$parameter_id]->result,2).'%</td>';
                            
                            $cnt++;
                        }else {
                            $lchtml .='>--- </td>';
                        }
                    echo $lchtml; }
                    //Added if statement isset()
                       if(isset($Vertical_avg[$parameter_id]))
                       {
                        $avg =$Vertical_avg[$parameter_id];
                        echo '<td class="bold" style="'.get_graphcolor($avg,2).'">'.$avg.'%</td>';
                        }
                        
                    
                   
                    ?>
                    
                </tr>
                <?php } ?>
                <tr class='bold'>
                    <td>Average</td>
                   <?php if(count($Horizontal_avg)>0){
                       foreach ($Horizontal_avg as  $value) {
                           echo '<td style="'.get_graphcolor($value,2).'">'.$value.'%</td>';    
                                }
                   }
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
//     table.dataTable();   
    });  
</script>
