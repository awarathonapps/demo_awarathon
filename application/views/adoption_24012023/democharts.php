<div id='container'>
    <div id='democharts' style='min-width: 100%; height: 300px;'></div>
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

        Highcharts.chart('democharts', {
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
                categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas'],
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
            plotOptions: {
                series: {
                    stacking: 'normal'
                }
            },

            credits: {
                enabled: false
            },
            series: [{
                name: 'John',
                data: [5, 3, 4, 7, 2]
            }, {
                name: 'Jane',
                data: [2, 2, 3, 2, 1]
            }, {
                name: 'Joe',
                data: [3, 4, 4, 2, 5]
            }]
        });
    });
</script>