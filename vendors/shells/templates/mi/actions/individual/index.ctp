<?php
$Inst =& ClassRegistry::init($currentModelName);
?>
	public function <?php echo $admin ?>index() {
<?php if ($Inst->Behaviors->attached('Tree')): ?>
		$this->helpers[] = 'Mi.Tree';
<?php endif; ?>
		if (isset($this->SwissArmy)) {
			$conditions = $this->SwissArmy->parseSearchFilter();
		} else {
			$conditions = array();
		}
		if ($conditions) {
			$this->set('filters', $this-><?php echo $currentModelName ?>->searchFilterFields());
			$this->set('addFilter', true);
		}
		$this->data = $this->paginate($conditions);
		$this->_setSelects();
	}