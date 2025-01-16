<?php
/**
 * @var \League\Plates\Template\Template $this
 * @var \App\Model\Page $page
  * @var string $title
 */
$pageClass = $page->stub==='/' ? 'home' : strtolower(str_replace('/','-',substr($page->stub,1)));
$title = $page->title ?: 'Gaffer';
?>
<?php $this->layout('layout', [
    'title' => $title,
    'pageClass' => $pageClass
])
?>
<?= $page->content; ?>