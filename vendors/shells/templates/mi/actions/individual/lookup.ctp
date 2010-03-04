	public function <?php echo $admin ?>lookup($input = '', $format = 'ui') {
		Configure::write('debug', 0); //safeguard
		$this->autoRender = false;

		if (!$input) {
			if (!empty($this->params['url']['term'])) {
				$input = $this->params['url']['term'];
			} elseif (!empty($this->params['url']['q'])) {
				$input = $this->params['url']['q'];
			}
		}

		$conditions = $this->SwissArmy->parseSearchFilter();
		if ($input) {
			$conditions['OR'] = array(
				'<?php echo ClassRegistry::init($currentModelName)->primaryKey ?> LIKE' => $input . '%',
				'<?php echo ClassRegistry::init($currentModelName)->displayField ?> LIKE' => $input . '%'
			);
		}
		if (!$conditions) {
			$this->output = '[]';
			return;
		}

		$page = 1;
		if (!empty($this->params['named']['page'])) {
			$page = $this->params['named']['page'];
		}

		$limit = 20;

		$this->data = $this-><?php echo $currentModelName?>->find('list', compact('conditions', 'page', 'limit'));
		if (!$this->data && $input) {
			$conditions[$this-><?php echo $currentModelName?>->displayField .' LIKE ?'] = '%' . $input . '%';
			$this->data = $this-><?php echo $currentModelName?>->find('list', compact('conditions', 'page', 'limit'));
		}

		if ($format === 'ui') {
			$return = array();
			foreach($this->data as $id => &$row)  {
				$return[] = array('label' => $row, 'value' => $row, 'id' => $id);
			}
			$this->output = json_encode($return);
			return;
		}
		$this->output = json_encode($this->data);
	}