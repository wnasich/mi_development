<?php
/**
 * Model template file.
 *
 * Used by bake to create new Model files.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       cake
 * @subpackage    cake.console.libs.templates.objects
 * @since         CakePHP(tm) v 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

echo "<?php\n"; ?>
class <?php echo $name ?> extends <?php echo $plugin; ?>AppModel {

	var $name = '<?php echo $name; ?>';

<?php if ($useDbConfig != 'default'): ?>
	var $useDbConfig = '<?php echo $useDbConfig; ?>';

<?php endif;?>
<?php if ($useTable && $useTable !== Inflector::tableize($name)):
	$table = "'$useTable'";
	echo "\tvar \$useTable = $table;\n";
endif;
if ($primaryKey !== 'id'): ?>
	var $primaryKey = '<?php echo $primaryKey; ?>';

<?php endif;
if ($displayField): ?>
	var $displayField = '<?php echo $displayField; ?>';

<?php endif; ?>
/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array(
		'MiUsers.UserAccount' => array('passwordPolicy' => 'weak', 'token' => array('length' => 10)),
		'MiEnums.Enum' => array('group')
	);

/**
 * validate variable
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'username' => array(
			'missing' => array('rule' => 'notEmpty', 'last' => true),
			'alphaNumeric' => array('rule' => 'alphaNumeric', 'last' => true),
			'tooShort' => array('rule' => array('minLength', 3), 'last' => true),
			'isUnique'
		),
		'first_name' => array(
			'missing' => array('rule' => 'notEmpty')
		),
		'last_name' => array(
			'missing' => array('rule' => 'notEmpty')
		),
		'email' => array(
			'missing' => array('rule' => 'notEmpty', 'last' => true),
			'email' => array('rule' => 'email', 'last' => true),
			'isUnique'
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed
<?php

foreach (array('hasOne', 'belongsTo') as $assocType):
	if (!empty($associations[$assocType])):
		$typeCount = count($associations[$assocType]);
		echo "\n\tvar \$$assocType = array(";
		foreach ($associations[$assocType] as $i => $relation):
			$out = "\n\t\t'{$relation['alias']}' => array(\n";
			$out .= "\t\t\t'className' => '{$relation['className']}',\n";
			$out .= "\t\t\t'foreignKey' => '{$relation['foreignKey']}',\n";
			$out .= "\t\t\t'conditions' => '',\n";
			$out .= "\t\t\t'fields' => '',\n";
			$out .= "\t\t\t'order' => ''\n";
			$out .= "\t\t)";
			if ($i + 1 < $typeCount) {
				$out .= ",";
			}
			echo $out;
		endforeach;
		echo "\n\t);\n";
	endif;
endforeach;

if (!empty($associations['hasMany'])):
	$belongsToCount = count($associations['hasMany']);
	echo "\n\tvar \$hasMany = array(";
	foreach ($associations['hasMany'] as $i => $relation):
		$out = "\n\t\t'{$relation['alias']}' => array(\n";
		$out .= "\t\t\t'className' => '{$relation['className']}',\n";
		$out .= "\t\t\t'foreignKey' => '{$relation['foreignKey']}',\n";
		$out .= "\t\t\t'dependent' => false,\n";
		$out .= "\t\t\t'conditions' => '',\n";
		$out .= "\t\t\t'fields' => '',\n";
		$out .= "\t\t\t'order' => '',\n";
		$out .= "\t\t\t'limit' => '',\n";
		$out .= "\t\t\t'offset' => '',\n";
		$out .= "\t\t\t'exclusive' => '',\n";
		$out .= "\t\t\t'finderQuery' => '',\n";
		$out .= "\t\t\t'counterQuery' => ''\n";
		$out .= "\t\t)";
		if ($i + 1 < $belongsToCount) {
			$out .= ",";
		}
		echo $out;
	endforeach;
	echo "\n\t);\n\n";
endif;

if (!empty($associations['hasAndBelongsToMany'])):
	$habtmCount = count($associations['hasAndBelongsToMany']);
	echo "\n\tvar \$hasAndBelongsToMany = array(";
	foreach ($associations['hasAndBelongsToMany'] as $i => $relation):
		$out = "\n\t\t'{$relation['alias']}' => array(\n";
		$out .= "\t\t\t'className' => '{$relation['className']}',\n";
		$out .= "\t\t\t'joinTable' => '{$relation['joinTable']}',\n";
		$out .= "\t\t\t'foreignKey' => '{$relation['foreignKey']}',\n";
		$out .= "\t\t\t'associationForeignKey' => '{$relation['associationForeignKey']}',\n";
		$out .= "\t\t\t'unique' => true,\n";
		$out .= "\t\t\t'conditions' => '',\n";
		$out .= "\t\t\t'fields' => '',\n";
		$out .= "\t\t\t'order' => '',\n";
		$out .= "\t\t\t'limit' => '',\n";
		$out .= "\t\t\t'offset' => '',\n";
		$out .= "\t\t\t'finderQuery' => '',\n";
		$out .= "\t\t\t'deleteQuery' => '',\n";
		$out .= "\t\t\t'insertQuery' => ''\n";
		$out .= "\t\t)";
		if ($i + 1 < $habtmCount) {
			$out .= ",";
		}
		echo $out;
	endforeach;
	echo "\n\t);\n\n";
endif;
?>

	//Delete these callbacks if they are not going to edit them

/**
 * Called before each find operation. Return false if you want to halt the find
 * call, otherwise return the (modified) query data.
 *
 * @param array $queryData Data used to execute this query, i.e. conditions, order, etc.
 * @return mixed true if the operation should continue, false if it should abort; or, modified $queryData to continue with new $queryData
 * @access public
 * @link http://book.cakephp.org/view/680/beforeFind
 */
	function beforeFind($queryData) {
		return true;
	}

