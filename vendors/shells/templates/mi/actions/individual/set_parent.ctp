<?php
$Inst =& ClassRegistry::init($currentModelName);
if (!$Inst->Behaviors->attached('Tree')) {
	return;
}
?>
	function <?php echo $admin ?>set_parent($id = null, $parentId = null) {
		if ($id == null && $parentId == null && $this->data) {
			$id = $this->data['<?php echo $currentModelName ?>']['node'];
			$this-><?php echo $currentModelName ?>->id = $id;
			$parentId = $this->data['<?php echo $currentModelName ?>']['parent'];
			$this->data['<?php echo $currentModelName ?>']['referer'] = $this->referer(array('action' => 'index'));
		}
		if (!$id) {
			return $this->_back();
		}
		$this-><?php echo $currentModelName ?>->save(array('parent_id' => $parentId));
		return $this->_back();
	}