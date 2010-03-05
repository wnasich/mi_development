<?php
/**
 * Dev specific logic and tools
 *
 * Long description for dev_controller.php
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       base
 * @subpackage    base.controllers
 * @since         v 1.0
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
if (!defined('BACKUPS_DIR')) {
	define('BACKUPS_DIR', TMP);
}

/**
 * DevController class
 *
 * All of the functions that afect the system are deliberately admin functions - do not make these functions
 * publically accessible.
 *
 * @uses          Controller
 * @package       base
 * @subpackage    base.controllers
 */
class DevController extends AppController {

/**
 * uses property
 *
 * @var array
 * @access public
 */
	var $uses = array();

/**
 * return property
 *
 * @var array
 * @access private
 */
	var $__return = array();

/**
 * beforeFilter method
 *
 * Ensure public access
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		if (isset($this->Auth)) {
			if (Configure::read()) {
				$this->Auth->allow('*');
			} else {
				$this->Auth->deny('*');
			}
		}
	}

/**
 * Incase a user lands here - send them to /admin
 *
 * @return void
 * @access public
 */
	function admin_index() {
		$this->redirect('/admin');
	}

/**
 * admin_install method
 *
 * @param mixed $package null
 * @return void
 * @access public
 */
	function admin_install($package = null) {
		if (!$package) {
			$this->Session->setFlash('No package name given to install');
			$this->_back();
		}

		$packages = func_get_args();
		$package = implode(array_map($packages, 'escapeshellarg'), ' ');
		$out = $this->__cake('iq install ' . $packages);
		$this->Session->setFlash(implode($out, '<br />'));
		return $this->redirect($this->referer());
	}

/**
 * admin_build method
 *
 * @param string $what '*'
 * @return void
 * @access public
 */
	function admin_build($what = '*') {
		if ($this->data) {
			unset($this->data['App']);
			$whats = array_keys(array_filter($this->data));
		} else {
			$whats = array($what);
		}
		foreach($whats as $what) {
			$what = escapeshellarg($what);
			$out = $this->__cake('build ' . $what);
			$this->Session->setFlash(implode($out, '<br />'));
		}
		return $this->redirect($this->referer());
	}

/**
 * admin_clear method
 *
 * Clear all files found in the requested (tmp dir is the root for this method) path.
 * Will ignore svn files and folders
 *
 * @return void
 * @access public
 */
	function admin_clear($what = 'TMP') {
		if ($this->data) {
			unset($this->data['App']);
			$whats = array_keys(array_filter($this->data));
		} else {
			$whats = array($what);
		}
		foreach($whats as $what) {
			$what = escapeshellarg($what);
			$out = $this->__cake("clear $what -appFiles -q");
			$this->Session->setFlash(implode($out, '<br />'));
		}
		return $this->redirect($this->referer());
	}

/*
 * admin_db_dump method
 *
 * Dumps the database to a file, by default named (name of app dir) appended with the date and time
 * The time is rounded to the nearest 10 minute interval to allow recovery of a partial downloaded file even
 * if no name for the file was requested. If you are having problems, specify a name so that it is not time
 * dependent, and can be recovered at any time in the future - Useful if you have an active database.
 *
 * @TODO Currently, if the download was successful the file is not deleted - add cleanup
 * @param bool $compress
 * @param string $name  the name of the download file
 * @return void
 * @access public
 */
	function admin_db_dump($compress = false, $name = null, $sources = null) {
		if ($this->data) {
			extract($this->data);
			if ($sources) {
				$sources = array_values(array_filter($sources));
			}
		}
		if (!$name) {
			$name = APP_DIR . '_' . date('ymd-H') . str_pad((int)(date('i') / 10) * 10, 2, '0');
			return $this->redirect(array($compress?$compress:'0', $name));
		}
		$dir = BACKUPS_DIR . $name;
		$name .= '.sql';
		$folder = new Folder($dir, true);
		if (!$sources) {
			$sources = $this->_deriveSources();
		}

		$multiFile = (count($sources) > 1);
		if ($multiFile && !$compress) {
			$compress = 'zip';
		}

		$file = $this->_dumpSql($sources, $dir);
		if ($compress) {
			$return = $this->_compressFiles($compress, compact('dir', 'file', 'name', 'multiFile'));
			extract($return);
		} else {
			$extension = 'txt';
		}
		$data['download'] = true;
		$data['path'] = dirname($file) . DS;
		$data['id'] = basename($file);
		$data['extension'] = $extension;
		$data['name'] = $name;
		$this->set($data);
		$this->view = 'Media';
		Configure::write('debug', 0);
		$this->render();
		// this would only be reached if the download failed
		//$folder->delete();
	}

/**
 * admin_upgrade method
 *
 * @param string $what 'app'
 * @param mixed $name null
 * @return void
 * @access public
 */
	function admin_upgrade($what = 'app', $name = null) {
		if ($this->data) {
			unset($this->data['App']);
			foreach($this->data as $type => $items) {
				if (!is_array($items)) {
					continue;
				}
				$items = array_keys(array_filter($items));
				foreach($items as $item) {
					$this->data["$type.$item"] = 1;
				}
				unset ($this->data[$type]);
			}
			$whats = array_keys(array_filter($this->data));
		} elseif ($name) {
			$whats = array("$what.$name");
		} else {
			$whats = array($what);
		}
		foreach($whats as $what) {
			$name = null;
			if (strpos($what, '.')) {
				list($what, $name) = explode('.', $what);
			}
			$what = escapeshellarg($what);
			if ($name) {
				$name = escapeshellarg($name);
				$name = ' ' . $name;
			}
			$out = $this->__cake('iq upgrade ' . $what . $name);
			$this->Session->setFlash(implode($out, '<br />'));
		}
		return $this->redirect($this->referer());
	}

/**
 * admin_check method
 *
 * @return void
 * @access public
 */
	function admin_check($correct = false) {
		$out = $this->__cake('iq check');
		$this->Session->setFlash(implode($out, '<br />'));
		$this->_back();
	}

/**
 * deriveSource
 *
 * Load each model in turn, to try and capture any model specific source changing logic.
 * Check config sources too
 *
 * @return array of source names
 * @access protected
 */
	function _deriveSources() {
		$models = Configure::listObjects('model');
		foreach ($models as $model) {
			ClassRegistry::init(array(
				'class' => $model,
				'table' => false
			));
		}
		App::import('Core', 'ConnectionManager');
		$sources = ConnectionManager::sourceList();
		$configSources = array_keys((get_class_vars('DATABASE_CONFIG')));
		foreach ($configSources as $source) {
			if (strpos($source, 'test') === 0) {
				continue;
			} elseif (in_array($source, $sources)) {
				continue;
			}
			$sources[] = $source;
		}
		return $sources;
	}

/**
 * dumpSql method
 *
 * For each source passed, create an sql dump for it. Generates 1 file per database, skips
 * if the db isn't mysql
 * If the target file for a specific dump already exists - it is not recreated
 *
 * @param array $sources
 * @return string the path to the (last or only) sql file generated
 * @access protected
 */
	function _dumpSql($sources = array(), $dir) {
		$databases = array();
		foreach ($sources as $source) {
			$db =& ConnectionManager::getDataSource($source);
			extract ($db->config);
			if (!in_array($driver, array('mysql', 'mysqli')) || isset($databases[$database])) {
				continue;
			}
			$file = $dir . DS . $database . '.sql';
			if (!file_exists($file)) {
				$params = array('-h' . $host);
				if ($port) {
					$params[] = '-P' . $port;
				}
				$params[] = '-u' . $login;
				if ($password) {
					$params[] = '-p' . $password;
				}
				if ($encoding) {
					$params[] = '--default-character-set=' . low($encoding);
				}
				$command = 'mysqldump ' . implode($params, ' ') . ' ' . $database .
					' --add-drop-table --compact=true --disable-keys >> ' . $file;
				$this->_exec($command);
			}
			$databases[$database] = true;
		}
		return $file;
	}

/**
 * importSql method
 *
 * @param mixed $file null
 * @return void
 * @access protected
 */
	function _importSql($file = null) {
		$db =& ConnectionManager::getDataSource('default');
		extract ($db->config);
		if (!in_array($driver, array('mysql', 'mysqli'))) {
			continue;
		}
		$params = array('-h' . $host);
		if ($port) {
			$params[] = '-P' . $port;
		}
		$params[] = '-u' . $login;
		if ($password) {
			$params[] = '-p' . $password;
		}
		$sources = array();
		$sources = $db->sources();

/*
foreach ($_sources as $table) {
	$sources[] = $db->name($table);
}
*/
		$commands[] = 'DROP TABLE ' . implode($sources, ', ') . ' | mysql ' . implode($params, ' ');
		if ($encoding) {
			$params[] = '--default-character-set=' . low($encoding);
		}
		$commands[] = 'mysql ' . implode($params, ' ') . ' ' . $database . ' < ' . $file;
		trigger_error ($commands); die;
	}

/**
 * compressFiles method
 *
 * Turn the file or folder of files generated by the dump into a compressed file
 * If the target compressed file already exists - it is not recreated
 *
 * @param string $method
 * @param mixed $params
 * @return array modified dir, file, name and the file extension
 * @access protected
 */
	function _compressFiles($method = 'zip', $params) {
		extract($params);
		$extension = $method;
		$command = '';
		if ($multiFile && $method != 'zip') {
			if (!file_exists($dir . '.tar')) {
				$this->_exec('cd ' . $dir . ' &&  tar -cf ' . $dir . '.tar *');
			}
			$dir .= '.tar';
			$name .= '.tar';
		}
		switch ($method) {
		case 'bz2':
			if ($multiFile) {
				$command .= 'bzip2 ' . $dir;
				$file = $dir . '.bz2';
			} else {
				$command .= 'bzip2 ' . $file;
				$file = $file . '.bz2';
			}
			break;
		case 'gzip':
			$extension = 'gz';
			if ($multiFile) {
				$command .= 'gzip ' . $dir;
				$file = $dir . '.gz';
			} else {
				$command .= 'gzip ' . $file;
				$file = $file . '.gz';
			}
			break;
		case 'zip':
			$command .= 'zip -rj ' . $dir . '.zip ' . $dir;
			$file = $dir . '.zip';
			break;
		}
		if ($command && !file_exists($file)) {
			$this->_exec($command);
		}
		return compact('dir', 'file', 'extension', 'name');
	}

/**
 * exec method
 *
 * @param mixed $cmd
 * @param mixed $out null
 * @return void
 * @access protected
 */
	protected function _exec($cmd, &$out = null) {
		if (!class_exists('Mi')) {
			APP::import('Vendor', 'Mi.Mi');
		}
		return Mi::exec($cmd, $out);
	}

/**
 * cake method
 *
 * @param mixed $command
 * @return void
 * @access private
 */
	function __cake($command) {
		$app = APP;
		$cake = CAKE_CORE_INCLUDE_PATH . DS . 'cake'. DS . 'console' . DS . 'cake';
		if (DS === '\\') {
			$cake .= '.bat';
		}
		$command = "cd $app && $cake $command -q";
		$this->_exec($command, $out);
		return $out;
	}
}