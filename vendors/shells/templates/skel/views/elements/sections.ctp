<?php /* SVN FILE: $Id$ */
if (!isset($sections)) {
	$sections = $menu->sections();
}
foreach ((array)$sections as $section) {
	$out = $menu->generate($section);
	if ($out) {
		echo '<div><h2><span>' . $section . '</span></h2>' . $out . '</div>';
	}
}