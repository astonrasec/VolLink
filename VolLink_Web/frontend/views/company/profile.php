<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array<string, string> $company */
/** @var list<array<string, mixed>> $events */

use common\helpers\Ui;
use frontend\helpers\Icon;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $company['name'];
?>
<section class="profile-hero">
    <div class="container">
        <?= Ui::avatar($company['name'], 'xl') ?>
        <div>
            <h1><?= Html::encode($company['name']) ?></h1>
            <span class="verified"><?= Icon::svg('check-circle', 14) ?> Verificada</span>
        </div>
        <a class="btn" href="<?= Url::to(['/company/edit-profile']) ?>">Editar perfil</a>
    </div>
</section>

<div class="container">
    <div class="profile-body">
        <div>
            <h2>Sobre</h2>
            <p><?= Html::encode($company['about']) ?></p>
            <div class="profile-meta">
                <span><?= Icon::svg('globe', 16) ?> <?= Html::encode($company['website']) ?></span>
                <span><?= Icon::svg('map-pin', 16) ?> <?= Html::encode($company['location']) ?></span>
            </div>
        </div>
        <a class="btn" href="https://<?= Html::encode($company['website']) ?>" rel="noopener">Website</a>
    </div>

    <div class="section-head" style="margin-top: 32px">
        <div>
            <h2>Eventos publicados</h2>
            <p>Próximas oportunidades desta organização.</p>
        </div>
    </div>

    <div class="grid-3" style="margin-bottom: 64px">
        <?php foreach ($events as $event): ?>
            <?= $this->render('/event/_card', ['event' => $event, 'showApplication' => false]) ?>
        <?php endforeach ?>
    </div>
</div>
