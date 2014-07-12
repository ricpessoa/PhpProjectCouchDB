<legend>Edit Device <?php echo $deviceID; ?></legend>
    <?php
    foreach ($arraySensors as $sensor):
        if ($sensor === "GPS") {
            include '_safezone.php';
        }
        if ($sensor->type === "temperature") {
            include '_temperature.php';
        }
        if ($sensor === "panic_button") {
            include '_panicbutton.php';
        }
        if ($sensor->type === "battery") {
            include '_battery.php';
        }
    endforeach;
    ?>