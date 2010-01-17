<?php
extract($data);
extract ($data['User']) ?>
<p>Hello <?php echo trim($first_name); ?>,</p>

<p>This messages is to confirm that your <?php echo $change ?> has been changed.</p>
<?php if ($oldValue) : ?>
<p>Your old <?php echo $change ?> (<?php echo $oldValue ?>), is nolonger valid for your account.</p>
<?php endif; ?>
<br />
<p>If you have not changed your <?php echo $change ?> - please <?php echo $html->link('contact us', array(
	'controller' => 'contact',
	'action' => 'us',
	'category' => '!',
	'subject' => 'My ' . $change . ' has changed and I didn\'t request it',
	'from' => $email
)) ?> as soon as possible.</p>