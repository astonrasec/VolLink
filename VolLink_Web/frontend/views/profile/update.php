<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var frontend\models\ProfileForm $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Editar perfil';
?>
<div class="container page">
    <div class="page-head">
        <div>
            <h1>Editar perfil</h1>
            <p>Atualiza os teus dados pessoais.</p>
        </div>
    </div>

    <section class="card profile-card" style="text-align: left">
        <?php $form = ActiveForm::begin([
            'id' => 'form-profile',
            'fieldConfig' => [
                'options' => ['class' => 'field'],
                'template' => "{label}\n{input}\n{error}",
                'inputOptions' => ['class' => 'input'],
                'errorOptions' => ['class' => 'invalid-feedback'],
            ],
        ]) ?>
            <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'skills')->textInput(['placeholder' => 'Ex.: Primeiros socorros, Comunicação']) ?>
            <?= $form->field($model, 'birthDate')->input('date') ?>

            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Cancelar', ['index'], ['class' => 'btn']) ?>
        <?php ActiveForm::end() ?>
    </section>
</div>
