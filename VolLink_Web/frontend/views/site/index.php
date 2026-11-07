<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var list<common\models\Event> $events */
/** @var list<string> $categories */
/** @var array<string, string> $filters */
/** @var yii\data\Pagination $pagination */

use frontend\helpers\Icon;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Eventos de voluntariado';
?>
<section class="hero">
    <div class="container">
        <h1>Encontra a tua próxima causa</h1>
        <p>Eventos de voluntariado perto de ti.</p>

        <form class="searchbar" method="get" role="search">
            <?php foreach (['category', 'location', 'date_from', 'date_to'] as $name): ?>
                <?php if ($filters[$name] !== ''): ?>
                    <input type="hidden" name="<?= Html::encode($name) ?>" value="<?= Html::encode($filters[$name]) ?>">
                <?php endif ?>
            <?php endforeach ?>
            <?= Icon::svg('search', 18) ?>
            <input type="search" name="q" value="<?= Html::encode($filters['q']) ?>"
                   placeholder="Pesquisar eventos" aria-label="Pesquisar eventos">
            <button class="btn btn-primary" type="submit">Pesquisar</button>
        </form>
    </div>
</section>

<div class="container">
    <form class="card filters" method="get">
        <input type="hidden" name="q" value="<?= Html::encode($filters['q']) ?>">

        <div>
            <label for="filter-category">Categoria</label>
            <select id="filter-category" class="input" name="category">
                <option value="">Todas</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= Html::encode($category) ?>"<?= $filters['category'] === $category ? ' selected' : '' ?>>
                        <?= Html::encode($category) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div>
            <label for="filter-location">Localização</label>
            <input id="filter-location" class="input" type="text" name="location"
                   value="<?= Html::encode($filters['location']) ?>" placeholder="Leiria">
        </div>
        <div>
            <label for="filter-date-from">Data de início</label>
            <input id="filter-date-from" class="input" type="date" name="date_from"
                   value="<?= Html::encode($filters['date_from']) ?>">
        </div>
        <div>
            <label for="filter-date-to">Data de fim</label>
            <input id="filter-date-to" class="input" type="date" name="date_to"
                   value="<?= Html::encode($filters['date_to']) ?>">
        </div>
        <a class="link" href="<?= Url::to(['/site/index']) ?>">Limpar</a>
    </form>

    <div class="section-head">
        <div>
            <h2>Eventos disponíveis</h2>
            <p>Escolhe uma causa e participa.</p>
        </div>
        <span class="muted"><?= (int) $pagination->totalCount ?> eventos</span>
    </div>

    <?php if (empty($events)): ?>
        <p class="muted">Nenhum evento encontrado. Tenta outro filtro.</p>
    <?php else: ?>
        <div class="grid-3">
            <?php foreach ($events as $event): ?>
                <?= $this->render('/event/_card', ['event' => $event]) ?>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <?php if ($pagination->pageCount > 1): ?>
        <nav class="pagination" aria-label="Paginação">
            <?php if ($pagination->page > 0): ?>
                <a href="<?= Html::encode($pagination->createUrl($pagination->page - 1)) ?>" aria-label="Página anterior">
                    <?= Icon::svg('chevron-left', 16) ?>
                </a>
            <?php else: ?>
                <span class="disabled"><?= Icon::svg('chevron-left', 16) ?></span>
            <?php endif ?>

            <?php for ($page = 0; $page < $pagination->pageCount; $page++): ?>
                <?php if ($page === $pagination->page): ?>
                    <span class="current"><?= $page + 1 ?></span>
                <?php else: ?>
                    <a href="<?= Html::encode($pagination->createUrl($page)) ?>" aria-label="Página <?= $page + 1 ?>">
                        <?= $page + 1 ?>
                    </a>
                <?php endif ?>
            <?php endfor ?>

            <?php if ($pagination->page < $pagination->pageCount - 1): ?>
                <a href="<?= Html::encode($pagination->createUrl($pagination->page + 1)) ?>" aria-label="Página seguinte">
                    <?= Icon::svg('chevron-right', 16) ?>
                </a>
            <?php else: ?>
                <span class="disabled"><?= Icon::svg('chevron-right', 16) ?></span>
            <?php endif ?>
        </nav>
    <?php endif ?>
</div>
