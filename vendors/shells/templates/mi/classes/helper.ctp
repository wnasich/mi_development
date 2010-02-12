<?php
class MyNewHelper extends AppHelper {

	public $name = 'MyNew';

	public $settings = array();

	protected $_defaultSettings = array();

	public function __construct($options = array()) {
		$this->settings = array_merge($this->_defaultSettings, $options);
		parent::__construct($options);
	}
}
