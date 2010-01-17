<?php
$section = isset($section)?$section:__('main', true);
$menu->settings($section);
if (!empty($setup) && $setup === true) {
	$controllers = array(
		'Settings',
		'Contact',
		'Editor',
		'Emails',
		'Enums',
		'Dev'
	);
	if (!empty($additionalSetupControllers)) {
		$controllers = am($additionalSetupControllers, $controllers);
	}
} else {
	$controllers = Configure::listObjects('controller');
}
$ignore = empty($ignore)?array():$ignore;
$ignore = am($ignore, array('App', 'Tree', 'List', 'Pages', 'Lookup'));
$controllers = array_diff($controllers, $ignore);

foreach($controllers as $controller) {
	$controller = Inflector::underscore($controller);
	$title = Inflector::humanize($controller);
	$menu->add(array(
		'title' => __($title, true),
		'url' => array('admin' => true, 'prefix' => false, 'plugin' => null, 'controller' => $controller, 'action' => 'index'),
	));
}