<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\Event;
use yii\test\ActiveFixture;

class EventFixture extends ActiveFixture
{
    public $modelClass = Event::class;
}
