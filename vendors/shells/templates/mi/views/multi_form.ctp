<?php /* SVN FILE: $Id: multi_form.ctp 2078 2010-01-08 14:20:17Z AD7six $ */
if ($plugin) {
	ob_start();
}
if (!function_exists('ignore4MultiForm')) {

/**
 * ignore4MultiForm method
 *
 * @param mixed $field
 * @return void
 * @access public
 */
	function ignore4MultiForm($field) {
		if (in_array($field, array('password', 'slug', 'created', 'modified', 'updated', 'deleted', 'lft', 'rght'))) {
			return true;
		}
		if (preg_match('/.*_count$/', $field)) {
			return true;
		}
		return false;
	}
}
$admin = (strpos($action, 'admin') === 0)?'admin_':'';
$namedString = implode ('\', \'', $fields);
$singularVar = ucfirst($singularVar);
$keyFields = array();
if (isset($associations['belongsTo'])) {
	foreach ($associations['belongsTo'] as $alias => $details) {
		$keyFields[$details['foreignKey']] = array(
			'alias' => $alias,
			'displayField' => $details['displayField'],
			'foreignKey' => $details['foreignKey']
		);
	}
}
echo "<?php\r\n"; ?>
<?php echo "\$this->set('title_for_layout', __('$pluralHumanName', true));\r\n"; ?>
<?php echo "echo \$form->create(); ?>\r\n"; ?>
<table>
<?php echo "<?php\r\n"; ?>
$th = array(
<?php foreach ($fields as $field):
	if (ignore4MultiForm($field)) {
		continue;
	}
?>
<?php if(!in_array($schema[$field]['type'], array('text'))) : ?>
	__d('field_names', '<?php echo $singularHumanName . ' ' . Inflector::humanize(str_replace('_id', '', $field)) ?>', true),
<?php endif; ?>
<?php endforeach; ?>
);
echo $html->tableHeaders($th);
foreach ($data as $i => $row) {
	if (!is_array($row) || !isset($row['<?php echo $modelClass ?>'])) {
		continue;
	}
	extract($row);
	$tr = array(
		array(
<?php foreach ($fields as $field) {
	if (ignore4MultiForm($field)) {
		continue;
	}
	if ($field == $primaryKey) {
		echo "\t\t\t\${$modelClass}['$field'] . \$form->input(\$i . '.$modelClass.$field', array('type' => 'hidden'))," . "\r\n";
		continue;
	}
	if(!in_array($schema[$field]['type'], array('text'))) {
		if (isset($keyFields[$field])) {
			echo "\t\t\t\$form->input(\$i . '.$modelClass.$field', array('div' => false, 'label' => false, 'empty' => true)),\r\n";
		} else {
			echo "\t\t\t\$form->input(\$i . '.$modelClass.$field', array('div' => false, 'label' => false)),\r\n";
		}
	}
}
?>
		),
	);
	$class = $i%2?'even':'odd';
	if ($this->action === '<?php echo $admin ?>multi_add') {
		$class .= ' clone';
	}
	echo $html->tableCells($tr, compact('class'), compact('class'));
}
<?php echo "?>\r\n"; ?>
</table>
<?php echo "<?php\r\n" ?>
echo $form->end(__('Submit', true));
if (isset($paginator) && $this->action !== '<?php echo $admin?>multi_add') {
	echo $this->element('paging');
}
<?php
if ($plugin) {
	$contents = ob_get_clean();
	$contents = preg_replace('@__d\(\'@', '__d(\'' . str_replace('.', '_', Inflector::underscore($plugin)), $contents);
	echo preg_replace('@__\(@', '__d(\'' . str_replace('.', '', Inflector::underscore($plugin)) . '\', ', $contents);
}