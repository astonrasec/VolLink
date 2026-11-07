<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use common\helpers\Ui;
use yii\helpers\Html;

$username = Yii::$app->user->isGuest ? 'Convidado' : (string) Yii::$app->user->identity->username;
?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light vl-topbar">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Mostrar ou esconder o menu">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <div class="vl-page-title">
        <h1><?= Html::encode((string) $this->title) ?></h1>
        <?php if (!empty($this->params['subtitle'])): ?>
            <p><?= Html::encode($this->params['subtitle']) ?></p>
        <?php endif ?>
    </div>

    <div class="vl-user-chip ml-auto">
        <?= Ui::avatar($username) ?>
        <span>
            <strong><?= Html::encode($username) ?></strong>
            <small>Administrador</small>
        </span>
    </div>
</nav>
