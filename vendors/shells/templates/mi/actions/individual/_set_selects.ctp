	protected function _setSelects($restrictToData = true) {
<?php
$methodType = 'protected';
$sets = false;
$Inst =& ClassRegistry::init($currentModelName);
if (empty($Inst->Behaviors)) {
	echo "\t}";
	return;
}
if ($Inst->Behaviors->attached('Tree')) {
	$sets = true;
	echo "\t\t\$sets['parents'] = \$this->{$currentModelName}->generateTreeList();\n";
}
if ($Inst->Behaviors->attached('Enum')) {
	foreach ($Inst->actsAs['MiEnums.Enum'] as $enumeratedField) {
		$sets = true;
		$key = Inflector::variable(Inflector::pluralize(preg_replace('/_id$/', '', $enumeratedField)));
		echo "\t\t\$sets['$key'] = \$this->{$currentModelName}->enumValues('$enumeratedField');\n";
	}
}
if ($Inst->hasField('foreign_id')) {
	$sets = true;
?>
		if (is_array($this->data) && isset($this->data[0]) && is_array($this->data[0])) {
			$sets = $stacks = array();
			foreach($this->data as $row) {
				$stacks[$row['<?php echo $currentModelName ?>']['model']][$row['<?php echo $currentModelName ?>']['foreign_id']] = $row['<?php echo $currentModelName ?>']['foreign_id'];
			}
			$models = array();
			foreach($stacks as $model => $ids) {
				if (isset($models[$model])) {
					$Model = $models[$model];
				} else {
					$models[$model] = $Model = ClassRegistry::init($model);
				}
				$alias = Inflector::underscore(Inflector::pluralize($model));
				$sets[$alias] = $Model->find('list', array('conditions' => array(
					$Model->primaryKey => $ids
				)));
			}
		} else {
			$models = array_values(MiCache::mi('models'));
			$sets['models'] = array_combine($models, $models);
		}
	function _setSelects($restrictToData = true) {
<?php
}
$conditionSets = array();
foreach (array('hasOne', 'hasMany', 'belongsTo', 'hasAndBelongsToMany') as $type) {
	foreach (array_keys($Inst->$type) as $model) {
		if ($type === 'hasAndBelongsToMany') {
			$key = $Inst->{$type}[$model]['associationForeignKey'];
			$conditionSets[$key] = "array()";
		} elseif ($type === 'belongsTo') {
			$key = $Inst->{$type}[$model]['foreignKey'];
			$conditionSets[$model]["$model.id"] = "Set::extract(\$this->data, '/$currentModelName/$key')";
		} else {
			$key = $Inst->{$type}[$model]['foreignKey'];
			$conditionSets[$model]["$model.$key"] = "Set::extract(\$this->data, '/$currentModelName/id')";
		}
		if (!empty($Inst->{$type}[$model]['conditions'])) {
			foreach($Inst->{$type}[$model]['conditions'] as $_key => $_val) {
				$conditionSets[$model][$_key] = $_val;
			}
		}
		$key = Inflector::variable(Inflector::pluralize($model));
		if ($Inst->$model->Behaviors->attached('Tree')) {
			echo "\t\t\$sets['$key'] = \$this->{$currentModelName}->{$model}->generateTreeList();\n";
		} else {
			echo "\t\t\$conditions = \$this->_setSelectConditions('$model', \$restrictToData);\n";
			echo "\t\t\$sets['$key'] = \$this->{$currentModelName}->{$model}->find('list', compact('conditions'));\n\n";
		}
	}
}
if ($conditionSets) {
	echo "\t\t" . '$this->set($sets);' . "\n";
}
 ?>
	}
<?php
if (!$conditionSets) {
	return;
}
?>

	protected function _setSelectConditions($alias = '', $restrictToData = true, $conditions = array()) {
		if (!$restrictToData) {
			return $conditions;
		}
		switch ($alias) {
<?php
foreach($conditionSets as $key => $code) {
	echo "\t\t\tcase ('" . preg_replace('@\..*@', '', $key) . "'):\n";
	foreach($code as $_key => $_val) {
		echo "\t\t\t\t\$conditions['$_key'] = $_val;\n";
	}
	echo "\t\t\t\tbreak;\n";
}
echo "\t\t\tdefault:\n";
echo "\t\t\t\t\$conditions[\"\$alias.id\"] = Set::extract(\$this->data, '/$currentModelName/id');\n";
?>
		}
		return $conditions;
	}