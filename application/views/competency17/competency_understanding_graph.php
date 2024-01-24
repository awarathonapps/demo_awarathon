<?php
$stsum = json_decode($index_dataset);
$newlen = array_sum($stsum);

if ($newlen == 0) {
?>
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
    <div id='competency_understanding_graph'> <?php echo $a; ?></div>
    <div class="clearfix"></div>
    </div>
    <script>
        var indexData = <?php echo $index_dataset; ?>;
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
                            this.renderer.image('<?php echo base_url(); ?>assets/images/poweredby-awarathon-logo.png', this.chartWidth / 2 - 24, this.chartHeight - 16, 80, 10).add();
                        }
                    }

                },
                title: {

                    text: 'Competency understanding graph',
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
                    }
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
                    enabled: false,
                },
                // credits: {
                //   text: 'Powered by Awarathon',
                //   href: '',
                //   }
                credits: {
                    enabled: false
                },
                series: [{
                    type: 'bar',
                    name: 'user',
                    data: <?php echo isset($index_dataset) ? $index_dataset : 0; ?>,
                    color: '#6ddee3',
                }],
                exporting: {
                    chartOptions: {
                        subtitle: {
                            text: 'Competency understanding graph',
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
                                return 'Percentage'
                            }
                            return false
                        }

                    },
                    filename: 'Competency understanding graph ' + <?php echo $report_title ?> + '',

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
