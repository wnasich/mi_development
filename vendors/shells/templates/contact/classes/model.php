<?php
/* SVN FILE: $Id$ */

/**
 * Short description for contact.php
 *
 * Long description for contact.php
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       base
 * @subpackage    base.models
 * @since         v 1.0
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Contact class
 *
 * @uses          AppModel
 * @package       base
 * @subpackage    base.models
 */
class Contact extends MiAppModel {

/**
 * name property
 *
 * @var string "Contact"
 * @access public
 */
	var $name = "Contact";

/**
 * useTable variable
 *
 * @var bool
 * @access public
 */
	var $useTable = false;

/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array(
		'Mi.Suspect' => array(
			'fields' => array(
				'ip' => 'ip',
				'subject' => 'string',
				'body' => 'textarea',
				'from' => 'email',
				'from_user_id' => 'user_id',
				'url' => 'link'
			),
			'statusHam' => 'sent',
		)
	);

/**
 * validate property
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'subject' => array(
			'missing' => array('rule' => 'notEmpty'),
		),
		'body' => array(
			'missing' => array('rule' => 'notEmpty')
		),
		'from' => array(
			'missing' => array('rule' => 'notEmpty', 'last' => true),
			'invalid' => array('rule' => 'email'),
		),
		'url' => array(
			'invalid' => array('rule' => 'url', 'allowEmpty' => true),
		),

	);

/**
 * save method
 *
 * @param mixed $data
 * @return void
 * @access public
 */
	function save($data = null) {
		$this->__email();
		if ($data) {
			$this->data = $data;
		}
		if (!$this->validates()) {
			return false;
		}
		$spam = $this->isSpam();
		$domain = substr(env('HTTP_BASE'), 1);
		$domain = $domain?$domain:APP_DIR . '.com';
		$from = $this->data['Contact']['from'];
		$to = 'contact@' . $domain;
		$status = $spam?'suspect':'pending';
		$data = array(
			'subject' => 'Web Contact ' . $this->data['Contact']['category'] . ' ' .
				htmlspecialchars($this->data['Contact']['subject']),
			'reply_to' => $from,
			'from' => $from,
			'from_user_id' => $this->data['Contact']['from_user_id'],
			'to' => $to,
			'template' => 'contact/us',
			'data' => $this->data['Contact']
		);
		$this->MiEmail->create();
		$this->MiEmail->send($data, $status);
		return true;
	}

/**
 * suspectFind method
 *
 * @param string $type
 * @param array $params
 * @return void
 * @access public
 */
	function suspectFind($type = 'first', $params = array()) {
		$this->__email();
		foreach ($params['conditions'] as $key => $value) {
			unset ($params['conditions'][$key]);
			if ($key == 'user_id') {
				if (!$value) {
					return 0;
				}
				$params['conditions']['from_user_id'] = $value;
			} elseif ($key == 'body') {
				$serialized = serialize($key) . serialize($value);
				$params['conditions']['data LIKE'] = '%' . $serialized . '%';
			} else {
				$params['conditions'][$key] = $value;
			}
		}
		return $this->MiEmail->find($type, $params);
	}

/**
 * schema function
 *
 * @param bool $clear
 * @access public
 * @return void
 */
	function schema($clear = false) {
		if (!$this->_schema) {
			$tableData['value']['subject'] = array('type' => 'string', 'null' => null, 'default' => null, 'length' => 100);
			$tableData['value']['body'] = array('type' => 'text', 'null' => null, 'default' => null, 'length' => null);
			$tableData['value']['email'] = array('type' => 'string', 'null' => null, 'default' => null, 'length' => 100);
			$this->_schema = $tableData;
		}
		return $this->_schema;
	}

/**
 * email method
 *
 * @return void
 * @access private
 */
	function __email() {
		if (isset($this->MiEmail)) {
			return;
		}
		$this->MiEmail = ClassRegistry::init('MiEmail.MiEmail');
	}
}