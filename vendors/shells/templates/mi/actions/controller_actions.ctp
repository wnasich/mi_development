<?php
/**
 * Bake Template for Controller action generation.
 *
 *
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
 * @subpackage    cake.console.libs.template.objects
 * @since         CakePHP(tm) v 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
$methodTypes = array('callbacks' => array(), 'actions' => array(), 'protected' => array(), 'private' => array());
$Folder = new Folder(dirname(__FILE__) . DS . 'individual');
$templates = $Folder->find('.*\.ctp');
foreach ($templates as $file) {
	$methodType = 'actions';
	$name = $file;
	ob_start();
	include (dirname(__FILE__) . DS . 'individual' . DS . $file);
	$contents = rtrim(ob_get_clean());
	if ($methodType === 'action') {
		$name = $admin . $name;
	}
	$methodTypes[$methodType][$name] = $contents;
}
foreach($methodTypes as &$methods) {
	if (empty($methods)) {
		continue;
	}
	ksort($methods);
	$methods = array_filter($methods);
	$methods = implode("\n\n", $methods);
}
$allMethods = array_filter($methodTypes);
echo implode("\n\n", $allMethods) . "\n";