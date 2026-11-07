<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\mock\MockData;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Gestão de contas de utilizadores (RF-BO-01, RF-BO-03 e RF-BO-05).
 */
final class UserController extends Controller
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
                    ['allow' => true, 'roles' => ['userModerate']],
                ],
            ],
        ];
    }

    /**
     * Lista de utilizadores com pesquisa e painel de detalhe.
     */
    public function actionIndex(): string
    {
        return $this->render('index', ['users' => MockData::users()]);
    }
}
