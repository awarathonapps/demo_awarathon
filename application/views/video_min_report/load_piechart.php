<fildset id="assessminvideo<?php echo $company_id ?>">
    <div class="portlet light bordered">
        <div class="portlet-title ">
            <div class="caption">
                <i class="icon-bar-chart font-green-sharp"></i>
                <span class="caption-subject font-green-sharp ">Assessment wise</span>
                <span class="caption-helper"></span>
            </div>
            <div class="actions">
                <div class="btn-group">
                    <a class="btn btn-sm btn-default btn-circle" href="" class="btn" data-toggle="modal" onclick="getfiltermodal(<?php echo $company_id ?>,1);">
                        Filter By <i class="fa fa-angle-down"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="portlet-body chart " id="piechart_videomin<?php echo $company_id ?>">
        </div>
        <input type="hidden" name='company_id' id='company_id' value='<?php echo $company_id ?>'>
    </div>
</fildset>
<script>
 $(document).ready(function () { 
        var totalusers = <?php echo $total_user ?>;
        var utilized_minute = <?php echo $utilized_minute ?>;
        Highcharts.chart('piechart_videomin<?php echo $company_id ?>', {
        chart: {
          plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
          text: '<?php echo $assessment ?>',
          align: 'center',
          style: {
                fontSize:'13px'
           }
        },
        tooltip: {
//          pointFormat: '{series.name}: <b>({point.percentage:.2f}% , {point.u})</b>'
          formatter: function () {
                            return 'Total Minute :'+ Highcharts.numberFormat(utilized_minute[this.point.index],2)+ ' min <br/> Total Users:'+totalusers[this.point.index];
                        }
        },
        plotOptions: {
          pie: {
                allowPointSelect: true,
                cursor: 'pointer',   
            dataLabels: {
                enabled: true,
                distance: 1,
//                format: '({point.percentage:.2f}% ,{point.u})',
                    formatter: function () {
                            return Highcharts.numberFormat(utilized_minute[this.point.index],2)+' min';
                        },
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
					fontSize:'10px'
                }
            },
            showInLegend: true,
            startAngle: 0,
            endAngle: 360,
            size: '150px'
          }
        },
         credits: {
            enabled: false
        },
        exporting: { 
            enabled: true
        },
        series: [{
            type: 'pie',
            name: 'Score',
//            innerSize: '70%',
            data:<?php echo $dataset; ?>
          
        }]
      });
    });

</script>


