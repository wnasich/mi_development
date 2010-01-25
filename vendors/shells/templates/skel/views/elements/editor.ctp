<?php /* SVN FILE: $Id$ */
$javascript->link('markitup/jquery.markitup.pack.js', false);
$javascript->link('markitup/sets/default/set.js', false);
$html->css(array('/js/markitup/skins/markitup/style.css', '/js/markitup/sets/default/style.css'), null, array(), false);
$javascript->codeBlock(
'$(document).ready(function() {
	$("' . $process . '").markItUp(mySettings, {
		previewParserPath:"' . $html->url(am($this->passedArgs, array('action' => 'preview'))) . '",
	});
});', array('inline' => false));