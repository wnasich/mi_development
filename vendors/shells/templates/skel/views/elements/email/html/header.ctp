<?php
if (!isset($emailData['MiEmail']['type']) || !in_array($emailData['MiEmail']['type'], array('normal', 'newsletter_copy'))) {
	return;
}
?>
<div class="message">Mail not displayed correctly? <?php
if ($emailData['MiEmail']['type'] == 'normal') {
	echo $html->link(__('See this message in your browser', true),
		array('full_base' => true, 'admin' => false, 'controller' => 'mi_email', 'action' => 'view',
			$emailData['MiEmail']['id'], Inflector::slug($emailData['MiEmail']['subject'])));
} else {
	echo $html->link(__('See this message in your browser', true),
		array('full_base' => true, 'admin' => false, 'controller' => 'mi_email', 'action' => 'newsletter',
			$emailData['MiEmail']['chain_id'], Inflector::slug($emailData['MiEmail']['subject'])));
}
?></div>