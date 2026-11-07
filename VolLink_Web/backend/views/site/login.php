<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \common\models\LoginForm $model */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Entrar';
$errorOptions = ['class' => 'invalid-feedback d-block'];
$passwordTemplate = '{label}<div class="vl-password">{input}'
    . '<button type="button" class="vl-eye" data-toggle-password="#loginform-password" aria-label="Mostrar ou esconder a palavra-passe">'
    . '<i class="far fa-eye"></i></button></div>{error}';
?>
<div class="vl-login">
    <section class="vl-login-brand">
        <div class="vl-login-logo">
            <strong>VolLink</strong>
            <span class="vl-outline-badge">Back-office</span>
        </div>

        <div>
            <h1>Gestão de voluntariado</h1>
            <p>Acesso reservado à equipa de administração.</p>
        </div>

        <small>VolLink · Back-office · <?= date('Y') ?></small>
    </section>

    <section class="vl-login-panel">
        <div class="vl-login-card">
            <h2>Entrar no back-office</h2>
            <p class="vl-muted">Use a conta de SysAdmin.</p>

            <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]) ?>

            <?= $form->field($model, 'username', ['errorOptions' => $errorOptions])
                ->textInput(['autofocus' => true, 'placeholder' => 'admin', 'autocomplete' => 'username'])
                ->label('Utilizador') ?>

            <?= $form->field($model, 'password', ['errorOptions' => $errorOptions, 'template' => $passwordTemplate])
                ->passwordInput(['placeholder' => 'Introduza a palavra-passe', 'autocomplete' => 'current-password'])
                ->label('Palavra-passe') ?>

            <?= $form->field($model, 'rememberMe')->checkbox()->label('Manter a sessão iniciada') ?>

            <?= Html::submitButton('Entrar', ['class' => 'btn btn-primary btn-block vl-btn-lg', 'name' => 'login-button']) ?>

            <?php ActiveForm::end() ?>

            <p class="vl-login-note"><i class="fas fa-shield-alt"></i> Acesso monitorizado.</p>
        </div>
    </section>
</div>
