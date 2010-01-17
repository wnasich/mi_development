<?php
extract($data);
$this->set('title_for_layout', $User['id']);
?>
<h3><?php echo $User['id']; ?></h3>
<div class="odd clearfix">
	<div class="field athird">
		<div class="name"><?php __d('field_names', 'User Web Id') ?></div>
		<div class="value"><?php echo $User['web_id']; ?></div>
	</div>
	<div class="field athird">
		<div class="name"><?php __d('field_names', 'User Email Verified') ?></div>
		<div class="value"><?php echo $User['email_verified']; ?></div>
	</div>
</div>
<h3>half</h3>
<div class="odd clearfix">
	<div class="field half">
		<div class="name"><?php __d('field_names', 'User Username') ?></div>
		<div class="value"><?php echo $User['username']; ?></div>
	</div>
	<div class="field half">
		<div class="name"><?php __d('field_names', 'User Group') ?></div>
		<div class="value"><?php echo $enum->display('User.group', $User['group']); ?></div>
	</div>
</div>
<h3>default</h3>
<div class="odd clearfix">
	<div class="field">
		<div class="name"><?php __d('field_names', 'User Email') ?></div>
		<div class="value"><?php echo $User['email']; ?></div>
	</div>
</div>
<div class="even clearfix">
	<div class="field">
		<div class="name"><?php __d('field_names', 'User First Name') ?></div>
		<div class="value"><?php echo $User['first_name']; ?></div>
	</div>
</div>
<div class="odd clearfix">
	<div class="field">
		<div class="name"><?php __d('field_names', 'User Last Name') ?></div>
		<div class="value"><?php echo $User['last_name']; ?></div>
	</div>
</div>
<?php
$menu->settings(__('This User', true));
$menu->add(array(
	array('title' => __('Edit', true), 'url' => array('action' => 'edit', $User['id'])),
	array('title' => __('Delete', true), 'url' => array('action' => 'delete', $User['id']))
));