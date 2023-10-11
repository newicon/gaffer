<?php
/**
 * @var \League\Plates\Template\Template $this
 * @var string $title
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta charset="utf-8"/>
    <title><?= $this->e($title); ?></title>
    <link rel="shortcut icon" href="/gaffer.ico" type="image/icon">
    <link rel="icon" href="/gaffer.ico" type="image/icon">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.2/ui/trumbowyg.min.css" integrity="sha512-K87nr2SCEng5Nrdwkb6d6crKqDAl4tJn/BD17YCXH0hu2swuNMqSV6S8hTBZ/39h+0pDpW/tbQKq9zua8WiZTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.2/plugins/colors/ui/trumbowyg.colors.min.css" integrity="sha512-VCJM62+9ou73PDL8ROa9D+lZKG9qrbGv91WxlU3Hyb4lfdnT5wBnLvX45vd45ENRU271iRI9xa1fYJbrVed8Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?= $this->asset('/assets/admin.css') ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<body>
<section class="admin <?= strtolower(str_replace(" ","",$title)); ?>">
    <?php if (!isset($hideNav)) {?>
    <nav>
        <a href="/admin">ADMIN</a>
        <hr>
        <a href="/admin/pages">Pages</a>
        <a href="/admin/images">Images</a>
        <hr>
        <a href="/admin/logout">Logout</a>
    </nav>
    <?php } ?>
    <div class="admin-content">
        <?=$this->section('content')?>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.2/trumbowyg.min.js" integrity="sha512-mBsoM2hTemSjQ1ETLDLBYvw6WP9QV8giiD33UeL2Fzk/baq/AibWjI75B36emDB6Td6AAHlysP4S/XbMdN+kSA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.2/plugins/colors/trumbowyg.colors.js" integrity="sha512-WfFBVVCqZr2xEyZK1Y4Wjj+bO3HD+z5EX1RmCeTY1owe6GenFCnJpVH8W6x0QnGMRRyIOrW2e39KXTLZf4ZX8Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.2/plugins/pasteembed/trumbowyg.pasteembed.min.js" integrity="sha512-TSf9IFC4Fd1wuS7sosoKceCrxVNdXtEhO4G2BW+M5F2GFPPHMnjQ14zd3DvUc7MwPbMYnSMwEUHzF19NqNgl8w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="<?= $this->asset('/assets/admin.js')?>"></script>
</body>
</html>