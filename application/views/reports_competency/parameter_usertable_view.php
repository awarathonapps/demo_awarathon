<table class="table table-bordered table-striped" id="region_2_table">
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
       // Image Function start here
       function heatmapimage() {
                   var h_count = $('#hitmap_count').val();
                   if (h_count == 0) {
                       ShowAlret("No data found please select module.!!", 'error');
                       return false;
                   }
                   if (Company_id == "") {
                       ShowAlret("Please select Company first.!!", 'error');
                       return false;
                   }

                   $.confirm({
                       title: 'Confirm!',
                       content: "Are you sure want to download Heat Map. ? ",

                       buttons: {
                           confirm: {
                               text: 'Confirm',
                               btnClass: 'btn-primary',
                               keys: ['enter', 'shift'],

                               action: function() {
                                   $('.btn-primary').hide();
                                   downloadpng();
                                   customBlockUI();
                               },
                           },
                           cancel: function() {
                               $('.btn-primary').show();
                               this.onClose();

                           }
                       }
                   });
               }

               function downloadpng() {
                   $('.btn-primary').hide();
                   customBlockUI();
                   const date = new Date();
                   const screenshotTarget = document.getElementById('append_table');
                   html2canvas(screenshotTarget).then((canvas) => {
                       const base64image = canvas.toDataURL("image/png");
                       var anchor = document.createElement('a');
                       anchor.setAttribute("href", base64image);
                       anchor.setAttribute("download", "Heat Map " + date + ".png");
                       anchor.click();
                       anchor.remove();
                       customunBlockUI();

                       // setTimeout(function(){
                       // $.unblockUI();
                       // }, 2500);
                   });

               }
                // Excel Function start here     
               function export_heatmap_data() {
                   var h_count = $('#hitmap_count').val();
                   if (h_count == 0) {
                       ShowAlret("No data found please select module.!!", 'error');
                       return false;
                   }
                   if (Company_id == "") {
                       ShowAlret("Please select Company first.!!", 'error');
                       return false;
                   }
                   $.confirm({
                       title: 'Confirm!',
                       content: "Are you sure want to Export Heat Map. ? ",
                       buttons: {
                           confirm: {
                               text: 'Confirm',
                               btnClass: 'btn-primary',
                               keys: ['enter', 'shift'],
                               action: function() {
                                   exportToExcel();
                               }
                           },
                           cancel: function() {
                               this.onClose();
                           }
                       }
                   });
               }

               function exportToExcel() {
                   var h_count = $('#hitmap_count').val();
                   if (h_count == "") {
                       ShowAlret("No data found please select module.!!", 'error');
                       return false;
                   }
                   var htmls = "";
                   var uri = 'data:application/vnd.ms-excel;base64,';
                   var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
                   var base64 = function(s) {
                       return window.btoa(unescape(encodeURIComponent(s)))
                   };
                   var format = function(s, c) {
                       return s.replace(/{(\w+)}/g, function(m, p) {
                           return c[p];
                       })
                   };
                   htmls = document.getElementById("append_table").innerHTML;

                   var ctx = {
                       worksheet: 'Worksheet',
                       table: htmls
                   }
                   const date = new Date();
                   var link = document.createElement("a");
                   link.download = "Heat Map " + date + ".xls";
                   link.href = uri + base64(format(template, ctx));
                   link.click();
               }
</script>