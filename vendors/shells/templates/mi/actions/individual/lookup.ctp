	public function <?php echo $admin ?>lookup($input = '') {
		$this->autoRender = false;
		if (!$input && !empty($this->params['url']['q'])) {
			$input = $this->params['url']['q'];
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

		if (!$this->data = $this-><?php echo $currentModelName?>->find('list', compact('conditions'))) {
			$this->output = '0';
			return;
		}
		return $this->render('/elements/lookup_results', 'json');
	}