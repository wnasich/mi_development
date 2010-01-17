<?php /* SVN FILE: $Id: index.ctp 1924 2009-11-24 22:53:36Z ad7six $ */ ?>
<table>
<?php
$this->set('title_for_layout', __d('mi', 'Web Contact Messsages', true));
$paginator->options(array('url' => $this->passedArgs));
$th = array(
	$paginator->sort('id'),
	$paginator->sort('status'),
	$paginator->sort(__d('mi', 'From', true), 'reply_to'),
	$paginator->sort('subject'),
	$paginator->sort(__d('mi', 'Sent', true), 'created'),
);
echo $html->tableHeaders($th);
foreach ($data as $row) {
	extract($row);
	$status = $MiEmail['status'];
	if ($status == 'spam') {
		$status = '<span title="Matching rules: ' . $MiEmail['data']['junk_rule_matches'] . '">' . $status .
			' (' . $MiEmail['data']['junk_score'] . ')</span>';
	}
	$tr = array(
		$html->link($MiEmail['id'], array('admin' => false, 'plugin' => 'mi_email', 'controller' => 'mi_email', 'action' => 'view', $MiEmail['id'])),
		$status,
		$MiEmail['reply_to'],
		$html->link(str_replace('Web Contact ', '', $MiEmail['subject']), array('plugin' => 'mi_email', 'controller' => 'mi_email', 'action' => 'text_preview', $MiEmail['id']), array('class' => 'popup', 'title' => 'popup preview (text format)')),
		$time->niceShort($MiEmail['created']),
	);
	echo $html->tableCells($tr, array('class' => 'odd'), array('class' => 'even'));
}
?>
</table>
<?php
echo $this->element('paging');
$menu->del(__d('mi', 'Options', true));
$menu->settings(__d('mi', 'Options', true));
$menu->add(array(
	array('title' => __d('mi', 'Spam', true), 'url' => array('MiEmail.status' => 'spam')),
	array('title' => __d('mi', 'Not Spam', true), 'url' => array('MiEmail.status' => 'sent'))
));