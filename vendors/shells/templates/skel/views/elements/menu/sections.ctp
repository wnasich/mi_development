<?php /* SVN FILE: $Id: sections.ctp 1523 2009-09-03 20:16:33Z ad7six $ */
if (!isset($sections)) {
	$sections = $menu->sections();
}
foreach ((array)$sections as $section) {
	$out = $menu->generate($section);
	if ($out) {
		echo '<div><h2><span>' . $section . '</span></h2>' . $out . '</div>';
	}
}
?>