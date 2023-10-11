<?php
/**
 * @var \League\Plates\Template\Template $this
  * @var string $title
 * @var string $pageClass
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta charset="utf-8"/>
    <title><?= $this->e($title); ?></title>
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Gaffer Demo" />
    <meta property="og:url" content="https://gaffer.local" />
    <link rel="shortcut icon" href="/gaffer.ico" type="image/icon">
    <link rel="icon" href="/gaffer.ico" type="image/icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/basicLightbox/5.0.0/basicLightbox.min.css" />
    <link rel="stylesheet" href="<?= $this->asset('/assets/styles.css') ?>">
</head>
<body>
    <header>
        <div id="main-banner">
            <a href="/"><img src="/assets/images/banner.png" alt="Gaffer Tape Banner" style="width:100px;height:100px"/></a>
        </div>
    </header>
    <main id="content" class="<?= $pageClass ?>">
<?=$this->section('content')?>
    </main>
    <script src="/assets/main.js"></script>
</body>
</html>