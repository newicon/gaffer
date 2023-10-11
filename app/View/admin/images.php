<?php
/**
 * @var \League\Plates\Template\Template $this
 * @var array $files
 * @var string $imageDirStr
 */
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.10.3/simple-lightbox.min.js" integrity="sha512-XGiM73niqHXRwBELBEktUKuGXC9yHyaxEsVWvUiCph8yxkaNkGeXJnqs5Ls1RSp4Q+PITMcCy2Dw7HxkzBWQCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.10.3/simple-lightbox.css" integrity="sha512-I/31oIfN7Ki1YPEt0Mv9yyKMXkRmI4xKFRvus0plHCFIOaXC3RuBMaG7LQa+L6ZAeNz5NKXlk8Czr11/1/hR5w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php $this->layout('admin::layout', ['title' => 'ADMIN - Images']) ?>
<h1>Images</h1>
<div>Location : <?= $imageDirStr ?></div>

<table>
    <thead>
        <tr>
            <th>Image</th>
            <th>File</th>
            <th>Size</th>
            <th>Dimensions</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($files as $file) { ?>
        <tr>
            <td class="center"><img src="<?= $file['url'] ?>" alt="preview" style="max-width:100px;  max-height:100px;"></td>
            <td><?= $file['name'] ?></td>
            <td><?= $file['size'] ?></td>
            <td><?= $file['width'] ?> x <?= $file['height'] ?></td>
            <td>
                <a  onclick="copyToClipboard('<?= $file['url'] ?>')" style="cursor:pointer" title="copy image URL to clipboard"><i class="fa fa-copy" ></i></a>
                <a class='lightbox' href="<?= $file['url'] ?>" target="_blank" rel="noopener noreferrer"><i class="fa fa-eye" style="cursor:pointer" title="open image in new tab"></i></a>
            </td>
        </tr>
<?php } ?>
    </tbody>
</table>
<script>
    $('a.lightbox').simpleLightbox();
</script>