	function <?php echo $admin ?>edit($id = null) {
		if ($this->data) {
			if ($this-><?php echo $currentModelName ?>->saveAll($this->data)) {
				$display = $this-><?php echo $currentModelName ?>->display();
				$this->Session->setFlash(sprintf(__('<?php echo ucfirst(strtolower($singularHumanName)) ?> "%1$s" updated', true), $display));
				return $this->_back();
			} else {
				$this->data = $this-><?php echo $currentModelName ?>->data;
				if (Configure::read()) {
					foreach ($this-><?php echo $currentModelName ?>->validationErrors as $i => &$error) {
						if (is_array($error)) {
							$error = implode($error, '<br />');
						}
					}
					$this->Session->setFlash(implode($this-><?php echo $currentModelName ?>->validationErrors, '<br />'));
				} else {
					$this->Session->setFlash(__('errors in form', true));
				}
			}
		} elseif ($id) {
			$this->data = $this-><?php echo $currentModelName ?>->read(null, $id);
			if (!$this->data) {
				$this->Session->setFlash(sprintf(__('<?php echo ucfirst(strtolower($singularHumanName)) ?> with id %1$s doesn\'t exist', true), $id));
				$this->_back();
			}
		} else {
			return $this->_back();
		}
		$this->_setSelects();
	}