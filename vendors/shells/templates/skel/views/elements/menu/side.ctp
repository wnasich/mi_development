<?php /* SVN FILE: $Id: side.ctp 1541 2009-09-06 21:34:16Z ad7six $ */ ?>
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