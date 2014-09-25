<h3>Sensor State Shoe</h3>
<div class="panic<?php echo $i; ?>">
    <?php
    $msShoe = MSShoe::getMonitoringSensorByKeys(User::current_user(), $device->_id, "shoe");
    if ($msShoe != NULL && $msShoe->removed === TRUE) {
        ?>
        <div class="alert alert-danger">
            <h4>Alert!</h4>
            <?php echo 'The shoe was removed in ' . $msShoe->timestamp; ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-info">
            Not yet received any information from sensor shoe!
        </div>
    <?php } ?>
</div>