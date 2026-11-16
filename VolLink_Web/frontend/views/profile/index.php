<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var common\models\UserDetails $details */
/** @var common\models\Attendance[] $attendances */
/** @var float $total */
/** @var frontend\models\ChangePasswordForm $passwordForm */

use common\helpers\Ui;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Perfil e histórico';

$birth = $details->birth_date ? date('d/m/Y', strtotime($details->birth_date)) : '-';
?>
<div class="container page">
    <div class="page-head">
        <div>
            <h1>Perfil e histórico</h1>
            <p>Consulta os teus dados e impacto.</p>
        </div>
    </div>

    <div class="grid-2">
        <section class="card profile-card">
            <?= Ui::avatar($details->name, 'xl') ?>
            <h2><?= Html::encode($details->name) ?></h2>
            <p class="muted"><?= Html::encode($user->email) ?></p>

            <?php if (!$details->isCompany()): ?>
                <div class="kv">
                    <small>Competências</small>
                    <?= Html::encode($details->skills ?: '-') ?>
                </div>
                <div class="kv">
                    <small>Data de nascimento</small>
                    <?= Html::encode($birth) ?>
                </div>
            <?php endif ?>

            <?= Html::a('Editar perfil', ['update'], ['class' => 'btn']) ?>
        </section>

        <section class="card history" id="historico">
            <div class="history-head">
                <div>
                    <strong>Histórico</strong>
                    <small>Total de voluntariado</small>
                </div>
                <span class="total"><?= Html::encode(rtrim(rtrim(number_format($total, 1, ',', ''), '0'), ',')) ?> h</span>
            </div>

            <?php foreach ($attendances as $attendance): ?>
                <div class="history-row">
                    <span class="dot"></span>
                    <div>
                        <strong><?= Html::encode($attendance->shift?->event?->title) ?></strong>
                        <small><?= Html::encode(date('d/m/Y', strtotime((string) $attendance->shift?->start_time))) ?></small>
                    </div>
                    <span class="hours"><?= Html::encode(rtrim(rtrim(number_format((float) $attendance->hours_worked, 1, ',', ''), '0'), ',')) ?> h</span>
                </div>
            <?php endforeach ?>

            <?php if ($attendances === []): ?>
                <p class="muted" style="padding: 16px 20px">Ainda não tens participações confirmadas.</p>
            <?php endif ?>
        </section>
    </div>

    <section class="card security">
        <h2>Segurança</h2>
        <?php $form = ActiveForm::begin([
            'id' => 'form-password',
            'action' => ['password'],
            'options' => ['class' => 'security-grid'],
            'fieldConfig' => [
                'options' => ['class' => 'field'],
                'template' => "{label}\n{input}\n{error}",
                'inputOptions' => ['class' => 'input'],
                'errorOptions' => ['class' => 'invalid-feedback'],
            ],
        ]) ?>
            <?= $form->field($passwordForm, 'current')->passwordInput(['placeholder' => 'Palavra-passe atual', 'autocomplete' => 'current-password']) ?>
            <?= $form->field($passwordForm, 'new')->passwordInput(['placeholder' => 'Nova palavra-passe', 'autocomplete' => 'new-password']) ?>
            <?= $form->field($passwordForm, 'repeat')->passwordInput(['placeholder' => 'Confirmar', 'autocomplete' => 'new-password']) ?>
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
        <?php ActiveForm::end() ?>
    </section>
</div>
