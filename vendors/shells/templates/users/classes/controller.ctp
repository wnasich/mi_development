<?php
/**
 * Controller bake template file
 *
 * Allows templating of Controllers generated from bake.
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
 * @subpackage    cake.
 * @since         CakePHP(tm) v 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

echo "<?php\n";
?>
class <?php echo $controllerName; ?>Controller extends <?php echo $plugin; ?>AppController {

	var $name = '<?php echo $controllerName; ?>';

<?php if ($isScaffold): ?>
	var $scaffold;
}
<?php
return;
endif;
?>
	var $components = array(<?php
if ($components) {
	echo "\n\t\t'" . implode( "',\n\t\t'", array_map(array('Inflector', 'camelize'), $components)) . "'";
}
echo ");\n";
?>

	var $helpers = array(<?php
if ($helpers) {
	echo "\n\t\t'" . implode("',\n\t\t'", array_map(array('Inflector', 'camelize'), $helpers)) . "'";
}
echo ");\n\n";

?>
	var $postActions = array(
		'admin_delete',
		'admin_sudo',
	);

<?php echo $actions; ?>

}