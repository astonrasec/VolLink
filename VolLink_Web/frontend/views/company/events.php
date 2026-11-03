<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var list<array<string, mixed>> $events */
/** @var string $filter */
/** @var array<string, string> $tabs */

use common\helpers\Ui;
use frontend\helpers\Icon;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Os meus eventos';
?>
<div class="container page">
    <div class="page-head">
        <div>
            <h1>Os meus eventos</h1>
            <p>Cria e gere os eventos da tua organização.</p>
        </div>
        <a class="btn btn-primary" href="<?= Url::to(['/company/create-event']) ?>"><?= Icon::svg('plus', 16) ?> Criar evento</a>
    </div>

    <nav class="tabs" aria-label="Estado do evento">
        <?php foreach ($tabs as $key => $label): ?>
            <a href="<?= Url::to(['/company/events', 'estado' => $key]) ?>"<?= $key === $filter ? ' class="active"' : '' ?>>
                <?= Html::encode($label) ?>
            </a>
        <?php endforeach ?>
    </nav>

    <section class="card table-card">
        <div class="head"><h2>Eventos</h2></div>
        <?php if ($events === []): ?>
            <div class="empty">Não há eventos neste estado.</div>
        <?php else: ?>
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr><th>Evento</th><th>Data</th><th>Estado</th><th>Candidaturas</th><th>Ações</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><strong><?= Html::encode($event['title']) ?></strong></td>
                                <td><?= Html::encode($event['date']) ?></td>
                                <td><?= Ui::pill($event['state']) ?></td>
                                <td><?= (int) $event['applications'] ?></td>
                                <td>
                                    <span class="cell-actions">
                                        <a class="link" href="<?= Url::to(['/company/manage', 'id' => $event['id']]) ?>">Gerir</a>
                                        <a class="btn btn-sm" href="<?= Url::to(['/event/view', 'id' => $event['id']]) ?>">Ver</a>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </section>
</div>
