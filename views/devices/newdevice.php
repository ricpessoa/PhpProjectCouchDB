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
    });
</script>

<form action="<?php echo $this->make_route('/device') ?>" method="post">
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
        <label class="control-label">Select your two favourite colours</label>
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
            <label class="checkbox" style="display:none">
                <input id="check_temperature_send" type="checkbox" name="check_temperature_send" value="0"/> Test1
            </label>
            <label class="checkbox" style="display:none">
                <input id="check_gps_send" type="checkbox" name="check_gps_send" value="0"/> Test2
            </label>
            <label class="checkbox" style="display:none">
                <input id="check_panic_bt_send" type="checkbox" name="check_panic_bt_send" value="0"/> Test3
            </label>
            <p class="help-block"></p>
        </div>
    </div>
    <div class="control-group">
        <div id="div_propreties_temperature" class="collapse"> 
            <div class="well">
                <h5>Temperature Settings</h5>
                <div class="control-group">
                    <label class="control-label">Minimum Temperature Notification</label>
                    <div class="controls">
                        <input id="min_temp_notification" type="number" min="22" max="28" name="min_temp_notification" />
                        <p class="help-block">NEED VALIDATE THE VALUE TO ABNORMAL LIKE hypothermia VALUES</p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Maximum Temperature Notification</label>
                    <div class="controls">
                        <input id="max_temp_notification" type="number" min="22" max="28" name="max_temp_notification" />
                        <p class="help-block">NEED VALIDATE THE VALUE TO ABNORMAL LIKE FEVER VALUES</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Add Device <i class="icon-ok icon-white"></i></button><br />
    </div>
</form>


