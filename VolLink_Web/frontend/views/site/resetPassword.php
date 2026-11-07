<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \frontend\models\ResetPasswordForm $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Nova palavra-passe';
?>
<h1>Nova palavra-passe</h1>
<p>Escolhe uma palavra-passe com pelo menos 8 caracteres.</p>

<?php $form = ActiveForm::begin([
    'id' => 'reset-password-form',
    'fieldConfig' => [
        'options' => ['class' => 'field'],
        'template' => "{label}\n{input}\n{error}",
        'inputOptions' => ['class' => 'input'],
        'errorOptions' => ['class' => 'invalid-feedback'],
    ],
]) ?>

<?= $form->field($model, 'password')
    ->passwordInput(['autofocus' => true, 'placeholder' => 'Nova palavra-passe', 'autocomplete' => 'new-password'])
    ->label('Palavra-passe') ?>

<?= Html::submitButton('Guardar', ['class' => 'btn btn-primary btn-lg']) ?>

<?php ActiveForm::end() ?>
