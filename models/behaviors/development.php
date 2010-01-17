<?php
/* SVN FILE: $Id: development.php 1468 2009-08-18 22:22:16Z ad7six $ */

/**
 * Short description for development.php
 *
 * Long description for development.php
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
 * @subpackage    base.models.behaviors
 * @since         v 1.0
 * @version       $Revision: 1468 $
 * @modifiedby    $LastChangedBy: ad7six $
 * @lastmodified  $Date: 2009-08-19 00:22:16 +0200 (Wed, 19 Aug 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * DevelopmentBehavior class
 *
 * A behavior with development-only functionality
 *
 * @uses          ModelBehavior
 * @package       base
 * @subpackage    base.models.behaviors
 */
class DevelopmentBehavior extends ModelBehavior {

/**
 * name property
 *
 * @var string 'Development'
 * @access public
 */
	var $name = 'Development';

/**
 * errors property
 *
 * @var array
 * @access public
 */
	var $errors = array();

/**
 * beforeFind method
 *
 * @param mixed $Model
 * @param mixed $queryData
 * @return void
 * @access public
 */
	function beforeFind(&$Model, $queryData) {
		if (Configure::read() && $errors = $this->isObsoleteSyntax($queryData)) {
			trigger_error('DevelopmentBehavior::beforeFind - Obsolete syntax detected!');
			$data['trace'] =  "\r\n" .Debugger::trace();
			$data['data'] = $queryData;
			$data['errors'] = $errors;
			debug($data); //@ignore
			$this->errors = array();
		}
		return true;
	}

/**
 * Convenience method to enable debugging from any point in a models use - and enable logging
 * from that point forward (will remove any queries logged before the call)
 *
 * @param int $val
 * @param bool $force
 * @return void
 * @access public
 */
	function debug(&$Model, $val = 2, $force = false) { //@ignore
		if ($force || Configure::read()) {
			Configure::write('debug',$val);
			$db =& ConnectionManager::getDataSource($Model->useDbConfig);
			if ($val > 1) {
				$db->fullDebug = true;
				$db->_queriesCnt = 0;
				$db->_queriesTime = null;
				$db->_queriesLog = array();
				$db->_queriesLogMax = 200;
			} else {
				$db->fullDebug = false;
			}
		}
	}

/**
 * isObsoleteSyntax method
 *
 * Check queryData for obsolete syntax.
 * Double check it's not a html string before registering an error
 *
 * @param mixed $conditions
 * @param string $type
 * @return void
 * @access public
 */
	function isObsoleteSyntax($queryData, $type = null) {
		if ($type == 'find') {
			$test = (array)$queryData;
		} else {
			$test = (array)$queryData['conditions'];
		}
		foreach ($test as $key => $value) {
			if (is_array($value)) {
				$this->isObsoleteSyntax($value, 'find');
			} elseif (is_string($value) && strlen($value)) {
				if (in_array($value[0], array('>', '<', '=', '!'))) {
					if (preg_match('@^<(\w+).*>@u', $value)) {
						continue;
					}
					$i = $value[0];
					$value = substr($value, 1);
					$this->errors[] = "the character '$i' is in the value, it should be in the key. e.g. 'Model.field $i' => '$value', instead of 'Model.field' => '$i $value'";
				} elseif (strpos($value, 'LIKE ') !== false) {
					$value = substr($value, 5);
					$this->errors[] = "'Model.field LIKE' => $value, instead of 'Model.field' => 'LIKE $value'";
				}
			}
		}
		return $this->errors;
	}
}