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
	foreach (array_unique($Inst->actsAs['MiEnums.Enum']) as $enumeratedField) {
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
<?php
}
$conditionSets = array();
foreach (array('hasOne', 'hasMany', 'belongsTo', 'hasAndBelongsToMany') as $type) {
	foreach (array_keys($Inst->$type) as $model) {
		if ($type === 'hasAndBelongsToMany') {
			$with = $Inst->{$type}[$model]['with'];
			$fKey = $Inst->{$type}[$model]['foreignKey'];
			$key = $Inst->{$type}[$model]['associationForeignKey'];
			$conditionSets[$model]["{$with}Filter.$key"] = "Set::extract(\$this->data, '/$with/$fKey')";
		} elseif ($type === 'belongsTo') {
			$key = $Inst->{$type}[$model]['foreignKey'];
			$conditionSets[$model]["$model.id"] = "Set::extract(\$this->data, '/$currentModelName/$key')";
		} else {
			$key = $Inst->{$type}[$model]['foreignKey'];
			$conditionSets[$model]["$model.$key"] = "Set::extract(\$this->data, '/$currentModelName/id')";
		}
		if (!empty($Inst->{$type}[$model]['conditions'])) {
			foreach($Inst->{$type}[$model]['conditions'] as $_key => $_val) {
				$conditionSets[$model][$_key] = "'$_val'";
			}
		}
		$key = Inflector::variable(Inflector::pluralize($model));
		if ($Inst->$model->Behaviors->attached('Tree')) {
			echo "\t\t\$sets['$key'] = \$this->{$currentModelName}->{$model}->generateTreeList();\n";
		} else {
			echo "\t\t\$conditions = \$this->_setSelectConditions('$model', \$restrictToData);\n";
			if (isset($Inst->hasAndBelongsToMany[$model])) {
				echo "\t\t\$recursive = 0;\n";
				echo "\t\t\$sets['$key'] = \$this->{$currentModelName}->{$model}->find('list', compact('conditions', 'recursive'));\n\n";
			} else {
				echo "\t\t\$sets['$key'] = \$this->{$currentModelName}->{$model}->find('list', compact('conditions'));\n\n";
			}
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
	if ($code) {
		echo "\t\t\tcase ('" . preg_replace('@\..*@', '', $key) . "'):\n";
		if (isset($Inst->hasAndBelongsToMany[$key])) {
			$with = $Inst->hasAndBelongsToMany[$key]['with'];
			echo "\t\t\t\t\$this->{$currentModelName}->{$key}->bindModel(array('hasOne' => array('{$with}Filter' => array('className' => '$with'))), false);\n";
		}
		if (!is_array($code)) {
			var_dump($code);
		}
		foreach($code as $_key => $_val) {
			echo "\t\t\t\t\$conditions['$_key'] = $_val;\n";
		}
	}
	echo "\t\t\t\tbreak;\n";
}
echo "\t\t\tdefault:\n";
$underscored = Inflector::underscore($currentModelName);
echo "\t\t\t\t\$conditions[\"\$alias.{$underscored}_id\"] = Set::extract(\$this->data, '/$currentModelName/id');\n";
?>
		}
		return $conditions;
	}