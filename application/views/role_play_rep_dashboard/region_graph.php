
<div class='col-md-4'  id='region_graph<?php echo $rg_id ?>' style='border:1px solid #d4d4d4; height: 250px; margin: 0 auto'>    
</div>

<script>    
   
    //var Lable = < ?php echo $label; ?>;
    $(document).ready(function () {    
        Highcharts.chart('region_graph<?php echo $rg_id ?>', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
        },
        title: {
          text: <?php echo $region_name?>,
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
          name: 'Percentage',
          colorByPoint: true,
          innerSize: '60%',
          data:<?php echo $rdataset; ?>,
        }]
      });
    });
</script>                              
