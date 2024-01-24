<?php
$stsum = json_decode($start);
$comsum = json_decode($complet);
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
        <div id='AdoptionByTeam' style='min-width: 100%; height: 350px; background:white;text-align:center'>
            <img src="<?= base_url(); ?>/assets/images/empty.jpeg" class="img-style" />
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
    <div id='container'>
        <div id='AdoptionByTeam' style='min-width: 100%; height: 350px;'></div>
        <div class="clearfix"></div>
    </div>
    <?php
    $reporttitle = str_replace('"', "", $team_title);
    $namecount = sizeof(json_decode($trainer_name));
    ?>
    <script>
        var ReportTitle = <?php echo $team_title; ?>;
        var start = <?php echo $start; ?>;
        var trainer_name = <?php echo $trainer_name; ?>;
        var complet = <?php echo $complet ?>;
        var started = <?php echo $started; ?>;
        var completed = <?php echo $completed; ?>;
        var mapping = <?php echo $mapping; ?>;
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

            Highcharts.chart('AdoptionByTeam', {
                chart: {
                    type: 'bar',
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
                    text: ReportTitle,
                    align: 'right',
                    verticalAlign: 'bottom',
                    y: 7,
                    'style': {
                        'fontSize': '12px',
                        'fontFamily': 'Catamaran',
                    }
                },
                xAxis: {
                    title: {
                        text: 'Managers',
                        align: 'middle',
                        y: 10
                    },
                    min: 0,
                    max: (count > '5' ? '4' : count - 1),
                    scrollbar: {
                        enabled: (count > '5' ? true : false)
                    },
                    categories: trainer_name
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '% of Progress',
                        align: 'middle',
                        y: 10,
                        // x: -30
                    },
                    tickInterval: 10
                },
                tooltip: {
                    formatter: function() {
                        if (this.series.name == 'Start (Reps %)') {
                            var usercnt = started[this.point.index];
                        } else {
                            var usercnt = completed[this.point.index];
                        }
                        return '<b>' + trainer_name[this.point.index] + '</b><br/>' + this.series.name + ' (' + usercnt + '/' + mapping[this.point.index] + ') : ' + this.point.y + '%';
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
                            enabled: true
                        },
                        pointWidth: 8
                    }
                },
                series: [{
                    name: 'Start (Reps %)',
                    data: start,
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
                    data: complet,
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
                            text: 'Adoption by Team',
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
                            text: ReportTitle,
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
                    filename: 'Adoption by Team ' + <?php echo $team_title ?> + '',
                    buttons: {
                        contextButton: {
                            symbol: 'download',
                            symbolStroke: "#004369",
                            menuItems: (count > '5' ? ['downloadCSV', 'downloadXLS'] : ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS'])
                            // menuItems: ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS']

                        }
                    },
                    enableImages: true

                }
            });

        });
    </script>
<?php } ?>