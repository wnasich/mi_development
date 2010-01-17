<div class="container form">
<?php echo "<?php\r\n"; ?>
if (empty($this->params['isAjax'])) {
	$legend = __('Login', true);
} else {
	$legend = false;
}
echo $form->create();
$after = '<p>' . $html->link(__('forgotten password', true), array('action' => 'forgotten_password')) .
	' ' . $html->link(__('sign up', true), array('action' => 'register')) .
	'</p>';
echo $form->inputs(array(
	'legend' => $legend,
	'username',
	'password' => array('value' => '', 'after' => $after),
	'remember_me' => array('label' => __('Remember me', true), 'type' => 'checkbox')
));
echo $form->end(__('Login', true));
<?php echo "?>"; ?>
</div>