<?php
$Inst =& ClassRegistry::init($currentModelName);
if (!$Inst->Behaviors->attached('Tree')) {
	return;
}
$this->templateVars['postActions'][] = $admin . 'promote';
?>
	function <?php echo $admin ?>promote($id) {
		$this-><?php echo $currentModelName ?>->id = $id;
		$node = $this-><?php echo $currentModelName ?>->read(null, $id);
		$parent = $this-><?php echo $currentModelName ?>->getparentnode();
		if ($parent) {
			$this-><?php echo $currentModelName ?>->saveField('parent_id', $parent['<?php echo $currentModelName ?>']['parent_id']);
		} else {
			$this->Session->setFlash($node['<?php echo $currentModelName ?>']['title'] . ' has no parent, cannot promote.');
		}
		return $this->_back();
	}