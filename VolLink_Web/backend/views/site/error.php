<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;
use yii\web\HttpException;

$this->title = $name;
$this->params['subtitle'] = 'Ocorreu um erro';
$statusCode = $exception instanceof HttpException ? $exception->statusCode : 500;
?>
<div class="vl-error">
    <p class="vl-error-code"><?= Html::encode((string) $statusCode) ?></p>
    <h2><?= Html::encode($message) ?></h2>
    <p class="vl-muted">
        O erro ocorreu enquanto o servidor processava o pedido.
        Se considera que é um problema do servidor, contacte a equipa.
    </p>
    <?= Html::a('Voltar ao dashboard', Yii::$app->homeUrl, ['class' => 'btn btn-primary']) ?>
</div>
