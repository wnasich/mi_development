<?php /* SVN FILE: $Id: default.ctp 1895 2009-11-22 12:52:13Z ad7six $ */
echo $this->element('email/text/header') . "\r\n";
echo wordwrap($content_for_layout, 80, "\n", true) . "\r\n";
echo $this->element('email/text/footer');