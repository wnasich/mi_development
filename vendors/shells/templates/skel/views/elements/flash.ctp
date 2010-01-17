<?php /* SVN FILE: $Id: flash.ctp 1466 2009-08-18 22:21:38Z ad7six $ */
$messages = $session->read('Message');
$emailFlash = '';
if (isset($isEmail)) {
	if ($isEmail === true || !Configure::read()) {
		$emailFlash = $this->element('email/html/header');
		echo $emailFlash;
		return;
	}
} elseif (!$messages) {
	return;
}
foreach (array_keys($messages) as $key) {
	$session->flash($key);
}
echo $emailFlash;