<?php
/**
 * @var \League\Plates\Template\Template $this
 * @var array $pages
 */
?>
<?php $this->layout('admin::layout', ['title' => 'ADMIN - Pages']) ?>
<h1>Pages</h1>
<a href="/admin/pages/add"><i class="fa fa-plus-circle"></i> Add a page</a>
<br><br>
<table id="pages" style="width:100%;margin-top:10px">
    <thead>
        <tr>
            <th>URL Stub</th>
            <th>Title</th>
            <th>Description</th>
            <th>Active</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($pages as $page) { ?>
        <tr>
            <td><?= $page['stub'] ?></td>
            <td><?= $page['title'] ?></td>
            <td><?= $page['description'] ?></td>
            <td class="center">
                <?php if ($page['active']) {?>
                    <i class="fa fa-check" style="color:green"></i>
                <?php } else { ?>
                    <i class="fa fa-times" style="color:red"></i>
                <?php } ?>
            </td>
            <td><a href="/admin/pages/<?= $page['id'] ?>"><i class="fa fa-pencil"></i> Edit</a></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<script>
$(document).ready(function() {
    $('#pages').DataTable({
        paging:false,
        aaSorting:[]
    });
});
</script>
