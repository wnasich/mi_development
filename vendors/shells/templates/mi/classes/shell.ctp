<?php
class MyNewShell extends Shell {

	public $name = 'MyNew';

	public $settings = array(
		'quiet' => false,
	);

	protected $version = '0.1';

	protected $_name = null;

	protected $_messages = array(
	);

	function help()  {
		$exclude = array('main');
		$shell = get_class_methods('Shell');
		$methods = get_class_methods($this);
		$methods = array_diff($methods, $shell);
		$methods = array_diff($methods, $exclude);
		$help = array();
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
		$this->_name = Inflector::underscore($this->name);
		if (file_exists('config' . DS . $this->_name . '.php')) {
			include('config' . DS . $this->_name . '.php');
			if (!empty($config)) {
				$this->settings = am($this->settings, $config);
			}
		} elseif (file_exists(APP . 'config' . DS . $this->_name . '.php')) {
			include(APP . 'config' . DS . $this->_name . '.php');
			if (!empty($config)) {
				$this->settings = am($this->settings, $config);
			}
		}
		if (!empty($this->params['q']) || !empty($this->params['quiet']) || !empty($this->params['-quiet'])) {
			$this->settings['quiet'] = true;
		}
		$this->_loadModels();
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