<?php
if (empty($plugin)) {
	return;
}
if (!class_exists('Mi')) {
	App::import('Vendor', 'Mi.Mi');
	return;
}

$plugins = Mi::plugins();
if (in_array($plugin, $plugins)) {
}
?>
<h2><?php echo sprintf('Missing Plugin: %s', $plugin);?></h2>
<p class="error">
	<strong>Error: </strong>
	<?php echo sprintf('Looks like the plugin %1$s isn\'t installed. %2$s?', "<em>" . Inflector::classify($plugin) . "</em>",
		$html->link('Install it', array('admin' => true, 'plugin' => 'mi_development', 'controller' => 'dev', 'action' => 'install', Inflector::underscore($plugin))));
	?>
</p>
<br />