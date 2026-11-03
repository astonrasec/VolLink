<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var list<array<string, mixed>> $applications */
/** @var string $filter */
/** @var array<string, string> $tabs */

use common\helpers\Ui;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'As minhas candidaturas';
?>
<div class="container page">
    <div class="page-head">
        <div>
            <h1>As minhas candidaturas</h1>
            <p>Acompanha o estado das tuas candidaturas.</p>
        </div>
    </div>

    <nav class="tabs" aria-label="Estado da candidatura">
        <?php foreach ($tabs as $key => $label): ?>
            <a href="<?= Url::to(['/application/index', 'estado' => $key]) ?>"<?= $key === $filter ? ' class="active"' : '' ?>>
                <?= Html::encode($label) ?>
            </a>
        <?php endforeach ?>
    </nav>

    <?php if ($applications === []): ?>
        <div class="card empty">Ainda não tens candidaturas neste estado.</div>
    <?php else: ?>
        <div class="list">
            <?php foreach ($applications as $application): ?>
                <div class="card list-row">
                    <div>
                        <h3><?= Html::encode($application['event']) ?></h3>
                        <p><?= Html::encode($application['company']) ?> · <?= Html::encode($application['date']) ?></p>
                    </div>
                    <div class="row-actions">
                        <?= Ui::pill($application['state']) ?>
                        <a class="link" href="<?= Url::to(['/event/view', 'id' => $application['id']]) ?>">Ver evento</a>
                        <?php if (in_array($application['state'], ['Aprovada', 'Pendente'], true)): ?>
                            <?php /* TODO(feat/applications): cancelar a candidatura. */ ?>
                            <button class="btn" type="button">Cancelar</button>
                        <?php endif ?>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>
