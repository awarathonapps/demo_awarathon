<div id='container'>
        <!-- <div id='assessment_index_end' style='min-width: 100%; height: 300px; margin: 0 auto'></div>     -->
        <div id='assessment_index_end' style='min-width: 100%; height: 300px;'></div>    
        <!-- <div id='assessment_index_end' style='width: 100%;height: 290px;position: absolute;top: 49px;left: 1px;'></div>         -->
      
</div>

    <?php
    $report_title;
    $reporttitle = str_replace('"', "", $report_title);
    $reporttitle;
  
   ?>
<script>
    var indexData =<?php echo $index_dataset; ?> 
    $(document).ready(function () {   
        Highcharts.SVGRenderer.prototype.symbols.download = function (x, y, w, h) {
            var path = [
                // Arrow stem
                'M', x + w * 0.5, y,
                'L', x + w * 0.5, y + h * 0.7,
                // Arrow head
                'M', x + w * 0.3, y + h * 0.5,
                'L', x + w * 0.5, y + h * 0.7,
                'L', x + w * 0.7, y + h * 0.5,
                // Box
                'M', x, y + h * 0.9,
                'L', x, y + h,
                'L', x + w, y + h,
                'L', x + w, y + h * 0.9
            ];
            return path;
        };
        Highcharts.chart('assessment_index_end', {
            chart: {
                 type: 'area',
                 marginTop: 80
            },
            title: {
                text: <?php echo $report_title;?>,
                align: 'left',
                verticalAlign: 'top',
                y: 10,
                'style': {
                'fontSize': '12px',
                'fontFamily': 'Catamaran',
                   
                }
            },
            subtitle: {
                text: '<?php  echo json_decode($count); ?>',
                align: 'left',
                verticalAlign: 'top',
                y: 45,
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Arial'
                }
            },
            xAxis: {
                categories:<?php echo $index_label; ?>,
                title: {
                    text: false
                }
            },
            yAxis: {
                // gridLineColor: 'black',
                gridLineDashStyle: 'longdash',
                title: {
                    text: '',
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
                    // lineThickness: 20,                   
                    name: 'Assessment',
                    data: indexData,
                    <?php count((array) $index_label) > 10 ? '' : 'pointWidth: 28,'; ?>
//                    color: '#ffc000',
                    color: '#dbc3c3',  
                    
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
                            color: '#dbc3c3',
                            'fontSize': '12px',
                        }
                    }
                }] ,
                exporting: {
                    csv: {
                        columnHeaderFormatter: function(item, key) {
                            if (!key) {
                                return 'Assement Month'
                            }
                            return false
                        }
                    },
                    filename: 'Assesment Completed ' + <?php echo $report_title ?> + '',
                    buttons: {
                        contextButton: {
                            symbol: 'download',
                            symbolStroke: "#004369",
                            menuItems: ['printChart','downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV','downloadXLS']
                            }
                    }
                }    
        });        
    });
</script>