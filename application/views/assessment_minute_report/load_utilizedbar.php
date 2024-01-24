<div id='container'>
        <div id='min_graph' style='min-width: 310px; height: auto; margin: 0 auto'></div>
</div>
<script>    
   
    
    $(document).ready(function () {    
    var indexdata = <?php echo $dataset ?>;
    var indexlabel = <?php echo $datalabel ?>; 
    Highcharts.chart('min_graph', {
            chart: {
                type: 'bar'
            },
            title: {
            text: 'Total Utilization',
                    'style': {
                        'fontSize': '12px',
                        'fontFamily': 'Arial'
                    }
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories:<?php echo $datalabel ?>,
                title: {
                    text: ''
                }
            },
            yAxis: {
                title: {
                        text: 'Minutes',
                        align: 'middle',
            },
            labels: {
                formatter: function () {
                return this.value;
                },
                overflow: 'justify'
            }
            },
            tooltip: {
//                valueSuffix: 'min'
                 formatter: function () {
                            return 'Total Minute :'+ Highcharts.numberFormat(this.y,2)+ ' min ';
                        }
            },
            legend: {
                reversed: true
            },
            plotOptions: {
		series: {
                    stacking: 'normal'
                },
                bar: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.2f}'+' min'
                        }
                    }
            },
            credits: {
                enabled: false
            },
           series: [{
                        name: 'Awarathon Minutes',
                        data: <?php echo $dataset; ?>,
                        color:'#4f81bd',
                        pointWidth: 30
                    }, {
                        name: 'Utilized Minutes',
                        data: <?php echo $dataset1; ?>,
                        color:'#c0504d',
                        pointWidth: 30
                    }
                ]
        });
    });
</script>                              
