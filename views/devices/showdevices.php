<link href="<?php echo $this->make_route('/css/bootstrap-switch.min.css') ?>" rel="stylesheet">
<script src="<?php echo $this->make_route('/js/bootstrap-switch.min.js') ?>"></script>
<legend>My Devices</legend>

<?php
$numberDevices = sizeof($devices);
if ($numberDevices == 0) {
    ?>
    <div class = "alert alert-info">
        Do not have devices to add press "Add Device"
    </div>
<?php } ?>
<form action="<?php echo $this->make_route('/devices/newdevice') ?>" method="get">	
    <button id="create_safezone" class="btn btn-success "><i class="icon-plus icon-white"></i> Add Device</button>
</form>
<!--http://jsfiddle.net/whytheday/2Dj7Y/11/ see =) -->
<?php if ($numberDevices > 0) { ?>
    <table class="table table-striped">
        <thead>
            <tr>
            <tr>
                <th>Device Name</th>
                <th>Mac Address</th>
                <th>Options</th>
            </tr>
            </tr>
        </thead>
        <tbody>
            <?php
            $j = 0;
            $i = 1;
            foreach ($devices as $device):
                ?>
                <tr>
                    <td>
                        <a data-toggle="collapse" data-target="#demo<?php echo $i; ?>" class="accordion-toggle"><i class="icon-th-list large"></i>
                            <?php
                            if ($device->name_device != null) {
                                echo 'Device ' . $device->name_device;
                            } else {
                                echo 'Device ' . $i;
                            }
                            ?></a></td>
                    <td><?php echo $device->_id; ?></td>
                    <td>
                        <a class="btn btn-primary btn-small" href="<?php echo $this->make_route('/devices/client') ?>"><i class="icon-eye-open icon-white"></i> Monitoring </a>
                        <a class="btn btn-info btn-small" href="<?php echo $this->make_route('/devices/newdevice/' . $device->_id) ?>"><i class="icon-pencil icon-white"></i>  Edit</a>
                        <button data-toggle="modal" data-id="<?php echo $device->_id; ?>" data-rev="<?php echo $device->_rev; ?>" title="Delete this Device" class="open-deleteDeviceModal btn btn-danger  btn-small" href="#deleteDeviceModal"><i class="icon-trash icon-white"></i> Delete</button>
                    </td>
                </tr>
                <tr>
                    <td class="hiddenRow" colspan="3">
                        <div class="accordian-body collapse span6" id="demo<?php echo $i; ?>">
                            <table class="table table-hover " style="margin-top: 5px;margin-bottom: 5px;">
                                <thead>
                                    <tr>
                                    <tr>
                                        <th>Sensor</th>
                                        <th style="text-align: center">Options</th>
                                    </tr>
                                    </tr>
                                </thead>
                                <?php
                                foreach ($device->sensors as $sensor):
                                    ?>
                                    <tr>
                                        <td> <i class="icon-tag"></i> <?php echo $sensor->name_sensor; ?></td>
                                        <td>
                                            <?php
                                            if ($sensor->enable == TRUE) {
                                                if ($sensor->type == "panic_button" || $sensor->type == "shoe" ) {
                                                    ?>
                                                    <a id="bt_editsensor<?php echo $j; ?>" style="visibility:hidden;" class="btn btn-info btn-small" href="<?php echo $this->make_route('/sensors/editsensor/' . $device->_id . '/' . $sensor->type) ?>"><i class="icon-pencil icon-white"></i>  Settings</a>
                                                            <!--<button id="bt_editsensor<?php echo $j; ?>" type="button" style="visibility:hidden;" class="btn btn-info btn-small" onclick="<?php echo $this->make_route('/devices/editdevice/' . $device->_id) ?>"><i class="icon-pencil icon-white"></i> Settings </button>-->
                                                <?php } else { ?>
                                                    <a id="bt_editsensor<?php echo $j; ?>"  class="btn btn-info btn-small" href="<?php echo $this->make_route('/sensors/editsensor/' . $device->_id . '/' . $sensor->type) ?>"><i class="icon-pencil icon-white"></i>  Settings</a>

                                                <?php } ?>
                                                <input type="checkbox" name="my-checkbox" data-deviceid= "<?php echo $device->_id; ?>" data-idsensor="<?php echo $j; ?>" data-sensortype="<?php echo $sensor->type; ?>" checked>
                                            <?php } else { ?>
                                                <a id="bt_editsensor<?php echo $j; ?>" style="visibility:hidden;" class="btn btn-info btn-small" href="<?php echo $this->make_route('/sensors/editsensor/' . $device->_id . '/' . $sensor->type) ?>"><i class="icon-pencil icon-white"></i>  Settings</a>
                                                <input type="checkbox" name="my-checkbox" data-deviceid= "<?php echo $device->_id; ?>" data-idsensor="<?php echo $j; ?>" data-sensortype="<?php echo $sensor->type; ?>">
                                            <?php } ?>

                                        </td>
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

    <script>
        $("[name='my-checkbox']").bootstrapSwitch();

        $('input[name="my-checkbox"]').on('switchChange.bootstrapSwitch', function(event, state) {
            //console.log(this); // DOM element
            //console.log(event); // jQuery event    
            var deviceID = $(this).data('deviceid');
            var sensorID = $(this).data('idsensor');
            var value = 0;
            var sensorType = $(this).data('sensortype');
            console.log("sensorID:" + sensorID + "sensorTyoe:" + sensorType + " - " + state); // true | false
            if (state == true) {
                value = 1;
                if (sensorType != "panic_button")
                    document.getElementById('bt_editsensor' + sensorID).style.visibility = "";
            } else {
                value = 0;
                document.getElementById('bt_editsensor' + sensorID).style.visibility = "hidden";
            }
            changeEnable(deviceID, sensorType, value);
        });

        $(document).on("click", ".open-deleteDeviceModal", function() {
            var myDocId = $(this).data('id');
            var myDocRev = $(this).data('rev');
            var finalURL = '/PhpProjectCouchDB/deletedevice/' + myDocId + '/' + myDocRev;
            $(".modal-footer #form_delete_device").attr('action', finalURL);
        });

        /*/sensor/setsensorenable/' . $device->_id . '/' . $device->_rev . '/' . $sensor->type . '/' . 0*/
        function changeEnable(deviceID, sensorType, value) {
            //console.log("received: " + "deviceID:" + deviceID + ",sensorType:" + sensorType + ",value:" + value);

            $.ajax({
                type: 'POST',
                url: '/PhpProjectCouchDB/sensor/setsensorenable/' + deviceID + "/" + sensorType + "/" + value,
                context: $(this),
                success: function() {
                    //alert("success");
                },
                error: function(request, status, error) {
                    alert('An error occurred, please try again.');
                }
            });
        }
    </script>
<?php }
?>