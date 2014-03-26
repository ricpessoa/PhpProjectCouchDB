<!--<script>
function validateForm()
{
    var x = document.getElementById("email").value;
    var atpos = x.indexOf("@");
    var dotpos = x.lastIndexOf(".");
    if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= x.length)
    {
        alert("Not a valid e-mail address");
        //document.write("");
        document.body.innerHTML += "<div class='alert alert-error'><a class='close' data-dismiss='alert'>x</a>Error Email </div>";

       return false;
    }
    return true;
}
</script>-->
<script>
    $(function() {
        $("input,select,textarea").not("[type=submit]").jqBootstrapValidation();
    });
</script>

<div class="page-header">
    <h1>Signup</h1>
</div>
<!--
<div class="row">
    <div class="span12">
        <form  >
            <fieldset>
                <?php Bootstrap::make_input('full_name', 'Full Name', 'text'); ?>
                <?php Bootstrap::make_input('email', 'Email', 'text'); ?>
                <?php Bootstrap::make_input('username', 'Username', 'text'); ?>
                <?php Bootstrap::make_input('password', 'Password', 'password'); ?>

                <div class="form-actions">
                    <button class="btn btn-primary" onclick="javascript:return validateForm();">Sign Up!</button>
                </div>
            </fieldset>
        </form>

    </div>
</div>-->
<div class="row">
    <div class="span12">

        <form action="<?php echo $this->make_route('/signup') ?>" method="post" >
            <div class="control-group">
                <label class="control-label" for="name">Name</label>
                <div class="controls">
                    <input type="text" minlength="3" name="full_name" id="full_name" required/>
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email">Email address</label>
                <div class="controls">
                    <input type="email" name="email" id="email" required>
                    <p class="help-block"></p>
                </div>
            </div>
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
                <button type="submit" class="btn btn-primary">Sign Up <i class="icon-ok icon-white"></i></button><br />
            </div>
        </form>
    </div>
</div>