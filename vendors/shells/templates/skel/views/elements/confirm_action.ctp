<?php
extract($data);
$action = __d('mi', Inflector::humanize(str_replace('admin_', '', $this->action)), true);
if (!empty($id)) {
	$class = __d('mi', Inflector::humanize(Inflector::underscore($modelClass)), true);
	echo '<p>' . sprintf(__d('mi', 'Are you sure you want to %1$s "<em>%2$s</em>" (%3$s id %4$s)?', true), $action, $display, $class, $id) . '</p>';
} else {
	$class = __d('mi', Inflector::humanize(Inflector::underscore($this->name)), true);
	echo '<p>' . sprintf(__d('mi', 'Are you sure you want to %1$s %2$s?', true), $action, $class) . '</p>';
}
uses('Sanitize');
echo $form->create(null, array('id' => Sanitize::paranoid($this->params['url']['url']) . 'Confirmation'));
echo $form->hidden('App.submit', array('value' => 'submit'));
echo $form->hidden('App.continue', array('value' => sprintf(__d('mi', 'continue (%1$s)', true), $action)));
echo $form->submit(sprintf(__d('mi', 'continue (%1$s)', true), $action), array('name' => 'data[App][submit]'));
if (empty($this->params['isAjax'])) {
	echo $form->submit(__d('mi', 'cancel (do nothing)', true), array('class' => 'cancel', 'name' => 'data[App][submit]'));
}
echo $form->end();