<?php /* SVN FILE: $Id$ */

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