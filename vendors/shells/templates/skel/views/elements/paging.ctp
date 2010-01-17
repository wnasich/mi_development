<?php /* SVN FILE: $Id: paging.ctp 1466 2009-08-18 22:21:38Z ad7six $ */ ?>
<div id='paging'>
<?php
echo $paginator->prev('«', array(), null);
echo '&nbsp;' . $paginator->numbers(array('separator' => ' | ')) . '&nbsp;';
echo $paginator->next('»', array(), null);
?>
</div>