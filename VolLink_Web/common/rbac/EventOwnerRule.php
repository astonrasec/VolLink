<?php

declare(strict_types=1);

namespace common\rbac;

use yii\rbac\Rule;

/**
 * So o criador do evento o gere. O SysAdmin nao entra aqui - usa moderateEvent no back-office.
 */
class EventOwnerRule extends Rule
{
    public $name = 'eventOwner';

    public function execute($user, $item, $params)
    {
        if ($user === null) {
            return false;
        }

        $event = $params['event'] ?? null;

        if (is_object($event)) {
            $creatorId = $event->creator_id ?? null;
        } elseif (is_array($event)) {
            $creatorId = $event['creator_id'] ?? null;
        } else {
            return false;
        }

        return $creatorId !== null && (int) $creatorId === (int) $user;
    }
}