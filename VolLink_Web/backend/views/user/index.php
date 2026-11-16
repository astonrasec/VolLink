<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use backend\models\UserForm;
use common\helpers\Ui;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Utilizadores';
$this->params['subtitle'] = 'Gerir contas de voluntários e empresas';
?>
<p>
    <?= Html::a('Novo utilizador', ['create'], ['class' => 'btn btn-primary']) ?>
</p>

<div class="card">
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table vl-table table-hover mb-0'],
            'layout' => "{items}\n<div class=\"p-3\">{summary}{pager}</div>",
            'columns' => [
                [
                    'label' => 'Nome',
                    'value' => fn($user) => $user->details?->name ?? $user->username,
                ],
                'email',
                [
                    'label' => 'Perfil',
                    'value' => function ($user) {
                        $roles = array_keys(Yii::$app->authManager->getRolesByUser($user->id));
                        return UserForm::ROLES[$roles[0] ?? ''] ?? '-';
                    },
                ],
                [
                    'label' => 'Estado',
                    'format' => 'raw',
                    'value' => fn($user) => Ui::pill($user->status === 10 ? 'Ativo' : 'Inativo'),
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete}',
                    'urlCreator' => fn($action, $user) => [$action, 'id' => $user->id],
                    'buttons' => [
                        'delete' => fn($url) => Html::a('Eliminar', $url, [
                            'data-method' => 'post',
                            'data-confirm' => 'Eliminar este utilizador?',
                        ]),
                        'view' => fn($url) => Html::a('Ver', $url),
                        'update' => fn($url) => Html::a('Editar', $url),
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
