<?php

declare(strict_types=1);

namespace frontend\controllers;

use frontend\mock\MockData;
use yii\web\Controller;

/**
 * Candidaturas do voluntário (RF-FO-14 e RF-FO-15).
 *
 * TODO(feat/applications): exigir a permissão applicationSubmit e ler as candidaturas da base de dados.
 */
final class ApplicationController extends Controller
{
    private const TABS = [
        'todas' => 'Todas',
        'pendentes' => 'Pendentes',
        'aprovadas' => 'Aprovadas',
        'rejeitadas' => 'Rejeitadas',
        'canceladas' => 'Canceladas',
    ];

    private const STATES = [
        'pendentes' => 'Pendente',
        'aprovadas' => 'Aprovada',
        'rejeitadas' => 'Rejeitada',
        'canceladas' => 'Cancelada',
    ];

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
     * Lista de candidaturas, filtrada pelo separador escolhido.
     */
    public function actionIndex(string $estado = 'todas'): string
    {
        $filter = array_key_exists($estado, self::TABS) ? $estado : 'todas';
        $applications = MockData::applications();

        if (isset(self::STATES[$filter])) {
            $applications = array_values(array_filter(
                $applications,
                static fn (array $application): bool => $application['state'] === self::STATES[$filter],
            ));
        }

        return $this->render('index', [
            'applications' => $applications,
            'filter' => $filter,
            'tabs' => self::TABS,
        ]);
    }
}
