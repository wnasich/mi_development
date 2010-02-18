<?php
$Inst =& ClassRegistry::init($currentModelName);
if (empty($Inst->hasAndBelongsToMany)) {
	return;
}
$methodType = 'callbacks';
?>
	public function beforeFilter() {
		parent::beforeFilter();
<?php if ($admin): ?>
		if (!empty($this->params['admin'])) {
<?php endif; ?>
			$this-><?php echo $currentModelName ?>->recursive = 0;
<?php if ($admin): ?>
		}
<?php endif; ?>
	}