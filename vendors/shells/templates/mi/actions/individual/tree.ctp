<?php
$Inst =& ClassRegistry::init($currentModelName);
if (!$Inst->Behaviors->attached('Tree')) {
	return;
}
?>
	public function <?php echo $admin ?>tree($id = null, $showAll = true) {
		$this->helpers[] = 'Mi.Tree';
		$order = <?php echo $currentModelName ?> . '.lft';
		if ($this->showAll) {
			if (!$id) {
				$conditions = array();
			} else {
				$row = $this-><?php echo $currentModelName ?>->read(null, $id);
				extract($row[<?php echo $currentModelName ?>]);
				$conditions = array('OR' => array(
				array(
					'<?php echo $currentModelName ?>.lft <=' => $lft,
					'<?php echo $currentModelName ?>.rght >=' => $rght,
					),
				'OR' => array(
					'<?php echo $currentModelName ?>.parent_id' => array($id, $parent_id),
					'<?php echo $currentModelName ?>.parent_id IS NULL',
					)
				));
			}
		} else {
			$conditions['<?php echo $currentModelName ?>.parent_id'] = null;
		}
		$this->data = $this-><?php echo $currentModelName ?>->find('all', compact('conditions', 'order', 'fields', 'recursive'));
		$this->set('displayField', $this-><?php echo $currentModelName ?>->displayField);
	}