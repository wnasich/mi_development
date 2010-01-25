	public function <?php echo $admin ?>multi_edit() {
		if ($this->data) {
			$data = array();
			foreach ($this->data as $key => $row) {
				if (!is_numeric($key)) {
					continue;
				}
				$data[$key] = $row;
			}
			if ($this-><?php echo $currentModelName ?>->saveAll($data, array('validate' => 'first'))) {
				$this->Session->setFlash(sprintf(__('<?php echo ucfirst(strtolower($pluralHumanName)) ?> updated', true)));
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
					$this->Session->setFlash(__('Some or all updates did not succeed', true));
				}
			}
			$this->params['paging'] = $this->Session->read('<?php echo $currentModelName ?>.paging');
			$this->helpers[] = 'Paginator';
		} else {
			$args = func_get_args();
			call_user_func_array(array($this, '<?php echo $admin ?>index'), $args);
			array_unshift($this->data, 'dummy');
			unset($this->data[0]);
			$this->Session->write('<?php echo $currentModelName ?>.paging', $this->params['paging']);
		}
		$this->_setSelects(false);
	}