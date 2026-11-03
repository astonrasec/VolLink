<?php

declare(strict_types=1);

use yii\helpers\Html;

/*
 * Mensagens rápidas da sessão (Yii::$app->session->setFlash). Os tipos "success" e "error"
 * têm estilo próprio; os restantes usam o de erro.
 */
foreach (Yii::$app->session->getAllFlashes(true) as $type => $messages) {
    foreach ((array) $messages as $message) {
        echo Html::tag('div', Html::encode((string) $message), [
            'class' => 'alert alert-' . ($type === 'success' ? 'success' : 'error'),
            'role' => 'alert',
        ]);
    }
}
