<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array<string, mixed> $event */

use common\helpers\Ui;
use frontend\helpers\Icon;
use frontend\mock\MockData;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $event['title'];
$category = MockData::categories()[$event['category']] ?? ['tone' => 'green', 'icon' => 'leaf'];
?>
<div class="container page">
    <nav class="breadcrumb" aria-label="Localização">
        <a href="<?= Url::to(['/site/index']) ?>">Eventos</a>
        <?= Icon::svg('chevron-right', 14) ?>
        <span><?= Html::encode($event['title']) ?></span>
    </nav>

    <div class="detail">
        <article class="card detail-main">
            <?= Ui::pill($event['category'], $category['tone'] === 'orange' ? 'amber' : $category['tone']) ?>
            <h1><?= Html::encode($event['title']) ?></h1>

            <div class="org-row">
                <?= Ui::avatar($event['company'], 'lg') ?>
                <div>
                    <strong><?= Html::encode($event['company']) ?></strong>
                    <?php if ($event['verified']): ?>
                        <span class="verified"><?= Icon::svg('check-circle', 14) ?> Verificada</span>
                    <?php endif ?>
                </div>
            </div>

            <div class="info-row">
                <div class="info-item">
                    <?= Icon::svg('calendar', 20) ?>
                    <div><small>Data e hora</small><strong><?= Html::encode($event['date']) ?>, <?= Html::encode($event['time']) ?></strong></div>
                </div>
                <div class="info-item">
                    <?= Icon::svg('map-pin', 20) ?>
                    <div><small>Localização</small><strong><?= Html::encode($event['location']) ?></strong></div>
                </div>
                <div class="info-item">
                    <?= Icon::svg('users', 20) ?>
                    <div><small>Capacidade</small><strong><?= (int) $event['seats'] ?> vagas</strong></div>
                </div>
            </div>

            <h2>Sobre o evento</h2>
            <p><?= Html::encode($event['about']) ?></p>

            <h2>Turnos</h2>
            <?php foreach ($event['shifts'] as $shift): ?>
                <div class="shift">
                    <span class="tile"><?= Icon::svg('clock', 20) ?></span>
                    <div>
                        <strong><?= Html::encode($shift['name']) ?></strong>
                        <small><?= Html::encode($shift['time']) ?></small>
                    </div>
                    <span class="seats"><?= (int) $shift['seats'] ?> vagas</span>
                </div>
            <?php endforeach ?>
        </article>

        <aside class="card detail-side">
            <p><span class="big-number"><?= (int) $event['seats'] ?></span> <span class="muted">vagas</span></p>

            <div class="side-meta">
                <strong><?= (int) $event['applications'] ?> candidaturas</strong>
                <small>Prazo: <?= Html::encode($event['deadline']) ?></small>
            </div>

            <?php if (Yii::$app->user->isGuest): ?>
                <a class="btn btn-primary" href="<?= Url::to(['/site/login']) ?>">Entrar para me candidatar</a>
            <?php else: ?>
                <?php /* TODO(feat/applications): submeter a candidatura ao turno escolhido. */ ?>
                <button class="btn btn-primary" type="button">Candidatar-me</button>
            <?php endif ?>

            <a class="link" href="<?= Url::to(['/company/profile']) ?>">Ver perfil da empresa</a>
        </aside>
    </div>
</div>
