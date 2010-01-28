<?php


/**
 * Enums config file
 *
 * This is an example config file used by the Enum model/behavior. It stores default
 * value => display pairs, and allows populating the Enum table without (necessarily) deriving
 * which Enums exist by inspection.
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2009, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2009, Andy Dawson
 * @link          www.ad7six.com
 * @package       base
 * @subpackage    base.config
 * @since         v 1.0 (08-Jun-2009)
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
$config = array(
	'MiEmail.layout' => array(
	),
	'MiEmail.send_as' => array(
		'both' => __('Html & Text Email', true),
		'text' => __('Text Email', true),
		'html' => __('Html Email', true),
	),
	'MiEmail.status' => array(
		'dataProblem' => __('Problem with Email data', true),
		'pending' => __('Hasn\'t been sent yet', true),
		'sent' => __('Sent', true),
		'sendError' => __('Connection problem, or email rejected', true),
	),
	'MiEmail.template' => array(
		'contact/us' => __d('email_subjects', 'Contact Us', true),
		'users/account_change' => __d('email_subjects', 'Users Account Change', true),
		'users/forgotten_password' => __d('email_subjects', 'Users Forgotten Account Change', true),
		'users/new_token' => __d('email_subjects', 'Users New Token', true),
		'users/welcome' => __d('email_subjects', 'Users Welcome', true),
	),
	'MiEmail.type' => array(
		'private' => __('Private (not web accessible)', true),
		'newsletter_copy' => __('Newsletter copy', true),
		'normal' => __('Normal', true),
	),
	'User.group' => array(
		'admin' => __('Admin', true),
		'user' => __('User', true)
	),
);