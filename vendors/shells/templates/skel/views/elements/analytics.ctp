<?php
if (!isProduction()) {
	return;
}
$code = MiCache::setting('Site.analyticsCode');
if (!$code) {
	return;
}
?>
<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
	try {
		var pageTracker = _gat._getTracker("<?php echo $code ?>");
		<?php if (!empty($domain)) : ?>
		pageTracker._setDomainName("<?php echo $domain ?>");
		<?php endif; ?>
		pageTracker._trackPageview();
	} catch(err) {}
</script>