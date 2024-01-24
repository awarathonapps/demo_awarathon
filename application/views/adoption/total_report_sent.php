<?php
$stsum = json_decode($index_dataset);
$newlen = array_sum($stsum);

if ($newlen == 0) {
?>
    <!-- <style>
    .img-style
    {
    height: 60%;
    width: 50%;
    text-align: center;
    margin: auto;
    margin-left: 23%;
    margin-top: 2%;
    }
    .head-text{
    font-family: "Open Sans",sans-serif;
    font-size: 16px;
    font-weight: 600;
    line-height: 24px;
    color: #2A2E36;
    text-transform: inherit;
    margin-bottom: 8px;
    text-align: center;
    }
    .sub-head{
    font-family: "Open Sans",sans-serif;
    font-size: 12px;
    font-weight: 400;
    line-height: 16px;
    color: #2A2E36;
    text-transform: inherit;
    text-align: center;
    }
</style> -->
    <div id='container'>
        <div id='total_report_sent' style='min-width: 100%; height: 300px; background:white;text-align:center'>
            <img src="<?= base_url(); ?>assets/images/empty.jpeg" class="img-style" />
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
    <div id='container'>
        <div id='total_report_sent' style='min-width: 100%; height: 300px;'></div>
        <div class="clearfix"></div>
    </div>


    <script>
        var indexData = <?php echo $index_dataset; ?>;

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

            Highcharts.chart('total_report_sent', {
                chart: {
                    type: 'area',
                    marginTop: 95,
                    events: {
                        load: function() {
                            this.renderer.image('https://ai.awarathon.com/assets/images/poweredby-awarathon-logo.png', this.chartWidth / 2 - 24, this.chartHeight - 12, 80, 10)
                                .add();
                        }
                    }

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
                    text: '<?php echo json_decode($count); ?>',
                    align: 'left',
                    verticalAlign: 'top',
                    y: 45,
                    'style': {
                        'fontSize': '12px',
                        'fontFamily': 'Catamaran'
                    }
                },
                xAxis: {
                    title: {
                        text: 'Time period',
                        align: 'middle',
                        y: 10
                    },
                    categories: <?php echo $index_label; ?>,
                },
                yAxis: {
                    // gridLineColor: 'black',
                    gridLineDashStyle: 'longdash',
                    title: {
                        text: 'No. of reports',
                        align: 'middle',
                        y: 10,
                        // x: -30
                    },
                    labels: {
                        formatter: function() {
                            return this.value;
                        },
                        overflow: 'justify'
                    }
                },
                tooltip: {
                    valueSuffix: ''
                },

                legend: {
                    enabled: false,
                    align: 'center',
                    verticalAlign: 'top',
                    y: 40,
                },

                credits: {
                    enabled: false
                },
                series: [{
                    type: 'area',
                    name: 'Report Sent',
                    data: indexData,
                    color: '#7cb5ec',
                    lineWidth: 3,
                    lineColor: '#fff',

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
                            color: '#7cb5ec',
                            'fontSize': '12px',
                        }
                    }

                }],
                exporting: {
                    chartOptions: {
                        title: {
                            text: 'Report Sent',
                            align: 'left',
                            verticalAlign: 'top',
                            y: 10,
                            'style': {
                                'fontSize': '12px',
                                'fontFamily': 'Catamaran',
                                'color': 'black',
                            },
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    color: 'black'
                                }
                            }
                        },
                        subtitle: {
                            text: <?php echo $report_title ?>,
                            align: 'left',
                            verticalAlign: 'top',
                            y: 30,
                            'style': {
                                'fontSize': '10px',
                                'fontFamily': 'Catamaran',
                                'color': 'black',
                            },
                        },
                    },
                    csv: {
                        columnHeaderFormatter: function(item, key) {
                            if (!key) {
                                return 'Sent Report Month'
                            }
                            return false
                        }
                    },
                    filename: 'Report Sent ' + <?php echo $report_title ?> + '',
                    buttons: {
                        contextButton: {

                            symbol: 'download',
                            'stroke-width': 1,
                            symbolStroke: "#004369",
                            menuItems: ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS']
                        }
                    },
                    enableImages: true
                }
            });
        });
    </script>
<?php } ?>