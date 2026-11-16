<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\User[] $users */

use backend\models\UserForm;
use backend\widgets\Drawer;
use common\helpers\Ui;
use common\models\User;
use common\models\UserDetails;
use yii\helpers\Html;

$this->title = 'Utilizadores';
$this->params['subtitle'] = 'Gerir contas de voluntários e empresas';

$stateOf = static function (User $user): string {
    $status = $user->details?->account_status;

    return match (true) {
        $status === UserDetails::STATUS_SUSPENDED => 'Suspenso',
        $status === UserDetails::STATUS_REJECTED => 'Rejeitado',
        $status === UserDetails::STATUS_PENDING => 'Pendente',
        $user->status === User::STATUS_ACTIVE => 'Ativo',
        default => 'Inativo',
    };
};

$typeOf = static function (User $user): string {
    $roles = array_keys(Yii::$app->authManager->getRolesByUser($user->id));

    return UserForm::ROLES[$roles[0] ?? ''] ?? '-';
};

$nameOf = static fn (User $user): string => $user->details?->name ?? $user->username;

$states = array_values(array_unique(array_map($stateOf, $users)));
$total = count($users);
?>
<p>
    <?= Html::a('Novo utilizador', ['create'], ['class' => 'btn btn-primary']) ?>
</p>

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
                    <tr class="vl-row" data-toggle="modal" data-target="#user-<?= (int) $user->id ?>"
                        data-state="<?= Html::encode($stateOf($user)) ?>">
                        <td><span class="vl-cell-name"><?= Ui::avatar($nameOf($user)) ?><?= Html::encode($nameOf($user)) ?></span></td>
                        <td><?= Html::encode($user->email) ?></td>
                        <td><?= Html::encode($typeOf($user)) ?></td>
                        <td><?= Ui::pill($stateOf($user)) ?></td>
                        <td class="text-right"><span class="vl-link">Ver</span></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer vl-muted">A mostrar <?= $total === 0 ? 0 : 1 ?>–<?= $total ?> de <?= $total ?></div>
</div>

<?php foreach ($users as $user): ?>
    <?php Drawer::begin([
        'id' => 'user-' . (int) $user->id,
        'title' => $nameOf($user),
        'subtitle' => $user->email,
        'pill' => Ui::pill($stateOf($user)),
        'avatar' => Ui::avatar($nameOf($user), 'lg'),
        'footer' => Html::a('Editar', ['update', 'id' => $user->id], ['class' => 'btn btn-outline-secondary'])
            . Html::beginForm(['delete', 'id' => $user->id], 'post', ['class' => 'd-inline'])
            . Html::submitButton('Eliminar', [
                'class' => 'btn btn-danger',
                'onclick' => "return confirm('Eliminar este utilizador?')",
            ])
            . Html::endForm(),
    ]) ?>
        <dl class="vl-detail">
            <div><dt>Tipo</dt><dd><?= Html::encode($typeOf($user)) ?></dd></div>
            <div><dt>Data de registo</dt><dd><?= Yii::$app->formatter->asDate($user->created_at, 'php:d/m/Y') ?></dd></div>
            <div><dt>Último login</dt><dd><?= Html::encode($user->details?->last_login_at ?: 'Nunca') ?></dd></div>
            <div><dt>Estado</dt><dd><?= Ui::pill($stateOf($user)) ?></dd></div>
        </dl>
    <?php Drawer::end() ?>
<?php endforeach ?>
