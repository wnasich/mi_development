<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Refresh" content="0;url=<?php echo $url?>"/>
		<script type='text/javascript'>
			document.location = '<?php echo $url?>'
		</script>
		<style><!--
			p.redirect { text-align:center; font:bold 1.1em sans-serif }
			p.redirect a { color:#444; text-decoration:none }
			p.redirect a:hover { text-decoration: underline; color:#44E }
		--></style>
	</head>
	<body>
		<p class="redirect"><?php echo $html->link(__('Redirecting...', true), $url, array('id' => 'redirect', 'class' => 'popout')); ?></p>
	</body>
</html>