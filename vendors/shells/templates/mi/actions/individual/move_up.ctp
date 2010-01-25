<?php
$Inst =& ClassRegistry::init($currentModelName);
if (!($Inst->Behaviors->attached('Tree') || $Inst->Behaviors->attached('List'))) {
	return;
}
$this->templateVars['postActions'][] = $admin . 'move_up';
?>
	public function <?php echo $admin ?>move_up($id = null, $steps = 1) {
		$this-><?php echo $currentModelName ?>->id = $id;
		if ($id && $this-><?php echo $currentModelName ?>->exists()) {
			$display = $this-><?php echo $currentModelName ?>->display($id);
			if (!$this-><?php echo $currentModelName ?>->moveUp($id, $steps)) {
				$this->Session->setFlash(sprintf(__('Problem moving <?php echo strtolower($singularHumanName) ?> %1$s "%2$s"', true), $id, $display));
			}
		} else {
			$this->Session->setFlash(sprintf(__('<?php echo ucfirst(strtolower($singularHumanName)) ?> with id %1$s doesn\'t exist', true), $id));
		}
		return $this->_back();
	}