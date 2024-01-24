<?php
$stsum= json_decode($index_dataset);

$newlen= array_sum($stsum);

if ($newlen==0) {
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
    font-family: "Catamaran";
    font-size: 16px;
    font-weight: 600;
    line-height: 24px;
    color: #2A2E36;
    text-transform: inherit;
    margin-bottom: 8px;
    text-align: center;
    }
    .sub-head{
    font-family: "Catamaran";
    font-size: 12px;
    font-weight: 400;
    line-height: 16px;
    color: #2A2E36;
    text-transform: inherit;
    text-align: center;
    }
</style>
    <div id='container'>
        <div id='performance_comparison_by_region' style='min-width: 100%; height: 300px; background:white;'>
            <img src="<?= base_url(); ?>assets/images/empty.jpeg"  class="img-style"/>
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
<div id='performance_comparison_by_region' style='min-width: 100%; height: 300px;'></div>
    <div id='performance_comparison_by_region'></div>
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
        Highcharts.chart('performance_comparison_by_region', {
            chart: {
                type: 'bar',
                marginTop: 50,
                height: 300,
                spacingBottom: 25,
                events: {
                    load: function() {
                        this.renderer.image('<?= base_url(); ?>assets/images/poweredby-awarathon-logo.png',this.chartWidth/2 - 24, this.chartHeight - 16, 80, 10).add();
                    }
                }
                // height: 300,
            },
            title: {
                text: 'Performance Comparison by region',
                align: 'left',
                verticalAlign: 'top',
                y: 10,
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Catamaran',
                    'display' : 'none',
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
                },
                overflow: 'justify'
            },
            yAxis: {
                title: {
                    text: <?php echo $report_title;?>,
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
                valueSuffix: ' %'
            },
            legend: {
                enabled: false,
            },
        //     credits: {
        //     //   text: '("https://ai.awarathon.com/assets/layouts/layout/img/Awarathon-logo-RedWhite.png" alt="logo" class="footer-logo")Powered by Awarathon',
        //     text:'Powerd By Awarathon',
        //     href: '',
        //   },
            credits: {
                enabled: false
            },
            plotOptions: {
                    series: {
                        pointWidth: 15//width of the column bars irrespective of the chart size
                    }
            },
            series: [{
                type: 'bar',
                name: 'percentage',
                data: indexData,
                color: '#dbc3c3',
            }],
            exporting: {
                chartOptions: {
                    subtitle: {
                        text: 'Performance comparison by region',
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
                            text: <?php echo $report_title;?>,
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
                            return 'Region Name'
                        }
                        return false
                    }
               
                },
                filename: 'Performance comparison by region ' +<?php echo $report_title ?>+ '',
                
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
