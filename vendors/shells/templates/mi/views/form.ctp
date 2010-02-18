<?php /* SVN FILE: $Id$ */
if ($plugin) {
	ob_start();
}

if (!function_exists('ignore4Form')) {

/**
 * ignore4Form method
 *
 * @param mixed $field
 * @param mixed $action
 * @param mixed $primaryKey
 * @return void
 * @access public
 */
	function ignore4Form($field, $action, $primaryKey) {
		if ($action == 'add' && $field == $primaryKey) {
			return true;
		}
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
if (isset($associations['hasAndBelongsToMany'])) {
	foreach ($associations['hasAndBelongsToMany'] as $alias => $details) {
		$fields[] = $alias;
	}
}
foreach($fields as $i => $field) {
	if (ignore4Form($field, $action, $primaryKey)) {
		unset($fields[$i]);
	}
}
if (count($fields > 12))  {
	$fieldsets = array_chunk($fields, 10);
	$multiTab = true;
} else {
	$fieldsets = array($fields);
}
if (!empty($multiTab)) :
?>
<div id="tabWrap" class="form-container">
<ul>
	<?php foreach($fieldsets as $i => $_): $i++; ?>
	<li><a href="#tab<?php echo $i?>">Tab <?php echo $i ?></a></li>
	<?php endforeach; ?>
</ul>
<?php
endif;
echo "<?php\r\n"; ?>
if ($this->action === '<?php echo $admin ?>add') {
	$this->set('title_for_layout', __('New <?php echo $singularHumanName ?>', true));
} else {
	$this->set('title_for_layout', __('Edit <?php echo $singularHumanName ?>', true));
}
<?php echo "?>\r\n"; ?>
<div class="form-container">
<?php echo "<?php\r\n"; ?>
echo $form->create(null, array('type' => 'file')); // Default to enable file uploads
<?php
$wysiwyg = array();
foreach ($fieldsets as $i => $fields) {
	$i++;
	if (!empty($multiTab)) {
		echo 'echo \'<div id="tab'. $i . '">\';' . "\r\n";
	}
?>
echo $form->inputs(array(
	'legend' => false,
<?php
	foreach ($fields as $field) {
		if (empty($schema[$field]['type'])) {
			echo "\t'$field',\r\n";
			continue;
		}
		if ($schema[$field]['type'] === 'text') {
			$wysiwyg[] = '#' . Inflector::classify($modelClass . '_' . $field);
		}
		if (isset($keyFields[$field])) {
			echo "\t'$field' => array('empty' => true),\r\n";
		} elseif ($schema[$field]['type'] === 'string' && $schema[$field]['length'] >= 100) {
			echo "\t'$field' => array('div' => 'wide input text'),\r\n";
		} else {
			echo "\t'$field',\r\n";
		}
	}
?>
));
<?php
	if (!empty($multiTab)) {
		echo 'echo \'</div>\';' . "\r\n";
	}
}
?>
echo $form->end(__('Submit', true));
<?php if (!empty($multiTab)) : ?>
$asset->js('jquery-ui.tabs', $this->name);
$asset->codeBlock('
	$(document).ready(function() {
		$("#tabWrap").tabs();
	});
');
<?php endif;
if ($wysiwyg) : ?>
echo $this->element('mi_panel/editor', array('process' => '<?php echo implode ($wysiwyg, ', ') ?>'));
<?php endif;
if ($plugin) {
	$contents = ob_get_clean();
	echo preg_replace('@__\(@', '__d(\'' . str_replace('.', '', Inflector::underscore($plugin)) . '\', ', $contents);
}
echo "?>"; ?>
<?php if (!empty($multiTab)) : ?>
</div>
<?php endif; ?>
</div>