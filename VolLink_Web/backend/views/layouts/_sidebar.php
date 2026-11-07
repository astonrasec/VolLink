<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use common\helpers\Ui;
use hail812\adminlte\widgets\Menu;
use yii\helpers\Html;
use yii\helpers\Url;

$username = Yii::$app->user->isGuest ? 'Convidado' : (string) Yii::$app->user->identity->username;
?>
<aside class="main-sidebar sidebar-light-primary vl-sidebar">
    <a href="<?= Url::to(['/site/index']) ?>" class="brand-link vl-brand">
        <span class="vl-brand-name">VolLink</span>
        <span class="vl-brand-sub">Back-office</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <?= Menu::widget([
                'items' => [
                    ['label' => 'Dashboard', 'icon' => 'th-large', 'url' => ['/site/index']],
                    ['label' => 'Utilizadores', 'icon' => 'users', 'url' => ['/user/index']],
                    ['label' => 'Empresas', 'icon' => 'building', 'url' => ['/company/index']],
                    ['label' => 'Eventos', 'icon' => 'calendar-alt', 'url' => ['/event/index']],
                ],
            ]) ?>
        </nav>

        <div class="vl-sidebar-user">
            <?= Ui::avatar($username) ?>
            <div>
                <strong><?= Html::encode($username) ?></strong>
                <?= Html::a('Sair', ['/site/logout'], ['class' => 'vl-logout', 'data-method' => 'post']) ?>
            </div>
        </div>
    </div>
</aside>
