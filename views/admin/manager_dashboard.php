<?php if (User::is_authenticated() && User::is_current_admin_authenticated()) { ?>
    <script src="<?php echo $this->make_route('/js/highcharts.js')?>"></script>

    <legend>Administrator Dashboard </legend>
    <div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>

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
                            ['Available', 90],
                            {
                                name: 'Unavailable',
                                y: 10,
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

