<?php
extract ($data['User']) ?>
Hello <?php echo trim($first_name); ?>,

A request has been made to reset your password.

To get a new password please go to the following url:
<?php echo $html->url(array('admin' => false, 'controller' => 'users', 'action' => 'reset_password', $token), true) ?>


Where you will be prompted to choose a new password. If the above link does not
work correctly your token is :
<?php echo $token ?>


If you didn't request to change your password, you can safely ignore this email.
Your password has not been changed nor given to anyone by us.