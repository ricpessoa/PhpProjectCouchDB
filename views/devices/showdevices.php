<legend>My Devices</legend>
<?php if ($numberDevices == 0) { ?>
    <div class = "alert alert-info">
        Do not have devices to add press "Add Device"
    </div>
<?php }
?>
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
                        <button id="edit_device" class="btn btn-info btn-small">Edit</button>
                        <button data-toggle="modal" data-id="<?php echo $device->_id; ?>" data-rev="<?php echo $device->_rev; ?>" title="Add this item" class="open-AddBookDialog btn btn-danger  btn-small" href="#addBookDialog">Delete</button>
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
                                    <tr<!--style="background-color: #CCFFFF"-->
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


    <div class="modal fade" id="addBookDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <input type="submit" class="btn btn-danger" >
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).on("click", ".open-AddBookDialog", function() {
            var myDocId = $(this).data('id');
            var myDocRev = $(this).data('rev');
            ///device/delete/:id/:rev
            var finalURL = '/PhpProjectTutorial/device/delete/' + myDocId + '/' + myDocRev;
            console.log("url:" + finalURL);
            //$(".modal-body #docID").val(myDocId);
            //$(".modal-body #docREV").val(myDocRev);
            //$(".modal-footer #deleteSafezone").attr('href', '/PhpProjectTutorial/post/delete/bbbe194c3d63570156ebfd6f640002a4/1-1855d23396fe20c1bcdbf73dd49cce27');
            $(".modal-footer #form_delete_device").attr('action', finalURL);

        });
        $(document).on("click", ".deleteSafezone", function() {
            console.log("Click on delete safezone");
        });
    </script>
    <?php
}?>