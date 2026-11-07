<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var list<array<string, mixed>> $users */

use backend\widgets\Drawer;
use common\helpers\Ui;
use yii\helpers\Html;

$this->title = 'Utilizadores';
$this->params['subtitle'] = 'Gerir contas de voluntários e empresas';

$states = array_values(array_unique(array_column($users, 'state')));
$total = count($users);
?>
<div class="card vl-filter" data-filter-table="#users-table">
    <div class="card-body">
        <div class="vl-search">
            <i class="fas fa-search"></i>
            <input type="search" class="form-control" placeholder="Pesquisar por nome ou email"
                   aria-label="Pesquisar por nome ou email" data-filter-search>
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
        <table class="table vl-table table-hover mb-0" id="users-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th class="text-right">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="vl-row" data-toggle="modal" data-target="#user-<?= (int) $user['id'] ?>"
                        data-state="<?= Html::encode($user['state']) ?>">
                        <td><span class="vl-cell-name"><?= Ui::avatar($user['name']) ?><?= Html::encode($user['name']) ?></span></td>
                        <td><?= Html::encode($user['email']) ?></td>
                        <td><?= Html::encode($user['type']) ?></td>
                        <td><?= Ui::pill($user['state']) ?></td>
                        <td class="text-right"><span class="vl-link">Ver</span></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer vl-muted">A mostrar 1–<?= $total ?> de <?= $total ?></div>
</div>

<?php foreach ($users as $user): ?>
    <?php Drawer::begin([
        'id' => 'user-' . (int) $user['id'],
        'title' => $user['name'],
        'subtitle' => $user['email'],
        'pill' => Ui::pill($user['state']),
        'avatar' => Ui::avatar($user['name'], 'lg'),
        'footer' => Html::button('Desativar', ['type' => 'button', 'class' => 'btn btn-outline-secondary'])
            . Html::button('Suspender', ['type' => 'button', 'class' => 'btn btn-danger']),
    ]) ?>
        <dl class="vl-detail">
            <div><dt>Tipo</dt><dd><?= Html::encode($user['type']) ?></dd></div>
            <div><dt>Data de registo</dt><dd><?= Html::encode($user['registered']) ?></dd></div>
            <div><dt>Último login</dt><dd><?= Html::encode($user['lastLogin']) ?></dd></div>
            <div><dt>Estado</dt><dd><?= Ui::pill($user['state']) ?></dd></div>
        </dl>
    <?php Drawer::end() ?>
<?php endforeach ?>
