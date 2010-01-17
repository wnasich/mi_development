<?php echo "<?php\r\n"; ?>
extract ($data);
<?php echo "?>"; ?>

	<h3><?php echo "<?php"; ?> echo $User['username'] <?php echo "?>"; ?></h3>
<?php echo "<?php\r\n"; ?>
if ($User['pic']) {
	echo $this->element('thumb', array('data' => $User, 'size' => 'medium'));
}
<?php echo "?>"; ?>
<dl>
	<dt>Username</dt>
	<dd><?php echo "<?php"; ?> echo $User['username']; <?php echo "?>"; ?></dd>
	<dt>Name</dt>
	<dd><?php echo "<?php"; ?> echo $User['first_name'] . ' ' . $User['last_name']; <?php echo "?>"; ?></dd>
	<dt>Email</dt>
	<dd><?php echo "<?php"; ?> echo $User['email']; <?php echo "?>"; ?></dd>
</dl>
<?php echo "<?php\r\n"; ?>
$menu->settings(__('Options', true));
$menu->add(array(
	array('title' => __('Your profile', true), 'url' => array('action' => 'profile', $session->read('Auth.User.username'))),
	array('title' => __('Edit your profile', true), 'url' => array('action' => 'edit')),
	array('title' => __('Change your password', true), 'url' => array('action' => 'change_password')),
));