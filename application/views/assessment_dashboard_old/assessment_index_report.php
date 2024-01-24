<div id='container'>
        <div id='assessment_index_graph' style='min-width: 310px; height: auto; margin: 0 auto'></div>
    </div>

<script>
    
    var indexData =<?php echo $index_dataset ;?> 
    $(document).ready(function () {   
        Highcharts.chart('assessment_index_graph', {
            chart: {
                 type: 'spline'
            },
            title: {
                text: '<?php echo $report_title; ?>',
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Arial'
                }
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories:<?php echo  $index_label; ?>,
                title: {
                    text: '<?php echo $report_period ; ?>'
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
                    name: 'Assessment Accuracy',
                    data: indexData,
                    <?php (count($index_label) > 10 ? '' : 'pointWidth: 28,')?>
//                    color: '#ffc000',
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