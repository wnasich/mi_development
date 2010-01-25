<?php
$Inst =& ClassRegistry::init($currentModelName);
$schema = $Inst->schema();
$alias = ucfirst(strtolower($pluralHumanName));
?>
	public function <?php echo $admin ?>multi_process($action = null) {
		if (!$this->data) {
			$this->_back();
		}
		$ids = array_filter($this->data['<?php echo $currentModelName ?>']);
		if (!$ids) {
			$this->Session->setFlash(__('Nothing selected, nothing to do', true));
			$this->_back();
		}
		if($action === null) {
			if (isset($_POST['deleteAll'])) {
				$action = 'delete';
				$message = __('<?php echo $alias ?> deleted.', true);
			} elseif (isset($_POST['editAll'])) {
				$ids = array_keys(array_filter($this->data['<?php echo $currentModelName ?>']));
				return $this->redirect(array(
					'action' => 'multi_edit',
					'id' => '(' . implode($ids, ',') . ')'
				));
<?php
foreach($schema as $field => $data) {
	if ($data['type'] === 'boolean') {
?>
			} elseif(isset($_POST['<?php echo $field ?>All'])) {
				$action = '<?php echo $field ?>';
				$message = __('<?php echo $alias . ' ' . $field ?>.', true);
			} elseif (isset($_POST['un<?php echo ucfirst($field) ?>All'])) {
				$action = 'un<?php echo ucfirst($field) ?>';
				$message = __('<?php echo $alias . ' un ' . $field ?>.', true);
<?php
	}
}
?>
			} else {
				$this->Session->setFlash(__('No action defined, don\'t know what to do', true));
				$this->_back();
			}
		}
		foreach($ids as $id => $do) {
			switch($action) {
				case 'delete':
					$this-><?php echo $currentModelName ?>->delete($id);
					break;
<?php
$alias = ucfirst(strtolower($pluralHumanName));
foreach($schema as $field => $data) {
	if ($data['type'] === 'boolean') {
?>
				case '<?php echo $field ?>':
					$this-><?php echo $currentModelName ?>->id = $id;
					$this-><?php echo $currentModelName ?>->saveField('<?php echo $field ?>', 1);
					break;
				case 'un<?php echo ucfirst($field) ?>':
					$this-><?php echo $currentModelName ?>->id = $id;
					$this-><?php echo $currentModelName ?>->saveField('<?php echo $field ?>', 0);
					break;
<?php
	}
}
?>
			}
		}
		$this->Session->setFlash($message);
		$this->_back();
	}