<?php
/* SVN FILE: $Id: mi_i18n.php 2061 2010-01-04 14:53:05Z AD7six $ */

/**
 * Short description for i18n_dynamic.php
 *
 * Long description for i18n_dynamic.php
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
 * @subpackage    base.vendors.shells
 * @since         v 1.0
 * @version       $Revision: 2061 $
 * @modifiedby    $LastChangedBy: AD7six $
 * @lastmodified  $Date: 2010-01-04 15:53:05 +0100 (Mon, 04 Jan 2010) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
require_once (CAKE_CORE_INCLUDE_PATH . '/cake/console/libs/i18n.php');

/**
 * I18nDynamicShell class
 *
 * @uses          I18nShell
 * @package       base
 * @subpackage    base.vendors.shells
 */
class MiI18nShell extends I18nShell {

/**
 * tasks property
 *
 * @var array
 * @access public
 */
	var $tasks = array('MiExtract');

/**
 * startup method
 *
 * @access public
 * @return void
 */
	function startup() {
		$this->initialize();
		$this->loadTasks();
		$this->_welcome();
	}

/**
 * main method
 *
 * @access public
 * @return void
 */
	function main() {
		if (preg_match('@plugins[\\\/]([^\\\/]*)@', $this->params['working'], $matches)) {
			$this->MiExtract->settings['autoPlugin'] = false;
			$this->params['plugin'] = $matches[1];
		} elseif (!empty($this->params['plugin'])) {
			$this->MiExtract->settings['autoPlugin'] = false;
			$this->params['working'] .= DS . 'plugins' . DS . $this->params['plugin'] . DS;
		} else {
			$this->params['plugin'] = null;
		}
		$this->params['merge'] = 'no';
		$this->out(__d('mi_development', 'MiI18n Extraction Shell', true));
		$this->hr();
		$this->MiExtract->execute();
		$this->hr();
	}

/**
 * Show help screen.
 *
 * @TODO update
 * @access public
 */
	function help() {
		$this->hr();
		$this->out(__d('mi_development', 'MiI18n Shell:', true));
		$this->hr();
		$this->out(__d('mi_development', 'This shell is a complement, not a replacement, for the shipped I18n Shell', true));
		$this->out(__d('mi_development', 'You can use it to extract all field names and validation error messages for easy one-stop editing of ', true));
		$this->out(__d('mi_development', 'Validation error messages irrespective of the language.', true));
		$this->out(__d('mi_development', 'The reason this shell was created was to avoid the possiblity for error messages to get "confused"', true));
		$this->out(__d('mi_development', 'and to prevent the need to change code to edit these messages. See MiForm Helper for more info on', true));
		$this->out(__d('mi_development', 'how these definitions are used.', true));
		$this->hr();
		$this->out(__d('mi_development', 'usage:', true));
		$this->out('   cake mi_i18n help');
		$this->out('');
		$this->hr();

		$this->ExtractDynamic->help();
	}
}