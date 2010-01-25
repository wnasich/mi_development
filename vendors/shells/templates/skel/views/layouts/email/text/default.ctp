<?php /* SVN FILE: $Id$ */
echo $this->element('email/text/header') . "\r\n";
echo wordwrap($content_for_layout, 80, "\n", true) . "\r\n";
echo $this->element('email/text/footer');