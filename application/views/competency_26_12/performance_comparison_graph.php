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
        <div id='performance_comparison_by_module' style='min-width: 100%; height: 300px; background:white;'>
            <img src="<?= base_url(); ?>assets/images/empty.jpeg"  class="img-style"/>
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
<div id='performance_comparison_by_module' style='min-width: 100%; height: 300px;'></div>
    <div id='performance_comparison_by_module'></div>
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
        Highcharts.chart('performance_comparison_by_module', {
            chart: {
                type: 'bar',
                marginTop: 50,
                spacingBottom: 25,
                events: {
                    load: function() {
                        this.renderer.image('<?= base_url(); ?>assets/images/poweredby-awarathon-logo.png',this.chartWidth/2 - 24, this.chartHeight - 16, 80, 10).add();
                    }
                }
                // height: 300,
            },
            title: {
                text: 'Performance Comparison by module',
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
                    'color':'black',
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
                color: '#6ddee3',
            }],
            exporting: {
                chartOptions: {
                    subtitle: {
                        text: 'Performance comparison by module',
                        align: 'left',
                        verticalAlign: 'top',
                        y: 10,
                        'style': {
                            'fontSize': '12px',
                            'fontFamily': 'Catamaran',
                            'color': 'black',
                        },  
                    } 
                },

                
                csv: {
                    columnHeaderFormatter: function(item, key) {
                        if (!key) {
                            return 'Module Name'
                        }
                        return false
                    }
               
                },
                filename: 'Performance comparison by module ',
                
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