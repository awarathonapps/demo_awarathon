<table class="table table-bordered table-striped" id="region_table">
    <thead>
        <tr class="uppercase bold">
            <th class="freeze-tb-column">By % Score</th>
            <th class="freeze-tb-column"><?php echo count($para_assess); ?> Parameters</th>
            <?php foreach ($para_assess as $parameter_id => $value) {
            ?>
                <th><?php echo $value ?> </th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="freeze-tb-column"> <?php echo count($user_list); ?> Learners</td>
            <!-- Horizontal Avg start here -->
            <!-- <tr class='bold'> -->
            <td class="freeze-tb-column">Avg. % Score</td>
            <?php if (count((array)$Horizontal_avg) > 0) {
                foreach ($Horizontal_avg as  $value) {
                    echo '<td class="bold" style="' . get_graphcolor($value, 2) . '">' . $value . '%</td>';
                }
            }
            // if (isset($Final_avg)) {
            //     echo '<td style="' . get_graphcolor($Final_avg, 2) . '">' . $Final_avg . '%</td>';
            // }
            ?>
        </tr>
        <?php
        $i = 1;
        foreach ($user_list as $user) {
        ?>
            <tr>
                <td class="freeze-tb-column"><?php echo $user['name'] ?></td>
                <?php
                $new = $user['id'];
                if (isset($Vertical_avg[$new])) {
                    $avg = $Vertical_avg[$new];
                    echo '<td class="bold" style="' . get_graphcolor($avg, 2) . '">' . $avg . '%</td>';
                }
                foreach ($regiondata[$new] as $reg) {
                    $lchtml = '<td width="20%" ';
                    if (!empty($reg->parameter_id)) {
                        $lchtml .= 'style="' . get_graphcolor($reg->p_result, 2) . '" >';
                        $lchtml .= number_format($reg->p_result, 2) . '%</td>';
                    } else {
                        $lchtml .= '>Yet to Play</td>';
                    }
                    echo $lchtml;
                }
                $i++;
                ?>
            </tr>
        <?php } ?>
    </tbody>
</table>

<script>
    table = $('#region_table');
</script>