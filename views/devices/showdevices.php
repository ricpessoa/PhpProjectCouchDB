<legend>My Devices</legend>
<?php if ($numberDevices == 0) { ?>
    <div class = "alert alert-info">
        Do not have devices to add press "Add Device"
    </div>
    <?php
}   ?>
<form action="<?php echo $this->make_route('/devices/newdevice') ?>" method="get">	
    <button id="create_safezone" class="btn btn-success">Add Device</button>
</form>
<!--http://jsfiddle.net/whytheday/2Dj7Y/11/ see =) -->
<?php if ($numberDevices > 0) { ?>
    <table class="table table-striped">
        <thead>
            <tr>
            <tr>
                <th>Name</th>
                <th>Mac Address</th>
                <th>Options</th>
            </tr>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($devices as $device):
                ?>
                <tr data-toggle="collapse" data-target="#demo<?php echo $i; ?>" class="accordion-toggle">
                    <td><?php
                        if ($device->name_device != null) {
                            echo 'Device ' . $device->name_device;
                        } else {
                            echo 'Device ' . $i;
                        }
                        ?></td>
                    <td><?php echo $device->_id; ?></td>
                    <td>
                        <form style="margin-bottom: 0px;"action="<?php echo $this->make_route('/devices/newdevice') ?>" method="get">
                            <button id="edit_device" class="btn btn-info btn-small">Edit</button>
                            <button id="delete_device" class="btn btn-danger btn-small">Delete</button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td class="hiddenRow" colspan="3">
                        <div class="accordian-body collapse span6" id="demo<?php echo $i; ?>"> 
                            <table class="table table-hover " style="margin-top: 5px;margin-bottom: 5px;">
                                <?php
                                $j = 0;
                                foreach ($device->sensors as $sensor):
                                    ?>
                                    <tr style="background-color: #CCFFFF">
                                           <!-- <td>Sensor <?php echo $j; ?></td>-->
                                        <td>Sensor <?php echo $sensor->type; ?></td>
                                        <td>Options</td>
                                    </tr>
                                    <?php
                                    $j = $j + 1;
                                endforeach;
                                ?>
                            </table> 
                        </div> 
                    </td>
                </tr>
                <?php
                $i = $i + 1;
            endforeach;
            ?>
        </tbody>
    </table>
<?php
}?>