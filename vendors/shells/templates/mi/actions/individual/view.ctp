	public function <?php echo $admin ?>view($id = null) {
		$this->data = $this-><?php echo $currentModelName ?>->read(null, $id);
<?php
$Inst =& ClassRegistry::init($currentModelName);
if ($Inst->hasField('foreign_id')) : ?>
		$Model = ClassRegistry::init($this->data['<?php echo $currentModelName ?>']['model']);
		$alias = Inflector::underscore(Inflector::pluralize($this->data['<?php echo $currentModelName ?>']['model']));
		$values[$this->data['<?php echo $currentModelName ?>']['foreign_id']] = $Model->display($this->data['<?php echo $currentModelName ?>']['foreign_id']);
		$this->set($alias, $values);
<?php endif; ?>

		$this->_setSelects();
		if(!$this->data) {
			$this->Session->setFlash(__('Invalid <?php echo low($singularHumanName) ?>', true));
			return $this->_back();
		}
	}