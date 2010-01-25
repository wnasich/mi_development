	public function <?php echo $admin ?>multi_delete() {
		if (!$this->data) {
			$this->_back();
		}
		foreach($this->data['<?php echo $currentModelName ?>'] as $id => $row) {
			if (!empty($row['delete'])) {
				$this-><?php echo $currentModelName ?>->delete($id);
			}
		}
		$this->_back();
	}