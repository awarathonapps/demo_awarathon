<table class="table table-bordered table-striped" id="region_table">
    <thead>
        <tr class="uppercase bold">
            <th class="freeze-tb-column">By % Score</th>
            <th><?php if(!empty($modules_count)){ echo $region_count;} ?> Region</th>

            <?php
            if (!empty($region_list)) {
                foreach ($region_list as $key => $value) {
            ?>
                    <th class="freeze-tb-column"><?php echo $value ?> </th>
            <?php }
            }
            ?>

        </tr>
    </thead>
    <tbody>
        <tr class="">
            <th class="freeze-tb-column"> <?php if(!empty($modules_count)){ echo $modules_count;} ?> Modules</th>
            <th class="freeze-tb-column" style="height: 30px;">Avg % score</th>

            <?php
            if (!empty($Horizontal_avg)) {
                foreach ($Horizontal_avg as  $H_g) {
                    echo '<td class="bold" style="' . get_graphcolor($H_g, 2) . '">' . $H_g . '%</td>';
                }
            }
            ?>

        </tr>
        <?php
        if(!empty($para_assess)){
        foreach ($para_assess as $parameter_id => $m_value) {
        ?>
            <tr>
                <td class="freeze-tb-column"><?php echo $m_value ?></td>
                <?php
                if (!empty($Vertical_avg)) {
                    $v_avg = isset($Vertical_avg[$parameter_id]) ? $Vertical_avg[$parameter_id]  : '0';
                    echo '<td class="bold" style="' . get_graphcolor($v_avg, 2) . '">' . $v_avg . '%</td>';
                }

                ?>
                <?php
                if (!empty($regiondata)) {
                    $cnt = 0;
                    foreach ($regiondata as $key1 => $rg_data) {
                        $lchtml = '<td ';
                        if (isset($rg_data[$parameter_id])) {
                            if ($rg_data[$parameter_id]->result == '0' AND $rg_data[$parameter_id]->assessor_status == '0') {
                                $lchtml .= '>Not started </td>';
                            } else {
                                $region_id = $rg_data[$parameter_id]->region_id;
                                $lchtml .= 'style="cursor: pointer; ' . get_graphcolor($rg_data[$parameter_id]->result, 2) . '" onclick="get_regionwisedata(' . $parameter_id . ',' . $region_id . ');">';
                                $lchtml .= number_format($rg_data[$parameter_id]->result, 2) . '% </td>';
                            }
                            $cnt++;
                        } else {
                            $lchtml .= '>Mapping is pending</td>';
                        }
                        echo $lchtml;
                    }
                }
                ?>
            </tr>
        <?php } }?>
    </tbody>
</table>
<script>
    table = $('#region_table');
    $(document).ready(function() {
        // table.dataTable();   
    });
</script>