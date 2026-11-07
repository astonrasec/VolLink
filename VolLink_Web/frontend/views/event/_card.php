<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array<string, mixed> $event */
/** @var bool $showApplication mostra o estado da candidatura do voluntário */

use common\helpers\Ui;
use frontend\helpers\Icon;
use frontend\mock\MockData;
use yii\helpers\Html;
use yii\helpers\Url;

$category = MockData::categories()[$event['category']] ?? ['tone' => 'green', 'icon' => 'leaf'];
?>
<article class="card event-card">
    <div class="band band-<?= Html::encode($category['tone']) ?>">
        <?= Icon::svg($category['icon'], 22) ?> <?= Html::encode($event['category']) ?>
    </div>
    <div class="body">
        <?php if ($showApplication): ?>
            <?= Ui::pill($event['myApplication']) ?>
        <?php endif ?>
        <h3><?= Html::encode($event['title']) ?></h3>
        <p class="org">
            <?= Html::encode($event['company']) ?>
            <?php if ($event['verified']): ?>
                <span class="verified"><?= Icon::svg('check-circle', 14) ?> Verificada</span>
            <?php endif ?>
        </p>
        <ul class="meta">
            <li><?= Icon::svg('calendar', 15) ?> <?= Html::encode($event['date']) ?> · <?= Html::encode($event['time']) ?></li>
            <li><?= Icon::svg('map-pin', 15) ?> <?= Html::encode($event['location']) ?></li>
            <li><?= Icon::svg('users', 15) ?> <?= (int) $event['seats'] ?> vagas</li>
        </ul>
        <a class="btn" href="<?= Url::to(['/event/view', 'id' => $event['id']]) ?>">Ver evento</a>
    </div>
</article>
