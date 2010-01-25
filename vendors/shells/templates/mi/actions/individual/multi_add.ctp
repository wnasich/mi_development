	public function <?php echo $admin ?>multi_add() {
		if ($this->data) {
			$data = array();
			foreach ($this->data as $key => $row) {
				if (!is_numeric($key) || !array_filter(current($row))) {
					continue;
				}
				$data[$key] = $row;
			}
			if ($this-><?php echo $currentModelName ?>->saveAll($data, array('validate' => 'first', 'atomic' => false))) {
				$this->Session->setFlash(sprintf(__('<?php echo ucfirst(strtolower($pluralHumanName)) ?> added', true)));
				$this->_back();
			} else {
				if (Configure::read()) {
					foreach ($this-><?php echo $currentModelName ?>->validationErrors as $i => &$error) {
						if (is_array($error)) {
							$error = implode($error, '<br />');
						}
					}
					if($this-><?php echo $currentModelName ?>->validationErrors) {
						$this->Session->setFlash(implode($this-><?php echo $currentModelName ?>->validationErrors, '<br />'));
					} else {
						$this->Session->setFlash(__('Save did not succeed with no validation errors', true));
					}
				} else {
					$this->Session->setFlash(__('Some or all additions did not succeed', true));
				}
			}
		} else {
			$this->data = array('1' => array('<?php echo $currentModelName ?>' => $this-><?php echo $currentModelName ?>->create()));
			$this->data[1]['<?php echo $currentModelName ?>']['<?php echo ClassRegistry::init($currentModelName)->primaryKey ?>'] = null;
		}
		$this->_setSelects(false);
		$this->render('<?php echo $admin ?>multi_edit');
	}