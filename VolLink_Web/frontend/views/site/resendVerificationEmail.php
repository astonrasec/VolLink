<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \frontend\models\ResendVerificationEmailForm $model */

use frontend\helpers\Icon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Confirma o teu email';
?>
<h1>Confirma o teu email</h1>
<p>Enviámos um link de confirmação para o teu email. Se não o recebeste, pede um novo.</p>

<div class="mail-badge"><?= Icon::svg('mail', 30) ?></div>

<?php $form = ActiveForm::begin([
    'id' => 'resend-verification-email-form',
    'fieldConfig' => [
        'options' => ['class' => 'field'],
        'template' => "{label}\n{input}\n{error}",
        'inputOptions' => ['class' => 'input'],
        'errorOptions' => ['class' => 'invalid-feedback'],
    ],
]) ?>

<?= $form->field($model, 'email')
    ->textInput(['autofocus' => true, 'placeholder' => 'email@exemplo.pt', 'autocomplete' => 'email'])
    ->label('Email') ?>

<?= Html::submitButton('Reenviar email', ['class' => 'btn btn-lg btn-block']) ?>

<?php ActiveForm::end() ?>

<a class="link" href="<?= Url::to(['/site/login']) ?>">Voltar ao login</a>

<p class="note">A conta de empresa será validada pelo SysAdmin depois da confirmação.</p>
