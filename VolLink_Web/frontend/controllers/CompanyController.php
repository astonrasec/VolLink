<?php

declare(strict_types=1);

namespace frontend\controllers;

use frontend\mock\MockData;
use yii\web\Controller;

/**
 * Área da empresa: dashboard, eventos, candidaturas, presenças e perfil público.
 *
 * TODO(feat/company): exigir a permissão eventCreate e ler os dados da base de dados.
 */
final class CompanyController extends Controller
{
    private const EVENT_TABS = [
        'todos' => 'Todos',
        'rascunho' => 'Rascunho',
        'publicado' => 'Publicado',
        'cancelado' => 'Cancelado',
        'concluido' => 'Concluído',
        'arquivado' => 'Arquivado',
    ];

    private const EVENT_STATES = [
        'rascunho' => 'Rascunho',
        'publicado' => 'Publicado',
        'cancelado' => 'Cancelado',
        'concluido' => 'Concluído',
        'arquivado' => 'Arquivado',
    ];

    private const MANAGE_TABS = [
        'resumo' => 'Resumo',
        'turnos' => 'Turnos',
        'candidaturas' => 'Candidaturas',
        'presencas' => 'Presenças',
        'relatorio' => 'Relatório',
    ];

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action): bool
    {
        // Dados de exemplo: o menu da empresa passa a vir do RBAC com a autenticação.
        $this->view->params['nav'] = 'company';

        return parent::beforeAction($action);
    }

    /**
     * Dashboard da empresa (RF-FO-18).
     */
    public function actionDashboard(): string
    {
        return $this->render('dashboard', [
            'stats' => MockData::companyStats(),
            'events' => MockData::companyEvents(),
            'applications' => MockData::pendingApplications(),
        ]);
    }

    /**
     * Lista dos eventos da empresa, filtrada pelo estado.
     */
    public function actionEvents(string $estado = 'todos'): string
    {
        $filter = array_key_exists($estado, self::EVENT_TABS) ? $estado : 'todos';
        $events = MockData::companyEvents();

        if (isset(self::EVENT_STATES[$filter])) {
            $events = array_values(array_filter(
                $events,
                static fn (array $event): bool => $event['state'] === self::EVENT_STATES[$filter],
            ));
        }

        return $this->render('events', ['events' => $events, 'filter' => $filter, 'tabs' => self::EVENT_TABS]);
    }

    /**
     * Assistente de criação de evento (RF-FO-02), passo 1.
     */
    public function actionCreateEvent(): string
    {
        return $this->render('create-event', ['categories' => array_keys(MockData::categories())]);
    }

    /**
     * Gestão de um evento: resumo, turnos, candidaturas, presenças e relatório.
     */
    public function actionManage(int $id = 1, string $tab = 'resumo'): string
    {
        return $this->render('manage', [
            'event' => MockData::event($id),
            'tab' => array_key_exists($tab, self::MANAGE_TABS) ? $tab : 'resumo',
            'tabs' => self::MANAGE_TABS,
        ]);
    }

    /**
     * Perfil público da empresa (RF-FO-19).
     */
    public function actionProfile(): string
    {
        return $this->render('profile', [
            'company' => MockData::company(),
            'events' => MockData::companyPublishedEvents(),
        ]);
    }

    /**
     * Edição do perfil público da empresa (RF-FO-20).
     */
    public function actionEditProfile(): string
    {
        return $this->render('edit-profile', ['company' => MockData::company()]);
    }
}
