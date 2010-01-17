<?php
extract ($data);
$id = $this->passedArgs[0];
echo '<h1>' . $category . ' ' . htmlspecialchars($subject) . '</h1>';
if ($isEmail === 'web') {
	$menu->settings(__('Options', true));
	if ($spam > 0) {
		$menu->add(array('title' => __('Not spam', true), 'url' => array('admin' => true, 'action' => 'status', $id, 'pending')));
	} else {
		$menu->add(array('title' => __('Spam', true), 'url' => array('admin' => true, 'action' => 'status', $id, 'spam')));
	}
	$menu->add(array(
		array('title' => __('Delete', true),
			'url' => array('admin' => true, 'action' => 'delete', $id)),
		array('title' => __('Edit', true),
			'url' => array('admin' => true, 'action' => 'edit', $id)),
		array('title' => __('Recalculate Junk score', true),
			'url' => array('admin' => true, 'controller' => 'contact', 'action' => 'score', $id)),
		array('title' => __('All messages from this Ip', true),
			'url' => array('admin' => true, 'controller' => 'contact', 'action' => 'index', 'MiEmail.ip' => $ip)),
		array('title' => __('All messages from this sender', true),
			'url' => array('admin' => true, 'controller' => 'contact', 'action' => 'index', 'MiEmail.from' => $from)),
		array('title' => __('Block Ip', true),
			'url' => array('admin' => true, 'controller' => 'contact', 'action' => 'block', $ip)),
		array('title' => __('Block Email', true),
			'url' => array('admin' => true, 'controller' => 'contact', 'action' => 'block', $from)),
	));
}
?>
<br />
<p><?php echo nl2br($html->clean($body)); ?></p>
<em><p>From: <?php echo $html->clean($from);?></p>
<?php
if ($url) {
	echo '<p>Url: ' . $html->clean($url) . '</p>';
	echo '<br />';
}
echo '<p>IP: ' . long2ip($ip) . '</p>';
echo '<hr />';
echo '<h2>JUNK SCORE: ' . $junk_score . '</h2>';
echo '<p>Matching rules: </p><ul><li>' .
	implode('</li><li>', explode(';', $junk_rule_matches)) . '</li></ul>';
echo '<hr />';
echo '<br />';
?></em>