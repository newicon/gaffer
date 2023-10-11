<?php
/**
 * @var League\Plates\Template\Template $this
 * @var App\Model\Page $page
 */
/** @var \App\Model\News[] $news */
$news = \App\Model\News::hydrateMany("active=1",null,'date DESC');
if (!$news) return;
?>
<div class="grid2c news-grid">
<?php foreach($news as $newsItem) {?>
    <a <?php if ($newsItem->link) { ?>href='<?= $newsItem->link; ?>' target="_blank" rel="noreferrer noopener"<?php } ?> class="news-item<?= $newsItem->image && $newsItem->image_2 ? ' double-image' : '' ?>">
        <?php if ( $newsItem->image) { ?>
        <div>
            <img src="<?= $newsItem->image ?>" alt="">
            <?php if ($newsItem->image_2) { ?><img src="<?= $newsItem->image_2 ?>" alt=""><?php } ?>
        </div>
        <?php } ?>
        <div>
            <?php if ($newsItem->title) { ?><div class="title"><?= $newsItem->title ?></div><?php } ?>
            <?php if ($newsItem->title) { ?><div class="summary"><?= $newsItem->summary ?></div><?php } ?>
        </div>
    </a>
<?php } ?>
</div>
