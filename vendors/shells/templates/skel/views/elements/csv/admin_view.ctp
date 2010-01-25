<?php /* SVN FILE: $Id$ */
header('Content-Disposition: attachment; filename="' . $modelClass . '_' . implode($this->passedArgs, '_') . '.csv"');
foreach ($data as $model => $vals) {
	echo $model . "\r\n";
	foreach ($vals as $key => $val) {
		if (is_array($val)) {
			$val = count ($val) . 'items';
		} else {
			$val = str_replace(array("\n", "\r", "\t"), '',  $val);
		}
		echo "\t" . $key . "\t" . $val . "\r\n";
	}
}