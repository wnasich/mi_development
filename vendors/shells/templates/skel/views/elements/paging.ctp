<?php /* SVN FILE: $Id$ */ ?>
<div id='paging'>
<?php
echo $paginator->prev('«', array(), null);
echo '&nbsp;' . $paginator->numbers(array('separator' => ' | ')) . '&nbsp;';
echo $paginator->next('»', array(), null);
?>
</div>