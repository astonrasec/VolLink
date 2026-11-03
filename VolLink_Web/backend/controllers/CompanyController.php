<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\mock\MockData;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Validação de contas de empresa (RF-BO-06).
 */
final class CompanyController extends Controller
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
     * Lista de empresas com pesquisa e painel de validação.
     */
    public function actionIndex(): string
    {
        return $this->render('index', ['companies' => MockData::companies()]);
    }
}
