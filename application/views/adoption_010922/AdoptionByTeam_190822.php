<?php
$stsum= json_decode($index_label);
$comsum= json_decode($user_completed);
$newlen= array_sum($stsum);
$newlen1= array_sum($comsum);

if ($newlen==0 && $newlen1==0) {
?>
<style>
    .img-style
    {
    height: 60%;
    width: 50%;
    text-align: center;
    margin: auto;
    margin-left: 23%;
    margin-top: 2%;
    }
    .head-text{
    font-family: "Open Sans",sans-serif;
    font-size: 16px;
    font-weight: 600;
    line-height: 24px;
    color: #2A2E36;
    text-transform: inherit;
    margin-bottom: 8px;
    text-align: center;
    }
    .sub-head{
    font-family: "Open Sans",sans-serif;
    font-size: 12px;
    font-weight: 400;
    line-height: 16px;
    color: #2A2E36;
    text-transform: inherit;
    text-align: center;
    }
</style>
    <div id='container'>
        <div id='AdoptionByTeam' style='min-width: 100%; height: 300px; background:white;'>
            <img src="<?= base_url(); ?>assets/images/empty.jpeg"  class="img-style"/>   
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php }  else{ ?>
<div id='container'>
    <div id='AdoptionByTeam' style='min-width: 100%; height: 300px;'></div>
    <div class="clearfix"></div>
</div>
<?php
$report_title;
$reporttitle = str_replace('"', "", $report_title);
$reporttitle;
?>
<script>
    var ReportTitle = <?php echo $report_title; ?>;
    var index_label = <?php echo $index_label; ?>;
    var indexData = <?php echo $index_dataset; ?>;
    var completed_dataset = <?php echo $user_completed ?>;
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
                marginTop: 50,
                spacingBottom: 25,
                events: {
                    load: function() {
                        this.renderer.image('<?= base_url(); ?>assets/images/poweredby-awarathon-logo.png',this.chartWidth/2 - 24, this.chartHeight - 16, 80, 10).add();
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
                align: 'center',
                verticalAlign: 'top',
                y: 30,
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Arial',
                }
            },
            xAxis: {
                categories: indexData,
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: ReportTitle,
                    align: 'high',
                    y: 10
                },
                labels: {
                    overflow: 'justify'
                }
            },
            tooltip: {
                valueSuffix: '%'
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
                name: 'Start (Users %)',
                data: index_label,
                dataLabels: {
                    style: {
                        fontWeight: 'normal',
                        textOutline: '0',
                        color: 'black',
                        'fontSize': '12px',
                    }
                },
                color: '#7cb5ec',
            }, {
                name: 'Completion (Users %)',
                data: completed_dataset,
                color: '#dbc3c3'
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
                        text: 'Adoption by Team',
                        align: 'left',
                        verticalAlign: 'top',
                        y: 4,
                        'style': {
                            'fontSize': '12px',
                            'fontFamily': 'Catamaran',
                            'color':'black',
                        },
                    },
                    subtitle: {
                        text: <?php echo $report_title ?>,
                        align: 'left',
                        verticalAlign: 'top',
                        y: 17,
                        'style': {
                            'fontSize': '10px',
                            'fontFamily': 'Catamaran',
                            'color':'black',
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
                            return 'Manager Name'
                        }
                        return false
                    }
                },
                filename: 'Adoption by Team ' + <?php echo $report_title ?> + '',
                buttons: {
                    contextButton: {
                        symbol: 'download',
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