<?php /* SVN FILE: $Id: bar.ctp 1466 2009-08-18 22:21:38Z ad7six $ */ ?>
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