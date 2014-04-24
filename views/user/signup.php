<script>
    $(function() {
        $("input,select,textarea").not("[type=submit]").jqBootstrapValidation();
    });
</script>

<div class="page-header">
    <h1>Signup</h1>
</div>

<div class="row">
    <div class="span12">

        <form action="<?php echo $this->make_route('/signup') ?>" method="post" >
            <div class="control-group">
                <label class="control-label" for="name">Name</label>
                <div class="controls">
                   <input type="text" minlength="3" name="full_name" id="full_name" value="<?php echo $ename; ?>" required/>
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email">Email address</label>
                <div class="controls">
                    <input type="email" name="email" id="email" value="<?php echo $email; ?>" required>
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="name">Username</label>
                <div class="controls">
                    <input type="text" minlength="5" name="username" id="username"value="<?php echo $eusername; ?>" required/>
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email">Password</label>
                <div class="controls">
                    <input type="password" minlength="5" name="password" id="password" required>
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Sign Up <i class="icon-ok icon-white"></i></button><br />
            </div>
        </form>
    </div>
</div>