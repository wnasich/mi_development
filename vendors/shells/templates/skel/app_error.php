<?php
/**
 * AppError class - handles any cakeError thrown by your app
 *
 * This is an example/tempalte app error class. Permission to enhance granted
 *
 * PHP version 4 and 5
 *
 * Copyright (c) 2009, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2009, Andy Dawson
 * @link          www.ad7six.com
 * @package       mi_development
 * @subpackage    mi_development.vendors.shells.templates.skel
 * @since         v 1.0 (25-Sep-2009)
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * AppError class
 *
 * @uses          ErrorHandler
 * @package       mi_development
 * @subpackage    mi_development.vendors.shells.templates.skel
 */
class AppError extends ErrorHandler {

/**
 * construct method
 *
 * If it's not just a bad url - throw a 500
 *
 * @param mixed $method
 * @param mixed $messages
 * @return void
 * @access private
 */
	function __construct($error, $messages) {
		if (Configure::read() || in_array($error, array('missingAction', 'missingController'))) {
			return parent::__construct($error, $messages);
		}
		if (method_exists($this->controller, 'apperror')) {
			return $this->controller->appError($method, $messages);
		}
		$this->controller = new Controller();
		$this->controller->viewPath = 'errors';
		$this->error500($messages);
		return $this->_stop();
	}

/**
 * Huston, it's production mode and something awful just happened
 * This method processes something that isn't a missing controller|action i.e.
 * a missing file or resource.
 *
 * The details of the error are saved to the server_error log file and given a uuid
 * so that it's possible for the error message to be traced to the cause (if you're lucky and the
 * reporter makes a note of the id that is)
 *
 * @param mixed $params
 * @return void
 * @access public
 */
	function error500($params) {
		extract($params, EXTR_OVERWRITE);

		if (!isset($url)) {
			$url = $this->controller->here;
		}
		$url = Router::normalize($url);
		App::import('Core', 'String');
		header("HTTP/1.0 500 Internal Server Error");
		$params['uuid'] = String::uuid();
		$this->log($params, 'server_error');
		$this->controller->set(am(array(
			'code' => '500',
			'name' => __('Server Error', true),
			'base' => $this->controller->base,
		), $params));
		$this->_outputMessage('error500');
	}

/**
 * error404 method
 *
 * @param mixed $params
 * @return void
 * @access public
 */
	function error404($params) {
		$this->_checkPages();
		$this->_checkRedirects($params['url']);
		return parent::error404($params);
	}

/**
 * missingController method
 *
 * @param mixed $params
 * @return void
 * @access public
 */
	function missingController($params) {
		$this->_checkPages();
		$this->_checkRedirects($params['url']);
		return parent::missingController($params);
	}

/**
 * missingAction method
 *
 * @param mixed $params
 * @return void
 * @access public
 */
	function missingAction($params = array()) {
		$this->_checkPages();
		$this->_checkRedirects($params['url']);
		return parent::missingAction($params);
	}

/**
 * checkPages
 *
 * Get the current request url and use it to look for a page. I.e. allow the url:
 * /this/is/a/page to be treated as if it were /pages/this/is/a/page without any special
 * routes being defined
 *
 * Detect the 'extension' ajax (which by mi-convention is always appended to ajax requests)
 * and set the isAjax parameter.
 *
 * Set a default layout, default|ajax as apropriate. this can be overriden by calling:
 * $this->layout = 'not-default'; in the view itself
 *
 * Setup components as if the error had not been triggered, set a default page title based on the
 * filename, enable full page view caching - so that the next request doesn't fall into the error
 * processing and is served earlier, render the page and die.
 *
 * @return void
 * @access protected
 */
	function _checkPages($params = array()) {
		$url = Inflector::underscore($this->controller->params['url']['url']);
		$requestedPath = ltrim($url, '/');
		if (!$requestedPath) {
			$requestedPath = 'home';
		} else {
			$_requestedPath = preg_replace('@.ajax$@', '', $requestedPath);
			if ($requestedPath !== $_requestedPath) {
				$this->controller->params['isAjax'] = true;
			};
			$requestedPath = $_requestedPath;
		}
		if (!class_exists('Mi')) {
			APP::import('Vendor', 'Mi.Mi');
		}
		$views = array_values(Mi::views('pages', null, null, false));
		if (DS === '\\') {
			$requestedPath = str_replace('/', DS, $requestedPath);
		}
		if(!in_array($requestedPath, $views)) {
			return;
		}
		extract(Router::getPaths());
		extract($params, EXTR_OVERWRITE);

		$this->controller->base = $base;
		$this->controller->webroot = $webroot;
		$this->controller->here = rtrim($webroot, '/') . $this->controller->params['url']['url'];
		$this->controller->constructClasses();
		$this->controller->Component->initialize($this->controller);
		$this->controller->beforeFilter();
		$this->controller->Component->startup($this->controller);
		$this->controller->name = 'Pages';
		$this->controller->viewPath = 'pages';

		if (empty($this->controller->params['isAjax'])) {
			$this->controller->layout = 'default';
		} else {
			$this->controller->layout = 'ajax';
		}

		$this->controller->set('title_for_layout', Inflector::humanize(str_replace('/', ' ', $requestedPath)));
		$this->controller->beforeRender();
		if (!isDevelopment()) {
			$this->controller->cacheAction = '+1 day';
			$this->controller->helpers[] = 'Cache';
			if (!$this->Session->read('Message')
				&& !$this->Auth->user()
				&& App::import('Helper', 'HtmlCache.HtmlCache')) {
				$this->helpers[] = 'HtmlCache.HtmlCache';
			}
		}
		$out = $this->controller->render($requestedPath);
		$this->controller->Component->shutdown($this->controller);
		$this->controller->afterFilter();
		echo $out;
		$this->_stop();
	}

/**
 * checkRedirects method
 *
 * Check for the existance of a model which encapsulates any automatic redirect logic that might be
 * required. The Redirect model must implement the method "to".
 * This logic is only appropriate/triggered if routes are changed such that the OLD url doesn't resolve
 * to the same controller/action.
 * Below is an example route and Redirect model, which converts the blog tutorial urls from
 * /posts/view/1 to posts/1-I-wrote-this - and resends any requests for /posts/view/1 to the right url
 * As a fallback, it send the user to the search page. Note this example only works if /posts/view/1
 * doesn't match a route, or matches a route that triggers an error.
 *
 * // routes.php
 * Router::connect(
 *		'/post/:id-:filename',
 *		array('controller' => 'posts', 'action' => 'view'),
 *		array('id' => '\d+')
 *	);
 *
 * // config or bootstrap.php
 * Configure::write('Redirect.model', 'Redirect');
 *
 * // models/redirect.php
 * class Redirect extends AppModel {
 * 	function to($from = '') {
 * 		preg_match('@\/?posts/view/(\d+)@', $from, $matches);
 * 		if ($matches) {
 * 			$row = ClassRegistry::init('Post')->read(array('slug'), $matches[1]);
 * 			if (!$row) {
 * 				return '/';
 * 			}
 * 			return array(
 * 				'controller' => 'posts',
 * 				'action' => 'view',
 * 				'id' => $matches[1],
 * 				'slug' => $row['Post']['slug']
 * 			);
 * 		}
 * 		return '/search/' . str_replace('-', ' ', Inflector::slug($from));
 * 	}
 * }
 *
 * @param string $url ''
 * @return void
 * @access protected
 */
	function _checkRedirects($url = '') {
		$redirectModel = Configure::read('Redirect.model');
		if (!$redirectModel) {
			return;
		}
		$RedirectModel = ClassRegistry::init(array('class' => $redirectModel, 'table' => false));
		if (!$RedirectModel || !method_exists($RedirectModel, 'to')) {
			return;
		}
		$redirectTo = $RedirectModel->to($url);
		if ($redirectTo) {
			$this->_header('HTTP/1.1 301 Moved Permanently');
			$this->_header('Location: ' . Router::url($redirectTo, true));
			$this->_header('HTTP/1.1 301 Moved Permanently');
			$this->_stop();
		}
	}

/**
 * Convenience method for header()
 *
 * Allows the possibility to test/disable/manipulate what headers are sent
 *
 * @param string $status
 * @return void
 * @access public
 */
	function _header($status) {
		header($status);
	}
}