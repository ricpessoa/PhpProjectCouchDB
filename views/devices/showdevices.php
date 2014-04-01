
<form action="<?php echo $this->make_route('/devices/newdevice') ?>" method="get">	
    <button id="create_safezone" class="btn btn-success">Add Device</button>
</form>
<!--http://jsfiddle.net/whytheday/2Dj7Y/11/ see =) -->
<table class="table table-bordered">
    <caption>Devices</caption>
    <thead>
        <tr>
            <th>ID</th>
            <th>Mac Address</th>
            <th>Options</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($devices as $device):?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $device->_id; ?></td>
                <td>
                    <button id="edit_device" class="btn btn-info btn-small">Edit</button>
                    <button id="delete_device" class="btn btn-danger btn-small">Delete</button>
                </td>
            </tr>
            <?php
            $i = $i + 1;
        endforeach;
        ?>
    </tbody>
</table>