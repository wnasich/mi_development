<?php
/* SVN FILE: $Id$ */

/**
 * Short description for file
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       base
 * @subpackage    base.vendors.shells.tasks
 * @since         v 0.1
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
require_once (CAKE_CORE_INCLUDE_PATH . '/cake/console/libs/tasks/extract.php');
App::import('Vendor', 'Mi.MiCache');

/**
 * ExtractDynamicTask class
 *
 * @uses          ExtractTask
 * @package       base
 * @subpackage    base.vendors.shells.tasks
 */
class MiExtractTask extends ExtractTask {

/**
 * settings property
 * autoPlugin - exlude plugins when running on the app, and run on each plugin under the app individually
 * autoUpdatePo - auto update/create po files
 * excludeAdmin - don't include admin view files
 * cmd
 * 	preMsgmerge - run on an individual po file before running message merge
 * 	msgmerge - run for each updated/generated pot file
 * miMethods - additional methods to run, in addition to the core extractTokens logic
 *
 * @var array
 * @access public
 */
	var $settings = array(
		'autoPlugin' => true,
		'autoUpdatePo' => true,
		'excludeAdmin' => true,
		'cmd' => array(
			'preMsgmerge' => "sed -i -e  '/^# File: /d' '/^#: /d' :po",
			'msgmerge' => "cd :localeFolder && find */*/:name.po -exec msgmerge -U --backup=numbered --no-fuzzy-matching --no-wrap --force-po -s '{}' :name.pot \;",
		),
		'miMethods' => array(
			'emailSubjects',
			'fields',
			'javascript',
			'pageTitles',
			'validationMessages',
		)
	);

/**
 * execute method
 *
 * Find all files taking account of inheritance paths - excluding vendors since vendors shouldn't
 * have any strings within them that cake needs to be aware of to translate
 *
 * @return void
 * @access public
 */
	function execute() {
		$this->_defineAppVersion();
		$bases = array($this->params['working']);
		if ($this->settings['autoPlugin']) {
			if (is_dir('plugins')) {
				$this->params['plugin'] = false;
				$Folder = new Folder('plugins');
				list($plugins) = $Folder->read();
				foreach($plugins as $plugin) {
					$bases[] = APP . 'plugins' . DS . $plugin . DS;
				}
			}
			if (basename(dirname(APP)) === 'plugins' && !file_exists(CONFIGS . 'database.php')) {
				$this->__includeForPlugin();
			}
		}
		App::import('Core', 'Controller');
		foreach($bases as $base) {
			$this->out(sprintf(__d('mi_development', 'Checking %s ...', true), $base));
			$this->hr();
			$this->_reset();
			if ($this->settings['autoPlugin'] && ($base !== APP || basename(dirname(APP)) === 'plugins')) {
				$this->params['plugin'] = basename($base);
			}
			$this->params['paths'] = $base;
			$this->params['output'] = $base . 'locale' . DS;

			foreach($this->settings['miMethods'] as $method => $config) {
				if (is_numeric($method)) {
					$method = $config;
					$config = array();
				}
				$this->out(sprintf(__d('mi_development', 'Running %s ...', true), $method));
				$this->$method($config);
			}
			parent::execute();
		}
	}

/**
 * emailSubjects method
 *
 * @return void
 * @access public
 */
	function emailSubjects() {
		$pluginDomain = $this->params['plugin'];
		if ($pluginDomain) {
			$pluginDomain .= '_';
		}

		$templates = MiCache::mi('views', 'elements/email/html', $this->params['plugin'], array(), false);
		$templates = array_merge($templates, MiCache::mi('views', 'elements/email/text',  $this->params['plugin'], array(), false));
		foreach ($templates as $path => $name) {
			$name = str_replace(array('elements/email/html/', 'elements/email/text/', '/'),
				array('', '', '_'), $name);
			if (in_array($name, array('header', 'footer'))) {
				continue;
			}
			$lookup = Inflector::humanize($name);
			$this->out(sprintf(__d('mi_development', '	for %1$s...', true), $lookup));
			$this->__strings[$pluginDomain . 'email_subjects'][$lookup][$path][0] = 0;
		}
		if (!empty($this->__strings[$pluginDomain . 'email_subjects'])) {
			ksort($this->__strings[$pluginDomain . 'email_subjects']);
		}
	}

/**
 * fields method
 *
 * @access public
 * @return void
 */
	function fields() {
		$plugin = $pluginDomain = $this->params['plugin'];
		if ($plugin) {
			$plugin .= '.';
			$pluginDomain .= '_';
		}
		$models = MiCache::mi('models', $this->params['plugin']);
		foreach ($models as $path => $model) {
			$this->out(sprintf(__d('mi_development', '	for %1$s...', true), $model));
			$inst = ClassRegistry::init($plugin . $model);
			foreach ($inst->schema() as $field => $_) {
				$text = $field;
				 if (substr($text, -3) == '_id') {
					 $text = substr($text, 0, strlen($text) - 3);
				}
				$text = Inflector::humanize(Inflector::underscore($model . ' ' . $text));
				$this->__strings[$pluginDomain . 'field_names'][$text][$path][0] = 40;
			}
		}
		if (!empty($this->__strings[$pluginDomain . 'field_names'])) {
			ksort($this->__strings[$pluginDomain . 'field_names']);
		}
	}

/**
 * javascript method
 *
 * Find vendor js files (not run on any files in the webroot) that contain __() usage
 *
 * @return void
 * @access public
 */
	function javascript() {
		$files = MiCache::mi('vendors',
			$this->params['plugin'],
			array('shells', 'css'),
			array('extension' => 'js')
		);
		foreach ($files as $file => $path) {
			$string = file($path);
			if (!$string || strpos(implode($string), '__(') === false) {
				continue;
			}
			$match = false;
			foreach ($string as $i => $line) {
				preg_match_all('@__\(([\'"])(.*)\1\)@U', $line, $matches);
				if ($matches[0]) {
					$match = true;
					foreach ($matches[2] as $text) {
						$this->__strings['javascript'][$text]['vendors/' . $file][] = $i + 1;

					}
				}
			}
		}
		if (!empty($this->__strings['javascript'])) {
			ksort($this->__strings['javascript']);
		}
	}

/**
 * pageTitles method
 *
 * @return void
 * @access public
 */
	function pageTitles() {
		$plugin = $pluginDomain = $this->params['plugin'];
		if ($plugin) {
			$plugin .= '.';
			$pluginDomain .= '_';
		}
		$controllers = MiCache::mi('controllers', $this->params['plugin']);

		foreach ($controllers as $path => $name) {
			$this->out(sprintf(__d('mi_development', '	for %1$s...', true), $name));
			App::import('Controller', $plugin . $name);
			$name .= 'Controller';
			$controller = new $name();
			$name = Inflector::humanize(Inflector::underscore($controller->name));
			$actions = MiCache::mi('actions', $controller->name, 'public', $this->params['plugin']);
			$adminActions = MiCache::mi('actions', $controller->name, 'admin', $this->params['plugin']);
			$views = MiCache::mi('views', $name, $this->params['plugin']);
			foreach ($views as $view) {
				if (strpos($view, 'admin_') === 0) {
					if (!in_array($view, $adminActions)) {
						$adminActions[] =  $view;
					}
				} else {
					if (!in_array($view, $actions)) {
						$actions[] =  $view;
					}
				}
			}
			$source = file($path);
			$classDec = 0;
			foreach ($source as $i => $line) {
				if (strpos($line, 'class ' . $name . 'Controller') !== false) {
					$classDec = $i;
					break;
				}
			}

			if (empty($this->settings['excludeAdmin'])) {
				foreach ($adminActions as $action) {
					$lineNo = $classDec;
					foreach ($source as $i => $line) {
						if (preg_match("/^\s*(public |static |abstract |protected |private )*function &?$action/", $line)) {
							$lineNo = $i;
							break;
						}
					}
					$action = str_replace('admin_', '', $action);
					$lookup = 'Admin :: ';
					$lookup .= $name . ' :: ' . Inflector::humanize(Inflector::underscore($action));
					$this->__strings['page_titles'][$lookup][$path][0] = $lineNo;
				}
			}
			foreach ($actions as $action) {
				$lineNo = $classDec;
				foreach ($source as $i => $line) {
					if (preg_match("/^\s*(public |static |abstract |protected |private )*function &?$action/", $line)) {
						$lineNo = $i;
						break;
					}
				}
				$lookup = $name . ' :: ' . Inflector::humanize(Inflector::underscore($action));
				$this->__strings[$pluginDomain. 'page_titles'][$lookup][$path][0] = $lineNo;
			}
		}
		if (!empty($this->__strings[$pluginDomain . 'page_titles'])) {
			ksort($this->__strings[$pluginDomain . 'page_titles']);
		}
	}

/**
 * Extract validation messages out of models
 *
 * @return void
 * @access public
 */
	function validationMessages() {
		$plugin = $pluginDomain = $this->params['plugin'];
		if ($plugin) {
			$plugin .= '.';
			$pluginDomain .= '_';
		}
		$models = MiCache::mi('models', $this->params['plugin']);

		foreach ($models as $path => $model) {
			$this->__file = Inflector::underscore($model) . '.php';
			$this->out(sprintf(__d('mi_development', '	for %1$s...', true), $model));
			$inst = ClassRegistry::init(array('class' => $plugin . $model));
			$inst->create();
			$inst->validates();
			foreach ($inst->validate as $field => $ruleSet) {
				if (!is_array($ruleSet) || (is_array($ruleSet) && isset($ruleSet['rule']))) {
					$ruleSet = array($ruleSet);
				}
				if (substr($field, -3) == '_id') {
					 $field = substr($field, 0, strlen($field) - 3);
				}
				foreach ($ruleSet as $index => $validator) {
					if (!is_array($validator)) {
						$validator = array('rule' => $validator);
					}
					if (is_string($index)) {
						$rule = $index;
					} elseif (is_array($validator['rule'])) {
						$rule = $validator['rule'][0];
					} else {
						$rule = $validator['rule'];
					}
					$rule = Inflector::humanize(Inflector::underscore($model . '_' . $field . '_' . $rule));
					$this->__strings[$pluginDomain . 'error_messages'][$rule][$path][0] = 40;
				}
			}
		}
		if (!empty($this->__strings[$pluginDomain . 'error_messages'])) {
			ksort($this->__strings[$pluginDomain . 'error_messages']);
		}
	}

/**
 * updatePoFiles method create or update existing po files
 *
 * @param mixed $localeFolders
 * @param mixed $potFiles null
 * @return void
 * @access protected
 */
	function _updatePoFiles($localeFolders, $potFiles = null) {
		foreach($localeFolders as $localeFolder) {
			$folder = new Folder ($localeFolder);
			list($locales) = $folder->read();
			if (!$potFiles) {
				$potFiles = $folder->find('.*\.pot');
			}
			foreach ((array)$potFiles as $pot) {
				foreach ($locales as $locale) {
					$po = $localeFolder . $locale . DS . 'LC_MESSAGES' . DS . str_replace('.pot', '.po', $pot);
					$this->_preparePo($po, $pot, $locale);
					if (DS === '/') {
						list($name) = explode('.', $pot);
						$this->out(sprintf(__d('mi_development', 'Auto Updating %s po files', true), $name));
						$command = String::insert($this->settings['cmd']['msgmerge'], compact('localeFolder', 'name'));
						$this->out($command);
						exec($command);
					}
				}
			}
		}
	}

/**
 * preparePo method - remove existing file references, so that there's no possibility of duplicates
 *
 * @param mixed $po
 * @param mixed $pot
 * @param mixed $locale
 * @return void
 * @access protected
 */
	function _preparePo($po, $pot, $locale) {
		if (file_exists($po)) {
			$command = $this->settings['cmd']['preMsgmerge'];
			if (!$command || DS !== '/') {
				return;
			}
			$command = String::insert($command, compact('po'));
			$this->out($command);
			exec($command);
			return;
		}
		$this->out(sprintf('Copying %s file to %s locale', $pot, $locale));
		$poFolder = dirname($po);
		if (!is_dir($poFolder)) {
			new Folder($poFolder, true);
		}
		copy($this->__output . $pot, $po);
	}

/**
 * defineAppVersion method
 *
 * @access public
 * @return void
 */
	function _defineAppVersion() {
		if (defined('SITE_VERSION')) {
			return;
		}
		if (file_exists(APP . '.git/refs/heads/master')) {
			define('SITE_VERSION', trim(file_get_contents(APP . '.git/refs/heads/master')));
		} elseif (file_exists(APP . '.svn' . DS . 'entries')) {
			$svn = file(APP . '.svn' . DS . 'entries');
			if (is_numeric(trim($svn[3]))) {
				$version = $svn[3];
			} else { // pre 1.4 svn used xml for this file
				$version = explode('"', $svn[4]);
				$version = $version[1];
			}
			define ('SITE_VERSION', trim($version));
		} else {
			define ('SITE_VERSION', 'unknown');
		}
	}

/**
 * reset method
 *
 * @return void
 * @access protected
 */
	function _reset() {
		$this->__paths = $this->__files = $this->__storage = $this->__tokens = $this->__strings = array();
		$this->__file = $this->__output = '';
	}

/**
 * Build the translate template file contents out of obtained strings
 *
 * @return void
 * @access private
 */
	function __buildFiles() {
		foreach ($this->__strings as $domain => $strings) {
			foreach ($strings as $string => $files) {
				$occurances = array();
				foreach ($files as $file => $lines) {
					$occurances[] = $file . ':' . implode(';', $lines);
				}
				$occurances = implode("\n#: ", $occurances);
				$header = '#: ' . str_replace($this->__paths, '', $occurances) . "\n";

				if (strpos($string, "\0") === false) {
					$sentence = "msgid \"{$string}\"\n";
					$sentence .= "msgstr \"\"\n\n";
				} else {
					list($singular, $plural) = explode("\0", $string);
					$sentence = "msgid \"{$singular}\"\n";
					$sentence .= "msgid_plural \"{$plural}\"\n";
					$sentence .= "msgstr[0] \"\"\n";
					$sentence .= "msgstr[1] \"\"\n\n";
				}

				$this->__store($domain, $header, $sentence);
				/* AD7six START
				   if ($domain != 'default') {
				   $this->__store('default', $header, $sentence);
				   }
			   AD7six END */
			}
		}
	}

/**
 * Build the translation template header
 *
 * Include a defualt European Plural-Forms entry so that untouched po files can still be edited
 * See @link for a list of plural form rules
 *
 * @link http://translate.sourceforge.net/wiki/l10n/pluralforms
 * @return string Translation template header
 * @access private
 */
	function __writeHeader() {
		if (APP_DIR != 'app') {
			$app = APP_DIR;
		} else {
			$app = basename(dirname(APP));
		}
		$year = date('Y');
		$output  = "# LANGUAGE translation of " . Inflector::humanize(Inflector::underscore($app)) . " Application\n";
		$output .= "# Copyright $year Your Name <you@example.com>\n";
		$output .= "# --VERSIONS--\n";
		$output .= "#\n";
		$output .= "#, fuzzy\n";
		$output .= "msgid \"\"\n";
		$output .= "msgstr \"\"\n";
		$output .= "\"Project-Id-Version: " . $app . '-' . SITE_VERSION . "\\n\"\n";
		$output .= "\"POT-Creation-Date: " . date("Y-m-d H:iO") . "\\n\"\n";
		$output .= "\"PO-Revision-Date: YYYY-mm-DD HH:MM+ZZZZ\\n\"\n";
		$output .= "\"Last-Translator: Your Name <you@example.com>\\n\"\n";
		$output .= "\"Language-Team:\\n\"\n";
		$output .= "\"MIME-Version: 1.0\\n\"\n";
		$output .= "\"Content-Type: text/plain; charset=utf-8\\n\"\n";
		$output .= "\"Content-Transfer-Encoding: 8bit\\n\"\n";
		$output .= "\"Plural-Forms: nplurals=2;plural=n!=1;\\n\"\n";
		$output .= "\"X-Poedit-Basepath: ../../../\\n\"\n";
		$output .= "\n";
		return $output;
	}

/**
 * writeFiles method
 *
 * Ensure paths to files are relative so that PoEdit can open them directly
 * If the domain is a plugin - write to the plugin
 *
 * @return void
 * @access private
 */
	function __writeFiles() {
		$localeFolders[] = rtrim($this->__output, DS) . DS;
		foreach ($this->__storage as $domain => $_) {
			if (is_dir($this->params['working'] . DS . 'plugins' . DS . $domain)) {
				unset ($this->__storage[$domain]);
				$localeFolders[] = rtrim($this->__output, DS) . DS . '..' . DS . 'plugins' . DS . $domain . DS . 'locale' . DS;
				$domain = '..' . DS . 'plugins' . DS . $domain . DS . 'locale' . DS . $domain;
				$this->__storage[$domain] = $_;
			}
			if (file_exists(rtrim($this->__output, DS) . DS . $domain . '.pot')) {
				unlink($this->__output . DS . $domain . '.pot');
			}
		}
		parent::__writeFiles();
		if ($this->settings['autoUpdatePo']) {
			$this->_updatePoFiles($localeFolders);
		}
	}

/**
 * searchFiles method - Drop any dot files, drop any test files, if it's in a plugin and we're
 * running in an app - drop any plugin files (processed seperately)
 *
 * @return void
 * @access private
 */
	function __searchFiles() {
		parent::__searchFiles();
		foreach($this->__files as $i => $file) {
			if (strpos($file, DS . '.') ||
			 strpos($file, DS . 'tests' . DS) ||
			 strpos($file, DS . 'uploads' . DS) ||
			 strpos($file, DS . 'webroot' . DS) ||
			 (!empty($this->settings['excludeAdmin']) && strpos($file, DS . 'admin_')) ||
			 ($this->settings['autoPlugin'] &&
				empty($this->params['plugin']) &&
				is_dir($this->params['paths'] . 'plugins') &&
				strpos($file, DS . 'plugins' . DS))
			) {
				unset ($this->__files[$i]);
			}
		}
		$this->__files = array_values($this->__files);
	}

/**
 * Prepare a file to be stored - use shortpaths
 *
 * @return void
 * @access private
 */
	function __store($domain, $header, $sentence) {
		$header = str_replace(APP, '', $header);
		$header = str_replace(ROOT, '..' . DS, $header);
		$header = str_replace(CAKE, '..' . DS . 'cake' . DS, $header);

		if (!isset($this->__storage[$domain])) {
			$this->__storage[$domain] = array();
		}
		if (!isset($this->__storage[$domain][$sentence])) {
			$this->__storage[$domain][$sentence] = $header;
		} else {
			$this->__storage[$domain][$sentence] .= $header;
		}
	}

/**
 * includeForPlugin method
 *
 * If the extract shell is launched directly in a plugin dir
 * Include all of its models and controllers to ensure that the calls to App::import
 * work
 *
 * @return void
 * @access private
 */
	function __includeForPlugin() {
		if (file_exists(dirname(dirname(APP)) . DS . 'config' . DS . 'bootstrap.php')) {
			include_once(dirname(dirname(APP)) . DS . 'config' . DS . 'bootstrap.php');
		}
		App::import('Core', array('Model', 'Controller'));
		if (file_exists(APP_DIR . '_app_controller.php')) {
			if (file_exists(APP . '..' . DS . '..' . DS . 'app_controller.php')) {
				require_once(APP . '..' . DS . '..' . DS . 'app_controller.php');
			} else {
				App::import('Controller', 'Dummy');
			}
			require_once(APP_DIR . '_app_controller.php');
			$controllers = MiCache::mi('controllers', $this->params['plugin']);
			foreach($controllers as $file => $_) {
				require_once($file);
			}
		}
		if (file_exists(APP_DIR . '_app_model.php')) {
			if (file_exists(dirname(dirname(APP)) . DS . 'config' . DS . 'database.php')) {
				include_once(dirname(dirname(APP)) . DS . 'config' . DS . 'database.php');
			}
			if (file_exists(APP . '..' . DS . '..' . DS . 'app_model.php')) {
				require_once(APP . '..' . DS . '..' . DS . 'app_model.php');
			} else {
				App::import('Model', 'Dummy');
			}
			require_once(APP_DIR . '_app_model.php');
			$models = MiCache::mi('models', $this->params['plugin']);
			foreach($models as $file => $_) {
				require_once($file);
			}
		}
	}
}