/**
 * Called after each find operation. Can be used to modify any results returned by find().
 * Return value should be the (modified) results.
 *
 * @param mixed $results The results of the find operation
 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 * @access public
 * @link http://book.cakephp.org/view/681/afterFind
 */
	function afterFind($results, $primary = false) {
		return $results;
	}

/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * @return boolean True if the operation should continue, false if it should abort
 * @access public
 * @link http://book.cakephp.org/view/683/beforeSave
 */
	function beforeSave($options = array()) {
		return true;
	}

/**
 * Called after each successful save operation.
 *
 * @param boolean $created True if this save created a new record
 * @access public
 * @link http://book.cakephp.org/view/684/afterSave
 */
	function afterSave($created) {
	}

/**
 * Called before every deletion operation.
 *
 * @param boolean $cascade If true records that depend on this record will also be deleted
 * @return boolean True if the operation should continue, false if it should abort
 * @access public
 * @link http://book.cakephp.org/view/685/beforeDelete
 */
	function beforeDelete($cascade = true) {
		return true;
	}

/**
 * Called after every deletion operation.
 *
 * @access public
 * @link http://book.cakephp.org/view/686/afterDelete
 */
	function afterDelete() {
	}

/**
 * Called during save operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @return boolean True if validate operation should continue, false to abort
 * @param $options array Options passed from model::save(), see $options of model::save().
 * @access public
 * @link http://book.cakephp.org/view/682/beforeValidate
 */
	function beforeValidate($options = array()) {
		return true;
	}

/**
 * Called when a DataSource-level error occurs.
 *
 * @access public
 * @link http://book.cakephp.org/view/687/onError
 */
	function onError() {
	}

/**
 * parentNode method
 *
 * @return void
 * @access public
 */
	function parentNode() {
		return $this->Behaviors->AclPlus->parentNode($this);
	}

/**
 * findList method
 *
 * List uses with their full name if possible
 *
 * @param mixed $state
 * @param mixed $query
 * @param array $results
 * @return void
 * @access protected
 */
	function _findList($state, $query, $results = array()) {
		if ($state === 'before' && isset($query['fields'])) {
			return parent::_findList($state, $query, $results);
		} elseif ($state === 'after' && isset($query['list'])) {
			return parent::_findList($state, $query, $results);
		}
		if (!$this->hasField('first_name') || !$this->hasField('last_name')) {
			if ($this->hasField('username')) {
				$this->displayField = 'username';
			} else {
				$this->displayField = 'email';
			}
			return parent::_findList($state, $query, $results);
		}
		if ($state == 'before') {
			$query['recursive'] = -1;
			$query['order'] = array($this->alias . '.last_name', $this->alias . '.first_name');
			$query['fields'] = array($this->alias . '.first_name', $this->alias . '.last_name', $this->alias . '.id');
			return $query;
		} elseif ($state == 'after') {
			if (empty($results)) {
				return array();
			}
			$keyPath = "{n}.{$this->alias}.id";
			//$valuePath = array('{1}, {0}',
			$valuePath = array('{0} {1}',
				'{n}.' . $this->alias . '.first_name',
				'{n}.' . $this->alias . '.last_name'
			);
			return Set::combine($results, $keyPath, $valuePath);
		}
	}
}