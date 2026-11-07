<?php

declare(strict_types=1);

namespace frontend\helpers;

use Yii;
use yii\db\Exception as DbException;

/**
 * Escolhe o menu do cabeçalho a partir do utilizador autenticado.
 */
final class Nav
{
    /**
     * Devolve "guest", "company" (quem pode criar eventos) ou "volunteer".
     */
    public static function mode(): string
    {
        if (Yii::$app->user->isGuest) {
            return 'guest';
        }

        try {
            return Yii::$app->user->can('eventCreate') ? 'company' : 'volunteer';
        } catch (DbException) {
            // Sem as tabelas de RBAC (por exemplo, numa base de dados de testes por migrar).
            return 'volunteer';
        }
    }
}
