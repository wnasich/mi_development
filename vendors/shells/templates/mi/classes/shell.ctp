<?php
class MyNewShell extends Shell {
	protected $name = 'MyNew';
	protected $version = '0.1';
	public $settings = array(
		'quiet' => false,
	);
	function help()  {
		$exclude = array('main');
		$shell = get_class_methods('Shell');
		$methods = get_class_methods($this);
		$methods = array_diff($methods, $shell);
		$methods = array_diff($methods, $exclude);
		foreach ($methods as $method) {
			if (!isset($help[$method]) && $method[0] !== '_') {
				$help[$method] = $method;
			}
		}
		$this->out($this->name . '. Version ' . $this->version);
		$this->out('Usage: cake ' . $this->name . ' command');
		$this->out('');
		$this->out($this->name . ' is a shell for <>');
		$this->out('');
		$this->out('Commands:');
		foreach($help as $message) {
			$this->out("\t" . $message);
		}
		$this->hr();

	}
	function startup() {
		$this->_welcome();
	}

	function initialize() {
		if (!empty($this->params['q']) || !empty($this->params['quiet']) || !empty($this->params['-quiet'])) {
			$this->settings['quiet'] = true;
		}
	}
	function main() {
		return $this->help();
	}

	function _welcome() {
		if ($this->settings['quiet']) {
			return;
		}
		parent::_welcome();
	}
}