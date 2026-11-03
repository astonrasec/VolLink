<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \frontend\models\ContactForm $model */

use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Contacto';
?>
<div class="container page">
    <div class="page-narrow" style="max-width: 640px">
        <div class="page-head">
            <div>
                <h1>Contacto</h1>
                <p>Preenche o formulário e respondemos assim que possível.</p>
            </div>
        </div>

        <div class="card" style="padding: 28px">
            <?php $form = ActiveForm::begin([
                'id' => 'contact-form',
                'fieldConfig' => [
                    'options' => ['class' => 'field'],
                    'template' => "{label}\n{input}\n{error}",
                    'inputOptions' => ['class' => 'input'],
                    'errorOptions' => ['class' => 'invalid-feedback'],
                ],
            ]) ?>

            <div class="field-row">
                <?= $form->field($model, 'name')->textInput(['autofocus' => true])->label('Nome') ?>
                <?= $form->field($model, 'email')->label('Email') ?>
            </div>

            <?= $form->field($model, 'subject')->label('Assunto') ?>
            <?= $form->field($model, 'body')->textarea(['rows' => 6])->label('Mensagem') ?>
            <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                'captchaAction' => 'site/captcha',
                'template' => '{image}{input}',
                'options' => ['class' => 'input', 'placeholder' => 'Código de verificação'],
            ])->label('Código de verificação') ?>

            <?= Html::submitButton('Enviar', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>

            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
