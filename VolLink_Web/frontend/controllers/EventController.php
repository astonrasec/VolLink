<?php

declare(strict_types=1);

namespace frontend\controllers;

use frontend\mock\MockData;
use yii\web\Controller;

/**
 * Detalhe público de um evento (RF-FO-04).
 */
final class EventController extends Controller
{
    /**
     * Página do evento com turnos, vagas e dados da empresa.
     */
    public function actionView(int $id): string
    {
        return $this->render('view', ['event' => MockData::event($id)]);
    }
}
