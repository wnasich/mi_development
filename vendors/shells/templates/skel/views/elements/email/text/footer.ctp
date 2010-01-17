<?php /* SVN FILE: $Id: footer.ctp 1467 2009-08-18 22:22:06Z ad7six $ */ ?>

___________________
<?php
$footerText = sprintf(__('Sent from %s', true), substr(env('HTTP_BASE'), 1));
$footerText .= ' | ' .  sprintf(__('Powered by %s', true), 'http://www.cakephp.org');
echo $footerText;