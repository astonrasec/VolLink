<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\Event $event */

use common\helpers\Ui;
use common\models\Event;
use frontend\helpers\Icon;
use frontend\mock\MockData;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $event->title;

$category = MockData::categories()[$event->category] ?? ['tone' => 'green', 'icon' => 'leaf'];
$companyName = $event->getOrganizerName();
$verified = $event->isOrganizerVerified();
$date = Yii::$app->formatter->asDate($event->start_time, 'php:d/m/Y');
$time = Yii::$app->formatter->asTime($event->start_time, 'php:H:i')
    . '–' . Yii::$app->formatter->asTime($event->end_time, 'php:H:i');
$deadline = Yii::$app->formatter->asDate($event->application_deadline, 'php:d/m/Y');
$applications = $event->getApplications()->count();
?>
<div class="container page">
    <nav class="breadcrumb" aria-label="Localização">
        <a href="<?= Url::to(['/site/index']) ?>">Eventos</a>
        <?= Icon::svg('chevron-right', 14) ?>
        <span><?= Html::encode($event->title) ?></span>
    </nav>

    <div class="detail">
        <article class="card detail-main">
            <?= Ui::pill($event->category, $category['tone'] === 'orange' ? 'amber' : $category['tone']) ?>
            <h1><?= Html::encode($event->title) ?></h1>

            <div class="org-row">
                <?= Ui::avatar($companyName, 'lg') ?>
                <div>
                    <strong><?= Html::encode($companyName) ?></strong>
                    <?php if ($verified): ?>
                        <span class="verified"><?= Icon::svg('check-circle', 14) ?> Verificada</span>
                    <?php endif ?>
                </div>
            </div>

            <div class="info-row">
                <div class="info-item">
                    <?= Icon::svg('calendar', 20) ?>
                    <div><small>Data e hora</small><strong><?= Html::encode($date) ?>, <?= Html::encode($time) ?></strong></div>
                </div>
                <div class="info-item">
                    <?= Icon::svg('map-pin', 20) ?>
                    <div><small>Localização</small><strong><?= Html::encode($event->location) ?></strong></div>
                </div>
                <div class="info-item">
                    <?= Icon::svg('users', 20) ?>
                    <div>
                        <small>Capacidade</small>
                        <strong><?= $event->getAvailableSeats() ?> de <?= (int) $event->capacity ?> vagas</strong>
                    </div>
                </div>
            </div>

            <h2>Sobre o evento</h2>
            <p><?= Html::encode($event->description) ?></p>

            <?php if ($event->required_skills !== null && $event->required_skills !== ''): ?>
                <h2>Requisitos</h2>
                <p><?= Html::encode($event->required_skills) ?></p>
            <?php endif ?>

            <h2>Turnos</h2>
            <?php if (empty($event->shifts)): ?>
                <p class="muted">Ainda não há turnos definidos.</p>
            <?php else: ?>
                <?php foreach ($event->shifts as $shift): ?>
                    <div class="shift">
                        <span class="tile"><?= Icon::svg('clock', 20) ?></span>
                        <div>
                            <strong><?= Html::encode($shift->name ?: 'Turno') ?></strong>
                            <small>
                                <?= Yii::$app->formatter->asTime($shift->start_time, 'php:H:i') ?>
                                –<?= Yii::$app->formatter->asTime($shift->end_time, 'php:H:i') ?>
                            </small>
                        </div>
                        <span class="seats"><?= $shift->getAvailableSeats() ?> de <?= (int) $shift->capacity ?> vagas</span>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
        </article>

        <aside class="card detail-side">
            <p>
                <span class="big-number"><?= $event->getAvailableSeats() ?></span>
                <span class="muted">vagas</span>
            </p>

            <div class="side-meta">
                <strong><?= (int) $applications ?> candidaturas</strong>
                <small>Prazo: <?= Html::encode($deadline) ?></small>
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
