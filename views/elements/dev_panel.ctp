<style>
form.mi_development {
	clear: both;
	margin-right: 20px;
	padding: 0;
	width: 80%;
}
form.mi_development fieldset {
	border: 1px solid #ccc;
	margin-top: 30px;
	padding: 16px 20px;
}
form.mi_development fieldset legend {
	background:#fff;
	color: #e32;
	font-size: 160%;
	font-weight: bold;
}
form.mi_development fieldset fieldset {
	margin-top: 0px;
	margin-bottom: 20px;
	padding: 16px 10px;
}
form.mi_development fieldset fieldset legend {
	font-size: 120%;
	font-weight: normal;
}
form.mi_development fieldset fieldset div {
	clear: left;
	margin: 0 20px;
}
form.mi_development div {
	clear: both;
	margin-bottom: 1em;
	padding: .5em;
	vertical-align: text-top;
}
form.mi_development div.input {
	color: #444;
}
form.mi_development div.required {
	color: #333;
	font-weight: bold;
}
form.mi_development div.submit {
	border: 0;
	clear: both;
	margin-top: 10px;
	margin-left: 140px;
}
form.mi_development label {
	display: block;
	font-size: 110%;
	padding-right: 20px;
}
form.mi_development input, form.mi_development textarea {
	clear: both;
	font-size: 140%;
	font-family: "frutiger linotype", "lucida grande", "verdana", sans-serif;
	padding: 2px;
	width: 100%;
}
form.mi_development select {
	clear: both;
	font-size: 120%;
	vertical-align: text-bottom;
}
form.mi_development select[multiple=multiple] {
	width: 100%;
}
form.mi_development option {
	font-size: 120%;
	padding: 0 3px;
}
form.mi_development input[type=checkbox] {
	float: left;
	margin: 0px 6px 7px 2px;
	width: 25%;
}
form.mi_development input[type=radio] {
	float:left;
	width:auto;
	margin: 0 3px 7px 0;
}
form.mi_development div.radio label {
	margin: 0 0 6px 20px;
}
form.mi_development input[type=submit] {
	display: inline;
	font-size: 110%;
	padding: 2px 5px;
	width: auto;
	vertical-align: bottom;
}

form.mi_development {
	clear: none;
	float: left;
	margin-right: 0;
	padding: 0;
	width: 49%;
}
form.mi_development fieldset {
	border: 1px solid #ccc;
	clear: left;
	float: left;
	margin-top: 5px;
	padding: 16px 20px;
	width: 75%;
}
form.mi_development input {
	clear: none;
	font-size: 100%;
	font-family: "frutiger linotype", "lucida grande", "verdana", sans-serif;
	height: 1.2em;
	margin:0 6px 7px 2px;
	padding: 2px;
	width: 100%;
}
form.mi_development label {
	height: 1.2em;
	float: left;
}
form.mi_development div.submit {
	clear: none;
	float: left;
	margin-top: 1em;
	margin-left: 1em;
	margin-right: 0;
	margin-bottom: 0;
	width: 50px;
}
form.mi_development div.submit input {
	background: none;
	height: 5em;
	width: 70px;
}
</style>
<script>
/**
 * easy refresh-css keybinding to alt-w
 * alt-r was taken in IE, so consider this a CSS Weefresh
 *
 * @link http://gist.github.com/221905
 * @link http://paulirish.com/2008/how-to-iterate-quickly-when-debugging-css/
 */
(function($) {$(function() {
	$(document).bind('keyup', function(e) {
		if ( e.which == 87 && e.altKey) {
			console.log('reloading css files');
			var h,a,f;
			a=document.getElementsByTagName('link');
			for(h=0;h<a.length;h++){
				f=a[h];
				if(f.rel.toLowerCase().match(/stylesheet/)&&f.href) {
					var g=f.href.replace(/(&|\?)forceReload=\d+/,'');
					f.href=g+(g.match(/\?/)?'&':'?')+'forceReload='+(new Date().valueOf())
				}
			}
		}
	});
});})(jQuery)
</script>
<?php
echo $form->create(false, array(
	'url' => array('plugin' => 'mi_development', 'admin' => true, 'controller' => 'dev', 'action' => 'clear'),
	'class' => 'mi_development clearfix'
));
echo $form->inputs(array(
	'legend' => 'Clear temporary Files',
	'TMP' => array('type' => 'checkbox', 'div' => false, 'checked' => 1),
	'css' => array('type' => 'checkbox', 'div' => false),
	'js' => array('type' => 'checkbox', 'div' => false),
	'images' => array('type' => 'checkbox', 'div' => false)
));
echo $form->end('Clear');

