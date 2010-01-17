<?php
/* SVN FILE: $Id: controller.php 1508 2009-09-01 21:59:41Z ad7six $ */

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
 * @filesource
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       base
 * @subpackage    base.controllers
 * @since         v 1.0 (16-May-2009)
 * @version       $Revision: 1508 $
 * @modifiedby    $LastChangedBy: ad7six $
 * @lastmodified  $Date: 2009-09-01 23:59:41 +0200 (Tue, 01 Sep 2009) $
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