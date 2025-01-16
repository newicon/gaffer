<?php
/**
 * @var \League\Plates\Template\Template $this
 * @var \App\Model\User[] $users
 */
?>
<?php $this->layout('admin::layout', ['title' => 'ADMIN - Users']) ?>
<h1>Users</h1>
<a href="/admin/users/add"><i class="fa fa-plus-circle"></i> Add a user</a>
<br><br>
<table id="users" style="width:100%;margin-top:10px">
    <thead>
    <tr>
        <th>Email</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user) { ?>
        <tr>
            <td><?= $user['email'] ?></td>
            <td class="center">
                <?php if ($user['status']==='active') {?>
                    <i class="fa fa-check" style="color:green"></i>
                <?php } else { ?>
                    <i class="fa fa-times" style="color:red"></i>
                <?php } ?>
            </td>
            <td><a href="/admin/users/<?= $user['id'] ?>"><i class="fa fa-pencil"></i> Edit</a></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<script>
    $(document).ready(function() {
        $('#users').DataTable({
            paging:false,
            aaSorting:[]
        });
    });
</script>
