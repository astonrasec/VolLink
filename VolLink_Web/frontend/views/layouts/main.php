<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use frontend\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> · <?= Html::encode(Yii::$app->name) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?= $this->render('_nav') ?>

<main>
    <?php if (Yii::$app->session->hasFlash('success') || Yii::$app->session->hasFlash('error')): ?>
        <div class="container"><?= $this->render('_flash') ?></div>
    <?php endif ?>

    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container">VolLink · <?= date('Y') ?></div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
