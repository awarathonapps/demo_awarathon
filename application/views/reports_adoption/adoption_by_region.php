<?php
$stsum = json_decode($rigion_start_count);
$comsum = json_decode($region_completed_count);
$newlen = array_sum($stsum);
$newlen1 = array_sum($comsum);

if ($newlen == 0 && $newlen1 == 0) {
?>
    <!-- <style>
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
    </style> -->
    <div id='container'>
        <div id='ad_by_region_overall' style='min-width: 100%; height: 350px; background:white;text-align:center'>
            <img src="<?= base_url(); ?>/assets/images/empty.jpeg" class="img-style" />
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
    <div id='container'>
        <div id='ad_by_region_overall' style='min-width: 100%; height: 350px;'></div>
        <div class="clearfix"></div>
    </div>
    <?php
    $rg_count = sizeof(json_decode($region_name_count));
    ?>
    <script>
        var rigion_start_count = <?php echo $rigion_start_count; ?>;
        var region_name_count = <?php echo $region_name_count; ?>;
        var region_completed_count = <?php echo $region_completed_count ?>;
        var started_count = <?php echo $started_count; ?>;
        var completed_count = <?php echo $completed_count; ?>;
        var user_mapped_count = <?php echo $user_mapped_count; ?>;
        var rg_count = <?php echo $rg_count; ?>;

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
                    if (this.renderTo.id == 'ad_by_region_overall') {

                        // Add the mousewheel event
                        H.addEvent(chart.container, document.onmousewheel === undefined ? 'DOMMouseScroll' : 'mousewheel', function(event) {

                            var delta, extr, step, newMin, newMax, axis = chart.xAxis[0];

                            e = chart.pointer.normalize(event);
                            // Firefox uses e.detail, WebKit and IE uses wheelDelta
                            delta = e.detail || -(e.wheelDelta / 120);
                            delta = delta < 0 ? -1 : 1;

                            if (chart.isInsidePlot(e.chartX - chart.plotLeft, e.chartY - chart.plotTop)) {
                                extr = axis.getExtremes();
                                step = (extr.max - extr.min) / 5 * delta;
                                axis.setExtremes(extr.min + step, extr.max + step, true, false);
                            }

                            stopEvent(event); // Issue #5011, returning false from non-jQuery event does not prevent default
                            return false;
                        });
                    }
                });
            }(Highcharts));
            Highcharts.chart('ad_by_region_overall', {
                chart: {
                    type: 'bar',
                    height: 350,
                    // marginLeft: 100,
                    marginBottom: 80,
                    marginTop: 50,
                    // spacingBottom: 25,

                    events: {
                        load: function() {
                            this.renderer.image('https://ai.awarathon.com/assets/images/poweredby-awarathon-logo.png', this.chartWidth / 2 - 24, this.chartHeight - 16, 80, 10).add();
                        }
                    }
                },
                title: {
                    text: '',
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
                    align: 'right',
                    verticalAlign: 'bottom',
                    y: -16,
                    'style': {
                        'fontSize': '12px',
                        'fontFamily': 'Catamaran',
                    }
                },
                xAxis: {
                    title: {
                        text: 'Region',
                        align: 'middle',
                        y: 10
                    },
                    min: 0,
                    max: (rg_count > '5' ? '4' : rg_count - 1),
                    scrollbar: {
                        enabled: (rg_count > '5' ? true : false)
                    },
                    categories: region_name_count,
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
                    labels: {
                        formatter: function() {
                            return this.value;
                        }
                    },
                    title: {
                        text: '% of Progress',
                        align: 'middle',
                        y: 20,
                        // x: -30
                    },
                    tickInterval: 10
                },
                tooltip: {
                    formatter: function() {
                        if (this.series.name == 'Start (Users %)') {
                            var usercnt = started_count[this.point.index];
                        } else {
                            var usercnt = completed_count[this.point.index];
                        }
                        return '<b>' + region_name_count[this.point.index] + '</b><br/>' + this.series.name + ' (' + usercnt + '/' + user_mapped_count[this.point.index] + ') ::: ' + this.point.y;
                    }
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'top',
                    itemMarginTop: -40,
                    x: 20,
                    y: 30,
                    floating: true,
                    borderWidth: 0,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                    shadow: false
                },
                credits: {
                    enabled: false
                },

                plotOptions: {
                    bar: {
                        dataLabels: {
                            format: '{y}',
                            enabled: true,

                        },
                        pointWidth: 8
                    }
                },
                series: [{
                    name: 'Start (Users %)',
                    data: rigion_start_count,
                    color: '#d2e7f7',
                    dataLabels: {
                        style: {
                            fontWeight: 'normal',
                            textOutline: '0',
                            color: 'black',
                            'fontFamily': 'Catamaran',
                            'fontSize': '12px',
                        }
                    },
                }, {
                    name: 'Completion (Users %)',
                    data: region_completed_count,
                    color: '#dbc3c3',
                    dataLabels: {
                        style: {
                            fontWeight: 'normal',
                            textOutline: '0',
                            color: 'black',
                            'fontFamily': 'Catamaran',
                            'fontSize': '12px',
                        }
                    },
                }],
                exporting: {
                    chartOptions: {
                        title: {
                            text: 'Adoption by region (Overall)',
                            align: 'left',
                            verticalAlign: 'top',
                            y: 4,
                            'style': {
                                'fontSize': '12px',
                                'fontFamily': 'Catamaran',
                                'color': 'black',
                            },
                        },
                        subtitle: {
                            text: '',
                            align: 'left',
                            verticalAlign: 'top',
                            y: 17,
                            'style': {
                                'fontSize': '12px',
                                'fontFamily': 'Catamaran',
                                'color': 'black',

                            }
                        },
                        legend: {
                            enabled: true,
                            itemMarginTop: 0,
                            verticalAlign: 'top',
                            itemMarginTop: -15,
                            y: 28,
                        },
                    },
                    csv: {
                        columnHeaderFormatter: function(item, key) {
                            if (!key) {
                                return 'Manager Name'
                            }
                            return false
                        }
                    },
                    filename: 'Adoption by region (Overall)',
                    buttons: {
                        contextButton: {
                            symbol: 'download',
                            symbolStroke: "#004369",
                            menuItems: (rg_count > '5' ? ['downloadCSV', 'downloadXLS'] : ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS'])
                            // menuItems: ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS']

                        }
                    },
                    enableImages: true

                }
            });

        });
    </script>
<?php } ?>