<div id='header' class="clearfix">
	<div id='navcontainer'>	<?php
		$menu->settings(__('main', true), array('activeMode' => 'controller'));
		if ($this->params['url']['url'] == 'admin') {
			$menu->add(array('title' => __('Public', true), 'url' => '/'));
		} else {
			$menu->add(array('title' => __('Admin', true), 'url' => '/admin'));
		}
		$this->element('admin/menu/auto', array('ignore' => array()));
		$menu->add(array('title' => __('Logout', true),
			'url' => array('admin' => false, 'prefix' => null, 'plugin' => null, 'controller' => 'users', 'action' => 'logout')
		));
		echo $menu->display();
	?></div>
</div>