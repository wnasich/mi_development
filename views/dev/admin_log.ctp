<?php /* SVN FILE $Id: admin_log.ctp 1444 2009-08-16 21:31:04Z ad7six $ */
foreach ($data as $log => $contents) {
	$title = Inflector::humanize(str_replace('.', '_', $log));
	echo '<h2>' . $title . '</h2><pre>';
	if ($contents) {
		echo implode($contents, "\r\n");
	} else {
		echo '--- No Messages ---';
	}
	echo '</pre>';
}
$baseUrl = array();
$menu->settings(__d('mi_development', 'Options', true));
$menu->add(array(
	array('title' => 'Show 100 lines', 'url' => am($baseUrl, array(100))),
	array('title' => 'Show 200 lines', 'url' => am($baseUrl, array(200))),
	array('title' => 'Show 500 lines', 'url' => am($baseUrl, array(500))),
	array('title' => 'Show 1000 lines', 'url' => am($baseUrl, array(1000))),
	array('title' => 'Show Entire File', 'url' => am($baseUrl, array(0))),
));