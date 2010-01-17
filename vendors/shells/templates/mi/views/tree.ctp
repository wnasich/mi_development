<?php 
if ($plugin) {
	ob_start();
}
echo "<?php\r\n"; ?>
<?php echo "\$this->set('pageTitle', __('$pluralHumanName', true));\r\n"; ?>
echo $tree->generate($data, array ('element' => 'admin/tree_node', 'class' => 'tree'));
$menu->settings(__('Options', true));
$menu->settings(__('Fix Problems', true));
$menu->add(array(
	array('title' => __("Verify Tree", true), 'url' => array('action' => 'verify')),
	array('title' => __("Rebuild Tree", true), 'url' => array('action' => 'recover')),
));
<?php
if ($plugin) {
	$contents = ob_get_clean();
	echo preg_replace('@__\(@', '__d(\'' . str_replace('.', '', Inflector::underscore($plugin)) . '\', ', $contents);
}