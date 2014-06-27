<?php if (User::is_authenticated() && User::is_current_admin_authenticated()) { ?>
    <legend>Administrator Manage Devices </legend>
    <?php
    $numberDevices = sizeof($devices);
    if ($numberDevices == 0) {
        ?>
        <div class = "alert alert-info">
            Do not have devices to add press "Add Device"
        </div>
    <?php } ?>
    <form action="<?php echo $this->make_route('/admin/manager_device') ?>" method="get">	
        <button id="create_device" class="btn btn-success "><i class="icon-plus icon-white"></i> Add Device</button>
    </form>   
    <?php
    if ($numberDevices > 0) {
        ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Device MAC</th>
                    <th>Device Name</th>
                    <th>Number of Sensors</th>
                    <th>User</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($devices as $_device): ?>
                    <tr>
                        <td><?php echo $_device->_id; ?></td>
                        <td><?php echo $_device->name_device; ?></td>
                        <td>
                            <?php
                            $nSensors = 0;
                            foreach ($_device->sensors as $_sensor) {
                                $nSensors++;
                            }
                            echo "number of sensors: " . $nSensors;
                            ?>
                        </td>
                        <td><?php echo $_device->owner; ?></td>
                        <td><?php if ($_device->owner != "") { ?>

                                <a class="btn btn-info btn-small" disabled><i class="icon-pencil icon-white"></i>  Edit</a>
                                <button class="open-deleteDeviceModal btn btn-danger  btn-small" disabled><i class="icon-trash icon-white"></i> Delete</button>
                            <?php } else { ?>
                                <a class="btn btn-info btn-small" href="<?php echo $this->make_route('/admin/manager_device/' . $_device->_id) ?>"><i class="icon-pencil icon-white"></i>  Edit</a>
                                <button data-toggle="modal" data-id="<?php echo $_device->_id; ?>" data-rev="<?php echo $_device->_rev; ?>" title="Delete this Device" class="open-deleteDeviceModal btn btn-danger  btn-small" href="#deleteDeviceModal"><i class="icon-trash icon-white"></i> Delete</button>
                            <?php } ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- MODAL DELETE DEVICE -->
        <div class="modal fade"  id="deleteDeviceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display:none">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Delete Device</h4>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to permanently delete the Device?
                    </div>
                    <div class="modal-footer">
                        <form id="form_delete_device" method="post">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
<script>
    $(document).on("click", ".open-deleteDeviceModal", function() {
        var myDocId = $(this).data('id');
        var myDocRev = $(this).data('rev');
        var finalURL = '/PhpProjectCouchDB/admin/deletedevice/' + myDocId + '/' + myDocRev;
        $(".modal-footer #form_delete_device").attr('action', finalURL);
    });
</script>