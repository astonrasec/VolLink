<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array<string, mixed> $event */

use frontend\helpers\Icon;
use yii\helpers\Html;

$months = ['jan.', 'fev.', 'mar.', 'abr.', 'mai.', 'jun.', 'jul.', 'ago.', 'set.', 'out.', 'nov.', 'dez.'];
[$day, $month] = array_map('intval', explode('/', (string) $event['date']));

$stats = [
    ['label' => 'Data e hora', 'value' => $day . ' ' . $months[$month - 1], 'icon' => 'calendar'],
    ['label' => 'Localização', 'value' => $event['location'], 'icon' => 'map-pin'],
    ['label' => 'Capacidade', 'value' => (string) $event['seats'], 'icon' => 'users'],
    ['label' => 'Candidaturas', 'value' => (string) $event['applications'], 'icon' => 'clipboard'],
    ['label' => 'Turnos', 'value' => (string) count($event['shifts']), 'icon' => 'clock'],
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
