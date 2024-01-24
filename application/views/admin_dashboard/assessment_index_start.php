<div id='container'>

    <!-- <div id='assessment_index_start' style='min-width: 100%; height: auto; margin: 0px auto;'></div> -->
    <!-- <div id='assessment_index_start' style='min-width: 310px; height: auto; margin: 0 auto'></div> -->
    <div id='assessment_index_start' style='min-width: 100%; height: 300px;'></div>
    <!-- <div id='assessment_index_start' style='min-width: 100%; height: 300px; position: absolute; top: 44px;'></div>  -->

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

        Highcharts.chart('assessment_index_start', {
            chart: {
                type: 'area',
                marginTop: 80

            },
            title: {

                text: <?php echo $report_title; ?>,
                align: 'left',
                verticalAlign: 'top',
                y: 10,
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Catamaran',

                }
            },
            subtitle: {
                text: '<?php echo json_decode($count); ?>',
                align: 'left',
                verticalAlign: 'top',
                y: 45,
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Catamaran'
                }
            },
            xAxis: {
                categories: <?php echo $index_label; ?>,
                title: {
                    text: false
                }
            },
            yAxis: {
                // gridLineColor: 'black',
                gridLineDashStyle: 'longdash',
                title: {
                    text: false,
                    align: 'middle',

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

            credits: {
                enabled: false
            },
            series: [{
                type: 'area',
                name: 'Assessment',
                data: indexData,
                <?php count((array) $index_label) > 10 ? '' : 'pointWidth: 0,'; ?>
                //                    color: '#ffc000',
                // color: '#fcb6c5', fa9d9d
                color: '#c0fcc5',
                lineWidth: 3,
                lineColor: '#fff',

                marker: {
                    lineWidth: 1.5,
                    radius: 5,
                    hover: {
                        lineWidthPlus: 1.5,
                        radiusPlus: 2
                    }
                },

                dataLabels: {
                    style: {
                        fontWeight: 'normal',
                        textOutline: '0',
                        color: '#c0fcc5',
                        'fontSize': '12px',
                    }
                }

            }],
            exporting: {
                csv: {
                    columnHeaderFormatter: function(item, key) {
                        if (!key) {
                            return 'Assement Month'
                        }
                        return false
                    }
                },
                filename: 'Assesment Started ' +<?php echo $report_title ?>+ '',
                buttons: {
                    contextButton: {

                        symbol: 'download',
                        'stroke-width': 1,
                        symbolStroke: "#004369",
                        menuItems: ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS']
                    }
                }
            }
            // navigation: {
            //             buttonOptions: {
            //                 theme: {
            //                 'stroke-width': 1,
            //                 stroke: 'silver',
            //                 r: 0,
            //                 states: {
            //                     hover: {
            //                     fill: '#bada55'
            //                     },
            //                     select: {
            //                     stroke: '#039',
            //                     fill: '#bbadab'
            //                     }
            //                 }
            //                 }
            //             }
            //         }
            // ,
            // exporting: {
            //     buttons: {
            //         contextButton: {
            //             symbol: '🢃'
            //         }
            //     }
            // }   
        });
    });
</script>