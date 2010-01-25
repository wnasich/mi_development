<?php /* SVN FILE: $Id$ */ ?>
<div id="sideBar"><?php
	$sections = $menu->sections();
	foreach ($sections as $section) {
		echo '<div><h2><span>' . $section . '</span></h2>';
		echo $menu->generate($section);
		echo '</div>';
	}
	echo $this->element('admin/search');
	echo $this->element('history');
?>
</div>