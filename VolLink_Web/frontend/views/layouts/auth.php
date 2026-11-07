<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

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

<div class="auth">
    <section class="auth-brand">
        <a class="brand" href="<?= Url::to(['/site/index']) ?>">VolLink</a>

        <div>
            <h1>Voluntariado que liga pessoas e causas</h1>
            <p>Encontra uma causa, candidata-te e acompanha o teu impacto.</p>
        </div>

        <small>VolLink · <?= date('Y') ?></small>
    </section>

    <section class="auth-panel">
        <div class="auth-card">
            <?= $this->render('_flash') ?>
            <?= $content ?>
        </div>
    </section>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
