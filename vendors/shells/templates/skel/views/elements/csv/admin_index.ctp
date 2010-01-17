<?php
header('Content-Disposition: attachment; filename="' . Inflector::pluralize($modelClass) . implode($this->passedArgs, '_') . '.csv"');
if (isset($data['0'])) {
	$header[] = 'delete (x)';
	foreach ($data['0'] as $model => $vals) {
		foreach ($vals as $key => $_val) {
			if (is_int($model)) {
				$header[] = $key;
			} else {
				$header[] = $model.'.'.$key;
			}
		}
	}
	echo implode ($header, "\t") . "\r\n";
}
foreach ($data as $result) {
	$results = array('');
	foreach ($result as $model => $vals) {
		foreach ($vals as $key => $val) {
			if (is_array($val)) {
				$results[] = count ($val) . 'items';
			} else {
				$results[] = str_replace(array("\n", "\r", "\t"), '',  $val);
			}
		}
	}
	echo implode ($results, "\t") . "\r\n";
}