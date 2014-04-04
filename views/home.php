<?php if (User::is_authenticated()) { ?>

    <div class="hero-unit">
        <h1>Yes you are here!</h1>
        <p>In the future this page will be your dashboard, so please waiting for news</p>
    </div>

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