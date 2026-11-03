<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var list<array<string, mixed>> $stats */
/** @var list<array<string, string>> $companies */
/** @var list<array<string, string>> $events */

use common\helpers\Ui;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard';
$this->params['subtitle'] = 'Visão geral da plataforma';
?>
<div class="row">
    <?php foreach ($stats as $stat): ?>
        <div class="col-sm-6 col-xl-3">
            <a class="card vl-stat" href="<?= Url::to($stat['url']) ?>">
                <div class="card-body">
                    <div>
                        <span class="vl-stat-label"><?= Html::encode($stat['label']) ?></span>
                        <strong class="vl-stat-value"><?= Html::encode($stat['value']) ?></strong>
                    </div>
                    <span class="vl-tile vl-tile-<?= Html::encode($stat['tone']) ?>">
                        <i class="fas fa-<?= Html::encode($stat['icon']) ?>"></i>
                    </span>
                </div>
            </a>
        </div>
    <?php endforeach ?>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Empresas pendentes</h3>
        <a class="vl-link" href="<?= Url::to(['/company/index']) ?>">Ver todas <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="card-body p-0">
        <table class="table vl-table mb-0">
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Email</th>
                    <th>Data</th>
                    <th>Estado</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $company): ?>
                    <tr>
                        <td><span class="vl-cell-name"><?= Ui::avatar($company['name']) ?><?= Html::encode($company['name']) ?></span></td>
                        <td><?= Html::encode($company['email']) ?></td>
                        <td><?= Html::encode($company['date']) ?></td>
                        <td><?= Ui::pill($company['state']) ?></td>
                        <td><?= Html::a('Validar', ['/company/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Eventos recentes</h3>
        <a class="vl-link" href="<?= Url::to(['/event/index']) ?>">Ver todos <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="card-body p-0">
        <table class="table vl-table mb-0">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Empresa</th>
                    <th>Estado</th>
                    <th>Data</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><strong><?= Html::encode($event['title']) ?></strong></td>
                        <td><?= Html::encode($event['company']) ?></td>
                        <td><?= Ui::pill($event['state']) ?></td>
                        <td><?= Html::encode($event['date']) ?></td>
                        <td><?= Html::a('Ver', ['/event/index'], ['class' => 'vl-link']) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
