<?php /* SVN FILE: $Id$ */
extract ($data) ?>
Hello <?php echo trim($first_name); ?>,

Your previous token expired.

Please visit <?php echo $html->url(array('controller' => 'users', 'action' => 'confirm', $token), true); ?> to change your password.

If the above link does not work your token is:
<?php echo $token;