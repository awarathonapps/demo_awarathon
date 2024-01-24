<?php $base_url = base_url();    
?>
 <div id='region_graph' style='border:1px solid #d4d4d4;padding: 5px; height: 300px;'></div>
<script>    
   
    //var Lable = < ?php echo $label; ?>;
    $(document).ready(function () {    
        Highcharts.chart('region_graph', {
        chart: {
          plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
          text: 'Overall Score',
          align: 'center'
        },
        tooltip: {
          pointFormat: '{series.name}: <b>({point.percentage:.2f}% , {point.u})</b>'
        },
        plotOptions: {
          pie: {
                allowPointSelect: true,
                cursor: 'pointer',   
            dataLabels: {
                enabled: true,
                distance: 1,
                format: '({point.percentage:.2f}% ,{point.u})',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
					fontSize:'11px'
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
            innerSize: '70%',
            data:<?php echo $dataset; ?>
          
        }]
      });
    });
</script>                              
