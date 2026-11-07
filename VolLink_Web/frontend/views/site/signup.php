<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \frontend\models\SignupForm $model */

use frontend\helpers\Icon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Criar conta';

/*
 * O SignupForm do esqueleto só tem username, email e password. O tipo de conta, a confirmação da
 * palavra-passe, a data de nascimento e os termos aparecem como nos mockups mas ainda não são
 * guardados: passam a fazer parte do registo na funcionalidade de autenticação (feat/auth).
 */
?>
<h2>Criar conta</h2>
<p>Escolhe como queres usar a VolLink.</p>

<?php $form = ActiveForm::begin([
    'id' => 'form-signup',
    'fieldConfig' => [
        'options' => ['class' => 'field'],
        'template' => "{label}\n{input}\n{error}",
        'inputOptions' => ['class' => 'input'],
        'errorOptions' => ['class' => 'invalid-feedback'],
    ],
]) ?>

<div class="roles">
    <label class="role">
        <input type="radio" name="account_type" value="volunteer" checked>
        <?= Icon::svg('users', 18) ?>
        <strong>Voluntário</strong>
        <span>Quero participar</span>
    </label>
    <label class="role">
        <input type="radio" name="account_type" value="company">
        <?= Icon::svg('globe', 18) ?>
        <strong>Empresa</strong>
        <span>Quero organizar</span>
    </label>
</div>

<?= $form->field($model, 'username')
    ->textInput(['autofocus' => true, 'placeholder' => 'Nome completo', 'autocomplete' => 'name'])
    ->label('Nome') ?>

<?= $form->field($model, 'email')
    ->textInput(['placeholder' => 'email@exemplo.pt', 'autocomplete' => 'email'])
    ->label('Email') ?>

<div class="field-row">
    <?= $form->field($model, 'password')
        ->passwordInput(['placeholder' => 'Mínimo 8 caracteres', 'autocomplete' => 'new-password'])
        ->label('Palavra-passe') ?>

    <div class="field">
        <label for="password-confirm">Confirmar palavra-passe</label>
        <input id="password-confirm" class="input" type="password" placeholder="Repete a palavra-passe" autocomplete="new-password">
    </div>
</div>

<div class="field">
    <label for="birth-date">Data de nascimento</label>
    <input id="birth-date" class="input" type="date" name="birth_date">
</div>

<label class="check field">
    <input type="checkbox" name="terms" required>
    Aceito os termos e a política de privacidade.
</label>

<?= Html::submitButton('Criar conta', ['class' => 'btn btn-primary btn-lg', 'name' => 'signup-button']) ?>

<?php ActiveForm::end() ?>

<p class="auth-alt">Já tens conta? <a href="<?= Url::to(['/site/login']) ?>">Entrar</a></p>
