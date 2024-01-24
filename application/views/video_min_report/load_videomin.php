<fildset id="minvideo<?php echo $company_id ?>">
    <div class="portlet light bordered">
        <div class="portlet-title ">
            <div class="caption">
                <i class="icon-bar-chart font-green-sharp"></i>
                <span class="caption-subject font-green-sharp "> <?php echo $company_name ?></span>
                <span class="caption-helper"><?php echo $period_title ?></span>
            </div>
            <div class="actions">
                <div class="btn-group">
                    <a class="btn btn-sm btn-default btn-circle" href="" class="btn" data-toggle="modal" onclick="getfiltermodal(<?php echo $company_id ?>,0);">
                        Filter By <i class="fa fa-angle-down"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="portlet-body chart " id="statistics_video<?php echo $company_id ?>">
        </div>
        <input type="hidden" name='company_id' id='company_id' value='<?php echo $company_id ?>'>
    </div>
</fildset>
<script>


 $(document).ready(function () {   
 var indexdata = <?php echo $index_data ?>;
 var indexlabel = <?php echo $index_label ?>;
 var totalusers = <?php echo $total_user ?>;
        Highcharts.chart('statistics_video<?php echo $company_id ?>', {
            chart: {
                 type: 'areaspline'
            },
            title: {
                text: '<?php echo $report_title ?>',
                'style': {
                    'fontSize': '12px',
                    'fontFamily': 'Arial'
                }
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories:indexlabel,
                title: {
                    text: '<?php echo $period_title ?>'
                }
            },
            yAxis: {
                title: {
                    text: 'Minute',
                    align: 'middle',
                },
                labels: {
                    formatter: function () {
                        return this.value + ' min';
                    },
                    overflow: 'justify'

                }
            },
            tooltip: {
//                valueSuffix: 'min'
                formatter: function () {
                    return 'Total Minute :'+ Highcharts.numberFormat(this.y,2)+ ' min <br/> Total Users:'+totalusers[this.point.index];
                }
            },
            legend: {
                enabled: true
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.2f}'
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                    type: 'areaspline',                        
                    name: 'Video Utilized Minute',
                    data: indexdata,
                    <?php (count((array)$index_label) > 10 ? '' : 'pointWidth: 28,')?>
                    color: '#fbc5c5',
                    dataLabels: {
                        style: {
                            fontWeight: 'normal',
                            textOutline: '0',
                            color: 'black',
                            'fontSize': '12px',
                        }
                    }
                }]       
        });        
  

    });

</script>


