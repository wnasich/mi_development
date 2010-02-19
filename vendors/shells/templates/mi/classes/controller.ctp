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
if ($plugin) {
	ob_start();
}
echo "<?php\n";
?>
class <?php echo $controllerName; ?>Controller extends <?php echo $plugin; ?>AppController {

	public $name = '<?php echo $controllerName; ?>';

<?php if ($isScaffold): ?>
	public $scaffold;
}
<?php
return;
endif;
?>
	public $components = array(<?php
if ($components) {
	echo "\n\t\t'" . implode( "',\n\t\t'", array_map(array('Inflector', 'camelize'), $components)) . "'";
}
echo "\n\t);\n";
?>

	public $helpers = array(<?php
if ($helpers) {
	echo "\n\t\t'" . implode("',\n\t\t'", array_map(array('Inflector', 'camelize'), $helpers)) . "'";
}
echo "\n\t);\n\n";
if (!empty($postActions)) {
$postActions[] = $admin . 'delete';
$postActions = array_unique(array_values($postActions));
sort($postActions);
?>
	public $paginate = array(
		'<?php echo $currentModelName ?>' => array(
		)
	);

	public $postActions = array(<?php
	echo "\n\t\t'" . implode( "',\n\t\t'", $postActions) . "'";
echo "\n\t);\n";
}
echo $actions;
if ($plugin) {
	$contents = ob_get_clean();
	echo preg_replace('@__\(@', '__d(\'' . Inflector::underscore($plugin) . '\', ', $contents);
}
?>
}