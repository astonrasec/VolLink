<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array<string, mixed> $event */

use common\helpers\Ui;
use frontend\mock\MockData;
use yii\helpers\Html;

$applications = MockData::pendingApplications();
?>
<section class="card table-card">
    <div class="head">
        <div>
            <h2>Candidaturas</h2>
            <p>Analisa as candidaturas ao evento.</p>
        </div>
        <div>
            <label class="label" for="application-state">Estado</label>
            <select id="application-state" class="input" style="width: 200px">
                <option>Todas</option>
                <option>Pendentes</option>
                <option>Aprovadas</option>
                <option>Rejeitadas</option>
            </select>
        </div>
    </div>
    <div class="table-scroll">
        <table class="table">
            <thead>
                <tr><th>Voluntário</th><th>Email</th><th>Turno</th><th>Estado</th><th>Ações</th></tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $index => $application): ?>
                    <tr>
                        <td><span class="cell-name"><?= Ui::avatar($application['name']) ?><?= Html::encode($application['name']) ?></span></td>
                        <td><?= Html::encode($application['email']) ?></td>
                        <td><?= Html::encode($application['shift']) ?></td>
                        <td><?= Ui::pill($application['state']) ?></td>
                        <td>
                            <span class="cell-actions">
                                <a class="link" href="#">Ver perfil</a>
                                <button class="btn btn-sm" type="button" data-dialog-open="decide-<?= (int) $index ?>">Decidir</button>
                            </span>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>

<?php foreach ($applications as $index => $application): ?>
    <dialog class="modal" id="decide-<?= (int) $index ?>" aria-labelledby="decide-<?= (int) $index ?>-title">
        <div class="modal-head">
            <div>
                <h2 id="decide-<?= (int) $index ?>-title">Aprovar ou rejeitar candidatura</h2>
                <p>Confirma a decisão para esta candidatura.</p>
            </div>
            <button class="modal-close" type="button" data-dialog-close aria-label="Fechar">&times;</button>
        </div>
        <div class="modal-body">
            <div class="person">
                <?= Ui::avatar($application['name'], 'lg') ?>
                <div>
                    <strong><?= Html::encode($application['name']) ?></strong>
                    <small><?= Html::encode($application['event']) ?> · <?= Html::encode($application['skills']) ?></small>
                </div>
            </div>
        </div>
        <div class="modal-foot">
            <?php /* TODO(feat/applications): gravar a decisão (RF-FO-13). */ ?>
            <button class="btn btn-danger" type="button" data-dialog-close>Rejeitar</button>
            <button class="btn btn-primary" type="button" data-dialog-close>Aprovar</button>
        </div>
    </dialog>
<?php endforeach ?>
