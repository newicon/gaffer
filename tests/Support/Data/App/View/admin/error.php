<?php
/**
 * @var \League\Plates\Template\Template $this
 * @var \Exception $exc
 */

?>
<?php $this->layout('admin::layout', ['title' => 'Gaffer ADMIN - Whoops!']) ?>
<h1>ADMIN ERROR : <?= $exc->getMessage() . PHP_EOL; ?></h1>
<?php if (DEBUG) { ?>
<pre>
<?= error . phpget_class($exc) . PHP_EOL ?>
<?= $exc->getFile().":".$exc->getLine().PHP_EOL ?>
<?= $exc->getTraceAsString() . PHP_EOL ?>
</pre>
<?php } ?>

