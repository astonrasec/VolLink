<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var list<array<string, mixed>> $notifications */

use frontend\helpers\Icon;
use yii\helpers\Html;

$this->title = 'Notificações';
?>
<div class="container page">
    <div class="page-head">
        <div>
            <h1>Notificações</h1>
            <p>Mantém-te a par das tuas candidaturas e eventos.</p>
        </div>
        <?php /* TODO(feat/applications): marcar todas as notificações como lidas. */ ?>
        <button class="btn" type="button">Marcar todas como lidas</button>
    </div>

    <div class="card notices">
        <?php foreach ($notifications as $notification): ?>
            <div class="notice<?= $notification['unread'] ? ' unread' : '' ?>">
                <span class="tile"><?= Icon::svg($notification['icon'], 20) ?></span>
                <div>
                    <h3><?= Html::encode($notification['title']) ?></h3>
                    <p><?= Html::encode($notification['text']) ?></p>
                    <time><?= Html::encode($notification['when']) ?></time>
                </div>
                <?php if ($notification['unread']): ?>
                    <span class="unread-dot" title="Por ler"></span>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
</div>
