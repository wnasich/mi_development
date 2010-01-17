<?php
extract ($data);
?>
	<h3><?php echo $User['username'] ?></h3>
<?php
if ($User['pic']) {
	echo $this->element('thumb', array('data' => $User, 'size' => 'medium'));
}
?><dl>
	<dt>Username</dt>
	<dd><?php echo $User['username']; ?></dd>
	<dt>Name</dt>
	<dd><?php echo $User['first_name'] . ' ' . $User['last_name']; ?></dd>
	<dt>Email</dt>
	<dd><?php echo $User['email']; ?></dd>
</dl>
<?php
$menu->settings(__('Options', true));
$menu->add(array(
	array('title' => __('Your profile', true), 'url' => array('action' => 'profile', $session->read('Auth.User.username'))),
	array('title' => __('Edit your profile', true), 'url' => array('action' => 'edit')),
	array('title' => __('Change your password', true), 'url' => array('action' => 'change_password')),
));