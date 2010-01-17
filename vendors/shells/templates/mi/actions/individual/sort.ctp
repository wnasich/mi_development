<?php
$Model =& ClassRegistry::init($currentModelName);
if (!$Model->Behaviors->attached('Tree')) {
	return;
}
$this->templateVars['postActions'][] = $admin . 'sort';
?>
	function <?php echo $admin ?>sort($verify = false) {
		set_time_limit(max(30, $this-><?php echo $currentModelName ?>->find('count')));
		$this-><?php echo $currentModelName ?>->reorder(array('verify' => $verify));
		$this->Session->setFlash('<?php echo $pluralHumanName ?> sorted alphabetically');
		return $this->_back();
	}