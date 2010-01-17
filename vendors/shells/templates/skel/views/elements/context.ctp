<?php
$user = $session->read('Auth.User');
if (!$user || (isset($isEmail) && $isEmail === true)) {
	if (isset($this->params['controller']) && $this->params['controller'] != 'users') {
		echo '<div>' . $this->element('login') . '</div>';
	}
	if (isset($isEmail) && $isEmail === true) {
		echo $this->element('static/email_blurb');
	}
} else {
	echo '<div id="context"><h3>' . sprintf(__('Logged in as %s', true), $user['username']) . '</h3>';
	$thumb = '';
	if ($user['pic']) {
		$thumb = $this->element('thumb', array('data' => $user, 'size' => 'thumb', 'div' => false));
	}
	echo '<p>' . $thumb . __(' ... the legendary', true) . '</p>';
	echo '</div>';
}
if (empty($this->isAjax)) {
	if ($this->name == 'Users') {
	       if ($this->action == 'login') {
			echo $this->element('static/terms_and_conditions');
		} elseif ($this->action == 'register') {
			echo $this->element('static/terms_and_conditions');
		} elseif ($this->action == 'forgotten_password') {
			echo $this->element('static/forgotten_password_blurb');
		}
	} elseif ($this->name == 'Contact' && $this->action == 'us') {
		echo $this->element('static/contact_blurb');
	}
}