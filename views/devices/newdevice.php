<script type="text/javascript" src="<?php echo $this->make_route('/js/prettify.js') ?>"></script>

<form action = "<?php echo $this->make_route('/devices/newdevice') ?>" method = "post">
    <legend>Add Device</legend>
    <div class = "control-group">
        <label class = "control-label">Device Name:</label>
        <div class = "controls">
            <input
                id = "name_device"
                name = "name_device"
                type = "text"
                value="<?php echo $deviceName; ?>"
                />
            <p class = "help-block">*not required</p>
        </div>
    </div>
    <?php if ($editDevice != TRUE) {
        ?>
        <div class="control-group">
            <label class="control-label">MAC Address:</label>
            <div class="controls">
                <input 
                    id="mac_address"
                    name="mac_address"
                    type="text" 
                    data-validation-regex-regex="^([0-9a-fA-F]{2}){5}([0-9a-fA-F]{2})$" 
                    data-validation-regex-message="Must start with 'a' and end with 'z'" 
                    required/>
                <p class="help-block"></p>
            </div>
        </div>
        <label class="checkbox" style="display:none">
            <input id="isEditDevice" type="checkbox" name="isEditDevice" value="0" checked/>
        </label>
    <?php } else { ?>
        <div class="control-group"style="display:none">
            <label class="control-label">MAC Address:</label>
            <div class="controls">
                <input 
                    id="mac_address"
                    name="mac_address"
                    type="text" 
                    value="<?php echo $deviceMacAddress ?>"
                    <p class="help-block"></p>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">MAC Address:</label>
            <div class="controls">
                <input 
                    id="disabled_mac_address"
                    name="disabled_mac_address"
                    type="text" 
                    value="<?php echo $deviceMacAddress ?>"
                    disabled
                    <p class="help-block"></p>
            </div>
        </div>
        <label class="checkbox" style="display:none">
            <input id="isEditDevice" type="checkbox" name="isEditDevice" value="1" checked/>
        </label>
    <?php } ?>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?php echo ($editDevice ? 'Edit Device' : 'Add device'); ?> </button><br />
    </div>
</form>

