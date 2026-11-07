<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\Event $event */

use common\models\Event;
use frontend\helpers\Icon;
use frontend\mock\MockData;
use yii\helpers\Html;
use yii\helpers\Url;

$category = MockData::categories()[$event->category] ?? ['tone' => 'green', 'icon' => 'leaf'];
$companyName = $event->getOrganizerName();
$verified = $event->isOrganizerVerified();
$date = Yii::$app->formatter->asDate($event->start_time, 'php:d/m/Y');
$time = Yii::$app->formatter->asTime($event->start_time, 'php:H:i')
    . '–' . Yii::$app->formatter->asTime($event->end_time, 'php:H:i');
?>
<article class="card event-card">
    <div class="band band-<?= Html::encode($category['tone']) ?>">
        <?= Icon::svg($category['icon'], 22) ?> <?= Html::encode($event->category) ?>
    </div>
    <div class="body">
        <h3><?= Html::encode($event->title) ?></h3>
        <p class="org">
            <?= Html::encode($companyName) ?>
            <?php if ($verified): ?>
                <span class="verified"><?= Icon::svg('check-circle', 14) ?> Verificada</span>
            <?php endif ?>
        </p>
        <ul class="meta">
            <li><?= Icon::svg('calendar', 15) ?> <?= Html::encode($date) ?> · <?= Html::encode($time) ?></li>
            <li><?= Icon::svg('map-pin', 15) ?> <?= Html::encode($event->location) ?></li>
            <li><?= Icon::svg('users', 15) ?> <?= $event->getAvailableSeats() ?> vagas</li>
        </ul>
        <a class="btn" href="<?= Url::to(['/event/view', 'id' => $event->id]) ?>">Ver evento</a>
    </div>
</article>
