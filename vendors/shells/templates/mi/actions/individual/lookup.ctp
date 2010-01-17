	function <?php echo $admin ?>lookup($input = '') {
		$this->autoRender = false;
		if (!$input) {
			$input = $this->params['url']['q'];
		}
		if (!$input) {
			$this->output = '0';
			return;
		}
		$conditions = array(
			'<?php echo ClassRegistry::init($currentModelName)->primaryKey ?> LIKE' => $input . '%',
			'<?php echo ClassRegistry::init($currentModelName)->displayField ?> LIKE' => $input . '%'
		);
		if (!$this->data = $this-><?php echo $currentModelName?>->find('list', compact('conditions'))) {
			$this->output = '0';
			return;
		}
		return $this->render('/elements/lookup_results');
	}