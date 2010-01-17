<?php
if (!empty($this->params['isAjax'])) {
	return;
}
echo $tidy->report($this->output);