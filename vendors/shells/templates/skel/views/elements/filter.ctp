<?php
if ($this->action != 'admin_advanced_search') {
	echo $html->link('Toggle Filter', '#', array('id' => 'toggleFilter'));
}
if ($this->action == 'admin_advanced_search') {
	echo '<div id="searchFilter">';
} else {
	echo '<div id="resultFilter">';
}
$_data = $form->data;
$form->data = $session->read($modelClass . '.filterForm');
echo $form->create();
foreach ($filters as $filter => $settings) {
	if (!is_array($settings)) {
		$filter = $settings;
		$settings = array();
	}
	$settings = am(array('filterOptions' => $filterOptions), $settings);
	if (!$filterOptions) {
		echo $form->input($filter, $settings);
		continue;
	}
	$select = '';
	if ($settings['filterOptions']) {
		$selectOptions = am(array('empty' => true, 'div' => 'filter', 'label' => false, 'options' => $settings['filterOptions']));
		$select = $form->input($filter . '_type', $selectOptions);
	}
	unset($settings['filterOptions']);
	if (strpos($filter, 'date') || strpos($filter, 'expires') || $filter == 'created' || $filter == 'modified' || $filter == 'deleted') {
		$inputOptions = am(array('empty' => true, 'between' => $select), $settings);
	} else {
		$inputOptions = am(array('between' => $select, 'multiple' => true), $settings);
	}
	$input = $form->input($filter, $inputOptions);
	echo $input;
}
echo $form->end('apply filter');
$form->data = $_data;
?>
</div>