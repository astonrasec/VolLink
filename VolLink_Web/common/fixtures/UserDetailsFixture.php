<?php

declare(strict_types=1);

namespace common\fixtures;

use common\models\UserDetails;
use yii\test\ActiveFixture;

class UserDetailsFixture extends ActiveFixture
{
    public $modelClass = UserDetails::class;
}
