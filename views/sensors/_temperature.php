<form action="<?php echo $this->make_route('/configsensortemperature/' . $deviceID.'/'.$deviceREV); ?>" method="post">
    <div class="control-group">
        <div id="div_propreties_temperature"> 
            <div class="well">
                <h4>Temperature Settings</h4>
                <div class="control-group">
                    <label class="control-label">Minimum Temperature Notification</label>
                    <div class="controls">
                        <input id="min_temp_notification" type="number" min="0" max="99" name="min_temp_notification" value="<?php echo $sensor->min_temperature; ?>"/>
                        <p class="help-block">NEED VALIDATE THE VALUE TO ABNORMAL LIKE hypothermia VALUES</p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Maximum Temperature Notification</label>
                    <div class="controls">
                        <input id="max_temp_notification" type="number" min="22" max="100" name="max_temp_notification" value="<?php echo $sensor->max_temperature; ?>"/>
                        <p class="help-block">NEED VALIDATE THE VALUE TO ABNORMAL LIKE FEVER VALUES</p>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save <i class="icon-ok icon-white"></i></button><br />
            </div>
        </div>
    </div> 
</form>