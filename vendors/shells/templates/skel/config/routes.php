<?php
Router::parseExtensions('rss', 'xml', 'ajax');

Router::connect('/', array('controller' => 'forced', 'action' => 'error'));
Router::connect('/admin', array('admin' => true, 'controller' => 'contact', 'action' => 'index'));

/**
 * If the code reaches here, there  is no cached or vendor-served css/js/etc file.
 * Serve images and files that look like app-generated requests via the media controller
 */
/**
 * Forward missing media requests to the media serve funciton
 */
Router::connect(
	'/:mediaType/*',
	array('plugin' => 'media', 'controller' => 'media', 'action' => 'serve'),
	array('mediaType' => '(aud|doc|gen|ico|img|txt|vid)')
);

/**
 * Forward css and js requests to the asset serve funciton
 */
Router::connect(
	'/:mediaType/*',
	array('plugin' => 'mi_asset', 'controller' => 'asset', 'action' => 'serve'),
	array('mediaType' => '(css|js)')
);