<?php
$stsum = json_decode($module_start);
$comsum = json_decode($module_completed);
$newlen = array_sum($stsum);
$newlen1 = array_sum($comsum);

if ($newlen == 0 && $newlen1 == 0) {
?>
    <div id='container'>
        <div id='AdoptionByModule' style='min-width: 100%; height: 350px; background:white;text-align:center'>
            <img src="<?= base_url(); ?>assets/images/empty.jpeg" class="img-style" />
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
    <div id='container'>
        <div id='AdoptionByModule' style='min-width: 100%; height: 350px;'></div>
        <div class="clearfix"></div>
    </div>
    <?php
    $Modulsnamecount = sizeof(json_decode($modules_name));
    ?>
    <script>
        var module_start = <?php echo $module_start; ?>;
        var modules_name = <?php echo $modules_name; ?>;
        var module_completed = <?php echo $module_completed ?>;
        var st_users = <?php echo $st_users; ?>;
        var co_users = <?php echo $co_users; ?>;
        var um_users = <?php echo $um_users; ?>;
        var Mcount = <?php echo $Modulsnamecount; ?>;


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
            Highcharts.chart('AdoptionByModule', {
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
                    min: 0,
                    // max: (Mcount > '4' ? 4 : Mcount - 1),
                    max: (Mcount > '5' ? '4' : Mcount - 1),
                    scrollbar: {
                        enabled: (Mcount > '5' ? true : false)
                    },
                    categories: modules_name
                },
                yAxis: {
                    title: {
                        text: false,
                        align: 'middle',
                        y: 10
                    },
                    labels: {
                        overflow: 'justify'
                    },
                    tickInterval: 10
                },
                tooltip: {
                    formatter: function() {
                        if (this.series.name == 'Start (Users %)') {
                            var usercnt = st_users[this.point.index];
                        } else {
                            var usercnt = co_users[this.point.index];
                        }
                        return '<b>' + modules_name[this.point.index] + '</b><br/>' + this.series.name + ' (' + usercnt + '/' + um_users[this.point.index] + ') : ' + this.point.y + '%';
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
                series: [{
                    name: 'Start (Users %)',
                    data: module_start,
                    color: '#d2e7f7',
                    dataLabels: {
                        style: {
                            fontWeight: 'normal',
                            color: 'black',

                        }
                    }
                }, {
                    name: 'Completion (Users %)',
                    data: module_completed,
                    color: '#dbc3c3',
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
                            text: 'Adoption by Module',
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
                                return 'Modules Name'
                            }
                            return false
                        }
                    },
                    filename: 'Adoption by Modules',
                    buttons: {
                        contextButton: {
                            symbol: 'download',
                            symbolStroke: "#004369",
                            menuItems: (Mcount > '5' ? ['downloadCSV', 'downloadXLS'] : ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS'])
                            // menuItems: ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS']
                        }
                    }
                }
            });
        });
    </script>
<?php } ?>