<h3>Battery Level</h3>

<?php
$monitoringsensorsBattery = MSBattery::getMonitoringSensorByKeys(User::current_user(), $device->_id, "battery");
if ($monitoringsensorsBattery != NULL || sizeof($monitoringsensorsBattery) > 0) {

    if (sizeof($monitoringsensorsBattery->arrayValues) > 0) {
        ?>
        <div id = "containerbattery<?php echo $i; ?>" style = "height: auto;width: 600px; margin:0 auto;"></div>

        <script>

            $(function() {
                $('#containerbattery<?php echo $i; ?>').highcharts({
                    chart: {
                        type: 'spline'
                    },
                    title: {
                        text: 'Battery Percentage'
                    },
                    xAxis: {
                        categories: <?php echo $monitoringsensorsBattery->getArrayTimes(); ?>
                    },
                    yAxis: {
                        title: {
                            text: 'Battery'
                        },
                        labels: {
                            formatter: function() {
                                return this.value + '%'
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
                            name: 'Battery Percentage',
                            marker: {
                                symbol: 'diamond'
                            },
                            data: <?php echo $monitoringsensorsBattery->getArrayValues(); ?>
                        }]
                });
            });
        </script>
    <?php } else {
        ?>
        <div class="alert alert-info">
            Not yet received any information from Battery Status!
        </div>
        <?php
    }
}
?>