<div id='search'>
<h2><span><?php __('Search All ' . Inflector::humanize(Inflector::humanize($this->name))); ?></span></h2>
<?php
echo $form->create(null, array('action' => 'search', 'class' => 'compact clearfix'));
echo $form->input('query', array('label' => false));
echo $form->input('extended', array('label' => __('extended search', true), 'type' => 'hidden'));
echo $form->submit(__('Search', true));
echo $form->end();
echo $html->link(__('Advanced search', true), array('action' => 'advanced_search'));
?></div>