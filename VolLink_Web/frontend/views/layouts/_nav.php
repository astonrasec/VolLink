<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use common\helpers\Ui;
use frontend\helpers\Icon;
use frontend\helpers\Nav;
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * Menu do cabeçalho. As páginas de voluntário e de empresa definem $this->params['nav']
 * ('volunteer' ou 'company') com dados de exemplo; nas restantes o perfil vem do RBAC.
 */
$explicit = isset($this->params['nav']);
$mode = $this->params['nav'] ?? Nav::mode();

$demo = [
    'volunteer' => ['name' => 'Ana Silva', 'alerts' => 3],
    'company' => ['name' => 'Associação Horizonte', 'alerts' => 2],
];
$userName = $explicit
    ? $demo[$mode]['name']
    : (string) (Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->username);
$alerts = $demo[$mode]['alerts'] ?? 0;
$route = (string) Yii::$app->controller?->route;

// [rótulo, rota, prefixos de rota que a marcam como atual, avisos por ler]
$links = match ($mode) {
    'volunteer' => [
        ['Eventos', ['/site/index'], ['site/index', 'event/'], 0],
        ['As minhas candidaturas', ['/application/index'], ['application/'], 0],
        ['Notificações', ['/notification/index'], ['notification/'], $alerts],
    ],
    'company' => [
        ['Dashboard', ['/company/dashboard'], ['company/dashboard'], 0],
        ['Os meus eventos', ['/company/events'], ['company/events', 'company/manage', 'company/create-event'], 0],
        ['Perfil público', ['/company/profile'], ['company/profile', 'company/edit-profile'], 0],
        ['Notificações', ['/notification/index', 'de' => 'empresa'], ['notification/'], $alerts],
    ],
    default => [
        ['Eventos', ['/site/index'], ['site/index', 'event/'], 0],
    ],
};
?>
<header class="site-header">
    <div class="container">
        <a class="brand" href="<?= Url::to(['/site/index']) ?>">VolLink</a>

        <nav class="nav" aria-label="Principal">
            <?php foreach ($links as [$label, $url, $prefixes, $count]): ?>
                <?php
                $current = false;
                foreach ($prefixes as $prefix) {
                    $current = $current || str_starts_with($route, $prefix);
                }
                ?>
                <a href="<?= Url::to($url) ?>"<?= $current ? ' aria-current="page"' : '' ?><?= $count > 0 ? ' class="nav-alert"' : '' ?>>
                    <?= Html::encode($label) ?>
                    <?php if ($count > 0): ?>
                        <span class="badge"><?= (int) $count ?></span>
                    <?php endif ?>
                </a>
            <?php endforeach ?>

            <?php if ($mode === 'guest'): ?>
                <a href="<?= Url::to(['/site/login']) ?>">Entrar</a>
                <a class="btn btn-primary" href="<?= Url::to(['/site/signup']) ?>">Criar conta</a>
            <?php else: ?>
                <span class="nav-user">
                    <?= Ui::avatar($userName) ?>
                    <?php if ($mode === 'volunteer'): ?>
                        <a href="<?= Url::to(['/profile/index']) ?>">Perfil</a>
                        <a href="<?= Url::to(['/profile/index', '#' => 'historico']) ?>">Histórico</a>
                    <?php endif ?>
                    <?= Html::a('Sair', ['/site/logout'], ['data-method' => 'post']) ?>
                </span>
                <?php if ($mode === 'company'): ?>
                    <a class="btn btn-primary" href="<?= Url::to(['/company/create-event']) ?>">
                        <?= Icon::svg('plus', 16) ?> Criar evento
                    </a>
                <?php endif ?>
            <?php endif ?>
        </nav>
    </div>
</header>
