<?php
if (empty($data['versions']['large'])) {
	return;
}
if (!isset($div) || $div) {
	echo '<div class=\'photo\'>';
}
if (!isset($size)) {
	$size = 'thumb';
}
extract($data);
$full = '/img/' . $versions['large'];
$image =  $html->image($versions[$size]);
if (!isset($description)) {
	$description = 'A picture';
}
echo $html->link($image, $full, array('title' => $description, 'class' => 'image'), null, false);
if (isset($after)) {
	echo $after;
}
if (!isset($div) || $div) {
	echo '</div>';
}