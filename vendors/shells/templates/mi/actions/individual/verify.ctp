<?php
$Inst =& ClassRegistry::init($currentModelName);
if (!($Inst->Behaviors->attached('Tree') || $Inst->Behaviors->attached('List'))) {
	return;
}
$this->templateVars['postActions'][$admin . 'verify'] = $admin . 'verify';
?>
	function <?php echo $admin ?>verify() {
		$return = $this-><?php echo $currentModelName ?>->verify();
		if ($return === true) {
			$this->Session->setFlash('Valid!');
		} else {
			$message = 'Found a few problems:<br />';
			foreach ($return as $key => $data) {
				if (is_string($data)) {
					$message .= $data;
				} else {
					$message .= implode ($data, ' ');
				}
				$message .= '<br />';
			}
			$this->Session->setFlash($message);
		}
		return $this->_back();
	}