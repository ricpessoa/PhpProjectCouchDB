<?php if (User::is_authenticated()) { ?>

    <script type="text/javascript" src="<?php echo $this->make_route('/js/mapfunctions.js') ?>"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDqUwWysKF_x_SkHDgB-q_NeaqBHpPTlME&sensor=false"></script>
    <script src="http://mapstraction.com/mxn/build/latest/mxn.js?(googlev3)" type="text/javascript"></script>
    <script src="js/highcharts.js"></script>

    <legend>My Dashboard</legend>
    <?php
    if ($devices != null && sizeof($devices) > 0) {
        ?>
        <div class="tabbable">
            <ul class="nav nav-tabs" id="myTab">
                <?php
                $i = 1;
                foreach ($devices as $device) {
                    $varNameDevice = 'Device ' . $i;
                    if ($device->name_device != null) {
                        $varNameDevice = $device->name_device;
                    }
                    ?>
                    <li <?php if ($i == 1) echo 'class ="active"'; ?>><a href="#pane<?php echo $i; ?>" data-toggle="tab" ><?php echo '<i class="icon-th-large"></i> Device ' . $varNameDevice; ?></a></li>
                    <?php
                    $i = $i + 1;
                }
                ?>
            </ul>
            <div class="tab-content">
                <?php
                $i = 1;
                foreach ($devices as $device) {
                    ?>
                    <div id="pane<?php echo $i; ?>" class="tab-pane<?php if ($i == 1) echo ' active'; ?>">
                        <?php
                        foreach ($device->sensors as $sensor):
                            $numberSensors = 0;
                            if ($sensor->type === "GPS" && $sensor->enable == TRUE) {
                                include 'sensors/monitorsensors/_ms_gps.php';
                                $numberSensors++;
                            }
                            if ($sensor->type === "temperature" && $sensor->enable == TRUE) {
                                include 'sensors/monitorsensors/_ms_temperature.php';
                                $numberSensors++;
                            }
                            if ($sensor->type === "panic_button" && $sensor->enable == TRUE) {
                                include 'sensors/monitorsensors/_ms_panicbutton.php';
                                $numberSensors++;
                            }
                            if ($sensor->type === "battery" && $sensor->enable == TRUE) {
                                include 'sensors/monitorsensors/_ms_battery.php';
                                $numberSensors++;
                            }

                        endforeach;
                        if ($numberSensors == 0) {
                            ?>
                            <div class = "alert alert-info">
                                This device has no active sensors
                            </div>   
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    $i = $i + 1;
                }
                ?>
            </div><!-- /.tab-content -->
        </div><!-- /.tabbable -->
        <?php
    } else {
        ?>
        <div class = "alert alert-info">
            Do not have devices to insert devices go to Page Devices
        </div>   
    <?php } ?>

<?php } else { ?>

    <div class="hero-unit">
        <h1>Welcome to Backend!</h1>
        <p>This backend is a sample framework to connect with couchDB</p>
        <p> 
            <a href="<?php echo $this->make_route('/signup') ?>"  class="btn btn-primary btn-large">Signup Now</a>
            <a href="<?php echo $this->make_route('/login') ?>"  class="btn btn-success btn-large">Login</a>
        </p>
        <p>You can sign up or log in </p>
    </div>

<?php } ?>