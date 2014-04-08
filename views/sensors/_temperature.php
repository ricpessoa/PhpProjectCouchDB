<script>
    $(function() {
        $('#container<?php echo $i; ?>').highcharts({
            chart: {
                type: 'spline'
            },
            title: {
                text: 'Average Temperature'
            },
            xAxis: {
                categories: ['10:00', '10:10', '10:20', '10:30', '10:40', '10:50',
                    '11:00', '11:10', '11:20', '11:30', '11:40', '11:50', '12:00']
            },
            yAxis: {
                title: {
                    text: 'Temperature'
                },
                labels: {
                    formatter: function() {
                        return this.value + '°'
                    }
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                }
            },
            series: [{
                    name: 'Temperature',
                    marker: {
                        symbol: 'diamond'
                    },
                    data: [4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
                }]
        });
    });
</script>


<div id="container<?php echo $i; ?>" style="height: auto;width: 600px; margin:0 auto;"></div>
