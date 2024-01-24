<?php
$range = json_decode($range_list);
$stsum2 = json_decode($less_than_range);
$stsum3 = json_decode($second_range);
$stsum4 = json_decode($third_range);
$stsum5 = json_decode($above_range_final);
$newlen2 = array_sum($stsum2);
$newlen3 = array_sum($stsum3);
$newlen4 = array_sum($stsum4);
$newlen5 = array_sum($stsum5);

if ($newlen2 == 0 && $newlen3 == 0 && $newlen4 == 0 && $newlen5 == 0) {
?>
    <style>
        .img-style {
            height: 60%;
            width: 50%;
            text-align: center;
            margin: auto;
            margin-left: 23%;
            margin-top: 2%;
        }

        .head-text {
            font-family: 'Catamaran';
            font-size: 16px;
            font-weight: 600;
            line-height: 24px;
            color: #2A2E36;
            text-transform: inherit;
            margin-bottom: 8px;
            text-align: center;
        }

        .sub-head {
            font-family: 'Catamaran';
            font-size: 12px;
            font-weight: 400;
            line-height: 16px;
            color: #2A2E36;
            text-transform: inherit;
            text-align: center;
        }
    </style>
    <div id='container'>
        <div id='region_performance' style='min-width: 100%; height: 300px; background:white;'>
            <img src="<?= base_url(); ?>assets/images/empty.jpeg" class="img-style" />
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
    <div id='region_performance' style='min-width: 100%; height: 300px;'></div>
    <div id='region_performance'></div>
    <div class="clearfix"></div>
    </div>
    <script>
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
            Highcharts.chart('region_performance', {
                    chart: {
                        type: 'bar',
                        marginTop: 80,
                        height: 300,
                        spacingBottom: 25,
                        events: {
                            load: function() {
                                this.renderer.image('<?= base_url(); ?>assets/images/poweredby-awarathon-logo.png', this.chartWidth / 2 - 24, this.chartHeight - 16, 80, 10).add();
                            }
                        }
                        // height: 300,
                    },
                    title: {
                        text: 'Region wise performance',
                        align: 'left',
                        verticalAlign: 'top',
                        y: 10,
                        'style': {
                            'fontSize': '12px',
                            'fontFamily': 'Catamaran',
                            'display': 'none',
                        }
                    },
                    subtitle: {
                        text: '',
                        align: 'left',
                        verticalAlign: 'top',
                        y: 10,
                        'style': {
                            'fontSize': '12px',
                            'fontFamily': 'Catamaran',
                        },
                    },
                    xAxis: {
                        categories: <?php echo $index_label; ?>,
                        title: {
                            text: false
                        },
                        overflow: 'justify'
                    },
                    yAxis: {
                        title: {
                            text: <?php echo $report_title; ?>,
                            align: 'high',
                            y: 10,
                            'style': {
                                'fontSize': '12px',
                                'fontFamily': 'Catamaran',
                            },
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
                        align: 'center',
                        layout: 'horizontal',
                        enabled: true,
                        itemMarginTop: 0,
                        verticalAlign: 'top',
                    },
                    credits: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            stacking: 'normal',
                            pointWidth: 15
                        }
                    },
                    series: [{
                        name: '<?php echo $range[3]; ?>',
                        data: <?php echo $above_range_final; ?>,
                        color: '#6ddee3',
                        dataLabels: {
                            style: {
                                fontWeight: 'normal',

                            }
                        }
                    }, {
                        name: '<?php echo $range[2]; ?>',
                        data: <?php echo $third_range; ?>,
                        color: '#E5DDC8',
                        dataLabels: {
                            style: {
                                fontWeight: 'normal',

                            }
                        }
                    }, {
                        name: '<?php echo $range[1]; ?>',
                        data: <?php echo $second_range; ?>,
                        color: '#7cb5ec',
                        dataLabels: {
                            style: {
                                fontWeight: 'normal',


                            }
                        }
                    }, {
                        name: '<?php echo $range[0]; ?>',
                        data: <?php echo $less_than_range; ?>,
                        color: '#dbc3c3',
                        dataLabels: {
                            style: {
                                fontWeight: 'normal',


                            }
                        }
                    }],
                    exporting: {
                        chartOptions: {
                            subtitle: {
                                text: 'Region wise performance',
                                align: 'left',
                                verticalAlign: 'top',
                                y: 10,
                                'style': {
                                    'fontSize': '12px',
                                    'fontFamily': 'Catamaran',
                                    'color': 'black',
                                },
                            },
                            yAxis: {
                            title: {
                                text: <?php echo $report_title; ?>,
                                align: 'high',
                                y: 2,
                                'style': {
                                    'fontSize': '10px',
                                    'fontFamily': 'Catamaran',
                                    'color': 'black',
                                },
                            },
                        }
                        },
                       
                        csv: {
                            columnHeaderFormatter: function(item, key) {
                                if (!key) {
                                    return 'Region Name'
                                }
                                return false
                            }

                        },
                        filename: 'Region wise Performance '+<?php echo $report_title; ?>+'',

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
                }

            );

        });
    </script>
<?php } ?>