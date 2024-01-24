<div id='container'>
        <div id='parameter_index_graph' style='min-width: 310px; height: auto; margin: 0 auto'></div>
    </div>

<script>
    
    var indexData =<?php echo $index_paradataset ;?> 
    $(document).ready(function () {   
        Highcharts.chart('parameter_index_graph', {
            chart: {
                 type: 'line'
            },
            title: {
                text: '<?php echo $report_paratitle; ?>',
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Arial'
                }
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories:<?php echo  $index_paralabel; ?>,
                title: {
                    text: 'Parameters List'
                }
            },
            yAxis: {
                title: {
                    text: 'Accuracy',
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
                        format: '{point.y:.2f}'
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                    type: 'spline',                        
                    name: 'Parameter Accuracy',
                    data: indexData,
                    <?php (count((array)$index_paralabel) > 10 ? '' : 'pointWidth: 28,')?>
                    color: '#0070c0',
                    dataLabels: {
                        style: {
                            fontWeight: 'normal',
                            textOutline: '0',
                            color: 'black',
                            'fontSize': '12px',
                        }
                    }
                }]     
        });        
    });
</script>