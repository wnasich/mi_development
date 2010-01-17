<?php /* SVN FILE: $Id: sections.ctp 1467 2009-08-18 22:22:06Z ad7six $ */
if (!isset($sections)) {
	$sections = $menu->sections();
}
foreach ((array)$sections as $section) {
	$out = $menu->generate($section);
	if ($out) {
		echo '<div><h2><span>' . $section . '</span></h2>' . $out . '</div>';
	}
}