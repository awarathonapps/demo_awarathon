<?php
$stsum = json_decode($rigion_start);
$comsum = json_decode($region_completed);
$newlen = array_sum($stsum);
$newlen1 = array_sum($comsum);

if ($newlen == 0 && $newlen1 == 0) {
?>
    <div id='container'>
        <div id='AdoptionByRegion' style='min-width: 100%; height: 350px; background:white;text-align:center'>
            <img src="<?= base_url(); ?>assets/images/empty.jpeg" class="img-style" />
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
    <div id='container'>
        <div id='AdoptionByRegion' style='min-width: 100%; height: 350px;'></div>
        <div class="clearfix"></div>
    </div>
    <?php
    $Regionnamecount = sizeof(json_decode($region_name));
    ?>
    <script>
        var region_title = <?php echo $region_title; ?>;
        var rigion_start = <?php echo $rigion_start; ?>;
        var region_name = <?php echo $region_name; ?>;
        var region_completed = <?php echo $region_completed ?>;
        var us_started = <?php echo $us_started; ?>;
        var us_completed = <?php echo $us_completed; ?>;
        var us_mapped = <?php echo $us_mapped; ?>;
        var Rcount = <?php echo $Regionnamecount; ?>;

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
            Highcharts.chart('AdoptionByRegion', {
                chart: {
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
                    text: region_title,
                    align: 'right',
                    verticalAlign: 'bottom',
                    y: 10,
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
                    max: (Rcount > '5' ? '4' : Rcount - 1),
                    scrollbar: {
                        enabled: (Rcount > '5' ? true : false)
                    },
                    categories: region_name,
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '% of Progress',
                        align: 'middle',
                        y: 10,
                        // x: -30
                    },
                    labels: {
                        overflow: 'justify'
                    },
                    tickInterval: 10
                },
                tooltip: {
                    formatter: function() {
                        if (this.series.name == 'Start (Reps %)') {
                            var usercnt = us_started[this.point.index];
                        } else {
                            var usercnt = us_completed[this.point.index];
                        }
                        return '<b>' + region_name[this.point.index] + '</b><br/>' + this.series.name + ' (' + usercnt + '/' + us_mapped[this.point.index] + ') : ' + this.point.y + '%';
                    }
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        },
                        pointWidth: 10
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
                series: [{
                    name: 'Start (Reps %)',
                    data: rigion_start,
                    color: '#6ddee3',
                    dataLabels: {
                        style: {
                            fontWeight: 'normal',
                            color: 'black',
                        }
                    }
                }, {
                    name: 'Completion (Reps %)',
                    data: region_completed,
                    color: '#FFFF00',
                    dataLabels: {
                        style: {
                            fontWeight: 'normal',
                            color: 'black',

                        }
                    }
                }],
                responsive: {
                    rules: [{
                        condition: {},
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
                    chartOptions: {
                        title: {
                            text: 'Adoption by Region',
                            align: 'left',
                            verticalAlign: 'top',
                            y: 4,
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
                            text: <?php echo $region_title ?>,
                            align: 'left',
                            verticalAlign: 'top',
                            y: 17,
                            'style': {
                                'fontSize': '10px',
                                'fontFamily': 'Catamaran',
                                'color': 'black',
                            },
                        },
                        legend: {
                            enabled: true,
                            itemMarginTop: 0,
                            verticalAlign: 'top',
                            itemMarginTop: -26,
                            // x: 20,
                            y: 28,
                        },
                    },
                    csv: {
                        columnHeaderFormatter: function(item, key) {
                            if (!key) {
                                return 'Region Name '
                            }
                            return false
                        }
                    },
                    filename: 'Adoption by Region ' + <?php echo $region_title ?> + '',
                    buttons: {
                        contextButton: {
                            symbol: 'download',
                            symbolStroke: "#004369",
                            menuItems: (Rcount > '5' ? ['downloadCSV', 'downloadXLS'] : ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS'])
                            // menuItems: ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS']
                        }
                    },
                    enableImages: true
                }
            });
        });
    </script>
<?php } ?>