<?php
/* SVN FILE: $Id: home.ctp 1467 2009-08-18 22:22:06Z ad7six $ */

/**
 * Home template
 *
 * Long description for file
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
 * @subpackage    base.vendors.shells.templates.views
 * @since         v 0.1
 * @version       $Revision: 1467 $
 * @modifiedby    $LastChangedBy: ad7six $
 * @lastmodified  $Date: 2009-08-19 00:22:06 +0200 (Wed, 19 Aug 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
echo '<?php /* SVN FILE: $Id' . '$ */ ?>' . "\r\n";
$output = "<h2>Sweet, \"".Inflector::humanize($app)."\" got Baked by CakePHP!</h2>\r\n";
$output .="
<?php
if(Configure::read() > 0):
	Debugger::checkSessionKey();
endif;
?>
<p>
<?php
	if (is_writable(TMP)):
		echo '<span class=\"notice success\">';
			__('Your tmp directory is writable.');
		echo '</span>';
	else:
		echo '<span class=\"notice\">';
			__('Your tmp directory is NOT writable.');
		echo '</span>';
	endif;
?>
</p>
<p>
<?php
	\$settings = Cache::settings();
	if (!empty(\$settings)):
		echo '<div class=\"notice success\">';
			echo '<p>';
				echo sprintf(__('The %1$s is being used for caching. To change the config edit APP/config/core.php ', true), '<em>'. \$settings['engine'] . 'Engine</em>');
			echo '</p>';
		echo '</div>';
	else:
		echo '<div class=\"notice\">';
			echo '<p>';
				__('Your cache is NOT working. Please check the settings in APP/config/core.php');
			echo '</p>';
		echo '</div>';
	endif;
?>
</p>
<p>
<?php
	\$filePresent = null;
	if (file_exists(CONFIGS . 'database.php')):
		echo '<span class=\"notice success\">';
			__('Your database configuration file is present.');
			\$filePresent = true;
		echo '</span>';
	else:
		echo '<span class=\"notice\">';
			__('Your database configuration file is NOT present.');
			echo '<br/>';
			__('Rename config/database.php.default to config/database.php');
		echo '</span>';
	endif;
?>
</p>
<?php
if (!empty(\$filePresent)):
	uses('model' . DS . 'connection_manager');
	\$db = ConnectionManager::getInstance();
	\$connected = \$db->getDataSource('default');
?>
<p>
<?php
	if (\$connected->isConnected()):
		echo '<span class=\"notice success\">';
			__('Cake is able to connect to the database.');
		echo '</span>';
	else:
		echo '<span class=\"notice\">';
			__('Cake is NOT able to connect to the database.');
		echo '</span>';
	endif;
?>
</p>\r\n";
$output .= "<?php endif;?>\r\n";
$output .= "<h3><?php __('Editing this Page') ?></h3>\r\n";
$output .= "<p>\r\n";
$output .= "<?php __('To change the content of this page, edit: ".$dir."pages".DS."home.ctp.<br />\r\n";
$output .= "To change its layout, edit: ".$dir."layouts".DS."default.ctp.<br />\r\n";
$output .= "You can also add some CSS styles for your pages at: ".$dir."webroot".DS."css.\r\n') ?>";
$output .= "</p>\r\n";