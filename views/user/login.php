<!--<script type="text/javascript" src="<?php echo $this->make_route('/js/jquery.jcryption.3.0.1.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->make_route('/js/prettify.js') ?>"></script>-->
<div class="page-header">
    <h1>Login</h1>
</div>
<div class="row">
    <div class="span12">
        <form id="formlogin" action="<?php echo $this->make_route('/login') ?>" method="post" >
            <div class="control-group">
                <label class="control-label" for="name">Username</label>
                <div class="controls">
                    <input type="text" minlength="5" name="username" id="username" required/>
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
                <button type="submit" class="btn btn-primary">Login</button><br />
            </div>
        </form>
    </div>
</div>

<!--<script type="text/javascript">
    $(function() {
        $("#formlogin").jCryption();
    });
</script>-->
