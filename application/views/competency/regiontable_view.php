
<style>
table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
}

th, td {
  text-align: left;
  padding: 8px;
}

</style>
<script type="text/javascript" src="https://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=JEgTwmG9nSxlouulm4pCzV6_2Z9arl-PXER1fzmo-9IcGbQLGGusIR0Uw2wwqzEsjJ0qSGtqgEeLF0Ks0XvNhQFV9PHPgRt3tbGKakdi6rQVlURl0gH2c1O57mlmUrQlbh30diRxAviho91J8E-t8U77xcL7YiZS3-gQ_9Sn5IDARIM1ywgepR_OLALypPiohLPT_9MzMS2C9qH76teIGapl_uykW4e4COKHNq8wEktVjwv8d8VCnvl9d-caNAoS64LMXGmZaYaNlb8RL97cfBj7maKxvJYi-DOJQVtUFXrBBiNZGdl6HObXpRXZ_q3W7bW2Yk8Whu9BpDNCGsFC70wNakVYWjbgCGcRj0n2nk9eZaWqJJ_0cUkgvwlBgDfamYn3pGS1YcFb0No0yZrsPpoLMbsIastUarAP1RLC1iwFXAEvjOz5u05BZ_MYikOy0CGSrNQgCCGeydr9gfpRnBgUsFHDZzRdjJ7PDtLFJhT6B5P3mO2Ljc3maDntm04Jazh5aQoy30QUhnxDt2S9yjJOqcp8YcnkRiiXaF0yVT76qTHmUmUWAJ_xZxkzfEM-ybmbOjUcw-0uXkfuX1FCESbgi_8doobw3rIsyiMXvjaQSrfpR-mYXenPwAOH2tV42wTeBIF9PtpsQWWx6VT_mEmIrAdWtlgg99coUZWA432APZAlevwV2wv5lLIg1yfCUkt0GjP2rJOo9vUgii1bJ-F_G0LJ-hrvtSC-kDPzUEnNKIOg60SFnxMVKdKpqtAe6LyClLjEPnHb7mJGMQgMnKe0iFFijN1mTedlln5DsULo_aJbYTk3IHJ8ATIi4wTHHDf5ZuoyOl49rdxEjy8jVaR9UYEFxK1Tuku2hHITuZiC6dD7nlCmUbCZy7ObtwmtOT4JO3mJglHfw5Ziv1QDJHtpYgf6ipBZVIP9qacKUIk_IJJ9cV4wdznTbTPzDtqCZOe1bD2oq-IPIT7ZSMYEBSb1ZS58NH-sdzPz886ZcxHlG8VVbemkM2911a-hCpVo96hqt9rjaQ11r6zbLSVKVIKNiURluCvuWVoyzCPbajtZHbryFyYQE83iGsgluKX-v97FuMSTRiUy8dBGIvIJtmITf39cHfkX2-sq_5CBWvlZVLAuYNfhOPI74QMWbmlo8uImQ7EIWjaKTc0GdYaoFY9a7ejYwYelMk4dCLHMK3y_RR9sovHHd3jH6UwhtgBtpd-v0qSQaOZPSkyZAdLCaJokB4riX1s8YsqC1lAQZMWvExOmOvzzL3iYI742xrnO7a33C-7EQJx1ionQqcOw3-DPWWboJXbGml5mXpuKbHysWbnvLx19mOjDlOY9Pt2GnNmcGkBoqZ56-NSYD_BW8tAFUJLtRYORQcmVTSpTU1vnSBWCcNhYJu3BiuwYhhsJqQtWWdLZ9blswk8LJU3qGPJMZPzp6XZND1QuTdGtr9QRXgiajUCswSPgUDCM2xu5QRVzK5oNvt8CmMuoRviOkawIrwKKFbEtrWhdqx2azEoREikPGq_PIa8emKoeT9eROqUnSSXRGZof2nuRmh4i6hkdgG9GqIHHsCRSQu0u3GrW7i7tJb0LF_S6WGOwgYkv" charset="UTF-8"></script></head>

<div style="overflow-x:auto; width: 1366px;">
<table class="table table-bordered table-striped table-body" id="region_table">
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
            </div>
<script>
    table = $('#region_table');
    $(document).ready(function() {
        // table.dataTable();   
    });
</script>