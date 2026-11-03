<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array<string, mixed> $event */

use frontend\helpers\Icon;
use frontend\mock\MockData;
use yii\helpers\Html;
use yii\helpers\Url;

$stats = [
    ['label' => 'Candidaturas', 'value' => '42', 'icon' => 'clipboard'],
    ['label' => 'Aprovadas', 'value' => '38', 'icon' => 'check-circle'],
    ['label' => 'Presenças', 'value' => '35', 'icon' => 'users'],
    ['label' => 'Horas', 'value' => '124,5', 'icon' => 'clock'],
];
?>
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
    <div class="head"><h2>Resumo por turno</h2></div>
    <div class="table-scroll">
        <table class="table">
            <thead>
                <tr><th>Turno</th><th>Vagas</th><th>Presenças</th><th>Horas</th></tr>
            </thead>
            <tbody>
                <?php foreach (MockData::reportShifts() as $row): ?>
                    <tr>
                        <td><strong><?= Html::encode($row['shift']) ?></strong></td>
                        <td><?= (int) $row['seats'] ?></td>
                        <td><?= (int) $row['present'] ?></td>
                        <td><?= Html::encode($row['hours']) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>

<div class="foot-actions">
    <a class="btn" href="<?= Url::to(['/company/manage', 'id' => $event['id']]) ?>">Voltar ao evento</a>
</div>
