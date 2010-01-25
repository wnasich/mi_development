<?php /* SVN FILE: $Id$ */ ?>
<div id="sideBar">
<?php
$topMenu = $menu->sections(0);
if ($topMenu) {
	echo $this->element('menu/sections', array('sections' => array($topMenu)));
}
echo $this->element('context');
echo $this->element('menu/sections');
?>
</div>