echo $form->create(false, array(
	'url' => array('plugin' => 'mi_development', 'admin' => true, 'controller' => 'dev', 'action' => 'build'),
	'class' => 'mi_development clearfix'
));
echo $form->inputs(array(
	'legend' => 'Build Assets',
	'css' => array('type' => 'checkbox', 'div' => false),
	'js' => array('type' => 'checkbox', 'div' => false),
));
echo $form->end('Build');

echo $form->create(false, array(
	'url' => array('plugin' => 'mi_development', 'admin' => true, 'controller' => 'dev', 'action' => 'upgrade'),
	'class' => 'mi_development clearfix'
));
$upgradeOptions = array(
	'legend' => 'Upgrade',
	'app' => array('type' => 'checkbox', 'div' => false, 'checked' => true),
	'cake' => array('type' => 'checkbox', 'div' => false),
);
$plugins = MiCache::mi('plugins');
foreach($plugins as $path => &$plugin) {
	if (is_dir($path . DS . '.git') || is_dir($path . DS . '.svn')) {
		$plugin = "plugin.$plugin";
		continue;
	}
	unset($plugins[$path]);
}
if (!in_array('mi', $plugins)) {
	$plugins['dummy'] = 'plugin.mi';
}
sort($plugins);
foreach($plugins as $plugin) {
	$upgradeOptions[$plugin] = array('type' => 'checkbox', 'div' => false);
}
echo $form->inputs($upgradeOptions);
echo $form->end('Upgrade');

echo $form->create(false, array(
	'url' => array('plugin' => 'mi_development', 'admin' => true, 'controller' => 'dev', 'action' => 'upgrade'),
	'class' => 'mi_development clearfix'
));
echo $form->inputs(array(
	'legend' => 'Check Dependencies',
	'all' => array('type' => 'checkbox', 'div' => false, 'checked' => true)
));
echo $form->end('Check');

echo $form->create(false, array(
	'url' => array('plugin' => 'mi_development', 'admin' => true, 'controller' => 'dev', 'action' => 'db_dump'),
	'class' => 'mi_development clearfix'
));
$sources = ConnectionManager::enumConnectionObjects();
$dbSources = array();
$DBConfig = new DATABASE_CONFIG();
foreach($sources as $source => $_) {
	if (!isset($DBConfig->$source) ||
		!isset($DBConfig->{$source}['host']) ||
		!isset($DBConfig->{$source}['database']) ||
		isset($dbSources[$DBConfig->{$source}['host'] . $DBConfig->{$source}['database']])) {
		continue;
	}
	$dbSources[$DBConfig->{$source}['host'] . $DBConfig->{$source}['database']] = $source;
}
$dbSources = array_combine(array_values($dbSources), array_values($dbSources));
echo $form->inputs(array(
	'legend' => 'Data dump',
	'name' => array('value' => APP_DIR . '_' . date('ymd-H') . str_pad((int)(date('i') / 10) * 10, 2, '0')),
	'sources' => array('options' => $dbSources, 'multiple' => true, 'div' => false),
	'structure' => array('type' => 'checkbox', 'checked' => true),
	'data' => array('type' => 'checkbox', 'checked' => true),
	'format' => array(
		'default' => 'sql',
		'disabled' => true,
		'options' => array('sql' => 'Native sql', 'schema' => 'schema'),
		'empty' => false
	),
	'compress' => array(
		'label' => 'compress',
		'options' => array('zip' => 'zip', 'bz2' => 'bz2', 'gzip' => 'gzip'),
		'value' => DS==='\\'?'zip':'gzip',
		'div' => false,
		'empty' => 'none'
	),
));
echo $form->end('Backup');

return;
ob_start();
phpinfo();
$out = ob_get_clean();
echo preg_replace('@</body>.*$@s', '', preg_replace('@.*<body>@s', '', $out));
return;
?>
<br />
<br />
<?php
$plugin = $this->params['plugin'];
$details = array();
$types = array('controllers', 'components', 'models', 'behaviors', 'datasources', 'helpers', 'shells');
foreach($types as $type) {
	$details[$type] = MiCache::mi($type, compact('plugin'));
}
echo $toolbar->makeNeatArray($details);