/*!
 * <?php echo $locale ?> Po File Generated with Mi CakePHP build shell
 *
 * Copyright (c) 2009, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2009, Andy Dawson
 * @link          www.ad7six.com
 * @package       base
 * @subpackage    base.vendors.shells.templates.js
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
var i18n = {
<?php
	foreach ($messages as $lookup => $string) {
		$lookup = str_replace("'", "\'", $lookup);
		$string = str_replace("'", "\'", $string);
		echo "\t'$lookup':'$string',\n";
	}
?>
}