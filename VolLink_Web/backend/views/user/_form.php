<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var backend\models\UserForm $model */

use backend\models\UserForm;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="card">
    <div class="card-body">
        <?php $form = ActiveForm::begin(['id' => 'form-user']) ?>

        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'username') ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'role')->dropDownList(UserForm::ROLES) ?>
        <?= $form->field($model, 'status')->dropDownList([
            User::STATUS_ACTIVE => 'Ativo',
            User::STATUS_INACTIVE => 'Inativo',
        ]) ?>

        <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>

        <?php ActiveForm::end() ?>
    </div>
</div>
