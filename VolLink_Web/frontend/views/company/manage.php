<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array<string, mixed> $event */
/** @var string $tab */
/** @var array<string, string> $tabs */

use common\helpers\Ui;
use frontend\helpers\Icon;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $event['title'];
?>
<div class="container page">
    <nav class="breadcrumb" aria-label="Localização">
        <a href="<?= Url::to(['/company/events']) ?>">Os meus eventos</a>
        <?= Icon::svg('chevron-right', 14) ?>
        <span><?= Html::encode($event['title']) ?></span>
    </nav>

    <div class="page-head">
        <div>
            <h1><?= Html::encode($event['title']) ?></h1>
            <p><?= Html::encode($event['company']) ?> · <?= Ui::pill($event['state']) ?></p>
        </div>
        <?php /* TODO(feat/events): editar, cancelar e concluir o evento (RF-FO-02 e RF-FO-09). */ ?>
        <div class="head-actions">
            <button class="btn" type="button">Editar</button>
            <button class="btn btn-danger" type="button">Cancelar</button>
            <button class="btn btn-primary" type="button">Concluir</button>
        </div>
    </div>

    <nav class="tabs" aria-label="Gestão do evento">
        <?php foreach ($tabs as $key => $label): ?>
            <a href="<?= Url::to(['/company/manage', 'id' => $event['id'], 'tab' => $key]) ?>"<?= $key === $tab ? ' class="active"' : '' ?>>
                <?= Html::encode($label) ?>
            </a>
        <?php endforeach ?>
    </nav>

    <?= $this->render('_manage-' . $tab, ['event' => $event]) ?>
</div>
