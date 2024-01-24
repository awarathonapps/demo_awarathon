<div id='container'>
        <!-- <div id='assessment_index_start' style='min-width: 100%; height: 300px; margin: 0 auto'></div> -->
        <!-- <div id='assessment_total_report' style='width: 519.6px; height:290px; position: absolute;top: 49px;left: 16px;'></div> -->
        <div id='assessment_total_report' style='width: 519.6px; height:290px; position: absolute;top: 49px;left: 1px;'></div>
        <!-- <div id='assessment_total_report' style='width: 520.6px; height: 290px;position: absolute;top: 49px; overflow: hidden;left: 0px;'></div> -->
</div>
    <?php 
    $report_title;
    $reporttitle=str_replace('"', "", $report_title);
    $reporttitle;
    ?>
<script>
    
    var indexData =<?php echo $index_dataset ;?> 
    var title= '<div style="font-size: 12px; color:#2A2E36; font-family: "Poppins",sans-serif; padding-bottom:30px"><?php echo $reporttitle; ?></div><br><div style="color:white">---</div>'
    $(document).ready(function () {   
        Highcharts.chart('assessment_total_report', {
            chart: {
                 type: 'area'
            },
            title: {
                text: title,
                align: 'left',
                // 'style': {
                //     'fontSize'      : '12px',
                //     'margin-bottom'    : '20px',
                //     'padding-bottom'  : '10px'
                // }
            },
            subtitle: {
                text: '<?php echo $count;?>',
                align: 'left',
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Arial'
                }
            },
            xAxis: {
                categories:<?php echo  $index_label; ?>,
                title: {
                    text: false
                }
            },  
            yAxis: {
                // gridLineColor: 'black',
                gridLineDashStyle: 'longdash',
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
                enabled: false,
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
                    name: 'Assessment',
                    data: indexData,
                    <?php (count((array)$index_label) > 10 ? '' : 'pointWidth: 28,')?>
//                    color: '#ffc000',
                    color: '#3d7deb', 

                    lineWidth: 3,
                    lineColor:'#fff',
                    
                    marker: {
                        lineWidth: 1.5,
                        radius: 5
                    },        
                         
                    dataLabels: {
                        style: {
                            fontWeight: 'normal',
                            textOutline: '0',
                            color: '#3d7deb',
                            'fontSize': '12px',
                        }
                    }
                }]     
        });        
    });
</script>