<?php
$Inst =& ClassRegistry::init($currentModelName);
if (!$Inst->Behaviors->attached('Tree') /* || $Inst->Behaviors->attached('List') */) {
	return;
}
$this->templateVars['postActions'][] = $admin . 'recover';
?>
	public function <?php echo $admin ?>recover($sort = null) {
		if (!$sort) {
			$sort = $this-><?php echo $currentModelName ?>->displayField;
		}
		$this-><?php echo $currentModelName ?>->recover(null, $sort);
		$this->Session->setFlash('<?php echo ucfirst(strtolower($pluralHumanName)) ?> reset based on ' . $sort . ' field.');
		return $this->_back();
	}