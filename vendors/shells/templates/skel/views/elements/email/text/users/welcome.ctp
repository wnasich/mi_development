<?php
extract ($data['User']) ?>
Hello <?php echo trim($first_name); ?>,

Thanks for registering (Username: <?php echo $username?>)!

To verify your email and fully enable your account please click the following
link within the next 24 hours:
<?php echo $html->url(array('admin' => false, 'controller' => 'users', 'action' => 'confirm', $token), true) ?>


If the above link does not work correctly your token is :
<?php echo $token ?>


If you did not request an account with us please ignore this email.