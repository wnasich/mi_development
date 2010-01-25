<?php /* SVN FILE: $Id$ */
extract ($data);
echo '***' . $category . ' ' . htmlspecialchars($subject) . '***';
?>


<?php echo $body; ?>


From: <?php echo $from;?>


<?php
if ($url) {
	echo 'Url: ' . $url . "\r\n";
}
echo 'IP: ' . long2ip($ip);
?>


<?php
echo '**JUNK SCORE: ' . $junk_score . '**' . "\r\n";
echo 'Matching rules: ' . "\r\n\t" . implode("\r\n\t" , explode(';', $junk_rule_matches));