<?php
/**
 * @var \League\Plates\Template\Template $this
 * @var \Exception $exc
 * @var bool|string $error
 */
?>
<?php $this->layout('admin::layout', ['title' => 'Gaffer - ADMIN','hideNav'=>true]) ?>
<form id="login-form" action="" method="post">
    <h1 style="text-align:center">Admin</h1>
    <div>
        <label>Email</label>
        <input type="email" name="email" />
    </div>
    <div>
        <label>Password</label>
        <input type="password" name="password" />
    </div>
    <div>
        <input type="submit" value="login">
    </div>
    <?php if ($error) {?>
    <div class="error"><?= $error ?></div>
    <?php } ?>
</form>