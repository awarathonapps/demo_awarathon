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
        <div id='competency_understanding_graph' style='min-width: 100%; height: 300px; background:white;'>
            <img src="<?= base_url(); ?>assets/images/empty.jpeg"  class="img-style"/>
            <br>
            <div class="head-text">No data found for selected filters</div>
            <div class="sub-head">Please retry with some other filters</div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } else { ?>
<div id='competency_understanding_graph' style='min-width: 100%; height: 300px;'></div>
    <div id='competency_understanding_graph'></div>
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
                
            },
            title: {

                text: '',
                align: 'left',
                verticalAlign: 'top',
                y: 10,
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Catamaran',

                }
            },
            subtitle: {
                text: <?php  echo $report_title;?>,
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
                type: 'bar',
                name: 'user',
                data: <?php echo isset($index_dataset)?$index_dataset:0; ?>,
                color: '#6ddee3',
            }],
            exporting: {
                chartOptions: {
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Powered by Awarathon',
                            align: 'high',
                            y: 10,
                            'style': {
                            'fontSize': '8px',
                            'fontFamily': 'Catamaran',
                            'color': 'black',
                            },
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
                            'color': 'black',
                        },  
                    },
                    subtitle: {
                        text: <?php echo $report_title ?>,
                        align: 'left',
                        verticalAlign: 'top',
                        y: 30,
                        'style': {
                            'fontSize': '10px',
                            'fontFamily': 'Catamaran',
                            'color': 'black',
                        },
                    }, 
                },

                csv: {
                    columnHeaderFormatter: function(item, key) {
                        if (!key) {
                            return 'Percentage'
                        }
                        return false
                    }
               
                },
                filename: 'Competency understanding graph ' +<?php echo $report_title ?>+ '',
                
                buttons: {
                    contextButton: {

                        symbol: 'download',
                        'stroke-width': 1,
                        symbolStroke: "#004369",
                        menuItems: ['printChart', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadCSV', 'downloadXLS']
                    }
                },
                
            }
        });
        
    });
</script>
<?php } ?>