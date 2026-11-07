<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var list<array<string, mixed>> $companies */

use backend\widgets\Drawer;
use common\helpers\Ui;
use yii\helpers\Html;

$this->title = 'Empresas';
$this->params['subtitle'] = 'Validar organizações e consultar contas';

$states = array_values(array_unique(array_column($companies, 'state')));
$total = count($companies);
?>
<div class="card vl-filter" data-filter-table="#companies-table">
    <div class="card-body">
        <div class="vl-search">
            <i class="fas fa-search"></i>
            <input type="search" class="form-control" placeholder="Pesquisar empresa ou email"
                   aria-label="Pesquisar empresa ou email" data-filter-search>
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
        <table class="table vl-table table-hover mb-0" id="companies-table">
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th class="text-right">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $company): ?>
                    <tr class="vl-row" data-toggle="modal" data-target="#company-<?= (int) $company['id'] ?>"
                        data-state="<?= Html::encode($company['state']) ?>">
                        <td><span class="vl-cell-name"><?= Ui::avatar($company['name']) ?><?= Html::encode($company['name']) ?></span></td>
                        <td><?= Html::encode($company['email']) ?></td>
                        <td><?= Ui::pill($company['state']) ?></td>
                        <td class="text-right"><span class="vl-link">Ver</span></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer vl-muted">A mostrar 1–<?= $total ?> de <?= $total ?></div>
</div>

<?php foreach ($companies as $company): ?>
    <?php Drawer::begin([
        'id' => 'company-' . (int) $company['id'],
        'title' => $company['name'],
        'subtitle' => $company['email'],
        'pill' => Ui::pill($company['state']),
        'avatar' => Ui::avatar($company['name'], 'lg'),
        'footer' => Html::button('Rejeitar', ['type' => 'button', 'class' => 'btn btn-danger'])
            . Html::button('Validar', ['type' => 'button', 'class' => 'btn btn-primary']),
    ]) ?>
        <h3 class="vl-drawer-section">Informação da empresa</h3>
        <p><?= Html::encode($company['about']) ?></p>
        <dl class="vl-detail">
            <div><dt>Website</dt><dd><span class="vl-link"><?= Html::encode($company['website']) ?></span></dd></div>
            <div><dt>Localização</dt><dd><?= Html::encode($company['location']) ?></dd></div>
            <div><dt>Data de registo</dt><dd><?= Html::encode($company['registered']) ?></dd></div>
            <div><dt>Estado</dt><dd><?= Ui::pill($company['state']) ?></dd></div>
        </dl>
    <?php Drawer::end() ?>
<?php endforeach ?>
