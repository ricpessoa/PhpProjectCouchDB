<div class="page-header">
    <h1>My Profile</h1>
</div>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span4">
            <p style="text-align:center" class="lead"><b>Username: </b> <?php echo $profile->name; ?></p>
            <img src="<?php echo $profile->gravatar('150'); ?>" style=" display: block;margin-left: auto;margin-right: auto;" class="img-polaroid" />
            <br> <p style="text-align:center" class="lead"><b> Number of Devices: </b> <?php echo $numberDevices; ?></p>
        </div>
        <div class="span8">
            <form action="<?php echo $this->make_route('/edituser') ?>" method="post" >
                <div class="control-group">
                    <label class="control-label" for="name">Name</label>
                    <div class="controls">
                        <input type="text" minlength="3" name="full_name" id="full_name" value="<?php echo $profile->full_name; ?>" required/>
                        <p class="help-block"></p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="email">Email address</label>
                    <div class="controls">
                        <input type="email" name="email" id="email" value="<?php echo $profile->email; ?>" readonly="readonly">
                        <p class="help-block"></p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="mobile_phone">Mobile Phone</label>
                    <div class="controls">
                        <input type="number" minlength="9" name="mobile_phone" id="mobile_phone" value="<?php echo $profile->mobile_phone; ?>"/>
                        <p class="help-block"></p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="contry">Country</label>
                    <div class="controls">
                        <input type="text" minlength="5" name="country" id="country"value="<?php echo $profile->country; ?>"/>
                        <p class="help-block"></p>
                    </div>
                </div>
                <!--<div class="control-group">
                    <label class="control-label" for="email">Password</label>
                    <div class="controls">
                        <input type="password" minlength="5" name="password" id="password" required>
                        <p class="help-block"></p>
                    </div>
                </div>-->
                <button type="submit" class="btn btn-primary"> Save </button><br />
            </form>
        </div>
    </div>
</div>
