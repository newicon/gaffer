<?php
/**
 * @var \League\Plates\Template\Template $this
 * @var \App\Form\User $form
 */
?>
<?php $this->layout('admin::layout', ['title' => 'ADMIN - Users']) ?>
<h1>Page : <?= $form->user->id ?: "New" ?></h1>
<br>

<?= $form->html() ?>
