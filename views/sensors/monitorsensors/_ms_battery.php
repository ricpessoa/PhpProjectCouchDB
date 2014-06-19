<h3>Battery Level</h3>

<?php
$monitoringsensors = MSBattery::getMonitoringSensorByKeys(User::current_user(), $device->_id, "battery");
echo 'size of -->'.sizeof($monitoringsensors);
if ($monitoringsensors != NULL || sizeof($monitoringsensors) > 0) {
    echo '<br>' . $monitoringsensors->getArrayTimes() . '<br>' . $monitoringsensors->getArrayValues();
    //echo '-->' . print_r($monitoringsensors->arrayValues);

   // if (sizeof($monitoringsensors->arrayValues) > 0) {
        ?>
        <div id="containerbattery<?php echo $i; ?>" style="height: auto;width: 600px; margin:0 auto;"></div>

        <script>
            $(function() {
                $('#containerbattery<?php echo $i; ?>').highcharts({
                    chart: {
                        zoomType: 'x'
                    },
                    title: {
                        text: 'Battery'
                    },
                    subtitle: {
                        text: document.ontouchstart === undefined ?
                                'Click and drag in the plot area to zoom in' :
                                'Pinch the chart to zoom in'
                    },
                    xAxis: {
                        type: 'datetime',
                        minRange: 14 * 24 * 3600000 // fourteen days
                    },
                    yAxis: {
                        title: {
                            text: 'Battery Percentage (%)'
                        },
                        max:100,
                        minColor: '#FFFFFF',
			maxColor: '#000000',
                    },
                    
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        area: {
                            fillColor: {
                                linearGradient: {x1: 0, y1: 0, x2: 1, y2: 1},
                                stops: [
                                    [0, Highcharts.getOptions().colors[0]],
                                    [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                                ]
                            },
                            marker: {
                                radius: 2
                            },
                            lineWidth: 1,
                            states: {
                                hover: {
                                    lineWidth: 1
                                }
                            },
                            threshold: null
                        }
                    },
                    series: [{
                            type: 'area',
                            name: 'Battery Level',
                            pointInterval: 24 * 3600 * 1000,
                            pointStart: Date.UTC(2006, 0, 01),
                            data: [
                                100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 87, 86, 85, 84, 83, 82, 81, 80, 79, 78, 77, 76, 75, 74, 73, 72, 71, 70, 69, 68, 67, 66, 65, 64, 63, 62, 61, 60, 59, 58, 57, 56, 55, 54, 53, 52, 51, 50, 49, 48, 47, 46, 45, 44, 43, 42, 41, 40, 39, 38, 37, 36, 35, 34, 33, 32, 31, 30, 29, 28, 27, 26, 25, 24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 12, 16, 20, 24, 28, 32, 36, 40, 44, 48, 52, 56, 60, 64, 68, 72, 76, 80, 84, 88, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 87, 86, 85, 84, 83, 82, 81, 80, 79, 78, 78, 78, 78, 77, 77, 77, 77, 77, 76, 75, 76, 75, 76, 75, 76, 75, 74, 73, 72, 71, 70, 69, 68, 67, 66, 65, 64, 63, 62, 61, 60, 59, 58, 57, 56, 55, 54, 53, 52, 51, 50, 49, 48, 47, 46, 45, 44, 43, 42, 41, 40, 39, 38, 37, 36, 35, 34, 33, 32, 31, 30, 29, 28, 27, 26, 25, 24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 12, 16, 20, 24, 28, 32, 36, 40, 44, 48, 52, 56, 60, 64, 68, 72, 76, 80, 84, 88

                            ]
                        }]
                });
            });
        </script>
    <?php //} else {
        ?>
        <div class="alert alert-info">
            Not yet received any information from Battery Status!
        </div>
        <?php
    //}
}
?>