<?php


/**
 * Short description for lookup_controller.php
 *
 * Long description for lookup_controller.php
 *
 * PHP version 4 and 5
 *
 * Copyright (c) 2009, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       base
 * @subpackage    base.controllers
 * @since         v 1.0 (16-May-2009)
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * LookupController class
 *
 * @package       base
 * @subpackage    base.controllers
 */
class LookupController extends AppController {

/**
 * uses property
 *
 * @var mixed null
 * @access public
 */
	var $uses = null;

/**
 * admin_index method
 *
 * @param mixed $alias null
 * @param mixed $field null
 * @param mixed $input null
 * @return void
 * @access public
 */
	function admin_index($alias = null, $field = null, $input = null) {
		if (!$input && !empty($this->params['url']['q'])) {
			$input = $this->params['url']['q'];
		}
		if (!$input) {
			return $this->render('/elements/lookup_results', 'json');
		}

		$Model =& ClassRegistry::init($alias);
		foreach($Model->belongsTo as $_alias => $params) {
			if ($params['foreignKey'] === $field) {
				$Model = $Model->$_alias;
				$field = 'query';
				break;
			}
		}
		if ($field === 'query') {
			$Model->recursive = -1;
			$conditions = $Model->searchConditions($input, isset($this->passedArgs['extended']));
		} else {
			$conditions[$field . ' LIKE'] = $input . '%';
			$fields = array($field, $field);
		}

		$this->data = $Model->find('list', compact('conditions', 'fields'));
		$this->render('/elements/lookup_results', 'json');
	}
}