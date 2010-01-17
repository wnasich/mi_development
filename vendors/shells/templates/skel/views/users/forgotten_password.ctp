<?php echo $this->set('title_for_layout', __('forgotten password', true)); ?>
<div class="container form">
	<p>
		If you've forgotten your password you can reset it by submitting the form below
	</p>
	<p>
		We'll send you an email that you must read to proceed, this helps to confirm that it's
		really you requesting to change your password. All you need to do is check the mail - click
		the link and enter a new password to regain access to your account.
	</p><?php
		echo $form->create();
		if ($authFields['username'] == 'email') {
			echo $form->input('email');
		} else {
			echo $form->input('email', array('label' => __('email or username', true)));
		}
		echo $form->submit(__('reset password', true))  ;
		echo $form->end();
		?>
</div>