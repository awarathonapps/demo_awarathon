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
        var Ncount = <?php echo $Ncount; ?>;
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
            Highcharts.wrap(Highcharts.Legend.prototype, 'renderTitle', function() {
                var options = this.options,
                    padding = this.padding,
                    titleOptions = options.title,
                    titleHeight = 0,
                    bBox;

                if (titleOptions.text) {
                    if (!this.title) {
                        this.title = this.chart.renderer.label(titleOptions.text, padding + 135, padding - 4, null, null, null, null, null, 'legend-title')
                            .attr({
                                zIndex: 1
                            })
                            .css(titleOptions.style)
                            .add(this.group);
                    }
                    bBox = this.title.getBBox();
                    titleHeight = bBox.height;
                    this.offsetWidth = bBox.width; // #1717
                    this.contentGroup.attr({
                        translateY: titleHeight
                    });
                }
                this.titleHeight = titleHeight;
            });
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
                    },
                    title: {
                        text: 'Region wise performance as per the industry thresholds',
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
                        allowDecimals: false,
                        categories: <?php echo $index_label; ?>,
                        title: {
                            text: 'Region'
                        },
                        overflow: 'justify',
                        min: 0,
                        max: (Ncount > '5' ? '4' : Ncount - 1),
                        scrollbar: {
                            enabled: (Ncount > '5' ? true : false)
                        }
                        // ,
                        // labels: {
                        //     formatter: function() {
                        //         if (typeof this.value !== 'number') {
                        //             return this.value;
                        //         }
                        //     }
                        // }
                        ,
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
                            text: 'No. of reps',
                            align: 'middle',
                            y: 10,
                            'style': {
                                'fontSize': '12px',
                                'fontFamily': 'Catamaran',
                            },
                        }
                        // ,
                        // labels: {
                        //     formatter: function() {
                        //         return this.value * 10;
                        //     },
                        //     overflow: 'justify'
                        // }
                    },
                    tooltip: {
                        valueSuffix: ''
                    },
                    legend: {
                        title: {
                            text: 'Industry Thresholds',
                            // x: 100,
                            // y: -200,
                            style: {
                                fontStyle: 'italic'
                            }
                        },
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
                        color: '#a6f7c5',
                        dataLabels: {
                            style: {
                                fontWeight: 'normal',

                            }
                        }
                    }, {
                        name: '<?php echo $range[2]; ?>',
                        data: <?php echo $third_range; ?>,
                        color: '#fff27d',
                        dataLabels: {
                            style: {
                                fontWeight: 'normal',

                            }
                        }
                    }, {
                        name: '<?php echo $range[1]; ?>',
                        data: <?php echo $second_range; ?>,
                        color: '#fab2af',
                        dataLabels: {
                            style: {
                                fontWeight: 'normal',


                            }
                        }
                    }, {
                        name: '<?php echo $range[0]; ?>',
                        data: <?php echo $less_than_range; ?>,
                        color: '#ff8288',
                        dataLabels: {
                            style: {
                                fontWeight: 'normal',


                            }
                        }
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
                                text: 'Region wise performance as per the industry thresholds',
                                align: 'left',
                                verticalAlign: 'top',
                                y: 10,
                                'style': {
                                    'fontSize': '12px',
                                    'fontFamily': 'Catamaran',
                                    'color': 'black',
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
                        filename: 'Region wise performance as per the industry thresholds ',

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