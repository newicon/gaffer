<?php
/**
 * @var League\Plates\Template\Template $this
 * @var App\Model\Page $page
 */
/** @var \App\Model\Work[] $works */
$works = \App\Model\Work::hydrateMany("active=1",null,"id DESC");
if (!$works) return;
?>
<div class="grid4c work-grid">
<?php foreach ($works as $work) { ?>
    <a class="work-item" href="<?= $work->link ?>" target="_blank" rel="noopener noreferrer">
        <div class="title"><?= $work->title ?></div>
        <?php if ($work->image) { ?>
            <img src="<?= $this->asset($work->image) ?>" alt="">
        <?php } else { ?>
            <img class="placeholder" src="/assets/images/placeholder.svg" />
        <?php } ?>
        <div class="strapline"><?= $work->strapline; ?></div>
        <div class="description"><?= str_replace(",","<br>",$work->description) ?></div>
    </a>
<?php } ?>
</div>