<div id='container'>
    <div id='PlayedCompletedGraph' style='min-width: 100%; height: 300px;'></div>
    <div class="clearfix"></div>
</div>

<?php
$report_title;
$reporttitle = str_replace('"', "", $report_title);
$reporttitle;

?>

<script>
   var indexData = <?php echo $index_dataset ?>;
    var completed_dataset = <?php echo $completed_dataset ?>;

    $(document).ready(function() {
        Highcharts.SVGRenderer.prototype.symbols.download = function(x, y, w, h) {
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
        Highcharts.chart('PlayedCompletedGraph', {
            chart: {
                type: 'line',
                marginTop: 99
            },
            title: {
                text: <?php echo $report_title; ?>,
                align: 'left',
                verticalAlign: 'top',
                y: 10,
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Catamaran',

                }
            },
            subtitle: {
                text: '',
                align: 'left',
                verticalAlign: 'top',
                y: 45,
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Arial'
                }
            },
            xAxis: {
                categories: <?php echo $index_label; ?>,
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
                    formatter: function() {
                        return this.value + '';
                    },
                    overflow: 'justify'

                }
            },
            tooltip: {
                valueSuffix: ''
            },
            legend: {
                enabled: true
            },
            plotOptions: {
                series: {
                        stacking: 'normal'
                    }
            },
            credits: {
                enabled: false
            },
            series: [{
                type: 'line',
                name: 'No. of Reps Played',
                data: indexData,
                color: '#e6e335',
                marker: {
                    lineWidth: 1.5,
                    radius: 5
                },
                dataLabels: {
                    style: {
                        fontWeight: 'normal',
                        textOutline: '0',
                        color: '#e6e335',
                        'fontSize': '12px',
                    }
                }
            }, {
                name: 'No. of Reps Completed',
                data: completed_dataset,
                color: '#b3ffe9'
            }],
            responsive: {
                rules: [{
                    condition: {
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'top',
                            y: 40,
                        }
                    }
                }]
            },
            exporting: {
                csv: {
                    columnHeaderFormatter: function(item, key) {
                        if (!key) {
                            return 'Month'
                        }
                        return false
                    }
                },
                filename: 'No of Raps played and Completed ' + <?php echo $report_title ?> + '',
                buttons: {
                    contextButton: {
                        symbol: 'download',
                        symbolStroke: "#004369",
                        menuItems: ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS']
                    }
                }
            }
        });
    });
</script>