<?php
extract ($data['User']) ?>
<p>Hello <?php echo trim($first_name); ?>,</p>

<p>A request has been made to reset your password.</p>
<br />
<p>To get a new password please go to the following url:</p>
<p><?php echo $html->link($html->url(array('admin' => false, 'controller' => 'users', 'action' => 'reset_password', $token), true)) ?></p>
<br />
<p>Where you will be prompted to choose a new password. If the above link does not work correctly your token is :</p>
<p><?php echo $token ?></p>
<br />
<p>If you didn't request to change your password, you can safely ignore this email. Your password has not been changed nor given to anyone by us.</p>