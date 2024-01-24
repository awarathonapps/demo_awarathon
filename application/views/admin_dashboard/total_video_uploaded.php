<div id='container'>
        <div id='total_video_uploaded' style='min-width: 100%; height: 300px;'></div>           
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
        Highcharts.chart('total_video_uploaded', {
            chart: {
                 type: 'area',
                 marginTop: 95
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
                text: <?php echo $count;?>,
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
                enabled: false,
                // align: 'center',
                // verticalAlign: 'top',
                // y: 40,
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
                    name: 'Total Videos Uploaded',
                    data: indexData,
                    "color" : "#ccb6fa",                      
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
                            color: '#ccb6fa',
                            'fontSize': '12px',
                        }
                    }
                }] ,
                exporting: {
                    csv: {
                        columnHeaderFormatter: function(item, key) {
                            if (!key) {
                                return 'Total videos uploaded Month'
                            }
                            return false
                        }
                    },
                    filename: 'Total videos uploaded ' + <?php echo $report_title ?> + '',
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