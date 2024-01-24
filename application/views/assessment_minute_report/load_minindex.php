<div id='container'>
        <div id='statistics_minchart' style='min-width: 310px; height: auto; margin: 0 auto'></div>
</div>    
<script>
 $(document).ready(function () {   
 var indexdata = <?php echo $index_data ?>;
 var indexlabel = <?php echo $index_label ?>;
        Highcharts.chart('statistics_minchart', {
            chart: {
                 type: 'spline'
            },
            title: {
                text: '<?php echo $report_title ?>',
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Arial'
                }
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories:indexlabel,
                title: {
                    text: '<?php echo $period_title ?>'
                }
            },
            yAxis: {
                title: {
                    text: 'Minute',
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
                    name: 'Assessment Period',
                    data: indexdata,
                    <?php (count($index_label) > 10 ? '' : 'pointWidth: 28,')?>
                 
                    color: '#4f81bd',
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


