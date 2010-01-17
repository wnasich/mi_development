<div id='header' class='clearfix'>
	<form id="SearchForm" method="post" action="<?php echo $html->url('/search') ?>">
		<div class="input text">
			<input class="watermark" name="data[Search][query]" title="Search Mi Base" value="" id="SearchQuery" type="text" />
			<input id="searchButton" value="Search" type="submit" />
		</div>
	</form>
	<h1><?php echo $html->link('Mi Base', '/'); ?></h1>
	<p class="tagline"><?php echo __('Every cake needs a strong base') ?></p>
	<div id='navcontainer'><?php
$menu->settings(__('main', true));
$menu->add(array(
	array('title' => __('Home', true), 'url' => '/'),
	array('title' => __('Contact Us', true), 'url' => array('controller' => 'contact', 'action' => 'us'))
));
if ($session->check('Auth.User') && empty($isEmail)) {
	$menu->add(array(
		array('title' => __('Your Profile', true), 'url' => array('controller' => 'users', 'action' => 'profile')),
		// A deliberate teaser. If the user isn't an admin they can't access it
		array('title' => __('Admin', true), 'url' => '/admin'),
		array('title' => __('Logout', true), 'url' => array('controller' => 'users', 'action' => 'logout')),
	));
} else {
	$menu->add(array(
		array('title' => __('Register', true), 'url' => array('controller' => 'users', 'action' => 'register')),
		array('title' => __('Login', true), 'url' => array('controller' => 'users', 'action' => 'login'), 'htmlAttributes' => array('class' => 'login')),
	));
}
echo $menu->display();
	?></div>
</div>