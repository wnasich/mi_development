<?php /* SVN FILE: $Id$ */
if ($plugin) {
	ob_start();
}

if (!function_exists('ignore4Index')) {

/**
 * ignore4Index method
 *
 * @param mixed $field
 * @return void
 * @access public
 */
	function ignore4Index($field, $extras) {
		if (in_array($field, array('password', 'slug', 'created', 'modified', 'updated', 'deleted', 'lft', 'rght')) ||
			in_array($field, $extras)) {
			return true;
		}
		return false;
	}
}
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
if ($plugin) {
	$plugin .= '.';
}
$Inst = ClassRegistry::init($plugin . $modelClass);
$ignoreFields = array($primaryKey);
if (in_array('foreign_id', $fields)) {
	$ignoreFields[] = 'model';
}
$enumFields = array();
foreach($schema as $field => $data) {
	if ($data['type'] === 'boolean' || $data['type'] === 'integer' && $data['length'] <= 2) {
		$enumFields[] = $field;
	}
}
if ($Inst->Behaviors->attached('Enum')) {
	$enumFields = array_merge($enumFields, $Inst->enumFields());
}
?>
<?php echo "<?php\r\n"; ?>
echo $form->create(null, array('action' => 'multi_process'));
<?php echo "?>\r\n"; ?>
<table class="stickyHeader">
<?php echo "<?php\r\n"; ?>
<?php echo "\$this->set('title_for_layout', __('$pluralHumanName', true));\r\n"; ?>
$th = array(
	<?php echo "\$form->checkbox('Mark.all$pluralHumanName', array('class' => 'markAll')),\r\n"; ?>
<?php foreach ($fields as $field):
	if (ignore4Index($field, $ignoreFields)) {
		continue;
	}
?>
<?php if ($field === 'parent_id') : ?>
	__('Parent', true),
<?php elseif ($field === 'foreign_id') : ?>
	__('Linked to', true),
<?php elseif (isset($keyFields[$field])) : ?>
	<?php echo "\$paginator->sort('" . $keyFields[$field]['alias'] . '.' . $keyFields[$field]['displayField'] . "'),\r\n"; ?>
<?php elseif(!in_array($schema[$field]['type'], array('text'))) : ?>
	<?php echo "\$paginator->sort('{$field}'),\r\n"; ?>
<?php endif; ?>
<?php endforeach; ?>
	__('actions', true)
);
echo $html->tableHeaders($th);
foreach ($data as $i => $row) {
	extract($row);
<?php
if (in_array('foreign_id', $fields)) {
	echo "\t\$linkedController = Inflector::underscore(Inflector::pluralize(\${$modelClass}['model']));\r\n";
}
?>
<?php if (($Inst->Behaviors->attached('Tree') || $Inst->Behaviors->attached('List'))) : ?>
	$actions = array();
	if ($<?php echo $modelClass ?>['order'] > 1) {
		$actions[] = $html->link(' ', array('action' => 'move_up',  <?php echo "\${$modelClass}['$primaryKey']"; ?>),
			array('class' => 'mini-icon mini-arrowthick-1-n', 'title' => __('Move Up', true)));
	}
	$actions[] = $html->link(' ', array('action' => 'move_down',  <?php echo "\${$modelClass}['$primaryKey']"; ?>),
			array('class' => 'mini-icon mini-arrowthick-1-s', 'title' => __('Move Down', true)));
	$actions = array_merge($actions, array(
<?php else: ?>
	$actions = array(
<?php endif; ?>
		$html->link(' ', array('action' => 'edit', <?php echo "\${$modelClass}['$primaryKey']"; ?>),
			array('class' => 'mini-icon mini-pencil', 'title' => __('Edit', true))),
		$html->link(' ', array('action' => 'delete',  <?php echo "\${$modelClass}['$primaryKey']"; ?>),
			array('class' => 'mini-icon mini-close', 'title' => __('Delete', true)))
<?php if (($Inst->Behaviors->attached('Tree') || $Inst->Behaviors->attached('List'))) : ?>
	));
<?php else: ?>
	);
<?php endif; ?>
	$tr = array(
		array(
			<?php echo "\$form->checkbox('$modelClass.' . \${$modelClass}['$primaryKey'], array('class' => 'identifyRow')) .\r\n"; ?>
				<?php echo "\$html->link(\${$modelClass}['$primaryKey'], array('action' => 'view', \${$modelClass}['$primaryKey']), array('class' => 'hidden')),\r\n"; ?>
<?php foreach ($fields as $field):
	if (ignore4Index($field, $ignoreFields)) {
		continue;
	}
?>
<?php if ($field === 'parent_id') : ?>
			<?php echo "!empty(\${$modelClass}['parent_id'])?\$parents[\${$modelClass}['parent_id']]:'',\r\n"; ?>
<?php elseif ($field === 'foreign_id') : ?>
			$html->link(${$linkedController}[$<?php echo $modelClass ?>['foreign_id']], array('controller' => $linkedController, 'action' => 'view', $<?php echo $modelClass ?>['foreign_id'])),
<?php elseif (isset($keyFields[$field])): ?>
			<?php $alias = Inflector::variable(Inflector::pluralize($keyFields[$field]['alias']));
			echo "!empty(\${$alias}['$modelClass'])?\${$alias}[\${$modelClass}['$field']]:'',\r\n"; ?>
<?php elseif (!in_array($schema[$field]['type'], array('text'))) : ?>
<?php if (in_array($field, array('ip', 'signup_ip'))) : ?>
			<?php echo "long2ip(\${$modelClass}['$field'])" . ",\r\n"; ?>
<?php elseif ((in_array($field, $enumFields))) : ?>
			<?php echo "\$enum->display('$modelClass.$field', \${$modelClass}['$field']),\r\n"; ?>
<?php else : ?>
			<?php echo "\${$modelClass}['$field']" . ",\r\n"; ?>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
			implode($actions)
		),
	);
	$class = $i%2?'even':'odd';
	echo $html->tableCells($tr, compact('class'), compact('class'));
}
<?php echo "?>\r\n"; ?>
</table>
<div class="buttonChoice">
<p><?php echo "<?php __('For the selected  $pluralHumanName:') ?>"; ?></p>
<?php echo "<?php\r\n"; ?>
echo $form->submit(__('Delete', true), array('name' => 'deleteAll', 'div' => false));
echo $form->submit(__('Edit', true), array('name' => 'editAll', 'div' => false));
<?php
foreach($schema as $field => $data) {
	if ($data['type'] === 'boolean') {
?>
	echo $form->submit(__('<?php echo Inflector::humanize($field) ?>', true), array('name' => '<?php echo $field ?>All', 'div' => false));
	echo $form->submit(__('Un <?php echo Inflector::humanize($field) ?>', true), array('name' => 'un<?php echo ucfirst($field) ?>All', 'div' => false));
<?php
	}
}

?>
//echo $form->submit(__('Add to clipboard', true), array('name' => 'clipAll', 'div' => false));
echo $form->end();
<?php echo "?>\r\n"; ?>
</div>
<?php echo "<?php\r\n"; ?>
echo $this->element('mi_panel/paging');
$menu->settings(__('Options', true), array());
$menu->add(array(
	array('title' => __('New <?php echo $singularHumanName ?>', true), 'url' => array('action' => 'add')),
	array('title' => __('Add <?php echo $pluralHumanName ?>', true), 'url' => array('action' => 'multi_add')),
	//array('title' => __('Edit These <?php echo $pluralHumanName ?>', true), 'url' => am($this->passedArgs, array('action' => 'multi_edit')))
));
<?php
if ($plugin) {
	$contents = ob_get_clean();
	echo preg_replace('@__\(@', '__d(\'' . str_replace('.', '', Inflector::underscore($plugin)) . '\', ', $contents);
}