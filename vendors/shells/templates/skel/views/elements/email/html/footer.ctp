<?php /* SVN FILE: $Id$ */ ?>
<div id="footer"><p><?php
	echo $html->link(sprintf(__('Sent from %s', true), substr(env('HTTP_BASE'), 1)), $html->url('/', true));
echo ' |  ' . $html->link(
	$html->image('cake.power.gif', array('alt'=> sprintf(__('Powered by %s', true), 'CakePHP : Rapid Development Framework'),'border'=>"0")),
	'http://www.cakephp.org', null, null, false); ?>
</p></div>