<?php echo $html->docType('xhtml-trans'); ?>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
	<head>
		<?php echo $html->charset(); ?>
		<title><?php echo htmlspecialchars($title_for_layout); ?></title>
		<?php
		if (!isset($pageTitle)) {
			$pageTitle = $title_for_layout;
		}
		echo $html->meta('icon');
		if (isset ($asset)) {
			echo $asset->css(array(
				'admin_default', 'jquery.tokeninput',
				'/js/theme/ui.all',
			));
			echo $asset->out('css');
			echo $asset->js(array(
				'jquery.blockUI',
				'jquery.mi_cloner', 'jquery.mi_dialogs', 'jquery.mi_lookups',
				'admin_default', 'lookups'
			));
			$locale = I18n::getInstance()->l10n->locale;
			if ($locale !== 'eng' && file_exists(APP . 'locale' . DS . $locale)) {
				echo $asset->js('i18n.' . $locale, 'localization');
			}
		}
		echo $scripts_for_layout;
		?>
	</head>
	<body>
		<?php echo $this->element('admin/header'); ?>
		<div id='container'>
			<div id='content'>
				<?php echo $this->element('flash'); ?>
				<h2><?php echo htmlspecialchars($pageTitle); ?></h2>
				<div class="container"><?php echo $content_for_layout; ?></div>
			</div>
			<?php echo $this->element('admin/menu/bar'); ?>
			<?php echo $this->element('hover_menu'); ?>
		</div><?php
		echo $this->element('admin/footer');
		echo $asset->out('js');
	?></body>
</html>