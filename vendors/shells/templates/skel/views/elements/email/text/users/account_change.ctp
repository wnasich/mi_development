<?php
extract($data);
extract ($data['User']) ?>
Hello <?php echo trim($first_name); ?>,

This messages is to confirm that your <?php echo $change ?> has been changed.

<?php if (!empty($oldValue)) : ?>

Your old <?php echo $change ?> (<?php echo $oldValue ?>), is nolonger valid for your account.
<?php endif; ?>

If you have not changed your <?php echo $change ?> - please contact us as soon
as possible.

<?php
echo $html->url(array(
	'controller' => 'contact',
	'action' => 'us',
), true);