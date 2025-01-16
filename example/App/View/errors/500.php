<?php
/**
 * @var string $message
 * @var \Exception $exception
 */
?>
<?php $this->layout('layout', ['title' => 'Gaffer ERROR', 'pageClass'=>'error']) ?>
<h1>SERVER ERROR</h1>
<?= $message; ?>

<?php if (DEBUG) { ?>
<pre>
<?= get_class($exception) . PHP_EOL ?>
<?= $exception->getFile().":".$exception->getLine().PHP_EOL ?>
<?= $exception->getTraceAsString() . PHP_EOL ?>
</pre>
<?php } ?>
