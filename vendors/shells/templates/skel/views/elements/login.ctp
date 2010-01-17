<?php
echo $form->create('User', array('action' => 'login', 'class' => 'compact clearfix'));
$after = '<p>' . $html->link(__('forgotten password', true), array('controller' => 'users', 'action' => 'forgotten_password')) .
	' ' . $html->link(__('sign up', true), array('controller' => 'users', 'action' => 'register')) .
	'</p>';
echo $form->inputs(array(
	'legend' => __('Login', true),
	'login_token' => array('type' => 'hidden', 'value' => $session->read('User.login_token')),
	'username',
	'password' => array('after' => $after, 'value' => ''),
));
echo $form->submit(__('Login', true));
echo $form->input('User.remember_me', array('label' => __('Remember me', true), 'type' => 'checkbox'));
echo $form->end();