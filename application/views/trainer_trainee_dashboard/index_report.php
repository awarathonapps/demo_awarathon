<div id='container'>
        <div id='trainee_index_graph' style='min-width: 310px; height: auto; margin: 0 auto'></div>
    </div>

<script>    
    var graph_type = '<?php echo ($graphtype_id == '' ? 1 : 1 );?>'        
    var indexData =<?php echo $index_dataset ;?> 
           
    $(document).ready(function () {   
        Highcharts.chart('trainee_index_graph', {
            chart: {
                type: (graph_type == 1 ? 'spline' : graph_type == 2 ? 'column' : '')
            },
            title: {
                text: '<?php echo $report_title; ?>',
                'style': {
                    'fontSize'  : '12px',
                    'fontFamily': 'Arial'
                }
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories:<?php echo  $index_label; ?>,
                title: {
                    text: '<?php echo  $rpt_period; ?>'
                }
            },
            yAxis: {
                title: {
                    text: 'Time Period',
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
                column: {
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
                    type: (graph_type == 3 ? 'column' :''),
                    name: 'Post Competency',
                    data: indexData,
                    color:'#00FFFF',
                    <?php (count((array)$index_label) > 10 ? '' : 'pointWidth: 28,')?>                 
                }]        
        });        
    });
</script>