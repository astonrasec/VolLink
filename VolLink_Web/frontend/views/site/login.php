<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \common\models\LoginForm $model */

use frontend\helpers\Icon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Entrar';
$passwordTemplate = '{label}<div class="password">{input}'
    . '<button type="button" data-toggle-password="#loginform-password" aria-label="Mostrar ou esconder a palavra-passe">'
    . Icon::svg('eye', 18) . '</button></div>{error}';
?>
<h2>Entrar</h2>
<p>Bem-vindo de volta.</p>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'fieldConfig' => [
        'options' => ['class' => 'field'],
        'template' => "{label}\n{input}\n{error}",
        'inputOptions' => ['class' => 'input'],
        'errorOptions' => ['class' => 'invalid-feedback'],
    ],
]) ?>

<?= $form->field($model, 'username')
    ->textInput(['autofocus' => true, 'placeholder' => 'O teu utilizador', 'autocomplete' => 'username'])
    ->label('Utilizador') ?>

<?= $form->field($model, 'password', ['template' => $passwordTemplate])
    ->passwordInput(['placeholder' => 'Introduz a tua palavra-passe', 'autocomplete' => 'current-password'])
    ->label('Palavra-passe') ?>

<a class="forgot" href="<?= Url::to(['/site/request-password-reset']) ?>">Esqueci-me da palavra-passe</a>

<?= Html::submitButton('Entrar', ['class' => 'btn btn-primary btn-lg', 'name' => 'login-button']) ?>

<?php ActiveForm::end() ?>

<p class="auth-alt">Ainda não tens conta? <a href="<?= Url::to(['/site/signup']) ?>">Criar conta</a></p>
