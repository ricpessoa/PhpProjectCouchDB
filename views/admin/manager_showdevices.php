<?php if (User::is_authenticated() && User::is_current_admin_authenticated()) { ?>
    <legend>Administrator Manage Devices </legend>
    <?php
    if ($numberDevices == 0) {
        ?>
        <div class = "alert alert-info">
            Do not have devices to add press "Add Device"
        </div>
    <?php } ?>
    <form action="<?php echo $this->make_route('/admin/manager_newdevice') ?>" method="get">	
        <button id="create_device" class="btn btn-success "><i class="icon-plus icon-white"></i> Add Device</button>
    </form>   
    <?php
    if ($numberDevices > 0) {
        ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Device MAC</th>
                    <th>Number of Sensors</th>
                    <th>Options</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($devices as $_device): ?>
                    <tr>
                        <td><?php echo $_device->_id; ?></td>
                        <td><?php echo "number of sensors: ".sizeof($_device->sensors); ?></td>
                        <td>Edit Delete</td>
                        <td><?php echo $_device->owner; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
}
?>

