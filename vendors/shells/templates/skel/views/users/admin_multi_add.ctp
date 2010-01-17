<?php
$this->set('title_for_layout', __('Users', true));
echo $form->create(); ?>
<table>
<?php
$th = array(
	__d('field_names', 'User Id', true),
	__d('field_names', 'User Web', true),
	__d('field_names', 'User Username', true),
	__d('field_names', 'User Email', true),
	__d('field_names', 'User Group', true),
	__d('field_names', 'User Email Verified', true),
	__d('field_names', 'User First Name', true),
	__d('field_names', 'User Last Name', true),
);
echo $html->tableHeaders($th);
foreach ($data as $i => $row) {
	if (!is_array($row) || !isset($row['User'])) {
		continue;
	}
	extract($row);
	$tr = array(
		array(
			$User['id'] . $form->input($i . '.User.id', array('type' => 'hidden')),
			$form->input($i . '.User.web_id', array('div' => false, 'label' => false)),
			$form->input($i . '.User.username', array('div' => false, 'label' => false)),
			$form->input($i . '.User.email', array('div' => false, 'label' => false)),
			$form->input($i . '.User.group', array('div' => false, 'label' => false)),
			$form->input($i . '.User.email_verified', array('div' => false, 'label' => false)),
			$form->input($i . '.User.first_name', array('div' => false, 'label' => false)),
			$form->input($i . '.User.last_name', array('div' => false, 'label' => false)),
		),
	);
	$class = $i%2?'even':'odd';
	if ($this->action === 'admin_multi_add') {
		$class .= ' clone';
	}
	echo $html->tableCells($tr, compact('class'), compact('class'));
}
?>
</table>
<?php
echo $form->end(__('Submit', true));
if (isset($paginator) && $this->action !== 'admin_multi_add') {
	echo $this->element('paging');
}