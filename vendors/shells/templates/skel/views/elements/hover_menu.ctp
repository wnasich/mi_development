<div id='hoverMenu'><?php
if (!isset($this->params['admin'])) {
	$currentLang = Configure::read('Config.language');
	if (!$currentLang) {
		$currentLang = 'eng';
	}
	if ($currentLang === 'eng') {
		$language = 'spa';
	} else {
		$language = 'eng';
	}
	Configure::write('Config.language', $language);
	I18n::getInstance()->l10n->get($language);
	$name = I18n::getInstance()->l10n->language;
	$name = __($name, true);
	echo $html->link(sprintf(__('switch to %1$s', true), $name), array('controller' => 'users', 'action' => 'switch_language', $language));
	Configure::write('Config.language', $currentLang);
}
?></div>