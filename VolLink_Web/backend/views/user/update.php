<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var backend\models\UserForm $model */
/** @var common\models\User $user */

$this->title = 'Editar ' . $user->username;
?>
<?= $this->render('_form', ['model' => $model]) ?>
