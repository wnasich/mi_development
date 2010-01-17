<?php /* SVN FILE: $Id: us.ctp 1508 2009-09-01 21:59:41Z ad7six $ */
echo $form->create();
$cats = array(
	':)' => ':) Compliment',
	':\'(' => ':\'( Complaint',
	'?' => '? Question',
	'$' => '$ Quote',
	'!' => '! Problem',

);
echo $form->inputs(array(
	'legend' => 'Mail me',
	'category' => array('options' => $cats),
	'subject' => array(),
	'body' => array('cols' => 60, 'type'=>'textarea'),
	'from' => array('title' => 'Please enter a contact email address', 'default' => $session->read('Auth.User.email')),
	'url'
));
echo $form->submit();
echo $form->end();