<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\mock\MockData;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Moderação de eventos (RF-BO-02).
 */
final class EventController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['moderateEvent']],
                ],
            ],
        ];
    }

    /**
     * Lista de eventos com pesquisa e painel de moderação.
     */
    public function actionIndex(): string
    {
        return $this->render('index', ['events' => MockData::events()]);
    }
}
