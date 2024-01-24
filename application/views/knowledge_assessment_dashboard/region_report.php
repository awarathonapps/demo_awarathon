<?php $base_url = base_url();
?>
<div class='col-lg-6' id='ChartDiv' style='border:1px solid #d4d4d4;padding: 5px;'>    
    <div id='container' style="max-height:370px; overflow-y:auto; ">
        <div id='region_graph' style='min-width: 310px; height: <?php echo ($totallabel>7 ? '600px':'400px') ?>; margin: 0 auto'></div>
    </div>
</div>
<script>    
   
    var Lable = <?php echo $label; ?>;
    $(document).ready(function () {    
        
    Highcharts.chart('region_graph', {
            chart: {
                type: 'bar'
            },
            title: {
            text: 'Region wise Performances',
                    'style': {
                        'fontSize': '12px',
                        'fontFamily': 'Arial'
                    }
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories:<?php echo $label; ?>,
                title: {
                    text: 'Region'
                }
        
            },
            yAxis: {
                title: {
                        text: 'Region Wise C.E.',
                        align: 'middle',
            },
            labels: {
                formatter: function () {
                return this.value + '%';
                },
                overflow: 'justify'
            }
            },
            tooltip: {
            valueSuffix: '%'
            },
            legend: {
            enabled: true
            },
			
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.2f}%',
                        allowOverlap: true,
                        crop: false,
                        style: {
                            fontWeight: 'normal',
                            textOutline: '0',
                            color:'black',
                            fontSize: '10px',
                        }
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                        name: 'Life Time',
                        data: <?php echo $dataset; ?>,
                        color:'#00cc33'
                    }, {
                        name: 'Last Month',
                        data: <?php echo $dataset1; ?>,
                        color:'#01cdfe'
                    }
                ]
        });
    });
</script>                              
