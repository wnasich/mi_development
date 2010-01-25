	public function <?php echo $admin ?>delete($id = null) {
		$this-><?php echo $currentModelName ?>->id = $id;
		if ($id && $this-><?php echo $currentModelName ?>->exists()) {
			$display = $this-><?php echo $currentModelName ?>->display($id);
			if ($this-><?php echo $currentModelName ?>->delete($id)) {
				$this->Session->setFlash(sprintf(__('<?php echo ucfirst(strtolower($singularHumanName)) ?> %1$s "%2$s" deleted', true), $id, $display));
			} else {
				$this->Session->setFlash(sprintf(__('Problem deleting <?php echo strtolower($singularHumanName) ?> %1$s "%2$s"', true), $id, $display));
			}
		} else {
			$this->Session->setFlash(sprintf(__('<?php echo ucfirst(strtolower($singularHumanName)) ?> with id %1$s doesn\'t exist', true), $id));
		}
		return $this->_back();
	}