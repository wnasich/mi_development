<?php
/**
 * PHP version 4 and 5
 *
 * Copyright (c) 2009, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2009, Andy Dawson
 * @link          www.ad7six.com
 * @package       debug_kit
 * @subpackage    debug_kit.vendors
 * @since         v 1.0 (22-Jun-2009)
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * TidyPanel class
 *
 * @uses          DebugPanel
 * @package       debug_kit
 * @subpackage    debug_kit.vendors
 */
class DevPanel extends DebugPanel {

/**
 * plugin property
 *
 * @var string 'debug_kit'
 * @access public
 */
	var $plugin = 'mi_development';

/**
 * title property
 *
 * @var string 'Tidy'
 * @access public
 */
	var $title = 'Dev';

/**
 * elementName property
 *
 * @var string 'tidy'
 * @access public
 */
	var $elementName = 'dev_panel';
}