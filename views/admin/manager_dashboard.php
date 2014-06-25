<?php
if (User::is_authenticated() && User::is_current_admin_authenticated()) {
    $percentAvailable = 100;
    if ($numbAllDevices != 0) {
        $percentAvailable = ($numbAvailableDevices / $numbAllDevices ) * 100;
    }
    ?>
    <script src="<?php echo $this->make_route('/js/highcharts.js') ?>"></script>

    <legend>Administrator Dashboard </legend>
    <div class="span12 thumbnail">
        <h3 class="well well-small">Statics of Devices</h3>
        <div class="span6">
            <div id="container" style="min-width: 310px; height: 400px; max-width: 600px;"></div>
        </div>
        <div class="span4">
            <ul class="thumbnails">
                <li class="span4">
                    <div class="thumbnail">
                        <br>
                        <h4><?php echo $numbAvailableDevices; ?> Devices Available</h4>
                        <br>
                        <h4><?php echo $numbAllDevices - $numbAvailableDevices; ?> Devices Already Acquired</h4>
                        <br>
                        <h4><?php echo $numbAllDevices; ?> Total Devices</h4>
                        <br>
                        <h4> Bar Status of Devices Available</h4>
                        <div class="progress">
                            <div class="bar bar-success" style="width: <?php echo 100 - $percentAvailable; ?>%;"></div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
   
    <script>
        $(function() {
            $('#container').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: 0, //null,
                    plotShadow: false
                },
                title: {
                    text: 'Percentage of Available Devices'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                        type: 'pie',
                        name: 'Percentage devices',
                        data: [
                            ['Available', <?php echo $percentAvailable; ?>],
                            {
                                name: 'Already Acquired',
                                y: <?php echo 100 - $percentAvailable; ?>,
                                sliced: true,
                                selected: true
                            }
                        ]
                    }]
            });
        });
    </script>

<?php }
?>

