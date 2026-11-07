<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array<string, mixed> $event */

use frontend\helpers\Icon;
use yii\helpers\Html;

?>
<section class="card table-card">
    <div class="head">
        <div>
            <h2>Turnos</h2>
            <p>Horários e vagas de cada turno.</p>
        </div>
        <?php /* TODO(feat/events): criar, editar e remover turnos (RF-FO-07). */ ?>
        <button class="btn btn-sm" type="button"><?= Icon::svg('plus', 14) ?> Novo turno</button>
    </div>
    <div style="padding: 20px 24px 8px">
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
    </div>
</section>
