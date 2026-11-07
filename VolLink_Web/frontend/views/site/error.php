<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;
use yii\web\HttpException;

$this->title = $name;
$statusCode = $exception instanceof HttpException ? $exception->statusCode : 500;
?>
<div class="container error-page">
    <p class="code"><?= Html::encode((string) $statusCode) ?></p>
    <h1><?= Html::encode($message) ?></h1>
    <p>O erro ocorreu enquanto o servidor processava o pedido. Se achas que é um problema do servidor, contacta a equipa.</p>
    <?= Html::a('Voltar aos eventos', Yii::$app->homeUrl, ['class' => 'btn btn-primary']) ?>
</div>
