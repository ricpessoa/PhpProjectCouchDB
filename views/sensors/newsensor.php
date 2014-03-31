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
                    submitSuccess: function($form, event) {
                        alert("OK");
                        event.preventDefault();
                    },
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
</script>

<form class="form-horizontal" action="<?php echo $this->make_route('/sensor') ?>" method="post">
    <div class="control-group">
        <label class="control-label">MAC Address:</label>
        <div class="controls">
            <input 
                type="text" 
                data-validation-regex-regex="a.*z" 
                data-validation-regex-message="Must start with 'a' and end with 'z'" 
                />
            <p class="help-block"></p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">Select your two favourite colours</label>
        <div class="controls">
            <label class="checkbox">
                <input 
                    type="checkbox" 
                    name="some_field" 
                    data-validation-minchecked-minchecked="1" 
                    data-validation-minchecked-message="Choose at least two" 
                    /> Red
            </label>
            <label class="checkbox">
                <input type="checkbox" name="some_field" /> Orange
            </label>
            <label class="checkbox">
                <input type="checkbox" name="some_field" /> Yellow
            </label>
            <label class="checkbox">
                <input type="checkbox" name="some_field" /> Green
            </label>
            <label class="checkbox">
                <input type="checkbox" name="some_field" /> Blue
            </label>
            <label class="checkbox">
                <input type="checkbox" name="some_field" /> Indigo
            </label>
            <label class="checkbox">
                <input type="checkbox" name="some_field" /> Violet
            </label>
            <p class="help-block"></p>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                Test Validation <i class="icon-ok icon-white"></i>
            </button>
        </div>
</form>