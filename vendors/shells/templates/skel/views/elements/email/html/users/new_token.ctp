<?php /* SVN FILE: $Id$ */
extract ($data) ?>
<p>Hello <?php echo trim($first_name); ?>,</p>
<br />
<p>Your previous token expired.</p>
<p>Please visit <?php echo $html->link($html->url(array('controller' => 'users', 'action' => 'confirm', $token), true)); ?> to change your password.</p>
<br />
<p>If the above link does not work correctly your token is :</p>
<p><?php echo $token ?></p>