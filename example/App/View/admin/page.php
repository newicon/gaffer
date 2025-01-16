<?php
/**
 * @var \League\Plates\Template\Template $this
 * @var \App\Form\Page $form
 */
?>
<?php $this->layout('admin::layout', ['title' => 'ADMIN - Pages']) ?>
<h1>Page : <?= $form->page->id ?: "New" ?></h1>
<br>

<?= $form->html() ?>
