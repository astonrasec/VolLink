<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var list<array<string, mixed>> $stats */
/** @var list<array<string, mixed>> $events */
/** @var list<array<string, mixed>> $applications */

use common\helpers\Ui;
use frontend\helpers\Icon;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard';
?>
<div class="container page">
    <div class="page-head">
        <div>
            <h1>Dashboard</h1>
            <p>Visão geral da tua organização.</p>
        </div>
        <a class="btn btn-primary" href="<?= Url::to(['/company/create-event']) ?>"><?= Icon::svg('plus', 16) ?> Criar evento</a>
    </div>

    <div class="stats">
        <?php foreach ($stats as $stat): ?>
            <div class="card stat">
                <div>
                    <span class="label"><?= Html::encode($stat['label']) ?></span>
                    <strong class="value"><?= Html::encode($stat['value']) ?></strong>
                </div>
                <span class="tile"><?= Icon::svg($stat['icon'], 18) ?></span>
            </div>
        <?php endforeach ?>
    </div>

    <section class="card table-card">
        <div class="head"><h2>Próximos eventos</h2></div>
        <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr><th>Evento</th><th>Data</th><th>Estado</th><th>Candidaturas</th><th>Ação</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><strong><?= Html::encode($event['title']) ?></strong></td>
                            <td><?= Html::encode($event['date']) ?></td>
                            <td><?= Ui::pill($event['state']) ?></td>
                            <td><?= (int) $event['applications'] ?></td>
                            <td><a class="link" href="<?= Url::to(['/company/manage', 'id' => $event['id']]) ?>">Gerir</a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="card table-card">
        <div class="head"><h2>Candidaturas pendentes</h2></div>
        <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr><th>Voluntário</th><th>Evento</th><th>Data</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><span class="cell-name"><?= Ui::avatar($application['name']) ?><?= Html::encode($application['name']) ?></span></td>
                            <td><?= Html::encode($application['event']) ?></td>
                            <td><?= Html::encode($application['date']) ?></td>
                            <td>
                                <?php /* TODO(feat/applications): aprovar ou rejeitar a candidatura. */ ?>
                                <span class="cell-actions">
                                    <button class="btn btn-sm" type="button">Rejeitar</button>
                                    <button class="btn btn-sm btn-primary" type="button">Aprovar</button>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
