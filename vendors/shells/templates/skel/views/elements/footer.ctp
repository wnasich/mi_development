<div id="lowerMenu">
	<div><h3>Mi Projects</h3><?php
// set here or define in the app controller
$Menu = array(
	array('title' => 'Link 1', 'url' => '#', 'options' => array('htmlAttributes' => array('title' => 'Link 1'))),
	array('title' => 'Link 2', 'url' => '#', 'options' => array('htmlAttributes' => array('title' => 'Link 2'))),
	array('title' => 'Link 3', 'url' => '#', 'options' => array('htmlAttributes' => array('title' => 'Link 3'))),
	array('title' => 'Link 4', 'url' => '#', 'options' => array('htmlAttributes' => array('title' => 'Link 4'))),
);
$menu->settings(__('links', true));
$menu->add($Menu);
echo $menu->display(__('links', true));
	?></div>
	<div><h3>Want to make some mods?</h3><?php
$Menu = array(
	array('title' => 'Change the layout', 'url' => 'http://www.alistapart.com/articles/holygrail'),
	array('title' => 'Change those colors', 'url' => 'http://www.colourlovers.com'),
	array('title' => 'Change the menu design', 'url' => 'http://css.maxdesign.com.au/listamatic'),
	array('title' => 'Check out some projects', 'url' => 'http://cakeforge.org')
);
$menu->settings(__('mods', true));
$menu->add($Menu);
echo $menu->display(__('mods', true));
	?></div>
	<div>
		<h3>About</h3>
		<p>This is your newly baked applicaiton</p>
	</div><br style='clear:both;' />&nbsp;
</div>
<div id="footer"><p>
	An <?php echo$html->link('AD7six.com', 'http://www.ad7six.com'); ?> creation
	| Powered by <?php echo $html->link($html->image('cake.power.gif', array('alt'=>"CakePHP : Rapid Development Framework", 'border'=>"0")), 'http://www.cakephp.org', null, null, false); ?>
</p></div>