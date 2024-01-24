<?php
$stsum = json_decode($DivisionStart);
$comsum = json_decode($DivisionCompleted);
$newlen = array_sum($stsum);
$newlen1 = array_sum($comsum);

if ($newlen == 0 && $newlen1 == 0) {
?>
    <div id='container'>
        <div id='adoption_by_division_overall' style='min-width: 100%; height: 350px; background:white;text-align:center'>
            <img src="<?= base_url(); ?>/assets/images/empty.jpeg" class="img-style" />
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
    <div id='container'>
        <div id='adoption_by_division_overall' style='min-width: 100%; height: 350px;'></div>
        <div class="clearfix"></div>
    </div>
    <?php
    $namecount = sizeof(json_decode($division_names));
    ?>
    <script>
        var ReportTitle = <?php echo $division_title; ?>;
        var start = <?php echo $DivisionStart; ?>;
        var trainer_name = <?php echo $division_names; ?>;
        var DivisionCompleted = <?php echo $DivisionCompleted ?>;
        var start_count = <?php echo $start_count; ?>;
        var complete_count = <?php echo $complete_count; ?>;
        var mapped_count = <?php echo $mapped_count; ?>;
        var count = <?php echo $namecount; ?>;
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

            Highcharts.chart('adoption_by_division_overall', {
                chart: {
                    type: 'bar',
                    height:'350',
                    marginBottom: 80,
                    marginTop: 50,
                    events: {
                        load: function() {
                            this.renderer.image('<?= base_url(); ?>assets/images/poweredby-awarathon-logo.png', this.chartWidth / 2 - 24, this.chartHeight - 16, 80, 10).add();
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
                    text: ReportTitle,
                    align: 'right',
                    verticalAlign: 'bottom',
                    y: -24,
                    'style': {
                        'fontSize': '12px',
                        'fontFamily': 'Arial',
                    }
                },
                xAxis: {
                    // By Bhautik Rana 10-01-2023
                    title: {
                        text: 'Division',
                        align: 'middle',
                        y: 10
                    },
                    // By Bhautik Rana 10-01-2023
                    min: (count > '4' ? 0 : 0),
                    max: (count > '4' ? 4 : count - 1),
                    scrollbar: {
                        enabled: (count > '5' ? true : false)
                    },
                    categories: trainer_name
                },
                yAxis: {
                    labels: {
                        formatter: function() {
                            return this.value + '%';
                        }
                    },
                    min: 0,
                    // By Bhautik Rana 10-01-2023
                    title: {
                        text: '% of Progress',
                        align: 'middle',
                        y: 3,
                        // x: -30
                    },
                    // By Bhautik Rana 10-01-2023
                    tickInterval: 10
                },
                tooltip: {
                    formatter: function() {
                        if (this.series.name == 'Start (Reps %)') {
                            var usercnt = start_count[this.point.index];
                        } else {
                            var usercnt = complete_count[this.point.index];
                        }
                        return '<b>' + trainer_name[this.point.index] + '</b><br/>' + this.series.name + ' (' + usercnt + '/' + mapped_count[this.point.index] + ') : ' + this.point.y + '%';
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
                },
                credits: {
                    enabled: false
                },

                plotOptions: {
                    bar: {
                        dataLabels: {
                            formate: '(y) %',
                            enabled: true,
                        },
                        pointWidth: 7
                    },
                    // series: {
                    //     groupPadding: 0
                    // }
                },
                series: [{
                    name: 'Start (Reps %)',
                    data: start,
                    color: '#91e6ea',
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
                    name: 'Completion (Reps %)',
                    data: DivisionCompleted,
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
                            text: 'Adoption by Division (Overall)',
                            align: 'left',
                            verticalAlign: 'top',
                            y: 4,
                            'style': {
                                'fontSize': '12px',
                                'fontFamily': 'Catamaran',
                                'color': 'black',
                            },
                        },
                        xAxis: {
                            scrollbar: {
                                enabled: false
                            },
                        },
                        // By Bhautik Rana 10-01-2023
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        // By Bhautik Rana 10-01-2023
                        subtitle: {
                            text: ReportTitle,
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
                            itemMarginTop: -15,
                            y: 28,
                        },
                    },
                    csv: {
                        columnHeaderFormatter: function(item, key) {
                            if (!key) {
                                return 'Division Name'
                            }
                            return false
                        }
                    },
                    filename: 'Adoption by Division (Overall) ' + ReportTitle + '',
                    buttons: {
                        contextButton: {
                            symbol: 'download',
                            symbolStroke: "#004369",
                            menuItems: (count > '5' ? ['downloadCSV', 'downloadXLS'] : ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS'])
                        }
                    }
                }
            });
        });
    </script>
<?php } ?>