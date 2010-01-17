	function <?php echo $admin ?>search($term = null) {
		if ($this->data) {
			$term = trim($this->data['<?php echo $currentModelName ?>']['query']);
			$url = array(urlencode($term));
			if (!empty($this->data['<?php echo $currentModelName ?>']['extended'])) {
				$url['extended'] = true;
			}
			$this->redirect($url);
		}
		$request = $_SERVER['REQUEST_URI'];
		$term = trim(str_replace(Router::url(array()), '', $request), '/');
		if (!$term) {
			$this->redirect(array('action' => 'index'));
		}
		$conditions = $this-><?php echo $currentModelName ?>->searchConditions($term, isset($this->passedArgs['extended']));
		$this->Session->setFlash(sprintf(__('All <?php echo strtolower($pluralHumanName) ?> matching the term "%1$s"', true), htmlspecialchars($term)));
		$this->data = $this->paginate($conditions);
		$this->_setSelects();
		$this->render('<?php echo $admin ?>index');
	}