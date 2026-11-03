<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var list<array<string, mixed>> $events */
/** @var list<string> $categories */

use frontend\helpers\Icon;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Eventos de voluntariado';
$showApplication = !Yii::$app->user->isGuest;
?>
<section class="hero">
    <div class="container">
        <h1>Encontra a tua próxima causa</h1>
        <p>Eventos de voluntariado perto de ti.</p>

        <form class="searchbar" method="get" role="search">
            <?= Icon::svg('search', 18) ?>
            <input type="search" name="q" placeholder="Pesquisar eventos" aria-label="Pesquisar eventos">
            <button class="btn btn-primary" type="submit">Pesquisar</button>
        </form>
    </div>
</section>

<div class="container">
    <form class="card filters" method="get">
        <div>
            <label for="filter-category">Categoria</label>
            <select id="filter-category" class="input" name="category">
                <option value="">Todas</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= Html::encode($category) ?>"><?= Html::encode($category) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div>
            <label for="filter-location">Localização</label>
            <input id="filter-location" class="input" type="text" name="location" placeholder="Leiria">
        </div>
        <div>
            <label for="filter-date">Data</label>
            <input id="filter-date" class="input" type="date" name="date">
        </div>
        <a class="link" href="<?= Url::to(['/site/index']) ?>">Limpar</a>
    </form>

    <div class="section-head">
        <div>
            <h2>Eventos disponíveis</h2>
            <p>Escolhe uma causa e participa.</p>
        </div>
        <span class="muted"><?= count($events) ?> eventos</span>
    </div>

    <div class="grid-3">
        <?php foreach ($events as $event): ?>
            <?= $this->render('/event/_card', ['event' => $event, 'showApplication' => $showApplication]) ?>
        <?php endforeach ?>
    </div>

    <nav class="pagination" aria-label="Paginação">
        <span class="disabled"><?= Icon::svg('chevron-left', 16) ?></span>
        <span class="current">1</span>
        <a href="#" aria-label="Página 2">2</a>
        <a href="#" aria-label="Página seguinte"><?= Icon::svg('chevron-right', 16) ?></a>
    </nav>
</div>
