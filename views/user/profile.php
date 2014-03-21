<div class="page-header">
	<h1><?php echo $user->full_name; ?>	
		<?php if ($is_current_user) { ?>
			<code>This is you!</code>
		<?php } ?>
	</h1>
</div>

<div class="container">
  <div class="row">
    <div class="span4">
      <div class="well sidebar-nav">
        <ul class="nav nav-list">
          <li><h3>User Information</h3></li>
          <li><b>Username:</b> <?php echo $user->name; ?></li>
          <li><b>Email:</b> <?php echo $user->email; ?></li>
        </ul>
      </div>
    </div>
  </div>
</div>