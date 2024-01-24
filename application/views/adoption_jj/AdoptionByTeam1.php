<?php
$stsum = json_decode($start);
$comsum = json_decode($complet);
$newlen = array_sum($stsum);
$newlen1 = array_sum($comsum);

if ($newlen == 0 && $newlen1 == 0) {
?>
    <div id='container'>
        <div id='Adb_with_Team' style='min-width: 100%; height: 350px; background:white;text-align:center'>
            <img src="<?= base_url(); ?>/assets/images/empty.jpeg" class="img-style" />
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
    <style>
        #pager  {
            display: inline-block;
            border: 1px solid black;
            background-color: white;
            padding: 2px 5px;
            text-decoration: none;
            margin: 2px;
            font-family: "Catamaran";
            color: black;
        }
        .pager {
            position: absolute;
            top: 81%;
            left: 44%;
        }
    </style>
    <div id='container'>
        <div id='Adb_with_Team' style='min-width: 100%; height: 350px;'></div>
        <div class="clearfix"></div>
        <div class="pager">
        <nav aria-label="...">
        <ul class="pagination">
                <li class="page-item" style="color:red;background:red;"></li>
            </ul>
        </nav>
        </div>
    </div>
    <?php
    $reporttitle = str_replace('"', "", $team_title);
    ?>
    <script>
        var repotitle = <?php echo $team_title; ?>;
        var st = <?php echo $start; ?>;
        var tr_name = <?php echo $trainer_name; ?>;
        var cp = <?php echo $complet ?>;
        var st_percent = <?php echo $started; ?>;
        var cp_percent = <?php echo $completed; ?>;
        var mp = <?php echo $mapping; ?>;

        var tr_name = tr_name;
        var teamarrSeries = [{
            name: 'Start (Users %)',
            data: st
        }, {
            name: 'Completion (Users %)',
            data: cp,
            color: '#dbc3c3'
        }];

        function AdoptionGraph(adoption_page) {

            // number of different elements
            var numDifferentElements = 5;
            // first element that needs to be extracted from trainer_name and teamarrSeries
            var intStartElement = adoption_page * numDifferentElements;

            var team_start_and_complted_series = [];
            var trainerName = [];

            for (var elem in teamarrSeries) {

                var arrSubData = [];

                // extract elements starting from a position based on the page number 
                // from the data array and only pass this one along when creating the chart
                for (var i = 0; i < numDifferentElements; i++) {

                    trainerName.push(tr_name[intStartElement + i]);
                    arrSubData.push(teamarrSeries[elem].data[intStartElement + i]);

                }
                team_start_and_complted_series.push({
                    name: teamarrSeries[elem].name,
                    data: arrSubData,
                    color: teamarrSeries[elem].color

                });

            }
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
                $('#Adb_with_Team').highcharts({
                    chart: {
                        type: 'bar',
                        marginBottom: 100,
                        marginTop: 60,
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
                        text: repotitle,
                        align: 'right',
                        verticalAlign: 'bottom',
                        y: -24,
                        'style': {
                            'fontSize': '12px',
                            'fontFamily': 'Catamaran',
                        }
                    },
                    xAxis: {
                        categories: trainerName
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        },
                        tickInterval:10
                    },
                    tooltip: {
                        formatter: function() {
                            if (this.series.name == 'Start (Users %)') {
                                var usercnt = st_percent[this.point.index];
                            } else {
                               var usercnt = cp_percent[this.point.index];
                            }
                            return '<b>' + tr_name[this.point.index] + '</b><br/>' + this.series.name + ' (' + usercnt + '/' + mp[this.point.index] + ') : ' + this.point.y + '%';
                        }
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
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
                    },
                    credits: {
                        enabled: false
                    },

                    series: team_start_and_complted_series,
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
                            subtitle: {
                                text: <?php echo $team_title ?>,
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
                                // x: 20,
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
                                menuItems: ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS']
                            }
                        }
                    }

                });
            });

        } // end function AdoptionGraph

        $(function() {

            // create a pager on start
            function Adoption_initialise_Pager() {
                var numPages = Math.ceil(tr_name.length / 5);
                var maangerNames_len = tr_name.length;
                const box = document.getElementsByClassName('page-item')[0];
                if (maangerNames_len > 5) {
                    for (i = 0; i < numPages; i++) {
                        var link = '<a href="javascript:AdoptionGraph(' + i + ')">' + Math.round(i + 1) + '</a>';
                        $('.page-item').append(link);
                    }
                } else {
                    box.style.visibility = 'hidden';
                }
            }

            Adoption_initialise_Pager();
            AdoptionGraph(0);
        });
    </script>
<?php } ?>