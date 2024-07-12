<?php
/**
 * @var \League\Plates\Template\Template $this
 * @var array $files
 * @var string $imageDirStr
 */
?>

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
