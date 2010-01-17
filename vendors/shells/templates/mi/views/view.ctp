<?php /* SVN FILE: $Id: view.ctp 2066 2010-01-07 13:22:46Z AD7six $ */
if ($plugin) {
	ob_start();
}

$goto = array();
$chunkData = array(
	'display' => array(
		'class' => false,
		'size' => 1,
		'title' => true,
		'titleOnly' => true,
	),
	'athird' => array(
		'class' => 'athird',
		'size' => 3,
		'title' => false,
	),
	'half' => array(
		'class' => 'half',
		'size' => 2,
		'title' => true,
	),
	'default' => array(
		'class' => false,
		'size' => 1,
		'title' => true,
	),
	'large' => array(
		'class' => 'large',
		'size' => 1,
		'title' => true,
	)
);

$stacks = array(
	'display' => array(),
	'athird' => array(),
	'half' => array(),
	'default' => array()
);

if (!function_exists('ignore4View')) {

/**
 * ignore4View method
 *
 * @param mixed $field
 * @return void
 * @access public
 */
	function ignore4View($field) {
		if (in_array($field, array('password', 'slug', 'created', 'modified', 'updated', 'deleted', 'lft', 'rght'))) {
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
			'foreignKey' => $details['foreignKey'],
			'primaryKey' => $details['primaryKey'],
			'controller' => $details['controller']
		);
	}
}
if ($plugin) {
	$plugin .= '.';
}
$Inst = ClassRegistry::init($plugin . $modelClass);
$enumFields = array();
foreach($schema as $field => $data) {
	if ($data['type'] === 'integer' && $data['length'] <= 2) {
		$enumFields[] = $field;
	}
}
if ($Inst->Behaviors->attached('Enum')) {
	$enumFields = array_merge($enumFields, $Inst->enumFields());
}

echo "<?php\r\n"; ?>
extract($data);
<?php echo "\$this->set('title_for_layout', \${$modelClass}['$displayField']);\r\n"; ?>
<?php echo "?>\r\n"; ?>
<?php 
if (in_array('foreign_id', $fields)) {
	echo "\t\$linkedController = Inflector::underscore(Inflector::pluralize(\${$modelClass}['model']));\r\n";
}

foreach ($fields as $field):
	if (ignore4View($field)) {
		continue;
	}
	$stack = 'default';
	$tr = array();
	if (isset($keyFields[$field])) {
		$humanizedField = Inflector::Humanize(str_replace('_id', '', $field));
		$key = "__d('field_names', '$singularHumanName $humanizedField')";
		$display = "\${$keyFields[$field]['alias']}?\${$keyFields[$field]['alias']}['{$keyFields[$field]['displayField']}']:''";
		$goto[] = array('title' => "\${$keyFields[$field]['alias']}['{$keyFields[$field]['displayField']}']",
			'controller' => $keyFields[$field]['controller'], 'id' => "\${$singularVar}['$field']");
	} elseif ($field === 'foreign_id') {
		$key = "__('Linked to')";
		$display = "\$html->link(\${\$linkedController}[\${$singularVar}['foreign_id']], array('controller' => \$linkedController, 'action' => 'view', \${$singularVar}['foreign_id']))";

	} else {
		$humanizedField = Inflector::Humanize($field);
		if ((in_array($field, $enumFields))) {
			$key = "__d('field_names', '$singularHumanName $humanizedField')";
			$display = "\$enum->display('$singularVar.$field', \${$singularVar}['$field'])";
		} else {
			$key = "__d('field_names', '$singularHumanName $humanizedField')";
			$display = "\${$singularVar}['$field']";
		}
	}
	if ($field === $displayField) {
		$stack = 'display';
	} elseif (isset($keyFields[$field])) {
		$stack = 'half';
	} elseif (in_array($schema[$field]['type'], array('time', 'date'))) {
		$stack = 'athird';
	} elseif (in_array($schema[$field]['type'], array('datetime'))) {
		$stack = 'half';
	} elseif (in_array($schema[$field]['type'], array('text'))) {
		$stack = 'large';
	} elseif (!empty($schema[$field]['length'])) {
		if ($schema[$field]['length'] < 15) {
			$stack = 'athird';
		} elseif ($schema[$field]['length'] < 55) {
			$stack = 'half';
		} else {
			$stack = 'default';
		}
	}
	$stacks[$stack]["<?php $key ?>"] = "<?php echo $display; ?>";
endforeach; 
$zebra = 'odd';
$chunks = array();
foreach($stacks as $stack => $fields) {
	$stackData = $chunkData[$stack];
	$chunks = array_chunk($fields, $stackData['size'], true);
	$class = null;
	$title = $stack;
	if (!empty($stackData['titleOnly'])) {
		$title = current($chunks[0]);
		$chunks = array();
	}
	if ($stackData['title']) {
		echo "<h3>$title</h3>\r\n";
		$zebra = 'odd';
	}
	foreach($chunks as $rows) {
		if (!empty($stackData['class'])) {
			$class = ' ' . $stackData['class'];
		}
		echo "<div class=\"{$zebra} clearfix\">\r\n";
		foreach($rows as $name => $value) {
			echo "\t<div class=\"field{$class}\">\r\n";
			echo "\t\t<div class=\"name\">$name</div>\r\n";
			echo "\t\t<div class=\"value\">$value</div>\r\n";
			echo "\t</div>\r\n";
		}
		echo "</div>\r\n";
		$zebra = $zebra=='odd'?'even':'odd';	
	}
}
echo "<?php\r\n"; ?>
$menu->settings(__('This <?php echo $singularHumanName ?>', true));
$menu->add(array(
	array('title' => __('Edit', true), 'url' => array('action' => 'edit', $<?php echo $singularVar ?>['id'])),
	array('title' => __('Delete', true), 'url' => array('action' => 'delete', $<?php echo $singularVar ?>['id']))
));<?php
if ($goto):
?>

$menu->settings(__('View', true));
<?php
endif;
foreach ($goto as $array) {
	extract($array);
	echo "if (!empty($title)) {\r\n";
	echo "\t\$menu->add(array(\r\n";
	echo "\t\tarray('title' => $title, 'url' => array('controller' => '$controller', 'action' => 'view', $id)),\r\n";
	echo "\t));\r\n";
	echo "}\r\n";
}
if ($plugin) {
	$contents = ob_get_clean();
	$contents = preg_replace('@__d\(\'@', '__d(\'' . str_replace('.', '_', Inflector::underscore($plugin)), $contents);
	echo preg_replace('@__\(@', '__d(\'' . str_replace('.', '', Inflector::underscore($plugin)) . '\', ', $contents);
}