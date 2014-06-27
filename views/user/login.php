<script type="text/javascript" src="jquery.jcryption.3.0.1.js"></script>

<div class="page-header">
    <h1>Login</h1>
</div>
<!--
<div class="row">
    <div class="span12">
        <form action="<?php echo $this->make_route('/login') ?>" method="post">
            <fieldset>
                <?php Bootstrap::make_input('username', 'Username', 'text'); ?>
                <?php Bootstrap::make_input('password', 'Password', 'password'); ?>

                <div class="form-actions">
                    <button class="btn btn-primary">Login</button>
                </div>
            </fieldset>
        </form>
    </div>
</div>-->

<div class="row">
    <div class="span12">

        <form action="<?php echo $this->make_route('/login') ?>" method="post" >
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
                <button type="submit" class="btn btn-primary">Login <i class="icon-ok icon-white"></i></button><br />
            </div>
        </form>
    </div>
</div>
