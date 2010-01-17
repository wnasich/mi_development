<?php /* SVN FILE: $Id: admin_svn_up.ctp 1388 2009-08-04 15:20:32Z AD7six $ */ ?>
<h2>7 up..</h2>
<pre>
<?php echo implode($svnReturn, "\r\n") ?>
</pre>
<h2>Schema up..</h2>
<pre>
<?php echo implode($schemaReturn, "\r\n");
$last = array_pop($schemaReturn);
if (!$last) {
	$last = array_pop($schemaReturn);
}
if ($last != 'End update.' && $last != 'Schema is up to date.') {
	echo ' ' . $html->link('Run this update?', array('confirm' => true));
}
?>
</pre>