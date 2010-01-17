<?php
extract($data);
if (!$data[$modelClass]['id']) { // If included, this is the dummy top level node
	echo $html->link(__('Show All', true), array(null));
	return;
}
extract ($data);
extract ($$modelClass);
$params = array();
$params['title'] = $modelClass . ' : "' . $$displayField;
echo $html->link($$displayField, array($id)) . ' ';
$links = array();
$links[] = $html->link(__('Edit', true), array ('action' => 'edit', $id));
$links[] = $html->link(__('Add', true), array ('action' => 'add', $id));
$links[] = $html->link(__('Delete', true), array ('action' => 'delete', $id));
if (!$firstChild) {
	$links[] = $html->link('↑', array ('action' => 'move_up', $id), array ('title' => __('Move Previous - Up the tree', true)));
	$links[] = $html->link('↑↑↑', array ('action' => 'move_up', $id, 100), array ('title' => __('Move First - Up the tree', true)));
}
if (!$lastChild) {
	$links[] = $html->link('↓', array ('action' => 'move_down', $id), array ('title' => __('Move After - Down the tree', true)));
	$links[] = $html->link('↓↓↓', array ('action' => 'move_down', $id, 100), array ('title' => __('Move Last - Down the tree', true)));
}
if ($links) {
	echo '<ul class=\'tree-options\'><li>' . implode ($links, '</li><li>') . '</li></ul>';
}