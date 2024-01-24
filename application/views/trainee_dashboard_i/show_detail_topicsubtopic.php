
<div class='col-lg-12' id='ChartDiv' >    
    <div id='container' style='max-height:600px; overflow-y:auto; '>
        <div id='histogram' style='height:<?php echo(count($label)>5 ? "800":"500") ?>px'></div>
    </div>
</div>
<script>    
    
    $(document).ready(function () {    
    Highcharts.chart('histogram', {
            chart: {
                type: 'bar'
            },
            title: {
            text: '',
                    'style': {
                        'fontSize': '12px',
                        'fontFamily': 'Arial'
                    }
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories:<?php echo $label; ?>,
                title: {
                    text: 'Topic Sub-Topic Wise'
                },
                
        scrollbar: {
            enabled: false
        }
            },
            yAxis: {
                title: {
                        text: 'Compentency',
                        align: 'middle',
            },
            labels: {
                formatter: function () {
                return this.value + '%';
                },
                overflow: 'justify'
            }
            },
            tooltip: {
            valueSuffix: '%'
            },
            legend: {
            enabled: true
            },
            plotOptions: {
                bar: {
                                            dataLabels: {
                                                enabled: true,
                                                format: '{point.y:.2f}%',
                                                allowOverlap: true,
                                                crop: false,
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color:'black',
                                                    fontSize: '10px',
                                                }
                                            }
                                        }
            },
            credits: {
                enabled: false
            },
            series: [{
                                            name: 'Pre',
                                            data: <?php echo $datasetpre; ?>,
                                            color:'#0070c0'
                                        }, {
                                            name: 'Post',
                                            data: <?php echo $datasetpost; ?>,
                                            color:'#00ffcc'
                                        }, {
                                            name: 'Positive C.E',
                                            data:  <?php echo $dataset3; ?>,
                                            stacking: 'normal',
                                            color:'#ffc000',
                                        },
                                        {
                                            name: 'Negative C.E',
                                            data: <?php echo $dataset4; ?>,
                                            stacking: 'normal',
                                            color:'#FF0000',
                                            dataLabels: {
                                                style: {
                                                    fontWeight: 'normal',
                                                    textOutline: '0',
                                                    color: 'black',
                                                    'fontSize': '12px',
                                                },
                                                formatter: function () {
                                                    if (this.y < 0) {
                                                        return this.y;
                                                    }
                                                },
                                                enabled: true,
                                                overflow: 'none'
                                            }
                                        }   
                                    ]
        });
    });
</script>                              
