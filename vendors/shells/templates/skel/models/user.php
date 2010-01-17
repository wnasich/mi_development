<?php
class User extends AppModel {

	var $name = 'User';

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