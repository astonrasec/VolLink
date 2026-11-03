<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array<string, mixed> $event */

use common\helpers\Ui;
use frontend\mock\MockData;
use yii\helpers\Html;

$volunteer = MockData::pendingApplications()[0];
?>
<section class="card table-card">
    <div class="head">
        <div>
            <h2>Presenças</h2>
            <p>As horas são registadas pelo coordenador.</p>
        </div>
        <div>
            <label class="label" for="attendance-shift">Turno</label>
            <select id="attendance-shift" class="input" style="width: 200px">
                <?php foreach ($event['shifts'] as $shift): ?>
                    <option><?= Html::encode($shift['name']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>

    <div class="chips">
        <span class="chip"><strong>1</strong> presente</span>
        <span class="chip"><strong>4</strong> horas totais</span>
    </div>

    <div class="attendee">
        <?= Ui::avatar($volunteer['name'], 'lg') ?>
        <div class="grow">
            <strong><?= Html::encode($volunteer['name']) ?></strong><br>
            <small><?= Html::encode($volunteer['email']) ?></small>
        </div>
        <label class="check"><input type="checkbox" checked> Presente</label>
        <div>
            <label class="label" for="attendance-hours">Horas</label>
            <input id="attendance-hours" class="input" type="number" min="0" step="0.5" value="4">
        </div>
    </div>

    <?php /* TODO(feat/attendance): gravar presenças e horas (RF-FO-10). */ ?>
    <div class="card-foot">
        <button class="btn btn-primary" type="button">Confirmar presenças</button>
    </div>
</section>
