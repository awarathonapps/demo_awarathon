<div id='container'>
    <div id='AIr_grpah' style='min-width: 100%; height: 300px;'></div>
</div>
<?php
$report_title;
$reporttitle = str_replace('"', "", $report_title);
$reporttitle;

?>

<script>
    var total_user = <?php echo $Total_User; ?>;
    var active_user = <?php echo $Active_User; ?>;
    var inactive_user = <?php echo $Inactive_User; ?>;

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
        Highcharts.chart('AIr_grpah', {
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
                text: 'demo test',
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
                // bar: {
                //     dataLabels: {
                //         enabled: true,
                //         format: '{point.y:.2f}'
                //     }
                // }
                series: {
                        stacking: 'normal'
                    }
            },
            credits: {
                enabled: false
            },
            series: [{
                type: 'line',
                name: 'Total Users',
                data: total_user,
                <?php count((array) $index_label) > 10 ? '' : 'pointWidth: 28,'; ?>
                color: '#dbc3c3',
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
            }, {
                name: 'Active Users',
                data: active_user,
                color: 'green'
            }, {
                name: 'Inactive Users',
                data: inactive_user,
                color: 'skyblue'
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
                            return 'Assement Month'
                        }
                        return false
                    }
                },
                filename: 'Assesment Users ' + <?php echo $report_title ?> + '',
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