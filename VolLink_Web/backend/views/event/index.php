<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var list<array<string, mixed>> $events */

use backend\widgets\Drawer;
use common\helpers\Ui;
use yii\helpers\Html;

$this->title = 'Eventos';
$this->params['subtitle'] = 'Consultar e moderar eventos da plataforma';

$states = array_values(array_unique(array_column($events, 'state')));
$total = count($events);
?>
<div class="card vl-filter" data-filter-table="#events-table">
    <div class="card-body">
        <div class="vl-search">
            <i class="fas fa-search"></i>
            <input type="search" class="form-control" placeholder="Pesquisar evento"
                   aria-label="Pesquisar evento" data-filter-search>
        </div>
        <select class="custom-select vl-select" aria-label="Filtrar por estado" data-filter-state>
            <option value="">Todos os estados</option>
            <?php foreach ($states as $state): ?>
                <option value="<?= Html::encode($state) ?>"><?= Html::encode($state) ?></option>
            <?php endforeach ?>
        </select>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table vl-table table-hover mb-0" id="events-table">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Empresa</th>
                    <th>Estado</th>
                    <th>Data</th>
                    <th class="text-right">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <tr class="vl-row" data-toggle="modal" data-target="#event-<?= (int) $event['id'] ?>"
                        data-state="<?= Html::encode($event['state']) ?>">
                        <td><strong><?= Html::encode($event['title']) ?></strong></td>
                        <td><?= Html::encode($event['company']) ?></td>
                        <td><?= Ui::pill($event['state']) ?></td>
                        <td><?= Html::encode($event['date']) ?></td>
                        <td class="text-right"><span class="vl-link">Ver</span></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer vl-muted">A mostrar 1–<?= $total ?> de <?= $total ?></div>
</div>

<?php foreach ($events as $event): ?>
    <?php Drawer::begin([
        'id' => 'event-' . (int) $event['id'],
        'title' => $event['title'],
        'subtitle' => $event['company'],
        'pill' => Ui::pill($event['state']),
        'footer' => Html::button('Arquivar evento', ['type' => 'button', 'class' => 'btn btn-outline-secondary'])
            . Html::button('Cancelar evento', ['type' => 'button', 'class' => 'btn btn-danger']),
    ]) ?>
        <h3 class="vl-drawer-section">Descrição</h3>
        <p><?= Html::encode($event['description']) ?></p>
        <dl class="vl-detail">
            <div><dt>Categoria</dt><dd><?= Html::encode($event['category']) ?></dd></div>
            <div><dt>Localização</dt><dd><?= Html::encode($event['location']) ?></dd></div>
            <div><dt>Início</dt><dd><?= Html::encode($event['start']) ?></dd></div>
            <div><dt>Fim</dt><dd><?= Html::encode($event['end']) ?></dd></div>
            <div><dt>Prazo de candidatura</dt><dd><?= Html::encode($event['deadline']) ?></dd></div>
            <div><dt>Capacidade</dt><dd><?= (int) $event['capacity'] ?> voluntários</dd></div>
            <div><dt>Candidaturas</dt><dd><?= (int) $event['applications'] ?></dd></div>
        </dl>

        <h3 class="vl-drawer-section">Turnos</h3>
        <?php foreach ($event['shifts'] as $shift): ?>
            <div class="vl-shift">
                <div>
                    <strong><?= Html::encode($shift['name']) ?></strong>
                    <span><?= Html::encode($shift['time']) ?></span>
                </div>
                <span class="vl-muted"><?= (int) $shift['seats'] ?> vagas</span>
            </div>
        <?php endforeach ?>
    <?php Drawer::end() ?>
<?php endforeach ?>
