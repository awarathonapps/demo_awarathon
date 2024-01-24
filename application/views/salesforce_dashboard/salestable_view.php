
        <table class="table table-bordered table-striped" id="saless_table">
            <thead>
                <tr class="uppercase bold">
                    <th>Knowledge</th>
                    <th>Skill / feedback</th> 
                    <th>Sales</th>
                    <th>Category / Status</th> 
                    <th>Frequency</th>
                    <th>Sales (in %)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_sales=array();
                $total_frequency=0;
                if(count($sales_data) > 0){ 
                foreach ($sales_data as $key => $value) {
                    $user_str = implode(",",$value['users']);
					$sale_percent_avg = array_sum($value['percent_avg']) / count($value['percent_avg']);
                ?>
                <tr class="tr-background">
                    <td style="<?php echo $value[$value['knowledge'].'_cl'] ?>"><?php echo $value['knowledge'] ?></td>                    
                    <td style="<?php echo $value[$value['skill'].'_cl'] ?>"><?php echo $value['skill'] ?></td>                    
                    <td style="<?php echo $value[$value['bussiness'].'_cl'] ?>"><?php echo $value['bussiness'] ?></td>                    
                    <td class="bold" >
                        <a data-target="#LoadModalFilter" data-toggle="modal" href="<?php echo base_url() . 'salesforce_dashboard/LoadViewModal/' . base64_encode($assessment_id) . '/' . base64_encode($user_str). '/' . base64_encode($value['status']) ?>" style="width : 100%;">
                            &nbsp;<?php echo $value['status']; ?>
                        </a>
                    </td>                    
                    <td><?php echo $value['frequency'] ?></td>                    
                    <td class="bold" style="<?php echo ($value['bussiness']=='High' ? 'color: green;' : 'color: red;') ?>"><?php echo number_format($sale_percent_avg,2) ?>%</td>                    
                </tr>
                <?php 
                $total_frequency += $value['frequency'];
                 $total_sales[]= $sale_percent_avg;
                } ?>
                <tr class="">
                    <td colspan="3"></td>
                    <td class="bold" > Total </td>
                    <td class="bold"><?php echo $total_frequency ?></td>
                    <td class="bold"><?php echo number_format(array_sum($total_sales) / count($total_sales),2); ?>%</td>
                </tr>
                <?php }else{ ?>
                <tr>
                    <td class="bold" colspan="6" style="text-align: center;">No Record Found</td>
                </tr>
              <?php } ?>
            </tbody>
        </table>

<script>
    $(document).ready(function () {

    });  
</script>
