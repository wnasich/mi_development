<?php /* SVN FILE: $Id: index.ctp 1508 2009-09-01 21:59:41Z ad7six $ */

/*
foreach ($data as $id => $display) {
		$data[$id] = array(
			'id' => $id,
			'name' => $display,
		);
}
echo $javascript->object($data);
return;
*/
echo '[';
$out = array();
foreach ($data as $id => $name) {
	$out[] = '{"id":"' . $id . '","name":"' . $name . '"}';
}
echo implode($out, ',');
echo ']';