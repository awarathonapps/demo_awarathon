<?php $base_url = base_url();    
?>
<div id='piechart_videomin' style='padding: 5px; height: 250px;'></div>
<script>
 
 $(document).ready(function () { 
        var utilized_minute = <?php echo $utilized_minute ?>;
        Highcharts.chart('piechart_videomin', {
        chart: {
          plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
          text: '<strong>Total Utilization<strong>',
          align: 'center',
          style: {
                fontSize:'13px'
           }
        },
        tooltip: {
//          pointFormat: '{series.name}: <b>({point.percentage:.2f}% , {point.u})</b>'
          formatter: function () {
                            return 'Total Minute :'+ Highcharts.numberFormat(utilized_minute[this.point.index],2)+ ' min';
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
                            return Highcharts.numberFormat(utilized_minute[this.point.index],2)+'min';
                        },
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
					fontSize:'10px',
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


