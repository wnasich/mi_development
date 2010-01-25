<?php /* SVN FILE: $Id$ */ ?>

___________________
<?php
$footerText = sprintf(__('Sent from %s', true), substr(env('HTTP_BASE'), 1));
$footerText .= ' | ' .  sprintf(__('Powered by %s', true), 'http://www.cakephp.org');
echo $footerText;