<div id='container'>
        <div id='assesmnet_user_count_report' style='min-width: 310px; height: auto; margin: 0 auto'></div>
    </div>

<script>
    
    var indexData =<?php echo $index_dataset ;?> 
    $(document).ready(function () {   
        Highcharts.chart('assesmnet_user_count_report', {
            chart: {
                 type: 'area'
            },
            title: {
                text: '<div style="color:white">---</div>',
                // 'style': {
                //     'fontSize': '12px',
                //     'fontFamily': 'Arial'
                // }
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories:<?php echo  $index_label; ?>,
                title: {
                    text: false
                }
            },
            yAxis: {
                title: {
                    text: false,
                    align: 'middle',
                },
                labels: {
                    formatter: function () {
                        return this.value + '';
                    },
                    overflow: 'justify'

                }
            },
            tooltip: {
                valueSuffix: ''
            },
            legend: {
                enabled: false
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
                    type: 'area',                        
                    name: 'Assessment Wise Users',
                    data: indexData,
                    <?php (count((array)$index_label) > 10 ? '' : 'pointWidth: 28,')?>
//                    color: '#ffc000',
                    color: '#b497f7',  

                    lineWidth: 3,
                    lineColor:'#fff',
                    
                    marker: {
                        lineWidth: 1.5,
                        radius: 5,
                        hover: {
                                lineWidthPlus: 1.5,
                                radiusPlus: 2
                            }
                    },

                    dataLabels: {
                        style: {
                            fontWeight: 'normal',
                            textOutline: '0',
                            color: '#b497f7',
                            'fontSize': '12px',
                        }
                    }
                }]     
        });        
    });
</script>