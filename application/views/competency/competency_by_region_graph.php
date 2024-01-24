<?php
$stsum = array();
$stsum = json_decode($index_dataset);
if ($stsum == 0) {
    $newlen = 0;
} else {
    $newlen = array_sum($stsum);
}

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
        <div id='competency_by_region' style='min-width: 100%; height: 300px; background:white;'>
            <img src="<?= base_url(); ?>assets/images/empty.jpeg" class="img-style" />
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
    <div id='competency_by_region' style='min-width: 100%; height: 300px;'></div>
    <div id='competency_by_region'></div>
    <div class="clearfix"></div>
    </div>
    <script>
        var indexLabel = <?php echo $index_label; ?>;
        var Dcount = <?php echo $div_count; ?>;
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
            (function(H) {

                //internal functions
                function stopEvent(e) {
                    if (e) {
                        if (e.preventDefault) {
                            e.preventDefault();
                        }
                        if (e.stopPropagation) {
                            e.stopPropagation();
                        }
                        e.cancelBubble = true;
                    }
                }

                //the wrap
                H.wrap(H.Chart.prototype, 'render', function(proceed) {
                    var chart = this,
                        mapNavigation = chart.options.mapNavigation;

                    proceed.call(chart);

                    // Add the mousewheel event
                    H.addEvent(chart.container, document.onmousewheel === undefined ? 'DOMMouseScroll' : 'mousewheel', function(event) {

                        var delta, extr, step, newMin, newMax, axis = chart.xAxis[0];

                        e = chart.pointer.normalize(event);
                        // Firefox uses e.detail, WebKit and IE uses wheelDelta
                        delta = e.detail || -(e.wheelDelta / 50);
                        delta = delta < 0 ? -1 : 1;

                        if (chart.isInsidePlot(e.chartX - chart.plotLeft, e.chartY - chart.plotTop)) {
                            extr = axis.getExtremes();
                            step = (extr.max - extr.min) / 5 * delta;
                            axis.setExtremes(extr.min + step, extr.max + step, true, false);
                        }

                        stopEvent(event); // Issue #5011, returning false from non-jQuery event does not prevent default
                        return false;
                    });
                });
            }(Highcharts));

            Highcharts.chart('competency_by_region', {
                chart: {
                    height: 300,
                    type: 'bar',
                    marginBottom: 80,
                    marginTop: 50,
                    events: {
                        load: function() {
                            this.renderer.image('<?= base_url(); ?>/assets/images/poweredby-awarathon-logo.png', this.chartWidth / 2 - 24, this.chartHeight - 16, 80, 10).add();
                        }
                    }
                },
                title: {
                    text: ' ',
                    align: 'center',
                    verticalAlign: 'top',
                    y: 10,
                    'style': {
                        'fontSize': '24px',
                        'fontFamily': 'Catamaran',
                        // 'display': 'none',
                    }
                },
                subtitle: {
                    text: '',
                    align: 'center',
                    verticalAlign: 'top',
                    y: 30,
                    'style': {
                        'fontSize': '12px',
                        'fontFamily': 'Arial',
                    }
                },
                xAxis: {
                    title: {
                        text: 'Region',
                        align: 'middle',
                        y: 10
                    },
                    min: 0,
                    // max: 4,
                    max: (Dcount > '5' ? '4' : Dcount - 1),
                    scrollbar: {
                        enabled: (Dcount > '5' ? true : false)
                    },
                    categories: <?php echo $index_label; ?>,
                    // tickLength: 0,
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
                    title: {
                        text: '% Scores',
                        align: 'middle',
                        y: 8
                    },
                    labels: {
                        overflow: 'justify'
                    },
                    tickInterval: 10
                },
                tooltip: {
                    valueSuffix: ' %'
                },
                plotOptions: {
                    // bar: {
                    //     dataLabels: {
                    //         enabled: true
                    //     },
                    //     pointWidth: 10

                    // }
                    series: {
                        pointWidth: 10,
                        dataLabels: {
                            format: '{y} %',
                            enabled: true,
                        }
                    }
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'top',
                    itemMarginTop: -30,
                    x: 20,
                    y: 30,
                    floating: true,
                    borderWidth: 0,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                    shadow: false,
                    textOverflow: "ellipsis",
                    overflow: "hidden",
                    whiteSpace: "nowrap",
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                series: [{
                    type: 'bar',
                    name: 'Average',
                    data: <?php echo $index_dataset; ?>,
                    color: '#7cb5ec',
                }],
                // responsive: {
                //     rules: [{
                //         condition: {},
                //         chartOptions: {
                //             legend: {
                //                 layout: 'horizontal',
                //                 align: 'center',
                //                 verticalAlign: 'top',
                //                 y: 40,
                //             }
                //         }
                //     }]
                // },
                exporting: {
                    chartOptions: {
                        title: {
                            text: 'Competency by Region (Overall)',
                            align: 'left',
                            verticalAlign: 'top',
                            y: 4,
                            'style': {
                                'fontSize': '12px',
                                'fontFamily': 'Catamaran',
                                'color': 'black',

                            },
                        },

                        legend: {
                            enabled: false,
                            itemMarginTop: 0,
                            verticalAlign: 'top',
                            itemMarginTop: -26,
                            y: 28,
                        },
                    },
                    csv: {
                        columnHeaderFormatter: function(item, key) {
                            if (!key) {
                                return 'Region Name'
                            }
                            return false
                        }
                    },
                    filename: 'Competency by Region (Overall)',
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
<?php } ?>