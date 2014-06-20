<script type="text/javascript" src="<?php echo $this->make_route('/js/prettify.js') ?>"></script>

<script type="text/javascript">
    $(function() {

        prettyPrint();

        $("input,textarea,select").jqBootstrapValidation(
                {
                    preventSubmit: true,
                    submitError: function($form, event, errors) {
                        // Here I do nothing, but you could do something like display 
                        // the error messages to the user, log, etc.
                    },
                    /* submitSuccess: function($form, event) {
                     //alert("OK");
                     event.preventDefault();
                     },*/
                    filter: function() {
                        return $(this).is(":visible");
                    }
                }
        );

        $("a[data-toggle=\"tab\"]").click(function(e) {
            e.preventDefault();
            $(this).tab("show");
        });

    });

    $(document).ready(function() {
        $('#check_temperature').click(function() {
            if ($('#check_temperature').is(':checked')) {
                $('#div_propreties_temperature').collapse('show');
                $('#min_temp_notification').prop("required", true);
                $('#max_temp_notification').prop("required", true);
                $('#check_temperature_send').val("1");
                $('#check_temperature_send').attr('checked', true);
            } else {
                $('#div_propreties_temperature').collapse('hide');
                $('#min_temp_notification').prop("required", false);
                $('#max_temp_notification').prop("required", false);
                $('#check_temperature_send').val("0");
                $('#check_temperature_send').attr('checked', false);

            }
        });

        $('#check_gps').click(function() {
            if ($('#check_gps').is(':checked')) {
                $('#check_gps_send').val("1");
                $('#check_gps_send').attr('checked', true);
            } else {
                $('#check_gps_send').val("0");
                $('#check_gps_send').attr('checked', false);

            }
        });
        $('#check_panic_bt').click(function() {
            if ($('#check_panic_bt').is(':checked')) {
                $('#check_panic_bt_send').val("1");
                $('#check_panic_bt_send').attr('checked', true);
            } else {
                $('#check_panic_bt_send').val("0");
                $('#check_panic_bt_send').attr('checked', false);
            }
        });
        $('#check_battery_lvl').click(function() {
            if ($('#check_battery_lvl').is(':checked')) {
                $('#check_battery_lvl_send').val("1");
                $('#check_battery_lvl_send').attr('checked', true);
                $('#div_propreties_battery').collapse('show');
                $('#low_battery_notification').prop("required", true);
                $('#critical_battery_notification').prop("required", true);
            } else {
                $('#check_battery_lvl_send').val("0");
                $('#check_battery_lvl_send').attr('checked', false);
                $('#div_propreties_battery').collapse('hide');
                $('#low_battery_notification').prop("required", true);
                $('#critical_battery_notification').prop("required", true);
            }
        });
    });
</script>

<form action="<?php echo $this->make_route('/manager_newdevice') ?>" method="post">
    <legend>Add Device</legend>
    <div class="control-group">
        <label class="control-label">Name device:</label>
        <div class="controls">
            <input 
                id="name_device"
                name="name_device"
                type="text" />
            <p class="help-block">*not required</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">MAC Address:</label>
        <div class="controls">
            <input 
                id="mac_address"
                name="mac_address"
                type="text" 
                data-validation-regex-regex="a.*z" 
                data-validation-regex-message="Must start with 'a' and end with 'z'" 
                required/>
            <p class="help-block"></p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">Select the following sensors in the device:</label>
        <div class="controls">
            <label class="checkbox">
                <input 
                    id="check_temperature" 
                    type="checkbox" 
                    name="check_"
                    data-validation-minchecked-minchecked="1" 
                    data-validation-minchecked-message="Choose at least one sensor"
                    value="0"/> 
                Temperature
            </label>
            <label class="checkbox">
                <input id="check_gps" type="checkbox" name="check_" value="0"/> GPS
            </label>
            <label class="checkbox">
                <input id="check_panic_bt" type="checkbox" name="check_" value="0"/> Panic Button
            </label>
            <label class="checkbox">
                <input id="check_battery_lvl" type="checkbox" name="check_" value="0"/> Battery Level
            </label>
            <label class="checkbox" style="display:none">
                <input id="check_temperature_send" type="checkbox" name="check_temperature_send" value="0"/> Test1
            </label>
            <label class="checkbox" style="display:none">
                <input id="check_gps_send" type="checkbox" name="check_gps_send" value="0"/> Test2
            </label>
            <label class="checkbox" style="display:none">
                <input id="check_panic_bt_send" type="checkbox" name="check_panic_bt_send" value="0"/> Test3
            </label>
            <label class="checkbox" style="display:none">
                <input id="check_battery_lvl_send" type="checkbox" name="check_battery_lvl_send" value="0"/> Test4
            </label>
            <p class="help-block"></p>
        </div>
        <div class="control-group">
            <div id="div_propreties_temperature" class="collapse"> 
                <div class="well">
                    <h5>Temperature Settings</h5>
                    <div class="control-group">
                        <label class="control-label">Minimum Temperature Notification</label>
                        <div class="controls">
                            <input id="min_temp_notification" type="number" min="0" max="99" name="min_temp_notification" value="<?php echo 20 ?>"/>
                            <p class="help-block">Notifies the user when Minimum Temperature is exceeded</p>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Maximum Temperature Notification</label>
                        <div class="controls">
                            <input id="max_temp_notification" type="number" min="22" max="100" name="max_temp_notification" value="<?php echo 35 ?>"/>
                            <p class="help-block">Notifies the user when Maximum Temperature is exceeded</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="control-group">
            <div id="div_propreties_battery" class="collapse"> 
                <div class="well">
                    <h5>Battery Level Settings</h5>
                    <div class="control-group">
                        <label class="control-label">Low Battery Level Notification</label>
                        <div class="controls">
                            <input id="low_battery_notification" type="number" min="10" max="50" name="low_battery_notification" value="<?php echo 25 ?>"/>
                            <p class="help-block">Notifies the user when Low Battery Level is exceeded</p>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Critical Battery Level Notification</label>
                        <div class="controls">
                            <input id="critical_battery_notification" type="number" min="1" max="30" name="critical_battery_notification" value="<?php echo 15 ?>"/>
                            <p class="help-block">Notifies the user when Critical Battery Level is exceeded </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> Save Device </button><br />
    </div>
</form>


