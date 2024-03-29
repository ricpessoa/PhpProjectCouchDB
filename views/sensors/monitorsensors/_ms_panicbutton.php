<h3>Sensor Panic Button</h3>
<div class="panic<?php echo $i; ?>">
    <?php
    $msShoe = MSPanicButton::getMonitoringSensorByKeys(User::current_user(), $device->_id, "panic_button");
    if ($msShoe != NULL && $msShoe->pressed === TRUE) {
        ?>
        <div class="alert alert-danger">
            <h4>Alert!</h4>
            <?php echo 'The panic button was pressed in ' . $msShoe->timestamp; ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-info">
            Not yet received any information from Panic Button sensor!
        </div>
    <?php } ?>
</div>