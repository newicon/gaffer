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
        <label>Email
            <input type="email" name="email" />
        </label>
    </div>
    <div>
        <label>Password
            <input type="password" name="password" />
        </label>
    </div>
    <div>
        <input type="submit" name="login" value="login">
    </div>
    <?php if ($error) {?>
    <div class="error"><?= $error ?></div>
    <?php } ?>
</form>