<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\User $user */

use common\models\UserDetails;
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $user->details?->name ?? $user->username;
?>
<p>
    <?= Html::a('Editar', ['update', 'id' => $user->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Eliminar', ['delete', 'id' => $user->id], [
        'class' => 'btn btn-danger',
        'data-method' => 'post',
        'data-confirm' => 'Eliminar este utilizador?',
    ]) ?>
    <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</p>

<div class="card">
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $user,
            'attributes' => [
                'id',
                'username',
                'email',
                ['label' => 'Nome', 'value' => $user->details?->name],
                ['label' => 'Tipo', 'value' => UserDetails::typeLabels()[$user->details?->account_type] ?? '-'],
                ['label' => 'Estado da conta', 'value' => UserDetails::statusLabels()[$user->details?->account_status] ?? '-'],
                'created_at:datetime',
            ],
        ]) ?>
    </div>
</div>
