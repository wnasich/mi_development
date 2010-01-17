<?php
extract ($data['User']) ?>
<p>Hello <?php echo trim($first_name); ?>,</p>

<p>Thanks for registering (Username: <?php echo $username?>)!</p>
<br />
<p>To verify your email and fully enable your account please <?php
echo $html->link('confirm your account', $html->url(array('admin' => false, 'controller' => 'users', 'action' => 'confirm', $token), true)); ?>
 within the next 24 hours:</p>
<br />
<p>If the above link does not work correctly your token is :</p>
<p><?php echo $token ?></p>
<br />
<p>If you did not request an account with us please ignore this email.</p>