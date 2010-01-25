<?php /* SVN FILE: $Id$ */
$menu->del(__d('mi_development', 'Options', true));
$menu->settings(__d('mi_development', 'Options', true));
$menu->add(array(
	array('title' => 'Clear Tmp files', 'url' => array('action' => 'clear')),
	array('title' => 'Backup database (txt)', 'url' => array('action' => 'db_dump')),
	array('title' => 'Backup database (zip)', 'url' => array('action' => 'db_dump', 'zip')),
	array('title' => 'Backup database (Gzip)', 'url' => array('action' => 'db_dump', 'gzip')),
	array('title' => 'Backup database (Bzip2)', 'url' => array('action' => 'db_dump', 'bz2')),
	array('title' => 'View Log files', 'url' => array('action' => 'log')),
	array('title' => 'Update application', 'url' => array('action' => 'svn_up')),
));
if (Configure::read()) {
	$menu->add(array('title' => 'Import Database', 'url' => array('action' => 'db_import')));
}
?>
<p><?php __d('mi_development', 'Choose an option from the menu') ?></p>