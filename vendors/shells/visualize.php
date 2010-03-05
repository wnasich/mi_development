<?php
/**
 * Visualize console task
 *
 * This task can be used to generate a graphical representation of your tables or models.
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) Tomenko Yevgeny
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Tomenko Yevgeny
 * @link          www.ad7six.com
 * @package       base
 * @subpackage    base.vendors.shells
 * @since         v 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
uses('Folder','File','model'.DS.'connection_manager');
App::import('Vendor', 'Mi.MiCache');

/**
 * VisualizeShell class
 *
 * @TODO restructure and consolidate to use a $settings var
 * @uses          Shell
 * @package       base
 * @subpackage    base.vendors.shells
 */
class VisualizeShell extends Shell {

/**
 * center property
 *
 * The central node
 *
 * @var mixed null
 * @access public
 */
	var $center = null;

/**
 * data property
 *
 * @var array
 * @access public
 */
	var $data = array();

/**
 * docDir property
 *
 * @var string ''
 * @access public
 */
	var $docDir = '';

/**
 * prefix property
 *
 * @var mixed APP_DIR
 * @access public
 */
	var $prefix = APP_DIR;

/**
 * recursive property
 *
 * how many relations from the central node to display. Only relevant if a center node is specified
 *
 * @var int 1
 * @access public
 */
	var $recursive = 0;

/**
 * style property
 *
 * What data to include, either details, fields or names
 *
 * @var string 'details'
 * @access public
 */
	var $style = 'details';

/**
 * graphToolPath property
 *
 * Set to the full path if necessary. e.g. C:\dev\_tools_\graphviz-2.16\bin\dot.exe
 *
 * @var string 'dot.exe'
 * @access public
 */
	var $graphToolPath = 'dot.exe';

/**
 * exclude property
 *
 * Models not to include in generation. Can be overriden at runtime e.g.:
 * cake visualize -e
 * 	No Excludes
 * cake visualize -e Model1,Model2
 * 	Exclude only Model1 and 2
 *
 * @var array
 * @access public
 */
	var $exclude = array(
		'Attachment', 'Image',  // Sunset
		'Email', 'Enum', 'Import', 'Media', 'MediaLink', 'MiEmail', 'Note', 'Setting'
	);

/**
 * log property
 *
 * @var mixed null
 * @access private
 */
	var $__log = null;

/**
 * createImg method
 *
 * @param mixed $command
 * @param mixed $dotFile
 * @param mixed $imgFile
 * @access public
 * @return void
 */
	function createImg($command, $dotFile, $imgFile) {
		$command = "{$command} -Tpng  -o\"{$imgFile}\" \"{$dotFile}\"";
		$this->__log->append($command . "\r\n");
		ob_start();
		if (!class_exists('Mi')) {
			APP::import('Vendor', 'Mi.Mi');
		}
		Mi::exec($command, $return);
		ob_clean();
		if ($return != 0) {
			$this->out("Command Error ($return) for command:\n");
			$this->out("$command\n");
			return false;
		}
		return true;
	}

/**
 * createImgs method
 *
 * @param mixed $dotFile
 * @param mixed $path
 * @param mixed $mode
 * @access public
 * @return void
 */
	function createImgs($dotFile, $path, $mode) {
		if (is_string($this->graphToolPath)) {
			$commands = array($this->graphToolPath);
		} else {
			$commands = $this->graphToolPath;
		}
		uses ('Sanitize');
		foreach ($commands as $command) {
			$imgFile = $path . DS . 'schematic_' . $mode . '_' . Sanitize::paranoid($command) . ".png";
			if (file_exists($imgFile)) {
				$f = new File($imgFile);
				$f->delete();
			}
			$this->out("Creating {$imgFile} ...");
			$start = getMicrotime();
			if ($this->createImg($command, $dotFile, $imgFile)) {
				$time = round(getMicrotime() - $start, 4);
				$this->out("... created in {$time}s");
			} else {
				$this->out("oops - problem generating {$imgFile}");
				break;
			}
		}
	}

/**
 * generateDataFromModels method
 *
 * @access public
 * @return void
 */
	function generateDataFromModels() {
		$allModels = array_values(MiCache::mi('models'));
		$models = array_diff($allModels, $this->exclude);
		$excluded = array_intersect($this->exclude, $allModels);
		if ($excluded) {
			$this->out("The following models have been excluded");
			$excluded = implode(', ', $excluded);
			$this->out(" 	$excluded");
		}
		foreach($models as $model) {
			$this->out("Looking at model: {$model}");
			$model = ClassRegistry::init($model);
			if (!$model || empty($model->useTable)) {
				$this->out("	Skipping because it has no table");
				continue;
			}
			$this->data['tables'][$model->name] = $model->schema(true);
			foreach ($this->data['tables'][$model->name] as $attrname => $attr) {
				if (!empty($attr['length'])) {
					$attr['type'] .= "[{$attr['length']}]";
				}
				$this->data['nodes'][$model->name][$attrname] = $attr['type'];
				if (!empty($attr['default'])) {
					$this->data['nodes'][$model->name][$attrname] .= ", default: \\\"{$attr['default']}\\\"";
				}
			}
			foreach($model->__associations as $type) {
				foreach ($model->$type as $alias => $association) {
					$otherModel = $association['className'];
					if (in_array($otherModel, $this->exclude)) {
						$this->out("Skipping $otherModel");
						continue;
					}
					if ($type == 'belongsTo') {
						$this->data['associations'][$model->name.$alias] =
							array('label'=> $model->name . '->' . $alias, 'node1'=> $model->name, 'node2'=> $otherModel);
					} elseif (in_array($type, array('hasOne', 'hasMany'))) {
						$this->data['associations'][$alias.$model->name] =
							array('label'=> $otherModel . '->' . $model->name, 'node1'=> $otherModel, 'node2'=> $model->name);
					} elseif ($type == 'hasAndBelongsToMany') {
						$names[] = $model->name;
						$names[] = $alias;
						sort($names);
						$modelName = implode($names, '');
						if (!isset($modelName)) {
							$DynamicModel = new Model(array('name'=> $modelName, 'table'=> $association['joinTable']));
							$this->data['tables'][$modelName] = $DynamicModel->schema(true);
							foreach ($this->data['tables'][$modelName] as $attrname => $attr) {
								if (!empty($attr['length'])) {
									$attr['type'] .= "[{$attr['length']}]";
								}
								$this->data['nodes'][$modelName][$attrname] = $attr['type'];
								$attrtype = $attr['type'];
								if (!empty($attr['default'])) {
									$this->data['nodes'][$modelName][$attrname] .= ", default: \\\"{$attr['default']}\\\"";
								}
							}
							$this->data['associations'][$model->name.$otherModel] =
								array('label'=> $model->name . '->' . $modelName, 'node1'=> $model->name, 'node2'=> $modelName);
							$this->data['associations'][$otherModel.$model->name] =
								array('label'=> $otherModel . '->' . $modelName, 'node1'=> $otherModel, 'node2'=> $modelName);
						}
					}
				}
			}
		}
	}

/**
 * generateDataFromTables method
 *
 * @access public
 * @return void
 */
	function generateDataFromTables() {
		$components =  MiCache::mi('components');
		$allModels = array_values(MiCache::mi('models'));
		$models = array_diff($allModels, $this->exclude);
		$excluded = array_intersect($this->exclude, $allModels);

		$tables = MiCache::mi('tables');
		if ($excluded) {
			$excludedTables = array_map(array('Inflector', 'tableize'), $excluded);
			$excluded = array_intersect($excludedTables, $tables);
			$tables = array_diff($tables, $excludedTables);
			if ($excluded) {
				$this->out("The following tables have been excluded");
				$excluded = implode(', ', $excluded);
				$this->out(" 	$excluded");
			}
		}

		$tableMap = array();
		foreach ($models as $model) {
			$inst = ClassRegistry::init($model);
			if (!empty($inst->useTable)) {
				$tableMap[$inst->useTable] = $model;
			}
		}
		foreach($tables as $table_name) {
			$this->out("Looking at table: {$table_name}");
			if (isset($tableMap[$table_name])) {
				$modelName = $tableMap[$table_name];
			} else {
				$modelName = $this->_modelName($table_name);
			}
			$this->data['tables'][$modelName] = $this->getSchemaInfo($modelName, $table_name);
		}
		foreach ($this->data['tables'] as $table => $attributes) {
			if (is_array($attributes) && count($attributes)>0) {
				foreach ($attributes as $attrname => $attr) {
					if (substr($attrname, -3) == '_id') {
						# Create an association to other table
						$otherTable = Inflector::camelize(r('_id','',$attrname));
						if (!empty($this->data['tables'][$otherTable])) {
							$other_table = $this->data['tables'][$otherTable];
							$this->data['associations'][] = array('label'=> $attrname, 'node1'=> $table, 'node2'=> $otherTable);
						}
					}
					if (!isset($attr['type'])) {
						$attr['type'] = '';
					}
					if (!empty($attr['length'])) {
						$attr['type'] .= "[{$attr['length']}]";
					}
					$this->data['nodes'][$table][$attrname] = $attr['type'];
					$attrtype = $attr['type'];
					if (!empty($attr['default'])) {
						$this->data['nodes'][$table][$attrname] .= ", default: \\\"{$attr['default']}\\\"";
					}
				}
			}
		}
	}

/**
 * getSchemaInfo method
 *
 * @param mixed $modelName
 * @param mixed $table_name
 * @access public
 * @return void
 */
	function getSchemaInfo($modelName, $table_name) {
		if (App::import('model',$modelName)) {
			if (class_exists($modelName)) {
				$model = new $modelName();
			} else {
				$model = new AppModel(false, $table_name);
			}
			$attrs = array();
			$attrs = $model->schema();
			return $attrs;
		} else {
			$DynamicModel = new Model(array('name'=> $modelName, 'table'=> $table_name));
			$attrs = $DynamicModel->schema();
			return $attrs;
		}
		return false;
	}

/**
 * help method
 *
 * @access public
 * @return void
 */
	function help() {
		$this->out('CakePHP visualise, Usage examples:');
		$this->out('cake visualize [tables|models] [-c CenterModel [-r 0-9]] [-e [Models,ToExclude]] [-style details|fields|names]');
		$this->out('cake visualize tables');
		$this->out('	- generate graphic based on table structure');
		$this->out('cake visualize models');
		$this->out('	- generate graphic based on model association definitions, if neither tables|models is specified both are generated');
		$this->out('cake visualise -tool /path/to/graphVizTool');
		$this->out('cake visualise -e(xclude) [These,Models]');
		$this->out('	- Will supress the passed arguments from appearing in the graphic. Note that if the model doesn\'t');
		$this->out('	  use the table according to conventional - its table will still appear in the table versions');
		$this->out('cake visualise -style details(default)|fields|names');
		$this->out('	- generate graphics with varying levels of info in the nodes');
		$this->out('cake visualise -c(enter) NameOfModel [-r(ecursive) 0-9]');
		$this->out('	- specify the central model, and optionally the level of recursion to put in the images');
		$this->out('	  e.g. specifying -c Blog -r 0 will show everything directly linked to Blog');
		$this->hr();
	}

/**
 * initialize method
 *
 * @access public
 * @return void
 */
	function initialize() {
		if (DS == '/') {
			$this->graphToolPath = array(
				'dot',
				'dot -Gmode=heir',
				'neato',
				'neato -Gmodel=subset'
			);
		}
		$this->docDir = APP . 'config' . DS . 'sql';
		$this->prefix= 'img_';
		if (isset($this->params['tool'])) {
			$this->graphToolPath = $this->params['tool'];
		}
		if (isset($this->params['style'])) {
			$this->style = $this->params['style'];
		}
		$this->__log = new File($this->docDir . DS . 'visualize.log');
		return true;
	}

/**
 * main method
 *
 * @access public
 * @return void
 */
	function main() {
		$shortKeys = array('e' => 'exclude', 'c' => 'center', 'r' => 'recursive');
		foreach ($this->params as $key => $value) {
			if (in_array($key, array('app', 'root', 'working'))) {
				continue;
			}
			if (isset($shortKeys[$key])) {
				$key = $shortKeys[$key];
			}
			if (isset($this->$key) && is_array($this->$key)) {
				$value = explode(',', $value);
			}
			$this->$key = $value;
		}
		$start = getMicrotime();
		$mode = false;
		if (!isset($this->args[0])) {
			$this->generateDataFromTables();
			$this->writeDotFile($this->docDir, 't');
			$this->data = array();
			$this->generateDataFromModels();
			$this->writeDotFile($this->docDir, 'm');
		} elseif ($this->args[0] == 'help') {
			$this->help();
		} elseif ($this->args[0] == 'tables') {
			$mode = 't';
			$this->generateDataFromTables();
			$this->writeDotFile($this->docDir, 't');
		} elseif ($this->args[0] == 'models') {
			$mode = 'm';
			$this->generateDataFromModels();
			$this->writeDotFile($this->docDir, 'm');
		}
		if ($this->data) {
			$time = round(getMicrotime() - $start, 4);
			$this->out("Finished! and it only took {$time}s.");
		} else {
			$this->out("Invalid mode specified.");
		}
	}

/**
 * trim method
 *
 * @TODO stub
 * @param mixed $data
 * @return void
 * @access public
 */
	function trim() {
		if (!$this->center) {
			return;
		}
		$nodes = array($this->center => 0);
		$nodes = $this->_buildAssociations($nodes);
		$names = array_keys($nodes);
		foreach ($nodes as $name => $distance) {
			foreach ($this->data['associations'] as $i => $assoc) {
				if (!in_array($assoc['node1'], $names) && !in_array($assoc['node2'], $names)) {
					unset ($this->data['associations'][$i]);
				}
			}
		}
		foreach ($this->exclude as $name) {
			foreach ($this->data['associations'] as $i => $assoc) {
				if ($assoc['node1'] == $name || $assoc['node2'] == $name) {
					unset ($this->data['associations'][$i]);
				}
			}
		}
		$uniqueNames = array();
		foreach ($this->data['associations'] as $i => $assoc) {
			if (!in_array($assoc['node1'], $uniqueNames)) {
				$uniqueNames[] = $assoc['node1'];
			}
			if (!in_array($assoc['node2'], $uniqueNames)) {
				$uniqueNames[] = $assoc['node2'];
			}
		}
		foreach ($this->data['nodes'] as $name => $_) {
			if (!in_array($name, $uniqueNames)) {
				unset ($this->data['nodes'][$name]);
			}
		}
	}

/**
 * buildAssociations method
 *
 * @param mixed $nodes
 * @return void
 * @access protected
 */
	function _buildAssociations($nodes) {
		if ($this->recursive == 0) {
			return $nodes;
		}
		$count = count($nodes);
		foreach ($this->data['associations'] as $i => $assoc) {
			if (isset($nodes[$assoc['node1']])) {
				if (!isset($nodes[$assoc['node2']]) || $nodes[$assoc['node2']] > $nodes[$assoc['node1']]) {
					$nodes[$assoc['node2']] = $nodes[$assoc['node1']] + 1;
				}
			} elseif (isset($nodes[$assoc['node2']])) {
				if (!isset($nodes[$assoc['node1']]) || $nodes[$assoc['node1']] > $nodes[$assoc['node2']]) {
					$nodes[$assoc['node1']] = $nodes[$assoc['node2']] + 1;
				}
			}
		}
		if (count($nodes) == $count) {
			foreach ($nodes as $name => $recursive) {
				if ($recursive >= $this->recursive) {
					unset ($nodes[$name]);
				}
			}
			return $nodes;
		}
		return $this->_buildAssociations($nodes);
	}

/**
 * writeDotFile method
 *
 * @param mixed $target_dir
 * @param mixed $mode
 * @access public
 * @return void
 */
	function writeDotFile($target_dir, $mode) {
	        if (!file_exists($target_dir) || !is_dir($target_dir)) {
			$this->out("Creating directory \"{$target_dir}\"…");
			$folder = new Folder($target_dir, true);
		}
		if ($this->center) {
			$mode .= '_' . $this->center . '_' . $this->recursive;
		}
		$header = ''; //$this->prefix+strftime('%Y-%m-%d %H:%M:%S', time());
		$version = 0;
		if ($version > 0) {
			$header .= "\\nSchema version $version";
		}
		$dotFile = $target_dir .DS. 'mode_' . $mode . '.dot';
		if (file_exists($dotFile)) {
			$f = new File($dotFile);
			$f->delete();
		}
		$f = new File($dotFile, true );
		// Define a graph and some global settings
		$f->append("digraph G {\n");
		$f->append("overlap=false;\n");
		$f->append("splines=true;\n");
		$f->append("edge [fontname=\"Helvetica\", fontsize=8];\n");
		$f->append("ranksep=0.1;\n");
		$f->append("nodesep=0.1;\n");
		//    $f->append("\tedge [decorate=\"true\"];\n");
		$f->append("node [shape=record, fontname=\"Helvetica\", fontsize=9];\n");
		$assocs = array();
		$this->trim();
		// Draw the tables/models as boxes
		if (!$this->data) {
			return;
		}
		foreach ($this->data['nodes'] as $table => $attributes) {
			if ($this->style == 'fields') {
				$f->append("\t\"{$table}\" [shape=Mrecord, label=\"{<0> {$table}|<f0> ");
				foreach ($attributes as $field => $label) {
					$f->append("{$field}\l");
				}
				$f->append("}\"];\n");
			} elseif ($this->style == 'details') {
				$f->append("\t\"{$table}\" [shape=Mrecord, label=\"{<0> {$table}|{<f0> ");
				foreach ($attributes as $field => $label) {
					$f->append("{$field}\l");
				}
				$f->append("|<f1> ");
				foreach ($attributes as $field => $label) {
					$f->append("{$label}\l");
				}
				$f->append("}}\"];\n");
			} else {
				$f->append("\t\"{$table}\" [shape=polygon, sides=0, label=\"{$table}}\"];\n");
			}
		}
		// Draw the relations
		if (!empty($this->data['associations'])) {
			foreach ($this->data['associations'] as $assoc) {
				$f->append("\t\"{$assoc['node1']}\" -> \"{$assoc['node2']}\" [label=\"{$assoc['label']}\"]\n");
			}
		}
		// Close the graph
		$f->append("}\n");
		$f->close();        // Create the images by using dot and neato (grapviz tools)
		$this->out("Generated {$dotFile}\n");
		$this->createImgs($dotFile, $target_dir, $mode);
		// Remove the .dot file // Keep it for debugging and general info
		//$f->delete();
	}

/**
 * svnRevision method
 *
 * @param mixed $file
 * @return void
 * @access private
 */
	function __svnRevision($file) {
		$contents = file_get_contents($file);
		preg_match('/@version\s*\$Revision:\s*(\d*)\s\$/', $contents, $result);
		if ($result) {
			return $result[1];
		}
		return false;
	}
}