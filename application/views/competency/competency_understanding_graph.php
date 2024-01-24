<?php
$stsum = json_decode($index_dataset);
$newlen = array_sum($stsum);

if ($newlen == 0) {
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
            font-family: "Catamaran";
            font-size: 16px;
            font-weight: 600;
            line-height: 24px;
            color: #2A2E36;
            text-transform: inherit;
            margin-bottom: 8px;
            text-align: center;
        }

        .sub-head {
            font-family: "Catamaran";
            font-size: 12px;
            font-weight: 400;
            line-height: 16px;
            color: #2A2E36;
            text-transform: inherit;
            text-align: center;
        }
    </style>
    <div id='container'>
        <div id='competency_understanding_graph' style='min-width: 100%; height: 300px; background:white;'>
            <img src="<?= base_url(); ?>assets/images/empty.jpeg" class="img-style" />
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
    <div id='competency_understanding_graph' style='min-width: 100%; height: 300px;'></div>
    <div id='competency_understanding_graph'></div>
    <div class="clearfix"></div>
    </div>
    <script>
        var indexData = <?php echo $index_dataset; ?>;
        var ucount = <?php echo $ucount; ?>;
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

            Highcharts.chart('competency_understanding_graph', {
                chart: {
                    type: 'bar',
                    marginTop: 50,
                    height: 300,
                    spacingBottom: 25,
                    events: {
                        load: function() {
                            this.renderer.image('<?= base_url(); ?>assets/images/poweredby-awarathon-logo.png', this.chartWidth / 2 - 24, this.chartHeight - 16, 80, 10).add();
                        }
                    }

                },
                title: {
                    text: 'Competency categorization',
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
                        text: 'categories'
                    },
                    min: 0,
                    max: (ucount > '11' ? '10' : ucount - 1),
                    scrollbar: {
                        enabled: (ucount > '10' ? true : false)
                    },
                    labels: {
                        formatter: function() {
                            if (typeof this.value !== 'number') {
                                return this.value;
                            }
                        }
                    },
                    events: {
                        afterSetExtremes: function() {
                            var xAxis = this,
                                numberOfPoints = xAxis.series[0].points.length - 1,
                                minRangeValue = xAxis.getExtremes().min,
                                maxRangeValue = xAxis.getExtremes().max;

                            if (minRangeValue < 0) {
                                xAxis.setExtremes(null, xAxis.options.max);
                            } else if (maxRangeValue > numberOfPoints) {
                                xAxis.setExtremes(numberOfPoints - xAxis.options.max, numberOfPoints);
                            }
                        }
                    }

                },
                yAxis: {
                    allowDecimals: false,
                    title: {
                        text: 'No.of reps',
                        align: 'middle',
                        y: 10,
                        'style': {
                            'fontSize': '12px',
                            'fontFamily': 'Catamaran',
                        },
                    }
                },
                tooltip: {
                    valueSuffix: ''
                },
                legend: {
                    enabled: false,
                },
                // credits: {
                //   text: 'Powered by Awarathon',
                //   href: '',
                //   }
                credits: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        pointWidth: 10 //width of the column bars irrespective of the chart size
                    }
                },
                series: [{
                    type: 'bar',
                    name: 'user',
                    data: <?php echo isset($index_dataset) ? $index_dataset : 0; ?>,
                    color: '#E5DDC8',
                }],
                exporting: {
                    chartOptions: {
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        subtitle: {
                            text: 'Competency categorization',
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
                                text: 'No.of reps',
                                align: 'middle',
                                y: -2,
                            }
                        },
                    },

                    csv: {
                        columnHeaderFormatter: function(item, key) {
                            if (!key) {
                                return 'Percentage'
                            }
                            return false
                        }

                    },
                    filename: 'Competency categorization ',

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