<legend>Edit Device <?php echo $deviceID; ?></legend>
<!-- Add Settings On sensor Temperature -->
<div class="well">
    <h4>Safezone Settings</h4>
    <?php if ($numberSafezones == 0) { ?>
        <div class = "alert alert-info">
            Do not have safezones in this sensor GPS to add press "Add Safezone"
        </div>
        <?php
    }
    //echo '$numberSafezones=' . $numberSafezones . "<br>";
    //echo '$jsonSafezones=' . $jsonSafezones . "<br>";
    ?>
    <form action="<?php echo $this->make_route('/safezone/newsafezone') ?>" method="post">	
        <button id="create_safezone" name="create_safezone" type="input" class="btn btn-success" value="<?php echo $_POST['edit_deviceID']; ?>">Add Safezone</button> 
    </form>

    <?php if ($numberSafezones != 0) { ?>
        <?php include '_safezone.php'; ?>
    <?php } ?>

</div>
        <?php include '_temperature.php'; ?>




