	public function <?php echo $admin ?>lookup($input = '') {
		$this->autoRender = false;
		if (!$input && !empty($this->params['url']['term'])) {
			$input = $this->params['url']['term'];
		}
		$conditions = $this->SwissArmy->parseSearchFilter();
		if ($input) {
			$conditions['OR'] = array(
				'<?php echo ClassRegistry::init($currentModelName)->primaryKey ?> LIKE' => $input . '%',
				'<?php echo ClassRegistry::init($currentModelName)->displayField ?> LIKE' => $input . '%'
			);
		}
		if (!$conditions) {
			$this->output = '{}';
			return;
		}

		$this->data = $this-><?php echo $currentModelName?>->find('list', compact('conditions'));

		$this->output = json_encode($this->data);
	}