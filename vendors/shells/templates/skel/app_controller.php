<?php
class AppController extends Controller {

/**
 * components property
 *
 * @var array
 * @access public
 */
	var $components = array(
		'Mi.MiSession',
		'Mi.SwissArmy' => array('autoLayout' => true),
		'Mi.Seo',
		'MiUsers.RememberMe',
		'Auth',
		'RequestHandler',
	);

/**
 * helpers property
 *
 * @var array
 * @access public
 */
	var $helpers = array(
		'Mi.MiForm',
		'Mi.MiHtml',
		'MiAsset.Asset',
		'Mi.Menu',
		'Time',
		'Text',
	);

/**
 * view property
 *
 * @var string 'Mi'
 * @access public
 */
	var $view = 'Mi.Mi';

/**
 * namedParams property
 *
 * @var bool true
 * @access public
 */
	var $namedParams = true;

/**
 * postActions property
 *
 * @var array
 * @access public
 */
	var $postActions = array(
		'admin_delete'
	);

/**
 * construct method
 *
 * Prevent missing component error if the DebugKit toolbar isn't available.
 * Load the dev and tidy panels (tidy panel is linux only) if available
 *
 * @return void
 * @access private
 */
	 public function __construct() {
		if (Configure::read() && App::import('Component', 'DebugKit.Toolbar')) {
			$panels = array();
			if (App::import('Vendor', 'MiDevelopment.DevPanel')) {
				if (DS === '/' && exec('which tidy')) {
					$panels[] = 'MiDevelopment.Tidy';
				}
				$panels[] = 'MiDevelopment.Dev';
			}
			$this->components['DebugKit.Toolbar'] = array(
				'panels' => $panels,
				'forceEnable' => true,
			);
		}
		return parent::__construct();
	}

/**
 * mergeVars method
 *
 * Put the toolbar first so that initialization of other components is included in the
 * 'Component Initialization' timer
 *
 * @return void
 * @access private
 */
	function __mergeVars() {
		parent::__mergeVars();
		if (!Configure::read() || !isset($this->components['DebugKit.Toolbar'])) {
			return;
		}
		$this->components = array_merge(array('DebugKit.Toolbar' => $this->components['DebugKit.Toolbar']), $this->components);
	}

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
			$message = print_r($message, true); //@ignore
		}
		$message = RequestHandlerComponent::getClientIP() . "\t" . $message;
		parent::log($message, $type);
	}

/**
 * beforeFilter method
 *
 * Set a default page title from the po file
 * Set to ajax layout if it's a popup request
 * Also set the requirePost property of the security component to the controller's postActions property
 * And finally, if it's an admin request - use the MiPanel plugin/theme
 *
 * @return void
 * @access public
 */
	function beforeFilter() {
		if (isset($this->SwissArmy)) {
			$this->SwissArmy->setDefaultPageTitle();
			$this->SwissArmy->handlePostActions();
		}
		$this->Auth->authorize = 'controller';
		if ($this->name === 'Pages') {
			$this->Auth->allow('display');
		}
		$this->Auth->logoutRedirect = '/';
		if (!empty($this->params['admin'])) {
			$this->SwissArmy->loadComponent('MiPanel.MiPanel');
		}
	}

/**
 * redirect method
 *
 * If it's an ajax request, and the $force parameter is true - render a js redirect
 *
 * @param mixed $url
 * @param mixed $code null
 * @param bool $exit true
 * @param bool $force false
 * @return void
 * @access public
 */
	function redirect($url, $code = null, $exit = true, $force = false) {
		if (isset($this->SwissArmy)) {
			if ($this->SwissArmy->redirect($url, $code, $exit, $force) && $exit) {
				$this->_stop();
			}
		}
		return parent::redirect($url, $code, $exit);
	}

/**
 * isAuthorized method
 *
 * Simple example, if it's an admin request and the user isn't in the admin group deny. Otherwise - allow
 *
 * @return void
 * @access public
 */
	function isAuthorized() {
		if (isset($this->params['admin']) && $this->Auth->user('group') == 'Admin') {
			return true;
		}
		return false;
	}

/**
 * back method
 *
 * (hopefully) Intelligent referer logic
 * Convenience method to call the back method in the Swiss army component. Can be overriden if the true
 * referer is not actually useful.
 *
 * @param int $steps
 * @return void
 * @access protected
 */
	function _back($steps = 1, $force = false) {
		if (isset($this->SwissArmy)) {
			if (($force || in_array($this->action, $this->postActions)) && $this->RequestHandler->isAjax()) {
				$url = $this->SwissArmy->back($steps, null, false);
				return $this->redirect($url, null, true, true);
			}
			return $this->SwissArmy->back($steps);
		}
		return $this->redirect($this->referer('/', true));
	}

/**
 * black hole method
 *
 * If a GET request is made for a method that must be run via POST/DELETE
 * present a confirmation screen which submits by POST/DELETE
 *
 * @param mixed $reason
 * @return void
 * @access protected
 */
	function _blackHole($reason = null) {
		if (isset($this->SwissArmy)) {
			return $this->SwissArmy->blackHole($reason);
		}
		return false;
	}

/**
 * setSelects method
 *
 * Populate variables used for selects
 *
 * @return void
 * @access protected
 */
	function _setSelects($params = array()) {
		if (isset($this->SwissArmy)) {
			$this->SwissArmy->setSelects($params);
			return true;
		}
		return false;
	}
}