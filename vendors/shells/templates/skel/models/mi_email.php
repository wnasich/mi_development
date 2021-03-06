<?php
/**
 * Mi Email
 *
 * A model based email solution to allow db events to trigger sending emails
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       mi
 * @subpackage    mi.models
 * @since         v 1.0
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * MiEmail class
 *
 * This class is used to configure the MiEmail behavior (smtp setings etc)
 * and to hanle auth if the standard emails controller is used
 *
 * @uses          AppModel
 * @package       mi
 * @subpackage    mi.models
 */
class MiEmail extends AppModel {

/**
 * name property
 *
 * @var string 'MiEmail'
 * @access public
 */
	var $name = 'MiEmail';

/**
 * displayField property
 *
 * @var string 'subject'
 * @access public
 */
	var $displayField = 'subject';

/**
 * useTable variable
 *
 * Set to false to use the this without saving to the database
 *
 * @var string
 * @access public
 */
	var $useTable = 'emails';

/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array(
		'MiEmail.Email' => array('sendAs' => 'text'),
		'Mi.Slugged',
		'MiEnums.Enum' => array('status', 'type', 'send_as', 'template', 'layout')
	);

/**
 * isAuthorized method
 *
 * Called only for the emails controller view action - restricts viewing an email on the web to 'normal' emails and
 * only for admin, the author or the recipient. The model id is set in the emails controller beforeFilter.
 *
 * @param mixed $user
 * @param mixed $controller
 * @param mixed $action
 * @return bool
 * @access public
 */
	function isAuthorized($user, $controller, $action) {
		if ($controller != 'Emails' || $action != 'read') {
			debug('Email model isAuthorized has been called'); //@ignore
			debug (Debugger::trace()); //@ignore
			return false;
		}
		if ($user['User']['group'] == 'admin') {
			return true;
		} elseif (!$this->id) {
			return false;
		}
		$data = $this->read(array('from_user_id', 'to_user_id', 'type'));
		$validUsers = array($data[$this->alias]['to_user_id'], $data[$this->alias]['from_user_id']);
		if ($data[$this->alias]['type'] == 'normal' && in_array($user['User']['id'], $validUsers)) {
			return true;
		}
		return false;
	}
}