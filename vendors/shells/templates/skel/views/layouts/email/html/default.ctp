<?php
/**
 * Use the default site layout, and then strip the css and send inline
 */
include (VIEWS . 'layouts/default.ctp');
$contents = ob_get_clean();
ob_start();
preg_match_all('@<script.*>.*</script>@iU', $contents, $matches);
foreach ($matches[0] as $match) {
	$contents = str_replace($match, '', $contents);
}
preg_match_all('@<link\s*rel="stylesheet"[^>]*type="text/css"[^>]*href="([^"]*)"[^>]*/>@i',  $contents, $result, PREG_PATTERN_ORDER);
App::import('Vendor', 'Mi.MiCompressor');
$styles = '';
foreach ($result[1] as $cssFile) {
	$contents = str_replace($result[0], '', $contents);
	$cssFile = str_replace($this->webroot . 'css/', '', $cssFile);
	if (file_exists(CSS . $cssFile)) {
		$styles .= file_get_contents(CSS . $cssFile);
	} else {
		$styles .= MiCompressor::serve($cssFile) . "\r\n";
	}
}
$base = $html->url('/', true);
$styleTag = '<style type="text/css">' . $styles . '</style>';
$contents = str_replace('</head>', $styleTag . '</head>', $contents);
$contents = str_replace('href="/', 'href="' . $base, $contents);
$contents = str_replace('src="/', 'src="' . $base, $contents);
$contents = preg_replace(array("@[\r\n\t]+@", '@>\s+<@', '@\s+@', '@\s?{\s?@', '@\s?}\s?@'), array(' ', '><', ' ', '{', '}'), $contents);
echo $contents;