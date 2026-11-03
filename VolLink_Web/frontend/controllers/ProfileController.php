<?php

declare(strict_types=1);

namespace frontend\controllers;

use frontend\mock\MockData;
use yii\web\Controller;

/**
 * Perfil do voluntário com o histórico de horas (RF-FO-05 e RF-FO-14).
 *
 * TODO(feat/auth): exigir autenticação e ler o perfil de user_details.
 */
final class ProfileController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action): bool
    {
        // Dados de exemplo: o menu do voluntário passa a vir do RBAC com a autenticação.
        $this->view->params['nav'] = 'volunteer';

        return parent::beforeAction($action);
    }

    /**
     * Dados pessoais, histórico de participações e alteração da palavra-passe.
     */
    public function actionIndex(): string
    {
        return $this->render('index', ['volunteer' => MockData::volunteer()]);
    }
}
