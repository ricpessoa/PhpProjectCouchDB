<?php if (User::is_authenticated()) { ?>
    <legend>My Dashboard</legend>
    <!-- <div class="hero-unit">
         <h1>Yes you are here!</h1>
         <p>In the future this page will be your dashboard, so please waiting for news</p>
     </div>-->

    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#pane1" data-toggle="tab">Device 1</a></li>
            <li><a href="#pane2" data-toggle="tab">Device 2</a></li>
            <li><a href="#pane3" data-toggle="tab">Device 3</a></li>
            <li><a href="#pane4" data-toggle="tab">Device 4</a></li>
        </ul>
        <div class="tab-content">
            <div id="pane1" class="tab-pane active">
                <?php include 'sensors/_gps.php'; ?>
            </div>
            <div id="pane2" class="tab-pane">
                <h4>Pane 2 Content</h4>
                <p> and so on ...</p>
            </div>
            <div id="pane3" class="tab-pane">
                <h4>Pane 3 Content</h4>
            </div>
            <div id="pane4" class="tab-pane">
                <h4>Pane 4 Content</h4>
            </div>
        </div><!-- /.tab-content -->
    </div><!-- /.tabbable -->


<?php } else { ?>

    <div class="hero-unit">
        <h1>Welcome to Backend!</h1>
        <p>This backend is a sample framework to connect with couchDB</p>
        <p> 
            <a href="<?php echo $this->make_route('/signup') ?>"  class="btn btn-primary btn-large">Signup Now</a>
            <a href="<?php echo $this->make_route('/login') ?>"  class="btn btn-success btn-large">Login</a>
        </p>
        <p>You can sign up or log in </p>
    </div>

<?php } ?>