<?php
App::import('Helper', 'Helper', false);

class AppHelper extends Helper {

/**
 * log method
 *
 * Always log the ip
 *
 * @param mixed $message
 * @param mixed $type
 * @return void
 * @access public
 */
	function log($message, $type = null) {
		if (!class_exists('RequestHandlerComponent')) {
			App::import('Component', 'RequestHandler');
		}
		if (!is_string($message)) {
			$message = print_r($message, true);
		}
		parent::log(RequestHandlerComponent::getClientIP() . "\t" . $message, $type);
	}

/**
 * tagIsInvalid method
 *
 * Temporary override for further investigation.
 * Allows validation errors from saveAll with multiple rows of the same class to be displayed
 *
 * @param mixed $model
 * @param mixed $field
 * @param mixed $modelID
 * @return void
 * @access public
 */
	function tagIsInvalid($model = null, $field = null, $modelID = null) {
		foreach (array('model', 'field', 'modelID') as $key) {
			if (empty(${$key})) {
				${$key} = $this->{$key}();
			}
		}
		if (is_numeric($model)) {
			$_id = $model;
			$model = $modelID;
			$modelID = $_id;
		}
		return parent::tagIsInvalid($model, $field, $modelID);
	}

/**
 * url method
 *
 * Use the UrlCache class
 *
 * @param mixed $url
 * @param bool $full
 * @return void
 * @access public
 */
	function url($url = null, $full = false) {
		if (empty($this->_urlCache)) {
			if (!App::import('Vendor', 'Mi.UrlCache')) {
				return Router::url($url);
			}
			$this->_urlCache =& UrlCache::getInstance();
		}
		return $this->_urlCache->url($url, $full);
	}

/**
 * Serve assets from the static subdomain
 *
 * @see config/bootstrap.php
 * @param mixed $file
 * @return void
 * @access public
 */
	function webroot($file) {
		static $hosts;
		if ($hosts === null) {
			$hosts = Configure::read('Asset.hosts');
		}
		$host = null;
		if ($hosts) {
			foreach($hosts as $function => $patterns) {
				if ($function) {
					$test = $function($file);
				} else {
					$test = $file;
				}
				foreach($patterns as $pattern => $h) {
					if (preg_match($pattern, $test, $matches)) {
						$host = $h;
						if (isset($matches[1])) {
							unset($matches[0]);
							foreach($matches as $i => $match) {
								$host = str_replace('{' . $i . '}', $match, $host);
							}
						}
						break 2;
					}
				}
			}
		}
		if (strpos($file, '?')) {
			$path = array_shift(explode('?', $file));
		} else {
			$path = $file;
		}
		if (!file_exists(WWW_ROOT . ltrim($path, '/'))) {
			$file = $path . '?' . Security::hash($path, null, true);
		}

		return $host . $this->webroot . ltrim($file, '/');
	}
}