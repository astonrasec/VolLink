<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Event;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Detalhe público de um evento (RF-FO-04).
 */
final class EventController extends Controller
{
    /**
     * Página do evento com turnos, vagas, requisitos e dados da empresa.
     *
     * @throws NotFoundHttpException quando o evento não existe ou não está publicado.
     */
    public function actionView(int $id): string
    {
        $event = Event::find()
            ->where(['id' => $id, 'status' => Event::STATUS_PUBLISHED])
            ->with(['creator.details', 'shifts'])
            ->one();

        if ($event === null) {
            throw new NotFoundHttpException('O evento não existe ou ainda não foi publicado.');
        }

        return $this->render('view', ['event' => $event]);
    }
}
