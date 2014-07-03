<form action="<?php echo $this->make_route('/configsensorbattery/' . $deviceID . '/' . $deviceREV); ?>" method="post">
    <div class="control-group">
        <div id="div_propreties_battery"> 
            <div class="well">
                <h4>Battery Level Settings</h4>
                <div class="control-group">
                    <label class="control-label">Low Battery Level Notification</label>
                    <div class="controls">
                        <input id="low_battery_notification" type="number" min="10" max="50" name="low_battery_notification" value="<?php echo $sensor->low_battery; ?>"/>
                        <p class="help-block">Notifies the user when Low Battery Level is exceeded </p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Critical Battery Level Notification</label>
                    <div class="controls">
                        <input id="critical_battery_notification" type="number" min="1" max="30" name="critical_battery_notification" value="<?php echo $sensor->critical_battery; ?>"/>
                        <p class="help-block">Notifies the user when Critical Battery Level is exceeded </p>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save <i class="icon-ok icon-white"></i></button><br />
            </div>
        </div>
    </div> 
</form>

