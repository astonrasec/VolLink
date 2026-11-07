<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \frontend\models\PasswordResetRequestForm $model */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Recuperar palavra-passe';
?>
<h1>Recuperar palavra-passe</h1>
<p>Indica o teu email e enviamos um link.</p>

<?php $form = ActiveForm::begin([
    'id' => 'request-password-reset-form',
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

<?= Html::submitButton('Enviar link', ['class' => 'btn btn-primary btn-lg']) ?>

<?php ActiveForm::end() ?>

<a class="link" href="<?= Url::to(['/site/login']) ?>">Voltar ao login</a>
