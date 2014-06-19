<h3>Sensor Temperature</h3>

<?php
$monitoringsensors = MSTemperature::getMonitoringSensorByKeys(User::current_user(), $device->_id, "temperature");
if ($monitoringsensors != NULL || sizeof($monitoringsensors) > 0) {
    //echo '<br>' . $monitoringsensors->getArrayTimes() . '<br>' . $monitoringsensors->getArrayValues();
    if (sizeof($monitoringsensors->arrayValues) > 0) {
        ?>
        <div id = "container<?php echo $i; ?>" style = "height: auto;width: 600px; margin:0 auto;"></div>

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
                        categories: <?php echo $monitoringsensors->getArrayTimes(); ?>
                    },
                    yAxis: {
                        title: {
                            text: 'Temperature'
                        },
                        labels: {
                            formatter: function() {
                                return this.value + 'C°'
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
                            data: <?php echo $monitoringsensors->getArrayValues(); ?>
                        }]
                });
            });
        </script>
    <?php } else {
        ?>
        <div class="alert alert-info">
            Not yet received any information from Temperature sensor!
        </div>
        <?php
    }
}
?>