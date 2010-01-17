<?php
App::import('Vendor', 'Mi.MiCache');

/**
 * UuidifyShell class
 *
 * Convert your numeric id schema to uuids in a snip.
 * Note that it doesn't do anything to change your model files so for example if you've got validation
 * rules which only allow numeric ids - these should be changed to notEmpty
 * a web_id field is added (delete this if you don't want it) to every affected table in preparation for
 * using the Mi.List behavior to have a unique but shorter/easier id for use in urls etc.
 *
 * @uses          Shell
 * @package       mi_development
 * @subpackage    mi_development.vendors.shells
 */
class UuidifyShell extends Shell {

/**
 * script property
 *
 * @var array
 * @access protected
 */
	var $_script = array();

/**
 * startup method
 *
 * Set param defaults
 *
 * @return void
 * @access public
 */
	function startup() {
		$this->params = array_merge(array(
			'ds' => 'default',
		), $this->params);
	}

/**
 * main method
 *
 * For each table in the db - find all primary or foreign keys (that follow convetions) and convert
 * to uuids store the old id in both a field named web_id (which can be deleted if it's not wanted)
 * and in the id_map table - which is essential to be able to ensure that referencial links are
 * correctly updated
 *
 * @return void
 * @access public
 */
	function main() {
		$sources = array();
		foreach(explode(',', $this->params['ds']) as $ds) {
			$sources[$ds] = MiCache::mi('tables', $ds);
		}
		foreach ($sources as $ds => $tables) {
			foreach ($tables as $table) {
				$model = Inflector::classify($table);
				$this->_script[$ds]['9cleanup'][0] = "UPDATE id_map SET id = UUID() WHERE id IS NULL OR id = ''";
				$Inst = ClassRegistry::init(array(
					'class' => $model,
					'table' => $table,
					'ds' => $ds
				));
				$fields = $Inst->schema();
				foreach ($fields as $field => $details) {
					if (!preg_match('@(^|_)(id|key)$@', $field)) {
						continue;
					}
					if ($details['type'] !== 'integer' || $field === 'web_id') {
						continue;
					}

					if ($field === 'id') {
						$this->_script[$ds]['0start'][] = "INSERT IGNORE INTO id_map SELECT NULL, '$model', id FROM $table";
						$this->_script[$ds]['1run'][] = "ALTER TABLE $table CHANGE $field $field CHAR( 36 ) NOT NULL";
						$this->_script[$ds]['1run'][] = "ALTER TABLE $table ADD web_id INT( 11 ) UNSIGNED NOT NULL AFTER id";
						$this->_script[$ds]['1run'][] = "UPDATE $table SET web_id = id";
						$this->_script[$ds]['9cleanup'][] = "UPDATE $table SET id = (SELECT id FROM id_map where foreign_id = $table.id AND model = '$model')";
					} else {
						$this->_script[$ds]['1run'][] = "ALTER TABLE $table CHANGE $field $field CHAR( 36 ) NULL";
						if (in_array($field, array('foreign_id', 'foreign_key'))) {
							$this->_script[$ds]['9cleanup'][] = "UPDATE $table SET $field = COALESCE((SELECT id FROM id_map where foreign_id = $table.$field AND id_map.model = $table.model), $table.$field)";
						} elseif ($field === 'parent_id') {
							$this->_script[$ds]['9cleanup'][] = "UPDATE $table SET parent_id = COALESCE((SELECT id FROM id_map where foreign_id = $table.parent_id AND model = '$model'), $table.parent_id)";
						} else {
							$model = Inflector::classify(preg_replace('@_(id|key)@', '', $field));
							$this->_script[$ds]['9cleanup'][] = "UPDATE $table SET $field = COALESCE((SELECT id FROM id_map where foreign_id = $table.$field AND model = '$model'), $table.$field)";
						}
					}
				}
			}
		}
		foreach($this->_script as $ds => $steps) {
			ksort($steps);
			$this->Db = ConnectionManager::getDataSource($ds);
			$this->_setupIdMap();
			foreach($steps as $step => $statements) {
				foreach($statements as $statement) {
					$this->_query($statement);
				}
			}
		}
	}

/**
 * query method
 *
 * If the force (or the shortcut f) parameter is set, don't ask for confirmation
 *
 * @param mixed $statement
 * @return void
 * @access public
 */
	function _query($statement) {
		$this->out($statement);
		if (!empty($this->params['dryrun'])) {
			return;
		}
		if (!empty($this->params['f']) || !empty($this->params['force'])) {
			$continue = 'Y';
		} else {
			$continue = strtoupper($this->in(__('Run this statement?', true), array('Y', 'N', 'A', 'Q')));
			switch ($continue) {
				case 'Q':
					$this->_stop();
					return;
				case 'N':
					return;
				case 'A':
					$continue = 'Y';
					$this->params['f'] = true;
				case 'Y':
					break;
			}
		}
		if ($continue === 'Y') {
			$this->Db->query($statement);
		}
	}

/**
 * Create a table (only if it doesn't exist) to store the map of
 * (this model and this id) changed to => this uuid
 *
 * @return void
 * @access protected
 */
	function _setupIdMap() {
		$this->_query("CREATE TABLE IF NOT EXISTS id_map (
		  id char(36) NULL,
		  model varchar(50) NOT NULL,
		  foreign_id int(11) NOT NULL,
		  PRIMARY KEY model (model,foreign_id)
		);");
	}
}