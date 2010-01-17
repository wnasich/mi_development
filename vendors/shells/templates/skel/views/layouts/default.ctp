<?php echo $html->docType('xhtml-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<?php echo $this->element('head', compact('title_for_layout', 'scripts_for_layout')); ?>
	<body id="<?php echo $this->name; ?>" class="<?php echo $this->action; ?>">
		<div id="container">
			<cake:nocache><?php echo $this->element('header'); ?></cake:nocache>
			<div id='wrapper' class="clearfix">
				<div id="content">
					<cake:nocache><?php echo $this->element('flash'); ?></cake:nocache>
					<?php echo $content_for_layout; ?>
				</div>
				<?php echo $this->element('menu/side'); ?>
				<?php echo $this->element('hover_menu'); ?>
			</div>
			<?php echo $this->element('footer'); ?>
		</div>
		<?php echo $asset->out('js'); ?>
	</body>
</html>