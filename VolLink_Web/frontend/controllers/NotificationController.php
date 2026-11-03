<?php

declare(strict_types=1);

namespace frontend\controllers;

use frontend\mock\MockData;
use yii\web\Controller;

/**
 * Notificações in-app de voluntários e empresas.
 *
 * TODO(feat/applications): exigir autenticação e ler as notificações da tabela notification.
 */
final class NotificationController extends Controller
{
    /**
     * Lista de notificações. O parâmetro "de=empresa" mostra o menu da empresa (dados de exemplo).
     */
    public function actionIndex(string $de = ''): string
    {
        $this->view->params['nav'] = $de === 'empresa' ? 'company' : 'volunteer';

        return $this->render('index', ['notifications' => MockData::notifications()]);
    }
}
